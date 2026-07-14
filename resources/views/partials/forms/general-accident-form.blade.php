@php
    $f = $formData ?? [];
    $isStaff = ($context ?? 'customer') === 'staff';
    $isEdit = !is_null($claim ?? null);
    $policyId = $policyId ?? ($policy->external_policy_id ?? '');
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <!-- Header with logo and company details (matching motor/fire forms) -->
    <div class="overflow-hidden border border-gray-200">

        {{-- Main header --}}
        <div class="px-4 sm:px-6 md:px-8 pt-6 pb-0 bg-white">
            <div class="grid grid-cols-1 md:grid-cols-[160px_1fr_auto] items-center md:items-start gap-4 md:gap-6">

                {{-- Logo --}}
                <div class="flex justify-center md:justify-start pt-1">
                    <img src="{{ asset('images/Vanguard.png') }}" alt="Vanguard Assurance Logo"
                        class="w-36 h-12 object-contain" />
                </div>

                {{-- Company name --}}
                <div class="text-center pt-1">
                    <p class="text-[15px] font-bold text-gray-800 tracking-wide mb-2 border-b border-b-gray-300 pb-2">
                        Vanguard Assurance Company Ltd
                    </p>
                    <p class="text-[10px] text-gray-500 mt-0.5 tracking-widest uppercase">
                        We always stand by you
                    </p>
                </div>

                {{-- Contact info --}}
                <div class="text-center md:text-right text-[11px] text-gray-500 leading-relaxed pt-1">
                    <p>vacmmails@vanguardassurance.com</p>
                    <p>claimsdepartment@vanguardassurance.com</p>
                    <p>030 266 6485 / 6486 / 6487</p>
                    <p>P.O. Box 1868, Accra</p>
                </div>

            </div>

            <div class="border-t border-gray-200 mt-5"></div>
        </div>

        {{-- Document title band --}}
        <div class="bg-[#0b529d] px-4 sm:px-6 md:px-8 py-2.5 flex items-center justify-center gap-4">
            <div class="flex-1 border-t border-white/20"></div>
            <p class="text-[13px] font-medium tracking-widest uppercase text-white whitespace-nowrap">
                Travel Protection Claim Form{{ $isEdit ? ' — Edit' : '' }}
            </p>
            <div class="flex-1 border-t border-white/20"></div>
        </div>

        {{-- Context banner --}}
        @if ($isEdit && $isStaff)
            <div class="bg-indigo-50 border-b border-indigo-200 px-4 sm:px-6 md:px-8 py-2 text-center">
                <p class="text-[11.5px] text-indigo-700 font-medium">
                    <i class="fas fa-user-shield mr-1"></i>
                    Editing as staff — all changes will be logged with your name and timestamp.
                </p>
            </div>
        @elseif($isEdit)
            <div class="bg-amber-50 border-b border-amber-200 px-4 sm:px-6 md:px-8 py-2 text-center">
                <p class="text-[11.5px] text-amber-700 font-medium">
                    <i class="fas fa-edit mr-1"></i>
                    Editing claim <strong>{{ $claim->claim_number }}</strong> — fields are pre-filled with your original
                    submission.
                </p>
            </div>
        @else
            <div class="bg-gray-50 border-b border-gray-200 px-4 sm:px-6 md:px-8 py-2 text-center">
                <p class="text-[11.5px] text-gray-500">
                    Please complete all sections accurately. Fields marked * are required.
                </p>
            </div>
        @endif

    </div>

    <div class="py-4 px-4 sm:px-6 md:px-8 lg:px-12">
        <!-- Note box (original content, restyled) -->
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 rounded-lg">
            <div class="flex items-start gap-3">
                <div class="shrink-0"><i class="fas fa-info-circle text-blue-600 text-lg"></i></div>
                <div class="flex-1">
                    <p class="text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">NOTE - THE
                        ORIGINAL CLAIM FORM AND DOCUMENTATION IS REQUIRED</p>
                    <ul class="space-y-2 text-xs text-gray-600">
                        <li class="flex items-start gap-2"><span class="text-blue-600 font-bold">•</span><span>THIS FORM
                                MUST BE SIGNED AND
                                DATED</span></li>
                        <li class="flex items-start gap-2"><span class="text-blue-600 font-bold">•</span><span>A COPY OF
                                YOUR TRAVEL
                                INSURANCE CERTIFICATE MUST BE ATTACHED</span></li>
                        <li class="flex items-start gap-2"><span
                                class="text-blue-600 font-bold">•</span><span>SUPPORTING DOCUMENTATION
                                SUBSTANTIATING THE CLAIM MUST BE SUBMITTED</span></li>
                        <li class="flex items-start gap-2"><span class="text-blue-600 font-bold">•</span><span>ALL
                                CLAIMS MUST BE PREPARED
                                WITHIN 24 HOURS OF THE INCIDENT</span></li>
                        <li class="flex items-start gap-2"><span class="text-blue-600 font-bold">•</span><span>ALL
                                CLAIMS MUST BE SUBMITTED
                                WITHIN 60 DAYS OF THE INCIDENT</span></li>
                        <li class="flex items-start gap-2 mt-2 font-semibold text-gray-700"><span
                                class="text-blue-600">•</span><span>DELAYED / LOSS OF LUGGAGE:</span></li>
                        <li class="flex items-start gap-2 ml-6"><span class="text-gray-400">1</span><span>Kindly submit
                                a complete baggage
                                Inventory Claim Form from the airline</span></li>
                        <li class="flex items-start gap-2 ml-6"><span class="text-gray-400">2</span><span>Travel
                                Ticket</span></li>
                    </ul>
                </div>
            </div>
        </div>

        <form id="travelClaimForm" data-action="{{ $action }}">
            @csrf
            @if ($method === 'PUT')
                @method('PUT')
            @endif
            <input type="hidden" name="claim_type" value="general_accident" />
            <input type="hidden" name="policy_id" value="{{ $policy->external_policy_id ?? $policy->id }}">
            <input type="hidden" name="risk_id" value="{{ $riskId ?? '' }}">

            <!-- Section: THIS FORM MUST BE COMPLETED IN FULL -->
            <section class="mb-6 border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                <h3
                    class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2 flex items-center gap-2">
                    THIS FORM MUST BE COMPLETED IN FULL
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Selling Agent/Broker
                            <span class="text-red-500">*</span></label><x-input name="agent_broker"
                            value="{{ $f['agent_broker'] ?? '' }}" class="w-full" required /></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Departure Date <span
                                class="text-red-500">*</span></label><input type="date" name="departure_date"
                            value="{{ $f['departure_date'] ?? '' }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                            required /></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Return Date <span
                                class="text-red-500">*</span></label><input type="date" name="return_date"
                            value="{{ $f['return_date'] ?? '' }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                            required /></div>
                </div>
            </section>

            <!-- Section: INSURED PERSON -->
            <section class="mb-6 border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                <h3
                    class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2 flex items-center gap-2">
                    INSURED PERSON
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Surname <span
                                class="text-red-500">*</span></label><x-input name="surname"
                            value="{{ $f['surname'] ?? '' }}" class="w-full" required />
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">First Name(s) <span
                                class="text-red-500">*</span></label><x-input name="firstname"
                            value="{{ $f['firstname'] ?? '' }}" class="w-full" required />
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Age <span
                                class="text-red-500">*</span></label><x-input name="insured_age"
                            value="{{ $f['insured_age'] ?? '' }}" class="w-full" required />
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Postal Address <span
                                class="text-red-500">*</span></label><x-input name="postal_address"
                            value="{{ $f['postal_address'] ?? '' }}" class="w-full" required /></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Code</label><x-input
                            name="postal_code" value="{{ $f['postal_code'] ?? '' }}" class="w-full" /></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Physical Address <span
                                class="text-red-500">*</span></label><x-input name="physical_address"
                            value="{{ $f['physical_address'] ?? '' }}" class="w-full" required /></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Address
                            Code</label><x-input name="address_code" value="{{ $f['address_code'] ?? '' }}"
                            class="w-full" /></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Business</label><x-input
                            name="business" value="{{ $f['business'] ?? '' }}" class="w-full" /></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">FAX</label><x-input
                            name="fax" value="{{ $f['fax'] ?? '' }}" class="w-full" /></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Res/Cell</label><x-input
                            name="res_cell" value="{{ $f['res_cell'] ?? '' }}" class="w-full" /></div>
                </div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Email
                        Address</label><x-input name="email" value="{{ $f['email'] ?? '' }}" class="w-full" />
                </div>
            </section>

            <!-- Conditional Sections: Contacted AAFIYA & Police Report -->
            <section class="mb-6 border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Did you contact AAFIYA at the time of
                        the occurrence? <span class="text-red-500">*</span></label>
                    <div class="flex flex-wrap gap-4">
                        <label class="flex items-center">
                            <input type="radio" name="contacted_aafiya" value="yes"
                                class="conditional-radio mr-2" data-target="contactedAafiyaDetails" required
                                {{ ($f['contacted_aafiya'] ?? '') === 'yes' ? 'checked' : '' }}>
                            <span>Yes</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="contacted_aafiya" value="no"
                                class="conditional-radio mr-2" data-target="contactedAafiyaDetails" required
                                {{ ($f['contacted_aafiya'] ?? '') === 'no' ? 'checked' : '' }}>
                            <span>No</span>
                        </label>
                    </div>
                    <div id="contactedAafiyaDetails"
                        class="{{ ($f['contacted_aafiya'] ?? '') === 'yes' ? '' : 'hidden' }} mt-3 pl-4 border-l-2 border-blue-200">
                        <label class="block text-sm font-medium text-gray-700 mb-1">If Yes, Please provide
                            details... <span class="text-red-500">*</span></label>
                        <x-textarea name="contacted_aafiya_details"
                            value="{{ $f['contacted_aafiya_details'] ?? '' }}" rows="3" class="w-full"
                            placeholder="Describe the details here..." />
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Was the accident reported to the
                        police? <span class="text-red-500">*</span></label>
                    <div class="flex flex-wrap gap-4">
                        <label class="flex items-center">
                            <input type="radio" name="police_report" value="yes" class="conditional-radio mr-2"
                                data-target="policeReportDetails" required
                                {{ ($f['police_report'] ?? '') === 'yes' ? 'checked' : '' }}>
                            <span>Yes</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="police_report" value="no" class="conditional-radio mr-2"
                                data-target="policeReportDetails" required
                                {{ ($f['police_report'] ?? '') === 'no' ? 'checked' : '' }}>
                            <span>No</span>
                        </label>
                    </div>
                    <div id="policeReportDetails"
                        class="{{ ($f['police_report'] ?? '') === 'yes' ? '' : 'hidden' }} mt-3 pl-4 border-l-2 border-blue-200">
                        <label class="block text-sm font-medium text-gray-700 mb-1">If Yes, Please provide
                            details... <span class="text-red-500">*</span></label>
                        <x-textarea name="police_report_details" value="{{ $f['police_report_details'] ?? '' }}"
                            rows="2" class="w-full" placeholder="Describe the details here..." />
                    </div>
                </div>
            </section>

            <!-- Section 1 – MEDICAL AND RELATED EXPENSES -->
            <section class="mb-6 border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                <h3
                    class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2 flex items-center gap-2">
                    SECTION 1 - MEDICAL AND RELATED EXPENSES
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Date of Illness /
                            Injury <span class="text-red-500">*</span></label><input type="date"
                            name="illness_date" value="{{ $f['illness_date'] ?? '' }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                            required /></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Place of Illness /
                            Injury <span class="text-red-500">*</span></label><x-input name="place_of_illness"
                            value="{{ $f['place_of_illness'] ?? '' }}" class="w-full" required /></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Cause of Illness /
                            Injury <span class="text-red-500">*</span></label><x-input name="cause_of_illness"
                            value="{{ $f['cause_of_illness'] ?? '' }}" class="w-full" required /></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Diagnosis <span
                                class="text-red-500">*</span></label><x-input name="diagnosis_sec1"
                            value="{{ $f['diagnosis_sec1'] ?? '' }}" class="w-full" required /></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Full Name of Doctor
                            Consulted <span class="text-red-500">*</span></label><x-input name="doctor_fullname_sec1"
                            value="{{ $f['doctor_fullname_sec1'] ?? '' }}" class="w-full" required /></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Telephone of Doctor
                            Consulted <span class="text-red-500">*</span></label><x-input name="doctor_telephone"
                            value="{{ $f['doctor_telephone'] ?? '' }}" class="w-full" required /></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Name Of Hospital
                            Admitted To <span class="text-red-500">*</span></label><x-input name="hospital_name"
                            value="{{ $f['hospital_name'] ?? '' }}" class="w-full" required /></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Total Amount Claimed
                            <span class="text-red-500">*</span></label><x-input name="total_amount_claimed"
                            value="{{ $f['total_amount_claimed'] ?? '' }}" class="w-full" required /></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Currency <span
                                class="text-red-500">*</span></label><x-input name="currency"
                            value="{{ $f['currency'] ?? '' }}" class="w-full" required />
                    </div>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Have you previously received
                        treatment or attention for this Illness/Condition? <span class="text-red-500">*</span></label>
                    <div class="flex flex-wrap gap-4">
                        <label class="flex items-center">
                            <input type="radio" name="treatment_received" value="yes"
                                class="conditional-radio mr-2" data-target="treatmentReceivedDetails" required
                                {{ ($f['treatment_received'] ?? '') === 'yes' ? 'checked' : '' }}>
                            <span>Yes</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="treatment_received" value="no"
                                class="conditional-radio mr-2" data-target="treatmentReceivedDetails" required
                                {{ ($f['treatment_received'] ?? '') === 'no' ? 'checked' : '' }}>
                            <span>No</span>
                        </label>
                    </div>
                    <div id="treatmentReceivedDetails"
                        class="{{ ($f['treatment_received'] ?? '') === 'yes' ? '' : 'hidden' }} mt-3 pl-4 border-l-2 border-blue-200">
                        <label class="block text-sm font-medium text-gray-700 mb-1">If Yes, a report from your
                            treating doctor detailing your medical history <span class="text-red-500">*</span></label>
                        <x-textarea name="treatment_received_details"
                            value="{{ $f['treatment_received_details'] ?? '' }}" rows="3" class="w-full"
                            placeholder="Describe the details here..." />
                    </div>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Have Submitted accounts
                        been paid? <span class="text-red-500">*</span></label>
                    <div class="flex flex-wrap gap-4">
                        <label class="flex items-center">
                            <input type="radio" name="submitted_accounts_yn" value="yes" required
                                class="mr-2" {{ ($f['submitted_accounts_yn'] ?? '') === 'yes' ? 'checked' : '' }}>
                            Yes
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="submitted_accounts_yn" value="no" required
                                class="mr-2" {{ ($f['submitted_accounts_yn'] ?? '') === 'no' ? 'checked' : '' }}> No
                        </label>
                    </div>
                </div>
            </section>

            <!-- Section 2 – DETAILS OF CLAIM -->
            <section class="mb-6 border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                <h3
                    class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2 flex items-center gap-2">
                    SECTION 2 - DETAILS OF CLAIM
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Full Name Of Subject
                            Of Claim <span class="text-red-500">*</span></label><x-input name="claim_subject_name"
                            value="{{ $f['claim_subject_name'] ?? '' }}" class="w-full" required /></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Date Of Birth <span
                                class="text-red-500">*</span></label><input type="date" name="dob_claim"
                            value="{{ $f['dob_claim'] ?? '' }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                            required /></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Date Of Illness /
                            Injury <span class="text-red-500">*</span></label><input type="date"
                            name="illness_date_sec2" value="{{ $f['illness_date_sec2'] ?? '' }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                            required /></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Attending Doctor's
                            Full Name <span class="text-red-500">*</span></label><x-input name="doctor_fullname_sec2"
                            value="{{ $f['doctor_fullname_sec2'] ?? '' }}" class="w-full" required /></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Diagnosis <span
                                class="text-red-500">*</span></label><x-input name="diagnosis_sec2"
                            value="{{ $f['diagnosis_sec2'] ?? '' }}" class="w-full" required /></div>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Has the above-mentioned person
                        suffered previously from Illness/Injury? <span class="text-red-500">*</span></label>
                    <div class="flex flex-wrap gap-4">
                        <label class="flex items-center">
                            <input type="radio" name="previous_injury" value="yes"
                                class="conditional-radio mr-2" data-target="previousInjuryDetails" required
                                {{ ($f['previous_injury'] ?? '') === 'yes' ? 'checked' : '' }}>
                            <span>Yes</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="previous_injury" value="no"
                                class="conditional-radio mr-2" data-target="previousInjuryDetails" required
                                {{ ($f['previous_injury'] ?? '') === 'no' ? 'checked' : '' }}>
                            <span>No</span>
                        </label>
                    </div>
                    <div id="previousInjuryDetails"
                        class="{{ ($f['previous_injury'] ?? '') === 'yes' ? '' : 'hidden' }} mt-3 pl-4 border-l-2 border-blue-200">
                        <label class="block text-sm font-medium text-gray-700 mb-1">If Yes, A report from the
                            treating doctor detailing medical history is required <span
                                class="text-red-500">*</span></label>
                        <x-textarea name="previous_injury_details" value="{{ $f['previous_injury_details'] ?? '' }}"
                            rows="3" class="w-full" placeholder="Describe the details here..." />
                    </div>
                </div>
            </section>

            <!-- Section 3 – ELECTRONIC FUNDS TRANSFER, DECLARATION AND AUTHORITY -->
            <section class="mb-6 border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                <h3
                    class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2 flex items-center gap-2">
                    SECTION 3 - ELECTRONIC FUNDS
                    TRANSFER, DECLARATION AND AUTHORITY
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Account Holder's Name
                            <span class="text-red-500">*</span></label><x-input name="account_holder_name"
                            value="{{ $f['account_holder_name'] ?? '' }}" class="w-full" required />
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Account Number <span
                                class="text-red-500">*</span></label><x-input name="account_number"
                            value="{{ $f['account_number'] ?? '' }}" class="w-full" required /></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Name Of Bank <span
                                class="text-red-500">*</span></label><x-input name="bank_name"
                            value="{{ $f['bank_name'] ?? '' }}" class="w-full" required />
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Type Of Account <span
                                class="text-red-500">*</span></label><x-input name="account_type"
                            value="{{ $f['account_type'] ?? '' }}" class="w-full" required /></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Branch Name <span
                                class="text-red-500">*</span></label><x-input name="branch_name"
                            value="{{ $f['branch_name'] ?? '' }}" class="w-full" required /></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Branch Code <span
                                class="text-red-500">*</span></label><x-input name="branch_code"
                            value="{{ $f['branch_code'] ?? '' }}" class="w-full" required /></div>
                </div>
            </section>

            <!-- General Section -->
            <section class="mb-6 border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                <h3
                    class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2 flex items-center gap-2">
                    General Section
                </h3>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Have you incurred any
                        travel claims in the past 5 years? If so, please supply details
                        below:</label><x-textarea name="general_section" value="{{ $f['general_section'] ?? '' }}"
                        rows="2" class="w-full" placeholder="Describe the details here..." required /></div>
            </section>

            {{-- Image Upload Section --}}
            <section class="mb-8">
                <h3
                    class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2 flex items-center gap-2">
                    Add Images
                </h3>

                {{-- Draft documents (create mode, resuming a saved draft) --}}
                @if (!$isEdit && ($draft ?? null) && $draft->documents->isNotEmpty())
                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-700 mb-2">From your saved draft:</p>
                        <div class="space-y-2" id="draftDocumentsList">
                            @foreach ($draft->documents as $doc)
                                <div class="flex flex-wrap items-center justify-between p-3 bg-blue-50 rounded-lg border border-blue-100"
                                    id="draft-doc-{{ $doc->id }}">
                                    <div class="flex items-center gap-2">
                                        <i
                                            class="fas {{ str_contains($doc->mime_type, 'pdf') ? 'fa-file-pdf text-red-400' : 'fa-image text-blue-400' }} text-sm"></i>
                                        <span class="text-sm text-gray-700">{{ $doc->original_name }}</span>
                                        <span
                                            class="text-xs text-gray-400">{{ number_format($doc->file_size / 1024, 1) }}
                                            KB</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <button type="button"
                                            onclick="openDocPreview('{{ route('customer.claims.draft.documents.preview', $doc->id) }}', '{{ $doc->original_name }}', '{{ $doc->mime_type }}')"
                                            class="text-xs text-blue-600 hover:underline">View</button>
                                        <button type="button"
                                            onclick="deleteDraftDocument({{ $doc->id }}, this)"
                                            class="text-xs text-red-500 hover:underline">Remove</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Existing documents (edit mode only) --}}
                @if ($isEdit && $claim->documents->isNotEmpty())
                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-700 mb-2">Previously uploaded:</p>
                        <div class="space-y-2">
                            @foreach ($claim->documents as $doc)
                                <div
                                    class="flex flex-wrap items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <div class="flex items-center gap-2">
                                        <i
                                            class="fas {{ str_contains($doc->mime_type, 'pdf') ? 'fa-file-pdf text-red-400' : 'fa-image text-blue-400' }} text-sm"></i>
                                        <span class="text-sm text-gray-700">{{ $doc->original_name }}</span>
                                        <span
                                            class="text-xs text-gray-400">{{ number_format($doc->file_size / 1024, 1) }}
                                            KB</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <button type="button"
                                            onclick="openDocPreview('{{ route('staff.documents.preview', $doc->id) }}', '{{ $doc->original_name }}', '{{ $doc->mime_type }}')"
                                            class="text-xs text-blue-600 hover:underline">View</button>
                                        <button type="button"
                                            onclick="markDocumentForDeletion({{ $doc->id }}, this)"
                                            class="text-xs text-red-500 hover:underline">Remove</button>
                                        <input type="hidden" name="delete_documents[]" value=""
                                            id="delete-doc-{{ $doc->id }}" disabled>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition cursor-pointer"
                    id="dropzone">
                    <i class="fas fa-cloud-upload-alt text-gray-400 text-4xl mb-2"></i>
                    <p class="text-gray-600">Drag & drop files here or <span
                            class="text-blue-600 font-medium">browse</span></p>
                    <p class="text-xs text-gray-400 mt-1">Supports: JPG, PNG, PDF (max 5MB each)</p>
                    <input type="file" id="imageUpload" accept="image/jpeg,image/png,image/gif,application/pdf"
                        multiple class="hidden">
                </div>
                <div id="imagePreviewContainer" class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                </div>
            </section>

            {{-- ── STAFF NOTE (staff edit only) ── --}}
            @if ($isStaff)
                <section class="mb-8">
                    <h3
                        class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2 flex items-center gap-2">
                        <i class="fas fa-sticky-note text-indigo-500"></i> Edit Note
                    </h3>
                    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Reason for edit <span class="text-xs text-gray-400">(logged in activity timeline)</span>
                        </label>
                        <input type="text" name="note"
                            placeholder="e.g. Customer called to correct account number"
                            class="w-full px-3 py-2 border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-400 outline-none bg-white">
                    </div>
                </section>
            @endif

            {{-- DECLARATION (customer only) --}}
            @if (!$isStaff)
                <section class="mb-8">
                    <h3
                        class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2 flex items-center gap-2">
                        <i class="fas fa-file-signature text-blue-500"></i> DECLARATION
                    </h3>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                        <p class="text-xs text-gray-700 leading-relaxed mb-4">I declare that the above
                            statement is true in all respects to the best of my knowledge and belief and I
                            hereby leave in the hands of the
                            Company in accordance with the conditions of the Policy the conduct of all claims
                            and litigation arising out of this accident and to
                            which the Policy applies, to deal with, to prosecute and/or settle as they deem fit
                            without further reference to me; and I undertake to
                            give all such information and assistance as the Company may require.</p>
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 rounded">
                            <p class="text-sm text-gray-700"><span class="font-semibold">Note:</span> The
                                Company does not admit liability by the issue of this form.</p>
                        </div>
                    </div>
                    <div class="bg-white border-2 border-blue-200 rounded-lg p-4 mb-6">
                        <label class="flex items-start cursor-pointer">
                            <input type="checkbox" name="declaration_agreement" required
                                class="mt-1 w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                                {{ !empty($f['declaration_agreement']) ? 'checked' : '' }}>
                            <span class="ml-3 text-xs text-gray-700">I have read and understood the
                                declaration above. I confirm that all information provided in this form is
                                true and accurate to the best of my knowledge. <span
                                    class="text-red-500">*</span></span>
                        </label>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Date of
                                Declaration <span class="text-red-500">*</span></label><input type="date"
                                name="declaration_date" value="{{ $f['declaration_date'] ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg"></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Digital Signature
                                <span class="text-xs text-gray-500">(Type your full name)</span> <span
                                    class="text-red-500">*</span></label><input type="text"
                                name="digital_signature" value="{{ $f['digital_signature'] ?? '' }}"
                                placeholder="Type your full name as your digital signature"
                                class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-cursive text-lg"
                                style="font-family: 'Brush Script MT', cursive;">
                            <p class="text-xs text-gray-500 mt-1">By typing your name above, you are
                                providing a legal digital signature for this declaration.</p>
                        </div>
                    </div>
                </section>
            @endif

            <!-- Submit Button -->
            <div
                class="mt-8 pt-4 border-t border-gray-200 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                @if (!$isStaff && !$isEdit)
                    <button type="button" id="saveDraftBtn"
                        class="w-full sm:w-auto px-6 py-2 border border-blue-300 text-blue-600 font-medium rounded-lg hover:bg-blue-50 transition flex items-center justify-center gap-2">
                        <i class="fas fa-clock"></i> <span>Save Draft</span>
                    </button>
                @endif
                <button type="submit"
                    class="w-full sm:w-auto px-6 py-2 {{ $isStaff ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-blue-600 hover:bg-blue-700' }} text-white font-medium rounded-lg transition flex items-center justify-center gap-2">
                    @if (!$isEdit)
                        <span>Submit Claim</span><i class="fas fa-paper-plane"></i>
                    @else
                        <span>Save Changes</span><i class="fas fa-save"></i>
                    @endif
                </button>
                @if ($isEdit)
                    <a href="{{ $isStaff ? route('staff.claims.show', $claim) : route('claims.show', $claim) }}"
                        class="w-full sm:w-auto px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition text-center">
                        Cancel
                    </a>
                @endif
            </div>

            @if ($isEdit && $isStaff)
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        @if (!$isAssignedToMe && !$isAssignedToOther)
                            Swal.fire({
                                title: 'Claim is unassigned',
                                html: `
                        <p class="text-sm text-gray-600 leading-relaxed">
                            Nobody is currently assigned to claim
                            <strong>{{ $claim->claim_number }}</strong>.<br><br>
                            Would you like to assign it to yourself before editing?
                        </p>`,
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonText: 'Yes, assign to me',
                                cancelButtonText: 'No, just edit',
                                confirmButtonColor: '#4f46e5',
                                cancelButtonColor: '#6b7280',
                                reverseButtons: true,
                            }).then(result => {
                                if (result.isConfirmed) {
                                    fetch('{{ route('staff.claims.assign', $claim) }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Accept': 'application/json',
                                            },
                                            body: JSON.stringify({
                                                assigned_to: {{ Auth::id() }},
                                                note: 'Self-assigned before editing form.',
                                            }),
                                        })
                                        .then(res => res.json())
                                        .then(() => {
                                            Swal.fire({
                                                toast: true,
                                                position: 'top-end',
                                                icon: 'success',
                                                title: 'Assigned to you',
                                                showConfirmButton: false,
                                                timer: 2500,
                                                timerProgressBar: true,
                                            });
                                        })
                                        .catch(() => {
                                            Swal.fire({
                                                toast: true,
                                                position: 'top-end',
                                                icon: 'warning',
                                                title: 'Could not assign — edits will still be logged',
                                                showConfirmButton: false,
                                                timer: 3000,
                                            });
                                        });
                                }
                            });
                        @elseif ($isAssignedToOther)
                            Swal.fire({
                                title: 'Claim already assigned',
                                html: `
                        <p class="text-sm text-gray-600 leading-relaxed">
                            This claim is currently assigned to
                            <strong>{{ $assignee->name }}</strong>.<br><br>
                            You can still make edits — everything will be logged with your name
                            and <strong>{{ $assignee->name }}</strong> will be able to see your changes
                            in the activity log.
                        </p>`,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Proceed with edits',
                                cancelButtonText: 'Go back',
                                confirmButtonColor: '#4f46e5',
                                cancelButtonColor: '#6b7280',
                                reverseButtons: true,
                            }).then(result => {
                                if (!result.isConfirmed) {
                                    window.location.href = '{{ route('staff.claims.show', $claim) }}';
                                }
                            });
                        @endif
                    });
                </script>
            @endif
        </form>
    </div>
