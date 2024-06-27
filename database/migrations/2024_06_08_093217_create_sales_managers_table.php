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
        Schema::create('sales_managers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email',50)->nullable();
            $table->tinyInteger('status')->default(0);
            $table->text('bio')->nullable();
            $table->string('photo')->nullable();
            $table->string('password')->nullable();

            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('shop_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_managers');
    }
};
