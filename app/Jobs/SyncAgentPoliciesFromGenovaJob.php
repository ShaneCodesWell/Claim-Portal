<?php

namespace App\Jobs;

use App\Models\Agent;
use App\Services\GenovaApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Coordinator for an agent's Genova policy sync.
 *
 * It only fetches page 1 (to learn how many pages exist) and then hands the pages
 * out to small SyncAgentGenovaPagesJob jobs. A slow or failed page now only
 * affects its own small job, and everything already saved is kept.
 */
class SyncAgentPoliciesFromGenovaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 90;
    public array $backoff = [30, 120];

    /** How many pages each small job handles. */
    private const PAGES_PER_JOB = 2;

    public function __construct(public Agent $agent)
    {
        $this->onQueue('low'); // never compete with customer logins on the 'high' queue
    }

    public function handle(GenovaApiService $genova): void
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

        $first = $genova->getPoliciesByAgentCode($agentCode, 1);

        if ($first->failed()) {
            // Throwing makes Laravel retry this job after the backoff
            throw new \RuntimeException("Genova page 1 failed for agent {$agentCode} (HTTP {$first->status()})");
        }

        $maxPage = max(1, (int) ($first->json('data.max_page') ?? 1));
        $jobs    = 0;

        foreach (array_chunk(range(1, $maxPage), self::PAGES_PER_JOB) as $pages) {
            SyncAgentGenovaPagesJob::dispatch($this->agent, $pages);
            $jobs++;
        }

        // From here on, progress is tracked by the page jobs (failed ones appear in queue:failed)
        $this->agent->update(['genova_last_synced_at' => now()]);

        Log::info('SyncAgentPoliciesFromGenovaJob: page jobs dispatched', [
            'agent_id'   => $this->agent->id,
            'agent_code' => $agentCode,
            'max_page'   => $maxPage,
            'page_jobs'  => $jobs,
        ]);
    }
}
