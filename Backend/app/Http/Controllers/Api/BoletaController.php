<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Periodo;
use App\Models\Inscripcion;
use App\Models\Grupo;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use ZipArchive;

class BoletaController extends Controller
{
    /**
     * Generar boleta de calificaciones individual
     * GET /api/documentos/boleta/{numero_control}
     */
    public function generarBoleta($numero_control, Request $request)
    {
        $alumno = Alumno::with(['persona', 'carrera', 'kardex'])
            ->where('numero_control', $numero_control)
            ->firstOrFail();

        $periodoId = $request->get('periodo');

        // Obtener inscripciones del alumno en el periodo
        $inscripciones = Inscripcion::with(['grupo.materia', 'calificaciones'])
            ->where('id_alumno', $alumno->id_alumno)
            ->when($periodoId, function($query) use ($periodoId) {
                return $query->whereHas('grupo', function($q) use ($periodoId) {
                    $q->where('id_periodo', $periodoId);
                });
            })
            ->get();

        $periodo = null;
        if ($periodoId) {
            $periodo = Periodo::find($periodoId);
        }

        // Calcular calificaciones por materia
        $materias = [];
        foreach ($inscripciones as $inscripcion) {
            $materia = $inscripcion->grupo->materia;
            $calificacionFinal = $inscripcion->getCalificacionFinalAttribute();

            $materias[] = (object)[
                'clave' => $materia->clave ?? 'N/A',
                'nombre' => $materia->nombre ?? 'N/A',
                'creditos' => $materia->creditos ?? 0,
                'calificacion' => $calificacionFinal,
                'estado' => $calificacionFinal >= 6 ? 'APROBADA' : 'REPROBADA'
            ];
        }

        // Calcular promedio del periodo
        $promedioPeriodo = count($materias) > 0
            ? collect($materias)->avg('calificacion')
            : 0;

        $data = [
            'alumno' => $alumno,
            'periodo' => $periodo,
            'materias' => $materias,
            'tipo_documento' => 'BOLETA DE CALIFICACIONES',
            'subtitulo' => 'DOCUMENTO OFICIAL DE CALIFICACIONES',
            'fecha_emision' => now(),
            'folio' => 'BOL-' . $alumno->numero_control . '-' . date('YmdHis'),
            'numero_control' => $alumno->numero_control,
            'nombre_completo' => $alumno->nombre_completo,
            'carrera' => $alumno->nombre_carrera,
            'semestre' => $alumno->semestre_actual ?? 'N/A',
            'promedio_periodo' => round($promedioPeriodo, 2),
            'promedio_general' => $alumno->kardex->promedio_general ?? 0,
            'creditos_obtenidos' => $alumno->kardex->creditos_acumulados ?? 0,
            'materias_cursadas' => count($materias),
            'materias_aprobadas' => collect($materias)->where('estado', 'APROBADA')->count(),
            'materias_reprobadas' => collect($materias)->where('estado', 'REPROBADA')->count()
        ];

        $pdf = Pdf::loadView('pdf.boleta', $data);
        $pdf->setPaper('letter', 'portrait');

        $pdf->setOptions([
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false
        ]);

        $filename = sprintf('BOLETA_%s_%s.pdf', $alumno->numero_control, $periodoId ?? date('Y'));

        return $pdf->download($filename);
    }

    /**
     * Generar boletas masivas en ZIP
     * GET /api/documentos/boleta-masiva
     */
    public function generarBoletasMasivas(Request $request)
    {
        $carreraNombre = $request->get('carrera');
        $semestre = $request->get('semestre');
        $periodoId = $request->get('periodo');

        // Buscar alumnos por carrera y semestre
        $query = Alumno::with(['persona', 'carrera', 'kardex']);

        if ($carreraNombre) {
            $query->whereHas('carrera', function($q) use ($carreraNombre) {
                $q->where('nombre', 'LIKE', '%' . $carreraNombre . '%');
            });
        }

        if ($semestre) {
            $query->where('semestre_actual', $semestre);
        }

        $alumnos = $query->get();

        if ($alumnos->isEmpty()) {
            return response()->json(['error' => 'No se encontraron alumnos con los criterios especificados'], 404);
        }

        $zip = new ZipArchive();
        $zipName = tempnam(sys_get_temp_dir(), 'boletas_') . '.zip';

        if ($zip->open($zipName, ZipArchive::CREATE) === TRUE) {
            foreach ($alumnos as $alumno) {
                // Obtener inscripciones del alumno
                $inscripciones = Inscripcion::with(['grupo.materia', 'calificaciones'])
                    ->where('id_alumno', $alumno->id_alumno)
                    ->when($periodoId, function($query) use ($periodoId) {
                        return $query->whereHas('grupo', function($q) use ($periodoId) {
                            $q->where('id_periodo', $periodoId);
                        });
                    })
                    ->get();

                $periodo = $periodoId ? Periodo::find($periodoId) : null;

                // Calcular materias
                $materias = [];
                foreach ($inscripciones as $inscripcion) {
                    $materia = $inscripcion->grupo->materia;
                    $calificacionFinal = $inscripcion->getCalificacionFinalAttribute();

                    $materias[] = (object)[
                        'clave' => $materia->clave ?? 'N/A',
                        'nombre' => $materia->nombre ?? 'N/A',
                        'creditos' => $materia->creditos ?? 0,
                        'calificacion' => $calificacionFinal,
                        'estado' => $calificacionFinal >= 6 ? 'APROBADA' : 'REPROBADA'
                    ];
                }

                $promedioPeriodo = count($materias) > 0
                    ? collect($materias)->avg('calificacion')
                    : 0;

                $data = [
                    'alumno' => $alumno,
                    'periodo' => $periodo,
                    'materias' => $materias,
                    'tipo_documento' => 'BOLETA DE CALIFICACIONES',
                    'subtitulo' => 'DOCUMENTO OFICIAL DE CALIFICACIONES',
                    'fecha_emision' => now(),
                    'numero_control' => $alumno->numero_control,
                    'nombre_completo' => $alumno->nombre_completo,
                    'carrera' => $alumno->nombre_carrera,
                    'semestre' => $alumno->semestre_actual ?? 'N/A',
                    'promedio_periodo' => round($promedioPeriodo, 2),
                    'promedio_general' => $alumno->kardex->promedio_general ?? 0
                ];

                $pdf = Pdf::loadView('pdf.boleta', $data);
                $pdfContent = $pdf->output();

                $filename = sprintf('BOLETA_%s_%s.pdf', $alumno->numero_control, $periodoId ?? date('Y'));
                $zip->addFromString($filename, $pdfContent);
            }

            $zip->close();

            return response()->download($zipName, 'BOLETAS_MASIVAS_' . ($periodoId ?? date('Y')) . '.zip')
                ->deleteFileAfterSend(true);
        }

        return response()->json(['error' => 'No se pudo crear el archivo ZIP'], 500);
    }
}
