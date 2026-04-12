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
                <input type="text" placeholder="Search documents..."
                    class="pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm w-64 bg-white" />
            </div>
            <button
                class="bg-white border border-gray-300 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2">
                <i class="fas fa-filter"></i> Filter
            </button>
            <button
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm shadow-sm flex items-center gap-2">
                <i class="fas fa-upload"></i> Upload New
            </button>
        </div>
    </div>

    <!-- Document Type Tabs (UI only) -->
    <div class="flex flex-wrap gap-2 mb-6 border-b border-gray-200 pb-2">
        <button class="px-4 py-2 text-sm font-medium text-indigo-600 border-b-2 border-indigo-600">
            All Documents (18)
        </button>
        <button class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
            PDFs
        </button>
        <button class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
            Images
        </button>
        <button class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
            Spreadsheets
        </button>
        <button class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
            Other
        </button>
    </div>

    <!-- Document Grid (Card View) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        <!-- Document Card 1: damage_estimate.pdf (Auto claim) -->
        <div
            class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition file-card">
            <div class="bg-gray-50 p-4 flex items-center justify-between border-b border-gray-300">
                <div class="flex items-center gap-3">
                    <i class="fas fa-file-pdf text-red-500 text-3xl"></i>
                    <div>
                        <p class="font-semibold text-gray-800 text-sm truncate w-36">
                            damage_estimate.pdf
                        </p>
                        <p class="text-xs text-gray-400">PDF · 1.2 MB</p>
                    </div>
                </div>
                <div class="relative group">
                    <button class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                </div>
            </div>
            <div class="p-4 space-y-2">
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <i class="fas fa-user-circle"></i>
                    <span>John Davis</span>
                    <span class="mx-1">•</span>
                    <i class="fas fa-file-alt"></i>
                    <span>Auto Claim</span>
                </div>
                <div class="flex justify-between items-center pt-2">
                    <span class="text-xs text-gray-400"><i class="far fa-clock"></i> Uploaded: 2024-11-05</span>
                    <div class="flex gap-2">
                        <button class="text-indigo-600 hover:text-indigo-800 text-sm">
                            <i class="fas fa-download"></i>
                        </button>
                        <button class="text-gray-500 hover:text-gray-700 text-sm">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: car_damage_front.jpg -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md">
            <div class="bg-gray-50 p-4 flex items-center justify-between border-b border-gray-300">
                <div class="flex items-center gap-3">
                    <i class="fas fa-image text-blue-500 text-3xl"></i>
                    <div>
                        <p class="font-semibold text-sm truncate w-36">
                            car_damage_front.jpg
                        </p>
                        <p class="text-xs text-gray-400">JPEG · 2.4 MB</p>
                    </div>
                </div>
                <button class="text-gray-400">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
            <div class="p-4 space-y-2">
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <i class="fas fa-user-circle"></i><span>John Davis</span><span>•</span><i
                        class="fas fa-car"></i><span>Auto</span>
                </div>
                <div class="flex justify-between pt-2">
                    <span class="text-xs text-gray-400"><i class="far fa-clock"></i> 2024-11-05</span>
                    <div class="flex gap-2">
                        <button class="text-indigo-600">
                            <i class="fas fa-download"></i></button><button class="text-gray-500">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: police_report_scanned.pdf -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gray-50 p-4 flex items-center justify-between border-b border-gray-300">
                <div class="flex gap-3">
                    <i class="fas fa-file-pdf text-red-500 text-3xl"></i>
                    <div>
                        <p class="font-semibold text-sm truncate">
                            police_report_scanned.pdf
                        </p>
                        <p class="text-xs text-gray-400">PDF · 856 KB</p>
                    </div>
                </div>
                <button class="text-gray-400">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
            <div class="p-4">
                <div class="flex text-xs text-gray-500 gap-2 mb-2">
                    <i class="fas fa-user-circle"></i><span>John Davis</span><span>•</span><span>Auto</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs text-gray-400"><i class="far fa-clock"></i> 2024-11-06</span>
                    <div>
                        <button class="text-indigo-600 mr-2">
                            <i class="fas fa-download"></i></button><button class="text-gray-500">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: plumber_invoice.pdf (Home claim) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gray-50 p-4 flex items-center justify-between border-b border-gray-300">
                <div class="flex gap-3">
                    <i class="fas fa-file-pdf text-red-500 text-3xl"></i>
                    <div>
                        <p class="font-semibold text-sm truncate">
                            plumber_invoice.pdf
                        </p>
                        <p class="text-xs text-gray-400">PDF · 423 KB</p>
                    </div>
                </div>
                <button class="text-gray-400">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
            <div class="p-4">
                <div class="flex text-xs text-gray-500 gap-2">
                    <i class="fas fa-user-circle"></i><span>Sarah Mitchell</span><span>•</span><i
                        class="fas fa-home"></i><span>Home</span>
                </div>
                <div class="flex justify-between mt-2">
                    <span class="text-xs text-gray-400"><i class="far fa-clock"></i> 2024-07-22</span>
                    <div>
                        <button class="text-indigo-600 mr-2">
                            <i class="fas fa-download"></i></button><button class="text-gray-500">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 5: water_damage_photo1.jpg -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gray-50 p-4 flex items-center justify-between border-b border-gray-300">
                <div class="flex gap-3">
                    <i class="fas fa-image text-blue-500 text-3xl"></i>
                    <div>
                        <p class="font-semibold text-sm truncate">
                            water_damage_photo1.jpg
                        </p>
                        <p class="text-xs text-gray-400">JPEG · 3.1 MB</p>
                    </div>
                </div>
                <button class="text-gray-400">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
            <div class="p-4">
                <div class="flex text-xs text-gray-500 gap-2">
                    <i class="fas fa-user-circle"></i><span>Sarah Mitchell</span><span>•</span><span>Home</span>
                </div>
                <div class="flex justify-between mt-2">
                    <span class="text-xs text-gray-400"><i class="far fa-clock"></i> 2024-07-22</span>
                    <div>
                        <button class="text-indigo-600 mr-2">
                            <i class="fas fa-download"></i></button><button class="text-gray-500">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 6: damage_inventory.xlsx -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gray-50 p-4 flex items-center justify-between border-b border-gray-300">
                <div class="flex gap-3">
                    <i class="fas fa-file-excel text-green-600 text-3xl"></i>
                    <div>
                        <p class="font-semibold text-sm truncate">
                            damage_inventory.xlsx
                        </p>
                        <p class="text-xs text-gray-400">XLSX · 212 KB</p>
                    </div>
                </div>
                <button class="text-gray-400">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
            <div class="p-4">
                <div class="flex text-xs text-gray-500 gap-2">
                    <i class="fas fa-user-circle"></i><span>Sarah Mitchell</span><span>•</span><span>Home</span>
                </div>
                <div class="flex justify-between mt-2">
                    <span class="text-xs text-gray-400"><i class="far fa-clock"></i> 2024-07-23</span>
                    <div>
                        <button class="text-indigo-600 mr-2">
                            <i class="fas fa-download"></i></button><button class="text-gray-500">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 7: medical_certificate.pdf (Life) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gray-50 p-4 flex items-center justify-between border-b border-gray-300">
                <div class="flex gap-3">
                    <i class="fas fa-file-pdf text-red-500 text-3xl"></i>
                    <div>
                        <p class="font-semibold text-sm truncate">
                            medical_certificate.pdf
                        </p>
                        <p class="text-xs text-gray-400">PDF · 1.1 MB</p>
                    </div>
                </div>
                <button class="text-gray-400">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
            <div class="p-4">
                <div class="flex text-xs text-gray-500 gap-2">
                    <i class="fas fa-user-circle"></i><span>Michael Chen</span><span>•</span><i
                        class="fas fa-heartbeat"></i><span>Life</span>
                </div>
                <div class="flex justify-between mt-2">
                    <span class="text-xs text-gray-400"><i class="far fa-clock"></i> 2024-02-18</span>
                    <div>
                        <button class="text-indigo-600 mr-2">
                            <i class="fas fa-download"></i></button><button class="text-gray-500">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 8: doctor_note_signed.jpg -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gray-50 p-4 flex items-center justify-between border-b border-gray-300">
                <div class="flex gap-3">
                    <i class="fas fa-file-image text-indigo-500 text-3xl"></i>
                    <div>
                        <p class="font-semibold text-sm truncate">
                            doctor_note_signed.jpg
                        </p>
                        <p class="text-xs text-gray-400">JPEG · 0.9 MB</p>
                    </div>
                </div>
                <button class="text-gray-400">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
            <div class="p-4">
                <div class="flex text-xs text-gray-500 gap-2">
                    <i class="fas fa-user-circle"></i><span>Michael Chen</span><span>•</span><span>Life</span>
                </div>
                <div class="flex justify-between mt-2">
                    <span class="text-xs text-gray-400"><i class="far fa-clock"></i> 2024-02-18</span>
                    <div>
                        <button class="text-indigo-600 mr-2">
                            <i class="fas fa-download"></i></button><button class="text-gray-500">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 9: flight_cancellation.pdf (Travel) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gray-50 p-4 flex items-center justify-between border-b border-gray-300">
                <div class="flex gap-3">
                    <i class="fas fa-file-pdf text-red-500 text-3xl"></i>
                    <div>
                        <p class="font-semibold text-sm truncate">
                            flight_cancellation.pdf
                        </p>
                        <p class="text-xs text-gray-400">PDF · 688 KB</p>
                    </div>
                </div>
                <button class="text-gray-400">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
            <div class="p-4">
                <div class="flex text-xs text-gray-500 gap-2">
                    <i class="fas fa-user-circle"></i><span>Olivia Rodriguez</span><span>•</span><i
                        class="fas fa-plane"></i><span>Travel</span>
                </div>
                <div class="flex justify-between mt-2">
                    <span class="text-xs text-gray-400"><i class="far fa-clock"></i> 2024-08-20</span>
                    <div>
                        <button class="text-indigo-600 mr-2">
                            <i class="fas fa-download"></i></button><button class="text-gray-500">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 10: baggage_claim_receipt.pdf -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gray-50 p-4 flex items-center justify-between border-b border-gray-300">
                <div class="flex gap-3">
                    <i class="fas fa-receipt text-gray-600 text-3xl"></i>
                    <div>
                        <p class="font-semibold text-sm truncate">
                            baggage_claim_receipt.pdf
                        </p>
                        <p class="text-xs text-gray-400">PDF · 210 KB</p>
                    </div>
                </div>
                <button class="text-gray-400">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
            <div class="p-4">
                <div class="flex text-xs text-gray-500 gap-2">
                    <i class="fas fa-user-circle"></i><span>Olivia Rodriguez</span><span>•</span><span>Travel</span>
                </div>
                <div class="flex justify-between mt-2">
                    <span class="text-xs text-gray-400"><i class="far fa-clock"></i> 2024-08-21</span>
                    <div>
                        <button class="text-indigo-600 mr-2">
                            <i class="fas fa-download"></i></button><button class="text-gray-500">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 11: lost_luggage_tag.jpg -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gray-50 p-4 flex items-center justify-between border-b border-gray-300">
                <div class="flex gap-3">
                    <i class="fas fa-image text-blue-500 text-3xl"></i>
                    <div>
                        <p class="font-semibold text-sm truncate">
                            lost_luggage_tag.jpg
                        </p>
                        <p class="text-xs text-gray-400">JPEG · 1.4 MB</p>
                    </div>
                </div>
                <button class="text-gray-400">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
            <div class="p-4">
                <div class="flex text-xs text-gray-500 gap-2">
                    <i class="fas fa-user-circle"></i><span>Olivia Rodriguez</span><span>•</span><span>Travel</span>
                </div>
                <div class="flex justify-between mt-2">
                    <span class="text-xs text-gray-400"><i class="far fa-clock"></i> 2024-08-20</span>
                    <div>
                        <button class="text-indigo-600 mr-2">
                            <i class="fas fa-download"></i></button><button class="text-gray-500">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 12: roof_inspection_report.pdf (Commercial) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gray-50 p-4 flex items-center justify-between border-b border-gray-300">
                <div class="flex gap-3">
                    <i class="fas fa-file-pdf text-red-500 text-3xl"></i>
                    <div>
                        <p class="font-semibold text-sm truncate">
                            roof_inspection_report.pdf
                        </p>
                        <p class="text-xs text-gray-400">PDF · 2.1 MB</p>
                    </div>
                </div>
                <button class="text-gray-400">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
            <div class="p-4">
                <div class="flex text-xs text-gray-500 gap-2">
                    <i class="fas fa-user-circle"></i><span>David Kim</span><span>•</span><i
                        class="fas fa-building"></i><span>Commercial</span>
                </div>
                <div class="flex justify-between mt-2">
                    <span class="text-xs text-gray-400"><i class="far fa-clock"></i> 2024-05-28</span>
                    <div>
                        <button class="text-indigo-600 mr-2">
                            <i class="fas fa-download"></i></button><button class="text-gray-500">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 13: hail_damage_photo.png -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gray-50 p-4 flex items-center justify-between border-b border-gray-300">
                <div class="flex gap-3">
                    <i class="fas fa-image text-blue-500 text-3xl"></i>
                    <div>
                        <p class="font-semibold text-sm truncate">
                            hail_damage_photo.png
                        </p>
                        <p class="text-xs text-gray-400">PNG · 3.7 MB</p>
                    </div>
                </div>
                <button class="text-gray-400">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
            <div class="p-4">
                <div class="flex text-xs text-gray-500 gap-2">
                    <i class="fas fa-user-circle"></i><span>David Kim</span><span>•</span><span>Commercial</span>
                </div>
                <div class="flex justify-between mt-2">
                    <span class="text-xs text-gray-400"><i class="far fa-clock"></i> 2024-05-29</span>
                    <div>
                        <button class="text-indigo-600 mr-2">
                            <i class="fas fa-download"></i></button><button class="text-gray-500">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 14: contractor_quote.pdf -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gray-50 p-4 flex items-center justify-between border-b border-gray-300">
                <div class="flex gap-3">
                    <i class="fas fa-file-pdf text-red-500 text-3xl"></i>
                    <div>
                        <p class="font-semibold text-sm truncate">
                            contractor_quote.pdf
                        </p>
                        <p class="text-xs text-gray-400">PDF · 534 KB</p>
                    </div>
                </div>
                <button class="text-gray-400">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
            <div class="p-4">
                <div class="flex text-xs text-gray-500 gap-2">
                    <i class="fas fa-user-circle"></i><span>David Kim</span><span>•</span><span>Commercial</span>
                </div>
                <div class="flex justify-between mt-2">
                    <span class="text-xs text-gray-400"><i class="far fa-clock"></i> 2024-05-30</span>
                    <div>
                        <button class="text-indigo-600 mr-2">
                            <i class="fas fa-download"></i></button><button class="text-gray-500">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 15: vet_report_luna.pdf (Pet claim extra) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gray-50 p-4 flex items-center justify-between border-b border-gray-300">
                <div class="flex gap-3">
                    <i class="fas fa-file-pdf text-red-500 text-3xl"></i>
                    <div>
                        <p class="font-semibold text-sm truncate">
                            vet_report_luna.pdf
                        </p>
                        <p class="text-xs text-gray-400">PDF · 1.0 MB</p>
                    </div>
                </div>
                <button class="text-gray-400">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
            <div class="p-4">
                <div class="flex text-xs text-gray-500 gap-2">
                    <i class="fas fa-user-circle"></i><span>Emily Watson</span><span>•</span><i
                        class="fas fa-paw"></i><span>Pet</span>
                </div>
                <div class="flex justify-between mt-2">
                    <span class="text-xs text-gray-400"><i class="far fa-clock"></i> 2024-10-12</span>
                    <div>
                        <button class="text-indigo-600 mr-2">
                            <i class="fas fa-download"></i></button><button class="text-gray-500">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 16: invoice_medication.pdf -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gray-50 p-4 flex items-center justify-between border-b border-gray-300">
                <div class="flex gap-3">
                    <i class="fas fa-file-pdf text-red-500 text-3xl"></i>
                    <div>
                        <p class="font-semibold text-sm truncate">
                            invoice_medication.pdf
                        </p>
                        <p class="text-xs text-gray-400">PDF · 198 KB</p>
                    </div>
                </div>
                <button class="text-gray-400">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
            <div class="p-4">
                <div class="flex text-xs text-gray-500 gap-2">
                    <i class="fas fa-user-circle"></i><span>Emily Watson</span><span>•</span><span>Pet</span>
                </div>
                <div class="flex justify-between mt-2">
                    <span class="text-xs text-gray-400"><i class="far fa-clock"></i> 2024-10-12</span>
                    <div>
                        <button class="text-indigo-600 mr-2">
                            <i class="fas fa-download"></i></button><button class="text-gray-500">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination (static) -->
    <div class="mt-8 flex justify-center">
        <div class="flex gap-2 items-center">
            <button class="px-3 py-1 border border-gray-300 rounded-md text-sm bg-white text-gray-600">
                <i class="fas fa-chevron-left"></i> Previous
            </button>
            <button class="px-3 py-1 bg-indigo-600 text-white rounded-md text-sm">
                1
            </button>
            <button class="px-3 py-1 border border-gray-300 rounded-md text-sm bg-white">
                2
            </button>
            <button class="px-3 py-1 border border-gray-300 rounded-md text-sm bg-white">
                3
            </button>
            <button class="px-3 py-1 border border-gray-300 rounded-md text-sm bg-white">
                Next <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>

    <!-- Summary stats footer -->
    <div
        class="mt-6 bg-indigo-50/40 rounded-xl border border-indigo-100 p-4 flex flex-wrap justify-between items-center gap-3">
        <div class="flex items-center gap-3 text-sm text-indigo-800">
            <i class="fas fa-database text-indigo-500"></i><span><strong>Document storage:</strong> 18 files · Total
                size 24.6 MB
                · Last updated today</span>
        </div>
        <div class="flex gap-2">
            <span class="bg-white px-3 py-1 rounded-full text-xs shadow-sm"><i class="fas fa-lock"></i> Secure
                storage</span><span class="bg-white px-3 py-1 rounded-full text-xs shadow-sm"><i
                    class="fas fa-clock"></i> Retention 7 years</span>
        </div>
    </div>
</x-layouts.staff>
