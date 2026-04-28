<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Policy;
use App\Services\GenovaApiService;
use App\Services\PolicySyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    protected GenovaApiService $api;
    protected PolicySyncService $policySync;

    public function __construct(GenovaApiService $api, PolicySyncService $policySync)
    {
        $this->api        = $api;
        $this->policySync = $policySync;
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

            if (! $userId && ! $phoneNumber && ! $customerCode) {
                return redirect()->route('login')
                    ->with('error', 'Session expired. Please login again.');
            }

            $dbCustomers = Customer::where('phone', $phoneNumber)
                ->orWhere('external_customer_code', $customerCode)
                ->orWhere('external_customer_id', $userId)
                ->get();

            $policies        = [];
            $customerData    = null;
            $businessClasses = [];

            if ($dbCustomers->isNotEmpty()) {
                $customerIds = $dbCustomers->pluck('id');

                $dbPolicies = Policy::whereIn('customer_id', $customerIds)
                    ->orderBy('last_synced_at', 'desc')
                    ->get();

                $policies = $dbPolicies->map(function ($policy) use ($dbCustomers) {
                    $customer = $dbCustomers->firstWhere('id', $policy->customer_id);
                    return [
                        'policy_id'           => $policy->external_policy_id,
                        'policy_number'       => $policy->policy_number,
                        'product_id'          => $policy->product_id,
                        'product_name'        => $policy->product_name,
                        'business_class_id'   => $policy->business_class_id,
                        'business_class_name' => $policy->business_class_name,
                        'policy_start_date'   => $policy->start_date,
                        'policy_end_date'     => $policy->end_date,
                        'renewal_date'        => $policy->renewal_date,
                        'effective_date'      => $policy->effective_date,
                        'status'              => $policy->status,
                        'source'              => $policy->source,
                        'vehicle_number'      => $policy->raw_payload['vehicle_number'] ?? null,
                        'customer_name'       => $customer->name ?? null,
                        'customer_code'       => $customer->external_customer_code ?? null,
                        'customer_phone'      => $customer->phone ?? null,
                        'customer_email'      => $customer->email ?? null,
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

            if (! session('user_id') && ! $phoneNumber && ! $customerCode) {
                return response()->json(['success' => false, 'message' => 'Session expired'], 401);
            }

            // Fetch business classes
            $businessClasses         = [];
            $businessClassesResponse = $this->api->getBusinessClasses($phoneNumber);
            if ($businessClassesResponse->successful()) {
                $businessClasses = $this->formatBusinessClasses(
                    $businessClassesResponse->json('data.content')
                );
            }

            // Fetch all products across business classes
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

            $syncedPoliciesMap = [];

            if ($customerCode) {
                $response = $this->api->getPolicies($customerCode, 'client_code');
                $this->processPoliciesResponse($response, $phoneNumber, $customerCode, $allProducts, $syncedPoliciesMap);
            }

            if ($phoneNumber) {
                $response = $this->api->getPolicies($phoneNumber, 'phone_number');
                $this->processPoliciesResponse($response, $phoneNumber, $customerCode, $allProducts, $syncedPoliciesMap);
            }

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

    private function processPoliciesResponse($response, $phoneNumber, $customerCode, $allProducts, &$syncedPoliciesMap): void
    {
        if (! $response->successful()) {
            return;
        }

        $content = $response->json('data.content') ?? [];

        foreach ($content as $customerInfo) {
            $matchesPhone = isset($customerInfo['phone_number']) && $customerInfo['phone_number'] === $phoneNumber;
            $matchesCode  = isset($customerInfo['code']) && $customerCode && $customerInfo['code'] === $customerCode;

            if (! $matchesPhone && ! $matchesCode) {
                continue;
            }

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

            // Delegate to the service — pass the running map to prevent cross-call duplicates
            $policies = $this->policySync->syncFromGenova($customerInfo, $allProducts, $dbCustomer);

            foreach ($policies as $policyNumber => $policyData) {
                if (! isset($syncedPoliciesMap[$policyNumber])) {
                    $syncedPoliciesMap[$policyNumber] = $policyData;
                }
            }
        }
    }

    private function formatBusinessClasses($businessClassesData): array
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
