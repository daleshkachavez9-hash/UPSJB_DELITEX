<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // Listado de usuarios (con su rol cargado para evitar el N+1)
    public function index()
    {
        $users = User::with('role')->orderBy('name')->get();

        return view('settings.users.index', compact('users'));
    }

    // Formulario para crear
    public function create()
    {
        $roles = Role::orderBy('name')->get();

        return view('settings.users.create', compact('roles'));
    }

    // Guardar nuevo usuario
    public function store(Request $request)
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'name' => 'required|max:255',
            'dni' => 'required|digits:8|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:8|confirmed', // confirmed: debe coincidir con password_confirmation
        ]);

        User::create($validated); // el cast 'hashed' del modelo encripta la contraseña al guardar

        return to_route('users.index')->with('ok', 'Usuario registrado correctamente.');
    }

    // Formulario para editar
    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();

        return view('settings.users.edit', compact('user', 'roles'));
    }

    // Actualizar usuario
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'name' => 'required|max:255',
            'dni' => ['required', 'digits:8', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:8|confirmed',
        ]);

        // Si no escribió contraseña nueva, se mantiene la actual
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return to_route('users.index')->with('ok', 'Usuario actualizado correctamente.');
    }

    // Eliminar usuario (solo si no tiene ventas ni reportes)
    public function destroy(User $user)
    {
        if ($user->sales()->exists() || $user->reports()->exists()) {
            return to_route('users.index')
                ->with('error', 'No se puede eliminar el usuario porque tiene ventas o reportes registrados.');
        }

        $user->delete();

        return to_route('users.index')->with('ok', 'Usuario eliminado correctamente.');
    }
}
