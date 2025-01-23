<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CasoController extends Controller
{
    public function update(Request $request)
    {
        $caso = Caso::findOrFail($request->id);
        $caso->descripcion = $request->descripcion;
        $caso->estatus = $request->estatus;
        $caso->save();
    
        return redirect()->back()->with('success', 'Caso actualizado correctamente.');
    }
    
