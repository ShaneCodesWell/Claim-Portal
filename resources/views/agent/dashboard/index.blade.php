<x-layouts.agent>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">
                        Agent Dashboard - <span
                            class="font-bold text-blue-500">{{ Auth::guard('agent')->user()?->name ?? 'Unknown' }}</span>
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
    {{-- <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-8">
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
                    <i class="fas fa-search text-xs"></i> Lookup Policy
                </button>
            </div>
        </div>
        <p class="text-xs text-gray-400 mt-3">
            <i class="fas fa-lock text-xs"></i> Access policies that have your polices here.
        </p>
    </div> --}}

    <!-- Assigned Policies List -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-3 border-b border-gray-200 bg-gray-50/50 rounded-t-xl">
                <div class="flex flex-col space-y-3 md:space-y-0 md:flex-row md:items-center md:justify-between">
                    <!-- Header text -->
                    <div>
                        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-file-contract text-blue-500"></i>
                            Intermediary Policies
                        </h2>
                        <p class="text-xs text-gray-500 mt-0.5">Click on any policy to view details or file a claim</p>
                    </div>

                    <!-- Filters -->
                    <div class="w-full md:w-auto">
                        <form method="GET" action="{{ route('dashboard') }}" id="filter-form">
                            <div class="flex flex-wrap items-center gap-2">
                                <!-- Search - grows, becomes longer -->
                                <div class="relative flex-1 min-w-50 md:flex-2">
                                    <i
                                        class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                    <input type="text" name="search" id="search-input"
                                        value="{{ request('search') }}" placeholder="Search..."
                                        class="pl-9 pr-4 py-2 border border-gray-300 rounded-xl w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm bg-white" />
                                </div>

                                <!-- Type - shorter width, less vertical padding -->
                                <select name="type" id="type-select"
                                    class="px-3 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-700 text-sm w-auto md:w-32">
                                    <option value="">All Types</option>
                                    @foreach ($businessClasses as $class)
                                        <option value="{{ $class }}" @selected(request('type') === $class)>
                                            {{ $class }}
                                        </option>
                                    @endforeach
                                </select>

                                <!-- Status - shorter width, less vertical padding -->
                                <select name="status" id="status-select"
                                    class="px-3 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-700 text-sm w-auto md:w-32">
                                    <option value="">All Statuses</option>
                                    <option value="active" @selected(request('status') === 'active')>Active</option>
                                    <option value="expired" @selected(request('status') === 'expired')>Expired</option>
                                </select>

                                <!-- Clear button - matches new height -->
                                @if (request()->hasAny(['search', 'type', 'status']))
                                    <a href="{{ route('dashboard') }}"
                                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition flex items-center gap-2 text-sm font-medium whitespace-nowrap">
                                        <i class="fas fa-times-circle"></i> Clear
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            @if (!isset($policies) || count($policies) === 0)
                <div id="empty-state" class="p-16 text-center">
                    <div class="w-32 h-32 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-folder-open text-blue-400 text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">No policies found</h3>
                    <p class="text-gray-500 max-w-md mx-auto mb-6">
                        Try adjusting your search or filter criteria, or refresh to sync the latest policies.
                    </p>
                    <button onclick="location.reload()"
                        class="bg-blue-600 text-white px-6 py-2.5 rounded-xl font-medium hover:bg-blue-700 transition inline-flex items-center gap-2">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full table-fixed divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Policy Details</th>
                                <th
                                    class="px-6 py-3 w-60 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Policy Number</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Insured Name</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Product</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Renewal Date</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100" id="policies-table-body">
                            @foreach ($policies as $policy)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-3">
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">
                                                {{ $policy['business_class_name'] }}
                                            </div>

                                            <div class="text-xs text-gray-500">
                                                {{ $policy['vehicle_number'] ?? ' ' }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-3">
                                        <div class="text-xs font-mono font-medium text-gray-900">
                                            {{ $policy['policy_number'] }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-3">
                                        <div class="text-xs font-medium text-gray-900">
                                            {{ ucwords(strtolower($customer->name)) }}
                                        </div>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            {{ $customer->external_customer_code }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-3">
                                        <div class="text-xs font-medium text-gray-900">
                                            {{ $policy['product_name'] }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-3">
                                        <span
                                            class="px-3 py-1 inline-flex text-xs font-semibold rounded-full
                                                {{ $policy['status'] === 'active'
                                                    ? 'bg-green-100 text-green-700 border border-green-200'
                                                    : ($policy['status'] === 'pending_renewal'
                                                        ? 'bg-amber-100 text-amber-700 border border-amber-200'
                                                        : 'bg-red-100 text-red-700 border border-red-200') }}">
                                            {{ ucfirst(str_replace('_', ' ', $policy['status'])) }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-3">
                                        <div class="text-xs text-gray-900 font-medium">
                                            {{ $policy['renewal_date'] ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-3 text-right">
                                        @php
                                            $key = strtolower($policy['business_class_name'] ?? '');
                                            $claimFormUrl =
                                                ($claimFormRoutes[$key] ?? '/motor-form') .
                                                '?policyId=' .
                                                $policy['policy_id'];
                                            $isFleet = count($policy['risks'] ?? []) > 1;
                                        @endphp
                                        <div class="relative inline-block">
                                            <button onclick="toggleDropdown(event, {{ $policy['policy_id'] }})"
                                                id="dropdown-button-{{ $policy['policy_id'] }}"
                                                class="text-gray-700 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors inline-flex items-center font-medium text-sm">
                                                Actions <i class="fas fa-chevron-down ml-2 text-xs"></i>
                                            </button>
                                            <div id="dropdown-{{ $policy['policy_id'] }}"
                                                class="hidden absolute right-0 mt-1 w-48 rounded-lg shadow-lg bg-white ring-1 ring-gray-200 z-30">
                                                <div class="py-1">
                                                    <button onclick="viewDetails({{ $policy['policy_id'] }})"
                                                        class="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 flex items-center transition">
                                                        <i class="fas fa-eye mr-2"></i> View Details
                                                    </button>
                                                    @if (!$isFleet)
                                                        @if ($policy['status'] === 'expired')
                                                            <button onclick="showExpiredPolicyAlert()"
                                                                class="w-full text-left px-4 py-2.5 text-sm flex items-center text-gray-400 cursor-not-allowed opacity-50">
                                                                <i class="fas fa-file-invoice mr-2"></i> Process Claim
                                                                <i class="fas fa-lock ml-auto text-xs"></i>
                                                            </button>
                                                        @else
                                                            <a href="{{ $claimFormUrl }}"
                                                                class="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600 flex items-center transition">
                                                                <i class="fas fa-file-invoice mr-2"></i> Process Claim
                                                            </a>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- Pagination --}}
                <div
                    class="bg-gray-50 px-6 py-3 border-t border-gray-300 flex justify-between items-center flex-wrap gap-3">
                    <div class="text-sm text-gray-500">
                        @if ($policies->firstItem())
                            Showing {{ $policies->lastItem() }} of {{ $policies->total() }}
                            policies
                        @else
                            No policies found
                        @endif
                    </div>
                    <div class="flex gap-2">
                        {{ $policies->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Helpful Tip -->
    <div class="mt-6 bg-blue-50 rounded-2xl border border-blue-100 shadow-sm p-4">
        <p class="text-sm font-medium text-blue-800">
            <i class="fas fa-info-circle mr-2"></i> Agent Access Note
        </p>
        <p class="text-sm text-blue-700 mt-1">
            Use the search box above to quickly locate a specific policy by number. The list below shows all policies
            currently brought in by you. If you believe a policy is missing, please contact Support.
        </p>
    </div>
</x-layouts.agent>
