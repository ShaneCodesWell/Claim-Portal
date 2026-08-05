<?php

namespace App\Console\Commands;

use App\Jobs\SyncAgentPoliciesFromGenovaJob;
use App\Jobs\SyncAgentPoliciesJob;
use App\Models\Agent;
use Illuminate\Console\Command;

class SyncAllAgentPolicies extends Command
{
    /**
     * php artisan agents:sync-policies
     *
     * Dispatches SyncAgentPoliciesJob (GLIMS) and/or SyncAgentPoliciesFromGenovaJob
     * (Genova) for every agent that has the relevant code set. Both jobs are
     * ShouldQueue, so this command itself just dispatches quickly — the actual
     * sync work happens on the queue worker afterward.
     *
     * Intended to run on a nightly schedule (see routes/console.php), same
     * pattern as RefreshGenovaProductCacheJob, so agent-synced policy/customer
     * data doesn't go stale between agent logins.
     */
    protected $signature = 'agents:sync-policies';

    protected $description = 'Dispatch GLIMS and Genova policy sync jobs for every agent with a linked agent code.';

    public function handle(): int
    {
        $agents = Agent::where(function ($q) {
            $q->whereNotNull('glims_agent_code')
                ->orWhereNotNull('genova_agent_code');
        })->get();

        if ($agents->isEmpty()) {
            $this->info('No agents with a linked GLIMS or Genova agent code found.');
            return self::SUCCESS;
        }

        $glimsDispatched  = 0;
        $genovaDispatched = 0;

        foreach ($agents as $agent) {
            if ($agent->glims_agent_code) {
                SyncAgentPoliciesJob::dispatch($agent);
                $glimsDispatched++;
            }

            if ($agent->genova_agent_code) {
                SyncAgentPoliciesFromGenovaJob::dispatch($agent);
                $genovaDispatched++;
            }
        }

        $this->info("Dispatched GLIMS sync for {$glimsDispatched} agent(s).");
        $this->info("Dispatched Genova sync for {$genovaDispatched} agent(s).");

        return self::SUCCESS;
    }
}
