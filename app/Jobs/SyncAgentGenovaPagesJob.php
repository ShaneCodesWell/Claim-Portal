<?php

namespace App\Jobs;

use App\Models\Agent;
use App\Models\Policy;
use App\Services\GenovaApiService;
use App\Services\PolicySyncService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Fetches and saves a small group of an agent's Genova policy pages.
 * Dispatched by SyncAgentPoliciesFromGenovaJob.
 *
 * Worst case: 2 pages x 60 second timeout = 120 seconds, inside the 150 second limit.
 */
class SyncAgentGenovaPagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 150;
    public array $backoff = [30, 120];

    public function __construct(public Agent $agent, public array $pages)
    {
        $this->onQueue('low');
    }

    public function handle(GenovaApiService $genova, PolicySyncService $policySync): void
    {
        $agentCode = $this->agent->genova_agent_code;

        if (! $agentCode) {
            return;
        }

        $entries     = [];
        $failedPages = [];

        foreach ($this->pages as $page) {
            try {
                $response = $genova->getPoliciesByAgentCode($agentCode, $page);

                if ($response->successful()) {
                    $entries = array_merge($entries, $response->json('data.policies') ?? []);
                } else {
                    $failedPages[] = $page;
                }
            } catch (\Throwable $e) {
                $failedPages[] = $page;
                Log::warning('SyncAgentGenovaPagesJob: page fetch failed', [
                    'agent_code' => $agentCode,
                    'page'       => $page,
                    'error'      => $e->getMessage(),
                ]);
            }
        }

        // Keep only the most recent term per policy number within these pages
        $latestPerPolicy = collect($entries)
            ->filter(fn($entry) => ! empty($entry['policy']['policy_no'] ?? null))
            ->groupBy(fn($entry) => $entry['policy']['policy_no'])
            ->map(fn($group) => $group->sortByDesc(
                fn($entry) => $this->endDate($entry['policy']['policy_end'] ?? null)?->timestamp ?? 0
            )->first());

        $synced  = 0;
        $skipped = 0;
        $errors  = 0;

        foreach ($latestPerPolicy as $policyNo => $entry) {
            try {
                // Pages are processed by different jobs, so never let an older term
                // overwrite a newer one that was already saved
                $newEnd   = $this->endDate($entry['policy']['policy_end'] ?? null);
                $existing = Policy::where('source', 'genova')
                    ->where('policy_number', $policyNo)
                    ->first(['id', 'end_date']);

                if ($existing && $existing->end_date && $newEnd && $existing->end_date->gt($newEnd)) {
                    $skipped++;
                    continue;
                }

                $policySync->syncAgentPolicyFromGenova($entry, $this->agent);
                $synced++;
            } catch (\Throwable $e) {
                $errors++;
                Log::warning('SyncAgentGenovaPagesJob: upsert failed for one policy', [
                    'policy_no' => $policyNo,
                    'error'     => $e->getMessage(),
                ]);
            }
        }

        Log::info('SyncAgentGenovaPagesJob: pages done', [
            'agent_id'     => $this->agent->id,
            'pages'        => $this->pages,
            'fetched_rows' => count($entries),
            'synced'       => $synced,
            'skipped_old'  => $skipped,
            'errors'       => $errors,
            'failed_pages' => $failedPages,
        ]);

        if (! empty($failedPages)) {
            // Saved policies stay saved. Throwing makes Laravel retry this small job later.
            throw new \RuntimeException('Genova pages failed: ' . implode(',', $failedPages));
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error('SyncAgentGenovaPagesJob: gave up after all attempts', [
            'agent_id' => $this->agent->id,
            'pages'    => $this->pages,
            'error'    => $e->getMessage(),
        ]);
    }

    private function endDate(?string $value): ?Carbon
    {
        if (blank($value)) {
            return null;
        }

        try {
            return Carbon::parse($value)->startOfDay();
        } catch (\Throwable $e) {
            return null;
        }
    }
}
