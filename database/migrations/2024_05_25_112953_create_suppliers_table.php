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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email',50)->nullable();
            $table->tinyInteger('status')->default(0);
            $table->text('details')->nullable();
            $table->string('logo')->nullable();

            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};