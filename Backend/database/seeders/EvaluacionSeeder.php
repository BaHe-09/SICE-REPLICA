<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EvaluacionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('evaluacion')->insert([
            [
                'id_grupo' => 4,
                'nombre' => 'Parcial 1',
                'porcentaje' => 30.00,
            ],
            [
                'id_grupo' => 4,
                'nombre' => 'Parcial 2',
                'porcentaje' => 30.00,
            ],
            [
                'id_grupo' => 4,
                'nombre' => 'Proyecto Final',
                'porcentaje' => 40.00,
            ],
        ]);
    }
}