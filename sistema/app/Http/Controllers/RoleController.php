<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    // Listado de roles
    public function index()
    {
        $roles = Role::withCount('users')->orderBy('name')->get();

        return view('settings.roles.index', compact('roles'));
    }

    // Formulario para crear
    public function create()
    {
        return view('settings.roles.create');
    }

    // Guardar nuevo rol
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:50|unique:roles',
            'description' => 'nullable|max:255',
        ]);

        Role::create($validated);

        return to_route('roles.index')->with('ok', 'Rol registrado correctamente.');
    }

    // Formulario para editar
    public function edit(Role $role)
    {
        return view('settings.roles.edit', compact('role'));
    }

    // Actualizar rol
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            // ignore(): el nombre no se repite, sin contar el rol que se está editando
            'name' => ['required', 'max:50', Rule::unique('roles')->ignore($role->id)],
            'description' => 'nullable|max:255',
        ]);

        $role->update($validated);

        return to_route('roles.index')->with('ok', 'Rol actualizado correctamente.');
    }

    // Eliminar rol (solo si no tiene usuarios)
    public function destroy(Role $role)
    {
        if ($role->users()->exists()) {
            return to_route('roles.index')
                ->with('error', 'No se puede eliminar el rol porque tiene usuarios asignados.');
        }

        $role->delete();

        return to_route('roles.index')->with('ok', 'Rol eliminado correctamente.');
    }
}
