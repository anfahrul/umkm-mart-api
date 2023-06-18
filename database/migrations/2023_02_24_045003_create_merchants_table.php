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
            $table->foreignUuid("user_id")->unique();
            $table->string('merchant_name');
            $table->foreignId("product_category_id");
            $table->string('domain');
            $table->string('address');
            $table->boolean('is_open')->default(1);
            $table->string('wa_number');
            $table->string('merchant_website_url')->nullable();
            $table->string('is_verified')->default(0);
            $table->string('original_logo_url');
            $table->string('operational_time_oneday');
            $table->longText('description');
            $table->timestamps();
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
