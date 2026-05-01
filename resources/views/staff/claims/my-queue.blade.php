<x-layouts.staff>
    <!-- Minimal Workspace Header -->
    <div class="mb-6">
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">
                        Welcome back, <span class="font-bold text-blue-500">{{ Auth::user()->name }}</span>
                    </p>
                    <h2 class="text-xl font-semibold text-gray-900">
                        Your Assigned Claims
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Review active claims, access supporting documents, and keep progress moving.
                    </p>
                </div>
                <button onclick="window.location.reload()"
                    class="bg-white border border-gray-300 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium text-gray-700 transition shadow-sm flex items-center gap-2">
                    <i class="fas fa-refresh text-gray-500"></i> Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Compact Neutral Stat Pills -->
    <div class="flex flex-wrap gap-3 mb-6">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gray-200 shadow-sm">
            <span class="h-2 w-2 rounded-full bg-indigo-400"></span>
            <span class="text-sm text-gray-600">Total</span>
            <span class="text-sm font-semibold text-gray-900">{{ $stats['total_claims'] }}</span>
        </div>

        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gray-200 shadow-sm">
            <span class="h-2 w-2 rounded-full bg-amber-400"></span>
            <span class="text-sm text-gray-600">Pending</span>
            <span class="text-sm font-semibold text-gray-900">{{ $stats['pending_claims'] }}</span>
        </div>

        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gray-200 shadow-sm">
            <span class="h-2 w-2 rounded-full bg-blue-400"></span>
            <span class="text-sm text-gray-600">Submitted</span>
            <span class="text-sm font-semibold text-gray-900">{{ $stats['submitted_claims'] }}</span>
        </div>

        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gray-200 shadow-sm">
            <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
            <span class="text-sm text-gray-600">Completed</span>
            <span class="text-sm font-semibold text-gray-900">{{ $stats['closed_claims'] }}</span>
        </div>
    </div>

    <!-- Premium Claims Desk -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Embedded Toolbar -->
        <div
            class="px-5 py-4 border-b border-gray-200 bg-gray-50 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">
                    Claims Assigned to You
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">
                    Use actions to review claim forms, attached files, or update status
                </p>
            </div>

            <div class="flex items-center gap-3">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" placeholder="Search client or policy..."
                        class="pl-8 pr-4 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-gray-300 w-64 bg-white" />
                </div>

                <button
                    class="bg-white border border-gray-300 hover:bg-gray-50 px-3 py-2 rounded-xl text-sm font-medium text-gray-700 transition flex items-center gap-2">
                    <i class="fas fa-filter text-xs"></i>
                    Filter
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto custom-scroll">
            <table class="min-w-[1050px] md:min-w-full w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Client</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Policy Number</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Product</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Policy Period</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Assigned By</th>
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
                            <td class="px-4 py-4 text-sm font-medium text-gray-900">{{ $claim->policy->product_name }}
                            </td>
                            <td class="px-4 py-4 text-xs text-gray-600">
                                {{ \Carbon\Carbon::parse($claim->policy->start_date)->format('M d, Y') }} -
                                {{ \Carbon\Carbon::parse($claim->policy->end_date)->format('M d, Y') }}</td>
                            <td class="px-4 py-4 text-sm text-gray-600">{{ $claim->assignedBy->name ?? 'Unassigned' }}
                            </td>
                            <td class="px-4 py-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-100">
                                    Pending
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right relative" x-data="{ open: false }"
                                style="overflow: visible;">
                                <button @click="open = !open"
                                    class="px-3 py-2 border border-gray-300 rounded-xl text-sm text-gray-700 hover:bg-gray-50">
                                    Actions <i class="fas fa-chevron-down text-xs ml-1"></i>
                                </button>
                                <div x-show="open" @click.outside="open = false" x-transition
                                    class="absolute right-4 top-12 z-50 w-48 bg-white rounded-xl shadow-lg border border-gray-200 py-2">
                                    <a href="{{ route('process-claim-motor') }}"
                                        class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                        <i class="fas fa-check-circle text-xs text-emerald-500"></i> Process Claim
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center gap-3 text-gray-500">

                                    <!-- Icon -->
                                    <div class="h-14 w-14 flex items-center justify-center rounded-full bg-green-100">
                                        <i class="fas fa-check text-xl text-green-600"></i>
                                    </div>

                                    <!-- Title -->
                                    <div class="text-sm font-semibold text-gray-800">
                                        You're all caught up 🎉
                                    </div>

                                    <!-- Description -->
                                    <div class="text-xs text-gray-400 max-w-xs">
                                        There are currently no active claims assigned to you. New assignments will
                                        appear here automatically.
                                    </div>

                                    <!-- Optional Actions -->
                                    <div class="flex items-center gap-2 mt-2">
                                        <a href="{{ route('staff.claims.index') }}"
                                            class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-700 transition">
                                            <i class="fas fa-list text-xs"></i>
                                            View All Claims
                                        </a>

                                        {{-- <a href="{{ route('staff.claims.index') }}"
                                            class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                                            <i class="fas fa-home text-xs"></i>
                                            Go to Dashboard
                                        </a> --}}
                                    </div>

                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-300 flex justify-between items-center flex-wrap gap-3">
            <div class="text-sm text-gray-500">
                @if ($claims->firstItem())
                    Showing {{ $claims->lastItem() }} of {{ $claims->total() }}
                    assigned claims
                @else
                    No assigned claims found
                @endif
            </div>
            <div class="flex gap-2">
                {{ $claims->links() }}
            </div>
        </div>
    </div>

    <!-- Workspace Tip -->
    <div class="mt-6 bg-blue-50/40 rounded-xl border border-blue-100 shadow-xs p-4">
        <p class="text-sm font-medium text-blue-800"><i class="fas fa-info-circle text-blue-500 text-lg"></i>
            Workspace tip
        </p>
        <p class="text-sm text-blue-800 mt-1">
            Keep pending claims at the top of your workflow. Resolve them first before moving to in-progress items.
        </p>
    </div>
</x-layouts.staff>
