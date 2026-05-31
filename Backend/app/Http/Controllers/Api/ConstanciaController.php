<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Periodo;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ConstanciaController extends Controller
{
    /**
     * Generar constancia de alumno
     * GET /api/documentos/constancia/{numero_control}
     */
    public function generarConstancia($numero_control, Request $request)
    {
        // Buscar alumno con sus relaciones
        $alumno = Alumno::with(['persona', 'carrera'])
            ->where('numero_control', $numero_control)
            ->firstOrFail();

        // Obtener parámetros
        $tipo = $request->get('tipo', 'estudios');
        $periodoClave = $request->get('periodo');

        // Obtener periodo si existe
        $periodo = null;
        if ($periodoClave) {
            $periodo = Periodo::where('id_periodo', $periodoClave)
                ->orWhere('nombre_periodo', $periodoClave)
                ->first();
        }

        // Obtener promedio si es necesario
        $promedio = null;
        if ($tipo === 'promedio' && $alumno->kardex) {
            $promedio = $alumno->kardex->promedio_general;
        }

        // Tipos de constancia con sus textos
        $tiposConstancia = [
            'estudios' => 'CONSTANCIA DE ESTUDIOS',
            'inscripcion' => 'CONSTANCIA DE INSCRIPCIÓN',
            'promedio' => 'CONSTANCIA DE PROMEDIO',
            'beca' => 'CONSTANCIA DE BECA',
            'visa' => 'CONSTANCIA PARA TRÁMITE DE VISA',
            'trabajo' => 'CONSTANCIA PARA TRÁMITE LABORAL',
            'imss' => 'CONSTANCIA PARA IMSS'
        ];

        $data = [
            'alumno' => $alumno,
            'tipo_constancia' => $tipo,
            'tipo_texto' => $tiposConstancia[$tipo] ?? 'CONSTANCIA ESCOLAR',
            'tipo_documento' => 'CONSTANCIA',
            'subtitulo' => $tiposConstancia[$tipo] ?? 'CONSTANCIA ESCOLAR',
            'periodo' => $periodo,
            'promedio' => $promedio,
            'fecha_emision' => now(),
            'folio' => 'CONST-' . $alumno->numero_control . '-' . date('YmdHis'),
            'numero_control' => $alumno->numero_control,
            'nombre_completo' => $alumno->nombre_completo,
            'carrera' => $alumno->nombre_carrera,
            'semestre' => $alumno->semestre_actual ?? 'N/A'
        ];

        // Generar PDF
        $pdf = Pdf::loadView('pdf.constancia', $data);
        $pdf->setPaper('letter', 'portrait');

        // Configurar opciones del PDF
        $pdf->setOptions([
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false
        ]);

        // Nombre del archivo
        $filename = sprintf(
            'CONSTANCIA_%s_%s_%s.pdf',
            strtoupper($tipo),
            $alumno->numero_control,
            $periodoClave ?? date('Y')
        );

        return $pdf->download($filename);
    }
}
