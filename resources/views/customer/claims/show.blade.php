<x-layouts.app>

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
                    {{-- <div class="flex justify-between">
                        <span class="text-gray-500">Source</span>
                        <span class="text-gray-700 capitalize">{{ $claim->policy->source ?? 'N/A' }}</span>
                    </div> --}}
                    <div class="flex justify-between">
                        <span class="text-gray-500">Expires</span>
                        <span class="{{ $claim->policy?->end_date?->isPast() ? 'text-red-600' : 'text-gray-700' }}">
                            {{ $claim->policy?->end_date?->format('d M Y') ?? 'N/A' }}
                        </span>
                    </div>
                </div>
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
                                onclick="openDocPreview('{{ route('documents.preview', $doc->id) }}', '{{ $doc->original_name }}', '{{ $doc->mime_type }}')"
                                class="text-xs text-blue-600 hover:underline">
                                View
                            </button>
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
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                            <i class="fas fa-clipboard-list text-blue-500"></i> Claim Form Data
                        </h3>
                    </div>
                    <div class="flex gap-2">
                        @if (in_array($claim->status, ['submitted', 'pending_info']))
                            <a href="{{ route('claims.edit', $claim) }}"
                                class="border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm px-4 py-2 rounded-lg transition font-medium flex items-center gap-2">
                                <i class="fas fa-edit"></i> Edit Form
                            </a>
                        @else
                            <span class="text-xs text-gray-400 italic flex items-center gap-1 px-4 py-2">
                                <i class="fas fa-lock"></i> Editing locked — claim is
                                {{ str_replace('_', ' ', $claim->status) }}
                            </span>
                        @endif
                    </div>
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
                    <form action="{{ route('staff.claims.request-info', $claim) }}" method="POST" class="space-y-3">
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

</x-layouts.app>
