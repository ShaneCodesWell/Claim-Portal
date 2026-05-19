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
        <button data-type="spreadsheet"
            class="type-tab px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
            Spreadsheets ({{ $totalOther }})
        </button>
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
                        <div class="h-10 w-10 {{ $typeColor }} rounded-xl flex items-center justify-center">
                            <i class="{{ $typeIcon }} text-sm"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 text-sm">{{ $customer?->name ?? 'Unknown' }}</h3>
                            <p class="text-xs text-gray-500">{{ $policy->policy_number }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs bg-gray-100 px-2 py-1 rounded-full">{{ $docs->count() }}</span>
                        <i
                            class="policy-toggle-icon fas fa-chevron-down text-gray-400 text-xs transition-transform"></i>
                    </div>
                </div>

                {{-- Documents List --}}
                <div class="policy-docs hidden p-4 space-y-2">
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
                            $size = $doc->file_size ? number_format($doc->file_size / 1024, 1) . ' KB' : 'Unknown';
                        @endphp
                        <div class="doc-row flex items-center justify-between p-3 bg-white rounded-lg border border-gray-100"
                            data-doc-type="{{ $docType }}">
                            <div class="flex items-center gap-3">
                                <i class="{{ $icon }} text-lg"></i>
                                <div>
                                    <p class="font-medium text-gray-800 text-xs">{{ $doc->original_name }}</p>
                                    <p class="text-xs text-gray-400">
                                        {{ $size }} · {{ $doc->created_at->format('M d, Y') }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button
                                    onclick="openDocPreview('{{ route('staff.documents.preview', $doc->id) }}', '{{ $doc->original_name }}', '{{ $doc->mime_type }}')"
                                    class="text-gray-500 hover:text-blue-600 w-7 h-7 flex items-center justify-center rounded hover:bg-blue-50 transition">
                                    <i class="far fa-eye text-sm"></i>
                                </button>
                                <a href="{{ route('staff.documents.preview', $doc->id) }}?download=1"
                                    class="text-blue-600 hover:text-blue-800 w-7 h-7 flex items-center justify-center rounded hover:bg-blue-50 transition">
                                    <i class="fas fa-download text-sm"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
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
        });
    </script>
</x-layouts.staff>
