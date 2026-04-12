<x-layouts.staff>
    <!-- Page header with actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-file-alt text-indigo-500 text-2xl"></i>
                Submitted Claim Forms
            </h2>
            <p class="text-gray-500 text-sm mt-1">
                View all completed claim forms with customer input and attached
                documentation.
            </p>
        </div>
        <div class="flex gap-3">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" placeholder="Search by client, policy..."
                    class="pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm w-64 bg-white" />
            </div>
            <button
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm shadow-sm flex items-center gap-2">
                <i class="fas fa-download"></i> Export Forms
            </button>
        </div>
    </div>

    <!-- Claim Forms Grid/Cards - each card represents a submitted claim form -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Form Card 1: John Davis - Auto -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden hover:shadow-lg transition">
            <div class="p-5 border-b border-gray-100 flex justify-between items-start">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">Auto
                            Insurance</span>
                        <span class="text-xs text-gray-400">Form ID: CLM-101</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">John Davis</h3>
                    <p class="text-sm text-gray-500">Policy: POL-AU-8723-01</p>
                </div>
                <div class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">
                    <i class="fas fa-check-circle mr-1"></i> Completed
                </div>
            </div>
            <div class="p-5 space-y-4">
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <span class="text-gray-500">Incident Date:</span><br /><span
                            class="font-medium">2024-11-02</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Loss Type:</span><br /><span class="font-medium">Collision /
                            Accident</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Claim Amount:</span><br /><span class="font-medium">$3,250.00</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Police Report:</span><br /><span
                            class="font-medium">#LAPD-9823-22</span>
                    </div>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg text-sm">
                    <p class="text-gray-600 font-semibold mb-1">
                        Description of loss:
                    </p>
                    <p class="text-gray-700">
                        Rear-end collision on highway, front bumper damage, airbags
                        deployed. Driver not injured. Police report attached.
                    </p>
                </div>
                <details class="text-sm">
                    <summary class="text-indigo-600 font-medium cursor-pointer">
                        View full claim form details
                    </summary>
                    <div class="mt-3 pt-3 border-t border-gray-200 space-y-2 text-sm">
                        <div class="grid grid-cols-2 gap-2">
                            <span class="text-gray-500">Witness:</span><span>Yes, available (James T.)</span><span
                                class="text-gray-500">Weather conditions:</span><span>Clear, dry road</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <span class="text-gray-500">Vehicle damage estimate:</span><span>$3,250
                                (approved)</span><span class="text-gray-500">Repair shop:</span><span>Elite Auto
                                Body</span>
                        </div>
                        <div class="text-xs text-gray-400 mt-2">
                            <i class="far fa-calendar-alt"></i> Form submitted on:
                            2024-11-05 14:23
                        </div>
                    </div>
                </details>
                <div class="flex flex-wrap gap-2 pt-2">
                    <span class="inline-flex items-center gap-1 text-xs bg-gray-100 px-2 py-1 rounded"><i
                            class="fas fa-file-pdf text-red-500"></i>
                        damage_estimate.pdf</span>
                    <span class="inline-flex items-center gap-1 text-xs bg-gray-100 px-2 py-1 rounded"><i
                            class="fas fa-image text-blue-500"></i>
                        car_damage_front.jpg</span>
                    <span class="inline-flex items-center gap-1 text-xs bg-gray-100 px-2 py-1 rounded"><i
                            class="fas fa-file-alt text-gray-500"></i>
                        police_report_scanned.pdf</span>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3 border-t border-gray-300 flex justify-between">
                <button class="text-indigo-600 text-sm font-medium hover:underline">
                    <i class="far fa-eye"></i> Preview form
                </button>
                <button class="text-gray-600 text-sm">
                    <i class="fas fa-paperclip"></i> Documents (3)
                </button>
            </div>
        </div>

        <!-- Form Card 2: Sarah Mitchell - Home -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden hover:shadow-lg transition">
            <div class="p-5 border-b border-gray-100 flex justify-between items-start">
                <div>
                    <span class="bg-teal-100 text-teal-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">Home
                        Insurance</span><span class="text-xs text-gray-400 ml-2">Form ID: CLM-102</span>
                    <h3 class="text-lg font-bold mt-1">Sarah Mitchell</h3>
                    <p class="text-sm text-gray-500">Policy: HOM-4562-89B</p>
                </div>
                <div class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">
                    <i class="fas fa-check-circle"></i> Completed
                </div>
            </div>
            <div class="p-5 space-y-4">
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <span class="text-gray-500">Incident Date:</span><br /><span
                            class="font-medium">2024-07-19</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Loss Type:</span><br /><span class="font-medium">Water damage /
                            Burst pipe</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Claim Amount:</span><br /><span class="font-medium">$7,430.00</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Contractor:</span><br /><span class="font-medium">PlumbingMaster
                            Inc</span>
                    </div>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg text-sm">
                    <p class="font-semibold mb-1">Description:</p>
                    <p>
                        Basement flooding due to pipe burst, damaged flooring and
                        furniture. Immediate mitigation started.
                    </p>
                </div>
                <details>
                    <summary class="text-indigo-600 font-medium">
                        View full claim form
                    </summary>
                    <div class="mt-3 pt-3 border-t space-y-2 text-sm">
                        <div class="grid grid-cols-2 gap-2">
                            <span class="text-gray-500">Repair Status:</span><span>In progress</span><span
                                class="text-gray-500">Estimated completion:</span><span>Dec 2024</span>
                        </div>
                        <div class="text-xs text-gray-400 mt-2">
                            Submitted: 2024-07-22
                        </div>
                    </div>
                </details>
                <div class="flex flex-wrap gap-2">
                    <span class="text-xs bg-gray-100 px-2 py-1 rounded"><i class="fas fa-file-pdf text-red-500"></i>
                        plumber_invoice.pdf</span><span class="text-xs bg-gray-100 px-2 py-1 rounded"><i
                            class="fas fa-image text-blue-500"></i>
                        water_damage_photo1.jpg</span><span class="text-xs bg-gray-100 px-2 py-1 rounded"><i
                            class="fas fa-file-excel text-green-600"></i>
                        damage_inventory.xlsx</span>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3 border-t border-gray-300 flex justify-between">
                <button class="text-indigo-600 text-sm">
                    <i class="far fa-eye"></i> Preview form</button><button class="text-gray-600 text-sm">
                    <i class="fas fa-paperclip"></i> Documents (3)
                </button>
            </div>
        </div>

        <!-- Form Card 3: Michael Chen - Life -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden hover:shadow-lg transition">
            <div class="p-5 border-b border-gray-100 flex justify-between items-start">
                <div>
                    <span class="bg-purple-100 text-purple-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">Life
                        Insurance</span><span class="text-xs text-gray-400 ml-2">Form ID: CLM-103</span>
                    <h3 class="text-lg font-bold mt-1">Michael Chen</h3>
                    <p class="text-sm text-gray-500">Policy: LIFE-9983-X2C</p>
                </div>
                <div class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">
                    <i class="fas fa-check-circle"></i> Completed
                </div>
            </div>
            <div class="p-5 space-y-4">
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <span class="text-gray-500">Beneficiary:</span><br /><span class="font-medium">Emma
                            Chen</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Claim Type:</span><br /><span class="font-medium">Critical
                            Illness</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Diagnosis Date:</span><br /><span
                            class="font-medium">2024-02-10</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Hospital:</span><br /><span class="font-medium">St. Mary's
                            Medical</span>
                    </div>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg text-sm">
                    <p class="font-semibold mb-1">Medical summary:</p>
                    <p>
                        Diagnosed with covered critical illness. Medical reports and
                        physician statement attached.
                    </p>
                </div>
                <details>
                    <summary class="text-indigo-600 font-medium">
                        Additional details
                    </summary>
                    <div class="mt-3 pt-3 border-t text-sm">
                        <span class="text-gray-500">Medical authorization:</span><span class="ml-2">Signed</span>
                        <div class="text-xs text-gray-400 mt-2">
                            Form completed: 2024-02-18
                        </div>
                    </div>
                </details>
                <div class="flex flex-wrap gap-2">
                    <span class="text-xs bg-gray-100 px-2 py-1 rounded"><i class="fas fa-file-pdf text-red-500"></i>
                        medical_certificate.pdf</span><span class="text-xs bg-gray-100 px-2 py-1 rounded"><i
                            class="fas fa-file-image text-indigo-500"></i>
                        doctor_note_signed.jpg</span>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3 border-t border-gray-300 flex justify-between">
                <button class="text-indigo-600 text-sm">Preview form</button><button
                    class="text-gray-600 text-sm">Documents (2)</button>
            </div>
        </div>

        <!-- Form Card 4: Olivia Rodriguez - Travel -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden hover:shadow-lg transition">
            <div class="p-5 border-b border-gray-100 flex justify-between items-start">
                <div>
                    <span class="bg-cyan-100 text-cyan-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">Travel
                        Insurance</span><span class="text-xs text-gray-400 ml-2">Form ID: CLM-104</span>
                    <h3 class="text-lg font-bold mt-1">Olivia Rodriguez</h3>
                    <p class="text-sm text-gray-500">Policy: TRV-1256-4AA</p>
                </div>
                <div class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">
                    Completed
                </div>
            </div>
            <div class="p-5 space-y-4">
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <span class="text-gray-500">Destination:</span><br /><span class="font-medium">Barcelona,
                            Spain</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Loss Event:</span><br /><span class="font-medium">Flight
                            cancellation / Baggage delay</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Claim Amount:</span><br /><span
                            class="font-medium">$1,280.50</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Airline:</span><br /><span class="font-medium">Iberia
                            Airlines</span>
                    </div>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg text-sm">
                    <p class="font-semibold mb-1">Details:</p>
                    <p>
                        Flight canceled due to strike, baggage delayed by 72 hours.
                        Receipts for essential purchases attached.
                    </p>
                </div>
                <details>
                    <summary class="text-indigo-600 font-medium">
                        Extra info
                    </summary>
                    <div class="mt-3 pt-3 border-t text-sm">
                        <span class="text-gray-500">Compensation requested:</span><span
                            class="ml-2">$1,280.50</span>
                        <div class="text-xs text-gray-400 mt-2">
                            Submitted: 2024-08-20
                        </div>
                    </div>
                </details>
                <div class="flex flex-wrap gap-2">
                    <span class="text-xs bg-gray-100 px-2 py-1 rounded"><i class="fas fa-file-pdf"></i>
                        flight_cancellation.pdf</span><span class="text-xs bg-gray-100 px-2 py-1 rounded"><i
                            class="fas fa-receipt"></i>
                        baggage_claim_receipt.pdf</span><span class="text-xs bg-gray-100 px-2 py-1 rounded"><i
                            class="fas fa-image"></i> lost_luggage_tag.jpg</span>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3 border-t border-gray-300 flex justify-between">
                <button class="text-indigo-600 text-sm">Preview form</button><button
                    class="text-gray-600 text-sm">Documents (3)</button>
            </div>
        </div>

        <!-- Form Card 5: David Kim - Commercial -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden hover:shadow-lg transition">
            <div class="p-5 border-b border-gray-100 flex justify-between items-start">
                <div>
                    <span
                        class="bg-orange-100 text-orange-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">Commercial
                        Property</span><span class="text-xs text-gray-400 ml-2">Form ID: CLM-105</span>
                    <h3 class="text-lg font-bold mt-1">David Kim</h3>
                    <p class="text-sm text-gray-500">Policy: COM-7789-3BZ</p>
                </div>
                <div class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">
                    Completed
                </div>
            </div>
            <div class="p-5 space-y-4">
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <span class="text-gray-500">Damage Type:</span><br /><span class="font-medium">Storm / Hail
                            damage</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Incident Date:</span><br /><span
                            class="font-medium">2024-05-22</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Estimate:</span><br /><span class="font-medium">$12,700.00</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Property address:</span><br /><span class="font-medium">221B
                            Business Park</span>
                    </div>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg text-sm">
                    <p class="font-semibold mb-1">Description:</p>
                    <p>
                        Severe hail damage to roof and HVAC units on commercial
                        building. Inspection completed.
                    </p>
                </div>
                <details>
                    <summary class="text-indigo-600 font-medium">
                        Full form details
                    </summary>
                    <div class="mt-3 pt-3 border-t text-sm">
                        <span class="text-gray-500">Roofing contractor:</span><span class="ml-2">Elite Roofing
                            Solutions</span>
                        <div class="text-xs text-gray-400 mt-2">
                            Submitted: 2024-05-28
                        </div>
                    </div>
                </details>
                <div class="flex flex-wrap gap-2">
                    <span class="text-xs bg-gray-100 px-2 py-1 rounded"><i class="fas fa-file-pdf"></i>
                        roof_inspection_report.pdf</span><span class="text-xs bg-gray-100 px-2 py-1 rounded"><i
                            class="fas fa-image"></i> hail_damage_photo.png</span><span
                        class="text-xs bg-gray-100 px-2 py-1 rounded"><i class="fas fa-file-alt"></i>
                        contractor_quote.pdf</span>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3 border-t border-gray-300 flex justify-between">
                <button class="text-indigo-600 text-sm">Preview form</button><button
                    class="text-gray-600 text-sm">Documents (3)</button>
            </div>
        </div>

        <!-- Form Card 6: Additional placeholder for "Emily Watson" - Pet Insurance (just to show variety) -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden hover:shadow-lg transition">
            <div class="p-5 border-b border-gray-100 flex justify-between items-start">
                <div>
                    <span class="bg-pink-100 text-pink-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">Pet
                        Insurance</span><span class="text-xs text-gray-400 ml-2">Form ID: CLM-106</span>
                    <h3 class="text-lg font-bold mt-1">Emily Watson</h3>
                    <p class="text-sm text-gray-500">Policy: PET-3321-7GG</p>
                </div>
                <div class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">
                    Completed
                </div>
            </div>
            <div class="p-5 space-y-4">
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <span class="text-gray-500">Incident Date:</span><br /><span
                            class="font-medium">2024-10-10</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Pet Name:</span><br /><span class="font-medium">Luna (Dog)</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Claim Amount:</span><br /><span class="font-medium">$890.00</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Vet Clinic:</span><br /><span class="font-medium">Paws & Claws
                            Hospital</span>
                    </div>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg text-sm">
                    <p class="font-semibold mb-1">Diagnosis:</p>
                    <p>
                        Emergency visit due to ingestion of foreign object. Surgery
                        not required, medication prescribed.
                    </p>
                </div>
                <details>
                    <summary class="text-indigo-600 font-medium">
                        Vet details
                    </summary>
                    <div class="mt-3 pt-3 border-t text-sm">
                        <span class="text-gray-500">Vet report attached:</span><span class="ml-2">Yes</span>
                        <div class="text-xs text-gray-400 mt-2">
                            Form signed: 2024-10-12
                        </div>
                    </div>
                </details>
                <div class="flex flex-wrap gap-2">
                    <span class="text-xs bg-gray-100 px-2 py-1 rounded"><i class="fas fa-file-pdf"></i>
                        vet_report_luna.pdf</span><span class="text-xs bg-gray-100 px-2 py-1 rounded"><i
                            class="fas fa-receipt"></i> invoice_medication.pdf</span>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3 border-t border-gray-300 flex justify-between">
                <button class="text-indigo-600 text-sm">Preview form</button><button
                    class="text-gray-600 text-sm">Documents (2)</button>
            </div>
        </div>
    </div>

    <!-- Pagination UI (static) -->
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
</x-layouts.staff>
