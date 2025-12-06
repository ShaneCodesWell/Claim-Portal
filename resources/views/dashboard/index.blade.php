<x-layouts.app>
    <style>
        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    {{-- Header --}}
    <div class="px-4 pb-6 space-y-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">

            <div>
                <h1 class="text-2xl font-bold text-gray-800">Claim Dashboard</h1>
                <p class="text-gray-500 text-sm mt-1">
                    Manage your insurance policies and submit new claim requests.
                </p>
            </div>
            @if (session('status'))
                <div class="mt-2 text-green-600">{{ session('status') }}</div>
            @endif

            <div class="flex flex-wrap gap-3">
                <div class="flex items-center gap-3 px-4 py-2 bg-blue-50 border border-blue-100 rounded-full">
                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm text-blue-600">
                        <span class="font-bold text-sm">4</span>
                    </div>
                    <div class="text-sm font-medium text-blue-700">Active Policies</div>
                </div>
                <div class="flex items-center gap-3 px-4 py-2 bg-green-50 border border-green-100 rounded-full">
                    <div
                        class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm text-green-600">
                        <span class="font-bold text-sm">2</span>
                    </div>
                    <div class="text-sm font-medium text-green-700">Pending Claims</div>
                </div>
                <div class="flex items-center gap-3 px-4 py-2 bg-purple-50 border border-purple-100 rounded-full">
                    <div
                        class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm text-purple-600">
                        <span class="font-bold text-sm">5</span>
                    </div>
                    <div class="text-sm font-medium text-purple-700">Processed</div>
                </div>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="w-full md:w-auto">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" id="search-policies" placeholder="Search policies..."
                            class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-full md:w-80 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" />
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <select id="policy-type"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Policy Types</option>
                        <option value="motor">Motor Insurance</option>
                        <option value="home">Home Insurance</option>
                        {{-- <option value="health">Health Insurance</option> --}}
                        <option value="life">Life Insurance</option>
                    </select>

                    <select id="policy-status"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="expired">Expired</option>
                        <option value="pending">Pending Renewal</option>
                    </select>

                    <button id="clear-filters"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        Clear Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Policies Section -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">
                    Your Insurance Policies
                </h2>
            </div>

            @if (count($policies) === 0)
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                        No policies found for this customer.
                    </h3>
                    <p class="text-gray-500 max-w-md mx-auto">
                        Try adjusting your search or filter to find what you're looking for.
                    </p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Policy Details
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Policy No.
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Product
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Premium
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Renewal Date
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="policies-table-body">
                            <!-- Policy rows currently be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- Empty State (initially hidden) -->
                <div id="empty-state" class="hidden p-12 text-center">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-search text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                        No policies found
                    </h3>
                    <p class="text-gray-500 max-w-md mx-auto">
                        Try adjusting your search or filter to find what you're looking for.
                    </p>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Sample policy data
        const policies = [{
                id: 1,
                number: "P-1001-103-2025-0000123",
                type: "motor",
                typeName: "Motor Insurance",
                vehicle: "Toyota Camry 2020",
                coverage: "Comprehensive",
                status: "active",
                statusText: "Active",
                renewalDate: "2023-10-15",
                premium: "$1,200.00",
            },
            {
                id: 2,
                number: "P-1001-203-2025-0000456",
                type: "home",
                typeName: "Happy Home Insurance",
                vehicle: "123 Main Street",
                coverage: "Property Damage",
                status: "active",
                statusText: "Active",
                renewalDate: "2024-03-22",
                premium: "$850.00",
            },
            {
                id: 3,
                number: "P-1001-103-2025-0000456",
                type: "motor",
                typeName: "Motor Insurance",
                vehicle: "Honda Civic 2018",
                coverage: "Third Party",
                status: "pending",
                statusText: "Pending Renewal",
                renewalDate: "2023-09-05",
                premium: "$750.00",
            },
            // {
            //     id: 4,
            //     number: "P-1001-203-2025-0000789",
            //     type: "health",
            //     typeName: "Health Insurance",
            //     vehicle: "Family Plan",
            //     coverage: "Comprehensive",
            //     status: "active",
            //     statusText: "Active",
            //     renewalDate: "2024-01-10",
            //     premium: "$2,400.00",
            // },
        ];

        // Function to render policies in the table
        function renderPolicies(filteredPolicies = policies) {
            const tableBody = document.getElementById("policies-table-body");
            const emptyState = document.getElementById("empty-state");

            // Clear the table
            tableBody.innerHTML = "";

            if (filteredPolicies.length === 0) {
                // Show empty state
                tableBody.classList.add("hidden");
                emptyState.classList.remove("hidden");
                return;
            }

            // Hide empty state
            tableBody.classList.remove("hidden");
            emptyState.classList.add("hidden");

            // Add policies to the table
            filteredPolicies.forEach((policy) => {
                const row = document.createElement("tr");
                row.className = "fade-in";

                // Status badge color
                let statusColor = "bg-green-100 text-green-800";
                if (policy.status === "pending") {
                    statusColor = "bg-yellow-100 text-yellow-800";
                } else if (policy.status === "expired") {
                    statusColor = "bg-red-100 text-red-800";
                }

                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file-contract text-blue-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">${
                                  policy.typeName
                                }</div>
                                <div class="text-sm text-gray-500">${
                                  policy.vehicle
                                }</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">${
                                  policy.number
                                }</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">${
                          policy.coverage
                        }</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">${
                          policy.premium
                        }</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusColor}">
                            ${policy.statusText}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${new Date(policy.renewalDate).toLocaleDateString(
                          "en-US",
                          { year: "numeric", month: "long", day: "numeric" }
                        )}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="relative inline-block text-left">
                            <button onclick="toggleDropdown(${policy.id})" 
                                    id="dropdown-button-${policy.id}"
                                    class="text-gray-700 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-md transition-colors inline-flex items-center">
                                Actions
                                <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            
                            <div id="dropdown-${policy.id}" 
                                class="hidden absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-gray-300 ring-opacity-5 z-10">
                                <div class="py-1">
                                    <button onclick="viewDetails(${policy.id})" 
                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 flex items-center">
                                        <i class="fas fa-eye mr-2"></i>
                                        View Details
                                    </button>
                                    <button onclick="processClaim(${policy.id})" 
                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600 flex items-center">
                                        <i class="fas fa-file-invoice mr-2"></i>
                                        Process Claim
                                    </button>
                                </div>
                            </div>
                        </div>
                    </td>
                `;

                tableBody.appendChild(row);
            });
        }

        // Function to filter policies based on search and filters
        function filterPolicies() {
            const searchTerm = document
                .getElementById("search-policies")
                .value.toLowerCase();
            const policyType = document.getElementById("policy-type").value;
            const policyStatus = document.getElementById("policy-status").value;

            const filteredPolicies = policies.filter((policy) => {
                const matchesSearch =
                    policy.number.toLowerCase().includes(searchTerm) ||
                    policy.typeName.toLowerCase().includes(searchTerm) ||
                    policy.vehicle.toLowerCase().includes(searchTerm);

                const matchesType = !policyType || policy.type === policyType;
                const matchesStatus = !policyStatus || policy.status === policyStatus;

                return matchesSearch && matchesType && matchesStatus;
            });

            renderPolicies(filteredPolicies);
        }

        // Function to process claim - navigates to claim form
        // Function to process claim - navigates to claim form
        function processClaim(policyId) {
            const policy = policies.find((p) => p.id === policyId);

            // Determine which form to navigate to based on policy type
            let routeUrl = '';

            switch (policy.type) {
                case 'motor':
                    routeUrl = '/motor-form';
                    break;
                    // case 'home':
                    //    routeUrl = '/home-form'; // You might want to create this route
                    //    break;
                    // case 'health':
                    //    routeUrl = '/health-form'; // You might want to create this route
                    //    break;
                default:
                    routeUrl = '/claim-form'; // Default fallback
            }

            // Add policy ID as query parameter
            const url = `${routeUrl}?policyId=${policyId}`;

            // Redirect to the appropriate form
            window.location.href = url;

            // Optional: Log for debugging
            console.log(`Redirecting to ${url} for policy: ${policy.number}`);
        }

        // Event listeners for search and filters
        document
            .getElementById("search-policies")
            .addEventListener("input", filterPolicies);
        document
            .getElementById("policy-type")
            .addEventListener("change", filterPolicies);
        document
            .getElementById("policy-status")
            .addEventListener("change", filterPolicies);

        // Clear filters button
        document.getElementById("clear-filters").addEventListener("click", () => {
            document.getElementById("search-policies").value = "";
            document.getElementById("policy-type").value = "";
            document.getElementById("policy-status").value = "";
            renderPolicies();
        });

        // Initial render
        document.addEventListener("DOMContentLoaded", () => {
            renderPolicies();
        });

        function toggleDropdown(policyId) {
            const dropdown = document.getElementById(`dropdown-${policyId}`);

            // Close all other dropdowns first
            document.querySelectorAll('[id^="dropdown-"]').forEach(d => {
                if (d.id !== `dropdown-${policyId}`) {
                    d.classList.add('hidden');
                }
            });

            // Toggle current dropdown
            dropdown.classList.toggle('hidden');
        }

        function viewDetails(policyId) {
            const policy = policies.find(p => p.id === policyId);
            alert(`Viewing details for policy: ${policy.number}`);
            console.log('Policy details:', policy);

            // Close dropdown
            document.getElementById(`dropdown-${policyId}`).classList.add('hidden');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', (event) => {
            if (!event.target.closest('[id^="dropdown-button-"]') &&
                !event.target.closest('[id^="dropdown-"]')) {
                document.querySelectorAll('[id^="dropdown-"]').forEach(d => {
                    d.classList.add('hidden');
                });
            }
        });
    </script>
</x-layouts.app>
