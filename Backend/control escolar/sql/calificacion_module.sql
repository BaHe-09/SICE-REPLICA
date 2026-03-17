USE sice;

-- ============================================================
-- DATOS BASE
-- Inserción de todos los registros necesarios para que el
-- módulo de calificacion funcione de forma independiente.
-- Se usa INSERT IGNORE para evitar errores si los datos
-- ya existen en la base de datos.
-- ============================================================

-- Catálogo de géneros
INSERT IGNORE INTO genero (id_genero, nombre_genero) VALUES (1, 'Masculino');

-- Persona del alumno de prueba
INSERT IGNORE INTO persona (id_persona, nombre, apellido_paterno, apellido_materno, curp, fecha_nacimiento, id_genero)
VALUES (1, 'Juan', 'García', 'López', 'GALJ010101HDFXXX01', '2001-01-01', 1);

-- Nivel educativo de la carrera
INSERT IGNORE INTO nivel_carrera (id_nivel, nombre_nivel) VALUES (1, 'Licenciatura');

-- Departamento académico responsable de la carrera
INSERT IGNORE INTO departamento (id_departamento, nombre, descripcion, estatus)
VALUES (1, 'Sistemas Computacionales', 'Área de TI', 1);

-- Carrera a la que pertenece el alumno
INSERT IGNORE INTO carrera (id_carrera, nombre, id_departamento, id_nivel, estatus)
VALUES (1, 'Ingeniería en Sistemas Computacionales', 1, 1, 1);

-- Alumno vinculado a la persona y carrera
INSERT IGNORE INTO alumno (id_alumno, id_persona, numero_control, id_carrera, fecha_ingreso, semestre_actual, estatus)
VALUES (1, 1, '21SIS001', 1, '2021-08-01', 5, 1);

-- Puesto del docente
INSERT IGNORE INTO puesto (id_puesto, nombre_puesto) VALUES (1, 'Docente');

-- Persona del docente
INSERT IGNORE INTO persona (id_persona, nombre, apellido_paterno, apellido_materno, curp, fecha_nacimiento, id_genero)
VALUES (2, 'Carlos', 'Martínez', 'Ruiz', 'MARC800101HDFXXX02', '1980-01-01', 1);

-- Empleado vinculado a la persona y puesto
INSERT IGNORE INTO empleado (id_empleado, id_persona, numero_empleado, id_puesto, fecha_contratacion, estatus)
VALUES (1, 2, 'EMP001', 1, '2010-01-15', 1);

-- Docente con grado académico y especialidad
INSERT IGNORE INTO docente (id_docente, id_empleado, grado_academico, especialidad)
VALUES (1, 1, 'Maestría', 'Bases de Datos');

-- Edificio donde se ubica el aula
INSERT IGNORE INTO edificio (id_edificio, nombre_edificio) VALUES (1, 'Edificio A');

-- Aula asignada al grupo
INSERT IGNORE INTO aula (id_aula, id_edificio, nombre, capacidad) VALUES (1, 1, 'A-101', 30);

-- Periodo escolar vigente
INSERT IGNORE INTO periodo (id_periodo, nombre_periodo, fecha_inicio, fecha_fin, estatus)
VALUES (1, 'Enero-Junio 2026', '2026-01-01', '2026-06-30', 1);

-- Materia que se imparte en el grupo
INSERT IGNORE INTO materia (id_materia, clave, nombre, creditos, horas_teoria, horas_practica, estatus)
VALUES (1, 'SIS401', 'Bases de Datos', 5, 3, 2, 1);

-- Grupo que vincula materia, docente, periodo y aula
INSERT IGNORE INTO grupo (id_grupo, id_materia, id_docente, id_periodo, id_aula, clave_grupo, capacidad, estatus)
VALUES (1, 1, 1, 1, 1, 'G01', 30, 1);

