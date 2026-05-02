@php
    $form = $claim->form_data ?? [];
    $f = fn($key) => $form[$key] ?? '';
    $yn = fn($key) => strtolower($form[$key] ?? '') === 'yes';
    $customer = $claim->customer;
    $policy = $claim->policy;
@endphp

{{-- ══════════ HEADER ══════════ --}}
<div class="header" style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:14px;">
    <div style="display:flex; align-items:flex-start; gap:12px;">
        <img src="{{ asset('images/Vanguard.png') }}" alt="Vanguard Assurance" style="height:56px; object-fit:contain;" />
        <div>
            <p style="font-weight:bold; font-size:12px; margin-bottom:4px;">VANGUARD ASSURANCE COMPANY LIMITED</p>
            <p style="font-size:10px;"><strong>HEAD OFFICE:</strong> P.O. Box 1868, ACCRA</p>
        </div>
    </div>
    <div style="text-align:right; font-size:10px;">
        <p>
            <strong>E-MAIL:</strong>
            <span style="color:#00008B;">vacmails@vanguardassurance.com</span>
        </p>
        <p>claimsdepartment@vanguardassurance.com</p>
        <p style="margin-top:4px;"><strong>TELEPHONE:</strong> 0302666485/6/7</p>
    </div>
</div>

{{-- ══════════ FORM TITLE ══════════ --}}
<div class="form-title">Motor Accident Report Form</div>

{{-- ══════════ NOTICE ══════════ --}}
<p style="font-size:10px; margin-bottom:14px; text-align:justify;">
    Please note, it is necessary that great care should be taken in completing this form and the information given
    therein should be strictly accurate, whether it is in your favor or otherwise. You should not make any payment,
    offer or promise of any payment or admit liability in any way, as by so doing you may prejudice your position
    and make settlement of the claim difficult.
</p>

{{-- ══════════ POLICY & RENEWAL (right-aligned block) ══════════ --}}
<table style="margin-bottom:10px;">
    <tbody>
        <tr>
            <td style="width:50%; border:none;">&nbsp;</td>
            <td class="field-label" style="width:20%; border:1px solid #000;">Policy No.</td>
            <td class="field-value" style="width:30%; border:1px solid #000;">
                {{ $policy?->policy_number ?? $f('policy_number') }}</td>
        </tr>
        <tr>
            <td style="border:none;">&nbsp;</td>
            <td class="field-label" style="border:1px solid #000;">Renewal Date</td>
            <td class="field-value" style="border:1px solid #000;">{{ $f('renewal_date') }}</td>
        </tr>
    </tbody>
</table>

{{-- ══════════ INSURED DETAILS ══════════ --}}
<table style="margin-bottom:10px;">
    <tbody>
        <tr>
            <td class="field-label" style="width:35%">Name of Insured</td>
            <td class="field-value">{{ $f('name_of_insured') ?: $customer?->full_name ?? '' }}</td>
        </tr>
        <tr>
            <td class="field-label">Address</td>
            <td class="field-value">{{ $f('address') }}</td>
        </tr>
        <tr>
            <td class="field-label">Occupation</td>
            <td class="field-value">
                {{ $f('occupation') }}
                &nbsp;&nbsp;&nbsp;
                <strong>Telephone No:</strong> {{ $f('telephone') }}
            </td>
        </tr>
    </tbody>
</table>

{{-- ══════════ MOTOR VEHICLE PARTICULARS ══════════ --}}
<p class="section-heading">Particulars of Motor Vehicle Concerned</p>
<table style="margin-bottom:10px;">
    <tbody>
        <tr>
            <td class="field-label" style="width:25%">Registration No.</td>
            <td class="field-value" style="width:25%">{{ $f('registration_no') }}</td>
            <td class="field-label" style="width:10%">Make</td>
            <td class="field-value" style="width:15%">{{ $f('vehicle_make') }}</td>
            <td class="field-label" style="width:10%">Model</td>
            <td class="field-value" style="width:15%">{{ $f('vehicle_model') }}</td>
        </tr>
        <tr>
            <td class="field-label">Year of Make</td>
            <td class="field-value" colspan="5">{{ $f('year_of_make') }}</td>
        </tr>
        <tr class="yn-row">
            <td colspan="4" style="border:1px solid #000; padding:4px 6px; font-size:10px;">
                Is the vehicle the subject of a hire purchase or loan agreement?
            </td>
            <td colspan="2" style="border:1px solid #000; text-align:center;">
                <span class="yn-box {{ $yn('hire_purchase') ? 'selected' : '' }}">YES</span>
                <span class="yn-box {{ !$yn('hire_purchase') ? 'selected' : '' }}">NO</span>
            </td>
        </tr>
        <tr>
            <td class="field-label" colspan="2">If so, name of finance company / lending organisation</td>
            <td class="field-value tall" colspan="4">{{ $f('finance_company') }}</td>
        </tr>
        <tr>
            <td class="field-label" colspan="2" style="vertical-align:top;">Purpose vehicle was being used</td>
            <td class="field-value tall" colspan="4" style="min-height:36px;">{{ $f('vehicle_purpose') }}</td>
        </tr>
        <tr class="yn-row">
            <td colspan="4" style="border:1px solid #000; padding:4px 6px; font-size:10px;">
                Was the vehicle being used with your consent?
            </td>
            <td colspan="2" style="border:1px solid #000; text-align:center;">
                <span class="yn-box {{ $yn('used_with_consent') ? 'selected' : '' }}">YES</span>
                <span class="yn-box {{ !$yn('used_with_consent') ? 'selected' : '' }}">NO</span>
            </td>
        </tr>
    </tbody>
