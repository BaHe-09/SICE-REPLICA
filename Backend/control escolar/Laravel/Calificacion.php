<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Calificacion
 *
 * Representa la tabla calificacion en la base de datos.
 * Permite realizar operaciones CRUD sobre las calificaciones
 * de los alumnos a través de Laravel Eloquent ORM.
 */
class Calificacion extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table = 'calificacion';

    // Llave primaria de la tabla
    protected $primaryKey = 'id_calificacion';

    // Se desactivan los timestamps automáticos (created_at, updated_at)
    // porque la tabla usa fecha_registro con valor por defecto en la BD
    public $timestamps = false;

    // Campos permitidos para asignación masiva (create, update)
    protected $fillable = [
        'id_inscripcion', // FK hacia la tabla inscripcion
        'id_evaluacion',  // FK hacia la tabla evaluacion
        'calificacion',   // Nota numérica del alumno (0 a 100)
    ];
}