-- Inscripción del alumno al grupo
INSERT IGNORE INTO inscripcion (id_inscripcion, id_alumno, id_grupo, estatus)
VALUES (1, 1, 1, 'activo');

-- Evaluaciones del grupo con su ponderación (deben sumar 100%)
INSERT IGNORE INTO evaluacion (id_evaluacion, id_grupo, nombre, porcentaje)
VALUES (1, 1, 'Primer Parcial', 30.00);

INSERT IGNORE INTO evaluacion (id_evaluacion, id_grupo, nombre, porcentaje)
VALUES (2, 1, 'Segundo Parcial', 30.00);

INSERT IGNORE INTO evaluacion (id_evaluacion, id_grupo, nombre, porcentaje)
VALUES (3, 1, 'Examen Final', 40.00);

-- Calificaciones de prueba para el alumno en cada evaluación
INSERT IGNORE INTO calificacion (id_inscripcion, id_evaluacion, calificacion)
VALUES (1, 1, 85.00);

INSERT IGNORE INTO calificacion (id_inscripcion, id_evaluacion, calificacion)
VALUES (1, 2, 90.50);

INSERT IGNORE INTO calificacion (id_inscripcion, id_evaluacion, calificacion)
VALUES (1, 3, 78.00);

-- ============================================================
-- PROCEDIMIENTOS ALMACENADOS
-- La aplicación accede a la tabla calificacion únicamente
-- a través de estos procedimientos. Ninguna operación se
-- realiza directamente sobre la tabla desde el exterior.
-- ============================================================

DELIMITER $$

-- ------------------------------------------------------------
-- sp_insertar_calificacion
-- Registra una nueva calificación para un alumno.
-- Valida que:
--   - La nota esté entre 0 y 100
--   - La inscripción exista y esté activa
--   - La evaluación pertenezca al grupo del alumno inscrito
-- Si alguna validación falla, se lanza un error con SIGNAL
-- y la operación se cancela sin insertar nada.
-- ------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_insertar_calificacion$$
CREATE PROCEDURE sp_insertar_calificacion(
    IN pIdInscripcion INT,   -- ID de la inscripción del alumno al grupo
    IN pIdEvaluacion  INT,   -- ID de la evaluación (parcial, examen, etc.)
    IN pCalificacion  DECIMAL(5,2) -- Nota numérica entre 0 y 100
)
BEGIN
    DECLARE vInscripcionActiva INT DEFAULT 0;
    DECLARE vEvaluacionValida  INT DEFAULT 0;

    -- Validar que la calificación esté en rango permitido
    IF pCalificacion < 0 OR pCalificacion > 100 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'La calificación debe estar entre 0 y 100.';
    END IF;

    -- Verificar que la inscripción exista y tenga estatus activo
    SELECT COUNT(*) INTO vInscripcionActiva
    FROM inscripcion
    WHERE id_inscripcion = pIdInscripcion
      AND estatus = 'activo';

    IF vInscripcionActiva = 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'La inscripción no existe o no está activa.';
    END IF;

    -- Verificar que la evaluación pertenezca al mismo grupo del alumno
    SELECT COUNT(*) INTO vEvaluacionValida
    FROM evaluacion e
    INNER JOIN inscripcion i ON i.id_grupo = e.id_grupo
    WHERE e.id_evaluacion = pIdEvaluacion
      AND i.id_inscripcion = pIdInscripcion;

    IF vEvaluacionValida = 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'La evaluación no pertenece al grupo del alumno inscrito.';
    END IF;

    -- Insertar la calificación si pasan todas las validaciones
    INSERT INTO calificacion (id_inscripcion, id_evaluacion, calificacion)
    VALUES (pIdInscripcion, pIdEvaluacion, pCalificacion);

    SELECT 'Calificación registrada correctamente.' AS mensaje;
END$$


