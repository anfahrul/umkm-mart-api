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
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('product_id')->primary();
            $table->string("name");
            $table->foreignUuid("merchant_id");
            $table->foreignId("product_category_id");
            $table->integer("minimal_order");
            $table->longText("short_desc");
            $table->integer("price_value");
            $table->integer("stock_value");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
