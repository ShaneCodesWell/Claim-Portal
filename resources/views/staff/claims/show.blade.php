<x-layouts.staff>

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('staff.claims.index') }}"
                class="p-2 rounded-lg border border-gray-200 hover:bg-gray-50 transition text-gray-500">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    {{ $claim->claim_number }}
                    @php
                        $statusColors = [
                            'submitted' => 'bg-blue-100 text-blue-700',
                            'under_review' => 'bg-indigo-100 text-indigo-700',
                            'pending_info' => 'bg-amber-100 text-amber-700',
                            'in_progress' => 'bg-purple-100 text-purple-700',
                            'approved' => 'bg-green-100 text-green-700',
                            'rejected' => 'bg-red-100 text-red-700',
                            'closed' => 'bg-gray-100 text-gray-600',
                        ];
                        $statusLabels = \App\Enums\ClaimStatus::labels();
                        $color = $statusColors[$claim->status] ?? 'bg-gray-100 text-gray-600';
                    @endphp
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $color }}">
                        {{ $statusLabels[$claim->status] ?? $claim->status }}
                    </span>
                </h1>
                <p class="text-sm text-gray-500 mt-0.5">
                    Submitted {{ $claim->submitted_at?->diffForHumans() }} &middot;
                    {{ ucfirst($claim->claim_type) }} Claim &middot;
                    via {{ ucfirst(str_replace('_', ' ', $claim->source)) }}
                </p>
            </div>
        </div>

        {{-- Quick Status Update --}}
        <form action="{{ route('staff.claims.status', $claim) }}" method="POST" class="flex items-center gap-2">
            @csrf
            <select name="status"
                class="text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-blue-500 outline-none">
                @foreach (\App\Enums\ClaimStatus::labels() as $value => $label)
                    <option value="{{ $value }}" @selected($claim->status === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition font-medium">
                Update Status
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ==================== LEFT COLUMN ==================== --}}
        <div class="space-y-5">

            {{-- Customer Card --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-user-circle text-blue-500"></i> Customer
                    </h3>
                </div>
                <div class="p-4">
                    <div class="flex items-center gap-3 mb-3">
                        <div
                            class="h-10 w-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-sm font-semibold">
                            {{ strtoupper(substr($claim->customer->name ?? 'C', 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $claim->customer->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">{{ $claim->customer->external_customer_code ?? '' }}</p>
                        </div>
                    </div>
                    <div class="space-y-1.5 text-xs text-gray-600">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-phone w-3 text-gray-400"></i>
                            {{ $claim->customer->phone ?? 'N/A' }}
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-envelope w-3 text-gray-400"></i>
                            {{ $claim->customer->email ?? 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Policy Card --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-file-contract text-blue-500"></i> Policy
                    </h3>
                </div>
                <div class="p-4 space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Policy No.</span>
                        <span
                            class="font-mono font-medium text-gray-800">{{ $claim->policy->policy_number ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Product</span>
                        <span
                            class="text-gray-700 text-right max-w-[60%]">{{ $claim->policy->product_name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Class</span>
                        <span class="text-gray-700">{{ $claim->policy->business_class_name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Source</span>
                        <span class="text-gray-700 capitalize">{{ $claim->policy->source ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Expires</span>
                        <span class="{{ $claim->policy?->end_date?->isPast() ? 'text-red-600' : 'text-gray-700' }}">
                            {{ $claim->policy?->end_date?->format('d M Y') ?? 'N/A' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Assignment Card --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-user-check text-blue-500"></i> Assignment
                    </h3>
                </div>
                <div class="p-4">
                    @if ($claim->assignedTo)
                        <div class="flex items-center gap-3 mb-3">
                            <div
                                class="h-9 w-9 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-semibold">
                                {{ strtoupper(substr($claim->assignedTo->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $claim->assignedTo->name }}</p>
                                <p class="text-xs text-gray-500">{{ $claim->assignedTo->email }}</p>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400">
                            Assigned {{ $claim->assigned_at?->diffForHumans() }}
                        </p>
                    @else
                        <p class="text-sm text-gray-400 italic">Not yet assigned</p>
                    @endif

                    {{-- Reassign Form --}}
                    <form action="{{ route('staff.claims.assign', $claim) }}" method="POST" class="mt-4 space-y-2">
                        @csrf
                        <select name="assigned_to"
                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="">Select staff member...</option>
                            @foreach ($staffMembers as $staff)
                                <option value="{{ $staff->id }}" @selected($claim->assigned_to === $staff->id)>
                                    {{ $staff->name }} ({{ ucfirst(str_replace('_', ' ', $staff->role)) }})
                                </option>
                            @endforeach
                        </select>
                        <input type="text" name="note" placeholder="Reason for assignment (optional)"
                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none" />
                        <button type="submit"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-sm py-2 rounded-lg transition font-medium">
                            {{ $claim->assignedTo ? 'Reassign Claim' : 'Assign Claim' }}
                        </button>
                    </form>
                </div>
            </div>

            {{-- Documents Card --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-paperclip text-blue-500"></i> Documents
                    </h3>
                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">
                        {{ $claim->documents->count() }}
                    </span>
                </div>
                <div class="p-4">
                    @forelse($claim->documents as $doc)
                        <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-file text-gray-400 text-sm"></i>
                                <span
                                    class="text-xs text-gray-700 truncate max-w-[140px]">{{ $doc->original_name }}</span>
                            </div>
                            <a href="{{ Storage::url($doc->file_path) }}" target="_blank"
                                class="text-xs text-blue-600 hover:underline">View</a>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 italic">No documents uploaded yet.</p>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- ==================== RIGHT COLUMN ==================== --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Form Data Card --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-clipboard-list text-blue-500"></i> Claim Form Data
                    </h3>
                    <span
                        class="text-xs text-gray-400 capitalize">{{ str_replace('_', ' ', $claim->claim_type) }}</span>
                    <button onclick="openPrintModal()"
                        class="border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm px-4 py-2 rounded-lg transition font-medium flex items-center gap-2">
                        <i class="fas fa-eye"></i> Preview Form
                    </button>
                    <x-claim-form-modal :claim="$claim" />
                </div>
                <div class="p-5">
                    @if ($claim->form_data && count($claim->form_data))
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-3">
                            @foreach ($claim->form_data as $key => $value)
                                @if (!is_array($value) && $value !== null && $value !== '')
                                    <div class="flex flex-col">
                                        <span class="text-xs text-gray-400 capitalize mb-0.5">
                                            {{ str_replace('_', ' ', $key) }}
                                        </span>
                                        <span class="text-sm text-gray-800 font-medium">
                                            @if (is_bool($value))
                                                {{ $value ? 'Yes' : 'No' }}
                                            @else
                                                {{ $value }}
                                            @endif
                                        </span>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        {{-- Array fields (injured persons, property items etc.) --}}
                        @foreach ($claim->form_data as $key => $value)
                            @if (is_array($value) && count($value))
                                <div class="mt-5 pt-4 border-t border-gray-100">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                                        {{ str_replace('_', ' ', $key) }}
                                    </p>
                                    <div class="overflow-x-auto rounded-lg border border-gray-100">
                                        <table class="w-full text-xs">
                                            <thead class="bg-gray-50 border-b border-gray-100">
                                                <tr>
                                                    @foreach (array_keys((array) $value[0]) as $col)
                                                        <th
                                                            class="px-3 py-2 text-left font-medium text-gray-500 capitalize">
                                                            {{ str_replace('_', ' ', $col) }}
                                                        </th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-50">
                                                @foreach ($value as $row)
                                                    <tr class="hover:bg-gray-50">
                                                        @foreach ((array) $row as $cell)
                                                            <td class="px-3 py-2 text-gray-700">{{ $cell ?: '—' }}
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @else
                        <p class="text-sm text-gray-400 italic">No form data available.</p>
                    @endif
                </div>
            </div>

            {{-- Request Additional Info --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-question-circle text-amber-500"></i> Request Additional Information
                    </h3>
                </div>
                <div class="p-5">
                    <form action="{{ route('staff.claims.request-info', $claim) }}" method="POST"
                        class="space-y-3">
                        @csrf
                        <textarea name="note" rows="3" required
                            placeholder="Describe what additional information or documents you need from the customer..."
                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-400 outline-none resize-none"></textarea>
                        <button type="submit"
                            class="bg-amber-500 hover:bg-amber-600 text-white text-sm px-4 py-2 rounded-lg transition font-medium flex items-center gap-2">
                            <i class="fas fa-paper-plane text-xs"></i> Send Request
                        </button>
                    </form>
                </div>
            </div>

            {{-- Activity Timeline --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-history text-blue-500"></i> Activity Timeline
                    </h3>
                </div>
                <div class="p-5">
                    @forelse($claim->activities as $activity)
                        @php
                            $activityIcons = [
                                'submitted' => ['icon' => 'fa-file-alt', 'color' => 'bg-blue-100 text-blue-600'],
                                'assigned' => ['icon' => 'fa-user-check', 'color' => 'bg-indigo-100 text-indigo-600'],
                                'reassigned' => [
                                    'icon' => 'fa-exchange-alt',
                                    'color' => 'bg-purple-100 text-purple-600',
                                ],
                                'status_changed' => ['icon' => 'fa-sync-alt', 'color' => 'bg-gray-100 text-gray-600'],
                                'info_requested' => [
                                    'icon' => 'fa-question-circle',
                                    'color' => 'bg-amber-100 text-amber-600',
                                ],
                                'form_updated' => ['icon' => 'fa-edit', 'color' => 'bg-green-100 text-green-600'],
                                'note_added' => [
                                    'icon' => 'fa-sticky-note',
                                    'color' => 'bg-yellow-100 text-yellow-600',
                                ],
                            ];
                            $meta = $activityIcons[$activity->action] ?? [
                                'icon' => 'fa-circle',
                                'color' => 'bg-gray-100 text-gray-500',
                            ];
                        @endphp
                        <div class="flex gap-3 pb-5 last:pb-0 relative">
                            {{-- Connecting line --}}
                            @if (!$loop->last)
                                <div class="absolute left-4 top-8 bottom-0 w-px bg-gray-100"></div>
                            @endif

                            <div
                                class="shrink-0 w-8 h-8 rounded-full {{ $meta['color'] }} flex items-center justify-center z-10">
                                <i class="fas {{ $meta['icon'] }} text-xs"></i>
                            </div>
                            <div class="flex-1 min-w-0 pt-0.5">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-sm font-medium text-gray-800">
                                        {{ ucfirst(str_replace('_', ' ', $activity->action)) }}
                                    </p>
                                    <span class="text-xs text-gray-400 whitespace-nowrap">
                                        {{ $activity->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                @if ($activity->note)
                                    <p class="text-sm text-gray-600 mt-0.5">{{ $activity->note }}</p>
                                @endif
                                @if ($activity->user)
                                    <p class="text-xs text-gray-400 mt-1">
                                        by {{ $activity->user->name }}
                                    </p>
                                @else
                                    <p class="text-xs text-gray-400 mt-1">by System</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 italic">No activity recorded yet.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            class="fixed bottom-6 right-6 bg-green-600 text-white px-5 py-3 rounded-xl shadow-xl flex items-center gap-3 z-50">
            <i class="fas fa-check-circle"></i>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            class="fixed bottom-6 right-6 bg-red-600 text-white px-5 py-3 rounded-xl shadow-xl flex items-center gap-3 z-50">
            <i class="fas fa-exclamation-circle"></i>
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

</x-layouts.staff>
