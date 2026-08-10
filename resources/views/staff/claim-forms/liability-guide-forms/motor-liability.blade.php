<x-layouts.staff>

    <div class="flex justify-center px-4 py-8">
        <div class="w-full max-w-4xl">

            {{-- Back button — outside the form --}}
            <div class="p-2 mb-4">
                <button type="button" onclick="window.history.back()"
                    class="bg-white border border-gray-300 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium text-gray-700 transition shadow-sm flex items-center gap-2">
                    <i class="fas fa-arrow-left text-sm"></i>
                    Go Back
                </button>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">

                {{-- ===== HEADER ===== --}}
                <div class="overflow-hidden">
                    {{-- Main header --}}
                    <div class="px-8 pt-6 pb-0 bg-white">
                        <div class="grid grid-cols-[160px_1fr_auto] items-start gap-6">
                            {{-- Logo --}}
                            <div class="pt-1">
                                <img src="{{ asset('images/Vanguard.png') }}" alt="Vanguard Assurance Logo"
                                    class="w-36 h-12 object-contain" />
                            </div>
                            {{-- Company name --}}
                            <div class="text-center pt-1">
                                <p
                                    class="text-[15px] font-bold text-gray-800 tracking-wide mb-2 border-b border-b-gray-200 pb-2">
                                    Vanguard Assurance Company Ltd
                                </p>
                                <p class="text-[10px] text-gray-400 mt-0.5 tracking-widest uppercase">
                                    We always stand by you
                                </p>
                            </div>
                            {{-- Contact info --}}
                            <div class="text-right text-[11px] text-gray-500 leading-relaxed pt-1">
                                <p>vacmmails@vanguardassurance.com</p>
                                <p>claimsdepartment@vanguardassurance.com</p>
                                <p>030 266 6485 / 6486 / 6487</p>
                                <p>P.O. Box 1868, Accra</p>
                            </div>
                        </div>
                        <div class="border-t border-gray-100 mt-5"></div>
                    </div>

                    {{-- Document title band --}}
                    <div class="bg-[#0b529d] px-8 py-2.5 flex items-center justify-center gap-4">
                        <div class="flex-1 border-t border-white/20"></div>
                        <p class="text-[13px] font-bold tracking-widest uppercase text-white whitespace-nowrap">
                            Motor Claims Liability Guide
                        </p>
                        <div class="flex-1 border-t border-white/20"></div>
                    </div>

                    {{-- Subtitle --}}
                    <div class="bg-gray-50 border-b border-gray-100 px-8 py-2 text-center">
                        <p class="text-[11.5px] text-gray-500">
                            For internal use in assessing motor claims liability. Please complete all sections
                            accurately.
                        </p>
                    </div>
                </div>

                {{-- Note box --}}
                <div class="py-6 px-8 md:px-12">
                    {{-- <div class="bg-amber-50 border-l-4 border-amber-300 p-4 mb-8 rounded-lg">
                        <p class="text-xs text-gray-600 leading-relaxed">
                            Please note, it is necessary that great care should be taken in completing this guide and
                            the information given therein should be strictly accurate, whether it is in the insured's
                            favor or otherwise. Do not make any payment, offer or promise of any payment, or admit
                            liability in any way, as doing so may prejudice the company's position and make
                            settlement of the claim difficult.
                        </p>
                    </div> --}}

                    <form id="liabilityGuideForm" method="POST" action="#">

                        {{-- ===== CLAIM INFORMATION ===== --}}
                        <section class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-100 pb-2">
                                Claim Information
                            </h3>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                                <div>
                                    <label for="branch_dept" class="block text-xs font-medium text-gray-500 mb-1">Branch
                                        / Dept.</label>
                                    <input type="text" id="branch_dept" name="branch_dept"
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b529d]/20 focus:border-[#0b529d] transition-colors">
                                </div>
                                <div>
                                    <label for="form_date"
                                        class="block text-xs font-medium text-gray-500 mb-1">Date</label>
                                    <input type="date" id="form_date" name="form_date"
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b529d]/20 focus:border-[#0b529d] transition-colors">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label for="claim_number" class="block text-xs font-medium text-gray-500 mb-1">Claim
                                        Number</label>
                                    <input type="text" id="claim_number" name="claim_number"
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b529d]/20 focus:border-[#0b529d] transition-colors">
                                </div>
                                <div>
                                    <label for="vehicle_number"
                                        class="block text-xs font-medium text-gray-500 mb-1">Vehicle Number</label>
                                    <input type="text" id="vehicle_number" name="vehicle_number"
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b529d]/20 focus:border-[#0b529d] transition-colors">
                                </div>
                            </div>
                        </section>

                        {{-- ===== REFERENCES ===== --}}
                        <section class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-100 pb-2">
                                References
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-3">
                                @foreach ([
        'P/R' => 'Police Report (P/R)',
        'ARF' => 'Accident Report Form (ARF)',
        'IR' => 'Investigation Report (IR)',
        'S/R' => "Surveyor's Report (S/R)",
        'D/L' => "Driver's License Name (D/L)",
    ] as $value => $label)
                                    <label
                                        class="inline-flex items-center gap-2.5 text-sm text-gray-700 cursor-pointer">
                                        <input type="checkbox" name="references[]" value="{{ $value }}"
                                            class="w-4 h-4 rounded border-gray-300 text-[#0b529d] focus:ring-[#0b529d]/30">
                                        {{ $label }}
                                    </label>
                                @endforeach
                            </div>
                        </section>

                        {{-- ===== CLAIM DETAILS ===== --}}
                        <section class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-100 pb-2">
                                Claim Details
                            </h3>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                                <div>
                                    <label for="insured"
                                        class="block text-xs font-medium text-gray-500 mb-1">Insured</label>
                                    <input type="text" id="insured" name="insured"
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b529d]/20 focus:border-[#0b529d] transition-colors">
                                </div>
                                <div>
                                    <label for="insured_name_arf"
                                        class="block text-xs font-medium text-gray-500 mb-1">Insured's Name on
                                        A.R.F</label>
                                    <input type="text" id="insured_name_arf" name="insured_name_arf"
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b529d]/20 focus:border-[#0b529d] transition-colors">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                                <div>
                                    <label for="vehicle_owner_name_pr"
                                        class="block text-xs font-medium text-gray-500 mb-1">Vehicle Owner's Name on
                                        (P/R)</label>
                                    <input type="text" id="vehicle_owner_name_pr" name="vehicle_owner_name_pr"
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b529d]/20 focus:border-[#0b529d] transition-colors">
                                </div>
                                <div>
                                    <label for="driver_name_arf"
                                        class="block text-xs font-medium text-gray-500 mb-1">Driver's Name on
                                        A.R.F.</label>
                                    <input type="text" id="driver_name_arf" name="driver_name_arf"
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b529d]/20 focus:border-[#0b529d] transition-colors">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                                <div>
                                    <label for="driver_name_pr"
                                        class="block text-xs font-medium text-gray-500 mb-1">Driver's Name on
                                        P/R</label>
                                    <input type="text" id="driver_name_pr" name="driver_name_pr"
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b529d]/20 focus:border-[#0b529d] transition-colors">
                                </div>
                                <div>
                                    <label for="license_first_issued"
                                        class="block text-xs font-medium text-gray-500 mb-1">Date License was First
                                        Issued</label>
                                    <input type="date" id="license_first_issued" name="license_first_issued"
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b529d]/20 focus:border-[#0b529d] transition-colors">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                                <div>
                                    <label for="driver_age"
                                        class="block text-xs font-medium text-gray-500 mb-1">Driver's Age</label>
                                    <input type="number" min="0" max="120" id="driver_age"
                                        name="driver_age"
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b529d]/20 focus:border-[#0b529d] transition-colors">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Period of
                                        Insurance</label>
                                    <div class="flex items-center gap-2">
                                        <input type="date" name="period_of_insurance_from"
                                            aria-label="Period of insurance from"
                                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b529d]/20 focus:border-[#0b529d] transition-colors">
                                        <span class="text-xs text-gray-400">to</span>
                                        <input type="date" name="period_of_insurance_to"
                                            aria-label="Period of insurance to"
                                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b529d]/20 focus:border-[#0b529d] transition-colors">
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                                <div>
                                    <label for="date_of_accident"
                                        class="block text-xs font-medium text-gray-500 mb-1">Date of Accident</label>
                                    <input type="date" id="date_of_accident" name="date_of_accident"
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b529d]/20 focus:border-[#0b529d] transition-colors">
                                </div>
                                <div>
                                    <label for="use_clause" class="block text-xs font-medium text-gray-500 mb-1">Use
                                        Clause</label>
                                    <input type="text" id="use_clause" name="use_clause"
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b529d]/20 focus:border-[#0b529d] transition-colors">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                                <div>
                                    <label for="premium_payable"
                                        class="block text-xs font-medium text-gray-500 mb-1">Premium Payable</label>
                                    <input type="number" step="0.01" min="0" id="premium_payable"
                                        name="premium_payable"
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b529d]/20 focus:border-[#0b529d] transition-colors">
                                </div>
                                <div>
                                    <label for="premium_paid"
                                        class="block text-xs font-medium text-gray-500 mb-1">Premium Paid</label>
                                    <input type="number" step="0.01" min="0" id="premium_paid"
                                        name="premium_paid"
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b529d]/20 focus:border-[#0b529d] transition-colors">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label for="cover" class="block text-xs font-medium text-gray-500 mb-1">
                                        Cover
                                        <span
                                            class="block font-normal italic text-gray-400 text-[10.5px] mt-0.5">Read-only
                                            — depends on Third Party / Third Party Fire &amp; Theft logic
                                            (pending)</span>
                                    </label>
                                    <input type="text" id="cover" name="cover" readonly
                                        class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-400 cursor-not-allowed">
                                </div>
                            </div>
                        </section>

                        {{-- ===== NARRATIVE ===== --}}
                        <section class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-100 pb-2">
                                Assessment
                            </h3>

                            <div class="mb-5">
                                <label for="brief_facts" class="block text-xs font-medium text-gray-500 mb-1">Brief
                                    Facts of the Accident</label>
                                <textarea id="brief_facts" name="brief_facts" rows="4"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b529d]/20 focus:border-[#0b529d] transition-colors"></textarea>
                            </div>

                            <div class="mb-5">
                                <label for="recommendation"
                                    class="block text-xs font-medium text-gray-500 mb-1">Recommendation on
                                    Liability</label>
                                <textarea id="recommendation" name="recommendation" rows="4"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b529d]/20 focus:border-[#0b529d] transition-colors"></textarea>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label for="staff_signature"
                                        class="block text-xs font-medium text-gray-500 mb-1">Staff's Signature /
                                        Username</label>
                                    <input type="text" id="staff_signature" name="staff_signature"
                                        style="font-family: 'Brush Script MT', cursive;"
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b529d]/20 focus:border-[#0b529d] transition-colors">
                                </div>
                            </div>
                        </section>

                        {{-- ===== MANAGER / SUPERVISOR COMMENTS ===== --}}
                        <section class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-800 mb-1 border-b border-gray-100 pb-2">
                                Manager's / Supervisor's Comment
                            </h3>
                            <p class="text-xs text-gray-400 mb-4">To be completed by the relevant unit.</p>

                            <div class="space-y-4">
                                @foreach ([
        'claims_unit' => 'Claims Unit',
        'survey' => 'Survey',
        'supervisor' => 'Supervisor',
        'survey_team' => 'Survey Team',
    ] as $key => $label)
                                    <div class="grid grid-cols-1 sm:grid-cols-[130px_1fr_180px] gap-3 items-start">
                                        <div class="text-xs font-medium text-gray-500 pt-2.5">{{ $label }}</div>
                                        <textarea name="comments[{{ $key }}][text]" rows="2" placeholder="Comment from {{ $label }}"
                                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b529d]/20 focus:border-[#0b529d] transition-colors"></textarea>
                                        <input type="text" name="comments[{{ $key }}][signature]"
                                            placeholder="Signature / Username"
                                            style="font-family: 'Brush Script MT', cursive;"
                                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b529d]/20 focus:border-[#0b529d] transition-colors">
                                    </div>
                                @endforeach
                            </div>
                        </section>

                        {{-- ===== REINSURANCE ===== --}}
                        <section class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-100 pb-2">
                                Reinsurance
                            </h3>

                            <div class="mb-5">
                                <label class="block text-xs font-medium text-gray-500 mb-2">Would there be a
                                    reinsurance recovery?</label>
                                <div class="flex gap-6">
                                    <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                        <input type="radio" name="reinsurance_recovery" value="Y"
                                            data-toggle="reinsurance-type-block"
                                            class="w-4 h-4 border-gray-300 text-[#0b529d] focus:ring-[#0b529d]/30">
                                        Yes
                                    </label>
                                    <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                        <input type="radio" name="reinsurance_recovery" value="N"
                                            data-toggle="reinsurance-type-block" checked
                                            class="w-4 h-4 border-gray-300 text-[#0b529d] focus:ring-[#0b529d]/30">
                                        No
                                    </label>
                                </div>
                            </div>

                            <div id="reinsurance-type-block"
                                class="bg-gray-50 border border-gray-100 rounded-lg p-4 mb-5" hidden>
                                <label class="block text-xs font-medium text-gray-500 mb-2">Type</label>
                                <div class="flex gap-6">
                                    <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                        <input type="checkbox" name="reinsurance_type[]" value="Treaty"
                                            id="reinsurance_treaty"
                                            class="w-4 h-4 rounded border-gray-300 text-[#0b529d] focus:ring-[#0b529d]/30">
                                        Treaty
                                    </label>
                                    <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                        <input type="checkbox" name="reinsurance_type[]" value="Facultative"
                                            id="reinsurance_facultative"
                                            class="w-4 h-4 rounded border-gray-300 text-[#0b529d] focus:ring-[#0b529d]/30">
                                        Facultative
                                    </label>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-2">Has the Reinsurance
                                        Department been notified?</label>
                                    <div class="flex gap-6">
                                        <label
                                            class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                            <input type="radio" name="reinsurance_notified" value="Y"
                                                data-toggle="reinsurance-date-block"
                                                class="w-4 h-4 border-gray-300 text-[#0b529d] focus:ring-[#0b529d]/30">
                                            Yes
                                        </label>
                                        <label
                                            class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                            <input type="radio" name="reinsurance_notified" value="N"
                                                data-toggle="reinsurance-date-block" checked
                                                class="w-4 h-4 border-gray-300 text-[#0b529d] focus:ring-[#0b529d]/30">
                                            No
                                        </label>
                                    </div>
                                </div>
                                <div id="reinsurance-date-block" hidden>
                                    <label for="notification_date"
                                        class="block text-xs font-medium text-gray-500 mb-1">Date of
                                        Notification</label>
                                    <input type="date" id="notification_date" name="notification_date"
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b529d]/20 focus:border-[#0b529d] transition-colors">
                                </div>
                            </div>
                        </section>

                        {{-- ===== FACULTATIVE REINSURANCE DETAILS ===== --}}
                        <section class="mb-8" id="facultative-details-section">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-100 pb-2">
                                Details of Facultative Reinsurance
                            </h3>
                            <div class="space-y-3">
                                <div
                                    class="grid grid-cols-[2fr_1fr] gap-3 text-[11px] font-semibold uppercase tracking-wide text-gray-400">
                                    <span>Company</span>
                                    <span>Percentage</span>
                                </div>
                                @for ($i = 1; $i <= 2; $i++)
                                    <div class="grid grid-cols-[2fr_1fr] gap-3">
                                        <input type="text" name="facultative[{{ $i }}][company]"
                                            placeholder="Company {{ $i }}"
                                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b529d]/20 focus:border-[#0b529d] transition-colors">
                                        <div class="flex items-center gap-2">
                                            <input type="number" min="0" max="100" step="0.01"
                                                name="facultative[{{ $i }}][percentage]" placeholder="0.00"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b529d]/20 focus:border-[#0b529d] transition-colors">
                                            <span class="text-sm text-gray-400">%</span>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </section>

                        {{-- ===== MANAGER'S DECISION ===== --}}
                        <section class="mb-2">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-100 pb-2">
                                Manager's Decision
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label for="manager_decision"
                                        class="block text-xs font-medium text-gray-500 mb-1">Decision</label>
                                    <input type="text" id="manager_decision" name="manager_decision"
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b529d]/20 focus:border-[#0b529d] transition-colors">
                                </div>
                                <div>
                                    <label for="manager_signature"
                                        class="block text-xs font-medium text-gray-500 mb-1">Signature</label>
                                    <input type="text" id="manager_signature" name="manager_signature"
                                        style="font-family: 'Brush Script MT', cursive;"
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b529d]/20 focus:border-[#0b529d] transition-colors">
                                </div>
                            </div>
                        </section>

                        {{-- ===== ACTIONS ===== --}}
                        <div class="flex justify-end gap-3 mt-10 pt-6 border-t border-gray-100">
                            <button type="reset"
                                class="rounded-lg border border-gray-200 bg-white px-5 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                                Clear Form
                            </button>
                            <button type="submit"
                                class="rounded-lg bg-[#0b529d] px-5 py-2 text-sm font-medium text-white shadow-sm hover:bg-[#094580] transition-colors">
                                Save Liability Guide
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const typeBlock = document.getElementById('reinsurance-type-block');
            const facSection = document.getElementById('facultative-details-section');
            const facCheckbox = document.getElementById('reinsurance_facultative');
            const dateBlock = document.getElementById('reinsurance-date-block');

            function updateTypeBlock() {
                const isYes = document.querySelector('input[name="reinsurance_recovery"]:checked')?.value === 'Y';
                typeBlock.hidden = !isYes;
                if (!isYes) {
                    document.querySelectorAll('input[name="reinsurance_type[]"]').forEach(cb => cb.checked = false);
                    updateFacultativeSection();
                }
            }

            function updateFacultativeSection() {
                facSection.style.display = facCheckbox.checked ? '' : 'none';
            }

            function updateDateBlock() {
                const isYes = document.querySelector('input[name="reinsurance_notified"]:checked')?.value === 'Y';
                dateBlock.hidden = !isYes;
                if (!isYes) document.getElementById('notification_date').value = '';
            }

            document.querySelectorAll('input[name="reinsurance_recovery"]').forEach(r => r.addEventListener('change',
                updateTypeBlock));
            document.querySelectorAll('input[name="reinsurance_notified"]').forEach(r => r.addEventListener('change',
                updateDateBlock));
            facCheckbox.addEventListener('change', updateFacultativeSection);

            updateTypeBlock();
            updateFacultativeSection();
            updateDateBlock();
        })();
    </script>

</x-layouts.staff>
