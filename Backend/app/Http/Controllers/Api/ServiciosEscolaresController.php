<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Evaluacion;
use App\Models\Calificacion;

class ServiciosEscolaresController extends Controller
{
    // 🔹 CALIFICACIONES


    public function getCalificacionesGrupo(Request $request)
{
    $grupoId = $request->query('grupo_id');

    $datos = DB::table('inscripcion as i')
        ->join('alumno as a', 'i.id_alumno', '=', 'a.id_alumno')
        ->join('persona as p', 'a.id_persona', '=', 'p.id_persona')
        ->leftJoin('calificacion as c', 'i.id_inscripcion', '=', 'c.id_inscripcion')
        ->leftJoin('evaluacion as e', 'c.id_evaluacion', '=', 'e.id_evaluacion')
        ->when($grupoId, fn($q) => $q->where('i.id_grupo', $grupoId))
        ->select(
            'i.id_inscripcion',          // <-- NECESARIO para guardar
            'a.numero_control',
            DB::raw("CONCAT(p.nombre, ' ', p.apellido_paterno, ' ', p.apellido_materno) as nombre"),
            'e.id_evaluacion',           // <-- NECESARIO para guardar
            'e.nombre as evaluacion',
            'c.calificacion'
        )
        ->get();

    $resultado = [];

    foreach ($datos as $d) {
        $key = $d->numero_control;

        if (!isset($resultado[$key])) {
            $resultado[$key] = [
                'id_inscripcion' => $d->id_inscripcion,  // <-- incluir
                'control'        => $d->numero_control,
                'nombre'         => $d->nombre,
                'p1'             => null,
                'p2'             => null,
                'proy'           => null,
                // IDs de evaluacion por parcial
                'id_evaluacion_parcial_1' => null,
                'id_evaluacion_parcial_2' => null,
                'id_evaluacion_proyecto'  => null,
            ];
        }

        if ($d->evaluacion === 'Parcial 1') {
            $resultado[$key]['p1'] = $d->calificacion;
            $resultado[$key]['id_evaluacion_parcial_1'] = $d->id_evaluacion;
        }
        if ($d->evaluacion === 'Parcial 2') {
            $resultado[$key]['p2'] = $d->calificacion;
            $resultado[$key]['id_evaluacion_parcial_2'] = $d->id_evaluacion;
        }
        if ($d->evaluacion === 'Proyecto') {
            $resultado[$key]['proy'] = $d->calificacion;
            $resultado[$key]['id_evaluacion_proyecto'] = $d->id_evaluacion;
        }
    }

    return response()->json(array_values($resultado));
}

    public function guardarCalificaciones(Request $request)
{
    $request->validate([
        'id_inscripcion' => 'required|integer',
        'id_evaluacion'  => 'required|integer',
        'calificacion'   => 'required|numeric|min:0|max:100',
        'fecha_registro' => 'nullable|date'
    ]);

    $calificacion = Calificacion::updateOrCreate(
        [
            'id_inscripcion' => $request->id_inscripcion,
            'id_evaluacion'  => $request->id_evaluacion,
        ],
        [
            'calificacion'   => $request->calificacion,
        ]
    );

    return response()->json([
        'mensaje'       => 'Calificación guardada correctamente',
        'calificacion'  => $calificacion
    ], 200);
}

    public function actualizarCalificacion(Request $request, $id)
    {
        $request->validate([
            'calificacion' => 'required|numeric|min:0|max:100'
        ]);

        $calificacion = Calificacion::findOrFail($id);
        $calificacion->update($request->all());

        return response()->json([
            'mensaje' => 'Calificación actualizada',
            'calificacion' => $calificacion
        ], 200);
    }

    public function eliminarCalificacion($id)
    {
        Calificacion::destroy($id);
        return response()->json(['mensaje' => 'Calificación eliminada'], 200);
    }

    // 🔹 ALUMNOS
    public function getAlumnos()
    {
        $alumnos = DB::table('alumno')->get();
        return response()->json($alumnos);
    }

    public function store(Request $request)
    {
        return response()->json(['mensaje' => 'Alumno registrado']);
    }

    public function buscarAlumnoInscripcion(Request $request)
    {
        return response()->json(['mensaje' => 'Alumno encontrado']);
    }

    // 🔹 GRUPOS / INSCRIPCIÓN
    public function getGruposDisponibles()
    {
        $grupos = DB::table('grupo')->get();
        return response()->json($grupos);
    }

    public function inscribirAlumno(Request $request)
    {
        return response()->json(['mensaje' => 'Alumno inscrito']);
    }

    // 🔹 EVALUACIONES
    public function getEvaluaciones($id_grupo)
    {
        $evaluaciones = DB::table('evaluacion')
            ->where('id_grupo', $id_grupo)
            ->get();

        return response()->json($evaluaciones);
    }

    public function guardarEvaluaciones(Request $request)
    {
        $request->validate([
            'id_grupo' => 'required|integer',
            'nombre' => 'required|string|max:50',
            'porcentaje' => 'required|numeric|min:0|max:100'
        ]);

        $evaluacion = Evaluacion::create($request->all());

        return response()->json([
            'mensaje' => 'Evaluación guardada correctamente',
            'evaluacion' => $evaluacion
        ], 201);
    }

    public function actualizarEvaluacion(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
            'porcentaje' => 'required|numeric|min:0|max:100'
        ]);

        $evaluacion = Evaluacion::findOrFail($id);
        $evaluacion->update($request->all());

        return response()->json([
            'mensaje' => 'Evaluación actualizada',
            'evaluacion' => $evaluacion
        ], 200);
    }

    public function eliminarEvaluacion($id)
    {
        Evaluacion::destroy($id);
        return response()->json(['mensaje' => 'Evaluación eliminada'], 200);
    }

    // 🔹 DASHBOARD
    public function getResumen()
    {
        return response()->json(['mensaje' => 'Resumen escolar']);
    }
}