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
        // Producto 1 — 0..1 Inventario. Si algún producto quedó con dos registros,
        // se conserva el más reciente antes de crear el índice único.
        $repetidos = DB::table('inventories')->select('product_id')->groupBy('product_id')->havingRaw('COUNT(*) > 1')->pluck('product_id');
        foreach ($repetidos as $productId) {
            $ultimo = DB::table('inventories')->where('product_id', $productId)->max('id');
            DB::table('inventories')->where('product_id', $productId)->where('id', '!=', $ultimo)->delete();
        }

        Schema::table('inventories', function (Blueprint $table) {
            $table->unique('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            // La clave foránea usa el índice: se crea uno normal antes de quitar el único
            $table->index('product_id');
            $table->dropUnique(['product_id']);
        });
    }
};
