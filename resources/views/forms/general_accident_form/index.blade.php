<x-layouts.staff>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Section - Claimant Information -->
        {{-- <x-claimant-info :policy="$policy" :customer="$customer" /> --}}
        <x-claimant-info />

        <!-- Right Section - Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <!-- Header with logo and company details (matching motor/fire forms) -->
                <div class="overflow-hidden border border-gray-200">

                    {{-- Top accent bar --}}
                    {{-- <div class="h-1 bg-[#1a3a5c]"></div> --}}

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
                                    class="text-[15px] font-bold text-gray-800 tracking-wide mb-2 border-b border-b-gray-300 pb-2">
                                    Vanguard Assurance Company Ltd
                                </p>
                                <p class="text-[10px] text-gray-500 mt-0.5 tracking-widest uppercase">
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

                        <div class="border-t border-gray-200 mt-5"></div>
                    </div>

                    {{-- Document title band --}}
                    <div class="bg-[#0b529d] px-8 py-2.5 flex items-center justify-center gap-4">
                        <div class="flex-1 border-t border-white/20"></div>
                        <p class="text-[13px] font-medium tracking-widest uppercase text-white whitespace-nowrap">
                            Travel Protection Claim Form
                        </p>
                        <div class="flex-1 border-t border-white/20"></div>
                    </div>

                    {{-- Subtitle --}}
                    <div class="bg-gray-50 border-b border-gray-200 px-8 py-2 text-center">
                        <p class="text-[11.5px] text-gray-500">
                            Please complete all sections accurately. Fields marked * are required.
                        </p>
                    </div>

                </div>

                <div class="py-6 px-12">
                    <!-- Note box (original content, restyled) -->
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 rounded-lg">
                        <div class="flex items-start gap-3">
                            <div class="shrink-0"><i class="fas fa-info-circle text-blue-600 text-lg"></i></div>
                            <div class="flex-1">
                                <p class="text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">NOTE - THE
                                    ORIGINAL CLAIM FORM AND DOCUMENTATION IS REQUIRED</p>
                                <ul class="space-y-2 text-xs text-gray-600">
                                    <li class="flex items-start gap-2"><span
                                            class="text-blue-600 font-bold">•</span><span>THIS FORM MUST BE SIGNED AND
                                            DATED</span></li>
                                    <li class="flex items-start gap-2"><span
                                            class="text-blue-600 font-bold">•</span><span>A COPY OF YOUR TRAVEL
                                            INSURANCE CERTIFICATE MUST BE ATTACHED</span></li>
                                    <li class="flex items-start gap-2"><span
                                            class="text-blue-600 font-bold">•</span><span>SUPPORTING DOCUMENTATION
                                            SUBSTANTIATING THE CLAIM MUST BE SUBMITTED</span></li>
                                    <li class="flex items-start gap-2"><span
                                            class="text-blue-600 font-bold">•</span><span>ALL CLAIMS MUST BE PREPARED
                                            WITHIN 24 HOURS OF THE INCIDENT</span></li>
                                    <li class="flex items-start gap-2"><span
                                            class="text-blue-600 font-bold">•</span><span>ALL CLAIMS MUST BE SUBMITTED
                                            WITHIN 60 DAYS OF THE INCIDENT</span></li>
                                    <li class="flex items-start gap-2 mt-2 font-semibold text-gray-700"><span
                                            class="text-blue-600">•</span><span>DELAYED / LOSS OF LUGGAGE:</span></li>
                                    <li class="flex items-start gap-2 ml-6"><span
                                            class="text-gray-400">1</span><span>Kindly submit a complete baggage
                                            Inventory Claim Form from the airline</span></li>
                                    <li class="flex items-start gap-2 ml-6"><span
                                            class="text-gray-400">2</span><span>Travel Ticket</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <form id="generalAccidentForm">
                        @csrf
                        <input type="hidden" name="policy_id" value="{{ $policyId }}" />
                        <input type="hidden" name="claim_type" value="general_accident" />
                        
                        <!-- Section: THIS FORM MUST BE COMPLETED IN FULL -->
                        <section class="mb-6 border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                            <h3
                                class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2 flex items-center gap-2">
                                {{-- <i class="fas fa-file-alt text-blue-500"></i>  --}}
                                THIS FORM MUST BE COMPLETED IN FULL
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Selling Agent/Broker
                                        <span class="text-red-500">*</span></label><x-input name="agent_broker"
                                        required /></div>
                                {{-- <div><label class="block text-sm font-medium text-gray-700 mb-1">Policy Number <span
                                            class="text-red-500">*</span></label><x-input name="policy_no"
                                        :value="$policy->policy_number" readonly required /></div> --}}
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Departure Date <span
                                            class="text-red-500">*</span></label><input type="date"
                                        name="departure_date"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                        required /></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Return Date <span
                                            class="text-red-500">*</span></label><input type="date"
                                        name="return_date"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                        required /></div>
                            </div>
                        </section>

                        <!-- Section: INSURED PERSON -->
                        <section class="mb-6 border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                            <h3
                                class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2 flex items-center gap-2">
                                {{-- <i class="fas fa-user-circle text-blue-500"></i>  --}}
                                INSURED PERSON
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Surname <span
                                            class="text-red-500">*</span></label><x-input name="surname" required />
                                </div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">First Name(s) <span
                                            class="text-red-500">*</span></label><x-input name="firstname" required />
                                </div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Age <span
                                            class="text-red-500">*</span></label><x-input name="insured_age" required />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Postal Address <span
                                            class="text-red-500">*</span></label><x-input name="postal_address"
                                        required /></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Code</label><x-input
                                        name="postal_code" /></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Physical Address <span
                                            class="text-red-500">*</span></label><x-input name="physical_address"
                                        required /></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Address
                                        Code</label><x-input name="address_code" /></div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div><label
                                        class="block text-sm font-medium text-gray-700 mb-1">Business</label><x-input
                                        name="business" /></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">FAX</label><x-input
                                        name="fax" /></div>
                                <div><label
                                        class="block text-sm font-medium text-gray-700 mb-1">Res/Cell</label><x-input
                                        name="res_cell" /></div>
                            </div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Email
                                    Address</label><x-input name="email" /></div>
                        </section>

                        <!-- Conditional Sections: Contacted AAFIYA & Police Report -->
                        <section class="mb-6 border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                            <x-conditional-section question="Did you contact AAFIYA at the time of the occurrence?"
                                name="contacted_aafiya" yes-section-id="contactedAafiyaDetails" required="true">
                                <label class="block text-sm font-medium text-gray-700 mb-1">If Yes, Please provide
                                    details... <span class="text-red-500">*</span></label>
                                <x-textarea name="contacted_aafiya_details" rows="3"
                                    placeholder="Describe the details here..." required />
                            </x-conditional-section>

                            <x-conditional-section question="Was the accident reported to the police?"
                                name="police_report" yes-section-id="policeReportDetails" required="true">
                                <label class="block text-sm font-medium text-gray-700 mb-1">If Yes, Please provide
                                    details... <span class="text-red-500">*</span></label>
                                <x-textarea name="police_report_details" rows="2"
                                    placeholder="Describe the details here..." required />
                            </x-conditional-section>
                        </section>

                        <!-- Section 1 – MEDICAL AND RELATED EXPENSES -->
                        <section class="mb-6 border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                            <h3
                                class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2 flex items-center gap-2">
                                {{-- <i class="fas fa-stethoscope text-blue-500"></i>  --}}
                                SECTION 1 - MEDICAL AND RELATED EXPENSES
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Date of Illness /
                                        Injury <span class="text-red-500">*</span></label><input type="date"
                                        name="illness_date"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                        required /></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Place of Illness /
                                        Injury <span class="text-red-500">*</span></label><x-input
                                        name="place_of_illness" required /></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Cause of Illness /
                                        Injury <span class="text-red-500">*</span></label><x-input
                                        name="cause_of_illness" required /></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Diagnosis <span
                                            class="text-red-500">*</span></label><x-input name="diagnosis_sec1"
                                        required /></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Full Name of Doctor
                                        Consulted <span class="text-red-500">*</span></label><x-input
                                        name="doctor_fullname_sec1" required /></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Telephone of Doctor
                                        Consulted <span class="text-red-500">*</span></label><x-input
                                        name="doctor_telephone" required /></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Name Of Hospital
                                        Admitted To <span class="text-red-500">*</span></label><x-input
                                        name="hospital_name" required /></div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Total Amount Claimed
                                        <span class="text-red-500">*</span></label><x-input
                                        name="total_amount_claimed" required /></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Currency <span
                                            class="text-red-500">*</span></label><x-input name="currency" required />
                                </div>
                            </div>
                            <x-conditional-section
                                question="Have you previously received treatment or attention for this Illness/Condition?"
                                name="treatment_received" yes-section-id="treatment_received_details"
                                required="true">
                                <label class="block text-sm font-medium text-gray-700 mb-1">If Yes, a report from your
                                    treating doctor detailing your medical history <span
                                        class="text-red-500">*</span></label>
                                <x-textarea name="treatment_received_details" rows="3"
                                    placeholder="Describe the details here..." required />
                            </x-conditional-section>
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Have Submitted accounts
                                    been paid? <span class="text-red-500">*</span></label>
                                <div class="flex gap-4"><label class="flex items-center"><input type="radio"
                                            name="submitted_accounts_yn" value="yes" required class="mr-2">
                                        Yes</label><label class="flex items-center"><input type="radio"
                                            name="submitted_accounts_yn" value="no" required class="mr-2">
                                        No</label></div>
                            </div>
                        </section>

                        <!-- Section 2 – DETAILS OF CLAIM -->
                        <section class="mb-6 border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                            <h3
                                class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2 flex items-center gap-2">
                                {{-- <i class="fas fa-file-invoice text-blue-500"></i>  --}}
                                SECTION 2 - DETAILS OF CLAIM
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Full Name Of Subject
                                        Of Claim <span class="text-red-500">*</span></label><x-input
                                        name="claim_subject_name" required /></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Date Of Birth <span
                                            class="text-red-500">*</span></label><input type="date"
                                        name="dob_claim"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                        required /></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Date Of Illness /
                                        Injury <span class="text-red-500">*</span></label><input type="date"
                                        name="illness_date_sec2"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                        required /></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Attending Doctor's
                                        Full Name <span class="text-red-500">*</span></label><x-input
                                        name="doctor_fullname_sec2" required /></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Diagnosis <span
                                            class="text-red-500">*</span></label><x-input name="diagnosis_sec2"
                                        required /></div>
                            </div>
                            <x-conditional-section
                                question="Has the above-mentioned person suffered previously from Illness/Injury"
                                name="previous_injury" yes-section-id="previous_injury_details" required="true">
                                <label class="block text-sm font-medium text-gray-700 mb-1">If Yes, A report from the
                                    treating doctor detailing medical history is required <span
                                        class="text-red-500">*</span></label>
                                <x-textarea name="previous_injury_details" rows="3"
                                    placeholder="Describe the details here..." required />
                            </x-conditional-section>
                        </section>

                        <!-- Section 3 – ELECTRONIC FUNDS TRANSFER, DECLARATION AND AUTHORITY -->
                        <section class="mb-6 border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                            <h3
                                class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2 flex items-center gap-2">
                                {{-- <i class="fas fa-university text-blue-500"></i>  --}}
                                SECTION 3 - ELECTRONIC FUNDS
                                TRANSFER, DECLARATION AND AUTHORITY
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Account Holder's Name
                                        <span class="text-red-500">*</span></label><x-input name="account_holder_name"
                                        required /></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Account Number <span
                                            class="text-red-500">*</span></label><x-input name="account_number"
                                        required /></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Name Of Bank <span
                                            class="text-red-500">*</span></label><x-input name="bank_name" required />
                                </div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Type Of Account <span
                                            class="text-red-500">*</span></label><x-input name="account_type"
                                        required /></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Branch Name <span
                                            class="text-red-500">*</span></label><x-input name="branch_name"
                                        required /></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Branch Code <span
                                            class="text-red-500">*</span></label><x-input name="branch_code"
                                        required /></div>
                            </div>
                        </section>

                        <!-- General Section -->
                        <section class="mb-6 border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                            <h3
                                class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2 flex items-center gap-2">
                                {{-- <i class="fas fa-globe text-blue-500"></i>  --}}
                                General Section
                            </h3>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Have you incurred any
                                    travel claims in the past 5 years? If so, please supply details
                                    below:</label><x-textarea name="general_section" rows="2"
                                    placeholder="Describe the details here..." required /></div>
                        </section>

                        {{-- Image Upload Section --}}
                        <section class="mb-8">
                            <h3
                                class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2 flex items-center gap-2">
                                {{-- <i class="fas fa-camera text-blue-500"></i>  --}}
                                Add Images
                            </h3>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition cursor-pointer"
                                id="dropzone">
                                <i class="fas fa-cloud-upload-alt text-gray-400 text-4xl mb-2"></i>
                                <p class="text-gray-600">Drag & drop images here or <span
                                        class="text-blue-600 font-medium">browse</span></p>
                                <p class="text-xs text-gray-400 mt-1">Supports: JPG, PNG, GIF (max 5MB each)</p>
                                <input type="file" id="imageUpload" accept="image/jpeg,image/png,image/gif"
                                    multiple class="hidden">
                            </div>
                            <div id="imagePreviewContainer" class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3"></div>
                        </section>

                        {{-- DECLARATION --}}
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
                                <label class="flex items-start cursor-pointer"><input type="checkbox"
                                        name="declaration_agreement" required
                                        class="mt-1 w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500"><span
                                        class="ml-3 text-xs text-gray-700">I have read and understood the
                                        declaration above. I confirm that all information provided in this form is
                                        true and accurate to the best of my knowledge. <span
                                            class="text-red-500">*</span></span></label>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Date of
                                        Declaration <span class="text-red-500">*</span></label><input type="date"
                                        name="declaration_date"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg"></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Digital Signature
                                        <span class="text-xs text-gray-500">(Type your full name)</span> <span
                                            class="text-red-500">*</span></label><input type="text"
                                        name="digital_signature"
                                        placeholder="Type your full name as your digital signature"
                                        class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-cursive text-lg"
                                        style="font-family: 'Brush Script MT', cursive;">
                                    <p class="text-xs text-gray-500 mt-1">By typing your name above, you are
                                        providing a legal digital signature for this declaration.</p>
                                </div>
                            </div>
                        </section>

                        <!-- Submit Button -->
                        <div class="mt-8 pt-4 border-t border-gray-200">
                            <button type="submit"
                                class="w-full md:w-auto px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition flex items-center justify-center gap-2">
                                <span>Submit Claim</span> <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>
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
                    if (!targetSection) return;

                    const isYes = this.value === 'yes';

                    if (isYes) {
                        targetSection.classList.remove('hidden');
                        targetSection.querySelectorAll('input, textarea, select').forEach(input =>
                            input.required = true);
                    } else {
                        targetSection.classList.add('hidden');
                        targetSection.querySelectorAll('input, textarea, select').forEach(input => {
                            input.required = false;
                            try {
                                input.value = '';
                            } catch (e) {}
                        });
                    }
                });
            });

            // Submit handler (demo with SweetAlert)
            const form = document.getElementById('generalAccidentForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        icon: "success",
                        title: "Claim Submitted Successfully",
                        text: "Your travel claim has been submitted. Our team will review it shortly.",
                        confirmButtonText: "OK",
                        confirmButtonColor: "#4f46e5",
                    });
                });
            }

            // Image upload handling
            const dropzone = document.getElementById('dropzone');
            const fileInput = document.getElementById('imageUpload');
            const previewContainer = document.getElementById('imagePreviewContainer');
            let uploadedFiles = []; // store files for later submission

            function renderPreviews() {
                previewContainer.innerHTML = '';
                uploadedFiles.forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const div = document.createElement('div');
                        div.className = 'relative group';
                        div.innerHTML = `
                <img src="${e.target.result}" class="w-full h-24 object-cover rounded-lg border border-gray-200 shadow-sm">
                <button type="button" onclick="removeImage(${index})" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition">✕</button>
            `;
                        previewContainer.appendChild(div);
                    };
                    if (file) reader.readAsDataURL(file);
                });
            }

            window.removeImage = (index) => {
                uploadedFiles.splice(index, 1);
                renderPreviews();
                // Update file input's FileList (optional: you can recreate a new DataTransfer)
                const dataTransfer = new DataTransfer();
                uploadedFiles.forEach(f => dataTransfer.items.add(f));
                fileInput.files = dataTransfer.files;
            };

            dropzone.addEventListener('click', () => fileInput.click());
            dropzone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropzone.classList.add('border-blue-500', 'bg-blue-50');
            });
            dropzone.addEventListener('dragleave', () => {
                dropzone.classList.remove('border-blue-500', 'bg-blue-50');
            });
            dropzone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropzone.classList.remove('border-blue-500', 'bg-blue-50');
                const files = Array.from(e.dataTransfer.files).filter(f => f.type.startsWith('image/'));
                uploadedFiles.push(...files);
                renderPreviews();
                // Sync file input
                const dataTransfer = new DataTransfer();
                uploadedFiles.forEach(f => dataTransfer.items.add(f));
                fileInput.files = dataTransfer.files;
            });
            fileInput.addEventListener('change', (e) => {
                const newFiles = Array.from(e.target.files);
                uploadedFiles.push(...newFiles);
                renderPreviews();
            });
        });
    </script>
</x-layouts.staff>
