<?php

namespace App\Services;

use App\Http\Resources\PolicyResource;
use App\Models\Agent;
use App\Models\Policy;
use Illuminate\Support\Facades\Log;

class AgentPolicySearchService
{
    public function __construct(
        private GlimsApiService $glims,
        private GenovaApiService $genova,
        private PolicySyncService $policySync,
    ) {}

    /**
     * Find a policy by number for this agent — local DB first, then GLIMS
     * if not synced yet. On a remote hit, persists it locally (agent-scoped)
     * so the next search for the same policy is instant.
     *
     * On every hit, also attempts a live-refresh call to the source system
     * for the fullest/freshest detail (Genova policySearch / GLIMS
     * policy details), falling back to raw_payload if that call fails —
     * this is a straight port of the original controller's behavior, not
     * something new. Skipped when the policy was JUST synced via the API
     * fallback below, since that already pulled the freshest data available.
     *
     * NOTE: Genova has no direct policy-number search endpoint (only lookup
     * by internal policy_id, which we don't have until a customer-search has
     * already run), so the remote fallback only covers GLIMS-sourced policies.
     * A Genova policy that hasn't synced yet will not be found here.
     *
     * @return array{policy: array, source: 'local'|'api', details: array}|null  null = not found / not this agent's
     */
    public function findForAgent(Agent $agent, string $policyNumber): ?array
    {
        $portfolioId = $agent->portfolioAgentId();

        $local = Policy::where('policy_number', $policyNumber)
            ->where('agent_id', $portfolioId)
            ->first();

        if ($local) {
            return [
                'policy'  => (new PolicyResource($local))->toArray(request()),
                'source'  => 'local',
                'details' => $this->getLiveDetails($local),
            ];
        }

        if (! $agent->glims_agent_code) {
            return null;
        }

        $remote = $this->glims->getPolicyByNumber($policyNumber);

        if (! $remote) {
            return null;
        }

        $remoteAgentCode = $remote['POLICY_AGENT_CODE'] ?? null;

        if (! $remoteAgentCode || strcasecmp(trim($remoteAgentCode), trim($agent->glims_agent_code)) !== 0) {
            // Exists in GLIMS but belongs to a different agent — treat as
            // "not found" rather than confirming it exists to this agent.
            return null;
        }

        // Enrich with full vehicle/risk detail before persisting — getPolicyByNumber()
        // only returns placeholder risks (plate numbers), same as the agent sync job does.
        try {
            $remote['risks'] = $this->glims->getRisksForPolicy($policyNumber);
        } catch (\Exception $e) {
            Log::warning('AgentPolicySearchService: risk enrichment failed, syncing with placeholder risks', [
                'policy_number' => $policyNumber,
                'error'         => $e->getMessage(),
            ]);
        }

        $this->policySync->syncAgentPolicyFromGlims($remote, $agent);

        $saved = Policy::where('policy_number', $policyNumber)
            ->where('agent_id', $portfolioId)
            ->first();

        if (! $saved) {
            Log::error('AgentPolicySearchService: sync appeared to succeed but policy not found locally afterward', [
                'policy_number' => $policyNumber,
                'agent_id'      => $agent->id,
                'portfolio_id'  => $portfolioId,
            ]);
            return null;
        }

        return [
            'policy'  => (new PolicyResource($saved))->toArray(request()),
            'source'  => 'api',
            'details' => [], // just synced from GLIMS above — already the freshest data available
        ];
    }

    /**
     * Live-refresh a local policy's detail from its source system.
     * Ported from the original PolicyController::getGenovaDetails(), now
     * covering both sources. Falls back to raw_payload on any failure so a
     * flaky upstream call never breaks an otherwise-successful local lookup.
     */
    private function getLiveDetails(Policy $policy): array
    {
        if ($policy->source === 'genova') {
            if (empty($policy->external_policy_id)) {
                return $policy->raw_payload ?? [];
            }

            try {
                $response = $this->genova->policySearch($policy->external_policy_id);

                if ($response->successful()) {
                    $policies = $response->json('data.policies') ?? [];
                    if (! empty($policies)) {
                        return $policies[0]; // live, richer data
                    }
                }

                Log::warning('AgentPolicySearchService: Genova live details empty/failed, falling back to raw_payload', [
                    'policy_id' => $policy->external_policy_id,
                    'status'    => $response->status(),
                ]);
            } catch (\Exception $e) {
                Log::warning('AgentPolicySearchService: Genova live details threw, falling back to raw_payload', [
                    'policy_id' => $policy->external_policy_id,
                    'error'     => $e->getMessage(),
                ]);
            }

            return $policy->raw_payload ?? [];
        }

        if ($policy->source === 'glims') {
            try {
                $details = $this->glims->getPolicyDetails($policy->policy_number);

                if (! empty($details)) {
                    return $details;
                }
            } catch (\Exception $e) {
                Log::warning('AgentPolicySearchService: GLIMS live details threw, falling back to raw_payload', [
                    'policy_number' => $policy->policy_number,
                    'error'         => $e->getMessage(),
                ]);
            }

            return $policy->raw_payload ?? [];
        }

        return $policy->raw_payload ?? [];
    }
}
