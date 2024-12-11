<?php

namespace App\Http\Controllers;

use App\Models\Evaluacion;
use Illuminate\Http\Request;

class CalificacionController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'empleado_id' => 'required|exists:usuarios,id_usuarios',
            'mes' => 'required|numeric|min:1|max:12',
            'anio' => 'required|numeric',
            'calificacion' => 'required|numeric|min:0|max:10',
            'comentarios' => 'nullable|string'
        ]);

        Evaluacion::create([
            'id_usuario' => $data['empleado_id'],
            'mes' => $data['mes'],
            'anio' => $data['anio'],
            'calificacion' => $data['calificacion'],
            'comentarios' => $data['comentarios']
        ]);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, Evaluacion $calificacion)
    {
        $data = $request->validate([
            'mes' => 'required|numeric|min:1|max:12',
            'anio' => 'required|numeric',
            'calificacion' => 'required|numeric|min:0|max:10',
            'comentarios' => 'nullable|string'
        ]);

        $calificacion->update($data);

        return response()->json(['success' => true]);
    }

    public function getCalificaciones($empleadoId)
    {
        $calificaciones = Evaluacion::where('id_usuario', $empleadoId)
            ->orderBy('anio', 'desc')
            ->orderBy('mes', 'desc')
            ->get();

        return response()->json($calificaciones);
    }
}
