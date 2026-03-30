<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CalificacionesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('calificaciones')->insert([
            [
                'control' => '20260000000000000001', // 20 caracteres
                'nombre'  => 'Juan Pérez',
                'p1'      => 85,
                'p2'      => 90,
                'proy'    => 95,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'control' => '20260000000000000002', // 20 caracteres
                'nombre'  => 'María López',
                'p1'      => 78,
                'p2'      => 82,
                'proy'    => 88,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}