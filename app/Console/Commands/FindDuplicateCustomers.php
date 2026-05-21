<?php
namespace App\Console\Commands;

use App\Models\Customer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FindDuplicateCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:find-duplicate-customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $duplicates = Customer::select(
            'phone',
            DB::raw('COUNT(*) as count'),
            DB::raw('STRING_AGG(id::text, \',\' ORDER BY created_at ASC) as ids')
        )
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->groupBy('phone')
            ->having(DB::raw('COUNT(*)'), '>', 1)
            ->get();

        $this->table(['Phone', 'Count', 'IDs'], $duplicates->map(fn($d) => [
            $d->phone, $d->count, $d->ids,
        ]));

        $this->info("Total duplicate groups: {$duplicates->count()}");
    }
}
