<x-layouts.staff>

    {{-- Page Header --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Customer Profile</h1>
            <p class="text-sm text-gray-500 mt-0.5">View details, policies, claims history and more</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('customers.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition shadow-sm">
                <i class="fas fa-arrow-left text-xs"></i> Back
            </a>
            <button onclick="window.location.reload()"
                class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition shadow-sm">
                <i class="fas fa-sync-alt text-xs"></i> Refresh
            </button>
        </div>
    </div>

    {{-- Compact Customer Header + Stats Pills (two rows) --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
        {{-- Row 1: Essential customer info --}}
        <div class="px-5 py-3 border-b border-gray-100">
            <div class="flex flex-wrap items-center gap-y-2 gap-x-1">
                {{-- Avatar + Name + Code --}}
                <div class="flex items-center gap-2">
                    <div
                        class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 text-xs font-semibold">
                        {{ strtoupper(substr($customer->name, 0, 2)) }}
                    </div>
                    <h2 class="text-sm font-semibold text-gray-900">{{ $customer->name }}</h2>
                    <span class="text-[11px] font-mono bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded-full">
                        {{ $customer->external_customer_code }}
                    </span>
                </div>

                {{-- Separator --}}
                <span class="text-gray-300 text-xs px-1">•</span>

                {{-- Email --}}
                <div class="flex items-center gap-2 text-gray-500 text-xs">
                    <i class="fas fa-envelope w-3"></i>
                    <span>{{ $customer->email }}</span>
                </div>

                {{-- Separator --}}
                <span class="text-gray-300 text-xs px-1">•</span>

                {{-- Phone --}}
                <div class="flex items-center gap-2 text-gray-500 text-xs">
                    <i class="fas fa-phone-alt w-3"></i>
                    <span>{{ $customer->phone }}</span>
                </div>

                {{-- Separator (only if address exists) --}}
                @if ($customer->address)
                    <span class="text-gray-300 text-xs px-1">•</span>

                    {{-- Address --}}
                    <div class="flex items-center gap-2 text-gray-500 text-xs">
                        <i class="fas fa-map-marker-alt w-3"></i>
                        <span>{{ Str::limit($customer->address, 40) }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Row 2: Stat pills --}}
        <div class="px-5 py-2.5 bg-gray-50/30 flex flex-wrap gap-2">
            <div
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-gray-200 shadow-sm text-xs">
                <span class="h-1.5 w-1.5 rounded-full bg-indigo-400"></span>
                <span class="text-gray-500">Active Policies</span>
                <span class="font-semibold text-gray-900">{{ $stats['active_policies'] }}</span>
            </div>
            <div
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-gray-200 shadow-sm text-xs">
                <span class="h-1.5 w-1.5 rounded-full bg-blue-400"></span>
                <span class="text-gray-500">Total Claims</span>
                <span class="font-semibold text-gray-900">{{ $stats['submitted_claims'] }}</span>
            </div>
            <div
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-gray-200 shadow-sm text-xs">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                <span class="text-gray-500">Approved / Closed</span>
                <span class="font-semibold text-gray-900">{{ $stats['closed_claims'] }}</span>
            </div>
            <div
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-gray-200 shadow-sm text-xs">
                <span class="h-1.5 w-1.5 rounded-full bg-amber-400"></span>
                <span class="text-gray-500">Pending</span>
                <span class="font-semibold text-gray-900">{{ $stats['pending_claims'] }}</span>
            </div>
        </div>
    </div>

    {{-- Policies & Claims Sections --}}
    <div class="space-y-8">

        {{-- Active Policies Table --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div
                class="px-6 py-4 border-b border-gray-100 bg-linear-to-r from-gray-50 to-white flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <i class="fas fa-file-contract text-blue-500 text-lg"></i>
                    <h3 class="text-base font-semibold text-gray-800">Policies</h3>
                    <span
                        class="text-xs bg-gray-200 text-gray-700 px-2 py-0.5 rounded-full">{{ $policies->total() }}</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Policy No.</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Product</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Period</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Source</th>
                            <th
                                class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($policies as $policy)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-mono text-sm font-medium text-gray-900">
                                    {{ $policy['policy_number'] }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <div class="font-medium">{{ $policy['business_class_name'] }}</div>
                                    <div class="text-xs text-gray-400">{{ $policy['product_name'] }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $policy['start_date'] }} – {{ $policy['end_date'] }}
                                </td>
                                <td class="px-6 py-4">
                                    @php $status = $policy['status']; @endphp
                                    @if ($status === 'expired')
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                            <i class="fas fa-circle text-[6px] mr-1"></i> Expired
                                        </span>
                                    @elseif ($status === 'pending_renewal')
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                                            <i class="fas fa-circle text-[6px] mr-1"></i> Expiring Soon
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                            <i class="fas fa-circle text-[6px] mr-1"></i> Active
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @php $source = strtolower($policy['source']); @endphp
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                {{ $source === 'genova' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }}">
                                        {{ ucfirst($policy['source']) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right relative" x-data="{ open: false }">
                                    <button @click="open = !open"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">
                                        Actions <i class="fas fa-chevron-down text-[10px]"></i>
                                    </button>
                                    <div x-show="open" @click.outside="open = false" x-transition
                                        class="absolute right-6 top-12 z-50 w-44 bg-white rounded-xl shadow-lg border border-gray-200 py-1.5">
                                        {{-- Only View Details here — Process Claim is inside the modal --}}
                                        <button @click="open = false; staffViewDetails({{ $policy['policy_id'] }})"
                                            class="w-full flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <i class="fas fa-eye text-blue-500 text-xs"></i> View Details
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <i class="fas fa-file-contract text-gray-300 text-4xl mb-3 block"></i>
                                    <p class="text-sm text-gray-500">No policies found for this customer.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($policies->total() > 0)
                <div
                    class="bg-gray-50 px-6 py-3 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3 text-xs text-gray-500">
                    <div>Showing {{ $policies->firstItem() }} to {{ $policies->lastItem() }} of
                        {{ $policies->total() }} policies</div>
                    <div class="flex gap-1">{{ $policies->links() }}</div>
                </div>
            @endif
        </div>

        {{-- Claims History Table --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div
                class="px-6 py-4 border-b border-gray-100 bg-linear-to-r from-gray-50 to-white flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <i class="fas fa-history text-blue-500 text-lg"></i>
                    <h3 class="text-base font-semibold text-gray-800">Claims History</h3>
                    <span
                        class="text-xs bg-gray-200 text-gray-700 px-2 py-0.5 rounded-full">{{ $claims->total() }}</span>
                </div>
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" placeholder="Search claims..."
                        class="pl-8 pr-3 py-1.5 text-sm border border-gray-300 rounded-lg w-64 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Policy No.</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Claim No.</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Type</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Risk Ref.</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Submitted</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Amount</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($claims as $claim)
                            @php $badge = \App\Enums\ClaimStatus::badge($claim->status); @endphp
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-mono text-sm font-medium text-gray-900">
                                    {{ $claim->policy->policy_number }}</td>
                                <td class="px-6 py-4 font-mono text-sm font-medium text-gray-900">
                                    {{ $claim->claim_number }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700 capitalize">
                                    {{ str_replace('_', ' ', $claim->claim_type) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $claim->registration_number ?? ' ' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $claim->submitted_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">GH₵
                                    {{ number_format($claim->amount, 2) }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $badge['class'] }}">
                                        {{ $badge['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right relative" x-data="{ open: false }">
                                    <button @click="open = !open"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">
                                        Actions <i class="fas fa-chevron-down text-[10px]"></i>
                                    </button>
                                    <div x-show="open" @click.outside="open = false" x-transition
                                        class="absolute right-6 top-12 z-50 w-44 bg-white rounded-xl shadow-lg border border-gray-200 py-1.5">
                                        <a href="{{ route('staff.claims.show', $claim) }}"
                                            class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <i class="fas fa-eye text-blue-500 text-xs"></i> View Details
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <i class="fas fa-inbox text-gray-300 text-4xl mb-3 block"></i>
                                    <p class="text-sm text-gray-500">No claims submitted yet.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($claims->total() > 0)
                <div
                    class="bg-gray-50 px-6 py-3 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3 text-xs text-gray-500">
                    <div>Showing {{ $claims->firstItem() }} to {{ $claims->lastItem() }} of {{ $claims->total() }}
                        claims</div>
                    <div class="flex gap-1">{{ $claims->links() }}</div>
                </div>
            @endif
        </div>
    </div>

    <x-policy-details-modal2 />

    {{-- Flash Message --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            class="fixed bottom-6 right-6 bg-green-600 text-white px-5 py-3 rounded-xl shadow-xl flex items-center gap-3 z-50">
            <i class="fas fa-check-circle"></i>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <script>
        const staffPoliciesMap = @json($policiesMapData);

        // ── Open modal ────────────────────────────────────────────────────────────────
        function staffViewDetails(policyId) {
            const policy = staffPoliciesMap[policyId];
            if (!policy) return;

            // Status badge
            const statusStyles = {
                active: 'text-green-700 bg-green-100',
                expired: 'text-red-700 bg-red-100',
                pending_renewal: 'text-amber-700 bg-amber-100',
            };
            const statusEl = document.getElementById('staff-modal-status');
            statusEl.textContent = policy.status.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
            statusEl.className =
                `text-xs font-semibold px-2.5 py-1 rounded-full ${statusStyles[policy.status] ?? 'text-gray-600 bg-gray-100'}`;

            // Basic fields
            document.getElementById('staff-modal-policy-number').textContent = policy.policy_number;
            document.getElementById('staff-modal-business-class').textContent = policy.business_class_name;
            document.getElementById('staff-modal-product').textContent = policy.product_name;
            document.getElementById('staff-modal-start-date').textContent = policy.start_date ?? '—';
            document.getElementById('staff-modal-end-date').textContent = policy.end_date ?? '—';
            document.getElementById('staff-modal-renewal-date').textContent = policy.renewal_date ?? '—';
            document.getElementById('staff-modal-customer-name').textContent = policy.customer_name ?? '—';
            document.getElementById('staff-modal-footer-name').textContent = policy.customer_name ?? 'the customer';

            // Risks
            const risks = Object.values(policy.risks ?? {});
            const isFleet = risks.length > 1;

            document.getElementById('staff-modal-risk-count').textContent = risks.length;
            document.getElementById('staff-modal-risks-list').innerHTML = risks.length ?
                risks.map(r => staffBuildRiskCard(r, policy, isFleet)).join('') :
                '<p class="text-sm text-gray-400 text-center py-6">No risk details available.</p>';

            // Footer claim button — only for single-risk policies
            // Fleet policies get a per-risk button inside each card instead
            const claimBtn = document.getElementById('staff-modal-claim-btn');
            if (isFleet) {
                claimBtn.style.display = 'none';
            } else {
                claimBtn.style.display = '';
                if (policy.status === 'expired') {
                    claimBtn.disabled = true;
                    claimBtn.className =
                        'px-4 py-2 text-sm rounded-lg flex items-center gap-2 bg-gray-200 text-gray-400 cursor-not-allowed opacity-60';
                    claimBtn.title = 'Policy is expired — claims cannot be processed.';
                    claimBtn.innerHTML = '<i class="fas fa-lock"></i> Process Claim';
                    claimBtn.onclick = null;
                } else {
                    claimBtn.disabled = false;
                    claimBtn.className =
                        'px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition flex items-center gap-2 shadow-sm';
                    claimBtn.title = '';
                    claimBtn.innerHTML = '<i class="fas fa-file-invoice"></i> Process Claim';
                    claimBtn.onclick = () => {
                        staffCloseModal();
                        window.location.href = policy.claim_create_url;
                    };
                }
            }

            document.getElementById('staffPolicyModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        // ── Close modal ───────────────────────────────────────────────────────────────
        function staffCloseModal() {
            document.getElementById('staffPolicyModal').style.display = 'none';
            document.body.style.overflow = '';
            const search = document.getElementById('staff-modal-risk-search');
            if (search) {
                search.value = '';
                staffFilterRisks('');
            }
        }

        document.getElementById('staffPolicyModal')?.addEventListener('click', e => {
            if (e.target.id === 'staffPolicyModal') staffCloseModal();
        });
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') staffCloseModal();
        });

        // ── Risk search ───────────────────────────────────────────────────────────────
        function staffFilterRisks(query) {
            const term = query.toLowerCase().trim();
            const cards = document.querySelectorAll('#staff-modal-risks-list .risk-card');
            let visible = 0;

            cards.forEach(card => {
                const matches = !term || (card.dataset.riskSearch || '').includes(term);
                card.style.display = matches ? '' : 'none';
                if (matches) visible++;
            });

            document.getElementById('staff-modal-risk-empty').classList.toggle('hidden', visible > 0);
            document.getElementById('staff-modal-risk-count').textContent = visible;
        }

        // ── Risk accordion toggle ─────────────────────────────────────────────────────
        function staffToggleRisk(btn) {
            const body = btn.closest('.risk-card').querySelector('.risk-body');
            const chevron = btn.querySelector('.risk-chevron');
            const isOpen = !body.classList.contains('hidden');
            body.classList.toggle('hidden', isOpen);
            chevron.style.transform = isOpen ? '' : 'rotate(180deg)';
        }

        // ── Build a risk card ─────────────────────────────────────────────────────────
        function staffBuildRiskCard(risk, policy, isFleet) {
            const regNo = risk.risk_ref_no || '—';
            const make = risk.vehicle_make || '';
            const model = risk.vehicle_model || '';
            const year = risk.vehicle_yr_manufacture || '';
            const chassis = risk.vehicle_chassis_no || '—';
            const colour = risk.vehicle_colour || '—';
            const sumInsured = risk.sum_insured ? `GHS ${parseFloat(risk.sum_insured).toLocaleString()}` : '—';
            const premium = risk.total_premium ? `GHS ${parseFloat(risk.total_premium).toLocaleString()}` : '—';
            const covers = Object.values(risk.covers ?? {});
            const isExpired = policy.status === 'expired';

            const coverTags = covers.map(c =>
                `<span class="text-xs px-2 py-1 bg-white border border-gray-200 rounded-md text-gray-600">${c.covername}</span>`
            ).join('');

            const iconHtml = make ?
                `<div class="flex-shrink-0 w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center"><i class="fas fa-car text-blue-600 text-sm"></i></div>` :
                `<div class="flex-shrink-0 w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center"><i class="fas fa-box text-purple-600 text-sm"></i></div>`;

            const title = make && model ? `${make} ${model}` : regNo;
            const subtitle = make ? `${regNo} · ${year}` : regNo;
            const searchData = [make, model, regNo, year, chassis].join(' ').toLowerCase();

            // Per-risk claim button — only rendered for fleet policies
            const riskUrl = `${policy.claim_create_url}&risk_id=${risk.id ?? ''}`;
            const claimButton = isFleet ? `
        <div class="border-t border-gray-200 pt-3 mt-3 flex justify-end">
            ${isExpired
                ? `<button disabled class="px-3 py-1.5 text-xs rounded-lg flex items-center gap-1.5 bg-gray-100 text-gray-400 cursor-not-allowed opacity-60">
                               <i class="fas fa-file-invoice"></i> Process Claim <i class="fas fa-lock ml-1 text-xs"></i>
                           </button>`
                : `<a href="${riskUrl}"
                               class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition flex items-center gap-1.5">
                               <i class="fas fa-file-invoice"></i> Process Claim
                           </a>`
            }
        </div>` : '';

            return `
        <div class="risk-card border border-gray-200 rounded-xl overflow-hidden" data-risk-search="${searchData}">
            <button type="button" onclick="staffToggleRisk(this)"
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
                    ${make    ? `<div><p class="text-xs text-gray-500 mb-0.5">Make &amp; Model</p><p class="text-sm font-semibold text-gray-900">${make} ${model}</p></div>` : ''}
                    ${year    ? `<div><p class="text-xs text-gray-500 mb-0.5">Year</p><p class="text-sm font-semibold text-gray-900">${year}</p></div>` : ''}
                    <div><p class="text-xs text-gray-500 mb-0.5">Chassis No.</p><p class="text-sm font-semibold text-gray-900">${chassis}</p></div>
                    <div><p class="text-xs text-gray-500 mb-0.5">Colour</p><p class="text-sm font-semibold text-gray-900">${colour}</p></div>
                    <div><p class="text-xs text-gray-500 mb-0.5">Sum Insured</p><p class="text-sm font-semibold text-gray-900">${sumInsured}</p></div>
                    <div><p class="text-xs text-gray-500 mb-0.5">Premium</p><p class="text-sm font-semibold text-gray-900">${premium}</p></div>
                </div>
                ${covers.length ? `
                            <div class="border-t border-gray-200 pt-3">
                                <p class="text-xs text-gray-500 mb-2">Covers Included</p>
                                <div class="flex flex-wrap gap-1.5">${coverTags}</div>
                            </div>` : ''}
                ${claimButton}
            </div>
        </div>`;
        }
    </script>
</x-layouts.staff>
