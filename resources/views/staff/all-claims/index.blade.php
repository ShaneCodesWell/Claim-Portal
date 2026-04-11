<x-layouts.staff>
    <!-- Header with stats -->
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

    <!-- CLAIMS TABLE: all required fields -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto custom-scroll">
            <table class="min-w-[1000px] md:min-w-full w-full table-auto">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Client Name
                        </th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Policy Number
                        </th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Product
                        </th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Policy Start
                        </th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Policy End
                        </th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Completed Claim Form
                        </th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            All Attached Documents
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <!-- ROW 1 -->
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <div
                                    class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 text-sm font-semibold">
                                    JD
                                </div>
                                <span class="font-medium text-gray-800">John Davis</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 font-mono text-sm text-gray-700">
                            POL-AU-8723-01
                        </td>
                        <td class="px-5 py-4">
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"><i
                                    class="fas fa-car text-xs"></i> Auto Insurance</span>
                        </td>
                        <td class="px-5 py-4 text-sm">2024-01-15</td>
                        <td class="px-5 py-4 text-sm">2025-01-14</td>
                        <td class="px-5 py-4">
                            <details class="group bg-gray-50 rounded-lg p-2 border border-gray-200">
                                <summary class="text-indigo-600 text-sm font-medium">
                                    <i class="fas fa-file-alt mr-1"></i> View completed
                                    claim form
                                </summary>
                                <div class="mt-3 text-xs space-y-2 border-t pt-2 border-gray-200">
                                    <div class="grid grid-cols-2 gap-1">
                                        <span class="font-semibold">Incident Date:</span><span>2024-11-02</span><span
                                            class="font-semibold">Loss Type:</span><span>Collision</span><span
                                            class="font-semibold">Claim Amount:</span><span>$3,250.00</span><span
                                            class="font-semibold">Description:</span><span class="col-span-2">Rear-end
                                            collision, front bumper damage.</span>
                                    </div>
                                </div>
                            </details>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex flex-col gap-1.5">
                                <div class="flex items-center gap-2 text-xs bg-gray-50 rounded-md px-2 py-1 w-fit">
                                    <i class="fas fa-file-pdf text-red-500"></i><span>damage_estimate.pdf</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs bg-gray-50 rounded-md px-2 py-1 w-fit">
                                    <i class="fas fa-image text-blue-500"></i><span>car_damage_front.jpg</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <!-- ROW 2 -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <div
                                    class="h-8 w-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 text-sm font-semibold">
                                    SM
                                </div>
                                <span class="font-medium">Sarah Mitchell</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 font-mono text-sm">HOM-4562-89B</td>
                        <td class="px-5 py-4">
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-teal-100 text-teal-800"><i
                                    class="fas fa-home"></i> Home Insurance</span>
                        </td>
                        <td class="px-5 py-4 text-sm">2023-09-10</td>
                        <td class="px-5 py-4 text-sm">2024-09-09</td>
                        <td class="px-5 py-4">
                            <details class="bg-gray-50 rounded-lg p-2 border border-gray-200">
                                <summary class="text-indigo-600 text-sm font-medium">
                                    <i class="fas fa-file-alt"></i> View completed claim
                                    form
                                </summary>
                                <div class="mt-3 text-xs space-y-2 border-t pt-2 border-gray-200">
                                    <div class="grid grid-cols-2 gap-1">
                                        <span class="font-semibold">Incident Date:</span><span>2024-07-19</span><span
                                            class="font-semibold">Loss Type:</span><span>Water damage</span><span
                                            class="font-semibold">Claim Amount:</span><span>$7,430.00</span><span
                                            class="font-semibold">Description:</span><span>Basement flooding due to
                                            burst
                                            pipe.</span>
                                    </div>
                                </div>
                            </details>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex flex-col gap-1.5">
                                <div class="flex items-center gap-2 text-xs bg-gray-50 rounded-md px-2 py-1">
                                    <i class="fas fa-file-pdf text-red-500"></i><span>plumber_invoice.pdf</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs bg-gray-50 rounded-md px-2 py-1">
                                    <i class="fas fa-image text-blue-500"></i><span>water_damage_photo1.jpg</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <!-- ROW 3 -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <div
                                    class="h-8 w-8 rounded-full bg-amber-100 flex items-center justify-center text-amber-700 text-sm font-semibold">
                                    MC
                                </div>
                                <span class="font-medium">Michael Chen</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 font-mono text-sm">LIFE-9983-X2C</td>
                        <td class="px-5 py-4">
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800"><i
                                    class="fas fa-heartbeat"></i> Life Insurance</span>
                        </td>
                        <td class="px-5 py-4 text-sm">2020-05-20</td>
                        <td class="px-5 py-4 text-sm">2035-05-19</td>
                        <td class="px-5 py-4">
                            <details class="bg-gray-50 rounded-lg p-2 border border-gray-200">
                                <summary class="text-indigo-600 text-sm font-medium">
                                    <i class="fas fa-file-alt"></i> View completed claim
                                    form
                                </summary>
                                <div class="mt-3 text-xs space-y-2 border-t pt-2 border-gray-200">
                                    <div class="grid grid-cols-2 gap-1">
                                        <span class="font-semibold">Beneficiary:</span><span>Emma Chen</span><span
                                            class="font-semibold">Claim Type:</span><span>Critical Illness</span><span
                                            class="font-semibold">Diagnosis Date:</span><span>2024-02-10</span>
                                    </div>
                                </div>
                            </details>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex flex-col gap-1.5">
                                <div class="flex items-center gap-2 text-xs bg-gray-50 rounded-md px-2 py-1">
                                    <i class="fas fa-file-pdf text-red-500"></i><span>medical_certificate.pdf</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs bg-gray-50 rounded-md px-2 py-1">
                                    <i class="fas fa-file-image text-indigo-500"></i><span>doctor_note.jpg</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <!-- ROW 4 -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <div
                                    class="h-8 w-8 rounded-full bg-rose-100 flex items-center justify-center text-rose-700 text-sm font-semibold">
                                    OR
                                </div>
                                <span class="font-medium">Olivia Rodriguez</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 font-mono text-sm">TRV-1256-4AA</td>
                        <td class="px-5 py-4">
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800"><i
                                    class="fas fa-plane"></i> Travel Insurance</span>
                        </td>
                        <td class="px-5 py-4 text-sm">2024-03-01</td>
                        <td class="px-5 py-4 text-sm">2024-09-01</td>
                        <td class="px-5 py-4">
                            <details class="bg-gray-50 rounded-lg p-2 border border-gray-200">
                                <summary class="text-indigo-600 text-sm font-medium">
                                    <i class="fas fa-file-alt"></i> View completed claim
                                    form
                                </summary>
                                <div class="mt-3 text-xs space-y-2 border-t pt-2 border-gray-200">
                                    <div class="grid grid-cols-2 gap-1">
                                        <span class="font-semibold">Trip Destination:</span><span>Barcelona</span><span
                                            class="font-semibold">Loss Event:</span><span>Flight
                                            cancellation</span><span class="font-semibold">Claim
                                            Amount:</span><span>$1,280.50</span>
                                    </div>
                                </div>
                            </details>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex flex-col gap-1.5">
                                <div class="flex items-center gap-2 text-xs bg-gray-50 rounded-md px-2 py-1">
                                    <i class="fas fa-file-pdf text-red-500"></i><span>flight_cancellation.pdf</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs bg-gray-50 rounded-md px-2 py-1">
                                    <i class="fas fa-receipt"></i><span>baggage_claim.pdf</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <!-- ROW 5 -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <div
                                    class="h-8 w-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-700 text-sm font-semibold">
                                    DK
                                </div>
                                <span class="font-medium">David Kim</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 font-mono text-sm">COM-7789-3BZ</td>
                        <td class="px-5 py-4">
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800"><i
                                    class="fas fa-building"></i> Commercial Property</span>
                        </td>
                        <td class="px-5 py-4 text-sm">2022-11-01</td>
                        <td class="px-5 py-4 text-sm">2024-11-01</td>
                        <td class="px-5 py-4">
                            <details class="bg-gray-50 rounded-lg p-2 border border-gray-200">
                                <summary class="text-indigo-600 text-sm font-medium">
                                    <i class="fas fa-file-alt"></i> View completed claim
                                    form
                                </summary>
                                <div class="mt-3 text-xs space-y-2 border-t pt-2 border-gray-200">
                                    <div class="grid grid-cols-2 gap-1">
                                        <span class="font-semibold">Damage Type:</span><span>Storm / Hail</span><span
                                            class="font-semibold">Estimate:</span><span>$12,700</span><span
                                            class="font-semibold">Incident Date:</span><span>2024-05-22</span>
                                    </div>
                                </div>
                            </details>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex flex-col gap-1.5">
                                <div class="flex items-center gap-2 text-xs bg-gray-50 rounded-md px-2 py-1">
                                    <i class="fas fa-file-pdf text-red-500"></i><span>roof_inspection.pdf</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs bg-gray-50 rounded-md px-2 py-1">
                                    <i class="fas fa-file-image text-blue-500"></i><span>hail_damage.png</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200 flex justify-between items-center flex-wrap gap-3">
            <div class="text-sm text-gray-500">
                <i class="fas fa-clipboard-list mr-1"></i> Showing 5 of 12
                registered claims
            </div>
            <div class="flex gap-2">
                <button class="px-3 py-1 border border-gray-200 rounded-md text-sm bg-white">
                    <i class="fas fa-chevron-left"></i> Previous</button><button
                    class="px-3 py-1 bg-indigo-600 text-white rounded-md text-sm">
                    1</button><button class="px-3 py-1 border border-gray-200 rounded-md text-sm bg-white">
                    2</button><button class="px-3 py-1 border border-gray-200 rounded-md text-sm bg-white">
                    Next <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
    <div
        class="mt-6 bg-indigo-50/40 rounded-xl border border-indigo-100 p-4 flex flex-wrap justify-between items-center gap-3">
        <div class="flex items-center gap-3 text-sm text-indigo-800">
            <i class="fas fa-info-circle text-indigo-500 text-lg"></i><span><strong>Claims team overview:</strong> Each
                row
                includes
                completed claim form (expandable) and all uploaded
                documents.</span>
        </div>
        <div class="flex gap-2">
            <span class="bg-white px-3 py-1 rounded-full text-xs shadow-sm"><i class="far fa-file-alt"></i> Form
                preview</span><span class="bg-white px-3 py-1 rounded-full text-xs shadow-sm"><i
                    class="far fa-folder-open"></i> Document access</span>
        </div>
    </div>
</x-layouts.staff>
