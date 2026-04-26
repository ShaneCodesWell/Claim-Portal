<x-layouts.app>
    @php
        $phoneNumber = session('phone_number') ?? session('mobile_no');
        $customerCode = session('customer_code');

        $nudgeCustomer = \App\Models\Customer::where('phone', $phoneNumber)
            ->orWhere('external_customer_code', $customerCode)
            ->orWhere('external_customer_id', session('user_id'))
            ->first();

        $showNudge = $nudgeCustomer && is_null($nudgeCustomer->local_password) && !session('nudge_dismissed');
    @endphp

    {{-- Nudge Banner (preserved) --}}
    @if ($showNudge)
        <div id="passwordNudge"
            class="mx-4 mt-4 mb-4 sm:mx-6 flex items-start justify-between gap-3 p-3 bg-amber-50 border border-amber-200 rounded-xl shadow-sm">
            <div class="flex items-start gap-2">
                <i class="fas fa-exclamation-triangle text-amber-500 text-sm mt-0.5"></i>
                <p class="text-xs text-amber-800">
                    <span class="font-semibold">Set up a local password</span> to access the portal even when the
                    verification service is down.
                    <a href="{{ route('password.setup') }}" class="underline font-semibold hover:text-amber-900 ml-1">Set
                        it up now →</a>
                </p>
            </div>
            <button onclick="dismissNudge()" class="text-amber-400 hover:text-amber-600 shrink-0 mt-0.5 transition-colors"
                aria-label="Dismiss">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <script>
            function dismissNudge() {
                document.getElementById('passwordNudge').style.display = 'none';
                fetch('{{ route('nudge.dismiss') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
            }
        </script>
    @endif

    {{-- Flash Messages --}}
    @if (session('success'))
        <div
            class="mx-4 mt-4 mb-4 sm:mx-6 p-3 bg-green-50 border border-green-200 rounded-xl text-xs text-green-800 font-medium">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div
            class="mx-4 mt-4 mb-4 sm:mx-6 p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-800 font-medium">
            {{ session('error') }}
        </div>
    @endif

    {{-- Main Container --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
        {{-- Header with Stats --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Claim Dashboard</h1>
                <p class="text-gray-500 text-sm mt-1">
                    Manage your insurance policies and submit new claim requests.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-3 px-4 py-2 bg-blue-50 border border-blue-100 rounded-full">
                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm text-blue-600">
                        <span class="font-bold text-sm" id="active-count">0</span>
                    </div>
                    <div class="text-sm font-medium text-blue-700">Active Policies</div>
                </div>
                <div class="flex items-center gap-3 px-4 py-2 bg-red-50 border border-red-100 rounded-full">
                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm text-red-600">
                        <span class="font-bold text-sm" id="expired-count">0</span>
                    </div>
                    <div class="text-sm font-medium text-red-700">Expired</div>
                </div>
            </div>
        </div>

        {{-- Search and Filter Section --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" id="search-policies"
                            placeholder="Search by policy number, product, or vehicle..."
                            class="pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm bg-white" />
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <select id="policy-type"
                        class="px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-700 text-sm">
                        <option value="">All Types</option>
                        @if (isset($businessClasses) && count($businessClasses) > 0)
                            @foreach ($businessClasses as $classId => $className)
                                <option value="{{ $className }}">{{ $className }}</option>
                            @endforeach
                        @endif
                    </select>

                    <select id="policy-status"
                        class="px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-700 text-sm">
                        <option value="">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="expired">Expired</option>
                    </select>

                    <button id="clear-filters"
                        class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition flex items-center gap-2 text-sm font-medium">
                        <i class="fas fa-times-circle"></i> Clear
                    </button>
                </div>
            </div>
        </div>

        {{-- Policies Table Section --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-file-contract text-blue-500"></i>
                            Your Insurance Policies
                        </h2>
                        <p class="text-xs text-gray-500 mt-0.5">Click on any policy to view details or file a claim</p>
                    </div>

                    <div>
                        <button onclick="syncPoliciesInBackground()"
                            class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-sm transition flex items-center gap-2">
                            <i class="fas fa-sync-alt"></i> Sync Policies
                        </button>
                    </div>
                </div>
            </div>

            @if (!isset($policies) || count($policies) === 0)
                <div id="empty-state" class="p-16 text-center">
                    <div class="w-32 h-32 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-folder-open text-blue-400 text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">No policies found</h3>
                    <p class="text-gray-500 max-w-md mx-auto mb-6">
                        Try adjusting your search or filter criteria, or refresh to sync the latest policies.
                    </p>
                    <button onclick="location.reload()"
                        class="bg-blue-600 text-white px-6 py-2.5 rounded-xl font-medium hover:bg-blue-700 transition inline-flex items-center gap-2">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Policy Details</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Policy Number</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Insured Name</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Product</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Renewal Date</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100" id="policies-table-body"></tbody>
                    </table>
                </div>
                <div id="pagination-container" class="hidden bg-gray-50 border-t border-gray-100"></div>
            @endif
        </div>
    </div>

    {{-- Policy Details Modal (styled with new theme) --}}
    <x-policy-details-modal />

    <script>
        // ==================== EXISTING JAVASCRIPT LOGIC ====================
        let policies = @json($policies).map(mapPolicy);

        // And in updatePoliciesData
        function updatePoliciesData(newPolicies) {
            const mappedPolicies = newPolicies.map(mapPolicy);
            policies.length = 0;
            policies.push(...mappedPolicies);
            updateStats();
            filterPolicies();
        }

        console.log('Loaded policies:', policies);

        let currentPage = 1;
        const itemsPerPage = 10;
        let currentFilteredPolicies = policies;
        let isSyncing = false;
        let syncInterval = null;
        let currentPolicyId = null;

        function updateStats() {
            const activePolicies = policies.filter(p => p.status === 'active').length;
            const expiredPolicies = policies.filter(p => p.status === 'expired').length;
            document.getElementById('active-count').textContent = activePolicies;
            document.getElementById('expired-count').textContent = expiredPolicies;
        }

        async function syncPoliciesInBackground() {
            if (isSyncing) return;
            isSyncing = true;
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
                    updatePoliciesData(data.policies);
                    sessionStorage.setItem('lastPolicySync', Date.now().toString());
                    showNotification('Policies updated successfully', 'success');
                } else {
                    sessionStorage.setItem('lastPolicySync', Date.now().toString());
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

        function mapPolicy(policy) {
            const rawClass = (policy.business_class_name || 'Unknown').toLowerCase().trim();

            return {
                id: policy.policy_id,
                number: policy.policy_number,
                type: rawClass,
                className: policy.business_class_name || 'Unknown Class',
                productName: policy.product_name || 'Unknown Product',
                vehicle: policy.vehicle_number || 'N/A',
                status: (() => {
                    const daysUntilExpiry = Math.ceil((new Date(policy.policy_end_date) - new Date()) / (1000 * 60 *
                        60 * 24));
                    return daysUntilExpiry < 0 ? 'expired' : 'active';
                })(),
                statusText: (() => {
                    const daysUntilExpiry = Math.ceil((new Date(policy.policy_end_date) - new Date()) / (1000 * 60 *
                        60 * 24));
                    return daysUntilExpiry < 0 ? 'Expired' : 'Active';
                })(),
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
            } else if (!show && indicator) indicator.remove();
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
            notification.innerHTML =
                `<i class="fas ${icons[type]} text-lg"></i><span class="font-medium">${message}</span>`;
            document.body.appendChild(notification);
            setTimeout(() => notification.remove(), 3000);
        }

        function getPolicyIcon(policyType) {
            const iconMap = {
                'motor': 'fa-car',
                'general accident': 'fa-shield-alt',
                'fire': 'fa-fire',
                'bond': 'fa-handshake',
                'engineering': 'fa-tools',
                'liability': 'fa-balance-scale',
                'marine': 'fa-ship',
                'aviation': 'fa-plane',
            };
            return iconMap[policyType] ?? 'fa-file-contract';
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
                row.className = "hover:bg-gray-50 transition";
                const icon = getPolicyIcon(policy.type);
                const statusBadge = policy.status === 'active' ?
                    'bg-green-100 text-green-700 border border-green-200' :
                    'bg-red-100 text-red-700 border border-red-200';

                row.innerHTML = `
                    <td class="px-6 py-3">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-11 w-11 bg-blue-50 rounded-xl flex items-center justify-center">
                                <i class="fas ${icon} text-blue-600 text-lg"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-semibold text-gray-900">${policy.className}</div>
                                <div class="text-xs text-gray-500 flex items-center gap-1">
                                    <i class="fas fa-car text-xs"></i> ${policy.vehicle}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-3"><div class="text-xs font-mono font-medium text-gray-900">${policy.number}</div></td>
                    <td class="px-6 py-3"><div class="text-xs font-medium text-gray-900">${policy.customer_name}</div></td>
                    <td class="px-6 py-3"><div class="text-xs font-medium text-gray-900">${policy.productName}</div></td>
                    <td class="px-6 py-3"><span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full ${statusBadge}">${policy.statusText}</span></td>
                    <td class="px-6 py-3"><div class="text-xs text-gray-900 font-medium">${new Date(policy.renewalDate).toLocaleDateString("en-US", { year: "numeric", month: "short", day: "numeric" })}</div></td>
                    <td class="px-6 py-3 text-right">
                        <div class="relative">
                            <button onclick="toggleDropdown(event, ${policy.id})" id="dropdown-button-${policy.id}"
                                class="text-gray-700 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors inline-flex items-center font-medium text-sm">
                                Actions <i class="fas fa-chevron-down ml-2 text-xs"></i>
                            </button>
                            <div id="dropdown-${policy.id}" class="hidden fixed w-48 rounded-lg shadow-lg bg-white ring-1 ring-gray-200 z-50">
                                <div class="py-1">
                                    <button onclick="viewDetails(${policy.id})" class="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 flex items-center transition">
                                        <i class="fas fa-eye mr-2"></i> View Details
                                    </button>
                                    <button onclick="processClaim(${policy.id})" class="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600 flex items-center transition">
                                        <i class="fas fa-file-invoice mr-2"></i> Process Claim
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
                const matchesSearch = policy.number.toLowerCase().includes(searchTerm) ||
                    policy.className.toLowerCase().includes(searchTerm) ||
                    policy.productName.toLowerCase().includes(searchTerm) ||
                    policy.vehicle.toLowerCase().includes(searchTerm);
                const matchesType = !policyType || policy.type === policyType || policy.className.toLowerCase() ===
                    policyType;
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
            const startItem = (currentPage - 1) * itemsPerPage + 1;
            const endItem = Math.min(currentPage * itemsPerPage, totalItems);
            let paginationHTML =
                '<div class="flex items-center justify-between px-6 py-4"><div class="text-sm text-gray-700 font-medium">Showing <span class="font-bold text-blue-600">' +
                startItem + '</span> to <span class="font-bold text-blue-600">' + endItem +
                '</span> of <span class="font-bold text-blue-600">' + totalItems +
                '</span> policies</div><div class="flex gap-2">';
            paginationHTML +=
                `<button onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''} class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition"><i class="fas fa-chevron-left"></i></button>`;
            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, startPage + 4);
            if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);
            for (let i = startPage; i <= endPage; i++) {
                const isActive = i === currentPage;
                paginationHTML +=
                    `<button onclick="changePage(${i})" class="px-4 py-2 text-sm font-medium ${isActive ? 'text-white bg-blue-600 border-blue-600' : 'text-gray-700 bg-white border-gray-300 hover:bg-gray-50'} border rounded-lg transition">${i}</button>`;
            }
            paginationHTML +=
                `<button onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''} class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition"><i class="fas fa-chevron-right"></i></button>`;
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
            document.querySelectorAll('[id^="dropdown-"]').forEach(d => {
                if (d.id !== `dropdown-${policyId}`) d.classList.add('hidden');
            });
            if (dropdown.classList.contains('hidden')) {
                const rect = button.getBoundingClientRect();
                dropdown.style.top = `${rect.bottom + window.scrollY + 4}px`;
                dropdown.style.left = `${rect.right - 192 + window.scrollX}px`;
                dropdown.classList.remove('hidden');
            } else {
                dropdown.classList.add('hidden');
            }
        }

        function viewDetails(policyId) {
            const policy = policies.find(p => p.id == policyId);
            if (!policy) return;
            currentPolicyId = policyId;
            const modal = document.getElementById('policyModal');
            modal.setAttribute('data-policy-id', policyId);
            document.getElementById('modal-policy-number').textContent = policy.number;
            document.getElementById('modal-business-class').textContent = policy.className;
            document.getElementById('modal-product').textContent = policy.productName;
            document.getElementById('modal-vehicle').textContent = policy.vehicle;
            const statusColors = {
                active: 'text-green-600 bg-green-50',
                expired: 'text-red-600 bg-red-50'
            };
            const statusElement = document.getElementById('modal-status');
            statusElement.textContent = policy.statusText;
            statusElement.className = `text-sm font-bold ${statusColors[policy.status]} px-3 py-1 rounded-full`;
            const formatDate = (dateString) => dateString ? new Date(dateString).toLocaleDateString("en-US", {
                year: "numeric",
                month: "long",
                day: "numeric"
            }) : 'N/A';
            document.getElementById('modal-start-date').textContent = formatDate(policy.policy_start_date);
            document.getElementById('modal-end-date').textContent = formatDate(policy.policy_end_date);
            document.getElementById('modal-renewal-date').textContent = formatDate(policy.renewalDate);
            document.getElementById('modal-customer-name').textContent = policy.customer_name || 'N/A';
            document.getElementById('modal-customer-code').textContent = policy.customer_code || 'N/A';
            document.getElementById('modal-customer-phone').textContent = policy.customer_phone || 'N/A';
            document.getElementById('modal-customer-email').textContent = policy.customer_email || 'N/A';
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            document.getElementById(`dropdown-${policyId}`)?.classList.add('hidden');
        }

        function closeModal() {
            const modal = document.getElementById('policyModal');
            modal.style.display = '';
            modal.classList.add('hidden');
            modal.setAttribute('data-policy-id', '');
            document.body.style.overflow = '';
            currentPolicyId = null;
        }

        function processClaim(policyId) {
            const policy = policies.find(p => p.id == policyId);
            if (!policy) {
                showNotification('Policy not found. Please try again.', 'error');
                return;
            }

            const policyType = policy.type.trim().toLowerCase();

            const routeMap = {
                'motor': '/motor-form',
                'general accident': '/general-accident-form',
                'fire': '/fire-form',
                'bond': '/bond-form',
                'engineering': '/engineering-form',
                'liability': '/liability-form',
                'marine': '/marine-form',
                'aviation': '/aviation-form',
            };

            const routeUrl = routeMap[policyType] ?? '/motor-form';

            document.getElementById(`dropdown-${policyId}`)?.classList.add('hidden');
            window.location.href = `${routeUrl}?policyId=${policyId}`;
        }

        document.addEventListener('click', (event) => {
            if (!event.target.closest('[id^="dropdown-button-"]') && !event.target.closest('[id^="dropdown-"]')) {
                document.querySelectorAll('[id^="dropdown-"]').forEach(d => d.classList.add('hidden'));
            }
        });
        document.getElementById('policyModal')?.addEventListener('click', (event) => {
            if (event.target.id === 'policyModal') closeModal();
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') closeModal();
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
            const fileClaimBtn = document.getElementById('modal-file-claim-btn');
            if (fileClaimBtn) {
                fileClaimBtn.addEventListener('click', function() {
                    const modal = document.getElementById('policyModal');
                    const policyId = modal.getAttribute('data-policy-id');
                    if (policyId) {
                        closeModal();
                        processClaim(parseInt(policyId));
                    } else showNotification('No policy selected. Please try again.', 'error');
                });
            }
            //Commented out for now - we can enable it later if needed. We don't want to overwhelm users with syncs right now, especially if they have a lot of policies or a slow connection. We can always add a manual "Sync" button for users who want to refresh their data on demand.
            // setTimeout(() => {
            //     syncPoliciesInBackground();
            //     syncInterval = setInterval(syncPoliciesInBackground, 10 * 60 * 1000);
            // }, 2000);
            setTimeout(() => {
                const lastSync = sessionStorage.getItem('lastPolicySync');
                const tenMinutes = 10 * 60 * 1000;

                // Only auto-sync if we haven't synced in the last 10 minutes
                if (!lastSync || (Date.now() - parseInt(lastSync)) > tenMinutes) {
                    syncPoliciesInBackground();
                }

                syncInterval = setInterval(syncPoliciesInBackground, 20 * 60 * 1000);
            }, 2000);
        });
        window.addEventListener('beforeunload', () => {
            if (syncInterval) clearInterval(syncInterval);
        });
    </script>
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

        .animate-pulse-slow {
            animation: pulse-slow 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
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
    </style>
</x-layouts.app>
