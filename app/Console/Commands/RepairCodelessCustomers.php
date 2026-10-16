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
 *
 * A customer is repaired only when:
 *   - GLIMS returns exactly one profile for their phone (or their name picks one),
 *   - the GLIMS name matches the local name (otherwise the row says REVIEW),
 *   - the GLIMS code is not already used by another customer.
 *
 * This command never deletes anything.
 *
 * Usage:
 *   php artisan customers:repair-codeless
 *   php artisan customers:repair-codeless --fix
 *   php artisan customers:repair-codeless --fix --approve=22310,22743
 */
class RepairCodelessCustomers extends Command
{
    protected $signature = 'customers:repair-codeless
        {--fix : Save the repairs (without this, only a report is shown)}
        {--approve= : Comma-separated customer IDs whose name differs from GLIMS but that you have checked by hand}';

    protected $description = 'Find customers with no Genova/GLIMS code and restore the GLIMS code from the GLIMS middleware';

    public function handle(GlimsApiService $glims): int
    {
        $apply    = (bool) $this->option('fix');
        $approved = array_values(array_filter(array_map('intval', explode(',', (string) $this->option('approve')))));

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

        $rows    = [];
        $fixed   = 0;
        $claimed = []; // GLIMS code => customer id, for codes handed out during this run

        foreach ($customers as $customer) {
            $policyCount   = $customer->policies()->count();
            $policySources = $customer->policies()->distinct()->pluck('source')->filter()->implode(',');
            $policyLabel   = $policyCount . ($policySources ? " ({$policySources})" : '');

            [$code, $glimsName, $note] = $this->findGlimsCode($glims, $customer);

            $action = 'SKIPPED';

            if ($code !== null) {
                if (isset($claimed[$code])) {
                    $action = 'DUPLICATE';
                    $note   = "same GLIMS code as customer #{$claimed[$code]} in this run";
                } elseif (! $this->namesMatch($customer->name, $glimsName) && ! in_array($customer->id, $approved, true)) {
                    $action = 'REVIEW';
                    $note   = 'name differs from GLIMS; if it is the same person use --approve=' . $customer->id;
                } else {
                    $claimed[$code] = $customer->id;

                    if ($apply) {
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
                }
            }

            $rows[] = [
                $customer->id,
                $customer->name,
                $glimsName ?? '-',
                $policyLabel,
                $code ?? '-',
                $action,
                $note,
            ];
        }

        $this->table(
            ['ID', 'Local name', 'GLIMS name', 'Policies', 'GLIMS code', 'Action', 'Note'],
            $rows
        );

        if ($apply) {
            $this->info("Done. {$fixed} customer(s) repaired, " . ($customers->count() - $fixed) . ' not changed.');
        } else {
            $this->info('Report finished. Review the table, then run again with --fix.');
        }

        return self::SUCCESS;
    }

    /**
     * Returns [glimsCode|null, glimsName|null, note].
     */
    private function findGlimsCode(GlimsApiService $glims, Customer $customer): array
    {
        if (blank($customer->phone)) {
            return [null, null, 'no phone number on record'];
        }

        try {
            $profiles = $glims->resolveProfilesByPhone($customer->phone);
        } catch (\Throwable $e) {
            return [null, null, 'GLIMS lookup failed: ' . $e->getMessage()];
        }

        if (empty($profiles)) {
            return [null, null, 'no GLIMS profile for this phone'];
        }

        // Several GLIMS profiles on one phone: only continue if the name picks exactly one
        if (count($profiles) > 1) {
            $matches = array_values(array_filter(
                $profiles,
                fn($p) => $this->namesMatch($customer->name, $p['name'] ?? '')
            ));

            if (count($matches) !== 1) {
                return [null, null, count($profiles) . ' GLIMS profiles on this phone, name did not pick one'];
            }

            $profiles = $matches;
        }

        $code      = (string) ($profiles[0]['code'] ?? '');
        $glimsName = (string) ($profiles[0]['name'] ?? '');

        if ($code === '') {
            return [null, $glimsName, 'GLIMS profile has no customer code'];
        }

        // Never give two customers the same GLIMS code
        $taken = Customer::where('glims_customer_code', $code)
            ->where('id', '!=', $customer->id)
            ->first();

        if ($taken) {
            return [null, $glimsName, "code {$code} already belongs to customer #{$taken->id}"];
        }

        return [$code, $glimsName, 'matched on phone'];
    }

    /**
     * Lower-cased name words with titles and punctuation removed.
     */
    private function nameTokens(?string $name): array
    {
        $name = mb_strtolower((string) $name);
        $name = preg_replace('/[^a-z0-9\s]/u', ' ', $name);

        $titles = ['mr', 'mrs', 'ms', 'miss', 'dr', 'prof', 'rev', 'hon', 'madam', 'mdm', 'chief', 'sir'];

        $tokens = array_filter(
            preg_split('/\s+/', trim($name)),
            fn($t) => $t !== '' && ! in_array($t, $titles, true)
        );

        return array_values(array_unique($tokens));
    }

    /**
     * Two names match when the shorter one is fully contained in the longer
     * and they share at least two words. Word order and titles do not matter.
     */
    private function namesMatch(?string $a, ?string $b): bool
    {
        $ta = $this->nameTokens($a);
        $tb = $this->nameTokens($b);

        if (empty($ta) || empty($tb)) {
            return false;
        }

        $common = count(array_intersect($ta, $tb));

        return $common >= 2 && $common === min(count($ta), count($tb));
    }
}
