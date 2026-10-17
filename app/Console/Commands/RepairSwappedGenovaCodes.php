<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Services\GenovaApiService;
use App\Services\GlimsApiService;
use Illuminate\Console\Command;

/**
 * Repairs customers where the old login bug saved the GLIMS code in the
 * genova_customer_code column (and overwrote the real Genova code).
 *
 * How they are found: genova_customer_code is all digits (real Genova codes
 * contain letters, e.g. CI2026025759) and glims_customer_code is empty.
 *
 * For each one the command:
 *   1. checks the number really is a GLIMS code, and that the GLIMS name matches the customer,
 *   2. looks the customer up in Genova by phone to recover the real Genova code,
 *   3. moves the number into glims_customer_code and restores the Genova code.
 *
 * Default run is a REPORT: nothing is changed. It never deletes anything.
 *
 * Usage:
 *   php artisan customers:repair-swapped-codes
 *   php artisan customers:repair-swapped-codes --fix
 *   php artisan customers:repair-swapped-codes --fix --approve=22501,22504
 */
class RepairSwappedGenovaCodes extends Command
{
    protected $signature = 'customers:repair-swapped-codes
        {--fix : Save the repairs (without this, only a report is shown)}
        {--approve= : Comma-separated customer IDs whose name differs from GLIMS but that you have checked by hand}';

    protected $description = 'Move GLIMS codes that were saved in the Genova column back to the GLIMS column and restore the real Genova code';

    public function handle(GlimsApiService $glims, GenovaApiService $genova): int
    {
        $apply    = (bool) $this->option('fix');
        $approved = array_values(array_filter(array_map('intval', explode(',', (string) $this->option('approve')))));

        $customers = Customer::whereRaw("genova_customer_code ~ '^[0-9]+\$'")
            ->whereNull('glims_customer_code')
            ->orderBy('id')
            ->get();

        if ($customers->isEmpty()) {
            $this->info('No customers with a swapped code were found. Nothing to do.');
            return self::SUCCESS;
        }

        $this->info($customers->count() . ' customer(s) have an all-digit code in the Genova column.');
        $this->line($apply
            ? 'MODE: FIX (changes will be saved)'
            : 'MODE: REPORT ONLY (nothing will be changed). Add --fix to apply.');

        $rows    = [];
        $fixed   = 0;
        $claimed = []; // GLIMS code => customer id, handed out during this run

        foreach ($customers as $customer) {
            $glimsCode   = (string) $customer->genova_customer_code;
            $policyCount = $customer->policies()->count();
            $glimsName   = $this->lookupGlimsName($glims, $glimsCode);
            $genovaCode  = null;
            $action      = 'SKIPPED';
            $note        = '';

            if ($glimsName === null) {
                $note = "code {$glimsCode} not found in GLIMS (or the lookup failed)";
            } elseif (isset($claimed[$glimsCode])) {
                $action = 'DUPLICATE';
                $note   = "same GLIMS code as customer #{$claimed[$glimsCode]} in this run";
            } elseif (! $this->namesMatch($customer->name, $glimsName) && ! in_array($customer->id, $approved, true)) {
                $action = 'REVIEW';
                $note   = 'name differs from GLIMS; if it is the same person use --approve=' . $customer->id;
            } elseif ($other = Customer::where('glims_customer_code', $glimsCode)->where('id', '!=', $customer->id)->first()) {
                $note = "GLIMS code already belongs to customer #{$other->id}";
            } else {
                [$genovaCode, $status, $genovaNote] = $this->lookupGenovaCode($genova, $customer);

                if (in_array($status, ['failed', 'ambiguous'], true)) {
                    $genovaCode = null;
                    $note       = $genovaNote;
                } elseif (
                    $genovaCode !== null
                    && ($other = Customer::where('genova_customer_code', $genovaCode)->where('id', '!=', $customer->id)->first())
                ) {
                    $note       = "Genova code {$genovaCode} already belongs to customer #{$other->id}";
                    $genovaCode = null;
                } else {
                    $claimed[$glimsCode] = $customer->id;
                    $note                = $genovaNote;

                    if ($apply) {
                        $customer->update([
                            'genova_customer_code' => $genovaCode,
                            'glims_customer_code'  => $glimsCode,
                            'sources'              => $genovaCode ? ['genova', 'glims'] : ['glims'],
                            'last_synced_at'       => null, // forces a fresh sync at next login
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
                $glimsCode,
                $genovaCode ?? '-',
                $policyCount,
                $action,
                $note,
            ];
        }

        $this->table(
            ['ID', 'Local name', 'GLIMS name', 'GLIMS code', 'Genova code', 'Policies', 'Action', 'Note'],
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
     * Name of the GLIMS customer with this code, or null if not found.
     */
    private function lookupGlimsName(GlimsApiService $glims, string $code): ?string
    {
        try {
            $results = $glims->searchCustomerByCode($code);
        } catch (\Throwable $e) {
            return null;
        }

        if (empty($results[0])) {
            return null;
        }

        $row  = $results[0];
        $name = trim(implode(' ', array_filter([
            $row['first_name'] ?? null,
            $row['other_names'] ?? null,
            $row['family_name'] ?? null,
        ])));

        return $name !== '' ? $name : null;
    }

    /**
     * Looks the customer up in Genova by phone and returns [code|null, status, note].
     * status: matched | none | ambiguous | failed
     */
    private function lookupGenovaCode(GenovaApiService $genova, Customer $customer): array
    {
        if (blank($customer->phone)) {
            return [null, 'none', 'no phone on record, Genova not checked'];
        }

        try {
            $response = $genova->getPolicies($customer->phone, 'phone_number');
        } catch (\Throwable $e) {
            return [null, 'failed', 'Genova lookup failed: ' . $e->getMessage()];
        }

        // Genova answers 400/404 when it does not know the phone number
        if (in_array($response->status(), [400, 404], true)) {
            return [null, 'none', 'phone not known to Genova'];
        }

        if (! $response->successful()) {
            return [null, 'failed', 'Genova returned HTTP ' . $response->status()];
        }

        $entries = $response->json('data.content') ?? [];

        $matches = array_values(array_filter(
            $entries,
            fn($e) => ! empty($e['code']) && $this->namesMatch($customer->name, $e['name'] ?? '')
        ));

        if (count($matches) === 1) {
            return [(string) $matches[0]['code'], 'matched', 'Genova profile matched by name'];
        }

        if (count($matches) > 1) {
            return [null, 'ambiguous', count($matches) . ' Genova profiles match the name'];
        }

        return [null, 'none', 'no Genova profile matched the name'];
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
