<?php

use App\Models\Role;
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
        // Módulos del sistema a los que puede entrar cada rol (lista guardada como JSON)
        Schema::table('roles', function (Blueprint $table) {
            $table->json('modules')->nullable()->after('description');
        });

        // Roles que ya existían: los del seeder reciben sus módulos; cualquier otro conserva el acceso total que tenía
        foreach (DB::table('roles')->get() as $rol) {
            $modulos = Role::MODULOS_POR_DEFECTO[$rol->name] ?? array_keys(Role::MODULOS);
            DB::table('roles')->where('id', $rol->id)->update(['modules' => json_encode($modulos)]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('modules');
        });
    }
};
