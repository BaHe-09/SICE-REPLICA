<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeriodoController extends Controller
{
    // Obtener todos los periodos
    public function index()
    {
        $periodos = DB::table('periodo')
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        return response()->json($periodos);
    }

    // Crear nuevo periodo
    public function store(Request $request)
    {
        $request->validate([
            'nombre_periodo' => 'required|string|max:50',
            'fecha_inicio'   => 'required|date',
            'fecha_fin'      => 'required|date|after:fecha_inicio',
            'estatus'        => 'required|boolean'
        ]);

        DB::beginTransaction();

        try {
            // Si se activa uno nuevo, desactivar los demás
            if ($request->estatus) {
                DB::table('periodo')->update(['estatus' => 0]);
            }

            DB::table('periodo')->insert([
                'nombre_periodo' => $request->nombre_periodo,
                'fecha_inicio'   => $request->fecha_inicio,
                'fecha_fin'      => $request->fecha_fin,
                'estatus'        => $request->estatus
            ]);

            DB::commit();

            return response()->json(['message' => 'Periodo creado correctamente']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al crear periodo'], 500);
        }
    }

    // Actualizar periodo
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre_periodo' => 'required|string|max:50',
            'fecha_inicio'   => 'required|date',
            'fecha_fin'      => 'required|date|after:fecha_inicio',
            'estatus'        => 'required|boolean'
        ]);

        DB::beginTransaction();

        try {
            // Si se activa uno, desactivar los demás
            if ($request->estatus) {
                DB::table('periodo')
                    ->where('id_periodo', '!=', $id)
                    ->update(['estatus' => 0]);
            }

            DB::table('periodo')
                ->where('id_periodo', $id)
                ->update([
                    'nombre_periodo' => $request->nombre_periodo,
                    'fecha_inicio'   => $request->fecha_inicio,
                    'fecha_fin'      => $request->fecha_fin,
                    'estatus'        => $request->estatus
                ]);

            DB::commit();

            return response()->json(['message' => 'Periodo actualizado']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al actualizar'], 500);
        }
    }

    // Eliminar periodo
    public function destroy($id)
    {
        try {
            DB::table('periodo')
                ->where('id_periodo', $id)
                ->delete();

            return response()->json(['message' => 'Periodo eliminado']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar'], 500);
        }
    }
}