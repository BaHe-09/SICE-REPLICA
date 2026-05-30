<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * DocumentoController
 *
 * Genera documentos oficiales en PDF usando dompdf.
 *
 * Instalación (una sola vez):
 *   composer require barryvdh/laravel-dompdf
 *
 * Rutas en api.php:
 *   Route::get('/documentos/constancia/{numero_control}',  [DocumentoController::class, 'constancia']);
 *   Route::get('/documentos/boleta/{numero_control}',      [DocumentoController::class, 'boleta']);
 *   Route::get('/documentos/certificado/{numero_control}', [DocumentoController::class, 'certificado']);
 *
 * Todos devuelven el PDF inline (para preview en browser) o como descarga.
 * Parámetro ?tipo=estudios|inscripcion|promedio en constancia.
 * Parámetro ?download=1 en cualquiera para forzar descarga.
 */
class DocumentoController extends Controller
{
    // ─── Datos base del alumno ─────────────────────────────────────────────

    private function datosAlumno(string $numero_control): ?object
    {
        return DB::table('alumno as a')
            ->join('persona as p',   'a.id_persona',  '=', 'p.id_persona')
            ->join('carrera as c',   'a.id_carrera',  '=', 'c.id_carrera')
            ->leftJoin('plan_estudio as pe', function ($j) {
                $j->on('pe.id_carrera', '=', 'a.id_carrera')
                  ->whereRaw('pe.id_plan = (SELECT MAX(id_plan) FROM plan_estudio WHERE id_carrera = a.id_carrera)');
            })
            ->leftJoin('departamento as d', 'c.id_departamento', '=', 'd.id_departamento')
            ->where('a.numero_control', $numero_control)
            ->select(
                'a.id_alumno',
                'a.numero_control',
                'a.semestre_actual',
                'a.fecha_ingreso',
                'a.estatus',
                DB::raw("CONCAT(p.nombre,' ',p.apellido_paterno,' ',COALESCE(p.apellido_materno,'')) as nombre_completo"),
                'p.curp',
                'c.nombre as carrera',
                'd.nombre as departamento',
                'pe.nombre_plan',
                'pe.total_creditos'
            )
            ->first();
    }

    private function periodoActivo(): ?object
    {
        return DB::table('periodo')
            ->where('estatus', 1)
            ->orderByDesc('id_periodo')
            ->first();
    }

    private function promedioAlumno(int $id_alumno): float
    {
        $promedio = DB::table('calificacion as c')
            ->join('evaluacion as e',  'c.id_evaluacion',  '=', 'e.id_evaluacion')
            ->join('inscripcion as i', 'c.id_inscripcion', '=', 'i.id_inscripcion')
            ->where('i.id_alumno', $id_alumno)
            ->avg(DB::raw('c.calificacion * e.porcentaje / 100'));

        return round((float) ($promedio ?? 0), 2);
    }

    // ─── Responder PDF ──────────────────────────────────────────────────────

    private function responderPDF($pdf, string $filename, Request $request)
    {
        if ($request->boolean('download')) {
            return $pdf->download($filename);
        }
        return $pdf->stream($filename);
    }

    // ─── 1. CONSTANCIA ─────────────────────────────────────────────────────

    /**
     * GET /api/documentos/constancia/{numero_control}?tipo=estudios|inscripcion|promedio
     */
    public function constancia(Request $request, string $numero_control)
    {
        $alumno = $this->datosAlumno($numero_control);

        if (!$alumno) {
            return response()->json(['error' => 'Alumno no encontrado.'], 404);
        }

        $tipo    = $request->query('tipo', 'estudios');
        $periodo = $this->periodoActivo();
        $promedio = $tipo === 'promedio' ? $this->promedioAlumno($alumno->id_alumno) : null;

        $titulos = [
            'estudios'    => 'CONSTANCIA DE ESTUDIOS',
            'inscripcion' => 'CONSTANCIA DE INSCRIPCIÓN',
            'promedio'    => 'CONSTANCIA DE PROMEDIO',
        ];

        $titulo = $titulos[$tipo] ?? 'CONSTANCIA DE ESTUDIOS';

        $pdf = Pdf::loadView('documentos.constancia', compact(
            'alumno', 'periodo', 'promedio', 'titulo', 'tipo'
        ))->setPaper('letter', 'portrait');

        return $this->responderPDF($pdf, "constancia_{$tipo}_{$numero_control}.pdf", $request);
    }

    // ─── 2. BOLETA ─────────────────────────────────────────────────────────

