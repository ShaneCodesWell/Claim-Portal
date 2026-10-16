<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Services\GlimsApiService;
use Illuminate\Console\Command;

/**
 * Repairs customers that were created by the old login bug with no
 * genova_customer_code and no glims_customer_code.
 *
 * Default run is a REPORT: nothing is changed.
 * Add --fix to save the repairs.
 *
 * A customer is only repaired when GLIMS returns exactly one matching profile
 * for their phone number (or the customer's name picks one out of several).
 * Anything unclear is skipped and listed so you can handle it by hand.
 * This command never deletes anything.
 */
class RepairCodelessCustomers extends Command
{
    protected $signature = 'customers:repair-codeless {--fix : Save the repairs (without this, only a report is shown)}';

    protected $description = 'Find customers with no Genova/GLIMS code and restore the GLIMS code from the GLIMS middleware';

    public function handle(GlimsApiService $glims): int
    {
        $apply = (bool) $this->option('fix');

        $customers = Customer::whereNull('genova_customer_code')
            ->whereNull('glims_customer_code')
            ->orderBy('id')
            ->get();

        if ($customers->isEmpty()) {
            $this->info('No customers without a code were found. Nothing to do.');
            return self::SUCCESS;
        }

        $this->info($customers->count() . ' customer(s) have no Genova or GLIMS code.');
        $this->line($apply
            ? 'MODE: FIX (changes will be saved)'
            : 'MODE: REPORT ONLY (nothing will be changed). Add --fix to apply.');

        $rows  = [];
        $fixed = 0;

        foreach ($customers as $customer) {
            $policyCount = $customer->policies()->count();

            [$code, $note] = $this->findGlimsCode($glims, $customer);

            if ($code === null) {
                $action = 'SKIPPED';
            } elseif ($apply) {
                $customer->update([
                    'glims_customer_code' => $code,
                    'sources'             => ['glims'],
                    'last_synced_at'      => null, // forces a fresh sync at next login
                ]);
                $action = 'FIXED';
                $fixed++;
            } else {
                $action = 'WOULD FIX';
            }

            $rows[] = [
                $customer->id,
                $customer->name,
                $customer->phone,
                $policyCount,
                $code ?? '-',
                $action,
                $note,
            ];
        }

        $this->table(
            ['ID', 'Name', 'Phone', 'Policies', 'GLIMS code', 'Action', 'Note'],
            $rows
        );

        if ($apply) {
            $this->info("Done. {$fixed} customer(s) repaired, " . ($customers->count() - $fixed) . ' skipped.');
        } else {
            $this->info('Report finished. Review the table, then run again with --fix.');
        }

        return self::SUCCESS;
    }

    /**
     * Returns [glimsCode|null, note].
     */
    private function findGlimsCode(GlimsApiService $glims, Customer $customer): array
    {
        if (blank($customer->phone)) {
            return [null, 'no phone number on record'];
        }

        try {
            $profiles = $glims->resolveProfilesByPhone($customer->phone);
        } catch (\Throwable $e) {
            return [null, 'GLIMS lookup failed: ' . $e->getMessage()];
        }

        if (empty($profiles)) {
            return [null, 'no GLIMS profile for this phone'];
        }

        // Several GLIMS profiles on one phone: only continue if the name picks exactly one
        if (count($profiles) > 1) {
            $name    = mb_strtolower(trim((string) $customer->name));
            $matches = array_values(array_filter(
                $profiles,
                fn($p) => mb_strtolower(trim((string) ($p['name'] ?? ''))) === $name
            ));

            if (count($matches) !== 1) {
                return [null, count($profiles) . ' GLIMS profiles on this phone, name did not pick one'];
            }

            $profiles = $matches;
        }

        $code = (string) ($profiles[0]['code'] ?? '');

        if ($code === '') {
            return [null, 'GLIMS profile has no customer code'];
        }

        // Never give two customers the same GLIMS code
        $taken = Customer::where('glims_customer_code', $code)
            ->where('id', '!=', $customer->id)
            ->first();

        if ($taken) {
            return [null, "code {$code} already belongs to customer #{$taken->id}"];
        }

        return [$code, 'matched on phone'];
    }
}
