<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alumno', function (Blueprint $table) {
            $table->string('estatus', 50)->default('Activo')->after('numero_control');
            $table->unsignedBigInteger('id_carrera')->nullable()->after('estatus');
            $table->integer('semestre_actual')->nullable()->after('id_carrera');

            $table->foreign('id_carrera')
                  ->references('id_carrera')
                  ->on('carrera')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('alumno', function (Blueprint $table) {
            $table->dropForeign(['id_carrera']);
            $table->dropColumn(['estatus', 'id_carrera', 'semestre_actual']);
        });
    }
};
