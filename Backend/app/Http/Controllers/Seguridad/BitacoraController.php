<?php

namespace App\Http\Controllers\Seguridad;

use App\Models\Bitacora;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class BitacoraController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Bitacora::with(['usuario', 'modulo'])
                ->select(
                    'bitacora.id_bitacora',
                    'bitacora.fecha_hora',
                    'bitacora.accion',
                    'bitacora.direccion_ip',
                    DB::raw("CONCAT(persona.nombre, ' ', persona.apellido_paterno) as usuario"),
                    'modulo.nombre_modulo as modulo',
                    'bitacora.accion as descripcion'   // puedes ajustar si quieres más detalle
                )
                ->leftJoin('usuario', 'bitacora.id_usuario', '=', 'usuario.id_usuario')
                ->leftJoin('persona', 'usuario.id_persona', '=', 'persona.id_persona')
                ->leftJoin('modulo', 'bitacora.id_modulo', '=', 'modulo.id_modulo')
                ->orderBy('bitacora.fecha_hora', 'desc');

            // Filtros
            if ($request->filled('usuario')) {
                $query->whereRaw("CONCAT(persona.nombre, ' ', persona.apellido_paterno) LIKE ?", ['%' . $request->usuario . '%']);
            }

            if ($request->filled('modulo')) {
                $query->where('modulo.nombre_modulo', $request->modulo);
            }

            if ($request->filled('accion')) {
                $query->where('bitacora.accion', 'LIKE', '%' . $request->accion . '%');
            }

            if ($request->filled('fecha_desde')) {
                $query->whereDate('bitacora.fecha_hora', '>=', $request->fecha_desde);
            }

            if ($request->filled('fecha_hasta')) {
                $query->whereDate('bitacora.fecha_hora', '<=', $request->fecha_hasta);
            }

            $bitacora = $query->get();

            // Formato que espera tu Vue
            $data = $bitacora->map(function ($item) {
                return [
                    'id_bitacora' => $item->id_bitacora,
                    'fecha_hora'  => $item->fecha_hora,
                    'usuario'     => $item->usuario ?? 'Sistema',
                    'accion'      => $item->accion,
                    'modulo'      => $item->modulo ?? 'Desconocido',
                    'descripcion' => $item->descripcion ?? $item->accion,
                    'ip'          => $item->direccion_ip,
                ];
            });

            return response()->json($data);

        } catch (\Exception $e) {
            \Log::error('Error en bitácora: ' . $e->getMessage());
            return response()->json(['error' => 'Error al cargar la bitácora'], 500);
        }
    }
}