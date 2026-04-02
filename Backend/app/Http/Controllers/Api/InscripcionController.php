<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Grupo;
use App\Models\Inscripcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InscripcionController extends Controller
{
    public function buscarAlumno(Request $request)
    {
        try {
            $term = trim($request->query('q', ''));

            if ($term === '') {
                return response()->json([
                    'error' => 'Debes escribir un nombre o número de control'
                ], 422);
            }

            $alumno = Alumno::with(['persona', 'carrera'])
                ->where('numero_control', $term)
                ->orWhereHas('persona', function ($query) use ($term) {
                    $query->where('nombre', 'like', "%{$term}%")
                          ->orWhere('apellido_paterno', 'like', "%{$term}%")
                          ->orWhere('apellido_materno', 'like', "%{$term}%");
                })
                ->first();

            if (!$alumno) {
                return response()->json([
                    'error' => 'Alumno no encontrado'
                ], 404);
            }

            return response()->json([
                'id_alumno' => $alumno->id_alumno,
                'noControl' => $alumno->numero_control,
                'nombre' => trim(
                    ($alumno->persona->nombre ?? '') . ' ' .
                    ($alumno->persona->apellido_paterno ?? '') . ' ' .
                    ($alumno->persona->apellido_materno ?? '')
                ),
                'carrera' => $alumno->carrera->nombre ?? 'N/A',
                'semestre' => $alumno->semestre_actual
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al buscar alumno: ' . $e->getMessage()
            ], 500);
        }
    }

    public function gruposDisponibles()
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
            return response()->json([
                'error' => 'Error al cargar grupos: ' . $e->getMessage()
            ], 500);
        }
    }

    public function inscribirAlumno(Request $request)
    {
        try {
            $request->validate([
                'id_alumno' => 'required|integer|exists:alumno,id_alumno',
                'id_grupo'  => 'required|integer|exists:grupo,id_grupo',
            ]);

            $grupo = Grupo::where('id_grupo', $request->id_grupo)->first();

            if (!$grupo) {
                return response()->json([
                    'error' => 'Grupo no encontrado'
                ], 404);
            }

            $yaInscrito = Inscripcion::where('id_alumno', $request->id_alumno)
                ->where('id_grupo', $request->id_grupo)
                ->exists();

            if ($yaInscrito) {
                return response()->json([
                    'error' => 'El alumno ya está inscrito en este grupo'
                ], 409);
            }

            $totalInscritos = Inscripcion::where('id_grupo', $request->id_grupo)->count();

            if ($totalInscritos >= $grupo->capacidad) {
                return response()->json([
                    'error' => 'El grupo ya no tiene cupo disponible'
                ], 409);
            }

            $inscripcion = Inscripcion::create([
                'id_alumno' => $request->id_alumno,
                'id_grupo' => $request->id_grupo,
                'fecha_inscripcion' => now()->format('Y-m-d'),
                'estatus' => 'Activo'
            ]);

            return response()->json([
                'message' => 'Inscripción realizada correctamente',
                'id' => $inscripcion->id_inscripcion
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Datos inválidos',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al realizar la inscripción: ' . $e->getMessage()
            ], 500);
        }
    }
}