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
        Schema::create('merchants', function (Blueprint $table) {
            $table->uuid('merchant_id')->primary();
            $table->string('name');
            $table->foreignId("product_category_id");
            $table->string('address');
            $table->string('operational_time_oneday');
            $table->string('logo');
            $table->longText('description');
            $table->boolean('is_open');
            $table->timestamps();

            // $table->foreign('product_category_id')->references('id')->on('product_categories');
            // $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchants');
    }
};
