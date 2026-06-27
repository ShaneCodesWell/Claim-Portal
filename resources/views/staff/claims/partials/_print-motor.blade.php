@php
    $form = $claim->form_data ?? [];
    $f = fn($key) => $form[$key] ?? '';
    $yn = fn($key) => strtolower($form[$key] ?? '') === 'yes';
    $customer = $claim->customer;
    $policy = $claim->policy;

    // Parse injured persons arrays (stored as JSON strings in the payload)
    $ownInjured = collect(json_decode($f('your_vehicle_injured') ?: '[]', true) ?? []);
    $otherInjured = collect(json_decode($f('other_vehicle_injured') ?: '[]', true) ?? []);

    // Parse involved vehicles
    $involvedVehicles = collect(json_decode($f('involved_vehicles') ?: '[]', true) ?? []);
@endphp

{{-- ══════════ HEADER ══════════ --}}
<div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:14px;">
    <div style="display:flex; align-items:flex-start; gap:12px;">
        <img src="{{ asset('images/Vanguard.png') }}" alt="Vanguard Assurance" style="height:56px; object-fit:contain;" />
        <div>
            <p style="font-weight:bold; font-size:12px; margin-bottom:4px;">VANGUARD ASSURANCE COMPANY LIMITED</p>
            <p style="font-size:10px;"><strong>HEAD OFFICE:</strong> P.O. Box 1868, ACCRA</p>
        </div>
    </div>
    <div style="text-align:right; font-size:10px;">
        <p><strong>E-MAIL:</strong> <span style="color:#00008B;">vacmails@vanguardassurance.com</span></p>
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

{{-- ══════════ POLICY & RENEWAL ══════════ --}}
<table style="margin-bottom:10px;">
    <tbody>
        <tr>
            <td style="width:50%; border:none;">&nbsp;</td>
            <td class="field-label" style="width:20%;">Policy No.</td>
            <td class="field-value" style="width:30%;">{{ $policy?->policy_number ?? '' }}</td>
        </tr>
        <tr>
            <td style="border:none;">&nbsp;</td>
            <td class="field-label">Renewal Date</td>
            <td class="field-value">{{ $policy?->renewal_date?->format('F j, Y') }}</td>
        </tr>
    </tbody>
</table>

{{-- ══════════ INSURED DETAILS ══════════ --}}
<table style="margin-bottom:10px;">
    <tbody>
        <tr>
            <td class="field-label" style="width:35%">Name of Insured</td>
            <td class="field-value">{{ $f('fullname') ?: $customer?->full_name ?? '' }}</td>
        </tr>
        {{-- Added an email fallback --}}
        <tr>
            <td class="field-label">Address</td>
            <td class="field-value">{{ $f('address') ?: $customer?->email ?? '' }}</td> 
        </tr>
        <tr>
            <td class="field-label">Occupation</td>
            <td class="field-value">
                {{ $f('occupation') }}
            </td>
        </tr>
        <tr>
            <td class="field-label">Telephone No:</td>
            <td class="field-value">{{ $f('phone') }}
            </td>
            </td>
        </tr>
    </tbody>
</table>

{{-- ══════════ MOTOR VEHICLE PARTICULARS ══════════ --}}
<p class="section-heading">Particulars of Motor Vehicle Concerned</p>
<table style="margin-bottom:10px;">
    <tbody>
        <tr>
            <td class="field-label" style="width:20%">Registration No.</td>
            <td class="field-value" style="width:20%">{{ $f('registration_no') }}</td>
            <td class="field-label" style="width:10%">Make</td>
            <td class="field-value" style="width:15%">{{ $f('make') }}</td>
            <td class="field-label" style="width:10%">Model</td>
            <td class="field-value" style="width:15%">{{ $f('model') }}</td>
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
        @if ($f('finance_company'))
            <tr>
                <td class="field-label" colspan="2">Name of finance company / lending organisation</td>
                <td class="field-value tall" colspan="4">{{ $f('finance_company') }}</td>
            </tr>
        @endif
        <tr>
            <td class="field-label" colspan="2" style="vertical-align:top;">Purpose vehicle was being used</td>
            <td class="field-value tall" colspan="4">{{ $f('vehicle_purpose') }}</td>
        </tr>
        <tr class="yn-row">
            <td colspan="4" style="border:1px solid #000; padding:4px 6px; font-size:10px;">
                Was the vehicle being used with your consent?
            </td>
            <td colspan="2" style="border:1px solid #000; text-align:center;">
                <span class="yn-box {{ strtolower($f('vehicle_consent')) === 'yes' ? 'selected' : '' }}">YES</span>
                <span class="yn-box {{ strtolower($f('vehicle_consent')) !== 'yes' ? 'selected' : '' }}">NO</span>
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
                @if ($f('driver_occupation'))
                    &nbsp;&nbsp; <strong>Occupation:</strong> {{ $f('driver_occupation') }}
                @endif
            </td>
            <td class="field-label">Telephone</td>
            <td class="field-value">{{ $f('driver_phone') }}</td>
        </tr>
        <tr>
            <td class="field-label">Driving Licence No.</td>
            <td class="field-value">{{ $f('driver_license') }}</td>
            <td class="field-label">Date of Issue</td>
            <td class="field-value">{{ formatDate($f('driver_license_date')) }}</td>
        </tr>
        <tr>
            <td colspan="4" style="border:1px solid #000; padding:4px 6px; font-size:10px;">
                State whether the person driving at the time of accident was:
                <strong>(a) The Owner</strong> &nbsp;
                <strong>(b) An Employee</strong> &nbsp;
                <strong>(c) Relative or Friend</strong> &nbsp;
                <strong>(d) Others</strong>
                &nbsp;→&nbsp;
                <strong style="text-transform:capitalize;">{{ $f('driver_type') }}</strong>
            </td>
        </tr>
        <tr>
            <td class="field-label" style="vertical-align:top;">Name &amp; address of insurer of driver and policy
                number</td>
            <td class="field-value tall" colspan="3">{{ $f('driver_insurance_details') }}</td>
        </tr>
    </tbody>
