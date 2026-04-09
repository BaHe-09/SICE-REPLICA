<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MateriaController extends Controller
{
    // ===============================
    // LISTAR
    // ===============================
    public function index()
    {
        return DB::table('materia')
            ->select(
                'id_materia',
                'clave',
                'nombre',
                'creditos',
                'horas_teoria',
                'horas_practica',
                'estatus'
            )
            ->get();
    }

    // ===============================
    // CREAR
    // ===============================
    public function store(Request $request)
    {
        DB::table('materia')->insert([
            'clave' => $request->clave,
            'nombre' => $request->nombre,
            'creditos' => $request->creditos,
            'horas_teoria' => $request->horas_teoria,
            'horas_practica' => $request->horas_practica,
            'estatus' => $request->estatus ?? 1
        ]);

        return response()->json(['message' => 'Materia creada']);
    }

    // ===============================
    // ACTUALIZAR
    // ===============================
    public function update(Request $request, $id)
    {
        DB::table('materia')
            ->where('id_materia', $id)
            ->update([
                'nombre' => $request->nombre,
                'creditos' => $request->creditos,
                'horas_teoria' => $request->horas_teoria,
                'horas_practica' => $request->horas_practica,
                'estatus' => $request->estatus
            ]);

        return response()->json(['message' => 'Materia actualizada']);
    }

    // ===============================
    // ELIMINAR
    // ===============================
    public function destroy($id)
    {
        DB::table('materia')
            ->where('id_materia', $id)
            ->delete();

        return response()->json(['message' => 'Materia eliminada']);
    }

    // ===============================
    // PLANES ASOCIADOS
    // ===============================
    public function planes($id)
    {
        return DB::table('plan_materia as pm')
            ->join('plan_estudio as p', 'pm.id_plan', '=', 'p.id_plan')
            ->join('carrera as c', 'p.id_carrera', '=', 'c.id_carrera')
            ->where('pm.id_materia', $id)
            ->select(
                'pm.id_plan',
                'pm.semestre',
                'p.nombre_plan',
                'c.nombre as carrera'
            )
            ->get();
    }
}