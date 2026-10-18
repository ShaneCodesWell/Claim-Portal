<?php

namespace App\Jobs;

use App\Models\Customer;
use App\Services\GenovaApiService;
use App\Services\GlimsApiService;
use App\Services\PolicySyncService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Support\NameMatcher;

class SyncCustomerPoliciesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 120;

    public function __construct(
        public Customer $customer,
        public ?string $secondaryCode = null // ← GLIMS code when profile was merged
    ) {}

    public function handle(
        GenovaApiService $api,
        GlimsApiService $glims,
        PolicySyncService $policySync
    ): void {
        // Skip if synced within the last 30 minutes
        if ($this->customer->last_synced_at?->gt(now()->subMinutes(30))) {
            Log::info('SyncCustomerPoliciesJob: skipped (recently synced)', [
                'customer_id' => $this->customer->id,
            ]);
            return;
        }

        Log::info('SyncCustomerPoliciesJob: starting', [
            'customer_id'    => $this->customer->id,
            'secondary_code' => $this->secondaryCode,
        ]);

        // Prefer the explicit GLIMS code from a merged profile,
        // otherwise the customer's own code (GLIMS-only customers)
        $glimsCode = $this->secondaryCode ?? $this->customer->glims_customer_code;

        $glimsOk = true;
        if ($glimsCode) {
            $glimsOk = $this->syncGlims($glims, $policySync, $glimsCode);
        }

        if ($glimsCode && $glimsOk) {
            $this->customer->update(['last_synced_at' => now()]);   // GLIMS data is saved
        }

        // Genova must never block GLIMS. Throwable (not Exception) because
        // "call to undefined method" is an Error and slips past catch (\Exception).
        $genovaOk = true;
        try {
            $this->syncGenova($api, $policySync);
        } catch (\Throwable $e) {
            $genovaOk = false;
            Log::error('SyncCustomerPoliciesJob: Genova sync failed', [
                'customer_id' => $this->customer->id,
                'error'       => $e->getMessage(),
            ]);
        }

        // Only mark as synced when the system that applies to this customer succeeded.
        // GLIMS customers are judged on GLIMS, so a Genova outage can't strand them.
        $synced = $glimsCode ? $glimsOk : $genovaOk;

        if ($synced) {
            $this->customer->update(['last_synced_at' => now()]);
        }

        Log::info('SyncCustomerPoliciesJob: finished', [
            'customer_id' => $this->customer->id,
            'glims_ok'    => $glimsOk,
            'genova_ok'   => $genovaOk,
            'stamped'     => $synced,
        ]);
    }

    // GLIMS sync path
    private function syncGlims(
        GlimsApiService $glims,
        PolicySyncService $policySync,
        string $customerCode
    ): bool {
        Log::info('SyncCustomerPoliciesJob: starting GLIMS sync', [
            'customer_id'   => $this->customer->id,
            'customer_code' => $customerCode,
        ]);

        try {
            // Step 1: Refresh the customer record from the main GLIMS code
            $this->refreshGlimsCustomer($glims, $policySync, $customerCode);

            // Step 2: The same person can be registered under more than one GLIMS code.
            // Find the other codes (same phone, matching name) and fetch policies for all of them.
            $siblingCodes = $this->findSiblingGlimsCodes($glims, $customerCode);

            if (! empty($siblingCodes)) {
                Log::info('SyncCustomerPoliciesJob: also syncing other GLIMS codes for this person', [
                    'customer_id'   => $this->customer->id,
                    'main_code'     => $customerCode,
                    'sibling_codes' => $siblingCodes,
                ]);
            }

            $policies = [];

            foreach (array_merge([$customerCode], $siblingCodes) as $code) {
                $policies = array_merge($policies, $glims->getPoliciesByClientCode($code));
            }

            if (empty($policies)) {
                Log::info('SyncCustomerPoliciesJob: no GLIMS policies found', [
                    'customer_id' => $this->customer->id,
                ]);
                return true;   // an empty result is a valid, successful answer
            }

            $policyNumbers = collect($policies)->pluck('POLICY_NUMBER')->filter()->unique()->values();

            Log::info('SyncCustomerPoliciesJob: syncing GLIMS policies', [
                'customer_id'  => $this->customer->id,
                'policy_count' => $policyNumbers->count(),
            ]);

            // Step 3: Fetch rich vehicle detail for all policies in concurrent batches
            $risksByPolicy = $glims->getRisksForPolicies($policyNumbers->all());

            foreach ($policies as &$policy) {
                $policyNumber = $policy['POLICY_NUMBER'] ?? '';
                $risks        = $risksByPolicy[$policyNumber] ?? [];

                if (! empty($risks)) {
                    // Fleet policies: the policy-number lookup can return only some of the vehicles.
                    // Look the missing ones up by plate, and keep a placeholder for any still missing.
                    $risks = $glims->completeVehicleList($policyNumber, $risks, $policy['risks'] ?? []);

                    $policy['risks']    = $risks;
                    $policy['is_fleet'] = count($risks) > 1;
                }
            }
            unset($policy);

            // Step 4: Sync all policies into the DB
            $synced = $policySync->syncFromGlimsRich($policies, $this->customer);

            Log::info('SyncCustomerPoliciesJob: GLIMS sync complete', [
                'customer_id'     => $this->customer->id,
                'policies_synced' => count($synced),
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('SyncCustomerPoliciesJob: GLIMS sync failed', [
                'customer_id' => $this->customer->id,
                'error'       => $e->getMessage(),
            ]);

            return false;   // caller will not stamp last_synced_at, so the next login retries
        }
    }

    /**
     * Other GLIMS codes that belong to the same person: same phone number AND a matching name.
     * The name check keeps other people who share the phone (family, colleagues) out.
     */
    private function findSiblingGlimsCodes(GlimsApiService $glims, string $mainCode): array
    {
        $phone = $this->customer->phone;

        if (blank($phone) || blank($this->customer->name)) {
            return [];
        }

        try {
            $profiles = $glims->resolveProfilesByPhone($phone);
        } catch (\Throwable $e) {
            return [];
        }

        $codes = [];

        foreach ($profiles as $profile) {
            $code = (string) ($profile['code'] ?? '');

            if ($code === '' || $code === (string) $mainCode) {
                continue;
            }

            if (NameMatcher::matches($this->customer->name, $profile['name'] ?? '')) {
                $codes[] = $code;
            }
        }

        return array_values(array_unique($codes));
    }

    /**
     * Fetch the customer record from the dedicated customer endpoint and
     * refresh the local Customer model with phone, email, and raw_payload.
     */
    private function refreshGlimsCustomer(
        GlimsApiService $glims,
        PolicySyncService $policySync,
        string $customerCode
    ): void {
        try {
            $results = $glims->searchCustomerByCode($customerCode);

            if (! empty($results)) {
                $policySync->refreshCustomerFromGlimsRow($this->customer, $results[0]);
            }
        } catch (\Exception $e) {
            Log::warning('SyncCustomerPoliciesJob: GLIMS customer refresh failed', [
                'customer_id' => $this->customer->id,
                'error'       => $e->getMessage(),
            ]);
        }
    }

    // Genova sync path (unchanged)
    private function syncGenova(GenovaApiService $api, PolicySyncService $policySync): void
    {
        $phone        = $this->customer->phone;
        $customerCode = $this->customer->genova_customer_code;

        // Step 1: Build product catalogue
        $allProducts = $this->fetchProductCatalogue($api, $phone);

        // Step 2: Get flat policy list via customer-search
        $policies = [];

        if ($customerCode) {
            $response = $api->getPolicies($customerCode, 'client_code');
            if ($response->successful()) {
                $content = $response->json('data.content') ?? [];

                // Refresh customer record from Genova while we have the data ─
                if (! empty($content[0])) {
                    $policySync->refreshCustomerFromGenova($this->customer, $content[0]);
                    $this->customer->refresh(); // pick up any phone/email updates
                }

                $policies = $content[0]['policies'] ?? [];
            }
        }

        if (empty($policies) && $phone) {
            $response = $api->getPolicies($phone, 'phone_number');

            if ($response->successful()) {
                $content = $response->json('data.content') ?? [];

                // Several people can share one phone number, so only use the profile
                // whose name matches this customer
                $matches = array_values(array_filter(
                    $content,
                    fn($entry) => NameMatcher::matches($this->customer->name, $entry['name'] ?? '')
                ));

                if (count($matches) === 1) {
                    $policies = $matches[0]['policies'] ?? [];
                } elseif (count($matches) > 1) {
                    Log::warning('SyncCustomerPoliciesJob: several Genova profiles match this customer, skipping phone lookup', [
                        'customer_id' => $this->customer->id,
                        'matches'     => count($matches),
                    ]);
                }
                // No match means no Genova policies are attached
            }
        }

        if (empty($policies)) {
            Log::info('SyncCustomerPoliciesJob: no Genova policies found', [
                'customer_id' => $this->customer->id,
            ]);
            return;
        }

        // Step 3: Deduplicate policy IDs
        $uniquePolicyIds = collect($policies)
            ->pluck('policy_id')
            ->unique()
            ->values();

        Log::info('SyncCustomerPoliciesJob: syncing Genova policies', [
            'customer_id'       => $this->customer->id,
            'unique_policy_ids' => $uniquePolicyIds->count(),
            'total_rows'        => count($policies),
        ]);

        // Step 4: Fetch rich data per policy and sync
        foreach ($uniquePolicyIds as $policyId) {
            try {
                $richResponse = $api->policySearch((string) $policyId);

                if (! $richResponse->successful()) {
                    Log::warning('SyncCustomerPoliciesJob: Genova policy-search failed', [
                        'policy_id' => $policyId,
                        'status'    => $richResponse->status(),
                    ]);
                    continue;
                }

                $richData = $richResponse->json('data.policies.0');

                if (! $richData) {
                    Log::warning('SyncCustomerPoliciesJob: empty Genova policy-search response', [
                        'policy_id' => $policyId,
                    ]);
                    continue;
                }

                $policySync->syncFromGenovaRich($richData, $allProducts, $this->customer);
            } catch (\Exception $e) {
                Log::error('SyncCustomerPoliciesJob: Genova error on policy', [
                    'policy_id' => $policyId,
                    'error'     => $e->getMessage(),
                ]);
            }
        }
    }

    private function fetchProductCatalogue(GenovaApiService $api, ?string $phone): array
    {
        $allProducts = [];

        if (! $phone) {
            return $allProducts;
        }

        try {
            $classResponse = $api->getBusinessClasses($phone);
            if (! $classResponse->successful()) {
                return $allProducts;
            }

            $businessClasses = collect($classResponse->json('data.content') ?? [])
                ->mapWithKeys(fn($c) => [$c['id'] => $c['name']]);

            foreach ($businessClasses as $classId => $className) {
                $productResponse = $api->getProductsByClass($classId);
                if (! $productResponse->successful()) {
                    continue;
                }

                foreach ($productResponse->json('data.content') ?? [] as $product) {
                    $allProducts[$product['id']] = [
                        'id'                  => $product['id'],
                        'name'                => $product['name'],
                        'business_class_id'   => $classId,
                        'business_class_name' => $className,
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning('SyncCustomerPoliciesJob: product catalogue fetch failed', [
                'error' => $e->getMessage(),
            ]);
        }

        return $allProducts;
    }
}
