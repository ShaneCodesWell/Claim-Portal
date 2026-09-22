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
        private GlimsPolicyFallbackService $glimsFallback,
    ) {}

    /**
     * Find a policy by number for this agent's own portfolio.
     *
     * Checks the local DB first (agent-scoped). If not found there — meaning
     * either it hasn't synced yet, or it was never properly tied to this
     * agent in GLIMS — falls back to a GLIMS-wide policy-details lookup by
     * policy number, independent of any agent tie-up.
     *
     * The GLIMS-wide fallback does NOT persist to the local Policy table:
     * since the policy isn't confirmed to belong to this agent, we don't
     * want to write it into their portfolio. It's shown read-only, flagged
     * as unlinked, and financial fields are stripped (see
     * GlimsPolicyFallbackService::stripFinancials()) — claim filing stays
     * blocked until the Data team ties the policy to the correct agent
     * in GLIMS.
     *
     * NOTE: Genova has no direct policy-number search endpoint (only lookup
     * by internal policy_id, which we don't have until a customer-search has
     * already run), so this fallback only ever queries GLIMS. A Genova policy
     * that hasn't synced to this agent yet will not be found by this method
     * at all — it will return null rather than surface an unlinked result.
     *
     * @return array{policy: array, source: 'local'|'unlinked', details: array, linked_to_agent?: bool}|null
     *         null = not found in this agent's portfolio, and not found in GLIMS either
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

        $fallback = $this->glimsFallback->search($policyNumber);

        if (! $fallback) {
            return null;
        }

        $this->glimsFallback->logUnlinkedView($agent, $fallback['policy_number']);

        return [
            'policy'  => $fallback,
            'source'  => 'unlinked',
            'details' => $fallback['risks'],
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
