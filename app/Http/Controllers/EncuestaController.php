<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Encuesta; // Asegúrate de tener el modelo Encuesta

class EncuestaController extends Controller
{
    public function store(Request $request)
    {
        // Validar los datos del formulario
        $request->validate([
            'pregunta1' => 'required|integer|between:1,5',
            'pregunta2' => 'required|integer|between:1,5',
            'pregunta3' => 'required|string|max:500',
        ]);

        // Guardar los datos en la base de datos
        Encuesta::create($request->all());

        // Retornar una respuesta
        return response()->json(['message' => 'Encuesta enviada correctamente.']);
    }
}

}
