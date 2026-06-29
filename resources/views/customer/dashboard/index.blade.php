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
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl text-xs text-green-800 font-medium">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-800 font-medium">
            {{ session('error') }}
        </div>
    @endif

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <p class="text-sm text-gray-500 font-medium mb-1">
                    Welcome back, <span
                        class="font-bold text-blue-500">{{ ucwords(strtolower($customer->name ?? 'Customer')) }}</span>
                </p>
                <h2 class="text-xl font-semibold text-gray-900">
                    Policy Dashboard
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Manage your insurance policies and submit new claim requests.
                </p>
            </div>

            {{-- <button onclick="window.location.reload()"
                    class="bg-white border border-gray-300 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium text-gray-700 transition shadow-sm flex items-center gap-2">
                    <i class="fas fa-refresh text-gray-500"></i> Refresh
                </button> --}}
        </div>
    </div>

    <!-- Policy Status Summary Pills -->
    <div class="flex flex-wrap gap-3 mb-6">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gray-200 shadow-sm">
            <span class="h-2 w-2 rounded-full bg-indigo-400"></span>
            <span class="text-sm text-gray-600">Total Policies</span>
            <span class="text-sm font-semibold text-gray-900">{{ $policies->total() }}</span>
        </div>

        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gray-200 shadow-sm">
            <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
            <span class="text-sm text-gray-600">Active</span>
            <span class="text-sm font-semibold text-gray-900">{{ $statusCounts['active'] ?? 0 }}</span>
        </div>

        {{-- <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gray-200 shadow-sm">
            <span class="h-2 w-2 rounded-full bg-amber-400"></span>
            <span class="text-sm text-gray-600">Pending Renewal</span>
            <span class="text-sm font-semibold text-gray-900">{{ $statusCounts['pending_renewal'] ?? 0 }}</span>
        </div>

        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gray-200 shadow-sm">
            <span class="h-2 w-2 rounded-full bg-rose-400"></span>
            <span class="text-sm text-gray-600">Expired</span>
            <span class="text-sm font-semibold text-gray-900">{{ $statusCounts['expired'] ?? 0 }}</span>
        </div> --}}
    </div>

    <!-- Policies Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Toolbar -->
        <div
            class="px-5 py-4 border-b border-gray-200 bg-gray-50 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">
                    Your Insurance Policies
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">
                    Click on any policy to view details or file a claim
                </p>
            </div>

            <div class="flex items-center gap-3">
                <form method="GET" action="{{ route('dashboard') }}" id="filter-form" class="flex items-center gap-3">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" name="search" id="search-input" value="{{ request('search') }}"
                            placeholder="Search policies..."
                            class="pl-8 pr-4 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-gray-300 w-52 bg-white" />
                    </div>

                    <select name="type" id="type-select"
                        class="px-3 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-gray-300 bg-white text-gray-700">
                        <option value="">All Types</option>
                        @foreach ($businessClasses as $class)
                            <option value="{{ $class }}" @selected(request('type') === $class)>{{ $class }}
                            </option>
                        @endforeach
                    </select>

                    @if (request()->hasAny(['search', 'type', 'status']))
                        <a href="{{ route('dashboard') }}"
                            class="bg-white border border-gray-300 hover:bg-gray-50 px-3 py-2 rounded-xl text-sm font-medium text-gray-700 transition flex items-center gap-2">
                            <i class="fas fa-times text-xs"></i> Clear
                        </a>
                    @endif
                </form>
            </div>
        </div>

        @if (!isset($policies) || count($policies) === 0)
            @if (is_null($customer->last_synced_at))
                <!-- Syncing State -->
                <div id="syncing-state" class="px-4 py-12 text-center text-sm text-gray-500">
                    <div class="flex flex-col items-center justify-center gap-3">
                        <i class="fas fa-sync-alt text-blue-400 text-4xl animate-spin"></i>
                        <p class="text-gray-600 font-medium">Fetching your policies...</p>
                        <p class="text-xs text-gray-400" id="polling-status">Connecting to Vanguard Assurance...</p>
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="px-4 py-12 text-center text-sm text-gray-500">
                    <div class="flex flex-col items-center justify-center gap-3">
                        <i class="fas fa-folder-open text-4xl text-gray-300"></i>
                        <p class="text-gray-600">No policies found. Try adjusting your filters.</p>
                        <button onclick="location.reload()"
                            class="mt-2 inline-flex items-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl shadow-sm transition">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
            @endif
        @else
            <!-- Table (Responsive) -->
            <div class="overflow-x-auto custom-scroll">
                <table class="min-w-225 md:min-w-full w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th
                                class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Policy Number</th>
                            <th
                                class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Policy Details</th>
                            <th
                                class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Product</th>
                            <th
                                class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Renewal Date</th>
                            <th
                                class="px-4 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200" id="policies-table-body">
                        @foreach ($policies as $policy)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-4">
                                    <span
                                        class="font-mono text-sm font-medium text-gray-900">{{ $policy['policy_number'] }}</span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $policy['business_class_name'] }}
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $policy['vehicle_number'] ?? ' ' }}</div>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-600">{{ $policy['product_name'] }}</td>
                                <td class="px-4 py-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border
                                        {{ $policy['status'] === 'active'
                                            ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                                            : ($policy['status'] === 'pending_renewal'
                                                ? 'bg-amber-50 text-amber-700 border-amber-200'
                                                : 'bg-rose-50 text-rose-700 border-rose-200') }}">
                                        {{ ucfirst(str_replace('_', ' ', $policy['status'])) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-600">{{ $policy['renewal_date'] ?? '-' }}</td>
                                <td class="px-4 py-4 text-right relative" style="overflow: visible;">
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
                                            class="px-3 py-2 border border-gray-300 rounded-xl text-sm text-gray-700 hover:bg-gray-50 inline-flex items-center">
                                            Actions
                                            <i class="fas fa-chevron-down text-xs ml-1"></i>
                                        </button>
                                        <div id="dropdown-{{ $policy['policy_id'] }}"
                                            class="hidden absolute right-0 mt-1 w-48 rounded-xl shadow-lg bg-white border border-gray-200 py-2 z-30">
                                            <button onclick="viewDetails({{ $policy['policy_id'] }})"
                                                class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                                <i class="fas fa-eye text-xs text-blue-500"></i>
                                                View Details
                                            </button>
                                            @if (!$isFleet)
                                                @if ($policy['status'] === 'expired')
                                                    <button onclick="showExpiredPolicyAlert()"
                                                        class="w-full px-4 py-2 text-left text-sm flex items-center gap-2 text-gray-400 cursor-not-allowed opacity-50">
                                                        <i class="fas fa-file-invoice text-xs"></i>
                                                        Process Claim
                                                        <i class="fas fa-lock ml-auto text-xs"></i>
                                                    </button>
                                                @else
                                                    <a href="{{ $claimFormUrl }}"
                                                        class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                                        <i class="fas fa-file-invoice text-xs text-green-500"></i>
                                                        Process Claim
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                class="bg-gray-50 px-6 py-3 border-t border-gray-300 flex justify-between items-center flex-wrap gap-3">
                <div class="text-sm text-gray-500">
                    <i class="fas fa-file mr-1"></i>
                    @if ($policies->firstItem())
                        Showing {{ $policies->lastItem() }} of {{ $policies->total() }} policies
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

    <!-- Support Card -->
    <div
        class="mt-6 bg-white rounded-2xl border border-gray-200 shadow-sm p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-gray-800">
                <i class="fas fa-life-ring text-blue-500 mr-2"></i> Need help with a policy or claim?
            </p>
            <p class="text-sm text-gray-500 mt-1">
                Contact our support team for assistance with your insurance policies or claim submissions.
            </p>
        </div>
        <a href="tel:+233302666485"
            class="bg-blue-50 hover:bg-blue-100 text-blue-700 px-4 py-2 rounded-xl text-sm font-medium transition flex items-center gap-2 w-full sm:w-auto justify-center">
            <i class="fas fa-headset"></i> Contact Support
        </a>
    </div>

    <x-policy-details-modal />

    {{-- Polling on fresh login --}}
    @if (is_null($customer->last_synced_at))
        <script>
            (function() {
                const POLL_URL = '{{ route('dashboard.poll') }}';
                const statusEl = document.getElementById('polling-status');
                const messages = [
                    'Connecting to Vanguard Assurance...',
                    'Loading your active policies...',
                    'Fetching policy details...',
                    'Almost there...',
                ];

                let attempt = 0;
                let msgIndex = 0;
                let maxAttempts = 20;

                const msgTimer = setInterval(() => {
                    msgIndex = (msgIndex + 1) % messages.length;
                    if (statusEl) statusEl.textContent = messages[msgIndex];
                }, 3000);

                function poll() {
                    attempt++;

                    fetch(POLL_URL, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.ready) {
                                clearInterval(msgTimer);
                                if (data.count > 0) {
                                    // Policies found — reload to render the table
                                    if (statusEl) statusEl.textContent =
                                        `Found ${data.count} polic${data.count === 1 ? 'y' : 'ies'}. Loading...`;
                                    setTimeout(() => window.location.reload(), 600);
                                } else {
                                    // Sync finished but no policies linked to this account
                                    const syncingEl = document.getElementById('syncing-state');
                                    if (syncingEl) {
                                        syncingEl.innerHTML = `
                                            <div class="flex flex-col items-center justify-center gap-3">
                                                <i class="fas fa-folder-open text-4xl text-gray-300"></i>
                                                <p class="text-gray-600 font-medium">No policies found</p>
                                                <p class="text-sm text-gray-500">There are no active policies linked to your account.</p>
                                            </div>`;
                                    }
                                }
                            } else if (attempt >= maxAttempts) {
                                clearInterval(msgTimer);
                                const syncingEl = document.getElementById('syncing-state');
                                if (syncingEl) {
                                    syncingEl.innerHTML = `
                                        <div class="flex flex-col items-center justify-center gap-3">
                                            <i class="fas fa-clock text-amber-400 text-4xl"></i>
                                            <p class="text-gray-600 font-medium">Taking longer than expected</p>
                                            <p class="text-sm text-gray-500">Your policies are still being fetched. Please refresh in a moment.</p>
                                            <button onclick="location.reload()"
                                                class="mt-2 inline-flex items-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl shadow-sm transition">
                                                <i class="fas fa-sync-alt"></i> Refresh Now
                                            </button>
                                        </div>`;
                                }
                            } else {
                                setTimeout(poll, 3000);
                            }
                        })
                        .catch(() => {
                            if (attempt < maxAttempts) {
                                setTimeout(poll, 3000);
                            }
                        });
                }

                setTimeout(poll, 2000);
            })();
        </script>
    @endif

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

        // ── Risk accordion ────────────────────────────────────────────────────────
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

        // ── Build a risk card ─────────────────────────────────────────────────────
        function buildRiskCard(risk, policy = null, isFleet = false) {
            const regNo = risk.risk_ref_no || '-';
            const make = risk.vehicle_make || '';
            const model = risk.vehicle_model || '';
            const year = risk.vehicle_yr_manufacture || '';
            const chassis = risk.vehicle_chassis_no || '-';
            const colour = risk.vehicle_colour || '-';
            const sumInsured = risk.sum_insured ?
                `GHS ${parseFloat(risk.sum_insured).toLocaleString()}` : '-';
            const premium = risk.total_premium ?
                `GHS ${parseFloat(risk.total_premium).toLocaleString()}` : '-';

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
                </div>` : '';

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

            const statusStyles = {
                active: 'text-green-600 bg-green-50',
                expired: 'text-red-600 bg-red-50',
                pending_renewal: 'text-amber-600 bg-amber-50',
            };
            const statusEl = document.getElementById('modal-status');
            statusEl.textContent = policy.status.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
            statusEl.className =
                `text-xs font-semibold px-2.5 py-1 rounded-full ${statusStyles[policy.status] ?? 'text-gray-600 bg-gray-50'}`;

            const riskEntries = Object.values(policy.risks ?? {});
            const isFleet = riskEntries.length > 1;

            document.getElementById('modal-risk-count').textContent = riskEntries.length;

            const risksList = document.getElementById('modal-risks-list');
            risksList.innerHTML = riskEntries.length ?
                riskEntries.map(risk => buildRiskCard(risk, policy, isFleet)).join('') :
                '<p class="text-sm text-gray-400 text-center py-6">No risk details available yet.</p>';

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

        let debounceTimer;
        searchInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => form.submit(), 400);
        });
        typeSelect.addEventListener('change', () => form.submit());
    </script>

</x-layouts.app>
