<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Inscripcion;
use App\Models\Persona;
use App\Models\Carrera;
use App\Models\Calificacion;
use App\Models\Grupo;
use App\Models\Evaluacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiciosEscolaresController extends Controller
{
    /**
     * DASHBOARD: Resumen estadístico
     */
    public function getResumen()
    {
        try {
            return response()->json([
                'total_alumnos'       => Alumno::count(),
                'total_inscripciones' => Inscripcion::count(),
                
                'recientes'           => Inscripcion::with(['alumno.persona', 'alumno.carrera'])
                    ->orderBy('id_inscripcion', 'desc')
                    ->take(5)
                    ->get()
                    ->map(function ($ins) {
                        return [
                            'id_inscripcion'    => $ins->id_inscripcion,
                            'noControl'         => $ins->alumno->numero_control ?? 'N/A',
                            'nombre'            => ($ins->alumno->persona->nombre ?? '') . ' ' . ($ins->alumno->persona->apellido_paterno ?? ''),
                            'carrera'           => $ins->alumno->carrera->nombre ?? 'N/A',
                            'semestre'          => $ins->alumno->semestre_actual ?? 0,
                            'fecha'             => $ins->fecha_inscripcion,
                            'estatus'           => $ins->estatus
                        ];
                    })
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * ALUMNOS: Lista completa y creación
     */
    public function getAlumnos()
    {
        try {
            $alumnos = Alumno::with(['persona', 'carrera'])
                ->get()
                ->map(function ($alumno) {
                    return [
                        'id'        => $alumno->id_alumno,
                        'noControl' => $alumno->numero_control,
                        'nombre'    => trim(($alumno->persona->nombre ?? 'Sin nombre') . ' ' . ($alumno->persona->apellido_paterno ?? '')),
                        'carrera'   => $alumno->carrera->nombre ?? 'Sin carrera asignada',
                        'semestre'  => $alumno->semestre_actual,
                        'estatus'   => $alumno->estatus == 1 ? 'Activo' : ($alumno->estatus == 2 ? 'Baja Temporal' : 'Baja Definitiva'),
                    ];
                });
            return response()->json($alumnos);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function inscribirAlumno(Request $request)
    {
        try {
            $request->validate([
                'id_alumno' => 'required|integer|exists:alumno,id_alumno',
                'id_grupo'  => 'required|integer|exists:grupo,id_grupo',
            ]);

            $grupo = Grupo::find($request->id_grupo);

            if (!$grupo) {
                return response()->json(['error' => 'Grupo no encontrado'], 404);
            }

            $yaInscrito = Inscripcion::where('id_alumno', $request->id_alumno)
                ->where('id_grupo', $request->id_grupo)
                ->exists();

            if ($yaInscrito) {
                return response()->json(['error' => 'El alumno ya está inscrito en este grupo'], 409);
            }

            $totalInscritos = Inscripcion::where('id_grupo', $request->id_grupo)->count();

            if ($totalInscritos >= $grupo->capacidad) {
                return response()->json(['error' => 'El grupo ya no tiene cupo disponible'], 409);
            }

            $inscripcion = Inscripcion::create([
                'id_alumno'         => $request->id_alumno,
                'id_grupo'          => $request->id_grupo,
                'fecha_inscripcion' => now(),
                'estatus'           => 'Activo'
            ]);

            return response()->json([
                'message' => 'Inscripción realizada correctamente',
                'id'      => $inscripcion->id_inscripcion
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Datos inválidos',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $persona = Persona::create([
                    'nombre'           => $request->nombre,
                    'apellido_paterno' => $request->apellidoPaterno,
                    'apellido_materno' => $request->apellidoMaterno,
                    'curp'             => 'POR DEFINIR',
                    'fecha_nacimiento' => '2000-01-01',
                    'id_genero'        => $request->genero === 'Masculino' ? 1 : 2,
                ]);

                $carrera = Carrera::where('nombre', $request->carrera)->first();

                $alumno = Alumno::create([
                    'id_persona'      => $persona->id_persona,
                    'numero_control'  => $request->noControl,
                    'id_carrera'      => $carrera ? $carrera->id_carrera : null,
                    'fecha_ingreso'   => $request->fechaIngreso,
                    'semestre_actual' => $request->semestre,
                    'estatus'         => $request->estatus === 'Activo' ? 1 : ($request->estatus === 'Baja Temporal' ? 2 : 0),
                ]);

                return response()->json(['message' => 'Alumno guardado correctamente', 'id' => $alumno->id_alumno], 201);
            });
        } catch (\Exception $e) {
            return response()->json(['error' => 'No se pudo guardar: ' . $e->getMessage()], 500);
        }
    }

    /**
     * INSCRIPCIÓN: Búsqueda y registro
     */
    public function buscarAlumnoInscripcion(Request $request)
    {
        $term = $request->query('q');
        $alumno = Alumno::with(['persona', 'carrera'])
            ->where('numero_control', $term)
            ->orWhereHas('persona', function($query) use ($term) {
                $query->where('nombre', 'like', "%$term%");
            })->first();

        if (!$alumno) return response()->json(['error' => 'Alumno no encontrado'], 404);

        return response()->json([
            'id_alumno' => $alumno->id_alumno,
            'noControl' => $alumno->numero_control,
            'nombre'    => $alumno->persona->nombre . ' ' . $alumno->persona->apellido_paterno,
            'carrera'   => $alumno->carrera->nombre ?? 'N/A',
            'semestre'  => $alumno->semestre_actual
        ]);
    }

    public function getGruposDisponibles()
    {
        try {
            $grupos = DB::table('grupo as g')
                ->join('materia as m', 'g.id_materia', '=', 'm.id_materia')
                ->join('docente as d', 'g.id_docente', '=', 'd.id_docente')
                ->join('empleado as e', 'd.id_empleado', '=', 'e.id_empleado')
                ->join('persona as p', 'e.id_persona', '=', 'p.id_persona')
                ->join('aula as a', 'g.id_aula', '=', 'a.id_aula')
                ->leftJoin('inscripcion as i', 'g.id_grupo', '=', 'i.id_grupo')
                ->select(
                    'g.id_grupo as id',
                    'm.nombre as materia',
                    DB::raw("TRIM(CONCAT(
                        COALESCE(p.nombre, ''), ' ',
                        COALESCE(p.apellido_paterno, ''), ' ',
                        COALESCE(p.apellido_materno, '')
                    )) as docente"),
                    'a.nombre as aula',
                    'g.capacidad',
                    DB::raw('COUNT(i.id_inscripcion) as inscritos')
                )
                ->groupBy(
                    'g.id_grupo',
                    'm.nombre',
                    'p.nombre',
                    'p.apellido_paterno',
                    'p.apellido_materno',
                    'a.nombre',
                    'g.capacidad'
                )
                ->orderBy('m.nombre')
                ->get();

            return response()->json($grupos);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * EVALUACIONES: Criterios de evaluación
     */
    public function getEvaluaciones($id_grupo)
    {
        try {
            $evaluaciones = Evaluacion::where('id_grupo', $id_grupo)->get();
            return response()->json($evaluaciones);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function guardarEvaluaciones(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                Evaluacion::where('id_grupo', $request->id_grupo)->delete();
                foreach ($request->criterios as $criterio) {
                    Evaluacion::create([
                        'id_grupo'   => $request->id_grupo,
                        'nombre'     => $criterio['nombre'],
                        'porcentaje' => $criterio['porcentaje']
                    ]);
                }
            });
            return response()->json(['message' => 'Configuración de evaluación guardada']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * CALIFICACIONES: Captura de notas
     */
    public function getCalificacionesGrupo() 
    {
        try {
            $datos = Inscripcion::with(['alumno.persona'])
                ->get()
                ->map(function($ins) {
                    return [
                        'id_inscripcion' => $ins->id_inscripcion,
                        'control'        => $ins->alumno->numero_control ?? 'N/A',
                        'nombre'         => ($ins->alumno->persona->nombre ?? '') . ' ' . ($ins->alumno->persona->apellido_paterno ?? ''),
                        'p1'             => $ins->parcial_1 ?? 0, 
                        'p2'             => $ins->parcial_2 ?? 0,
                        'proy'           => $ins->proyecto ?? 0
                    ];
                });
            return response()->json($datos);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function guardarCalificaciones(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                foreach ($request->alumnos as $alumnoData) {
                    $final = (floatval($alumnoData['p1']) * 0.3) + 
                             (floatval($alumnoData['p2']) * 0.3) + 
                             (floatval($alumnoData['proy']) * 0.4);

                    // Buscar la evaluacion final del grupo
                    $evaluacion = \App\Models\Evaluacion::where('id_grupo', $alumnoData['id_grupo'] ?? null)
                        ->where('nombre', 'Final')
                        ->first();
                    $id_evaluacion = $evaluacion ? $evaluacion->id_evaluacion : 1;

                    \App\Models\Calificacion::updateOrCreate(
                        [
                            'id_inscripcion' => $alumnoData['id_inscripcion'],
                            'id_evaluacion' => $id_evaluacion
                        ],
                        [
                            'calificacion'  => $final
                        ]
                    );
                }
            });
            return response()->json(['message' => 'Calificaciones guardadas exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al guardar notas: ' . $e->getMessage()], 500);
        }
    }
}