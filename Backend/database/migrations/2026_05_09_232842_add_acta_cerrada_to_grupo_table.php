<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('grupo', function (Blueprint $table) {
            $table->boolean('acta_cerrada')->default(false)->after('estatus');
            $table->timestamp('fecha_cierre_acta')->nullable()->after('acta_cerrada');
        });
    }

    public function down(): void
    {
        Schema::table('grupo', function (Blueprint $table) {
            $table->dropColumn(['acta_cerrada', 'fecha_cierre_acta']);
        });
    }
};
