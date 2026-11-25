<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'description', 'modules'])]
class Role extends Model
{
    // Módulos del sistema que se pueden dar a un rol.
    // "seguimiento" solo consulta los pedidos; "pedidos" permite registrarlos y modificarlos.
    const MODULOS = [
        'reportes' => 'Reportes',
        'clientes' => 'Clientes',
        'seguimiento' => 'Seguimiento de pedidos (consultar)',
        'pedidos' => 'Pedidos (registrar y modificar)',
        'ventas' => 'Ventas',
        'produccion' => 'Producción',
        'productos' => 'Productos',
        'inventario' => 'Inventario y alertas de stock',
        'pagos' => 'Pagos',
        'entregas' => 'Entregas',
        'roles' => 'Roles',
        'usuarios' => 'Usuarios',
        'categorias' => 'Categorías',
    ];

    // Módulos de los roles que crea el seeder (los mismos del prototipo)
    const MODULOS_POR_DEFECTO = [
        'Dueña' => ['reportes', 'clientes', 'seguimiento', 'pedidos', 'ventas', 'produccion', 'productos', 'inventario', 'pagos', 'entregas', 'roles', 'usuarios', 'categorias'],
        'Administradora' => ['reportes', 'clientes', 'seguimiento', 'pedidos', 'ventas', 'produccion', 'productos', 'inventario', 'pagos', 'entregas', 'usuarios', 'categorias'],
        'Confeccionista' => ['seguimiento', 'produccion', 'productos', 'inventario'],
    ];

    protected function casts(): array
    {
        return [
            // Se guarda como JSON en la base de datos y se usa como arreglo en PHP
            'modules' => 'array',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // ¿Este rol puede entrar al módulo?
    public function permite(string $modulo): bool
    {
        return in_array($modulo, $this->modules ?? [], true);
    }
}
