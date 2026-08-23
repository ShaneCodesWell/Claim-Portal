@php
    $form = $guide?->data ?? [];
    $f = fn($key) => $form[$key] ?? '';
    $yesNo = fn($key) => strtolower($form[$key] ?? '') === 'yes';

    // References (checkboxes) – stored as array or comma-separated string
    $references = $form['references'] ?? [];
    if (is_string($references)) {
        $references = array_map('trim', explode(',', $references));
    }
    $references = array_filter($references);

    // Manager comments
    $comments = $form['comments'] ?? [];

    // Reinsurance type (array)
    $reinsuranceTypes = $form['reinsurance_type'] ?? [];
    if (is_string($reinsuranceTypes)) {
        $reinsuranceTypes = array_map('trim', explode(',', $reinsuranceTypes));
    }
    $reinsuranceTypes = array_filter($reinsuranceTypes);

    // Facultative details
    $facultative = $form['facultative'] ?? [];
    if (!is_array($facultative)) {
        $facultative = [];
    }
    // Limit to 2 entries
    $facultative = array_slice($facultative, 0, 2);

    // Format date helper (reuse if needed)
    if (!function_exists('formatDate')) {
        function formatDate($date)
        {
            if (empty($date)) {
                return '';
            }
            try {
                return \Carbon\Carbon::parse($date)->format('F j, Y');
            } catch (\Exception $e) {
                return $date;
            }
        }
    }
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
<div class="form-title">Motor Claims Liability Guide</div>

{{-- ══════════ NOTE ══════════ --}}
<p style="font-size:10px; margin-bottom:14px; text-align:justify;">
    For internal use in assessing motor claims liability. Please complete all sections accurately.
</p>

{{-- ══════════ CLAIM INFORMATION ══════════ --}}
<p class="section-heading">Claim Information</p>
<table style="margin-bottom:10px;">
    <tbody>
        <tr>
            <td class="field-label" style="width:35%">Branch / Dept.</td>
            <td class="field-value">{{ $f('branch_dept') }}</td>
            <td class="field-label" style="width:25%">Date</td>
            <td class="field-value">{{ formatDate($f('form_date')) }}</td>
        </tr>
        <tr>
            <td class="field-label">Claim Number</td>
            <td class="field-value">{{ $f('claim_number') }}</td>
            <td class="field-label">Vehicle Number</td>
            <td class="field-value">{{ $f('vehicle_number') }}</td>
        </tr>
    </tbody>
</table>

{{-- ══════════ REFERENCES ══════════ --}}
<p class="section-heading">References</p>
<table style="margin-bottom:10px;">
    <tbody>
        <tr>
            <td class="field-value" colspan="4" style="padding:6px;">
                @if (!empty($references))
                    <div style="display:flex; flex-wrap:wrap; gap:20px;">
                        @foreach ($references as $ref)
                            <span style="font-size:10px; font-weight:bold;">✔ {{ $ref }}</span>
                        @endforeach
                    </div>
                @else
                    <span style="color:#999; font-style:italic;">None selected</span>
                @endif
            </td>
        </tr>
    </tbody>
</table>

{{-- ══════════ CLAIM DETAILS ══════════ --}}
<p class="section-heading">Claim Details</p>
<table style="margin-bottom:10px;">
    <tbody>
        <tr>
            <td class="field-label" style="width:35%">Insured</td>
            <td class="field-value" style="width:65%">{{ $f('insured') }}</td>
        </tr>
        <tr>
            <td class="field-label">Insured's Name on A.R.F</td>
            <td class="field-value">{{ $f('insured_name_arf') }}</td>
        </tr>
        <tr>
            <td class="field-label">Vehicle Owner's Name on P/R</td>
            <td class="field-value">{{ $f('vehicle_owner_name_pr') }}</td>
        </tr>
        <tr>
            <td class="field-label">Driver's Name on A.R.F.</td>
            <td class="field-value">{{ $f('driver_name_arf') }}</td>
        </tr>
        <tr>
            <td class="field-label">Driver's Name on P/R</td>
            <td class="field-value">{{ $f('driver_name_pr') }}</td>
        </tr>
        <tr>
            <td class="field-label">Date License was First Issued</td>
            <td class="field-value">{{ formatDate($f('license_first_issued')) }}</td>
        </tr>
        <tr>
            <td class="field-label">Driver's Age</td>
            <td class="field-value">{{ $f('driver_age') }}</td>
        </tr>
        <tr>
            <td class="field-label">Period of Insurance</td>
            <td class="field-value">
                {{ formatDate($f('period_of_insurance_from')) }}
                @if ($f('period_of_insurance_from') && $f('period_of_insurance_to'))
                    &nbsp; to &nbsp;
                @endif
                {{ formatDate($f('period_of_insurance_to')) }}
            </td>
        </tr>
        <tr>
            <td class="field-label">Date of Accident</td>
            <td class="field-value">{{ formatDate($f('date_of_accident')) }}</td>
        </tr>
        <tr>
            <td class="field-label">Use Clause</td>
            <td class="field-value">{{ $f('use_clause') }}</td>
        </tr>
        <tr>
            <td class="field-label">Premium Payable</td>
            <td class="field-value">{{ $f('premium_payable') }}</td>
        </tr>
        <tr>
            <td class="field-label">Premium Paid</td>
            <td class="field-value">{{ $f('premium_paid') }}</td>
        </tr>
        <tr>
            <td class="field-label">Cover</td>
            <td class="field-value">{{ $f('cover') }}</td>
        </tr>
    </tbody>
</table>

