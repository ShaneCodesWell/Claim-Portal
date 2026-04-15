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
            $table->dropUnique('policies_external_policy_id_unique');
            // policy_number is already unique from previous migration
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('policies', function (Blueprint $table) {
            $table->unique('external_policy_id', 'policies_external_policy_id_unique');
        });
    }
};
