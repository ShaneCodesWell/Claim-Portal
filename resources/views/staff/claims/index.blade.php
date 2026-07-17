<x-layouts.staff>
    <!-- Header with stats and filters -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-clipboard-list text-blue-500 text-2xl"></i>
                Incoming Claims
            </h2>
            <p class="text-gray-500 text-sm mt-1">
                All customer-initiated claims · ready for team review
            </p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <form method="GET" action="{{ route('staff.claims.index') }}" id="filterForm">
                {{-- Preserve amount filter and branch when searching --}}
                <input type="hidden" name="filter" value="{{ request('filter', 'all') }}">
                <input type="hidden" name="branch" value="{{ request('branch') }}">

                <div class="flex items-center gap-3 flex-wrap">
                    {{-- Search --}}
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                            placeholder="Search client, policy..."
                            class="pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-blue-300 w-56 bg-white" />
                    </div>

                    {{-- Branch filter --}}
                    <select name="branch" onchange="this.form.submit()"
                        class="py-2 pl-3 pr-8 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-blue-300 bg-white text-gray-700">
                        <option value="">All Branches</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}"
                                {{ (string) request('branch') === (string) $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>

            {{-- Reset clears everything --}}
            <a href="{{ route('staff.claims.index') }}"
                class="bg-white border border-gray-300 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium text-gray-700 transition shadow-sm flex items-center gap-2">
                <i class="fas fa-refresh text-gray-500"></i> Reset
            </a>
        </div>
    </div>

    <!-- Claim Amount Filter Tabs -->
    <div class="flex flex-wrap gap-2 mb-6 border-b border-gray-200 pb-2">
        @foreach ([
        'all' => 'All Claims',
        'low' => 'Same Day (≤ 30k)',
        'medium' => 'Medium (30k - 100k)',
        'high' => 'High (> 100k)',
    ] as $value => $label)
            <a href="{{ route('staff.claims.index', array_merge(request()->only('search', 'branch'), ['filter' => $value])) }}"
                class="amount-filter-tab px-4 py-2 text-sm font-medium
                {{ request('filter', 'all') === $value
                    ? 'text-blue-600 border-b-2 border-blue-600'
                    : 'text-gray-500 hover:text-gray-700' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <!-- Claims Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto custom-scroll">
            <table class="min-w-300 md:min-w-full w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Client</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Policy Number</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Product</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Sum Insured</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Repair Bill</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Assigned To</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Status</th>
                        <th class="px-4 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody id="claimsTableBody" class="divide-y divide-gray-200">
                    @forelse ($claims as $claim)
                        <tr class="hover:bg-gray-50 transition" data-amount="{{ $claim->amount }}">
                            <td class="px-4 py-4 max-w-52">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div
                                        class="h-9 w-9 rounded-xl bg-gray-100 text-gray-700 flex items-center justify-center text-sm font-semibold shrink-0">
                                        {{ strtoupper(substr($claim->customer->name, 0, 1)) }}{{ strtoupper(substr(strrchr($claim->customer->name, ' '), 1, 1)) }}
                                    </div>
                                    <span class="text-sm font-medium text-gray-900 truncate">
                                        {{ $claim->customer->name }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-4 font-mono text-sm text-gray-700">
                                <div>{{ $claim->policy->policy_number }}</div>
                                @php
                                    $source = strtolower($claim->policy->source);
                                @endphp

                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-[0.7rem] font-medium
                                    {{ $source === 'genova' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                                    {{ strtoupper($claim->policy->source) }}
                                </span>
                            </td>
                            {{-- <td class="px-4 py-4 text-xs text-gray-700">
                                <div>{{ \Carbon\Carbon::parse($claim->policy->start_date)->format('M d, Y') }}</div>
                                <span class="text-xs text-gray-400">to</span>
                                {{ \Carbon\Carbon::parse($claim->policy->end_date)->format('M d, Y') }}</span>
                            </td> --}}
                            <td class="px-4 py-4 text-xs font-medium text-gray-900 max-w-40">
                                <span class="truncate block">{{ $claim->policy->product_name }}</span>
                            </td>
                            <td class="px-4 py-4 text-sm font-medium text-gray-900">GH₵
                                {{ number_format($claim->amount) }}</td>
                            <td class="px-4 py-4 text-sm font-medium text-gray-900">
                                GH₵ {{ number_format($claim->repair_estimate) }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-700">{{ $claim->assignedTo->name ?? 'Unassigned' }}
                            </td>
                            @php($badge = \App\Enums\ClaimStatus::badge($claim->status))
                            <td class="px-4 py-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $badge['class'] }}">
                                    {{ $badge['label'] }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right relative" x-data="{ open: false }"
                                style="overflow: visible;">
                                <button x-ref="claimActionsBtn" @click="open = !open"
                                    class="h-9 w-9 rounded-lg hover:bg-gray-100 text-gray-500 transition inline-flex items-center justify-center">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>

                                <template x-teleport="body">
                                    <div x-show="open" @click.outside="open = false" x-transition
                                        x-anchor.bottom-end="$refs.claimActionsBtn"
                                        class="fixed w-48 bg-white rounded-xl shadow-lg border border-gray-200 py-2 z-9999">
                                        <a href="{{ route('staff.claims.show', $claim->id) }}"
                                            class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                            <i class="fas fa-check-circle text-xs text-emerald-500"></i> Process Claim
                                        </a>
                                        <div class="border-t border-gray-100 my-1"></div>
                                    </div>
                                </template>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-10 text-center">
                                <div class="flex flex-col items-center justify-center gap-3 text-gray-500">
                                    <div class="h-14 w-14 flex items-center justify-center rounded-full bg-gray-100">
                                        <i class="fas fa-file-alt text-xl text-gray-400"></i>
                                    </div>

                                    <div class="text-sm font-medium text-gray-700">
                                        No claims available
                                    </div>

                                    <div class="text-xs text-gray-400 max-w-xs">
                                        There are currently no claims in the system. Once claims are created, they will
                                        appear here.
                                    </div>

                                    {{-- <a href="{{ route('create-claim') }}"
                                        class="mt-2 inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">
                                        <i class="fas fa-plus text-xs"></i>
                                        Create Claim
                                    </a> --}}
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer with pagination info -->
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-300 flex justify-between items-center flex-wrap gap-3">
            <div class="text-sm text-gray-500">
                @if ($claims->firstItem())
                    Showing {{ $claims->lastItem() }} of {{ $claims->total() }}
                    claims
                @else
                    No claims found
                @endif
            </div>
            <div class="flex gap-2">
                {{ $claims->links() }}
            </div>
        </div>
    </div>

    <div
        class="mt-6 bg-blue-50/40 rounded-xl border border-blue-100 p-4 flex flex-wrap justify-between items-center gap-3">
        <div class="flex items-center gap-3 text-sm text-blue-800">
            <i class="fas fa-info-circle text-blue-500 text-lg"></i>
            <span><strong>Claims team overview:</strong> Use the amount filters to triage claims by value.</span>
        </div>
        {{-- <div class="flex gap-2">
            <span class="bg-white px-3 py-1 rounded-full text-xs shadow-sm"><i class="far fa-file-alt"></i> Modal
                preview</span>
            <span class="bg-white px-3 py-1 rounded-full text-xs shadow-sm"><i class="far fa-folder-open"></i> File
                list modal</span>
        </div> --}}
    </div>

    <script>
        // Submit search form on Enter key
        document.getElementById('searchInput').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('filterForm').submit();
            }
        });
    </script>
</x-layouts.staff>
