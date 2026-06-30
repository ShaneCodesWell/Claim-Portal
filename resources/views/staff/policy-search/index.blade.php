<x-layouts.staff>

    <div class="mb-6">
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">
                        Staff Portal —
                        <span class="font-bold text-blue-500">Walk-in Claim Initiation</span>
                    </p>
                    <h2 class="text-xl font-semibold text-gray-900">
                        Find a Policy
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Search by phone number or policy number to load a customer's policy directly from GLIMS, then
                        process a claim on their behalf.
                    </p>
                </div>
                <a href="{{ route('customers.index') }}"
                    class="self-start lg:self-auto bg-white border border-gray-300 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium text-gray-700 transition shadow-sm flex items-center gap-2">
                    <i class="fas fa-arrow-left text-gray-400"></i> Back to Customers
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-start gap-3 mb-5">
            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                <i class="fas fa-search text-blue-600"></i>
            </div>
            <div>
                <h3 class="text-base font-semibold text-gray-900">Customer / Policy Lookup</h3>
                <p class="text-sm text-gray-500">
                    Enter a phone number (e.g. <span class="font-mono">0241234567</span>) or a policy number
                    (e.g. <span class="font-mono">P-1002-201-2026-012723</span>).
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('staff.policy-search.search') }}" class="flex flex-col sm:flex-row gap-3"
            x-data="{ loading: false }" @submit="loading = true">
            @csrf

            <div class="flex-1">
                <label for="query" class="block text-xs font-medium text-gray-600 mb-1">
                    Phone Number or Policy Number
                </label>
                <input id="query" type="text" name="query" value="{{ $searchQuery ?? '' }}"
                    placeholder="0241234567  or  P-1002-201-2026-012723" autofocus
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                           @error('query') border-red-400 @enderror">
                @error('query')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="sm:self-end">
                <button type="submit" :disabled="loading"
                    class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700
                           disabled:opacity-60 disabled:cursor-not-allowed
                           text-white px-6 py-2.5 rounded-xl text-sm font-medium
                           shadow-sm transition flex items-center justify-center gap-2 mt-5 sm:mt-0">
                    <i class="fas fa-spinner fa-spin text-xs" x-show="loading" x-cloak></i>
                    <i class="fas fa-search text-xs" x-show="!loading"></i>
                    <span x-text="loading ? 'Searching GLIMS...' : 'Search'"></span>
                </button>
            </div>
        </form>

        {{-- Inline search error --}}
        @if (!empty($searchError))
            <div
                class="mt-4 flex items-start gap-2 text-sm text-red-600
                        bg-red-50 border border-red-200 rounded-lg px-4 py-3">
                <i class="fas fa-exclamation-circle shrink-0 mt-0.5"></i>
                <span>{{ $searchError }}</span>
            </div>
        @endif

        <p class="text-xs text-gray-400 mt-4 flex items-center gap-1.5">
            <i class="fas fa-bolt"></i>
            This hits the GLIMS API directly — no OTP required. Results are synced to the local database automatically.
        </p>
    </div>

    {{-- ── Results ─────────────────────────────────────────────────────────── --}}
    @if ($searchQuery)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

            {{-- Result header --}}
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/60 flex items-center gap-2">
                <i class="fas fa-file-contract text-blue-500"></i>
                <div>
                    <h2 class="text-base font-bold text-gray-800">Search Results</h2>
                    <p class="text-xs text-gray-500">
                        Results for <span class="font-mono text-gray-700">{{ $searchQuery }}</span>
                    </p>
                </div>
            </div>

            @if (!empty($searchError))
                {{-- No results --}}
                <div class="py-20 text-center">
                    <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-file-circle-xmark text-red-400 text-2xl"></i>
                    </div>
                    <h3 class="text-base font-bold text-gray-800 mb-1">Nothing found</h3>
                    <p class="text-sm text-gray-500 max-w-sm mx-auto">
                        {{ $searchError }}
                    </p>
                </div>
            @elseif ($searchResult)
                @php
                    $customer = $searchResult['customer'];
                    $policies = $searchResult['policies'];
                @endphp

                {{-- Customer info strip --}}
                <div class="px-6 py-4 bg-blue-50 border-b border-blue-100">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                        {{-- Avatar --}}
                        <div
                            class="h-12 w-12 rounded-xl bg-blue-200 text-blue-700
                                    flex items-center justify-center text-lg font-bold shrink-0">
                            {{ strtoupper(substr($customer->name, 0, 1)) }}
                        </div>

                        {{-- Details --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900">
                                {{ $customer->name }}
                            </p>
                            <div class="flex flex-wrap gap-x-5 gap-y-1 mt-1 text-xs text-gray-500">
                                <span>
                                    <i class="fas fa-phone mr-1 text-gray-400"></i>
                                    {{ $customer->phone ?? '—' }}
                                </span>
                                <span>
                                    <i class="fas fa-envelope mr-1 text-gray-400"></i>
                                    {{ $customer->email ?? '—' }}
                                </span>
                                <span class="font-mono">
                                    <i class="fas fa-id-card mr-1 text-gray-400"></i>
                                    {{ $customer->external_customer_code ?? '—' }}
                                </span>
                            </div>
                        </div>

                        <a href="{{ route('customers.show', $customer) }}"
                            class="shrink-0 text-xs text-blue-600 hover:text-blue-800 font-medium underline">
                            View Full Profile →
                        </a>
                    </div>
                </div>

                {{-- Policies table --}}
                @if ($policies->isEmpty())
                    <div class="py-16 text-center text-gray-400">
                        <i class="fas fa-folder-open text-4xl mb-3 text-gray-300"></i>
                        <p class="text-sm font-medium text-gray-500">No policies found for this customer.</p>
                        <p class="text-xs mt-1">They may have no active policies in GLIMS.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Policy Details
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Product
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Expiry Date
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach ($policies as $policy)
                                    @php
                                        $risks = $policy->raw_payload['risks'] ?? [];
                                        $isFleet = count($risks) > 1;
                                        $policyParam = $policy->external_policy_id ?? $policy->id;
                                        $claimUrl = route('customers.claims.create', [
                                            'customer' => $customer->id,
                                            'policy_id' => $policyParam,
                                            'via' => 'policy_search',
                                        ]);
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-3.5">
                                            <div class="text-sm font-semibold text-gray-900">
                                                {{ $policy->business_class_name ?? '—' }}
                                            </div>
                                            <div class="text-xs font-mono text-gray-500 mt-0.5">
                                                {{ $policy->policy_number }}
                                            </div>
                                            @if ($isFleet)
                                                <span
                                                    class="inline-flex items-center mt-1 px-1.5 py-0.5
                                                             text-[10px] font-semibold rounded
                                                             bg-purple-100 text-purple-700">
                                                    FLEET ({{ count($risks) }} vehicles)
                                                </span>
                                            @elseif (!empty($risks[0]['risk_ref_no']))
                                                <div class="text-xs text-gray-400 mt-0.5">
                                                    {{ $risks[0]['risk_ref_no'] }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-3.5 text-xs text-gray-700">
                                            {{ $policy->product_name ?? '—' }}
                                        </td>
                                        <td class="px-6 py-3.5">
                                            <span
                                                class="px-2.5 py-1 inline-flex text-xs font-semibold rounded-full
                                                {{ $policy->status === 'active'
                                                    ? 'bg-green-100 text-green-700 border border-green-200'
                                                    : 'bg-red-100 text-red-700 border border-red-200' }}">
                                                {{ ucfirst($policy->status ?? 'unknown') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3.5 text-xs text-gray-700">
                                            {{ $policy->end_date ? \Carbon\Carbon::parse($policy->end_date)->format('d M Y') : '—' }}
                                        </td>
                                        <td class="px-6 py-3.5 text-right">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('customers.show', $customer) }}"
                                                    class="text-gray-700 hover:text-blue-600 bg-gray-100 hover:bg-blue-50
                                                           px-3 py-1.5 rounded-lg transition text-xs font-medium">
                                                    <i class="fas fa-eye mr-1"></i> View Customer
                                                </a>

                                                @if ($policy->status !== 'expired')
                                                    <a href="{{ $claimUrl }}"
                                                        class="text-gray-700 hover:text-green-600 bg-gray-100 hover:bg-green-50
                                                               px-3 py-1.5 rounded-lg transition text-xs font-medium">
                                                        <i class="fas fa-file-invoice mr-1"></i> Process Claim
                                                    </a>
                                                @else
                                                    <span
                                                        class="text-gray-400 bg-gray-50 px-3 py-1.5 rounded-lg text-xs
                                                                 font-medium border border-gray-200 cursor-not-allowed">
                                                        <i class="fas fa-ban mr-1"></i> Expired
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @endif
        </div>
    @endif

    {{-- ── Notice banner ───────────────────────────────────────────────────── --}}
    <div class="mt-6 bg-amber-50 rounded-2xl border border-amber-100 shadow-sm p-4">
        <p class="text-sm font-semibold text-amber-800">
            <i class="fas fa-triangle-exclamation mr-2"></i> Staff Action Notice
        </p>
        <p class="text-sm text-amber-700 mt-1">
            Any claim opened through this portal is logged against your staff account as the initiating officer.
            The customer will receive an SMS notification confirming that a claim has been opened on their behalf.
        </p>
    </div>

</x-layouts.staff>
