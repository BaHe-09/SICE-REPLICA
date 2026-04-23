<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalificacionesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('calificaciones', function (Blueprint $table) {
            $table->bigIncrements('id_calificacion'); // PK BIGINT UNSIGNED

            $table->string('control', 20)->unique(); // número de control
            $table->string('nombre');                // nombre del alumno
            $table->integer('p1')->nullable();       // parcial 1 (30%)
            $table->integer('p2')->nullable();       // parcial 2 (30%)
            $table->integer('proy')->nullable();     // proyecto (40%)

            // Relación con evaluacion
            $table->unsignedBigInteger('id_evaluacion'); // FK BIGINT UNSIGNED
            $table->foreign('id_evaluacion')
                  ->references('id_evaluacion')
                  ->on('evaluacion')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calificaciones');
    }
}