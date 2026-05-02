
@php
        $form = $claim->form_data ?? [];
        $f = fn($key) => $form[$key] ?? '';
        $yn = fn($key) => strtolower($form[$key] ?? '') === 'yes';
        $type = strtolower($claim->claim_type ?? '');
        $customer = $claim->customer;
        $policy = $claim->policy;
    @endphp

    {{-- Print Button (hidden on actual print) --}}
    <div class="no-print" style="text-align:right; margin-bottom:12px;">
        <button onclick="window.print()"
            style="background:#1a3a5c; color:#fff; border:none; padding:8px 20px; border-radius:6px; font-size:12px; cursor:pointer; font-weight:bold;">
            &#128438; Print / Save as PDF
        </button>
        <button onclick="window.close()"
            style="background:#f3f4f6; color:#374151; border:1px solid #d1d5db; padding:8px 16px; border-radius:6px; font-size:12px; cursor:pointer; margin-left:8px;">
            Close
        </button>
    </div>

    {{-- ══════════ HEADER ══════════ --}}
    <div class="header">
        <div class="header-left">
            <p class="company-name">Aafiya Medical Billing Services LLC</p>
            <p>PHONE: +971525207091</p>
            <p>EMAIL: cs@aafiya.ae / claims@aafiya.ae / aprovals@aafiya.ae</p>
        </div>
        <div class="logo-block">
            <img src="{{ asset('images/Vanguard.png') }}" alt="Vanguard Assurance" />
            <p class="tagline">We always stand by you.</p>
        </div>
    </div>

    {{-- ══════════ FORM TITLE ══════════ --}}
    @if ($type === 'general_accident')
        <div class="form-title">Travel Protection Claim Form</div>
    @elseif($type === 'motor')
        <div class="form-title">Motor Accident Report Form</div>
    @elseif($type === 'fire')
        <div class="form-title">Fire Claim Form</div>
    @else
        <div class="form-title">Insurance Claim Form</div>
    @endif

    {{-- ══════════ NOTE BOX ══════════ --}}
    <div class="note-box">
        <p class="note-heading">NOTE — THE ORIGINAL CLAIM FORM AND DOCUMENTATION IS REQUIRED</p>
        <ul>
            <li>THIS FORM MUST BE SIGNED AND DATED</li>
            <li>A COPY OF YOUR TRAVEL INSURANCE CERTIFICATE MUST BE ATTACHED</li>
            <li>SUPPORTING DOCUMENTATION SUBSTANTIATING THE CLAIM MUST BE SUBMITTED</li>
            <li>ALL CLAIMS MUST BE PREPARED WITHIN 24 HOURS OF THE INCIDENT</li>
            <li>ALL CLAIMS MUST BE SUBMITTED WITHIN 60 DAYS OF THE INCIDENT</li>
            <li>DELAYED / LOSS OF LUGGAGE:
                <ul class="sub-list">
                    <li class="sub">Kindly submit a complete baggage Inventory Claim Form from the airline</li>
                    <li class="sub">Travel Ticket</li>
                </ul>
            </li>
        </ul>
    </div>

    {{-- ══════════ FORM HEADER TABLE ══════════ --}}
    <p class="section-heading">This Form Must Be Completed In Full</p>
    <table>
        <thead>
            <tr>
                <th>Selling Agent / Broker</th>
                <th>Policy Number</th>
                <th>Departure Date</th>
                <th>Return Date</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $f('agent_broker') }}</td>
                <td>{{ $policy?->policy_number ?? '' }}</td>
                <td>{{ $f('departure_date') }}</td>
                <td>{{ $f('return_date') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- ══════════ INSURED PERSON ══════════ --}}
    <p class="section-heading">Insured Person</p>
    <table>
        <tbody>
            <tr>
                <td class="field-label">Surname</td>
                <td class="field-value">{{ $f('surname') }}</td>
                <td class="field-label" style="width:20%">Age</td>
                <td class="field-value" style="width:15%">{{ $f('insured_age') }}</td>
            </tr>
            <tr>
                <td class="field-label">First Name(s)</td>
                <td class="field-value" colspan="3">{{ $f('firstname') }}</td>
            </tr>
            <tr>
                <td class="field-label">Postal Address</td>
                <td class="field-value">{{ $f('postal_address') }}</td>
                <td class="field-label">Code</td>
                <td class="field-value">{{ $f('postal_code') }}</td>
            </tr>
            <tr>
                <td class="field-label">Physical Address</td>
                <td class="field-value" colspan="3">{{ $f('physical_address') }}</td>
            </tr>
            <tr>
                <td class="field-label">Telephone Numbers</td>
                <td class="field-value">
                    BUS: {{ $f('business') }} &nbsp;&nbsp;
                    FAX: {{ $f('fax') }} &nbsp;&nbsp;
                    RES/CELL: {{ $f('res_cell') }}
                </td>
                <td class="field-label">Email Address</td>
                <td class="field-value">{{ $f('email') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- ══════════ AAFIYA & POLICE CONDITIONALS ══════════ --}}
    <table>
        <tbody>
            <tr class="yn-row">
                <td style="width:70%">Did you contact AAFIYA at the time of the occurrence?</td>
                <td style="width:30%; text-align:center">
                    <span class="yn-box {{ $yn('contacted_aafiya') ? 'selected' : '' }}">YES</span>
                    <span class="yn-box {{ !$yn('contacted_aafiya') ? 'selected' : '' }}">NO</span>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="field-label" style="width:100%">If Yes, please provide details:</td>
            </tr>
            <tr>
                <td colspan="2" class="field-value tall">{{ $f('contacted_aafiya_details') }}</td>
            </tr>
            <tr class="yn-row">
                <td>Was the accident reported to the police?</td>
                <td style="text-align:center">
                    <span class="yn-box {{ $yn('police_report') ? 'selected' : '' }}">YES</span>
                    <span class="yn-box {{ !$yn('police_report') ? 'selected' : '' }}">NO</span>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="field-label">If Yes, please provide details:</td>
            </tr>
            <tr>
                <td colspan="2" class="field-value tall">{{ $f('police_report_details') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- ══════════ SECTION 1 — MEDICAL ══════════ --}}
    <p class="section-heading">Section 1 — Medical and Related Expenses</p>
    <table>
        <tbody>
            <tr>
                <td class="field-label">Date of Illness / Injury</td>
                <td class="field-value">{{ $f('illness_date') }}</td>
                <td class="field-label">Place of Illness / Injury</td>
                <td class="field-value">{{ $f('place_of_illness') }}</td>
            </tr>
            <tr>
                <td class="field-label">Cause of Illness / Injury</td>
                <td class="field-value" colspan="3">{{ $f('cause_of_illness') }}</td>
            </tr>
            <tr>
                <td class="field-label">Diagnosis</td>
                <td class="field-value" colspan="3">{{ $f('diagnosis_sec1') }}</td>
            </tr>
            <tr>
                <td class="field-label">Full Name of Doctor Consulted</td>
                <td class="field-value">{{ $f('doctor_fullname_sec1') }}</td>
                <td class="field-label">TEL</td>
                <td class="field-value">{{ $f('doctor_telephone') }}</td>
            </tr>
            <tr>
                <td class="field-label">Name of Hospital Admitted To</td>
                <td class="field-value" colspan="3">{{ $f('hospital_name') }}</td>
            </tr>
            <tr>
                <td class="field-label">Total Amount Claimed</td>
                <td class="field-value">{{ $f('total_amount_claimed') }}</td>
                <td class="field-label">Currency</td>
                <td class="field-value">{{ $f('currency') }}</td>
            </tr>
            <tr class="yn-row">
                <td colspan="3">Have you previously received treatment or attention for this Illness/Condition?
                    <br><small>(If Yes, a report from your treating doctor detailing your medical history)</small>
                </td>
                <td style="text-align:center">
                    <span class="yn-box {{ $yn('treatment_received') ? 'selected' : '' }}">YES</span>
                    <span class="yn-box {{ !$yn('treatment_received') ? 'selected' : '' }}">NO</span>
                </td>
            </tr>
            @if ($f('treatment_received_details'))
                <tr>
                    <td colspan="4" class="field-value">{{ $f('treatment_received_details') }}</td>
                </tr>
            @endif
            <tr class="yn-row">
                <td colspan="3">Have submitted accounts been paid?</td>
                <td style="text-align:center">
                    <span class="yn-box {{ $yn('submitted_accounts_yn') ? 'selected' : '' }}">YES</span>
                    <span class="yn-box {{ !$yn('submitted_accounts_yn') ? 'selected' : '' }}">NO</span>
                </td>
            </tr>
        </tbody>
    </table>

    {{-- ══════════ SECTION 2 — DETAILS OF CLAIM ══════════ --}}
    <p class="section-heading">Section 2 — Details of Claim</p>
    <p style="font-weight:bold; text-align:center; font-size:10px; margin-bottom:6px; text-transform:uppercase;">
        If Claim is Due to Illness / Injury / Death
    </p>
    <table>
        <tbody>
            <tr>
                <td class="field-label">Full Name of Subject of Claim</td>
                <td class="field-value">{{ $f('claim_subject_name') }}</td>
                <td class="field-label">Date of Birth</td>
                <td class="field-value">{{ $f('dob_claim') }}</td>
            </tr>
            <tr>
                <td class="field-label">Date of Illness / Injury</td>
                <td class="field-value">{{ $f('illness_date_sec2') }}</td>
                <td class="field-label">Attending Doctor's Full Name</td>
                <td class="field-value">{{ $f('doctor_fullname_sec2') }}</td>
            </tr>
            <tr>
                <td class="field-label">Diagnosis</td>
                <td class="field-value" colspan="3">{{ $f('diagnosis_sec2') }}</td>
            </tr>
            <tr class="yn-row">
                <td colspan="3">Has the above-mentioned person suffered previously from Illness/Injury?
                    <br><small>(If Yes, report from the treating doctor detailing medical history is required)</small>
                </td>
                <td style="text-align:center">
                    <span class="yn-box {{ $yn('previous_injury') ? 'selected' : '' }}">YES</span>
                    <span class="yn-box {{ !$yn('previous_injury') ? 'selected' : '' }}">NO</span>
                </td>
            </tr>
            @if ($f('previous_injury_details'))
                <tr>
                    <td colspan="4" class="field-value">{{ $f('previous_injury_details') }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- ══════════ SECTION 3 — EFT ══════════ --}}
    <p class="section-heading">Section 3 — Electronic Funds Transfer, Declaration and Authority</p>
    <table>
        <tbody>
            <tr>
                <td class="field-label">Account Holder's Name</td>
                <td class="field-value">{{ $f('account_holder_name') }}</td>
                <td class="field-label">Account Number</td>
                <td class="field-value">{{ $f('account_number') }}</td>
            </tr>
            <tr>
                <td class="field-label">Name of Bank</td>
                <td class="field-value">{{ $f('bank_name') }}</td>
                <td class="field-label">Type of Account</td>
                <td class="field-value">{{ $f('account_type') }}</td>
            </tr>
            <tr>
                <td class="field-label">Branch Name</td>
                <td class="field-value">{{ $f('branch_name') }}</td>
                <td class="field-label">Branch Code</td>
                <td class="field-value">{{ $f('branch_code') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- ══════════ GENERAL SECTION ══════════ --}}
    <table>
        <tbody>
            <tr>
                <td class="field-label" style="width:30%">General Section</td>
                <td></td>
            </tr>
            <tr>
                <td colspan="2" style="font-size:10px; font-weight:bold; padding:4px 6px;">
                    Have you incurred any travel claims in the past 5 years? If so, please supply details below:
                </td>
            </tr>
            <tr>
                <td colspan="2" class="field-value tall">{{ $f('general_section') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- ══════════ DECLARATION ══════════ --}}
    <div class="declaration-block">
        <p style="font-weight:bold; text-align:center; text-transform:uppercase; margin-bottom:8px;">Declaration</p>
        <p>I/We solemnly declare that the above particulars are true in every respect.</p>
        <div class="signature-line">
            <div>
                <p class="sig-label">NAME(s) IN FULL</p>
                <div class="sig-field">{{ $f('digital_signature') }}</div>
            </div>
            <div style="flex:0.5">
                <p class="sig-label">DATE</p>
                <div class="sig-field">{{ $f('declaration_date') }}</div>
            </div>
        </div>
        <div style="margin-top: 14px;">
            <p class="sig-label">SIGNATURE(s)</p>
            <div class="sig-field" style="min-height:30px; border-bottom:1px solid #000;"></div>
        </div>
    </div>

    {{-- ══════════ FOOTER ══════════ --}}
    <div class="footer">
        EMERGENCY CONTACT NUMBERS: VANGUARD +233 302666485 | CLAIMS HOTLINE +233 244334407
    </div>

    <script>
        // Auto-open print dialog when page loads
        window.addEventListener('load', function() {
            // Small delay to ensure styles render before print dialog opens
            // Remove this if you don't want auto-print
            // window.print();
        });
    </script>