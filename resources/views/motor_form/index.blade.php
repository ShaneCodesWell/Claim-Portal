<x-layouts.app>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Section - Claimant Information -->
        <x-claimant-info />

        <!-- Right Section - Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <h1 class="text-2xl font-bold text-gray-900 text-center mb-3 pb-3 border-b border-gray-600 mx-auto"
                    style="max-width: 400px;">
                    MOTOR ACCIDENT REPORT FORM
                </h1>

                <div class="pb-4 mb-6">
                    <p class="text-sm text-gray-700 leading-relaxed">
                        Please note, it is necessary that great care should be taken in
                        completing this form and the information given therein should be
                        strictly accurate, whether it is in your favor or otherwise. You
                        should not make any payment, offer or promise of any payment or
                        admit liability in any way, as by so doing you may prejudice
                        your position and make settlement of the claim difficult.
                    </p>
                </div>

                <form id="claimForm">
                    <section>
                        <!-- Particulars of Motor Vehicle Section -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-b-gray-300 pb-2">
                                PARTICULARS OF MOTOR VEHICLE CONCERNED:
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Registration Number
                                        <span class="text-red-500">*</span></label>
                                    <input type="text" required
                                        class="w-full px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Make <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" required
                                        class="w-full px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Model <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" required
                                        class="w-full px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Year of Make <span
                                            class="text-red-500">*</span></label>
                                    <input type="date" required
                                        class="w-full px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                                </div>
                            </div>
                        </div>

                        <!-- Hire Purchase/Loan Agreement Section -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Is the vehicle the subject of a hire purchase or loan
                                agreement? <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-4 mb-3">
                                <label class="flex items-center">
                                    <input type="radio" name="hirePurchase" value="yes" required class="mr-2" />
                                    <span class="text-sm text-gray-700">Yes</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="hirePurchase" value="no" required class="mr-2" />
                                    <span class="text-sm text-gray-700">No</span>
                                </label>
                            </div>

                            <div id="financeCompanySection" class="hidden overflow-hidden mt-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    If so, state the name of the finance company or lending
                                    organization <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="financeCompany"
                                    class="w-full px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                            </div>
                        </div>

                        <!-- Purpose of Vehicle Use -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                State fully the purpose for which the vehicle was being used.
                                <span class="text-red-500">*</span>
                                <span class="text-xs text-gray-500 block mt-1">(It is not sufficient to state "Business"
                                    or
                                    "Private")</span>
                            </label>
                            <textarea required rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none outline-none"
                                placeholder="Please provide detailed description of vehicle usage..."></textarea>
                        </div>

                        <!-- Vehicle Consent Section -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Was the vehicle being used with your consent?
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-4">
                                <label class="flex items-center">
                                    <input type="radio" name="vehicleConsent" value="yes" required
                                        class="mr-2" />
                                    <span class="text-sm text-gray-700">Yes</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="vehicleConsent" value="no" required
                                        class="mr-2" />
                                    <span class="text-sm text-gray-700">No</span>
                                </label>
                            </div>
                        </div>
                    </section>

                    <section>
                        <!-- Particulars of Person Driving Vehicle Section -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-b-gray-300 pb-2">
                                PARTICULARS OF PERSON DRIVING AT THE TIME OF ACCIDENT:
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name
                                        <span class="text-red-500">*</span></label>
                                    <input type="text" required
                                        class="w-full px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Address<span
                                            class="text-red-500">*</span></label>
                                    <input type="text" required
                                        class="w-full px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Age<span
                                            class="text-red-500">*</span></label>
                                    <input type="text" required
                                        class="w-full px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Occupation <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" required
                                        class="w-full px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Telephone <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" required
                                        class="w-full px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Driving Licence No.
                                        <span class="text-red-500">*</span></label>
                                    <input type="text" required
                                        class="w-full px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Issue <span
                                            class="text-red-500">*</span></label>
                                    <input type="date" required
                                        class="w-full px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                                </div>
                            </div>
                        </div>

                        <!-- Who was driving? -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Who was driving at the time of accident? <span class="text-red-500">*</span>
                            </label>
                            <select required
                                class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                                <option value="">Select driver</option>
                                <option value="self">Myself</option>
                                <option value="spouse">Spouse</option>
                                <option value="family_member">Family Member</option>
                                <option value="employee">Employee</option>
                                <option value="friend">Friend</option>
                                <option value="other">Other Person</option>
                            </select>
                        </div>

                        <!-- Driver Details -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1 mt-4">
                                State name and address of the insurer of the person driving and number of the motor
                                vehicle Policy held by
                                him/her <span class="text-red-500">*</span>
                            </label>
                            <textarea required rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none outline-none"
                                placeholder="Please provide detailed description of vehicle usage..."></textarea>
                        </div>

                    </section>


                    <!-- Submit Button -->
                    <div class="mt-8 pt-4 border-t border-t-gray-300">
                        <button type="submit"
                            class="w-full md:w-auto px-6 py-2 bg-blue-600 text-white font-medium rounded hover:bg-blue-700 transition-colors">
                            Continue to Next Section
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        // Show/hide finance company field with smooth animation
        document.addEventListener("DOMContentLoaded", function() {
            const hirePurchaseYes = document.querySelector(
                'input[name="hirePurchase"][value="yes"]'
            );
            const hirePurchaseNo = document.querySelector(
                'input[name="hirePurchase"][value="no"]'
            );
            const financeSection = document.getElementById("financeCompanySection");
            const financeInput = document.getElementById("financeCompany");

            hirePurchaseYes.addEventListener("change", function() {
                if (this.checked) {
                    financeSection.classList.remove("hidden");
                    // Trigger reflow to restart animation
                    void financeSection.offsetWidth;
                    financeSection.classList.remove("slide-up");
                    financeSection.classList.add("slide-down");
                    financeInput.required = true;
                }
            });

            hirePurchaseNo.addEventListener("change", function() {
                if (this.checked) {
                    financeSection.classList.remove("slide-down");
                    financeSection.classList.add("slide-up");
                    // Wait for animation to complete before hiding
                    setTimeout(() => {
                        financeSection.classList.add("hidden");
                    }, 300);
                    financeInput.required = false;
                    financeInput.value = "";
                }
            });
        });

        document
            .getElementById("claimForm")
            .addEventListener("submit", function(e) {
                e.preventDefault();
                alert(
                    "Form submitted successfully! (This is a UI demo - no backend connected)"
                );
            });
    </script>
</x-layouts.app>
