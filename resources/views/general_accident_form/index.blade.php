<x-layouts.app>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Section - Claimant Information -->
        <x-claimant-info :policy="$policy" :customer="$customer" />

        <!-- Right Section - Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow px-10 py-6">
                <h1 class="text-2xl font-bold text-gray-900 text-center mb-3 pb-3 border-b border-gray-600 mx-auto"
                    style="max-width: 400px;">
                    TRAVEL PROTECTION CLAIM FORM
                </h1>

                <div class="bg-gray-50 border border-gray-200 p-4 mb-6 rounded-lg">
                    <div class="flex items-start gap-3">
                        <div class="shrink-0">
                            <i class="fas fa-info-circle text-blue-600 text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">
                                NOTE - THE ORIGINAL CLAIM FORM AND DOCUMENTATION IS REQUIRED
                            </p>
                            <ul class="space-y-2 text-sm text-gray-600">
                                <li class="flex items-start gap-2">
                                    <span class="text-blue-600 font-bold">•</span>
                                    <span>THIS FORM MUST BE SIGNED AND DATED</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-blue-600 font-bold">•</span>
                                    <span>A COPY OF YOUR TRAVEL INSURANCE CERTIFICATE MUST BE ATTACHED</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-blue-600 font-bold">•</span>
                                    <span>SUPPORTING DOCUMENTATION SUBSTANTIATING THE CLAIM MUST BE SUBMITTED</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-blue-600 font-bold">•</span>
                                    <span>ALL CLAIMS MUST BE PREPARED WITHIN 24 HOURS OF THE INCIDENT</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-blue-600 font-bold">•</span>
                                    <span>ALL CLAIMS MUST BE SUBMITTED WITHIN 60 DAYS OF THE INCIDENT</span>
                                </li>

                                <li class="flex items-start gap-2 mt-2 font-semibold text-gray-700">
                                    <span class="text-blue-600">•</span>
                                    <span>DELAYED / LOSS OF LUGGAGE:</span>
                                </li>
                                <li class="flex items-start gap-2 ml-6">
                                    <span class="text-gray-400">1</span>
                                    <span>Kindly submit a complete baggage Inventory Claim Form from the airline</span>
                                </li>
                                <li class="flex items-start gap-2 ml-6">
                                    <span class="text-gray-400">2</span>
                                    <span>Travel Ticket</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <form id="generalAccidentForm">
                    {{-- Insured Person Section --}}
                    <section class="mb-6 border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-b-gray-300 pb-2">
                                THIS FORM MUST BE COMPLETED IN FULL
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Selling Agent/Broker
                                        <span class="text-red-500">*</span></label>
                                    <x-input name="agent_broker" id="agent_broker" required />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Policy Number <span class="text-red-500">*</span>
                                    </label>
                                    <x-input name="policy_no" id="policy_no" :value="$policy->policy_number" required readonly />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Departure Date <span
                                            class="text-red-500">*</span></label>
                                    <input type="date" name="departure_date" id="departure_date" required
                                        class="w-full px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Return Date <span
                                            class="text-red-500">*</span></label>
                                    <input type="date" name="return_date" id="return_date" required
                                        class="w-full px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="mb-6 border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-b-gray-300 pb-2">
                                INSURED PERSON
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Surname
                                        <span class="text-red-500">*</span></label>
                                    <x-input name="surname" id="surname" required />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name(s)<span
                                            class="text-red-500">*</span></label>
                                    <x-input name="firstname" id="firstname" required />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Age<span
                                            class="text-red-500">*</span></label>
                                    <x-input name="insured_age" id="insured_age" required />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Postal Address<span
                                            class="text-red-500">*</span></label>
                                    <x-input name="postal_address" id="postal_address" required />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                                    <x-input name="postal_code" id="postal_code" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Physical Address
                                        <span class="text-red-500">*</span></label>
                                    <x-input name="physical_address" id="physical_address" required />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Address Code</label>
                                    <x-input name="address_code" id="address_code" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Business</label>
                                    <x-input name="business" id="business" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">FAX</label>
                                    <x-input name="fax" id="fax" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Res/Cell</label>
                                    <x-input name="res_cell" id="res_cell" />
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                <x-input name="email" id="email" />
                            </div>
                        </div>
                    </section>

                    <section class="mb-6 border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                        <!-- Contact Made -->
                        <x-conditional-section question="Did you contact AAFIYA at the time of the occurence?"
                            name="contacted_aafiya" yes-section-id="contactedAafiyaDetails" required="true">

                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                If Yes, Please provide details...
                                <span class="text-red-500">*</span>
                            </label>
                            <x-textarea name="contacted_aafiya_details" id="contactedAafiyaDetails" required
                                rows="3" placeholder="Describe the details here..." />
                        </x-conditional-section>

                        <!-- Others Insured -->
                        <x-conditional-section question="Was the accident reported to the police?"
                            name="police_report" yes-section-id="policeReportDetails" required="true">

                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                If Yes, Please provide details...
                                <span class="text-red-500">*</span>
                            </label>
                            <x-textarea name="police_report_details" id="policeReportDetails" required rows="2"
                                placeholder="Describe the details here..." />
                        </x-conditional-section>
                    </section>

                    {{-- Section 1 --}}
                    <section class="mb-6 border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-b-gray-300 pb-2">
                                SECTION 1 – MEDICAL AND RELATED EXPENSES
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Illness /
                                        Injury <span class="text-red-500">*</span></label>
                                    <input type="date" name="illness_date" id="illness_date" required
                                        class="w-full px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Place of Illness /
                                        Injury
                                        <span class="text-red-500">*</span></label>
                                    <x-input name="place_of_illness" id="place_of_illness" required />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Cause of Illness /
                                        Injury
                                        <span class="text-red-500">*</span></label>
                                    <x-input name="cause_of_illness" id="cause_of_illness" required />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Diagnosis
                                        <span class="text-red-500">*</span></label>
                                    <x-input name="diagnosis_sec1" id="diagnosis_sec1" required />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name of Doctor
                                        Consulted<span class="text-red-500">*</span></label>
                                    <x-input name="doctor_fullname_sec1" id="doctor_fullname_sec1" required />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Telephone of Doctor
                                        Consulted<span class="text-red-500">*</span></label>
                                    <x-input name="doctor_telephone" id="doctor_telephone" required />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Name Of Hospital
                                        Admitted To<span class="text-red-500">*</span></label>
                                    <x-input name="hospital_name" id="hospital_name" required />
                                </div>
                            </div>

                            <!-- Total Amount Claimed -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Amount
                                        Claimed<span class="text-red-500">*</span></label>
                                    <x-input name="total_amount_claimed" id="total_amount_claimed" required />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Currency<span
                                            class="text-red-500">*</span></label>
                                    <x-input name="currency" id="currency" required />
                                </div>
                            </div>

                            <x-conditional-section
                                question="Have you previously received treatment or attention for this Illness/Condition?"
                                name="treatment_received" yes-section-id="treatment_received_details"
                                required="true">

                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    If Yes, a report from you treating doctor detailing your medical history
                                    <span class="text-red-500">*</span>
                                </label>
                                <x-textarea name="treatment_received_details" id="treatment_received_details" required
                                    rows="3" placeholder="Describe the details here..." />
                            </x-conditional-section>

                            <!-- Submitted Accounts Paid -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Have Submitted accounts been paid?
                                    <span class="text-red-500">*</span>
                                </label>
                                <div class="flex gap-4">
                                    <label class="flex items-center">
                                        <input type="radio" name="submitted_accounts_yn" value="yes" required
                                            class="mr-2" />
                                        <span class="text-sm text-gray-700">Yes</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="submitted_accounts_yn" value="no" required
                                            class="mr-2" />
                                        <span class="text-sm text-gray-700">No</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- Section 2 --}}
                    <section class="mb-6 border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-b-gray-300 pb-2">
                                SECTION 2 – DETAILS OF CLAIM
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name Of Subject Of
                                        Claim <span class="text-red-500">*</span></label>
                                    <x-input name="claim_subject_name" id="claim_subject_name" required />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Date Of Birth <span
                                            class="text-red-500">*</span></label>
                                    <input type="date" name="dob_claim" id="dob_claim" required
                                        class="w-full px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Date Of Illness /
                                        Injury <span class="text-red-500">*</span></label>
                                    <input type="date" name="illness_date_sec2" id="illness_date_sec2" required
                                        class="w-full px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Attending Doctor's Full
                                        Name
                                        <span class="text-red-500">*</span></label>
                                    <x-input name="doctor_fullname_sec2" id="doctor_fullname_sec2" required />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Diagnosis
                                        <span class="text-red-500">*</span></label>
                                    <x-input name="diagnosis_sec2" id="diagnosis_sec2" required />
                                </div>
                            </div>
                            <x-conditional-section
                                question="Has the above-mentioned person suffered previously from Illness/Injury"
                                name="previous_injury" yes-section-id="previous_injury_details" required="true">

                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    If Yes, A report from the treating doctor detailing medical history is required
                                    <span class="text-red-500">*</span>
                                </label>

                                <x-textarea name="previous_injury_details" id="previous_injury_details" required
                                    rows="3" placeholder="Describe the details here..." />
                            </x-conditional-section>
                        </div>
                    </section>

                    {{-- Section 3 --}}
                    <section class="mb-6 border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-b-gray-300 pb-2">
                                SECTION 3 – ELECTRONIC FUNDS TRANSFER, DECLARATION AND AUTHORITY
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Account Holder's Name
                                        <span class="text-red-500">*</span></label>
                                    <x-input name="account_holder_name" id="account_holder_name" required />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Account Number <span
                                            class="text-red-500">*</span></label>
                                    <x-input name="account_number" id="account_number" required />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Name Of Bank <span
                                            class="text-red-500">*</span></label>
                                    <x-input name="bank_name" id="bank_name" required />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Type Of Account <span
                                            class="text-red-500">*</span></label>
                                    <x-input name="account_type" id="account_type" required />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Branch Name <span
                                            class="text-red-500">*</span></label>
                                    <x-input name="branch_name" id="branch_name" required />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Branch Code <span
                                            class="text-red-500">*</span></label>
                                    <x-input name="branch_code" id="branch_code" required />
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- General Section --}}
                    <section class="mb-6 border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-b-gray-300 pb-2">
                                General Section
                            </h3>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Have you incurred any
                                    travel claims in the past 5 years, If so, Please supply details below:</label>
                                <x-textarea name="general_section" id="general_section" required rows="2"
                                    placeholder="Describe the details here..." />
                            </div>
                        </div>
                    </section>

                    {{-- Declaration Section --}}
                    <section class="mb-6 border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-b-gray-300 pb-2">
                                DECLARATION:
                            </h3>

                            <!-- Declaration Text -->
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                                <p class="text-sm text-gray-700 leading-relaxed mb-4">
                                    I declare that the above statement is true in all respects to the best of my
                                    knowledge and belief and I hereby leave in the hands of the
                                    Company in accordance with the conditions of the Policy the conduct of all claims
                                    and litigation arising out of this accident and to
                                    which the Policy applies, to deal with, to prosecute and/or settle as they deem fit
                                    without further reference to me; and I undertake to
                                    give all such information and assistance as the Company may require.
                                </p>

                                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 rounded">
                                    <p class="text-sm text-gray-700">
                                        <span class="font-semibold text-gray-900">Note:</span> The Company does not
                                        admit liability by the
                                        issue of this form.
                                    </p>
                                </div>
                            </div>

                            <!-- Agreement Checkbox -->
                            <div class="bg-white border-2 border-blue-200 rounded-lg p-4 mb-6">
                                <label class="flex items-start cursor-pointer group">
                                    <input type="checkbox" name="declaration_agreement" id="declaration_agreement"
                                        required
                                        class="mt-1 w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500" />
                                    <span class="ml-3 text-sm text-gray-700 select-none">
                                        I have read and understood the declaration above. I confirm that all information
                                        provided in this form is true and accurate to the best of my knowledge.
                                        <span class="text-red-500">*</span>
                                    </span>
                                </label>
                            </div>

                            <!-- Date and Claimant Name -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Date of Declaration <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="declaration_date" id="declaration_date" required
                                        class="w-full px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                                </div>
                            </div>

                            <!-- Optional: Digital Signature (Simple text input) -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Digital Signature <span class="text-xs text-gray-500">(Type your full name)</span>
                                    <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" name="digital_signature" id="digital_signature" required
                                        placeholder="Type your full name as your digital signature"
                                        class="w-full px-3 py-2 border-2 border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none font-cursive text-lg"
                                        style="font-family: 'Brush Script MT', cursive;" />
                                    <div class="absolute bottom-2 left-3 right-3 border-b border-gray-300"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    By typing your name above, you are providing a legal digital signature for this
                                    declaration.
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- Submit Button -->
                    <div class="mt-8 pt-4 border-t border-t-gray-300">
                        <button type="submit" id="demoSubmitBtn"
                            class="w-full md:w-auto px-6 py-2 bg-blue-600 text-white font-medium rounded hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                            <span>Submit Claim</span>
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Generic conditional section toggle (keeps component behavior)
            document.querySelectorAll('.conditional-radio').forEach(radio => {
                radio.addEventListener('change', function() {
                    const targetId = this.getAttribute('data-target');
                    const targetSection = document.getElementById(targetId);
                    if (!targetSection) return; // guard

                    const isYes = this.value === 'yes';

                    if (isYes) {
                        targetSection.classList.remove('hidden');
                        void targetSection.offsetWidth;
                        targetSection.classList.remove('slide-up');
                        targetSection.classList.add('slide-down');

                        // Make all inputs in the section required
                        targetSection.querySelectorAll('input, textarea, select').forEach(input => {
                            input.required = true;
                        });
                    } else {
                        targetSection.classList.remove('slide-down');
                        targetSection.classList.add('slide-up');
                        setTimeout(() => {
                            targetSection.classList.add('hidden');
                        }, 300);

                        // Remove required and clear values
                        targetSection.querySelectorAll('input, textarea, select').forEach(input => {
                            input.required = false;
                            try {
                                input.value = '';
                            } catch (e) {}
                        });
                    }
                });
            });

            // Submit handler (demo)
            const form = document.getElementById('generalAccidentForm');
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // demo behavior - prevents actual submission
                Swal.fire({
                    icon: "success",
                    title: "Claim Submitted Successfully",
                    text: "Your claim has been submitted. Our team will review it shortly.",
                    confirmButtonText: "OK",
                    confirmButtonColor: "#2563eb",
                });
            });
        });
    </script>
</x-layouts.app>
