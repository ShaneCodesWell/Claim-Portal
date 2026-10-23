<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('claims', 'repair_bill') && ! Schema::hasColumn('claims', 'estimated_repair_cost')) {
            Schema::table('claims', fn(Blueprint $t) => $t->renameColumn('repair_bill', 'estimated_repair_cost'));
        }

        // Catch claims created or edited since the first backfill (their column is still null).
        DB::table('claims')->select('id', 'form_data')
            ->whereNull('estimated_repair_cost')
            ->orderBy('id')
            ->chunkById(500, function ($claims) {
                foreach ($claims as $claim) {
                    $data = json_decode((string) $claim->form_data, true);
                    if (! is_array($data)) continue;

                    foreach (['estimated_repair_cost', 'repair_estimate_amount'] as $field) {
                        $v = $data[$field] ?? null;
                        if (is_string($v)) $v = trim($v);

                        if ($v !== null && $v !== '' && is_numeric($v)) {
                            DB::table('claims')->where('id', $claim->id)->update(['estimated_repair_cost' => $v]);
                            break;
                        }
                    }
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('claims', 'estimated_repair_cost')) {
            Schema::table('claims', fn(Blueprint $t) => $t->renameColumn('estimated_repair_cost', 'repair_bill'));
        }
    }
};
