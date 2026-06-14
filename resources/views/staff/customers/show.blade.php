<x-layouts.staff>

    {{-- Page Header --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Customer Profile</h1>
            <p class="text-sm text-gray-500 mt-0.5">View details, policies, claims history and more</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('customers.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition shadow-sm">
                <i class="fas fa-arrow-left text-xs"></i> Back
            </a>
            <button onclick="window.location.reload()"
                class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition shadow-sm">
                <i class="fas fa-sync-alt text-xs"></i> Refresh
            </button>
        </div>
    </div>

    {{-- Compact Customer Header + Stats Pills (two rows) --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
        {{-- Row 1: Essential customer info --}}
        <div class="px-5 py-3 border-b border-gray-100">
            <div class="flex flex-wrap items-center gap-y-2 gap-x-1">
                {{-- Avatar + Name + Code --}}
                <div class="flex items-center gap-2">
                    <div
                        class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 text-xs font-semibold">
                        {{ strtoupper(substr($customer->name, 0, 2)) }}
                    </div>
                    <h2 class="text-sm font-semibold text-gray-900">{{ $customer->name }}</h2>
                    <span class="text-[11px] font-mono bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded-full">
                        {{ $customer->external_customer_code }}
                    </span>
                </div>

                {{-- Separator --}}
                <span class="text-gray-300 text-xs px-1">•</span>

                {{-- Email --}}
                <div class="flex items-center gap-2 text-gray-500 text-xs">
                    <i class="fas fa-envelope w-3"></i>
                    <span>{{ $customer->email }}</span>
                </div>

                {{-- Separator --}}
                <span class="text-gray-300 text-xs px-1">•</span>

                {{-- Phone --}}
                <div class="flex items-center gap-2 text-gray-500 text-xs">
                    <i class="fas fa-phone-alt w-3"></i>
                    <span>{{ $customer->phone }}</span>
                </div>

                {{-- Separator (only if address exists) --}}
                @if ($customer->address)
                    <span class="text-gray-300 text-xs px-1">•</span>

                    {{-- Address --}}
                    <div class="flex items-center gap-2 text-gray-500 text-xs">
                        <i class="fas fa-map-marker-alt w-3"></i>
                        <span>{{ Str::limit($customer->address, 40) }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Row 2: Stat pills --}}
        <div class="px-5 py-2.5 bg-gray-50/30 flex flex-wrap gap-2">
            <div
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-gray-200 shadow-sm text-xs">
                <span class="h-1.5 w-1.5 rounded-full bg-indigo-400"></span>
                <span class="text-gray-500">Active Policies</span>
                <span class="font-semibold text-gray-900">{{ $stats['active_policies'] }}</span>
            </div>
            <div
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-gray-200 shadow-sm text-xs">
                <span class="h-1.5 w-1.5 rounded-full bg-blue-400"></span>
                <span class="text-gray-500">Total Claims</span>
                <span class="font-semibold text-gray-900">{{ $stats['submitted_claims'] }}</span>
            </div>
            <div
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-gray-200 shadow-sm text-xs">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                <span class="text-gray-500">Approved / Closed</span>
                <span class="font-semibold text-gray-900">{{ $stats['closed_claims'] }}</span>
            </div>
            <div
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-gray-200 shadow-sm text-xs">
                <span class="h-1.5 w-1.5 rounded-full bg-amber-400"></span>
                <span class="text-gray-500">Pending</span>
                <span class="font-semibold text-gray-900">{{ $stats['pending_claims'] }}</span>
            </div>
        </div>
    </div>

    {{-- Policies & Claims Sections (Full width, no side columns) --}}
    <div class="space-y-8">

        {{-- Active Policies Table --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div
                class="px-6 py-4 border-b border-gray-100 bg-linear-to-r from-gray-50 to-white flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <i class="fas fa-file-contract text-blue-500 text-lg"></i>
                    <h3 class="text-base font-semibold text-gray-800">Active Policies</h3>
                    <span
                        class="text-xs bg-gray-200 text-gray-700 px-2 py-0.5 rounded-full">{{ $policies->total() }}</span>
                </div>
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" placeholder="Search policies..."
                        class="pl-8 pr-3 py-1.5 text-sm border border-gray-300 rounded-lg w-64 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Policy No.</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Product</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Period</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Source</th>
                            <th
                                class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($policies as $policy)
                            @php
                                $now = now();
                                $isExpired = $policy->end_date->isPast();
                                $isExpiringSoon = !$isExpired && $policy->end_date->isBefore($now->copy()->addDays(30));
                            @endphp
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-mono text-sm font-medium text-gray-900">
                                    {{ $policy->policy_number }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $policy->product_name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $policy->start_date->format('M d, Y') }} –
                                    {{ $policy->end_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    @if ($isExpired)
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                            <i class="fas fa-circle text-[6px] mr-1 text-red-500"></i> Expired
                                        </span>
                                    @elseif ($isExpiringSoon)
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                                            <i class="fas fa-circle text-[6px] mr-1 text-amber-500"></i> Expiring Soon
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                            <i class="fas fa-circle text-[6px] mr-1 text-green-600"></i> Active
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @php $source = strtolower($policy->source); @endphp
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                        {{ $source === 'genova' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }}">
                                        {{ ucfirst($policy->source) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right relative" x-data="{ open: false }">
                                    <button @click="open = !open"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">
                                        Actions <i class="fas fa-chevron-down text-[10px]"></i>
                                    </button>
                                    <div x-show="open" @click.outside="open = false" x-transition
                                        class="absolute right-6 top-12 z-50 w-44 bg-white rounded-xl shadow-lg border border-gray-200 py-1.5">
                                        <a href="#"
                                            class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <i class="fas fa-file-invoice text-blue-500 text-xs"></i> Process Claim
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <i class="fas fa-file-contract text-gray-300 text-4xl mb-3 block"></i>
                                    <p class="text-sm text-gray-500">No policies found for this customer.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($policies->total() > 0)
                <div
                    class="bg-gray-50 px-6 py-3 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3 text-xs text-gray-500">
                    <div>Showing {{ $policies->firstItem() }} to {{ $policies->lastItem() }} of
                        {{ $policies->total() }} policies</div>
                    <div class="flex gap-1">{{ $policies->links() }}</div>
                </div>
            @endif
        </div>

        {{-- Claims History Table --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div
                class="px-6 py-4 border-b border-gray-100 bg-linear-to-r from-gray-50 to-white flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <i class="fas fa-history text-blue-500 text-lg"></i>
                    <h3 class="text-base font-semibold text-gray-800">Claims History</h3>
                    <span
                        class="text-xs bg-gray-200 text-gray-700 px-2 py-0.5 rounded-full">{{ $claims->total() }}</span>
                </div>
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" placeholder="Search claims..."
                        class="pl-8 pr-3 py-1.5 text-sm border border-gray-300 rounded-lg w-64 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Claim No.</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Type</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Submitted</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Amount</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($claims as $claim)
                            @php $badge = \App\Enums\ClaimStatus::badge($claim->status); @endphp
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-mono text-sm font-medium text-gray-900">
                                    {{ $claim->claim_number }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700 capitalize">
                                    {{ str_replace('_', ' ', $claim->claim_type) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $claim->submitted_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">GH₵
                                    {{ number_format($claim->amount, 2) }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $badge['class'] }}">
                                        {{ $badge['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right relative" x-data="{ open: false }">
                                    <button @click="open = !open"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">
                                        Actions <i class="fas fa-chevron-down text-[10px]"></i>
                                    </button>
                                    <div x-show="open" @click.outside="open = false" x-transition
                                        class="absolute right-6 top-12 z-50 w-44 bg-white rounded-xl shadow-lg border border-gray-200 py-1.5">
                                        <a href="{{ route('staff.claims.show', $claim) }}"
                                            class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <i class="fas fa-eye text-blue-500 text-xs"></i> View Details
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <i class="fas fa-inbox text-gray-300 text-4xl mb-3 block"></i>
                                    <p class="text-sm text-gray-500">No claims submitted yet.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($claims->total() > 0)
                <div
                    class="bg-gray-50 px-6 py-3 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3 text-xs text-gray-500">
                    <div>Showing {{ $claims->firstItem() }} to {{ $claims->lastItem() }} of {{ $claims->total() }}
                        claims</div>
                    <div class="flex gap-1">{{ $claims->links() }}</div>
                </div>
            @endif
        </div>
    </div>

    {{-- Flash Message --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            class="fixed bottom-6 right-6 bg-green-600 text-white px-5 py-3 rounded-xl shadow-xl flex items-center gap-3 z-50">
            <i class="fas fa-check-circle"></i>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

</x-layouts.staff>
