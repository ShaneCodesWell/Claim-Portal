<?php
namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Policy;
use App\Services\GlimsApiService;
use App\Services\PolicySyncService;
use Illuminate\Http\Request;

class StaffPolicySearchController extends Controller
{
    public function __construct(
        private GlimsApiService $glims,
        private PolicySyncService $policySync,
    ) {}

    public function index(): \Illuminate\View\View
    {
        return view('staff.policy-search.index', [
            'searchQuery'  => null,
            'searchResult' => null,
            'searchError'  => null,
        ]);
    }

    public function search(Request $request): \Illuminate\View\View
    {
        $request->validate([
            'query' => 'required|string|min:3|max:100',
        ]);

        $query = trim($request->input('query'));

        [$result, $error] = $this->resolve($query);

        return view('staff.policy-search.index', [
            'searchQuery'  => $query,
            'searchResult' => $result,
            'searchError'  => $error,
        ]);
    }

    // ── Resolution ────────────────────────────────────────────────────────────

    /**
     * Route the query to the correct lookup strategy.
     * Returns [result|null, error|null].
     */
    private function resolve(string $query): array
    {
        if ($this->looksLikePhone($query)) {
            return $this->resolveByPhone($query);
        }

        if ($this->looksLikePolicyNumber($query)) {
            return $this->resolveByPolicyNumber($query);
        }

        // Ambiguous — try phone first, fall back to policy number
        [$result] = $this->resolveByPhone($query);
        if ($result) {
            return [$result, null];
        }

        return $this->resolveByPolicyNumber($query);
    }

    private function resolveByPhone(string $phone): array
    {
        $normalised = $this->normalisePhone($phone);
        $rows       = $this->glims->searchCustomerByPhone($normalised);

        // Some GLIMS setups expect the local 0XX format — try both
        if (empty($rows) && str_starts_with($normalised, '233')) {
            $rows = $this->glims->searchCustomerByPhone('0' . substr($normalised, 3));
        }

        if (empty($rows)) {
            return [null, "No customer found with phone number: {$phone}"];
        }

        return [$this->buildResult($rows[0]), null];
    }

    private function resolveByPolicyNumber(string $policyNumber): array
    {
        // Check the local DB first — we may already have the customer linked
        $localPolicy = Policy::where('policy_number', $policyNumber)
            ->with('customer')
            ->first();

        if ($localPolicy?->customer?->external_customer_code) {
            $rows = $this->glims->searchCustomerByCode(
                $localPolicy->customer->external_customer_code
            );

            if (! empty($rows)) {
                return [$this->buildResult($rows[0], $policyNumber), null];
            }
        }

        return [
            null,
            "No policy found for {$policyNumber}. Try searching by the customer's phone number instead.",
        ];
    }

    /**
     * Given a raw GLIMS customer row, find-or-create the local Customer,
     * sync their policies from GLIMS, then return the result bag.
     */
    private function buildResult(array $glimsRow, ?string $filterPolicyNumber = null): array
    {
        $customer     = $this->findOrCreateCustomer($glimsRow);
        $customerCode = $glimsRow['customer_code'] ?? $customer->external_customer_code;

        if ($customerCode) {
            $glimsPolicies = $this->glims->getPoliciesByClientCode((string) $customerCode);

            // Enrich each policy with full vehicle/risk detail before syncing
            foreach ($glimsPolicies as &$policy) {
                $policyNumber = $policy['POLICY_NUMBER'] ?? null;
                if (! $policyNumber) {
                    continue;
                }

                $richRisks = $this->glims->getRisksForPolicy($policyNumber);
                if (! empty($richRisks)) {
                    $policy['risks']    = $richRisks;
                    $policy['is_fleet'] = count($richRisks) > 1;
                }
            }
            unset($policy); // always break the reference after a foreach by-ref

            $this->policySync->syncFromGlimsRich($glimsPolicies, $customer);
        }

        $policies = Policy::where('customer_id', $customer->id)
            ->when(
                $filterPolicyNumber,
                fn($q) => $q->where('policy_number', $filterPolicyNumber)
            )
            ->latest('last_synced_at')
            ->get();

        return [
            'customer' => $customer->fresh(),
            'policies' => $policies,
        ];
    }

    // ── Customer find-or-create ───────────────────────────────────────────────

    private function findOrCreateCustomer(array $glimsRow): Customer
    {
        $customerCode = (string) ($glimsRow['customer_code'] ?? '');
        $phone        = $glimsRow['mobile_number'] ?? null;
        $email        = $glimsRow['email'] ?? null;

        $firstName  = $glimsRow['first_name'] ?? '';
        $otherNames = $glimsRow['other_names'] ?? '';
        $familyName = $glimsRow['family_name'] ?? '';
        $fullName   = trim(implode(' ', array_filter([$firstName, $otherNames, $familyName]))) ?: 'Unknown';

        // Prefer code match, then phone match
        $customer = $customerCode
            ? Customer::where('external_customer_code', $customerCode)->first()
            : null;

        if (! $customer && $phone) {
            $customer = Customer::where('phone', $phone)->first();
        }

        if ($customer) {
            // Refresh existing record with latest GLIMS data
            $this->policySync->refreshCustomerFromGlimsRow($customer, $glimsRow);

            return $customer->fresh();
        }

        // Brand-new customer — record how they entered the system
        return Customer::create([
            'name'                   => $fullName,
            'phone'                  => $phone,
            'email'                  => $email,
            'external_customer_code' => $customerCode ?: null,
            'sources'                => ['glims'],
            'raw_payload'            => [
                'glims' => array_merge($glimsRow, [
                    '_created_via' => 'staff_policy_search',
                ]),
            ],
        ]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /** Ghanaian phone: 10 digits (0XX…) or 12 digits (233XX…) */
    private function looksLikePhone(string $value): bool
    {
        $digits = preg_replace('/\D/', '', $value);

        return in_array(strlen($digits), [10, 12]);
    }

    /** Policy numbers always contain a dash or slash */
    private function looksLikePolicyNumber(string $value): bool
    {
        return str_contains($value, '-') || str_contains($value, '/');
    }

    private function normalisePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (str_starts_with($digits, '0')) {
            return '233' . substr($digits, 1);
        }

        return $digits;
    }
}
