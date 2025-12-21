<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
            $customer = [
                'name' => session('fullname') ?? session('name'),
                'phone_number' => $phoneNumber,
                'user_id' => session('user_id'),
            ];

            if (!$phoneNumber && !$customerCode) {
                return redirect()->route('login')->with('error', 'Session expired. Please login again.');
            }

            // Fetch business classes first
            $businessClassesResponse = $this->api->getBusinessClasses($phoneNumber);
            $businessClasses = [];

            if ($businessClassesResponse->successful()) {
                $businessClassesData = $businessClassesResponse->json('data.content');
                $businessClasses = $this->formatBusinessClasses($businessClassesData);

                Log::info('Business classes fetched:', ['classes' => $businessClasses]);
            } else {
                Log::warning('Failed to fetch business classes', [
                    'status' => $businessClassesResponse->status(),
                    'response' => $businessClassesResponse->body()
                ]);
            }

            // Fetch all products for all business classes
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
                } else {
                    Log::warning('Failed to fetch products for class', [
                        'class_id' => $classId,
                        'class_name' => $className
                    ]);
                }
            }

            Log::info('Products fetched:', ['total_products' => count($allProducts)]);

            // Fetch customer policies using customer code or phone number
            $identifier = $customerCode ?? $phoneNumber;
            $policiesResponse = $this->api->getPolicies($identifier);

            if ($policiesResponse->successful()) {
                $responseData = $policiesResponse->json('data');

                // Extract policies only for the logged-in customer
                $allPolicies = [];
                $customerData = null;

                if (isset($responseData['content']) && is_array($responseData['content'])) {
                    // Find the customer that matches the logged-in user's phone number
                    foreach ($responseData['content'] as $customerInfo) {
                        // Match by phone number or customer code
                        $matchesPhone = isset($customerInfo['phone_number']) &&
                            $customerInfo['phone_number'] === $phoneNumber;
                        $matchesCode = isset($customerInfo['code']) &&
                            $customerCode &&
                            $customerInfo['code'] === $customerCode;

                        if ($matchesPhone || $matchesCode) {
                            // Store the matched customer data
                            $customerData = $customerInfo;

                            // Update customer code in session if not set
                            if (!$customerCode && isset($customerInfo['code'])) {
                                session(['customer_code' => $customerInfo['code']]);
                            }

                            Log::info('Matched customer found', [
                                'customer_name' => $customerInfo['name'],
                                'customer_code' => $customerInfo['code'],
                                'total_policies' => count($customerInfo['policies'] ?? [])
                            ]);

                            // Extract policies for this customer only
                            if (isset($customerInfo['policies']) && is_array($customerInfo['policies'])) {
                                foreach ($customerInfo['policies'] as $policy) {
                                    // Enrich policy with product and business class information
                                    $productId = $policy['product_id'];

                                    if (isset($allProducts[$productId])) {
                                        $policy['product_name'] = $allProducts[$productId]['name'];
                                        $policy['business_class_id'] = $allProducts[$productId]['business_class_id'];
                                        $policy['business_class_name'] = $allProducts[$productId]['business_class_name'];
                                    } else {
                                        $policy['product_name'] = 'Unknown Product';
                                        $policy['business_class_id'] = null;
                                        $policy['business_class_name'] = 'Unknown Class';

                                        Log::warning('Product not found for policy', [
                                            'policy_id' => $policy['policy_id'],
                                            'product_id' => $productId
                                        ]);
                                    }

                                    // Add customer info to policy
                                    $policy['customer_name'] = $customerInfo['name'];
                                    $policy['customer_code'] = $customerInfo['code'];
                                    $policy['customer_phone'] = $customerInfo['phone_number'];
                                    $policy['customer_email'] = $customerInfo['email'] ?? '';

                                    $allPolicies[] = $policy;
                                }
                            }

                            break; // Stop after finding the matching customer
                        }
                    }

                    if (!$customerData) {
                        Log::warning('No matching customer found in response', [
                            'searched_phone' => $phoneNumber,
                            'searched_code' => $customerCode,
                            'returned_customers' => count($responseData['content'])
                        ]);
                    }
                }

                $policies = $allPolicies;

                Log::info('Policies filtered for logged-in customer', [
                    'total_policies' => count($policies),
                    'customer_name' => $customerData['name'] ?? 'Unknown'
                ]);
            } else {
                $policies = [];
                $customerData = null;

                Log::error('Failed to fetch policies', [
                    'status' => $policiesResponse->status(),
                    'response' => $policiesResponse->json(),
                    'identifier' => $identifier
                ]);
            }

            return view('dashboard.index', [
                'name' => $customer['name'] ?? 'Guest',
                'policies' => $policies,
                'customerData' => $customerData,
                'businessClasses' => $businessClasses,
                'allProducts' => $allProducts,
                'customer' => $customer,
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