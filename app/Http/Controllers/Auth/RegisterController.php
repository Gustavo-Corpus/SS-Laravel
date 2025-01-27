<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users',
            'nombre' => 'required|string',
            'apellido' => 'required|string',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'username' => $request->username,
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'password' => Hash::make($request->password),
            'rol' => 'cliente'
        ]);

        auth()->login($user);

        return redirect()->intended('dashboard');
    }
}
