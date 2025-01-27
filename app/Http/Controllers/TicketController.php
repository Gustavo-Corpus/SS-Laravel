<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\CalificacionTicket;
use App\Models\User;
use App\Notifications\TicketAsignado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Usuario;

class TicketController extends Controller
{
    public function index()
{
    try {
        $userLogged = auth()->user();

        $query = Ticket::with(['usuario', 'empleadoAsignado'])
            ->orderBy('created_at', 'desc');

        // Filtrar tickets según el rol del usuario
        if ($userLogged->rol === 'cliente') {
            $tickets = $query->where('id_usuario', $userLogged->id)->get();
        }
        elseif ($userLogged->rol === 'empleado') {
            $tickets = $query->where('id_asignado', $userLogged->id)->get();
        }
        else {
            // Admin ve todos los tickets
            $tickets = $query->get();
        }

        return view('tickets.index', compact('tickets'));
    } catch (\Exception $e) {
        \Log::error('Error en index de tickets: ' . $e->getMessage());
        return back()->with('error', 'Error al cargar los tickets');
    }
}

public function store(Request $request)
{
    try {
        DB::beginTransaction(); // Iniciar transacción

        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'prioridad' => 'required|in:baja,media,alta',
        ]);

        // Buscar empleado disponible
        $empleadoDisponible = User::where('rol', 'empleado')
            ->inRandomOrder()
            ->first();

        if (!$empleadoDisponible) {
            DB::rollBack();
            \Log::error('No se encontró empleado disponible');
            return response()->json([
                'message' => 'Error: No hay empleados registrados en el sistema',
                'error' => true
            ], 400);
        }

        // Generar número de ticket
        $lastTicket = Ticket::latest('id')->first();
        $numeroTicket = $lastTicket
            ? 'TK-' . str_pad((intval(substr($lastTicket->numero_ticket, 3)) + 1), 3, '0', STR_PAD_LEFT)
            : 'TK-001';

        // Crear el ticket
        $ticket = Ticket::create([
            'numero_ticket' => $numeroTicket,
            'id_usuario' => auth()->id(),
            'id_asignado' => $empleadoDisponible->id,
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'prioridad' => $request->prioridad,
            'estado' => 'abierto'
        ]);

        \Log::info('Ticket creado:', [
            'ticket_id' => $ticket->id,
            'empleado_id' => $empleadoDisponible->id,
            'empleado_nombre' => $empleadoDisponible->username
        ]);

