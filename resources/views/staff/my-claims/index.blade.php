<x-layouts.staff>
    <!-- Minimal Workspace Header -->
    <div class="mb-6">
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">
                        Welcome back, Shane
                    </p>
                    <h2 class="text-2xl font-semibold text-gray-900">
                        Your Assigned Claims
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Review active claims, access supporting documents, and keep progress moving.
                    </p>
                </div>

                <button
                    class="bg-gray-900 hover:bg-black text-white px-4 py-2.5 rounded-xl text-sm font-medium shadow-sm transition flex items-center gap-2">
                    <i class="fas fa-download text-xs"></i>
                    Export Claims
                </button>
            </div>
        </div>
    </div>

    <!-- Compact Neutral Stat Pills -->
    <div class="flex flex-wrap gap-3 mb-6">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gray-200 shadow-sm">
            <span class="h-2 w-2 rounded-full bg-gray-400"></span>
            <span class="text-sm text-gray-600">Total</span>
            <span class="text-sm font-semibold text-gray-900">5</span>
        </div>

        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gray-200 shadow-sm">
            <span class="h-2 w-2 rounded-full bg-amber-400"></span>
            <span class="text-sm text-gray-600">Pending</span>
            <span class="text-sm font-semibold text-gray-900">2</span>
        </div>

        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gray-200 shadow-sm">
            <span class="h-2 w-2 rounded-full bg-blue-400"></span>
            <span class="text-sm text-gray-600">In Progress</span>
            <span class="text-sm font-semibold text-gray-900">2</span>
        </div>

        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gray-200 shadow-sm">
            <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
            <span class="text-sm text-gray-600">Completed</span>
            <span class="text-sm font-semibold text-gray-900">1</span>
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
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-9 w-9 rounded-xl bg-gray-100 text-gray-700 flex items-center justify-center text-sm font-semibold">
                                    JD
                                </div>
                                <span class="text-sm font-medium text-gray-900">John Davis</span>
                            </div>
                        </td>
                        <td class="px-4 py-4 font-mono text-sm text-gray-700">
                            <div>P-1001-101-2026-000020</div>
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-[0.7rem] font-medium bg-green-100 text-green-700">GLIMS</span>
                        </td>
                        <td class="px-4 py-4 text-sm font-medium text-gray-900">Comprehensive</td>
                        <td class="px-4 py-4 text-sm text-gray-600">Jan 15, 2024 - Jan 14, 2025</td>
                        <td class="px-4 py-4 text-sm text-gray-600">Shane Miller</td>
                        <td class="px-4 py-4">
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-100">
                                Pending
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right">
                            <button
                                class="px-3 py-2 border border-gray-300 rounded-xl text-sm text-gray-700 hover:bg-gray-50">
                                Actions <i class="fas fa-chevron-down text-xs ml-1"></i>
                            </button>
                        </td>
                    </tr>

                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-9 w-9 rounded-xl bg-gray-100 text-gray-700 flex items-center justify-center text-sm font-semibold">
                                    MD
                                </div>
                                <span class="text-sm font-medium text-gray-900">Marcus Davis</span>
                            </div>
                        </td>
                        <td class="px-4 py-4 font-mono text-sm text-gray-700">
                            <div>P-1001-101-2026-000023</div>
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-[0.7rem] font-medium bg-blue-100 text-blue-700">Genova</span>
                        </td>
                        <td class="px-4 py-4 text-sm font-medium text-gray-900">Comprehensive</td>
                        <td class="px-4 py-4 text-sm text-gray-600">Jan 15, 2024 - Jan 14, 2025</td>
                        <td class="px-4 py-4 text-sm text-gray-600">Admin Team</td>
                        <td class="px-4 py-4">
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                In Progress
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right">
                            <button
                                class="px-3 py-2 border border-gray-300 rounded-xl text-sm text-gray-700 hover:bg-gray-50">
                                Actions <i class="fas fa-chevron-down text-xs ml-1"></i>
                            </button>
                        </td>
                    </tr>

                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-9 w-9 rounded-xl bg-gray-100 text-gray-700 flex items-center justify-center text-sm font-semibold">
                                    DK
                                </div>
                                <span class="text-sm font-medium text-gray-900">David Kim</span>
                            </div>
                        </td>
                        <td class="px-4 py-4 font-mono text-sm text-gray-700">
                            <div>P-1001-103-2026-000035</div>
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-[0.7rem] font-medium bg-blue-100 text-blue-700">Genova</span>
                        </td>
                        <td class="px-4 py-4 text-sm font-medium text-gray-900">Commercial Fire</td>
                        <td class="px-4 py-4 text-sm text-gray-600">Nov 01, 2022 - Nov 01, 2024</td>
                        <td class="px-4 py-4 text-sm text-gray-600">Lisa Crawford</td>
                        <td class="px-4 py-4">
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                                Completed
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right">
                            <button
                                class="px-3 py-2 border border-gray-300 rounded-xl text-sm text-gray-700 hover:bg-gray-50">
                                Actions <i class="fas fa-chevron-down text-xs ml-1"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200 flex justify-between items-center">
            <div class="text-sm text-gray-500">
                Showing 3 of 3 assigned claims
            </div>

            <div class="flex gap-2">
                <button class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm bg-white hover:bg-gray-50">
                    Previous
                </button>
                <button class="px-3 py-1.5 bg-gray-900 text-white rounded-lg text-sm">
                    1
                </button>
                <button class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm bg-white hover:bg-gray-50">
                    Next
                </button>
            </div>
        </div>
    </div>

    <!-- Workspace Tip -->
    <div class="mt-6 bg-white rounded-2xl border border-gray-200 shadow-sm p-4">
        <p class="text-sm font-medium text-gray-800">
            Workspace tip
        </p>
        <p class="text-sm text-gray-500 mt-1">
            Keep pending claims at the top of your workflow. Resolve them first before moving to in-progress items.
        </p>
    </div>
</x-layouts.staff>
