<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
{
    \Log::info('Intento de login', ['username' => $request->username]);

    try {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        // Usamos Auth::attempt en lugar de la consulta directa
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            \Log::info('Login exitoso');
            return redirect()->intended('/dashboard');
        }

        \Log::info('Credenciales incorrectas');
        return back()->withErrors([
            'username' => 'Las credenciales no coinciden.'
        ]);

    } catch (\Exception $e) {
        \Log::error('Error en login: ' . $e->getMessage());
        return back()->withErrors([
            'error' => 'Error al iniciar sesión'
        ]);
    }
}

// Agregar el método logout si no lo tienes
public function logout(Request $request)
{
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
}
}
