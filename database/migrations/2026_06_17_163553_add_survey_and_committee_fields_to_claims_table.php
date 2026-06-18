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
            // Survey columns
            $table->foreignId('surveyed_by')->nullable()->after('assigned_at')->constrained('users')->nullOnDelete();
            $table->timestamp('surveyed_at')->nullable()->after('surveyed_by');
            $table->timestamp('survey_completed_at')->nullable()->after('surveyed_at');
            $table->text('survey_notes')->nullable()->after('survey_completed_at');

            // Committee columns
            $table->timestamp('committee_review_at')->nullable()->after('survey_notes');
            $table->text('committee_notes')->nullable()->after('committee_review_at');
            $table->foreignId('committee_decided_by')->nullable()->after('committee_notes')->constrained('users')->nullOnDelete();
            $table->timestamp('committee_decided_at')->nullable()->after('committee_decided_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropConstrainedForeignId('surveyed_by');
            $table->dropConstrainedForeignId('committee_decided_by');
            $table->dropColumn([
                'surveyed_at',
                'survey_completed_at',
                'survey_notes',
                'committee_review_at',
                'committee_notes',
                'committee_decided_at',
            ]);
        });
    }
};
