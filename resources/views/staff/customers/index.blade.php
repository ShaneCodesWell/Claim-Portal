<x-layouts.staff>
    <!-- Header with actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-users text-indigo-500 text-2xl"></i>
                All Policyholders
            </h2>
            <p class="text-gray-500 text-sm mt-1">
                Manage customers, view policies, and track claim history.
            </p>
        </div>
        <div class="flex flex-wrap gap-3">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" placeholder="Search by name, policy..."
                    class="pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm w-64 bg-white" />
            </div>
            <button
                class="bg-white border border-gray-300 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2">
                <i class="fas fa-filter"></i> Filter
            </button>
            <button
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm shadow-sm flex items-center gap-2">
                <i class="fas fa-user-plus"></i> Add Policyholder
            </button>
        </div>
    </div>

    <!-- Stats cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Customers</p>
                <p class="text-2xl font-bold text-gray-800">124</p>
            </div>
            <div class="bg-indigo-100 p-3 rounded-full">
                <i class="fas fa-user-friends text-indigo-600 text-xl"></i>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Active Policies</p>
                <p class="text-2xl font-bold text-gray-800">187</p>
            </div>
            <div class="bg-green-100 p-3 rounded-full">
                <i class="fas fa-file-contract text-green-600 text-xl"></i>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Pending Claims</p>
                <p class="text-2xl font-bold text-gray-800">8</p>
            </div>
            <div class="bg-yellow-100 p-3 rounded-full">
                <i class="fas fa-clock text-yellow-600 text-xl"></i>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Premium (Annual)</p>
                <p class="text-2xl font-bold text-gray-800">$2.4M</p>
            </div>
            <div class="bg-purple-100 p-3 rounded-full">
                <i class="fas fa-dollar-sign text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Policyholders Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto custom-scroll">
            <table class="min-w-[900px] w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Customer
                        </th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Contact
                        </th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Policy Number
                        </th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Product
                        </th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Policy Period
                        </th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Claims
                        </th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-5 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <!-- John Davis -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-9 w-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-semibold">
                                    JD
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">John Davis</p>
                                    <p class="text-xs text-gray-400">Client since 2022</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-600">
                            john.davis@email.com<br /><span class="text-xs text-gray-400">(555) 123-4567</span>
                        </td>
                        <td class="px-5 py-4 font-mono text-sm">POL-AU-8723-01</td>
                        <td class="px-5 py-4">
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"><i
                                    class="fas fa-car"></i> Auto</span>
                        </td>
                        <td class="px-5 py-4 text-sm">01/15/2024 - 01/14/2025</td>
                        <td class="px-5 py-4">
                            <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded-full">1 claim</span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">Active</span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <button class="text-indigo-600 hover:text-indigo-800 mx-1">
                                <i class="fas fa-eye"></i></button><button
                                class="text-gray-500 hover:text-gray-700 mx-1">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                    <!-- Sarah Mitchell -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-9 w-9 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-semibold">
                                    SM
                                </div>
                                <div>
                                    <p class="font-medium">Sarah Mitchell</p>
                                    <p class="text-xs text-gray-400">Client since 2023</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-sm">
                            sarah.m@email.com<br /><span class="text-xs text-gray-400">(555) 234-5678</span>
                        </td>
                        <td class="px-5 py-4 font-mono text-sm">HOM-4562-89B</td>
                        <td class="px-5 py-4">
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-teal-100 text-teal-800"><i
                                    class="fas fa-home"></i> Home</span>
                        </td>
                        <td class="px-5 py-4 text-sm">09/10/2023 - 09/09/2024</td>
                        <td class="px-5 py-4">
                            <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded-full">1 claim</span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-1 rounded-full">Renewal
                                due</span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <button class="text-indigo-600">
                                <i class="fas fa-eye"></i></button><button class="text-gray-500 ml-2">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                    <!-- Michael Chen -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-4">
                            <div class="flex gap-3">
                                <div
                                    class="h-9 w-9 rounded-full bg-amber-100 flex items-center justify-center text-amber-700 font-semibold">
                                    MC
                                </div>
                                <div>
                                    <p class="font-medium">Michael Chen</p>
                                    <p class="text-xs text-gray-400">Client since 2020</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-sm">
                            mchen@email.com<br /><span class="text-xs">(555) 345-6789</span>
                        </td>
                        <td class="px-5 py-4 font-mono text-sm">LIFE-9983-X2C</td>
                        <td class="px-5 py-4">
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800"><i
                                    class="fas fa-heartbeat"></i> Life</span>
                        </td>
                        <td class="px-5 py-4 text-sm">05/20/2020 - 05/19/2035</td>
                        <td class="px-5 py-4">
                            <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded-full">1 claim</span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">Active</span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <button class="text-indigo-600">
                                <i class="fas fa-eye"></i></button><button class="text-gray-500 ml-2">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                    <!-- Olivia Rodriguez -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-4">
                            <div class="flex gap-3">
                                <div
                                    class="h-9 w-9 rounded-full bg-rose-100 flex items-center justify-center text-rose-700 font-semibold">
                                    OR
                                </div>
                                <div>
                                    <p class="font-medium">Olivia Rodriguez</p>
                                    <p class="text-xs">Client since 2024</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-sm">
                            olivia.r@email.com<br /><span class="text-xs">(555) 456-7890</span>
                        </td>
                        <td class="px-5 py-4 font-mono text-sm">TRV-1256-4AA</td>
                        <td class="px-5 py-4">
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800"><i
                                    class="fas fa-plane"></i> Travel</span>
                        </td>
                        <td class="px-5 py-4 text-sm">03/01/2024 - 09/01/2024</td>
                        <td class="px-5 py-4">
                            <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded-full">1 claim</span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">Expired</span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <button class="text-indigo-600">
                                <i class="fas fa-eye"></i></button><button class="text-gray-500 ml-2">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                    <!-- David Kim -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-4">
                            <div class="flex gap-3">
                                <div
                                    class="h-9 w-9 rounded-full bg-slate-100 flex items-center justify-center text-slate-700 font-semibold">
                                    DK
                                </div>
                                <div>
                                    <p class="font-medium">David Kim</p>
                                    <p class="text-xs">Client since 2022</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-sm">
                            david.kim@email.com<br /><span class="text-xs">(555) 567-8901</span>
                        </td>
                        <td class="px-5 py-4 font-mono text-sm">COM-7789-3BZ</td>
                        <td class="px-5 py-4">
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800"><i
                                    class="fas fa-building"></i> Commercial</span>
                        </td>
                        <td class="px-5 py-4 text-sm">11/01/2022 - 11/01/2024</td>
                        <td class="px-5 py-4">
                            <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded-full">1 claim</span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">Active</span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <button class="text-indigo-600">
                                <i class="fas fa-eye"></i></button><button class="text-gray-500 ml-2">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                    <!-- Emily Watson (Pet) extra -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-4">
                            <div class="flex gap-3">
                                <div
                                    class="h-9 w-9 rounded-full bg-pink-100 flex items-center justify-center text-pink-700 font-semibold">
                                    EW
                                </div>
                                <div>
                                    <p class="font-medium">Emily Watson</p>
                                    <p class="text-xs">Client since 2023</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-sm">
                            emily.w@email.com<br /><span class="text-xs">(555) 678-9012</span>
                        </td>
                        <td class="px-5 py-4 font-mono text-sm">PET-3321-7GG</td>
                        <td class="px-5 py-4">
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-pink-100 text-pink-800"><i
                                    class="fas fa-paw"></i> Pet</span>
                        </td>
                        <td class="px-5 py-4 text-sm">01/10/2023 - 01/09/2025</td>
                        <td class="px-5 py-4">
                            <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded-full">1 claim</span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">Active</span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <button class="text-indigo-600">
                                <i class="fas fa-eye"></i></button><button class="text-gray-500 ml-2">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                    <!-- Robert Turner (no claims yet) -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-4">
                            <div class="flex gap-3">
                                <div
                                    class="h-9 w-9 rounded-full bg-gray-200 flex items-center justify-center text-gray-700 font-semibold">
                                    RT
                                </div>
                                <div>
                                    <p class="font-medium">Robert Turner</p>
                                    <p class="text-xs">Client since 2024</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-sm">
                            robert.t@email.com<br /><span class="text-xs">(555) 789-0123</span>
                        </td>
                        <td class="px-5 py-4 font-mono text-sm">AUTO-4421-9FF</td>
                        <td class="px-5 py-4">
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"><i
                                    class="fas fa-car"></i> Auto</span>
                        </td>
                        <td class="px-5 py-4 text-sm">03/15/2024 - 03/14/2025</td>
                        <td class="px-5 py-4">
                            <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full">0 claims</span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">Active</span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <button class="text-indigo-600">
                                <i class="fas fa-eye"></i></button><button class="text-gray-500 ml-2">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                    <!-- Lisa Garcia (Home) -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-4">
                            <div class="flex gap-3">
                                <div
                                    class="h-9 w-9 rounded-full bg-teal-100 flex items-center justify-center text-teal-700 font-semibold">
                                    LG
                                </div>
                                <div>
                                    <p class="font-medium">Lisa Garcia</p>
                                    <p class="text-xs">Client since 2021</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-sm">
                            lisa.g@email.com<br /><span class="text-xs">(555) 890-1234</span>
                        </td>
                        <td class="px-5 py-4 font-mono text-sm">HOME-2387-4KK</td>
                        <td class="px-5 py-4">
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-teal-100 text-teal-800"><i
                                    class="fas fa-home"></i> Home</span>
                        </td>
                        <td class="px-5 py-4 text-sm">07/22/2021 - 07/21/2025</td>
                        <td class="px-5 py-4">
                            <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full">0 claims</span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">Active</span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <button class="text-indigo-600">
                                <i class="fas fa-eye"></i></button><button class="text-gray-500 ml-2">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="bg-gray-50 px-6 py-3 border-t flex justify-between items-center flex-wrap gap-3">
            <div class="text-sm text-gray-500">
                Showing 8 of 124 policyholders
            </div>
            <div class="flex gap-2">
                <button class="px-3 py-1 border rounded-md text-sm bg-white">
                    Previous</button><button class="px-3 py-1 bg-indigo-600 text-white rounded-md text-sm">
                    1</button><button class="px-3 py-1 border rounded-md text-sm bg-white">
                    2</button><button class="px-3 py-1 border rounded-md text-sm bg-white">
                    3</button><button class="px-3 py-1 border rounded-md text-sm bg-white">
                    Next
                </button>
            </div>
        </div>
    </div>

    <!-- Recently added / quick actions -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">
                    <i class="fas fa-chart-simple text-indigo-500 mr-2"></i> Policy
                    Distribution
                </h3>
                <span class="text-xs text-gray-400">by product type</span>
            </div>
            <div class="space-y-3">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Auto Insurance</span><span>42%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full" style="width: 42%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Home Insurance</span><span>28%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-teal-500 h-2 rounded-full" style="width: 28%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Life Insurance</span><span>15%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-purple-500 h-2 rounded-full" style="width: 15%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Travel & Other</span><span>15%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-cyan-500 h-2 rounded-full" style="width: 15%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-800 mb-3">
                <i class="fas fa-bell text-indigo-500 mr-2"></i> Recent Activity
            </h3>
            <div class="space-y-3 text-sm">
                <div class="flex items-start gap-3">
                    <div class="w-1.5 h-1.5 mt-2 rounded-full bg-green-500"></div>
                    <div>
                        <span class="font-medium">John Davis</span> submitted a claim
                        with documents
                        <span class="text-gray-500 text-xs">2 days ago</span>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-1.5 h-1.5 mt-2 rounded-full bg-blue-500"></div>
                    <div>
                        <span class="font-medium">Sarah Mitchell</span> renewed home
                        policy <span class="text-gray-500 text-xs">5 days ago</span>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-1.5 h-1.5 mt-2 rounded-full bg-amber-500"></div>
                    <div>
                        <span class="font-medium">Michael Chen</span> uploaded medical
                        certificate
                        <span class="text-gray-500 text-xs">1 week ago</span>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-1.5 h-1.5 mt-2 rounded-full bg-purple-500"></div>
                    <div>
                        <span class="font-medium">Olivia Rodriguez</span> travel claim
                        reviewed <span class="text-gray-500 text-xs">3 days ago</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.staff>