-- ------------------------------------------------------------
-- sp_mostrar_calificaciones
-- Devuelve todas las calificaciones con información completa
-- usando JOINs para mostrar nombres en lugar de IDs.
-- Incluye: nombre del alumno, número de control, materia,
-- tipo de evaluación, porcentaje y calificación obtenida.
-- ------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_mostrar_calificaciones$$
CREATE PROCEDURE sp_mostrar_calificaciones()
BEGIN
    SELECT
        c.id_calificacion,
        -- Nombre completo del alumno construido desde la tabla persona
        CONCAT(p.nombre, ' ', p.apellido_paterno, ' ', IFNULL(p.apellido_materno, '')) AS alumno,
        al.numero_control,
        m.nombre        AS materia,
        e.nombre        AS tipo_evaluacion,
        e.porcentaje    AS porcentaje_evaluacion,
        c.calificacion,
        c.fecha_registro
    FROM calificacion c
    -- JOIN para obtener datos del alumno inscrito
    INNER JOIN inscripcion  i  ON c.id_inscripcion = i.id_inscripcion
    INNER JOIN alumno       al ON i.id_alumno       = al.id_alumno
    INNER JOIN persona      p  ON al.id_persona     = p.id_persona
    -- JOIN para obtener nombre y porcentaje de la evaluación
    INNER JOIN evaluacion   e  ON c.id_evaluacion   = e.id_evaluacion
    -- JOIN para obtener el nombre de la materia
    INNER JOIN grupo        g  ON e.id_grupo         = g.id_grupo
    INNER JOIN materia      m  ON g.id_materia       = m.id_materia
    ORDER BY alumno, m.nombre, e.nombre;
END$$


-- ------------------------------------------------------------
-- sp_buscar_calificacion_por_curp
-- Busca todas las calificaciones de un alumno usando su CURP
-- como identificador único.
-- Incluye el periodo escolar y un campo calculado que indica
-- si el alumno aprobó o reprobó (mínimo aprobatorio: 70).
-- ------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_buscar_calificacion_por_curp$$
CREATE PROCEDURE sp_buscar_calificacion_por_curp(
    IN pCurp VARCHAR(18) -- CURP del alumno a consultar
)
BEGIN
    DECLARE vPersonaExiste INT DEFAULT 0;

    -- Validar que el CURP exista en el sistema antes de consultar
    SELECT COUNT(*) INTO vPersonaExiste
    FROM persona WHERE curp = pCurp;

    IF vPersonaExiste = 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'No se encontró ninguna persona con ese CURP.';
    END IF;

    SELECT
        c.id_calificacion,
        CONCAT(p.nombre, ' ', p.apellido_paterno, ' ', IFNULL(p.apellido_materno, '')) AS alumno,
        al.numero_control,
        m.nombre           AS materia,
        per.nombre_periodo AS periodo,
        e.nombre           AS tipo_evaluacion,
        e.porcentaje       AS porcentaje_evaluacion,
        c.calificacion,
        -- Campo calculado: Aprobado si calificacion >= 70, Reprobado si es menor
        CASE WHEN c.calificacion >= 70 THEN 'Aprobado' ELSE 'Reprobado' END AS estatus_aprobacion,
        c.fecha_registro
    FROM calificacion c
    INNER JOIN inscripcion  i   ON c.id_inscripcion = i.id_inscripcion
    INNER JOIN alumno       al  ON i.id_alumno       = al.id_alumno
    INNER JOIN persona      p   ON al.id_persona     = p.id_persona
    INNER JOIN evaluacion   e   ON c.id_evaluacion   = e.id_evaluacion
    INNER JOIN grupo        g   ON e.id_grupo         = g.id_grupo
    INNER JOIN materia      m   ON g.id_materia       = m.id_materia
    INNER JOIN periodo      per ON g.id_periodo       = per.id_periodo
    WHERE p.curp = pCurp
    ORDER BY m.nombre, e.nombre;
END$$


