USE SICE;

DELIMITER //

-- PROCEDURE 1
-- Ver todos los alumnos

CREATE PROCEDURE ver_alumnos()
BEGIN
    SELECT * FROM alumno;
END //

-- PROCEDURE 2
-- Insertar alumno

CREATE PROCEDURE insertar_alumno(
    p_id_persona INT,
    p_numero_control VARCHAR(20),
    p_id_carrera INT,
    p_fecha_ingreso DATE,
    p_semestre INT
)

BEGIN

    INSERT INTO alumno
    (id_persona,numero_control,id_carrera,fecha_ingreso,semestre_actual)

    VALUES
    (p_id_persona,p_numero_control,p_id_carrera,p_fecha_ingreso,p_semestre);

END //

-- PROCEDURE 3
-- Ver carreras

CREATE PROCEDURE ver_carreras()
BEGIN
    SELECT * FROM carrera;
END //

-- PROCEDURE 4
-- Ver inscripciones

CREATE PROCEDURE ver_inscripciones()
BEGIN
    SELECT * FROM inscripcion;
END //

-- PROCEDURE 5
-- Registrar inscripción

CREATE PROCEDURE registrar_inscripcion(
    p_id_alumno INT,
    p_id_grupo INT,
    p_fecha DATE,
    p_estatus VARCHAR(50)
)

BEGIN

    INSERT INTO inscripcion
    (id_alumno,id_grupo,fecha_inscripcion,estatus)

    VALUES
    (p_id_alumno,p_id_grupo,p_fecha,p_estatus);

END //

DELIMITER ;