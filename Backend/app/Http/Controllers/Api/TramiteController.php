<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TramiteController extends Controller
{
    /**
     * GET /api/tramites
     */
    public function index(Request $request)
    {
        try {
            $query = DB::table('solicitud_comite as sc')
                ->join('alumno as a', 'sc.id_alumno', '=', 'a.id_alumno')
                ->join('persona as p', 'a.id_persona', '=', 'p.id_persona')
                ->leftJoin('tipo_solicitud as ts', 'sc.id_tipo_solicitud', '=', 'ts.id_tipo_solicitud')
                ->select(
                    'sc.id_solicitud',
                    'a.numero_control',
                    DB::raw("CONCAT(p.nombre,' ',p.apellido_paterno,' ',COALESCE(p.apellido_materno,'')) as nombre_alumno"),
                    'ts.nombre as tipo_tramite',
                    'sc.fecha_solicitud',
                    'sc.estatus',
                    'sc.descripcion'
                )
                ->orderBy('sc.fecha_solicitud', 'desc');

            if ($request->filled('estatus')) {
                $query->where('sc.estatus', $request->estatus);
            }

            if ($request->filled('q')) {
                $q = $request->q;
                $query->where(function ($w) use ($q) {
                    $w->where('p.nombre', 'LIKE', "%$q%")
                      ->orWhere('a.numero_control', 'LIKE', "%$q%");
                });
            }

            return response()->json($query->get());

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /api/tramites
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_alumno'         => 'required|integer|exists:alumno,id_alumno',
                'id_tipo_solicitud' => 'required|integer|exists:tipo_solicitud,id_tipo_solicitud',
                'descripcion'       => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $id = DB::table('solicitud_comite')->insertGetId([
                'id_alumno'         => $request->id_alumno,
                'id_tipo_solicitud' => $request->id_tipo_solicitud,
                'descripcion'       => $request->descripcion,
                'fecha_solicitud'   => now()->format('Y-m-d'),
                'estatus'           => 'Pendiente',
            ]);

            return response()->json(['success' => true, 'message' => 'Trámite registrado', 'id' => $id], 201);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * PUT /api/tramites/{id}
     */
    public function update(Request $request, int $id)
    {
        try {
            $tramite = DB::table('solicitud_comite')->where('id_solicitud', $id)->first();
            if (!$tramite) {
                return response()->json(['success' => false, 'error' => 'Trámite no encontrado'], 404);
            }

            $validator = Validator::make($request->all(), [
                'estatus'     => 'required|string|in:Pendiente,En Proceso,Resuelto,Rechazado',
                'descripcion' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            DB::table('solicitud_comite')->where('id_solicitud', $id)->update([
                'estatus'     => $request->estatus,
                'descripcion' => $request->descripcion ?? $tramite->descripcion,
            ]);

            return response()->json(['success' => true, 'message' => 'Trámite actualizado']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
