<?php

namespace App\Jobs;

use App\Models\Agent;
use App\Services\GlimsApiService;
use App\Services\PolicySyncService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncAgentPoliciesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 600; // large portfolios + concurrent batches; still bounded

    private const RISK_FETCH_CONCURRENCY = 15;
    private const PERSIST_BATCH_SIZE     = 15; // matches fetch concurrency — persist each batch as it's enriched

    public function __construct(public Agent $agent) {}

    public function handle(GlimsApiService $glims, PolicySyncService $policySync): void
    {
        $glimsAgentCode = $this->agent->glims_agent_code;

        if (! $glimsAgentCode) {
            Log::info('SyncAgentPoliciesJob: agent has no glims_agent_code, skipping', [
                'agent_id' => $this->agent->id,
            ]);
            return;
        }

        Log::info('SyncAgentPoliciesJob: starting sync', [
            'agent_id'   => $this->agent->id,
            'agent_code' => $glimsAgentCode,
        ]);

        // ── Step 1: collect every raw row across all pages first ────────────
        $allRows = [];
        $page    = 1;

        do {
            try {
                $response = $glims->getAgentPolicies($glimsAgentCode, $page);
            } catch (\Exception $e) {
                Log::error('SyncAgentPoliciesJob: API call failed', [
                    'agent_id' => $this->agent->id,
                    'page'     => $page,
                    'error'    => $e->getMessage(),
                ]);
                break;
            }

            if ($response->failed()) {
                Log::error('SyncAgentPoliciesJob: non-200 response', [
                    'agent_id' => $this->agent->id,
                    'page'     => $page,
                    'status'   => $response->status(),
                ]);
                break;
            }

            $body    = $response->json();
            $results = $body['results'] ?? [];
            $count   = $body['count'] ?? 0;

            if (empty($results)) {
                break;
            }

            $allRows = array_merge($allRows, $results);

            $fetched = $page * count($results);
            $page++;
        } while ($fetched < $count);

        if (empty($allRows)) {
            Log::info('SyncAgentPoliciesJob: no policies found', [
                'agent_id' => $this->agent->id,
            ]);
            $this->agent->update(['glims_last_synced_at' => now()]);
            return;
        }

        // ── Step 2: group flat rows into one record per policy_number ───────
        $policies = $glims->groupPolicyRows($allRows);

        Log::info('SyncAgentPoliciesJob: grouped rows into policies', [
            'agent_id'     => $this->agent->id,
            'raw_rows'     => count($allRows),
            'policy_count' => count($policies),
        ]);

        // ── Step 3 & 4: enrich + persist in batches ──────────────────────────
        // Each batch fetches risk detail concurrently (Http::pool), then
        // persists immediately — so a failure partway through the run still
        // leaves earlier batches saved, and progress is visible in the DB
        // as the job runs rather than all-or-nothing at the very end.
        $synced = 0;
        $errors = 0;
        $batchNum = 0;
        $totalBatches = (int) ceil(count($policies) / self::PERSIST_BATCH_SIZE);
        $failedPolicyNumbers = [];

        foreach (array_chunk($policies, self::PERSIST_BATCH_SIZE) as $batch) {
            $batchNum++;

            $policyNumbers = collect($batch)->pluck('POLICY_NUMBER')->filter()->values()->all();

            $riskMap = $glims->getRisksForPolicies($policyNumbers, self::RISK_FETCH_CONCURRENCY);

            foreach ($batch as $policy) {
                $policyNumber = $policy['POLICY_NUMBER'] ?? null;
                if (! $policyNumber) {
                    continue;
                }

                if (! empty($riskMap[$policyNumber])) {
                    $policy['risks']    = $riskMap[$policyNumber];
                    $policy['is_fleet'] = count($riskMap[$policyNumber]) > 1;
                } else {
                    // Enrichment failed for this one — track it for a retry pass
                    // rather than leaving it permanently on placeholder data.
                    $failedPolicyNumbers[] = $policyNumber;
                }

                try {
                    $policySync->syncAgentPolicyFromGlims($policy, $this->agent);
                    $synced++;
                } catch (\Exception $e) {
                    $errors++;
                    Log::warning('SyncAgentPoliciesJob: upsert failed for one policy', [
                        'agent_id'      => $this->agent->id,
                        'policy_number' => $policyNumber,
                        'error'         => $e->getMessage(),
                    ]);
                }
            }

            Log::info('SyncAgentPoliciesJob: batch persisted', [
                'agent_id' => $this->agent->id,
                'batch'    => "{$batchNum}/{$totalBatches}",
                'synced_so_far' => $synced,
            ]);
        }

        // ── Retry pass: one more attempt for anything that failed enrichment ──
        // Timeouts are often transient — worth one cheap follow-up attempt
        // rather than leaving these on placeholder data until the next full sync.
        if (! empty($failedPolicyNumbers)) {
            Log::info('SyncAgentPoliciesJob: retrying failed enrichments', [
                'agent_id' => $this->agent->id,
                'count'    => count($failedPolicyNumbers),
            ]);

            $retryMap = $glims->getRisksForPolicies($failedPolicyNumbers, self::RISK_FETCH_CONCURRENCY);
            $retriedOk = 0;

            $policiesByNumber = collect($policies)->keyBy('POLICY_NUMBER');

            foreach ($retryMap as $policyNumber => $risks) {
                $policy = $policiesByNumber->get($policyNumber);
                if (! $policy || empty($risks)) {
                    continue;
                }

                $policy['risks']    = $risks;
                $policy['is_fleet'] = count($risks) > 1;

                try {
                    $policySync->syncAgentPolicyFromGlims($policy, $this->agent);
                    $retriedOk++;
                } catch (\Exception $e) {
                    Log::warning('SyncAgentPoliciesJob: retry upsert failed', [
                        'agent_id'      => $this->agent->id,
                        'policy_number' => $policyNumber,
                        'error'         => $e->getMessage(),
                    ]);
                }
            }

            Log::info('SyncAgentPoliciesJob: retry pass completed', [
                'agent_id'          => $this->agent->id,
                'attempted'         => count($failedPolicyNumbers),
                'recovered'         => $retriedOk,
                'still_unresolved'  => count($failedPolicyNumbers) - $retriedOk,
            ]);
        }


        $this->agent->update(['glims_last_synced_at' => now()]);

        Log::info('SyncAgentPoliciesJob: completed', [
            'agent_id' => $this->agent->id,
            'synced'   => $synced,
            'errors'   => $errors,
        ]);
    }
}