    /**
     * GET /api/documentos/boleta/{numero_control}?periodo_id=1
     * Si no se pasa periodo_id, usa el periodo activo.
     */
    public function boleta(Request $request, string $numero_control)
    {
        $alumno = $this->datosAlumno($numero_control);

        if (!$alumno) {
            return response()->json(['error' => 'Alumno no encontrado.'], 404);
        }

        $periodoId = $request->query('periodo_id');
        $periodo   = $periodoId
            ? DB::table('periodo')->where('id_periodo', $periodoId)->first()
            : $this->periodoActivo();

        if (!$periodo) {
            return response()->json(['error' => 'Periodo no encontrado.'], 404);
        }

        // Materias inscritas en el periodo con calificación ponderada
        $materias = DB::table('inscripcion as i')
            ->join('grupo as g',   'i.id_grupo',   '=', 'g.id_grupo')
            ->join('materia as m', 'g.id_materia', '=', 'm.id_materia')
            ->leftJoin(
                DB::raw('(
                    SELECT c.id_inscripcion,
                           SUM(c.calificacion * e.porcentaje / 100) as calificacion_final
                    FROM calificacion c
                    INNER JOIN evaluacion e ON c.id_evaluacion = e.id_evaluacion
                    GROUP BY c.id_inscripcion
                ) as cp'),
                'cp.id_inscripcion', '=', 'i.id_inscripcion'
            )
            ->where('i.id_alumno',  $alumno->id_alumno)
            ->where('g.id_periodo', $periodo->id_periodo)
            ->select(
                'm.clave',
                'm.nombre as materia',
                'm.creditos',
                DB::raw('ROUND(COALESCE(cp.calificacion_final, 0), 1) as calificacion'),
                DB::raw("CASE WHEN COALESCE(cp.calificacion_final, 0) >= 70 THEN 'A' ELSE 'R' END as resultado")
            )
            ->orderBy('m.nombre')
            ->get();

        $promedioPeriodo = $materias->avg('calificacion');
        $promedioPeriodo = $promedioPeriodo ? round($promedioPeriodo, 2) : 0;

        $pdf = Pdf::loadView('documentos.boleta', compact(
            'alumno', 'periodo', 'materias', 'promedioPeriodo'
        ))->setPaper('letter', 'portrait');

        return $this->responderPDF($pdf, "boleta_{$numero_control}_{$periodo->nombre_periodo}.pdf", $request);
    }

    // ─── 3. CERTIFICADO ────────────────────────────────────────────────────

    /**
     * GET /api/documentos/certificado/{numero_control}
     * Genera el certificado de estudios completo con todas las materias del plan.
     */
    public function certificado(Request $request, string $numero_control)
    {
        $alumno = $this->datosAlumno($numero_control);

        if (!$alumno) {
            return response()->json(['error' => 'Alumno no encontrado.'], 404);
        }

        // Todas las materias del plan con calificación
        $materias = DB::table('plan_materia as pm')
            ->join('plan_estudio as pe', 'pm.id_plan',    '=', 'pe.id_plan')
            ->join('materia as m',       'pm.id_materia', '=', 'm.id_materia')
            ->leftJoin(
                DB::raw('(
                    SELECT g.id_materia,
                           MAX(c.calificacion) as calificacion_final
                    FROM inscripcion i
                    JOIN grupo g       ON i.id_grupo       = g.id_grupo
                    JOIN calificacion c ON i.id_inscripcion = c.id_inscripcion
                    WHERE i.id_alumno = ' . (int) $alumno->id_alumno . '
                    GROUP BY g.id_materia
                ) as cal'),
                'cal.id_materia', '=', 'm.id_materia'
            )
            ->where('pe.id_carrera', DB::table('alumno')->where('id_alumno', $alumno->id_alumno)->value('id_carrera'))
            ->select(
                'pm.semestre',
                'm.clave',
                'm.nombre as materia',
                'm.creditos',
                'm.horas_teoria',
                'm.horas_practica',
                DB::raw('COALESCE(cal.calificacion_final, NULL) as calificacion'),
                DB::raw("CASE
                    WHEN cal.calificacion_final IS NULL THEN 'Pendiente'
                    WHEN cal.calificacion_final >= 70   THEN 'Acreditada'
                    ELSE 'Reprobada'
                END as estado")
            )
            ->orderBy('pm.semestre')
            ->orderBy('m.nombre')
            ->get();

        $creditosAcumulados = $materias
            ->where('estado', 'Acreditada')
            ->sum('creditos');

        $promedioGeneral = $materias
            ->whereNotNull('calificacion')
            ->avg('calificacion');
        $promedioGeneral = $promedioGeneral ? round($promedioGeneral, 2) : 0;

        $materiasPorSemestre = $materias->groupBy('semestre');

        $pdf = Pdf::loadView('documentos.certificado', compact(
            'alumno', 'materiasPorSemestre', 'creditosAcumulados', 'promedioGeneral'
        ))->setPaper('letter', 'portrait');

        return $this->responderPDF($pdf, "certificado_{$numero_control}.pdf", $request);
    }
}