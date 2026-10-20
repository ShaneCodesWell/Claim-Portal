<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add the column only if it does not already exist.
        if (!Schema::hasColumn('claims', 'repair_bill')) {
            Schema::table('claims', function (Blueprint $table) {
                $table->decimal('repair_bill', 14, 2)->nullable();
            });
        }

        // Backfill existing claims from their form data.
        DB::table('claims')
            ->select('id', 'form_data')
            ->whereNull('repair_bill')
            ->orderBy('id')
            ->chunkById(500, function ($claims) {
                foreach ($claims as $claim) {
                    $data = json_decode(
                        (string) $claim->form_data,
                        true
                    );

                    if (!is_array($data)) {
                        continue;
                    }

                    $repairBill = null;

                    // Prefer repair_estimate_amount.
                    // Fall back to estimated_repair_cost.
                    foreach (
                        [
                            'repair_estimate_amount',
                            'estimated_repair_cost',
                        ] as $field
                    ) {
                        $value = $data[$field] ?? null;

                        if (is_string($value)) {
                            $value = trim($value);
                        }

                        if (
                            $value !== null
                            && $value !== ''
                            && is_numeric($value)
                        ) {
                            $repairBill = $value;
                            break;
                        }
                    }

                    if ($repairBill !== null) {
                        DB::table('claims')
                            ->where('id', $claim->id)
                            ->update([
                                'repair_bill' => $repairBill,
                            ]);
                    }
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            //
        });
    }
};
