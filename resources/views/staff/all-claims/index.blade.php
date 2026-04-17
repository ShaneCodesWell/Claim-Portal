<x-layouts.staff>
    <!-- Header with stats and filters -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-clipboard-list text-indigo-500 text-2xl"></i>
                Registered Claims
            </h2>
            <p class="text-gray-500 text-sm mt-1">
                All customer-initiated claims · ready for team review
            </p>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="searchInput" placeholder="Search client, policy..."
                    class="pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-indigo-300 w-64 bg-white" />
            </div>
            <button id="filterResetBtn"
                class="bg-white border border-gray-300 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium text-gray-700 transition shadow-sm flex items-center gap-2">
                <i class="fas fa-refresh text-gray-500"></i> Reset
            </button>
            <button
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-sm transition flex items-center gap-2">
                <i class="fas fa-download"></i> Export
            </button>
        </div>
    </div>

    <!-- Claim Amount Filter Tabs -->
    <div class="flex flex-wrap gap-2 mb-6 border-b border-gray-200 pb-2">
        <button data-filter="all"
            class="amount-filter-tab px-4 py-2 text-sm font-medium text-indigo-600 border-b-2 border-indigo-600">
            All Claims
        </button>
        <button data-filter="low"
            class="amount-filter-tab px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
            Same Day (≤ 30k)
        </button>
        <button data-filter="medium"
            class="amount-filter-tab px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
            Medium (30k - 100k)
        </button>
        <button data-filter="high"
            class="amount-filter-tab px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
            High (> 100k)
        </button>
    </div>

    <!-- Claims Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto custom-scroll">
            <table class="min-w-[1200px] md:min-w-full w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Client</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Policy Number</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Product</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Policy Period</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Claim Amount</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Assigned To</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Status</th>
                        <th class="px-4 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody id="claimsTableBody" class="divide-y divide-gray-200">
                    <!-- Row 1: John Davis - Low (25,000) -->
                    <tr class="hover:bg-gray-50 transition" data-amount="25000">
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-9 w-9 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-semibold">
                                    JD</div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">John Davis</p>
                                    <p class="text-xs text-gray-500">Motor claim</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 font-mono text-sm text-gray-700">P-1001-101-2026-000020</td>
                        <td class="px-4 py-4"><span
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"><i
                                    class="fas fa-car text-[10px]"></i> Comprehensive</span></td>
                        <td class="px-4 py-4 text-sm text-gray-700">
                            <div>2024-01-15</div>
                            <div class="text-xs text-gray-400">to 2025-01-14</div>
                        </td>
                        <td class="px-4 py-4"><span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">$25,000</span>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700">Chase Miller</td>
                        <td class="px-4 py-4"><span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Pending
                                Review</span></td>
                        <td class="px-4 py-4 text-right relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                class="h-9 w-9 rounded-lg hover:bg-gray-100 text-gray-500 transition inline-flex items-center justify-center"><i
                                    class="fas fa-ellipsis-v"></i></button>
                            <div x-show="open" @click.outside="open = false" x-transition
                                class="absolute right-4 top-12 z-20 w-48 bg-white rounded-xl shadow-lg border border-gray-200 py-2">
                                <button onclick="openClaimFormModal(1)"
                                    class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2"><i
                                        class="fas fa-eye text-xs text-indigo-500"></i> View Form</button>
                                <button onclick="openDocumentsModal(1)"
                                    class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2"><i
                                        class="fas fa-paperclip text-xs text-gray-500"></i> View Documents</button>
                                <div class="border-t border-gray-100 my-1"></div>
                                <button onclick="assignClaim(1)"
                                    class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2"><i
                                        class="fas fa-user-check text-xs text-emerald-500"></i> Assign Claim</button>
                            </div>
                        </td>
                    </tr>

                    <!-- Row 2: Sarah Mitchell - Medium (65,000) -->
                    <tr class="hover:bg-gray-50 transition" data-amount="65000">
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-9 w-9 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm font-semibold">
                                    SM</div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">Sarah Mitchell</p>
                                    <p class="text-xs text-gray-500">Property claim</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 font-mono text-sm text-gray-700">P-1001-102-2026-000095</td>
                        <td class="px-4 py-4"><span
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-teal-100 text-teal-800"><i
                                    class="fas fa-home text-[10px]"></i> Happy Home</span></td>
                        <td class="px-4 py-4 text-sm text-gray-700">
                            <div>2023-09-10</div>
                            <div class="text-xs text-gray-400">to 2024-09-09</div>
                        </td>
                        <td class="px-4 py-4"><span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">$65,000</span>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700">Lisa Crawford</td>
                        <td class="px-4 py-4"><span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Assigned</span>
                        </td>
                        <td class="px-4 py-4 text-right relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                class="h-9 w-9 rounded-lg hover:bg-gray-100 text-gray-500 transition inline-flex items-center justify-center"><i
                                    class="fas fa-ellipsis-v"></i></button>
                            <div x-show="open" @click.outside="open = false" x-transition
                                class="absolute right-4 top-12 z-20 w-48 bg-white rounded-xl shadow-lg border border-gray-200 py-2">
                                <button onclick="openClaimFormModal(2)"
                                    class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2"><i
                                        class="fas fa-eye text-xs text-indigo-500"></i> View Form</button>
                                <button onclick="openDocumentsModal(2)"
                                    class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2"><i
                                        class="fas fa-paperclip text-xs text-gray-500"></i> View Documents</button>
                                <div class="border-t border-gray-100 my-1"></div>
                                <button onclick="assignClaim(2)"
                                    class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2"><i
                                        class="fas fa-user-check text-xs text-emerald-500"></i> Assign Claim</button>
                            </div>
                        </td>
                    </tr>

                    <!-- Row 3: Olivia Rodriguez - High (150,000) -->
                    <tr class="hover:bg-gray-50 transition" data-amount="150000">
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-9 w-9 rounded-full bg-rose-100 text-rose-700 flex items-center justify-center text-sm font-semibold">
                                    OR</div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">Olivia Rodriguez</p>
                                    <p class="text-xs text-gray-500">Travel claim</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 font-mono text-sm text-gray-700">P-1003-310-2026-000150</td>
                        <td class="px-4 py-4"><span
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800"><i
                                    class="fas fa-plane text-[10px]"></i> Safe Travel</span></td>
                        <td class="px-4 py-4 text-sm text-gray-700">
                            <div>2024-03-01</div>
                            <div class="text-xs text-gray-400">to 2024-09-01</div>
                        </td>
                        <td class="px-4 py-4"><span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">$150,000</span>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700">Unassigned</td>
                        <td class="px-4 py-4"><span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">New</span>
                        </td>
                        <td class="px-4 py-4 text-right relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                class="h-9 w-9 rounded-lg hover:bg-gray-100 text-gray-500 transition inline-flex items-center justify-center"><i
                                    class="fas fa-ellipsis-v"></i></button>
                            <div x-show="open" @click.outside="open = false" x-transition
                                class="absolute right-4 top-12 z-20 w-48 bg-white rounded-xl shadow-lg border border-gray-200 py-2">
                                <button onclick="openClaimFormModal(3)"
                                    class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2"><i
                                        class="fas fa-eye text-xs text-indigo-500"></i> View Form</button>
                                <button onclick="openDocumentsModal(3)"
                                    class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2"><i
                                        class="fas fa-paperclip text-xs text-gray-500"></i> View Documents</button>
                                <div class="border-t border-gray-100 my-1"></div>
                                <button onclick="assignClaim(3)"
                                    class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2"><i
                                        class="fas fa-user-check text-xs text-emerald-500"></i> Assign Claim</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Footer with pagination info -->
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200 flex justify-between items-center flex-wrap gap-3">
            <div class="text-sm text-gray-500">
                <i class="fas fa-clipboard-list mr-1"></i>
                Showing <span id="visibleCount">3</span> of <span id="totalCount">3</span> registered claims
            </div>
            <div class="flex gap-2">
                <button class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm bg-white hover:bg-gray-50"><i
                        class="fas fa-chevron-left"></i> Previous</button>
                <button class="px-3 py-1.5 bg-indigo-600 text-white rounded-lg text-sm">1</button>
                <button class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm bg-white hover:bg-gray-50">Next <i
                        class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </div>

    <div
        class="mt-6 bg-indigo-50/40 rounded-xl border border-indigo-100 p-4 flex flex-wrap justify-between items-center gap-3">
        <div class="flex items-center gap-3 text-sm text-indigo-800">
            <i class="fas fa-info-circle text-indigo-500 text-lg"></i>
            <span><strong>Claims team overview:</strong> Use the amount filters to triage claims by value.</span>
        </div>
        <div class="flex gap-2">
            <span class="bg-white px-3 py-1 rounded-full text-xs shadow-sm"><i class="far fa-file-alt"></i> Modal
                preview</span>
            <span class="bg-white px-3 py-1 rounded-full text-xs shadow-sm"><i class="far fa-folder-open"></i> File
                list modal</span>
        </div>
    </div>

    <!-- Modals (Blade components) -->
    {{-- <x-claim-form-modal />
    <x-documents-modal /> --}}

    <script>
        // Amount filter logic
        const filterTabs = document.querySelectorAll('.amount-filter-tab');
        const tableRows = document.querySelectorAll('#claimsTableBody tr');
        const totalCountSpan = document.getElementById('totalCount');
        const visibleCountSpan = document.getElementById('visibleCount');
        const searchInput = document.getElementById('searchInput');
        const resetBtn = document.getElementById('filterResetBtn');

        let currentAmountFilter = 'all';
        let currentSearchTerm = '';

        function filterTable() {
            let visible = 0;
            tableRows.forEach(row => {
                const amount = parseInt(row.getAttribute('data-amount'));
                let matchesAmount = true;
                if (currentAmountFilter === 'low') matchesAmount = amount <= 30000;
                else if (currentAmountFilter === 'medium') matchesAmount = amount > 30000 && amount <= 100000;
                else if (currentAmountFilter === 'high') matchesAmount = amount > 100000;

                // Search filter (client name or policy number)
                const clientName = row.querySelector('td:first-child .text-sm.font-medium')?.innerText
                .toLowerCase() || '';
                const policyNumber = row.querySelector('td:nth-child(2)')?.innerText.toLowerCase() || '';
                const matchesSearch = currentSearchTerm === '' || clientName.includes(currentSearchTerm) ||
                    policyNumber.includes(currentSearchTerm);

                if (matchesAmount && matchesSearch) {
                    row.style.display = '';
                    visible++;
                } else {
                    row.style.display = 'none';
                }
            });
            visibleCountSpan.innerText = visible;
        }

        // Tab click handlers
        filterTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                currentAmountFilter = tab.getAttribute('data-filter');
                filterTabs.forEach(t => {
                    t.classList.remove('text-indigo-600', 'border-b-2', 'border-indigo-600');
                    t.classList.add('text-gray-500');
                });
                tab.classList.remove('text-gray-500');
                tab.classList.add('text-indigo-600', 'border-b-2', 'border-indigo-600');
                filterTable();
            });
        });

        // Search input
        searchInput.addEventListener('input', (e) => {
            currentSearchTerm = e.target.value.toLowerCase().trim();
            filterTable();
        });

        // Reset button: clear search and set filter to All
        resetBtn.addEventListener('click', () => {
            searchInput.value = '';
            currentSearchTerm = '';
            // Reset amount filter to All
            currentAmountFilter = 'all';
            filterTabs.forEach(t => {
                t.classList.remove('text-indigo-600', 'border-b-2', 'border-indigo-600');
                t.classList.add('text-gray-500');
            });
            const allTab = document.querySelector('.amount-filter-tab[data-filter="all"]');
            if (allTab) {
                allTab.classList.remove('text-gray-500');
                allTab.classList.add('text-indigo-600', 'border-b-2', 'border-indigo-600');
            }
            filterTable();
        });

        // Initialize counts
        totalCountSpan.innerText = tableRows.length;
        filterTable();

        // Modal functions (unchanged)
        const claimsData = {
            1: {
                claimForm: {
                    incidentDate: "2024-11-02",
                    lossType: "Collision / Accident",
                    claimAmount: "$3,250.00",
                    description: "Rear-end collision on highway, front bumper damage, airbags deployed.",
                    policeReport: "#LAPD-9823-22",
                    witness: "Yes, available",
                    additional: "Vehicle towed to Elite Auto Body"
                },
                documents: [{
                    name: "damage_estimate.pdf",
                    icon: "fas fa-file-pdf",
                    color: "text-red-500",
                    size: "1.2 MB"
                }, {
                    name: "car_damage_front.jpg",
                    icon: "fas fa-image",
                    color: "text-blue-500",
                    size: "2.4 MB"
                }, {
                    name: "police_report_scanned.pdf",
                    icon: "fas fa-file-pdf",
                    color: "text-red-500",
                    size: "856 KB"
                }]
            },
            2: {
                claimForm: {
                    incidentDate: "2024-07-19",
                    lossType: "Water damage / Burst pipe",
                    claimAmount: "$7,430.00",
                    description: "Basement flooding due to burst pipe, damaged flooring and furniture.",
                    contractor: "PlumbingMaster Inc",
                    repairStatus: "In progress"
                },
                documents: [{
                    name: "plumber_invoice.pdf",
                    icon: "fas fa-file-pdf",
                    color: "text-red-500",
                    size: "423 KB"
                }, {
                    name: "water_damage_photo1.jpg",
                    icon: "fas fa-image",
                    color: "text-blue-500",
                    size: "3.1 MB"
                }, {
                    name: "damage_inventory.xlsx",
                    icon: "fas fa-file-excel",
                    color: "text-green-600",
                    size: "212 KB"
                }]
            },
            3: {
                claimForm: {
                    incidentDate: "2024-08-12",
                    lossType: "Flight cancellation & baggage delay",
                    claimAmount: "$1,280.50",
                    description: "Flight canceled due to strike, baggage delayed by 72 hours.",
                    airline: "Iberia Airlines",
                    compensationRequested: "$1,280.50"
                },
                documents: [{
                    name: "flight_cancellation_confirmation.pdf",
                    icon: "fas fa-file-pdf",
                    color: "text-red-500",
                    size: "688 KB"
                }, {
                    name: "baggage_claim_receipt.pdf",
                    icon: "fas fa-receipt",
                    color: "text-gray-600",
                    size: "210 KB"
                }, {
                    name: "lost_luggage_tag.jpg",
                    icon: "fas fa-image",
                    color: "text-blue-500",
                    size: "1.4 MB"
                }]
            }
        };
        const claimModal = document.getElementById("claimFormModal");
        const docsModal = document.getElementById("documentsModal");

        function openClaimFormModal(claimId) {
            const claim = claimsData[claimId];
            if (!claim) return;
            const form = claim.claimForm;
            const contentDiv = document.getElementById("claimFormContent");
            contentDiv.innerHTML =
                `<div class="grid grid-cols-2 gap-4 text-sm"><div><span class="text-gray-500">Incident Date:</span><br><span class="font-medium">${form.incidentDate}</span></div><div><span class="text-gray-500">Loss Type:</span><br><span class="font-medium">${form.lossType}</span></div><div><span class="text-gray-500">Claim Amount:</span><br><span class="font-medium">${form.claimAmount}</span></div>${form.policeReport ? `<div><span class="text-gray-500">Police Report:</span><br><span class="font-medium">${form.policeReport}</span></div>` : ""}${form.witness ? `<div><span class="text-gray-500">Witness:</span><br><span class="font-medium">${form.witness}</span></div>` : ""}${form.contractor ? `<div><span class="text-gray-500">Contractor:</span><br><span class="font-medium">${form.contractor}</span></div>` : ""}${form.beneficiary ? `<div><span class="text-gray-500">Beneficiary:</span><br><span class="font-medium">${form.beneficiary}</span></div>` : ""}${form.airline ? `<div><span class="text-gray-500">Airline:</span><br><span class="font-medium">${form.airline}</span></div>` : ""}</div><div class="bg-gray-50 p-3 rounded-lg text-sm mt-2"><p class="font-semibold text-gray-700 mb-1">Description of loss:</p><p class="text-gray-600">${form.description}</p></div>${form.additional ? `<div class="text-xs text-gray-500 mt-2"><i class="fas fa-info-circle"></i> ${form.additional}</div>` : ""}`;
            claimModal.classList.add("active");
            document.body.style.overflow = "hidden";
        }

        function openDocumentsModal(claimId) {
            const claim = claimsData[claimId];
            if (!claim) return;
            const docs = claim.documents;
            const contentDiv = document.getElementById("documentsContent");
            if (docs.length === 0) {
                contentDiv.innerHTML = `<p class="text-gray-500 text-sm">No documents attached.</p>`;
            } else {
                contentDiv.innerHTML =
                    `<div class="space-y-2">${docs.map(doc => `<div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100"><div class="flex items-center gap-3"><i class="${doc.icon} ${doc.color} text-lg"></i><div><p class="font-medium text-gray-800">${doc.name}</p><p class="text-xs text-gray-400">${doc.size}</p></div></div><button onclick="alert('Download: ${doc.name} (demo)')" class="text-indigo-600 hover:text-indigo-800"><i class="fas fa-download"></i></button></div>`).join('')}</div>`;
            }
            docsModal.classList.add("active");
            document.body.style.overflow = "hidden";
        }

        function closeModals() {
            claimModal.classList.remove("active");
            docsModal.classList.remove("active");
            document.body.style.overflow = "";
        }
        document.querySelectorAll(".close-modal-btn, .close-docs-modal").forEach(btn => btn.addEventListener("click",
            closeModals));
        claimModal.addEventListener("click", (e) => {
            if (e.target === claimModal) closeModals();
        });
        docsModal.addEventListener("click", (e) => {
            if (e.target === docsModal) closeModals();
        });
        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") closeModals();
        });
    </script>
</x-layouts.staff>
