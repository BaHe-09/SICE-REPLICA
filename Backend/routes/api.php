<?php

use Illuminate\Support\Facades\DB;

// controllers
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Api\ServiciosEscolaresController;   // ✅ CORREGIDO
use App\Http\Controllers\Api\GrupoController;
use App\Http\Controllers\Api\AlumnoController;
use App\Http\Controllers\Api\EvaluacionController;           // ✅ IMPORTADO

// DASHBOARD
Route::get('/dashboard', [DashboardController::class, 'index']);

// 🔹 CALIFICACIONES
Route::get('/calificaciones-grupo', [ServiciosEscolaresController::class, 'getCalificacionesGrupo']);
Route::post('/guardar-calificaciones', [ServiciosEscolaresController::class, 'guardarCalificaciones']);
Route::put('/calificaciones/{id}', [ServiciosEscolaresController::class, 'actualizarCalificacion']);
Route::delete('/calificaciones/{id}', [ServiciosEscolaresController::class, 'eliminarCalificacion']);

// 🔹 ALUMNOS
Route::get('/alumnos-full', [ServiciosEscolaresController::class, 'getAlumnos']);
Route::post('/alumnos', [ServiciosEscolaresController::class, 'store']);
Route::get('/buscar-alumno', [ServiciosEscolaresController::class, 'buscarAlumnoInscripcion']);

// CRUD de alumnos
Route::get('/alumnos-crud', [AlumnoController::class, 'index']);
Route::put('/alumnos/{id}', [AlumnoController::class, 'update']);
Route::delete('/alumnos/{id}', [AlumnoController::class, 'destroy']);

// 🔹 GRUPOS / INSCRIPCIÓN
Route::get('/grupos-disponibles', [ServiciosEscolaresController::class, 'getGruposDisponibles']);
Route::post('/inscribir', [ServiciosEscolaresController::class, 'inscribirAlumno']);
Route::get('/grupos', [GrupoController::class, 'index']);
Route::post('/grupos', [GrupoController::class, 'store']);
Route::put('/grupos/{id}', [GrupoController::class, 'update']);
Route::delete('/grupos/{id}', [GrupoController::class, 'destroy']);

// 🔹 EVALUACIONES
Route::get('/evaluaciones/{id_grupo}', [ServiciosEscolaresController::class, 'getEvaluaciones']);
Route::post('/evaluaciones/guardar', [ServiciosEscolaresController::class, 'guardarEvaluaciones']);
Route::put('/evaluaciones/{id}', [ServiciosEscolaresController::class, 'actualizarEvaluacion']);
Route::delete('/evaluaciones/{id}', [ServiciosEscolaresController::class, 'eliminarEvaluacion']);

// CRUD de evaluaciones con EvaluacionController
Route::get('/evaluaciones', [EvaluacionController::class, 'index']);
Route::post('/evaluaciones', [EvaluacionController::class, 'store']);
Route::get('/evaluaciones/{id}', [EvaluacionController::class, 'show']);
Route::put('/evaluaciones/{id}', [EvaluacionController::class, 'update']);
Route::delete('/evaluaciones/{id}', [EvaluacionController::class, 'destroy']);

// 🔹 RESUMEN ESCOLAR
Route::get('/resumen-escolar', [ServiciosEscolaresController::class, 'getResumen']);

// 🔹 FILTROS DINÁMICOS
Route::get('/filtros', function () {
    return response()->json([
        'periodos' => DB::table('periodo')->get(),
        'carreras' => DB::table('carrera')->get(),
        'materias' => DB::table('materia')->get(),
        'grupos' => DB::table('grupo')->get(),
    ]);
});