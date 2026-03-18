<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    protected $table = 'alumno';          // Nombre de la tabla
    protected $primaryKey = 'id_alumno';  // Llave primaria

    protected $fillable = [
        'id_persona',
        'numero_control',
        'id_carrera',
        'fecha_ingreso',
        'semestre_actual',
        'estatus'
    ];

    public $timestamps = false; // Desactivar timestamps si no los usas
}