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

        .stat-card {
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px -5px rgba(0, 0, 0, 0.12);
        }

        .policy-row {
            transition: all 0.2s ease;
        }

        .policy-row:hover {
            background-color: #f8fafc;
        }

        .search-input:focus {
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        @keyframes pulse-slow {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.6;
            }
        }

        .animate-pulse-slow {
            animation: pulse-slow 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.2s ease;
        }

        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: white;
            border-radius: 1rem;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
    {{-- Header Section --}}
    <div class="max-w-7xl mx-auto px-6 pb-12 space-y-8">
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

            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-3 px-4 py-2 bg-blue-50 border border-blue-100 rounded-full">
                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm text-blue-600">
                        <span class="font-bold text-sm" id="active-count">0</span>
                    </div>
                    <div class="text-sm font-medium text-blue-700">Active Policies</div>
                </div>
                <div class="flex items-center gap-3 px-4 py-2 bg-green-50 border border-green-100 rounded-full">
                    <div
                        class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm text-green-600">
                        <span class="font-bold text-sm" id="pending-count">0</span>
                    </div>
                    <div class="text-sm font-medium text-green-700">Pending Renewal</div>
                </div>
                <div class="flex items-center gap-3 px-4 py-2 bg-red-50 border border-red-100 rounded-full">
                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm text-red-600">
                        <span class="font-bold text-sm" id="expired-count">0</span>
                    </div>
                    <div class="text-sm font-medium text-red-700">Expired</div>
                </div>
                <button onclick="syncPoliciesInBackground()"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                    <i class="fas fa-sync-alt"></i>
                    Refresh
                </button>
            </div>
        </div>
        {{-- Search and Filter Section --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                {{-- Search Input --}}
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" id="search-policies"
                            placeholder="Search by policy number, product, or vehicle..."
                            class="search-input pl-11 pr-4 py-2.5 border-2 border-gray-200 rounded-lg w-full focus:outline-none focus:border-blue-500 transition-colors text-sm" />
                    </div>
                </div>

                {{-- Filters --}}
                <div class="flex flex-col sm:flex-row gap-3">
                    <select id="policy-type"
                        class="px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-blue-500 transition-colors font-medium text-gray-700 text-sm">
                        <option value="">All Types</option>
                        @if (isset($businessClasses) && count($businessClasses) > 0)
                            @foreach ($businessClasses as $classId => $className)
                                <option value="{{ $className }}">{{ $className }}</option>
                            @endforeach
                        @endif
                    </select>

                    <select id="policy-status"
                        class="px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-blue-500 transition-colors font-medium text-gray-700 text-sm">
                        <option value="">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="expired">Expired</option>
                        <option value="pending">Pending Renewal</option>
                    </select>

                    <button id="clear-filters"
                        class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all font-medium flex items-center gap-2 text-sm">
                        <i class="fas fa-times-circle"></i>
                        Clear
                    </button>
                </div>
            </div>
        </div>

        {{-- Policies Table Section --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-file-contract text-blue-600"></i>
                            Your Insurance Policies
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">Click on any policy to view details or file a claim</p>
                    </div>
                </div>
            </div>

            @if (!isset($policies) || count($policies) === 0)
                {{-- Empty State --}}
                <div id="empty-state" class="p-16 text-center">
                    <div class="w-32 h-32 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-folder-open text-blue-400 text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">
                        No policies found
                    </h3>
                    <p class="text-gray-600 max-w-md mx-auto mb-6">
                        Try adjusting your search or filter criteria, or refresh to sync the latest policies.
                    </p>
                    <button onclick="syncPoliciesInBackground()"
                        class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-medium hover:bg-blue-700 transition-colors inline-flex items-center gap-2">
                        <i class="fas fa-sync-alt"></i>
                        Sync Policies
                    </button>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Policy Details
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Policy Number
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Product
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Renewal Date
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100" id="policies-table-body">
                            {{-- Populated by JavaScript --}}
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Container --}}
                <div id="pagination-container" class="hidden bg-gray-50">
                    {{-- Pagination will be rendered here by JavaScript --}}
                </div>
            @endif
        </div>
    </div>

    {{-- Policy Details Modal --}}
    <div id="policyModal" class="modal">
        <div class="modal-content">
            {{-- Modal Header --}}
            <div class="bg-blue-600 text-white px-6 py-4 rounded-t-xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="bg-white/20 p-2 rounded-lg">
                            <i class="fas fa-file-contract text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold">Policy Details</h3>
                            <p class="text-blue-100 text-sm" id="modal-policy-number"></p>
                        </div>
                    </div>
                    <button onclick="closeModal()" class="text-white hover:text-blue-100 transition-colors">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
            </div>

            {{-- Modal Body --}}
            <div class="p-6 space-y-6">
                {{-- Logo --}}
                <div class="flex items-center justify-center pb-4 border-b border-gray-200">
                    <img src="{{ asset('images/Vanguard.png') }}" alt="Logo" class="w-40 h-12">
                </div>

                {{-- Policy Information --}}
                <div class="space-y-4">
                    <h4 class="font-bold text-gray-900 text-base flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-600"></i>
                        Policy Information
                    </h4>
                    <div class="grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg">
                        <div>
                            <p class="text-xs text-gray-600 mb-1">Business Class</p>
                            <p class="text-sm font-semibold text-gray-900" id="modal-business-class"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 mb-1">Product</p>
                            <p class="text-sm font-semibold text-gray-900" id="modal-product"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 mb-1">Vehicle/Asset</p>
                            <p class="text-sm font-semibold text-gray-900" id="modal-vehicle"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 mb-1">Status</p>
                            <span class="text-sm font-bold" id="modal-status"></span>
                        </div>
                    </div>
                </div>

                {{-- Coverage Period --}}
                <div class="space-y-4">
                    <h4 class="font-bold text-gray-900 text-base flex items-center gap-2">
                        <i class="fas fa-calendar-alt text-blue-600"></i>
                        Coverage Period
                    </h4>
                    <div class="grid grid-cols-3 gap-4 bg-gray-50 p-4 rounded-lg">
                        <div>
                            <p class="text-xs text-gray-600 mb-1">Start Date</p>
                            <p class="text-sm font-semibold text-gray-900" id="modal-start-date"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 mb-1">End Date</p>
                            <p class="text-sm font-semibold text-gray-900" id="modal-end-date"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 mb-1">Renewal Date</p>
                            <p class="text-sm font-semibold text-gray-900" id="modal-renewal-date"></p>
                        </div>
                    </div>
                </div>

                {{-- Customer Information --}}
                <div class="space-y-4">
                    <h4 class="font-bold text-gray-900 text-base flex items-center gap-2">
                        <i class="fas fa-user text-blue-600"></i>
                        Customer Information
                    </h4>
                    <div class="grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg">
                        <div>
                            <p class="text-xs text-gray-600 mb-1">Name</p>
                            <p class="text-sm font-semibold text-gray-900" id="modal-customer-name"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 mb-1">Customer Code</p>
                            <p class="text-sm font-semibold text-gray-900" id="modal-customer-code"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 mb-1">Phone</p>
                            <p class="text-sm font-semibold text-gray-900" id="modal-customer-phone"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 mb-1">Email</p>
                            <p class="text-sm font-semibold text-gray-900" id="modal-customer-email"></p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="px-6 py-4 bg-gray-50 rounded-b-xl border-t border-gray-200 flex justify-end gap-3">
                <button onclick="closeModal()"
                    class="px-5 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                    Close
                </button>
                <button onclick="processClaimFromModal()"
                    class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center gap-2">
                    <i class="fas fa-file-invoice"></i>
                    File Claim
                </button>
            </div>
        </div>
    </div>

    <script>
        let policies = @json($policies).map(policy => {
            let policyClass = (policy.business_class_name || 'Unknown').toLowerCase();
            let policyClassName = policy.business_class_name || 'Unknown Class';
            let policyProduct = policy.product_name || 'Unknown Product';

            if (policyClass.includes('motor')) {
                policyClass = 'motor';
            } else if (policyClass.includes('fire') || policyClass.includes('home')) {
                policyClass = 'home';
            } else if (policyClass.includes('life')) {
                policyClass = 'life';
            } else if (policyClass.includes('health')) {
                policyClass = 'health';
            }

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
                premium: 'N/A',
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

        console.log('Loaded policies:', policies);

        let currentPage = 1;
        const itemsPerPage = 10;
        let currentFilteredPolicies = policies;
        let isSyncing = false;
        let syncInterval = null;
        let currentPolicyId = null;

        function updateStats() {
            const activePolicies = policies.filter(p => p.status === 'active').length;
            const pendingRenewal = policies.filter(p => p.status === 'pending').length;
            const expiredPolicies = policies.filter(p => p.status === 'expired').length;

            document.getElementById('active-count').textContent = activePolicies;
            document.getElementById('pending-count').textContent = pendingRenewal;
            document.getElementById('expired-count').textContent = expiredPolicies;
        }

        async function syncPoliciesInBackground() {
            if (isSyncing) {
                console.log('Sync already in progress, skipping...');
                return;
            }

            isSyncing = true;
            console.log('Starting background policy sync...');
            showSyncIndicator(true);

            try {
                const response = await fetch('/dashboard/sync-policies', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                const data = await response.json();

                if (data.success && data.policies && data.policies.length > 0) {
                    console.log('Sync successful:', data.policies.length, 'policies updated');
                    updatePoliciesData(data.policies);
                    showNotification('Policies updated successfully', 'success');
                } else {
                    console.log('Sync completed but no new data');
                    showNotification('Your policies are up to date', 'info');
                }
            } catch (error) {
                console.error('Background sync failed:', error);
                showNotification('Failed to sync policies', 'error');
            } finally {
                isSyncing = false;
                showSyncIndicator(false);
            }
        }

        function updatePoliciesData(newPolicies) {
            const mappedPolicies = newPolicies.map(policy => {
                let policyClass = (policy.business_class_name || 'Unknown').toLowerCase();
                let policyClassName = policy.business_class_name || 'Unknown Class';
                let policyProduct = policy.product_name || 'Unknown Product';

                if (policyClass.includes('motor')) {
                    policyClass = 'motor';
                } else if (policyClass.includes('fire') || policyClass.includes('home')) {
                    policyClass = 'home';
                } else if (policyClass.includes('life')) {
                    policyClass = 'life';
                } else if (policyClass.includes('health')) {
                    policyClass = 'health';
                }

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
                    premium: 'N/A',
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

            policies.length = 0;
            policies.push(...mappedPolicies);
            updateStats();
            filterPolicies();
        }

        function showSyncIndicator(show) {
            let indicator = document.getElementById('sync-indicator');

            if (show && !indicator) {
                indicator = document.createElement('div');
                indicator.id = 'sync-indicator';
                indicator.className =
                    'fixed top-4 right-4 bg-blue-600 text-white px-6 py-3 rounded-xl shadow-xl flex items-center gap-3 z-50 animate-pulse-slow';
                indicator.innerHTML = `
                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="font-medium">Syncing policies...</span>
                `;
                document.body.appendChild(indicator);
            } else if (!show && indicator) {
                indicator.remove();
            }
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            const colors = {
                success: 'bg-green-600',
                error: 'bg-red-600',
                info: 'bg-blue-600'
            };

            const icons = {
                success: 'fa-check-circle',
                error: 'fa-exclamation-circle',
                info: 'fa-info-circle'
            };

            notification.className =
                `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-xl shadow-xl z-50 flex items-center gap-3 fade-in`;
            notification.innerHTML = `
                <i class="fas ${icons[type]} text-lg"></i>
                <span class="font-medium">${message}</span>
            `;

            document.body.appendChild(notification);
            setTimeout(() => notification.remove(), 3000);
        }

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

        function renderPolicies(filteredPolicies = policies, page = 1) {
            const tableBody = document.getElementById("policies-table-body");
            const emptyState = document.getElementById("empty-state");
            const paginationContainer = document.getElementById("pagination-container");

            currentFilteredPolicies = filteredPolicies;
            currentPage = page;
            tableBody.innerHTML = "";

            if (filteredPolicies.length === 0) {
                tableBody.classList.add("hidden");
                if (emptyState) emptyState.classList.remove("hidden");
                if (paginationContainer) paginationContainer.classList.add("hidden");
                return;
            }

            tableBody.classList.remove("hidden");
            if (emptyState) emptyState.classList.add("hidden");

            const totalPages = Math.ceil(filteredPolicies.length / itemsPerPage);
            const startIndex = (page - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            const paginatedPolicies = filteredPolicies.slice(startIndex, endIndex);

            paginatedPolicies.forEach((policy) => {
                const row = document.createElement("tr");
                row.className = "policy-row fade-in";

                let statusBadge = {
                    active: 'bg-green-100 text-green-700 border border-green-200',
                    pending: 'bg-amber-100 text-amber-700 border border-amber-200',
                    expired: 'bg-red-100 text-red-700 border border-red-200'
                };

                const icon = getPolicyIcon(policy.type);

                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-11 w-11 bg-blue-50 rounded-lg flex items-center justify-center">
                                <i class="fas ${icon} text-blue-600 text-lg"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-semibold text-gray-900">${policy.className}</div>
                                <div class="text-xs text-gray-500 flex items-center gap-1">
                                    <i class="fas fa-car text-xs"></i>
                                    ${policy.vehicle}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-mono font-medium text-gray-900">${policy.number}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">${policy.productName}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full ${statusBadge[policy.status]}">
                            ${policy.statusText}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 font-medium">
                            ${new Date(policy.renewalDate).toLocaleDateString("en-US", { year: "numeric", month: "short", day: "numeric" })}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="relative">
                            <button onclick="toggleDropdown(event, ${policy.id})" 
                                    id="dropdown-button-${policy.id}"
                                    class="text-gray-700 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors inline-flex items-center font-medium">
                                Actions
                                <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            
                            <div id="dropdown-${policy.id}" 
                                class="hidden fixed w-48 rounded-lg shadow-lg bg-white ring-1 ring-gray-200 z-50">
                                <div class="py-1">
                                    <button onclick="viewDetails(${policy.id})" 
                                            class="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 flex items-center transition-colors font-medium">
                                        <i class="fas fa-eye mr-2"></i>
                                        View Details
                                    </button>
                                    <button onclick="processClaim(${policy.id})" 
                                            class="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600 flex items-center transition-colors font-medium">
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

            renderPagination(filteredPolicies.length, page);
        }

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

                const matchesType = !policyType ||
                    policy.type === policyType ||
                    policy.className.toLowerCase() === policyType;

                const matchesStatus = !policyStatus || policy.status === policyStatus;

                return matchesSearch && matchesType && matchesStatus;
            });

            renderPolicies(filteredPolicies, 1);
        }

        function renderPagination(totalItems, currentPage) {
            const paginationContainer = document.getElementById("pagination-container");
            if (!paginationContainer) return;

            const totalPages = Math.ceil(totalItems / itemsPerPage);

            if (totalPages <= 1) {
                paginationContainer.classList.add("hidden");
                return;
            }

            paginationContainer.classList.remove("hidden");

            let paginationHTML = '<div class="flex items-center justify-between px-6 py-4">';
            const startItem = (currentPage - 1) * itemsPerPage + 1;
            const endItem = Math.min(currentPage * itemsPerPage, totalItems);

            paginationHTML += `
                <div class="text-sm text-gray-700 font-medium">
                    Showing <span class="font-bold text-blue-600">${startItem}</span> to <span class="font-bold text-blue-600">${endItem}</span> of <span class="font-bold text-blue-600">${totalItems}</span> policies
                </div>
            `;

            paginationHTML += '<div class="flex gap-2">';
            paginationHTML += `
                <button onclick="changePage(${currentPage - 1})" 
                        ${currentPage === 1 ? 'disabled' : ''}
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border-2 border-gray-200 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                    <i class="fas fa-chevron-left"></i>
                </button>
            `;

            const maxVisiblePages = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
            let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

            if (endPage - startPage < maxVisiblePages - 1) {
                startPage = Math.max(1, endPage - maxVisiblePages + 1);
            }

            for (let i = startPage; i <= endPage; i++) {
                const isActive = i === currentPage;
                paginationHTML += `
                    <button onclick="changePage(${i})" 
                            class="px-4 py-2 text-sm font-medium ${isActive ? 'text-white bg-blue-600' : 'text-gray-700 bg-white hover:bg-gray-50'} border-2 ${isActive ? 'border-blue-600' : 'border-gray-200'} rounded-lg transition-all">
                        ${i}
                    </button>
                `;
            }

            paginationHTML += `
                <button onclick="changePage(${currentPage + 1})" 
                        ${currentPage === totalPages ? 'disabled' : ''}
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border-2 border-gray-200 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                    <i class="fas fa-chevron-right"></i>
                </button>
            `;

            paginationHTML += '</div></div>';
            paginationContainer.innerHTML = paginationHTML;
        }

        function changePage(page) {
            const totalPages = Math.ceil(currentFilteredPolicies.length / itemsPerPage);
            if (page < 1 || page > totalPages) return;

            renderPolicies(currentFilteredPolicies, page);
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        function toggleDropdown(event, policyId) {
            event.stopPropagation();
            const button = document.getElementById(`dropdown-button-${policyId}`);
            const dropdown = document.getElementById(`dropdown-${policyId}`);

            // Close all other dropdowns
            document.querySelectorAll('[id^="dropdown-"]').forEach(d => {
                if (d.id !== `dropdown-${policyId}`) {
                    d.classList.add('hidden');
                }
            });

            // Toggle current dropdown
            const isHidden = dropdown.classList.contains('hidden');

            if (isHidden) {
                // Position the dropdown
                const rect = button.getBoundingClientRect();
                dropdown.style.top = `${rect.bottom + window.scrollY + 4}px`;
                dropdown.style.left = `${rect.right - 192 + window.scrollX}px`; // 192px = w-48
                dropdown.classList.remove('hidden');
            } else {
                dropdown.classList.add('hidden');
            }
        }

        function viewDetails(policyId) {
            const policy = policies.find(p => p.id === policyId);
            if (!policy) return;

            currentPolicyId = policyId;

            // Populate modal
            document.getElementById('modal-policy-number').textContent = policy.number;
            document.getElementById('modal-business-class').textContent = policy.className;
            document.getElementById('modal-product').textContent = policy.productName;
            document.getElementById('modal-vehicle').textContent = policy.vehicle;

            const statusColors = {
                active: 'text-green-600',
                pending: 'text-amber-600',
                expired: 'text-red-600'
            };
            const statusElement = document.getElementById('modal-status');
            statusElement.textContent = policy.statusText;
            statusElement.className = `text-sm font-bold ${statusColors[policy.status]}`;

            document.getElementById('modal-start-date').textContent = new Date(policy.policy_start_date).toLocaleDateString(
                "en-US", {
                    year: "numeric",
                    month: "long",
                    day: "numeric"
                });
            document.getElementById('modal-end-date').textContent = new Date(policy.policy_end_date).toLocaleDateString(
                "en-US", {
                    year: "numeric",
                    month: "long",
                    day: "numeric"
                });
            document.getElementById('modal-renewal-date').textContent = new Date(policy.renewalDate).toLocaleDateString(
                "en-US", {
                    year: "numeric",
                    month: "long",
                    day: "numeric"
                });

            document.getElementById('modal-customer-name').textContent = policy.customer_name;
            document.getElementById('modal-customer-code').textContent = policy.customer_code;
            document.getElementById('modal-customer-phone').textContent = policy.customer_phone;
            document.getElementById('modal-customer-email').textContent = policy.customer_email || 'N/A';

            // Show modal
            document.getElementById('policyModal').classList.add('active');

            // Close dropdown
            document.getElementById(`dropdown-${policyId}`)?.classList.add('hidden');
        }

        function closeModal() {
            document.getElementById('policyModal').classList.remove('active');
            currentPolicyId = null;
        }

        function processClaimFromModal() {
            if (currentPolicyId) {
                closeModal();
                processClaim(currentPolicyId);
            }
        }

        function processClaim(policyId) {
            const policy = policies.find((p) => p.id === policyId);
            if (!policy) {
                showNotification('Policy not found. Please try again.', 'error');
                return;
            }

            const policyType = policy.type ? policy.type.trim().toLowerCase() : '';
            let routeUrl = '/motor-form';

            switch (policyType) {
                case 'motor':
                    routeUrl = '/motor-form';
                    break;
                case 'general accident':
                    routeUrl = '/general-accident-form';
                    break;
                case 'fire':
                    routeUrl = '/fire-form';
                    break;
                case 'bond':
                    routeUrl = '/bond-form';
                    break;
                case 'engineering':
                    routeUrl = '/engineering-form';
                    break;
                case 'liability':
                    routeUrl = '/liability-form';
                    break;
                case 'marine':
                    routeUrl = '/marine-form';
                    break;
                case 'aviation':
                    routeUrl = '/aviation-form';
                    break;
            }

            sessionStorage.setItem('selectedPolicy', JSON.stringify(policy));

            // Close dropdown if open
            document.getElementById(`dropdown-${policyId}`)?.classList.add('hidden');

            window.location.href = `${routeUrl}?policyId=${policyId}`;
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

        // Close modal when clicking outside
        document.getElementById('policyModal')?.addEventListener('click', (event) => {
            if (event.target.id === 'policyModal') {
                closeModal();
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            updateStats();
            renderPolicies();

            document.getElementById("search-policies")?.addEventListener("input", filterPolicies);
            document.getElementById("policy-type")?.addEventListener("change", filterPolicies);
            document.getElementById("policy-status")?.addEventListener("change", filterPolicies);

            document.getElementById("clear-filters")?.addEventListener("click", () => {
                document.getElementById("search-policies").value = "";
                document.getElementById("policy-type").value = "";
                document.getElementById("policy-status").value = "";
                renderPolicies(policies, 1);
            });

            setTimeout(() => {
                syncPoliciesInBackground();
                syncInterval = setInterval(syncPoliciesInBackground, 5 * 60 * 1000);
            }, 2000);
        });

        window.addEventListener('beforeunload', () => {
            if (syncInterval) clearInterval(syncInterval);
        });
    </script>
</x-layouts.app>
