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
        Schema::create('claim_drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('policy_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('risk_id')->nullable();
            $table->string('claim_type');
            $table->json('form_data')->nullable();
            $table->timestamp('last_saved_at')->nullable();
            $table->timestamps();
            $table->unique(['customer_id', 'policy_id', 'claim_type'], 'claim_drafts_unique_per_policy_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claim_drafts');
    }
};
