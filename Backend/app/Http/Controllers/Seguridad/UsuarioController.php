<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Persona;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    /**
     * Listar todos los usuarios con sus relaciones
     */
    public function index()
    {
        try {
            $usuarios = Usuario::with(['persona', 'roles'])
                ->select('id_usuario', 'nombre_usuario', 'estatus', 'id_persona')
                ->get();

            $resultado = $usuarios->map(function ($usuario) {
                $nombreCompleto = $usuario->persona 
                    ? trim("{$usuario->persona->nombre} {$usuario->persona->apellido_paterno} {$usuario->persona->apellido_materno}")
                    : 'Sin nombre';

                return [
                    'id_usuario'     => $usuario->id_usuario,
                    'nombre_usuario' => $usuario->nombre_usuario,
                    'estatus'        => $usuario->estatus ? 'Activo' : 'Inactivo',
                    'nombre_completo'=> $nombreCompleto,
                    'roles'          => $usuario->roles 
                        ? $usuario->roles->pluck('nombre_rol')->toArray() 
                        : [],
                ];
            });

            return response()->json($resultado);
        } catch (\Exception $e) {
            Log::error("Error al listar usuarios: " . $e->getMessage());
            return response()->json(['error' => 'Error al cargar usuarios'], 500);
        }
    }

    /**
     * Buscar personas para asociar a un nuevo usuario
     * GET /api/personas/buscar?q=texto
     */
    public function buscarPersonas(Request $request)
    {
        try {
            $q = trim($request->get('q') ?? '');

            if (strlen($q) < 2) {
                return response()->json([]);
            }

            $personas = Persona::where(function ($query) use ($q) {
                $query->where('nombre', 'LIKE', "%{$q}%")
                      ->orWhere('apellido_paterno', 'LIKE', "%{$q}%")
                      ->orWhere('apellido_materno', 'LIKE', "%{$q}%")
                      ->orWhere(DB::raw("CONCAT(nombre, ' ', apellido_paterno, ' ', apellido_materno)"), 'LIKE', "%{$q}%");
            })
            ->select(
                'id_persona',
                'nombre',
                'apellido_paterno',
                'apellido_materno',
                'curp'
            )
            ->limit(12)
            ->get();

            $resultado = $personas->map(function ($p) {
                return [
                    'id_persona'     => $p->id_persona,
                    'nombre_completo'=> trim("{$p->nombre} {$p->apellido_paterno} {$p->apellido_materno}"),
                    'curp'           => $p->curp,
                ];
            });

            return response()->json($resultado);

        } catch (\Exception $e) {
            Log::error('Error buscando personas: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    /**
     * Crear nuevo usuario
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'id_persona'     => 'required|exists:persona,id_persona',
                'nombre_usuario' => 'required|string|unique:usuario,nombre_usuario|max:50',
                'contrasena'     => 'required|string|min:8',
                'estatus'        => 'required|in:Activo,Inactivo',
                'roles'          => 'array'
            ]);

            // Crear el usuario
            $usuario = Usuario::create([
                'id_persona'     => $request->id_persona,
                'nombre_usuario' => $request->nombre_usuario,
                'password_hash'  => Hash::make($request->contrasena),   // Se genera el hash automáticamente
                'estatus'        => $request->estatus === 'Activo',
            ]);

            // Asignar roles
            if ($request->has('roles') && is_array($request->roles) && count($request->roles) > 0) {
                $rolesIds = Rol::whereIn('nombre_rol', $request->roles)
                              ->pluck('id_rol');
                $usuario->roles()->sync($rolesIds);
            }

            DB::commit();

            return response()->json([
                'message' => 'Usuario creado correctamente',
                'usuario' => $usuario->load('roles', 'persona')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error creando usuario: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Actualizar usuario
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $usuario = Usuario::findOrFail($id);

            $request->validate([
                'nombre_usuario' => "required|string|unique:usuario,nombre_usuario,{$id},id_usuario",
                'estatus'        => 'required|in:Activo,Inactivo',
                'roles'          => 'array'
            ]);

            $usuario->update([
                'nombre_usuario' => $request->nombre_usuario,
                'estatus'        => $request->estatus === 'Activo',
            ]);

            // Actualizar roles
            if ($request->has('roles')) {
                $rolesIds = Rol::whereIn('nombre_rol', $request->roles)->pluck('id_rol');
                $usuario->roles()->sync($rolesIds);
            }

            DB::commit();

            return response()->json([
                'message' => 'Usuario actualizado correctamente',
                'data'    => $usuario->load('roles')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error actualizando usuario {$id}: " . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar usuario'], 500);
        }
    }

    /**
     * Cambiar contraseña
     */
    public function cambiarContrasena(Request $request, $id)
    {
        $request->validate([
            'contrasena' => 'required|string|min:8'
        ]);

        try {
            $usuario = Usuario::findOrFail($id);
            $usuario->update([
                'password_hash' => Hash::make($request->contrasena)
            ]);

            return response()->json(['message' => 'Contraseña actualizada correctamente']);
        } catch (\Exception $e) {
            Log::error("Error cambiando contraseña: " . $e->getMessage());
            return response()->json(['error' => 'Error al cambiar contraseña'], 500);
        }
    }

    /**
     * Eliminar usuario
     */
    public function destroy($id)
    {
        try {
            $usuario = Usuario::findOrFail($id);
            $usuario->delete();

            return response()->json(['message' => 'Usuario eliminado correctamente']);
        } catch (\Exception $e) {
            Log::error("Error eliminando usuario {$id}: " . $e->getMessage());
            return response()->json(['error' => 'Error al eliminar usuario'], 500);
        }
    }
}
