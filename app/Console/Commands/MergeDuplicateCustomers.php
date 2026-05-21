<?php
namespace App\Console\Commands;

use App\Models\Claim;
use App\Models\Customer;
use App\Models\Policy;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MergeDuplicateCustomers extends Command
{
    protected $signature = 'app:merge-duplicate-customers {--dry-run : Preview changes without modifying the database}';

    protected $description = 'Merge duplicate customers using phone number as unique identifier';

    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('--- DRY RUN — no changes will be made ---');
        }

        $duplicates = Customer::select(
            'phone',
            DB::raw('MIN(id) as keep_id'),
            DB::raw('STRING_AGG(id::text, \',\' ORDER BY created_at ASC) as all_ids'),
            DB::raw('COUNT(*) as count')
        )
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->groupBy('phone')
            ->having(DB::raw('COUNT(*)'), '>', 1)
            ->get();

        $this->info("Found {$duplicates->count()} duplicate groups.");
        $merged = 0;

        foreach ($duplicates as $group) {
            $allIds    = explode(',', $group->all_ids);
            $keepId    = $group->keep_id;
            $deleteIds = array_values(array_filter($allIds, fn($id) => $id != $keepId));

            $this->line("  Phone {$group->phone}: keeping ID {$keepId}, removing IDs " . implode(', ', $deleteIds));

            if (! $dryRun) {
                DB::transaction(function () use ($keepId, $deleteIds) {
                    Policy::whereIn('customer_id', $deleteIds)->update(['customer_id' => $keepId]);
                    Claim::whereIn('customer_id', $deleteIds)->update(['customer_id' => $keepId]);
                    Customer::whereIn('id', $deleteIds)->delete();
                });
                $merged += count($deleteIds);
            }
        }

        if ($dryRun) {
            $this->warn('Dry run complete — no changes made.');
        } else {
            $this->info("Done. Removed {$merged} duplicate customer records.");
        }
    }
}
