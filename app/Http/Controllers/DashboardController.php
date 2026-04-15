<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Policy;
use App\Services\GenovaApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    protected $api;

    public function __construct(GenovaApiService $api)
    {
        $this->api = $api;
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

            // Load ALL matching customers
            $dbCustomers = Customer::where('phone', $phoneNumber)
                ->orWhere('external_customer_code', $customerCode)
                ->orWhere('external_customer_id', $userId)
                ->get();

            $policies        = [];
            $customerData    = null;
            $businessClasses = [];

            if ($dbCustomers->isNotEmpty()) {

                // FIX: collect all customer IDs
                $customerIds = $dbCustomers->pluck('id');

                // Load ALL policies for ALL matched customers
                $dbPolicies = Policy::whereIn('customer_id', $customerIds)
                    ->orderBy('last_synced_at', 'desc')
                    ->get();

                // Map policies with correct customer relationship
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
                        'vehicle_number'      => $policy->raw_payload['vehicle_number'] ?? null,

                        // customer info per policy
                        'customer_name'       => $customer->name ?? null,
                        'customer_code'       => $customer->external_customer_code ?? null,
                        'customer_phone'      => $customer->phone ?? null,
                        'customer_email'      => $customer->email ?? null,
                    ];
                })->toArray();

                // Business classes derived from local policies
                $businessClasses = $dbPolicies
                    ->whereNotNull('business_class_id')
                    ->unique('business_class_id')
                    ->pluck('business_class_name', 'business_class_id')
                    ->toArray();

                // FIX: handle multiple customers properly
                $primaryCustomer = $dbCustomers->first();

                $customerData = [
                    'name'         => $primaryCustomer->name ?? null,
                    'code'         => $primaryCustomer->external_customer_code ?? null,
                    'phone_number' => $primaryCustomer->phone ?? null,
                    'email'        => $primaryCustomer->email ?? null,
                ];

                Log::info('Loaded from DB instantly', [
                    'customer_count' => $dbCustomers->count(),
                    'policy_count'   => count($policies),
                ]);
            }

            return view('dashboard.index', [
                'name'            => $sessionCustomer['name'] ?? 'Guest',
                'policies'        => $policies,
                'customerData'    => $customerData,
                'businessClasses' => $businessClasses,
                'allProducts'     => [],
                'customer'        => $sessionCustomer,
            ]);

        } catch (\Exception $e) {

            Log::error('Dashboard error: ' . $e->getMessage());

            return view('dashboard.index', [
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

    // Method 1: Background sync called during page load
    private function syncPoliciesFromApi($phoneNumber, $customerCode, $allProducts)
    {
        try {
            // Search by client_code first (gets Moses)
            if ($customerCode) {
                $policiesResponse = $this->api->getPolicies($customerCode, 'client_code');
                $this->processPoliciesResponse($policiesResponse, $phoneNumber, $customerCode, $allProducts, $syncedPoliciesMap);
            }

            // Also search by phone number (gets Samuel too)
            if ($phoneNumber) {
                $policiesResponse = $this->api->getPolicies($phoneNumber, 'phone_number');
                $this->processPoliciesResponse($policiesResponse, $phoneNumber, $customerCode, $allProducts, $syncedPoliciesMap);
            }

            if (! $policiesResponse->successful()) {
                Log::warning('API sync failed', ['status' => $policiesResponse->status()]);
                return;
            }

            $responseData = $policiesResponse->json('data');

            if (! isset($responseData['content']) || ! is_array($responseData['content'])) {
                return;
            }

            foreach ($responseData['content'] as $customerInfo) {
                $matchesPhone = isset($customerInfo['phone_number']) &&
                    $customerInfo['phone_number'] === $phoneNumber;
                $matchesCode = isset($customerInfo['code']) &&
                    $customerCode &&
                    $customerInfo['code'] === $customerCode;

                if ($matchesPhone || $matchesCode) {
                    if (! empty($customerInfo['code'])) {
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

                        if (isset($customerInfo['policies']) && is_array($customerInfo['policies'])) {
                            foreach ($customerInfo['policies'] as $policy) {
                                $productId = $policy['product_id'] ?? null;

                                Policy::updateOrCreate(
                                    ['external_policy_id' => $policy['policy_id']], // use policy_id, not policy_number
                                    [
                                        'customer_id'         => $dbCustomer->id,
                                        'policy_number'       => $policy['policy_number'],
                                        'product_id'          => $productId,
                                        'product_name'        => $allProducts[$productId]['name'] ?? 'Unknown Product',
                                        'business_class_id'   => $allProducts[$productId]['business_class_id'] ?? null,
                                        'business_class_name' => $allProducts[$productId]['business_class_name'] ?? 'Unknown Class',
                                        'start_date'          => $policy['policy_start_date'] ?? $policy['start_date'] ?? null,
                                        'end_date'            => $policy['policy_end_date'] ?? $policy['end_date'] ?? null,
                                        'effective_date'      => $policy['effective_date'] ?? null,
                                        'renewal_date'        => $policy['renewal_date'] ?? null,
                                        'status'              => $policy['status'] ?? null,
                                        'raw_payload'         => $policy,
                                        'last_synced_at'      => now(),
                                    ]
                                );
                            }
                        }
                    }
                    // No break — continue to next customer
                }
            }

            Log::info('API sync completed successfully');

        } catch (\Exception $e) {
            Log::error('API sync error: ' . $e->getMessage());
        }
    }

    // Method 2: AJAX endpoint for background refresh
    public function syncPolicies(Request $request)
    {
        try {
            $phoneNumber  = session('phone_number') ?? session('mobile_no');
            $customerCode = session('customer_code');

            if (! session('user_id') && ! $phoneNumber && ! $customerCode) {
                return response()->json(['success' => false, 'message' => 'Session expired'], 401);
            }

            $businessClassesResponse = $this->api->getBusinessClasses($phoneNumber);
            $businessClasses         = [];

            if ($businessClassesResponse->successful()) {
                $businessClassesData = $businessClassesResponse->json('data.content');
                $businessClasses     = $this->formatBusinessClasses($businessClassesData);
            }

            $allProducts = [];
            foreach ($businessClasses as $classId => $className) {
                $productsResponse = $this->api->getProductsByClass($classId);

                if ($productsResponse->successful()) {
                    $productsData = $productsResponse->json('data.content');
                    if ($productsData && is_array($productsData)) {
                        foreach ($productsData as $productId => $product) {
                            $allProducts[$productId] = [
                                'id'                  => $product['id'],
                                'name'                => $product['name'],
                                'business_class_id'   => $classId,
                                'business_class_name' => $className,
                            ];
                        }
                    }
                }
            }

            if ($customerCode) {
                $policiesResponse = $this->api->getPolicies($customerCode, 'client_code');
                $this->processPoliciesResponse($policiesResponse, $phoneNumber, $customerCode, $allProducts, $syncedPoliciesMap);
            }

            // Also search by phone number (gets Samuel too)
            if ($phoneNumber) {
                $policiesResponse = $this->api->getPolicies($phoneNumber, 'phone_number');
                $this->processPoliciesResponse($policiesResponse, $phoneNumber, $customerCode, $allProducts, $syncedPoliciesMap);
            }

            // TEMPORARY DEBUG - remove after fixing
            Log::error('Policies API Debug', [
                'status_code' => $policiesResponse->status(),
                'body'        => $policiesResponse->body(),
                'headers'     => $policiesResponse->headers(),
            ]);

            if (! $policiesResponse->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch policies from API',
                    'debug'   => [ // temporary
                        'status' => $policiesResponse->status(),
                        'body'   => $policiesResponse->body(),
                    ],
                ], 500);
            }

            $responseData = $policiesResponse->json('data');

            // ADD DEBUG LOG HERE
            Log::info('API Sync - Raw Response', [
                'customer_count' => count($responseData['content'] ?? []),
                'phone_searched' => $phoneNumber,
                'code_searched'  => $customerCode
            ]);

            // Use associative array to prevent duplicates by policy_number
            $syncedPoliciesMap = [];

            if (isset($responseData['content']) && is_array($responseData['content'])) {
                foreach ($responseData['content'] as $customerInfo) {
                    $matchesPhone = isset($customerInfo['phone_number']) &&
                        $customerInfo['phone_number'] === $phoneNumber;
                    $matchesCode = isset($customerInfo['code']) &&
                        $customerCode &&
                        $customerInfo['code'] === $customerCode;

                    if ($matchesPhone || $matchesCode) {
                        if (! empty($customerInfo['code'])) {
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

                            if (isset($customerInfo['policies']) && is_array($customerInfo['policies'])) {
                                // Group sub-policies by policy_number
                                $groupedByNumber = collect($customerInfo['policies'])
                                    ->groupBy('policy_number');

                                foreach ($groupedByNumber as $policyNumber => $subPolicies) {
                                    $firstPolicy = $subPolicies->first();
                                    $productId   = $firstPolicy['product_id'] ?? null;

                                    $dbPolicy = Policy::updateOrCreate(
                                        [
                                            'policy_number' => $policyNumber,
                                        ],
                                        [
                                            'customer_id'         => $dbCustomer->id,
                                            'insured_name'        => $customerInfo['name'],
                                            'external_policy_id'  => $firstPolicy['policy_id'] ?? null,
                                            'product_id'          => $productId,
                                            'product_name'        => $allProducts[$productId]['name'] ?? 'Unknown Product',
                                            'business_class_id'   => $allProducts[$productId]['business_class_id'] ?? null,
                                            'business_class_name' => $allProducts[$productId]['business_class_name'] ?? 'Unknown Class',
                                            'start_date'          => $firstPolicy['policy_start_date'] ?? null,
                                            'end_date'            => $firstPolicy['policy_end_date'] ?? null,
                                            'effective_date'      => $firstPolicy['effective_date'] ?? null,
                                            'renewal_date'        => $firstPolicy['renewal_date'] ?? null,
                                            'status'              => $firstPolicy['status'] ?? null,
                                            'raw_payload'         => $subPolicies->values()->toArray(), // all sub-policies
                                            'last_synced_at'      => now(),
                                        ]
                                    );

                                    $syncedPoliciesMap[$policyNumber] = [
                                        'policy_id'           => $dbPolicy->external_policy_id,
                                        'policy_number'       => $dbPolicy->policy_number,
                                        'insured_name'        => $dbPolicy->insured_name,
                                        'product_id'          => $dbPolicy->product_id,
                                        'product_name'        => $dbPolicy->product_name,
                                        'business_class_id'   => $dbPolicy->business_class_id,
                                        'business_class_name' => $dbPolicy->business_class_name,
                                        'policy_start_date'   => $dbPolicy->start_date,
                                        'policy_end_date'     => $dbPolicy->end_date,
                                        'renewal_date'        => $dbPolicy->renewal_date,
                                        'effective_date'      => $dbPolicy->effective_date,
                                        'vehicle_number'      => $firstPolicy['vehicle_number'] ?? null,
                                        'customer_name'       => $dbCustomer->name,
                                        'customer_code'       => $dbCustomer->external_customer_code,
                                        'customer_phone'      => $dbCustomer->phone,
                                        'customer_email'      => $dbCustomer->email,
                                    ];
                                }
                            }
                        }
                    }
                }
            }

            // Convert map to array
            $syncedPolicies = array_values($syncedPoliciesMap);

            Log::info('Sync completed', [
                'unique_policies_synced' => count($syncedPolicies),
            ]);

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
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    private function processPoliciesResponse($policiesResponse, $phoneNumber, $customerCode, $allProducts, &$syncedPoliciesMap)
    {
        if (! $policiesResponse->successful()) {
            return;
        }

        $responseData = $policiesResponse->json('data');
        if (! isset($responseData['content'])) {
            return;
        }

        foreach ($responseData['content'] as $customerInfo) {
            $matchesPhone = isset($customerInfo['phone_number']) &&
                $customerInfo['phone_number'] === $phoneNumber;
            $matchesCode = isset($customerInfo['code']) &&
                $customerCode &&
                $customerInfo['code'] === $customerCode;

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

            $groupedByNumber = collect($customerInfo['policies'] ?? [])->groupBy('policy_number');

            foreach ($groupedByNumber as $policyNumber => $subPolicies) {
                if (isset($syncedPoliciesMap[$policyNumber])) {
                    continue;
                }
                // already processed

                $firstPolicy = $subPolicies->first();
                $productId   = $firstPolicy['product_id'] ?? null;

                $dbPolicy = Policy::updateOrCreate(
                    ['policy_number' => $policyNumber],
                    [
                        'customer_id'         => $dbCustomer->id,
                        'insured_name'        => $customerInfo['name'],
                        'external_policy_id'  => $firstPolicy['policy_id'] ?? null,
                        'product_id'          => $productId,
                        'product_name'        => $allProducts[$productId]['name'] ?? 'Unknown Product',
                        'business_class_id'   => $allProducts[$productId]['business_class_id'] ?? null,
                        'business_class_name' => $allProducts[$productId]['business_class_name'] ?? 'Unknown Class',
                        'start_date'          => $firstPolicy['policy_start_date'] ?? null,
                        'end_date'            => $firstPolicy['policy_end_date'] ?? null,
                        'effective_date'      => $firstPolicy['effective_date'] ?? null,
                        'renewal_date'        => $firstPolicy['renewal_date'] ?? null,
                        'status'              => $firstPolicy['status'] ?? null,
                        'raw_payload'         => $subPolicies->values()->toArray(),
                        'last_synced_at'      => now(),
                    ]
                );

                $syncedPoliciesMap[$policyNumber] = [
                    'policy_id'           => $dbPolicy->external_policy_id,
                    'policy_number'       => $dbPolicy->policy_number,
                    'insured_name'        => $dbPolicy->insured_name,
                    'product_id'          => $dbPolicy->product_id,
                    'product_name'        => $dbPolicy->product_name,
                    'business_class_id'   => $dbPolicy->business_class_id,
                    'business_class_name' => $dbPolicy->business_class_name,
                    'policy_start_date'   => $dbPolicy->start_date,
                    'policy_end_date'     => $dbPolicy->end_date,
                    'renewal_date'        => $dbPolicy->renewal_date,
                    'effective_date'      => $dbPolicy->effective_date,
                    'vehicle_number'      => $firstPolicy['vehicle_number'] ?? null,
                    'customer_name'       => $dbCustomer->name,
                    'customer_code'       => $dbCustomer->external_customer_code,
                    'customer_phone'      => $dbCustomer->phone,
                    'customer_email'      => $dbCustomer->email,
                ];
            }
        }
    }

    public function form()
    {
        return view('dashboard.form');
    }

    /**
     * Format business classes data from API response
     */
    private function formatBusinessClasses($businessClassesData)
    {
        $formatted = [];

        if (is_array($businessClassesData)) {
            foreach ($businessClassesData as $class) {
                if (isset($class['id']) && isset($class['name'])) {
                    $formatted[$class['id']] = $class['name'];
                }
            }
        }

        return $formatted;
    }
}
