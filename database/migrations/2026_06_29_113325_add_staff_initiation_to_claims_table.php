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
        Schema::table('claims', function (Blueprint $table) {
            // Was this claim opened by a staff member on behalf of a customer?
            $table->boolean('initiated_by_staff')->default(false)->after('source');

            // Which staff member opened it (nullable — pre-existing claims have no value)
            $table->foreignId('initiated_by')
                ->nullable()
                ->after('initiated_by_staff')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropForeign(['initiated_by']);
            $table->dropColumn(['initiated_by_staff', 'initiated_by']);
        });
    }
};
