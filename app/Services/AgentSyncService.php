<?php

namespace App\Services;

use App\Jobs\SyncAgentPoliciesFromGenovaJob;
use App\Jobs\SyncAgentPoliciesJob;
use App\Models\Agent;
use Illuminate\Support\Facades\Log;

class AgentSyncService
{
    public function dispatchPolicySync(Agent $agent): void
    {
        try {
            SyncAgentPoliciesJob::dispatch($agent);
        } catch (\Exception $e) {
            Log::error('AgentSyncService: GLIMS sync dispatch failed', [
                'agent_id' => $agent->id,
                'error'    => $e->getMessage(),
            ]);
        }

        try {
            SyncAgentPoliciesFromGenovaJob::dispatch($agent);
        } catch (\Exception $e) {
            Log::error('AgentSyncService: Genova sync dispatch failed', [
                'agent_id' => $agent->id,
                'error'    => $e->getMessage(),
            ]);
        }
    }
}
