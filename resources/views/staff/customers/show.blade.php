<x-layouts.staff>

    {{-- Page Header with Back Button --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div>
                <h1 class="text-xl font-bold text-gray-800">
                    Customer Profile
                </h1>
                <p class="text-sm text-gray-500 mt-0.5">
                    View details, policies, and claims history
                </p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('customers.index') }}"
                class="bg-white border border-gray-300 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium text-gray-700 transition shadow-sm flex items-center gap-2">
                <i class="fas fa-arrow-left text-sm"></i>Back
            </a>
            <button onclick="window.location.reload()"
                class="bg-white border border-gray-300 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium text-gray-700 transition shadow-sm flex items-center gap-2">
                <i class="fas fa-refresh text-gray-500"></i> Refresh
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ==================== LEFT COLUMN – Customer Info ==================== --}}
        <div class="space-y-5">

            {{-- Quick Stats Card --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-chart-line text-blue-500"></i> Summary
                    </h3>
                </div>
                <div class="p-4 grid grid-cols-2 gap-3 text-center">
                    <div class="bg-gray-50 rounded-lg p-2">
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['active_policies'] }}</p>
                        <p class="text-xs text-gray-500">Active Policies</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-2">
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['submitted_claims'] }}</p>
                        <p class="text-xs text-gray-500">Total Claims</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-2">
                        <p class="text-2xl font-bold text-green-600">{{ $stats['closed_claims'] }}</p>
                        <p class="text-xs text-gray-500">Approved Claims</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-2">
                        <p class="text-2xl font-bold text-amber-600">{{ $stats['pending_claims'] }}</p>
                        <p class="text-xs text-gray-500">Pending Claims</p>
                    </div>
                </div>
            </div>

            {{-- Customer Card --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-user-circle text-blue-500"></i> Customer Information
                    </h3>
                    {{-- <button class="text-xs text-blue-600 hover:underline flex items-center gap-1">
                        <i class="fas fa-edit"></i> Edit
                    </button> --}}
                </div>
                <div class="p-4">
                    <div class="flex items-center gap-4 mb-4">
                        <div
                            class="h-12 w-12 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-lg font-semibold">
                            {{ strtoupper(substr($customer->name, 0, 1)) }}{{ strtoupper(substr(strrchr($customer->name, ' '), 1, 1)) }}
                        </div>
                        <div>
                            <p class="text-base font-bold text-gray-900">{{ $customer->name }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $customer->external_customer_code }}</p>
                        </div>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-envelope w-4 text-gray-400"></i>
                            <span class="text-gray-700">{{ $customer->email }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-phone w-4 text-gray-400"></i>
                            <span class="text-gray-700">{{ $customer->phone }}</span>
                        </div>
                        {{-- <div class="flex items-center gap-2">
                            <i class="fas fa-map-marker-alt w-4 text-gray-400"></i>
                            <span class="text-gray-700">{{ $customer->address }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-id-card w-4 text-gray-400"></i>
                            <span class="text-gray-700">National ID: {{ $customer->national_id }}</span>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>

        {{-- ==================== RIGHT COLUMN – Policies & Claims ==================== --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Active Policies Table --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div
                    class="px-5 py-4 border-b border-gray-100 bg-linear-to-r from-gray-50 to-white flex flex-wrap items-center justify-between gap-3">
                    <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-file-contract text-blue-500"></i> Active Policies
                        <span
                            class="text-xs bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full">{{ $policies->total() }}</span>
                    </h3>
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" placeholder="Search policies..."
                            class="pl-8 pr-3 py-1.5 text-xs border border-gray-300 rounded-lg w-48 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-[800px] w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th
                                    class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Policy No.</th>
                                <th
                                    class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Product</th>
                                <th
                                    class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Period</th>
                                <th
                                    class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Source</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($policies as $policy)
                                @php
                                    $isExpiringSoon = $policy->end_date->isBefore(now()->addDays(30));
                                @endphp
                                <tr class="hover:bg-gray-50 transition group">
                                    <td class="px-5 py-3 font-mono text-xs font-medium text-gray-800">
                                        {{ $policy->policy_number }}
                                    </td>
                                    <td class="px-5 py-3 text-xs text-gray-700">
                                        {{ $policy->product_name }}
                                    </td>
                                    <td class="px-5 py-3 text-xs text-gray-500">
                                        {{ $policy->start_date->format('M d, Y') }} –
                                        {{ $policy->end_date->format('M d, Y') }}
                                        @if ($isExpiringSoon)
                                            <span class="ml-1 text-[10px] text-amber-600 font-medium">(Expires
                                                soon)</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-medium bg-green-100 text-green-700">
                                            <i class="fas fa-circle text-[6px] mr-1 text-green-600"></i> Active
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        @php $source = strtolower($policy->source); @endphp
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-medium
                                            {{ $source === 'genova' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }}">
                                            {{ ucfirst($policy->source) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-10 text-center">
                                        <i class="fas fa-file-contract text-gray-300 text-3xl mb-2 block"></i>
                                        <p class="text-sm text-gray-500">No active policies found for this customer.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($policies->total() > 0)
                    <div
                        class="bg-gray-50 px-5 py-3 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3 text-xs text-gray-500">
                        <div>
                            Showing {{ $policies->firstItem() }} to {{ $policies->lastItem() }} of
                            {{ $policies->total() }} policies
                        </div>
                        <div class="flex gap-1">
                            {{ $policies->links() }}
                        </div>
                    </div>
                @endif
            </div>

            {{-- Claims History Table --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div
                    class="px-5 py-4 border-b border-gray-100 bg-linear-to-r from-gray-50 to-white flex flex-wrap items-center justify-between gap-3">
                    <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-clipboard-list text-blue-500"></i> Claims History
                        <span
                            class="text-xs bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full">{{ $claims->total() }}</span>
                    </h3>
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" placeholder="Search claims..."
                            class="pl-8 pr-3 py-1.5 text-xs border border-gray-300 rounded-lg w-48 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-[800px] w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th
                                    class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Claim No.</th>
                                <th
                                    class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Type</th>
                                <th
                                    class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Submitted</th>
                                <th
                                    class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Amount</th>
                                <th
                                    class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($claims as $claim)
                                @php $badge = \App\Enums\ClaimStatus::badge($claim->status); @endphp
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-5 py-3 font-mono text-xs font-medium text-gray-800">
                                        {{ $claim->claim_number }}
                                    </td>
                                    <td class="px-5 py-3 text-xs text-gray-700 capitalize">
                                        {{ str_replace('_', ' ', $claim->claim_type) }}
                                    </td>
                                    <td class="px-5 py-3 text-xs text-gray-500">
                                        {{ $claim->submitted_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-5 py-3 text-xs font-semibold text-gray-800">
                                        {{ number_format($claim->amount, 2) }}
                                    </td>
                                    <td class="px-5 py-3">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-medium {{ $badge['class'] }}">
                                            {{ $badge['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-right relative" x-data="{ open: false }">
                                        <button @click="open = !open"
                                            class="px-3 py-1.5 border border-gray-300 rounded-lg text-xs text-gray-700 hover:bg-gray-50 transition flex items-center gap-1 ml-auto">
                                            Actions <i class="fas fa-chevron-down text-[10px]"></i>
                                        </button>
                                        <div x-show="open" @click.outside="open = false" x-transition
                                            class="absolute right-5 top-10 z-50 w-44 bg-white rounded-xl shadow-lg border border-gray-200 py-1.5">
                                            <a href="{{ route('staff.claims.show', $claim) }}"
                                                class="flex items-center gap-2 px-4 py-2 text-xs text-gray-700 hover:bg-gray-50">
                                                <i class="fas fa-eye text-blue-500 text-[11px]"></i> View Details
                                            </a>
                                            <a href="#"
                                                class="flex items-center gap-2 px-4 py-2 text-xs text-gray-700 hover:bg-gray-50">
                                                <i class="fas fa-edit text-amber-500 text-[11px]"></i> Update Status
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-10 text-center">
                                        <i class="fas fa-inbox text-gray-300 text-3xl mb-2 block"></i>
                                        <p class="text-sm text-gray-500">No claims have been submitted yet.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($claims->total() > 0)
                    <div
                        class="bg-gray-50 px-5 py-3 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3 text-xs text-gray-500">
                        <div>
                            Showing {{ $claims->firstItem() }} to {{ $claims->lastItem() }} of {{ $claims->total() }}
                            claims
                        </div>
                        <div class="flex gap-1">
                            {{ $claims->links() }}
                        </div>
                    </div>
                @endif
            </div>

            {{-- Recent Activities / Notes (Optional) --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-history text-blue-500"></i> Recent Activity
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    <div class="flex gap-3 text-sm">
                        <i class="fas fa-file-alt text-gray-400 mt-0.5"></i>
                        <div><span class="font-medium">Claim CLM-2025-0021</span><span class="text-gray-500">
                                submitted on Mar 15, 2025</span></div>
                    </div>
                    <div class="flex gap-3 text-sm">
                        <i class="fas fa-phone-alt text-gray-400 mt-0.5"></i>
                        <div><span class="font-medium">Customer service call</span><span class="text-gray-500"> –
                                Policy renewal reminder (Mar 10, 2025)</span></div>
                    </div>
                    <div class="flex gap-3 text-sm">
                        <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                        <div><span class="font-medium">Payment received</span><span class="text-gray-500"> – Premium
                                for Home Protector Plus (Mar 01, 2025)</span></div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Flash Message Placeholder (if needed) --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            class="fixed bottom-6 right-6 bg-green-600 text-white px-5 py-3 rounded-xl shadow-xl flex items-center gap-3 z-50">
            <i class="fas fa-check-circle"></i>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

</x-layouts.staff>
