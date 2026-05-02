<x-layouts.agent>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">
                        Agent Dashboard - <span
                            class="font-bold text-blue-500">{{ Auth::user()->name ?? 'Michael Chen' }}</span>
                    </p>
                    <h2 class="text-xl font-semibold text-gray-900">
                        Policy Access Portal
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Search for a specific policy by number, or browse the list of policies assigned to you.
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

        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <label class="block text-xs font-medium text-gray-600 mb-1">Policy Number or Vehicle #</label>
                <input type="text" placeholder="e.g., P-1001-101-2026-000020 or GR 1234 AB"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="sm:self-end">
                <button
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-medium shadow-sm transition flex items-center gap-2 mt-6 sm:mt-0">
                    <i class="fas fa-chevron-right text-xs"></i> Lookup Policy
                </button>
            </div>
        </div>
        <p class="text-xs text-gray-400 mt-3">
            <i class="fas fa-lock text-xs"></i> You can only access policies that have been assigned to you.
        </p>
    </div>

    <!-- Assigned Policies List -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Toolbar -->
        <div
            class="px-5 py-4 border-b border-gray-200 bg-gray-50 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">
                    Policies Assigned to You
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">
                    All active policies where you are the handling agent
                </p>
            </div>
            <div class="flex items-center gap-3">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" placeholder="Filter assigned list..."
                        class="pl-8 pr-4 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-gray-300 w-64 bg-white">
                </div>
                <button
                    class="bg-white border border-gray-300 hover:bg-gray-50 px-3 py-2 rounded-xl text-sm font-medium text-gray-700 transition flex items-center gap-2">
                    <i class="fas fa-filter text-xs"></i> Filter
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-[800px] w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Policy #</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Client / Vehicle</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Product</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Status</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Last Activity</th>
                        <th class="px-4 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <!-- Row 1 -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-4 font-mono text-sm text-gray-900">P-1001-101-2026-000020</td>
                        <td class="px-4 py-4">
                            <div class="text-sm font-medium text-gray-900">John Davis</div>
                            <div class="text-xs text-gray-500">Toyota Camry · GR 1234 AB</div>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-600">Comprehensive Auto</td>
                        <td class="px-4 py-4">
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-100">Active</span>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-500">2025-03-28</td>
                        <td class="px-4 py-4 text-right">
                            <a href="#"
                                class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center justify-end gap-1">
                                Access Policy <i class="fas fa-arrow-right text-xs"></i>
                            </a>
                        </td>
                    </tr>
                    <!-- Row 2 -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-4 font-mono text-sm text-gray-900">H-2203-892-2025-000012</td>
                        <td class="px-4 py-4">
                            <div class="text-sm font-medium text-gray-900">Marcus Davis</div>
                            <div class="text-xs text-gray-500">Property: 123 Maple St</div>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-600">Home Protector Plus</td>
                        <td class="px-4 py-4">
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-100">Pending
                                Review</span>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-500">2025-03-25</td>
                        <td class="px-4 py-4 text-right">
                            <a href="#"
                                class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center justify-end gap-1">
                                Access Policy <i class="fas fa-arrow-right text-xs"></i>
                            </a>
                        </td>
                    </tr>
                    <!-- Row 3 -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-4 font-mono text-sm text-gray-900">T-5510-742-2025-000089</td>
                        <td class="px-4 py-4">
                            <div class="text-sm font-medium text-gray-900">David Kim</div>
                            <div class="text-xs text-gray-500">Travel: Europe trip</div>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-600">TravelSecure</td>
                        <td class="px-4 py-4">
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">In
                                Progress</span>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-500">2025-03-20</td>
                        <td class="px-4 py-4 text-right">
                            <a href="#"
                                class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center justify-end gap-1">
                                Access Policy <i class="fas fa-arrow-right text-xs"></i>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200 flex justify-between items-center flex-wrap gap-3">
            <div class="text-sm text-gray-500">Showing <span class="font-medium">3</span> of <span
                    class="font-medium">3</span> assigned policies</div>
            <div class="flex gap-2">
                <button
                    class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm bg-white hover:bg-gray-50 opacity-50 cursor-not-allowed"
                    disabled>Previous</button>
                <button class="px-3 py-1.5 bg-gray-900 text-white rounded-lg text-sm">1</button>
                <button
                    class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm bg-white hover:bg-gray-50">Next</button>
            </div>
        </div>
    </div>

    <!-- Helpful Tip -->
    <div class="mt-6 bg-blue-50 rounded-2xl border border-blue-100 shadow-sm p-4">
        <p class="text-sm font-medium text-blue-800">
            <i class="fas fa-info-circle mr-2"></i> Agent Access Note
        </p>
        <p class="text-sm text-blue-700 mt-1">
            Use the search box above to quickly locate a specific policy by number. The list below shows all policies
            currently assigned to you. If you believe a policy is missing, please contact your supervisor.
        </p>
    </div>
</x-layouts.agent>
