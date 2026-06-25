<x-layouts.staff>

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
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
            <a href="{{ route('staff.claims.index') }}"
                class="bg-white border border-gray-300 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium text-gray-700 transition shadow-sm flex items-center gap-2">
                <i class="fas fa-arrow-left text-sm"></i> Back
            </a>
            @if ($claim->isEditable())
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
            @else
                <span class="text-xs text-gray-400 italic flex items-center gap-1 px-4 py-2">
                    <i class="fas fa-lock"></i> Editing locked
                </span>
            @endif
        </form>

        <div class="flex gap-2">
            {{-- Send to Survey --}}
            @if (
                !in_array(
                    $claim->status,
                    array_merge(\App\Enums\ClaimStatus::terminal(), [
                        \App\Enums\ClaimStatus::UNDER_SURVEY,
                        \App\Enums\ClaimStatus::COMMITTEE_REVIEW,
                    ])))
                <div x-data="{ open: false }">
                    <button @click="open = true"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-cyan-700 bg-cyan-50 border border-cyan-300 rounded-lg hover:bg-cyan-100 transition">
                        <i class="fas fa-search-location text-xs"></i> Send to Survey
                    </button>

                    <div x-show="open" x-cloak class="fixed inset-0 bg-black/30 flex items-center justify-center z-50"
                        @click.self="open = false">
                        <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Send Claim to Survey</h3>
                            <p class="text-sm text-gray-500 mb-4">This will route the claim to the survey team for
                                assessment.</p>
                            <form action="{{ route('staff.claims.send-to-survey', $claim->id) }}" method="POST">
                                @csrf
                                <textarea name="note" rows="3" maxlength="500" placeholder="Optional note for the surveyor..."
                                    class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:outline-none focus:ring-1 focus:ring-cyan-300"></textarea>
                                <div class="flex justify-end gap-2 mt-4">
                                    <button type="button" @click="open = false"
                                        class="px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
                                    <button type="submit"
                                        class="px-4 py-2 text-sm font-medium text-white bg-cyan-600 hover:bg-cyan-700 rounded-lg">Confirm</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Send to Committee --}}
            @if (
                !in_array(
                    $claim->status,
                    array_merge(\App\Enums\ClaimStatus::terminal(), [\App\Enums\ClaimStatus::COMMITTEE_REVIEW])))
                <div x-data="{ open: false }">
                    <button @click="open = true"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-orange-700 border border-orange-300 bg-orange-50 rounded-lg hover:bg-orange-100 transition">
                        <i class="fas fa-gavel text-xs"></i> Send to Committee
                    </button>

                    <div x-show="open" x-cloak class="fixed inset-0 bg-black/30 flex items-center justify-center z-50"
                        @click.self="open = false">
                        <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Escalate to Claims Committee</h3>
                            <p class="text-sm text-gray-500 mb-4">This will route the claim to the committee for a final
                                decision.</p>
                            <form action="{{ route('staff.claims.send-to-committee', $claim->id) }}" method="POST">
                                @csrf
                                <textarea name="note" rows="3" maxlength="500" placeholder="Optional note for the committee..."
                                    class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:outline-none focus:ring-1 focus:ring-orange-300"></textarea>
                                <div class="flex justify-end gap-2 mt-4">
                                    <button type="button" @click="open = false"
                                        class="px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
                                    <button type="submit"
                                        class="px-4 py-2 text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 rounded-lg">Confirm</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ==================== LEFT COLUMN ==================== --}}
        <div class="space-y-5">

            {{-- Policy Card --}}
            {{-- <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
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
            </div> --}}

            {{-- Assignment Card --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-user-check text-blue-500"></i> Assignment
                    </h3>
                </div>
                @if ($claim->isEditable())
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
                        <form action="{{ route('staff.claims.assign', $claim) }}" method="POST"
                            class="mt-4 space-y-2">
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
                @else
                    <div class="flex items-center justify-center p-2">
                        <span class="text-xs text-gray-400 italic flex items-center gap-1 px-4 py-2">
                            <i class="fas fa-lock"></i> Editing locked
                        </span>
                    </div>
                @endif
            </div>

            {{-- Documents Card --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-paperclip text-blue-500"></i> Uploaded Documents
                    </h3>
                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">
                        {{ $claim->documents->count() }}
                    </span>
                </div>
                <div class="p-4">
                    @forelse($claim->documents as $doc)
                        <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                            <div class="flex items-center gap-2">
                                @if (str_contains($doc->mime_type, 'pdf'))
                                    <i class="fas fa-file-pdf text-red-400 text-sm"></i>
                                @else
                                    <i class="fas fa-image text-blue-400 text-sm"></i>
                                @endif
                                <span class="text-xs text-gray-700 truncate max-w-35">{{ $doc->original_name }}</span>
                            </div>
                            <button
                                onclick="openDocPreview('{{ route('staff.documents.preview', $doc->id) }}', '{{ $doc->original_name }}', '{{ $doc->mime_type }}')"
                                class="text-xs text-blue-600 hover:underline">
                                View
                            </button>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 italic">No documents uploaded yet.</p>
                    @endforelse
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

        {{-- ==================== RIGHT COLUMN ==================== --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Form Data Card --}}
            {{-- Claim Summary Card (merged policy + form data) --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                {{-- Header with action button --}}
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-file-alt text-blue-500"></i> Claim Summary
                    </h3>
                    <div class="flex gap-2">
                        <button onclick="openPrintModal()"
                            class="border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm px-4 py-2 rounded-lg transition font-medium flex items-center gap-2">
                            <i class="fas fa-eye"></i> Preview Form
                        </button>
                        {{-- <a href="{{ route('staff.claims.edit', $claim) }}"
                            class="border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm px-4 py-2 rounded-lg transition font-medium flex items-center gap-2">
                            <i class="fas fa-edit"></i> Edit Form
                        </a> --}}
                        @if ($claim->isEditable())
                            <a href="{{ route('staff.claims.edit', $claim) }}"
                                class="border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm px-4 py-2 rounded-lg transition font-medium flex items-center gap-2">
                                <i class="fas fa-edit"></i> Edit Form
                            </a>
                        @else
                            <span class="text-xs text-gray-400 italic flex items-center gap-1 px-4 py-2">
                                <i class="fas fa-lock"></i> Editing locked
                            </span>
                        @endif
                    </div>
                </div>
                {{-- Print Modal Preview --}}
                <x-claim-form-modal :claim="$claim" />

                <div class="p-5 space-y-5">

                    {{-- Section: Policy Information --}}
                    @php $policy = $claim->policy; @endphp
                    @if ($policy)
                        <div>
                            <h4
                                class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 flex items-center gap-2">
                                <i class="fas fa-file-contract text-blue-400"></i> Policy
                            </h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                                <div>
                                    <span class="text-gray-500 block">Policy No.</span>
                                    <span
                                        class="font-mono font-medium text-gray-800">{{ $policy->policy_number ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500 block">Product</span>
                                    <span class="text-gray-700">{{ $policy->product_name ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500 block">Class</span>
                                    <span class="text-gray-700">{{ $policy->business_class_name ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500 block">Expiry</span>
                                    <span
                                        class="{{ $policy->end_date?->isPast() ? 'text-red-600' : 'text-gray-700' }}">
                                        {{ $policy->end_date?->format('d M Y') ?? 'N/A' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <hr class="border-gray-100">
                    @endif

                    {{-- Section: Claimant / Policyholder --}}
                    @php
                        $claimant = [
                            'name' => $claim->form_data['claimant_name'] ?? ($claim->policy?->customer?->name ?? ''),
                            'email' => $claim->form_data['claimant_email'] ?? ($claim->policy?->customer?->email ?? ''),
                            'phone' => $claim->form_data['claimant_phone'] ?? ($claim->policy?->customer?->phone ?? ''),
                            'occupation' => $claim->form_data['claimant_occupation'] ?? '',
                        ];
                    @endphp
                    <div>
                        <h4
                            class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 flex items-center gap-2">
                            <i class="fas fa-user-circle text-blue-400"></i> Claimant / Policyholder
                        </h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                            <div>
                                <span class="text-gray-500 block">Full Name</span>
                                <span class="text-gray-800 font-medium">{{ $claimant['name'] ?: '—' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 block">Email</span>
                                <span class="text-gray-700">{{ $claimant['email'] ?: '—' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 block">Phone</span>
                                <span class="text-gray-700">{{ $claimant['phone'] ?: '—' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 block">Occupation</span>
                                <span class="text-gray-700">{{ $claimant['occupation'] ?: '—' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Section: Vehicle Particulars --}}
                    @php
                        $vehicleFields = ['registration_no', 'make', 'model', 'year_of_make'];
                        $hasVehicle = collect($vehicleFields)->some(fn($k) => !empty($claim->form_data[$k]));
                    @endphp
                    @if ($hasVehicle)
                        <hr class="border-gray-100">
                        <div>
                            <h4
                                class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 flex items-center gap-2">
                                <i class="fas fa-car text-blue-400"></i> Vehicle Particulars
                            </h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                                @foreach ($vehicleFields as $key)
                                    @php
                                        $label = str_replace('_', ' ', $key);
                                        $value = $claim->form_data[$key] ?? '';
                                    @endphp
                                    @if ($value)
                                        <div>
                                            <span class="text-gray-500 block">{{ ucfirst($label) }}</span>
                                            <span class="text-gray-800 font-medium">{{ $value }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Section: Accident Circumstances --}}
                    @php
                        $accidentKeys = [
                            'accident_date',
                            'accident_time',
                            'exact_location',
                            'accident_description',
                            'people_in_vehicle',
                            'vehicle_damage',
                            'damaged_vehicle_location',
                        ];
                        $hasAccident = collect($accidentKeys)->some(fn($k) => !empty($claim->form_data[$k]));
                    @endphp
                    @if ($hasAccident)
                        <hr class="border-gray-100">
                        <div>
                            <h4
                                class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 flex items-center gap-2">
                                <i class="fas fa-exclamation-triangle text-amber-400"></i> Accident Details
                            </h4>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-xs">
                                @foreach (['accident_date', 'accident_time', 'exact_location', 'people_in_vehicle', 'vehicle_damage', 'damaged_vehicle_location'] as $key)
                                    @php
                                        $label = str_replace('_', ' ', $key);
                                        $value = $claim->form_data[$key] ?? '';
                                    @endphp
                                    @if ($value)
                                        <div>
                                            <span class="text-gray-500 block">{{ ucfirst($label) }}</span>
                                            <span class="text-gray-800">{{ $value }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            @if (!empty($claim->form_data['accident_description']))
                                <div class="mt-3">
                                    <span class="text-gray-500 text-xs block">Description</span>
                                    <p
                                        class="text-sm text-gray-700 bg-gray-50 rounded-lg p-3 mt-1 border border-gray-100">
                                        {{ $claim->form_data['accident_description'] }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Section: Driver Details (if applicable) --}}
                    @php
                        $driverKeys = ['driver_fullname', 'driver_address', 'driver_phone', 'driver_license'];
                        $hasDriver = collect($driverKeys)->some(fn($k) => !empty($claim->form_data[$k]));
                    @endphp
                    @if ($hasDriver)
                        <hr class="border-gray-100">
                        <div>
                            <h4
                                class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 flex items-center gap-2">
                                <i class="fas fa-user-tie text-blue-400"></i> Driver at Time of Accident
                            </h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                                @foreach ($driverKeys as $key)
                                    @php
                                        $label = str_replace('_', ' ', $key);
                                        $value = $claim->form_data[$key] ?? '';
                                    @endphp
                                    @if ($value)
                                        <div>
                                            <span class="text-gray-500 block">{{ ucfirst($label) }}</span>
                                            <span class="text-gray-800">{{ $value }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Section: Other Key Fields (consent, hire purchase, etc.) --}}
                    @php
                        $otherKeys = ['vehicle_consent', 'hire_purchase', 'police_report'];
                        $hasOther = collect($otherKeys)->some(fn($k) => !empty($claim->form_data[$k]));
                    @endphp
                    @if ($hasOther)
                        <hr class="border-gray-100">
                        <div>
                            <h4
                                class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 flex items-center gap-2">
                                <i class="fas fa-flag text-blue-400"></i> Additional Details
                            </h4>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-xs">
                                @foreach ($otherKeys as $key)
                                    @php
                                        $label = str_replace('_', ' ', $key);
                                        $value = $claim->form_data[$key] ?? '';
                                        if ($value === 'yes' || $value === 'no') {
                                            $value = ucfirst($value);
                                        }
                                    @endphp
                                    @if ($value)
                                        <div>
                                            <span class="text-gray-500 block">{{ ucfirst($label) }}</span>
                                            <span class="text-gray-800">{{ $value }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- If no data at all --}}
                    @if (!$hasVehicle && !$hasAccident && !$hasDriver && !$hasOther && empty($claimant['name']))
                        <p class="text-sm text-gray-400 italic">No form data available.</p>
                    @endif

                </div>
            </div>

            {{-- Survey Findings --}}
            @if ($claim->survey_notes)
                <div class="bg-white rounded-xl border border-cyan-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-cyan-100 bg-cyan-50/50 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                            <i class="fas fa-search-location text-cyan-500"></i> Survey Findings
                        </h3>
                        <div class="text-xs text-gray-500 flex items-center gap-2">
                            <span>by <strong
                                    class="text-gray-700">{{ $claim->surveyor?->name ?? 'Unknown' }}</strong></span>
                            <span>&middot;</span>
                            <span>{{ $claim->survey_completed_at?->format('d M Y, g:i A') ?? '—' }}</span>
                        </div>
                    </div>
                    <div class="p-5">
                        <p
                            class="text-sm text-gray-700 bg-cyan-50/30 rounded-lg p-4 border border-cyan-100 whitespace-pre-line leading-relaxed">
                            {{ $claim->survey_notes }}
                        </p>
                    </div>
                </div>
            @endif

            {{-- Committee Notes --}}
            @if ($claim->committee_notes)
                <div class="bg-white rounded-xl border border-orange-200 shadow-sm overflow-hidden">
                    <div
                        class="px-5 py-4 border-b border-orange-100 bg-orange-50/50 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                            <i class="fas fa-gavel text-orange-500"></i> Committee Decision
                        </h3>
                        <div class="text-xs text-gray-500 flex items-center gap-2">
                            @php
                                $decisionColors = [
                                    'approved' => 'bg-green-100 text-green-700',
                                    'rejected' => 'bg-red-100 text-red-700',
                                ];
                                $decisionColor = $decisionColors[$claim->status] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $decisionColor }}">
                                {{ \App\Enums\ClaimStatus::labels()[$claim->status] ?? $claim->status }}
                            </span>
                            <span>&middot;</span>
                            <span>by <strong
                                    class="text-gray-700">{{ $claim->committeeDecidedBy?->name ?? 'Unknown' }}</strong></span>
                            <span>&middot;</span>
                            <span>{{ $claim->committee_decided_at?->format('d M Y, g:i A') ?? '—' }}</span>
                        </div>
                    </div>
                    <div class="p-5">
                        <p
                            class="text-sm text-gray-700 bg-orange-50/30 rounded-lg p-4 border border-orange-100 whitespace-pre-line leading-relaxed">
                            {{ $claim->committee_notes }}
                        </p>
                    </div>
                </div>
            @endif

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
        </div>
    </div>

    {{-- Document Preview Modal --}}
    <x-documents-modal />

    {{-- Flash Messages --}}
    @if (session('success') || session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                @if (session('success'))
                    Toast.fire({
                        icon: 'success',
                        title: @json(session('success'))
                    });
                @endif

                @if (session('error'))
                    Toast.fire({
                        icon: 'error',
                        title: @json(session('error'))
                    });
                @endif
            });
        </script>
    @endif

    <script>
        function openDocPreview(url, name, mimeType) {
            const modal = document.getElementById('docViewModal');
            const body = document.getElementById('docViewBody');
            const nameEl = document.getElementById('docViewName');
            const iconEl = document.getElementById('docViewIcon');
            const download = document.getElementById('docViewDownload');

            nameEl.textContent = name;
            download.href = url + '?download=1';

            // Reset body
            body.innerHTML = `<div class="text-center text-gray-400">
                <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
                <p class="text-sm">Loading document...</p>
                </div>`;

            if (mimeType.includes('pdf')) {
                iconEl.className = 'fas fa-file-pdf text-red-400';
                body.innerHTML =
                    `<iframe src="${url}" class="w-full rounded" style="height:65vh;" frameborder="0"></iframe>`;
            } else if (mimeType.includes('image')) {
                iconEl.className = 'fas fa-image text-blue-400';
                body.innerHTML =
                    `<img src="${url}" class="max-w-full max-h-[65vh] rounded-lg shadow object-contain" 
                                 onerror="this.parentElement.innerHTML='<p class=text-red-500 text-sm>Failed to load image.</p>'" />`;
            } else {
                iconEl.className = 'fas fa-file text-gray-400';
                body.innerHTML = `<div class="text-center text-gray-500 py-12">
                    <i class="fas fa-file-alt text-5xl mb-4 text-gray-300"></i>
                    <p class="text-sm font-medium">${name}</p>
                    <p class="text-xs text-gray-400 mt-1">Preview not available for this file type.</p>
                    <a href="${url}?download=1" class="mt-4 inline-block bg-blue-600 text-white text-sm px-4 py-2 rounded-lg">
                        Download to view
                    </a>
                </div>`;
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeDocPreview() {
            const modal = document.getElementById('docViewModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
            document.getElementById('docViewBody').innerHTML = '';
        }

        document.getElementById('docViewModal')?.addEventListener('click', (e) => {
            if (e.target.id === 'docViewModal') closeDocPreview();
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeDocPreview();
        });
    </script>

</x-layouts.staff>
