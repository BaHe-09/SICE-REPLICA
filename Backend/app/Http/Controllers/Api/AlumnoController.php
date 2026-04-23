<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AlumnoController extends Controller
{
    public function index()
    {
        try {
            return Alumno::with(['persona', 'carrera'])->get();
        } catch (\Exception $e) {
            Log::error("Error index alumnos: " . $e->getMessage());
            return response()->json(['error' => 'Error al cargar alumnos'], 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'numero_control'   => 'required|string|unique:alumno,numero_control',
                'nombre'           => 'required|string',
                'apellido_paterno' => 'required|string',
                'genero'           => 'required|in:Masculino,Femenino,Otro',
                'id_carrera'       => 'required|integer|exists:carrera,id_carrera',
                'semestre_actual'  => 'required|integer|between:1,8',
                'estatus'          => 'required',
                'fecha_ingreso'    => 'required|date',
            ]);

            $idGenero = match ($request->genero) {
                'Masculino' => 1,
                'Femenino'  => 2,
                default     => 3,
            };

            // Crear Persona
            $persona = Persona::create([
                'nombre'           => $request->nombre,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno ?? null,
                'id_genero'        => $idGenero,
                'curp'             => $request->curp ?? null,
                'fecha_nacimiento' => $request->fecha_nacimiento ?? null,
            ]);

            // Crear Alumno
            $alumno = Alumno::create([
                'numero_control'   => $request->numero_control,
                'id_persona'       => $persona->id_persona,
                'id_carrera'       => $request->id_carrera,
                'semestre_actual'  => $request->semestre_actual,
                'estatus'          => $request->estatus,           // ya viene como 1/0 desde el form
                'fecha_ingreso'    => $request->fecha_ingreso,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Alumno registrado correctamente',
                'data'    => $alumno->load(['persona', 'carrera'])
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json(['error' => 'Datos inválidos', 'detalle' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al crear alumno: " . $e->getMessage());
            return response()->json([
                'error'   => 'Error interno del servidor',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            Log::info("Actualizando alumno ID: {$id}", $request->all());

            $alumno = Alumno::findOrFail($id);

            if ($alumno->persona) {
                $nombreCompleto = explode(' ', trim($request->nombre));
                $nombre           = $nombreCompleto[0] ?? '';
                $apellido_paterno = $nombreCompleto[1] ?? '';
                $apellido_materno = implode(' ', array_slice($nombreCompleto, 2));

                $alumno->persona->update([
                    'nombre'           => $nombre,
                    'apellido_paterno' => $apellido_paterno,
                    'apellido_materno' => $apellido_materno,
                ]);
            }

            // 👇 Convierte el texto a BOOLEAN (1/0) que espera la BD
            $estatusBoolean = match($request->estatus) {
                'Activo'          => 1,
                'Baja Temporal'   => 0,
                'Baja Definitiva' => 0,
                default           => $alumno->estatus
            };

            $alumno->update([
                'id_carrera'      => $request->id_carrera      ?? $alumno->id_carrera,
                'semestre_actual' => $request->semestre_actual  ?? $alumno->semestre_actual,
                'estatus'         => $estatusBoolean,
            ]);

            DB::commit();
            return response()->json([
                'message' => 'Alumno actualizado con éxito',
                'data'    => $alumno->load(['persona', 'carrera'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al actualizar alumno ID {$id}: " . $e->getMessage());
            return response()->json([
                'error'   => 'No se pudo actualizar el alumno',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $alumno = Alumno::with('persona')->findOrFail($id);
            $idPersona = $alumno->id_persona;

            // ==================== 1. LIMPIEZA DE ALUMNO ====================
            DB::table('calificacion')
                ->whereIn('id_inscripcion', function ($query) use ($id) {
                    $query->select('id_inscripcion')
                        ->from('inscripcion')
                        ->where('id_alumno', $id);
                })
                ->delete();

            DB::table('inscripcion')->where('id_alumno', $id)->delete();
            DB::table('participacion_evento')->where('id_alumno', $id)->delete();
            DB::table('alumno')->where('id_alumno', $id)->delete();

          
            DB::table('persona_correo')->where('id_persona', $idPersona)->delete();
            DB::table('persona_telefono')->where('id_persona', $idPersona)->delete();
            DB::table('persona_direccion')->where('id_persona', $idPersona)->delete();

          
            DB::table('resolucion_comite')
                ->whereIn('id_solicitud', function ($query) use ($idPersona) {
                    $query->select('id_solicitud')
                        ->from('solicitud_comite')
                        ->where('id_persona', $idPersona);
                })
                ->delete();

            DB::table('solicitud_comite')->where('id_persona', $idPersona)->delete();

       
            $usuarios = DB::table('usuario')
                        ->where('id_persona', $idPersona)
                        ->pluck('id_usuario');

            if ($usuarios->isNotEmpty()) {
                DB::table('bitacora')->whereIn('id_usuario', $usuarios)->delete();
                DB::table('usuario_rol')->whereIn('id_usuario', $usuarios)->delete();
                DB::table('usuario')->whereIn('id_usuario', $usuarios)->delete();
            }

            $empleados = DB::table('empleado')
                        ->where('id_persona', $idPersona)
                        ->pluck('id_empleado');

            if ($empleados->isNotEmpty()) {
                DB::table('adscripcion')->whereIn('id_empleado', $empleados)->delete();
                DB::table('docente')->whereIn('id_empleado', $empleados)->delete();
                DB::table('empleado')->whereIn('id_empleado', $empleados)->delete();
            }


            DB::table('persona')->where('id_persona', $idPersona)->delete();

            DB::commit();

            return response()->json(['message' => 'Alumno eliminado correctamente']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("ERROR DELETE ALUMNO ID {$id}: " . $e->getMessage());
            
            return response()->json([
                'error'   => 'No se pudo eliminar',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }
}
