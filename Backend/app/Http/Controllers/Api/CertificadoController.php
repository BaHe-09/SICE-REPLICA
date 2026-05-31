<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\DetalleKardex;
use App\Models\Materia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CertificadoController extends Controller
{
    /**
     * Generar certificado de estudios
     * GET /api/documentos/certificado/{numero_control}
     */
    public function generarCertificado($numero_control, Request $request)
    {
        $alumno = Alumno::with(['persona', 'carrera', 'kardex'])
            ->where('numero_control', $numero_control)
            ->firstOrFail();

        // Obtener historial académico completo
        $historial = DetalleKardex::with(['materia'])
            ->where('id_kardex', $alumno->kardex->id_kardex ?? 0)
            ->orderBy('fecha_registro')
            ->get();

        // Agrupar por semestre (esto puede requerir ajuste según tu estructura)
        $materiasPorSemestre = [];
        foreach ($historial as $detalle) {
            // Asumiendo que tienes una relación para obtener el semestre
            // Si no, puedes usar otro campo o dejar sin agrupar
            $semestre = $detalle->materia->semestre ?? 'General';
            if (!isset($materiasPorSemestre[$semestre])) {
                $materiasPorSemestre[$semestre] = [];
            }
            $materiasPorSemestre[$semestre][] = $detalle;
        }

        $totalMaterias = $historial->count();
        $materiasAprobadas = $historial->filter(function($item) {
            return ($item->calificacion_final ?? 0) >= 6;
        })->count();

        $porcentajeAvance = $totalMaterias > 0
            ? round(($materiasAprobadas / $totalMaterias) * 100, 2)
            : 0;

        $data = [
            'alumno' => $alumno,
            'historial' => $historial,
            'materias_por_semestre' => $materiasPorSemestre,
            'tipo_documento' => 'CERTIFICADO DE ESTUDIOS',
            'subtitulo' => 'DOCUMENTO OFICIAL QUE ACREDITA LOS ESTUDIOS REALIZADOS',
            'numero_certificado' => 'CERT-' . date('Y') . '-' . $alumno->numero_control,
            'fecha_emision' => now(),
            'folio' => 'CERT-' . $alumno->numero_control . '-' . date('YmdHis'),
            'numero_control' => $alumno->numero_control,
            'nombre_completo' => $alumno->nombre_completo,
            'carrera' => $alumno->nombre_carrera,
            'fecha_ingreso' => $alumno->fecha_ingreso ? $alumno->fecha_ingreso->format('d/m/Y') : 'N/A',
            'total_materias' => $totalMaterias,
            'materias_aprobadas' => $materiasAprobadas,
            'materias_reprobadas' => $totalMaterias - $materiasAprobadas,
            'promedio_general' => $alumno->kardex->promedio_general ?? 0,
            'creditos_acumulados' => $alumno->kardex->creditos_acumulados ?? 0,
            'porcentaje_avance' => $porcentajeAvance
        ];

        $pdf = Pdf::loadView('pdf.certificado', $data);
        $pdf->setPaper('letter', 'portrait');

        $pdf->setOptions([
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false
        ]);

        $filename = sprintf('CERTIFICADO_%s_%s.pdf', $alumno->numero_control, date('Ymd'));

        return $pdf->download($filename);
    }
}