</table>

{{-- ══════════ DRIVER PARTICULARS ══════════ --}}
<p class="section-heading">Particulars of Person Driving at the Time of Accident</p>
<table style="margin-bottom:10px;">
    <tbody>
        <tr>
            <td class="field-label" style="width:20%">Full Name</td>
            <td class="field-value" style="width:45%">{{ $f('driver_fullname') }}</td>
            <td class="field-label" style="width:15%">Address</td>
            <td class="field-value" style="width:20%">{{ $f('driver_address') }}</td>
        </tr>
        <tr>
            <td class="field-label">Age</td>
            <td class="field-value">
                {{ $f('driver_age') }}
                &nbsp;&nbsp; <strong>Occupation:</strong> {{ $f('driver_occupation') }}
            </td>
            <td class="field-label">Telephone</td>
            <td class="field-value">{{ $f('driver_telephone') }}</td>
        </tr>
        <tr>
            <td class="field-label">Driving Licence No.</td>
            <td class="field-value">{{ $f('licence_no') }}</td>
            <td class="field-label">Date of Issue</td>
            <td class="field-value">{{ $f('licence_date_issued') }}</td>
        </tr>
        <tr>
            <td colspan="4" style="border:1px solid #000; padding:4px 6px; font-size:10px;">
                State whether the person driving at the time of accident was:
                <strong>(a) The Owner</strong> &nbsp;
                <strong>(b) An Employee</strong> &nbsp;
                <strong>(c) Relative or Friend</strong> &nbsp;
                <strong>(d) Others</strong>
                <span
                    style="border-bottom:1px solid #000; display:inline-block; min-width:120px; margin-left:6px;">{{ $f('driver_relationship') }}</span>
            </td>
        </tr>
        <tr>
            <td class="field-label" colspan="1" style="vertical-align:top;">Name &amp; address of insurer of driver
                and policy number</td>
            <td class="field-value tall" colspan="3">{{ $f('driver_insurer_details') }}</td>
        </tr>
    </tbody>
</table>

{{-- ══════════ CIRCUMSTANCES OF ACCIDENT ══════════ --}}
<p class="section-heading">Circumstances of Accident</p>
<table style="margin-bottom:10px;">
    <tbody>
        <tr>
            <td class="field-label" style="width:30%">Date and Time</td>
            <td class="field-value">
                {{ $f('accident_date') }}
                &nbsp;&nbsp; at &nbsp;&nbsp;
                {{ $f('accident_time') }} am/pm
            </td>
        </tr>
        <tr>
            <td class="field-label">Exact Location of Incident</td>
            <td class="field-value">{{ $f('accident_location') }}</td>
        </tr>
        <tr>
            <td class="field-label">How many people were in your vehicle?</td>
            <td class="field-value">{{ $f('people_in_vehicle') }}</td>
        </tr>
        <tr>
            <td class="field-label">If not in vehicle, when was accident reported to you?</td>
            <td class="field-value">{{ $f('accident_reported_when') }}</td>
        </tr>
        <tr>
            <td class="field-label" style="vertical-align:top;">Full description of how accident happened</td>
            <td class="field-value tall" style="min-height:60px;">{{ $f('accident_description') }}</td>
        </tr>
        <tr>
            <td class="field-label" style="vertical-align:top;">In your opinion, was accident caused by you or your
                driver? If not, by whom?</td>
            <td class="field-value tall">{{ $f('accident_caused_by') }}</td>
        </tr>
        <tr>
            <td class="field-label" style="vertical-align:top;">Damage to your vehicle</td>
            <td class="field-value tall">{{ $f('own_vehicle_damage') }}</td>
        </tr>
        <tr>
            <td class="field-label">State exact current location of the damaged vehicle</td>
            <td class="field-value">{{ $f('damaged_vehicle_location') }}</td>
        </tr>
        <tr>
            <td class="field-label">Name and address of nearest Repairers</td>
            <td class="field-value">{{ $f('nearest_repairer') }}</td>
        </tr>
    </tbody>
