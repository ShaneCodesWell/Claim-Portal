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
        Schema::create('genova_business_classes', function (Blueprint $table) {
            $table->unsignedInteger('esu_main_product_id')->primary();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('genova_products', function (Blueprint $table) {
            $table->unsignedInteger('esu_product_id')->primary();
            $table->unsignedInteger('esu_main_product_id');
            $table->string('name');
            $table->timestamps();

            $table->index('esu_main_product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('genova_products');
        Schema::dropIfExists('genova_business_classes');
    }
};
