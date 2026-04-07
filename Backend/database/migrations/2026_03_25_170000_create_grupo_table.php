<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluacion', function (Blueprint $table) {
            $table->id('id_evaluacion');
            $table->string('nombre');
            $table->date('fecha');
            $table->unsignedBigInteger('id_grupo');
            $table->timestamps();

            $table->foreign('id_grupo')
                  ->references('id_grupo')
                  ->on('grupo')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluacion');
    }
};