-- ------------------------------------------------------------
-- sp_actualizar_calificacion
-- Modifica la nota de una calificación existente.
-- Valida que el registro exista y que la nueva nota
-- esté dentro del rango permitido (0 a 100).
-- ------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_actualizar_calificacion$$
CREATE PROCEDURE sp_actualizar_calificacion(
    IN pIdCalificacion    INT,           -- ID del registro a modificar
    IN pNuevaCalificacion DECIMAL(5,2)   -- Nueva calificación a asignar
)
BEGIN
    DECLARE vExiste INT DEFAULT 0;

    -- Validar rango de la nueva calificación
    IF pNuevaCalificacion < 0 OR pNuevaCalificacion > 100 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'La calificación debe estar entre 0 y 100.';
    END IF;

    -- Verificar que el registro exista antes de actualizar
    SELECT COUNT(*) INTO vExiste
    FROM calificacion WHERE id_calificacion = pIdCalificacion;

    IF vExiste = 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'No se encontró la calificación con ese ID.';
    END IF;

    -- Actualizar la calificación
    UPDATE calificacion
    SET calificacion = pNuevaCalificacion
    WHERE id_calificacion = pIdCalificacion;

    SELECT 'Calificación actualizada correctamente.' AS mensaje;
END$$


-- ------------------------------------------------------------
-- sp_eliminar_calificacion
-- Elimina una calificación por su ID.
-- Verifica que el registro exista antes de eliminar para
-- evitar errores silenciosos.
-- ------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_eliminar_calificacion$$
CREATE PROCEDURE sp_eliminar_calificacion(
    IN pIdCalificacion INT -- ID del registro a eliminar
)
BEGIN
    DECLARE vExiste INT DEFAULT 0;

    -- Verificar que el registro exista antes de eliminar
    SELECT COUNT(*) INTO vExiste
    FROM calificacion WHERE id_calificacion = pIdCalificacion;

    IF vExiste = 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'No se encontró la calificación con ese ID.';
    END IF;

    -- Eliminar el registro
    DELETE FROM calificacion WHERE id_calificacion = pIdCalificacion;

    SELECT 'Calificación eliminada correctamente.' AS mensaje;
END$$

DELIMITER ;

-- ============================================================
-- SEGURIDAD
-- Se crea un usuario específico para la aplicación con
-- permisos restringidos. Este usuario solo puede ejecutar
-- los procedimientos almacenados del módulo, sin acceso
-- directo a ninguna tabla de la base de datos.
-- ============================================================

-- Crear el usuario de la aplicación si no existe
CREATE USER IF NOT EXISTS 'usuario_app'@'localhost' IDENTIFIED BY 'AppSice2026!';

-- Revocar cualquier permiso directo sobre tablas
REVOKE ALL PRIVILEGES ON sice.* FROM 'usuario_app'@'localhost';

-- Otorgar permiso únicamente para ejecutar los procedimientos del módulo
GRANT EXECUTE ON PROCEDURE sice.sp_insertar_calificacion       TO 'usuario_app'@'localhost';
GRANT EXECUTE ON PROCEDURE sice.sp_mostrar_calificaciones       TO 'usuario_app'@'localhost';
GRANT EXECUTE ON PROCEDURE sice.sp_buscar_calificacion_por_curp TO 'usuario_app'@'localhost';
GRANT EXECUTE ON PROCEDURE sice.sp_actualizar_calificacion      TO 'usuario_app'@'localhost';
GRANT EXECUTE ON PROCEDURE sice.sp_eliminar_calificacion        TO 'usuario_app'@'localhost';

-- Aplicar los cambios de permisos
FLUSH PRIVILEGES;

-- ============================================================
-- PRUEBAS
-- Consultas para verificar que los procedimientos
-- funcionan correctamente después de ejecutar el script.
-- ============================================================

-- Ver todas las calificaciones con detalle completo
CALL sp_mostrar_calificaciones();

-- Buscar calificaciones de un alumno por su CURP
CALL sp_buscar_calificacion_por_curp('GALJ010101HDFXXX01');
