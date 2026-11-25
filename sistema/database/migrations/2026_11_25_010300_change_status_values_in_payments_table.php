<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // El estado del pago dice qué fue ese pago para su pedido:
        // "adelanto" (todavía queda saldo) o "cancelacion" (el pago que completa el total)
        DB::table('payments')->where('status', 'completo')->update(['status' => 'cancelacion']);
        DB::table('payments')->where('status', '!=', 'cancelacion')->update(['status' => 'adelanto']);

        Schema::table('payments', function (Blueprint $table) {
            $table->string('status')->default('adelanto')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('payments')->where('status', 'cancelacion')->update(['status' => 'completo']);
        DB::table('payments')->where('status', 'adelanto')->update(['status' => 'parcial']);

        Schema::table('payments', function (Blueprint $table) {
            $table->string('status')->default('pendiente')->change();
        });
    }
};
