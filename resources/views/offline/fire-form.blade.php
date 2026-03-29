<x-layouts.offline>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <x-offline.claimant-info />

        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow px-10 py-6">
                <div class="text-center mb-6 pb-4 border-b border-gray-600">
                    <h1 class="text-2xl font-bold text-gray-900 mx-auto mt-1" style="max-width: 400px;">
                        FIRE CLAIM FORM
                    </h1>
                </div>

                <div class="bg-amber-50 border-l-4 border-amber-400 p-4 mb-8">
                    <p class="text-sm text-amber-800 leading-relaxed">
                        <strong>Please note:</strong> It is necessary that great care should be taken in completing this
                        form and the information given therein should be strictly accurate, whether it is in your favor
                        or otherwise. You should not make any payment, offer or promise of any payment or admit
                        liability in any way, as by so doing you may prejudice your position and make settlement of the
                        claim difficult.
                    </p>
                </div>

                <form id="fireClaimForm">
                    {{-- Section 1: Policy & Insured Details --}}
                    <section class="mb-10">
                        <h3
                            class="text-lg font-semibold text-gray-800 mb-4 border-b border-b-gray-300 pb-2 uppercase tracking-wider">
                            I. Policy & Insured Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Policy No.</label>
                                <x-input name="policy_no" required />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Renewal Date</label>
                                <input type="date" name="renewal_date"
                                    class="w-full px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 outline-none"
                                    required />
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name of Insured </label>
                                <x-input name="insured_name" required />
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                <x-input name="address" required />
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nature of Business</label>
                                <x-input name="nature_of_business" required />
                            </div>
                        </div>
                    </section>

                    {{-- Section 2: Incident Details --}}
                    <section class="mb-10">
                        <h3
                            class="text-lg font-semibold text-gray-800 mb-4 border-b border-b-gray-300 pb-2 uppercase tracking-wider">
                            II. Details of Loss/Incident
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date and Time of Accident
                                </label>
                                <x-input name="incident_datetime" type="datetime-local" required />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Exact Location </label>
                                <x-input name="exact_location" required />
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description of Incident </label>
                            <x-textarea name="incident_description" rows="3" required />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nature of Damage/Loss to
                                Property</label>
                            <x-textarea name="damage_nature" rows="3" required />
                        </div>
                    </section>

                    {{-- Section 3: Particulars of Claim (Page 2 Table)  --}}
                    <section class="mb-10">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                            <h3 class="text-lg font-semibold text-gray-800 uppercase tracking-wider">
                                III. Particulars of Claim
                            </h3>
                            <button type="button" onclick="addPropertyRow()"
                                class="inline-flex items-center justify-center gap-1 text-sm bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                Add Property
                            </button>
                        </div>

                        <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
                            <table class="w-full text-sm text-left border-collapse" id="propertyTable">
                                <thead class="bg-gray-50 border-b-2 border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3 font-semibold text-gray-700 w-20">Qty</th>
                                        <th class="px-4 py-3 font-semibold text-gray-700">Description</th>
                                        <th class="px-4 py-3 font-semibold text-gray-700 w-32">Price Paid</th>
                                        <th class="px-4 py-3 font-semibold text-gray-700 w-32">Depreciation</th>
                                        <th class="px-4 py-3 font-semibold text-gray-700 w-32">Claim Amt</th>
                                        <th class="px-4 py-3 w-10"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        class="property-row border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                        <td class="px-2 py-2">
                                            <input type="number" name="prop_qty[]" placeholder="0"
                                                class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                        </td>
                                        <td class="px-2 py-2">
                                            <input type="text" name="prop_desc[]" placeholder="Item description"
                                                class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                        </td>
                                        <td class="px-2 py-2">
                                            <input type="number" name="prop_price[]" placeholder="0.00" step="0.01"
                                                class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                        </td>
                                        <td class="px-2 py-2">
                                            <input type="number" name="prop_deprec[]" placeholder="0.00" step="0.01"
                                                class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                        </td>
                                        <td class="px-2 py-2">
                                            <input type="number" name="prop_claim[]" placeholder="0.00" step="0.01"
                                                class="claim-amount w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-gray-50">
                                        </td>
                                        <td class="px-2 py-2 text-center">
                                            <button type="button" onclick="removeRow(this)"
                                                class="text-red-500 hover:text-red-700 transition-colors p-1 rounded-full hover:bg-red-50">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-gray-50 border-t border-gray-200">
                                    <tr>
                                        <td colspan="4" class="px-4 py-3 text-right font-semibold text-gray-700">
                                            Total Claim Amount:</td>
                                        <td class="px-2 py-3 font-bold text-gray-900">
                                            <span id="totalClaimDisplay">0.00</span>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">* Claim amount is calculated as Price Paid -
                            Depreciation. You can edit it manually if needed.</p>
                    </section>

                    {{-- Section 4: Reports & Other Insurances --}}
                    <section class="mb-10">
                        <h3
                            class="text-lg font-semibold text-gray-800 mb-4 border-b border-b-gray-300 pb-2 uppercase tracking-wider">
                            IV. Reports & Witness Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name and address of any
                                    person injured </label>
                                <x-input name="injured_persons" />
                            </div>
                            <x-conditional-section question="Was it reported to the Police? " name="police_reported"
                                yes-section-id="policeDetails" required="true">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Police Witness/Officer
                                    Number & Evidence Details </label>
                                <x-textarea name="police_evidence" rows="2" />
                            </x-conditional-section>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Other Information Necessary
                                </label>
                                <x-textarea name="additional_info" rows="2" />
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
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Handle Conditional Sections (Police Report Toggle)
            const policeRadios = document.querySelectorAll('input[name="police_reported"]');
            const policeSection = document.getElementById('policeDetails');

            policeRadios.forEach(radio => {
                radio.addEventListener('change', (e) => {
                    if (e.target.value === 'yes') {
                        policeSection.classList.remove('hidden');
                    } else {
                        policeSection.classList.add('hidden');
                    }
                });
            });

            // 2. Form Submission Handler
            const fireForm = document.getElementById('fireClaimForm');
            if (fireForm) {
                fireForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    const data = Object.fromEntries(formData.entries());
                    console.log('Form Data Collected:', data);
                    alert('Fire claim submitted successfully for processing.');
                });
            }

            // 3. Auto-calculation setup for existing rows
            const setupRowAutoCalc = (row) => {
                const priceInput = row.querySelector('input[name="prop_price[]"]');
                const deprecInput = row.querySelector('input[name="prop_deprec[]"]');
                const claimInput = row.querySelector('input[name="prop_claim[]"]');

                if (priceInput && deprecInput) {
                    const updateClaim = () => autoCalculateClaim(row);
                    priceInput.addEventListener('input', updateClaim);
                    deprecInput.addEventListener('input', updateClaim);
                }
                if (claimInput) {
                    claimInput.addEventListener('input', () => updateTotalClaim());
                }
            };

            const rows = document.querySelectorAll('#propertyTable tbody .property-row');
            rows.forEach(row => setupRowAutoCalc(row));

            // Initial total calculation
            updateTotalClaim();
        });

        /**
         * Updates the total claim amount display
         */
        function updateTotalClaim() {
            const claimInputs = document.querySelectorAll('#propertyTable tbody input[name="prop_claim[]"]');
            let total = 0;
            claimInputs.forEach(input => {
                let val = parseFloat(input.value);
                if (!isNaN(val)) total += val;
            });
            const totalDisplay = document.getElementById('totalClaimDisplay');
            if (totalDisplay) totalDisplay.innerText = total.toFixed(2);
        }

        /**
         * Auto-calculates claim amount = price - depreciation for a given row
         * @param {HTMLElement} row - The table row element
         */
        function autoCalculateClaim(row) {
            const priceInput = row.querySelector('input[name="prop_price[]"]');
            const deprecInput = row.querySelector('input[name="prop_deprec[]"]');
            const claimInput = row.querySelector('input[name="prop_claim[]"]');

            if (priceInput && deprecInput && claimInput) {
                const price = parseFloat(priceInput.value) || 0;
                const deprec = parseFloat(deprecInput.value) || 0;
                const calculated = (price - deprec).toFixed(2);
                claimInput.value = calculated >= 0 ? calculated : 0;
                updateTotalClaim();
            }
        }

        /**
         * Adds a new row to the Particulars of Claim table
         */
        function addPropertyRow() {
            const tableBody = document.querySelector('#propertyTable tbody');
            if (!tableBody) return;

            const newRow = document.createElement('tr');
            newRow.className = 'property-row border-b border-gray-100 hover:bg-gray-50 transition-colors';
            newRow.innerHTML = `
            <td class="px-2 py-2">
                <input type="number" name="prop_qty[]" placeholder="0"
                    class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
            </td>
            <td class="px-2 py-2">
                <input type="text" name="prop_desc[]" placeholder="Item description"
                    class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
            </td>
            <td class="px-2 py-2">
                <input type="number" name="prop_price[]" placeholder="0.00" step="0.01"
                    class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
            </td>
            <td class="px-2 py-2">
                <input type="number" name="prop_deprec[]" placeholder="0.00" step="0.01"
                    class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
            </td>
            <td class="px-2 py-2">
                <input type="number" name="prop_claim[]" placeholder="0.00" step="0.01"
                    class="claim-amount w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-gray-50">
            </td>
            <td class="px-2 py-2 text-center">
                <button type="button" onclick="removeRow(this)"
                    class="text-red-500 hover:text-red-700 transition-colors p-1 rounded-full hover:bg-red-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </td>
        `;

            // Attach event listeners for auto-calculation on the new row
            const priceInput = newRow.querySelector('input[name="prop_price[]"]');
            const deprecInput = newRow.querySelector('input[name="prop_deprec[]"]');
            const claimInput = newRow.querySelector('input[name="prop_claim[]"]');

            if (priceInput && deprecInput) {
                const updateClaim = () => autoCalculateClaim(newRow);
                priceInput.addEventListener('input', updateClaim);
                deprecInput.addEventListener('input', updateClaim);
            }
            if (claimInput) {
                claimInput.addEventListener('input', () => updateTotalClaim());
            }

            tableBody.appendChild(newRow);
            updateTotalClaim();
        }

        /**
         * Removes a specific row from the table
         * @param {HTMLElement} btn - The button element that was clicked
         */
        function removeRow(btn) {
            const tableBody = document.querySelector('#propertyTable tbody');
            const row = btn.closest('tr');

            if (tableBody.children.length > 1) {
                row.remove();
            } else {
                // Clear all input values in the last row instead of removing it
                const inputs = row.querySelectorAll('input');
                inputs.forEach(input => {
                    if (input.type === 'number' || input.type === 'text') {
                        input.value = '';
                    }
                });
            }
            updateTotalClaim();
        }
    </script>
</x-layouts.offline>