</table>

{{-- ══════════ CIRCUMSTANCES OF ACCIDENT ══════════ --}}
<p class="section-heading">Circumstances of Accident</p>
<table style="margin-bottom:10px;">
    <tbody>
        <tr>
            <td class="field-label" style="width:35%">Date and Time</td>
            <td class="field-value">
                {{ $f('accident_date') }}
                @if ($f('accident_time'))
                    &nbsp;&nbsp; at &nbsp;&nbsp; {{ $f('accident_time') }}
                @endif
            </td>
        </tr>
        <tr>
            <td class="field-label">Exact Location of Incident</td>
            <td class="field-value">{{ $f('exact_location') }}</td>
        </tr>
        <tr>
            <td class="field-label">How many people were in your vehicle?</td>
            <td class="field-value">{{ $f('people_in_vehicle') }}</td>
        </tr>
        <tr>
            <td class="field-label">If not in vehicle, when was accident reported to you?</td>
            <td class="field-value">{{ $f('report_date') }}</td>
        </tr>
        <tr>
            <td class="field-label" style="vertical-align:top;">Full description of how accident happened</td>
            <td class="field-value tall" style="min-height:50px;">{{ $f('accident_description') }}</td>
        </tr>
        <tr>
            <td class="field-label" style="vertical-align:top;">In your opinion, was accident caused by you or your
                driver? If not, by whom?</td>
            <td class="field-value tall">{{ $f('fault_person') }}</td>
        </tr>
        <tr>
            <td class="field-label" style="vertical-align:top;">Damage to your vehicle</td>
            <td class="field-value tall">{{ $f('vehicle_damage') }}</td>
        </tr>
        <tr>
            <td class="field-label">State exact current location of the damaged vehicle</td>
            <td class="field-value">{{ $f('damaged_vehicle_location') }}</td>
        </tr>
        <tr>
            <td class="field-label">Name and address of nearest Repairers</td>
            <td class="field-value">
                {{ $f('repairer_fullname') }}
                @if ($f('repairer_address'))
                    , {{ $f('repairer_address') }}
                @endif
            </td>
        </tr>
    </tbody>
</table>