</div>

{{-- Shared document preview modal --}}
<x-documents-modal />
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isEdit = {{ $isEdit ? 'true' : 'false' }};
        const isStaff = {{ $isStaff ? 'true' : 'false' }};

        // Route template for deleting a draft document — swap 0 for the real ID at call time
        const draftDocDestroyTemplate =
            "{{ route('customer.claims.draft.documents.destroy', ['document' => 0]) }}";

        // ── Conditional radio toggles ──────────────────────────────────────────
        document.addEventListener('change', function(e) {
            if (!e.target.classList.contains('conditional-radio')) return;

            const target = document.getElementById(e.target.getAttribute('data-target'));
            if (!target) return;

            const isYes = e.target.value === 'yes';
            target.classList.toggle('hidden', !isYes);
            target.querySelectorAll('input, textarea, select').forEach(input => {
                input.required = isYes;
                if (!isYes) input.value = '';
            });
        });

        // ── File upload ────────────────────────────────────────────────────────
        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('imageUpload');
        const previewContainer = document.getElementById('imagePreviewContainer');
        let uploadedFiles = [];

        function renderPreviews() {
            previewContainer.innerHTML = '';
            uploadedFiles.forEach((file, index) => {
                const div = document.createElement('div');
                div.className =
                    'relative group border border-gray-200 rounded-lg overflow-hidden bg-gray-50';
                if (file.type === 'application/pdf') {
                    div.innerHTML =
                        `
                        <div class="w-full h-24 flex flex-col items-center justify-center gap-1 bg-red-50">
                            <i class="fas fa-file-pdf text-red-500 text-3xl"></i>
                            <span class="text-xs text-gray-500 truncate px-2 w-full text-center">${file.name}</span>
                        </div>
                        <button type="button" onclick="removeFile(${index})"
                            class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition">✕</button>`;
                } else {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        div.innerHTML =
                            `
                            <img src="${e.target.result}" class="w-full h-24 object-cover">
                            <button type="button" onclick="removeFile(${index})"
                                class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition">✕</button>`;
                    };
                    reader.readAsDataURL(file);
                }
                previewContainer.appendChild(div);
            });
        }

        const MAX_FILE_SIZE_MB = 10;
        const MAX_FILE_COUNT = 10;
        const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];

        function addFiles(newFiles) {
            const errors = [];

            for (const file of newFiles) {
                if (!ALLOWED_TYPES.includes(file.type)) {
                    errors.push(`"${file.name}" is not a supported file type.`);
                    continue;
                }

                if (file.size > MAX_FILE_SIZE_MB * 1024 * 1024) {
                    errors.push(`"${file.name}" exceeds the ${MAX_FILE_SIZE_MB}MB limit.`);
                    continue;
                }

                if (uploadedFiles.length >= MAX_FILE_COUNT) {
                    errors.push(`You can upload a maximum of ${MAX_FILE_COUNT} files.`);
                    break;
                }

                const isDuplicate = uploadedFiles.some(f => f.name === file.name && f.size === file.size);
                if (isDuplicate) {
                    errors.push(`"${file.name}" has already been added.`);
                    continue;
                }

                uploadedFiles.push(file);
            }

            if (errors.length) {
                showClaimError(errors.join('\n'));
            }

            renderPreviews();
        }

        window.removeFile = function(index) {
            uploadedFiles.splice(index, 1);
            renderPreviews();
        };

        dropzone?.addEventListener('click', () => fileInput.click());
        dropzone?.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.classList.add('border-blue-500', 'bg-blue-50');
        });
        dropzone?.addEventListener('dragleave', () => {
            dropzone.classList.remove('border-blue-500', 'bg-blue-50');
        });
        dropzone?.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.classList.remove('border-blue-500', 'bg-blue-50');
            addFiles(Array.from(e.dataTransfer.files));
        });
        fileInput?.addEventListener('change', (e) => {
            addFiles(Array.from(e.target.files));
            e.target.value = '';
        });

        // ── Document deletion marking (edit mode) ─────────────────────────────
        window.markDocumentForDeletion = function(docId, btn) {
            const input = document.getElementById(`delete-doc-${docId}`);
            const card = btn.closest('.flex.items-center.justify-between');
            if (input) {
                input.value = docId;
                input.disabled = false;
            }
            card?.classList.add('opacity-40', 'line-through');
            btn.textContent = 'Undo';
            btn.onclick = () => undoDocumentDeletion(docId, btn, card);
        };

        window.undoDocumentDeletion = function(docId, btn, card) {
            const input = document.getElementById(`delete-doc-${docId}`);
            if (input) {
                input.value = '';
                input.disabled = true;
            }
            card?.classList.remove('opacity-40', 'line-through');
            btn.textContent = 'Remove';
            btn.onclick = () => markDocumentForDeletion(docId, btn);
        };

        // ── Draft document deletion (instant — no defer-to-submit step) ─────────
        window.deleteDraftDocument = async function(docId, btn) {
            const row = document.getElementById(`draft-doc-${docId}`);
            btn.disabled = true;

            try {
                const url = draftDocDestroyTemplate.replace('/0', `/${docId}`);
                const response = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .getAttribute('content'),
                        'Accept': 'application/json',
                    },
                });
                const data = await response.json();
                if (data.success) {
                    row?.remove();
                } else {
                    btn.disabled = false;
                }
            } catch (err) {
                btn.disabled = false;
            }
        };

        // ── Shared payload builder — used by both Submit and Save Draft ─────────
        function buildClaimFormData({
            includeDeleteDocuments = true
        } = {}) {
            const formData = new FormData();
            if (isEdit) formData.append('_method', 'PUT');
            formData.append('claim_type', 'general_accident');
            formData.append('_token', document.querySelector('meta[name="csrf-token"]')
                .getAttribute('content'));
            formData.append('policy_id', document.querySelector('[name="policy_id"]')?.value ?? '');
            formData.append('risk_id', document.querySelector('[name="risk_id"]')?.value ?? '');

            const claimFields = {
                agent_broker: val('agent_broker'),
                departure_date: val('departure_date'),
                return_date: val('return_date'),
                surname: val('surname'),
                firstname: val('firstname'),
                insured_age: val('insured_age'),
                postal_address: val('postal_address'),
                postal_code: val('postal_code'),
                physical_address: val('physical_address'),
                address_code: val('address_code'),
                business: val('business'),
                fax: val('fax'),
                res_cell: val('res_cell'),
                email: val('email'),
                contacted_aafiya: checked('contacted_aafiya'),
                contacted_aafiya_details: val('contacted_aafiya_details'),
                police_report: checked('police_report'),
                police_report_details: val('police_report_details'),
                illness_date: val('illness_date'),
                place_of_illness: val('place_of_illness'),
                cause_of_illness: val('cause_of_illness'),
                diagnosis_sec1: val('diagnosis_sec1'),
                doctor_fullname_sec1: val('doctor_fullname_sec1'),
                doctor_telephone: val('doctor_telephone'),
                hospital_name: val('hospital_name'),
                total_amount_claimed: val('total_amount_claimed'),
                currency: val('currency'),
                treatment_received: checked('treatment_received'),
                treatment_received_details: val('treatment_received_details'),
                submitted_accounts_yn: checked('submitted_accounts_yn'),
                claim_subject_name: val('claim_subject_name'),
                dob_claim: val('dob_claim'),
                illness_date_sec2: val('illness_date_sec2'),
                doctor_fullname_sec2: val('doctor_fullname_sec2'),
                diagnosis_sec2: val('diagnosis_sec2'),
                previous_injury: checked('previous_injury'),
                previous_injury_details: val('previous_injury_details'),
                account_holder_name: val('account_holder_name'),
                account_number: val('account_number'),
                bank_name: val('bank_name'),
                account_type: val('account_type'),
                branch_name: val('branch_name'),
                branch_code: val('branch_code'),
                general_section: val('general_section'),
                declaration_date: val('declaration_date'),
                digital_signature: val('digital_signature'),
                declaration_agreement: isChecked('declaration_agreement'),
            };

            Object.entries(claimFields).forEach(([key, value]) => {
                if (value !== null && value !== undefined) {
                    formData.append(`form_data[${key}]`, typeof value === 'object' ? JSON
                        .stringify(value) : value);
                }
            });

            // Staff note
            if (isStaff) {
                const note = val('note');
                if (note) formData.append('note', note);
            }

            // New files
            uploadedFiles.forEach((file, index) => {
                formData.append(`documents[${index}]`, file, file.name);
            });

            if (includeDeleteDocuments) {
                document.querySelectorAll('[id^="delete-doc-"]:not([disabled])').forEach(input => {
                    formData.append('delete_documents[]', input.value);
                });
            }

            return formData;
        }

        // ── Form submission ────────────────────────────────────────────────────
        document.getElementById('travelClaimForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();

            if (!isStaff && !isChecked('declaration_agreement')) {
                showClaimError('Please read and accept the declaration before submitting.');
                return;
            }

            if (!isStaff && !val('digital_signature').trim()) {
                showClaimError('Please provide your digital signature before submitting.');
                return;
            }

            const formData = buildClaimFormData();
            const action = document.getElementById('travelClaimForm').dataset.action;
            await submitClaimWithFiles('travelClaimForm', formData, action);
        });

        // ── Save Draft (bypasses declaration/signature validation entirely) ─────
        document.getElementById('saveDraftBtn')?.addEventListener('click', async function() {
            const btn = this;
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Saving...</span>';

            try {
                const formData = buildClaimFormData({
                    includeDeleteDocuments: false
                });

                const response = await fetch('{{ route('claims.draft.save') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const data = await response.json();

                if (data.success) {
                    uploadedFiles = []; // already persisted server-side, clear the pending queue
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Progress saved — you can continue later',
                        showConfirmButton: false,
                        timer: 2500,
                        timerProgressBar: true,
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    showClaimError(data.message ??
                        'Could not save your progress. Please try again.');
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            } catch (err) {
                showClaimError(
                    'Could not save your progress. Please check your connection and try again.');
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        });
    });
</script>
