<x-layouts.app>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- <x-claimant-info :policy="$policy" :customer="$customer" /> --}}
        <x-claimant-info />

        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <!-- Header -->
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
                            Fire Claim Form
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
                    <!-- Note box (warning) -->
                    <div class="bg-amber-50 border-l-4 border-amber-400 p-4 mb-6 rounded-r-lg">
                        <p class="text-sm text-gray-700 leading-relaxed">
                            <strong>Please note:</strong> It is necessary that great care should be taken in completing
                            this
                            form and the information given therein should be strictly accurate, whether it is in your
                            favor
                            or otherwise. You should not make any payment, offer or promise of any payment or admit
                            liability in any way, as by so doing you may prejudice your position and make settlement of
                            the
                            claim difficult.
                        </p>
                    </div>

                    <form id="fireClaimForm">
                        @csrf
                        <input type="hidden" name="policy_id" value="{{ $policyId }}" />
                        <input type="hidden" name="claim_type" value="fire" />
                        {{-- Section 1: Policy & Insured Details --}}
                        <section class="mb-8">
                            <h3
                                class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2 flex items-center gap-2">
                                {{-- <i class="fas fa-file-contract text-blue-500"></i>  --}}
                                I. Policy & Insured Information
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Policy
                                        No.</label><x-input name="policy_no" required /></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Renewal
                                        Date</label><input type="date" name="renewal_date"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                        required /></div>
                                <div class="md:col-span-2"><label
                                        class="block text-sm font-medium text-gray-700 mb-1">Name of
                                        Insured</label><x-input name="insured_name" required /></div>
                                <div class="md:col-span-2"><label
                                        class="block text-sm font-medium text-gray-700 mb-1">Address</label><x-input
                                        name="address" required /></div>
                                <div class="md:col-span-2"><label
                                        class="block text-sm font-medium text-gray-700 mb-1">Nature of
                                        Business</label><x-input name="nature_of_business" required /></div>
                            </div>
                        </section>

                        {{-- Section 2: Incident Details --}}
                        <section class="mb-8">
                            <h3
                                class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2 flex items-center gap-2">
                                {{-- <i class="fas fa-fire text-blue-500"></i>  --}}
                                II. Details of Loss/Incident
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Date and Time of
                                        Accident</label><x-input name="incident_datetime" type="datetime-local"
                                        required /></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Exact
                                        Location</label><x-input name="exact_location" required /></div>
                            </div>
                            <div class="mb-4"><label class="block text-sm font-medium text-gray-700 mb-1">Description
                                    of Incident</label><x-textarea name="incident_description" rows="3"
                                    required /></div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Nature of Damage/Loss to
                                    Property</label><x-textarea name="damage_nature" rows="3" required /></div>
                        </section>

                        {{-- Section 3: Particulars of Claim (Table) --}}
                        <section class="mb-8">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                                <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                                    {{-- <i class="fas fa-table-list text-blue-500"></i>  --}}
                                    III. Particulars of Claim
                                </h3>
                                <button type="button" onclick="addPropertyRow()"
                                    class="inline-flex items-center gap-1 text-sm bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition shadow-sm">
                                    <i class="fas fa-plus-circle"></i> Add Property
                                </button>
                            </div>

                            <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
                                <table class="w-full text-sm text-left border-collapse" id="propertyTable">
                                    <thead class="bg-gray-50 border-b border-gray-200">
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
                                        <tr class="property-row border-b border-gray-100 hover:bg-gray-50 transition">
                                            <td class="px-2 py-2"><input type="number" name="prop_qty[]"
                                                    placeholder="0"
                                                    class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                            </td>
                                            <td class="px-2 py-2"><input type="text" name="prop_desc[]"
                                                    placeholder="Item description"
                                                    class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                            </td>
                                            <td class="px-2 py-2"><input type="number" name="prop_price[]"
                                                    placeholder="0.00" step="0.01"
                                                    class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                            </td>
                                            <td class="px-2 py-2"><input type="number" name="prop_deprec[]"
                                                    placeholder="0.00" step="0.01"
                                                    class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                            </td>
                                            <td class="px-2 py-2"><input type="number" name="prop_claim[]"
                                                    placeholder="0.00" step="0.01"
                                                    class="claim-amount w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-gray-50">
                                            </td>
                                            <td class="px-2 py-2 text-center"><button type="button"
                                                    onclick="removeRow(this)"
                                                    class="text-red-500 hover:text-red-700 transition p-1 rounded-full hover:bg-red-50"><i
                                                        class="fas fa-trash-alt"></i></button></td>
                                        </tr>
                                    </tbody>
                                    <tfoot class="bg-gray-50 border-t border-gray-200">
                                        <tr>
                                            <td colspan="4"
                                                class="px-4 py-3 text-right font-semibold text-gray-700">Total Claim
                                                Amount:</td>
                                            <td class="px-2 py-3 font-bold text-gray-900"><span
                                                    id="totalClaimDisplay">0.00</span></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">* Claim amount is calculated as Price Paid –
                                Depreciation. You can edit it manually if needed.</p>
                        </section>

                        {{-- Section 4: Reports & Witness Information --}}
                        <section class="mb-8">
                            <h3
                                class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2 flex items-center gap-2">
                                {{-- <i class="fas fa-flag-checkered text-blue-500"></i>  --}}
                                IV. Reports & Witness Information
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div class="md:col-span-2"><label
                                        class="block text-sm font-medium text-gray-700 mb-1">Name and address of any
                                        person injured</label><x-input name="injured_persons" /></div>
                                <x-conditional-section question="Was it reported to the Police?"
                                    name="police_reported" yes-section-id="policeDetails" required="true">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Police Witness/Officer
                                        Number & Evidence Details</label>
                                    <x-textarea name="police_evidence" rows="2" />
                                </x-conditional-section>
                                <div class="md:col-span-2"><label
                                        class="block text-sm font-medium text-gray-700 mb-1">Other Information
                                        Necessary</label><x-textarea name="additional_info" rows="2" /></div>
                            </div>
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
        // ==================== PROPERTY TABLE ====================
        function setupRowAutoCalc(row) {
            const priceInput = row.querySelector('input[name="prop_price[]"]');
            const deprecInput = row.querySelector('input[name="prop_deprec[]"]');
            const claimInput = row.querySelector('input[name="prop_claim[]"]');
            if (priceInput && deprecInput) {
                const update = () => autoCalculateClaim(row);
                priceInput.addEventListener('input', update);
                deprecInput.addEventListener('input', update);
            }
            if (claimInput) claimInput.addEventListener('input', () => updateTotalClaim());
        }

        function updateTotalClaim() {
            let total = 0;
            document.querySelectorAll('#propertyTable tbody input[name="prop_claim[]"]').forEach(input => {
                const v = parseFloat(input.value);
                if (!isNaN(v)) total += v;
            });
            document.getElementById('totalClaimDisplay').innerText = total.toFixed(2);
        }

        function autoCalculateClaim(row) {
            const price = parseFloat(row.querySelector('input[name="prop_price[]"]').value) || 0;
            const deprec = parseFloat(row.querySelector('input[name="prop_deprec[]"]').value) || 0;
            const claimInput = row.querySelector('input[name="prop_claim[]"]');
            const calculated = (price - deprec).toFixed(2);
            claimInput.value = calculated >= 0 ? calculated : 0;
            updateTotalClaim();
        }

        function addPropertyRow() {
            const tbody = document.querySelector('#propertyTable tbody');
            const newRow = document.createElement('tr');
            newRow.className = 'property-row border-b border-gray-100 hover:bg-gray-50 transition';
            newRow.innerHTML = `
            <td class="px-2 py-2"><input type="number" name="prop_qty[]" placeholder="0" class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"></td>
            <td class="px-2 py-2"><input type="text" name="prop_desc[]" placeholder="Item description" class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"></td>
            <td class="px-2 py-2"><input type="number" name="prop_price[]" placeholder="0.00" step="0.01" class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"></td>
            <td class="px-2 py-2"><input type="number" name="prop_deprec[]" placeholder="0.00" step="0.01" class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"></td>
            <td class="px-2 py-2"><input type="number" name="prop_claim[]" placeholder="0.00" step="0.01" class="claim-amount w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-gray-50"></td>
            <td class="px-2 py-2 text-center"><button type="button" onclick="removeRow(this)" class="text-red-500 hover:text-red-700 transition p-1 rounded-full hover:bg-red-50"><i class="fas fa-trash-alt"></i></button></td>
        `;
            tbody.appendChild(newRow);
            setupRowAutoCalc(newRow);
            updateTotalClaim();
        }

        function removeRow(btn) {
            const row = btn.closest('tr');
            const tbody = row.parentElement;
            if (tbody.children.length > 1) row.remove();
            else row.querySelectorAll('input').forEach(inp => inp.value = '');
            updateTotalClaim();
        }

        // ==================== IMAGE UPLOAD ====================
        let uploadedFiles = [];

        function renderPreviews() {
            const previewContainer = document.getElementById('imagePreviewContainer');
            previewContainer.innerHTML = '';
            uploadedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const div = document.createElement('div');
                    div.className = 'relative group';
                    div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-24 object-cover rounded-lg border border-gray-200 shadow-sm">
                    <button type="button" onclick="removeImage(${index})"
                        class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition">✕</button>
                `;
                    previewContainer.appendChild(div);
                };
                if (file) reader.readAsDataURL(file);
            });
        }

        window.removeImage = function(index) {
            uploadedFiles.splice(index, 1);
            renderPreviews();
            const dt = new DataTransfer();
            uploadedFiles.forEach(f => dt.items.add(f));
            document.getElementById('imageUpload').files = dt.files;
        };

        document.addEventListener('DOMContentLoaded', function() {

            // ==================== POLICE CONDITIONAL ====================
            const policeRadios = document.querySelectorAll('input[name="police_reported"]');
            const policeSection = document.getElementById('policeDetails');

            policeRadios.forEach(radio => {
                radio.addEventListener('change', (e) => {
                    policeSection.classList.toggle('hidden', e.target.value !== 'yes');
                });
            });

            // ==================== PROPERTY TABLE INIT ====================
            document.querySelectorAll('#propertyTable tbody .property-row').forEach(row => setupRowAutoCalc(row));
            updateTotalClaim();

            // ==================== IMAGE UPLOAD INIT ====================
            const dropzone = document.getElementById('dropzone');
            const fileInput = document.getElementById('imageUpload');

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
                const files = Array.from(e.dataTransfer.files).filter(f => f.type.startsWith('image/'));
                uploadedFiles.push(...files);
                renderPreviews();
            });
            fileInput?.addEventListener('change', (e) => {
                uploadedFiles.push(...Array.from(e.target.files));
                renderPreviews();
            });

            // ==================== PRE-FILL FROM POLICY ====================
            @if ($policy)
                const prefill = {
                    'policy_no': '{{ $policy->policy_number ?? '' }}',
                    'insured_name': '{{ $policy->insured_name ?? '' }}',
                    'renewal_date': '{{ $policy->renewal_date ? \Carbon\Carbon::parse($policy->renewal_date)->format('Y-m-d') : '' }}',
                };
                Object.entries(prefill).forEach(([name, value]) => {
                    const el = document.querySelector(`[name="${name}"]`);
                    if (el && value) el.value = value;
                });
            @endif

            // ==================== FORM SUBMISSION ====================
            document.getElementById('fireClaimForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                if (!isChecked('declaration_agreement')) {
                    showClaimError('Please read and accept the declaration before submitting.');
                    return;
                }

                if (!val('digital_signature').trim()) {
                    showClaimError('Please provide your digital signature before submitting.');
                    return;
                }

                const formData = {
                    policy_id: val('policy_id') || '{{ $policyId }}',
                    claim_type: 'fire',
                    form_data: {
                        // Section 1 — Policy & Insured
                        policy_no: val('policy_no'),
                        renewal_date: val('renewal_date'),
                        insured_name: val('insured_name'),
                        address: val('address'),
                        nature_of_business: val('nature_of_business'),

                        // Section 2 — Incident
                        incident_datetime: val('incident_datetime'),
                        exact_location: val('exact_location'),
                        incident_description: val('incident_description'),
                        damage_nature: val('damage_nature'),

                        // Section 3 — Property items
                        property_items: collectPropertyRows(),

                        // Section 4 — Reports & Witness
                        injured_persons: val('injured_persons'),
                        police_reported: checked('police_reported'),
                        police_evidence: val('police_evidence'),
                        additional_info: val('additional_info'),

                        // Declaration
                        declaration_date: val('declaration_date'),
                        digital_signature: val('digital_signature'),
                        declaration_agreement: isChecked('declaration_agreement'),
                    }
                };

                await submitClaim('fireClaimForm', formData);
            });
        });
    </script>
</x-layouts.app>
