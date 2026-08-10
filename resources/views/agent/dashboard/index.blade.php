<x-layouts.agent>
    @php
        $neverSynced = is_null($agent->glims_last_synced_at) && is_null($agent->genova_last_synced_at);

        $policiesMapData = collect();
        if ($searchQuery && !$searchError && isset($searchResult['local'])) {
            $policy = $searchResult['local'];
            $policiesMapData = collect([
                $policy['policy_id'] => array_merge($policy, [
                    'claim_form_url' => route('agent.claims.create', ['policy_id' => $policy['policy_id']]),
                ]),
            ]);
        }

    @endphp
    <!-- Page Header -->
    <div class="mb-6">
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">
                        Intermediary Dashboard - <span
                            class="font-bold text-blue-500">{{ Auth::guard('agent')->user()?->name ?? 'Intermediary' }}</span>
                    </p>
                    <h2 class="text-xl font-semibold text-gray-900">
                        Policy Access Portal
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Search for a specific policy by number or vehicle registration, or browse the list of policies
                        assigned to you.
                    </p>
                </div>
                <button onclick="window.location.reload()"
                    class="bg-white border border-gray-300 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium text-gray-700 transition shadow-sm flex items-center gap-2">
                    <i class="fas fa-refresh text-gray-500"></i> Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Search Section - Find a specific policy -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-start gap-3 mb-4">
            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                <i class="fas fa-search text-blue-600"></i>
            </div>
            <div>
                <h3 class="text-base font-semibold text-gray-900">Find a Policy</h3>
                <p class="text-sm text-gray-500">Enter the policy number or vehicle registration to access a specific
                    policy.</p>
            </div>
        </div>
        {{-- Sync status banner — placed right after the header, before the form,
             so it's the first thing an agent sees before attempting a search --}}
        {{-- @if ($neverSynced)
            <div
                class="bg-blue-50 border border-blue-200 text-blue-700 text-sm rounded-lg px-4 py-3 mb-4 flex items-center gap-2">
                <i class="fas fa-spinner fa-spin"></i>
                <span>We're setting up your account and pulling your policies for the first time. This can take a few
                    minutes for larger portfolios — try searching again shortly.</span>
            </div>
        @elseif (
            ($agent->glims_agent_code && $agent->glims_last_synced_at?->lt(now()->subMinutes(5))) ||
                ($agent->genova_agent_code && $agent->genova_last_synced_at?->lt(now()->subMinutes(10))))
            <div class="bg-amber-50 border border-amber-200 text-amber-700 text-sm rounded-lg px-4 py-2 mb-4">
                <i class="fas fa-sync fa-spin mr-1"></i> Your policy list may still be refreshing — if a policy isn't
                showing up yet, try again in a moment.
            </div>
        @endif --}}

        <form method="GET" action="{{ route('agent.policy.search') }}" class="flex flex-col sm:flex-row gap-3"
            x-data="{ loading: false }" @submit="loading = true">

            <div class="flex-1">
                <label class="block text-xs font-medium text-gray-600 mb-1">Policy Number</label>
                <input type="text" name="policy_number" value="{{ $searchQuery ?? '' }}"
                    placeholder="e.g., P-1002-201-2026-012723"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div class="sm:self-end">
                <button type="submit" :disabled="loading"
                    class="bg-blue-600 hover:bg-blue-700 disabled:opacity-60 disabled:cursor-not-allowed text-white px-6 py-2.5 rounded-xl text-sm font-medium shadow-sm transition flex items-center gap-2 mt-6 sm:mt-0">
                    <i class="fas fa-spinner fa-spin text-xs" x-show="loading"></i>
                    <i class="fas fa-search text-xs" x-show="!loading"></i>
                    <span x-text="loading ? 'Searching...' : 'Lookup Policy'"></span>
                </button>
            </div>
        </form>

        {{-- Search error --}}
        @if (!empty($searchError))
            <div
                class="mt-3 flex items-center gap-2 text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-2">
                <i class="fas fa-exclamation-circle shrink-0"></i>
                {{ $searchError }}
            </div>
        @endif
        <p class="text-xs text-gray-400 mt-3">
            <i class="fas fa-lock text-xs"></i> Access all your active polices here.
        </p>
    </div>

    <!-- Assigned Policies List -->
    @if ($searchQuery)
        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-3 border-b border-gray-200 bg-gray-50/50 rounded-t-xl">
                <div>
                    <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-file-contract text-blue-500"></i>
                        Policy Lookup Result
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">Enter a policy number above to search your portfolio</p>
                </div>
            </div>

            {{-- No search attempted yet --}}
            @if (!$searchQuery)
                <div class="p-16 text-center">
                    <div class="w-24 h-24 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-5">
                        <i class="fas fa-search text-blue-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Search for a policy</h3>
                    <p class="text-gray-500 text-sm max-w-sm mx-auto">
                        Enter a policy number in the search box above to look up a specific policy in your portfolio.
                    </p>
                </div>

                {{-- Search was attempted but nothing found --}}
            @elseif ($searchError)
                <div class="p-16 text-center">
                    <div class="w-24 h-24 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-5">
                        <i class="fas fa-file-circle-xmark text-red-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Policy not found</h3>
                    <p class="text-gray-500 text-sm max-w-sm mx-auto">
                        <span class="font-mono text-gray-700">{{ $searchQuery }}</span> was not found in your assigned
                        portfolio.
                    </p>
                </div>

                {{-- Result found --}}
            @else
                @php
                    $policy = $searchResult['local'];
                    $isFleet = count($policy['risks'] ?? []) > 1;
                    $claimFormUrl = route('agent.claims.create', [
                        'policy_id' => $policy['policy_id'],
                    ]);
                @endphp
                <div class="overflow-x-auto">
                    <table class="min-w-full table-fixed divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Policy Details</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-60">
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
                        <tbody class="bg-white divide-y divide-gray-100">
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-3">
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ $policy['business_class_name'] }}</div>
                                    <div class="text-xs text-gray-500">{{ $policy['vehicle_number'] ?? '—' }}</div>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="text-xs font-mono font-medium text-gray-900">
                                        {{ $policy['policy_number'] }}</div>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="text-xs font-medium text-gray-900">
                                        {{ ucwords(strtolower($policy['customer_name'])) }}</div>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $policy['customer_code'] }}</p>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="text-xs font-medium text-gray-900">{{ $policy['product_name'] }}</div>
                                </td>
                                <td class="px-6 py-3">
                                    <span
                                        class="px-3 py-1 inline-flex text-xs font-semibold rounded-full
                                {{ $policy['status'] === 'active'
                                    ? 'bg-green-100 text-green-700 border border-green-200'
                                    : 'bg-red-100 text-red-700 border border-red-200' }}">
                                        {{ ucfirst($policy['status']) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="text-xs text-gray-900 font-medium">{{ $policy['renewal_date'] ?? '—' }}
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-right relative" x-data="{ open: false }"
                                    style="overflow: visible;">
                                    <button x-ref="actionBtn" @click="open = !open"
                                        class="h-9 w-9 rounded-lg hover:bg-gray-100 text-gray-500 transition inline-flex items-center justify-center">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <template x-teleport="body">
                                        <div x-show="open" @click.outside="open = false" x-transition
                                            x-anchor.bottom-end="$refs.actionBtn"
                                            class="fixed w-52 bg-white rounded-xl shadow-lg border border-gray-200 py-2 z-9999">
                                            <button @click="open = false; viewDetails({{ $policy['policy_id'] }})"
                                                class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                                <i class="fas fa-eye text-xs text-blue-500"></i>
                                                View Details
                                            </button>
                                            @if (!$isFleet)
                                                @if ($policy['status'] === 'expired')
                                                    <button @click="open = false; showExpiredPolicyAlert()"
                                                        class="w-full px-4 py-2 text-left text-sm flex items-center gap-2 text-gray-400 cursor-not-allowed opacity-50">
                                                        <i class="fas fa-file-invoice text-xs"></i>
                                                        File a Claim
                                                        <i class="fas fa-lock ml-auto text-xs"></i>
                                                    </button>
                                                @else
                                                    <a href="{{ $claimFormUrl }}"
                                                        class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                                        <i class="fas fa-file-invoice text-xs text-green-500"></i>
                                                        File a Claim
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
                                    </template>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

    <!-- Helpful Tip -->
    <div class="mt-6 bg-blue-50 rounded-2xl border border-blue-100 shadow-sm p-4">
        <p class="text-sm font-medium text-blue-800">
            <i class="fas fa-info-circle mr-2"></i> Intermediary Access Note
        </p>
        <p class="text-sm text-blue-700 mt-1">
            Use the search box above to quickly locate a specific policy by number or vehicle registration. The list
            below shows all policies currently assigned to you. If you believe a policy is missing, please contact our
            support team.
        </p>
    </div>

    {{-- Policy Details Modal --}}
    <x-policy-details-modal />

    <script>
        const policiesMap = @json($policiesMapData);

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

            document.getElementById('modal-risk-empty')?.classList.toggle('hidden', visible > 0);
            document.getElementById('modal-risk-count').textContent = visible;
        }

        // ── Build a risk card ─────────────────────────────────────────────────────
        function buildRiskCard(risk, policy = null, isFleet = false, riskId = null) {
            const regNo = risk.risk_ref_no || '-';
            const make = risk.vehicle_make || '';
            const model = risk.vehicle_model || '';
            const year = risk.vehicle_yr_manufacture || '';
            const chassis = risk.vehicle_chassis_no || '-';
            const colour = risk.vehicle_colour || '-';
            const sumInsured = risk.sum_insured ? `GHS ${parseFloat(risk.sum_insured).toLocaleString()}` : '-';
            const premium = risk.total_premium ? `GHS ${parseFloat(risk.total_premium).toLocaleString()}` : '-';
            const riskClaimUrl = policy ? `${policy.claim_form_url}&risk_id=${riskId}` : '#';

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

            const isExpired = policy?.status === 'expired';

            const claimButton = isFleet ?
                `<div class="border-t border-gray-200 pt-3 mt-3 flex justify-end">
            ${isExpired
                ? `<button onclick="showExpiredPolicyAlert()" class="px-3 py-1.5 text-xs rounded-lg flex items-center gap-1.5 bg-gray-100 text-gray-400 cursor-not-allowed opacity-60">
                                    <i class="fas fa-file-invoice"></i> File a Claim <i class="fas fa-lock ml-1 text-xs"></i>
                                </button>`
                : `<a href="${riskClaimUrl}" class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition flex items-center gap-1.5">
                                    <i class="fas fa-file-invoice"></i> File a Claim
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

            const riskEntries = Object.entries(policy.risks ?? {});
            const isFleet = riskEntries.length > 1;

            document.getElementById('modal-risk-count').textContent = riskEntries.length;

            const risksList = document.getElementById('modal-risks-list');
            risksList.innerHTML = riskEntries.length ?
                riskEntries.map(([riskId, risk]) => buildRiskCard(risk, policy, isFleet, riskId)).join('') :
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

        function showExpiredPolicyAlert() {
            Swal.fire({
                icon: 'warning',
                title: 'Policy Expired',
                text: 'This policy has expired and is no longer eligible for new claims. Please contact us if you need assistance with an active policy.',
                confirmButtonText: 'Got it',
                confirmButtonColor: '#2563eb',
            });
        }
    </script>
</x-layouts.agent>
