<div class="overflow-x-auto custom-scroll">
    <table class="min-w-262.5 md:min-w-full w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                @foreach (['Client', 'Policy Number', 'Product', 'Policy Period', 'Assigned By', 'Status'] as $heading)
                    <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        {{ $heading }}</th>
                @endforeach
                <th class="px-4 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse ($claims as $claim)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-4 truncate max-w-52">
                        <div class="flex items-center gap-3">
                            <div
                                class="h-9 w-9 rounded-xl bg-gray-100 text-gray-700 flex items-center justify-center text-sm font-semibold">
                                {{ strtoupper(substr($claim->customer->name, 0, 1)) }}{{ strtoupper(substr(strrchr($claim->customer->name, ' '), 1, 1)) }}
                            </div>
                            <span class="text-sm font-medium text-gray-900">{{ $claim->customer->name }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-4 font-mono text-sm text-gray-700">
                        <div>{{ $claim->policy->policy_number }}</div>
                        @php $source = strtolower($claim->policy->source); @endphp
                        <span
                            class="inline-flex items-center px-2.5 py-1 rounded-full text-[0.7rem] font-medium
                            {{ $source === 'genova' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                            {{ strtoupper($claim->policy->source) }}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-sm font-medium text-gray-900 truncate max-w-42">
                        {{ $claim->policy->product_name }}
                    </td>
                    <td class="px-4 py-4 text-xs text-gray-600">
                        {{ \Carbon\Carbon::parse($claim->policy->start_date)->format('M d, Y') }} -
                        {{ \Carbon\Carbon::parse($claim->policy->end_date)->format('M d, Y') }}
                    </td>
                    <td class="px-4 py-4 text-sm text-gray-600">{{ $claim->assignedBy->name ?? 'Unassigned' }}</td>
                    @php $badge = \App\Enums\ClaimStatus::badge($claim->status); @endphp
                    <td class="px-4 py-4">
                        <span
                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $badge['class'] }}">
                            {{ $badge['label'] }}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-right relative" x-data="{ open: false }" style="overflow: visible;">
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
                                    @if (in_array($claim->status, \App\Enums\ClaimStatus::decided()))
                                        <i class="fas fa-flag-checkered text-xs text-orange-500"></i>
                                        {{ $claim->status === \App\Enums\ClaimStatus::REJECTED ? 'Close Claim' : 'Finalize Claim' }}
                                    @else
                                        <i class="fas fa-check-circle text-xs text-emerald-500"></i> Process Claim
                                    @endif
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                                @if (in_array($claim->status, \App\Enums\ClaimStatus::cancellable()))
                                    <form method="POST" action="{{ route('staff.claims.cancel', $claim->id) }}"
                                        onsubmit="return confirm('Reset this claim back to Submitted? It will be unassigned.')">
                                        @csrf
                                        <input type="hidden" name="note"
                                            value="Cancelled by {{ Auth::user()->name }} from queue.">
                                        <button type="submit"
                                            class="w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                                            <i class="fas fa-undo text-xs"></i> Cancel Claim
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </template>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center gap-3 text-gray-500">
                            @if (filled(request('search')))
                                <div class="h-14 w-14 flex items-center justify-center rounded-full bg-gray-100">
                                    <i class="fas fa-search text-xl text-gray-400"></i>
                                </div>
                                <div class="text-sm font-semibold text-gray-800">No matching claims</div>
                                <div class="text-xs text-gray-400 max-w-xs">
                                    Nothing in your queue matches "{{ request('search') }}". Check the spelling or try
                                    a policy or claim number.
                                </div>
                            @else
                                <div class="h-14 w-14 flex items-center justify-center rounded-full bg-green-100">
                                    <i class="fas fa-check text-xl text-green-600"></i>
                                </div>
                                <div class="text-sm font-semibold text-gray-800">You're all caught up 🎉</div>
                                <div class="text-xs text-gray-400 max-w-xs">
                                    There are currently no active claims assigned to you. New assignments will appear
                                    here automatically.
                                </div>
                                <a href="{{ route('staff.claims.index') }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-700 transition mt-2">
                                    <i class="fas fa-list text-xs"></i> View All Claims
                                </a>
                            @endif
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
            Showing {{ $claims->firstItem() }}–{{ $claims->lastItem() }} of {{ $claims->total() }} assigned claims
        @else
            No assigned claims found
        @endif
    </div>
    <div class="flex gap-2">
        {{ $claims->links() }}
    </div>
</div>
