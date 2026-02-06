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
                    <div class="text-sm font-medium text-green-700">Pending Renewal</div>
                </div>
                <div class="flex items-center gap-3 px-4 py-2 bg-red-50 border border-red-100 rounded-full">
                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm text-red-600">
                        <span class="font-bold text-sm">5</span>
                    </div>
                    <div class="text-sm font-medium text-red-700">Expired</div>
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
                        @if (isset($businessClasses) && count($businessClasses) > 0)
                            @foreach ($businessClasses as $classId => $className)
                                <option value="{{ $className }}">{{ $className }}</option>
                            @endforeach
                        @else
                            <!-- Fallback options if API data isn't available -->
                            <option value="">N/A</option>
                        @endif
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

            @if (!isset($policies) || count($policies) === 0)
                <!-- Empty State (initially hidden) -->
                <div id="empty-state" class="p-12 text-center">
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
                <!-- Pagination Container -->
                <div id="pagination-container" class="hidden">
                    <!-- Pagination will be rendered here by JavaScript -->
                </div>
            @endif
        </div>
    </div>
    <script>
        // Get policies from backend (passed from controller)
        const policies = @json($policies).map(policy => {
            // Use the business class name from API
            let policyClass = (policy.business_class_name || 'Unknown').toLowerCase();
            let policyClassName = policy.business_class_name || 'Unknown Class';
            let policyProduct = policy.product_name || 'Unknown Product';

            // Normalize class names for filtering
            if (policyClass.includes('motor')) {
                policyClass = 'motor';
            } else if (policyClass.includes('fire') || policyClass.includes('home')) {
                policyClass = 'home';
            } else if (policyClass.includes('life')) {
                policyClass = 'life';
            } else if (policyClass.includes('health')) {
                policyClass = 'health';
            }

            // Determine status based on dates
            const today = new Date();
            const endDate = new Date(policy.policy_end_date);
            const daysUntilExpiry = Math.ceil((endDate - today) / (1000 * 60 * 60 * 24));

            let status = 'active';
            let statusText = 'Active';

            if (daysUntilExpiry < 0) {
                status = 'expired';
                statusText = 'Expired';
            } else if (daysUntilExpiry <= 30) {
                status = 'pending';
                statusText = 'Pending Renewal';
            }

            return {
                id: policy.policy_id,
                number: policy.policy_number,
                type: policyClass,
                className: policyClassName,
                productName: policyProduct,
                vehicle: policy.vehicle_number || 'N/A',
                status: status,
                statusText: statusText,
                renewalDate: policy.renewal_date,
                premium: 'N/A', // Premium not in current API response
                policy_start_date: policy.policy_start_date,
                policy_end_date: policy.policy_end_date,
                product_id: policy.product_id,
                business_class_id: policy.business_class_id,
                customer_name: policy.customer_name || '',
                customer_code: policy.customer_code || '',
                customer_phone: policy.customer_phone || '',
                customer_email: policy.customer_email || ''
            };
        });

        console.log('Loaded policies:', policies); // Debug log

        // Calculate stats
        const activePolicies = policies.filter(p => p.status === 'active').length;
        const pendingRenewal = policies.filter(p => p.status === 'pending').length;
        const expiredPolicies = policies.filter(p => p.status === 'expired').length;

        // Pagination state
        let currentPage = 1;
        const itemsPerPage = 10;
        let currentFilteredPolicies = policies;

        // Update stats badges
        document.addEventListener('DOMContentLoaded', () => {
            const statBadges = document.querySelectorAll('.flex.items-center.gap-3.px-4.py-2');
            if (statBadges.length >= 3) {
                statBadges[0].querySelector('.font-bold').textContent = activePolicies;
                statBadges[0].querySelector('.text-sm.font-medium').textContent = 'Active Policies';

                statBadges[1].querySelector('.font-bold').textContent = pendingRenewal;
                statBadges[1].querySelector('.text-sm.font-medium').textContent = 'Pending Renewal';

                statBadges[2].querySelector('.font-bold').textContent = expiredPolicies;
                statBadges[2].querySelector('.text-sm.font-medium').textContent = 'Expired';
            }
        });

        // Function to get icon based on policy class
        function getPolicyIcon(policyType) {
            const iconMap = {
                'motor': 'fa-car',
                'home': 'fa-home',
                'fire': 'fa-fire',
                'health': 'fa-heartbeat',
                'life': 'fa-user-shield',
                'marine': 'fa-ship',
                'aviation': 'fa-plane',
                'engineering': 'fa-tools',
                'bond': 'fa-handshake',
                'liability': 'fa-shield-alt',
                'agriculture': 'fa-tractor'
            };

            return iconMap[policyType] || 'fa-file-contract';
        }

        // Function to render policies in the table
        function renderPolicies(filteredPolicies = policies, page = 1) {
            const tableBody = document.getElementById("policies-table-body");
            const emptyState = document.getElementById("empty-state");
            const paginationContainer = document.getElementById("pagination-container");

            // Store current filtered policies for pagination
            currentFilteredPolicies = filteredPolicies;
            currentPage = page;

            // Clear the table
            tableBody.innerHTML = "";

            if (filteredPolicies.length === 0) {
                // Show empty state
                tableBody.classList.add("hidden");
                if (emptyState) emptyState.classList.remove("hidden");
                if (paginationContainer) paginationContainer.classList.add("hidden");
                return;
            }

            // Hide empty state
            tableBody.classList.remove("hidden");
            if (emptyState) emptyState.classList.add("hidden");

            // Calculate pagination
            const totalPages = Math.ceil(filteredPolicies.length / itemsPerPage);
            const startIndex = (page - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            const paginatedPolicies = filteredPolicies.slice(startIndex, endIndex);

            // Add policies to the table
            paginatedPolicies.forEach((policy) => {
                const row = document.createElement("tr");
                row.className = "fade-in";

                // Status badge color
                let statusColor = "bg-green-100 text-green-800";
                if (policy.status === "pending") {
                    statusColor = "bg-yellow-100 text-yellow-800";
                } else if (policy.status === "expired") {
                    statusColor = "bg-red-100 text-red-800";
                }

                // Icon based on policy type
                const icon = getPolicyIcon(policy.type);

                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas ${icon} text-blue-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">${policy.className}</div>
                                <div class="text-sm text-gray-500">${policy.vehicle}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">${policy.number}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">${policy.productName}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">${policy.premium}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusColor}">
                            ${policy.statusText}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${new Date(policy.renewalDate).toLocaleDateString("en-US", 
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

            // Render pagination controls
            renderPagination(filteredPolicies.length, page);
        }

        // Function to filter policies based on search and filters
        function filterPolicies() {
            const searchTerm = document.getElementById("search-policies").value.toLowerCase();
            const policyType = document.getElementById("policy-type").value;
            const policyStatus = document.getElementById("policy-status").value;

            const filteredPolicies = policies.filter((policy) => {
                const matchesSearch =
                    policy.number.toLowerCase().includes(searchTerm) ||
                    policy.className.toLowerCase().includes(searchTerm) ||
                    policy.productName.toLowerCase().includes(searchTerm) ||
                    policy.vehicle.toLowerCase().includes(searchTerm);

                // Match by either normalized type or full business class name
                const matchesType = !policyType ||
                    policy.type === policyType ||
                    policy.className.toLowerCase() === policyType;

                const matchesStatus = !policyStatus || policy.status === policyStatus;

                return matchesSearch && matchesType && matchesStatus;
            });

            renderPolicies(filteredPolicies, 1); // Reset to page 1 when filtering
        }

        // Function to render pagination controls
        function renderPagination(totalItems, currentPage) {
            const paginationContainer = document.getElementById("pagination-container");

            if (!paginationContainer) return;

            const totalPages = Math.ceil(totalItems / itemsPerPage);

            // Hide pagination if only one page
            if (totalPages <= 1) {
                paginationContainer.classList.add("hidden");
                return;
            }

            paginationContainer.classList.remove("hidden");

            let paginationHTML = '<div class="flex items-center justify-between px-6 py-4 border-t border-gray-200">';

            // Showing X to Y of Z results
            const startItem = (currentPage - 1) * itemsPerPage + 1;
            const endItem = Math.min(currentPage * itemsPerPage, totalItems);

            paginationHTML += `
                <div class="text-sm text-gray-700">
                    Showing <span class="font-medium">${startItem}</span> to <span class="font-medium">${endItem}</span> of <span class="font-medium">${totalItems}</span> policies
                </div>
            `;

            // Pagination buttons
            paginationHTML += '<div class="flex gap-2">';

            // Previous button
            paginationHTML += `
                <button onclick="changePage(${currentPage - 1})" 
                        ${currentPage === 1 ? 'disabled' : ''}
                        class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-chevron-left"></i>
                </button>
            `;

            // Page numbers
            const maxVisiblePages = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
            let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

            if (endPage - startPage < maxVisiblePages - 1) {
                startPage = Math.max(1, endPage - maxVisiblePages + 1);
            }

            // First page
            if (startPage > 1) {
                paginationHTML += `
                    <button onclick="changePage(1)" 
                            class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        1
                    </button>
                `;
                if (startPage > 2) {
                    paginationHTML += '<span class="px-2 py-2 text-gray-500">...</span>';
                }
            }

            // Visible page numbers
            for (let i = startPage; i <= endPage; i++) {
                const isActive = i === currentPage;
                paginationHTML += `
                    <button onclick="changePage(${i})" 
                            class="px-3 py-2 text-sm font-medium ${isActive ? 'text-white bg-blue-600' : 'text-gray-700 bg-white hover:bg-gray-50'} border border-gray-300 rounded-md">
                        ${i}
                    </button>
                `;
            }

            // Last page
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    paginationHTML += '<span class="px-2 py-2 text-gray-500">...</span>';
                }
                paginationHTML += `
                    <button onclick="changePage(${totalPages})" 
                            class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        ${totalPages}
                    </button>
                `;
            }

            // Next button
            paginationHTML += `
                <button onclick="changePage(${currentPage + 1})" 
                        ${currentPage === totalPages ? 'disabled' : ''}
                        class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-chevron-right"></i>
                </button>
            `;

            paginationHTML += '</div></div>';

            paginationContainer.innerHTML = paginationHTML;
        }

        // Function to change page
        function changePage(page) {
            const totalPages = Math.ceil(currentFilteredPolicies.length / itemsPerPage);

            if (page < 1 || page > totalPages) return;

            renderPolicies(currentFilteredPolicies, page);

            // Scroll to top of table
            document.getElementById('policies-table-body').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }

        // Function to process claim - navigates to claim form
        function processClaim(policyId) {
            const policy = policies.find(p => p.id === policyId);
            if (!policy) return alert('Policy not found.');

            // Map policy type directly to routes
            const typeToRoute = {
                'motor': '/motor-form',
                'general accident': '/general-accident-form',
                'fire': '/fire-form',
                'bond': '/bond-form',
                'engineering': '/engineering-form',
                'liability': '/liability-form',
                'marine': '/marine-form',
                'aviation': '/aviation-form',
            };

            const routeUrl = typeToRoute[policy.type] || '/motor-form';

            // Store selected policy
            sessionStorage.setItem('selectedPolicy', JSON.stringify(policy));

            window.location.href = `${routeUrl}?policyId=${policyId}`;
            console.log(`Redirecting to ${routeUrl} for policy: ${policy.number}`);
        }

        function viewDetails(policyId) {
            const policy = policies.find(p => p.id === policyId);

            if (!policy) {
                console.error('Policy not found');
                return;
            }

            // Create a better formatted alert or modal
            const details = `
                === POLICY DETAILS ===

                Policy Number: ${policy.number}
                Business Class: ${policy.className}
                Product: ${policy.productName}
                Vehicle/Asset: ${policy.vehicle}
                Status: ${policy.statusText}

                Start Date: ${policy.policy_start_date}
                End Date: ${policy.policy_end_date}
                Renewal Date: ${new Date(policy.renewalDate).toLocaleDateString()}

                Customer: ${policy.customer_name}
                Customer Code: ${policy.customer_code}
                Phone: ${policy.customer_phone}
                Email: ${policy.customer_email}
                    `.trim();

            alert(details);
            console.log('Policy details:', policy);

            // Close dropdown
            document.getElementById(`dropdown-${policyId}`).classList.add('hidden');
        }

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

        // Event listeners
        document.addEventListener('DOMContentLoaded', () => {
            // Initial render
            renderPolicies();

            // Search and filter listeners
            document.getElementById("search-policies")?.addEventListener("input", filterPolicies);
            document.getElementById("policy-type")?.addEventListener("change", filterPolicies);
            document.getElementById("policy-status")?.addEventListener("change", filterPolicies);

            // Clear filters button
            document.getElementById("clear-filters")?.addEventListener("click", () => {
                document.getElementById("search-policies").value = "";
                document.getElementById("policy-type").value = "";
                document.getElementById("policy-status").value = "";
                renderPolicies(policies, 1); // Reset to page 1
            });
        });

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
