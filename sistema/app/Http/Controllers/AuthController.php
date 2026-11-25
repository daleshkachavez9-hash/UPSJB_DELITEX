<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Formulario de inicio de sesión
    public function create()
    {
        return view('auth.login');
    }

    // Ingresar con el correo o el DNI y la contraseña
    public function store(Request $request)
    {
        $request->validate([
            'login' => 'required|max:255',
            'password' => 'required',
        ], [], [
            'login' => 'usuario o correo',
        ]);

        // Si escribió 8 números se busca por DNI; si no, por correo
        $campo = preg_match('/^\d{8}$/', $request->login) ? 'dni' : 'email';
        $user = User::where($campo, $request->login)->first();

        // Método del diagrama: iniciarSesion()
        if (! $user || ! $user->iniciarSesion($request->password, $request->boolean('remember'))) {
            return back()
                ->withErrors(['login' => 'El usuario o la contraseña no son correctos.'])
                ->onlyInput('login');
        }

        // Nueva sesión para evitar que se reutilice la anterior
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard.index'));
    }

    // Cerrar sesión
    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('login')->with('ok', 'Cerraste sesión correctamente.');
    }
}
