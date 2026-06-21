<x-layouts.surveyor>

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
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
                            'under_survey' => 'bg-cyan-100 text-cyan-700',
                            'survey_completed' => 'bg-teal-100 text-teal-700',
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
                    Sent to survey {{ $claim->surveyed_at?->diffForHumans() }}
                </p>
            </div>
        </div>
        <a href="{{ route('surveyor.claims.index') }}"
            class="bg-white border border-gray-300 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium text-gray-700 transition shadow-sm flex items-center gap-2">
            <i class="fas fa-arrow-left text-sm"></i> Back
        </a>
    </div>

    {{-- Assign to me banner --}}
    @if (is_null($claim->surveyed_by))
        <div class="mb-5 flex items-center justify-between gap-4 rounded-xl border border-cyan-200 bg-cyan-50 px-5 py-4">
            <div class="flex items-center gap-3 text-sm text-cyan-800">
                <i class="fas fa-user-clock text-cyan-500 text-lg"></i>
                <span>This claim has not been assigned to a surveyor yet. You can take ownership of it.</span>
            </div>
            <form action="{{ route('surveyor.claims.assign-to-me', $claim) }}" method="POST">
                @csrf
                <button type="submit"
                    class="bg-cyan-600 hover:bg-cyan-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition whitespace-nowrap">
                    <i class="fas fa-user-check mr-1 text-xs"></i> Assign to Me
                </button>
            </form>
        </div>
    @elseif($claim->surveyed_by === Auth::id())
        <div
            class="mb-5 flex items-center gap-3 rounded-xl border border-teal-200 bg-teal-50 px-5 py-3 text-sm text-teal-800">
            <i class="fas fa-check-circle text-teal-500"></i>
            <span>This claim is assigned to you. Submit your findings below when ready.</span>
        </div>
    @else
        <div
            class="mb-5 flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 px-5 py-3 text-sm text-gray-600">
            <i class="fas fa-user text-gray-400"></i>
            <span>Assigned to <strong>{{ $claim->surveyor->name }}</strong></span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ==================== LEFT COLUMN ==================== --}}
        <div class="space-y-5">

            {{-- Documents Card (view) --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-paperclip text-cyan-500"></i> Uploaded Documents
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
                                class="text-xs text-blue-600 hover:underline">View</button>
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
                        <i class="fas fa-upload text-cyan-500"></i> Upload Survey Documents
                    </h3>
                </div>
                <div class="p-4">
                    <form action="{{ route('surveyor.claims.documents', $claim) }}" method="POST"
                        enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <input type="file" name="documents[]" multiple accept=".jpg,.jpeg,.png,.gif,.pdf"
                            class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100 cursor-pointer" />
                        <p class="text-xs text-gray-400">PDF, JPG, PNG up to 5MB each</p>
                        <button type="submit"
                            class="w-full bg-cyan-600 hover:bg-cyan-700 text-white text-sm py-2 rounded-lg transition font-medium flex items-center justify-center gap-2">
                            <i class="fas fa-upload text-xs"></i> Upload Documents
                        </button>
                    </form>
                </div>
            </div>

            {{-- Activity Timeline --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-history text-cyan-500"></i> Activity Timeline
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
                                'sent_to_survey' => [
                                    'icon' => 'fa-search-location',
                                    'color' => 'bg-cyan-100 text-cyan-600',
                                ],
                                'surveyor_assigned' => [
                                    'icon' => 'fa-user-check',
                                    'color' => 'bg-cyan-100 text-cyan-600',
                                ],
                                'survey_completed' => [
                                    'icon' => 'fa-check-circle',
                                    'color' => 'bg-teal-100 text-teal-600',
                                ],
                                'documents_uploaded' => [
                                    'icon' => 'fa-paperclip',
                                    'color' => 'bg-blue-100 text-blue-600',
                                ],
                                'form_updated' => ['icon' => 'fa-edit', 'color' => 'bg-green-100 text-green-600'],
                            ];
                            $meta = $activityIcons[$activity->action] ?? [
                                'icon' => 'fa-circle',
                                'color' => 'bg-gray-100 text-gray-500',
                            ];
                        @endphp
                        <div class="flex gap-3 pb-5 last:pb-0 relative">
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
                                <p class="text-xs text-gray-400 mt-1">
                                    by {{ $activity->user?->name ?? 'System' }}
                                </p>
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

            {{-- Claim Summary --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-file-alt text-cyan-500"></i> Claim Summary
                    </h3>
                </div>

                <div class="p-5 space-y-5">
                    {{-- Policy --}}
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

                    {{-- Claimant --}}
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
                            <div><span class="text-gray-500 block">Full Name</span><span
                                    class="text-gray-800 font-medium">{{ $claimant['name'] ?: '—' }}</span></div>
                            <div><span class="text-gray-500 block">Email</span><span
                                    class="text-gray-700">{{ $claimant['email'] ?: '—' }}</span></div>
                            <div><span class="text-gray-500 block">Phone</span><span
                                    class="text-gray-700">{{ $claimant['phone'] ?: '—' }}</span></div>
                            <div><span class="text-gray-500 block">Occupation</span><span
                                    class="text-gray-700">{{ $claimant['occupation'] ?: '—' }}</span></div>
                        </div>
                    </div>

                    {{-- Vehicle Particulars --}}
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
                                    @php $value = $claim->form_data[$key] ?? ''; @endphp
                                    @if ($value)
                                        <div>
                                            <span
                                                class="text-gray-500 block">{{ ucfirst(str_replace('_', ' ', $key)) }}</span>
                                            <span class="text-gray-800 font-medium">{{ $value }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Accident Details --}}
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
                                    @php $value = $claim->form_data[$key] ?? ''; @endphp
                                    @if ($value)
                                        <div>
                                            <span
                                                class="text-gray-500 block">{{ ucfirst(str_replace('_', ' ', $key)) }}</span>
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

                </div>
            </div>

            {{-- Survey Completion --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-clipboard-check text-teal-500"></i> Submit Survey Findings
                    </h3>
                </div>
                <div class="p-5">
                    <form action="{{ route('surveyor.claims.complete', $claim) }}" method="POST" class="space-y-3"
                        id="surveyCompleteForm"
                        data-unassigned="{{ is_null($claim->surveyed_by) ? 'true' : 'false' }}">
                        @csrf
                        <textarea name="survey_notes" rows="5" required maxlength="2000"
                            placeholder="Describe your findings, observations, estimated damage, and recommendation..."
                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-teal-400 outline-none resize-none">{{ old('survey_notes') }}</textarea>
                        @error('survey_notes')
                            <p class="text-xs text-red-500">{{ $message }}</p>
                        @enderror
                        <button type="submit"
                            class="bg-teal-600 hover:bg-teal-700 text-white text-sm px-5 py-2.5 rounded-lg transition font-medium flex items-center gap-2">
                            <i class="fas fa-paper-plane text-xs"></i> Submit Findings
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
        // SweetAlert confirmation for unassigned survey submission
        const surveyForm = document.getElementById('surveyCompleteForm');
        if (surveyForm) {
            surveyForm.addEventListener('submit', function(e) {
                if (this.dataset.unassigned === 'true') {
                    e.preventDefault();
                    const form = this;
                    Swal.fire({
                        title: 'Submit Survey?',
                        html: `This survey report will be logged under your name<br><strong>{{ Auth::user()->name }}</strong>.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#0d9488',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, Submit',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Prevent the listener from firing again on programmatic submit
                            form.dataset.unassigned = 'false';
                            form.submit();
                        }
                    });
                }
            });
        }

        function openDocPreview(url, name, mimeType) {
            const modal = document.getElementById('docViewModal');
            const body = document.getElementById('docViewBody');
            const nameEl = document.getElementById('docViewName');
            const download = document.getElementById('docViewDownload');
            nameEl.textContent = name;
            download.href = url + '?download=1';
            if (mimeType.includes('pdf')) {
                body.innerHTML =
                    `<iframe src="${url}" class="w-full rounded" style="height:65vh;" frameborder="0"></iframe>`;
            } else if (mimeType.includes('image')) {
                body.innerHTML = `<img src="${url}" class="max-w-full max-h-[65vh] rounded-lg shadow object-contain" />`;
            } else {
                body.innerHTML =
                    `<div class="text-center py-12 text-gray-500"><i class="fas fa-file-alt text-5xl mb-4 text-gray-300"></i><p class="text-sm">${name}</p><a href="${url}?download=1" class="mt-4 inline-block bg-blue-600 text-white text-sm px-4 py-2 rounded-lg">Download to view</a></div>`;
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

</x-layouts.surveyor>
