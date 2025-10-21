-- MYSQL
USE zero;

-- 8 Tópicos
INSERT INTO topicos (nombre) VALUES
('Backend'), ('Seguridad'), ('UX/UI'), ('Base de Datos'), ('API'), ('Frontend'), ('DevOps'), ('Testing');

-- 50 usuarios
DELIMITER //
CREATE PROCEDURE seed_usuarios()
BEGIN
  DECLARE i INT DEFAULT 1;
  WHILE i <= 50 DO
    INSERT INTO usuarios (rut, nombre, email)
    VALUES (
      CONCAT(LPAD(i,8,'0'), '-', MOD(i,10)),
      CONCAT('Usuario_', i),
      CONCAT('usuario', i, '@mail.com')
    );
    SET i = i + 1;
  END WHILE;
END;
//
DELIMITER ;
CALL seed_usuarios();
DROP PROCEDURE IF EXISTS seed_usuarios;

-- 300 solicitudes de error
DELIMITER //
CREATE PROCEDURE seed_solicitudes_error()
BEGIN
  DECLARE i INT DEFAULT 1;
  DECLARE topico_id INT;
  DECLARE autor_rut VARCHAR(20);
  DECLARE estado_val VARCHAR(20);
  WHILE i <= 300 DO
    SELECT id INTO topico_id FROM topicos ORDER BY RAND() LIMIT 1;
    SELECT rut INTO autor_rut FROM usuarios ORDER BY RAND() LIMIT 1;
    SET estado_val = ELT(FLOOR(RAND()*4)+1, 'Abierto','En Progreso','Resuelto','Cerrado');
    INSERT INTO solicitudes_error (titulo, descripcion, fecha_publicacion, id_topico, autor_rut, estado)
    VALUES (
      CONCAT('Error_', i),
      CONCAT('Descripción del error número ', i),
      DATE_SUB(CURDATE(), INTERVAL FLOOR(RAND()*3000) DAY),
      topico_id,
      autor_rut,
      estado_val
    );
    SET i = i + 1;
  END WHILE;
END;
//
DELIMITER ;
CALL seed_solicitudes_error();
DROP PROCEDURE IF EXISTS seed_solicitudes_error;

-- 200 solicitudes de funcionalidad
DELIMITER //
CREATE PROCEDURE seed_solicitudes_funcionalidad()
BEGIN
  DECLARE i INT DEFAULT 1;
  DECLARE topico_id INT;
  DECLARE solicitante_rut VARCHAR(20);
  DECLARE ambiente_val VARCHAR(10);
  DECLARE estado_val VARCHAR(20);
  WHILE i <= 200 DO
    SELECT id INTO topico_id FROM topicos ORDER BY RAND() LIMIT 1;
    SELECT rut INTO solicitante_rut FROM usuarios ORDER BY RAND() LIMIT 1;
    SET ambiente_val = CASE FLOOR(RAND()*3)
                         WHEN 0 THEN 'Web'
                         WHEN 1 THEN 'Movil'
                         ELSE NULL
                       END;
    SET estado_val = ELT(FLOOR(RAND()*4)+1, 'Abierto','En Progreso','Resuelto','Cerrado');
    INSERT INTO solicitudes_funcionalidad (titulo, ambiente, resumen, id_topico, solicitante_rut, estado, fecha_creacion)
    VALUES (
      CONCAT('Funcionalidad_', i),
      ambiente_val,
      CONCAT('Resumen de funcionalidad número ', i),
      topico_id,
      solicitante_rut,
      estado_val,
      DATE_SUB(CURDATE(), INTERVAL FLOOR(RAND()*3000) DAY)
    );
    SET i = i + 1;
  END WHILE;
END;
//
DELIMITER ;
CALL seed_solicitudes_funcionalidad();
DROP PROCEDURE IF EXISTS seed_solicitudes_funcionalidad;

-- Criterios de aceptación: 3 por funcionalidad
DELIMITER //
CREATE PROCEDURE seed_criterios_aceptacion()
BEGIN
  DECLARE done INT DEFAULT 0;
  DECLARE func_id INT;
  DECLARE j INT;
  DECLARE cur CURSOR FOR SELECT id FROM solicitudes_funcionalidad;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

  OPEN cur;
  read_loop: LOOP
    FETCH cur INTO func_id;
    IF done = 1 THEN
      LEAVE read_loop;
    END IF;
    SET j = 1;
    WHILE j <= 3 DO
      INSERT INTO criterios_aceptacion (id_funcionalidad, criterio)
      VALUES (func_id, CONCAT('Criterio ', j, ' para funcionalidad ', func_id));
      SET j = j + 1;
    END WHILE;
  END LOOP;
  CLOSE cur;
END;
//
DELIMITER ;
CALL seed_criterios_aceptacion();
DROP PROCEDURE IF EXISTS seed_criterios_aceptacion;

-- 99 ingenieros
DELIMITER //
CREATE PROCEDURE seed_ingenieros()
BEGIN
  DECLARE i INT DEFAULT 1;
  WHILE i <= 99 DO
    INSERT INTO ingenieros (rut, nombre, email)
    VALUES (
      CONCAT(LPAD(i+100,8,'0'), '-', MOD(i,10)),
      CONCAT('Ingeniero_', i),
      CONCAT('ingeniero', i, '@mail.com')
    );
    SET i = i + 1;
  END WHILE;
END;
//
DELIMITER ;
CALL seed_ingenieros();
DROP PROCEDURE IF EXISTS seed_ingenieros;

