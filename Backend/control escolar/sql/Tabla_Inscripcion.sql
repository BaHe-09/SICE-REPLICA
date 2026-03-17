USE sice;

-- ============================================================
-- DATOS BASE
-- Inserción de datos necesarios respetando integridad referencial
-- ============================================================

-- GENERO
INSERT IGNORE INTO genero (id_genero, nombre_genero)
VALUES (1, 'Masculino'), (2, 'Femenino');

-- PERSONA (Alumno)
INSERT IGNORE INTO persona (id_persona, nombre, apellido_paterno, apellido_materno, curp, fecha_nacimiento, id_genero)
VALUES (1,'Juan','Perez','Lopez','PEJL010101HNLXXX01','2001-01-01',1);

-- DEPARTAMENTO
INSERT IGNORE INTO departamento (id_departamento, nombre, descripcion)
VALUES (1,'Sistemas','Departamento de Sistemas');

-- NIVEL CARRERA
INSERT IGNORE INTO nivel_carrera (id_nivel, nombre_nivel)
VALUES (1,'Licenciatura');

-- CARRERA
INSERT IGNORE INTO carrera (id_carrera, nombre, id_departamento, id_nivel)
VALUES (1,'Ingenieria en Sistemas',1,1);

-- ALUMNO
INSERT IGNORE INTO alumno (id_alumno, id_persona, numero_control, id_carrera, fecha_ingreso, semestre_actual)
VALUES (1,1,'22180001',1,'2024-08-01',1);

-- PERIODO
INSERT IGNORE INTO periodo (id_periodo, nombre_periodo, fecha_inicio, fecha_fin)
VALUES (1,'2025-1','2025-01-01','2025-06-01');

-- EDIFICIO
INSERT IGNORE INTO edificio (id_edificio, nombre_edificio)
VALUES (1,'Edificio A');

-- AULA
INSERT IGNORE INTO aula (id_aula, id_edificio, nombre, capacidad)
VALUES (1,1,'A101',30);

-- PUESTO
INSERT IGNORE INTO puesto (id_puesto, nombre_puesto)
VALUES (1,'Profesor');

-- EMPLEADO
INSERT IGNORE INTO empleado (id_empleado, id_persona, numero_empleado, id_puesto, fecha_contratacion)
VALUES (1,1,'EMP001',1,'2020-01-01');

-- DOCENTE
INSERT IGNORE INTO docente (id_docente, id_empleado, grado_academico, especialidad)
VALUES (1,1,'Maestria','Programacion');

-- MATERIA
INSERT IGNORE INTO materia (id_materia, clave, nombre, creditos, horas_teoria, horas_practica)
VALUES (1,'ISC101','Programacion',8,4,4);

-- GRUPO
INSERT IGNORE INTO grupo (id_grupo, id_materia, id_docente, id_periodo, id_aula, clave_grupo, capacidad)
VALUES (1,1,1,1,1,'A1',30);

-- INSCRIPCION
INSERT IGNORE INTO inscripcion (id_inscripcion, id_alumno, id_grupo, fecha_inscripcion, estatus)
VALUES (1,1,1,CURDATE(),'ACTIVA');

-- ============================================================
-- PROCEDIMIENTOS ALMACENADOS (CRUD ALUMNO)
-- ============================================================

DELIMITER $$

-- ------------------------------------------------------------
-- CONSULTAR ALUMNOS
-- ------------------------------------------------------------
DROP PROCEDURE IF EXISTS ver_alumnos$$
CREATE PROCEDURE ver_alumnos()
BEGIN
    SELECT 
        a.id_alumno,
        p.nombre,
        p.apellido_paterno,
        a.numero_control,
        c.nombre AS carrera
    FROM alumno a
    JOIN persona p ON a.id_persona = p.id_persona
    JOIN carrera c ON a.id_carrera = c.id_carrera;
END$$


-- ------------------------------------------------------------
-- INSERTAR ALUMNO
-- ------------------------------------------------------------
DROP PROCEDURE IF EXISTS insertar_alumno$$
CREATE PROCEDURE insertar_alumno(
    IN p_id_persona INT,
    IN p_numero_control VARCHAR(20),
    IN p_id_carrera INT,
    IN p_fecha_ingreso DATE,
    IN p_semestre INT
)
BEGIN
    IF p_numero_control IS NULL OR p_numero_control = '' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El número de control es obligatorio';
    END IF;

    INSERT INTO alumno
    (id_persona,numero_control,id_carrera,fecha_ingreso,semestre_actual)
    VALUES
    (p_id_persona,p_numero_control,p_id_carrera,p_fecha_ingreso,p_semestre);

    SELECT 'Alumno insertado correctamente' AS mensaje;
END$$


-- ------------------------------------------------------------
-- ACTUALIZAR ALUMNO
-- ------------------------------------------------------------
DROP PROCEDURE IF EXISTS actualizar_alumno$$
CREATE PROCEDURE actualizar_alumno(
    IN p_id INT,
    IN p_semestre INT
)
BEGIN
    DECLARE existe INT;

    SELECT COUNT(*) INTO existe FROM alumno WHERE id_alumno = p_id;

    IF existe = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El alumno no existe';
    END IF;

    UPDATE alumno
    SET semestre_actual = p_semestre
    WHERE id_alumno = p_id;

    SELECT 'Alumno actualizado correctamente' AS mensaje;
END$$


-- ------------------------------------------------------------
-- ELIMINAR ALUMNO
-- ------------------------------------------------------------
DROP PROCEDURE IF EXISTS eliminar_alumno$$
CREATE PROCEDURE eliminar_alumno(
    IN p_id INT
)
BEGIN
    DECLARE existe INT;

    SELECT COUNT(*) INTO existe FROM alumno WHERE id_alumno = p_id;

    IF existe = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El alumno no existe';
    END IF;

    DELETE FROM alumno WHERE id_alumno = p_id;

    SELECT 'Alumno eliminado correctamente' AS mensaje;
END$$

DELIMITER ;

-- ============================================================
-- SEGURIDAD
-- ============================================================

CREATE USER IF NOT EXISTS 'usuario_app'@'localhost' IDENTIFIED BY '123456';

REVOKE ALL PRIVILEGES ON sice.* FROM 'usuario_app'@'localhost';

GRANT EXECUTE ON PROCEDURE sice.ver_alumnos TO 'usuario_app'@'localhost';
GRANT EXECUTE ON PROCEDURE sice.insertar_alumno TO 'usuario_app'@'localhost';
GRANT EXECUTE ON PROCEDURE sice.actualizar_alumno TO 'usuario_app'@'localhost';
GRANT EXECUTE ON PROCEDURE sice.eliminar_alumno TO 'usuario_app'@'localhost';

FLUSH PRIVILEGES;

-- ============================================================
-- PRUEBAS
-- ============================================================

CALL ver_alumnos();

CALL insertar_alumno(1,'22180005',1,'2024-08-01',1);

CALL actualizar_alumno(1,2);

CALL eliminar_alumno(1);