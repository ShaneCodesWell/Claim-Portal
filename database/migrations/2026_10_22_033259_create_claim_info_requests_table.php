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
        Schema::create('claim_info_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('claim_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requested_by')->constrained('users');
            $table->text('note');
            $table->string('previous_status');          // restored when the customer responds
            $table->string('status')->default('pending')->index(); // pending | responded | cancelled
            $table->text('response_note')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->string('sms_status')->default('queued'); // queued | sent | failed
            $table->timestamp('sms_sent_at')->nullable();
            $table->text('sms_error')->nullable();
            $table->timestamps();
            $table->index(['claim_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claim_info_requests');
    }
};
