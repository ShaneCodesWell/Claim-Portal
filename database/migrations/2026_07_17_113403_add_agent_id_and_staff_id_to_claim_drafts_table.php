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
        Schema::table('claim_drafts', function (Blueprint $table) {
            $table->foreignId('agent_id')->nullable()->after('policy_id')
                ->constrained('agents')->nullOnDelete();
            $table->foreignId('staff_id')->nullable()->after('agent_id')
                ->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claim_drafts', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
            $table->dropForeign(['staff_id']);
            $table->dropColumn(['agent_id', 'staff_id']);
        });
    }
};
