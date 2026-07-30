<?php

namespace App\Jobs;

use App\Models\Agent;
use App\Services\GenovaApiService;
use App\Services\PolicySyncService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncAgentPoliciesFromGenovaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 180; // Genova responses run heavier/slower than GLIMS

    public function __construct(public Agent $agent) {}

    public function handle(GenovaApiService $genova, PolicySyncService $policySync): void
    {
        $agentCode = $this->agent->genova_agent_code;

        if (! $agentCode) {
            Log::info('SyncAgentPoliciesFromGenovaJob: agent has no genova_agent_code, skipping', [
                'agent_id' => $this->agent->id,
            ]);
            return;
        }

        Log::info('SyncAgentPoliciesFromGenovaJob: starting sync', [
            'agent_id'   => $this->agent->id,
            'agent_code' => $agentCode,
        ]);

        $policies = $genova->getAllPoliciesByAgentCode($agentCode);

        $synced = 0;
        $errors = 0;

        foreach ($policies as $entry) {
            try {
                $policySync->syncAgentPolicyFromGenova($entry, $this->agent);
                $synced++;
            } catch (\Exception $e) {
                $errors++;
                Log::warning('SyncAgentPoliciesFromGenovaJob: upsert failed for one policy', [
                    'policy_no' => $entry['policy']['policy_no'] ?? 'unknown',
                    'error'     => $e->getMessage(),
                ]);
            }
        }

        $this->agent->update(['genova_last_synced_at' => now()]);

        Log::info('SyncAgentPoliciesFromGenovaJob: completed', [
            'agent_id' => $this->agent->id,
            'synced'   => $synced,
            'errors'   => $errors,
        ]);
    }
}
