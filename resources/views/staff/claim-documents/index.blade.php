<x-layouts.staff>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-folder-open text-blue-500 text-2xl"></i>
                All Claim Attachments
            </h2>
            <p class="text-gray-500 text-sm mt-1">
                Documents submitted with claims — grouped by policy.
            </p>
        </div>
        <div class="flex flex-wrap gap-3">
            <form method="GET" class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by client or policy..."
                    class="pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm w-64 bg-white" />
            </form>
        </div>
    </div>

    <!-- Tab counts -->
    <div class="flex flex-wrap gap-2 mb-6 border-b border-gray-200 pb-2">
        <button data-type="all" class="type-tab px-4 py-2 text-sm font-medium text-blue-600 border-b-2 border-blue-600">
            All Documents ({{ $totalDocs }})
        </button>
        <button data-type="pdf" class="type-tab px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
            PDFs ({{ $totalPdfs }})
        </button>
        <button data-type="image" class="type-tab px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
            Images ({{ $totalImages }})
        </button>
        {{-- <button data-type="spreadsheet"
            class="type-tab px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
            Spreadsheets ({{ $totalOther }})
        </button> --}}
        <button data-type="other" class="type-tab px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
            Other ({{ $totalOther }})
        </button>
    </div>

    <!-- Policies Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 items-start">
        @forelse($grouped as $group)
            @php
                $policy = $group['policy'];
                $customer = $group['customer'];
                $docs = $group['documents'];
                $typeColor = match (strtolower($policy->business_class_name ?? '')) {
                    'motor' => 'bg-blue-100 text-blue-800',
                    'fire' => 'bg-orange-100 text-orange-800',
                    'marine' => 'bg-cyan-100 text-cyan-800',
                    'aviation' => 'bg-sky-100 text-sky-800',
                    default => 'bg-gray-100 text-gray-800',
                };
                $typeIcon = match (strtolower($policy->business_class_name ?? '')) {
                    'motor' => 'fas fa-car',
                    'fire' => 'fas fa-fire',
                    'marine' => 'fas fa-ship',
                    'aviation' => 'fas fa-plane',
                    default => 'fas fa-file-contract',
                };
            @endphp

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">

                {{-- Policy Header --}}
                <div
                    class="p-4 border-b border-gray-100 flex items-center justify-between cursor-pointer hover:bg-gray-50 transition policy-header">
                    <div class="flex items-center gap-3">
                        {{-- Static folder icon — no more dynamic matching --}}
                        <div class="h-10 w-10 bg-gray-100 rounded-xl flex items-center justify-center shrink-0">
                            <i class="fas fa-folder text-gray-400 text-sm"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800 text-sm">{{ $customer?->name ?? 'Unknown' }}</h3>
                            <p class="text-xs text-gray-400 font-mono">{{ $policy->policy_number }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs bg-gray-100 text-gray-500 px-2 py-1 rounded-full">{{ $docs->count() }}
                            files</span>

                        @php $latestClaim = $policy->claims->first(); @endphp
                        @if ($latestClaim)
                            <button type="button"
                                onclick="event.stopPropagation(); toggleUploadForm('upload-{{ $latestClaim->id }}')"
                                class="text-white bg-blue-600 hover:bg-blue-700 w-7 h-7 flex items-center justify-center rounded-lg transition"
                                title="Upload Document">
                                <i class="fas fa-plus text-xs"></i>
                            </button>
                        @endif

                        <i
                            class="policy-toggle-icon fas fa-chevron-down text-gray-400 text-xs transition-transform duration-200"></i>
                    </div>
                </div>

                {{-- Upload form — hidden by default --}}
                @if ($latestClaim)
                    <div id="upload-{{ $latestClaim->id }}" class="hidden border-b border-gray-100 bg-blue-50/60 p-4">
                        <form action="{{ route('staff.claims.documents', $latestClaim) }}" method="POST"
                            enctype="multipart/form-data" class="flex items-center gap-2">
                            @csrf
                            <label
                                class="flex-1 flex items-center gap-2 bg-white border border-gray-200 rounded-lg px-3 py-2 cursor-pointer hover:border-blue-400 transition text-sm text-gray-400">
                                <i class="fas fa-paperclip text-xs"></i>
                                <span class="upload-label truncate">Choose files...</span>
                                <input type="file" name="documents[]" multiple
                                    accept="image/jpeg,image/png,image/gif,application/pdf" class="hidden"
                                    onchange="updateUploadLabel(this)">
                            </label>
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium px-3 py-2 rounded-lg transition whitespace-nowrap">
                                <i class="fas fa-upload mr-1"></i> Upload
                            </button>
                            <button type="button" onclick="toggleUploadForm('upload-{{ $latestClaim->id }}')"
                                class="text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-200 transition">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </form>
                        <p class="text-xs text-gray-400 mt-2 pl-1">
                            JPG, PNG, PDF · max 5MB each · attaches to <span
                                class="font-medium text-gray-500">{{ $latestClaim->claim_number }}</span>
                        </p>
                    </div>
                @endif

                {{-- Documents List --}}
                <div class="policy-docs hidden divide-y divide-gray-100">

                    {{-- Claim Forms --}}
                    @if ($group['claims']->isNotEmpty())
                        <div class="p-4 space-y-2">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Claim Forms</p>
                            @foreach ($group['claims'] as $claimRow)
                                <div
                                    class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100">
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-file-alt text-blue-400"></i>
                                        <div>
                                            <p class="font-medium text-gray-800 text-xs">{{ $claimRow['number'] }}</p>
                                            <p class="text-xs text-gray-400">
                                                {{ ucfirst(str_replace('_', ' ', $claimRow['type'])) }} claim</p>
                                        </div>
                                    </div>
                                    <button
                                        onclick="openClaimFormModal('{{ $claimRow['print_url'] }}', '{{ $claimRow['number'] }}')"
                                        class="text-gray-400 hover:text-blue-600 w-7 h-7 flex items-center justify-center rounded hover:bg-blue-100 transition"
                                        title="Preview Claim Form">
                                        <i class="far fa-eye text-sm"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Uploaded Documents --}}
                    @if ($docs->isNotEmpty())
                        <div class="p-4 space-y-2">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Attachments</p>
                            @foreach ($docs as $doc)
                                @php
                                    $isPdf = str_contains($doc->mime_type, 'pdf');
                                    $isImage = str_contains($doc->mime_type, 'image');
                                    $isSpreadsheet =
                                        str_contains($doc->mime_type, 'spreadsheet') ||
                                        str_contains($doc->original_name, '.xlsx') ||
                                        str_contains($doc->original_name, '.csv');
                                    $docType = $isPdf
                                        ? 'pdf'
                                        : ($isImage
                                            ? 'image'
                                            : ($isSpreadsheet
                                                ? 'spreadsheet'
                                                : 'other'));
                                    $icon = $isPdf
                                        ? 'fas fa-file-pdf text-red-400'
                                        : ($isImage
                                            ? 'fas fa-image text-blue-400'
                                            : 'fas fa-file text-gray-400');
                                    $size = $doc->file_size
                                        ? number_format($doc->file_size / 1024, 1) . ' KB'
                                        : 'Unknown';
                                @endphp

                                <div class="doc-row flex items-center justify-between p-3 bg-white rounded-lg border border-gray-100 hover:border-gray-200 hover:bg-gray-50 transition"
                                    data-doc-type="{{ $docType }}">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <i class="{{ $icon }} text-lg shrink-0"></i>
                                        <div class="min-w-0">
                                            <p class="font-medium text-gray-800 text-xs truncate">
                                                {{ $doc->original_name }}</p>
                                            <p class="text-xs text-gray-400">{{ $size }} ·
                                                {{ $doc->created_at->format('M d, Y') }}</p>
                                        </div>
                                    </div>

                                    {{-- Actions — single block, no duplicates --}}
                                    <div class="flex items-center gap-1 shrink-0 ml-2">
                                        <button
                                            onclick="openDocPreview('{{ route('staff.documents.preview', $doc->id) }}', '{{ $doc->original_name }}', '{{ $doc->mime_type }}')"
                                            class="text-gray-400 hover:text-blue-600 w-7 h-7 flex items-center justify-center rounded hover:bg-blue-50 transition"
                                            title="Preview">
                                            <i class="far fa-eye text-sm"></i>
                                        </button>
                                        <a href="{{ route('staff.documents.preview', $doc->id) }}?download=1"
                                            class="text-gray-400 hover:text-blue-600 w-7 h-7 flex items-center justify-center rounded hover:bg-blue-50 transition"
                                            title="Download">
                                            <i class="fas fa-download text-sm"></i>
                                        </a>
                                        <form action="{{ route('staff.claims.documents.destroy', $doc->id) }}"
                                            method="POST" onsubmit="return confirmDeleteDoc(event, this)">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-gray-400 hover:text-red-600 w-7 h-7 flex items-center justify-center rounded hover:bg-red-50 transition"
                                                title="Delete">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-16 text-gray-400">
                <i class="fas fa-folder-open text-5xl mb-4"></i>
                <p class="text-sm">No documents uploaded yet.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $policies->links() }}
    </div>

    <!-- Summary footer -->
    <div
        class="mt-6 bg-blue-50/40 rounded-xl border border-blue-100 p-4 flex flex-wrap justify-between items-center gap-3">
        <div class="flex items-center gap-3 text-sm text-blue-800">
            <i class="fas fa-database text-blue-500"></i>
            <span><strong>Document storage:</strong> {{ $totalDocs }} files · Secure · Retention 7 years</span>
        </div>
        <div class="flex gap-2">
            <span class="bg-white px-3 py-1 rounded-full text-xs shadow-sm"><i class="fas fa-lock"></i> Secure
                storage</span>
            <span class="bg-white px-3 py-1 rounded-full text-xs shadow-sm"><i class="fas fa-clock"></i> Retention 7
                years</span>
        </div>
    </div>

    {{-- Shared Document Preview Modal --}}
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
        document.addEventListener('DOMContentLoaded', () => {

            // ── Expand / collapse policy cards ──────────────────────────────────────
            document.querySelectorAll('.policy-header').forEach(header => {
                header.addEventListener('click', () => {
                    const card = header.closest('.bg-white');
                    const docs = card.querySelector('.policy-docs');
                    const icon = header.querySelector('.policy-toggle-icon');
                    const isOpen = !docs.classList.contains('hidden');
                    docs.classList.toggle('hidden', isOpen);
                    icon.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
                });
            });

            // ── Tab filtering ────────────────────────────────────────────────────────
            let activeTab = 'all';

            function applyTabFilter() {
                document.querySelectorAll('.policy-header').forEach(header => {
                    const card = header.closest('.bg-white.rounded-xl');
                    const allRows = card.querySelectorAll('.doc-row');

                    // Show/hide individual doc rows based on active tab
                    let visibleCount = 0;
                    allRows.forEach(row => {
                        const match = activeTab === 'all' || row.dataset.docType === activeTab;
                        row.classList.toggle('hidden', !match);
                        if (match) visibleCount++;
                    });

                    // Hide the entire card if no docs match the filter
                    card.classList.toggle('hidden', visibleCount === 0);

                    // Update the doc count badge on the card header
                    const badge = header.querySelector('.text-xs.bg-gray-100.px-2');
                    if (badge) badge.textContent = visibleCount;
                });

                // Show empty state if all cards are hidden
                const container = document.getElementById('policiesContainer');
                const visibleCards = [...container.querySelectorAll('.bg-white.rounded-xl:not(.hidden)')];
                let emptyState = document.getElementById('tab-empty-state');

                if (visibleCards.length === 0) {
                    if (!emptyState) {
                        emptyState = document.createElement('div');
                        emptyState.id = 'tab-empty-state';
                        emptyState.className = 'col-span-full text-center py-16 text-gray-400';
                        emptyState.innerHTML = `
                    <i class="fas fa-folder-open text-5xl mb-4"></i>
                    <p class="text-sm">No ${activeTab === 'all' ? '' : activeTab + ' '}documents found.</p>`;
                        container.appendChild(emptyState);
                    }
                } else {
                    emptyState?.remove();
                }
            }

            document.querySelectorAll('.type-tab').forEach(tab => {
                tab.addEventListener('click', () => {
                    activeTab = tab.dataset.type;

                    // Update active tab styles
                    document.querySelectorAll('.type-tab').forEach(t => {
                        t.classList.remove('text-blue-600', 'border-b-2',
                            'border-blue-600');
                        t.classList.add('text-gray-500');
                    });
                    tab.classList.remove('text-gray-500');
                    tab.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');

                    applyTabFilter();
                });
            });

            let loadedClaimForms = {}; // cache so we don't re-fetch
            let currentPrintClaimId = null;

            window.openClaimFormModal = function(printUrl, claimNumber) {
                const modal = document.getElementById('claimFormModal');
                const content = document.getElementById('claimFormModalContent');
                const subtitle = document.getElementById('claimFormModalSubtitle');

                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                currentPrintClaimId = printUrl; // store URL instead of ID

                if (subtitle) subtitle.textContent = claimNumber;

                if (loadedClaimForms[printUrl]) {
                    content.innerHTML = loadedClaimForms[printUrl];
                    return;
                }

                content.innerHTML = `
                    <div class="flex items-center justify-center py-20">
                        <svg class="animate-spin h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span class="ml-3 text-sm text-gray-500">Loading preview...</span>
                    </div>`;

                fetch(printUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ??
                                '',
                            'Accept': 'text/html',
                        }
                    })
                    .then(res => {
                        if (!res.ok) throw new Error(`HTTP ${res.status}`);
                        return res.text();
                    })
                    .then(html => {
                        const rendered = `<style>
                            #claimFormModalContent table { width:100%; border-collapse:collapse; margin-bottom:10px; }
                            #claimFormModalContent th, #claimFormModalContent td { border:1px solid #000; padding:4px 6px; font-size:10px; vertical-align:top; }
                            #claimFormModalContent th { font-weight:bold; background:#f0f0f0; text-transform:uppercase; font-size:9px; }
                            #claimFormModalContent .field-label { font-weight:bold; text-transform:uppercase; background:#fafafa; }
                            #claimFormModalContent .field-value { min-height:18px; }
                            #claimFormModalContent * { font-family: Arial, sans-serif; font-size:11px; }
                            #claimFormModalContent .no-print { display:none !important; }
                        </style>${html}`;
                        loadedClaimForms[printUrl] = rendered;
                        content.innerHTML = rendered;
                    })
                    .catch(err => {
                        content.innerHTML = `<p class="text-center text-red-500 py-12 text-sm">
                            Failed to load preview (${err.message}). 
                            <a href="${printUrl}" target="_blank" class="underline">Open in new tab instead.</a>
                        </p>`;
                    });
            };

            window.closeClaimFormModal = function() {
                document.getElementById('claimFormModal').classList.add('hidden');
                document.body.style.overflow = '';
                currentPrintClaimId = null;
            };

            document.getElementById('claimFormPrintBtn')?.addEventListener('click', function() {
                if (!currentPrintClaimId) return;
                const iframe = document.createElement('iframe');
                iframe.style.display = 'none';
                iframe.src = currentPrintClaimId; // now a URL, not an ID
                document.body.appendChild(iframe);
                iframe.onload = () => {
                    iframe.contentWindow.print();
                    setTimeout(() => iframe.remove(), 2000);
                };
            });

            // Close on backdrop click
            document.getElementById('claimFormModal')?.addEventListener('click', function(e) {
                if (e.target === this) closeClaimFormModal();
            });
        });

        // Toggle upload form visibility
        window.toggleUploadForm = function(id) {
            const form = document.getElementById(id);
            if (!form) return;
            form.classList.toggle('hidden');

            // Also expand the card if it's collapsed
            if (!form.classList.contains('hidden')) {
                const card = form.closest('.bg-white.rounded-xl');
                const docs = card?.querySelector('.policy-docs');
                const icon = card?.querySelector('.policy-toggle-icon');
                if (docs?.classList.contains('hidden')) {
                    docs.classList.remove('hidden');
                    if (icon) icon.style.transform = 'rotate(180deg)';
                }
            }
        };

        // Update file input label with selected file names
        window.updateUploadLabel = function(input) {
            const label = input.closest('label').querySelector('.upload-label');
            if (!label) return;
            label.textContent = input.files.length === 1 ?
                input.files[0].name :
                `${input.files.length} files selected`;
        };

        // SweetAlert confirm before delete
        window.confirmDeleteDoc = function(e, form) {
            e.preventDefault();
            Swal.fire({
                title: 'Delete this document?',
                text: 'This cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
            }).then(result => {
                if (result.isConfirmed) form.submit();
            });
            return false;
        };
    </script>

    {{-- At the very bottom, before closing x-layouts.staff --}}
    <div id="claimFormModal"
        class="hidden fixed inset-0 bg-black/60 z-50 flex items-start justify-center overflow-y-auto py-8 px-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl relative">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-white rounded-t-2xl">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-alt text-blue-600 text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-800">Claim Form Preview</h3>
                        <p id="claimFormModalSubtitle" class="text-xs text-gray-500"></p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button id="claimFormPrintBtn"
                        class="bg-[#1a3a5c] hover:bg-[#0f2540] text-white text-sm px-4 py-2 rounded-lg font-medium flex items-center gap-2 transition">
                        <i class="fas fa-print"></i> Print
                    </button>
                    <button onclick="closeClaimFormModal()"
                        class="p-2 hover:bg-gray-100 rounded-lg text-gray-500 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="p-8 m-8 border border-gray-300">
                <div id="claimFormModalContent" class="p-6 min-h-64"></div>
            </div>
        </div>
    </div>
</x-layouts.staff>
