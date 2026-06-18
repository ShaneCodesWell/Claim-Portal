<x-layouts.staff>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-gavel text-orange-500 text-2xl"></i>
                Claims Committee Review
            </h2>
            <p class="text-gray-500 text-sm mt-1">
                Claims escalated for committee decision
            </p>
        </div>
    </div>

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
                            Claim Amount</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Escalated</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Status</th>
                        <th class="px-4 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($claims as $claim)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="h-9 w-9 rounded-xl bg-gray-100 text-gray-700 flex items-center justify-center text-sm font-semibold">
                                        {{ strtoupper(substr($claim->customer->name, 0, 1)) }}{{ strtoupper(substr(strrchr($claim->customer->name, ' '), 1, 1)) }}
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $claim->customer->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4 font-mono text-sm text-gray-700">{{ $claim->policy->policy_number }}
                            </td>
                            <td class="px-4 py-4 text-xs font-medium text-gray-900">{{ $claim->policy->product_name }}
                            </td>
                            <td class="px-4 py-4 text-sm font-medium text-gray-900">GH₵
                                {{ number_format($claim->amount) }}</td>
                            <td class="px-4 py-4 text-xs text-gray-700">
                                {{ $claim->committee_review_at?->format('M d, Y') ?? '—' }}</td>
                            @php($badge = \App\Enums\ClaimStatus::badge($claim->status))
                            <td class="px-4 py-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $badge['class'] }}">
                                    {{ $badge['label'] }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <a href="{{ route('committee.claims.show', $claim->id) }}"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-medium text-orange-700 bg-orange-50 rounded-lg hover:bg-orange-100 transition">
                                    <i class="fas fa-gavel text-xs"></i> Review &amp; Decide
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center">
                                <div class="flex flex-col items-center justify-center gap-3 text-gray-500">
                                    <div class="h-14 w-14 flex items-center justify-center rounded-full bg-gray-100">
                                        <i class="fas fa-gavel text-xl text-gray-400"></i>
                                    </div>
                                    <div class="text-sm font-medium text-gray-700">No claims awaiting decision</div>
                                    <div class="text-xs text-gray-400 max-w-xs">
                                        Claims escalated by staff will appear here for committee review.
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
</x-layouts.staff>
