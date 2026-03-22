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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('local_password')->nullable()->after('password');
            $table->timestamp('local_password_set_at')->nullable()->after('local_password');
        });
    }
 
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['local_password', 'local_password_set_at']);
        });
    }
};
