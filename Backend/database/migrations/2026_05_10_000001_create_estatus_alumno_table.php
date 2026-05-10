<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estatus_alumno', function (Blueprint $table) {
            $table->id('id_estatus_alumno');
            $table->string('nombre', 50)->unique();
        });

        // Insertar valores por defecto
        DB::table('estatus_alumno')->insert([
            ['nombre' => 'Activo'],
            ['nombre' => 'Baja Temporal'],
            ['nombre' => 'Baja Definitiva'],
            ['nombre' => 'Titulado'],
            ['nombre' => 'Egresado'],
            ['nombre' => 'Aspirante'],
            ['nombre' => 'Preinscrito'],
            ['nombre' => 'Admitido'],
            ['nombre' => 'Proceso de Egreso'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('estatus_alumno');
    }
};
