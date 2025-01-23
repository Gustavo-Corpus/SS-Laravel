<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\CalificacionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogoutController ;

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

   // Ruta de vista clientes

   Route::get('/admin/clientes', function () {
    return view('admin.clientes');
})->name('admin.clientes');


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
    Route::post('/submit-survey', [SurveyController::class, 'store'])->name('submit.survey');

    Route::put('/casos/update', [CasoController::class, 'update'])->name('casos.update');
    Route::post('/encuesta', [EncuestaController::class, 'store'])->name('encuesta.store');
    



});
