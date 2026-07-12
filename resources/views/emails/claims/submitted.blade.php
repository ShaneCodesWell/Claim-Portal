<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Claim Submitted</title>
</head>

<body style="margin:0; padding:0; background-color:#f4f5f7; font-family: Arial, Helvetica, sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
        style="background-color:#f4f5f7; padding:32px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0"
                    style="background-color:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.08);">

                    {{-- Header --}}
                    <tr>
                        <td style="background-color:#1e3a8a; padding:24px 32px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
                                        <span style="color:#ffffff; font-size:18px; font-weight:bold;">Vanguard
                                            Assurance</span>
                                    </td>
                                    <td align="right">
                                        <span style="color:#c7d2fe; font-size:13px;">Claims Portal</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:32px;">
                            <h1 style="margin:0 0 8px 0; font-size:20px; color:#111827;">New Claim Submitted</h1>
                            <p style="margin:0 0 24px 0; font-size:14px; color:#6b7280; line-height:1.5;">
                                A new claim has been submitted and is awaiting review on the Claims Dashboard.
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="border-collapse:collapse; margin-bottom:24px;">
                                {{-- <tr>
                                    <td
                                        style="padding:10px 0; border-bottom:1px solid #e5e7eb; font-size:13px; color:#6b7280; width:40%;">
                                        Claim Number</td>
                                    <td
                                        style="padding:10px 0; border-bottom:1px solid #e5e7eb; font-size:14px; color:#111827; font-weight:bold;">
                                        {{ $claim->claim_number }}</td>
                                </tr> --}}
                                <tr>
                                    <td
                                        style="padding:10px 0; border-bottom:1px solid #e5e7eb; font-size:13px; color:#6b7280;">
                                        Customer</td>
                                    <td
                                        style="padding:10px 0; border-bottom:1px solid #e5e7eb; font-size:14px; color:#111827;">
                                        {{ $claim->customer->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td
                                        style="padding:10px 0; border-bottom:1px solid #e5e7eb; font-size:13px; color:#6b7280;">
                                        Policy Number</td>
                                    <td
                                        style="padding:10px 0; border-bottom:1px solid #e5e7eb; font-size:14px; color:#111827;">
                                        {{ $claim->policy->policy_number ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td
                                        style="padding:10px 0; border-bottom:1px solid #e5e7eb; font-size:13px; color:#6b7280;">
                                        Claim Type</td>
                                    <td
                                        style="padding:10px 0; border-bottom:1px solid #e5e7eb; font-size:14px; color:#111827; text-transform:capitalize;">
                                        {{ str_replace('_', ' ', $claim->claim_type) }}</td>
                                </tr>
                                <tr>
                                    <td
                                        style="padding:10px 0; border-bottom:1px solid #e5e7eb; font-size:13px; color:#6b7280;">
                                        Branch</td>
                                    <td
                                        style="padding:10px 0; border-bottom:1px solid #e5e7eb; font-size:14px; color:#111827;">
                                        {{ $claim->branch->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td
                                        style="padding:10px 0; border-bottom:1px solid #e5e7eb; font-size:13px; color:#6b7280;">
                                        Submitted Via</td>
                                    <td
                                        style="padding:10px 0; border-bottom:1px solid #e5e7eb; font-size:14px; color:#111827;">
                                        {{ $claim->initiated_by_staff ? 'Staff (on behalf of customer)' : 'Customer Portal' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 0; font-size:13px; color:#6b7280;">Submitted At</td>
                                    <td style="padding:10px 0; font-size:14px; color:#111827;">
                                        {{ $claim->submitted_at?->format('d M Y, h:i A') }}</td>
                                </tr>
                            </table>

                            <table role="presentation" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="border-radius:6px; background-color:#1e3a8a;">
                                        <a href="{{ route('staff.claims.show', $claim) }}"
                                            style="display:inline-block; padding:12px 24px; font-size:14px; color:#ffffff; text-decoration:none; font-weight:bold;">
                                            View Claim on Dashboard
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:20px 32px; background-color:#f9fafb; border-top:1px solid #e5e7eb;">
                            <p style="margin:0; font-size:12px; color:#9ca3af;">
                                This is an automated notification from the Vanguard Assurance Claims Portal.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>
