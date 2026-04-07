<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class GrupoController extends Controller
{
    public function index()
    {
        try {
            $grupos = DB::table('grupo as g')
                ->leftJoin('materia as m', 'g.id_materia', '=', 'm.id_materia')
                ->leftJoin('persona as p', 'g.id_docente', '=', 'p.id_persona')
                ->leftJoin('aula as a', 'g.id_aula', '=', 'a.id_aula')
                ->leftJoin('inscripcion as i', 'g.id_grupo', '=', 'i.id_grupo')
                ->select(
                    'g.id_grupo',
                    'g.clave_grupo',
                    'm.nombre as materia',
                    DB::raw("COALESCE(CONCAT(p.nombre, ' ', p.apellido_paterno), 'Sin docente') as docente"),
                    'a.nombre as aula',
                    'g.capacidad',
                    DB::raw("COUNT(CASE WHEN i.estatus IN ('activo','inscrito') THEN 1 END) as inscritos")
                )
                ->groupBy('g.id_grupo', 'g.clave_grupo', 'm.nombre', 'p.nombre', 'p.apellido_paterno', 'a.nombre', 'g.capacidad')
                ->get();

            return response()->json($grupos);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(\Illuminate\Http\Request $request)
{
    try {
        $id_materia = DB::table('materia')->where('nombre', $request->nombre_materia)->value('id_materia');
        $id_aula = DB::table('aula')->where('nombre', $request->aula)->value('id_aula');
        $id_docente = DB::table('persona')->where(
            DB::raw("CONCAT(nombre, ' ', apellido_paterno)"), $request->nombre_docente
        )->value('id_persona');

        $id = DB::table('grupo')->insertGetId([
            'clave_grupo' => $request->clave_grupo ?? 'G-NEW',
            'id_materia'  => $id_materia,
            'id_docente'  => $id_docente,
            'id_aula'     => $id_aula,
            'id_periodo'  => 1,
            'capacidad'   => $request->capacidad ?? 30,
            'estatus'     => 1,
        ]);
        return response()->json(['message' => 'Grupo creado', 'id' => $id], 201);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

     public function update(\Illuminate\Http\Request $request, $id)
{
    try {
        $id_materia = DB::table('materia')->where('nombre', $request->nombre_materia)->value('id_materia');
        $id_aula = DB::table('aula')->where('nombre', $request->aula)->value('id_aula');
        $id_docente = DB::table('persona')->where(
            DB::raw("CONCAT(nombre, ' ', apellido_paterno)"), $request->nombre_docente
        )->value('id_persona');

        DB::table('grupo')->where('id_grupo', $id)->update([
            'id_materia'  => $id_materia ?? DB::table('grupo')->where('id_grupo', $id)->value('id_materia'),
            'id_aula'     => $id_aula ?? DB::table('grupo')->where('id_grupo', $id)->value('id_aula'),
            'id_docente'  => $id_docente ?? DB::table('grupo')->where('id_grupo', $id)->value('id_docente'),
            'capacidad'   => $request->capacidad ?? 30,
        ]);
        return response()->json(['message' => 'Grupo actualizado']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

    public function destroy($id)
    {
        try {
            DB::table('grupo')->where('id_grupo', $id)->delete();
            return response()->json(['message' => 'Grupo eliminado']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}