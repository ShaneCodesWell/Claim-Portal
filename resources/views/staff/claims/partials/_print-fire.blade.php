@php
    $form = $claim->form_data ?? [];
    $f = fn($key) => $form[$key] ?? '';
    $yn = fn($key) => strtolower($form[$key] ?? '') === 'yes';
    $customer = $claim->customer;
    $policy = $claim->policy;

    // Particulars of claim rows — stored as array of objects in form_data
    $particulars = $form['particulars'] ?? [];
    // Pad to at least 10 rows for the table
    while (count($particulars) < 10) {
        $particulars[] = [
            'quantity' => '',
            'description' => '',
            'date_place_purchase' => '',
            'price_paid' => '',
            'deduction' => '',
            'value_before_loss' => '',
            'value_salvage' => '',
            'amount_claimed' => '',
        ];
    }

    // Insurances in force rows
    $insurances = $form['insurances_in_force'] ?? [];
    while (count($insurances) < 5) {
        $insurances[] = [
            'insurer' => '',
            'policy_number' => '',
            'sum_insured' => '',
        ];
    }
@endphp

{{-- ══════════ HEADER ══════════ --}}
<div class="header">
    <div class="logo-block" style="text-align:left;">
        <img src="{{ asset('images/Vanguard.png') }}" alt="Vanguard Assurance" style="height:56px;" />
    </div>
    <div class="header-right" style="text-align:right;">
        <p class="company-name" style="font-weight:bold; font-size:12px; margin-bottom:3px;">VANGUARD ASSURANCE COMPANY
            LIMITED</p>
        <p>No. 25 Independence Avenue</p>
        <p>P.O. Box 1868, Accra, Ghana</p>
        <p>Tel: +233 0302 666 485-7/782 921-2</p>
        <p>Website: www.vanguardassurance.com</p>
    </div>
</div>

{{-- ══════════ FORM TITLE ══════════ --}}
<div class="form-title">Fire Claim Form</div>

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
            <td class="field-label" style="width:20%">Policy No.</td>
            <td class="field-value" style="width:30%">{{ $policy?->policy_number ?? $f('policy_number') }}</td>
            <td class="field-label" style="width:20%">Renewal Date</td>
            <td class="field-value" style="width:30%">{{ $f('renewal_date') }}</td>
        </tr>
    </tbody>
</table>

{{-- ══════════ MAIN FIELDS ══════════ --}}
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
            <td class="field-label">Nature of Business</td>
            <td class="field-value">{{ $f('nature_of_business') }}</td>
        </tr>
        <tr>
            <td class="field-label">Date and Time of Accident</td>
            <td class="field-value">{{ $f('date_time_of_accident') }}</td>
        </tr>
        <tr>
            <td class="field-label">Description of Incident</td>
            <td class="field-value tall" style="min-height:40px;">{{ $f('description_of_incident') }}</td>
        </tr>
        <tr>
            <td class="field-label">State Exact Location</td>
            <td class="field-value">{{ $f('exact_location') }}</td>
        </tr>
        <tr>
            <td class="field-label">Nature of Damage / Loss to Property</td>
            <td class="field-value tall">{{ $f('nature_of_damage') }}</td>
        </tr>
        <tr>
            <td class="field-label">Name and Address of Any Person Injured</td>
            <td class="field-value tall">{{ $f('injured_person') }}</td>
        </tr>
        <tr class="yn-row">
            <td class="field-label">Was it Reported to the Police?</td>
            <td class="field-value">
                <span class="yn-box {{ $yn('reported_to_police') ? 'selected' : '' }}">YES</span>
                <span class="yn-box {{ !$yn('reported_to_police') ? 'selected' : '' }}">NO</span>
            </td>
        </tr>
        <tr>
            <td class="field-label" style="vertical-align:top;">Names and Addresses of all Witnesses and the Number of
                the Police who took Evidence</td>
            <td class="field-value tall" style="min-height:44px;">{{ $f('witnesses') }}</td>
        </tr>
        <tr>
            <td class="field-label" style="vertical-align:top;">State any Other Information Necessary</td>
            <td class="field-value tall" style="min-height:44px;">{{ $f('other_information') }}</td>
        </tr>
    </tbody>
</table>

