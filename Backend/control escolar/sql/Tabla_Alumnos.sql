-- ============================================
-- 1. Inserción de datos iniciales
-- ============================================

-- Insertar géneros disponibles para las personas
INSERT INTO genero (nombre_genero)
VALUES ('Masculino'), ('Femenino');

-- Insertar personas (cada persona debe tener un género válido)
INSERT INTO persona (nombre, apellido_paterno, apellido_materno, curp, fecha_nacimiento, id_genero)
VALUES ('Carlos Alberto','Pérez','Téllez','PEPJ800101HDFRRN09','1980-01-01',1);

INSERT INTO persona (nombre, apellido_paterno, apellido_materno, curp, fecha_nacimiento, id_genero)
VALUES ('María','García','Hernández','GAHM950505MDFRRN08','1995-05-05',2);

-- Insertar un departamento (padre de carrera)
INSERT INTO departamento (nombre, descripcion)
VALUES ('Sistemas','Departamento de Sistemas');

-- Insertar nivel de carrera (padre de carrera)
INSERT INTO nivel_carrera (nombre_nivel)
VALUES ('Licenciatura');

-- Insertar carrera (requiere departamento y nivel existentes)
INSERT INTO carrera (nombre, id_departamento, id_nivel)
VALUES ('Ingeniería Industrial',1,1);

-- ============================================
-- 2. Inserción de alumnos
-- ============================================

-- Insertar alumno vinculado a persona y carrera válidas
INSERT INTO alumno (id_persona, numero_control, id_carrera, fecha_ingreso, semestre_actual)
VALUES (1,'22180010',1,'2024-08-01',1);

INSERT INTO alumno (id_persona, numero_control, id_carrera, fecha_ingreso, semestre_actual)
VALUES (2,'22180011',1,'2024-08-01',1);

-- ============================================
-- 3. CRUD sobre alumno
-- ============================================

-- CREATE: insertar un nuevo alumno
INSERT INTO alumno (id_persona, numero_control, id_carrera, fecha_ingreso, semestre_actual)
VALUES (2,'22180012',1,'2024-08-01',1);

-- READ: consultar todos los alumnos
SELECT * FROM alumno;

-- UPDATE: actualizar semestre de un alumno
UPDATE alumno
SET semestre_actual = 2
WHERE numero_control = '22180012';

-- DELETE: eliminar un alumno
DELETE FROM alumno
WHERE numero_control = '22180012';

-- ============================================
-- 4. Procedimientos almacenados
-- ============================================

-- Procedimiento para consultar alumnos
DELIMITER //
CREATE PROCEDURE ver_alumnos()
BEGIN
    SELECT * FROM alumno;
END //
DELIMITER ;

-- Procedimiento para insertar alumnos
DELIMITER //
CREATE PROCEDURE insertar_alumno(
    p_id_persona INT,
    p_numero_control VARCHAR(20),
    p_id_carrera INT,
    p_fecha_ingreso DATE,
    p_semestre INT
)
BEGIN
    INSERT INTO alumno (id_persona, numero_control, id_carrera, fecha_ingreso, semestre_actual)
    VALUES (p_id_persona, p_numero_control, p_id_carrera, p_fecha_ingreso, p_semestre);
END //
DELIMITER ;

-- ============================================
-- 5. Seguridad
-- ============================================

-- Crear usuario específico para la aplicación
CREATE USER 'usuario_app'@'localhost' IDENTIFIED BY '123456';

-- Dar permisos solo a procedimientos, no a tablas
GRANT EXECUTE ON PROCEDURE SICE.ver_alumnos TO 'usuario_app'@'localhost';
GRANT EXECUTE ON PROCEDURE SICE.insertar_alumno TO 'usuario_app'@'localhost';

-- Refrescar privilegios para aplicar cambios
FLUSH PRIVILEGES;