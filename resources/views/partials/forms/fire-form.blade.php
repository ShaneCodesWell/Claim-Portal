@php
    $f = $formData ?? [];
    $isStaff = ($context ?? 'customer') === 'staff';
    $isEdit = !is_null($claim ?? null);
    $policyId = $policyId ?? ($policy->external_policy_id ?? '');
    $customer = App\Models\Customer::findOrFail($policy->customer_id);

    $propertyItems = json_decode($f['property_items'] ?? '[]', true);
    $propertyItems = is_array($propertyItems) ? $propertyItems : [];
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <!-- Header -->
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
                Fire Claim Form
            </p>
            <div class="flex-1 border-t border-white/20"></div>
        </div>

        {{-- Subtitle --}}
        <div class="bg-gray-50 border-b border-gray-200 px-4 sm:px-6 md:px-8 py-2 text-center">
            <p class="text-[11.5px] text-gray-500">
                Please complete all sections accurately. Fields marked * are required.
            </p>
        </div>

    </div>

    <div class="py-4 px-4 sm:px-6 md:px-8 lg:px-12">
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

        <form id="fireClaimForm" data-action="{{ $action }}">
            @csrf
            @if ($method === 'PUT')
                @method('PUT')
            @endif
            <input type="hidden" name="claim_type" value="fire" />
            <input type="hidden" name="policy_id" value="{{ $policy->external_policy_id ?? $policy->id }}">
            <input type="hidden" name="risk_id" value="{{ $riskId ?? '' }}">
            {{-- Section 1: Policy & Insured Details --}}
            <section class="mb-8">
                <h3
                    class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2 flex items-center gap-2">
                    I. Policy & Insured Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Policy No.
                        </label>
                        <input type="text" name="policy_no"
                            value="{{ $f['policy_no'] ?? ($policy->policy_number ?? '') }}"
                            class="w-full bg-transparent border-0 p-0 text-gray-700 font-medium focus:outline-none focus:ring-0"
                            readonly required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Renewal Date
                        </label>
                        <input type="date" name="renewal_date"
                            value="{{ $f['renewal_date'] ?? ($policy->renewal_date ? \Carbon\Carbon::parse($policy->renewal_date)->format('Y-m-d') : '') }}"
                            class="w-full bg-transparent border-0 p-0 text-gray-700 font-medium focus:outline-none focus:ring-0"
                            readonly required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Name of Insured
                        </label>
                        <input type="text" name="fullname" value="{{ $f['fullname'] ?? '' }}" readonly
                            class="w-full bg-transparent border-0 p-0 text-gray-900 font-medium focus:outline-none focus:ring-0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            E-Mail Address
                        </label>
                        <input type="text" name="email" value="{{ $f['email'] ?? '' }}" readonly
                            class="w-full bg-transparent border-0 p-0 text-gray-900 font-medium focus:outline-none focus:ring-0">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nature of
                            Business</label>
                        <x-input name="nature_of_business" value="{{ $f['nature_of_business'] ?? '' }}" class="w-full"
                            required />
                    </div>
                </div>
            </section>

            {{-- Section 2: Incident Details --}}
            <section class="mb-8">
                <h3
                    class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2 flex items-center gap-2">
                    II. Details of Loss/Incident
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Date and Time of
                            Accident</label><x-input name="incident_datetime"
                            value="{{ $f['incident_datetime'] ?? '' }}" type="datetime-local" class="w-full" required />
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Exact
                            Location</label><x-input name="exact_location" value="{{ $f['exact_location'] ?? '' }}"
                            class="w-full" required /></div>
                </div>
                <div class="mb-4"><label class="block text-sm font-medium text-gray-700 mb-1">Description
                        of Incident</label><x-textarea name="incident_description"
                        value="{{ $f['incident_description'] ?? '' }}" rows="3" class="w-full" required /></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Nature of Damage/Loss to
                        Property</label><x-textarea name="damage_nature" value="{{ $f['damage_nature'] ?? '' }}"
                        rows="3" class="w-full" required /></div>
            </section>

            {{-- Section 3: Particulars of Claim (Table) --}}
            <section class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
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
                            @forelse($propertyItems as $item)
                                <tr class="property-row border-b border-gray-100 hover:bg-gray-50 transition">
                                    <td class="px-2 py-2"><input type="number" name="prop_qty[]"
                                            value="{{ $item['qty'] ?? '' }}" placeholder="0"
                                            class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                    </td>
                                    <td class="px-2 py-2"><input type="text" name="prop_desc[]"
                                            value="{{ $item['description'] ?? '' }}" placeholder="Item description"
                                            class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                    </td>
                                    <td class="px-2 py-2"><input type="number" name="prop_price[]"
                                            value="{{ $item['price_paid'] ?? '' }}" placeholder="0.00"
                                            step="0.01"
                                            class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                    </td>
                                    <td class="px-2 py-2"><input type="number" name="prop_deprec[]"
                                            value="{{ $item['depreciation'] ?? '' }}" placeholder="0.00"
                                            step="0.01"
                                            class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                    </td>
                                    <td class="px-2 py-2"><input type="number" name="prop_claim[]"
                                            value="{{ $item['claim_amount'] ?? '' }}" placeholder="0.00"
                                            step="0.01"
                                            class="claim-amount w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-gray-50">
                                    </td>
                                    <td class="px-2 py-2 text-center"><button type="button"
                                            onclick="removeRow(this)"
                                            class="text-red-500 hover:text-red-700 transition p-1 rounded-full hover:bg-red-50"><i
                                                class="fas fa-trash-alt"></i></button></td>
                                </tr>
                            @empty
                                <tr class="property-row border-b border-gray-100 hover:bg-gray-50 transition">
                                    <td class="px-2 py-2"><input type="number" name="prop_qty[]" placeholder="0"
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
                            @endforelse
                        </tbody>
                        <tfoot class="bg-gray-50 border-t border-gray-200">
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-right font-semibold text-gray-700">Total
                                    Claim
                                    Amount:</td>
                                <td class="px-2 py-3 font-bold text-gray-900"><span id="totalClaimDisplay">0.00</span>
                                </td>
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
                    IV. Reports & Witness Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">Name and
                            address of any
                            person injured</label><x-input name="injured_persons"
                            value="{{ $f['injured_persons'] ?? '' }}" class="w-full" /></div>
                    <div class="md:col-span-2 mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Was it reported to the Police?
                            <span class="text-red-500">*</span></label>
                        <div class="flex flex-wrap gap-4">
                            <label class="flex items-center">
                                <input type="radio" name="police_reported" value="yes"
                                    class="conditional-radio mr-2" data-target="policeDetails" required
                                    {{ ($f['police_reported'] ?? '') === 'yes' ? 'checked' : '' }}>
                                <span>Yes</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="police_reported" value="no"
                                    class="conditional-radio mr-2" data-target="policeDetails" required
                                    {{ ($f['police_reported'] ?? '') === 'no' ? 'checked' : '' }}>
                                <span>No</span>
                            </label>
                        </div>
                        <div id="policeDetails"
                            class="{{ ($f['police_reported'] ?? '') === 'yes' ? '' : 'hidden' }} mt-3 pl-4 border-l-2 border-blue-200">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Police Witness/Officer
                                Number & Evidence Details</label>
                            <x-textarea name="police_evidence" value="{{ $f['police_evidence'] ?? '' }}"
                                rows="2" class="w-full" />
                        </div>
                    </div>
                    <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">Other
                            Information
                            Necessary</label><x-textarea name="additional_info"
                            value="{{ $f['additional_info'] ?? '' }}" rows="2" class="w-full" />
                    </div>
                </div>
            </section>

            {{-- Image Upload Section --}}
            <section class="mb-8">
                <h3
                    class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2 flex items-center gap-2">
                    Add Images
                </h3>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition cursor-pointer"
                    id="dropzone">
                    <i class="fas fa-cloud-upload-alt text-gray-400 text-4xl mb-2"></i>
                    <p class="text-gray-600">Drag & drop images here or <span
                            class="text-blue-600 font-medium">browse</span></p>
                    <p class="text-xs text-gray-400 mt-1">Supports: JPG, PNG, PDF (max 5MB each)</p>
                    <input type="file" id="imageUpload" accept="image/jpeg,image/png,image/gif,application/pdf"
                        multiple class="hidden">
                </div>
                <div id="imagePreviewContainer" class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
            </section>

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
                                    <span class="text-xs text-gray-400">{{ number_format($doc->file_size / 1024, 1) }}
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
                            placeholder="e.g. Customer called to correct property description"
                            class="w-full px-3 py-2 border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-400 outline-none bg-white">
                    </div>
                </section>
            @endif

            {{-- ── DECLARATION (customer only) ── --}}
            @if (!$isStaff)
                <section class="mb-8">
                    <h3
                        class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2 flex items-center gap-2">
                        <i class="fas fa-file-signature text-blue-500"></i> DECLARATION
                    </h3>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                        <p class="text-xs text-gray-700 leading-relaxed mb-4">
                            I declare that the above statement is true in all respects to the best of my knowledge and
                            belief
                            and I hereby leave in the hands of the Company in accordance with the conditions of the
                            Policy
                            the conduct of all claims and litigation arising out of this accident and to which the
                            Policy
                            applies, to deal with, to prosecute and/or settle as they deem fit without further reference
                            to me;
                            and I undertake to give all such information and assistance as the Company may require.
                        </p>
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 rounded">
                            <p class="text-sm text-gray-700"><span class="font-semibold">Note:</span> The Company does
                                not
                                admit liability by the issue of this form.</p>
                        </div>
                    </div>
                    <div class="bg-white border-2 border-blue-200 rounded-lg p-4 mb-6">
                        <label class="flex items-start cursor-pointer">
                            <input type="checkbox" name="declaration_agreement" required
                                class="mt-1 w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                                {{ !empty($f['declaration_agreement']) ? 'checked' : '' }}>
                            <span class="ml-3 text-xs text-gray-700">I have read and understood the declaration above.
                                I confirm that all information provided in this form is true and accurate to the best of
                                my knowledge.
                                <span class="text-red-500">*</span></span>
                        </label>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date of Declaration <span
                                    class="text-red-500">*</span></label>
                            <input type="date" name="declaration_date" value="{{ $f['declaration_date'] ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Digital Signature
                                <span class="text-xs text-gray-500">(Type your full name)</span>
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="digital_signature"
                                value="{{ $f['digital_signature'] ?? '' }}"
                                placeholder="Type your full name as your digital signature"
                                class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-lg"
                                style="font-family: 'Brush Script MT', cursive;">
                            <p class="text-xs text-gray-500 mt-1">By typing your name above, you are providing a legal
                                digital signature for this declaration.</p>
                        </div>
                    </div>
                </section>
            @endif

            {{-- ── ACTION BUTTONS ── --}}
            <div
                class="mt-8 pt-4 border-t border-gray-200 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
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
    const isEdit = {{ $isEdit ? 'true' : 'false' }};
    const isStaff = {{ $isStaff ? 'true' : 'false' }};

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

    function collectPropertyRows() {
        const rows = document.querySelectorAll('#propertyTable tbody .property-row');
        return Array.from(rows).map(row => ({
            qty: row.querySelector('[name="prop_qty[]"]')?.value || '',
            description: row.querySelector('[name="prop_desc[]"]')?.value || '',
            price_paid: row.querySelector('[name="prop_price[]"]')?.value || '',
            depreciation: row.querySelector('[name="prop_deprec[]"]')?.value || '',
            claim_amount: row.querySelector('[name="prop_claim[]"]')?.value || '',
        })).filter(item => item.description || item.qty || item.price_paid || item.claim_amount);
    }

    // ==================== FILE UPLOAD ====================
    let uploadedFiles = [];
    const MAX_FILE_SIZE_MB = 5;
    const MAX_FILE_COUNT = 10;
    const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];

    function renderPreviews() {
        const previewContainer = document.getElementById('imagePreviewContainer');
        previewContainer.innerHTML = '';
        uploadedFiles.forEach((file, index) => {
            const div = document.createElement('div');
            div.className = 'relative group border border-gray-200 rounded-lg overflow-hidden bg-gray-50';
            if (file.type === 'application/pdf') {
                div.innerHTML =
                    `
                <div class="w-full h-24 flex flex-col items-center justify-center gap-1 bg-red-50">
                    <i class="fas fa-file-pdf text-red-500 text-3xl"></i>
                    <span class="text-xs text-gray-500 truncate px-2 w-full text-center">${file.name}</span>
                </div>
                <button type="button" onclick="removeImage(${index})"
                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition">✕</button>`;
            } else {
                const reader = new FileReader();
                reader.onload = (e) => {
                    div.innerHTML =
                        `
                    <img src="${e.target.result}" class="w-full h-24 object-cover">
                    <button type="button" onclick="removeImage(${index})"
                        class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition">✕</button>`;
                };
                reader.readAsDataURL(file);
            }
            previewContainer.appendChild(div);
        });
    }

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
        if (errors.length) showClaimError(errors.join('\n'));
        renderPreviews();
    }

    window.removeImage = function(index) {
        uploadedFiles.splice(index, 1);
        renderPreviews();
    };

    document.addEventListener('DOMContentLoaded', function() {

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

        // ==================== PROPERTY TABLE INIT ====================
        document.querySelectorAll('#propertyTable tbody .property-row').forEach(row => setupRowAutoCalc(row));
        updateTotalClaim();

        // ==================== FILE UPLOAD INIT ====================
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
            addFiles(Array.from(e.dataTransfer.files));
        });
        fileInput?.addEventListener('change', (e) => {
            addFiles(Array.from(e.target.files));
            e.target.value = '';
        });

        // ==================== PRE-FILL FROM POLICY ====================
        @if ($policy)
            const prefill = {
                'policy_no': '{{ $policy->policy_number ?? '' }}',
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

            if (!isChecked('declaration_agreement') && !isStaff) {
                showClaimError('Please read and accept the declaration before submitting.');
                return;
            }

            if (!isStaff && !val('digital_signature').trim()) {
                showClaimError('Please provide your digital signature before submitting.');
                return;
            }

            const formData = new FormData();
            if (isEdit) formData.append('_method', 'PUT');
            formData.append('claim_type', 'fire');
            formData.append('_token', document.querySelector('meta[name="csrf-token"]')
                .getAttribute('content'));
            formData.append('policy_id', val('policy_id') || '{{ $policyId }}');

            const claimFields = {
                policy_no: val('policy_no'),
                renewal_date: val('renewal_date'),
                fullname: val('fullname'),
                email: val('email'),
                address: val('address'),
                nature_of_business: val('nature_of_business'),
                incident_datetime: val('incident_datetime'),
                exact_location: val('exact_location'),
                incident_description: val('incident_description'),
                damage_nature: val('damage_nature'),
                property_items: collectPropertyRows(),
                injured_persons: val('injured_persons'),
                police_reported: checked('police_reported'),
                police_evidence: val('police_evidence'),
                additional_info: val('additional_info'),
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

            if (isStaff) {
                const note = val('note');
                if (note) formData.append('note', note);
            }

            uploadedFiles.forEach((file, index) => {
                formData.append(`documents[${index}]`, file, file.name);
            });

            document.querySelectorAll('[id^="delete-doc-"]:not([disabled])').forEach(input => {
                formData.append('delete_documents[]', input.value);
            });

            const action = document.getElementById('fireClaimForm').dataset.action;
            await submitClaimWithFiles('fireClaimForm', formData, action);
        });
    });
</script>
