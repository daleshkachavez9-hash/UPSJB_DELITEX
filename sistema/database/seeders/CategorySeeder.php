<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Prendas', 'type' => 'prenda', 'description' => 'Polos, buzos, shorts y conjuntos deportivos terminados'],
            ['name' => 'Telas', 'type' => 'insumo', 'description' => 'Dry-fit, microfibra, piqué y otras telas por metro'],
            ['name' => 'Hilos', 'type' => 'insumo', 'description' => 'Conos de hilo para costura y remallado'],
            ['name' => 'Accesorios', 'type' => 'insumo', 'description' => 'Cierres, elásticos, botones y etiquetas'],
            ['name' => 'Estampado', 'type' => 'insumo', 'description' => 'Vinil, tintas y papel para sublimación'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['name' => $category['name']], $category);
        }
    }
}
