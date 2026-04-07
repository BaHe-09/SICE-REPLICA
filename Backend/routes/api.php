<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// controllers
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Api\ServiciosEscolaresController;
use App\Http\Controllers\Api\GrupoController;
use App\Http\Controllers\Api\AlumnoController;
use App\Http\Controllers\Api\EvaluacionController;
use App\Http\Controllers\CalificacionController;

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

// === CRUD COMPLETO DE ALUMNOS ===
Route::apiResource('alumnos', AlumnoController::class);
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
Route::post('/evaluaciones/guardar', [ServiciosEscolaresController::class, 'guardarEvaluaciones']);
Route::get('/evaluaciones/{id_grupo}', [ServiciosEscolaresController::class, 'getEvaluaciones']);
Route::put('/evaluaciones/{id}', [ServiciosEscolaresController::class, 'actualizarEvaluacion']);
Route::delete('/evaluaciones/{id}', [ServiciosEscolaresController::class, 'eliminarEvaluacion']);

// 🔹 RESUMEN ESCOLAR
Route::get('/resumen-escolar', [ServiciosEscolaresController::class, 'getResumen']);

// 🔹 FILTROS DINÁMICOS
Route::get('/filtros', function () {
    return response()->json([
        'periodos' => DB::table('periodo')->get(),
        'carreras' => DB::table('carrera')->get(),
        'materias' => DB::table('materia')->get(),
        'grupos'   => DB::table('grupo')->get(),
    ]);
});