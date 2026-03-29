<x-layouts.app>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <x-offline.claimant-info />

        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow px-10 py-6">
                <div class="text-center mb-6 pb-4 border-b border-gray-600">
                    <h2 class="text-sm font-semibold text-gray-600 uppercase tracking-widest">Vanguard Assurance Company
                        Limited</h2>
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
                        <div class="flex items-center justify-between mb-4 border-b border-b-gray-300 pb-2">
                            <h3 class="text-lg font-semibold text-gray-800 uppercase tracking-wider">
                                III. Particulars of Claim
                            </h3>
                            <button type="button" onclick="addPropertyRow()"
                                class="text-xs bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                                + Add Property
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left border-collapse" id="propertyTable">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="border p-2 font-medium text-gray-600">Qty</th>
                                        <th class="border p-2 font-medium text-gray-600">Description </th>
                                        <th class="border p-2 font-medium text-gray-600">Price Paid</th>
                                        <th class="border p-2 font-medium text-gray-600">Depreciation</th>
                                        <th class="border p-2 font-medium text-gray-600">Claim Amt</th>
                                        <th class="border p-2"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="property-row">
                                        <td class="border p-1"><input type="number" name="prop_qty[]"
                                                class="w-full border-0 focus:ring-0"></td>
                                        <td class="border p-1"><input type="text" name="prop_desc[]"
                                                class="w-full border-0 focus:ring-0"></td>
                                        <td class="border p-1"><input type="number" name="prop_price[]"
                                                class="w-full border-0 focus:ring-0"></td>
                                        <td class="border p-1"><input type="number" name="prop_deprec[]"
                                                class="w-full border-0 focus:ring-0"></td>
                                        <td class="border p-1"><input type="number" name="prop_claim[]"
                                                class="w-full border-0 focus:ring-0"></td>
                                        <td class="border p-1 text-center"><button type="button" class="text-red-500"
                                                onclick="this.closest('tr').remove()">×</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
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

                    {{-- Final Declaration --}}
                    <section class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                        <h4 class="font-bold text-gray-900 mb-3">DECLARATION</h4>
                        <p class="text-xs text-gray-700 leading-relaxed mb-6">
                            I declare that the above statement is true in all respects to the best of my knowledge and
                            belief and I hereby leave in the hands of the
                            Company in accordance with the conditions of the Policy the conduct of all claims and
                            litigation arising out of this accident and to
                            which the Policy applies, to deal with, to prosecute and/or settle as they deem fit without
                            further reference to me; and I undertake to
                            give all such information and assistance as the Company may require.
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase">Name of Insured/Claimant
                                </label>
                                <x-input name="claimant_name_sig" required />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase">Date of Signature
                                </label>
                                <input type="date"
                                    class="w-full border-b border-gray-400 focus:border-blue-500 outline-none bg-transparent py-1"
                                    required />
                            </div>
                        </div>
                    </section>

                    <div class="mt-8 flex justify-end">
                        <button type="submit"
                            class="bg-blue-500 text-white px-8 py-3 rounded font-bold hover:bg-blue-400 transition-colors">
                            SUBMIT CLAIM
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Handle Conditional Sections (Police Report Toggle)
            // This looks for radio buttons with the name 'police_reported' 
            // and toggles the visibility of the 'policeDetails' section.
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
            fireForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Basic validation or data collection
                const formData = new FormData(this);
                const data = Object.fromEntries(formData.entries());

                console.log('Form Data Collected:', data);
                alert('Fire claim submitted successfully for processing.');
            });
        });

        /**
         * Adds a new row to the Particulars of Claim table
         */
        function addPropertyRow() {
            const tableBody = document.querySelector('#propertyTable tbody');
            const rowCount = tableBody.children.length;

            const newRow = document.createElement('tr');
            newRow.className = 'property-row';

            // Using the same structure as the initial row
            newRow.innerHTML = `
        <td class="border p-1">
            <input type="number" name="prop_qty[]" class="w-full border-0 focus:ring-0 outline-none p-1">
        </td>
        <td class="border p-1">
            <input type="text" name="prop_desc[]" class="w-full border-0 focus:ring-0 outline-none p-1">
        </td>
        <td class="border p-1">
            <input type="number" name="prop_price[]" class="w-full border-0 focus:ring-0 outline-none p-1" step="0.01">
        </td>
        <td class="border p-1">
            <input type="number" name="prop_deprec[]" class="w-full border-0 focus:ring-0 outline-none p-1" step="0.01">
        </td>
        <td class="border p-1">
            <input type="number" name="prop_claim[]" class="w-full border-0 focus:ring-0 outline-none p-1" step="0.01">
        </td>
        <td class="border p-1 text-center">
            <button type="button" 
                    class="text-red-500 hover:text-red-700 font-bold transition-colors" 
                    onclick="removeRow(this)">
                <i class="fas fa-times"></i> ×
            </button>
        </td>
    `;

            tableBody.appendChild(newRow);
        }

        /**
         * Removes a specific row from the table
         * @param {HTMLElement} btn - The button element that was clicked
         */
        function removeRow(btn) {
            const tableBody = document.querySelector('#propertyTable tbody');

            // Ensure at least one row remains
            if (tableBody.children.length > 1) {
                btn.closest('tr').remove();
            } else {
                // Clear the values instead of removing the last row
                const inputs = btn.closest('tr').querySelectorAll('input');
                inputs.forEach(input => input.value = '');
            }
        }
    </script>
</x-layouts.app>
