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
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            // Pedido 1 — 0..* Produccion «pasa por»
            $table->foreignId('order_id')->constrained()->restrictOnDelete();
            // Usuario 1 — 0..* Produccion «realiza» (responsable del taller)
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('stage')->default('corte');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default('pendiente');
            $table->text('observation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productions');
    }
};