{{-- ══════════ SIGNATURE BLOCK ══════════ --}}
<table style="margin-bottom:14px;">
    <tbody>
        <tr>
            <td class="field-label" style="width:35%">Name of Insured / Claimant</td>
            <td class="field-value">{{ $f('claimant_name') ?: $customer?->full_name ?? '' }}</td>
        </tr>
        <tr>
            <td colspan="2" style="padding:4px 6px; font-size:10px;">
                Witness my hand this
                <span
                    style="border-bottom:1px solid #000; display:inline-block; min-width:80px; padding:0 4px;">{{ $f('witness_day') }}</span>
                Day of
                <span
                    style="border-bottom:1px solid #000; display:inline-block; min-width:120px; padding:0 4px;">{{ $f('witness_month_year') }}</span>
                20<span
                    style="border-bottom:1px solid #000; display:inline-block; min-width:40px; padding:0 4px;">{{ $f('witness_year_suffix') }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align:right; padding:4px 6px; font-size:10px;">
                <strong>Signature</strong>
                <span
                    style="border-bottom:1px solid #000; display:inline-block; min-width:200px; padding:0 4px; margin-left:8px;">{{ $f('claimant_signature') }}</span>
            </td>
        </tr>
    </tbody>
</table>

<hr style="border:none; border-top:1px solid #000; margin-bottom:14px;" />

{{-- ══════════ PARTICULARS OF CLAIM ══════════ --}}
<p class="section-heading" style="text-align:center; font-size:11px;">Particulars of Claim</p>
<table style="margin-bottom:14px; font-size:9px;">
    <thead>
        <tr>
            <th style="width:6%">Quantity</th>
            <th style="width:22%">Description of the Property Destroyed or Damaged</th>
            <th style="width:12%">Date &amp; Place of Purchase</th>
            <th style="width:10%">Price Paid</th>
            <th style="width:16%">Deduction for Depreciation, Wear &amp; Tear, etc.</th>
            <th style="width:13%">Value immediately before the Loss</th>
            <th style="width:9%">Value of Salvage</th>
            <th style="width:12%">Amount Claimed</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($particulars as $row)
            <tr>
                <td style="min-height:16px; height:20px;">{{ $row['quantity'] ?? '' }}</td>
                <td>{{ $row['description'] ?? '' }}</td>
                <td>{{ $row['date_place_purchase'] ?? '' }}</td>
                <td>{{ $row['price_paid'] ?? '' }}</td>
                <td>{{ $row['deduction'] ?? '' }}</td>
                <td>{{ $row['value_before_loss'] ?? '' }}</td>
                <td>{{ $row['value_salvage'] ?? '' }}</td>
                <td>{{ $row['amount_claimed'] ?? '' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- ══════════ STATEMENT OF INSURANCES IN FORCE ══════════ --}}
<table style="font-size:9px; margin-bottom:14px;">
    <thead>
        <tr>
            <th colspan="8" style="text-align:left; font-size:10px; background:#fff; border:none; padding:4px 0;">
                STATEMENT OF INSURANCES IN FORCE UPON THE PROPERTY DESTROYED OR DAMAGED
            </th>
        </tr>
        <tr>
            <th style="width:20%">Insurer</th>
            <th style="width:30%">Policy Number</th>
            <th style="width:20%">Sum Insured</th>
            <th style="width:30%">&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($insurances as $ins)
            <tr style="height:20px;">
                <td>{{ $ins['insurer'] ?? '' }}</td>
                <td>{{ $ins['policy_number'] ?? '' }}</td>
                <td>{{ $ins['sum_insured'] ?? '' }}</td>
                <td>&nbsp;</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="3" style="text-align:right; font-weight:bold; font-size:9px;">Amount Claimed</td>
            <td style="background:#d0d0d0; font-weight:bold;">{{ $f('total_amount_claimed') }}</td>
        </tr>
        <tr>
            <td colspan="3" style="text-align:right; font-weight:bold; font-size:9px;">Total value of property to
                which your policy relates (see N.B on page 3)</td>
            <td style="background:#d0d0d0;">{{ $f('total_property_value') }}</td>
        </tr>
    </tbody>
</table>

{{-- ══════════ DECLARATION ══════════ --}}
<div class="declaration-block">
    <p style="font-weight:bold; text-transform:uppercase; margin-bottom:6px;">Declaration:</p>
    <p>
        I declare that the above statement is true in all respects to the best of my knowledge and belief and I hereby
        leave in the hands of the Company in accordance with the conditions of the Policy the conduct of all claims
        and litigation arising out of this accident and to which the Policy applies, to deal with, to prosecute and/or
        settle as they deem fit without further reference to me; and I undertake to give all such information and
        assistance as the Company may require.
    </p>
    <div class="signature-line" style="margin-top:20px;">
        <div>
            <p class="sig-label">NAME(s) IN FULL</p>
            <div class="sig-field">{{ $f('digital_signature') }}</div>
        </div>
        <div style="flex:0.5">
            <p class="sig-label">DATE</p>
            <div class="sig-field">{{ $f('declaration_date') }}</div>
        </div>
    </div>
    <div style="margin-top:14px;">
        <p class="sig-label">SIGNATURE(s)</p>
        <div class="sig-field" style="min-height:30px; border-bottom:1px solid #000;"></div>
    </div>
</div>

{{-- ══════════ FOOTER ══════════ --}}
<div class="footer">
    EMERGENCY CONTACT NUMBERS: VANGUARD +233 302666485 | CLAIMS HOTLINE +233 244334407
</div>
