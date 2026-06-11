<div id="policyModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm items-center justify-center p-4 z-50"
    data-policy-id="" style="display: none;">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        {{-- Modal Header --}}
        <div
            class="px-6 py-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white z-10 shrink-0">
            <div class="flex items-center gap-3">
                {{-- Logo --}}
                <div class="flex">
                    <img src="{{ asset('images/Vanguard.png') }}" alt="Logo" class="h-8">
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Policy Details</h3>
                    <span class="text-sm text-gray-500" id="modal-policy-number"></span>
                </div>
            </div>
            <button onclick="closeModal()"
                class="text-gray-400 hover:text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg p-2 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        {{-- Modal Body --}}
        <div class="p-6 overflow-y-auto flex-1">

            {{-- Policy + Coverage Hero --}}
            <div class="bg-gray-50 rounded-xl border border-gray-200 p-4 mb-6">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-sm font-semibold text-gray-900" id="modal-product"></p>
                        <p class="text-xs text-gray-500 mt-0.5" id="modal-business-class"></p>
                    </div>
                    <span id="modal-status" class="text-xs font-semibold px-2.5 py-1 rounded-full"></span>
                </div>
                <div class="grid grid-cols-3 gap-px bg-gray-200 rounded-lg overflow-hidden border border-gray-200">
                    <div class="bg-white px-4 py-3">
                        <p class="text-xs text-gray-500 mb-1">Start Date</p>
                        <p class="text-sm font-semibold text-gray-900" id="modal-start-date"></p>
                    </div>
                    <div class="bg-white px-4 py-3">
                        <p class="text-xs text-gray-500 mb-1">End Date</p>
                        <p class="text-sm font-semibold text-gray-900" id="modal-end-date"></p>
                    </div>
                    <div class="bg-white px-4 py-3">
                        <p class="text-xs text-gray-500 mb-1">Renewal Date</p>
                        <p class="text-sm font-semibold text-gray-900" id="modal-renewal-date"></p>
                    </div>
                </div>
            </div>

            {{-- Risks Section --}}
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center gap-2">
                        Insured Risks
                        <span id="modal-risk-count"
                            class="bg-blue-50 text-blue-700 text-xs font-semibold px-2 py-0.5 rounded-full normal-case tracking-normal">
                            3
                        </span>
                    </h4>
                </div>

                {{-- Risk Search --}}
                <div class="relative mb-3">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="fas fa-search text-gray-400 text-sm"></i>
                    </div>
                    <input type="text" id="modal-risk-search" placeholder="Search risks..."
                        oninput="filterRisks(this.value)"
                        class="w-full pl-9 pr-4 py-2 text-sm bg-white border border-gray-200 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                {{-- No results message --}}
                <div id="modal-risk-empty" class="hidden text-center py-8 text-sm text-gray-400">
                    No risks match your search.
                </div>

                {{-- Risk Accordion --}}
                <div id="modal-risks-list" class="space-y-2">

                    {{-- Risk 1 --}}
                    <div class="risk-card border border-gray-200 rounded-xl overflow-hidden"
                        data-risk-search="toyota land cruiser gr-4821-24 motor vehicle comprehensive">
                        <button type="button" onclick="toggleRisk(this)"
                            class="w-full flex items-center gap-3 px-4 py-3 bg-white hover:bg-gray-50 transition-colors text-left">
                            <div class="shrink-0 w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                                <i class="fas fa-car text-blue-600 text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">Toyota Land Cruiser — GR-4821-24
                                </p>
                                <p class="text-xs text-gray-500">Motor Vehicle · Comprehensive</p>
                            </div>
                            <i
                                class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-200 risk-chevron"></i>
                        </button>
                        <div class="risk-body hidden border-t border-gray-100 bg-gray-50 px-4 py-4">
                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div>
                                    <p class="text-xs text-gray-500 mb-0.5">Make &amp; Model</p>
                                    <p class="text-sm font-semibold text-gray-900">Toyota Land Cruiser 200</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-0.5">Year</p>
                                    <p class="text-sm font-semibold text-gray-900">2022</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-0.5">Chassis No.</p>
                                    <p class="text-sm font-semibold text-gray-900">JTMHV05J204123456</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-0.5">Sum Insured</p>
                                    <p class="text-sm font-semibold text-gray-900">GHS 420,000</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-0.5">Excess</p>
                                    <p class="text-sm font-semibold text-gray-900">GHS 2,000</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-0.5">Premium</p>
                                    <p class="text-sm font-semibold text-gray-900">GHS 8,400</p>
                                </div>
                            </div>
                            <div class="border-t border-gray-200 pt-3">
                                <p class="text-xs text-gray-500 mb-2">Covers Included</p>
                                <div class="flex flex-wrap gap-1.5">
                                    <span
                                        class="text-xs px-2 py-1 bg-white border border-gray-200 rounded-md text-gray-600">Own
                                        Damage</span>
                                    <span
                                        class="text-xs px-2 py-1 bg-white border border-gray-200 rounded-md text-gray-600">Third
                                        Party Liability</span>
                                    <span
                                        class="text-xs px-2 py-1 bg-white border border-gray-200 rounded-md text-gray-600">Theft</span>
                                    <span
                                        class="text-xs px-2 py-1 bg-white border border-gray-200 rounded-md text-gray-600">Fire</span>
                                    <span
                                        class="text-xs px-2 py-1 bg-white border border-gray-200 rounded-md text-gray-600">Personal
                                        Accident</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Risk 2 --}}
                    <div class="risk-card border border-gray-200 rounded-xl overflow-hidden"
                        data-risk-search="hyundai tucson gw-2210-24 motor vehicle third party fire theft">
                        <button type="button" onclick="toggleRisk(this)"
                            class="w-full flex items-center gap-3 px-4 py-3 bg-white hover:bg-gray-50 transition-colors text-left">
                            <div class="shrink-0 w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                                <i class="fas fa-car text-blue-600 text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">Hyundai Tucson — GW-2210-24</p>
                                <p class="text-xs text-gray-500">Motor Vehicle · Third Party Fire &amp; Theft</p>
                            </div>
                            <i
                                class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-200 risk-chevron"></i>
                        </button>
                        <div class="risk-body hidden border-t border-gray-100 bg-gray-50 px-4 py-4">
                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div>
                                    <p class="text-xs text-gray-500 mb-0.5">Make &amp; Model</p>
                                    <p class="text-sm font-semibold text-gray-900">Hyundai Tucson 2.0</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-0.5">Year</p>
                                    <p class="text-sm font-semibold text-gray-900">2020</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-0.5">Chassis No.</p>
                                    <p class="text-sm font-semibold text-gray-900">KMHJX81AABU654321</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-0.5">Sum Insured</p>
                                    <p class="text-sm font-semibold text-gray-900">GHS 180,000</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-0.5">Excess</p>
                                    <p class="text-sm font-semibold text-gray-900">GHS 1,500</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-0.5">Premium</p>
                                    <p class="text-sm font-semibold text-gray-900">GHS 3,600</p>
                                </div>
                            </div>
                            <div class="border-t border-gray-200 pt-3">
                                <p class="text-xs text-gray-500 mb-2">Covers Included</p>
                                <div class="flex flex-wrap gap-1.5">
                                    <span
                                        class="text-xs px-2 py-1 bg-white border border-gray-200 rounded-md text-gray-600">Third
                                        Party Liability</span>
                                    <span
                                        class="text-xs px-2 py-1 bg-white border border-gray-200 rounded-md text-gray-600">Fire</span>
                                    <span
                                        class="text-xs px-2 py-1 bg-white border border-gray-200 rounded-md text-gray-600">Theft</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Risk 3 --}}
                    <div class="risk-card border border-gray-200 rounded-xl overflow-hidden"
                        data-risk-search="14 industrial road tema commercial property all risks warehouse office">
                        <button type="button" onclick="toggleRisk(this)"
                            class="w-full flex items-center gap-3 px-4 py-3 bg-white hover:bg-gray-50 transition-colors text-left">
                            <div class="shrink-0 w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center">
                                <i class="fas fa-building text-purple-600 text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">No. 14 Industrial Rd, Tema</p>
                                <p class="text-xs text-gray-500">Commercial Property · All Risks</p>
                            </div>
                            <i
                                class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-200 risk-chevron"></i>
                        </button>
                        <div class="risk-body hidden border-t border-gray-100 bg-gray-50 px-4 py-4">
                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div>
                                    <p class="text-xs text-gray-500 mb-0.5">Property Type</p>
                                    <p class="text-sm font-semibold text-gray-900">Warehouse / Office</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-0.5">Construction</p>
                                    <p class="text-sm font-semibold text-gray-900">Concrete Block</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-0.5">Location</p>
                                    <p class="text-sm font-semibold text-gray-900">Tema Industrial Area</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-0.5">Sum Insured</p>
                                    <p class="text-sm font-semibold text-gray-900">GHS 2,500,000</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-0.5">Excess</p>
                                    <p class="text-sm font-semibold text-gray-900">GHS 5,000</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-0.5">Premium</p>
                                    <p class="text-sm font-semibold text-gray-900">GHS 12,500</p>
                                </div>
                            </div>
                            <div class="border-t border-gray-200 pt-3">
                                <p class="text-xs text-gray-500 mb-2">Covers Included</p>
                                <div class="flex flex-wrap gap-1.5">
                                    <span
                                        class="text-xs px-2 py-1 bg-white border border-gray-200 rounded-md text-gray-600">Fire
                                        &amp; Perils</span>
                                    <span
                                        class="text-xs px-2 py-1 bg-white border border-gray-200 rounded-md text-gray-600">Burglary</span>
                                    <span
                                        class="text-xs px-2 py-1 bg-white border border-gray-200 rounded-md text-gray-600">Flood</span>
                                    <span
                                        class="text-xs px-2 py-1 bg-white border border-gray-200 rounded-md text-gray-600">Malicious
                                        Damage</span>
                                    <span
                                        class="text-xs px-2 py-1 bg-white border border-gray-200 rounded-md text-gray-600">Public
                                        Liability</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        {{-- Modal Footer --}}
        <div
            class="px-6 py-4 bg-gray-50 rounded-b-xl border-t border-gray-100 flex justify-end gap-3 sticky bottom-0 shrink-0">
            <button onclick="closeModal()"
                class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 font-medium rounded-lg hover:bg-gray-200 transition-colors">
                Close
            </button>
            <button id="modal-file-claim-btn"
                class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2 shadow-sm hover:shadow">
                <i class="fas fa-file-invoice"></i>
                File Claim
            </button>
        </div>
    </div>
</div>
