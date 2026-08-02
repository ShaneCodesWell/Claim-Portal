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
    public int $timeout = 180; // bumped: now also fetches per-policy risk detail

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
        // We can't group fleet policies (multiple plate_number rows sharing
        // a policy_number) until we've seen every page.
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
        // Same fleet logic as getPoliciesByClientCode()/getPoliciesByAgentCode():
        // multiple rows sharing a policy_number become one policy with a
        // 'risks' array of placeholder (plate-number-only) entries.
        $policies = $glims->groupPolicyRows($allRows);

        Log::info('SyncAgentPoliciesJob: grouped rows into policies', [
            'agent_id'     => $this->agent->id,
            'raw_rows'     => count($allRows),
            'policy_count' => count($policies),
        ]);

        // ── Step 3: enrich each policy with rich vehicle/risk detail ────────
        // Mirrors SyncCustomerPoliciesJob::syncGlims() — placeholder risks
        // (plate number only) get replaced with full detail (make, model,
        // chassis, year, etc.) fetched per policy_number.
        $synced = 0;
        $errors = 0;

        foreach ($policies as &$policy) {
            $policyNumber = $policy['POLICY_NUMBER'] ?? null;
            if (! $policyNumber) {
                continue;
            }

            try {
                $richRisks = $glims->getRisksForPolicy($policyNumber);

                if (! empty($richRisks)) {
                    $policy['risks']    = $richRisks;
                    $policy['is_fleet'] = count($richRisks) > 1;
                }
            } catch (\Exception $e) {
                // Don't let one failed detail call kill the rest — the
                // placeholder risk (plate number only) is still better than nothing.
                Log::warning('SyncAgentPoliciesJob: policy details fetch failed', [
                    'agent_id'      => $this->agent->id,
                    'policy_number' => $policyNumber,
                    'error'         => $e->getMessage(),
                ]);
            }
        }
        unset($policy); // clean up reference

        // ── Step 4: persist ──────────────────────────────────────────────
        foreach ($policies as $policy) {
            try {
                $policySync->syncAgentPolicyFromGlims($policy, $this->agent);
                $synced++;
            } catch (\Exception $e) {
                $errors++;
                Log::warning('SyncAgentPoliciesJob: upsert failed for one policy', [
                    'agent_id'      => $this->agent->id,
                    'policy_number' => $policy['POLICY_NUMBER'] ?? 'unknown',
                    'error'         => $e->getMessage(),
                ]);
            }
        }

        $this->agent->update(['glims_last_synced_at' => now()]);

        Log::info('SyncAgentPoliciesJob: completed', [
            'agent_id' => $this->agent->id,
            'synced'   => $synced,
            'errors'   => $errors,
        ]);
    }
}
