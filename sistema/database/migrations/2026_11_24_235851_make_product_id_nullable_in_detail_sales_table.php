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
        // La venta que origina un pedido copia sus prendas, y una prenda personalizada
        // no está en el catálogo: igual que en order_details, el producto pasa a ser opcional
        // (Producto 0..1 — 0..* DetalleVenta)
        Schema::table('detail_sales', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_sales', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable(false)->change();
        });
    }
};
