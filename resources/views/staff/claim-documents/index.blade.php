<x-layouts.staff>
    <!-- Header with filters and actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-folder-open text-indigo-500 text-2xl"></i>
                All Claim Attachments
            </h2>
            <p class="text-gray-500 text-sm mt-1">
                Manage documents submitted with claims — invoices, photos,
                reports, and more.
            </p>
        </div>
        <div class="flex flex-wrap gap-3">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="searchInput" placeholder="Search by client or policy..."
                    class="pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm w-64 bg-white" />
            </div>
            <button id="filterBtn"
                class="bg-white border border-gray-300 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2">
                <i class="fas fa-filter"></i> Filter
            </button>
            <button
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm shadow-sm flex items-center gap-2">
                <i class="fas fa-upload"></i> Upload New
            </button>
        </div>
    </div>

    <!-- Document Type Tabs (Functional) -->
    <div class="flex flex-wrap gap-2 mb-6 border-b border-gray-200 pb-2">
        <button data-type="all"
            class="type-tab px-4 py-2 text-sm font-medium text-indigo-600 border-b-2 border-indigo-600">
            All Documents (<span id="allCount">0</span>)
        </button>
        <button data-type="pdf" class="type-tab px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
            PDFs (<span id="pdfCount">0</span>)
        </button>
        <button data-type="image" class="type-tab px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
            Images (<span id="imageCount">0</span>)
        </button>
        <button data-type="spreadsheet"
            class="type-tab px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
            Spreadsheets (<span id="spreadsheetCount">0</span>)
        </button>
        <button data-type="other" class="type-tab px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
            Other (<span id="otherCount">0</span>)
        </button>
    </div>

    <!-- Policies Grid Container (responsive: 1/2/3 columns) -->
    <div id="policiesContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 items-start">
        <!-- Dynamically populated -->
    </div>

    <!-- Pagination (static) -->
    <div class="mt-8 flex justify-center">
        <div class="flex gap-2 items-center">
            <button class="px-3 py-1 border border-gray-300 rounded-md text-sm bg-white text-gray-600">
                <i class="fas fa-chevron-left"></i> Previous
            </button>
            <button class="px-3 py-1 bg-indigo-600 text-white rounded-md text-sm">1</button>
            <button class="px-3 py-1 border border-gray-300 rounded-md text-sm bg-white">2</button>
            <button class="px-3 py-1 border border-gray-300 rounded-md text-sm bg-white">Next <i
                    class="fas fa-chevron-right"></i></button>
        </div>
    </div>

    <!-- Summary stats footer -->
    <div
        class="mt-6 bg-indigo-50/40 rounded-xl border border-indigo-100 p-4 flex flex-wrap justify-between items-center gap-3">
        <div class="flex items-center gap-3 text-sm text-indigo-800">
            <i class="fas fa-database text-indigo-500"></i><span><strong>Document storage:</strong> <span
                    id="totalFilesCount">0</span> files · Total size 24.6 MB · Last updated today</span>
        </div>
        <div class="flex gap-2">
            <span class="bg-white px-3 py-1 rounded-full text-xs shadow-sm"><i class="fas fa-lock"></i> Secure
                storage</span>
            <span class="bg-white px-3 py-1 rounded-full text-xs shadow-sm"><i class="fas fa-clock"></i> Retention 7
                years</span>
        </div>
    </div>

    <!-- Modal for document preview -->
    <div id="docPreviewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl max-w-md w-full mx-4 p-6">
            <h3 class="text-lg font-bold mb-2">Document Preview</h3>
            <p id="previewFileName" class="text-sm text-gray-600 mb-4"></p>
            <div class="bg-gray-100 h-32 rounded flex items-center justify-center">
                <i class="fas fa-file-alt text-gray-400 text-5xl"></i>
            </div>
            <div class="flex justify-end gap-2 mt-4">
                <button id="closePreviewBtn" class="px-4 py-2 border rounded">Close</button>
                <button id="downloadFromPreview" class="px-4 py-2 bg-indigo-600 text-white rounded">Download</button>
            </div>
        </div>
    </div>

    <script>
        // ----- Data Structure: Policies with their documents -----
        const policiesData = [{
                id: 1,
                clientName: "John Davis",
                policyNumber: "P-1001-101-2026-000020",
                product: "Comprehensive",
                productIcon: "fas fa-car",
                productColor: "bg-blue-100 text-blue-800",
                documents: [{
                        name: "damage_estimate.pdf",
                        type: "pdf",
                        icon: "fas fa-file-pdf",
                        color: "text-red-500",
                        size: "1.2 MB",
                        uploaded: "2024-11-05"
                    },
                    {
                        name: "car_damage_front.jpg",
                        type: "image",
                        icon: "fas fa-image",
                        color: "text-blue-500",
                        size: "2.4 MB",
                        uploaded: "2024-11-05"
                    },
                    {
                        name: "police_report_scanned.pdf",
                        type: "pdf",
                        icon: "fas fa-file-pdf",
                        color: "text-red-500",
                        size: "856 KB",
                        uploaded: "2024-11-06"
                    }
                ]
            },
            {
                id: 2,
                clientName: "Sarah Mitchell",
                policyNumber: "P-1001-102-2026-000095",
                product: "Happy Home",
                productIcon: "fas fa-home",
                productColor: "bg-teal-100 text-teal-800",
                documents: [{
                        name: "plumber_invoice.pdf",
                        type: "pdf",
                        icon: "fas fa-file-pdf",
                        color: "text-red-500",
                        size: "423 KB",
                        uploaded: "2024-07-22"
                    },
                    {
                        name: "water_damage_photo1.jpg",
                        type: "image",
                        icon: "fas fa-image",
                        color: "text-blue-500",
                        size: "3.1 MB",
                        uploaded: "2024-07-22"
                    },
                    {
                        name: "damage_inventory.xlsx",
                        type: "spreadsheet",
                        icon: "fas fa-file-excel",
                        color: "text-green-600",
                        size: "212 KB",
                        uploaded: "2024-07-23"
                    }
                ]
            },
            {
                id: 3,
                clientName: "Michael Chen",
                policyNumber: "P-1001-101-2026-000023",
                product: "Fire Loss Of Profit",
                productIcon: "fas fa-fire",
                productColor: "bg-orange-100 text-orange-800",
                documents: [{
                        name: "medical_certificate.pdf",
                        type: "pdf",
                        icon: "fas fa-file-pdf",
                        color: "text-red-500",
                        size: "1.1 MB",
                        uploaded: "2024-02-18"
                    },
                    {
                        name: "doctor_note_signed.jpg",
                        type: "image",
                        icon: "fas fa-file-image",
                        color: "text-indigo-500",
                        size: "0.9 MB",
                        uploaded: "2024-02-18"
                    }
                ]
            },
            {
                id: 4,
                clientName: "Olivia Rodriguez",
                policyNumber: "P-1003-310-2026-000150",
                product: "Vanguard Safe Travel",
                productIcon: "fas fa-plane",
                productColor: "bg-cyan-100 text-cyan-800",
                documents: [{
                        name: "flight_cancellation_confirmation.pdf",
                        type: "pdf",
                        icon: "fas fa-file-pdf",
                        color: "text-red-500",
                        size: "688 KB",
                        uploaded: "2024-08-20"
                    },
                    {
                        name: "baggage_claim_receipt.pdf",
                        type: "pdf",
                        icon: "fas fa-receipt",
                        color: "text-gray-600",
                        size: "210 KB",
                        uploaded: "2024-08-21"
                    },
                    {
                        name: "lost_luggage_tag.jpg",
                        type: "image",
                        icon: "fas fa-image",
                        color: "text-blue-500",
                        size: "1.4 MB",
                        uploaded: "2024-08-20"
                    }
                ]
            },
            {
                id: 5,
                clientName: "David Kim",
                policyNumber: "COM-7789-3BZ",
                product: "Commercial Property",
                productIcon: "fas fa-building",
                productColor: "bg-orange-100 text-orange-800",
                documents: [{
                        name: "roof_inspection_report.pdf",
                        type: "pdf",
                        icon: "fas fa-file-pdf",
                        color: "text-red-500",
                        size: "2.1 MB",
                        uploaded: "2024-05-28"
                    },
                    {
                        name: "hail_damage_photo.png",
                        type: "image",
                        icon: "fas fa-image",
                        color: "text-blue-500",
                        size: "3.7 MB",
                        uploaded: "2024-05-29"
                    },
                    {
                        name: "contractor_quote.pdf",
                        type: "pdf",
                        icon: "fas fa-file-pdf",
                        color: "text-red-500",
                        size: "534 KB",
                        uploaded: "2024-05-30"
                    }
                ]
            },
            {
                id: 6,
                clientName: "Emily Watson",
                policyNumber: "P-1003-310-2026-000154",
                product: "Money Insurance",
                productIcon: "fas fa-car",
                productColor: "bg-pink-100 text-pink-800",
                documents: [{
                        name: "bank_report.pdf",
                        type: "pdf",
                        icon: "fas fa-file-pdf",
                        color: "text-red-500",
                        size: "1.0 MB",
                        uploaded: "2024-10-12"
                    },
                    {
                        name: "drivers_license.pdf",
                        type: "pdf",
                        icon: "fas fa-file-pdf",
                        color: "text-red-500",
                        size: "198 KB",
                        uploaded: "2024-10-12"
                    }
                ]
            }
        ];

        function getFileCategory(fileName) {
            const ext = fileName.split('.').pop().toLowerCase();
            if (ext === 'pdf') return 'pdf';
            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) return 'image';
            if (['xls', 'xlsx', 'csv'].includes(ext)) return 'spreadsheet';
            return 'other';
        }

        function updateTabCounts() {
            let pdfCount = 0,
                imageCount = 0,
                spreadsheetCount = 0,
                otherCount = 0;
            policiesData.forEach(policy => {
                policy.documents.forEach(doc => {
                    const cat = doc.type || getFileCategory(doc.name);
                    if (cat === 'pdf') pdfCount++;
                    else if (cat === 'image') imageCount++;
                    else if (cat === 'spreadsheet') spreadsheetCount++;
                    else otherCount++;
                });
            });
            document.getElementById('pdfCount').innerText = pdfCount;
            document.getElementById('imageCount').innerText = imageCount;
            document.getElementById('spreadsheetCount').innerText = spreadsheetCount;
            document.getElementById('otherCount').innerText = otherCount;
            document.getElementById('allCount').innerText = pdfCount + imageCount + spreadsheetCount + otherCount;
            document.getElementById('totalFilesCount').innerText = pdfCount + imageCount + spreadsheetCount + otherCount;
        }

        let activeTab = 'all';
        let searchQuery = '';

        function renderPolicies() {
            const container = document.getElementById('policiesContainer');
            container.innerHTML = '';

            policiesData.forEach(policy => {
                let filteredDocs = policy.documents.filter(doc => {
                    const cat = doc.type || getFileCategory(doc.name);
                    const matchesTab = activeTab === 'all' || cat === activeTab;
                    const matchesSearch = searchQuery === '' ||
                        policy.clientName.toLowerCase().includes(searchQuery) ||
                        policy.policyNumber.toLowerCase().includes(searchQuery);
                    return matchesTab && matchesSearch;
                });

                if (filteredDocs.length === 0) return;

                const docsHtml = filteredDocs.map(doc => {
                    const cat = doc.type || getFileCategory(doc.name);
                    let iconClass = 'fas fa-file-alt';
                    let colorClass = 'text-gray-500';
                    if (cat === 'pdf') {
                        iconClass = 'fas fa-file-pdf';
                        colorClass = 'text-red-500';
                    } else if (cat === 'image') {
                        iconClass = 'fas fa-image';
                        colorClass = 'text-blue-500';
                    } else if (cat === 'spreadsheet') {
                        iconClass = 'fas fa-file-excel';
                        colorClass = 'text-green-600';
                    }
                    return `
                        <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200">
                            <div class="flex items-center gap-3">
                                <i class="${iconClass} ${colorClass} text-lg"></i>
                                <div>
                                    <p class="font-medium text-gray-800">${doc.name}</p>
                                    <p class="text-xs text-gray-400">${doc.size} · Uploaded: ${doc.uploaded}</p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button onclick="previewDocument('${doc.name}')" class="text-gray-500 hover:text-gray-700"><i class="far fa-eye"></i></button>
                                <button onclick="downloadDocument('${doc.name}')" class="text-indigo-600 hover:text-indigo-800"><i class="fas fa-download"></i></button>
                            </div>
                        </div>
                    `;
                }).join('');

                const policyCard = `
                    <div class="policy-group bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">
                        <div class="policy-header p-4 cursor-pointer hover:bg-gray-50 transition flex justify-between items-center" data-policy-id="${policy.id}">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 ${policy.productColor} rounded-xl flex items-center justify-center">
                                    <i class="${policy.productIcon} text-sm"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-800 text-sm">${policy.clientName}</h3>
                                    <p class="text-xs text-gray-500">${policy.policyNumber}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs bg-gray-100 px-2 py-1 rounded-full">${filteredDocs.length}</span>
                                <i class="policy-toggle-icon fas fa-chevron-down text-gray-400 text-xs transition-transform"></i>
                            </div>
                        </div>
                        <div class="policy-docs hidden p-4 pt-0 space-y-2">
                            ${docsHtml}
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', policyCard);
            });

            // Attach toggle handlers
            document.querySelectorAll('.policy-header').forEach(header => {
                header.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const card = header.closest('.policy-group');
                    const docsDiv = card.querySelector('.policy-docs');
                    const icon = header.querySelector('.policy-toggle-icon');
                    if (docsDiv.classList.contains('hidden')) {
                        docsDiv.classList.remove('hidden');
                        icon.style.transform = 'rotate(180deg)';
                    } else {
                        docsDiv.classList.add('hidden');
                        icon.style.transform = 'rotate(0deg)';
                    }
                });
            });

            if (container.children.length === 0) {
                container.innerHTML =
                    '<div class="col-span-full text-center py-12 text-gray-400"><i class="fas fa-folder-open text-4xl mb-3"></i><p>No documents match your filters.</p></div>';
            }
        }

        // Tab switching
        document.querySelectorAll('.type-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                activeTab = tab.getAttribute('data-type');
                document.querySelectorAll('.type-tab').forEach(t => {
                    t.classList.remove('text-indigo-600', 'border-b-2', 'border-indigo-600');
                    t.classList.add('text-gray-500');
                });
                tab.classList.remove('text-gray-500');
                tab.classList.add('text-indigo-600', 'border-b-2', 'border-indigo-600');
                renderPolicies();
            });
        });

        // Search
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('input', (e) => {
            searchQuery = e.target.value.toLowerCase().trim();
            renderPolicies();
        });

        // Filter reset button
        document.getElementById('filterBtn').addEventListener('click', () => {
            searchInput.value = '';
            searchQuery = '';
            activeTab = 'all';
            document.querySelectorAll('.type-tab').forEach(t => {
                t.classList.remove('text-indigo-600', 'border-b-2', 'border-indigo-600');
                t.classList.add('text-gray-500');
            });
            document.querySelector('.type-tab[data-type="all"]').classList.remove('text-gray-500');
            document.querySelector('.type-tab[data-type="all"]').classList.add('text-indigo-600', 'border-b-2',
                'border-indigo-600');
            renderPolicies();
        });

        // Preview modal
        let currentPreviewFile = null;
        window.previewDocument = (fileName) => {
            currentPreviewFile = fileName;
            document.getElementById('previewFileName').innerText = fileName;
            document.getElementById('docPreviewModal').classList.remove('hidden');
            document.getElementById('docPreviewModal').classList.add('flex');
            document.body.style.overflow = 'hidden';
        };
        window.downloadDocument = (fileName) => {
            alert(`Downloading: ${fileName} (demo action)`);
        };

        function closePreviewModal() {
            document.getElementById('docPreviewModal').classList.add('hidden');
            document.getElementById('docPreviewModal').classList.remove('flex');
            document.body.style.overflow = '';
        }
        document.getElementById('closePreviewBtn').addEventListener('click', closePreviewModal);
        document.getElementById('downloadFromPreview').addEventListener('click', () => {
            if (currentPreviewFile) alert(`Downloading: ${currentPreviewFile}`);
            closePreviewModal();
        });
        document.getElementById('docPreviewModal').addEventListener('click', (e) => {
            if (e.target === document.getElementById('docPreviewModal')) closePreviewModal();
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closePreviewModal();
        });

        updateTabCounts();
        renderPolicies();
    </script>
</x-layouts.staff>
