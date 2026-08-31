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
                        Search by policy number, client name, phone number, or vehicle registration.
                    </p>
                </div>
                <button onclick="window.location.reload()"
                    class="bg-white border border-gray-300 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium text-gray-700 transition shadow-sm flex items-center gap-2">
                    <i class="fas fa-refresh text-gray-500"></i> Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- ===================== SEARCH SECTION ===================== -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-8" x-data="agentSearch()">
        <div class="flex items-start gap-3 mb-5">
            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                <i class="fas fa-search text-blue-600"></i>
            </div>
            <div>
                <h3 class="text-base font-semibold text-gray-900">Find a policy or customer</h3>
                <p class="text-sm text-gray-500">Choose how you want to search below.</p>
            </div>
        </div>

        <!-- Tabs -->
        <div class="flex flex-wrap gap-2 mb-5">
            <template x-for="tab in tabs" :key="tab.id">
                <button type="button" @click="switchTab(tab.id)"
                    class="px-4 py-2 rounded-xl text-sm font-medium border transition flex items-center gap-2"
                    :class="activeTab === tab.id ?
                        'bg-blue-50 border-blue-300 text-blue-700' :
                        'bg-white border-gray-200 text-gray-600 hover:bg-gray-50'">
                    <i :class="tab.icon" class="text-xs"></i>
                    <span x-text="tab.label"></span>
                </button>
            </template>
        </div>

        <!-- Adaptive input -->
        <label class="block text-xs font-medium text-gray-600 mb-1" x-text="currentTab.label"></label>
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <i :class="currentTab.icon"
                    class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" x-model="query" :placeholder="currentTab.placeholder" @keydown.enter="runSearch()"
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <button type="button" @click="runSearch()" :disabled="loading"
                class="bg-blue-600 hover:bg-blue-700 disabled:opacity-60 disabled:cursor-not-allowed text-white px-6 py-2.5 rounded-xl text-sm font-medium shadow-sm transition flex items-center justify-center gap-2 whitespace-nowrap">
                <i class="fas fa-spinner fa-spin text-xs" x-show="loading"></i>
                <i class="fas fa-search text-xs" x-show="!loading"></i>
                <span x-text="loading ? 'Searching...' : 'Search'"></span>
            </button>
        </div>
        <p class="text-xs text-gray-400 mt-3" x-text="currentTab.hint"></p>

        <!-- Empty input -->
        <div x-show="showEmptyError" x-transition
            class="mt-3 flex items-center gap-2 text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-2">
            <i class="fas fa-exclamation-circle shrink-0"></i>
            Enter a value to search first.
        </div>

        <!-- Slow-search banner (policy tab only) -->
        <div x-show="loading && activeTab === 'policy' && escalated" x-transition
            class="mt-3 flex items-center gap-2 text-sm text-blue-700 bg-blue-50 border border-blue-200 rounded-lg px-3 py-2">
            <i class="fas fa-sync fa-spin shrink-0"></i>
            Checking with the insurer — this can take a few seconds.
        </div>

        <!-- Request error -->
        <div x-show="requestError" x-transition
            class="mt-3 flex items-center gap-2 text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-2">
            <i class="fas fa-exclamation-circle shrink-0"></i>
            <span x-text="requestError"></span>
        </div>

        <!-- ===================== POLICY NUMBER RESULT ===================== -->
        <div x-show="activeTab === 'policy' && searched && !loading" x-transition
            class="bg-white rounded-xl border border-gray-200 overflow-hidden mt-6">
            <div class="px-6 py-3 border-b border-gray-200 bg-gray-50/50">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-file-contract text-blue-500"></i> Policy Lookup Result
                </h2>
            </div>

            <template x-if="policyResult">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Policy
                                    Details</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Policy
                                    Number</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Insured
                                    Name</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Product
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-3">
                                    <div class="text-sm font-semibold text-gray-900"
                                        x-text="policyResult.business_class_name"></div>
                                    <div class="text-xs text-gray-500" x-text="policyResult.vehicle_number || '—'">
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-xs font-mono font-medium text-gray-900"
                                    x-text="policyResult.policy_number"></td>
                                <td class="px-6 py-3">
                                    <div class="text-xs font-medium text-gray-900" x-text="policyResult.customer_name">
                                    </div>
                                    <div class="text-xs text-gray-400 mt-0.5" x-text="policyResult.customer_code"></div>
                                </td>
                                <td class="px-6 py-3 text-xs font-medium text-gray-900"
                                    x-text="policyResult.product_name"></td>
                                <td class="px-6 py-3">
                                    <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full"
                                        :class="policyResult.status === 'active' ?
                                            'bg-green-100 text-green-700 border border-green-200' :
                                            'bg-red-100 text-red-700 border border-red-200'"
                                        x-text="policyResult.status"></span>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <button @click="viewDetails(policyResult.policy_id)"
                                        class="text-blue-600 hover:underline text-xs font-medium">
                                        View Details
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="text-xs text-gray-400 px-6 py-2 border-t border-gray-100" x-show="lastSource === 'api'">
                        <i class="fas fa-cloud mr-1"></i> Not yet synced locally — retrieved live from the insurer
                        system.
                    </p>
                </div>
            </template>

            <template x-if="!policyResult">
                <div class="p-16 text-center">
                    <div class="w-24 h-24 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-5">
                        <i class="fas fa-file-circle-xmark text-red-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Policy not found</h3>
                    <p class="text-gray-500 text-sm max-w-sm mx-auto">
                        <span class="font-mono text-gray-700" x-text="query"></span> was not found in your assigned
                        portfolio.
                    </p>
                </div>
            </template>
        </div>

        <!-- ===================== CUSTOMER SEARCH RESULT ===================== -->
        <div x-show="activeTab !== 'policy' && searched && !loading" x-transition
            class="bg-white rounded-xl border border-gray-200 overflow-hidden mt-6">
            <div class="px-6 py-3 border-b border-gray-200 bg-gray-50/50">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-users text-blue-500"></i> Customer Search Result
                </h2>
            </div>

            <template x-if="customerResults.length === 0">
                <div class="p-16 text-center">
                    <div class="w-24 h-24 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-5">
                        <i class="fas fa-user-slash text-red-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">No matching customer found</h3>
                    <p class="text-gray-500 text-sm max-w-sm mx-auto">
                        If you're expecting to see this customer, they may not have synced to your portfolio yet — try
                        again shortly, or search by policy number if you have it.
                    </p>
                </div>
            </template>

            <template x-if="customerResults.length > 0">
                <div class="divide-y divide-gray-100">
                    <template x-for="cust in customerResults" :key="cust.id">
                        <div
                            class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 hover:bg-gray-50 transition">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-semibold text-sm">
                                    <span x-text="initials(cust.name)"></span>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900" x-text="cust.name"></p>
                                    <p class="text-xs text-gray-400"
                                        x-text="(cust.code || '—') + ' · ' + (cust.phone || '—')"></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-gray-500"
                                    x-text="cust.policies.length + ' polic' + (cust.policies.length === 1 ? 'y' : 'ies')"></span>
                                <button type="button" @click="openCustomerModal(cust)"
                                    class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium px-4 py-2 rounded-lg transition flex items-center gap-2">
                                    <i class="fas fa-eye"></i> View policies
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>

    <!-- Helpful Tip -->
    <div class="mt-6 bg-blue-50 rounded-2xl border border-blue-100 shadow-sm p-4">
        <p class="text-sm font-medium text-blue-800">
            <i class="fas fa-info-circle mr-2"></i> Intermediary Access Note
        </p>
        <p class="text-sm text-blue-700 mt-1">
            Use the search box above to locate a policy by number, or a customer by name, phone, or vehicle
            registration.
            If you believe something is missing, please contact our support team.
        </p>
    </div>

    <!-- Policy Details Modal (unchanged — used by the Policy Number tab) -->
    <x-policy-details-modal />

    <!-- ===================== CUSTOMER POLICIES MODAL ===================== -->
    <div id="customerPoliciesModal" style="display:none;"
        class="fixed inset-0 bg-black/40 flex items-center justify-center p-4 z-50"
        onclick="if(event.target === this) closeCustomerModal()">
        <div class="bg-white rounded-2xl shadow-lg w-full max-w-2xl max-h-[85vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <div>
                    <h3 id="cust-modal-name" class="text-base font-semibold text-gray-900"></h3>
                    <p id="cust-modal-meta" class="text-xs text-gray-400"></p>
                </div>
                <button onclick="closeCustomerModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <div id="cust-modal-policies" class="p-6 space-y-3"></div>
        </div>
    </div>

    <script>
        function agentSearch() {
            return {
                tabs: [{
                        id: 'policy',
                        label: 'Policy number',
                        icon: 'fas fa-file-contract',
                        placeholder: 'e.g. P-1002-201-2026-012723',
                        hint: 'Checks your portfolio, then the insurer system if not found locally.'
                    },
                    {
                        id: 'name',
                        label: 'Client name',
                        icon: 'fas fa-user',
                        placeholder: 'e.g. Kwame Mensah',
                        hint: 'Searches customers linked to your portfolio.'
                    },
                    {
                        id: 'phone',
                        label: 'Phone number',
                        icon: 'fas fa-phone',
                        placeholder: 'e.g. 0244 123 456',
                        hint: 'Searches customers linked to your portfolio.'
                    },
                    {
                        id: 'vehicle',
                        label: 'Vehicle number',
                        icon: 'fas fa-car',
                        placeholder: 'e.g. GT 1234-24',
                        hint: 'Searches customers linked to your portfolio.'
                    },
                ],
                activeTab: 'policy',
                query: '',
                loading: false,
                searched: false,
                escalated: false,
                showEmptyError: false,
                minLengthError: false,
                requestError: null,
                policyResult: null,
                lastSource: null,
                customerResults: [],

                // Keep in sync with the 'query' => 'min:2' rule in SearchController.
                MIN_QUERY_LENGTH: 2,

                get currentTab() {
                    return this.tabs.find(t => t.id === this.activeTab);
                },

                switchTab(id) {
                    this.activeTab = id;
                    this.query = '';
                    this.searched = false;
                    this.showEmptyError = false;
                    this.minLengthError = false;
                    this.requestError = null;
                    this.policyResult = null;
                    this.customerResults = [];
                },

                initials(name) {
                    return (name || '').split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase();
                },

                viewDetails(policyId) {
                    window.viewDetails(policyId);
                },

                async runSearch() {
                    const trimmed = this.query.trim();

                    if (!trimmed) {
                        this.showEmptyError = true;
                        this.minLengthError = false;
                        return;
                    }
                    if (trimmed.length < this.MIN_QUERY_LENGTH) {
                        this.minLengthError = true;
                        this.showEmptyError = false;
                        return;
                    }
                    this.showEmptyError = false;
                    this.minLengthError = false;
                    this.requestError = null;
                    this.loading = true;
                    this.searched = false;
                    this.escalated = false;

                    let escalateTimer = null;
                    if (this.activeTab === 'policy') {
                        escalateTimer = setTimeout(() => {
                            this.escalated = true;
                        }, 1500);
                    }

                    try {
                        const url = new URL(@json(route('agent.customers.search')));
                        url.searchParams.set('type', this.activeTab);
                        url.searchParams.set('query', trimmed);

                        const response = await fetch(url, {
                            headers: {
                                'Accept': 'application/json'
                            },
                        });
                        const data = await response.json().catch(() => null);

                        if (!response.ok) {
                            // 422 = validation failure (e.g. query too short/long server-side),
                            // 404 = policy tab's "not found" (handled below, not an error state).
                            if (response.status === 422 && data?.errors) {
                                this.requestError = Object.values(data.errors).flat().join(' ');
                            } else if (response.status !== 404) {
                                this.requestError = data?.message ||
                                    `Search failed (error ${response.status}). Please try again.`;
                            }

                            if (response.status === 404 && this.activeTab === 'policy') {
                                this.policyResult = null;
                                this.lastSource = null;
                                this.searched = true;
                            }

                            return;
                        }

                        if (this.activeTab === 'policy') {
                            this.policyResult = data.success ? data.policy : null;
                            this.lastSource = data.source ?? null;

                            if (data.success && data.policy) {
                                window.policiesMap = window.policiesMap || {};
                                window.policiesMap[data.policy.policy_id] = {
                                    ...data.policy,
                                    claim_form_url: @json(route('agent.claims.create')) + '?policy_id=' + data.policy
                                        .policy_id,
                                };
                            }
                        } else {
                            this.customerResults = data.customers ?? [];
                        }

                        this.searched = true;
                    } catch (err) {
                        this.requestError =
                            'Could not reach the search service. Please check your connection and try again.';
                        console.error('[Agent Search Error]', err);
                    } finally {
                        clearTimeout(escalateTimer);
                        this.loading = false;
                    }
                },

                openCustomerModal(cust) {
                    document.getElementById('cust-modal-name').textContent = cust.name;
                    document.getElementById('cust-modal-meta').textContent = (cust.code || '—') + ' · ' + (cust.phone ||
                        '—');

                    const container = document.getElementById('cust-modal-policies');
                    container.innerHTML = cust.policies.map(pol => `
                        <div class="cust-policy-row border border-gray-200 rounded-xl overflow-hidden" data-policy-id="${pol.id}">
                            <button type="button" onclick="toggleCustomerPolicy(this, ${pol.id})"
                                class="w-full flex items-center justify-between gap-3 px-4 py-3 bg-white hover:bg-gray-50 transition-colors text-left">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">${pol.number}</p>
                                    <p class="text-xs text-gray-500">${pol.product} · ${pol.status}</p>
                                </div>
                                <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-200 cust-policy-chevron"></i>
                            </button>
                            <div class="cust-policy-body hidden border-t border-gray-100 bg-gray-50 px-4 py-4"></div>
                        </div>
                    `).join('');

                    document.getElementById('customerPoliciesModal').style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                }
            }
        }

        function closeCustomerModal() {
            document.getElementById('customerPoliciesModal').style.display = 'none';
            document.body.style.overflow = '';
        }

        // Route template for the risks endpoint — swap 0 for the real policy ID at call time.
        // Uses route() rather than a hardcoded path so it always matches wherever the
        // route is actually registered (prefix, middleware group, etc.).
        const policyRisksUrlTemplate = @json(route('agent.policies.risks', ['policy' => 0]));

        // Cache of fetched risk detail, keyed by policy id, so re-opening
        // an already-expanded row within the same page load is instant.
        const customerPolicyRiskCache = {};

        async function toggleCustomerPolicy(btn, policyId) {
            const row = btn.closest('.cust-policy-row');
            const body = row.querySelector('.cust-policy-body');
            const chevron = row.querySelector('.cust-policy-chevron');
            const isOpen = !body.classList.contains('hidden');

            if (isOpen) {
                body.classList.add('hidden');
                chevron.style.transform = '';
                return;
            }

            body.classList.remove('hidden');
            chevron.style.transform = 'rotate(180deg)';

            if (customerPolicyRiskCache[policyId]) {
                renderCustomerPolicyDetail(body, customerPolicyRiskCache[policyId]);
                return;
            }

            body.innerHTML = `
                <div class="flex items-center gap-2 text-sm text-gray-500 py-2">
                    <i class="fas fa-spinner fa-spin text-xs"></i> Fetching policy details...
                </div>`;

            try {
                const url = policyRisksUrlTemplate.replace('/0/', `/${policyId}/`);
                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    body.innerHTML =
                        `<p class="text-sm text-red-500 py-2">Could not load details for this policy (error ${response.status}). Try again shortly.</p>`;
                    console.error('[Customer Policy Risk Fetch] non-OK response', response.status, await response.text()
                        .catch(() => ''));
                    return;
                }

                const data = await response.json();

                if (!data.success) {
                    body.innerHTML =
                        `<p class="text-sm text-red-500 py-2">Could not load details for this policy. Try again shortly.</p>`;
                    return;
                }

                customerPolicyRiskCache[policyId] = data;
                renderCustomerPolicyDetail(body, data);
            } catch (err) {
                body.innerHTML =
                    `<p class="text-sm text-red-500 py-2">Could not reach the server. Please check your connection.</p>`;
                console.error('[Customer Policy Risk Fetch Error]', err);
            }
        }

        function renderCustomerPolicyDetail(body, data) {
            const policyForCard = {
                status: data.status,
                claim_form_url: data.claim_form_url
            };

            if (!data.risks || data.risks.length === 0) {
                body.innerHTML =
                    `<p class="text-sm text-gray-400 py-2">No vehicle/risk details available yet for this policy.</p>`;
                return;
            }

            body.innerHTML = data.risks
                .map((risk, i) => buildRiskCard(risk, policyForCard, data.is_fleet, i))
                .join('');
        }

        const policiesMap = @json($policiesMapData);
        window.policiesMap = policiesMap;

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
            const policy = window.policiesMap[policyId];
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
            statusEl.textContent = (policy.status || '').replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
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
        window.viewDetails = viewDetails;

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
            if (e.key === 'Escape') {
                closeModal();
                closeCustomerModal();
            }
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
