<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['role' => 'Dueña', 'name' => 'María Quispe', 'dni' => '40123456', 'email' => 'maria.quispe@delitex.pe'],
            ['role' => 'Administradora', 'name' => 'Lucía Rojas', 'dni' => '42567891', 'email' => 'lucia.rojas@delitex.pe'],
            ['role' => 'Confeccionista', 'name' => 'Jorge Taipe', 'dni' => '46789123', 'email' => 'jorge.taipe@delitex.pe'],
        ];

        foreach ($users as $user) {
            // El cast 'hashed' del modelo User encripta la contraseña al guardar
            User::updateOrCreate(['email' => $user['email']], [
                'role_id' => Role::where('name', $user['role'])->value('id'),
                'name' => $user['name'],
                'dni' => $user['dni'],
                'password' => 'delitex123',
                'email_verified_at' => now(),
            ]);
        }
    }
}
