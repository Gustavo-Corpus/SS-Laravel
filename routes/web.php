<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\CalificacionController;
use Illuminate\Support\Facades\Route;

// Rutas de autenticación
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/register', [RegisterController::class, 'store'])->name('register');
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

    // Calificaciones
    Route::get('/calificaciones/{empleado}', [CalificacionController::class, 'getCalificaciones']);
    Route::post('/calificaciones', [CalificacionController::class, 'store']);
    Route::put('/calificaciones/{calificacion}', [CalificacionController::class, 'update']);

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