</table>

{{-- ══════════ THIRD PARTIES ══════════ --}}
<p class="section-heading">Third Parties Involved in Accident</p>
<table style="margin-bottom:10px;">
    <tbody>
        <tr>
            <td class="field-label" style="vertical-align:top;">Names &amp; addresses of persons injured and extent of
                injuries</td>
            <td class="field-value tall">{{ $f('third_party_injuries') }}</td>
        </tr>
        <tr>
            <td colspan="2" style="border:1px solid #000; padding:4px 6px;">
                <strong>Injured persons in your vehicle:</strong>
                <table style="margin:6px 0 0; border:none;">
                    <tbody>
                        <tr>
                            <td style="border:none; width:5%; font-size:10px;">1.</td>
                            <td style="border-bottom:1px solid #000; width:40%;">{{ $f('own_injured_1') }}</td>
                            <td style="border:none; width:5%; font-size:10px; padding-left:8px;">2.</td>
                            <td style="border-bottom:1px solid #000; width:40%;">{{ $f('own_injured_2') }}</td>
                            <td style="border:none; width:10%;"></td>
                        </tr>
                        <tr>
                            <td style="border:none; font-size:10px;">3.</td>
                            <td style="border-bottom:1px solid #000;">{{ $f('own_injured_3') }}</td>
                            <td style="border:none; font-size:10px; padding-left:8px;">4.</td>
                            <td style="border-bottom:1px solid #000;">{{ $f('own_injured_4') }}</td>
                            <td style="border:none;"></td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="border:1px solid #000; padding:4px 6px;">
                <strong>Injured persons in the other vehicle:</strong>
                <table style="margin:6px 0 0; border:none;">
                    <tbody>
                        <tr>
                            <td style="border:none; width:5%; font-size:10px;">1.</td>
                            <td style="border-bottom:1px solid #000; width:40%;">{{ $f('other_injured_1') }}</td>
                            <td style="border:none; width:5%; font-size:10px; padding-left:8px;">2.</td>
                            <td style="border-bottom:1px solid #000; width:40%;">{{ $f('other_injured_2') }}</td>
                            <td style="border:none; width:10%;"></td>
                        </tr>
                        <tr>
                            <td style="border:none; font-size:10px;">3.</td>
                            <td style="border-bottom:1px solid #000;">{{ $f('other_injured_3') }}</td>
                            <td style="border:none; font-size:10px; padding-left:8px;">4.</td>
                            <td style="border-bottom:1px solid #000;">{{ $f('other_injured_4') }}</td>
                            <td style="border:none;"></td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td class="field-label">Other vehicle — Regd. No.</td>
            <td class="field-value">
                {{ $f('other_vehicle_reg') }}
                &nbsp;&nbsp; <strong>Make:</strong> {{ $f('other_vehicle_make') }}
                &nbsp;&nbsp; <strong>Model:</strong> {{ $f('other_vehicle_model') }}
            </td>
        </tr>
        <tr>
            <td class="field-label">Name &amp; address of driver of other vehicle</td>
            <td class="field-value">{{ $f('other_driver_name_address') }}</td>
        </tr>
        <tr>
            <td class="field-label">Telephone of driver of other vehicle</td>
            <td class="field-value">{{ $f('other_driver_telephone') }}</td>
        </tr>
        <tr>
            <td class="field-label">Name &amp; address of owner of other vehicle</td>
            <td class="field-value">{{ $f('other_owner_name_address') }}</td>
        </tr>
        <tr>
            <td class="field-label">Telephone of owner of other vehicle</td>
            <td class="field-value">{{ $f('other_owner_telephone') }}</td>
        </tr>
        <tr>
            <td class="field-label" style="vertical-align:top;">Name &amp; address of Insurer of other vehicle and
                policy number</td>
            <td class="field-value tall">{{ $f('other_vehicle_insurer') }}</td>
        </tr>
        <tr>
            <td class="field-label" style="vertical-align:top;">Details of damage to other vehicle</td>
            <td class="field-value tall">{{ $f('other_vehicle_damage') }}</td>
        </tr>
        <tr class="yn-row">
            <td colspan="2" style="border:1px solid #000; padding:4px 6px; font-size:10px; text-align:justify;">
                Has any claim been made upon you?
                <span class="yn-box {{ $yn('claim_made_upon_you') ? 'selected' : '' }}">YES</span>
                <span class="yn-box {{ !$yn('claim_made_upon_you') ? 'selected' : '' }}">NO</span>
                If so, state particulars below and note that any letter or communication received by you must be
                forwarded immediately unanswered, to this company.
            </td>
        </tr>
        <tr>
            <td class="field-value tall" colspan="2">{{ $f('claim_particulars') }}</td>
        </tr>
    </tbody>
