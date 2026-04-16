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
        Schema::create('policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('external_policy_id')->nullable()->index();
            $table->string('policy_number')->index();
            $table->string('insured_name')->nullable();
            $table->string('business_class_id')->nullable();
            $table->string('business_class_name')->nullable();
            $table->string('product_id')->nullable();
            $table->string('product_name')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('effective_date')->nullable();
            $table->timestamp('renewal_date')->nullable();
            $table->string('status')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policies');
    }
};
