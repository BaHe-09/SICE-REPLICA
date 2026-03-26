<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class InscripcionController extends Controller
{
    public function gruposDisponibles()
    {
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
    }
}