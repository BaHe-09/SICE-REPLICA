<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Inscripcion
 *
 * Representa la tabla inscripcion en la base de datos.
 * Permite realizar operaciones CRUD sobre las inscripciones
 * de los alumnos mediante Laravel Eloquent ORM.
 */
class Inscripcion extends Model
{
    protected $table = 'inscripcion';
    protected $primaryKey = 'id_inscripcion';
    public $timestamps = false;

    protected $fillable = [
        'id_alumno',
        'id_grupo',
        'fecha_inscripcion',
        'estatus'
    ];
}