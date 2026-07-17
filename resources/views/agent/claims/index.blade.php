<x-layouts.agent>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">
                        Welcome back, <span
                            class="font-bold text-blue-500">{{ Auth::guard('agent')->user()?->name ?? 'Intermediary' }}</span>
                    </p>
                    <h2 class="text-xl font-semibold text-gray-900">
                        My Claims
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Track the status of your submitted claims and access supporting documents.
                    </p>
                </div>

                <button onclick="window.location.reload()"
                    class="bg-white border border-gray-300 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium text-gray-700 transition shadow-sm flex items-center gap-2">
                    <i class="fas fa-refresh text-gray-500"></i> Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Claim Status Summary Pills -->
    <div class="flex flex-wrap gap-3 mb-6">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gray-200 shadow-sm">
            <span class="h-2 w-2 rounded-full bg-indigo-400"></span>
            <span class="text-sm text-gray-600">Total Claims</span>
            <span class="text-sm font-semibold text-gray-900">{{ $claims->total() }}</span>
        </div>

        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gray-200 shadow-sm">
            <span class="h-2 w-2 rounded-full bg-amber-400"></span>
            <span class="text-sm text-gray-600">Pending Review</span>
            <span class="text-sm font-semibold text-gray-900">{{ $claims->where('status', 'pending')->count() }}</span>
        </div>

        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gray-200 shadow-sm">
            <span class="h-2 w-2 rounded-full bg-blue-400"></span>
            <span class="text-sm text-gray-600">In Progress</span>
            <span
                class="text-sm font-semibold text-gray-900">{{ $claims->where('status', 'in_progress')->count() }}</span>
        </div>

        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gray-200 shadow-sm">
            <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
            <span class="text-sm text-gray-600">Approved</span>
            <span class="text-sm font-semibold text-gray-900">{{ $claims->where('status', 'approved')->count() }}</span>
        </div>

        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gray-200 shadow-sm">
            <span class="h-2 w-2 rounded-full bg-rose-400"></span>
            <span class="text-sm text-gray-600">Declined</span>
            <span class="text-sm font-semibold text-gray-900">{{ $claims->where('status', 'declined')->count() }}</span>
        </div>
    </div>

    <!-- Claims Table / List -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Toolbar -->
        <div
            class="px-5 py-4 border-b border-gray-200 bg-gray-50 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">
                    All Claim Requests
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">
                    Claims you've submitted across your active policies
                </p>
            </div>

            <div class="flex items-center gap-3">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" placeholder="Search by claim ID or policy..."
                        class="pl-8 pr-4 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-gray-300 w-64 bg-white" />
                </div>
                <button
                    class="bg-white border border-gray-300 hover:bg-gray-50 px-3 py-2 rounded-xl text-sm font-medium text-gray-700 transition flex items-center gap-2">
                    <i class="fas fa-filter text-xs"></i>
                    Filter
                </button>
            </div>
        </div>

        <!-- Table (Responsive) -->
        <div class="overflow-x-auto custom-scroll">
            <table class="min-w-225 md:min-w-full w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Claim ID</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Policy / Product</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Submitted On</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Sum Insured</th>
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
                                <span
                                    class="font-mono text-sm font-medium text-gray-900">{{ $claim->claim_number }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $claim->policy->product_name }}</div>
                                <div class="text-xs text-gray-500">Policy: {{ $claim->policy->policy_number }}
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-600">{{ $claim->created_at->format('M j, Y') }}</td>
                            <td class="px-4 py-4 text-sm font-semibold text-gray-900">
                                GH₵ {{ number_format($claim->amount, 2) }}</td>
                            <td class="px-4 py-4">
                                @php($badge = \App\Enums\ClaimStatus::badge($claim->status))
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $badge['class'] }}">
                                    {{ $badge['label'] }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right relative" x-data="{ open: false }"
                                style="overflow: visible;">

                                <button @click="open = !open"
                                    class="px-3 py-2 border border-gray-300 rounded-xl text-sm text-gray-700 hover:bg-gray-50 inline-flex items-center">
                                    Details
                                    <i class="fas fa-chevron-down text-xs ml-1"></i>
                                </button>

                                <div x-show="open" @click.outside="open = false" x-transition
                                    x-anchor.bottom-end="$el.previousElementSibling"
                                    class="fixed w-48 bg-white rounded-xl shadow-lg border border-gray-200 py-2 z-9999">

                                    <a href="{{ route('agent.claims.show', $claim->id) }}"
                                        class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                        <i class="fas fa-eye text-xs text-blue-500"></i>
                                        View Full Details
                                    </a>

                                    @if (in_array($claim->status, \App\Enums\ClaimStatus::cancellable()))
                                        <form method="POST" action="{{ route('agent.claims.cancel', $claim->id) }}"
                                            onsubmit="return confirm('Are you sure you want to cancel this claim? It will be sent back to Submitted.')">
                                            @csrf
                                            <button type="submit"
                                                class="w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                                                <i class="fas fa-undo text-xs"></i>
                                                Cancel Claim
                                            </button>
                                        </form>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-sm text-gray-500">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <i class="fas fa-inbox text-4xl text-gray-300"></i>
                                    <p class="text-gray-600">You haven't submitted any claims yet.</p>
                                    <a href="{{ route('agent.dashboard.index') }}"
                                        class="mt-2 inline-flex items-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl shadow-sm transition">
                                        <i class="fas fa-plus-circle"></i> Start a Claim
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-300 flex justify-between items-center flex-wrap gap-3">
            <div class="text-sm text-gray-500">
                <i class="fas fa-file mr-1"></i>
                @if ($claims->firstItem())
                    Showing {{ $claims->lastItem() }} of {{ $claims->total() }} claims
                @else
                    No claims found
                @endif
            </div>
            <div class="flex gap-2">
                {{ $claims->links() }}
            </div>
        </div>
    </div>

    <!-- Helpful Tip / Support Card -->
    <div
        class="mt-6 bg-white rounded-2xl border border-gray-200 shadow-sm p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-gray-800">
                <i class="fas fa-life-ring text-blue-500 mr-2"></i> Need help with a claim?
            </p>
            <p class="text-sm text-gray-500 mt-1">
                Contact our claims support team for assistance with pending or in-progress claims.
            </p>
        </div>
        <button
            class="bg-blue-50 hover:bg-blue-100 text-blue-700 px-4 py-2 rounded-xl text-sm font-medium transition flex items-center gap-2 w-full sm:w-auto justify-center">
            <i class="fas fa-headset"></i> Contact Support
        </button>
    </div>

</x-layouts.agent>
