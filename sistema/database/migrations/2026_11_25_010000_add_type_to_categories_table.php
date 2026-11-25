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
        // DELITEX confecciona y vende prendas; las telas, hilos y accesorios son insumos del taller
        Schema::table('categories', function (Blueprint $table) {
            $table->string('type', 20)->default('prenda')->after('name');
        });

        // Las categorías de insumos que crea el seeder
        DB::table('categories')->whereIn('name', ['Telas', 'Hilos', 'Accesorios', 'Estampado'])->update(['type' => 'insumo']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