{{-- ══════════ THIRD PARTIES ══════════ --}}
<p class="section-heading">Third Parties Involved in Accident</p>
<table style="margin-bottom:10px;">
    <tbody>

        {{-- Injured persons in YOUR vehicle --}}
        <tr>
            <td colspan="2" style="border:1px solid #000; padding:6px;">
                <strong>Injured persons in your vehicle:</strong>
                @php $hasOwn = $ownInjured->filter(fn($p) => !empty($p['name']))->count(); @endphp
                @if ($hasOwn)
                    <table style="margin-top:6px; width:100%; border-collapse:collapse;">
                        <thead>
                            <tr>
                                <th style="font-size:9px; border:1px solid #ccc; background:#f5f5f5; padding:2px 4px;">
                                    #</th>
                                <th style="font-size:9px; border:1px solid #ccc; background:#f5f5f5; padding:2px 4px;">
                                    Name</th>
                                <th style="font-size:9px; border:1px solid #ccc; background:#f5f5f5; padding:2px 4px;">
                                    Age</th>
                                <th style="font-size:9px; border:1px solid #ccc; background:#f5f5f5; padding:2px 4px;">
                                    Address</th>
                                <th style="font-size:9px; border:1px solid #ccc; background:#f5f5f5; padding:2px 4px;">
                                    Injuries</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ownInjured as $i => $person)
                                @if (!empty($person['name']))
                                    <tr>
                                        <td style="border:1px solid #ccc; font-size:9px; padding:3px;">
                                            {{ $i + 1 }}</td>
                                        <td style="border:1px solid #ccc; font-size:9px; padding:3px;">
                                            {{ $person['name'] }}</td>
                                        <td style="border:1px solid #ccc; font-size:9px; padding:3px;">
                                            {{ $person['age'] ?? '' }}</td>
                                        <td style="border:1px solid #ccc; font-size:9px; padding:3px;">
                                            {{ $person['address'] ?? '' }}</td>
                                        <td style="border:1px solid #ccc; font-size:9px; padding:3px;">
                                            {{ $person['injuries'] ?? '' }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p style="font-size:9px; color:#888; margin-top:4px; font-style:italic;">None reported</p>
                @endif
            </td>
        </tr>

        {{-- Injured persons in OTHER vehicle --}}
        <tr>
            <td colspan="2" style="border:1px solid #000; padding:6px;">
                <strong>Injured persons in the other vehicle:</strong>
                @php $hasOther = $otherInjured->filter(fn($p) => !empty($p['name']))->count(); @endphp
                @if ($hasOther)
                    <table style="margin-top:6px; width:100%; border-collapse:collapse;">
                        <thead>
                            <tr>
                                <th style="font-size:9px; border:1px solid #ccc; background:#f5f5f5; padding:2px 4px;">
                                    #</th>
                                <th style="font-size:9px; border:1px solid #ccc; background:#f5f5f5; padding:2px 4px;">
                                    Name</th>
                                <th style="font-size:9px; border:1px solid #ccc; background:#f5f5f5; padding:2px 4px;">
                                    Age</th>
                                <th style="font-size:9px; border:1px solid #ccc; background:#f5f5f5; padding:2px 4px;">
                                    Address</th>
                                <th style="font-size:9px; border:1px solid #ccc; background:#f5f5f5; padding:2px 4px;">
                                    Injuries</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($otherInjured as $i => $person)
                                @if (!empty($person['name']))
                                    <tr>
                                        <td style="border:1px solid #ccc; font-size:9px; padding:3px;">
                                            {{ $i + 1 }}</td>
                                        <td style="border:1px solid #ccc; font-size:9px; padding:3px;">
                                            {{ $person['name'] }}</td>
                                        <td style="border:1px solid #ccc; font-size:9px; padding:3px;">
                                            {{ $person['age'] ?? '' }}</td>
                                        <td style="border:1px solid #ccc; font-size:9px; padding:3px;">
                                            {{ $person['address'] ?? '' }}</td>
                                        <td style="border:1px solid #ccc; font-size:9px; padding:3px;">
                                            {{ $person['injuries'] ?? '' }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p style="font-size:9px; color:#888; margin-top:4px; font-style:italic;">None reported</p>
                @endif
            </td>
        </tr>

        {{-- Other vehicle details --}}
        <tr>
            <td class="field-label" style="width:35%; vertical-align:top;">
                Other Vehicle(s)
            </td>
            <td class="field-value">
                @if ($involvedVehicles->isNotEmpty())
                    @foreach ($involvedVehicles as $index => $vehicle)
                        <div @if (!$loop->last) style="margin-bottom:6px;" @endif>
                            <strong>Reg. No.:</strong>
                            {{ $vehicle['reg'] ?? 'N/A' }}
                            @if (!empty($vehicle['make']))
                                &nbsp;&nbsp;
                                <strong>Make:</strong>
                                {{ $vehicle['make'] }}
                            @endif
                            @if (!empty($vehicle['model']))
                                &nbsp;&nbsp;
                                <strong>Model:</strong>
                                {{ $vehicle['model'] }}
                            @endif
                        </div>
                    @endforeach
                @else
                    <span style="color:#888;font-style:italic;">None provided</span>
                @endif
            </td>
        </tr>
        <tr>
            <td class="field-label">Name &amp; address of owner of other vehicle</td>
            <td class="field-value">
                {{ $f('owner_fullname') }}
                @if ($f('owner_address'))
                    , {{ $f('owner_address') }}
                @endif
            </td>
        </tr>
        <tr>
            <td class="field-label">Telephone of owner of other vehicle</td>
            <td class="field-value">{{ $f('owner_telephone') }}</td>
        </tr>
        <tr>
            <td class="field-label">Policy number of other vehicle</td>
            <td class="field-value">{{ $f('owner_policy') }}</td>
        </tr>
        <tr>
            <td class="field-label" style="vertical-align:top;">Details of damage to other vehicle</td>
            <td class="field-value tall">{{ $f('other_vehicle_damage') }}</td>
        </tr>
        <tr class="yn-row">
            <td colspan="2" style="border:1px solid #000; padding:4px 6px; font-size:10px;">
                Has any claim been made upon you?
                <span class="yn-box {{ strtolower($f('claim_made')) === 'yes' ? 'selected' : '' }}">YES</span>
                <span class="yn-box {{ strtolower($f('claim_made')) !== 'yes' ? 'selected' : '' }}">NO</span>
                @if ($f('claim_made_details'))
                    <br><span style="font-weight:normal; font-size:10px;">{{ $f('claim_made_details') }}</span>
                @endif
            </td>
        </tr>
    </tbody>
</table>

{{-- ══════════ POLICE & MULTI-POLICY ══════════ --}}
<table style="margin-bottom:14px;">
    <tbody>
        <tr class="yn-row">
            <td colspan="2" style="border:1px solid #000; padding:4px 6px; font-size:10px;">
                Was the accident reported to the police?
                <span class="yn-box {{ $yn('police_report') ? 'selected' : '' }}">YES</span>
                <span class="yn-box {{ !$yn('police_report') ? 'selected' : '' }}">NO</span>
            </td>
        </tr>
        @if ($f('police_report_details'))
            <tr>
                <td class="field-label" style="width:35%">When / Which Police Station</td>
                <td class="field-value">{{ $f('police_report_details') }}</td>
            </tr>
        @endif
        <tr>
            <td class="field-label" style="width:35%">Name of Police Officer who took particulars</td>
            <td class="field-value">{{ $f('officer_details') }}</td>
        </tr>
        <tr class="yn-row">
            <td colspan="2" style="border:1px solid #000; padding:4px 6px; font-size:10px;">
                Do you hold more than one policy indemnifying you in respect of this accident?
                <span class="yn-box {{ strtolower($f('indem_policy')) === 'yes' ? 'selected' : '' }}">YES</span>
                <span class="yn-box {{ strtolower($f('indem_policy')) !== 'yes' ? 'selected' : '' }}">NO</span>
                @if ($f('indem_policy_details'))
                    <br><span style="font-weight:normal; font-size:10px;">{{ $f('indem_policy_details') }}</span>
                @endif
            </td>
        </tr>
    </tbody>
</table>

{{-- ══════════ DECLARATION ══════════ --}}
<div class="declaration-block">
    <p style="font-weight:bold; text-transform:uppercase; margin-bottom:6px;">Declaration:</p>
    <p style="text-align:justify; font-size:10px;">
        I declare that the above statement is true in all respects to the best of my knowledge and belief and I hereby
        leave in the hands of the Company in accordance with the conditions of the Policy the conduct of all claims
        and litigation arising out of this accident and to which the Policy applies, to deal with, to prosecute
        and/or settle as they deem fit without further reference to me; and I undertake to give all such information
        and assistance as the Company may require.
    </p>
    <div class="signature-line" style="margin-top:24px;">
        <div style="flex:1;">
            <p class="sig-label">DATE</p>
            <div class="sig-field">{{ formatDate($f('declaration_date')) }}</div>
        </div>
        <div style="flex:1;">
            <p class="sig-label">SIGNATURE / NAME</p>
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
        and showing the direction in which they were travelling.
    </p>

    <p
        style="font-weight:bold; text-decoration:underline; text-align:center; font-size:11px; margin-bottom:8px; text-transform:uppercase;">
        Position Before Accident
    </p>
    @if (!empty($form['sketch_before']))
        <div style="text-align:center; margin-bottom:20px;">
            <img src="{{ $form['sketch_before'] }}" alt="Sketch Before"
                style="max-width:100%; max-height:220px; border:1px solid #ccc;" />
        </div>
    @else
        <div style="border:1px solid #ccc; height:220px; margin-bottom:20px; background:#fafafa;"></div>
    @endif

    <p
        style="font-weight:bold; text-decoration:underline; text-align:center; font-size:11px; margin-bottom:8px; text-transform:uppercase;">
        Position After Accident
    </p>
    @if (!empty($form['sketch_after']))
        <div style="text-align:center; margin-bottom:20px;">
            <img src="{{ $form['sketch_after'] }}" alt="Sketch After"
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