-- Especialidades de ingenieros
DELIMITER //
CREATE PROCEDURE seed_ingeniero_especialidad()
BEGIN
  DECLARE done INT DEFAULT 0;
  DECLARE ing_rut VARCHAR(20);
  DECLARE topico1 INT;
  DECLARE topico2 INT;
  DECLARE cur CURSOR FOR SELECT rut FROM ingenieros;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

  OPEN cur;
  read_loop: LOOP
    FETCH cur INTO ing_rut;
    IF done = 1 THEN
      LEAVE read_loop;
    END IF;
    SELECT id INTO topico1 FROM topicos ORDER BY RAND() LIMIT 1;
    INSERT IGNORE INTO ingeniero_especialidad (rut_ingeniero, id_topico) VALUES (ing_rut, topico1);

    IF RAND() > 0.5 THEN
      -- elige distinto al primero (intentos limitados)
      SET topico2 = topico1;
      WHILE topico2 = topico1 DO
        SELECT id INTO topico2 FROM topicos ORDER BY RAND() LIMIT 1;
      END WHILE;
      INSERT IGNORE INTO ingeniero_especialidad (rut_ingeniero, id_topico) VALUES (ing_rut, topico2);
    END IF;
  END LOOP;
  CLOSE cur;
END;
//
DELIMITER ;
CALL seed_ingeniero_especialidad();
DROP PROCEDURE IF EXISTS seed_ingeniero_especialidad;

-- Asignar ingenieros a funcionalidades (hasta 3 por solicitud, hasta 20 por ingeniero)
DELIMITER //
CREATE PROCEDURE assign_ingenieros_funcionalidad()
BEGIN
  DECLARE done INT DEFAULT 0;
  DECLARE func_id INT;
  DECLARE topico_id INT;
  DECLARE ing_rut VARCHAR(20);
  DECLARE attempts INT;
  DECLARE i INT;
  DECLARE cur CURSOR FOR SELECT id FROM solicitudes_funcionalidad;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

  OPEN cur;
  read_loop: LOOP
    FETCH cur INTO func_id;
    IF done = 1 THEN
      LEAVE read_loop;
    END IF;
    SELECT id_topico INTO topico_id FROM solicitudes_funcionalidad WHERE id = func_id;
    SET i = 1;
    WHILE i <= 3 DO
      SET attempts = 0;
      SET ing_rut = NULL;
      repeat_select: LOOP
        SELECT rut_ingeniero INTO ing_rut
        FROM ingeniero_especialidad
        WHERE id_topico = topico_id
          AND (
            SELECT COUNT(*) FROM ingeniero_solicitud
            WHERE rut_ingeniero = ingeniero_especialidad.rut_ingeniero
          ) < 20
        ORDER BY RAND()
        LIMIT 1;
        IF ing_rut IS NULL THEN
          LEAVE repeat_select;
        END IF;
        IF NOT EXISTS (
          SELECT 1 FROM ingeniero_solicitud
          WHERE rut_ingeniero = ing_rut
            AND tipo_solicitud = 'funcionalidad'
            AND id_solicitud = func_id
        ) THEN
          INSERT INTO ingeniero_solicitud (rut_ingeniero, tipo_solicitud, id_solicitud)
          VALUES (ing_rut, 'funcionalidad', func_id);
          LEAVE repeat_select;
        END IF;
        SET attempts = attempts + 1;
        IF attempts > 10 THEN
          LEAVE repeat_select;
        END IF;
      END LOOP repeat_select;
      SET i = i + 1;
    END WHILE;
  END LOOP;
  CLOSE cur;
END;
//
DELIMITER ;
CALL assign_ingenieros_funcionalidad();
DROP PROCEDURE IF EXISTS assign_ingenieros_funcionalidad;

-- Asignar ingenieros a errores (similar)
DELIMITER //
CREATE PROCEDURE assign_ingenieros_error()
BEGIN
  DECLARE done INT DEFAULT 0;
  DECLARE err_id INT;
  DECLARE topico_id INT;
  DECLARE ing_rut VARCHAR(20);
  DECLARE attempts INT;
  DECLARE i INT;
  DECLARE cur CURSOR FOR SELECT id FROM solicitudes_error;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

  OPEN cur;
  read_loop: LOOP
    FETCH cur INTO err_id;
    IF done = 1 THEN
      LEAVE read_loop;
    END IF;
    SELECT id_topico INTO topico_id FROM solicitudes_error WHERE id = err_id;
    SET i = 1;
    WHILE i <= 3 DO
      SET attempts = 0;
      SET ing_rut = NULL;
      repeat_select: LOOP
        SELECT rut_ingeniero INTO ing_rut
        FROM ingeniero_especialidad
        WHERE id_topico = topico_id
          AND (
            SELECT COUNT(*) FROM ingeniero_solicitud
            WHERE rut_ingeniero = ingeniero_especialidad.rut_ingeniero
          ) < 20
        ORDER BY RAND()
        LIMIT 1;
        IF ing_rut IS NULL THEN
          LEAVE repeat_select;
        END IF;
        IF NOT EXISTS (
          SELECT 1 FROM ingeniero_solicitud
          WHERE rut_ingeniero = ing_rut
            AND tipo_solicitud = 'error'
            AND id_solicitud = err_id
        ) THEN
          INSERT INTO ingeniero_solicitud (rut_ingeniero, tipo_solicitud, id_solicitud)
          VALUES (ing_rut, 'error', err_id);
          LEAVE repeat_select;
        END IF;
        SET attempts = attempts + 1;
        IF attempts > 10 THEN
          LEAVE repeat_select;
        END IF;
      END LOOP repeat_select;
      SET i = i + 1;
    END WHILE;
  END LOOP;
  CLOSE cur;
END;
//
DELIMITER ;
CALL assign_ingenieros_error();
DROP PROCEDURE IF EXISTS assign_ingenieros_error;