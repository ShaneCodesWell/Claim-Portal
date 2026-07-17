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
            // Was this claim opened by an intermediary/agent on behalf of a customer?
            $table->boolean('initiated_by_agent')->default(false)->after('initiated_by');

            // Which agent opened it
            $table->foreignId('initiated_by_agent_id')
                ->nullable()
                ->after('initiated_by_agent')
                ->constrained('agents')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropForeign(['initiated_by_agent_id']);
            $table->dropColumn(['initiated_by_agent', 'initiated_by_agent_id']);
        });
    }
};