</table>

{{-- ══════════ POLICE & MULTI-POLICY ══════════ --}}
<table style="margin-bottom:14px;">
    <tbody>
        <tr class="yn-row">
            <td style="border:1px solid #000; padding:4px 6px; font-size:10px;" colspan="2">
                Was the accident reported to the police?
                <span class="yn-box {{ $yn('reported_to_police') ? 'selected' : '' }}">YES</span>
                <span class="yn-box {{ !$yn('reported_to_police') ? 'selected' : '' }}">NO</span>
                If Yes, state when and at which Police Station:
            </td>
        </tr>
        <tr>
            <td class="field-value tall" colspan="2">{{ $f('police_station_details') }}</td>
        </tr>
        <tr>
            <td class="field-label" style="width:40%">Name of Police Officer who took particulars</td>
            <td class="field-value">{{ $f('police_officer_name') }}</td>
        </tr>
        <tr class="yn-row">
            <td colspan="2" style="border:1px solid #000; padding:4px 6px; font-size:10px;">
                Do you hold more than one policy indemnifying you in respect of this accident?
                <span class="yn-box {{ $yn('multiple_policies') ? 'selected' : '' }}">YES</span>
                <span class="yn-box {{ !$yn('multiple_policies') ? 'selected' : '' }}">NO</span>
            </td>
        </tr>
    </tbody>
</table>

{{-- ══════════ DECLARATION ══════════ --}}
<div class="declaration-block">
    <p style="font-weight:bold; text-transform:uppercase; margin-bottom:6px;">Declaration:</p>
    <p style="text-align:justify;">
        I declare that the above statement is true in all respects to the best of my knowledge and belief and I hereby
        leave in the hands of the Company in accordance with the conditions of the Policy the conduct of all claims
        and litigation arising out of this accident and to which the Policy applies, to deal with, to prosecute
        and/or settle as they dem fit without further reference to me; and I undertake to give all such information
        an assistance as the Company may require.
    </p>
    <div class="signature-line" style="margin-top:24px;">
        <div style="flex:1;">
            <p class="sig-label">DATE</p>
            <div class="sig-field">{{ $f('declaration_date') }}</div>
        </div>
        <div style="flex:1;">
            <p class="sig-label">SIGNATURE</p>
            <div class="sig-field">{{ $f('digital_signature') }}</div>
        </div>
    </div>
</div>

<p style="font-size:10px; margin-top:14px;">
    <strong>Note:</strong> The Company does not admit liability by the issue of this form.
</p>

{{-- ══════════ SKETCH PAGE ══════════ --}}
<div style="page-break-before:always; padding-top:20px;">
    <p class="section-heading" style="text-align:center; font-size:13px; text-decoration:underline;">Sketch</p>
    <p style="font-size:10px; margin-bottom:20px; text-align:justify;">
        Please make a sketch showing position of Vehicles and Persons concerned both before and after the Accident
        and showing the direction in which, they were travelling.
    </p>

    {{-- Position Before --}}
    <p
        style="font-weight:bold; text-decoration:underline; text-align:center; font-size:11px; margin-bottom:8px; text-transform:uppercase;">
        Position Before Accident
    </p>
    @if (!empty($form['sketch_before']))
        {{-- If a sketch image was uploaded/stored as base64 or URL --}}
        <div style="text-align:center; margin-bottom:20px;">
            <img src="{{ $form['sketch_before'] }}" alt="Sketch Before Accident"
                style="max-width:100%; max-height:220px; border:1px solid #ccc;" />
        </div>
    @else
        <div style="border:1px solid #ccc; height:220px; margin-bottom:20px; background:#fafafa;"></div>
    @endif

    {{-- Position After --}}
    <p
        style="font-weight:bold; text-decoration:underline; text-align:center; font-size:11px; margin-bottom:8px; text-transform:uppercase;">
        Position After Accident
    </p>
    @if (!empty($form['sketch_after']))
        <div style="text-align:center; margin-bottom:20px;">
            <img src="{{ $form['sketch_after'] }}" alt="Sketch After Accident"
                style="max-width:100%; max-height:220px; border:1px solid #ccc;" />
        </div>
    @else
        <div style="border:1px solid #ccc; height:220px; margin-bottom:20px; background:#fafafa;"></div>
    @endif
</div>

{{-- ══════════ FOOTER ══════════ --}}
<div class="footer">
    EMERGENCY CONTACT NUMBERS: VANGUARD +233 302666485 | CLAIMS HOTLINE +233 244334407
</div>
