<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Dueña', 'description' => 'Cuenta principal con acceso a todos los módulos'],
            ['name' => 'Administradora', 'description' => 'Gestiona clientes, pedidos, pagos y entregas'],
            ['name' => 'Confeccionista', 'description' => 'Atiende la producción y el uso de insumos'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}
