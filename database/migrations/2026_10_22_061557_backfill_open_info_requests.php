<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * One-off backfill: claims that are already in "pending_info" but have no
 * open row in claim_info_requests (they were requested before this feature).
 * Safe to run more than once: it only touches claims with no pending request.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $claimIds = DB::table('claims')
            ->where('status', 'pending_info')
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('claim_info_requests')
                    ->whereColumn('claim_info_requests.claim_id', 'claims.id')
                    ->where('claim_info_requests.status', 'pending');
            })
            ->pluck('id');

        $created = 0;
        $skipped = [];

        foreach ($claimIds as $claimId) {
            // Table name assumed: change if your activity table is named differently.
            $activity = DB::table('claim_activities')
                ->where('claim_id', $claimId)
                ->where('action', 'info_requested')
                ->whereNotNull('user_id')
                ->orderByDesc('id')
                ->first();

            if (! $activity) {
                // Status was set to pending_info without a request (e.g. via the status dropdown).
                $skipped[] = $claimId;
                continue;
            }

            DB::table('claim_info_requests')->insert([
                'claim_id'        => $claimId,
                'requested_by'    => $activity->user_id,
                'note'            => $activity->note ?: 'Additional information was requested by the claims team.',
                'previous_status' => 'under_review',   // original status wasn't recorded; staff can adjust
                'status'          => 'pending',
                'sms_status'      => 'skipped',        // predates the SMS feature; shows as a grey badge
                'created_at'      => $activity->created_at,
                'updated_at'      => now(),
            ]);

            $created++;
        }

        Log::info('Info request backfill complete', [
            'created'           => $created,
            'skipped_claim_ids' => $skipped,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentionally empty: backfilled rows can't be told apart from real ones.
    }
};
