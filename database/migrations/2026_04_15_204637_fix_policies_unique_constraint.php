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

            // Drop external_policy_id unique (we added this earlier)
            $table->dropUnique('policies_external_policy_id_unique');

            // Make policy_number the unique key
            $table->unique('policy_number', 'policies_policy_number_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('policies', function (Blueprint $table) {
            $table->dropColumn('insured_name');
            $table->dropUnique('policies_policy_number_unique');
            $table->unique('external_policy_id', 'policies_external_policy_id_unique');
        });
    }
};
