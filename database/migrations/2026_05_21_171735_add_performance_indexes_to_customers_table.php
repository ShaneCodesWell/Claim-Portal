<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->index('created_at'); // speeds up ->latest()
        });

        Schema::table('policies', function (Blueprint $table) {
            $table->index('status');      // speeds up active_policies count
            $table->index('customer_id'); // speeds up withCount('policies')
        });

        Schema::table('claims', function (Blueprint $table) {
            $table->index('status'); // speeds up claim counts
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('customers_created_at_index');
        });

        Schema::table('policies', function (Blueprint $table) {
            $table->dropIndex('policies_status_index');
            $table->dropIndex('policies_customer_id_index');
        });

        Schema::table('claims', function (Blueprint $table) {
            $table->dropIndex('claims_status_index');
        });
    }
};
