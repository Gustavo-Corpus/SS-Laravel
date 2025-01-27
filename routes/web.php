<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\CalificacionController;
use App\Http\Controllers\Auth\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;

// Rutas de autenticación
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/register', [RegisterController::class, 'store'])->name('register');
    Route::post('/cambiar-password', [LoginController::class, 'cambiarPassword'])->name('cambiar.password');
});

// Rutas protegidas
Route::middleware(['auth'])->group(function () {
    // Redirección de raíz a dashboard
    Route::get('/', function () {
        return redirect('/dashboard');
    });

    // Dashboard y estadísticas
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/estadisticas', [DashboardController::class, 'getEstadisticas'])->name('estadisticas');

    // Empleados
    Route::get('/empleados/por-estado', [DashboardController::class, 'getEmpleadosPorEstado']);
    Route::get('/empleados/exportar', [DashboardController::class, 'exportarEmpleados']);

    // CRUD Empleados
    Route::post('/empleados', [EmpleadoController::class, 'store']);
    Route::get('/empleados/{empleado}', [EmpleadoController::class, 'show']);
    Route::post('/empleados/{empleado}', [EmpleadoController::class, 'update']);
    Route::delete('/empleados/{empleado}', [EmpleadoController::class, 'destroy']);

    Route::get('/empleado/{id}/evaluaciones', [EmpleadoController::class, 'getEvaluaciones']);

    // Calificaciones
    Route::get('/calificaciones/{empleado}', [CalificacionController::class, 'getCalificaciones']);
    Route::post('/calificaciones', [CalificacionController::class, 'store']);
    Route::put('/calificaciones/{calificacion}', [CalificacionController::class, 'update']);

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    //Rutas para Tickets
    Route::get('/tickets/dashboard', [TicketController::class, 'dashboard'])->name('tickets.dashboard');
    Route::get('/tickets/estadisticas/estado', [TicketController::class, 'ticketsPorEstado']);
    Route::get('/tickets/estadisticas/prioridad', [TicketController::class, 'ticketsPorPrioridad']);
    Route::get('/mis-tickets', [TicketController::class, 'misTickets'])->name('tickets.mis-tickets');
    Route::resource('tickets', TicketController::class);
    Route::post('/tickets/{ticket}/calificar', [TicketController::class, 'calificar'])->name('tickets.calificar');
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::put('/tickets/{ticket}', [TicketController::class, 'update'])->name('tickets.update');
    Route::post('/tickets/{ticket}/calificar', [TicketController::class, 'calificar'])->name('tickets.calificar');
    Route::get('/notificaciones', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notificaciones/marcar-leida/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notificaciones/marcar-todas-leidas', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

    // Rutas de gestión de usuarios
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users/{id}/role', [UserController::class, 'updateRole'])->name('users.update-role');
});

Route::post('/notifications/mark-as-read', function() {
    auth()->user()->unreadNotifications->markAsRead();
    return response()->json(['message' => 'Notificaciones marcadas como leídas']);
})->name('notifications.markAsRead');
