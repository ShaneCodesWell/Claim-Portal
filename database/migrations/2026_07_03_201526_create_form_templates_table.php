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
        Schema::create('form_templates', function (Blueprint $table) {
            $table->id();
            $table->string('product_type');       // motor | fire | general_accident
            $table->unsignedInteger('version')->default(1);
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('schema');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
 
            $table->unique(['product_type', 'version']);
        });
 
        Schema::table('claims', function (Blueprint $table) {
            $table->foreignId('form_template_id')->nullable()->after('claim_type')
                ->constrained('form_templates')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropConstrainedForeignId('form_template_id');
        });
 
        Schema::dropIfExists('form_templates');
    }
};
