<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CarreraController extends Controller
{
    // ─────────────────────────────────────────────
    // Obtener todas las carreras
    public function index()
    {
        $carreras = DB::table('carrera as c')
            ->leftJoin('departamento as d', 'c.id_departamento', '=', 'd.id_departamento')
            ->leftJoin('nivel_carrera as n', 'c.id_nivel', '=', 'n.id_nivel')
            ->select(
                'c.id_carrera',
                'c.nombre',
                'c.id_departamento',
                'c.id_nivel',
                'c.estatus',
                'd.nombre as departamento_nombre',
                'n.nombre_nivel'
            )
            ->get()
            ->map(function ($c) {
                return [
                    'id_carrera' => $c->id_carrera,
                    'nombre' => $c->nombre,
                    'id_departamento' => $c->id_departamento,
                    'id_nivel' => $c->id_nivel,
                    'estatus' => $c->estatus,
                    'departamento' => [
                        'nombre' => $c->departamento_nombre
                    ],
                    'nivel' => [
                        'nombre_nivel' => $c->nombre_nivel
                    ]
                ];
            });

        return response()->json($carreras);
    }

    // ─────────────────────────────────────────────
    // Crear carrera
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|max:150',
            'id_departamento' => 'required|exists:departamento,id_departamento',
            'id_nivel' => 'required|exists:nivel_carrera,id_nivel',
            'estatus' => 'boolean'
        ]);

        $id = DB::table('carrera')->insertGetId([
            'nombre' => $request->nombre,
            'id_departamento' => $request->id_departamento,
            'id_nivel' => $request->id_nivel,
            'estatus' => $request->estatus ?? 1
        ]);

        return response()->json([
            'message' => 'Carrera creada',
            'id' => $id
        ], 201);
    }

    // ─────────────────────────────────────────────
    // Actualizar carrera
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|max:150',
            'id_departamento' => 'required|exists:departamento,id_departamento',
            'id_nivel' => 'required|exists:nivel_carrera,id_nivel',
            'estatus' => 'boolean'
        ]);

        DB::table('carrera')
            ->where('id_carrera', $id)
            ->update([
                'nombre' => $request->nombre,
                'id_departamento' => $request->id_departamento,
                'id_nivel' => $request->id_nivel,
                'estatus' => $request->estatus
            ]);

        return response()->json([
            'message' => 'Carrera actualizada'
        ]);
    }

    // ─────────────────────────────────────────────
    // Eliminar carrera
    public function destroy($id)
    {
        DB::table('carrera')
            ->where('id_carrera', $id)
            ->delete();

        return response()->json([
            'message' => 'Carrera eliminada'
        ]);
    }
}
