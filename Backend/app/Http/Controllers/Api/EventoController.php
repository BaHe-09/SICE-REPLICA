<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventoController extends Controller
{
    /**
     * GET /api/eventos
     * Lista de eventos para EventosView.vue
     */
    public function index(Request $request)
    {
        try {
            $nombre = trim($request->query('nombre', ''));
            $tipo   = trim($request->query('tipo', ''));

            $query = DB::table('evento as e')
                ->join('tipo_evento as t', 'e.id_tipo_evento', '=', 't.id_tipo_evento')
                ->leftJoin('participacion_evento as pe', 'e.id_evento', '=', 'pe.id_evento')
                ->select(
                    'e.id_evento',
                    'e.nombre_evento',
                    't.nombre_tipo',
                    'e.fecha',
                    'e.descripcion',
                    DB::raw('COUNT(pe.id_participacion) as participantes')
                )
                ->when($nombre !== '', function ($q) use ($nombre) {
                    $q->where('e.nombre_evento', 'like', '%' . $nombre . '%');
                })
                ->when($tipo !== '', function ($q) use ($tipo) {
                    $q->where('t.nombre_tipo', $tipo);
                })
                ->groupBy(
                    'e.id_evento',
                    'e.nombre_evento',
                    't.nombre_tipo',
                    'e.fecha',
                    'e.descripcion'
                )
                ->orderBy('e.fecha', 'asc');

            $eventos = $query->get()->map(function ($evento) {
                $hoy = now()->toDateString();

                return [
                    'id'            => (int) $evento->id_evento,
                    'nombre'        => $evento->nombre_evento,
                    'tipo'          => $this->mapearTipo($evento->nombre_tipo),
                    'fecha'         => $evento->fecha,
                    'lugar'         => 'Por definir',
                    'descripcion'   => $evento->descripcion,
                    'participantes' => (int) $evento->participantes,
                    'cupo_maximo'   => null,
                    'activo'        => $evento->fecha >= $hoy,
                ];
            });

            return response()->json($eventos, 200);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al obtener eventos',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/tipos-evento
     * Datos para el select de filtro
     */
    public function tiposEvento()
    {
        try {
            $tipos = DB::table('tipo_evento')
                ->select(
                    'id_tipo_evento as id',
                    DB::raw("
                        CASE
                            WHEN nombre_tipo IN ('Conferencia', 'Taller', 'Seminario') THEN 'Académico'
                            WHEN nombre_tipo = 'Evento Cultural' THEN 'Cultural'
                            WHEN nombre_tipo = 'Evento Deportivo' THEN 'Deportivo'
                            WHEN nombre_tipo = 'Concurso' THEN 'Institucional'
                            ELSE nombre_tipo
                        END as nombre
                    ")
                )
                ->get();

            $tiposUnicos = $tipos
                ->unique('nombre')
                ->values();

            return response()->json($tiposUnicos, 200);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al obtener tipos de evento',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mapea los tipos de BD a los tipos visuales esperados por el front.
     */
    private function mapearTipo(string $tipoBd): string
    {
        return match ($tipoBd) {
            'Conferencia', 'Taller', 'Seminario' => 'Académico',
            'Evento Cultural'                    => 'Cultural',
            'Evento Deportivo'                   => 'Deportivo',
            'Concurso'                           => 'Institucional',
            default                              => $tipoBd,
        };
    }
}