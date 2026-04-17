<?php

namespace App\Http\Controllers\Docentes;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DocenteController extends Controller
{
    /**
     * GET /api/docentes/disponibles
     * Devuelve docentes disponibles (como espera el frontend)
     */
    public function disponibles()
    {
        try {
            $docentes = DB::table('docente as d')
                ->join('persona as p', 'd.id_persona', '=', 'p.id_persona')
                ->select([
                    'd.id_docente',
                    DB::raw("CONCAT(p.nombre, ' ', p.apellido_paterno, ' ', COALESCE(p.apellido_materno, '')) as nombre"),
                    'd.numero_empleado',
                    DB::raw('0 as carga_actual'),           // Puedes calcularlo después si quieres
                    DB::raw('[] as horarios')               // Placeholder (puedes expandir después)
                ])
                ->where('d.estatus', true)
                ->orderBy('p.apellido_paterno')
                ->get();

            return response()->json($docentes);

        } catch (\Exception $e) {
            \Log::error("Error en docentes disponibles: " . $e->getMessage());
            return response()->json(['error' => 'Error al cargar docentes'], 500);
        }
    }
}
