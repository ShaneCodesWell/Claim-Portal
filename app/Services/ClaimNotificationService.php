<?php
namespace App\Services;

use App\Models\Claim;
use App\Models\ClaimNotification;
use Illuminate\Support\Facades\Log;

class ClaimNotificationService
{
    public function __construct(private ArkeselService $sms)
    {}

    // Call this after under_review is set — fires once per claim lifetime
    public function notifyUnderReview(Claim $claim): void
    {
        $this->sendOnce($claim, 'under_review', function (string $phone) use ($claim) {
            $this->sms->sendSms($phone,
                "Dear {$this->customerName($claim)}, your claim has been received and is currently under review. We will keep you updated. - Vanguard Assurance"
            );
        });
    }

    public function notifyApproved(Claim $claim): void
    {
        $this->sendOnce($claim, 'approved', function (string $phone) use ($claim) {
            $this->sms->sendSms($phone,
                "Dear {$this->customerName($claim)}, we are pleased to inform you that your claim has been approved. Our team will be in touch regarding next steps. - Vanguard Assurance"
            );
        });
    }

    public function notifyRejected(Claim $claim): void
    {
        $this->sendOnce($claim, 'rejected', function (string $phone) use ($claim) {
            $this->sms->sendSms($phone,
                "Dear {$this->customerName($claim)}, after careful review, your claim could not be approved. Please contact us for more information. - Vanguard Assurance"
            );
        });
    }

    public function notifyFinalized(Claim $claim): void
    {
        $this->sendOnce($claim, 'closed', function (string $phone) use ($claim) {
            $this->sms->sendSms(
                $phone,
                "Dear {$this->customerName($claim)}, we are pleased to inform you that your claim has been finalized and forwarded to our Finance team for payment processing. You will be notified once payment has been completed. Thank you for choosing Vanguard Assurance."
            );
        });
    }

    // ── Core guard — skips silently if already sent ───────────────────────────
    private function sendOnce(Claim $claim, string $type, callable $sender): void
    {
        $phone = $this->resolvePhone($claim);

        if (! $phone) {
            Log::warning("ClaimNotificationService: no phone for claim {$claim->claim_number}");
            return;
        }

        // DB-level unique constraint backs this up as a second safety net
        $alreadySent = ClaimNotification::where('claim_id', $claim->id)
            ->where('type', $type)
            ->exists();

        if ($alreadySent) {
            return;
        }

        $sent = $sender($phone);

        // Record regardless of SMS success so we don't spam on retries
        // If you'd rather only record on success, wrap this in: if ($sent)
        if ($sent) {
            ClaimNotification::create([
                'claim_id' => $claim->id,
                'type'     => $type,
                'phone'    => $phone,
                'sent_at'  => now(),
            ]);
        }
    }

    private function resolvePhone(Claim $claim): ?string
    {
        $raw = $claim->customer?->phone;
        if (! $raw) {
            return null;
        }

        // Normalise to 233XXXXXXXXX — strip leading 0 and prepend 233
        $digits = preg_replace('/\D/', '', $raw);
        if (str_starts_with($digits, '0')) {
            $digits = '233' . substr($digits, 1);
        }

        return $digits;
    }

    private function customerName(Claim $claim): string
    {
        return $claim->customer?->name ?? 'Valued Customer';
    }
}
