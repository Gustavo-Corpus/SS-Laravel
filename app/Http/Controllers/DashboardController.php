<?php

namespace App\Http\Controllers;

use App\Models\Estado;
use App\Models\Usuario;
use App\Models\Departamento;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Obtener estados y departamentos para los formularios
        $estados = Estado::orderBy('estado')->get();
        $departamentos = Departamento::orderBy('nombre_departamento')->get();

        // Obtener empleados con sus relaciones y promedio de calificaciones
        $empleados = Usuario::select('usuarios.*')
            ->where('estatus', 'activo')
            ->leftJoin('departamentos', 'usuarios.id_departamento', '=', 'departamentos.id_departamento')
            ->leftJoin('estados', 'usuarios.id_estado', '=', 'estados.id_estado')
            ->with(['departamento', 'estado'])
            ->addSelect(DB::raw('(SELECT AVG(calificacion) FROM evaluaciones WHERE evaluaciones.id_usuario = usuarios.id_usuarios) as promedio_calificacion'))
            ->orderBy('nombre')
            ->get();

        return view('dashboard', compact('estados', 'departamentos', 'empleados'));
    }

    public function getEmpleadosPorEstado(Request $request)
    {
        $empleados = Usuario::select('usuarios.*')
            ->where('estatus', 'activo')
            ->where('id_estado', $request->estado)
            ->addSelect(DB::raw('(SELECT AVG(calificacion) FROM evaluaciones WHERE evaluaciones.id_usuario = usuarios.id_usuarios) as promedio_calificacion'))
            ->orderBy('nombre')
            ->get();

        return view('empleados.lista', compact('empleados'));
    }

    public function getEstadisticas()
    {
        $stats = [
            'totalEmpleados' => Usuario::where('estatus', 'activo')->count(),
            'promedioGlobal' => DB::table('evaluaciones')->avg('calificacion'),
            'totalEstados' => Estado::count(),
            'distribucionEstados' => DB::table('usuarios')
                ->select('estados.estado', DB::raw('count(*) as total'))
                ->join('estados', 'usuarios.id_estado', '=', 'estados.id_estado')
                ->where('usuarios.estatus', 'activo')
                ->groupBy('estados.estado')
                ->get(),
            'promediosPorEstado' => DB::table('estados')
                ->select('estados.estado',
                        DB::raw('COALESCE(AVG(evaluaciones.calificacion), 0) as promedio'))
                ->leftJoin('usuarios', 'estados.id_estado', '=', 'usuarios.id_estado')
                ->leftJoin('evaluaciones', 'usuarios.id_usuarios', '=', 'evaluaciones.id_usuario')
                ->where('usuarios.estatus', 'activo')
                ->groupBy('estados.estado')
                ->get()
        ];

        return response()->json($stats);
    }

    public function exportarEmpleados(Request $request)
    {
        $estado = Estado::findOrFail($request->estado);

        $empleados = Usuario::select(
                'usuarios.id_usuarios as ID',
                'usuarios.nombre as Nombre',
                'usuarios.apellido as Apellido',
                'usuarios.edad as Edad',
                'usuarios.sexo as Sexo',
                'usuarios.correo as Correo',
                'usuarios.ocupacion as Puesto',
                'departamentos.nombre_departamento as Departamento',
                DB::raw('COALESCE((SELECT AVG(calificacion) FROM evaluaciones WHERE evaluaciones.id_usuario = usuarios.id_usuarios), 0) as Promedio')
            )
            ->where('usuarios.id_estado', $request->estado)
            ->where('usuarios.estatus', 'activo')
            ->leftJoin('departamentos', 'usuarios.id_departamento', '=', 'departamentos.id_departamento')
            ->get();

        $nombreArchivo = 'Empleados_' . str_replace(' ', '_', $estado->estado) . '_' . date('Y-m-d') . '.csv';

        return response()->streamDownload(function() use ($empleados) {
            $file = fopen('php://output', 'w');
            // BOM para UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Encabezados
            fputcsv($file, array_keys($empleados->first()->toArray()));

            // Datos
            foreach ($empleados as $empleado) {
                $empleado->Promedio = number_format($empleado->Promedio, 1);
                fputcsv($file, $empleado->toArray());
            }

            fclose($file);
        }, $nombreArchivo);
    }
}
