<x-layouts.surveyor>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-search-location text-cyan-500 text-2xl"></i>
                Claims Under Survey
            </h2>
            <p class="text-gray-500 text-sm mt-1">
                Claims awaiting on-site assessment · submit your findings to move them forward
            </p>
        </div>
        <div class="flex items-center gap-3">
            <form method="GET" action="{{ route('surveyor.claims.index') }}" id="filterForm">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                        placeholder="Search client, policy..."
                        class="pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-cyan-300 w-64 bg-white" />
                    <input type="hidden" name="filter" value="{{ request('filter', 'all') }}">
                </div>
            </form>
            <a href="{{ route('surveyor.claims.index') }}"
                class="bg-white border border-gray-300 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium text-gray-700 transition shadow-sm flex items-center gap-2">
                <i class="fas fa-refresh text-gray-500"></i> Reset
            </a>
        </div>
    </div>

    <div class="flex flex-wrap gap-2 mb-6 border-b border-gray-200 pb-2">
        @foreach ([
            'all'    => 'All Claims',
            'low'    => 'Same Day (≤ 30k)',
            'medium' => 'Medium (30k – 100k)',
            'high'   => 'High (> 100k)',
        ] as $value => $label)
            <a href="{{ route('surveyor.claims.index', array_merge(request()->only('search'), ['filter' => $value])) }}"
                class="px-4 py-2 text-sm font-medium
                    {{ request('filter', 'all') === $value
                        ? 'text-cyan-600 border-b-2 border-cyan-600'
                        : 'text-gray-500 hover:text-gray-700' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto custom-scroll">
            <table class="min-w-300 md:min-w-full w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Client</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Policy Number</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Claim Amount</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Sent to Survey</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($claims as $claim)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-xl bg-gray-100 text-gray-700 flex items-center justify-center text-sm font-semibold">
                                        {{ strtoupper(substr($claim->customer->name, 0, 1)) }}{{ strtoupper(substr(strrchr($claim->customer->name, ' '), 1, 1)) }}
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $claim->customer->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4 font-mono text-sm text-gray-700">
                                <div>{{ $claim->policy->policy_number }}</div>
                                @php($source = strtolower($claim->policy->source))
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[0.7rem] font-medium
                                    {{ $source === 'genova' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                                    {{ strtoupper($claim->policy->source) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-xs font-medium text-gray-900">{{ $claim->policy->product_name }}</td>
                            <td class="px-4 py-4 text-sm font-medium text-gray-900">GH₵ {{ number_format($claim->amount) }}</td>
                            <td class="px-4 py-4 text-xs text-gray-700">{{ $claim->surveyed_at?->format('M d, Y') ?? '—' }}</td>
                            @php($badge = \App\Enums\ClaimStatus::badge($claim->status))
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $badge['class'] }}">
                                    {{ $badge['label'] }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <a href="{{ route('surveyor.claims.show', $claim->id) }}"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-medium text-cyan-700 bg-cyan-50 rounded-lg hover:bg-cyan-100 transition">
                                    <i class="fas fa-clipboard-check text-xs"></i> Review &amp; Submit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center">
                                <div class="flex flex-col items-center justify-center gap-3 text-gray-500">
                                    <div class="h-14 w-14 flex items-center justify-center rounded-full bg-gray-100">
                                        <i class="fas fa-search-location text-xl text-gray-400"></i>
                                    </div>
                                    <div class="text-sm font-medium text-gray-700">No claims pending survey</div>
                                    <div class="text-xs text-gray-400 max-w-xs">
                                        Claims sent for survey by staff will appear here for review.
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-gray-50 px-6 py-3 border-t border-gray-300 flex justify-between items-center flex-wrap gap-3">
            <div class="text-sm text-gray-500">
                @if ($claims->firstItem())
                    Showing {{ $claims->firstItem() }}–{{ $claims->lastItem() }} of {{ $claims->total() }} claims
                @else
                    No claims found
                @endif
            </div>
            <div class="flex gap-2">
                {{ $claims->links() }}
            </div>
        </div>
    </div>

    <script>
        document.getElementById('searchInput').addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('filterForm').submit();
            }
        });
    </script>
</x-layouts.surveyor>