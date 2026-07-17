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
        Schema::table('claim_documents', function (Blueprint $table) {
            $table->foreignId('uploaded_by_agent_id')->nullable()
                ->after('uploaded_by_customer_id')
                ->constrained('agents')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claim_documents', function (Blueprint $table) {
            $table->dropForeign(['uploaded_by_agent_id']);
            $table->dropColumn('uploaded_by_agent_id');
        });
    }
};
