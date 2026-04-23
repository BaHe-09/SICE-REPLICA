<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanMateriaController extends Controller
{
    public function store(Request $request)
    {
        DB::table('plan_materia')->insert([
            'id_plan' => $request->id_plan,
            'id_materia' => $request->id_materia,
            'semestre' => $request->semestre
        ]);

        return response()->json(['message' => 'Materia agregada al plan']);
    }
}