<?php

namespace App\Console\Commands;

use App\Models\Policy;
use App\Support\GlimsRiskResolver;
use Illuminate\Console\Command;

class BackfillGlimsRiskDuplicates extends Command
{
    /**
     * php artisan glims:backfill-risk-duplicates
     * php artisan glims:backfill-risk-duplicates --dry-run
     *
     * Fixes existing GLIMS policies whose raw_payload['risks'] contains
     * multiple rows for the same physical vehicle (endorsement re-rating
     * appended a new row instead of updating the old one). This matters
     * specifically because MotorFormController reads raw_payload['risks']
     * directly — PolicyResource already resolves this live, but stored
     * data feeding the claim form does not correct itself.
     */
    protected $signature = 'glims:backfill-risk-duplicates {--dry-run : Show what would change without saving}';

    protected $description = 'Collapse duplicate vehicle-endorsement risk rows in stored raw_payload for existing glims policies.';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $query = Policy::where('source', 'glims')
            ->whereNotNull('raw_payload');

        $total = $query->count();

        if ($total === 0) {
            $this->info('No glims policies found.');
            return self::SUCCESS;
        }

        $this->info("Checking {$total} glims policies.".($dryRun ? ' (dry run — no writes)' : ''));

        $fixed   = 0;
        $skipped = 0;

        $query->chunkById(200, function ($policies) use (&$fixed, &$skipped, $dryRun) {
            foreach ($policies as $policy) {
                $raw = $policy->raw_payload;

                $risks = $raw['risks'] ?? [];

                if (count($risks) <= 1) {
                    $skipped++;
                    continue;
                }

                $resolved = GlimsRiskResolver::resolve($risks);

                // No change if resolving didn't actually collapse anything
                // (i.e. it really was a genuine multi-vehicle fleet)
                if (count($resolved) === count($risks)) {
                    $skipped++;
                    continue;
                }

                $newRaw               = $raw;
                $newRaw['risks']      = $resolved;
                $newRaw['is_fleet']   = count($resolved) > 1;

                $this->line("  [fix] policy {$policy->policy_number} (id {$policy->id}) — ".count($risks)." risks -> ".count($resolved)." risk(s)");

                if (! $dryRun) {
                    $policy->update(['raw_payload' => $newRaw]);
                }

                $fixed++;
            }
        });

        $this->newLine();
        $this->info("Fixed: {$fixed}");
        $this->info("Skipped (already correct or genuine fleet): {$skipped}");

        return self::SUCCESS;
    }
}