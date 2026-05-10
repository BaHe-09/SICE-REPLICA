<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    protected $table = 'alumno';
    protected $primaryKey = 'id_alumno';

    protected $fillable = [
        'id_persona',
        'numero_control',
        'id_carrera',
        'fecha_ingreso',
        'semestre_actual',
        'estatus',
        'id_estatus_alumno',   // ID del catálogo estatus_alumno
    ];

    public $timestamps = false;

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona', 'id_persona');
    }

    // Relación con Carrera
    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'id_carrera', 'id_carrera');
    }

    // Relación con el catálogo EstatusAlumno
    // Permite obtener el nombre legible (Activo, Egresado, etc.)
    // en lugar del booleano 0/1 de la columna "estatus"
    public function estatusAlumno()
    {
        return $this->belongsTo(\App\Models\EstatusAlumno::class, 'id_estatus_alumno', 'id_estatus_alumno');
    }
}
