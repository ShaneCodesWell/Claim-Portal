<x-layouts.staff>
    <!-- Header with actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-users text-blue-500 text-2xl"></i>
                All Policyholders
            </h2>
            <p class="text-gray-500 text-sm mt-1">
                Manage customers, view policies, and track claim history.
            </p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm shadow-sm flex items-center gap-2">
                <i class="fas fa-user-plus"></i> Add Policyholder
            </button>
        </div>
    </div>

    <!-- Compact Neutral Stat Pills -->
    <div class="flex flex-wrap gap-3 mb-6">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gray-200 shadow-sm">
            <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
            <span class="text-sm text-gray-600">Total Customers</span>
            <span class="text-sm font-semibold text-gray-900">{{ number_format($stats['total_customers']) }}</span>
        </div>

        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gray-200 shadow-sm">
            <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
            <span class="text-sm text-gray-600">Active Policies</span>
            <span class="text-sm font-semibold text-gray-900">{{ number_format($stats['active_policies']) }}</span>
        </div>

        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gray-200 shadow-sm">
            <span class="h-2 w-2 rounded-full bg-amber-400"></span>
            <span class="text-sm text-gray-600">Pending Claims</span>
            <span class="text-sm font-semibold text-gray-900">2</span>
        </div>

        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gray-200 shadow-sm">
            <span class="h-2 w-2 rounded-full bg-blue-400"></span>
            <span class="text-sm text-gray-600">Processed Claims</span>
            <span class="text-sm font-semibold text-gray-900">1</span>
        </div>
    </div>

    <!-- Policyholders Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">

        <!-- Embedded Toolbar -->
        <div
            class="px-5 py-4 border-b border-gray-200 bg-gray-50 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">
                    Policyholder Directory
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">
                    Browse customer records, view linked policies, and access claim activity
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
            <table class="min-w-[900px] w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Customer
                        </th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Email
                        </th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Phone
                        </th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Customer Code
                        </th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Policies
                        </th>
                        <th class="px-5 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($customers as $customer)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="h-9 w-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-sm font-semibold">
                                        {{ strtoupper(substr($customer->name, 0, 1)) }}{{ strtoupper(substr(strrchr($customer->name, ' '), 1, 1)) }}
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $customer->name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">
                                {{ $customer->email }}
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">
                                {{ $customer->phone }}
                            </td>
                            <td class="px-5 py-4 font-mono text-sm">{{ $customer->external_customer_code }}</td>
                            <td class="px-5 py-4">
                                <span class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded-full">
                                    {{ $customer->policies_count }}
                                    {{ Str::plural('Policy', $customer->policies_count) }}
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
                                    <a href="#"
                                        class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                        <i class="fas fa-edit text-xs text-blue-500"></i> Edit
                                    </a>
                                    <a href="#"
                                        class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                        <i class="fas fa-trash-alt text-xs text-red-500"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-gray-400">
                                    <div class="bg-gray-100 p-4 rounded-full">
                                        <i class="fas fa-users text-3xl text-gray-300"></i>
                                    </div>
                                    <p class="text-base font-medium text-gray-500">No policyholders found</p>
                                    <p class="text-sm text-gray-400">Add a new customer policy or adjust your search
                                        filters.
                                    </p>
                                    {{-- <a href="{{ route('staff.customers.create') }}"
                                        class="mt-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2">
                                        <i class="fas fa-user-plus"></i> Add Policyholder
                                    </a> --}}
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
                @if ($customers->firstItem())
                    Showing {{ $customers->lastItem() }} of {{ $customers->total() }}
                    policyholders
                @else
                    No policyholders found
                @endif
            </div>
            <div class="flex gap-2">
                {{ $customers->links() }}
            </div>
        </div>
    </div>

    <!-- Recently added / quick actions -->
    {{-- <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">
                    <i class="fas fa-chart-simple text-blue-500 mr-2"></i> Policy
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
                <i class="fas fa-bell text-blue-500 mr-2"></i> Recent Activity
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
    </div> --}}
</x-layouts.staff>
