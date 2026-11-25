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
    public function create(Request $request)
    {
        $roles = $this->rolesAsignables($request);

        return view('settings.users.create', compact('roles'));
    }

    // Guardar nuevo usuario
    public function store(Request $request)
    {
        $validated = $request->validate([
            'role_id' => ['required', Rule::in($this->rolesAsignables($request)->pluck('id'))],
            'name' => 'required|max:255',
            'dni' => 'required|digits:8|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:8|confirmed', // confirmed: debe coincidir con password_confirmation
        ]);

        User::create($validated); // el cast 'hashed' del modelo encripta la contraseña al guardar

        return to_route('users.index')->with('ok', 'Usuario registrado correctamente.');
    }

    // Formulario para editar
    public function edit(Request $request, User $user)
    {
        $roles = $this->rolesAsignables($request);
        if (! $roles->contains('id', $user->role_id)) {
            return to_route('users.index')->with('error', 'No puedes modificar a un usuario con más permisos que tú.');
        }

        return view('settings.users.edit', compact('user', 'roles'));
    }

    // Actualizar usuario
    public function update(Request $request, User $user)
    {
        $asignables = $this->rolesAsignables($request);
        if (! $asignables->contains('id', $user->role_id)) {
            return to_route('users.index')->with('error', 'No puedes modificar a un usuario con más permisos que tú.');
        }

        $validated = $request->validate([
            'role_id' => ['required', Rule::in($asignables->pluck('id'))],
            'name' => 'required|max:255',
            'dni' => ['required', 'digits:8', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:8|confirmed',
        ]);

        // Nadie cambia su propio rol: así no puede darse más permisos ni quitarse el acceso por error
        if ($user->is($request->user()) && (int) $validated['role_id'] !== $user->role_id) {
            return back()->withInput()->withErrors(['role_id' => 'No puedes cambiar tu propio rol.']);
        }

        // Si no escribió contraseña nueva, se mantiene la actual
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return to_route('users.index')->with('ok', 'Usuario actualizado correctamente.');
    }

    // Eliminar usuario (solo si no tiene ventas ni reportes)
    public function destroy(Request $request, User $user)
    {
        if ($user->is($request->user())) {
            return to_route('users.index')->with('error', 'No puedes eliminar tu propio usuario.');
        }

        if (! $this->rolesAsignables($request)->contains('id', $user->role_id)) {
            return to_route('users.index')->with('error', 'No puedes eliminar a un usuario con más permisos que tú.');
        }

        if ($user->sales()->exists() || $user->reports()->exists() || $user->productions()->exists()) {
            return to_route('users.index')
                ->with('error', 'No se puede eliminar el usuario porque tiene ventas, reportes o trabajos de producción registrados.');
        }

        $user->delete();

        return to_route('users.index')->with('ok', 'Usuario eliminado correctamente.');
    }

    // Roles que el usuario puede asignar: solo los que tienen los mismos módulos que el suyo o menos.
    // Así una Administradora no puede crear (ni convertirse en) Dueña.
    private function rolesAsignables(Request $request)
    {
        $propios = $request->user()->role->modules ?? [];

        return Role::orderBy('name')->get()
            ->filter(fn ($rol) => empty(array_diff($rol->modules ?? [], $propios)))
            ->values();
    }
}
