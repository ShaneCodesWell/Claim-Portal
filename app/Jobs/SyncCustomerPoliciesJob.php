<?php
namespace App\Jobs;

use App\Models\Customer;
use App\Services\GenovaApiService;
use App\Services\PolicySyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncCustomerPoliciesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 120;

    public function __construct(public Customer $customer)
    {}

    public function handle(GenovaApiService $api, PolicySyncService $policySync): void
    {
        // Skip if synced within the last 30 minutes
        if ($this->customer->last_synced_at?->gt(now()->subMinutes(30))) {
            Log::info('SyncCustomerPoliciesJob: skipped (recently synced)', [
                'customer_id' => $this->customer->id,
            ]);
            return;
        }

        Log::info('SyncCustomerPoliciesJob: starting', ['customer_id' => $this->customer->id]);

        $phone        = $this->customer->phone;
        $customerCode = $this->customer->external_customer_code;

        // ── Step 1: Build product catalogue (needed to resolve names for new policies) ──
        $allProducts = $this->fetchProductCatalogue($api, $phone);

        // ── Step 2: Get flat policy list via customer-search ──────────────────────────
        $policies = [];

        if ($customerCode) {
            $response = $api->getPolicies($customerCode, 'client_code');
            if ($response->successful()) {
                $content  = $response->json('data.content') ?? [];
                $policies = $content[0]['policies'] ?? [];
            }
        }

        if (empty($policies) && $phone) {
            $response = $api->getPolicies($phone, 'phone_number');
            if ($response->successful()) {
                $content  = $response->json('data.content') ?? [];
                $policies = $content[0]['policies'] ?? [];
            }
        }

        if (empty($policies)) {
            Log::info('SyncCustomerPoliciesJob: no policies found', ['customer_id' => $this->customer->id]);
            return;
        }

        // ── Step 3: Deduplicate policy IDs (fleet policies repeat per vehicle) ────────
        $uniquePolicyIds = collect($policies)
            ->pluck('policy_id')
            ->unique()
            ->values();

        Log::info('SyncCustomerPoliciesJob: syncing policies', [
            'customer_id'       => $this->customer->id,
            'unique_policy_ids' => $uniquePolicyIds->count(),
            'total_rows'        => count($policies), // includes fleet duplicates
        ]);

        // ── Step 4: Fetch rich data per policy and sync ───────────────────────────────
        foreach ($uniquePolicyIds as $policyId) {
            try {
                $richResponse = $api->policySearch((string) $policyId);

                if (! $richResponse->successful()) {
                    Log::warning('SyncCustomerPoliciesJob: policy-search failed', [
                        'policy_id' => $policyId,
                        'status'    => $richResponse->status(),
                    ]);
                    continue;
                }

                $richData = $richResponse->json('data.policies.0');

                if (! $richData) {
                    Log::warning('SyncCustomerPoliciesJob: empty policy-search response', [
                        'policy_id' => $policyId,
                    ]);
                    continue;
                }

                $policySync->syncFromGenovaRich($richData, $allProducts, $this->customer);

            } catch (\Exception $e) {
                // Don't let one failed policy kill the whole job
                Log::error('SyncCustomerPoliciesJob: error on policy', [
                    'policy_id' => $policyId,
                    'error'     => $e->getMessage(),
                ]);
            }
        }

        $this->customer->update(['last_synced_at' => now()]);

        Log::info('SyncCustomerPoliciesJob: completed', [
            'customer_id' => $this->customer->id,
        ]);
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
