<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class PlanEstudioController extends Controller
{
    public function index()
    {
        return DB::table('plan_estudio')
            ->select('id_plan', 'nombre_plan')
            ->get();
    }
}