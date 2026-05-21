<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Policy;
use App\Services\GenovaApiService;
use App\Services\GlimsService;
use App\Services\GlimsSyncService;
use App\Services\PolicySyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    protected GenovaApiService $api;
    protected PolicySyncService $policySync;
    protected GlimsService $glimsService;
    protected GlimsSyncService $glimsSyncService;

    public function __construct(
        GenovaApiService $api,
        PolicySyncService $policySync,
        GlimsService $glimsService,
        GlimsSyncService $glimsSyncService
    ) {
        $this->api              = $api;
        $this->policySync       = $policySync;
        $this->glimsService     = $glimsService;
        $this->glimsSyncService = $glimsSyncService;
    }

    public function index()
    {
        try {
            $phoneNumber  = session('phone_number') ?? session('mobile_no');
            $customerCode = session('customer_code');
            $userId       = session('user_id');

            $sessionCustomer = [
                'name'         => session('fullname') ?? session('name'),
                'phone_number' => $phoneNumber,
                'user_id'      => $userId,
            ];

            Log::debug('Dashboard session state', [
                'phone_number'  => session('phone_number'),
                'mobile_no'     => session('mobile_no'),
                'customer_code' => session('customer_code'),
                'user_id'       => session('user_id'),
                'auth_source'   => session('auth_source'),
            ]);

            if (! $userId && ! $phoneNumber && ! $customerCode) {
                return redirect()->route('login')
                    ->with('error', 'Session expired. Please login again.');
            }

            $dbCustomers = Customer::where(function ($q) use ($phoneNumber, $customerCode, $userId) {
                $matched = false;

                if ($phoneNumber) {
                    $q->orWhere('phone', $phoneNumber);
                    $matched = true;
                }

                if ($customerCode) {
                    $q->orWhere('external_customer_code', $customerCode);
                    $matched = true;
                }

                if ($userId) {
                    $q->orWhere('external_customer_id', (string) $userId);
                    $matched = true;
                }

                // If nothing to match on, force no results
                if (! $matched) {
                    $q->whereRaw('1 = 0');
                }
            })->get();

            $policies        = [];
            $customerData    = null;
            $businessClasses = [];

            if ($dbCustomers->isNotEmpty()) {
                $customerIds = $dbCustomers->pluck('id');

                $dbPolicies = Policy::whereIn('customer_id', $customerIds)
                    ->orderBy('last_synced_at', 'desc')
                    ->get()
                    ->groupBy(function ($policy) {
                        // Group renewal chains by their base policy identity:
                        // P-203-1101-2025-013572 → "203-1101-013572" (product-branch-serial, drop year)
                        // This collapses all yearly renewals of the same policy into one group
                        if (preg_match('/^P-(\d+)-(\d+)-\d{4}-(\d+)$/', $policy->policy_number, $m)) {
                            return $m[1] . '-' . $m[2] . '-' . $m[3]; // product-branch-serial
                        }
                        return $policy->policy_number; // fallback: treat as unique
                    })
                    ->map(function ($group) {
                        // Prefer active, fallback to most recently synced
                        return $group->firstWhere('status', 'active') ?? $group->first();
                    })
                    ->values();

                $policies = $dbPolicies->map(function ($policy) use ($dbCustomers) {
                    $customer   = $dbCustomers->firstWhere('id', $policy->customer_id);
                    $rawPayload = is_array($policy->raw_payload) ? $policy->raw_payload : [];
                    $isGlims    = $policy->source === 'glims';

                    return [
                        // ── Core fields (both sources) ──────────────────────
                        'policy_id'            => $policy->external_policy_id,
                        'policy_number'        => $policy->policy_number,
                        'product_id'           => $policy->product_id ?? $rawPayload['POLICY_PRODUCT_ID'] ?? null,
                        'product_name'         => $policy->product_name ?? $rawPayload['POLICY_PRODUCT_NAME'] ?? 'Unknown Product',
                        'business_class_id'    => $policy->business_class_id ?? $rawPayload['POLICY_LOB_ID'] ?? null,
                        'business_class_name'  => $policy->business_class_name ?? $rawPayload['POLICY_LOB_NAME'] ?? 'Unknown Class',
                        'policy_start_date'    => $policy->start_date,
                        'policy_end_date'      => $policy->end_date,
                        'renewal_date'         => $policy->renewal_date,
                        'effective_date'       => $policy->effective_date,
                        'status'               => $policy->status,
                        'source'               => $policy->source,
                        'vehicle_number'       => $rawPayload['vehicle_number'] ?? null,
                        'customer_name'        => $customer->name ?? null,
                        'customer_code'        => $customer->external_customer_code ?? null,
                        'customer_phone'       => $customer->phone ?? null,
                        'customer_email'       => $customer->email ?? null,

                        // ── GLIMS-only readable fields ───────────────────────
                        // These are null for Genova policies — safe to pass
                        // through to the view and conditionally display.
                        'lob_name'             => $isGlims ? ($rawPayload['POLICY_LOB_NAME'] ?? null) : null,
                        'branch_name'          => $isGlims ? ($rawPayload['POLICY_BRANCH_NAME'] ?? null) : null,
                        'agent_name'           => $isGlims ? ($rawPayload['POLICY_AGENT_NAME'] ?? null) : null,
                        'policy_status'        => $isGlims ? ($rawPayload['POLICY_STATUS'] ?? null) : null,
                        'policy_currency'      => $isGlims ? ($rawPayload['POLICY_CURRENCY'] ?? null) : null,
                        'policy_total_premium' => $isGlims ? ($rawPayload['POLICY_TOTAL_PREMIUM'] ?? null) : null,
                    ];
                })->toArray();

                $businessClasses = $dbPolicies
                    ->whereNotNull('business_class_id')
                    ->unique('business_class_id')
                    ->pluck('business_class_name', 'business_class_id')
                    ->toArray();

                $primaryCustomer = $dbCustomers->first();
                $customerData    = [
                    'name'         => $primaryCustomer->name ?? null,
                    'code'         => $primaryCustomer->external_customer_code ?? null,
                    'phone_number' => $primaryCustomer->phone ?? null,
                    'email'        => $primaryCustomer->email ?? null,
                ];
            }

            return view('customer.dashboard.index', [
                'name'            => $sessionCustomer['name'] ?? 'Guest',
                'policies'        => $policies,
                'customerData'    => $customerData,
                'businessClasses' => $businessClasses,
                'allProducts'     => [],
                'customer'        => $sessionCustomer,
            ]);

        } catch (\Exception $e) {
            Log::error('Dashboard error: ' . $e->getMessage());
            return view('customer.dashboard.index', [
                'name'            => session('fullname') ?? session('name') ?? 'Guest',
                'policies'        => [],
                'customerData'    => null,
                'businessClasses' => [],
                'allProducts'     => [],
                'customer'        => [
                    'name'         => session('fullname') ?? session('name'),
                    'phone_number' => session('phone_number') ?? session('mobile_no'),
                ],
                'error'           => 'Unable to load dashboard data. Please try again.',
            ]);
        }
    }

    public function syncPolicies(Request $request)
    {
        try {
            $phoneNumber  = session('phone_number') ?? session('mobile_no');
            $customerCode = session('customer_code');
            $userId       = session('user_id');

            if (! $userId && ! $phoneNumber && ! $customerCode) {
                return response()->json(['success' => false, 'message' => 'Session expired'], 401);
            }

            // ── 1. Build product catalogue (Genova) ───────────────────────────
            $businessClasses         = [];
            $businessClassesResponse = $this->api->getBusinessClasses($phoneNumber);
            if ($businessClassesResponse->successful()) {
                $businessClasses = $this->formatBusinessClasses(
                    $businessClassesResponse->json('data.content')
                );
            }

            $allProducts = [];
            foreach ($businessClasses as $classId => $className) {
                $productsResponse = $this->api->getProductsByClass($classId);
                if ($productsResponse->successful()) {
                    $productsData = $productsResponse->json('data.content');
                    if ($productsData && is_array($productsData)) {
                        foreach ($productsData as $product) {
                            $allProducts[$product['id']] = [
                                'id'                  => $product['id'],
                                'name'                => $product['name'],
                                'business_class_id'   => $classId,
                                'business_class_name' => $className,
                            ];
                        }
                    }
                }
            }

            // ── 2. Genova policy sync ─────────────────────────────────────────
            $syncedPoliciesMap = [];

            if ($customerCode) {
                $response = $this->api->getPolicies($customerCode, 'client_code');
                $this->processPoliciesResponse($response, $phoneNumber, $customerCode, $allProducts, $syncedPoliciesMap);
            }

            if ($phoneNumber) {
                $response = $this->api->getPolicies($phoneNumber, 'phone_number');
                $this->processPoliciesResponse($response, $phoneNumber, $customerCode, $allProducts, $syncedPoliciesMap);
            }

            // ── 3. Fallback: policy-number login — use the policy number itself ──────
            if (empty($syncedPoliciesMap) && $userId && ! $phoneNumber && ! $customerCode) {
                Log::info('Sync: falling back to policy_number lookup', [
                    'user_id'    => $userId,
                    'login_type' => session('login_type'),
                    'username'   => session('username'),
                ]);

                $this->processFallbackByPolicyNumber(
                    session('username'), // the policy number they logged in with
                    $userId,
                    $allProducts,
                    $syncedPoliciesMap,
                    $phoneNumber, // passed by reference via the session backfill below
                    $customerCode
                );
            }

            // ── 4. GLIMS sync ─────────────────────────────────────────────────
            $this->syncGlimsForCurrentSession($phoneNumber, $customerCode, $syncedPoliciesMap);

            $syncedPolicies = array_values($syncedPoliciesMap);

            Log::info('Sync completed', ['unique_policies_synced' => count($syncedPolicies)]);

            return response()->json([
                'success'   => true,
                'message'   => 'Policies synced successfully',
                'policies'  => $syncedPolicies,
                'synced_at' => now()->toISOString(),
            ]);

        } catch (\Exception $e) {
            Log::error('Policy sync error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during sync',
            ], 500);
        }
    }

    private function syncGlimsForCurrentSession(
        ?string $phoneNumber,
        ?string $customerCode,
        array &$syncedPoliciesMap
    ): void {
        try {
            if (! $this->glimsService->isConnected()) {
                Log::info('GLIMS sync skipped — not reachable (off-premise)');
                return;
            }

            // ── GUARD: bail out if we still have nothing to match on.
            //    Without this, where('phone', null) silently matches the first
            //    customer in the table whose phone IS NULL — a completely wrong record.
            if (! $phoneNumber && ! $customerCode) {
                Log::info('GLIMS sync skipped — no phone or customer code in session after resolution');
                return;
            }

            // Strict sequential lookup — never combine into one orWhere with nulls.
            $dbCustomer = null;

            if ($customerCode) {
                $dbCustomer = Customer::where('external_customer_code', $customerCode)->first();
            }

            if (! $dbCustomer && $phoneNumber) {
                $dbCustomer = Customer::where('phone', $phoneNumber)->first();
            }

            if (! $dbCustomer || ! $dbCustomer->external_customer_code) {
                Log::info('GLIMS sync skipped — no customer code available');
                return;
            }

            $glimsCustomer = $this->glimsService->customerVerification(
                $dbCustomer->external_customer_code,
                'client_code'
            );

            if (! $glimsCustomer) {
                Log::info('GLIMS sync skipped — customer not found in VACLIVE', [
                    'customer_code' => $dbCustomer->external_customer_code,
                ]);
                return;
            }

            $glimsPolicies = $this->glimsSyncService->syncCustomer($glimsCustomer);

            foreach ($glimsPolicies as $policyNumber => $policyData) {
                if (! isset($syncedPoliciesMap[$policyNumber])) {
                    $syncedPoliciesMap[$policyNumber] = $policyData;
                }
            }

            Log::info('GLIMS sync completed for session customer', [
                'customer_code'   => $dbCustomer->external_customer_code,
                'policies_synced' => count($glimsPolicies),
            ]);

        } catch (\Exception $e) {
            Log::error('GLIMS session sync error: ' . $e->getMessage());
        }
    }

    private function processPoliciesResponse($response, $phoneNumber, $customerCode, $allProducts, &$syncedPoliciesMap): void
    {
        if (! $response->successful()) {
            return;
        }

        $content = $response->json('data.content') ?? [];

        foreach ($content as $customerInfo) {
            // $matchesPhone = isset($customerInfo['phone_number']) && $customerInfo['phone_number'] === $phoneNumber;
            // $matchesCode  = isset($customerInfo['code']) && $customerCode && $customerInfo['code'] === $customerCode;

            // if (! $matchesPhone && ! $matchesCode) {
            //     continue;
            // }

            if (empty($customerInfo['code'])) {
                continue;
            }

            $dbCustomer = Customer::updateOrCreate(
                ['external_customer_code' => $customerInfo['code']],
                [
                    'external_customer_id' => session('user_id'),
                    'name'                 => $customerInfo['name'],
                    'phone'                => $customerInfo['phone_number'] ?? $phoneNumber,
                    'email'                => $customerInfo['email'] ?? null,
                    'last_synced_at'       => now(),
                ]
            );

            // ── Merge 'genova' into sources without overwriting existing ones ──
            $sources = $dbCustomer->sources ?? [];
            if (! in_array('genova', $sources)) {
                $sources[] = 'genova';
                $dbCustomer->update(['sources' => $sources]);
            }

            // Delegate to the service — pass the running map to prevent cross-call duplicates
            $policies = $this->policySync->syncFromGenova($customerInfo, $allProducts, $dbCustomer);

            foreach ($policies as $policyNumber => $policyData) {
                if (! isset($syncedPoliciesMap[$policyNumber])) {
                    $syncedPoliciesMap[$policyNumber] = $policyData;
                }
            }
        }
    }

    private function processFallbackByPolicyNumber(
        ?string $policyNumber,
        int | string $userId,
        array $allProducts,
        array &$syncedPoliciesMap,
        ? string &$phoneNumber,
        ? string &$customerCode
    ) : void {
        if (! $policyNumber) {
            Log::info('Sync fallback: no policy number in session, cannot resolve');
            return;
        }

        // Ask Genova for all customers associated with this user_id
        $response = $this->api->getPolicies($userId, 'customer_id');

        if (! $response->successful()) {
            Log::warning('Sync fallback: customer_id lookup failed', ['user_id' => $userId]);
            return;
        }

        $content = $response->json('data.content') ?? [];

        // Find the specific customer entry that owns this policy number.
        // Do NOT match by phone/code — both are null. Match by the policy itself.
        $matchedEntry = null;
        foreach ($content as $customerInfo) {
            $policyNumbers = collect($customerInfo['policies'] ?? [])->pluck('policy_number')->toArray();
            if (in_array($policyNumber, $policyNumbers)) {
                $matchedEntry = $customerInfo;
                break;
            }
        }

        if (! $matchedEntry) {
            Log::warning('Sync fallback: policy number not found in Genova response', [
                'policy_number' => $policyNumber,
                'user_id'       => $userId,
            ]);
            return;
        }

        if (empty($matchedEntry['code'])) {
            Log::warning('Sync fallback: matched entry has no customer code', [
                'policy_number' => $policyNumber,
            ]);
            return;
        }

        Log::info('Sync fallback: matched customer entry by policy number', [
            'policy_number' => $policyNumber,
            'customer_code' => $matchedEntry['code'],
            'customer_name' => $matchedEntry['name'] ?? null,
            'phone'         => $matchedEntry['phone_number'] ?? null,
        ]);

        // Upsert by customer code — never by external_customer_id alone
        $dbCustomer = Customer::updateOrCreate(
            ['external_customer_code' => $matchedEntry['code']],
            [
                // Only set external_customer_id if the record is genuinely new.
                // Avoid overwriting another customer's user_id.
                'name'           => $matchedEntry['name'],
                'phone'          => $matchedEntry['phone_number'] ?? null,
                'email'          => $matchedEntry['email'] ?? null,
                'last_synced_at' => now(),
            ]
        );

        // Backfill session with the real identifiers
        $phoneNumber  = $matchedEntry['phone_number'] ?? null;
        $customerCode = $matchedEntry['code'];

        session([
            'phone_number'  => $phoneNumber,
            'mobile_no'     => $phoneNumber,
            'customer_code' => $customerCode,
        ]);

        // Now sync policies for this confirmed customer
        $policies = $this->policySync->syncFromGenova($matchedEntry, $allProducts, $dbCustomer);

        foreach ($policies as $number => $data) {
            if (! isset($syncedPoliciesMap[$number])) {
                $syncedPoliciesMap[$number] = $data;
            }
        }
    }

    private function formatBusinessClasses($businessClassesData) : array
    {
        $formatted = [];
        if (is_array($businessClassesData)) {
            foreach ($businessClassesData as $class) {
                if (isset($class['id'], $class['name'])) {
                    $formatted[$class['id']] = $class['name'];
                }
            }
        }
        return $formatted;
    }
}
