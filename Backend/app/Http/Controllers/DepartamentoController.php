<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DepartamentoController extends Controller
{
    public function index()
    {
        return DB::table('departamento')
            ->select('id_departamento', 'nombre')
            ->where('estatus', 1)
            ->get();
    }
}