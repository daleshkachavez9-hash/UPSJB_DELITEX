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
        // Imagen de referencia del pedido (logo, escudo o diseño que envía el cliente).
        // Igual que la imagen del producto: se guarda la ruta y el archivo va al disco public.
        Schema::table('orders', function (Blueprint $table) {
            $table->string('reference_image')->nullable()->after('description');
        });

        // Datos de cada prenda que necesita el taller para la ficha de producción
        Schema::table('order_details', function (Blueprint $table) {
            $table->string('garment_type', 50)->nullable()->after('product_id');
            $table->string('fabric', 30)->nullable()->after('color');
            $table->string('print_type', 50)->default('Sin estampado')->after('fabric');
            $table->string('print_detail')->nullable()->after('print_type');
            $table->string('notes')->nullable()->after('print_detail');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn(['garment_type', 'fabric', 'print_type', 'print_detail', 'notes']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('reference_image');
        });
    }
};
