<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    // Mi perfil: datos del usuario que inició sesión y cambio de contraseña
    public function edit(Request $request)
    {
        $user = $request->user()->load('role');

        return view('settings.profile.edit', compact('user'));
    }

    // Actualizar nombre y correo (el rol y el DNI los cambia la administración desde Usuarios)
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->update($validated);

        return to_route('profile.edit')->with('ok', 'Tus datos se actualizaron correctamente.');
    }

    // Cambiar contraseña: pide la actual para confirmar que es el mismo usuario
    public function password(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|min:8|confirmed|different:current_password',
        ], [
            'current_password.current_password' => 'La contraseña actual no es correcta.',
            'password.different' => 'La nueva contraseña debe ser distinta a la actual.',
        ], [
            'current_password' => 'contraseña actual',
            'password' => 'nueva contraseña',
        ]);

        // El cast 'hashed' del modelo User la encripta al guardar
        $request->user()->update(['password' => $validated['password']]);

        return to_route('profile.edit')->with('ok', 'Tu contraseña se cambió correctamente.');
    }
}
