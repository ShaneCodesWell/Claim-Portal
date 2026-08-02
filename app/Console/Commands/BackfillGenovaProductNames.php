<?php

namespace App\Console\Commands;

use App\Models\GenovaBusinessClass;
use App\Models\GenovaProduct;
use App\Models\Policy;
use Illuminate\Console\Command;

class BackfillGenovaProductNames extends Command
{
    /**
     * php artisan genova:backfill-product-names
     * php artisan genova:backfill-product-names --dry-run
     */
    protected $signature = 'genova:backfill-product-names {--dry-run : Show what would change without saving}';

    protected $description = 'Re-resolve business_class_name/product_name for genova policies stuck on Unknown Class/Product, using the local product cache and already-stored raw_payload.';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $query = Policy::where('source', 'genova')
            ->where(function ($q) {
                $q->where('business_class_name', 'Unknown Class')
                    ->orWhere('product_name', 'Unknown Product');
            });

        $total = $query->count();

        if ($total === 0) {
            $this->info('No genova policies with Unknown Class/Product found.');
            return self::SUCCESS;
        }

        $this->info("Found {$total} policies to check." . ($dryRun ? ' (dry run — no writes)' : ''));

        $updated = 0;
        $stillUnresolved = 0;
        $skippedNoIds = 0;

        $query->chunkById(200, function ($policies) use (&$updated, &$stillUnresolved, &$skippedNoIds, $dryRun) {
            foreach ($policies as $policy) {
                $raw = $policy->raw_payload;

                // Two known raw_payload shapes on genova policies:
                //  1) syncAgentPolicyFromGenova(): raw_payload['policy'][...]
                //  2) syncFromGenova() / syncFromGenovaRich(): flatter structure
                $policyData = $raw['policy'] ?? $raw ?? [];

                $businessClassId = $policyData['esu_main_product_id'] ?? null;
                $productId       = $policyData['esu_product_id'] ?? null;

                if (! $businessClassId && ! $productId) {
                    $skippedNoIds++;
                    continue;
                }

                $businessClass = $businessClassId ? GenovaBusinessClass::find($businessClassId) : null;
                $product       = $productId ? GenovaProduct::find($productId) : null;

                $newBusinessClassName = $businessClass->name ?? null;
                $newProductName       = $product->name ?? null;

                // Only touch fields we can actually resolve — don't overwrite
                // a currently-unknown value with another unknown value.
                $changes = [];

                if ($newBusinessClassName && $policy->business_class_name === 'Unknown Class') {
                    $changes['business_class_name'] = $newBusinessClassName;
                }

                if ($businessClassId && $policy->business_class_id !== $businessClassId) {
                    $changes['business_class_id'] = $businessClassId;
                }

                if ($newProductName && $policy->product_name === 'Unknown Product') {
                    $changes['product_name'] = $newProductName;
                }

                if (empty($changes)) {
                    $stillUnresolved++;
                    $this->line("  [unresolved] policy {$policy->policy_number} — class_id={$businessClassId}, product_id={$productId} not yet in cache");
                    continue;
                }

                $this->line("  [update] policy {$policy->policy_number}: " . json_encode($changes));

                if (! $dryRun) {
                    $policy->update($changes);
                }

                $updated++;
            }
        });

        $this->newLine();
        $this->info("Updated: {$updated}");
        $this->info("Still unresolved (not in product cache yet): {$stillUnresolved}");
        $this->info("Skipped (no product/class IDs in raw_payload): {$skippedNoIds}");

        return self::SUCCESS;
    }
}
