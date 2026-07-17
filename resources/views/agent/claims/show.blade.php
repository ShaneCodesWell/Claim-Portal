<x-layouts.agent>
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
        <div class="flex items-center gap-2">
            <a href="{{ route('claims.index') }}"
                class="bg-white border border-gray-300 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium text-gray-700 transition shadow-sm flex items-center gap-2">
                <i class="fas fa-arrow-left text-sm"></i> Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ==================== LEFT COLUMN ==================== --}}
        <div class="space-y-5">

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
                            <div class="flex items-center gap-2 min-w-0">
                                @if (str_contains($doc->mime_type, 'pdf'))
                                    <i class="fas fa-file-pdf text-red-400 text-sm shrink-0"></i>
                                @else
                                    <i class="fas fa-image text-blue-400 text-sm shrink-0"></i>
                                @endif
                                <span class="text-xs text-gray-700 truncate max-w-35">{{ $doc->original_name }}</span>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <button
                                    onclick="openDocPreview('{{ route('agent.documents.preview', $doc->id) }}', '{{ $doc->original_name }}', '{{ $doc->mime_type }}')"
                                    class="text-xs text-blue-600 hover:underline">
                                    View
                                </button>
                                @if (in_array($claim->status, ['submitted', 'pending_info']))
                                    <form action="{{ route('agent.claims.documents.destroy', $doc->id) }}"
                                        method="POST" class="delete-doc-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-500 hover:underline">
                                            Remove
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 italic">No documents uploaded yet.</p>
                    @endforelse
                </div>
            </div>

            {{-- Upload Documents Card --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-upload text-indigo-500"></i> Upload Documents
                    </h3>
                </div>
                <div class="p-4">
                    <form action="{{ route('agent.claims.documents', $claim) }}" method="POST"
                        enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <input type="file" name="documents[]" multiple accept=".jpg,.jpeg,.png,.gif,.pdf"
                            class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer" />
                        <p class="text-xs text-gray-400">PDF, JPG, PNG up to 5MB each</p>
                        <button type="submit"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-sm py-2 rounded-lg transition font-medium flex items-center justify-center gap-2">
                            <i class="fas fa-upload text-xs"></i> Upload Documents
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ==================== RIGHT COLUMN ==================== --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Claim Summary Card (merged policy + form data) --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                {{-- Header with action button --}}
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-file-alt text-blue-500"></i> Claim Summary
                    </h3>
                    @if (\App\Enums\ClaimStatus::isEditable($claim->status))
                        <a href="{{ route('agent.claims.edit', $claim) }}"
                            class="border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm px-4 py-2 rounded-lg transition font-medium flex items-center gap-2">
                            <i class="fas fa-edit"></i> Edit Form
                        </a>
                    @else
                        <span class="text-xs text-gray-400 italic flex items-center gap-1 px-4 py-2">
                            <i class="fas fa-lock"></i> Editing locked
                        </span>
                    @endif
                </div>

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
                            'name' => $claim->form_data['name'] ?? ($claim->policy?->customer?->name ?? ''),
                            'email' => $claim->form_data['email'] ?? ($claim->policy?->customer?->email ?? ''),
                            'phone' => $claim->form_data['phone'] ?? ($claim->policy?->customer?->phone ?? ''),
                            'occupation' => $claim->form_data['occupation'] ?? '',
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
        document.querySelectorAll('.delete-doc-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Remove this document?',
                    text: 'This cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, remove it',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true,
                }).then(result => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    </script>
</x-layouts.agent>