{{-- ══════════ ASSESSMENT ══════════ --}}
<p class="section-heading">Assessment</p>
<table style="margin-bottom:10px;">
    <tbody>
        <tr>
            <td class="field-label" style="width:35%; vertical-align:top;">Brief Facts of the Accident</td>
            <td class="field-value tall" style="min-height:50px;">{{ $f('brief_facts') }}</td>
        </tr>
        <tr>
            <td class="field-label" style="vertical-align:top;">Recommendation on Liability</td>
            <td class="field-value tall" style="min-height:50px;">{{ $f('recommendation') }}</td>
        </tr>
        <tr>
            <td class="field-label">Staff's Signature / Username</td>
            <td class="field-value" style="font-family: 'Brush Script MT', cursive; font-size:16px;">
                {{ $f('staff_signature') }}</td>
        </tr>
    </tbody>
</table>

{{-- ══════════ MANAGER / SUPERVISOR COMMENTS ══════════ --}}
<p class="section-heading">Manager's / Supervisor's Comment</p>
<table style="margin-bottom:10px;">
    <tbody>
        @php
            $commentUnits = [
                'claims_unit' => 'Claims Unit',
                'survey' => 'Survey',
                'supervisor' => 'Supervisor',
                'survey_team' => 'Survey Team',
            ];
        @endphp
        @foreach ($commentUnits as $key => $label)
            <tr>
                <td
                    style="width:25%; font-weight:bold; font-size:10px; vertical-align:top; padding:4px 6px; border:1px solid #000;">
                    {{ $label }}
                </td>
                <td style="width:45%; border:1px solid #000; padding:4px 6px; vertical-align:top;">
                    {{ $comments[$key]['text'] ?? '' }}
                </td>
                <td
                    style="width:30%; border:1px solid #000; padding:4px 6px; font-family: 'Brush Script MT', cursive; font-size:14px; vertical-align:top;">
                    {{ $comments[$key]['signature'] ?? '' }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- ══════════ REINSURANCE ══════════ --}}
<p class="section-heading">Reinsurance</p>
<table style="margin-bottom:10px;">
    <tbody>
        <tr>
            <td class="field-label" style="width:35%">Would there be a reinsurance recovery?</td>
            <td class="field-value">
                @php $rec = strtoupper($f('reinsurance_recovery')); @endphp
                <span class="yn-box {{ $rec === 'Y' ? 'selected' : '' }}">Yes</span>
                <span class="yn-box {{ $rec === 'N' ? 'selected' : '' }}">No</span>
            </td>
        </tr>
        @if ($rec === 'Y')
            <tr>
                <td class="field-label">Type</td>
                <td class="field-value">
                    @php
                        $types = $reinsuranceTypes;
                    @endphp
                    @if (in_array('Treaty', $types))
                        <span class="yn-box selected">Treaty</span>
                    @else
                        <span class="yn-box">Treaty</span>
                    @endif
                    @if (in_array('Facultative', $types))
                        <span class="yn-box selected">Facultative</span>
                    @else
                        <span class="yn-box">Facultative</span>
                    @endif
                </td>
            </tr>
        @endif
        <tr>
            <td class="field-label">Has the Reinsurance Department been notified?</td>
            <td class="field-value">
                @php $notif = strtoupper($f('reinsurance_notified')); @endphp
                <span class="yn-box {{ $notif === 'Y' ? 'selected' : '' }}">Yes</span>
                <span class="yn-box {{ $notif === 'N' ? 'selected' : '' }}">No</span>
            </td>
        </tr>
        @if ($notif === 'Y')
            <tr>
                <td class="field-label">Date of Notification</td>
                <td class="field-value">{{ formatDate($f('notification_date')) }}</td>
            </tr>
        @endif
        {{-- Facultative details (only if Facultative is checked and recovery is Yes) --}}
        @if ($rec === 'Y' && in_array('Facultative', $reinsuranceTypes) && !empty($facultative))
            <tr>
                <td colspan="2" style="border:1px solid #000; padding:6px;">
                    <p style="font-weight:bold; font-size:10px; margin-bottom:4px;">Details of Facultative Reinsurance
                    </p>
                    <table style="width:100%; border-collapse:collapse; margin:0;">
                        <thead>
                            <tr>
                                <th style="border:1px solid #000; font-size:9px; padding:3px; background:#f0f0f0;">
                                    Company</th>
                                <th style="border:1px solid #000; font-size:9px; padding:3px; background:#f0f0f0;">
                                    Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($facultative as $item)
                                <tr>
                                    <td style="border:1px solid #000; font-size:9px; padding:3px;">
                                        {{ $item['company'] ?? '' }}</td>
                                    <td style="border:1px solid #000; font-size:9px; padding:3px;">
                                        {{ $item['percentage'] ?? '' }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
        @endif
    </tbody>
</table>

{{-- ══════════ MANAGER'S DECISION ══════════ --}}
<p class="section-heading">Manager's Decision</p>
<table style="margin-bottom:10px;">
    <tbody>
        <tr>
            <td class="field-label" style="width:35%">Decision</td>
            <td class="field-value">{{ $f('manager_decision') }}</td>
        </tr>
        <tr>
            <td class="field-label">Signature</td>
            <td class="field-value" style="font-family: 'Brush Script MT', cursive; font-size:16px;">
                {{ $f('manager_signature') }}</td>
        </tr>
    </tbody>
</table>

{{-- ══════════ FOOTER ══════════ --}}
<div class="footer">
    EMERGENCY CONTACT NUMBERS: VANGUARD +233 302666485 | CLAIMS HOTLINE +233 244334407
</div>
