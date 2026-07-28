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
        Schema::table('agents', function (Blueprint $table) {
            $table->renameColumn('partner_code', 'glims_agent_code');
            $table->string('genova_agent_code')->nullable()->after('glims_agent_code');

            // Replace the single last_synced_at with per-source timestamps —
            // otherwise a GLIMS sync and a future Genova sync will clobber each other's timestamp,
            // and the "syncPending" check we added will lie about which system is actually stale.
            $table->renameColumn('last_synced_at', 'glims_last_synced_at');
            $table->timestamp('genova_last_synced_at')->nullable();

            // Track which systems this agent is known to, same pattern as Customer::sources
            $table->json('sources')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            //
        });
    }
};
