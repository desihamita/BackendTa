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
        Schema::create('trxIngredients', function (Blueprint $table) {
            $table->id();
            $table->string('trxIngredients_id')->nullable();
            $table->tinyInteger('transaction_type')->comment('1=cash, 2=debit, 3=gopay, 4=ovo, 5=dana, 6=qris');
            $table->tinyInteger('status')->comment('1=success, 2=failed');
            $table->integer('amount');

            $table->morphs('transactionable');
            $table->unsignedBigInteger('order_bahan_baku_id')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->foreignId('payment_method_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trxIngredients');
    }
};
