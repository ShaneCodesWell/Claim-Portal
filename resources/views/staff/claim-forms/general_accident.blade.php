{{-- general_accident.blade.php --}}
<div class="space-y-6">
    {{-- NOTE / HEADER --}}
    <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-md text-sm text-amber-800">
        <p class="font-semibold">⚠️ THE ORIGINAL CLAIM FORM AND DOCUMENTATION IS REQUIRED</p>
        <ul class="list-disc list-inside text-xs mt-1 space-y-0.5">
            <li>This form must be signed and dated</li>
            <li>A copy of your travel insurance certificate must be attached</li>
            <li>Supporting documentation substantiating the claim must be submitted</li>
            <li>All claims must be prepared within 24 hours of the incident</li>
            <li>All claims must be submitted within 60 days of the incident</li>
            <li>Delayed / Loss of luggage: submit airline baggage inventory form + travel ticket</li>
        </ul>
    </div>

    {{-- SELLING AGENT / BROKER & POLICY PERIOD --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 border-b pb-4">
        <div>
            <label class="block text-xs font-semibold text-gray-500">Selling Agent/Broker</label>
            <p class="text-sm text-gray-800 mt-1">{{ $form['selling_agent'] ?? '—' }}</p>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500">Policy Number</label>
            <p class="text-sm font-mono text-gray-800 mt-1">
                {{ $form['policy_number'] ?? ($claim->policy->policy_number ?? '—') }}</p>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500">Departure Date</label>
            <p class="text-sm text-gray-800 mt-1">{{ $form['departure_date'] ?? '—' }}</p>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500">Return Date</label>
            <p class="text-sm text-gray-800 mt-1">{{ $form['return_date'] ?? '—' }}</p>
        </div>
    </div>

    {{-- INSURED PERSON --}}
    <div class="border-b pb-4">
        <h4 class="text-sm font-bold text-gray-700 mb-3">INSURED PERSON</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-xs text-gray-500">Surname</label>
                <p class="text-sm font-medium">{{ $form['insured_surname'] ?? '—' }}</p>
            </div>
            <div>
                <label class="text-xs text-gray-500">Age</label>
                <p class="text-sm font-medium">{{ $form['insured_age'] ?? '—' }}</p>
            </div>
            <div>
                <label class="text-xs text-gray-500">First Name(s)</label>
                <p class="text-sm font-medium">{{ $form['insured_first_names'] ?? '—' }}</p>
            </div>
            <div>
                <label class="text-xs text-gray-500">Code</label>
                <p class="text-sm font-medium">{{ $form['insured_code'] ?? '—' }}</p>
            </div>
            <div class="md:col-span-2">
                <label class="text-xs text-gray-500">Postal Address</label>
                <p class="text-sm">{{ $form['postal_address'] ?? '—' }}</p>
            </div>
            <div class="md:col-span-2">
                <label class="text-xs text-gray-500">Physical Address</label>
                <p class="text-sm">{{ $form['physical_address'] ?? '—' }}</p>
            </div>
            <div>
                <label class="text-xs text-gray-500">Telephone (Bus)</label>
                <p class="text-sm">{{ $form['tel_bus'] ?? '—' }}</p>
            </div>
            <div>
                <label class="text-xs text-gray-500">Fax</label>
                <p class="text-sm">{{ $form['tel_fax'] ?? '—' }}</p>
            </div>
            <div>
                <label class="text-xs text-gray-500">Res/Cell</label>
                <p class="text-sm">{{ $form['tel_mobile'] ?? '—' }}</p>
            </div>
            <div>
                <label class="text-xs text-gray-500">Email Address</label>
                <p class="text-sm">{{ $form['email'] ?? '—' }}</p>
            </div>
        </div>
    </div>

    {{-- CONTACT AAFIYA / OTHER INSURER --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-b pb-4">
        <div>
            <label class="text-xs text-gray-500 block">Did you contact AAFIYA at time of occurrence?</label>
            <p class="text-sm font-medium mt-1">
                {{ isset($form['contacted_aafiya']) ? ($form['contacted_aafiya'] ? 'Yes' : 'No') : '—' }}</p>
            @if (!empty($form['contacted_aafiya_details']))
                <div class="mt-1 text-xs text-gray-500">Details: {{ $form['contacted_aafiya_details'] }}</div>
            @endif
        </div>
        <div>
            <label class="text-xs text-gray-500 block">Insured with any other insurer for this claim?</label>
            <p class="text-sm font-medium mt-1">
                {{ isset($form['other_insurer']) ? ($form['other_insurer'] ? 'Yes' : 'No') : '—' }}</p>
            @if (!empty($form['other_insurer_details']))
                <div class="mt-1 text-xs text-gray-500">Details: {{ $form['other_insurer_details'] }}</div>
            @endif
        </div>
    </div>

    {{-- SECTION 1 – MEDICAL AND RELATED EXPENSES --}}
    <div class="border-b pb-4">
        <h4 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">🏥 SECTION 1 – MEDICAL AND RELATED
            EXPENSES</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="text-xs text-gray-500">Date of illness/injury</label>
                <p class="text-sm">{{ $form['medical_date'] ?? '—' }}</p>
            </div>
            <div><label class="text-xs text-gray-500">Place of illness/injury</label>
                <p class="text-sm">{{ $form['medical_place'] ?? '—' }}</p>
            </div>
            <div><label class="text-xs text-gray-500">Cause</label>
                <p class="text-sm">{{ $form['medical_cause'] ?? '—' }}</p>
            </div>
            <div><label class="text-xs text-gray-500">Diagnosis</label>
                <p class="text-sm">{{ $form['medical_diagnosis'] ?? '—' }}</p>
            </div>
            <div><label class="text-xs text-gray-500">Doctor consulted</label>
                <p class="text-sm">{{ $form['doctor_name'] ?? '—' }} @if (!empty($form['doctor_phone']))
                        ({{ $form['doctor_phone'] }})
                    @endif
                </p>
            </div>
            <div><label class="text-xs text-gray-500">Hospital admitted to</label>
                <p class="text-sm">{{ $form['hospital_name'] ?? '—' }}</p>
            </div>
            <div><label class="text-xs text-gray-500">Total amount claimed</label>
                <p class="text-sm font-semibold">{{ $form['medical_amount'] ?? '—' }}
                    {{ $form['medical_currency'] ?? '' }}</p>
            </div>
            <div><label class="text-xs text-gray-500">Previously received treatment for this?</label>
                <p class="text-sm">
                    {{ isset($form['previous_treatment']) ? ($form['previous_treatment'] ? 'Yes' : 'No') : '—' }}</p>
            </div>
            <div><label class="text-xs text-gray-500">Submitted accounts paid?</label>
                <p class="text-sm">{{ isset($form['accounts_paid']) ? ($form['accounts_paid'] ? 'Yes' : 'No') : '—' }}
                </p>
            </div>
        </div>
    </div>

    {{-- SECTION 2 – DETAILS OF CLAIM (for illness/injury/death) --}}
    <div class="border-b pb-4">
        <h4 class="text-sm font-bold text-gray-700 mb-3">👤 SECTION 2 - DETAILS OF CLAIM (if due to
            illness/injury/death)</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="text-xs text-gray-500">Full name of subject</label>
                <p class="text-sm">{{ $form['subject_name'] ?? '—' }}</p>
            </div>
            <div><label class="text-xs text-gray-500">Date of birth</label>
                <p class="text-sm">{{ $form['subject_dob'] ?? '—' }}</p>
            </div>
            <div><label class="text-xs text-gray-500">Relationship to insured</label>
                <p class="text-sm">{{ $form['subject_relationship'] ?? '—' }}</p>
            </div>
            <div><label class="text-xs text-gray-500">Date of illness/injury</label>
                <p class="text-sm">{{ $form['subject_illness_date'] ?? '—' }}</p>
            </div>
            <div><label class="text-xs text-gray-500">Attending doctor</label>
                <p class="text-sm">{{ $form['subject_doctor'] ?? '—' }}</p>
            </div>
            <div><label class="text-xs text-gray-500">Diagnosis</label>
                <p class="text-sm">{{ $form['subject_diagnosis'] ?? '—' }}</p>
            </div>
            <div class="md:col-span-2"><label class="text-xs text-gray-500">Previous illness/injury?</label>
                <p class="text-sm">
                    {{ isset($form['subject_previous']) ? ($form['subject_previous'] ? 'Yes' : 'No') : '—' }}</p>
            </div>
        </div>
    </div>

    {{-- SECTION 3 – EFT, DECLARATION --}}
    <div class="border-b pb-4">
        <h4 class="text-sm font-bold text-gray-700 mb-3">💰 SECTION 3 - ELECTRONIC FUNDS TRANSFER, DECLARATION AND
            AUTHORITY</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="text-xs text-gray-500">Account holder's name</label>
                <p class="text-sm">{{ $form['account_name'] ?? '—' }}</p>
            </div>
            <div><label class="text-xs text-gray-500">Account number</label>
                <p class="text-sm font-mono">{{ $form['account_number'] ?? '—' }}</p>
            </div>
            <div><label class="text-xs text-gray-500">Bank name</label>
                <p class="text-sm">{{ $form['bank_name'] ?? '—' }}</p>
            </div>
            <div><label class="text-xs text-gray-500">Type of account</label>
                <p class="text-sm">{{ $form['account_type'] ?? '—' }}</p>
            </div>
            <div><label class="text-xs text-gray-500">Branch name</label>
                <p class="text-sm">{{ $form['branch_name'] ?? '—' }}</p>
            </div>
            <div><label class="text-xs text-gray-500">Branch code</label>
                <p class="text-sm">{{ $form['branch_code'] ?? '—' }}</p>
            </div>
        </div>
    </div>

    {{-- GENERAL SECTION – previous travel claims --}}
    <div class="border-b pb-4">
        <h4 class="text-sm font-bold text-gray-700 mb-2">📋 GENERAL SECTION - previous travel claims (last 5 years)
        </h4>
        <p class="text-sm whitespace-pre-wrap">{{ $form['previous_claims_details'] ?? '—' }}</p>
    </div>

    {{-- DECLARATION --}}
    <div class="border-b pb-4">
        <h4 class="text-sm font-bold text-gray-700 mb-2">📝 DECLARATION</h4>
        <p class="text-sm italic">I/WE SOLEMNLY DECLARE THAT THE ABOVE PARTICULARS ARE TRUE IN EVERY RESPECT.</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
            <div><label class="text-xs text-gray-500">Name(s) in full</label>
                <p class="text-sm">{{ $form['declarant_name'] ?? '—' }}</p>
            </div>
            <div><label class="text-xs text-gray-500">Signature</label>
                <p class="text-sm">{{ $form['signature'] ?? '—' }}</p>
            </div>
            <div><label class="text-xs text-gray-500">Date</label>
                <p class="text-sm">{{ $form['declaration_date'] ?? '—' }}</p>
            </div>
        </div>
    </div>

    {{-- EMERGENCY CONTACT (static footer from image) --}}
    <div class="text-xs text-center text-gray-400 pt-2">
        Emergency Contact Numbers: VANGUARD +233 302666485 | Claims Hotline +233 244334407
    </div>
</div>
