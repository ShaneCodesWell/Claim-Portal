<x-layouts.app>
    @php
        $policiesMapData = collect($policies->items())->keyBy('policy_id');
        $claimFormRoutes = [
            'motor' => '/motor-form',
            'general accident' => '/general-accident-form',
            'fire' => '/fire-form',
            'bond' => '/bond-form',
            'engineering' => '/engineering-form',
            'liability' => '/liability-form',
            'marine' => '/marine-form',
            'aviation' => '/aviation-form',
        ];

        $policiesMapData = collect($policies->items())
            ->keyBy('policy_id')
            ->map(function ($policy) use ($claimFormRoutes) {
                $key = strtolower($policy['business_class_name'] ?? '');
                return array_merge($policy, [
                    'claim_form_url' => ($claimFormRoutes[$key] ?? '/motor-form') . '?policyId=' . $policy['policy_id'],
                ]);
            });
    @endphp

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
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-6 space-y-6">
        {{-- Header with Stats --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Policy Dashboard</h1>
                <p class="text-gray-500 text-sm mt-1">
                    Manage your insurance policies and submit new claim requests.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-3 px-4 py-2 bg-blue-50 border border-blue-100 rounded-full">
                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm text-blue-600">
                        <span class="font-bold text-sm" id="active-count">{{ $statusCounts['active'] ?? 0 }}</span>
                    </div>
                    <div class="text-sm font-medium text-blue-700">Active Policies</div>
                </div>
                <div class="flex items-center gap-3 px-4 py-2 bg-red-50 border border-red-100 rounded-full">
                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm text-red-600">
                        <span class="font-bold text-sm" id="expired-count">{{ $statusCounts['expired'] ?? 0 }}</span>
                    </div>
                    <div class="text-sm font-medium text-red-700">Expired</div>
                </div>
            </div>
        </div>

        {{-- Policies Table Section --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-3 border-b border-gray-200 bg-gray-50/50 rounded-t-xl">
                <div class="flex flex-col space-y-3 md:space-y-0 md:flex-row md:items-center md:justify-between">
                    <!-- Header text -->
                    <div>
                        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-file-contract text-blue-500"></i>
                            Your Insurance Policies
                        </h2>
                        <p class="text-xs text-gray-500 mt-0.5">Click on any policy to view details or file a claim</p>
                    </div>

                    <!-- Filters -->
                    <div class="w-full md:w-auto">
                        <form method="GET" action="{{ route('dashboard') }}" id="filter-form">
                            <div class="flex flex-wrap items-center gap-2">
                                <!-- Search - grows, becomes longer -->
                                <div class="relative flex-1 min-w-50 md:flex-2">
                                    <i
                                        class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                    <input type="text" name="search" id="search-input"
                                        value="{{ request('search') }}" placeholder="Search..."
                                        class="pl-9 pr-4 py-2 border border-gray-300 rounded-xl w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm bg-white" />
                                </div>

                                <!-- Type - shorter width, less vertical padding -->
                                <select name="type" id="type-select"
                                    class="px-3 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-700 text-sm w-auto md:w-32">
                                    <option value="">All Types</option>
                                    @foreach ($businessClasses as $class)
                                        <option value="{{ $class }}" @selected(request('type') === $class)>
                                            {{ $class }}
                                        </option>
                                    @endforeach
                                </select>

                                <!-- Status - shorter width, less vertical padding -->
                                <select name="status" id="status-select"
                                    class="px-3 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-700 text-sm w-auto md:w-32">
                                    <option value="">All Statuses</option>
                                    <option value="active" @selected(request('status') === 'active')>Active</option>
                                    <option value="expired" @selected(request('status') === 'expired')>Expired</option>
                                </select>

                                <!-- Clear button - matches new height -->
                                @if (request()->hasAny(['search', 'type', 'status']))
                                    <a href="{{ route('dashboard') }}"
                                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition flex items-center gap-2 text-sm font-medium whitespace-nowrap">
                                        <i class="fas fa-times-circle"></i> Clear
                                    </a>
                                @endif
                            </div>
                        </form>
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
                    <table class="min-w-full table-fixed divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Policy Details</th>
                                <th
                                    class="px-6 py-3 w-60 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
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
                        <tbody class="bg-white divide-y divide-gray-100" id="policies-table-body">
                            @foreach ($policies as $policy)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-3">
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">
                                                {{ $policy['business_class_name'] }}
                                            </div>

                                            <div class="text-xs text-gray-500">
                                                {{ $policy['vehicle_number'] ?? ' ' }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-3">
                                        <div class="text-xs font-mono font-medium text-gray-900">
                                            {{ $policy['policy_number'] }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-3">
                                        <div class="text-xs font-medium text-gray-900">
                                            {{ ucwords(strtolower($customer->name)) }}
                                        </div>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            {{ $customer->external_customer_code }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-3">
                                        <div class="text-xs font-medium text-gray-900">
                                            {{ $policy['product_name'] }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-3">
                                        <span
                                            class="px-3 py-1 inline-flex text-xs font-semibold rounded-full
                                                {{ $policy['status'] === 'active'
                                                    ? 'bg-green-100 text-green-700 border border-green-200'
                                                    : ($policy['status'] === 'pending_renewal'
                                                        ? 'bg-amber-100 text-amber-700 border border-amber-200'
                                                        : 'bg-red-100 text-red-700 border border-red-200') }}">
                                            {{ ucfirst(str_replace('_', ' ', $policy['status'])) }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-3">
                                        <div class="text-xs text-gray-900 font-medium">
                                            {{ $policy['renewal_date'] ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-3 text-right">
                                        @php
                                            $key = strtolower($policy['business_class_name'] ?? '');
                                            $claimFormUrl =
                                                ($claimFormRoutes[$key] ?? '/motor-form') .
                                                '?policyId=' .
                                                $policy['policy_id'];
                                            $isFleet = count($policy['risks'] ?? []) > 1;
                                        @endphp
                                        <div class="relative inline-block">
                                            <button onclick="toggleDropdown(event, {{ $policy['policy_id'] }})"
                                                id="dropdown-button-{{ $policy['policy_id'] }}"
                                                class="text-gray-700 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors inline-flex items-center font-medium text-sm">
                                                Actions <i class="fas fa-chevron-down ml-2 text-xs"></i>
                                            </button>
                                            <div id="dropdown-{{ $policy['policy_id'] }}"
                                                class="hidden absolute right-0 mt-1 w-48 rounded-lg shadow-lg bg-white ring-1 ring-gray-200 z-30">
                                                <div class="py-1">
                                                    <button onclick="viewDetails({{ $policy['policy_id'] }})"
                                                        class="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 flex items-center transition">
                                                        <i class="fas fa-eye mr-2"></i> View Details
                                                    </button>
                                                    @if (!$isFleet)
                                                        @if ($policy['status'] === 'expired')
                                                            <button onclick="showExpiredPolicyAlert()"
                                                                class="w-full text-left px-4 py-2.5 text-sm flex items-center text-gray-400 cursor-not-allowed opacity-50">
                                                                <i class="fas fa-file-invoice mr-2"></i> Process Claim
                                                                <i class="fas fa-lock ml-auto text-xs"></i>
                                                            </button>
                                                        @else
                                                            <a href="{{ $claimFormUrl }}"
                                                                class="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600 flex items-center transition">
                                                                <i class="fas fa-file-invoice mr-2"></i> Process Claim
                                                            </a>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- Pagination --}}
                <div
                    class="bg-gray-50 px-6 py-3 border-t border-gray-300 flex justify-between items-center flex-wrap gap-3">
                    <div class="text-sm text-gray-500">
                        @if ($policies->firstItem())
                            Showing {{ $policies->lastItem() }} of {{ $policies->total() }}
                            policies
                        @else
                            No policies found
                        @endif
                    </div>
                    <div class="flex gap-2">
                        {{ $policies->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Policy Details Modal (styled with new theme) --}}
    <x-policy-details-modal />

    <script>
        const policiesMap = @json($policiesMapData);

        // ── Dropdown ──────────────────────────────────────────────────────────────
        function toggleDropdown(event, policyId) {
            event.stopPropagation();
            document.querySelectorAll('[id^="dropdown-"]').forEach(d => {
                if (d.id !== `dropdown-${policyId}`) d.classList.add('hidden');
            });
            document.getElementById(`dropdown-${policyId}`)?.classList.toggle('hidden');
        }

        document.addEventListener('click', () => {
            document.querySelectorAll('[id^="dropdown-"]').forEach(d => d.classList.add('hidden'));
        });

        // ── Risk accordion (used by modal HTML) ───────────────────────────────────
        function toggleRisk(btn) {
            const body = btn.closest('.risk-card').querySelector('.risk-body');
            const chevron = btn.querySelector('.risk-chevron');
            const isOpen = !body.classList.contains('hidden');
            body.classList.toggle('hidden', isOpen);
            chevron.style.transform = isOpen ? '' : 'rotate(180deg)';
        }

        function filterRisks(query) {
            const term = query.toLowerCase().trim();
            const cards = document.querySelectorAll('#modal-risks-list .risk-card');
            let visible = 0;

            cards.forEach(card => {
                const matches = !term || (card.dataset.riskSearch || '').includes(term);
                card.style.display = matches ? '' : 'none';
                if (matches) visible++;
            });

            document.getElementById('modal-risk-empty').classList.toggle('hidden', visible > 0);
            document.getElementById('modal-risk-count').textContent = visible;
        }

        // ── Build a risk card from raw API data ───────────────────────────────────
        function buildRiskCard(risk, policy = null, isFleet = false) {
            const regNo = risk.risk_ref_no || '-';
            const make = risk.vehicle_make || '';
            const model = risk.vehicle_model || '';
            const year = risk.vehicle_yr_manufacture || '';
            const chassis = risk.vehicle_chassis_no || '-';
            const colour = risk.vehicle_colour || '-';
            const sumInsured = risk.sum_insured ?
                `GHS ${parseFloat(risk.sum_insured).toLocaleString()}` :
                '-';
            const premium = risk.total_premium ?
                `GHS ${parseFloat(risk.total_premium).toLocaleString()}` :
                '-';

            const covers = Object.values(risk.covers ?? {});
            const coverTags = covers.map(c =>
                `<span class="text-xs px-2 py-1 bg-white border border-gray-200 rounded-md text-gray-600">${c.covername}</span>`
            ).join('');

            const isMotor = !!make;
            const iconHtml = isMotor ?
                `<div class="flex-shrink-0 w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center"><i class="fas fa-car text-blue-600 text-sm"></i></div>` :
                `<div class="flex-shrink-0 w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center"><i class="fas fa-box text-purple-600 text-sm"></i></div>`;

            const title = make && model ? `${make} ${model}` : regNo;
            const subtitle = make ? `${regNo} · ${year}` : regNo;
            const searchData = [make, model, regNo, year, chassis].join(' ').toLowerCase();

            // Per-risk claim button — only for fleet, only if active
            const riskClaimUrl = policy ? `${policy.claim_form_url}&riskId=${risk.id}` : '#';
            const isExpired = policy?.status === 'expired';

            const claimButton = isFleet ?
                `<div class="border-t border-gray-200 pt-3 mt-3 flex justify-end">
               ${isExpired
                   ? `<button onclick="showExpiredPolicyAlert()" class="px-3 py-1.5 text-xs rounded-lg flex items-center gap-1.5 bg-gray-100 text-gray-400 cursor-not-allowed opacity-60">
                                      <i class="fas fa-file-invoice"></i> Process Claim <i class="fas fa-lock ml-1 text-xs"></i>
                                  </button>`
                   : `<a href="${riskClaimUrl}" class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition flex items-center gap-1.5">
                                      <i class="fas fa-file-invoice"></i> Process Claim
                                  </a>`
               }
           </div>` :
                '';

            return `
                <div class="risk-card border border-gray-200 rounded-xl overflow-hidden" data-risk-search="${searchData}">
                    <button type="button" onclick="toggleRisk(this)"
                        class="w-full flex items-center gap-3 px-4 py-3 bg-white hover:bg-gray-50 transition-colors text-left">
                        ${iconHtml}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">${title}</p>
                            <p class="text-xs text-gray-500">${subtitle}</p>
                        </div>
                        <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-200 risk-chevron"></i>
                    </button>
                    <div class="risk-body hidden border-t border-gray-100 bg-gray-50 px-4 py-4">
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            ${make   ? `<div><p class="text-xs text-gray-500 mb-0.5">Make &amp; Model</p><p class="text-sm font-semibold text-gray-900">${make} ${model}</p></div>` : ''}
                            ${year   ? `<div><p class="text-xs text-gray-500 mb-0.5">Year</p><p class="text-sm font-semibold text-gray-900">${year}</p></div>` : ''}
                            <div><p class="text-xs text-gray-500 mb-0.5">Chassis No.</p><p class="text-sm font-semibold text-gray-900">${chassis}</p></div>
                            <div><p class="text-xs text-gray-500 mb-0.5">Colour</p><p class="text-sm font-semibold text-gray-900">${colour}</p></div>
                            <div><p class="text-xs text-gray-500 mb-0.5">Sum Insured</p><p class="text-sm font-semibold text-gray-900">${sumInsured}</p></div>
                            <div><p class="text-xs text-gray-500 mb-0.5">Premium</p><p class="text-sm font-semibold text-gray-900">${premium}</p></div>
                        </div>
                        ${covers.length > 0 ? `
                                    <div class="border-t border-gray-200 pt-3">
                                        <p class="text-xs text-gray-500 mb-2">Covers Included</p>
                                        <div class="flex flex-wrap gap-1.5">${coverTags}</div>
                                    </div>` : ''}
                        ${claimButton}
                    </div>
                </div>`;
        }

        // ── Modal ─────────────────────────────────────────────────────────────────
        function viewDetails(policyId) {
            const policy = policiesMap[policyId];
            if (!policy) return;

            const modal = document.getElementById('policyModal');
            modal.setAttribute('data-policy-id', policyId);

            document.getElementById('modal-policy-number').textContent = policy.policy_number;
            document.getElementById('modal-business-class').textContent = policy.business_class_name;
            document.getElementById('modal-product').textContent = policy.product_name;
            document.getElementById('modal-start-date').textContent = policy.start_date ?? 'N/A';
            document.getElementById('modal-end-date').textContent = policy.end_date ?? 'N/A';
            document.getElementById('modal-renewal-date').textContent = policy.renewal_date ?? 'N/A';

            // Status badge
            const statusStyles = {
                active: 'text-green-600 bg-green-50',
                expired: 'text-red-600 bg-red-50',
                pending_renewal: 'text-amber-600 bg-amber-50',
            };
            const statusEl = document.getElementById('modal-status');
            statusEl.textContent = policy.status.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
            statusEl.className =
                `text-xs font-semibold px-2.5 py-1 rounded-full ${statusStyles[policy.status] ?? 'text-gray-600 bg-gray-50'}`;

            // Populate risks accordion
            const riskEntries = Object.values(policy.risks ?? {});
            const isFleet = riskEntries.length > 1;

            document.getElementById('modal-risk-count').textContent = riskEntries.length;

            const risksList = document.getElementById('modal-risks-list');
            risksList.innerHTML = riskEntries.length ?
                riskEntries.map(risk => buildRiskCard(risk, policy, isFleet)).join('') :
                '<p class="text-sm text-gray-400 text-center py-6">No risk details available yet.</p>';

            // Footer File Claim button — hide for fleet, show for single risk
            const fileClaimBtn = document.getElementById('modal-file-claim-btn');
            if (isFleet) {
                fileClaimBtn.style.display = 'none';
            } else {
                fileClaimBtn.style.display = '';
                if (policy.status === 'expired') {
                    fileClaimBtn.disabled = true;
                    fileClaimBtn.className =
                        'px-4 py-2 text-sm rounded-lg flex items-center gap-2 bg-gray-200 text-gray-400 cursor-not-allowed opacity-60';
                    fileClaimBtn.onclick = e => {
                        e.preventDefault();
                        showExpiredPolicyAlert();
                    };
                } else {
                    fileClaimBtn.disabled = false;
                    fileClaimBtn.className =
                        'px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2 shadow-sm hover:shadow';
                    fileClaimBtn.onclick = () => {
                        closeModal();
                        window.location.href = policy.claim_form_url;
                    };
                }
            }



            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            document.getElementById(`dropdown-${policyId}`)?.classList.add('hidden');
        }

        function closeModal() {
            const modal = document.getElementById('policyModal');
            modal.style.display = 'none';
            modal.setAttribute('data-policy-id', '');
            document.body.style.overflow = '';
            // Reset risk search
            const searchInput = document.getElementById('modal-risk-search');
            if (searchInput) {
                searchInput.value = '';
                filterRisks('');
            }
        }

        document.getElementById('policyModal')?.addEventListener('click', e => {
            if (e.target.id === 'policyModal') closeModal();
        });
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeModal();
        });

        // ── Expired alert ─────────────────────────────────────────────────────────
        function showExpiredPolicyAlert() {
            Swal.fire({
                icon: 'warning',
                title: 'Policy Expired',
                text: 'This policy has expired and is no longer eligible for new claims. Please contact us if you need assistance with an active policy.',
                confirmButtonText: 'Got it',
                confirmButtonColor: '#2563eb',
            });
        }

        // ── Filter form ───────────────────────────────────────────────────────────
        const form = document.getElementById('filter-form');
        const searchInput = document.getElementById('search-input');
        const typeSelect = document.getElementById('type-select');
        const statusSelect = document.getElementById('status-select');

        let debounceTimer;
        searchInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => form.submit(), 400);
        });
        typeSelect.addEventListener('change', () => form.submit());
        statusSelect.addEventListener('change', () => form.submit());
    </script>

</x-layouts.app>
