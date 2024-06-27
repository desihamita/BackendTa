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
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->nullable();
            $table->integer('price')->nullable();
            $table->integer('stock')->nullable();
            $table->string('photo')->nullable();
            $table->string('sku')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('status')->nullable()->comment('0 = Inactive, 1 = active');

            $table->unsignedBigInteger('brand_id')->nullable;
            $table->unsignedBigInteger('sub_category_id')->nullable;
            $table->unsignedBigInteger('supplier_id')->nullable;
            $table->unsignedBigInteger('created_by_id')->nullable;
            $table->unsignedBigInteger('updated_by_id')->nullable;

            $table->foreignId('category_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attributes');
    }
};