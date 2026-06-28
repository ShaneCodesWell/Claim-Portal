<x-layouts.agent>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">
                        Agent Dashboard - <span
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
            <i class="fas fa-lock text-xs"></i> Access policies that have your polices here.
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
                    $key = strtolower($policy['business_class_name'] ?? '');
                    $claimFormUrl = ($claimFormRoutes[$key] ?? '/motor-form') . '?policyId=' . $policy['policy_id'];
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
                                <td class="px-6 py-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('agent.claims.show', ['claim' => $policy['policy_id']]) }}"
                                            class="text-gray-700 hover:text-blue-600 bg-gray-100 hover:bg-blue-50 px-3 py-1.5 rounded-lg transition text-xs font-medium">
                                            <i class="fas fa-eye mr-1"></i> View
                                        </a>
                                        @if (!$isFleet && $policy['status'] !== 'expired')
                                            <a href="{{ $claimFormUrl }}"
                                                class="text-gray-700 hover:text-green-600 bg-gray-100 hover:bg-green-50 px-3 py-1.5 rounded-lg transition text-xs font-medium">
                                                <i class="fas fa-file-invoice mr-1"></i> Process Claim
                                            </a>
                                        @endif
                                    </div>
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
</x-layouts.agent>
