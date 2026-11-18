<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        User::factory()->create([
            'name' => 'María Quispe',
            'email' => 'maria.quispe@delitex.pe',
            'dni' => '40123456',
            'role_id' => Role::where('name', 'Dueña')->value('id'),
        ]);
    }
}
