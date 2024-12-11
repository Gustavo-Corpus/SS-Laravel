<?php

namespace App\Http\Controllers;

use App\Models\Usuario;

class PruebaController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::all();
        dd($usuarios); // Esto mostrará todos los usuarios en pantalla
    }
}
