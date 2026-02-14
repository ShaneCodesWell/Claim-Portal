<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Policy;
use App\Services\GenovaApiService;
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
            // Get phone number and customer info from session
            $phoneNumber = session('phone_number') ?? session('mobile_no');
            $customerCode = session('customer_code');

            // Prepare customer data from session
            $sessionCustomer = [
                'name' => session('fullname') ?? session('name'),
                'phone_number' => $phoneNumber,
                'user_id' => session('user_id'),
            ];

            if (!$phoneNumber && !$customerCode) {
                return redirect()->route('login')->with('error', 'Session expired. Please login again.');
            }

            // STEP 1: Load from database first
            $dbCustomer = Customer::where('phone', $phoneNumber)
                ->orWhere('external_customer_code', $customerCode)
                ->first();

            $policies = [];
            $customerData = null;

            if ($dbCustomer) {
                // Load policies from database
                $dbPolicies = Policy::where('customer_id', $dbCustomer->id)
                    ->orderBy('last_synced_at', 'desc')
                    ->get();

                // Convert database policies to array format expected by view
                $policies = $dbPolicies->map(function($policy) use ($dbCustomer) {
                    return [
                        'policy_id' => $policy->external_policy_id,
                        'policy_number' => $policy->policy_number,
                        'product_id' => $policy->product_id,
                        'product_name' => $policy->product_name,
                        'business_class_id' => $policy->business_class_id,
                        'business_class_name' => $policy->business_class_name,
                        'policy_start_date' => $policy->start_date,
                        'policy_end_date' => $policy->end_date,
                        'renewal_date' => $policy->renewal_date,
                        'effective_date' => $policy->effective_date,
                        'status' => $policy->status,
                        'vehicle_number' => $policy->raw_payload['vehicle_number'] ?? null,
                        'customer_name' => $dbCustomer->name,
                        'customer_code' => $dbCustomer->external_customer_code,
                        'customer_phone' => $dbCustomer->phone,
                        'customer_email' => $dbCustomer->email,
                    ];
                })->toArray();

                $customerData = [
                    'name' => $dbCustomer->name,
                    'code' => $dbCustomer->external_customer_code,
                    'phone_number' => $dbCustomer->phone,
                    'email' => $dbCustomer->email,
                ];

                Log::info('Loaded policies from database', [
                    'customer_id' => $dbCustomer->id,
                    'total_policies' => count($policies)
                ]);
            }

            // STEP 2: Fetch business classes (needed for both DB and API data)
            $businessClassesResponse = $this->api->getBusinessClasses($phoneNumber);
            $businessClasses = [];

            if ($businessClassesResponse->successful()) {
                $businessClassesData = $businessClassesResponse->json('data.content');
                $businessClasses = $this->formatBusinessClasses($businessClassesData);
                Log::info('Business classes fetched:', ['classes' => $businessClasses]);
            }

            // STEP 3: Fetch all products
            $allProducts = [];
            foreach ($businessClasses as $classId => $className) {
                $productsResponse = $this->api->getProductsByClass($classId);

                if ($productsResponse->successful()) {
                    $productsData = $productsResponse->json('data.content');
                    if ($productsData && is_array($productsData)) {
                        foreach ($productsData as $productId => $product) {
                            $allProducts[$productId] = [
                                'id' => $product['id'],
                                'name' => $product['name'],
                                'business_class_id' => $classId,
                                'business_class_name' => $className
                            ];
                        }
                    }
                }
            }

            // STEP 4: Background sync from API (async or queued would be better)
            // This refreshes the data but doesn't block the page load
            $this->syncPoliciesFromApi($phoneNumber, $customerCode, $allProducts);

            return view('dashboard.index', [
                'name' => $sessionCustomer['name'] ?? 'Guest',
                'policies' => $policies,
                'customerData' => $customerData,
                'businessClasses' => $businessClasses,
                'allProducts' => $allProducts,
                'customer' => $sessionCustomer,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Dashboard error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return view('dashboard.index', [
                'name' => session('fullname') ?? session('name') ?? 'Guest',
                'policies' => [],
                'customerData' => null,
                'businessClasses' => [],
                'allProducts' => [],
                'customer' => [
                    'name' => session('fullname') ?? session('name'),
                    'phone_number' => session('phone_number') ?? session('mobile_no'),
                ],
                'error' => 'Unable to load dashboard data. Please try again.'
            ]);
        }
    }

    // Method 1: Background sync called during page load
    private function syncPoliciesFromApi($phoneNumber, $customerCode, $allProducts)
    {
        try {
            $identifier = $customerCode ?? $phoneNumber;
            $policiesResponse = $this->api->getPolicies($identifier);

            if (!$policiesResponse->successful()) {
                Log::warning('API sync failed', ['status' => $policiesResponse->status()]);
                return;
            }

            $responseData = $policiesResponse->json('data');
            
            if (!isset($responseData['content']) || !is_array($responseData['content'])) {
                return;
            }

            foreach ($responseData['content'] as $customerInfo) {
                $matchesPhone = isset($customerInfo['phone_number']) && 
                    $customerInfo['phone_number'] === $phoneNumber;
                $matchesCode = isset($customerInfo['code']) && 
                    $customerCode && 
                    $customerInfo['code'] === $customerCode;

                if ($matchesPhone || $matchesCode) {
                    if (!empty($customerInfo['code'])) {
                        $dbCustomer = Customer::updateOrCreate(
                            ['external_customer_code' => $customerInfo['code']],
                            [
                                'external_customer_id' => session('user_id'),
                                'name' => $customerInfo['name'],
                                'phone' => $customerInfo['phone_number'] ?? $phoneNumber,
                                'email' => $customerInfo['email'] ?? null,
                                'last_synced_at' => now(),
                            ]
                        );

                        if (isset($customerInfo['policies']) && is_array($customerInfo['policies'])) {
                            foreach ($customerInfo['policies'] as $policy) {
                                $productId = $policy['product_id'] ?? null;
                                
                                Policy::updateOrCreate(
                                    [
                                        'customer_id' => $dbCustomer->id,
                                        'policy_number' => $policy['policy_number'],
                                    ],
                                    [
                                        'external_policy_id' => $policy['policy_id'] ?? null,
                                        'product_id' => $productId,
                                        'product_name' => $allProducts[$productId]['name'] ?? 'Unknown Product',
                                        'business_class_id' => $allProducts[$productId]['business_class_id'] ?? null,
                                        'business_class_name' => $allProducts[$productId]['business_class_name'] ?? 'Unknown Class',
                                        
                                        // Map API fields to our DB columns
                                        'start_date' => $policy['policy_start_date'] ?? $policy['start_date'] ?? null,
                                        'end_date' => $policy['policy_end_date'] ?? $policy['end_date'] ?? null,
                                        'effective_date' => $policy['effective_date'] ?? null,
                                        'renewal_date' => $policy['renewal_date'] ?? null,
                                        'status' => $policy['status'] ?? null,
                                        'raw_payload' => $policy,
                                        'last_synced_at' => now(),
                                    ]
                                );
                            }
                        }
                    }
                    break;
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
            $phoneNumber = session('phone_number') ?? session('mobile_no');
            $customerCode = session('customer_code');

            if (!$phoneNumber && !$customerCode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session expired'
                ], 401);
            }

            $businessClassesResponse = $this->api->getBusinessClasses($phoneNumber);
            $businessClasses = [];
            
            if ($businessClassesResponse->successful()) {
                $businessClassesData = $businessClassesResponse->json('data.content');
                $businessClasses = $this->formatBusinessClasses($businessClassesData);
            }

            $allProducts = [];
            foreach ($businessClasses as $classId => $className) {
                $productsResponse = $this->api->getProductsByClass($classId);
                
                if ($productsResponse->successful()) {
                    $productsData = $productsResponse->json('data.content');
                    if ($productsData && is_array($productsData)) {
                        foreach ($productsData as $productId => $product) {
                            $allProducts[$productId] = [
                                'id' => $product['id'],
                                'name' => $product['name'],
                                'business_class_id' => $classId,
                                'business_class_name' => $className
                            ];
                        }
                    }
                }
            }

            $identifier = $customerCode ?? $phoneNumber;
            $policiesResponse = $this->api->getPolicies($identifier);

            if (!$policiesResponse->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch policies from API'
                ], 500);
            }

            $responseData = $policiesResponse->json('data');
            
            // ADD DEBUG LOG HERE
            Log::info('API Sync - Raw Response', [
                'customer_count' => count($responseData['content'] ?? []),
                'phone_searched' => $phoneNumber,
                'code_searched' => $customerCode
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

                    // ADD DEBUG LOG
                    Log::info('Checking customer match', [
                        'customer_name' => $customerInfo['name'] ?? 'unknown',
                        'customer_phone' => $customerInfo['phone_number'] ?? 'none',
                        'customer_code' => $customerInfo['code'] ?? 'none',
                        'matches_phone' => $matchesPhone,
                        'matches_code' => $matchesCode,
                        'policy_count' => count($customerInfo['policies'] ?? [])
                    ]);

                    if ($matchesPhone || $matchesCode) {
                        if (!empty($customerInfo['code'])) {
                            $dbCustomer = Customer::updateOrCreate(
                                ['external_customer_code' => $customerInfo['code']],
                                [
                                    'external_customer_id' => session('user_id'),
                                    'name' => $customerInfo['name'],
                                    'phone' => $customerInfo['phone_number'] ?? $phoneNumber,
                                    'email' => $customerInfo['email'] ?? null,
                                    'last_synced_at' => now(),
                                ]
                            );

                            if (isset($customerInfo['policies']) && is_array($customerInfo['policies'])) {
                                foreach ($customerInfo['policies'] as $policy) {
                                    $policyNumber = $policy['policy_number'] ?? null;
                                    
                                    if (!$policyNumber) {
                                        continue; // Skip if no policy number
                                    }

                                    // Check if we already processed this policy
                                    if (isset($syncedPoliciesMap[$policyNumber])) {
                                        Log::warning('Duplicate policy detected, skipping', [
                                            'policy_number' => $policyNumber
                                        ]);
                                        continue;
                                    }

                                    $productId = $policy['product_id'] ?? null;
                                    
                                    $dbPolicy = Policy::updateOrCreate(
                                        [
                                            'customer_id' => $dbCustomer->id,
                                            'policy_number' => $policyNumber,
                                        ],
                                        [
                                            'external_policy_id' => $policy['policy_id'] ?? null,
                                            'product_id' => $productId,
                                            'product_name' => $allProducts[$productId]['name'] ?? 'Unknown Product',
                                            'business_class_id' => $allProducts[$productId]['business_class_id'] ?? null,
                                            'business_class_name' => $allProducts[$productId]['business_class_name'] ?? 'Unknown Class',
                                            'start_date' => $policy['policy_start_date'] ?? $policy['start_date'] ?? null,
                                            'end_date' => $policy['policy_end_date'] ?? $policy['end_date'] ?? null,
                                            'effective_date' => $policy['effective_date'] ?? null,
                                            'renewal_date' => $policy['renewal_date'] ?? null,
                                            'status' => $policy['status'] ?? null,
                                            'raw_payload' => $policy,
                                            'last_synced_at' => now(),
                                        ]
                                    );

                                    // Store in map using policy_number as key to prevent duplicates
                                    $syncedPoliciesMap[$policyNumber] = [
                                        'policy_id' => $dbPolicy->external_policy_id,
                                        'policy_number' => $dbPolicy->policy_number,
                                        'product_id' => $dbPolicy->product_id,
                                        'product_name' => $dbPolicy->product_name,
                                        'business_class_id' => $dbPolicy->business_class_id,
                                        'business_class_name' => $dbPolicy->business_class_name,
                                        'policy_start_date' => $dbPolicy->start_date,
                                        'policy_end_date' => $dbPolicy->end_date,
                                        'renewal_date' => $dbPolicy->renewal_date,
                                        'effective_date' => $dbPolicy->effective_date,
                                        'vehicle_number' => $policy['vehicle_number'] ?? null,
                                        'customer_name' => $dbCustomer->name,
                                        'customer_code' => $dbCustomer->external_customer_code,
                                        'customer_phone' => $dbCustomer->phone,
                                        'customer_email' => $dbCustomer->email,
                                    ];
                                }
                            }
                        }
                        
                        // IMPORTANT: Break after finding first match to avoid processing same customer multiple times
                        break;
                    }
                }
            }

            // Convert map to array
            $syncedPolicies = array_values($syncedPoliciesMap);

            Log::info('Sync completed', [
                'unique_policies_synced' => count($syncedPolicies)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Policies synced successfully',
                'policies' => $syncedPolicies,
                'synced_at' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Policy sync error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during sync',
                'error' => $e->getMessage()
            ], 500);
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