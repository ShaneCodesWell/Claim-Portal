<x-layouts.staff>
    <!-- Header with stats and filters (non-functional UI) -->
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
                <input type="text" placeholder="Search client, policy..."
                    class="pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-indigo-300 w-64 bg-white" />
            </div>
            <button
                class="bg-white border border-gray-300 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium text-gray-700 transition shadow-sm flex items-center gap-2">
                <i class="fas fa-filter text-gray-500"></i> Filter
            </button>
            <button
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-sm transition flex items-center gap-2">
                <i class="fas fa-download"></i> Export
            </button>
        </div>
    </div>

    <!-- Claims Table (static HTML rows) -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto custom-scroll">
            <table class="min-w-[1000px] md:min-w-full w-full table-auto">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Client Name</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Policy Number</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Product</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Policy Start</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Policy End</th>
                        <th class="px-5 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Claim Form</th>
                        <th class="px-5 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Documents</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <!-- Row 1: John Davis -->
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <div
                                    class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 text-sm font-semibold">
                                    JD</div>
                                <span class="font-medium text-gray-800">John Davis</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 font-mono text-sm text-gray-700">POL-AU-8723-01</td>
                        <td class="px-5 py-4">
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"><i
                                    class="fas fa-car text-xs"></i> Comprehensive</span>
                        </td>
                        <td class="px-5 py-4 text-sm">2024-01-15</td>
                        <td class="px-5 py-4 text-sm">2025-01-14</td>
                        <td class="px-5 py-4 text-center">
                            <button onclick="openClaimFormModal(1)"
                                class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm font-medium px-3 py-1.5 rounded-lg transition flex items-center gap-1 mx-auto"><i
                                    class="fas fa-eye mr-1"></i> View Form</button>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <button onclick="openDocumentsModal(1)"
                                class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-3 py-1.5 rounded-lg transition flex items-center gap-1 mx-auto"><i
                                    class="fas fa-paperclip mr-1"></i> Documents (3)</button>
                        </td>
                    </tr>
                    <!-- Row 2: Sarah Mitchell -->
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <div
                                    class="h-8 w-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 text-sm font-semibold">
                                    SM</div>
                                <span class="font-medium text-gray-800">Sarah Mitchell</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 font-mono text-sm text-gray-700">HOM-4562-89B</td>
                        <td class="px-5 py-4">
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-teal-100 text-teal-800"><i
                                    class="fas fa-home text-xs"></i> Happy Home</span>
                        </td>
                        <td class="px-5 py-4 text-sm">2023-09-10</td>
                        <td class="px-5 py-4 text-sm">2024-09-09</td>
                        <td class="px-5 py-4 text-center">
                            <button onclick="openClaimFormModal(2)"
                                class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm font-medium px-3 py-1.5 rounded-lg transition flex items-center gap-1 mx-auto"><i
                                    class="fas fa-eye mr-1"></i> View Form</button>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <button onclick="openDocumentsModal(2)"
                                class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-3 py-1.5 rounded-lg transition flex items-center gap-1 mx-auto"><i
                                    class="fas fa-paperclip mr-1"></i> Documents (3)</button>
                        </td>
                    </tr>
                    <!-- Row 3: Michael Chen -->
                    {{-- <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <div
                                    class="h-8 w-8 rounded-full bg-amber-100 flex items-center justify-center text-amber-700 text-sm font-semibold">
                                    MC</div>
                                <span class="font-medium text-gray-800">Michael Chen</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 font-mono text-sm text-gray-700">LIFE-9983-X2C</td>
                        <td class="px-5 py-4">
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800"><i
                                    class="fas fa-heartbeat text-xs"></i> Vanguard Life</span>
                        </td>
                        <td class="px-5 py-4 text-sm">2020-05-20</td>
                        <td class="px-5 py-4 text-sm">2035-05-19</td>
                        <td class="px-5 py-4 text-center">
                            <button onclick="openClaimFormModal(3)"
                                class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm font-medium px-3 py-1.5 rounded-lg transition flex items-center gap-1 mx-auto"><i
                                    class="fas fa-eye mr-1"></i> View Form</button>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <button onclick="openDocumentsModal(3)"
                                class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-3 py-1.5 rounded-lg transition flex items-center gap-1 mx-auto"><i
                                    class="fas fa-paperclip mr-1"></i> Documents (2)</button>
                        </td>
                    </tr> --}}
                    <!-- Row 4: Olivia Rodriguez -->
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <div
                                    class="h-8 w-8 rounded-full bg-rose-100 flex items-center justify-center text-rose-700 text-sm font-semibold">
                                    OR</div>
                                <span class="font-medium text-gray-800">Olivia Rodriguez</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 font-mono text-sm text-gray-700">TRV-1256-4AA</td>
                        <td class="px-5 py-4">
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800"><i
                                    class="fas fa-plane text-xs"></i> Vanguard Safe Travel</span>
                        </td>
                        <td class="px-5 py-4 text-sm">2024-03-01</td>
                        <td class="px-5 py-4 text-sm">2024-09-01</td>
                        <td class="px-5 py-4 text-center">
                            <button onclick="openClaimFormModal(4)"
                                class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm font-medium px-3 py-1.5 rounded-lg transition flex items-center gap-1 mx-auto"><i
                                    class="fas fa-eye mr-1"></i> View Form</button>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <button onclick="openDocumentsModal(4)"
                                class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-3 py-1.5 rounded-lg transition flex items-center gap-1 mx-auto"><i
                                    class="fas fa-paperclip mr-1"></i> Documents (3)</button>
                        </td>
                    </tr>
                    <!-- Row 5: David Kim -->
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <div
                                    class="h-8 w-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-700 text-sm font-semibold">
                                    DK</div>
                                <span class="font-medium text-gray-800">David Kim</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 font-mono text-sm text-gray-700">COM-7789-3BZ</td>
                        <td class="px-5 py-4">
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800"><i
                                    class="fas fa-building text-xs"></i> Commercial Fire</span>
                        </td>
                        <td class="px-5 py-4 text-sm">2022-11-01</td>
                        <td class="px-5 py-4 text-sm">2024-11-01</td>
                        <td class="px-5 py-4 text-center">
                            <button onclick="openClaimFormModal(5)"
                                class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm font-medium px-3 py-1.5 rounded-lg transition flex items-center gap-1 mx-auto"><i
                                    class="fas fa-eye mr-1"></i> View Form</button>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <button onclick="openDocumentsModal(5)"
                                class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-3 py-1.5 rounded-lg transition flex items-center gap-1 mx-auto"><i
                                    class="fas fa-paperclip mr-1"></i> Documents (3)</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-300 flex justify-between items-center flex-wrap gap-3">
            <div class="text-sm text-gray-500">
                <i class="fas fa-clipboard-list mr-1"></i> Showing 5 of 5 registered claims
            </div>
            <div class="flex gap-2">
                <button class="px-3 py-1 border border-gray-300 rounded-md text-sm bg-white"><i class="fas fa-chevron-left"></i>
                    Previous</button>
                <button class="px-3 py-1 bg-indigo-600 text-white rounded-md text-sm">1</button>
                <button class="px-3 py-1 border border-gray-300 rounded-md text-sm bg-white">2</button>
                <button class="px-3 py-1 border border-gray-300 rounded-md text-sm bg-white">Next <i
                        class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </div>

    <div
        class="mt-6 bg-indigo-50/40 rounded-xl border border-indigo-100 p-4 flex flex-wrap justify-between items-center gap-3">
        <div class="flex items-center gap-3 text-sm text-indigo-800">
            <i class="fas fa-info-circle text-indigo-500 text-lg"></i>
            <span><strong>Claims team overview:</strong> Click "View Form" to see full claim details, and "Documents
                (X)" to see/download all attached files.</span>
        </div>
        <div class="flex gap-2">
            <span class="bg-white px-3 py-1 rounded-full text-xs shadow-sm"><i class="far fa-file-alt"></i> Modal
                preview</span>
            <span class="bg-white px-3 py-1 rounded-full text-xs shadow-sm"><i class="far fa-folder-open"></i> File
                list modal</span>
        </div>
    </div>

    <!-- Modals (Blade components) -->
    <x-claim-form-modal />
    <x-documents-modal />

    <script>
        
        const claimsData = {
            1: {
                claimForm: {
                    incidentDate: "2024-11-02",
                    lossType: "Collision / Accident",
                    claimAmount: "$3,250.00",
                    description: "Rear-end collision on highway, front bumper damage, airbags deployed.",
                    policeReport: "#LAPD-9823-22",
                    witness: "Yes, available",
                    additional: "Vehicle towed to Elite Auto Body",
                },
                documents: [{
                        name: "damage_estimate.pdf",
                        icon: "fas fa-file-pdf",
                        color: "text-red-500",
                        size: "1.2 MB"
                    },
                    {
                        name: "car_damage_front.jpg",
                        icon: "fas fa-image",
                        color: "text-blue-500",
                        size: "2.4 MB"
                    },
                    {
                        name: "police_report_scanned.pdf",
                        icon: "fas fa-file-pdf",
                        color: "text-red-500",
                        size: "856 KB"
                    }
                ]
            },
            2: {
                claimForm: {
                    incidentDate: "2024-07-19",
                    lossType: "Water damage / Burst pipe",
                    claimAmount: "$7,430.00",
                    description: "Basement flooding due to burst pipe, damaged flooring and furniture.",
                    contractor: "PlumbingMaster Inc",
                    repairStatus: "In progress",
                },
                documents: [{
                        name: "plumber_invoice.pdf",
                        icon: "fas fa-file-pdf",
                        color: "text-red-500",
                        size: "423 KB"
                    },
                    {
                        name: "water_damage_photo1.jpg",
                        icon: "fas fa-image",
                        color: "text-blue-500",
                        size: "3.1 MB"
                    },
                    {
                        name: "damage_inventory.xlsx",
                        icon: "fas fa-file-excel",
                        color: "text-green-600",
                        size: "212 KB"
                    }
                ]
            },
            3: {
                claimForm: {
                    incidentDate: "2024-02-10",
                    lossType: "Critical Illness",
                    claimAmount: "Benefit claim",
                    description: "Diagnosed with covered critical illness. Medical reports attached.",
                    beneficiary: "Emma Chen (Spouse)",
                    hospital: "St. Mary's Medical",
                },
                documents: [{
                        name: "medical_certificate.pdf",
                        icon: "fas fa-file-pdf",
                        color: "text-red-500",
                        size: "1.1 MB"
                    },
                    {
                        name: "doctor_note_signed.jpg",
                        icon: "fas fa-file-image",
                        color: "text-indigo-500",
                        size: "0.9 MB"
                    }
                ]
            },
            4: {
                claimForm: {
                    incidentDate: "2024-08-12",
                    lossType: "Flight cancellation & baggage delay",
                    claimAmount: "$1,280.50",
                    description: "Flight canceled due to strike, baggage delayed by 72 hours.",
                    airline: "Iberia Airlines",
                    compensationRequested: "$1,280.50",
                },
                documents: [{
                        name: "flight_cancellation_confirmation.pdf",
                        icon: "fas fa-file-pdf",
                        color: "text-red-500",
                        size: "688 KB"
                    },
                    {
                        name: "baggage_claim_receipt.pdf",
                        icon: "fas fa-receipt",
                        color: "text-gray-600",
                        size: "210 KB"
                    },
                    {
                        name: "lost_luggage_tag.jpg",
                        icon: "fas fa-image",
                        color: "text-blue-500",
                        size: "1.4 MB"
                    }
                ]
            },
            5: {
                claimForm: {
                    incidentDate: "2024-05-22",
                    lossType: "Storm / Hail damage",
                    claimAmount: "$12,700.00",
                    description: "Severe hail damage to roof and HVAC units on commercial building.",
                    propertyAddress: "221B Business Park",
                    roofingContractor: "Elite Roofing Solutions",
                },
                documents: [{
                        name: "roof_inspection_report.pdf",
                        icon: "fas fa-file-pdf",
                        color: "text-red-500",
                        size: "2.1 MB"
                    },
                    {
                        name: "hail_damage_photo.png",
                        icon: "fas fa-image",
                        color: "text-blue-500",
                        size: "3.7 MB"
                    },
                    {
                        name: "contractor_quote.pdf",
                        icon: "fas fa-file-pdf",
                        color: "text-red-500",
                        size: "534 KB"
                    }
                ]
            }
        };

        // Modal handling (same as before, but using the static object)
        const claimModal = document.getElementById("claimFormModal");
        const docsModal = document.getElementById("documentsModal");

        function openClaimFormModal(claimId) {
            const claim = claimsData[claimId];
            if (!claim) return;
            const form = claim.claimForm;
            const contentDiv = document.getElementById("claimFormContent");
            contentDiv.innerHTML = `
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><span class="text-gray-500">Incident Date:</span><br><span class="font-medium">${form.incidentDate}</span></div>
                    <div><span class="text-gray-500">Loss Type:</span><br><span class="font-medium">${form.lossType}</span></div>
                    <div><span class="text-gray-500">Claim Amount:</span><br><span class="font-medium">${form.claimAmount}</span></div>
                    ${form.policeReport ? `<div><span class="text-gray-500">Police Report:</span><br><span class="font-medium">${form.policeReport}</span></div>` : ""}
                    ${form.witness ? `<div><span class="text-gray-500">Witness:</span><br><span class="font-medium">${form.witness}</span></div>` : ""}
                    ${form.contractor ? `<div><span class="text-gray-500">Contractor:</span><br><span class="font-medium">${form.contractor}</span></div>` : ""}
                    ${form.beneficiary ? `<div><span class="text-gray-500">Beneficiary:</span><br><span class="font-medium">${form.beneficiary}</span></div>` : ""}
                    ${form.airline ? `<div><span class="text-gray-500">Airline:</span><br><span class="font-medium">${form.airline}</span></div>` : ""}
                </div>
                <div class="bg-gray-50 p-3 rounded-lg text-sm mt-2">
                    <p class="font-semibold text-gray-700 mb-1">Description of loss:</p>
                    <p class="text-gray-600">${form.description}</p>
                </div>
                ${form.additional ? `<div class="text-xs text-gray-500 mt-2"><i class="fas fa-info-circle"></i> ${form.additional}</div>` : ""}
            `;
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
                contentDiv.innerHTML = `
                    <div class="space-y-2">
                        ${docs.map(doc => `
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100">
                                    <div class="flex items-center gap-3">
                                        <i class="${doc.icon} ${doc.color} text-lg"></i>
                                        <div><p class="font-medium text-gray-800">${doc.name}</p><p class="text-xs text-gray-400">${doc.size}</p></div>
                                    </div>
                                    <button onclick="alert('Download: ${doc.name} (demo)')" class="text-indigo-600 hover:text-indigo-800"><i class="fas fa-download"></i></button>
                                </div>
                            `).join('')}
                    </div>
                `;
            }
            docsModal.classList.add("active");
            document.body.style.overflow = "hidden";
        }

        function closeModals() {
            claimModal.classList.remove("active");
            docsModal.classList.remove("active");
            document.body.style.overflow = "";
        }

        // Attach close events
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
