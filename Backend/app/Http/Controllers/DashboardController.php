<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        try {

            // KPIs básicos
            $alumnos = DB::table('alumno')->count();

            $inscripciones = DB::table('inscripcion')->count();

            $grupos = DB::table('grupo')->count();

            // Bajas 
            $bajasTemporales = DB::table('alumno')
                ->where('estatus', 'FALSE')
                ->count();

            $bajasDefinitivas = DB::table('alumno')
                ->where('estatus', 'FALSE')
                ->count();

            // Promedio general
            $promedio = DB::table('calificacion')
                ->avg('calificacion');

            // Evaluaciones pendientes
            $evaluaciones = DB::table('evaluacion')->count();

            return response()->json([
                'kpis' => [
                    'alumnos' => $alumnos,
                    'inscripciones' => $inscripciones,
                    'grupos' => $grupos,
                    'baja_temporal' => $bajasTemporales,
                    'baja_definitiva' => $bajasDefinitivas,
                    'promedio' => round($promedio, 2),
                    'evaluaciones' => $evaluaciones
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error en servidor',
                'mensaje' => $e->getMessage()
            ], 500);
        }
    }
}
