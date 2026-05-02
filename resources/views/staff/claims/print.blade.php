{{-- print.blade.php --}}
@php
    $isModal = request()->header('X-Requested-With') === 'XMLHttpRequest';
    $type = strtolower($claim->claim_type ?? '');
    $form = $claim->form_data ?? [];
    $f = fn($key) => $form[$key] ?? '';
    $yn = fn($key) => strtolower($form[$key] ?? '') === 'yes';
@endphp

@if (!$isModal)
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8" />
        <title>{{ $claim->claim_number }} — Print</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: Arial, sans-serif;
                font-size: 11px;
                color: #000;
                background: #fff;
                padding: 20px 30px;
            }

            /* ── Header ── */
            .header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 14px;
            }

            .header-left p {
                margin-bottom: 3px;
                font-size: 11px;
            }

            .header-left .company-name {
                font-weight: bold;
                font-size: 12px;
                margin-bottom: 6px;
            }

            .logo-block {
                text-align: right;
            }

            .logo-block img {
                height: 48px;
                object-fit: contain;
            }

            .logo-block .tagline {
                font-size: 9px;
                color: #555;
                margin-top: 2px;
            }

            /* ── Title ── */
            .form-title {
                text-align: center;
                font-size: 14px;
                font-weight: bold;
                text-decoration: underline;
                text-transform: uppercase;
                margin: 12px 0 14px;
            }

            /* ── Note Box ── */
            .note-box {
                margin-bottom: 14px;
            }

            .note-box .note-heading {
                font-weight: bold;
                font-size: 11px;
                margin-bottom: 6px;
            }

            .note-box ul {
                padding-left: 20px;
            }

            .note-box ul li {
                margin-bottom: 3px;
                font-weight: bold;
                font-size: 10px;
            }

            .note-box ul li.sub {
                font-weight: normal;
            }

            .note-box ul.sub-list {
                list-style-type: decimal;
                margin-top: 3px;
            }

            /* ── Section headings ── */
            .section-heading {
                font-weight: bold;
                font-size: 11px;
                margin: 14px 0 6px;
                text-transform: uppercase;
            }

            /* ── Tables ── */
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 10px;
            }

            th,
            td {
                border: 1px solid #000;
                padding: 4px 6px;
                font-size: 10px;
                vertical-align: top;
            }

            th {
                font-weight: bold;
                background: #f0f0f0;
                text-transform: uppercase;
                font-size: 9px;
            }

            .field-label {
                font-weight: bold;
                text-transform: uppercase;
                width: 35%;
                background: #fafafa;
            }

            .field-value {
                width: 65%;
                min-height: 18px;
            }

            .field-value.tall {
                min-height: 36px;
            }

            /* ── Yes/No boxes ── */
            .yn-row td {
                vertical-align: middle;
            }

            .yn-box {
                display: inline-block;
                border: 1px solid #000;
                padding: 2px 8px;
                font-size: 10px;
                font-weight: bold;
                margin-left: 4px;
            }

            .yn-box.selected {
                background: #000;
                color: #fff;
            }

            /* ── Footer ── */
            .footer {
                margin-top: 20px;
                border-top: 2px solid #000;
                padding-top: 8px;
                text-align: center;
                font-weight: bold;
                font-size: 10px;
            }

            /* ── Declaration ── */
            .declaration-block {
                margin-top: 14px;
                border: 1px solid #000;
                padding: 10px;
            }

            .declaration-block p {
                margin-bottom: 6px;
                font-size: 10px;
            }

            .signature-line {
                display: flex;
                justify-content: space-between;
                margin-top: 16px;
                gap: 20px;
            }

            .signature-line .sig-field {
                flex: 1;
                border-bottom: 1px solid #000;
                padding-bottom: 2px;
                font-size: 10px;
            }

            .signature-line .sig-label {
                font-size: 9px;
                color: #555;
                margin-bottom: 14px;
            }

            /* ── Print ── */
            @media print {
                body {
                    padding: 10px 15px;
                }

                .no-print {
                    display: none !important;
                }

                @page {
                    margin: 1cm;
                }
            }
        </style>
    </head>

    <body>
        <div class="no-print" style="text-align:right; margin-bottom:12px;">
            <button onclick="window.print()"
                style="background:#1a3a5c;color:#fff;border:none;padding:8px 20px;border-radius:6px;font-size:12px;cursor:pointer;font-weight:bold;">
                &#128438; Print / Save as PDF
            </button>
            <button onclick="window.close()"
                style="background:#f3f4f6;color:#374151;border:1px solid #d1d5db;padding:8px 16px;border-radius:6px;font-size:12px;cursor:pointer;margin-left:8px;">
                Close
            </button>
        </div>
@endif

@if ($type === 'general_accident')
    @include('staff.claims.partials._print-travel', compact('claim', 'form', 'f', 'yn'))
@elseif($type === 'motor')
    @include('staff.claims.partials._print-motor', compact('claim', 'form', 'f', 'yn'))
@elseif($type === 'fire')
    @include('staff.claims.partials._print-fire', compact('claim', 'form', 'f', 'yn'))
@else
    <p style="padding:20px;color:#888;">No print template available for this claim type.</p>
@endif

@if (!$isModal)
    </body>

    </html>
@endif