        // Enviar notificación
        try {
            $empleadoDisponible->notify(new TicketAsignado($ticket));
            \Log::info('Notificación enviada correctamente');
        } catch (\Exception $e) {
            \Log::error('Error al enviar notificación: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
        }

        DB::commit(); // Confirmar transacción

        return response()->json([
            'message' => 'Ticket creado exitosamente',
            'ticket' => $ticket->load(['usuario', 'empleadoAsignado'])
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error en store: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        return response()->json([
            'message' => 'Error al crear el ticket',
            'error' => $e->getMessage()
        ], 500);
    }
}

    public function show(Ticket $ticket)
    {
        try {
            // Cargar las relaciones necesarias
            $ticket->load([
                'usuario',
                'empleadoAsignado',
                'calificaciones'
            ]);

            // Verificar permiso (solo el cliente dueño del ticket o el empleado asignado pueden verlo)
            $userLogged = auth()->user();
            if ($userLogged->rol === 'cliente' && $ticket->id_usuario !== $userLogged->id ||
                $userLogged->rol === 'empleado' && $ticket->id_asignado !== $userLogged->id) {
                return response()->json([
                    'error' => 'No autorizado para ver este ticket'
                ], 403);
            }

            \Log::info('Mostrando ticket:', [
                'ticket_id' => $ticket->id,
                'usuario' => $userLogged->id
            ]);

            return view('tickets.modals.detalles', compact('ticket'));

        } catch (\Exception $e) {
            \Log::error('Error en show ticket: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al cargar los detalles del ticket: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Ticket $ticket)
{
    try {
        \Log::info('Actualizando ticket', $request->all());

        $request->validate([
            'estado' => 'required|in:abierto,en_proceso,resuelto,cerrado',
            'comentario' => 'nullable|string'
        ]);

        $ticket->estado = $request->estado;
        $ticket->comentario = $request->comentario;
        $ticket->save();

        return response()->json([
            'message' => 'Ticket actualizado correctamente',
            'ticket' => $ticket->load(['usuario', 'empleadoAsignado'])
        ]);

    } catch (\Exception $e) {
        \Log::error('Error al actualizar ticket: ' . $e->getMessage());
        return response()->json([
            'message' => 'Error al actualizar el ticket',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function calificar(Request $request, Ticket $ticket)
{
    try {
        \Log::info('Intento de calificación:', [
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'ticket_user_id' => $ticket->id_usuario,
            'estado_ticket' => $ticket->estado
        ]);

        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comentario' => 'nullable|string'
        ]);

        // Verificar que el ticket esté resuelto
        if ($ticket->estado !== 'resuelto') {
            \Log::warning('Ticket no está resuelto', ['estado' => $ticket->estado]);
            return response()->json([
                'message' => 'El ticket debe estar resuelto para poder calificarlo'
            ], 403);
        }

        // Verificar que el usuario que califica sea el dueño del ticket
        $userLogged = auth()->user();
        \Log::info('Usuario intentando calificar:', [
            'user_logged_id' => $userLogged->id,
            'ticket_user_id' => $ticket->id_usuario,
            'roles' => [
                'user_role' => $userLogged->rol,
                'is_owner' => $ticket->id_usuario === $userLogged->id
            ]
        ]);

        if ($ticket->id_usuario != $userLogged->id) {
            return response()->json([
                'message' => 'Solo el creador del ticket puede calificarlo'
            ], 403);
        }

        // Verificar que el ticket no haya sido calificado antes
        $calificacionesExistentes = $ticket->calificaciones()->count();
        \Log::info('Calificaciones existentes:', ['count' => $calificacionesExistentes]);

        if ($calificacionesExistentes > 0) {
            return response()->json([
                'message' => 'Este ticket ya ha sido calificado'
            ], 403);
        }

        // Crear la calificación
        $calificacion = CalificacionTicket::create([
            'id_ticket' => $ticket->id,
            'id_usuario' => $userLogged->id,
            'id_empleado' => $ticket->id_asignado,
            'calificacion' => $request->rating,
            'comentario' => $request->comentario
        ]);

        // Actualizar la calificación promedio del empleado
        $empleado = User::find($ticket->id_asignado);
        if ($empleado) {
            // Actualizar promedio de calificaciones
            $promedio = CalificacionTicket::where('id_empleado', $empleado->id)
                ->avg('calificacion');

            // Actualizar número de tickets resueltos
            $ticketsResueltos = Ticket::where('id_asignado', $empleado->id)
                ->where('estado', 'resuelto')
                ->count();

            // Guardar ambos valores
            $empleado->average_rating = round($promedio, 2);
            $empleado->tickets_resolved = $ticketsResueltos;
            $empleado->save();
        }

        // Marcar el ticket como cerrado después de calificar
        $ticket->estado = 'cerrado';
        $ticket->save();

        return response()->json([
            'message' => 'Calificación registrada exitosamente',
            'calificacion' => $calificacion
        ]);

    } catch (\Exception $e) {
        \Log::error('Error al calificar ticket: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'message' => 'Error al registrar la calificación: ' . $e->getMessage()
        ], 500);
    }
}

    public function misTickets()
    {
        $tickets = Ticket::where('id_usuario', auth()->user()->id_usuarios)
            ->with(['empleadoAsignado'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('tickets.mis-tickets', compact('tickets'));
    }

    public function ticketsPorEstado()
    {
        $estadisticas = Ticket::selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->get()
            ->map(function($item) {
                return [
                    'estado' => ucfirst($item->estado),
                    'total' => $item->total
                ];
            });

        return response()->json($estadisticas);
    }

    public function ticketsPorPrioridad()
    {
        $estadisticas = Ticket::selectRaw('prioridad, COUNT(*) as total')
            ->groupBy('prioridad')
            ->get()
            ->map(function($item) {
                return [
                    'prioridad' => ucfirst($item->prioridad),
                    'total' => $item->total
                ];
            });

        return response()->json($estadisticas);
    }

    public function dashboard()
    {
        // Contadores generales
        $totalTickets = Ticket::count();
        $ticketsEnProceso = Ticket::where('estado', 'en_proceso')->count();
        $ticketsResueltos = Ticket::where('estado', 'resuelto')->count();
        $ticketsSinAsignar = Ticket::whereNull('id_asignado')->count();

        // Top empleados con todas sus estadísticas
        $topEmpleados = User::where('rol', 'empleado')
            ->withCount(['ticketsAsignados as tickets_pendientes' => function($query) {
                $query->whereIn('estado', ['abierto', 'en_proceso']);
            }])
            ->withCount(['ticketsAsignados as tickets_resueltos' => function($query) {
                $query->where('estado', 'resuelto');
            }])
            ->withAvg('calificacionesRecibidas as calificacion_promedio', 'calificacion')
            ->having('tickets_resueltos', '>', 0)  // Solo mostrar empleados que han resuelto tickets
            ->orderByDesc('tickets_resueltos')
            ->take(5)
            ->get();

        // Mapear los datos para un formato más amigable
        $topEmpleados = $topEmpleados->map(function($empleado) {
            return [
                'nombre' => $empleado->nombre . ' ' . $empleado->apellido,
                'tickets_resueltos' => $empleado->tickets_resueltos,
                'calificacion_promedio' => round($empleado->calificacion_promedio ?? 0, 2),
                'tickets_pendientes' => $empleado->tickets_pendientes
            ];
        });

        return view('tickets.dashboard', compact(
            'totalTickets',
            'ticketsEnProceso',
            'ticketsResueltos',
            'ticketsSinAsignar',
            'topEmpleados'
        ));
    }

public function destroy(Ticket $ticket)
{
    try {
        // Verificar que el usuario sea el dueño del ticket
        if ($ticket->id_usuario !== auth()->id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Verificar que el ticket esté en estado abierto
        if ($ticket->estado !== 'abierto') {
            return response()->json([
                'message' => 'Solo se pueden eliminar tickets que estén abiertos'
            ], 400);
        }

        $ticket->delete();

        return response()->json([
            'message' => 'Ticket eliminado exitosamente'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error al eliminar el ticket'
        ], 500);
    }
}
}
