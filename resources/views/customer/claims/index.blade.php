<x-layouts.app>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">
                        Welcome back, <span
                            class="font-bold text-blue-500">{{ $customer->name ?? 'Unknown Customer' }}</span>
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
            <table class="min-w-[900px] md:min-w-full w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Claim ID</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Policy / Product</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Submitted On</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Amount Requested</th>
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
                                ${{ number_format($claim->amount_requested, 2) }}</td>
                            <td class="px-4 py-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-100">
                                    Pending Review
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right relative" x-data="{ open: false }"
                                style="overflow: visible;">
                                <button @click="open = !open"
                                    class="px-3 py-2 border border-gray-300 rounded-xl text-sm text-gray-700 hover:bg-gray-50">
                                    Details <i class="fas fa-chevron-down text-xs ml-1"></i>
                                </button>
                                <div x-show="open" @click.outside="open = false" x-transition
                                    class="absolute right-4 top-12 z-50 w-56 bg-white rounded-xl shadow-lg border border-gray-200 py-2">
                                    <a href="{{ route('claims.show', $claim->id) }}"
                                        class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                        <i class="fas fa-eye text-xs text-blue-500"></i> View Full Details
                                    </a>
                                    <a href="#"
                                        class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                        <i class="fas fa-download text-xs text-gray-500"></i> Download Documents
                                    </a>
                                    <a href="#"
                                        class="w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-gray-50 flex items-center gap-2">
                                        <i class="fas fa-times-circle text-xs"></i> Cancel Claim
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-sm text-gray-500">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <i class="fas fa-inbox text-4xl text-gray-300"></i>
                                    <p class="text-gray-600">You haven't submitted any claims yet.</p>
                                    <a href="{{ route('dashboard') }}"
                                        class="mt-2 inline-flex items-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl shadow-sm transition">
                                        <i class="fas fa-plus-circle"></i> Start a Claim
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    <!-- Claim 1 - Pending -->


                    <!-- Claim 2 - In Progress -->
                    {{-- <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-4">
                            <span class="font-mono text-sm font-medium text-gray-900">CLM-2025-0187</span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm font-medium text-gray-900">Home Protector Plus</div>
                            <div class="text-xs text-gray-500">Policy: H-2203-892-2025-000012</div>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-600">Feb 28, 2025</td>
                        <td class="px-4 py-4 text-sm font-semibold text-gray-900">$7,200.00</td>
                        <td class="px-4 py-4">
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                In Progress
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right relative" x-data="{ open: false }" style="overflow: visible;">
                            <button @click="open = !open"
                                class="px-3 py-2 border border-gray-300 rounded-xl text-sm text-gray-700 hover:bg-gray-50">
                                Details <i class="fas fa-chevron-down text-xs ml-1"></i>
                            </button>
                            <div x-show="open" @click.outside="open = false" x-transition
                                class="absolute right-4 top-12 z-50 w-56 bg-white rounded-xl shadow-lg border border-gray-200 py-2">
                                <a href="#"
                                    class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                    <i class="fas fa-eye text-xs text-blue-500"></i> View Full Details
                                </a>
                                <a href="#"
                                    class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                    <i class="fas fa-upload text-xs text-gray-500"></i> Upload Additional Docs
                                </a>
                            </div>
                        </td>
                    </tr> --}}

                    <!-- Claim 3 - Completed / Approved -->
                    {{-- <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-4">
                            <span class="font-mono text-sm font-medium text-gray-900">CLM-2024-0982</span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm font-medium text-gray-900">Comprehensive Auto</div>
                            <div class="text-xs text-gray-500">Policy: P-1001-101-2026-000020</div>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-600">Dec 10, 2024</td>
                        <td class="px-4 py-4 text-sm font-semibold text-gray-900">$1,200.00</td>
                        <td class="px-4 py-4">
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                                Approved & Paid
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right relative" x-data="{ open: false }" style="overflow: visible;">
                            <button @click="open = !open"
                                class="px-3 py-2 border border-gray-300 rounded-xl text-sm text-gray-700 hover:bg-gray-50">
                                Details <i class="fas fa-chevron-down text-xs ml-1"></i>
                            </button>
                            <div x-show="open" @click.outside="open = false" x-transition
                                class="absolute right-4 top-12 z-50 w-56 bg-white rounded-xl shadow-lg border border-gray-200 py-2">
                                <a href="#"
                                    class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                    <i class="fas fa-receipt text-xs text-gray-500"></i> View Settlement
                                </a>
                                <a href="#"
                                    class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                    <i class="fas fa-download text-xs text-gray-500"></i> Download Documents
                                </a>
                            </div>
                        </td>
                    </tr> --}}

                    <!-- Claim 4 - Declined -->
                    {{-- <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-4">
                            <span class="font-mono text-sm font-medium text-gray-900">CLM-2025-0034</span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm font-medium text-gray-900">TravelSecure</div>
                            <div class="text-xs text-gray-500">Policy: T-5510-742-2025-000089</div>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-600">Jan 20, 2025</td>
                        <td class="px-4 py-4 text-sm font-semibold text-gray-900">$890.00</td>
                        <td class="px-4 py-4">
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-rose-50 text-rose-700 border border-rose-100">
                                Declined
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right relative" x-data="{ open: false }" style="overflow: visible;">
                            <button @click="open = !open"
                                class="px-3 py-2 border border-gray-300 rounded-xl text-sm text-gray-700 hover:bg-gray-50">
                                Details <i class="fas fa-chevron-down text-xs ml-1"></i>
                            </button>
                            <div x-show="open" @click.outside="open = false" x-transition
                                class="absolute right-4 top-12 z-50 w-56 bg-white rounded-xl shadow-lg border border-gray-200 py-2">
                                <a href="#"
                                    class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                    <i class="fas fa-info-circle text-xs text-red-500"></i> View Reason
                                </a>
                                <a href="#"
                                    class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                    <i class="fas fa-redo-alt text-xs text-gray-500"></i> Appeal Decision
                                </a>
                            </div>
                        </td>
                    </tr> --}}
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        {{-- <div class="bg-gray-50 px-6 py-3 border-t border-gray-200 flex justify-between items-center flex-wrap gap-3">
            <div class="text-sm text-gray-500">
                Showing <span class="font-medium">4</span> of <span class="font-medium">4</span> claims
            </div>

            <div class="flex gap-2">
                <button
                    class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm bg-white hover:bg-gray-50 opacity-50 cursor-not-allowed"
                    disabled>
                    Previous
                </button>
                <button class="px-3 py-1.5 bg-gray-900 text-white rounded-lg text-sm">1</button>
                <button class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm bg-white hover:bg-gray-50">
                    Next
                </button>
            </div>
        </div> --}}
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
</x-layouts.app>
