<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('policies', function (Blueprint $table) {
            // Add insured_name column
            $table->string('insured_name')->nullable()->after('policy_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('policies', function (Blueprint $table) {
            $table->dropColumn('insured_name');
        });
    }
};
