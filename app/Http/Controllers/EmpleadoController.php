<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class EmpleadoController extends Controller
{
    public function store(Request $request)
{
    try {
        $data = $request->validate([
            'nombre' => 'required',
            'apellido' => 'required',
            'edad' => 'required|numeric',
            'sexo' => 'required',
            'correo' => 'required|email',
            'ocupacion' => 'required',
            'estado_empleado' => 'required',
            'departamento' => 'required',
            'avatar' => 'nullable|image|max:2048'
        ]);

        $empleado = new Usuario();
        $empleado->nombre = $data['nombre'];
        $empleado->apellido = $data['apellido'];
        $empleado->edad = $data['edad'];
        $empleado->sexo = $data['sexo'];
        $empleado->correo = $data['correo'];
        $empleado->ocupacion = $data['ocupacion'];
        $empleado->id_estado = $data['estado_empleado'];
        $empleado->id_departamento = $data['departamento'];
        $empleado->estatus = 'activo';

        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            $avatarNombre = time() . '_' . $request->file('avatar')->getClientOriginalName();
            $request->file('avatar')->storeAs('fotos_empleados', $avatarNombre, 'public');
            $empleado->avatar = $avatarNombre;
        }

        $empleado->save();

        // Recargar el empleado con sus relaciones
        $empleado->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Empleado agregado correctamente',
            'empleado' => $empleado
        ]);

    } catch (\Exception $e) {
        \Log::error('Error al crear empleado: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error al crear el empleado: ' . $e->getMessage()
        ], 500);
    }
}

public function update(Request $request, $id)
{
    try {
        $empleado = Usuario::findOrFail($id);

        $data = $request->validate([
            'nombre' => 'required',
            'apellido' => 'required',
            'edad' => 'required|numeric',
            'sexo' => 'required',
            'correo' => 'required|email',
            'ocupacion' => 'required',
            'estado_empleado' => 'required',
            'departamento' => 'required',
            'avatar' => 'nullable|image|max:2048'
        ]);

        // Actualizar campos básicos
        $empleado->nombre = $data['nombre'];
        $empleado->apellido = $data['apellido'];
        $empleado->edad = $data['edad'];
        $empleado->sexo = $data['sexo'];
        $empleado->correo = $data['correo'];
        $empleado->ocupacion = $data['ocupacion'];
        $empleado->id_estado = $data['estado_empleado'];
        $empleado->id_departamento = $data['departamento'];

        // Manejar el avatar si se subió uno nuevo
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            // Eliminar avatar anterior si existe
            if ($empleado->avatar) {
                Storage::disk('public')->delete('fotos_empleados/' . $empleado->avatar);
            }

            $avatarNombre = time() . '_' . $request->file('avatar')->getClientOriginalName();
            $request->file('avatar')->storeAs('fotos_empleados', $avatarNombre, 'public');
            $empleado->avatar = $avatarNombre;
        }

        $empleado->save();

        return response()->json([
            'success' => true,
            'message' => 'Empleado actualizado correctamente'
        ]);

    } catch (\Exception $e) {
        Log::error('Error al actualizar empleado: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error al actualizar el empleado: ' . $e->getMessage()
        ], 500);
    }
}

    public function destroy(Usuario $empleado)
    {
        try {
            // Eliminar avatar si existe
            if ($empleado->avatar) {
                Storage::delete('public/fotos_empleados/' . $empleado->avatar);
            }

            $empleado->estatus = 'inactivo';
            $empleado->save();

            return response()->json([
                'success' => true,
                'message' => 'Empleado eliminado correctamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al eliminar empleado: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el empleado: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Usuario $empleado)
{
    try {
        // Cargar las relaciones necesarias
        $empleado->load('departamento', 'estado');

        // Calcular el promedio de calificaciones
        $promedio = $empleado->evaluaciones()
            ->avg('calificacion') ?? 0;

        // Agregar el promedio al objeto empleado
        $empleado->promedio_calificacion = round($promedio, 1);

        return response()->json($empleado);
    } catch (\Exception $e) {
        Log::error('Error al obtener empleado: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener el empleado: ' . $e->getMessage()
        ], 500);
    }
}

public function getEvaluaciones($id)
{
    try {
        $empleado = Usuario::with(['departamento', 'evaluaciones' => function($query) {
            $query->orderBy('anio', 'ASC')
                  ->orderBy('mes', 'ASC')
                  ->limit(9);
        }])->findOrFail($id);

        $evaluaciones = $empleado->evaluaciones->map(function($eval) {
            $nombresMeses = [
                1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr',
                5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
                9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic'
            ];
            return [
                'mes' => $nombresMeses[$eval->mes] . ' ' . $eval->anio,
                'valor' => round($eval->calificacion, 1)
            ];
        })->values();

        return response()->json([
            'success' => true,
            'empleado' => [
                'nombre' => $empleado->nombre,
                'apellido' => $empleado->apellido,
                'puesto' => $empleado->ocupacion,
                'departamento' => $empleado->departamento->nombre_departamento,
                'correo' => $empleado->correo,
                'promedio' => round($empleado->evaluaciones->avg('calificacion'), 1),
                'avatar' => $empleado->avatar,
                'evaluaciones' => $evaluaciones
            ]
        ]);
    } catch (\Exception $e) {
        \Log::error('Error al obtener evaluaciones: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener datos del empleado'
        ], 500);
    }
}
}
