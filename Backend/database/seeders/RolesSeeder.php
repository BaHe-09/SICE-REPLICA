<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Inserta los roles básicos del sistema SICE si no existen.
     */
    public function run(): void
    {
        $roles = [
            ['nombre_rol' => 'Administrador',        'descripcion' => 'Acceso total al sistema',                         'estatus' => 1],
            ['nombre_rol' => 'Servicios Escolares',  'descripcion' => 'Gestión de alumnos, grupos, calificaciones',      'estatus' => 1],
            ['nombre_rol' => 'Docente',              'descripcion' => 'Registro de calificaciones de sus grupos',        'estatus' => 1],
            ['nombre_rol' => 'Alumno',               'descripcion' => 'Consulta de kardex, horarios y trámites',        'estatus' => 1],
            ['nombre_rol' => 'Coordinador',          'descripcion' => 'Supervisión académica por carrera',               'estatus' => 1],
            ['nombre_rol' => 'Dirección',            'descripcion' => 'Acceso a reportes y estadísticas generales',      'estatus' => 1],
            ['nombre_rol' => 'Recursos Humanos',     'descripcion' => 'Gestión de empleados y docentes',                 'estatus' => 1],
            ['nombre_rol' => 'Comité Académico',     'descripcion' => 'Gestión de solicitudes y sesiones del comité',   'estatus' => 1],
        ];

        foreach ($roles as $rol) {
            // updateOrInsert evita duplicados por nombre
            DB::table('rol')->updateOrInsert(
                ['nombre_rol' => $rol['nombre_rol']],
                $rol
            );
        }
    }
}
