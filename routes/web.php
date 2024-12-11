<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\CalificacionController;
use Illuminate\Support\Facades\Route;

// Ruta raíz redirige al login
Route::get('/', function () {
    return redirect('/login');
});

// Rutas de autenticación
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/register', [RegisterController::class, 'store'])->name('register'); // Nueva ruta
});

// Rutas protegidas que requieren autenticación
Route::middleware('auth')->group(function () {
    // Ruta de logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rutas para empleados (filtrado, estadísticas y CRUD)
    Route::prefix('empleados')->group(function () {
        // Filtrado y estadísticas
        Route::get('/por-estado', [DashboardController::class, 'getEmpleadosPorEstado'])->name('empleados.por-estado');
        Route::get('/estadisticas', [DashboardController::class, 'getEstadisticas'])->name('empleados.estadisticas');
        Route::get('/exportar', [DashboardController::class, 'exportarEmpleados'])->name('empleados.exportar');

        // CRUD
        Route::post('/', [EmpleadoController::class, 'store'])->name('empleados.store'); // Cambio aquí
        Route::get('/{empleado}', [EmpleadoController::class, 'show'])->name('empleados.show');
        Route::post('/{empleado}', [EmpleadoController::class, 'update'])->name('empleados.update');
        Route::delete('/{empleado}', [EmpleadoController::class, 'destroy'])->name('empleados.destroy');
    });

    // Rutas para calificaciones
    Route::prefix('calificaciones')->group(function () {
        Route::get('/{empleado}', [CalificacionController::class, 'getCalificaciones'])->name('calificaciones.get');
        Route::post('/', [CalificacionController::class, 'store'])->name('calificaciones.store');
        Route::put('/{calificacion}', [CalificacionController::class, 'update'])->name('calificaciones.update');
    });
});

// Ruta de prueba (remover en producción)
Route::get('/prueba', [PruebaController::class, 'index']);
