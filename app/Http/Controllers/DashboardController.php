<?php

namespace App\Http\Controllers;

use App\Models\Estado;
use App\Models\Usuario;
use App\Models\Evaluacion;
use App\Models\Departamento;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
    try {
        Log::info('Iniciando getEstadisticas');

        // Verificar la conexión a la base de datos
        try {
            DB::connection()->getPdo();
            Log::info('Conexión a base de datos exitosa');
        } catch (\Exception $e) {
            Log::error('Error de conexión a la base de datos: ' . $e->getMessage());
            throw $e;
        }

        // Debuggear cada consulta
        Log::info('Obteniendo total de empleados');
        $totalEmpleados = Usuario::where('estatus', 'activo')->count();
        Log::info('Total empleados: ' . $totalEmpleados);

        Log::info('Obteniendo promedio global');
        $promedioGlobal = Evaluacion::avg('calificacion') ?? 0;
        Log::info('Promedio global: ' . $promedioGlobal);

        Log::info('Obteniendo total de estados');
        $totalEstados = Estado::count();
        Log::info('Total estados: ' . $totalEstados);

        Log::info('Obteniendo distribución de estados');
        $distribucionEstados = DB::table('estados')
            ->leftJoin('usuarios', function($join) {
                $join->on('estados.id_estado', '=', 'usuarios.id_estado')
                     ->where('usuarios.estatus', '=', 'activo');
            })
            ->select('estados.estado', DB::raw('COUNT(usuarios.id_usuarios) as total'))
            ->groupBy('estados.id_estado', 'estados.estado')
            ->orderBy('estados.estado')
            ->get();
        Log::info('Distribución de estados:', $distribucionEstados->toArray());

        Log::info('Obteniendo promedios por estado');
        $promediosPorEstado = DB::table('estados')
            ->leftJoin('usuarios', function($join) {
                $join->on('estados.id_estado', '=', 'usuarios.id_estado')
                     ->where('usuarios.estatus', '=', 'activo');
            })
            ->leftJoin('evaluaciones', 'usuarios.id_usuarios', '=', 'evaluaciones.id_usuario')
            ->select('estados.estado', DB::raw('COALESCE(AVG(evaluaciones.calificacion), 0) as promedio'))
            ->groupBy('estados.id_estado', 'estados.estado')
            ->orderBy('estados.estado')
            ->get();
        Log::info('Promedios por estado:', $promediosPorEstado->toArray());

        $datos = [
            'totalEmpleados' => $totalEmpleados,
            'promedioGlobal' => round($promedioGlobal, 2),
            'totalEstados' => $totalEstados,
            'distribucionEstados' => $distribucionEstados->map(function($item) {
                return [
                    'estado' => $item->estado,
                    'total' => (int)$item->total
                ];
            }),
            'promediosPorEstado' => $promediosPorEstado->map(function($item) {
                return [
                    'estado' => $item->estado,
                    'promedio' => round((float)$item->promedio, 1)
                ];
            })
        ];

        Log::info('Datos finales a devolver:', $datos);
        return response()->json($datos);

    } catch (\Exception $e) {
        Log::error('Error en getEstadisticas: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        Log::error('Línea del error: ' . $e->getLine());
        Log::error('Archivo del error: ' . $e->getFile());

        return response()->json([
            'error' => 'Error interno del servidor',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], 500);
    }
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
