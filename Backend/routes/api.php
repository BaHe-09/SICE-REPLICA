<?php

use Illuminate\Support\Facades\DB;

// controllers
use App\Http\Controllers\DashboardController;

Route::get('/dashboard', [DashboardController::class, 'index']);

// CALIFICACIONES
Route::get('/calificaciones-grupo', [ServiciosEscolaresController::class, 'getCalificacionesGrupo']);
Route::post('/guardar-calificaciones', [ServiciosEscolaresController::class, 'guardarCalificaciones']);

// ALUMNOS
Route::get('/alumnos-full', [ServiciosEscolaresController::class, 'getAlumnos']);
Route::post('/alumnos', [ServiciosEscolaresController::class, 'store']);
Route::get('/buscar-alumno', [ServiciosEscolaresController::class, 'buscarAlumnoInscripcion']);

// Obtener lista
Route::get('/alumnos-crud', [AlumnoController::class, 'index']);

// Actualizar alumno
Route::put('/alumnos/{id}', [AlumnoController::class, 'update']);

// Eliminar alumno
Route::delete('/alumnos/{id}', [AlumnoController::class, 'destroy']);

// GRUPOS / INSCRIPCIÓN
Route::get('/grupos-disponibles', [ServiciosEscolaresController::class, 'getGruposDisponibles']);
Route::post('/inscribir', [ServiciosEscolaresController::class, 'inscribirAlumno']);

// EVALUACIONES
Route::get('/evaluaciones/{id_grupo}', [ServiciosEscolaresController::class, 'getEvaluaciones']);
Route::post('/evaluaciones/guardar', [ServiciosEscolaresController::class, 'guardarEvaluaciones']);

// 🔹 DASHBOARD
Route::get('/resumen-escolar', [ServiciosEscolaresController::class, 'getResumen']);

// FILTROS DINÁMICOS
Route::get('/filtros', function () {
    return response()->json([
        'periodos' => DB::table('periodo')->get(),
        'carreras' => DB::table('carrera')->get(),
        'materias' => DB::table('materia')->get(),
        'grupos' => DB::table('grupo')->get(),
    ]);
});
