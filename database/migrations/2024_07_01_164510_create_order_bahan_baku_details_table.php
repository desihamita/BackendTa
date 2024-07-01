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
        Schema::create('order_bahan_baku_details', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('sku')->nullable();
            $table->string('photo')->nullable();
            $table->integer('quantity')->nullable();

            $table->integer('price')->nullable();
            $table->integer('sale_price')->nullable();

            $table->foreignId('orderBahanBaku_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('brand_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->unsignedBigInteger('sub_category_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_bahan_baku_details');
    }
};