-- phpMyAdmin SQL Dump
-- version 4.6.6
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 14-11-2025 a las 21:51:53
-- Versión del servidor: 5.7.17-log
-- Versión de PHP: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `dbsupervision`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`system`@`127.0.0.1` PROCEDURE `sp_preguntas_agregar` (IN `p_encuesta_id` BIGINT, IN `p_enunciado` TEXT, IN `p_tipo` VARCHAR(20), IN `p_ponderacion` DECIMAL(5,2), IN `p_orden` INT, IN `p_activo` TINYINT, OUT `p_id_nuevo` BIGINT)  BEGIN
    INSERT INTO preguntas (encuesta_id, enunciado, tipo, ponderacion, orden, activo)
    VALUES (p_encuesta_id, p_enunciado, p_tipo, COALESCE(p_ponderacion,1.00), p_orden, COALESCE(p_activo,1));
    SET p_id_nuevo = LAST_INSERT_ID();
END$$

CREATE DEFINER=`system`@`127.0.0.1` PROCEDURE `sp_preguntas_eliminar_logico` (IN `p_id` BIGINT)  BEGIN
    UPDATE preguntas SET activo = 0 WHERE id = p_id;
END$$

CREATE DEFINER=`system`@`127.0.0.1` PROCEDURE `sp_preguntas_modificar` (IN `p_id` BIGINT, IN `p_encuesta_id` BIGINT, IN `p_enunciado` TEXT, IN `p_tipo` VARCHAR(20), IN `p_ponderacion` DECIMAL(5,2), IN `p_orden` INT, IN `p_activo` TINYINT)  BEGIN
    UPDATE preguntas
       SET encuesta_id = p_encuesta_id,
           enunciado   = p_enunciado,
           tipo        = p_tipo,
           ponderacion = COALESCE(p_ponderacion,1.00),
           orden       = p_orden,
           activo      = COALESCE(p_activo,1)
     WHERE id = p_id;
END$$

CREATE DEFINER=`system`@`127.0.0.1` PROCEDURE `sp_respuestas_agregar` (IN `p_pregunta_id` BIGINT, IN `p_respuesta_texto` TEXT, IN `p_respuesta_numero` DECIMAL(12,4), IN `p_es_correcta` TINYINT, IN `p_activo` TINYINT, OUT `p_id_nuevo` BIGINT)  BEGIN
    IF NOT EXISTS (SELECT 1 FROM preguntas WHERE id = p_pregunta_id) THEN
        SET p_id_nuevo = NULL;
    ELSE
        INSERT INTO respuestas (pregunta_id, respuesta_texto, respuesta_numero, es_correcta, activo)
        VALUES (p_pregunta_id, p_respuesta_texto, p_respuesta_numero, p_es_correcta, COALESCE(p_activo,1));
        SET p_id_nuevo = LAST_INSERT_ID();
    END IF;
END$$

CREATE DEFINER=`system`@`127.0.0.1` PROCEDURE `sp_respuestas_alumnos_agregar` (IN `p_alumno_user_id` BIGINT, IN `p_encuesta_id` BIGINT, IN `p_pregunta_id` BIGINT, IN `p_respuesta_id` BIGINT, IN `p_respuesta_texto` TEXT, IN `p_respuesta_numero` DECIMAL(12,4), OUT `p_id_nuevo` BIGINT)  BEGIN
    DECLARE v_es_correcta TINYINT(1);
    IF p_respuesta_id IS NOT NULL THEN
        SELECT es_correcta INTO v_es_correcta FROM respuestas WHERE id = p_respuesta_id LIMIT 1;
    ELSE
        SET v_es_correcta = NULL;
    END IF;

    INSERT INTO respuestas_alumnos (
        alumno_user_id, encuesta_id, pregunta_id,
        respuesta_id, respuesta_texto, respuesta_numero,
        es_correcta, activo
    ) VALUES (
        p_alumno_user_id, p_encuesta_id, p_pregunta_id,
        p_respuesta_id, p_respuesta_texto, p_respuesta_numero,
        v_es_correcta, 1
    );

    SET p_id_nuevo = LAST_INSERT_ID();
END$$

CREATE DEFINER=`system`@`127.0.0.1` PROCEDURE `sp_respuestas_eliminar_logico` (IN `p_id` BIGINT)  BEGIN
    UPDATE respuestas SET activo = 0 WHERE id = p_id;
END$$

CREATE DEFINER=`system`@`127.0.0.1` PROCEDURE `sp_respuestas_modificar` (IN `p_id` BIGINT, IN `p_pregunta_id` BIGINT, IN `p_respuesta_texto` TEXT, IN `p_respuesta_numero` DECIMAL(12,4), IN `p_es_correcta` TINYINT, IN `p_activo` TINYINT)  BEGIN
    IF EXISTS (SELECT 1 FROM preguntas WHERE id = p_pregunta_id) THEN
        UPDATE respuestas
           SET pregunta_id      = p_pregunta_id,
               respuesta_texto  = p_respuesta_texto,
               respuesta_numero = p_respuesta_numero,
               es_correcta      = p_es_correcta,
               activo           = COALESCE(p_activo,1)
         WHERE id = p_id;
    END IF;
END$$

CREATE DEFINER=`system`@`127.0.0.1` PROCEDURE `sp_usuarios_delete_logico_min` (IN `p_id` BIGINT, OUT `p_rows_affected` INT)  BEGIN
  UPDATE usuarios SET activo = 0 WHERE id = p_id AND activo = 1;
  SET p_rows_affected = ROW_COUNT();
END$$

CREATE DEFINER=`system`@`127.0.0.1` PROCEDURE `sp_usuarios_insert_min` (IN `p_codigo` VARCHAR(40), IN `p_nombres` VARCHAR(120), IN `p_apellidos` VARCHAR(120), IN `p_grado_id` BIGINT, IN `p_institucion_id` BIGINT, IN `p_rol` VARCHAR(16), IN `p_password_hash` VARCHAR(255), IN `p_seccion` INT, OUT `p_id` BIGINT)  BEGIN
  INSERT INTO usuarios (
    codigo, nombres, apellidos, grado_id, institucion_id, rol, password_hash, seccion, activo
  ) VALUES (
    p_codigo, p_nombres, p_apellidos, p_grado_id, p_institucion_id, p_rol, p_password_hash, p_seccion, 1
  );
  SET p_id = LAST_INSERT_ID();
END$$

CREATE DEFINER=`system`@`127.0.0.1` PROCEDURE `sp_usuarios_update_min` (IN `p_id` BIGINT, IN `p_codigo` VARCHAR(40), IN `p_nombres` VARCHAR(120), IN `p_apellidos` VARCHAR(120), IN `p_grado_id` BIGINT, IN `p_institucion_id` BIGINT, IN `p_rol` VARCHAR(16), IN `p_password_hash` VARCHAR(255), IN `p_seccion` INT, OUT `p_rows_affected` INT)  BEGIN
  UPDATE usuarios
     SET codigo         = COALESCE(p_codigo, codigo),
         nombres        = COALESCE(p_nombres, nombres),
         apellidos      = COALESCE(p_apellidos, apellidos),
         grado_id       = COALESCE(p_grado_id, grado_id),
         institucion_id = COALESCE(p_institucion_id, institucion_id),
         rol            = COALESCE(p_rol, rol),
         password_hash  = COALESCE(p_password_hash, password_hash),
         seccion        = COALESCE(p_seccion, seccion)
   WHERE id = p_id
     AND activo = 1;
  SET p_rows_affected = ROW_COUNT();
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificaciones`
--

CREATE TABLE `calificaciones` (
  `id` bigint(20) NOT NULL,
  `alumno_user_id` bigint(20) NOT NULL,
  `curso_id` bigint(20) NOT NULL,
  `institucion_id` bigint(20) DEFAULT NULL,
  `grado_id` bigint(20) NOT NULL,
  `periodo` varchar(48) NOT NULL DEFAULT '',
  `puntaje` decimal(6,2) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `calificaciones`
--

INSERT INTO `calificaciones` (`id`, `alumno_user_id`, `curso_id`, `institucion_id`, `grado_id`, `periodo`, `puntaje`, `activo`) VALUES
(1, 3, 1, 12, 1, '2025-10', '70.00', 1),
(3, 72, 2, 6, 3, '2025-10', '40.00', 1),
(4, 80, 2, 6, 3, '2025-10', '45.00', 1),
(5, 87, 2, 6, 3, '2025-10', '25.00', 1),
(6, 78, 2, 6, 3, '2025-10', '45.00', 1),
(7, 79, 2, 6, 3, '2025-10', '40.00', 1),
(8, 76, 2, 6, 3, '2025-10', '40.00', 1),
(9, 88, 2, 6, 3, '2025-10', '35.00', 1),
(10, 86, 2, 6, 3, '2025-10', '20.00', 1),
(11, 90, 2, 6, 3, '2025-10', '50.00', 1),
(12, 75, 2, 6, 3, '2025-10', '40.00', 1),
(13, 73, 2, 6, 3, '2025-10', '40.00', 1),
(14, 91, 2, 6, 3, '2025-10', '40.00', 1),
(15, 85, 2, 6, 3, '2025-10', '45.00', 1),
(16, 82, 2, 6, 3, '2025-10', '40.00', 1),
(17, 93, 2, 6, 3, '2025-10', '25.00', 1),
(18, 77, 2, 6, 3, '2025-10', '45.00', 1),
(19, 81, 2, 6, 3, '2025-10', '15.00', 1),
(20, 89, 2, 6, 3, '2025-10', '35.00', 1),
(21, 84, 2, 6, 3, '2025-10', '55.00', 1),
(22, 92, 2, 6, 3, '2025-10', '30.00', 1),
(23, 83, 2, 6, 3, '2025-10', '20.00', 1),
(44, 68, 2, 6, 2, '2025-10', '40.00', 1),
(45, 44, 2, 6, 2, '2025-10', '65.00', 1),
(46, 57, 2, 6, 2, '2025-10', '50.00', 1),
(47, 45, 2, 6, 2, '2025-10', '50.00', 1),
(48, 66, 2, 6, 2, '2025-10', '50.00', 1),
(49, 61, 2, 6, 2, '2025-10', '45.00', 1),
(50, 47, 2, 6, 2, '2025-10', '35.00', 1),
(51, 62, 2, 6, 2, '2025-10', '65.00', 1),
(52, 70, 1, 6, 2, '2025-10', '40.00', 1),
(53, 49, 2, 6, 2, '2025-10', '45.00', 1),
(55, 94, 2, 6, 2, '2025-10', '70.00', 1),
(58, 63, 2, 6, 2, '2025-10', '35.00', 1),
(59, 50, 2, 6, 2, '2025-10', '65.00', 1),
(61, 48, 1, 6, 2, '2025-10', '70.00', 1),
(63, 56, 1, 6, 2, '2025-10', '25.00', 1),
(64, 46, 1, 6, 2, '2025-10', '45.00', 1),
(65, 64, 1, 6, 2, '2025-10', '50.00', 1),
(66, 43, 1, 6, 2, '2025-10', '65.00', 1),
(68, 59, 2, 6, 2, '2025-10', '60.00', 1),
(69, 71, 1, 6, 2, '2025-10', '70.00', 1),
(70, 51, 1, 6, 2, '2025-10', '65.00', 1),
(71, 65, 2, 6, 2, '2025-10', '50.00', 1),
(73, 55, 1, 6, 2, '2025-10', '30.00', 1),
(74, 42, 1, 6, 2, '2025-10', '20.00', 1),
(80, 54, 1, 6, 2, '2025-10', '25.00', 1),
(81, 58, 1, 6, 2, '2025-10', '55.00', 1),
(82, 60, 1, 6, 2, '2025-10', '40.00', 1),
(83, 41, 1, 6, 2, '2025-10', '50.00', 1),
(86, 53, 1, 6, 2, '2025-10', '25.00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos`
--

CREATE TABLE `cursos` (
  `id` bigint(20) NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `area` varchar(80) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `cursos`
--

INSERT INTO `cursos` (`id`, `nombre`, `area`, `activo`) VALUES
(1, 'Matemáticas', 'Matemáticas', 1),
(2, 'Tecnología', 'Tecnología', 1);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `dim_encuestas`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `dim_encuestas` (
`id` bigint(20)
,`titulo` varchar(160)
,`curso_id` bigint(20)
,`curso_nombre` varchar(80)
,`grado_id` bigint(20)
,`grado_nombre` varchar(60)
,`institucion_id` bigint(20)
,`estado` enum('BORRADOR','ACTIVA','CERRADA')
,`fecha_inicio` timestamp
,`fecha_fin` timestamp
,`creado_por` bigint(20)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `dim_usuarios`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `dim_usuarios` (
`id` bigint(20)
,`nombres` varchar(120)
,`apellidos` varchar(120)
,`rol` enum('ADMIN','DIRECTOR','DOCENTE','ALUMNO')
,`seccion` int(11)
,`institucion_id` bigint(20)
,`grado_id` bigint(20)
,`codigo` varchar(40)
,`creado_en` timestamp
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `distritos`
--

CREATE TABLE `distritos` (
  `id` bigint(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `distritos`
--

INSERT INTO `distritos` (`id`, `nombre`, `activo`) VALUES
(1, 'Centro', 1),
(2, 'Norte', 1),
(3, 'Sur', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `encuestas`
--

CREATE TABLE `encuestas` (
  `id` bigint(20) NOT NULL,
  `titulo` varchar(160) NOT NULL,
  `curso_id` bigint(20) NOT NULL,
  `grado_id` bigint(20) NOT NULL,
  `unidad_numero` tinyint(1) NOT NULL DEFAULT '1',
  `institucion_id` bigint(20) DEFAULT NULL,
  `descripcion` text,
  `fecha_inicio` timestamp NULL DEFAULT NULL,
  `fecha_fin` timestamp NULL DEFAULT NULL,
  `estado` enum('BORRADOR','ACTIVA','CERRADA') DEFAULT 'ACTIVA',
  `creado_por` bigint(20) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `encuestas`
--

INSERT INTO `encuestas` (`id`, `titulo`, `curso_id`, `grado_id`, `unidad_numero`, `institucion_id`, `descripcion`, `fecha_inicio`, `fecha_fin`, `estado`, `creado_por`, `activo`) VALUES
(1, 'Matemáticas, Primero Básico', 1, 1, 4, 13, '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'CERRADA', 0, 1),
(2, 'Matemáticas, Segundo Básico', 1, 2, 4, 13, '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'CERRADA', 0, 1),
(3, 'Matemáticas, Tercero Básico', 1, 3, 4, 13, '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'CERRADA', 0, 1),
(4, 'Tecnología, Primero Básico', 2, 1, 4, 13, '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'CERRADA', 0, 1),
(5, 'Tecnología, Segundo Básico', 2, 2, 4, 13, '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'CERRADA', 0, 1),
(6, 'Tecnología, Tercero Básico', 2, 3, 4, 13, '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'CERRADA', 0, 1),
(7, 'Unidad Matemáticas, Tercero Básico', 1, 3, 4, 13, '', '2025-10-24 14:00:00', '2025-10-30 18:00:00', 'CERRADA', 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grados`
--

CREATE TABLE `grados` (
  `id` bigint(20) NOT NULL,
  `nombre` varchar(60) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `grados`
--

INSERT INTO `grados` (`id`, `nombre`, `activo`) VALUES
(1, 'Primero Básico', 1),
(2, 'Segundo Básico', 1),
(3, 'Tercero Básico', 1),
(4, 'TI', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `instituciones`
--

CREATE TABLE `instituciones` (
  `id` bigint(20) NOT NULL,
  `distrito_id` bigint(20) NOT NULL,
  `nombre` varchar(160) NOT NULL,
  `codigo` varchar(40) DEFAULT NULL,
  `tipo` varchar(60) DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `instituciones`
--

INSERT INTO `instituciones` (`id`, `distrito_id`, `nombre`, `codigo`, `tipo`, `direccion`, `activo`) VALUES
(1, 1, 'INEBT Las Delicias', '09-04-0027-45', 'Público', 'San Carlos Sija', 1),
(2, 1, 'INEBT Cipresales', '09-04-3226-45', 'Público', 'San Carlos Sija', 1),
(3, 1, 'INEBT La Libertad', '09-04-3225-45', 'Público', 'San Carlos Sija', 1),
(4, 1, 'INEB Santa Elena', '09-04-0019-45', 'Público', 'San Carlos Sija', 1),
(5, 1, 'INEB Chiquival', '09-04-0030-45', 'Público', 'San Carlos Sija', 1),
(6, 1, 'INEB Nuevo San Antonio', '09-04-0047-45', 'Público', 'San Carlos Sija', 1),
(7, 2, 'IEBC Aldea Calel', '09-04-0867-45', 'Público', 'San Carlos Sija', 1),
(8, 1, 'IEBC Caserío la Fuente', '09-04-3631-45', 'Público', 'San Carlos Sija', 1),
(9, 1, 'IEBC Recuerdo a Barrios', '09-04-4079-45', 'Público', 'San Carlos Sija', 1),
(10, 1, 'INEBOO San Carlos Sija', '09-04-0231-45', 'Público', 'San Carlos Sija', 1),
(11, 1, 'NUFED No. 6', '09-04-0232-45', 'Público', 'San Carlos Sija', 1),
(12, 1, 'Colegio Cristiano Montecristo', '09-04-0038-45', 'Privado', 'San Carlos Sija', 1),
(13, 1, 'Supervisión Educativa', '09-04-01', 'Público', 'San Carlos Sija', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materiales`
--

CREATE TABLE `materiales` (
  `id` bigint(20) NOT NULL,
  `docente_user_id` bigint(20) NOT NULL,
  `curso_id` bigint(20) NOT NULL,
  `grado_id` bigint(20) NOT NULL,
  `unidad_numero` tinyint(3) UNSIGNED DEFAULT NULL,
  `unidad_titulo` varchar(120) DEFAULT NULL,
  `anio_lectivo` year(4) DEFAULT NULL,
  `titulo` varchar(200) NOT NULL,
  `descripcion` text,
  `publicado_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `activo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `materiales`
--

INSERT INTO `materiales` (`id`, `docente_user_id`, `curso_id`, `grado_id`, `unidad_numero`, `unidad_titulo`, `anio_lectivo`, `titulo`, `descripcion`, `publicado_at`, `activo`) VALUES
(1, 1, 1, 1, 1, NULL, NULL, 'Prueba', 'Bienvenida', '2025-10-27 12:41:05', 0),
(2, 1, 1, 1, 1, NULL, NULL, 'Prueba', 'Bienvenida', '2025-10-27 12:41:46', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `material_archivos`
--

CREATE TABLE `material_archivos` (
  `id` bigint(20) NOT NULL,
  `material_id` bigint(20) NOT NULL,
  `url` varchar(500) NOT NULL,
  `nombre_archivo` varchar(255) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `material_archivos`
--

INSERT INTO `material_archivos` (`id`, `material_id`, `url`, `nombre_archivo`, `activo`) VALUES
(1, 1, 'uploads/materiales/1/68ff686112fe29.22443757_Bienvenidos.pdf', 'Bienvenidos.pdf', 1),
(2, 2, 'uploads/materiales/2/68ff688a923f92.83515982_Bienvenidos.pdf', 'Bienvenidos.pdf', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE `password_resets` (
  `id` bigint(20) NOT NULL,
  `usuario_id` bigint(20) NOT NULL,
  `token` char(64) NOT NULL,
  `expira_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `usado_en` timestamp NULL DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `preguntas`
--

CREATE TABLE `preguntas` (
  `id` bigint(20) NOT NULL,
  `encuesta_id` bigint(20) NOT NULL,
  `enunciado` text NOT NULL,
  `tipo` enum('opcion_unica','opcion_multiple','abierta','numerica') NOT NULL,
  `ponderacion` decimal(5,2) DEFAULT '1.00',
  `orden` int(11) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `preguntas`
--

INSERT INTO `preguntas` (`id`, `encuesta_id`, `enunciado`, `tipo`, `ponderacion`, `orden`, `activo`) VALUES
(1, 1, 'El doble de un número es:', 'opcion_unica', '5.00', 3, 1),
(2, 1, 'Un número disminuido en cinco unidades.', 'opcion_unica', '5.00', 3, 1),
(3, 1, 'Un número aumenta en cuatro unidades.', 'opcion_unica', '5.00', 3, 1),
(4, 1, 'El producto de dos números.', 'opcion_unica', '5.00', 4, 1),
(5, 1, 'Los elementos básicos en geometría son; El punto, la recta y el plano.', 'opcion_unica', '5.00', 2, 1),
(6, 1, 'Una recta se conforma de una sucesión de puntos.', 'opcion_unica', '5.00', 2, 1),
(7, 1, 'Son dos rectas que se ubican en el mismo plano cartesiano y que nunca se intersectan, es decir que no se cruzan la una con la otra.', 'opcion_unica', '5.00', 3, 1),
(8, 1, 'Son dos rectas que se intersectan, es decir que se cruzan entre ellas.', 'opcion_unica', '5.00', 3, 1),
(9, 1, '¿Como podemos calcular el perímetro de un polígono regular?', 'opcion_unica', '5.00', 3, 1),
(10, 1, 'Seleccione la formula correcta para poder calcular el área de un polígono regular.', 'opcion_unica', '5.00', 3, 1),
(11, 1, 'Seleccione la manera correcta para representar una expresión abierta.', 'opcion_unica', '5.00', 4, 1),
(12, 1, 'Una proposición puede ser:', 'opcion_unica', '5.00', 3, 1),
(13, 1, '¿Una proposición compuesta prácticamente es la unión de dos o más proposiciones simples?', 'opcion_unica', '5.00', 2, 1),
(14, 1, 'Determine el valor de verdad para lo siguiente:  3+4 = 6', 'opcion_unica', '5.00', 2, 1),
(15, 1, '¿Cuál de los siguientes representa una proposición simple?', 'opcion_unica', '5.00', 3, 1),
(16, 1, 'Seleccione el conjunto de números impares.', 'opcion_unica', '5.00', 3, 1),
(17, 1, '¿Cuál es la variable para la expresión 3+2y = 23?', 'opcion_unica', '5.00', 3, 1),
(18, 1, '¿Cuál es la variable para la expresión 2b-5 = 5?', 'opcion_unica', '5.00', 3, 1),
(19, 1, 'Resuelva la siguiente ecuación 5x = 8x-15 y seleccione el valor correcto para x.', 'opcion_unica', '5.00', 3, 1),
(20, 1, 'Resuelva la siguiente ecuación y-5 = 3y-25, seleccione el valor correcto para y.', 'opcion_unica', '5.00', 4, 1),
(21, 1, 'Resuelva la siguiente ecuación 5x+6=10x+5, seleccione el valor correcto para x.', 'opcion_unica', '5.00', 4, 1),
(22, 1, '¿Los números enteros son aquellos que no tienen una parte fraccionaria o decimal?', 'opcion_unica', '5.00', 2, 1),
(23, 1, 'Los números enteros son todos aquellos que tienen una parte fraccionaria o decimal.', 'opcion_unica', '5.00', 2, 1),
(24, 1, 'seleccione los números enteros correctos.', 'opcion_unica', '5.00', 3, 1),
(25, 1, 'En un recta numerica si el punto de partida es -3 y te mueves 6 espacios a la derecha, a que valor llegas.', 'opcion_unica', '5.00', 3, 1),
(26, 1, 'en una recta numérica si el punto de partida es 1 y te mueves 4 espacios a la derecha, a que valor llegas.', 'opcion_unica', '5.00', 3, 1),
(27, 1, 'Determina el valor de verdad o falos para la siguiente relación 5>8', 'opcion_unica', '5.00', 2, 1),
(28, 1, 'Determina el valor de verdad o falso para la siguiente relación 5<8', 'opcion_unica', '5.00', 2, 1),
(29, 1, 'Determina el valor de verdad o falso para la siguiente relación -2>-8', 'opcion_unica', '5.00', 2, 1),
(30, 1, '¿Cuál es valor absoluto de |-2|?', 'opcion_unica', '5.00', 3, 1),
(31, 1, '¿Cuál es valor absoluto de |6|?', 'opcion_unica', '5.00', 3, 1),
(32, 1, 'Resuelva el siguiente ejercicio 20-2*5 aplicando jerarquía de operaciones, seleccione su respuesta correcta.', 'opcion_unica', '5.00', 4, 1),
(33, 1, 'Resuelva el siguiente ejercicio 5*5+8 aplicando jerarquía de operaciones, seleccione su respuesta correcta.', 'opcion_unica', '5.00', 4, 1),
(34, 1, 'Una proposición es una igualdad entre dos razones.', 'opcion_unica', '5.00', 2, 1),
(35, 1, 'Selecciona una forma correcta para recolectar datos.', 'opcion_unica', '5.00', 3, 1),
(36, 1, 'Selecciona una forma correcta para recolectar datos.', 'opcion_unica', '5.00', 3, 1),
(37, 1, 'Es una parte mínima o subconjunto que se selecciona para realizar el estudio de una población.', 'opcion_unica', '5.00', 3, 1),
(38, 1, 'Seleccione la manera correcta para representar una población.', 'opcion_unica', '5.00', 2, 1),
(39, 1, 'Seleccione la manera correcta de representar una muestra.', 'opcion_unica', '5.00', NULL, 0),
(40, 1, 'Seleccione la manera correcta de representar una muestra.', 'opcion_unica', '5.00', 2, 1),
(41, 1, 'En la recta numérica los números positivos van hacia la derecha y los negativos hacia la izquierda.', 'opcion_unica', '5.00', 2, 1),
(42, 3, '¿Cuál es el desarrollo (a + b)3?', 'opcion_unica', '5.00', 4, 1),
(43, 3, '¿Cómo se denomina (a – b)3?', 'opcion_unica', '5.00', 4, 1),
(44, 3, '¿Qué herramienta se utiliza para desarrollar (a + b)n', 'opcion_unica', '5.00', 3, 1),
(45, 3, '¿Qué figura matemática representa el Triángulo de Pascal?', 'opcion_unica', '5.00', 4, 1),
(46, 3, '¿Cuál es el resultado de simplificar (6x/9x)?', 'opcion_unica', '5.00', 4, 1),
(47, 3, 'Si (a2b / ab2), ¿a qué equivale?', 'opcion_unica', '5.00', 3, 1),
(48, 3, 'La simplificación de (x2 – 9)/(x -3) es:', 'opcion_unica', '5.00', 3, 1),
(49, 3, 'La simplificación de (x2 + 5x)/(x) es:', 'opcion_unica', '5.00', 4, 1),
(50, 3, '¿Cuál es el factor común de 12x2 + 18x?', 'opcion_unica', '5.00', 4, 1),
(51, 3, '¿Cómo se factoriza a2 – b2?', 'opcion_unica', '5.00', 0, 0),
(52, 3, '¿Cómo se factoriza a2 – b2?', 'opcion_unica', '5.00', 3, 1),
(53, 3, 'Factorizar (x / 2) * (4 / x)', 'opcion_unica', '5.00', 4, 1),
(54, 3, 'El perímetro de un círculo se llama:', 'opcion_unica', '5.00', 3, 1),
(55, 3, 'Un ángulo recto mide:', 'opcion_unica', '5.00', 4, 1),
(56, 3, 'El segmento que une el centro con un punto de circunferencia es:', 'opcion_unica', '5.00', 4, 1),
(57, 3, '¿Cómo se llama la figura con 6 caras cuadradas?', 'opcion_unica', '5.00', 4, 1),
(58, 3, 'El volumen de un cubo se calcula con:', 'opcion_unica', '5.00', 4, 1),
(59, 3, 'Un triángulo obtusángulo tiene:', 'opcion_unica', '5.00', 3, 1),
(60, 3, 'La Fórmula del Teorema del Coseno es:', 'opcion_unica', '5.00', 3, 1),
(61, 3, 'La Intersección de A y B es:', 'opcion_unica', '5.00', 4, 1),
(62, 3, 'La unión de A y B es:', 'opcion_unica', '5.00', 2, 1),
(63, 3, 'Si P = F y Q = F, P V Q es:', 'opcion_unica', '5.00', 2, 1),
(64, 3, 'Demostraciones: Un anunciado aceptado sin demostración se llama:', 'opcion_unica', '5.00', 3, 1),
(65, 3, 'Una preposición que se deduce de un teorema se llama:', 'opcion_unica', '5.00', 3, 1),
(66, 3, 'El complemento de A son los elementos:', 'opcion_unica', '5.00', 2, 1),
(67, 3, 'El producto cartesiano AxB está formado por:', 'opcion_unica', '5.00', 3, 1),
(68, 3, 'Una función es biyectiva si es:', 'opcion_unica', '5.00', 3, 1),
(69, 3, 'La gráfica de y = x2 es:', 'opcion_unica', '5.00', 3, 1),
(70, 3, 'El discriminante de ax2 +´bx + c es:', 'opcion_unica', '5.00', 3, 1),
(71, 3, 'El método de reducción consiste en:', 'opcion_unica', '5.00', 3, 1),
(72, 3, 'Un sistema incompatible es:', 'opcion_unica', '5.00', 3, 1),
(73, 3, 'La gráfica de y = x2 ≥ 0 es:', 'opcion_unica', '5.00', 3, 1),
(74, 3, '√2 es:', 'opcion_unica', '5.00', 4, 1),
(75, 3, 'El conjunto de los números reales incluye:', 'opcion_unica', '5.00', 4, 1),
(76, 3, 'El número 3+4i en el plano se ubica en:', 'opcion_unica', '5.00', 4, 1),
(77, 3, 'Medidas de dispersión: ¿Cuál es el rango de 2,4,6,8?', 'opcion_unica', '5.00', 3, 1),
(78, 3, 'Probabilidad de obtener par en un dado:', 'opcion_unica', '5.00', 4, 1),
(79, 3, 'Permutaciones de 4 elementos:', 'opcion_unica', '5.00', 3, 1),
(80, 3, 'Combinaciones de 3 elementos de un conjunto de 5:', 'opcion_unica', '5.00', 3, 1),
(81, 3, 'Números complejos: Determine el módulo de 3 + 4i es:', 'opcion_unica', '5.00', 4, 1),
(82, 3, 'Operaciones con complejos: (2+3i) + (4+5i)', 'opcion_unica', '5.00', 4, 1),
(83, 4, '¿Qué invento permitió la era de la información?', 'opcion_unica', '5.00', 3, 1),
(84, 4, '¿Cuál es una tecnología moderna que ha cambiado la forma en la que trabajamos?', 'opcion_unica', '5.00', 4, 1),
(85, 4, '¿Qué tecnología ha sido clave para los teléfonos inteligentes?', 'opcion_unica', '5.00', 3, 1),
(86, 4, '¿Cuál de estas tecnologías ha permitido el almacenamiento masivo de información?', 'opcion_unica', '5.00', 2, 1),
(87, 4, '¿Qué ha facilitado el trabajo remoto en los últimos años?', 'opcion_unica', '5.00', 3, 1),
(88, 4, '¿Qué cambio ha hecho posible la educación virtual?', 'opcion_unica', '5.00', 3, 1),
(89, 4, '¿Cuál es la tecnología emergente actualmente?', 'opcion_unica', '5.00', 3, 1),
(90, 4, '¿Qué tecnología utilizamos hoy para pagar digitalmente?', 'opcion_unica', '5.00', 3, 1),
(91, 4, '¿Cuál es un efecto negativo del uso excesivo de la tecnología?', 'opcion_unica', '5.00', 3, 1),
(92, 4, '¿Cuál ha sido un cambio reciente en la forma en que se consumen noticias?', 'opcion_unica', '5.00', 3, 1),
(93, 4, 'La tecnología solo se aplica en informática.', 'opcion_unica', '5.00', 2, 1),
(94, 4, 'La inteligencia artificial es una tecnología moderna que imita el pensamiento humano.', 'opcion_unica', '5.00', 2, 1),
(95, 4, 'El uso excesivo de la tecnología puede generar dependencia.', 'opcion_unica', '5.00', 2, 1),
(96, 4, 'La energía solar es una tecnología limpia y renovable.', 'opcion_unica', '5.00', 2, 1),
(97, 4, '¿Cuál es el componente principal que ejecuta las instrucciones de una computadora?', 'opcion_unica', '5.00', 3, 1),
(98, 4, '¿Qué componentes almacenan datos de forma permanente?', 'opcion_unica', '5.00', 3, 1),
(99, 4, '¿Cuál es la función de la memoria RAM?', 'opcion_unica', '5.00', 3, 1),
(100, 4, '¿Qué parte de la computadora muestra la información visual al usuario?', 'opcion_unica', '5.00', 4, 1),
(101, 4, '¿Qué componente convierte la corriente alterna en continua para alimentar los demás componentes?', 'opcion_unica', '5.00', 3, 1),
(102, 4, '¿Qué componente permite la interacción del usuario con el sistema escribiendo texto?', 'opcion_unica', '5.00', 2, 1),
(103, 4, '¿Qué dispositivo se utiliza para mover el puntero en la pantalla?', 'opcion_unica', '5.00', 4, 1),
(104, 4, '¿Qué es la placa base (motherboard)?', 'opcion_unica', '5.00', 3, 1),
(105, 4, '¿Qué componente se encarga de procesar gráficos complejos en videojuegos o diseño?', 'opcion_unica', '5.00', 3, 1),
(106, 4, '¿Qué unidad se usa común mente para medir la capacidad de almacenamiento?', 'opcion_unica', '5.00', 4, 1),
(107, 4, '¿Cuál de estos componentes es considerado un periférico de entrada?', 'opcion_unica', '5.00', 3, 1),
(108, 4, '¿Cuál de estos componentes es un periférico de salida?', 'opcion_unica', '5.00', 4, 1),
(109, 4, '¿Qué componente permite la conexión de dispositivos externos como memorias USB?', 'opcion_unica', '5.00', 3, 1),
(110, 4, '¿Qué componente ayuda a enfriar en procesador?', 'opcion_unica', '5.00', 4, 1),
(111, 4, '¿Dónde se instalan los sistemas operativos y programas?', 'opcion_unica', '5.00', 3, 1),
(112, 4, '¿Qué componente permite la conexión a redes inalámbricas?', 'opcion_unica', '5.00', 3, 1),
(113, 4, '¿Qué comando se usa para copiar un texto seleccionado?', 'opcion_unica', '5.00', 4, 1),
(114, 4, '¿Qué comando permite cortar un texto seleccionado?', 'opcion_unica', '5.00', 4, 1),
(115, 4, '¿Qué hace el comando CTRL + Z?', 'opcion_unica', '5.00', 3, 1),
(116, 4, '¿Qué hace el comando CTRL + P?', 'opcion_unica', '5.00', 3, 1),
(117, 4, '¿Qué función tiene la tecla ESC?', 'opcion_unica', '5.00', 4, 1),
(118, 4, '¿Qué hace la combinación SHIFT + una letra?', 'opcion_unica', '5.00', 3, 1),
(119, 4, 'Google Chrome es un navegador web.', 'opcion_unica', '5.00', 2, 1),
(120, 4, 'Mozilla Firefox es un buscador.', 'opcion_unica', '5.00', 2, 1),
(121, 4, 'Un navegador permite acceder y visualizar paginas web.', 'opcion_unica', '5.00', 2, 1),
(122, 4, 'Un buscador necesita de un navegador para funcionar.', 'opcion_unica', '5.00', 2, 1),
(123, 2, 'Seleccione la manera correcta de declarar un término algebraico.', 'opcion_unica', '5.00', 3, 1),
(124, 2, 'Seleccione la manera correcta para declarar un término algebraico.', 'opcion_unica', '5.00', 3, 1),
(125, 2, '¿Cuantos términos tiene la siguiente expresión 6x+2y+z?', 'opcion_unica', '5.00', 4, 1),
(126, 2, '¿Cuantos términos tiene la siguiente expresión 4a-2b?', 'opcion_unica', '5.00', 4, 1),
(127, 2, 'Identifica las variables de la siguiente expresión 4xy+x', 'opcion_unica', '5.00', 3, 1),
(128, 2, 'Identifica las variables de la siguiente expresión 2ab+c', 'opcion_unica', '5.00', 3, 1),
(129, 2, 'A continuación se le presenta una expresión algebraica de 8x seleccione que tipo es', 'opcion_unica', '5.00', 3, 1),
(130, 2, 'A continuación se le presenta una expresión algebraica de 8x+2 seleccione que tipo es', 'opcion_unica', '5.00', 3, 1),
(131, 2, 'A continuación se le presenta una expresión algebraica de 8x+2y-4 seleccione que tipo es', 'opcion_unica', '5.00', 3, 1),
(132, 2, 'Reduzca el siguiente término semejante y seleccione la respuesta correcta.\n3x+2x', 'opcion_unica', '5.00', 4, 1),
(133, 2, 'Reduzca el siguiente término semejante y seleccione la respuesta correcta.\n-8x-3x', 'opcion_unica', '5.00', 4, 1),
(134, 2, 'Reduzca el siguiente término semejante y seleccione la respuesta correcta.\n-4x² -x²', 'opcion_unica', '5.00', 4, 1),
(135, 2, 'Reduzca el siguiente término semejante y seleccione la respuesta correcta.\ny+4y', 'opcion_unica', '5.00', 4, 1),
(136, 2, 'Reduzca el siguiente término semejante y seleccione la respuesta correcta.\n4z+2z+z', 'opcion_unica', '5.00', 3, 1),
(137, 2, 'Reduzca el siguiente término semejante y seleccione la respuesta correcta.\n-5y-3y-2y', 'opcion_unica', '5.00', 4, 1),
(138, 2, 'Es la combinación de dos proposiciones simples a una compuesta y su resultado es verdadero únicamente cuando ambas proposiciones sean verdaderas.', 'opcion_unica', '5.00', 3, 1),
(139, 2, 'Es la combinación de dos proposiciones simples a una compuesta y su resultado es verdadero cuando al menos una proposición sea verdaderas.', 'opcion_unica', '5.00', 3, 1),
(140, 2, 'Niega la siguiente proposición: voy al colegio', 'opcion_unica', '5.00', 3, 1),
(141, 2, 'Selecciona la manera correcta de como representar una proposición compuesta utilizando la conjunción.', 'opcion_unica', '5.00', 3, 1),
(142, 2, 'Selecciona la manera correcta de como representar una proposición compuesta utilizando la conjunción.', 'opcion_unica', '5.00', 3, 1),
(143, 2, 'Los conectivos lógicos permiten unir dos o más proposiciones simples', 'opcion_unica', '5.00', 2, 1),
(144, 2, 'Seleccione los signos de agrupación.', 'opcion_unica', '5.00', 3, 1),
(145, 2, 'Para resolver operaciones utilizando jerarquía de operaciones primero se realizan las sumas y restas luego las multiplicaciones y divisiones.', 'opcion_unica', '5.00', 2, 1),
(146, 2, '¿Cuál es el valor numérico para la siguiente expresión: 15-5{3(2^2-6)-12}-8?', 'opcion_unica', '5.00', 4, 1),
(147, 2, '¿Cuál es el valor numérico para la siguiente expresión: 25 / 5 +{4^2-(3+12/3)+2?', 'opcion_unica', '5.00', 4, 1),
(148, 5, 'Los dispositivos de entrada nos permiten introducir datos los cuales serán procesados por el ordenador.', 'opcion_unica', '5.00', 2, 1),
(149, 5, 'Los dispositivos de salida nos permiten introducir datos los cuales serán procesados por el ordenador.', 'opcion_unica', '5.00', 2, 1),
(150, 5, 'Selecciones los dispositivos correctos para poder introducir datos en una computadora.', 'opcion_unica', '5.00', 3, 1),
(151, 5, 'Selecciones los dispositivos correctos que brindan salida de información.', 'opcion_unica', '5.00', 3, 1),
(152, 5, 'Selecciones los dispositivos periféricos de comunicación.', 'opcion_unica', '5.00', 3, 1),
(153, 5, 'Seleccione el atajo correcto para poder abrir la venta de impresión.', 'opcion_unica', '5.00', 4, 1),
(154, 5, 'Seleccione el formato correcto para una imagen', 'opcion_unica', '5.00', 4, 1),
(155, 5, 'Seleccione el formato correcto para una imagen.', 'opcion_unica', '5.00', 4, 1),
(156, 5, '¿Cuáles son las formas correctas que se pueden utilizar para representar las imágenes a la hora de poder almacenarlos?', 'opcion_unica', '5.00', 3, 1),
(157, 5, 'Word es un procesador de textos', 'opcion_unica', '5.00', 2, 1),
(158, 5, 'Word nos permite poder realizar correcciones de errores automáticamente.', 'opcion_unica', '5.00', 2, 1),
(159, 5, 'Para realizar una búsqueda eficiente en internet es necesario definir correctamente lo que se desea investigar.', 'opcion_unica', '5.00', 2, 1),
(160, 5, 'La información con una mayor calidad en internet se encuentra en el idioma inglés.', 'opcion_unica', '5.00', 2, 1),
(161, 5, 'Seleccione una técnica correcta para poder realizar una búsqueda eficiente en internet.', 'opcion_unica', '5.00', 3, 1),
(162, 5, 'Seleccione un instrumento para poder realizar una investigación cualitativa.', 'opcion_unica', '5.00', 3, 1),
(163, 5, '¿Cuáles son las características de un sitio seguro en internet?', 'opcion_unica', '5.00', 3, 1),
(164, 5, 'Seleccione un delito informático.', 'opcion_unica', '5.00', 3, 1),
(165, 5, 'Los virus informáticos prácticamente son software realizados con objetivos maliciosos.', 'opcion_unica', '5.00', 2, 1),
(166, 5, 'Selecciones un tipo de virus informático', 'opcion_unica', '5.00', 4, 1),
(167, 5, 'Selecciones un tipo de virus informático.', 'opcion_unica', '5.00', 4, 1),
(168, 5, 'Selecciones un tipo de antivirus informático.', 'opcion_unica', '5.00', 4, 1),
(169, 5, 'Selecciones un tipo de antivirus informático.', 'opcion_unica', '5.00', 3, 1),
(170, 5, 'Seleccione una técnica correcta para evitar el plagio.', 'opcion_unica', '5.00', 3, 0),
(171, 5, 'Seleccione una técnica correcta para evitar el plagio.', 'opcion_unica', '5.00', NULL, 0),
(172, 5, 'Es una persona con conocimientos en sistemas informáticos enfocado a resolver problema de seguridad y que tiene principios y ética profesional.', 'opcion_unica', '5.00', 3, 1),
(173, 6, 'Es una herramienta que nos brinda un servicio de almacenamiento de archivos en la nube y nosotros podemos acceder desde cualquier lugar y dispositivo mediante internet.', 'opcion_unica', '5.00', 2, 0),
(174, 6, 'Selecciona un buscador de tipo general', 'opcion_unica', '5.00', 3, 1),
(175, 6, 'Selecciona un buscador de tipo general', 'opcion_unica', '5.00', 3, 1),
(176, 6, 'Los buscadores generales se centran en información específica como: educación, noticias, entre otros.', 'opcion_unica', '5.00', 2, 1),
(177, 6, 'Los multibuscadores se basan prácticamente en búsqueda de información en varios motores de búsqueda.', 'opcion_unica', '5.00', 2, 1),
(178, 6, '¿Cuál es el significado de FTP?', 'opcion_unica', '5.00', 3, 1),
(179, 6, 'Seleccione algunos de los métodos de aprendizaje visual.', 'opcion_unica', '5.00', 0, 0),
(180, 6, 'Seleccione algunos de los métodos de aprendizaje visual.', 'opcion_unica', '5.00', 3, 1),
(181, 6, 'Las problemáticas educativas afectan el rendimiento académico de los estudiantes.', 'opcion_unica', '5.00', 2, 1),
(182, 6, 'Seleccione un tipo de problemática educativa', 'opcion_unica', '5.00', 3, 1),
(183, 6, 'Seleccione un tipo de problemática educativa', 'opcion_unica', '5.00', 3, 1),
(184, 6, 'Es una herramienta que nos permite realizar el diseño y aplicación de una encuesta digital.', 'opcion_unica', '5.00', 4, 1),
(185, 6, 'Una encuesta se compone de preguntas abiertas y cerradas', 'opcion_unica', '5.00', 2, 1),
(186, 6, 'Seleccione la manera correcta para realizar una  pregunta cerrada.', 'opcion_unica', '5.00', 3, 1),
(187, 6, 'Seleccione la manera correcta para realizar una  pregunta abierta.', 'opcion_unica', '5.00', 3, 1),
(188, 6, 'Es un término utilizado para poder subir o publicar material en internet', 'opcion_unica', '5.00', 3, 1),
(189, 6, 'Seleccione una desventaja de las redes sociales', 'opcion_unica', '5.00', 3, 1),
(190, 6, 'Seleccione una desventaja de las redes sociales', 'opcion_unica', '5.00', 3, 1),
(191, 6, 'Seleccione una ventaja de las redes sociales', 'opcion_unica', '5.00', 3, 1),
(192, 6, 'Seleccione una ventaja de las redes sociales', 'opcion_unica', '5.00', 3, 1),
(193, 6, 'Seleccione el atajo correcto para poder colocar la letra en negrita.', 'opcion_unica', '5.00', 4, 1),
(194, 6, 'Seleccione el atajo correcto para poder abrir otra ventana de Word.', 'opcion_unica', '5.00', 3, 1),
(195, 6, 'Seleccione el atajo correcto para poder centrar un título o párrafo.', 'opcion_unica', '5.00', 4, 1),
(196, 6, 'Seleccione el atajo correcto para poder abrir la ventana de impresión de un documento.', 'opcion_unica', '5.00', 4, 1),
(197, 6, 'Una tabla de 2*5 significa insertar dos columnas y cinco filas', 'opcion_unica', '5.00', 2, 1),
(198, 6, 'Seleccione la descripción correcta de 5*2', 'opcion_unica', '5.00', 3, 1),
(199, 6, 'Seleccione la descripción correcta de 3*6', 'opcion_unica', '5.00', 3, 1),
(200, 6, 'La orientación de las hojas puede ser presentadas en:', 'opcion_unica', '5.00', 3, 1),
(201, 6, 'Seleccione la función correcta para encontrar el valor máximo en una tabla de datos numéricos.', 'opcion_unica', '5.00', 3, 1),
(202, 6, 'Seleccione la función correcta para encontrar el valor mínimo en una tabla de datos numéricos.', 'opcion_unica', '5.00', 3, 1),
(203, 6, 'Seleccione la función correcta para convertir un texto en minúsculas a mayúsculas.', 'opcion_unica', '5.00', 4, 1),
(204, 6, 'Seleccione la función lógica para evaluar una condición.', 'opcion_unica', '5.00', 4, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `respuestas`
--

CREATE TABLE `respuestas` (
  `id` bigint(20) NOT NULL,
  `pregunta_id` bigint(20) NOT NULL,
  `respuesta_texto` text,
  `respuesta_numero` decimal(12,4) DEFAULT NULL,
  `es_correcta` tinyint(1) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `respuestas`
--

INSERT INTO `respuestas` (`id`, `pregunta_id`, `respuesta_texto`, `respuesta_numero`, `es_correcta`, `activo`) VALUES
(1, 1, '2x', NULL, 1, 1),
(2, 1, '4x', NULL, 0, 1),
(3, 1, '3x', NULL, 0, 1),
(4, 2, 'x+5', NULL, 0, 1),
(5, 2, 'x/5', NULL, 0, 1),
(6, 2, 'x-5', NULL, 1, 1),
(7, 3, 'x-4', NULL, 0, 1),
(8, 3, 'x+4', NULL, 1, 1),
(9, 3, 'x/4', NULL, 0, 1),
(10, 4, 'xy', NULL, 1, 1),
(11, 4, 'x+y', NULL, 0, 1),
(12, 4, 'x-y', NULL, 0, 1),
(13, 4, 'x/y', NULL, 0, 1),
(14, 5, 'Falso', NULL, 0, 1),
(15, 5, 'Verdadero', NULL, 1, 1),
(16, 6, 'Verdadero', NULL, 1, 1),
(17, 6, 'Falso', NULL, 0, 1),
(18, 7, 'Rectas perpendiculares', NULL, 0, 1),
(19, 7, 'Rectas paralelas', NULL, 1, 1),
(20, 7, 'Ninguna', NULL, 0, 1),
(21, 8, 'Rectas paralelas', NULL, 0, 1),
(22, 8, 'Rectas perpendiculares', NULL, 1, 1),
(23, 8, 'Otra', NULL, 0, 1),
(24, 9, 'Sumando todos sus lados', NULL, 1, 1),
(25, 9, 'Multiplicando todos sus lados', NULL, 0, 1),
(26, 9, 'Restando', NULL, 0, 1),
(27, 10, 'área = (Perímetro * apotema)/4', NULL, 0, 1),
(28, 10, 'área = (perímetro + apotema)/6', NULL, 0, 1),
(29, 10, 'área = (perímetro * apotema)/2', NULL, 1, 1),
(30, 11, 'x+8 = 20', NULL, 1, 1),
(31, 11, '10-8 = 2', NULL, 0, 1),
(32, 11, '2+2 = 4', NULL, 0, 1),
(33, 11, '5*5 = 25', NULL, 0, 1),
(34, 12, 'Lenguaje algebraico', NULL, 0, 1),
(35, 12, 'Valor de verdad', NULL, 0, 1),
(36, 12, 'Simples y compuestas', NULL, 1, 1),
(37, 13, 'Falso', NULL, 0, 1),
(38, 13, 'Verdadero', NULL, 1, 1),
(39, 14, 'Verdadero', NULL, 0, 1),
(40, 14, 'Falso', NULL, 1, 1),
(41, 15, 'Voy al colegio o me quedo en casa.', NULL, 0, 1),
(42, 15, 'Liam estudia computación y matemáticas.', NULL, 0, 1),
(43, 15, 'Mi hermano es docente.', NULL, 1, 1),
(44, 16, '{1, 2, 3, 4...}', NULL, 0, 1),
(45, 16, '{1, 3, 5...}', NULL, 1, 1),
(46, 16, '{2, 4, 6...}', NULL, 0, 1),
(47, 17, 'x', NULL, 0, 1),
(48, 17, 'z', NULL, 0, 1),
(49, 17, 'y', NULL, 1, 1),
(50, 18, 'a', NULL, 0, 1),
(51, 18, 'b', NULL, 1, 1),
(52, 18, 'c', NULL, 0, 1),
(53, 19, 'x = -5', NULL, 0, 1),
(54, 19, 'x = 5', NULL, 1, 1),
(55, 19, 'x = 8', NULL, 0, 1),
(56, 20, 'y = 4', NULL, 0, 1),
(57, 20, 'y = 10', NULL, 1, 1),
(58, 20, 'y = 9', NULL, 0, 1),
(59, 20, 'y = 6', NULL, 0, 1),
(60, 21, 'x = 1/5', NULL, 1, 1),
(61, 21, 'x = 3', NULL, 0, 1),
(62, 21, 'x = 1/2', NULL, 0, 1),
(63, 21, 'x = 6', NULL, 0, 1),
(64, 22, 'Falso', NULL, 0, 1),
(65, 22, 'Verdadero', NULL, 1, 1),
(66, 23, 'Verdadero', NULL, 0, 1),
(67, 23, 'Falso', NULL, 1, 1),
(68, 24, '1, 2, 3, 4, 5, -1, -2, -3,-4', NULL, 1, 1),
(69, 24, '1/2, 1/4, 1/5', NULL, 0, 1),
(70, 24, '2.3, 2.7, 3.5', NULL, 0, 1),
(71, 25, NULL, '1.0000', 0, 1),
(72, 25, NULL, '2.0000', 0, 1),
(73, 25, NULL, '3.0000', 1, 1),
(74, 26, NULL, '4.0000', 0, 1),
(75, 26, NULL, '5.0000', 1, 1),
(76, 26, NULL, '6.0000', 0, 1),
(77, 27, 'Verdadero', NULL, 0, 1),
(78, 27, 'Falso', NULL, 1, 1),
(79, 28, 'Verdadero', NULL, 1, 1),
(80, 28, 'Falso', NULL, 0, 1),
(81, 29, 'Verdadera', NULL, 1, 1),
(82, 29, 'Falso', NULL, 0, 1),
(83, 30, NULL, '-2.0000', 0, 1),
(84, 30, NULL, '2.0000', 1, 1),
(85, 30, NULL, '3.0000', 0, 1),
(86, 31, NULL, '5.0000', 0, 1),
(87, 31, NULL, '6.0000', 1, 1),
(88, 31, NULL, '-6.0000', 0, 1),
(89, 32, NULL, '-10.0000', 0, 1),
(90, 32, NULL, '20.0000', 0, 1),
(91, 32, NULL, '10.0000', 1, 1),
(92, 32, NULL, '21.0000', 0, 1),
(93, 33, NULL, '20.0000', 0, 1),
(94, 33, NULL, '33.0000', 1, 1),
(95, 33, NULL, '25.0000', 0, 1),
(96, 33, NULL, '40.0000', 0, 1),
(97, 34, 'Verdadero', NULL, 1, 1),
(98, 34, 'Falso', NULL, 0, 1),
(99, 35, 'Muestra', NULL, 0, 1),
(100, 35, 'Población', NULL, 0, 1),
(101, 35, 'Encuestas', NULL, 1, 1),
(102, 36, 'Entrevistas', NULL, 1, 1),
(103, 36, 'Muestra', NULL, 0, 1),
(104, 36, 'Población', NULL, 0, 1),
(105, 37, 'Estadística', NULL, 0, 1),
(106, 37, 'Muestra', NULL, 1, 1),
(107, 37, 'Población', NULL, 0, 1),
(108, 38, 'Todos los estudiantes del departamento de Quetzaltenango', NULL, 1, 1),
(109, 38, '100 estudiantes escogido al azar en el departamento de Quetzaltenango', NULL, 0, 1),
(110, 39, 'Todos los estudiantes del departamento de Quetzaltenango', NULL, 0, 1),
(111, 40, 'Todos los estudiantes del departamento de Quetzaltenango', NULL, 0, 1),
(112, 40, '60 estudiantes del departamento de Quetzaltenango', NULL, 1, 1),
(113, 41, 'Verdadero', NULL, 1, 1),
(114, 41, 'Falso', NULL, 0, 1),
(115, 42, 'a3 + b3', NULL, 0, 1),
(116, 42, 'a3 + 3a2b + 3ab2 + b3', NULL, 1, 1),
(117, 42, 'a3 + 2ab + b3', NULL, 0, 1),
(118, 42, 'a2 + b2', NULL, 0, 1),
(119, 43, 'Productos de cuadrados', NULL, 0, 1),
(120, 43, 'Trinomio especial', NULL, 0, 1),
(121, 43, 'Cubo de binomio', NULL, 1, 1),
(122, 43, 'Diferencia de cubos', NULL, 0, 1),
(123, 44, 'Factorización', NULL, 0, 1),
(124, 44, 'Regla de tres', NULL, 0, 1),
(125, 44, 'Regla de Ruffin', NULL, 0, 1),
(126, 45, 'Un triángulo numérico.', NULL, 1, 1),
(127, 45, 'Una matriz', NULL, 0, 1),
(128, 45, 'Un polígono', NULL, 0, 1),
(129, 45, 'Un cuadro', NULL, 0, 1),
(130, 46, '6/9x', NULL, 0, 1),
(131, 46, '9/6x', NULL, 0, 1),
(132, 46, '2/3', NULL, 1, 1),
(133, 46, 'x/9', NULL, 0, 1),
(134, 47, 'a/b', NULL, 1, 1),
(135, 47, 'a2/b2', NULL, 0, 1),
(136, 47, 'a3/b3', NULL, 0, 1),
(137, 48, 'x2 – 3', NULL, 0, 1),
(138, 48, 'x – 3', NULL, 0, 1),
(139, 48, 'x + 3', NULL, 1, 1),
(140, 49, 'x2 + 5', NULL, 0, 1),
(141, 49, 'x2 + 5x2', NULL, 0, 1),
(142, 49, 'x + 5', NULL, 1, 1),
(143, 49, 'x2 – 5', NULL, 0, 1),
(144, 50, '3x', NULL, 0, 1),
(145, 50, '6x', NULL, 1, 1),
(146, 50, NULL, '12.0000', 0, 1),
(147, 50, NULL, '18.0000', 0, 1),
(148, 52, '(a - b) (a + b)', NULL, 1, 1),
(149, 52, '(a – b)2', NULL, 0, 1),
(150, 52, 'a2 – 2ab + b2', NULL, 0, 1),
(151, 53, '4x2', NULL, 0, 1),
(152, 53, NULL, '2.0000', 1, 1),
(153, 53, 'x / 2', NULL, 0, 1),
(154, 53, 'x2 / 8', NULL, 0, 1),
(155, 54, 'Área', NULL, 0, 1),
(156, 54, 'Radio', NULL, 0, 1),
(157, 54, 'Circunferencia', NULL, 1, 1),
(158, 55, '35˚', NULL, 0, 1),
(159, 55, '90˚', NULL, 1, 1),
(160, 55, '120˚', NULL, 0, 1),
(161, 55, '365˚', NULL, 0, 1),
(162, 56, 'Diámetro', NULL, 0, 1),
(163, 56, 'Cuerda', NULL, 0, 1),
(164, 56, 'Secante', NULL, 0, 1),
(165, 56, 'Radio', NULL, 1, 1),
(166, 57, 'Cubo', NULL, 1, 1),
(167, 57, 'Pirámide', NULL, 0, 1),
(168, 57, 'Esfera', NULL, 0, 1),
(169, 57, 'Prisma', NULL, 0, 1),
(170, 58, 'Área * Altura', NULL, 0, 1),
(171, 58, 'lado^3', NULL, 1, 1),
(172, 58, 'Base * Altura', NULL, 0, 1),
(173, 58, 'Radio * PI', NULL, 0, 1),
(174, 59, 'Tres ángulos agudos', NULL, 0, 1),
(175, 59, 'Un ángulo mayo de 90˚', NULL, 1, 1),
(176, 59, 'Todos los lados iguales', NULL, 0, 1),
(177, 60, 'a2 = b2 + c2', NULL, 0, 1),
(178, 60, 'a2 = b2 + c2 – 2bc.cos(A)', NULL, 1, 1),
(179, 60, 'a2 + b2 = c2', NULL, 0, 1),
(180, 61, 'Elementos en A o B', NULL, 0, 1),
(181, 61, 'Solo en A', NULL, 0, 1),
(182, 61, 'Solo en B', NULL, 0, 1),
(183, 61, 'Elementos comunes en A y B', NULL, 1, 1),
(184, 62, 'Elementos de A y B', NULL, 1, 1),
(185, 62, 'Solo comunes', NULL, 0, 1),
(186, 63, 'Verdadero', NULL, 0, 1),
(187, 63, 'Falso', NULL, 1, 1),
(188, 64, 'Teorema', NULL, 0, 1),
(189, 64, 'Postulado', NULL, 1, 1),
(190, 64, 'Falacia', NULL, 0, 1),
(191, 65, 'Axioma', NULL, 0, 1),
(192, 65, 'Falacia', NULL, 0, 1),
(193, 65, 'Corolario', NULL, 1, 1),
(194, 66, 'Que están en A', NULL, 0, 1),
(195, 66, 'Que no están en A', NULL, 1, 1),
(196, 67, 'Sumas de elementos', NULL, 0, 1),
(197, 67, 'Uniones', NULL, 0, 1),
(198, 67, 'Pares ordenados', NULL, 1, 1),
(199, 68, 'Constante', NULL, 0, 1),
(200, 68, 'Inyectiva', NULL, 0, 1),
(201, 68, 'Inyectiva o sobreyectiva', NULL, 1, 1),
(202, 69, 'Parábola', NULL, 1, 1),
(203, 69, 'Circunferencia', NULL, 0, 1),
(204, 69, 'Recta', NULL, 0, 1),
(205, 70, 'a2 + b2', NULL, 0, 1),
(206, 70, 'b^2 – 4ac', NULL, 1, 1),
(207, 70, 'a2 - b2', NULL, 0, 1),
(208, 71, 'Multiplicar ecuaciones', NULL, 0, 1),
(209, 71, 'Sumar o restar ecuaciones', NULL, 1, 1),
(210, 71, 'Dividir ecuaciones', NULL, 0, 1),
(211, 72, 'Una Solución', NULL, 0, 1),
(212, 72, 'Sin solución', NULL, 1, 1),
(213, 72, 'Ninguno', NULL, 0, 1),
(214, 73, 'Siempre no negativa', NULL, 1, 1),
(215, 73, 'Siempre negativa', NULL, 0, 1),
(216, 73, 'Línea recta', NULL, 0, 1),
(217, 74, 'Entero', NULL, 0, 1),
(218, 74, 'Complejo', NULL, 0, 1),
(219, 74, 'Racional', NULL, 0, 1),
(220, 74, 'Irracional', NULL, 1, 1),
(221, 75, 'Enteros y racionales', NULL, 0, 1),
(222, 75, 'Solo naturales', NULL, 0, 1),
(223, 75, 'Naturales, enteros, racionales e irracionales', NULL, 1, 1),
(224, 75, 'Irracionales y racionales', NULL, 0, 1),
(225, 76, '(3,4)', NULL, 1, 1),
(226, 76, '(-3,4)', NULL, 0, 1),
(227, 76, '(3,-4)', NULL, 0, 1),
(228, 76, '(4,3)', NULL, 0, 1),
(229, 77, '4', NULL, 1, 1),
(230, 77, '5', NULL, 0, 1),
(231, 77, '6', NULL, 0, 1),
(232, 78, '1/6', NULL, 0, 1),
(233, 78, '2/6', NULL, 0, 1),
(234, 78, '3/6', NULL, 1, 1),
(235, 78, '4/6', NULL, 0, 1),
(236, 79, '12', NULL, 0, 1),
(237, 79, '24', NULL, 1, 1),
(238, 79, '20', NULL, 0, 1),
(239, 80, NULL, '5.0000', 0, 1),
(240, 80, NULL, '10.0000', 1, 1),
(241, 80, NULL, '30.0000', 0, 1),
(242, 81, NULL, '5.0000', 1, 1),
(243, 81, NULL, '6.0000', 0, 1),
(244, 81, NULL, '7.0000', 0, 1),
(245, 81, NULL, '9.0000', 0, 1),
(246, 82, '6 + 8i', NULL, 1, 1),
(247, 82, '6+2i', NULL, 0, 1),
(248, 82, '8 + 15i', NULL, 0, 1),
(249, 82, '2+8i', NULL, 0, 1),
(250, 83, 'Automóvil', NULL, 0, 1),
(251, 83, 'Computadora', NULL, 1, 1),
(252, 83, 'Lavadora', NULL, 0, 1),
(253, 84, 'El fax', NULL, 0, 1),
(254, 84, 'La radio', NULL, 0, 1),
(255, 84, 'El internet', NULL, 1, 1),
(256, 84, 'El telégrafo', NULL, 0, 1),
(257, 85, 'Reloj', NULL, 0, 1),
(258, 85, 'Pantallas táctiles', NULL, 1, 1),
(259, 85, 'Disquetes', NULL, 0, 1),
(260, 86, 'El papel', NULL, 0, 1),
(261, 86, 'Disco duro', NULL, 1, 1),
(262, 87, 'El correo tradicional', NULL, 0, 1),
(263, 87, 'Plataformas digitales', NULL, 1, 1),
(264, 87, 'La televisión', NULL, 0, 1),
(265, 88, 'Plataformas educativas en linea', NULL, 1, 1),
(266, 88, 'El transporte público', NULL, 0, 1),
(267, 88, 'Teléfonos', NULL, 0, 1),
(268, 89, 'El internet', NULL, 0, 1),
(269, 89, 'La radio', NULL, 0, 1),
(270, 89, 'Inteligencia artificial', NULL, 1, 1),
(271, 90, 'Dinero en efectivo', NULL, 0, 1),
(272, 90, 'Aplicaciones móviles', NULL, 1, 1),
(273, 90, 'Tarjetas sin contacto', NULL, 0, 1),
(274, 91, 'Mejor educación', NULL, 0, 1),
(275, 91, 'Comunicación', NULL, 0, 1),
(276, 91, 'Aislamiento social', NULL, 1, 1),
(277, 92, 'Periódico', NULL, 0, 1),
(278, 92, 'Carteles en la calle', NULL, 0, 1),
(279, 92, 'Noticias en las redes sociales', NULL, 1, 1),
(280, 93, 'Verdadero', NULL, 0, 1),
(281, 93, 'Falso', NULL, 1, 1),
(282, 94, 'Verdadero', NULL, 1, 1),
(283, 94, 'Falso', NULL, 0, 1),
(284, 95, 'Verdadero', NULL, 1, 1),
(285, 95, 'Falso', NULL, 0, 1),
(286, 96, 'Verdadero', NULL, 1, 1),
(287, 96, 'Falso', NULL, 0, 1),
(288, 97, 'Monitor', NULL, 0, 1),
(289, 97, 'Procesador (CPU)', NULL, 1, 1),
(290, 97, 'Disco duro', NULL, 0, 1),
(291, 98, 'Memoria RAM', NULL, 0, 1),
(292, 98, 'Disco duro', NULL, 1, 1),
(293, 98, 'Fuente de poder', NULL, 0, 1),
(294, 99, 'Almacenar datos de forma indefinida', NULL, 0, 1),
(295, 99, 'Mostrar gráficos', NULL, 0, 1),
(296, 99, 'Almacenar datos temporales mientras se usan', NULL, 1, 1),
(297, 100, 'CPU', NULL, 0, 1),
(298, 100, 'Teclado', NULL, 0, 1),
(299, 100, 'Placa base', NULL, 0, 1),
(300, 100, 'Monitor', NULL, 1, 1),
(301, 101, 'Fuente de poder', NULL, 1, 1),
(302, 101, 'Placa base', NULL, 0, 1),
(303, 101, 'Procesador', NULL, 0, 1),
(304, 102, 'Mouse', NULL, 0, 1),
(305, 102, 'Memoria', NULL, 0, 1),
(306, 103, 'Teclado', NULL, 1, 1),
(307, 103, 'Otra', NULL, 0, 1),
(308, 103, 'Mouse', NULL, 1, 1),
(309, 103, 'Disco duro', NULL, 0, 1),
(310, 104, 'Una aplicación del software', NULL, 0, 1),
(311, 104, 'Puerto USB', NULL, 0, 1),
(312, 104, 'Tarjeta que conecta todos los componentes internos', NULL, 1, 1),
(313, 105, 'Tarjeta grafica', NULL, 1, 1),
(314, 105, 'CPU', NULL, 0, 1),
(315, 105, 'Placa base', NULL, 0, 1),
(316, 106, 'Metros', NULL, 0, 1),
(317, 106, 'Voltios', NULL, 0, 1),
(318, 106, 'pulgadas', NULL, 0, 1),
(319, 106, 'Gigabytes (GB)', NULL, 1, 1),
(320, 107, 'Teclado', NULL, 1, 1),
(321, 107, 'Monitor', NULL, 0, 1),
(322, 107, 'Impresora', NULL, 0, 1),
(323, 108, 'Teclado', NULL, 0, 1),
(324, 108, 'Mouse', NULL, 0, 1),
(325, 108, 'Micrófono', NULL, 0, 1),
(326, 108, 'Monitor', NULL, 1, 1),
(327, 109, 'Tarjeta gráfica', NULL, 0, 1),
(328, 109, 'Puerto de red', NULL, 0, 1),
(329, 109, 'Puerto USB', NULL, 1, 1),
(330, 110, 'Fuente de poder', NULL, 0, 1),
(331, 110, 'Disco duro', NULL, 0, 1),
(332, 110, 'Ventilador o disipador de calor', NULL, 1, 1),
(333, 110, 'Monitor', NULL, 0, 1),
(334, 111, 'Memoria RAM', NULL, 0, 1),
(335, 111, 'Placa base', NULL, 0, 1),
(336, 111, 'Disco duro', NULL, 1, 1),
(337, 112, 'Placa base', NULL, 0, 1),
(338, 112, 'Puerto de red', NULL, 0, 1),
(339, 112, 'Tarjeta de red WI-FI', NULL, 1, 1),
(340, 113, 'CTRL + X', NULL, 0, 1),
(341, 113, 'CTRL + T', NULL, 0, 1),
(342, 113, 'CTRL + C', NULL, 1, 1),
(343, 113, 'CTRL + Z', NULL, 0, 1),
(344, 114, 'CTRL + X', NULL, 1, 1),
(345, 114, 'CTRL + B', NULL, 0, 1),
(346, 114, 'CTRL + N', NULL, 0, 1),
(347, 114, 'CTRL + G', NULL, 0, 1),
(348, 115, 'Guardar el documento', NULL, 0, 1),
(349, 115, 'Imprime el archivo', NULL, 0, 1),
(350, 115, 'Deshace la ultima acción', NULL, 1, 1),
(351, 116, 'Selecciona el texto', NULL, 0, 1),
(352, 116, 'Abre la ventana de impresión', NULL, 1, 1),
(353, 116, 'Abre un nuevo archivo', NULL, 0, 1),
(354, 117, 'Guarda el archivo', NULL, 0, 1),
(355, 117, 'Cancela o cierra menús  y acciones', NULL, 1, 1),
(356, 117, 'Pega texto', NULL, 0, 1),
(357, 117, 'Aplica negrita', NULL, 0, 1),
(358, 118, 'Escribe símbolos', NULL, 0, 1),
(359, 118, 'Copia letras', NULL, 0, 1),
(360, 118, 'Escribe la letra en mayúscula', NULL, 1, 1),
(361, 119, 'Verdadero', NULL, 1, 1),
(362, 119, 'Falso', NULL, 0, 1),
(363, 120, 'Verdadero', NULL, 0, 1),
(364, 120, 'Falso', NULL, 1, 1),
(365, 121, 'Verdadero', NULL, 1, 1),
(366, 121, 'Falso', NULL, 0, 1),
(367, 122, 'Verdadero', NULL, 1, 1),
(368, 122, 'Falso', NULL, 0, 1),
(369, 123, '3x+2x', NULL, 0, 1),
(370, 123, '3xy', NULL, 1, 1),
(371, 123, '5xy-3y', NULL, 0, 1),
(372, 124, '6x+3x', NULL, 0, 1),
(373, 124, '4xy-2y', NULL, 0, 1),
(374, 124, '6x', NULL, 1, 1),
(375, 125, NULL, '1.0000', 0, 1),
(376, 125, NULL, '2.0000', 0, 1),
(377, 125, NULL, '3.0000', 1, 1),
(378, 125, NULL, '4.0000', 0, 1),
(379, 126, NULL, '1.0000', 0, 1),
(380, 126, NULL, '2.0000', 1, 1),
(381, 126, NULL, '3.0000', 0, 1),
(382, 126, NULL, '4.0000', 0, 1),
(383, 127, 'a, b', NULL, 0, 1),
(384, 127, 'm, n', NULL, 0, 1),
(385, 127, 'x, y', NULL, 1, 1),
(386, 128, 'a,b', NULL, 0, 1),
(387, 128, 'a, b, c', NULL, 1, 1),
(388, 128, 'x, y, z', NULL, 0, 1),
(389, 129, 'Monomio', NULL, 1, 1),
(390, 129, 'Trinomio', NULL, 0, 1),
(391, 129, 'Binomio', NULL, 0, 1),
(392, 130, 'Monomio', NULL, 0, 1),
(393, 130, 'Binomio', NULL, 1, 1),
(394, 130, 'Trinomio', NULL, 0, 1),
(395, 131, 'Monomio', NULL, 0, 1),
(396, 131, 'Binomio', NULL, 0, 1),
(397, 131, 'Trinomio', NULL, 1, 1),
(398, 132, 'x', NULL, 0, 1),
(399, 132, '-5x', NULL, 0, 1),
(400, 132, '6x', NULL, 0, 1),
(401, 132, '5x', NULL, 1, 1),
(402, 133, '11x', NULL, 0, 1),
(403, 133, '-11x', NULL, 1, 1),
(404, 133, '5x', NULL, 0, 1),
(405, 133, '-5x', NULL, 0, 1),
(406, 134, '4x', NULL, 0, 1),
(407, 134, '4x²', NULL, 0, 1),
(408, 134, '-5x²', NULL, 1, 1),
(409, 134, '-4x²', NULL, 0, 1),
(410, 135, '4y', NULL, 0, 1),
(411, 135, '6y', NULL, 0, 1),
(412, 135, '4x', NULL, 0, 1),
(413, 135, '5y', NULL, 1, 1),
(414, 136, '6z', NULL, 0, 1),
(415, 136, '-7z', NULL, 0, 1),
(416, 136, '7z', NULL, 1, 1),
(417, 137, '6y', NULL, 0, 1),
(418, 137, '-10y', NULL, 1, 1),
(419, 137, '10y', NULL, 0, 1),
(420, 137, '8y', NULL, 0, 1),
(421, 138, 'Conjunción', NULL, 1, 1),
(422, 138, 'Negación', NULL, 0, 1),
(423, 138, 'Disyunción', NULL, 0, 1),
(424, 139, 'Conjunción', NULL, 0, 1),
(425, 139, 'Negación', NULL, 0, 1),
(426, 139, 'Disyunción', NULL, 1, 1),
(427, 140, 'Voy al colegio', NULL, 0, 1),
(428, 140, 'Tengo que ir al colegio', NULL, 0, 1),
(429, 140, 'No voy al colegio', NULL, 1, 1),
(430, 141, 'Hoy es lunes entonces tengo clases de computación', NULL, 0, 1),
(431, 141, 'Hoy es lunes no tengo clases de computación', NULL, 0, 1),
(432, 141, 'Hoy es lunes y tengo clases de computación', NULL, 1, 1),
(433, 142, 'Vamos al parque o miramos una película', NULL, 1, 1),
(434, 142, 'Vamos al parque y miramos una película', NULL, 0, 1),
(435, 142, 'Si vamos al parque entonces miramos la película', NULL, 0, 1),
(436, 143, 'Falso', NULL, 0, 1),
(437, 143, 'Verdadero', NULL, 1, 1),
(438, 144, '>, <, <>', NULL, 0, 1),
(439, 144, '+, -, *, /', NULL, 0, 1),
(440, 144, '( ), [ ], { }', NULL, 1, 1),
(441, 145, 'Verdadero', NULL, 0, 1),
(442, 145, 'Falso', NULL, 1, 1),
(443, 146, NULL, '25.0000', 0, 1),
(444, 146, NULL, '75.0000', 0, 1),
(445, 146, NULL, '97.0000', 1, 1),
(446, 146, NULL, '100.0000', 0, 1),
(447, 147, NULL, '8.0000', 0, 1),
(448, 147, NULL, '16.0000', 1, 1),
(449, 147, NULL, '21.0000', 0, 1),
(450, 147, NULL, '26.0000', 0, 1),
(451, 148, 'Verdadero', NULL, 1, 1),
(452, 148, 'Falso', NULL, 0, 1),
(453, 149, 'Verdadero', NULL, 0, 1),
(454, 149, 'Falso', NULL, 1, 1),
(455, 150, 'Monitor, impresoras, bocinas.', NULL, 0, 1),
(456, 150, 'Teclado, mouse, escáner.', NULL, 1, 1),
(457, 150, 'Router de red, tarjetas de red, bluetooth.', NULL, 0, 1),
(458, 151, 'Teclado, mouse, escáner.', NULL, 0, 1),
(459, 151, 'Router de red, tarjetas de red, bluetooth.', NULL, 0, 1),
(460, 151, 'Monitor, impresoras, bocinas.', NULL, 1, 1),
(461, 152, 'Router de red, tarjetas de red, bluetooth.', NULL, 1, 1),
(462, 152, 'Teclado, mouse, escáner.', NULL, 0, 1),
(463, 152, 'Monitor, impresoras, bocinas.', NULL, 0, 1),
(464, 153, 'CTRL + N', NULL, 0, 1),
(465, 153, 'CTRL + T', NULL, 0, 1),
(466, 153, 'CTRL + S', NULL, 0, 1),
(467, 153, 'CTRL + P', NULL, 1, 1),
(468, 154, 'MP4', NULL, 0, 1),
(469, 154, 'Txt', NULL, 0, 1),
(470, 154, 'Docx', NULL, 0, 1),
(471, 154, 'JPEG', NULL, 1, 1),
(472, 155, 'PNG', NULL, 1, 1),
(473, 155, 'Docx', NULL, 0, 1),
(474, 155, 'MP4', NULL, 0, 1),
(475, 155, 'MP3', NULL, 0, 1),
(476, 156, 'Formato de imágenes', NULL, 1, 1),
(477, 156, 'Imágenes', NULL, 0, 1),
(478, 156, 'Digitales', NULL, 0, 1),
(479, 157, 'Verdadero', NULL, 1, 1),
(480, 157, 'Falso', NULL, 0, 1),
(481, 158, 'Verdadero', NULL, 1, 1),
(482, 158, 'Falso', NULL, 0, 1),
(483, 159, 'Verdadero', NULL, 1, 1),
(484, 159, 'Falso', NULL, 0, 1),
(485, 160, 'Verdadero', NULL, 1, 1),
(486, 160, 'Falso', NULL, 0, 1),
(487, 161, 'Utilizar palabras claves.', NULL, 1, 1),
(488, 161, 'Enfocarse en la primera página.', NULL, 0, 1),
(489, 161, 'no especificarse', NULL, 0, 1),
(490, 162, 'Encuestas', NULL, 0, 1),
(491, 162, 'Entrevistas personal', NULL, 1, 1),
(492, 162, 'Investigación experimental', NULL, 0, 1),
(493, 163, 'Protocolos http', NULL, 0, 1),
(494, 163, 'Protocolos de https o candadito de seguridad.', NULL, 1, 1),
(495, 163, 'Dominio web extraño', NULL, 0, 1),
(496, 164, 'Cooperación internacional', NULL, 0, 1),
(497, 164, 'Ninguno', NULL, 0, 1),
(498, 164, 'Robo de identidad', NULL, 1, 1),
(499, 165, 'Verdadero', NULL, 1, 1),
(500, 165, 'Falso', NULL, 0, 1),
(501, 166, 'Virus de la gripe', NULL, 0, 1),
(502, 166, 'ESET NOD32', NULL, 0, 1),
(503, 166, 'Troyano', NULL, 1, 1),
(504, 166, 'Avast', NULL, 0, 1),
(505, 167, 'Gusanos', NULL, 1, 1),
(506, 167, 'ESET NOD32', NULL, 0, 1),
(507, 167, 'Virus de la gripe', NULL, 0, 1),
(508, 167, 'Avast', NULL, 0, 1),
(509, 168, 'Gusanos', NULL, 0, 1),
(510, 168, 'Troyanos', NULL, 0, 1),
(511, 168, 'Ninguno', NULL, 0, 1),
(512, 168, 'ESET NOD32', NULL, 1, 1),
(513, 169, 'Gusanos', NULL, 0, 1),
(514, 169, 'Troyanos', NULL, 0, 1),
(515, 169, 'Avast', NULL, 1, 1),
(516, 170, 'Copia literal del texto', NULL, 0, 1),
(517, 170, 'No citar la fuente', NULL, 0, 1),
(518, 170, 'Parafraseo', NULL, 1, 1),
(519, 172, 'Craker', NULL, 0, 1),
(520, 172, 'Ninguno', NULL, 0, 1),
(521, 172, 'Hacker', NULL, 1, 1),
(522, 173, 'Tarjetas de memorias', NULL, 0, 1),
(523, 173, 'Servidor de archivos locales', NULL, 0, 1),
(524, 174, 'Google', NULL, 1, 1),
(525, 174, 'Firefox', NULL, 0, 1),
(526, 174, 'Google Chrome', NULL, 0, 1),
(527, 175, 'Google Chrome', NULL, 0, 1),
(528, 175, 'Firefox', NULL, 0, 1),
(529, 175, 'Bing', NULL, 1, 1),
(530, 176, 'Verdadero', NULL, 0, 1),
(531, 176, 'Falso', NULL, 1, 1),
(532, 177, 'Falso', NULL, 0, 1),
(533, 177, 'Verdadero', NULL, 1, 1),
(534, 178, 'Protocolo de transferencia', NULL, 0, 1),
(535, 178, 'Protocolo de Transferencia de Archivos', NULL, 1, 1),
(536, 178, 'Archivos de protocolo de Transferencia', NULL, 0, 1),
(537, 180, 'Imágenes, esquemas, diagramas…', NULL, 1, 1),
(538, 180, 'Lectura, preguntas, experimento…', NULL, 0, 1),
(539, 180, 'Ninguna', NULL, 0, 1),
(540, 181, 'Verdadero', NULL, 1, 1),
(541, 181, 'Falso', NULL, 0, 1),
(542, 182, 'Desempleo', NULL, 0, 1),
(543, 182, 'Medio ambiente', NULL, 0, 1),
(544, 182, 'Desmotivación del estudiante', NULL, 1, 1),
(545, 183, 'Medio ambiente', NULL, 0, 1),
(546, 183, 'Desempleo', NULL, 0, 1),
(547, 183, 'Infraestructura', NULL, 1, 1),
(548, 184, 'Word', NULL, 0, 1),
(549, 184, 'Excel', NULL, 0, 1),
(550, 184, 'Paint', NULL, 0, 1),
(551, 184, 'Google forms', NULL, 1, 1),
(552, 185, 'Verdadero', NULL, 1, 1),
(553, 185, 'Falso', NULL, 0, 1),
(554, 186, '¿Qué te gustaría mejorar en la clase de computación?', NULL, 0, 1),
(555, 186, '¿Qué tema te gustaría aprender en el curso de computación?', NULL, 0, 1),
(556, 186, '¿Sabe leer?', NULL, 1, 1),
(557, 187, '¿Qué te gustaría mejorar en la clase de computación?', NULL, 1, 1),
(558, 187, '¿Sabe leer?', NULL, 0, 1),
(559, 187, '¿Sabe escribir?', NULL, 0, 1),
(560, 188, 'Navegar en la web', NULL, 0, 1),
(561, 188, 'Explorar en la web', NULL, 0, 1),
(562, 188, 'Colgar en la web', NULL, 1, 1),
(563, 189, 'Adicción y pérdida de tiempo', NULL, 1, 1),
(564, 189, 'Comunicación inmediata', NULL, 0, 1),
(565, 189, 'Acceso a la información', NULL, 0, 1),
(566, 190, 'Riesgo de Privacidad', NULL, 1, 1),
(567, 190, 'Comunicación inmediata', NULL, 0, 1),
(568, 190, 'Acceso a la información', NULL, 0, 1),
(569, 191, 'Adicción y pérdida de tiempo', NULL, 0, 1),
(570, 191, 'Riesgo de Privacidad', NULL, 0, 1),
(571, 191, 'Comunicación inmediata', NULL, 1, 1),
(572, 192, 'Riesgo de Privacidad', NULL, 0, 1),
(573, 192, 'Adicción y pérdida de tiempo', NULL, 0, 1),
(574, 192, 'Acceso a la información', NULL, 1, 1),
(575, 193, 'CTRL + P', NULL, 0, 1),
(576, 193, 'CTRL + N', NULL, 1, 1),
(577, 193, 'CTRL + S', NULL, 0, 1),
(578, 193, 'CTRL + X', NULL, 0, 1),
(579, 194, 'CTRL + U', NULL, 1, 1),
(580, 194, 'CTRL + S', NULL, 0, 1),
(581, 194, 'CTRL + P', NULL, 0, 1),
(582, 195, 'CTRL + P', NULL, 0, 1),
(583, 195, 'CTRL + Z', NULL, 0, 1),
(584, 195, 'CTRL + T', NULL, 1, 1),
(585, 195, 'CTRL + S', NULL, 0, 1),
(586, 196, 'CTRL + S', NULL, 0, 1),
(587, 196, 'CTRL + A', NULL, 0, 1),
(588, 196, 'CTRL + Y', NULL, 0, 1),
(589, 196, 'CTRL + P', NULL, 1, 1),
(590, 197, 'Verdadero', NULL, 1, 1),
(591, 197, 'Falso', NULL, 0, 1),
(592, 198, 'Ninguna', NULL, 0, 1),
(593, 198, 'Cinco columnas por dos filas', NULL, 1, 1),
(594, 198, 'Dos filas por cinco columnas', NULL, 0, 1),
(595, 199, 'Seis filas por tres columnas', NULL, 0, 1),
(596, 199, 'Tres columnas por seis filas', NULL, 1, 1),
(597, 199, 'Ninguna', NULL, 0, 1),
(598, 200, 'Vertical', NULL, 0, 1),
(599, 200, 'Vertical y horizontal', NULL, 1, 1),
(600, 200, 'Horizontal', NULL, 0, 1),
(601, 201, 'MIN', NULL, 0, 1),
(602, 201, 'SUMA', NULL, 0, 1),
(603, 201, 'MAX', NULL, 1, 1),
(604, 202, 'PRODUCTO', NULL, 0, 1),
(605, 202, 'MAX', NULL, 0, 1),
(606, 202, 'MIN', NULL, 1, 1),
(607, 203, 'MAX', NULL, 0, 1),
(608, 203, 'MAYUSC', NULL, 1, 1),
(609, 203, 'MIN', NULL, 0, 1),
(610, 203, 'MINUSC', NULL, 0, 1),
(611, 204, 'MAX', NULL, 0, 1),
(612, 204, 'MIN', NULL, 0, 1),
(613, 204, 'PRODUCTO', NULL, 0, 1),
(614, 204, 'SI', NULL, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `respuestas_alumnos`
--

CREATE TABLE `respuestas_alumnos` (
  `id` bigint(20) NOT NULL,
  `alumno_user_id` bigint(20) NOT NULL,
  `encuesta_id` bigint(20) NOT NULL,
  `pregunta_id` bigint(20) NOT NULL,
  `respuesta_id` bigint(20) DEFAULT NULL,
  `respuesta_texto` text,
  `respuesta_numero` decimal(12,4) DEFAULT NULL,
  `es_correcta` tinyint(1) DEFAULT NULL,
  `respondido_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `activo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `respuestas_alumnos`
--

INSERT INTO `respuestas_alumnos` (`id`, `alumno_user_id`, `encuesta_id`, `pregunta_id`, `respuesta_id`, `respuesta_texto`, `respuesta_numero`, `es_correcta`, `respondido_at`, `activo`) VALUES
(1, 3, 1, 10, 27, NULL, NULL, 0, '2025-10-27 17:15:26', 1),
(2, 3, 1, 26, 75, NULL, NULL, 1, '2025-10-27 17:15:26', 1),
(3, 3, 1, 38, 109, NULL, NULL, 0, '2025-10-27 17:15:26', 1),
(4, 3, 1, 16, 45, NULL, NULL, 1, '2025-10-27 17:15:26', 1),
(5, 3, 1, 15, 42, NULL, NULL, 0, '2025-10-27 17:15:26', 1),
(6, 3, 1, 25, 73, NULL, NULL, 1, '2025-10-27 17:15:26', 1),
(7, 3, 1, 14, 40, NULL, NULL, 1, '2025-10-27 17:15:26', 1),
(8, 3, 1, 5, 15, NULL, NULL, 1, '2025-10-27 17:15:26', 1),
(9, 3, 1, 24, 68, NULL, NULL, 1, '2025-10-27 17:15:26', 1),
(10, 3, 1, 21, 62, NULL, NULL, 0, '2025-10-27 17:15:26', 1),
(11, 3, 1, 13, 37, NULL, NULL, 0, '2025-10-27 17:15:26', 1),
(12, 3, 1, 23, 67, NULL, NULL, 1, '2025-10-27 17:15:26', 1),
(13, 3, 1, 36, 102, NULL, NULL, 1, '2025-10-27 17:15:26', 1),
(14, 3, 1, 31, 87, NULL, NULL, 1, '2025-10-27 17:15:26', 1),
(15, 3, 1, 12, 35, NULL, NULL, 0, '2025-10-27 17:15:26', 1),
(16, 3, 1, 28, 80, NULL, NULL, 0, '2025-10-27 17:15:26', 1),
(17, 3, 1, 30, 84, NULL, NULL, 1, '2025-10-27 17:15:26', 1),
(18, 3, 1, 6, 16, NULL, NULL, 1, '2025-10-27 17:15:26', 1),
(19, 3, 1, 9, 26, NULL, NULL, 0, '2025-10-27 17:15:26', 1),
(20, 3, 1, 34, 97, NULL, NULL, 1, '2025-10-27 17:15:26', 1),
(21, 3, 4, 116, 352, NULL, NULL, 1, '2025-10-27 17:23:34', 1),
(22, 3, 4, 86, 261, NULL, NULL, 1, '2025-10-27 17:23:34', 1),
(23, 3, 4, 102, 304, NULL, NULL, 0, '2025-10-27 17:23:34', 1),
(24, 3, 4, 113, 342, NULL, NULL, 1, '2025-10-27 17:23:34', 1),
(25, 3, 4, 107, 321, NULL, NULL, 0, '2025-10-27 17:23:34', 1),
(26, 3, 4, 88, 265, NULL, NULL, 1, '2025-10-27 17:23:34', 1),
(27, 3, 4, 105, 313, NULL, NULL, 1, '2025-10-27 17:23:34', 1),
(28, 3, 4, 119, 361, NULL, NULL, 1, '2025-10-27 17:23:34', 1),
(29, 3, 4, 93, 280, NULL, NULL, 0, '2025-10-27 17:23:34', 1),
(30, 3, 4, 120, 363, NULL, NULL, 0, '2025-10-27 17:23:34', 1),
(31, 3, 4, 104, 312, NULL, NULL, 1, '2025-10-27 17:23:34', 1),
(32, 3, 4, 87, 263, NULL, NULL, 1, '2025-10-27 17:23:34', 1),
(33, 3, 4, 111, 334, NULL, NULL, 0, '2025-10-27 17:23:34', 1),
(34, 3, 4, 96, 286, NULL, NULL, 1, '2025-10-27 17:23:34', 1),
(35, 3, 4, 117, 355, NULL, NULL, 1, '2025-10-27 17:23:34', 1),
(36, 3, 4, 91, 276, NULL, NULL, 1, '2025-10-27 17:23:34', 1),
(37, 3, 4, 121, 365, NULL, NULL, 1, '2025-10-27 17:23:34', 1),
(38, 3, 4, 99, 294, NULL, NULL, 0, '2025-10-27 17:23:34', 1),
(39, 3, 4, 95, 284, NULL, NULL, 1, '2025-10-27 17:23:34', 1),
(40, 3, 4, 115, 350, NULL, NULL, 1, '2025-10-27 17:23:34', 1),
(41, 72, 6, 183, 547, NULL, NULL, 1, '2025-10-27 22:24:39', 1),
(42, 72, 6, 177, 533, NULL, NULL, 1, '2025-10-27 22:24:39', 1),
(43, 72, 6, 195, 582, NULL, NULL, 0, '2025-10-27 22:24:39', 1),
(44, 72, 6, 189, 563, NULL, NULL, 1, '2025-10-27 22:24:39', 1),
(45, 72, 6, 181, 540, NULL, NULL, 1, '2025-10-27 22:24:39', 1),
(46, 72, 6, 174, 524, NULL, NULL, 1, '2025-10-27 22:24:39', 1),
(47, 72, 6, 175, 527, NULL, NULL, 0, '2025-10-27 22:24:39', 1),
(48, 72, 6, 201, 603, NULL, NULL, 1, '2025-10-27 22:24:39', 1),
(49, 72, 6, 202, 606, NULL, NULL, 1, '2025-10-27 22:24:39', 1),
(50, 72, 6, 192, 574, NULL, NULL, 1, '2025-10-27 22:24:39', 1),
(51, 72, 6, 194, 579, NULL, NULL, 1, '2025-10-27 22:24:39', 1),
(52, 72, 6, 200, 599, NULL, NULL, 1, '2025-10-27 22:24:39', 1),
(53, 72, 6, 186, 556, NULL, NULL, 1, '2025-10-27 22:24:39', 1),
(54, 72, 6, 197, 590, NULL, NULL, 1, '2025-10-27 22:24:39', 1),
(55, 72, 6, 188, 560, NULL, NULL, 0, '2025-10-27 22:24:39', 1),
(56, 72, 6, 204, 614, NULL, NULL, 1, '2025-10-27 22:24:39', 1),
(57, 72, 6, 199, 596, NULL, NULL, 1, '2025-10-27 22:24:39', 1),
(58, 72, 6, 193, 576, NULL, NULL, 1, '2025-10-27 22:24:39', 1),
(59, 72, 6, 203, 609, NULL, NULL, 0, '2025-10-27 22:24:39', 1),
(60, 72, 6, 190, 566, NULL, NULL, 1, '2025-10-27 22:24:39', 1),
(61, 80, 6, 174, 524, NULL, NULL, 1, '2025-10-27 22:27:49', 1),
(62, 80, 6, 181, 540, NULL, NULL, 1, '2025-10-27 22:27:49', 1),
(63, 80, 6, 176, 530, NULL, NULL, 0, '2025-10-27 22:27:49', 1),
(64, 80, 6, 194, 580, NULL, NULL, 0, '2025-10-27 22:27:49', 1),
(65, 80, 6, 193, 577, NULL, NULL, 0, '2025-10-27 22:27:49', 1),
(66, 80, 6, 204, 612, NULL, NULL, 0, '2025-10-27 22:27:49', 1),
(67, 80, 6, 195, 584, NULL, NULL, 1, '2025-10-27 22:27:49', 1),
(68, 80, 6, 177, 533, NULL, NULL, 1, '2025-10-27 22:27:49', 1),
(69, 80, 6, 188, 562, NULL, NULL, 1, '2025-10-27 22:27:49', 1),
(70, 80, 6, 202, 606, NULL, NULL, 1, '2025-10-27 22:27:49', 1),
(71, 80, 6, 185, 552, NULL, NULL, 1, '2025-10-27 22:27:49', 1),
(72, 80, 6, 198, 593, NULL, NULL, 1, '2025-10-27 22:27:49', 1),
(73, 80, 6, 189, 565, NULL, NULL, 0, '2025-10-27 22:27:49', 1),
(74, 80, 6, 182, 542, NULL, NULL, 0, '2025-10-27 22:27:49', 1),
(75, 80, 6, 197, 590, NULL, NULL, 1, '2025-10-27 22:27:49', 1),
(76, 80, 6, 199, 596, NULL, NULL, 1, '2025-10-27 22:27:49', 1),
(77, 80, 6, 175, 527, NULL, NULL, 0, '2025-10-27 22:27:49', 1),
(78, 80, 6, 191, 570, NULL, NULL, 0, '2025-10-27 22:27:49', 1),
(79, 80, 6, 183, 546, NULL, NULL, 0, '2025-10-27 22:27:49', 1),
(80, 80, 6, 192, 574, NULL, NULL, 1, '2025-10-27 22:27:49', 1),
(81, 87, 6, 181, 540, NULL, NULL, 1, '2025-10-27 22:28:01', 1),
(82, 87, 6, 191, 569, NULL, NULL, 0, '2025-10-27 22:28:01', 1),
(83, 87, 6, 197, 590, NULL, NULL, 1, '2025-10-27 22:28:01', 1),
(84, 87, 6, 204, 611, NULL, NULL, 0, '2025-10-27 22:28:01', 1),
(85, 87, 6, 193, 577, NULL, NULL, 0, '2025-10-27 22:28:01', 1),
(86, 87, 6, 201, 603, NULL, NULL, 1, '2025-10-27 22:28:01', 1),
(87, 87, 6, 186, 556, NULL, NULL, 1, '2025-10-27 22:28:01', 1),
(88, 87, 6, 174, 524, NULL, NULL, 1, '2025-10-27 22:28:01', 1),
(89, 87, 6, 183, 546, NULL, NULL, 0, '2025-10-27 22:28:01', 1),
(90, 87, 6, 194, 580, NULL, NULL, 0, '2025-10-27 22:28:01', 1),
(91, 87, 6, 190, 566, NULL, NULL, 1, '2025-10-27 22:28:01', 1),
(92, 87, 6, 175, 527, NULL, NULL, 0, '2025-10-27 22:28:01', 1),
(93, 87, 6, 188, 561, NULL, NULL, 0, '2025-10-27 22:28:01', 1),
(94, 87, 6, 198, 593, NULL, NULL, 1, '2025-10-27 22:28:01', 1),
(95, 87, 6, 187, 559, NULL, NULL, 0, '2025-10-27 22:28:01', 1),
(96, 87, 6, 202, 605, NULL, NULL, 0, '2025-10-27 22:28:01', 1),
(97, 87, 6, 189, 564, NULL, NULL, 0, '2025-10-27 22:28:01', 1),
(98, 87, 6, 182, 544, NULL, NULL, 1, '2025-10-27 22:28:01', 1),
(99, 87, 6, 192, 572, NULL, NULL, 0, '2025-10-27 22:28:01', 1),
(100, 87, 6, 180, 538, NULL, NULL, 0, '2025-10-27 22:28:01', 1),
(101, 78, 6, 174, 526, NULL, NULL, 0, '2025-10-27 22:28:22', 1),
(102, 78, 6, 191, 571, NULL, NULL, 1, '2025-10-27 22:28:22', 1),
(103, 78, 6, 203, 610, NULL, NULL, 0, '2025-10-27 22:28:22', 1),
(104, 78, 6, 190, 566, NULL, NULL, 1, '2025-10-27 22:28:22', 1),
(105, 78, 6, 177, 533, NULL, NULL, 1, '2025-10-27 22:28:22', 1),
(106, 78, 6, 197, 590, NULL, NULL, 1, '2025-10-27 22:28:22', 1),
(107, 78, 6, 195, 585, NULL, NULL, 0, '2025-10-27 22:28:22', 1),
(108, 78, 6, 199, 596, NULL, NULL, 1, '2025-10-27 22:28:22', 1),
(109, 78, 6, 180, 537, NULL, NULL, 1, '2025-10-27 22:28:22', 1),
(110, 78, 6, 194, 579, NULL, NULL, 1, '2025-10-27 22:28:22', 1),
(111, 78, 6, 183, 547, NULL, NULL, 1, '2025-10-27 22:28:22', 1),
(112, 78, 6, 189, 563, NULL, NULL, 1, '2025-10-27 22:28:22', 1),
(113, 78, 6, 187, 557, NULL, NULL, 1, '2025-10-27 22:28:22', 1),
(114, 78, 6, 198, 593, NULL, NULL, 1, '2025-10-27 22:28:22', 1),
(115, 78, 6, 181, 540, NULL, NULL, 1, '2025-10-27 22:28:22', 1),
(116, 78, 6, 184, 548, NULL, NULL, 0, '2025-10-27 22:28:22', 1),
(117, 78, 6, 176, 530, NULL, NULL, 0, '2025-10-27 22:28:22', 1),
(118, 78, 6, 175, 527, NULL, NULL, 0, '2025-10-27 22:28:22', 1),
(119, 78, 6, 196, 589, NULL, NULL, 1, '2025-10-27 22:28:22', 1),
(120, 78, 6, 185, 552, NULL, NULL, 1, '2025-10-27 22:28:22', 1),
(121, 79, 6, 175, 527, NULL, NULL, 0, '2025-10-27 22:28:27', 1),
(122, 79, 6, 185, 552, NULL, NULL, 1, '2025-10-27 22:28:27', 1),
(123, 79, 6, 192, 574, NULL, NULL, 1, '2025-10-27 22:28:27', 1),
(124, 79, 6, 187, 557, NULL, NULL, 1, '2025-10-27 22:28:27', 1),
(125, 79, 6, 174, 526, NULL, NULL, 0, '2025-10-27 22:28:27', 1),
(126, 79, 6, 180, 538, NULL, NULL, 0, '2025-10-27 22:28:27', 1),
(127, 79, 6, 183, 547, NULL, NULL, 1, '2025-10-27 22:28:27', 1),
(128, 79, 6, 182, 544, NULL, NULL, 1, '2025-10-27 22:28:27', 1),
(129, 79, 6, 177, 533, NULL, NULL, 1, '2025-10-27 22:28:27', 1),
(130, 79, 6, 186, 555, NULL, NULL, 0, '2025-10-27 22:28:27', 1),
(131, 79, 6, 198, 593, NULL, NULL, 1, '2025-10-27 22:28:27', 1),
(132, 79, 6, 176, 530, NULL, NULL, 0, '2025-10-27 22:28:27', 1),
(133, 79, 6, 178, 534, NULL, NULL, 0, '2025-10-27 22:28:27', 1),
(134, 79, 6, 200, 598, NULL, NULL, 0, '2025-10-27 22:28:27', 1),
(135, 79, 6, 195, 584, NULL, NULL, 1, '2025-10-27 22:28:27', 1),
(136, 79, 6, 193, 575, NULL, NULL, 0, '2025-10-27 22:28:27', 1),
(137, 79, 6, 199, 596, NULL, NULL, 1, '2025-10-27 22:28:27', 1),
(138, 79, 6, 184, 548, NULL, NULL, 0, '2025-10-27 22:28:27', 1),
(139, 79, 6, 194, 581, NULL, NULL, 0, '2025-10-27 22:28:27', 1),
(140, 79, 6, 196, 587, NULL, NULL, 0, '2025-10-27 22:28:27', 1),
(141, 76, 6, 197, 590, NULL, NULL, 1, '2025-10-27 22:30:25', 1),
(142, 76, 6, 182, 544, NULL, NULL, 1, '2025-10-27 22:30:25', 1),
(143, 76, 6, 175, 527, NULL, NULL, 0, '2025-10-27 22:30:25', 1),
(144, 76, 6, 202, 604, NULL, NULL, 0, '2025-10-27 22:30:25', 1),
(145, 76, 6, 203, 608, NULL, NULL, 1, '2025-10-27 22:30:25', 1),
(146, 76, 6, 184, 548, NULL, NULL, 0, '2025-10-27 22:30:25', 1),
(147, 76, 6, 183, 546, NULL, NULL, 0, '2025-10-27 22:30:25', 1),
(148, 76, 6, 200, 599, NULL, NULL, 1, '2025-10-27 22:30:25', 1),
(149, 76, 6, 174, 524, NULL, NULL, 1, '2025-10-27 22:30:25', 1),
(150, 76, 6, 192, 574, NULL, NULL, 1, '2025-10-27 22:30:25', 1),
(151, 76, 6, 187, 557, NULL, NULL, 1, '2025-10-27 22:30:25', 1),
(152, 76, 6, 178, 534, NULL, NULL, 0, '2025-10-27 22:30:25', 1),
(153, 76, 6, 204, 613, NULL, NULL, 0, '2025-10-27 22:30:25', 1),
(154, 76, 6, 193, 576, NULL, NULL, 1, '2025-10-27 22:30:25', 1),
(155, 76, 6, 195, 584, NULL, NULL, 1, '2025-10-27 22:30:25', 1),
(156, 76, 6, 188, 560, NULL, NULL, 0, '2025-10-27 22:30:25', 1),
(157, 76, 6, 176, 530, NULL, NULL, 0, '2025-10-27 22:30:25', 1),
(158, 76, 6, 194, 579, NULL, NULL, 1, '2025-10-27 22:30:25', 1),
(159, 76, 6, 177, 533, NULL, NULL, 1, '2025-10-27 22:30:25', 1),
(160, 76, 6, 181, 540, NULL, NULL, 1, '2025-10-27 22:30:25', 1),
(161, 88, 6, 174, 526, NULL, NULL, 0, '2025-10-27 22:30:29', 1),
(162, 88, 6, 197, 591, NULL, NULL, 0, '2025-10-27 22:30:29', 1),
(163, 88, 6, 204, 613, NULL, NULL, 0, '2025-10-27 22:30:29', 1),
(164, 88, 6, 193, 575, NULL, NULL, 0, '2025-10-27 22:30:29', 1),
(165, 88, 6, 198, 592, NULL, NULL, 0, '2025-10-27 22:30:29', 1),
(166, 88, 6, 203, 608, NULL, NULL, 1, '2025-10-27 22:30:29', 1),
(167, 88, 6, 191, 571, NULL, NULL, 1, '2025-10-27 22:30:29', 1),
(168, 88, 6, 186, 556, NULL, NULL, 1, '2025-10-27 22:30:29', 1),
(169, 88, 6, 192, 574, NULL, NULL, 1, '2025-10-27 22:30:29', 1),
(170, 88, 6, 185, 552, NULL, NULL, 1, '2025-10-27 22:30:29', 1),
(171, 88, 6, 200, 599, NULL, NULL, 1, '2025-10-27 22:30:29', 1),
(172, 88, 6, 176, 530, NULL, NULL, 0, '2025-10-27 22:30:29', 1),
(173, 88, 6, 195, 585, NULL, NULL, 0, '2025-10-27 22:30:29', 1),
(174, 88, 6, 180, 537, NULL, NULL, 1, '2025-10-27 22:30:29', 1),
(175, 88, 6, 194, 579, NULL, NULL, 1, '2025-10-27 22:30:29', 1),
(176, 88, 6, 199, 597, NULL, NULL, 0, '2025-10-27 22:30:29', 1),
(177, 88, 6, 202, 604, NULL, NULL, 0, '2025-10-27 22:30:29', 1),
(178, 88, 6, 201, 602, NULL, NULL, 0, '2025-10-27 22:30:29', 1),
(179, 88, 6, 183, 547, NULL, NULL, 1, '2025-10-27 22:30:29', 1),
(180, 88, 6, 177, 533, NULL, NULL, 1, '2025-10-27 22:30:29', 1),
(181, 86, 6, 192, 574, NULL, NULL, 1, '2025-10-27 22:31:42', 1),
(182, 86, 6, 194, 579, NULL, NULL, 1, '2025-10-27 22:31:42', 1),
(183, 86, 6, 198, 593, NULL, NULL, 1, '2025-10-27 22:31:42', 1),
(184, 86, 6, 185, 552, NULL, NULL, 1, '2025-10-27 22:31:42', 1),
(185, 86, 6, 204, 613, NULL, NULL, 0, '2025-10-27 22:31:42', 1),
(186, 86, 6, 182, 544, NULL, NULL, 1, '2025-10-27 22:31:42', 1),
(187, 86, 6, 195, 582, NULL, NULL, 0, '2025-10-27 22:31:42', 1),
(188, 86, 6, 181, 540, NULL, NULL, 1, '2025-10-27 22:31:42', 1),
(189, 86, 6, 202, 606, NULL, NULL, 1, '2025-10-27 22:31:42', 1),
(190, 86, 6, 201, 602, NULL, NULL, 0, '2025-10-27 22:31:42', 1),
(191, 86, 6, 188, 560, NULL, NULL, 0, '2025-10-27 22:31:42', 1),
(192, 86, 6, 175, 527, NULL, NULL, 0, '2025-10-27 22:31:42', 1),
(193, 86, 6, 174, 524, NULL, NULL, 1, '2025-10-27 22:31:42', 1),
(194, 86, 6, 184, 548, NULL, NULL, 0, '2025-10-27 22:31:42', 1),
(195, 86, 6, 180, 538, NULL, NULL, 0, '2025-10-27 22:31:42', 1),
(196, 86, 6, 186, 556, NULL, NULL, 1, '2025-10-27 22:31:42', 1),
(197, 86, 6, 193, 576, NULL, NULL, 1, '2025-10-27 22:31:42', 1),
(198, 86, 6, 200, 600, NULL, NULL, 0, '2025-10-27 22:31:42', 1),
(199, 86, 6, 190, 566, NULL, NULL, 1, '2025-10-27 22:31:42', 1),
(200, 86, 6, 189, 563, NULL, NULL, 1, '2025-10-27 22:31:42', 1),
(201, 90, 6, 195, 582, NULL, NULL, 0, '2025-10-27 22:31:47', 1),
(202, 90, 6, 176, 530, NULL, NULL, 0, '2025-10-27 22:31:47', 1),
(203, 90, 6, 188, 562, NULL, NULL, 1, '2025-10-27 22:31:47', 1),
(204, 90, 6, 202, 606, NULL, NULL, 1, '2025-10-27 22:31:47', 1),
(205, 90, 6, 174, 524, NULL, NULL, 1, '2025-10-27 22:31:47', 1),
(206, 90, 6, 197, 591, NULL, NULL, 0, '2025-10-27 22:31:47', 1),
(207, 90, 6, 185, 552, NULL, NULL, 1, '2025-10-27 22:31:47', 1),
(208, 90, 6, 186, 556, NULL, NULL, 1, '2025-10-27 22:31:47', 1),
(209, 90, 6, 200, 598, NULL, NULL, 0, '2025-10-27 22:31:47', 1),
(210, 90, 6, 178, 535, NULL, NULL, 1, '2025-10-27 22:31:47', 1),
(211, 90, 6, 184, 548, NULL, NULL, 0, '2025-10-27 22:31:47', 1),
(212, 90, 6, 196, 586, NULL, NULL, 0, '2025-10-27 22:31:47', 1),
(213, 90, 6, 190, 566, NULL, NULL, 1, '2025-10-27 22:31:47', 1),
(214, 90, 6, 187, 557, NULL, NULL, 1, '2025-10-27 22:31:47', 1),
(215, 90, 6, 199, 596, NULL, NULL, 1, '2025-10-27 22:31:47', 1),
(216, 90, 6, 204, 613, NULL, NULL, 0, '2025-10-27 22:31:47', 1),
(217, 90, 6, 201, 602, NULL, NULL, 0, '2025-10-27 22:31:47', 1),
(218, 90, 6, 189, 563, NULL, NULL, 1, '2025-10-27 22:31:47', 1),
(219, 90, 6, 193, 576, NULL, NULL, 1, '2025-10-27 22:31:47', 1),
(220, 90, 6, 183, 546, NULL, NULL, 0, '2025-10-27 22:31:47', 1),
(221, 75, 6, 180, 538, NULL, NULL, 0, '2025-10-27 22:32:05', 1),
(222, 75, 6, 200, 599, NULL, NULL, 1, '2025-10-27 22:32:05', 1),
(223, 75, 6, 183, 546, NULL, NULL, 0, '2025-10-27 22:32:05', 1),
(224, 75, 6, 178, 536, NULL, NULL, 0, '2025-10-27 22:32:05', 1),
(225, 75, 6, 182, 544, NULL, NULL, 1, '2025-10-27 22:32:05', 1),
(226, 75, 6, 184, 551, NULL, NULL, 1, '2025-10-27 22:32:05', 1),
(227, 75, 6, 195, 585, NULL, NULL, 0, '2025-10-27 22:32:05', 1),
(228, 75, 6, 202, 605, NULL, NULL, 0, '2025-10-27 22:32:05', 1),
(229, 75, 6, 197, 590, NULL, NULL, 1, '2025-10-27 22:32:05', 1),
(230, 75, 6, 175, 527, NULL, NULL, 0, '2025-10-27 22:32:05', 1),
(231, 75, 6, 203, 607, NULL, NULL, 0, '2025-10-27 22:32:05', 1),
(232, 75, 6, 191, 569, NULL, NULL, 0, '2025-10-27 22:32:05', 1),
(233, 75, 6, 186, 554, NULL, NULL, 0, '2025-10-27 22:32:05', 1),
(234, 75, 6, 185, 552, NULL, NULL, 1, '2025-10-27 22:32:05', 1),
(235, 75, 6, 188, 560, NULL, NULL, 0, '2025-10-27 22:32:05', 1),
(236, 75, 6, 193, 576, NULL, NULL, 1, '2025-10-27 22:32:05', 1),
(237, 75, 6, 192, 574, NULL, NULL, 1, '2025-10-27 22:32:05', 1),
(238, 75, 6, 199, 595, NULL, NULL, 0, '2025-10-27 22:32:05', 1),
(239, 75, 6, 187, 559, NULL, NULL, 0, '2025-10-27 22:32:05', 1),
(240, 75, 6, 194, 579, NULL, NULL, 1, '2025-10-27 22:32:05', 1),
(241, 73, 6, 184, 549, NULL, NULL, 0, '2025-10-27 22:32:25', 1),
(242, 73, 6, 186, 556, NULL, NULL, 1, '2025-10-27 22:32:25', 1),
(243, 73, 6, 201, 603, NULL, NULL, 1, '2025-10-27 22:32:25', 1),
(244, 73, 6, 183, 546, NULL, NULL, 0, '2025-10-27 22:32:25', 1),
(245, 73, 6, 174, 524, NULL, NULL, 1, '2025-10-27 22:32:25', 1),
(246, 73, 6, 200, 599, NULL, NULL, 1, '2025-10-27 22:32:25', 1),
(247, 73, 6, 180, 537, NULL, NULL, 1, '2025-10-27 22:32:25', 1),
(248, 73, 6, 177, 533, NULL, NULL, 1, '2025-10-27 22:32:25', 1),
(249, 73, 6, 202, 606, NULL, NULL, 1, '2025-10-27 22:32:25', 1),
(250, 73, 6, 181, 540, NULL, NULL, 1, '2025-10-27 22:32:25', 1),
(251, 73, 6, 204, 614, NULL, NULL, 1, '2025-10-27 22:32:25', 1),
(252, 73, 6, 175, 528, NULL, NULL, 0, '2025-10-27 22:32:25', 1),
(253, 73, 6, 193, 577, NULL, NULL, 0, '2025-10-27 22:32:25', 1),
(254, 73, 6, 188, 561, NULL, NULL, 0, '2025-10-27 22:32:25', 1),
(255, 73, 6, 176, 530, NULL, NULL, 0, '2025-10-27 22:32:25', 1),
(256, 73, 6, 195, 584, NULL, NULL, 1, '2025-10-27 22:32:25', 1),
(257, 73, 6, 190, 568, NULL, NULL, 0, '2025-10-27 22:32:25', 1),
(258, 73, 6, 178, 534, NULL, NULL, 0, '2025-10-27 22:32:25', 1),
(259, 73, 6, 182, 544, NULL, NULL, 1, '2025-10-27 22:32:25', 1),
(260, 73, 6, 203, 608, NULL, NULL, 1, '2025-10-27 22:32:25', 1),
(261, 91, 6, 175, 527, NULL, NULL, 0, '2025-10-27 22:32:33', 1),
(262, 91, 6, 181, 540, NULL, NULL, 1, '2025-10-27 22:32:33', 1),
(263, 91, 6, 200, 598, NULL, NULL, 0, '2025-10-27 22:32:33', 1),
(264, 91, 6, 193, 576, NULL, NULL, 1, '2025-10-27 22:32:33', 1),
(265, 91, 6, 188, 560, NULL, NULL, 0, '2025-10-27 22:32:33', 1),
(266, 91, 6, 190, 568, NULL, NULL, 0, '2025-10-27 22:32:33', 1),
(267, 91, 6, 197, 590, NULL, NULL, 1, '2025-10-27 22:32:33', 1),
(268, 91, 6, 198, 593, NULL, NULL, 1, '2025-10-27 22:32:33', 1),
(269, 91, 6, 185, 552, NULL, NULL, 1, '2025-10-27 22:32:33', 1),
(270, 91, 6, 174, 524, NULL, NULL, 1, '2025-10-27 22:32:33', 1),
(271, 91, 6, 176, 531, NULL, NULL, 1, '2025-10-27 22:32:33', 1),
(272, 91, 6, 189, 565, NULL, NULL, 0, '2025-10-27 22:32:33', 1),
(273, 91, 6, 195, 584, NULL, NULL, 1, '2025-10-27 22:32:33', 1),
(274, 91, 6, 186, 556, NULL, NULL, 1, '2025-10-27 22:32:33', 1),
(275, 91, 6, 178, 534, NULL, NULL, 0, '2025-10-27 22:32:33', 1),
(276, 91, 6, 177, 533, NULL, NULL, 1, '2025-10-27 22:32:33', 1),
(277, 91, 6, 187, 557, NULL, NULL, 1, '2025-10-27 22:32:33', 1),
(278, 91, 6, 199, 596, NULL, NULL, 1, '2025-10-27 22:32:33', 1),
(279, 91, 6, 183, 547, NULL, NULL, 1, '2025-10-27 22:32:33', 1),
(280, 91, 6, 184, 548, NULL, NULL, 0, '2025-10-27 22:32:33', 1),
(281, 85, 6, 175, 527, NULL, NULL, 0, '2025-10-27 22:32:36', 1),
(282, 85, 6, 176, 530, NULL, NULL, 0, '2025-10-27 22:32:36', 1),
(283, 85, 6, 174, 524, NULL, NULL, 1, '2025-10-27 22:32:36', 1),
(284, 85, 6, 183, 545, NULL, NULL, 0, '2025-10-27 22:32:36', 1),
(285, 85, 6, 193, 577, NULL, NULL, 0, '2025-10-27 22:32:36', 1),
(286, 85, 6, 192, 574, NULL, NULL, 1, '2025-10-27 22:32:36', 1),
(287, 85, 6, 194, 580, NULL, NULL, 0, '2025-10-27 22:32:36', 1),
(288, 85, 6, 177, 532, NULL, NULL, 0, '2025-10-27 22:32:36', 1),
(289, 85, 6, 191, 571, NULL, NULL, 1, '2025-10-27 22:32:36', 1),
(290, 85, 6, 185, 552, NULL, NULL, 1, '2025-10-27 22:32:36', 1),
(291, 85, 6, 190, 566, NULL, NULL, 1, '2025-10-27 22:32:36', 1),
(292, 85, 6, 195, 585, NULL, NULL, 0, '2025-10-27 22:32:36', 1),
(293, 85, 6, 197, 590, NULL, NULL, 1, '2025-10-27 22:32:36', 1),
(294, 85, 6, 186, 554, NULL, NULL, 0, '2025-10-27 22:32:36', 1),
(295, 85, 6, 188, 560, NULL, NULL, 0, '2025-10-27 22:32:36', 1),
(296, 85, 6, 181, 540, NULL, NULL, 1, '2025-10-27 22:32:36', 1),
(297, 85, 6, 196, 586, NULL, NULL, 0, '2025-10-27 22:32:36', 1),
(298, 85, 6, 182, 542, NULL, NULL, 0, '2025-10-27 22:32:36', 1),
(299, 85, 6, 187, 557, NULL, NULL, 1, '2025-10-27 22:32:36', 1),
(300, 85, 6, 198, 592, NULL, NULL, 0, '2025-10-27 22:32:36', 1),
(301, 82, 6, 194, 580, NULL, NULL, 0, '2025-10-27 22:32:47', 1),
(302, 82, 6, 176, 530, NULL, NULL, 0, '2025-10-27 22:32:47', 1),
(303, 82, 6, 188, 561, NULL, NULL, 0, '2025-10-27 22:32:47', 1),
(304, 82, 6, 181, 540, NULL, NULL, 1, '2025-10-27 22:32:47', 1),
(305, 82, 6, 175, 527, NULL, NULL, 0, '2025-10-27 22:32:47', 1),
(306, 82, 6, 190, 566, NULL, NULL, 1, '2025-10-27 22:32:47', 1),
(307, 82, 6, 198, 593, NULL, NULL, 1, '2025-10-27 22:32:47', 1),
(308, 82, 6, 187, 558, NULL, NULL, 0, '2025-10-27 22:32:47', 1),
(309, 82, 6, 184, 548, NULL, NULL, 0, '2025-10-27 22:32:47', 1),
(310, 82, 6, 186, 555, NULL, NULL, 0, '2025-10-27 22:32:47', 1),
(311, 82, 6, 193, 575, NULL, NULL, 0, '2025-10-27 22:32:47', 1),
(312, 82, 6, 200, 599, NULL, NULL, 1, '2025-10-27 22:32:47', 1),
(313, 82, 6, 201, 602, NULL, NULL, 0, '2025-10-27 22:32:47', 1),
(314, 82, 6, 174, 526, NULL, NULL, 0, '2025-10-27 22:32:47', 1),
(315, 82, 6, 196, 587, NULL, NULL, 0, '2025-10-27 22:32:47', 1),
(316, 82, 6, 203, 608, NULL, NULL, 1, '2025-10-27 22:32:47', 1),
(317, 82, 6, 178, 534, NULL, NULL, 0, '2025-10-27 22:32:47', 1),
(318, 82, 6, 199, 596, NULL, NULL, 1, '2025-10-27 22:32:47', 1),
(319, 82, 6, 191, 569, NULL, NULL, 0, '2025-10-27 22:32:47', 1),
(320, 82, 6, 182, 542, NULL, NULL, 0, '2025-10-27 22:32:47', 1),
(321, 93, 6, 197, 591, NULL, NULL, 0, '2025-10-27 22:33:15', 1),
(322, 93, 6, 178, 536, NULL, NULL, 0, '2025-10-27 22:33:15', 1),
(323, 93, 6, 188, 560, NULL, NULL, 0, '2025-10-27 22:33:15', 1),
(324, 93, 6, 200, 599, NULL, NULL, 1, '2025-10-27 22:33:15', 1),
(325, 93, 6, 176, 530, NULL, NULL, 0, '2025-10-27 22:33:15', 1),
(326, 93, 6, 195, 584, NULL, NULL, 1, '2025-10-27 22:33:15', 1),
(327, 93, 6, 202, 605, NULL, NULL, 0, '2025-10-27 22:33:15', 1),
(328, 93, 6, 182, 544, NULL, NULL, 1, '2025-10-27 22:33:15', 1),
(329, 93, 6, 174, 526, NULL, NULL, 0, '2025-10-27 22:33:15', 1),
(330, 93, 6, 203, 608, NULL, NULL, 1, '2025-10-27 22:33:15', 1),
(331, 93, 6, 181, 540, NULL, NULL, 1, '2025-10-27 22:33:15', 1),
(332, 93, 6, 184, 548, NULL, NULL, 0, '2025-10-27 22:33:15', 1),
(333, 93, 6, 198, 592, NULL, NULL, 0, '2025-10-27 22:33:15', 1),
(334, 93, 6, 180, 537, NULL, NULL, 1, '2025-10-27 22:33:15', 1),
(335, 93, 6, 192, 572, NULL, NULL, 0, '2025-10-27 22:33:15', 1),
(336, 93, 6, 187, 557, NULL, NULL, 1, '2025-10-27 22:33:15', 1),
(337, 93, 6, 186, 556, NULL, NULL, 1, '2025-10-27 22:33:15', 1),
(338, 93, 6, 175, 527, NULL, NULL, 0, '2025-10-27 22:33:15', 1),
(339, 93, 6, 189, 563, NULL, NULL, 1, '2025-10-27 22:33:15', 1),
(340, 93, 6, 194, 579, NULL, NULL, 1, '2025-10-27 22:33:15', 1),
(341, 77, 6, 187, 557, NULL, NULL, 1, '2025-10-27 22:33:27', 1),
(342, 77, 6, 190, 566, NULL, NULL, 1, '2025-10-27 22:33:27', 1),
(343, 77, 6, 188, 560, NULL, NULL, 0, '2025-10-27 22:33:27', 1),
(344, 77, 6, 193, 576, NULL, NULL, 1, '2025-10-27 22:33:27', 1),
(345, 77, 6, 201, 603, NULL, NULL, 1, '2025-10-27 22:33:27', 1),
(346, 77, 6, 198, 593, NULL, NULL, 1, '2025-10-27 22:33:27', 1),
(347, 77, 6, 204, 613, NULL, NULL, 0, '2025-10-27 22:33:27', 1),
(348, 77, 6, 197, 590, NULL, NULL, 1, '2025-10-27 22:33:27', 1),
(349, 77, 6, 195, 582, NULL, NULL, 0, '2025-10-27 22:33:27', 1),
(350, 77, 6, 176, 530, NULL, NULL, 0, '2025-10-27 22:33:27', 1),
(351, 77, 6, 199, 596, NULL, NULL, 1, '2025-10-27 22:33:27', 1),
(352, 77, 6, 192, 574, NULL, NULL, 1, '2025-10-27 22:33:27', 1),
(353, 77, 6, 174, 524, NULL, NULL, 1, '2025-10-27 22:33:27', 1),
(354, 77, 6, 202, 605, NULL, NULL, 0, '2025-10-27 22:33:27', 1),
(355, 77, 6, 183, 546, NULL, NULL, 0, '2025-10-27 22:33:27', 1),
(356, 77, 6, 191, 571, NULL, NULL, 1, '2025-10-27 22:33:27', 1),
(357, 77, 6, 178, 535, NULL, NULL, 1, '2025-10-27 22:33:27', 1),
(358, 77, 6, 177, 532, NULL, NULL, 0, '2025-10-27 22:33:27', 1),
(359, 77, 6, 189, 563, NULL, NULL, 1, '2025-10-27 22:33:27', 1),
(360, 77, 6, 181, 541, NULL, NULL, 0, '2025-10-27 22:33:27', 1),
(361, 81, 6, 181, 540, NULL, NULL, 1, '2025-10-27 22:35:43', 1),
(362, 81, 6, 180, 538, NULL, NULL, 0, '2025-10-27 22:35:43', 1),
(363, 81, 6, 196, 587, NULL, NULL, 0, '2025-10-27 22:35:43', 1),
(364, 81, 6, 195, 583, NULL, NULL, 0, '2025-10-27 22:35:43', 1),
(365, 81, 6, 190, 568, NULL, NULL, 0, '2025-10-27 22:35:43', 1),
(366, 81, 6, 174, 526, NULL, NULL, 0, '2025-10-27 22:35:43', 1),
(367, 81, 6, 197, 590, NULL, NULL, 1, '2025-10-27 22:35:43', 1),
(368, 81, 6, 192, 573, NULL, NULL, 0, '2025-10-27 22:35:43', 1),
(369, 81, 6, 204, 613, NULL, NULL, 0, '2025-10-27 22:35:43', 1),
(370, 81, 6, 193, 575, NULL, NULL, 0, '2025-10-27 22:35:43', 1),
(371, 81, 6, 177, 532, NULL, NULL, 0, '2025-10-27 22:35:43', 1),
(372, 81, 6, 187, 559, NULL, NULL, 0, '2025-10-27 22:35:43', 1),
(373, 81, 6, 198, 592, NULL, NULL, 0, '2025-10-27 22:35:43', 1),
(374, 81, 6, 202, 605, NULL, NULL, 0, '2025-10-27 22:35:43', 1),
(375, 81, 6, 201, 602, NULL, NULL, 0, '2025-10-27 22:35:43', 1),
(376, 81, 6, 191, 569, NULL, NULL, 0, '2025-10-27 22:35:43', 1),
(377, 81, 6, 183, 545, NULL, NULL, 0, '2025-10-27 22:35:43', 1),
(378, 81, 6, 199, 597, NULL, NULL, 0, '2025-10-27 22:35:43', 1),
(379, 81, 6, 186, 555, NULL, NULL, 0, '2025-10-27 22:35:43', 1),
(380, 81, 6, 175, 527, NULL, NULL, 0, '2025-10-27 22:35:43', 1),
(381, 89, 6, 201, 602, NULL, NULL, 0, '2025-10-27 22:37:05', 1),
(382, 89, 6, 191, 571, NULL, NULL, 1, '2025-10-27 22:37:05', 1),
(383, 89, 6, 176, 530, NULL, NULL, 0, '2025-10-27 22:37:05', 1),
(384, 89, 6, 184, 550, NULL, NULL, 0, '2025-10-27 22:37:05', 1),
(385, 89, 6, 182, 542, NULL, NULL, 0, '2025-10-27 22:37:05', 1),
(386, 89, 6, 174, 524, NULL, NULL, 1, '2025-10-27 22:37:05', 1),
(387, 89, 6, 193, 576, NULL, NULL, 1, '2025-10-27 22:37:05', 1),
(388, 89, 6, 203, 608, NULL, NULL, 1, '2025-10-27 22:37:05', 1),
(389, 89, 6, 177, 533, NULL, NULL, 1, '2025-10-27 22:37:05', 1),
(390, 89, 6, 195, 584, NULL, NULL, 1, '2025-10-27 22:37:05', 1),
(391, 89, 6, 186, 554, NULL, NULL, 0, '2025-10-27 22:37:05', 1),
(392, 89, 6, 202, 604, NULL, NULL, 0, '2025-10-27 22:37:05', 1),
(393, 89, 6, 192, 574, NULL, NULL, 1, '2025-10-27 22:37:05', 1),
(394, 89, 6, 189, 563, NULL, NULL, 1, '2025-10-27 22:37:05', 1),
(395, 89, 6, 194, 579, NULL, NULL, 1, '2025-10-27 22:37:05', 1),
(396, 89, 6, 185, 552, NULL, NULL, 1, '2025-10-27 22:37:05', 1),
(397, 89, 6, 178, 535, NULL, NULL, 1, '2025-10-27 22:37:05', 1),
(398, 89, 6, 196, 589, NULL, NULL, 1, '2025-10-27 22:37:05', 1),
(399, 89, 6, 204, 613, NULL, NULL, 0, '2025-10-27 22:37:05', 1),
(400, 89, 6, 187, 559, NULL, NULL, 0, '2025-10-27 22:37:05', 1),
(401, 84, 6, 204, NULL, NULL, NULL, NULL, '2025-10-27 22:37:46', 1),
(402, 84, 6, 177, 533, NULL, NULL, 1, '2025-10-27 22:37:46', 1),
(403, 84, 6, 202, 606, NULL, NULL, 1, '2025-10-27 22:37:46', 1),
(404, 84, 6, 184, 550, NULL, NULL, 0, '2025-10-27 22:37:46', 1),
(405, 84, 6, 193, 575, NULL, NULL, 0, '2025-10-27 22:37:46', 1),
(406, 84, 6, 178, 534, NULL, NULL, 0, '2025-10-27 22:37:46', 1),
(407, 84, 6, 196, 587, NULL, NULL, 0, '2025-10-27 22:37:46', 1),
(408, 84, 6, 200, 599, NULL, NULL, 1, '2025-10-27 22:37:46', 1),
(409, 84, 6, 199, 596, NULL, NULL, 1, '2025-10-27 22:37:46', 1),
(410, 84, 6, 191, 569, NULL, NULL, 0, '2025-10-27 22:37:46', 1),
(411, 84, 6, 187, 557, NULL, NULL, 1, '2025-10-27 22:37:46', 1),
(412, 84, 6, 201, 603, NULL, NULL, 1, '2025-10-27 22:37:46', 1),
(413, 84, 6, 198, 593, NULL, NULL, 1, '2025-10-27 22:37:46', 1),
(414, 84, 6, 192, 574, NULL, NULL, 1, '2025-10-27 22:37:46', 1),
(415, 84, 6, 181, 540, NULL, NULL, 1, '2025-10-27 22:37:46', 1),
(416, 84, 6, 183, 547, NULL, NULL, 1, '2025-10-27 22:37:46', 1),
(417, 84, 6, 189, 564, NULL, NULL, 0, '2025-10-27 22:37:46', 1),
(418, 84, 6, 176, 530, NULL, NULL, 0, '2025-10-27 22:37:46', 1),
(419, 84, 6, 185, 552, NULL, NULL, 1, '2025-10-27 22:37:46', 1),
(420, 84, 6, 195, 583, NULL, NULL, 0, '2025-10-27 22:37:46', 1),
(421, 92, 6, 174, 524, NULL, NULL, 1, '2025-10-27 22:38:55', 1),
(422, 92, 6, 195, 582, NULL, NULL, 0, '2025-10-27 22:38:55', 1),
(423, 92, 6, 196, 587, NULL, NULL, 0, '2025-10-27 22:38:55', 1),
(424, 92, 6, 182, 542, NULL, NULL, 0, '2025-10-27 22:38:55', 1),
(425, 92, 6, 199, 596, NULL, NULL, 1, '2025-10-27 22:38:55', 1),
(426, 92, 6, 190, 566, NULL, NULL, 1, '2025-10-27 22:38:55', 1),
(427, 92, 6, 181, 540, NULL, NULL, 1, '2025-10-27 22:38:55', 1),
(428, 92, 6, 192, 572, NULL, NULL, 0, '2025-10-27 22:38:55', 1),
(429, 92, 6, 197, 590, NULL, NULL, 1, '2025-10-27 22:38:55', 1),
(430, 92, 6, 184, 548, NULL, NULL, 0, '2025-10-27 22:38:55', 1),
(431, 92, 6, 180, 538, NULL, NULL, 0, '2025-10-27 22:38:55', 1),
(432, 92, 6, 186, 554, NULL, NULL, 0, '2025-10-27 22:38:55', 1),
(433, 92, 6, 189, 564, NULL, NULL, 0, '2025-10-27 22:38:55', 1),
(434, 92, 6, 193, 575, NULL, NULL, 0, '2025-10-27 22:38:55', 1),
(435, 92, 6, 176, 530, NULL, NULL, 0, '2025-10-27 22:38:55', 1),
(436, 92, 6, 191, 569, NULL, NULL, 0, '2025-10-27 22:38:55', 1),
(437, 92, 6, 203, 607, NULL, NULL, 0, '2025-10-27 22:38:55', 1),
(438, 92, 6, 201, 601, NULL, NULL, 0, '2025-10-27 22:38:55', 1),
(439, 92, 6, 185, 552, NULL, NULL, 1, '2025-10-27 22:38:55', 1),
(440, 92, 6, 188, 560, NULL, NULL, 0, '2025-10-27 22:38:55', 1),
(441, 83, 6, 190, 567, NULL, NULL, 0, '2025-10-27 22:40:13', 1),
(442, 83, 6, 202, 604, NULL, NULL, 0, '2025-10-27 22:40:13', 1),
(443, 83, 6, 183, 546, NULL, NULL, 0, '2025-10-27 22:40:13', 1),
(444, 83, 6, 180, 537, NULL, NULL, 1, '2025-10-27 22:40:13', 1),
(445, 83, 6, 184, 548, NULL, NULL, 0, '2025-10-27 22:40:13', 1),
(446, 83, 6, 197, 590, NULL, NULL, 1, '2025-10-27 22:40:13', 1),
(447, 83, 6, 175, 527, NULL, NULL, 0, '2025-10-27 22:40:13', 1),
(448, 83, 6, 199, 595, NULL, NULL, 0, '2025-10-27 22:40:13', 1),
(449, 83, 6, 187, 557, NULL, NULL, 1, '2025-10-27 22:40:13', 1),
(450, 83, 6, 200, 598, NULL, NULL, 0, '2025-10-27 22:40:13', 1),
(451, 83, 6, 192, 573, NULL, NULL, 0, '2025-10-27 22:40:13', 1),
(452, 83, 6, 193, 575, NULL, NULL, 0, '2025-10-27 22:40:13', 1),
(453, 83, 6, 178, 536, NULL, NULL, 0, '2025-10-27 22:40:13', 1),
(454, 83, 6, 198, 594, NULL, NULL, 0, '2025-10-27 22:40:13', 1),
(455, 83, 6, 185, 552, NULL, NULL, 1, '2025-10-27 22:40:13', 1),
(456, 83, 6, 176, 530, NULL, NULL, 0, '2025-10-27 22:40:13', 1),
(457, 83, 6, 196, 587, NULL, NULL, 0, '2025-10-27 22:40:13', 1),
(458, 83, 6, 186, 555, NULL, NULL, 0, '2025-10-27 22:40:13', 1),
(459, 83, 6, 177, 532, NULL, NULL, 0, '2025-10-27 22:40:13', 1),
(460, 83, 6, 189, 563, NULL, NULL, 1, '2025-10-27 22:40:13', 1),
(461, 92, 3, 71, 209, NULL, NULL, 1, '2025-10-27 22:43:18', 1),
(462, 92, 3, 67, 196, NULL, NULL, 0, '2025-10-27 22:43:18', 1),
(463, 92, 3, 69, 203, NULL, NULL, 0, '2025-10-27 22:43:18', 1),
(464, 92, 3, 50, 147, NULL, NULL, 0, '2025-10-27 22:43:18', 1),
(465, 92, 3, 74, 217, NULL, NULL, 0, '2025-10-27 22:43:18', 1),
(466, 92, 3, 80, 239, NULL, NULL, 0, '2025-10-27 22:43:18', 1),
(467, 92, 3, 61, 181, NULL, NULL, 0, '2025-10-27 22:43:18', 1),
(468, 92, 3, 73, 214, NULL, NULL, 1, '2025-10-27 22:43:18', 1),
(469, 92, 3, 47, 134, NULL, NULL, 1, '2025-10-27 22:43:18', 1),
(470, 92, 3, 49, 140, NULL, NULL, 0, '2025-10-27 22:43:18', 1),
(471, 92, 3, 46, 131, NULL, NULL, 0, '2025-10-27 22:43:18', 1),
(472, 92, 3, 58, 170, NULL, NULL, 0, '2025-10-27 22:43:18', 1),
(473, 92, 3, 57, 166, NULL, NULL, 1, '2025-10-27 22:43:18', 1),
(474, 92, 3, 45, 126, NULL, NULL, 1, '2025-10-27 22:43:18', 1),
(475, 92, 3, 48, 137, NULL, NULL, 0, '2025-10-27 22:43:18', 1),
(476, 92, 3, 62, 184, NULL, NULL, 1, '2025-10-27 22:43:18', 1),
(477, 92, 3, 79, 236, NULL, NULL, 0, '2025-10-27 22:43:18', 1),
(478, 92, 3, 63, 186, NULL, NULL, 0, '2025-10-27 22:43:18', 1),
(479, 92, 3, 56, 163, NULL, NULL, 0, '2025-10-27 22:43:18', 1),
(480, 92, 3, 65, 191, NULL, NULL, 0, '2025-10-27 22:43:18', 1),
(481, 87, 3, 56, 163, NULL, NULL, 0, '2025-10-27 22:44:10', 1),
(482, 87, 3, 82, 246, NULL, NULL, 1, '2025-10-27 22:44:10', 1),
(483, 87, 3, 77, 230, NULL, NULL, 0, '2025-10-27 22:44:10', 1),
(484, 87, 3, 47, 135, NULL, NULL, 0, '2025-10-27 22:44:10', 1),
(485, 87, 3, 53, 151, NULL, NULL, 0, '2025-10-27 22:44:10', 1),
(486, 87, 3, 54, 157, NULL, NULL, 1, '2025-10-27 22:44:10', 1),
(487, 87, 3, 44, 123, NULL, NULL, 0, '2025-10-27 22:44:10', 1),
(488, 87, 3, 46, 130, NULL, NULL, 0, '2025-10-27 22:44:10', 1),
(489, 87, 3, 64, 189, NULL, NULL, 1, '2025-10-27 22:44:10', 1),
(490, 87, 3, 49, 140, NULL, NULL, 0, '2025-10-27 22:44:10', 1),
(491, 87, 3, 71, 208, NULL, NULL, 0, '2025-10-27 22:44:10', 1),
(492, 87, 3, 79, 236, NULL, NULL, 0, '2025-10-27 22:44:10', 1),
(493, 87, 3, 55, 159, NULL, NULL, 1, '2025-10-27 22:44:10', 1),
(494, 87, 3, 78, 233, NULL, NULL, 0, '2025-10-27 22:44:10', 1),
(495, 87, 3, 57, 167, NULL, NULL, 0, '2025-10-27 22:44:10', 1),
(496, 87, 3, 43, 120, NULL, NULL, 0, '2025-10-27 22:44:10', 1),
(497, 87, 3, 58, 170, NULL, NULL, 0, '2025-10-27 22:44:10', 1),
(498, 87, 3, 74, 217, NULL, NULL, 0, '2025-10-27 22:44:10', 1),
(499, 87, 3, 68, 200, NULL, NULL, 0, '2025-10-27 22:44:10', 1),
(500, 87, 3, 61, 183, NULL, NULL, 1, '2025-10-27 22:44:10', 1),
(501, 91, 3, 49, 141, NULL, NULL, 0, '2025-10-27 22:44:16', 1),
(502, 91, 3, 71, 210, NULL, NULL, 0, '2025-10-27 22:44:16', 1),
(503, 91, 3, 82, 246, NULL, NULL, 1, '2025-10-27 22:44:16', 1),
(504, 91, 3, 50, 145, NULL, NULL, 1, '2025-10-27 22:44:16', 1),
(505, 91, 3, 64, 190, NULL, NULL, 0, '2025-10-27 22:44:16', 1),
(506, 91, 3, 70, 207, NULL, NULL, 0, '2025-10-27 22:44:16', 1),
(507, 91, 3, 60, 178, NULL, NULL, 1, '2025-10-27 22:44:16', 1),
(508, 91, 3, 52, 149, NULL, NULL, 0, '2025-10-27 22:44:16', 1),
(509, 91, 3, 59, 174, NULL, NULL, 0, '2025-10-27 22:44:16', 1),
(510, 91, 3, 45, 126, NULL, NULL, 1, '2025-10-27 22:44:16', 1),
(511, 91, 3, 46, 130, NULL, NULL, 0, '2025-10-27 22:44:16', 1),
(512, 91, 3, 56, 162, NULL, NULL, 0, '2025-10-27 22:44:16', 1),
(513, 91, 3, 61, 183, NULL, NULL, 1, '2025-10-27 22:44:16', 1),
(514, 91, 3, 74, 217, NULL, NULL, 0, '2025-10-27 22:44:16', 1),
(515, 91, 3, 57, 166, NULL, NULL, 1, '2025-10-27 22:44:16', 1),
(516, 91, 3, 66, 195, NULL, NULL, 1, '2025-10-27 22:44:16', 1),
(517, 91, 3, 65, 191, NULL, NULL, 0, '2025-10-27 22:44:16', 1),
(518, 91, 3, 42, 115, NULL, NULL, 0, '2025-10-27 22:44:16', 1),
(519, 91, 3, 75, 221, NULL, NULL, 0, '2025-10-27 22:44:16', 1),
(520, 91, 3, 63, 187, NULL, NULL, 1, '2025-10-27 22:44:16', 1),
(521, 82, 3, 55, 160, NULL, NULL, 0, '2025-10-27 22:44:43', 1),
(522, 82, 3, 75, 223, NULL, NULL, 1, '2025-10-27 22:44:43', 1),
(523, 82, 3, 48, 137, NULL, NULL, 0, '2025-10-27 22:44:43', 1),
(524, 82, 3, 43, 121, NULL, NULL, 1, '2025-10-27 22:44:43', 1),
(525, 82, 3, 50, 145, NULL, NULL, 1, '2025-10-27 22:44:43', 1),
(526, 82, 3, 60, 177, NULL, NULL, 0, '2025-10-27 22:44:43', 1),
(527, 82, 3, 74, 219, NULL, NULL, 0, '2025-10-27 22:44:43', 1),
(528, 82, 3, 62, 184, NULL, NULL, 1, '2025-10-27 22:44:43', 1),
(529, 82, 3, 53, 151, NULL, NULL, 0, '2025-10-27 22:44:43', 1),
(530, 82, 3, 78, 232, NULL, NULL, 0, '2025-10-27 22:44:43', 1),
(531, 82, 3, 77, 229, NULL, NULL, 1, '2025-10-27 22:44:43', 1),
(532, 82, 3, 68, 199, NULL, NULL, 0, '2025-10-27 22:44:43', 1),
(533, 82, 3, 42, 115, NULL, NULL, 0, '2025-10-27 22:44:43', 1),
(534, 82, 3, 64, 188, NULL, NULL, 0, '2025-10-27 22:44:43', 1),
(535, 82, 3, 58, 171, NULL, NULL, 1, '2025-10-27 22:44:43', 1),
(536, 82, 3, 65, 192, NULL, NULL, 0, '2025-10-27 22:44:43', 1),
(537, 82, 3, 57, 166, NULL, NULL, 1, '2025-10-27 22:44:43', 1),
(538, 82, 3, 70, 206, NULL, NULL, 1, '2025-10-27 22:44:43', 1),
(539, 82, 3, 44, 124, NULL, NULL, 0, '2025-10-27 22:44:43', 1),
(540, 82, 3, 71, 208, NULL, NULL, 0, '2025-10-27 22:44:43', 1),
(541, 77, 3, 75, 221, NULL, NULL, 0, '2025-10-27 22:45:07', 1),
(542, 77, 3, 49, 141, NULL, NULL, 0, '2025-10-27 22:45:07', 1),
(543, 77, 3, 67, 197, NULL, NULL, 0, '2025-10-27 22:45:07', 1),
(544, 77, 3, 43, 120, NULL, NULL, 0, '2025-10-27 22:45:07', 1),
(545, 77, 3, 62, 185, NULL, NULL, 0, '2025-10-27 22:45:07', 1),
(546, 77, 3, 81, 244, NULL, NULL, 0, '2025-10-27 22:45:07', 1),
(547, 77, 3, 57, 166, NULL, NULL, 1, '2025-10-27 22:45:07', 1),
(548, 77, 3, 54, 157, NULL, NULL, 1, '2025-10-27 22:45:07', 1),
(549, 77, 3, 76, 225, NULL, NULL, 1, '2025-10-27 22:45:07', 1),
(550, 77, 3, 42, 115, NULL, NULL, 0, '2025-10-27 22:45:07', 1),
(551, 77, 3, 61, 183, NULL, NULL, 1, '2025-10-27 22:45:07', 1),
(552, 77, 3, 69, 202, NULL, NULL, 1, '2025-10-27 22:45:07', 1),
(553, 77, 3, 77, 231, NULL, NULL, 0, '2025-10-27 22:45:07', 1),
(554, 77, 3, 45, 128, NULL, NULL, 0, '2025-10-27 22:45:07', 1),
(555, 77, 3, 79, 237, NULL, NULL, 1, '2025-10-27 22:45:07', 1),
(556, 77, 3, 56, 165, NULL, NULL, 1, '2025-10-27 22:45:07', 1),
(557, 77, 3, 80, 240, NULL, NULL, 1, '2025-10-27 22:45:07', 1),
(558, 77, 3, 70, 205, NULL, NULL, 0, '2025-10-27 22:45:07', 1),
(559, 77, 3, 47, 134, NULL, NULL, 1, '2025-10-27 22:45:07', 1),
(560, 77, 3, 44, 123, NULL, NULL, 0, '2025-10-27 22:45:07', 1),
(561, 75, 3, 65, 193, NULL, NULL, 1, '2025-10-27 22:45:08', 1),
(562, 75, 3, 53, 154, NULL, NULL, 0, '2025-10-27 22:45:08', 1),
(563, 75, 3, 46, 133, NULL, NULL, 0, '2025-10-27 22:45:08', 1),
(564, 75, 3, 47, 135, NULL, NULL, 0, '2025-10-27 22:45:08', 1),
(565, 75, 3, 55, 160, NULL, NULL, 0, '2025-10-27 22:45:08', 1),
(566, 75, 3, 43, 120, NULL, NULL, 0, '2025-10-27 22:45:08', 1),
(567, 75, 3, 62, 184, NULL, NULL, 1, '2025-10-27 22:45:08', 1),
(568, 75, 3, 64, 189, NULL, NULL, 1, '2025-10-27 22:45:08', 1),
(569, 75, 3, 77, 231, NULL, NULL, 0, '2025-10-27 22:45:08', 1),
(570, 75, 3, 76, 226, NULL, NULL, 0, '2025-10-27 22:45:08', 1),
(571, 75, 3, 48, 139, NULL, NULL, 1, '2025-10-27 22:45:08', 1),
(572, 75, 3, 50, 147, NULL, NULL, 0, '2025-10-27 22:45:08', 1),
(573, 75, 3, 44, 123, NULL, NULL, 0, '2025-10-27 22:45:08', 1),
(574, 75, 3, 80, 240, NULL, NULL, 1, '2025-10-27 22:45:08', 1),
(575, 75, 3, 67, 197, NULL, NULL, 0, '2025-10-27 22:45:08', 1),
(576, 75, 3, 78, 234, NULL, NULL, 1, '2025-10-27 22:45:08', 1),
(577, 75, 3, 54, 157, NULL, NULL, 1, '2025-10-27 22:45:08', 1),
(578, 75, 3, 59, 174, NULL, NULL, 0, '2025-10-27 22:45:08', 1),
(579, 75, 3, 52, 149, NULL, NULL, 0, '2025-10-27 22:45:08', 1),
(580, 75, 3, 70, 206, NULL, NULL, 1, '2025-10-27 22:45:08', 1),
(581, 86, 3, 65, 192, NULL, NULL, 0, '2025-10-27 22:45:12', 1),
(582, 86, 3, 69, 204, NULL, NULL, 0, '2025-10-27 22:45:12', 1),
(583, 86, 3, 43, 121, NULL, NULL, 1, '2025-10-27 22:45:12', 1),
(584, 86, 3, 66, 194, NULL, NULL, 0, '2025-10-27 22:45:12', 1),
(585, 86, 3, 42, 115, NULL, NULL, 0, '2025-10-27 22:45:12', 1),
(586, 86, 3, 54, 155, NULL, NULL, 0, '2025-10-27 22:45:12', 1),
(587, 86, 3, 82, 246, NULL, NULL, 1, '2025-10-27 22:45:12', 1),
(588, 86, 3, 62, 184, NULL, NULL, 1, '2025-10-27 22:45:12', 1),
(589, 86, 3, 44, 124, NULL, NULL, 0, '2025-10-27 22:45:12', 1),
(590, 86, 3, 73, 215, NULL, NULL, 0, '2025-10-27 22:45:12', 1),
(591, 86, 3, 52, 149, NULL, NULL, 0, '2025-10-27 22:45:12', 1),
(592, 86, 3, 63, 187, NULL, NULL, 1, '2025-10-27 22:45:12', 1),
(593, 86, 3, 49, 143, NULL, NULL, 0, '2025-10-27 22:45:12', 1),
(594, 86, 3, 78, 235, NULL, NULL, 0, '2025-10-27 22:45:12', 1),
(595, 86, 3, 81, 244, NULL, NULL, 0, '2025-10-27 22:45:12', 1),
(596, 86, 3, 53, 151, NULL, NULL, 0, '2025-10-27 22:45:12', 1),
(597, 86, 3, 75, 221, NULL, NULL, 0, '2025-10-27 22:45:12', 1),
(598, 86, 3, 79, 236, NULL, NULL, 0, '2025-10-27 22:45:12', 1),
(599, 86, 3, 48, 137, NULL, NULL, 0, '2025-10-27 22:45:12', 1),
(600, 86, 3, 72, 211, NULL, NULL, 0, '2025-10-27 22:45:12', 1),
(601, 93, 3, 44, 124, NULL, NULL, 0, '2025-10-27 22:45:42', 1),
(602, 93, 3, 61, 183, NULL, NULL, 1, '2025-10-27 22:45:42', 1),
(603, 93, 3, 43, 119, NULL, NULL, 0, '2025-10-27 22:45:42', 1),
(604, 93, 3, 67, 198, NULL, NULL, 1, '2025-10-27 22:45:42', 1),
(605, 93, 3, 60, 177, NULL, NULL, 0, '2025-10-27 22:45:42', 1),
(606, 93, 3, 76, 225, NULL, NULL, 1, '2025-10-27 22:45:42', 1),
(607, 93, 3, 75, 221, NULL, NULL, 0, '2025-10-27 22:45:42', 1),
(608, 93, 3, 80, 239, NULL, NULL, 0, '2025-10-27 22:45:42', 1),
(609, 93, 3, 49, 141, NULL, NULL, 0, '2025-10-27 22:45:42', 1),
(610, 93, 3, 54, 155, NULL, NULL, 0, '2025-10-27 22:45:42', 1),
(611, 93, 3, 74, 217, NULL, NULL, 0, '2025-10-27 22:45:42', 1),
(612, 93, 3, 78, 235, NULL, NULL, 0, '2025-10-27 22:45:42', 1),
(613, 93, 3, 59, 176, NULL, NULL, 0, '2025-10-27 22:45:42', 1),
(614, 93, 3, 77, 231, NULL, NULL, 0, '2025-10-27 22:45:42', 1),
(615, 93, 3, 63, 187, NULL, NULL, 1, '2025-10-27 22:45:42', 1),
(616, 93, 3, 79, 238, NULL, NULL, 0, '2025-10-27 22:45:42', 1),
(617, 93, 3, 50, 145, NULL, NULL, 1, '2025-10-27 22:45:42', 1),
(618, 93, 3, 52, 149, NULL, NULL, 0, '2025-10-27 22:45:42', 1),
(619, 93, 3, 68, 200, NULL, NULL, 0, '2025-10-27 22:45:42', 1),
(620, 93, 3, 62, 185, NULL, NULL, 0, '2025-10-27 22:45:42', 1),
(621, 72, 3, 78, 233, NULL, NULL, 0, '2025-10-27 22:45:50', 1),
(622, 72, 3, 56, 165, NULL, NULL, 1, '2025-10-27 22:45:50', 1),
(623, 72, 3, 58, 170, NULL, NULL, 0, '2025-10-27 22:45:50', 1),
(624, 72, 3, 49, 140, NULL, NULL, 0, '2025-10-27 22:45:50', 1),
(625, 72, 3, 63, 186, NULL, NULL, 0, '2025-10-27 22:45:50', 1),
(626, 72, 3, 82, 246, NULL, NULL, 1, '2025-10-27 22:45:50', 1),
(627, 72, 3, 53, 151, NULL, NULL, 0, '2025-10-27 22:45:50', 1),
(628, 72, 3, 55, 160, NULL, NULL, 0, '2025-10-27 22:45:50', 1),
(629, 72, 3, 52, 148, NULL, NULL, 1, '2025-10-27 22:45:50', 1),
(630, 72, 3, 74, 217, NULL, NULL, 0, '2025-10-27 22:45:50', 1),
(631, 72, 3, 75, 221, NULL, NULL, 0, '2025-10-27 22:45:50', 1),
(632, 72, 3, 79, 237, NULL, NULL, 1, '2025-10-27 22:45:50', 1),
(633, 72, 3, 65, 193, NULL, NULL, 1, '2025-10-27 22:45:50', 1),
(634, 72, 3, 50, 145, NULL, NULL, 1, '2025-10-27 22:45:50', 1),
(635, 72, 3, 54, 155, NULL, NULL, 0, '2025-10-27 22:45:50', 1),
(636, 72, 3, 59, 174, NULL, NULL, 0, '2025-10-27 22:45:50', 1),
(637, 72, 3, 80, 239, NULL, NULL, 0, '2025-10-27 22:45:50', 1),
(638, 72, 3, 61, 180, NULL, NULL, 0, '2025-10-27 22:45:50', 1),
(639, 72, 3, 81, 242, NULL, NULL, 1, '2025-10-27 22:45:50', 1),
(640, 72, 3, 64, 189, NULL, NULL, 1, '2025-10-27 22:45:50', 1),
(641, 85, 3, 81, 244, NULL, NULL, 0, '2025-10-27 22:46:07', 1),
(642, 85, 3, 66, 195, NULL, NULL, 1, '2025-10-27 22:46:07', 1),
(643, 85, 3, 76, 227, NULL, NULL, 0, '2025-10-27 22:46:07', 1),
(644, 85, 3, 48, 139, NULL, NULL, 1, '2025-10-27 22:46:07', 1),
(645, 85, 3, 45, 126, NULL, NULL, 1, '2025-10-27 22:46:07', 1),
(646, 85, 3, 72, 211, NULL, NULL, 0, '2025-10-27 22:46:07', 1),
(647, 85, 3, 74, 217, NULL, NULL, 0, '2025-10-27 22:46:07', 1),
(648, 85, 3, 59, 176, NULL, NULL, 0, '2025-10-27 22:46:07', 1),
(649, 85, 3, 43, 121, NULL, NULL, 1, '2025-10-27 22:46:07', 1),
(650, 85, 3, 42, 116, NULL, NULL, 1, '2025-10-27 22:46:07', 1),
(651, 85, 3, 57, 166, NULL, NULL, 1, '2025-10-27 22:46:07', 1),
(652, 85, 3, 65, 192, NULL, NULL, 0, '2025-10-27 22:46:07', 1),
(653, 85, 3, 67, 196, NULL, NULL, 0, '2025-10-27 22:46:07', 1),
(654, 85, 3, 44, 124, NULL, NULL, 0, '2025-10-27 22:46:07', 1),
(655, 85, 3, 54, 156, NULL, NULL, 0, '2025-10-27 22:46:07', 1),
(656, 85, 3, 70, 206, NULL, NULL, 1, '2025-10-27 22:46:07', 1),
(657, 85, 3, 58, 172, NULL, NULL, 0, '2025-10-27 22:46:07', 1),
(658, 85, 3, 82, 246, NULL, NULL, 1, '2025-10-27 22:46:07', 1),
(659, 85, 3, 52, 150, NULL, NULL, 0, '2025-10-27 22:46:07', 1),
(660, 85, 3, 62, 184, NULL, NULL, 1, '2025-10-27 22:46:07', 1),
(661, 76, 3, 64, 190, NULL, NULL, 0, '2025-10-27 22:47:12', 1),
(662, 76, 3, 80, 241, NULL, NULL, 0, '2025-10-27 22:47:12', 1),
(663, 76, 3, 72, 211, NULL, NULL, 0, '2025-10-27 22:47:12', 1),
(664, 76, 3, 57, 166, NULL, NULL, 1, '2025-10-27 22:47:12', 1),
(665, 76, 3, 43, 121, NULL, NULL, 1, '2025-10-27 22:47:12', 1),
(666, 76, 3, 54, 155, NULL, NULL, 0, '2025-10-27 22:47:12', 1),
(667, 76, 3, 60, 178, NULL, NULL, 1, '2025-10-27 22:47:12', 1),
(668, 76, 3, 55, 159, NULL, NULL, 1, '2025-10-27 22:47:12', 1),
(669, 76, 3, 75, 221, NULL, NULL, 0, '2025-10-27 22:47:12', 1),
(670, 76, 3, 79, 237, NULL, NULL, 1, '2025-10-27 22:47:12', 1),
(671, 76, 3, 77, 229, NULL, NULL, 1, '2025-10-27 22:47:12', 1),
(672, 76, 3, 70, 205, NULL, NULL, 0, '2025-10-27 22:47:12', 1),
(673, 76, 3, 56, 162, NULL, NULL, 0, '2025-10-27 22:47:12', 1),
(674, 76, 3, 76, 225, NULL, NULL, 1, '2025-10-27 22:47:12', 1),
(675, 76, 3, 52, 150, NULL, NULL, 0, '2025-10-27 22:47:12', 1),
(676, 76, 3, 59, 176, NULL, NULL, 0, '2025-10-27 22:47:12', 1),
(677, 76, 3, 82, 246, NULL, NULL, 1, '2025-10-27 22:47:12', 1),
(678, 76, 3, 48, 137, NULL, NULL, 0, '2025-10-27 22:47:12', 1),
(679, 76, 3, 44, 123, NULL, NULL, 0, '2025-10-27 22:47:12', 1),
(680, 76, 3, 58, 170, NULL, NULL, 0, '2025-10-27 22:47:12', 1),
(681, 88, 3, 53, 154, NULL, NULL, 0, '2025-10-27 22:47:12', 1),
(682, 88, 3, 66, 194, NULL, NULL, 0, '2025-10-27 22:47:12', 1),
(683, 88, 3, 48, 137, NULL, NULL, 0, '2025-10-27 22:47:12', 1),
(684, 88, 3, 78, 233, NULL, NULL, 0, '2025-10-27 22:47:12', 1),
(685, 88, 3, 63, 186, NULL, NULL, 0, '2025-10-27 22:47:12', 1),
(686, 88, 3, 65, 191, NULL, NULL, 0, '2025-10-27 22:47:12', 1),
(687, 88, 3, 57, 166, NULL, NULL, 1, '2025-10-27 22:47:12', 1),
(688, 88, 3, 59, 174, NULL, NULL, 0, '2025-10-27 22:47:12', 1),
(689, 88, 3, 71, 209, NULL, NULL, 1, '2025-10-27 22:47:12', 1),
(690, 88, 3, 68, 201, NULL, NULL, 1, '2025-10-27 22:47:12', 1),
(691, 88, 3, 80, 239, NULL, NULL, 0, '2025-10-27 22:47:12', 1),
(692, 88, 3, 64, 189, NULL, NULL, 1, '2025-10-27 22:47:12', 1),
(693, 88, 3, 52, 150, NULL, NULL, 0, '2025-10-27 22:47:12', 1),
(694, 88, 3, 79, 236, NULL, NULL, 0, '2025-10-27 22:47:12', 1),
(695, 88, 3, 72, 211, NULL, NULL, 0, '2025-10-27 22:47:12', 1),
(696, 88, 3, 75, 223, NULL, NULL, 1, '2025-10-27 22:47:12', 1),
(697, 88, 3, 54, 157, NULL, NULL, 1, '2025-10-27 22:47:12', 1),
(698, 88, 3, 74, 218, NULL, NULL, 0, '2025-10-27 22:47:12', 1),
(699, 88, 3, 50, 146, NULL, NULL, 0, '2025-10-27 22:47:12', 1),
(700, 88, 3, 76, 225, NULL, NULL, 1, '2025-10-27 22:47:12', 1),
(701, 89, 3, 57, 166, NULL, NULL, 1, '2025-10-27 22:48:28', 1),
(702, 89, 3, 78, 232, NULL, NULL, 0, '2025-10-27 22:48:28', 1),
(703, 89, 3, 68, 199, NULL, NULL, 0, '2025-10-27 22:48:28', 1),
(704, 89, 3, 74, 217, NULL, NULL, 0, '2025-10-27 22:48:28', 1),
(705, 89, 3, 43, 119, NULL, NULL, 0, '2025-10-27 22:48:28', 1),
(706, 89, 3, 77, 231, NULL, NULL, 0, '2025-10-27 22:48:28', 1),
(707, 89, 3, 63, 186, NULL, NULL, 0, '2025-10-27 22:48:28', 1),
(708, 89, 3, 67, 196, NULL, NULL, 0, '2025-10-27 22:48:28', 1),
(709, 89, 3, 53, 153, NULL, NULL, 0, '2025-10-27 22:48:28', 1),
(710, 89, 3, 71, 209, NULL, NULL, 1, '2025-10-27 22:48:28', 1),
(711, 89, 3, 72, 211, NULL, NULL, 0, '2025-10-27 22:48:28', 1),
(712, 89, 3, 70, 206, NULL, NULL, 1, '2025-10-27 22:48:28', 1),
(713, 89, 3, 62, 184, NULL, NULL, 1, '2025-10-27 22:48:28', 1),
(714, 89, 3, 52, 149, NULL, NULL, 0, '2025-10-27 22:48:28', 1),
(715, 89, 3, 65, 193, NULL, NULL, 1, '2025-10-27 22:48:28', 1),
(716, 89, 3, 45, 126, NULL, NULL, 1, '2025-10-27 22:48:28', 1),
(717, 89, 3, 69, 203, NULL, NULL, 0, '2025-10-27 22:48:28', 1),
(718, 89, 3, 60, 178, NULL, NULL, 1, '2025-10-27 22:48:28', 1),
(719, 89, 3, 49, 140, NULL, NULL, 0, '2025-10-27 22:48:28', 1),
(720, 89, 3, 44, 124, NULL, NULL, 0, '2025-10-27 22:48:28', 1),
(721, 80, 3, 67, 198, NULL, NULL, 1, '2025-10-27 22:48:33', 1),
(722, 80, 3, 63, 187, NULL, NULL, 1, '2025-10-27 22:48:33', 1),
(723, 80, 3, 74, 219, NULL, NULL, 0, '2025-10-27 22:48:33', 1),
(724, 80, 3, 78, 235, NULL, NULL, 0, '2025-10-27 22:48:33', 1),
(725, 80, 3, 45, 129, NULL, NULL, 0, '2025-10-27 22:48:33', 1),
(726, 80, 3, 54, 157, NULL, NULL, 1, '2025-10-27 22:48:33', 1),
(727, 80, 3, 61, 183, NULL, NULL, 1, '2025-10-27 22:48:33', 1),
(728, 80, 3, 77, 229, NULL, NULL, 1, '2025-10-27 22:48:33', 1),
(729, 80, 3, 57, 166, NULL, NULL, 1, '2025-10-27 22:48:33', 1),
(730, 80, 3, 43, 122, NULL, NULL, 0, '2025-10-27 22:48:33', 1),
(731, 80, 3, 62, 184, NULL, NULL, 1, '2025-10-27 22:48:33', 1),
(732, 80, 3, 81, 244, NULL, NULL, 0, '2025-10-27 22:48:33', 1),
(733, 80, 3, 76, 225, NULL, NULL, 1, '2025-10-27 22:48:33', 1),
(734, 80, 3, 72, 211, NULL, NULL, 0, '2025-10-27 22:48:33', 1),
(735, 80, 3, 70, 207, NULL, NULL, 0, '2025-10-27 22:48:33', 1),
(736, 80, 3, 73, 216, NULL, NULL, 0, '2025-10-27 22:48:33', 1),
(737, 80, 3, 58, 170, NULL, NULL, 0, '2025-10-27 22:48:33', 1),
(738, 80, 3, 69, 204, NULL, NULL, 0, '2025-10-27 22:48:33', 1),
(739, 80, 3, 71, 209, NULL, NULL, 1, '2025-10-27 22:48:33', 1),
(740, 80, 3, 48, 137, NULL, NULL, 0, '2025-10-27 22:48:33', 1),
(741, 90, 3, 82, 248, NULL, NULL, 0, '2025-10-27 22:49:08', 1),
(742, 90, 3, 74, 217, NULL, NULL, 0, '2025-10-27 22:49:08', 1),
(743, 90, 3, 55, 159, NULL, NULL, 1, '2025-10-27 22:49:08', 1),
(744, 90, 3, 46, 132, NULL, NULL, 1, '2025-10-27 22:49:08', 1),
(745, 90, 3, 70, 206, NULL, NULL, 1, '2025-10-27 22:49:08', 1),
(746, 90, 3, 54, 157, NULL, NULL, 1, '2025-10-27 22:49:08', 1),
(747, 90, 3, 68, 199, NULL, NULL, 0, '2025-10-27 22:49:08', 1),
(748, 90, 3, 56, 165, NULL, NULL, 1, '2025-10-27 22:49:08', 1),
(749, 90, 3, 73, 214, NULL, NULL, 1, '2025-10-27 22:49:08', 1),
(750, 90, 3, 44, 124, NULL, NULL, 0, '2025-10-27 22:49:08', 1),
(751, 90, 3, 80, 239, NULL, NULL, 0, '2025-10-27 22:49:08', 1),
(752, 90, 3, 59, 174, NULL, NULL, 0, '2025-10-27 22:49:08', 1),
(753, 90, 3, 43, 120, NULL, NULL, 0, '2025-10-27 22:49:08', 1),
(754, 90, 3, 52, 149, NULL, NULL, 0, '2025-10-27 22:49:08', 1),
(755, 90, 3, 69, 202, NULL, NULL, 1, '2025-10-27 22:49:08', 1),
(756, 90, 3, 72, 212, NULL, NULL, 1, '2025-10-27 22:49:08', 1),
(757, 90, 3, 45, 128, NULL, NULL, 0, '2025-10-27 22:49:08', 1),
(758, 90, 3, 47, 134, NULL, NULL, 1, '2025-10-27 22:49:08', 1),
(759, 90, 3, 61, 183, NULL, NULL, 1, '2025-10-27 22:49:08', 1),
(760, 90, 3, 42, 115, NULL, NULL, 0, '2025-10-27 22:49:08', 1),
(761, 81, 3, 56, 163, NULL, NULL, 0, '2025-10-27 22:50:03', 1),
(762, 81, 3, 78, 235, NULL, NULL, 0, '2025-10-27 22:50:03', 1),
(763, 81, 3, 54, 156, NULL, NULL, 0, '2025-10-27 22:50:03', 1),
(764, 81, 3, 69, 204, NULL, NULL, 0, '2025-10-27 22:50:03', 1),
(765, 81, 3, 62, 185, NULL, NULL, 0, '2025-10-27 22:50:03', 1),
(766, 81, 3, 61, 183, NULL, NULL, 1, '2025-10-27 22:50:03', 1),
(767, 81, 3, 49, 141, NULL, NULL, 0, '2025-10-27 22:50:03', 1),
(768, 81, 3, 63, 186, NULL, NULL, 0, '2025-10-27 22:50:03', 1),
(769, 81, 3, 72, 211, NULL, NULL, 0, '2025-10-27 22:50:03', 1),
(770, 81, 3, 48, 138, NULL, NULL, 0, '2025-10-27 22:50:03', 1),
(771, 81, 3, 53, 154, NULL, NULL, 0, '2025-10-27 22:50:03', 1),
(772, 81, 3, 82, 246, NULL, NULL, 1, '2025-10-27 22:50:03', 1),
(773, 81, 3, 45, 126, NULL, NULL, 1, '2025-10-27 22:50:03', 1),
(774, 81, 3, 75, 222, NULL, NULL, 0, '2025-10-27 22:50:03', 1),
(775, 81, 3, 55, 158, NULL, NULL, 0, '2025-10-27 22:50:03', 1),
(776, 81, 3, 52, 149, NULL, NULL, 0, '2025-10-27 22:50:03', 1),
(777, 81, 3, 74, 218, NULL, NULL, 0, '2025-10-27 22:50:03', 1),
(778, 81, 3, 67, 196, NULL, NULL, 0, '2025-10-27 22:50:03', 1),
(779, 81, 3, 59, 174, NULL, NULL, 0, '2025-10-27 22:50:03', 1),
(780, 81, 3, 71, 210, NULL, NULL, 0, '2025-10-27 22:50:03', 1),
(781, 73, 3, 53, 152, NULL, NULL, 1, '2025-10-27 22:50:14', 1),
(782, 73, 3, 70, 206, NULL, NULL, 1, '2025-10-27 22:50:14', 1),
(783, 73, 3, 57, 167, NULL, NULL, 0, '2025-10-27 22:50:14', 1),
(784, 73, 3, 80, 241, NULL, NULL, 0, '2025-10-27 22:50:14', 1),
(785, 73, 3, 42, 115, NULL, NULL, 0, '2025-10-27 22:50:14', 1),
(786, 73, 3, 60, 178, NULL, NULL, 1, '2025-10-27 22:50:14', 1),
(787, 73, 3, 65, 191, NULL, NULL, 0, '2025-10-27 22:50:14', 1),
(788, 73, 3, 81, 242, NULL, NULL, 1, '2025-10-27 22:50:14', 1),
(789, 73, 3, 78, 235, NULL, NULL, 0, '2025-10-27 22:50:14', 1),
(790, 73, 3, 48, 139, NULL, NULL, 1, '2025-10-27 22:50:14', 1),
(791, 73, 3, 45, 129, NULL, NULL, 0, '2025-10-27 22:50:14', 1),
(792, 73, 3, 74, 217, NULL, NULL, 0, '2025-10-27 22:50:14', 1),
(793, 73, 3, 75, 223, NULL, NULL, 1, '2025-10-27 22:50:14', 1),
(794, 73, 3, 43, 120, NULL, NULL, 0, '2025-10-27 22:50:14', 1),
(795, 73, 3, 50, 145, NULL, NULL, 1, '2025-10-27 22:50:14', 1),
(796, 73, 3, 61, 183, NULL, NULL, 1, '2025-10-27 22:50:14', 1),
(797, 73, 3, 66, 194, NULL, NULL, 0, '2025-10-27 22:50:14', 1),
(798, 73, 3, 52, 149, NULL, NULL, 0, '2025-10-27 22:50:14', 1),
(799, 73, 3, 77, 231, NULL, NULL, 0, '2025-10-27 22:50:14', 1);
INSERT INTO `respuestas_alumnos` (`id`, `alumno_user_id`, `encuesta_id`, `pregunta_id`, `respuesta_id`, `respuesta_texto`, `respuesta_numero`, `es_correcta`, `respondido_at`, `activo`) VALUES
(800, 73, 3, 44, 123, NULL, NULL, 0, '2025-10-27 22:50:14', 1),
(801, 83, 3, 48, 138, NULL, NULL, 0, '2025-10-27 22:50:54', 1),
(802, 83, 3, 59, 175, NULL, NULL, 1, '2025-10-27 22:50:54', 1),
(803, 83, 3, 80, 239, NULL, NULL, 0, '2025-10-27 22:50:54', 1),
(804, 83, 3, 77, 231, NULL, NULL, 0, '2025-10-27 22:50:54', 1),
(805, 83, 3, 73, 216, NULL, NULL, 0, '2025-10-27 22:50:54', 1),
(806, 83, 3, 75, 222, NULL, NULL, 0, '2025-10-27 22:50:54', 1),
(807, 83, 3, 55, 158, NULL, NULL, 0, '2025-10-27 22:50:54', 1),
(808, 83, 3, 70, 205, NULL, NULL, 0, '2025-10-27 22:50:54', 1),
(809, 83, 3, 54, 155, NULL, NULL, 0, '2025-10-27 22:50:54', 1),
(810, 83, 3, 66, 194, NULL, NULL, 0, '2025-10-27 22:50:54', 1),
(811, 83, 3, 63, 187, NULL, NULL, 1, '2025-10-27 22:50:54', 1),
(812, 83, 3, 44, 124, NULL, NULL, 0, '2025-10-27 22:50:54', 1),
(813, 83, 3, 81, 242, NULL, NULL, 1, '2025-10-27 22:50:54', 1),
(814, 83, 3, 43, 119, NULL, NULL, 0, '2025-10-27 22:50:54', 1),
(815, 83, 3, 72, 211, NULL, NULL, 0, '2025-10-27 22:50:54', 1),
(816, 83, 3, 56, 163, NULL, NULL, 0, '2025-10-27 22:50:54', 1),
(817, 83, 3, 47, 134, NULL, NULL, 1, '2025-10-27 22:50:54', 1),
(818, 83, 3, 68, 199, NULL, NULL, 0, '2025-10-27 22:50:54', 1),
(819, 83, 3, 74, 219, NULL, NULL, 0, '2025-10-27 22:50:54', 1),
(820, 83, 3, 49, 141, NULL, NULL, 0, '2025-10-27 22:50:54', 1),
(821, 78, 3, 69, 204, NULL, NULL, 0, '2025-10-27 22:51:32', 1),
(822, 78, 3, 42, 118, NULL, NULL, 0, '2025-10-27 22:51:32', 1),
(823, 78, 3, 71, 210, NULL, NULL, 0, '2025-10-27 22:51:32', 1),
(824, 78, 3, 67, 197, NULL, NULL, 0, '2025-10-27 22:51:32', 1),
(825, 78, 3, 60, 178, NULL, NULL, 1, '2025-10-27 22:51:32', 1),
(826, 78, 3, 65, 193, NULL, NULL, 1, '2025-10-27 22:51:32', 1),
(827, 78, 3, 63, 187, NULL, NULL, 1, '2025-10-27 22:51:32', 1),
(828, 78, 3, 48, 138, NULL, NULL, 0, '2025-10-27 22:51:32', 1),
(829, 78, 3, 46, 132, NULL, NULL, 1, '2025-10-27 22:51:32', 1),
(830, 78, 3, 43, 119, NULL, NULL, 0, '2025-10-27 22:51:32', 1),
(831, 78, 3, 70, 206, NULL, NULL, 1, '2025-10-27 22:51:32', 1),
(832, 78, 3, 80, 241, NULL, NULL, 0, '2025-10-27 22:51:32', 1),
(833, 78, 3, 57, 169, NULL, NULL, 0, '2025-10-27 22:51:32', 1),
(834, 78, 3, 59, 174, NULL, NULL, 0, '2025-10-27 22:51:32', 1),
(835, 78, 3, 79, 238, NULL, NULL, 0, '2025-10-27 22:51:32', 1),
(836, 78, 3, 56, 165, NULL, NULL, 1, '2025-10-27 22:51:32', 1),
(837, 78, 3, 75, 223, NULL, NULL, 1, '2025-10-27 22:51:32', 1),
(838, 78, 3, 62, 185, NULL, NULL, 0, '2025-10-27 22:51:32', 1),
(839, 78, 3, 61, 183, NULL, NULL, 1, '2025-10-27 22:51:32', 1),
(840, 78, 3, 72, 212, NULL, NULL, 1, '2025-10-27 22:51:32', 1),
(841, 79, 3, 72, 211, NULL, NULL, 0, '2025-10-27 22:52:08', 1),
(842, 79, 3, 77, 229, NULL, NULL, 1, '2025-10-27 22:52:08', 1),
(843, 79, 3, 49, 140, NULL, NULL, 0, '2025-10-27 22:52:08', 1),
(844, 79, 3, 50, 145, NULL, NULL, 1, '2025-10-27 22:52:08', 1),
(845, 79, 3, 65, 193, NULL, NULL, 1, '2025-10-27 22:52:08', 1),
(846, 79, 3, 55, 159, NULL, NULL, 1, '2025-10-27 22:52:08', 1),
(847, 79, 3, 61, 183, NULL, NULL, 1, '2025-10-27 22:52:08', 1),
(848, 79, 3, 70, 205, NULL, NULL, 0, '2025-10-27 22:52:08', 1),
(849, 79, 3, 53, 151, NULL, NULL, 0, '2025-10-27 22:52:08', 1),
(850, 79, 3, 48, 137, NULL, NULL, 0, '2025-10-27 22:52:08', 1),
(851, 79, 3, 74, 217, NULL, NULL, 0, '2025-10-27 22:52:08', 1),
(852, 79, 3, 45, 126, NULL, NULL, 1, '2025-10-27 22:52:08', 1),
(853, 79, 3, 76, 225, NULL, NULL, 1, '2025-10-27 22:52:08', 1),
(854, 79, 3, 46, 132, NULL, NULL, 1, '2025-10-27 22:52:08', 1),
(855, 79, 3, 73, 216, NULL, NULL, 0, '2025-10-27 22:52:08', 1),
(856, 79, 3, 71, 210, NULL, NULL, 0, '2025-10-27 22:52:08', 1),
(857, 79, 3, 52, 150, NULL, NULL, 0, '2025-10-27 22:52:08', 1),
(858, 79, 3, 43, 120, NULL, NULL, 0, '2025-10-27 22:52:08', 1),
(859, 79, 3, 68, 199, NULL, NULL, 0, '2025-10-27 22:52:08', 1),
(860, 79, 3, 75, 222, NULL, NULL, 0, '2025-10-27 22:52:08', 1),
(861, 68, 5, 152, 463, NULL, NULL, 0, '2025-10-27 23:06:56', 1),
(862, 68, 5, 164, 498, NULL, NULL, 1, '2025-10-27 23:06:56', 1),
(863, 68, 5, 158, 481, NULL, NULL, 1, '2025-10-27 23:06:56', 1),
(864, 68, 5, 157, 479, NULL, NULL, 1, '2025-10-27 23:06:56', 1),
(865, 68, 5, 155, 474, NULL, NULL, 0, '2025-10-27 23:06:56', 1),
(866, 68, 5, 165, 500, NULL, NULL, 0, '2025-10-27 23:06:56', 1),
(867, 68, 5, 150, 456, NULL, NULL, 1, '2025-10-27 23:06:56', 1),
(868, 68, 5, 170, 517, NULL, NULL, 0, '2025-10-27 23:06:56', 1),
(869, 68, 5, 159, 483, NULL, NULL, 1, '2025-10-27 23:06:56', 1),
(870, 68, 5, 168, 512, NULL, NULL, 1, '2025-10-27 23:06:56', 1),
(871, 68, 5, 162, 492, NULL, NULL, 0, '2025-10-27 23:06:56', 1),
(872, 68, 5, 167, 506, NULL, NULL, 0, '2025-10-27 23:06:56', 1),
(873, 68, 5, 163, 493, NULL, NULL, 0, '2025-10-27 23:06:56', 1),
(874, 68, 5, 169, 513, NULL, NULL, 0, '2025-10-27 23:06:56', 1),
(875, 68, 5, 151, 459, NULL, NULL, 0, '2025-10-27 23:06:56', 1),
(876, 68, 5, 156, 478, NULL, NULL, 0, '2025-10-27 23:06:56', 1),
(877, 68, 5, 153, 464, NULL, NULL, 0, '2025-10-27 23:06:56', 1),
(878, 68, 5, 166, 502, NULL, NULL, 0, '2025-10-27 23:06:56', 1),
(879, 68, 5, 172, 519, NULL, NULL, 0, '2025-10-27 23:06:56', 1),
(880, 68, 5, 154, 468, NULL, NULL, 0, '2025-10-27 23:06:56', 1),
(881, 44, 5, 161, 488, NULL, NULL, 0, '2025-10-27 23:08:14', 1),
(882, 44, 5, 168, 509, NULL, NULL, 0, '2025-10-27 23:08:14', 1),
(883, 44, 5, 170, 516, NULL, NULL, 0, '2025-10-27 23:08:14', 1),
(884, 44, 5, 159, 483, NULL, NULL, 1, '2025-10-27 23:08:14', 1),
(885, 44, 5, 169, 513, NULL, NULL, 0, '2025-10-27 23:08:14', 1),
(886, 44, 5, 157, 480, NULL, NULL, 0, '2025-10-27 23:08:14', 1),
(887, 44, 5, 163, 493, NULL, NULL, 0, '2025-10-27 23:08:14', 1),
(888, 44, 5, 148, 452, NULL, NULL, 0, '2025-10-27 23:08:14', 1),
(889, 44, 5, 150, 456, NULL, NULL, 1, '2025-10-27 23:08:14', 1),
(890, 44, 5, 167, 506, NULL, NULL, 0, '2025-10-27 23:08:14', 1),
(891, 44, 5, 152, 461, NULL, NULL, 1, '2025-10-27 23:08:14', 1),
(892, 44, 5, 156, 477, NULL, NULL, 0, '2025-10-27 23:08:14', 1),
(893, 44, 5, 158, 481, NULL, NULL, 1, '2025-10-27 23:08:14', 1),
(894, 44, 5, 160, 486, NULL, NULL, 0, '2025-10-27 23:08:14', 1),
(895, 44, 5, 172, 521, NULL, NULL, 1, '2025-10-27 23:08:14', 1),
(896, 44, 5, 164, 498, NULL, NULL, 1, '2025-10-27 23:08:14', 1),
(897, 44, 5, 154, 471, NULL, NULL, 1, '2025-10-27 23:08:14', 1),
(898, 44, 5, 149, 453, NULL, NULL, 0, '2025-10-27 23:08:14', 1),
(899, 44, 5, 151, 458, NULL, NULL, 0, '2025-10-27 23:08:14', 1),
(900, 44, 5, 162, 492, NULL, NULL, 0, '2025-10-27 23:08:14', 1),
(901, 57, 5, 161, 487, NULL, NULL, 1, '2025-10-27 23:10:19', 1),
(902, 57, 5, 158, 482, NULL, NULL, 0, '2025-10-27 23:10:19', 1),
(903, 57, 5, 164, 498, NULL, NULL, 1, '2025-10-27 23:10:19', 1),
(904, 57, 5, 171, NULL, NULL, NULL, NULL, '2025-10-27 23:10:19', 1),
(905, 57, 5, 172, 521, NULL, NULL, 1, '2025-10-27 23:10:19', 1),
(906, 57, 5, 150, 457, NULL, NULL, 0, '2025-10-27 23:10:19', 1),
(907, 57, 5, 165, 500, NULL, NULL, 0, '2025-10-27 23:10:19', 1),
(908, 57, 5, 160, 486, NULL, NULL, 0, '2025-10-27 23:10:19', 1),
(909, 57, 5, 151, 460, NULL, NULL, 1, '2025-10-27 23:10:19', 1),
(910, 57, 5, 157, 479, NULL, NULL, 1, '2025-10-27 23:10:19', 1),
(911, 57, 5, 155, 473, NULL, NULL, 0, '2025-10-27 23:10:19', 1),
(912, 57, 5, 170, 516, NULL, NULL, 0, '2025-10-27 23:10:19', 1),
(913, 57, 5, 154, 470, NULL, NULL, 0, '2025-10-27 23:10:19', 1),
(914, 57, 5, 152, 462, NULL, NULL, 0, '2025-10-27 23:10:19', 1),
(915, 57, 5, 159, 483, NULL, NULL, 1, '2025-10-27 23:10:19', 1),
(916, 57, 5, 168, 510, NULL, NULL, 0, '2025-10-27 23:10:19', 1),
(917, 57, 5, 162, 490, NULL, NULL, 0, '2025-10-27 23:10:19', 1),
(918, 57, 5, 148, 451, NULL, NULL, 1, '2025-10-27 23:10:19', 1),
(919, 57, 5, 156, 476, NULL, NULL, 1, '2025-10-27 23:10:19', 1),
(920, 57, 5, 163, 494, NULL, NULL, 1, '2025-10-27 23:10:19', 1),
(921, 45, 5, 158, 481, NULL, NULL, 1, '2025-10-27 23:11:03', 1),
(922, 45, 5, 167, 508, NULL, NULL, 0, '2025-10-27 23:11:03', 1),
(923, 45, 5, 151, 458, NULL, NULL, 0, '2025-10-27 23:11:03', 1),
(924, 45, 5, 153, 466, NULL, NULL, 0, '2025-10-27 23:11:03', 1),
(925, 45, 5, 154, 468, NULL, NULL, 0, '2025-10-27 23:11:03', 1),
(926, 45, 5, 164, 496, NULL, NULL, 0, '2025-10-27 23:11:03', 1),
(927, 45, 5, 148, 451, NULL, NULL, 1, '2025-10-27 23:11:03', 1),
(928, 45, 5, 149, 454, NULL, NULL, 1, '2025-10-27 23:11:03', 1),
(929, 45, 5, 157, 479, NULL, NULL, 1, '2025-10-27 23:11:03', 1),
(930, 45, 5, 168, 512, NULL, NULL, 1, '2025-10-27 23:11:03', 1),
(931, 45, 5, 163, 494, NULL, NULL, 1, '2025-10-27 23:11:03', 1),
(932, 45, 5, 160, 486, NULL, NULL, 0, '2025-10-27 23:11:03', 1),
(933, 45, 5, 172, 521, NULL, NULL, 1, '2025-10-27 23:11:03', 1),
(934, 45, 5, 152, 461, NULL, NULL, 1, '2025-10-27 23:11:03', 1),
(935, 45, 5, 156, 476, NULL, NULL, 1, '2025-10-27 23:11:03', 1),
(936, 45, 5, 171, NULL, NULL, NULL, NULL, '2025-10-27 23:11:03', 1),
(937, 45, 5, 155, 474, NULL, NULL, 0, '2025-10-27 23:11:03', 1),
(938, 45, 5, 159, 484, NULL, NULL, 0, '2025-10-27 23:11:03', 1),
(939, 45, 5, 169, 515, NULL, NULL, 1, '2025-10-27 23:11:03', 1),
(940, 45, 5, 165, 500, NULL, NULL, 0, '2025-10-27 23:11:03', 1),
(941, 66, 5, 162, 492, NULL, NULL, 0, '2025-10-27 23:11:56', 1),
(942, 66, 5, 148, 451, NULL, NULL, 1, '2025-10-27 23:11:56', 1),
(943, 66, 5, 157, 480, NULL, NULL, 0, '2025-10-27 23:11:56', 1),
(944, 66, 5, 156, 476, NULL, NULL, 1, '2025-10-27 23:11:56', 1),
(945, 66, 5, 149, 453, NULL, NULL, 0, '2025-10-27 23:11:56', 1),
(946, 66, 5, 166, 502, NULL, NULL, 0, '2025-10-27 23:11:56', 1),
(947, 66, 5, 158, 481, NULL, NULL, 1, '2025-10-27 23:11:56', 1),
(948, 66, 5, 154, 468, NULL, NULL, 0, '2025-10-27 23:11:56', 1),
(949, 66, 5, 168, 512, NULL, NULL, 1, '2025-10-27 23:11:56', 1),
(950, 66, 5, 165, 500, NULL, NULL, 0, '2025-10-27 23:11:56', 1),
(951, 66, 5, 151, 458, NULL, NULL, 0, '2025-10-27 23:11:56', 1),
(952, 66, 5, 152, 463, NULL, NULL, 0, '2025-10-27 23:11:56', 1),
(953, 66, 5, 169, 515, NULL, NULL, 1, '2025-10-27 23:11:56', 1),
(954, 66, 5, 160, 486, NULL, NULL, 0, '2025-10-27 23:11:56', 1),
(955, 66, 5, 155, 473, NULL, NULL, 0, '2025-10-27 23:11:56', 1),
(956, 66, 5, 150, 457, NULL, NULL, 0, '2025-10-27 23:11:56', 1),
(957, 66, 5, 163, 493, NULL, NULL, 0, '2025-10-27 23:11:56', 1),
(958, 66, 5, 159, 483, NULL, NULL, 1, '2025-10-27 23:11:56', 1),
(959, 66, 5, 172, 519, NULL, NULL, 0, '2025-10-27 23:11:56', 1),
(960, 66, 5, 164, 498, NULL, NULL, 1, '2025-10-27 23:11:56', 1),
(961, 61, 5, 162, 491, NULL, NULL, 1, '2025-10-27 23:12:31', 1),
(962, 61, 5, 153, 467, NULL, NULL, 1, '2025-10-27 23:12:31', 1),
(963, 61, 5, 158, 481, NULL, NULL, 1, '2025-10-27 23:12:31', 1),
(964, 61, 5, 156, 476, NULL, NULL, 1, '2025-10-27 23:12:31', 1),
(965, 61, 5, 163, 494, NULL, NULL, 1, '2025-10-27 23:12:31', 1),
(966, 61, 5, 167, 507, NULL, NULL, 0, '2025-10-27 23:12:31', 1),
(967, 61, 5, 151, 458, NULL, NULL, 0, '2025-10-27 23:12:31', 1),
(968, 61, 5, 172, 521, NULL, NULL, 1, '2025-10-27 23:12:31', 1),
(969, 61, 5, 149, 453, NULL, NULL, 0, '2025-10-27 23:12:31', 1),
(970, 61, 5, 170, 516, NULL, NULL, 0, '2025-10-27 23:12:31', 1),
(971, 61, 5, 157, 479, NULL, NULL, 1, '2025-10-27 23:12:31', 1),
(972, 61, 5, 152, 461, NULL, NULL, 1, '2025-10-27 23:12:31', 1),
(973, 61, 5, 154, 471, NULL, NULL, 1, '2025-10-27 23:12:31', 1),
(974, 61, 5, 161, 487, NULL, NULL, 1, '2025-10-27 23:12:31', 1),
(975, 61, 5, 166, 501, NULL, NULL, 0, '2025-10-27 23:12:31', 1),
(976, 61, 5, 155, 472, NULL, NULL, 1, '2025-10-27 23:12:31', 1),
(977, 61, 5, 159, 483, NULL, NULL, 1, '2025-10-27 23:12:31', 1),
(978, 61, 5, 160, 485, NULL, NULL, 1, '2025-10-27 23:12:31', 1),
(979, 61, 5, 148, 451, NULL, NULL, 1, '2025-10-27 23:12:31', 1),
(980, 61, 5, 169, 513, NULL, NULL, 0, '2025-10-27 23:12:31', 1),
(981, 47, 5, 163, 493, NULL, NULL, 0, '2025-10-27 23:14:37', 1),
(982, 47, 5, 156, 476, NULL, NULL, 1, '2025-10-27 23:14:37', 1),
(983, 47, 5, 149, 453, NULL, NULL, 0, '2025-10-27 23:14:37', 1),
(984, 47, 5, 151, 458, NULL, NULL, 0, '2025-10-27 23:14:37', 1),
(985, 47, 5, 155, 473, NULL, NULL, 0, '2025-10-27 23:14:37', 1),
(986, 47, 5, 161, 487, NULL, NULL, 1, '2025-10-27 23:14:37', 1),
(987, 47, 5, 159, 483, NULL, NULL, 1, '2025-10-27 23:14:37', 1),
(988, 47, 5, 158, 481, NULL, NULL, 1, '2025-10-27 23:14:37', 1),
(989, 47, 5, 148, 451, NULL, NULL, 1, '2025-10-27 23:14:37', 1),
(990, 47, 5, 152, 461, NULL, NULL, 1, '2025-10-27 23:14:37', 1),
(991, 47, 5, 167, 505, NULL, NULL, 1, '2025-10-27 23:14:37', 1),
(992, 47, 5, 157, 479, NULL, NULL, 1, '2025-10-27 23:14:37', 1),
(993, 47, 5, 150, 455, NULL, NULL, 0, '2025-10-27 23:14:37', 1),
(994, 47, 5, 172, 520, NULL, NULL, 0, '2025-10-27 23:14:37', 1),
(995, 47, 5, 164, 498, NULL, NULL, 1, '2025-10-27 23:14:37', 1),
(996, 47, 5, 162, 492, NULL, NULL, 0, '2025-10-27 23:14:37', 1),
(997, 47, 5, 153, 464, NULL, NULL, 0, '2025-10-27 23:14:37', 1),
(998, 47, 5, 168, 512, NULL, NULL, 1, '2025-10-27 23:14:37', 1),
(999, 47, 5, 160, 485, NULL, NULL, 1, '2025-10-27 23:14:37', 1),
(1000, 47, 5, 165, 499, NULL, NULL, 1, '2025-10-27 23:14:37', 1),
(1001, 62, 5, 157, 479, NULL, NULL, 1, '2025-10-27 23:14:53', 1),
(1002, 62, 5, 167, 506, NULL, NULL, 0, '2025-10-27 23:14:53', 1),
(1003, 62, 5, 152, 463, NULL, NULL, 0, '2025-10-27 23:14:53', 1),
(1004, 62, 5, 151, 458, NULL, NULL, 0, '2025-10-27 23:14:53', 1),
(1005, 62, 5, 153, 465, NULL, NULL, 0, '2025-10-27 23:14:53', 1),
(1006, 62, 5, 150, 455, NULL, NULL, 0, '2025-10-27 23:14:53', 1),
(1007, 62, 5, 166, 502, NULL, NULL, 0, '2025-10-27 23:14:53', 1),
(1008, 62, 5, 156, 476, NULL, NULL, 1, '2025-10-27 23:14:53', 1),
(1009, 62, 5, 169, 515, NULL, NULL, 1, '2025-10-27 23:14:53', 1),
(1010, 62, 5, 154, 470, NULL, NULL, 0, '2025-10-27 23:14:53', 1),
(1011, 62, 5, 164, 498, NULL, NULL, 1, '2025-10-27 23:14:53', 1),
(1012, 62, 5, 158, 481, NULL, NULL, 1, '2025-10-27 23:14:53', 1),
(1013, 62, 5, 160, 486, NULL, NULL, 0, '2025-10-27 23:14:53', 1),
(1014, 62, 5, 163, 494, NULL, NULL, 1, '2025-10-27 23:14:53', 1),
(1015, 62, 5, 161, 487, NULL, NULL, 1, '2025-10-27 23:14:53', 1),
(1016, 62, 5, 155, 473, NULL, NULL, 0, '2025-10-27 23:14:53', 1),
(1017, 62, 5, 170, 516, NULL, NULL, 0, '2025-10-27 23:14:53', 1),
(1018, 62, 5, 149, 453, NULL, NULL, 0, '2025-10-27 23:14:53', 1),
(1019, 62, 5, 172, 519, NULL, NULL, 0, '2025-10-27 23:14:53', 1),
(1020, 62, 5, 165, 500, NULL, NULL, 0, '2025-10-27 23:14:53', 1),
(1021, 70, 2, 123, 369, NULL, NULL, 0, '2025-10-27 23:18:07', 1),
(1022, 70, 2, 133, 402, NULL, NULL, 0, '2025-10-27 23:18:07', 1),
(1023, 70, 2, 124, 372, NULL, NULL, 0, '2025-10-27 23:18:07', 1),
(1024, 70, 2, 130, 394, NULL, NULL, 0, '2025-10-27 23:18:07', 1),
(1025, 70, 2, 127, 385, NULL, NULL, 1, '2025-10-27 23:18:07', 1),
(1026, 70, 2, 139, 424, NULL, NULL, 0, '2025-10-27 23:18:07', 1),
(1027, 70, 2, 136, 414, NULL, NULL, 0, '2025-10-27 23:18:07', 1),
(1028, 70, 2, 129, 389, NULL, NULL, 1, '2025-10-27 23:18:07', 1),
(1029, 70, 2, 144, 440, NULL, NULL, 1, '2025-10-27 23:18:07', 1),
(1030, 70, 2, 134, 408, NULL, NULL, 1, '2025-10-27 23:18:07', 1),
(1031, 70, 2, 146, 444, NULL, NULL, 0, '2025-10-27 23:18:07', 1),
(1032, 70, 2, 147, 447, NULL, NULL, 0, '2025-10-27 23:18:07', 1),
(1033, 70, 2, 128, 387, NULL, NULL, 1, '2025-10-27 23:18:07', 1),
(1034, 70, 2, 125, 375, NULL, NULL, 0, '2025-10-27 23:18:07', 1),
(1035, 70, 2, 140, 428, NULL, NULL, 0, '2025-10-27 23:18:07', 1),
(1036, 70, 2, 141, 430, NULL, NULL, 0, '2025-10-27 23:18:07', 1),
(1037, 70, 2, 138, 422, NULL, NULL, 0, '2025-10-27 23:18:07', 1),
(1038, 70, 2, 126, 379, NULL, NULL, 0, '2025-10-27 23:18:07', 1),
(1039, 70, 2, 132, 399, NULL, NULL, 0, '2025-10-27 23:18:07', 1),
(1040, 70, 2, 143, 437, NULL, NULL, 1, '2025-10-27 23:18:07', 1),
(1041, 49, 5, 157, 479, NULL, NULL, 1, '2025-10-27 23:18:39', 1),
(1042, 49, 5, 154, 468, NULL, NULL, 0, '2025-10-27 23:18:39', 1),
(1043, 49, 5, 151, 459, NULL, NULL, 0, '2025-10-27 23:18:39', 1),
(1044, 49, 5, 148, 451, NULL, NULL, 1, '2025-10-27 23:18:39', 1),
(1045, 49, 5, 168, 509, NULL, NULL, 0, '2025-10-27 23:18:39', 1),
(1046, 49, 5, 152, 463, NULL, NULL, 0, '2025-10-27 23:18:39', 1),
(1047, 49, 5, 169, 515, NULL, NULL, 1, '2025-10-27 23:18:39', 1),
(1048, 49, 5, 167, 508, NULL, NULL, 0, '2025-10-27 23:18:39', 1),
(1049, 49, 5, 160, 486, NULL, NULL, 0, '2025-10-27 23:18:39', 1),
(1050, 49, 5, 149, 454, NULL, NULL, 1, '2025-10-27 23:18:39', 1),
(1051, 49, 5, 162, 491, NULL, NULL, 1, '2025-10-27 23:18:39', 1),
(1052, 49, 5, 158, 481, NULL, NULL, 1, '2025-10-27 23:18:39', 1),
(1053, 49, 5, 172, 521, NULL, NULL, 1, '2025-10-27 23:18:39', 1),
(1054, 49, 5, 156, 476, NULL, NULL, 1, '2025-10-27 23:18:39', 1),
(1055, 49, 5, 155, 473, NULL, NULL, 0, '2025-10-27 23:18:39', 1),
(1056, 49, 5, 161, 487, NULL, NULL, 1, '2025-10-27 23:18:39', 1),
(1057, 49, 5, 159, 483, NULL, NULL, 1, '2025-10-27 23:18:39', 1),
(1058, 49, 5, 153, 466, NULL, NULL, 0, '2025-10-27 23:18:39', 1),
(1059, 49, 5, 166, 501, NULL, NULL, 0, '2025-10-27 23:18:39', 1),
(1060, 49, 5, 164, 498, NULL, NULL, 1, '2025-10-27 23:18:39', 1),
(1061, 45, 2, 141, 430, NULL, NULL, 0, '2025-10-27 23:19:15', 1),
(1062, 45, 2, 124, 372, NULL, NULL, 0, '2025-10-27 23:19:15', 1),
(1063, 45, 2, 146, 443, NULL, NULL, 0, '2025-10-27 23:19:15', 1),
(1064, 45, 2, 131, 397, NULL, NULL, 1, '2025-10-27 23:19:15', 1),
(1065, 45, 2, 123, 369, NULL, NULL, 0, '2025-10-27 23:19:15', 1),
(1066, 45, 2, 132, 401, NULL, NULL, 1, '2025-10-27 23:19:15', 1),
(1067, 45, 2, 135, 410, NULL, NULL, 0, '2025-10-27 23:19:15', 1),
(1068, 45, 2, 147, 448, NULL, NULL, 1, '2025-10-27 23:19:15', 1),
(1069, 45, 2, 127, 383, NULL, NULL, 0, '2025-10-27 23:19:15', 1),
(1070, 45, 2, 133, 404, NULL, NULL, 0, '2025-10-27 23:19:15', 1),
(1071, 45, 2, 129, 389, NULL, NULL, 1, '2025-10-27 23:19:15', 1),
(1072, 45, 2, 142, 433, NULL, NULL, 1, '2025-10-27 23:19:15', 1),
(1073, 45, 2, 128, 387, NULL, NULL, 1, '2025-10-27 23:19:15', 1),
(1074, 45, 2, 130, 393, NULL, NULL, 1, '2025-10-27 23:19:15', 1),
(1075, 45, 2, 140, 428, NULL, NULL, 0, '2025-10-27 23:19:15', 1),
(1076, 45, 2, 144, 440, NULL, NULL, 1, '2025-10-27 23:19:15', 1),
(1077, 45, 2, 138, 421, NULL, NULL, 1, '2025-10-27 23:19:15', 1),
(1078, 45, 2, 143, 437, NULL, NULL, 1, '2025-10-27 23:19:15', 1),
(1079, 45, 2, 125, 376, NULL, NULL, 0, '2025-10-27 23:19:15', 1),
(1080, 45, 2, 137, 417, NULL, NULL, 0, '2025-10-27 23:19:15', 1),
(1081, 94, 5, 156, 476, NULL, NULL, 1, '2025-10-27 23:20:18', 1),
(1082, 94, 5, 160, 485, NULL, NULL, 1, '2025-10-27 23:20:18', 1),
(1083, 94, 5, 152, 461, NULL, NULL, 1, '2025-10-27 23:20:18', 1),
(1084, 94, 5, 154, 469, NULL, NULL, 0, '2025-10-27 23:20:18', 1),
(1085, 94, 5, 149, 453, NULL, NULL, 0, '2025-10-27 23:20:18', 1),
(1086, 94, 5, 172, 521, NULL, NULL, 1, '2025-10-27 23:20:18', 1),
(1087, 94, 5, 151, 458, NULL, NULL, 0, '2025-10-27 23:20:18', 1),
(1088, 94, 5, 166, 501, NULL, NULL, 0, '2025-10-27 23:20:18', 1),
(1089, 94, 5, 165, 499, NULL, NULL, 1, '2025-10-27 23:20:18', 1),
(1090, 94, 5, 162, 491, NULL, NULL, 1, '2025-10-27 23:20:18', 1),
(1091, 94, 5, 169, 513, NULL, NULL, 0, '2025-10-27 23:20:18', 1),
(1092, 94, 5, 150, 455, NULL, NULL, 0, '2025-10-27 23:20:18', 1),
(1093, 94, 5, 167, 505, NULL, NULL, 1, '2025-10-27 23:20:18', 1),
(1094, 94, 5, 155, 475, NULL, NULL, 0, '2025-10-27 23:20:18', 1),
(1095, 94, 5, 159, 483, NULL, NULL, 1, '2025-10-27 23:20:18', 1),
(1096, 94, 5, 168, 510, NULL, NULL, 0, '2025-10-27 23:20:18', 1),
(1097, 94, 5, 163, 494, NULL, NULL, 1, '2025-10-27 23:20:18', 1),
(1098, 94, 5, 153, 464, NULL, NULL, 0, '2025-10-27 23:20:18', 1),
(1099, 94, 5, 158, 481, NULL, NULL, 1, '2025-10-27 23:20:18', 1),
(1100, 94, 5, 157, 479, NULL, NULL, 1, '2025-10-27 23:20:18', 1),
(1101, 61, 2, 125, 376, NULL, NULL, 0, '2025-10-27 23:20:20', 1),
(1102, 61, 2, 128, 388, NULL, NULL, 0, '2025-10-27 23:20:20', 1),
(1103, 61, 2, 142, 434, NULL, NULL, 0, '2025-10-27 23:20:20', 1),
(1104, 61, 2, 135, 413, NULL, NULL, 1, '2025-10-27 23:20:20', 1),
(1105, 61, 2, 131, 396, NULL, NULL, 0, '2025-10-27 23:20:20', 1),
(1106, 61, 2, 143, 437, NULL, NULL, 1, '2025-10-27 23:20:20', 1),
(1107, 61, 2, 137, 418, NULL, NULL, 1, '2025-10-27 23:20:20', 1),
(1108, 61, 2, 144, 440, NULL, NULL, 1, '2025-10-27 23:20:20', 1),
(1109, 61, 2, 138, 421, NULL, NULL, 1, '2025-10-27 23:20:20', 1),
(1110, 61, 2, 130, 392, NULL, NULL, 0, '2025-10-27 23:20:20', 1),
(1111, 61, 2, 140, 429, NULL, NULL, 1, '2025-10-27 23:20:20', 1),
(1112, 61, 2, 126, 379, NULL, NULL, 0, '2025-10-27 23:20:20', 1),
(1113, 61, 2, 127, 385, NULL, NULL, 1, '2025-10-27 23:20:20', 1),
(1114, 61, 2, 134, 409, NULL, NULL, 0, '2025-10-27 23:20:20', 1),
(1115, 61, 2, 132, 401, NULL, NULL, 1, '2025-10-27 23:20:20', 1),
(1116, 61, 2, 147, 449, NULL, NULL, 0, '2025-10-27 23:20:20', 1),
(1117, 61, 2, 141, 432, NULL, NULL, 1, '2025-10-27 23:20:20', 1),
(1118, 61, 2, 129, 391, NULL, NULL, 0, '2025-10-27 23:20:20', 1),
(1119, 61, 2, 133, 402, NULL, NULL, 0, '2025-10-27 23:20:20', 1),
(1120, 61, 2, 136, 414, NULL, NULL, 0, '2025-10-27 23:20:20', 1),
(1121, 57, 2, 124, 372, NULL, NULL, 0, '2025-10-27 23:21:11', 1),
(1122, 57, 2, 133, 402, NULL, NULL, 0, '2025-10-27 23:21:11', 1),
(1123, 57, 2, 146, 444, NULL, NULL, 0, '2025-10-27 23:21:11', 1),
(1124, 57, 2, 141, 431, NULL, NULL, 0, '2025-10-27 23:21:11', 1),
(1125, 57, 2, 123, 369, NULL, NULL, 0, '2025-10-27 23:21:11', 1),
(1126, 57, 2, 131, 397, NULL, NULL, 1, '2025-10-27 23:21:11', 1),
(1127, 57, 2, 130, 393, NULL, NULL, 1, '2025-10-27 23:21:11', 1),
(1128, 57, 2, 132, 401, NULL, NULL, 1, '2025-10-27 23:21:11', 1),
(1129, 57, 2, 144, 440, NULL, NULL, 1, '2025-10-27 23:21:11', 1),
(1130, 57, 2, 138, 423, NULL, NULL, 0, '2025-10-27 23:21:11', 1),
(1131, 57, 2, 134, 407, NULL, NULL, 0, '2025-10-27 23:21:11', 1),
(1132, 57, 2, 139, 424, NULL, NULL, 0, '2025-10-27 23:21:11', 1),
(1133, 57, 2, 140, 429, NULL, NULL, 1, '2025-10-27 23:21:11', 1),
(1134, 57, 2, 127, 385, NULL, NULL, 1, '2025-10-27 23:21:11', 1),
(1135, 57, 2, 128, 387, NULL, NULL, 1, '2025-10-27 23:21:11', 1),
(1136, 57, 2, 137, 418, NULL, NULL, 1, '2025-10-27 23:21:11', 1),
(1137, 57, 2, 125, 376, NULL, NULL, 0, '2025-10-27 23:21:11', 1),
(1138, 57, 2, 126, 380, NULL, NULL, 1, '2025-10-27 23:21:11', 1),
(1139, 57, 2, 135, 410, NULL, NULL, 0, '2025-10-27 23:21:11', 1),
(1140, 57, 2, 145, 442, NULL, NULL, 1, '2025-10-27 23:21:11', 1),
(1141, 63, 5, 172, 520, NULL, NULL, 0, '2025-10-27 23:21:14', 1),
(1142, 63, 5, 161, 487, NULL, NULL, 1, '2025-10-27 23:21:14', 1),
(1143, 63, 5, 167, 506, NULL, NULL, 0, '2025-10-27 23:21:14', 1),
(1144, 63, 5, 156, 477, NULL, NULL, 0, '2025-10-27 23:21:14', 1),
(1145, 63, 5, 150, 457, NULL, NULL, 0, '2025-10-27 23:21:14', 1),
(1146, 63, 5, 159, 483, NULL, NULL, 1, '2025-10-27 23:21:14', 1),
(1147, 63, 5, 154, 468, NULL, NULL, 0, '2025-10-27 23:21:14', 1),
(1148, 63, 5, 160, 485, NULL, NULL, 1, '2025-10-27 23:21:14', 1),
(1149, 63, 5, 165, 500, NULL, NULL, 0, '2025-10-27 23:21:14', 1),
(1150, 63, 5, 169, 514, NULL, NULL, 0, '2025-10-27 23:21:14', 1),
(1151, 63, 5, 148, 452, NULL, NULL, 0, '2025-10-27 23:21:14', 1),
(1152, 63, 5, 152, 463, NULL, NULL, 0, '2025-10-27 23:21:14', 1),
(1153, 63, 5, 163, 493, NULL, NULL, 0, '2025-10-27 23:21:14', 1),
(1154, 63, 5, 149, 453, NULL, NULL, 0, '2025-10-27 23:21:14', 1),
(1155, 63, 5, 153, 466, NULL, NULL, 0, '2025-10-27 23:21:14', 1),
(1156, 63, 5, 162, 492, NULL, NULL, 0, '2025-10-27 23:21:14', 1),
(1157, 63, 5, 151, 458, NULL, NULL, 0, '2025-10-27 23:21:14', 1),
(1158, 63, 5, 158, 482, NULL, NULL, 0, '2025-10-27 23:21:14', 1),
(1159, 63, 5, 157, 479, NULL, NULL, 1, '2025-10-27 23:21:14', 1),
(1160, 63, 5, 166, 504, NULL, NULL, 0, '2025-10-27 23:21:14', 1),
(1161, 50, 5, 159, 483, NULL, NULL, 1, '2025-10-27 23:21:27', 1),
(1162, 50, 5, 148, 451, NULL, NULL, 1, '2025-10-27 23:21:27', 1),
(1163, 50, 5, 156, 476, NULL, NULL, 1, '2025-10-27 23:21:27', 1),
(1164, 50, 5, 149, 453, NULL, NULL, 0, '2025-10-27 23:21:27', 1),
(1165, 50, 5, 153, 466, NULL, NULL, 0, '2025-10-27 23:21:27', 1),
(1166, 50, 5, 166, 503, NULL, NULL, 1, '2025-10-27 23:21:27', 1),
(1167, 50, 5, 151, 458, NULL, NULL, 0, '2025-10-27 23:21:27', 1),
(1168, 50, 5, 161, 487, NULL, NULL, 1, '2025-10-27 23:21:27', 1),
(1169, 50, 5, 150, 457, NULL, NULL, 0, '2025-10-27 23:21:27', 1),
(1170, 50, 5, 165, 500, NULL, NULL, 0, '2025-10-27 23:21:27', 1),
(1171, 50, 5, 167, 508, NULL, NULL, 0, '2025-10-27 23:21:27', 1),
(1172, 50, 5, 169, 514, NULL, NULL, 0, '2025-10-27 23:21:27', 1),
(1173, 50, 5, 163, 494, NULL, NULL, 1, '2025-10-27 23:21:27', 1),
(1174, 50, 5, 172, 519, NULL, NULL, 0, '2025-10-27 23:21:27', 1),
(1175, 50, 5, 158, 481, NULL, NULL, 1, '2025-10-27 23:21:27', 1),
(1176, 50, 5, 157, 479, NULL, NULL, 1, '2025-10-27 23:21:27', 1),
(1177, 50, 5, 160, 485, NULL, NULL, 1, '2025-10-27 23:21:27', 1),
(1178, 50, 5, 168, 512, NULL, NULL, 1, '2025-10-27 23:21:27', 1),
(1179, 50, 5, 162, 492, NULL, NULL, 0, '2025-10-27 23:21:27', 1),
(1180, 50, 5, 154, 468, NULL, NULL, 0, '2025-10-27 23:21:27', 1),
(1181, 68, 2, 130, 394, NULL, NULL, 0, '2025-10-27 23:22:30', 1),
(1182, 68, 2, 127, 385, NULL, NULL, 1, '2025-10-27 23:22:30', 1),
(1183, 68, 2, 128, 387, NULL, NULL, 1, '2025-10-27 23:22:30', 1),
(1184, 68, 2, 142, 435, NULL, NULL, 0, '2025-10-27 23:22:30', 1),
(1185, 68, 2, 132, 399, NULL, NULL, 0, '2025-10-27 23:22:30', 1),
(1186, 68, 2, 144, 440, NULL, NULL, 1, '2025-10-27 23:22:30', 1),
(1187, 68, 2, 125, 376, NULL, NULL, 0, '2025-10-27 23:22:30', 1),
(1188, 68, 2, 138, 423, NULL, NULL, 0, '2025-10-27 23:22:30', 1),
(1189, 68, 2, 126, 380, NULL, NULL, 1, '2025-10-27 23:22:30', 1),
(1190, 68, 2, 129, 390, NULL, NULL, 0, '2025-10-27 23:22:30', 1),
(1191, 68, 2, 147, 448, NULL, NULL, 1, '2025-10-27 23:22:30', 1),
(1192, 68, 2, 135, 410, NULL, NULL, 0, '2025-10-27 23:22:30', 1),
(1193, 68, 2, 137, 419, NULL, NULL, 0, '2025-10-27 23:22:30', 1),
(1194, 68, 2, 134, 409, NULL, NULL, 0, '2025-10-27 23:22:30', 1),
(1195, 68, 2, 145, 441, NULL, NULL, 0, '2025-10-27 23:22:30', 1),
(1196, 68, 2, 124, 373, NULL, NULL, 0, '2025-10-27 23:22:30', 1),
(1197, 68, 2, 131, 397, NULL, NULL, 1, '2025-10-27 23:22:30', 1),
(1198, 68, 2, 141, 432, NULL, NULL, 1, '2025-10-27 23:22:30', 1),
(1199, 68, 2, 146, 443, NULL, NULL, 0, '2025-10-27 23:22:30', 1),
(1200, 68, 2, 140, 429, NULL, NULL, 1, '2025-10-27 23:22:30', 1),
(1201, 48, 2, 141, 432, NULL, NULL, 1, '2025-10-27 23:23:36', 1),
(1202, 48, 2, 147, 448, NULL, NULL, 1, '2025-10-27 23:23:36', 1),
(1203, 48, 2, 146, 443, NULL, NULL, 0, '2025-10-27 23:23:36', 1),
(1204, 48, 2, 127, 385, NULL, NULL, 1, '2025-10-27 23:23:36', 1),
(1205, 48, 2, 129, 391, NULL, NULL, 0, '2025-10-27 23:23:36', 1),
(1206, 48, 2, 139, 426, NULL, NULL, 1, '2025-10-27 23:23:36', 1),
(1207, 48, 2, 124, 374, NULL, NULL, 1, '2025-10-27 23:23:36', 1),
(1208, 48, 2, 128, 387, NULL, NULL, 1, '2025-10-27 23:23:36', 1),
(1209, 48, 2, 131, 397, NULL, NULL, 1, '2025-10-27 23:23:36', 1),
(1210, 48, 2, 132, 401, NULL, NULL, 1, '2025-10-27 23:23:36', 1),
(1211, 48, 2, 142, 433, NULL, NULL, 1, '2025-10-27 23:23:36', 1),
(1212, 48, 2, 125, 378, NULL, NULL, 0, '2025-10-27 23:23:36', 1),
(1213, 48, 2, 137, 419, NULL, NULL, 0, '2025-10-27 23:23:36', 1),
(1214, 48, 2, 144, 440, NULL, NULL, 1, '2025-10-27 23:23:36', 1),
(1215, 48, 2, 135, 410, NULL, NULL, 0, '2025-10-27 23:23:36', 1),
(1216, 48, 2, 126, 380, NULL, NULL, 1, '2025-10-27 23:23:36', 1),
(1217, 48, 2, 143, 437, NULL, NULL, 1, '2025-10-27 23:23:36', 1),
(1218, 48, 2, 130, 393, NULL, NULL, 1, '2025-10-27 23:23:36', 1),
(1219, 48, 2, 133, 404, NULL, NULL, 0, '2025-10-27 23:23:36', 1),
(1220, 48, 2, 145, 442, NULL, NULL, 1, '2025-10-27 23:23:36', 1),
(1221, 44, 2, 127, 385, NULL, NULL, 1, '2025-10-27 23:24:02', 1),
(1222, 44, 2, 139, 426, NULL, NULL, 1, '2025-10-27 23:24:02', 1),
(1223, 44, 2, 128, 387, NULL, NULL, 1, '2025-10-27 23:24:02', 1),
(1224, 44, 2, 141, 432, NULL, NULL, 1, '2025-10-27 23:24:02', 1),
(1225, 44, 2, 123, 369, NULL, NULL, 0, '2025-10-27 23:24:02', 1),
(1226, 44, 2, 136, 416, NULL, NULL, 1, '2025-10-27 23:24:02', 1),
(1227, 44, 2, 138, 421, NULL, NULL, 1, '2025-10-27 23:24:02', 1),
(1228, 44, 2, 134, 408, NULL, NULL, 1, '2025-10-27 23:24:02', 1),
(1229, 44, 2, 126, 380, NULL, NULL, 1, '2025-10-27 23:24:02', 1),
(1230, 44, 2, 144, 440, NULL, NULL, 1, '2025-10-27 23:24:02', 1),
(1231, 44, 2, 132, 401, NULL, NULL, 1, '2025-10-27 23:24:02', 1),
(1232, 44, 2, 135, 413, NULL, NULL, 1, '2025-10-27 23:24:02', 1),
(1233, 44, 2, 142, 433, NULL, NULL, 1, '2025-10-27 23:24:02', 1),
(1234, 44, 2, 140, 428, NULL, NULL, 0, '2025-10-27 23:24:02', 1),
(1235, 44, 2, 130, 394, NULL, NULL, 0, '2025-10-27 23:24:02', 1),
(1236, 44, 2, 129, 391, NULL, NULL, 0, '2025-10-27 23:24:02', 1),
(1237, 44, 2, 146, 443, NULL, NULL, 0, '2025-10-27 23:24:02', 1),
(1238, 44, 2, 147, 449, NULL, NULL, 0, '2025-10-27 23:24:02', 1),
(1239, 44, 2, 124, 372, NULL, NULL, 0, '2025-10-27 23:24:02', 1),
(1240, 44, 2, 133, 403, NULL, NULL, 1, '2025-10-27 23:24:02', 1),
(1241, 56, 2, 127, 384, NULL, NULL, 0, '2025-10-27 23:24:08', 1),
(1242, 56, 2, 123, 371, NULL, NULL, 0, '2025-10-27 23:24:08', 1),
(1243, 56, 2, 136, 415, NULL, NULL, 0, '2025-10-27 23:24:08', 1),
(1244, 56, 2, 131, 396, NULL, NULL, 0, '2025-10-27 23:24:08', 1),
(1245, 56, 2, 137, 420, NULL, NULL, 0, '2025-10-27 23:24:08', 1),
(1246, 56, 2, 126, 382, NULL, NULL, 0, '2025-10-27 23:24:08', 1),
(1247, 56, 2, 142, 435, NULL, NULL, 0, '2025-10-27 23:24:08', 1),
(1248, 56, 2, 134, 408, NULL, NULL, 1, '2025-10-27 23:24:08', 1),
(1249, 56, 2, 144, 440, NULL, NULL, 1, '2025-10-27 23:24:08', 1),
(1250, 56, 2, 138, 422, NULL, NULL, 0, '2025-10-27 23:24:08', 1),
(1251, 56, 2, 133, 402, NULL, NULL, 0, '2025-10-27 23:24:08', 1),
(1252, 56, 2, 128, 387, NULL, NULL, 1, '2025-10-27 23:24:08', 1),
(1253, 56, 2, 147, 448, NULL, NULL, 1, '2025-10-27 23:24:08', 1),
(1254, 56, 2, 143, 436, NULL, NULL, 0, '2025-10-27 23:24:08', 1),
(1255, 56, 2, 125, 376, NULL, NULL, 0, '2025-10-27 23:24:08', 1),
(1256, 56, 2, 130, 392, NULL, NULL, 0, '2025-10-27 23:24:08', 1),
(1257, 56, 2, 129, 391, NULL, NULL, 0, '2025-10-27 23:24:08', 1),
(1258, 56, 2, 140, 427, NULL, NULL, 0, '2025-10-27 23:24:08', 1),
(1259, 56, 2, 146, 444, NULL, NULL, 0, '2025-10-27 23:24:08', 1),
(1260, 56, 2, 132, 401, NULL, NULL, 1, '2025-10-27 23:24:08', 1),
(1261, 46, 2, 131, 396, NULL, NULL, 0, '2025-10-27 23:24:08', 1),
(1262, 46, 2, 139, 426, NULL, NULL, 1, '2025-10-27 23:24:08', 1),
(1263, 46, 2, 141, 432, NULL, NULL, 1, '2025-10-27 23:24:08', 1),
(1264, 46, 2, 132, 401, NULL, NULL, 1, '2025-10-27 23:24:08', 1),
(1265, 46, 2, 127, 383, NULL, NULL, 0, '2025-10-27 23:24:08', 1),
(1266, 46, 2, 124, 372, NULL, NULL, 0, '2025-10-27 23:24:08', 1),
(1267, 46, 2, 130, 392, NULL, NULL, 0, '2025-10-27 23:24:08', 1),
(1268, 46, 2, 137, 420, NULL, NULL, 0, '2025-10-27 23:24:08', 1),
(1269, 46, 2, 144, 440, NULL, NULL, 1, '2025-10-27 23:24:08', 1),
(1270, 46, 2, 125, 377, NULL, NULL, 1, '2025-10-27 23:24:08', 1),
(1271, 46, 2, 128, 387, NULL, NULL, 1, '2025-10-27 23:24:08', 1),
(1272, 46, 2, 140, 429, NULL, NULL, 1, '2025-10-27 23:24:08', 1),
(1273, 46, 2, 147, 449, NULL, NULL, 0, '2025-10-27 23:24:08', 1),
(1274, 46, 2, 126, 380, NULL, NULL, 1, '2025-10-27 23:24:08', 1),
(1275, 46, 2, 129, 391, NULL, NULL, 0, '2025-10-27 23:24:08', 1),
(1276, 46, 2, 135, 410, NULL, NULL, 0, '2025-10-27 23:24:08', 1),
(1277, 46, 2, 138, 422, NULL, NULL, 0, '2025-10-27 23:24:08', 1),
(1278, 46, 2, 143, 437, NULL, NULL, 1, '2025-10-27 23:24:08', 1),
(1279, 46, 2, 133, 402, NULL, NULL, 0, '2025-10-27 23:24:08', 1),
(1280, 46, 2, 146, 444, NULL, NULL, 0, '2025-10-27 23:24:08', 1),
(1281, 64, 2, 137, 418, NULL, NULL, 1, '2025-10-27 23:24:35', 1),
(1282, 64, 2, 145, 441, NULL, NULL, 0, '2025-10-27 23:24:35', 1),
(1283, 64, 2, 142, 433, NULL, NULL, 1, '2025-10-27 23:24:35', 1),
(1284, 64, 2, 135, 413, NULL, NULL, 1, '2025-10-27 23:24:35', 1),
(1285, 64, 2, 138, 422, NULL, NULL, 0, '2025-10-27 23:24:35', 1),
(1286, 64, 2, 139, 424, NULL, NULL, 0, '2025-10-27 23:24:35', 1),
(1287, 64, 2, 128, 387, NULL, NULL, 1, '2025-10-27 23:24:35', 1),
(1288, 64, 2, 129, 390, NULL, NULL, 0, '2025-10-27 23:24:35', 1),
(1289, 64, 2, 127, 385, NULL, NULL, 1, '2025-10-27 23:24:35', 1),
(1290, 64, 2, 132, 401, NULL, NULL, 1, '2025-10-27 23:24:35', 1),
(1291, 64, 2, 131, 397, NULL, NULL, 1, '2025-10-27 23:24:35', 1),
(1292, 64, 2, 146, 445, NULL, NULL, 1, '2025-10-27 23:24:35', 1),
(1293, 64, 2, 140, 427, NULL, NULL, 0, '2025-10-27 23:24:35', 1),
(1294, 64, 2, 133, 403, NULL, NULL, 1, '2025-10-27 23:24:35', 1),
(1295, 64, 2, 123, 371, NULL, NULL, 0, '2025-10-27 23:24:35', 1),
(1296, 64, 2, 144, 438, NULL, NULL, 0, '2025-10-27 23:24:35', 1),
(1297, 64, 2, 134, 408, NULL, NULL, 1, '2025-10-27 23:24:35', 1),
(1298, 64, 2, 147, 449, NULL, NULL, 0, '2025-10-27 23:24:35', 1),
(1299, 64, 2, 126, 382, NULL, NULL, 0, '2025-10-27 23:24:35', 1),
(1300, 64, 2, 143, 436, NULL, NULL, 0, '2025-10-27 23:24:35', 1),
(1301, 43, 2, 137, 418, NULL, NULL, 1, '2025-10-27 23:25:02', 1),
(1302, 43, 2, 139, 426, NULL, NULL, 1, '2025-10-27 23:25:02', 1),
(1303, 43, 2, 127, 385, NULL, NULL, 1, '2025-10-27 23:25:02', 1),
(1304, 43, 2, 128, 387, NULL, NULL, 1, '2025-10-27 23:25:02', 1),
(1305, 43, 2, 142, 433, NULL, NULL, 1, '2025-10-27 23:25:02', 1),
(1306, 43, 2, 133, 403, NULL, NULL, 1, '2025-10-27 23:25:02', 1),
(1307, 43, 2, 143, 436, NULL, NULL, 0, '2025-10-27 23:25:02', 1),
(1308, 43, 2, 130, 393, NULL, NULL, 1, '2025-10-27 23:25:02', 1),
(1309, 43, 2, 141, 432, NULL, NULL, 1, '2025-10-27 23:25:02', 1),
(1310, 43, 2, 126, 380, NULL, NULL, 1, '2025-10-27 23:25:02', 1),
(1311, 43, 2, 145, 442, NULL, NULL, 1, '2025-10-27 23:25:02', 1),
(1312, 43, 2, 124, 372, NULL, NULL, 0, '2025-10-27 23:25:02', 1),
(1313, 43, 2, 146, 443, NULL, NULL, 0, '2025-10-27 23:25:02', 1),
(1314, 43, 2, 138, 422, NULL, NULL, 0, '2025-10-27 23:25:02', 1),
(1315, 43, 2, 135, 411, NULL, NULL, 0, '2025-10-27 23:25:02', 1),
(1316, 43, 2, 140, 428, NULL, NULL, 0, '2025-10-27 23:25:02', 1),
(1317, 43, 2, 132, 401, NULL, NULL, 1, '2025-10-27 23:25:02', 1),
(1318, 43, 2, 129, 391, NULL, NULL, 0, '2025-10-27 23:25:02', 1),
(1319, 43, 2, 123, 370, NULL, NULL, 1, '2025-10-27 23:25:02', 1),
(1320, 43, 2, 144, 440, NULL, NULL, 1, '2025-10-27 23:25:02', 1),
(1321, 66, 2, 126, 380, NULL, NULL, 1, '2025-10-27 23:25:11', 1),
(1322, 66, 2, 127, 385, NULL, NULL, 1, '2025-10-27 23:25:11', 1),
(1323, 66, 2, 125, 377, NULL, NULL, 1, '2025-10-27 23:25:11', 1),
(1324, 66, 2, 124, 372, NULL, NULL, 0, '2025-10-27 23:25:11', 1),
(1325, 66, 2, 144, 438, NULL, NULL, 0, '2025-10-27 23:25:11', 1),
(1326, 66, 2, 128, 387, NULL, NULL, 1, '2025-10-27 23:25:11', 1),
(1327, 66, 2, 142, 435, NULL, NULL, 0, '2025-10-27 23:25:11', 1),
(1328, 66, 2, 141, 432, NULL, NULL, 1, '2025-10-27 23:25:11', 1),
(1329, 66, 2, 140, 428, NULL, NULL, 0, '2025-10-27 23:25:11', 1),
(1330, 66, 2, 129, 390, NULL, NULL, 0, '2025-10-27 23:25:11', 1),
(1331, 66, 2, 134, 409, NULL, NULL, 0, '2025-10-27 23:25:11', 1),
(1332, 66, 2, 143, 437, NULL, NULL, 1, '2025-10-27 23:25:11', 1),
(1333, 66, 2, 130, 392, NULL, NULL, 0, '2025-10-27 23:25:11', 1),
(1334, 66, 2, 135, 413, NULL, NULL, 1, '2025-10-27 23:25:11', 1),
(1335, 66, 2, 147, 450, NULL, NULL, 0, '2025-10-27 23:25:11', 1),
(1336, 66, 2, 145, 442, NULL, NULL, 1, '2025-10-27 23:25:11', 1),
(1337, 66, 2, 139, 425, NULL, NULL, 0, '2025-10-27 23:25:11', 1),
(1338, 66, 2, 137, 418, NULL, NULL, 1, '2025-10-27 23:25:11', 1),
(1339, 66, 2, 133, 403, NULL, NULL, 1, '2025-10-27 23:25:11', 1),
(1340, 66, 2, 131, 395, NULL, NULL, 0, '2025-10-27 23:25:11', 1),
(1341, 59, 5, 155, 474, NULL, NULL, 0, '2025-10-27 23:25:26', 1),
(1342, 59, 5, 158, 481, NULL, NULL, 1, '2025-10-27 23:25:26', 1),
(1343, 59, 5, 169, 515, NULL, NULL, 1, '2025-10-27 23:25:26', 1),
(1344, 59, 5, 150, 456, NULL, NULL, 1, '2025-10-27 23:25:26', 1),
(1345, 59, 5, 164, 498, NULL, NULL, 1, '2025-10-27 23:25:26', 1),
(1346, 59, 5, 152, 462, NULL, NULL, 0, '2025-10-27 23:25:26', 1),
(1347, 59, 5, 153, 466, NULL, NULL, 0, '2025-10-27 23:25:26', 1),
(1348, 59, 5, 159, 483, NULL, NULL, 1, '2025-10-27 23:25:26', 1),
(1349, 59, 5, 156, 476, NULL, NULL, 1, '2025-10-27 23:25:26', 1),
(1350, 59, 5, 168, 512, NULL, NULL, 1, '2025-10-27 23:25:26', 1),
(1351, 59, 5, 148, 451, NULL, NULL, 1, '2025-10-27 23:25:26', 1),
(1352, 59, 5, 160, 486, NULL, NULL, 0, '2025-10-27 23:25:26', 1),
(1353, 59, 5, 162, 490, NULL, NULL, 0, '2025-10-27 23:25:26', 1),
(1354, 59, 5, 154, 468, NULL, NULL, 0, '2025-10-27 23:25:26', 1),
(1355, 59, 5, 157, 479, NULL, NULL, 1, '2025-10-27 23:25:26', 1),
(1356, 59, 5, 161, 487, NULL, NULL, 1, '2025-10-27 23:25:26', 1),
(1357, 59, 5, 167, 508, NULL, NULL, 0, '2025-10-27 23:25:26', 1),
(1358, 59, 5, 151, 458, NULL, NULL, 0, '2025-10-27 23:25:26', 1),
(1359, 59, 5, 165, 499, NULL, NULL, 1, '2025-10-27 23:25:26', 1),
(1360, 59, 5, 163, 494, NULL, NULL, 1, '2025-10-27 23:25:26', 1),
(1361, 71, 2, 147, 447, NULL, NULL, 0, '2025-10-27 23:25:51', 1),
(1362, 71, 2, 127, 385, NULL, NULL, 1, '2025-10-27 23:25:51', 1),
(1363, 71, 2, 140, 429, NULL, NULL, 1, '2025-10-27 23:25:51', 1),
(1364, 71, 2, 131, 397, NULL, NULL, 1, '2025-10-27 23:25:51', 1),
(1365, 71, 2, 142, 433, NULL, NULL, 1, '2025-10-27 23:25:51', 1),
(1366, 71, 2, 130, 393, NULL, NULL, 1, '2025-10-27 23:25:51', 1),
(1367, 71, 2, 132, 401, NULL, NULL, 1, '2025-10-27 23:25:51', 1),
(1368, 71, 2, 138, 421, NULL, NULL, 1, '2025-10-27 23:25:51', 1),
(1369, 71, 2, 143, 437, NULL, NULL, 1, '2025-10-27 23:25:51', 1),
(1370, 71, 2, 126, 380, NULL, NULL, 1, '2025-10-27 23:25:51', 1),
(1371, 71, 2, 137, 418, NULL, NULL, 1, '2025-10-27 23:25:51', 1),
(1372, 71, 2, 139, 424, NULL, NULL, 0, '2025-10-27 23:25:51', 1),
(1373, 71, 2, 136, 414, NULL, NULL, 0, '2025-10-27 23:25:51', 1),
(1374, 71, 2, 141, 432, NULL, NULL, 1, '2025-10-27 23:25:51', 1),
(1375, 71, 2, 146, 443, NULL, NULL, 0, '2025-10-27 23:25:51', 1),
(1376, 71, 2, 128, 387, NULL, NULL, 1, '2025-10-27 23:25:51', 1),
(1377, 71, 2, 129, 389, NULL, NULL, 1, '2025-10-27 23:25:51', 1),
(1378, 71, 2, 123, 369, NULL, NULL, 0, '2025-10-27 23:25:51', 1),
(1379, 71, 2, 135, 410, NULL, NULL, 0, '2025-10-27 23:25:51', 1),
(1380, 71, 2, 133, 403, NULL, NULL, 1, '2025-10-27 23:25:51', 1),
(1381, 51, 2, 124, 372, NULL, NULL, 0, '2025-10-27 23:26:04', 1),
(1382, 51, 2, 146, 443, NULL, NULL, 0, '2025-10-27 23:26:04', 1),
(1383, 51, 2, 137, 418, NULL, NULL, 1, '2025-10-27 23:26:04', 1),
(1384, 51, 2, 142, 433, NULL, NULL, 1, '2025-10-27 23:26:04', 1),
(1385, 51, 2, 138, 421, NULL, NULL, 1, '2025-10-27 23:26:04', 1),
(1386, 51, 2, 135, 413, NULL, NULL, 1, '2025-10-27 23:26:04', 1),
(1387, 51, 2, 145, 441, NULL, NULL, 0, '2025-10-27 23:26:04', 1),
(1388, 51, 2, 133, 403, NULL, NULL, 1, '2025-10-27 23:26:04', 1),
(1389, 51, 2, 129, 389, NULL, NULL, 1, '2025-10-27 23:26:04', 1),
(1390, 51, 2, 130, 393, NULL, NULL, 1, '2025-10-27 23:26:04', 1),
(1391, 51, 2, 134, 408, NULL, NULL, 1, '2025-10-27 23:26:04', 1),
(1392, 51, 2, 127, 385, NULL, NULL, 1, '2025-10-27 23:26:04', 1),
(1393, 51, 2, 123, 370, NULL, NULL, 1, '2025-10-27 23:26:04', 1),
(1394, 51, 2, 141, 432, NULL, NULL, 1, '2025-10-27 23:26:04', 1),
(1395, 51, 2, 147, 450, NULL, NULL, 0, '2025-10-27 23:26:04', 1),
(1396, 51, 2, 125, 376, NULL, NULL, 0, '2025-10-27 23:26:04', 1),
(1397, 51, 2, 139, 424, NULL, NULL, 0, '2025-10-27 23:26:04', 1),
(1398, 51, 2, 132, 401, NULL, NULL, 1, '2025-10-27 23:26:04', 1),
(1399, 51, 2, 136, 414, NULL, NULL, 0, '2025-10-27 23:26:04', 1),
(1400, 51, 2, 128, 387, NULL, NULL, 1, '2025-10-27 23:26:04', 1),
(1401, 65, 5, 169, 515, NULL, NULL, 1, '2025-10-27 23:26:08', 1),
(1402, 65, 5, 166, 504, NULL, NULL, 0, '2025-10-27 23:26:08', 1),
(1403, 65, 5, 160, 486, NULL, NULL, 0, '2025-10-27 23:26:08', 1),
(1404, 65, 5, 167, 508, NULL, NULL, 0, '2025-10-27 23:26:08', 1),
(1405, 65, 5, 161, 487, NULL, NULL, 1, '2025-10-27 23:26:08', 1),
(1406, 65, 5, 157, 480, NULL, NULL, 0, '2025-10-27 23:26:08', 1),
(1407, 65, 5, 153, 466, NULL, NULL, 0, '2025-10-27 23:26:08', 1),
(1408, 65, 5, 164, 498, NULL, NULL, 1, '2025-10-27 23:26:08', 1),
(1409, 65, 5, 158, 481, NULL, NULL, 1, '2025-10-27 23:26:08', 1),
(1410, 65, 5, 149, 453, NULL, NULL, 0, '2025-10-27 23:26:08', 1),
(1411, 65, 5, 168, 511, NULL, NULL, 0, '2025-10-27 23:26:08', 1),
(1412, 65, 5, 172, 521, NULL, NULL, 1, '2025-10-27 23:26:08', 1),
(1413, 65, 5, 163, 494, NULL, NULL, 1, '2025-10-27 23:26:08', 1),
(1414, 65, 5, 148, 452, NULL, NULL, 0, '2025-10-27 23:26:08', 1),
(1415, 65, 5, 156, 476, NULL, NULL, 1, '2025-10-27 23:26:08', 1),
(1416, 65, 5, 155, 474, NULL, NULL, 0, '2025-10-27 23:26:08', 1),
(1417, 65, 5, 151, 460, NULL, NULL, 1, '2025-10-27 23:26:08', 1),
(1418, 65, 5, 165, 500, NULL, NULL, 0, '2025-10-27 23:26:08', 1),
(1419, 65, 5, 159, 483, NULL, NULL, 1, '2025-10-27 23:26:08', 1),
(1420, 65, 5, 150, 457, NULL, NULL, 0, '2025-10-27 23:26:08', 1),
(1421, 62, 2, 129, 391, NULL, NULL, 0, '2025-10-27 23:26:13', 1),
(1422, 62, 2, 142, 433, NULL, NULL, 1, '2025-10-27 23:26:13', 1),
(1423, 62, 2, 135, 410, NULL, NULL, 0, '2025-10-27 23:26:13', 1),
(1424, 62, 2, 137, 418, NULL, NULL, 1, '2025-10-27 23:26:13', 1),
(1425, 62, 2, 132, 401, NULL, NULL, 1, '2025-10-27 23:26:13', 1),
(1426, 62, 2, 126, 380, NULL, NULL, 1, '2025-10-27 23:26:13', 1),
(1427, 62, 2, 136, 414, NULL, NULL, 0, '2025-10-27 23:26:13', 1),
(1428, 62, 2, 139, 425, NULL, NULL, 0, '2025-10-27 23:26:13', 1),
(1429, 62, 2, 143, 437, NULL, NULL, 1, '2025-10-27 23:26:13', 1),
(1430, 62, 2, 133, 403, NULL, NULL, 1, '2025-10-27 23:26:13', 1),
(1431, 62, 2, 127, 385, NULL, NULL, 1, '2025-10-27 23:26:13', 1),
(1432, 62, 2, 147, 448, NULL, NULL, 1, '2025-10-27 23:26:13', 1),
(1433, 62, 2, 144, 440, NULL, NULL, 1, '2025-10-27 23:26:13', 1),
(1434, 62, 2, 131, 395, NULL, NULL, 0, '2025-10-27 23:26:13', 1),
(1435, 62, 2, 124, 372, NULL, NULL, 0, '2025-10-27 23:26:13', 1),
(1436, 62, 2, 125, 377, NULL, NULL, 1, '2025-10-27 23:26:13', 1),
(1437, 62, 2, 141, 432, NULL, NULL, 1, '2025-10-27 23:26:13', 1),
(1438, 62, 2, 128, 387, NULL, NULL, 1, '2025-10-27 23:26:13', 1),
(1439, 62, 2, 134, 407, NULL, NULL, 0, '2025-10-27 23:26:13', 1),
(1440, 62, 2, 140, 429, NULL, NULL, 1, '2025-10-27 23:26:13', 1),
(1441, 55, 2, 137, 418, NULL, NULL, 1, '2025-10-27 23:26:57', 1),
(1442, 55, 2, 132, 401, NULL, NULL, 1, '2025-10-27 23:26:57', 1),
(1443, 55, 2, 135, 410, NULL, NULL, 0, '2025-10-27 23:26:57', 1),
(1444, 55, 2, 126, 380, NULL, NULL, 1, '2025-10-27 23:26:57', 1),
(1445, 55, 2, 142, 434, NULL, NULL, 0, '2025-10-27 23:26:57', 1),
(1446, 55, 2, 140, 428, NULL, NULL, 0, '2025-10-27 23:26:57', 1),
(1447, 55, 2, 143, 437, NULL, NULL, 1, '2025-10-27 23:26:57', 1),
(1448, 55, 2, 146, 446, NULL, NULL, 0, '2025-10-27 23:26:57', 1),
(1449, 55, 2, 124, 372, NULL, NULL, 0, '2025-10-27 23:26:57', 1),
(1450, 55, 2, 133, 403, NULL, NULL, 1, '2025-10-27 23:26:57', 1),
(1451, 55, 2, 134, 407, NULL, NULL, 0, '2025-10-27 23:26:57', 1),
(1452, 55, 2, 145, 441, NULL, NULL, 0, '2025-10-27 23:26:57', 1),
(1453, 55, 2, 123, 369, NULL, NULL, 0, '2025-10-27 23:26:57', 1),
(1454, 55, 2, 139, 424, NULL, NULL, 0, '2025-10-27 23:26:57', 1),
(1455, 55, 2, 136, 414, NULL, NULL, 0, '2025-10-27 23:26:57', 1),
(1456, 55, 2, 141, 430, NULL, NULL, 0, '2025-10-27 23:26:57', 1),
(1457, 55, 2, 144, 440, NULL, NULL, 1, '2025-10-27 23:26:57', 1),
(1458, 55, 2, 131, 396, NULL, NULL, 0, '2025-10-27 23:26:57', 1),
(1459, 55, 2, 129, 390, NULL, NULL, 0, '2025-10-27 23:26:57', 1),
(1460, 55, 2, 138, 423, NULL, NULL, 0, '2025-10-27 23:26:57', 1),
(1461, 42, 2, 147, 447, NULL, NULL, 0, '2025-10-27 23:27:04', 1),
(1462, 42, 2, 139, 424, NULL, NULL, 0, '2025-10-27 23:27:04', 1),
(1463, 42, 2, 133, 403, NULL, NULL, 1, '2025-10-27 23:27:04', 1),
(1464, 42, 2, 137, 417, NULL, NULL, 0, '2025-10-27 23:27:04', 1),
(1465, 42, 2, 124, 372, NULL, NULL, 0, '2025-10-27 23:27:04', 1),
(1466, 42, 2, 146, 444, NULL, NULL, 0, '2025-10-27 23:27:04', 1),
(1467, 42, 2, 125, 378, NULL, NULL, 0, '2025-10-27 23:27:04', 1),
(1468, 42, 2, 134, 406, NULL, NULL, 0, '2025-10-27 23:27:04', 1),
(1469, 42, 2, 144, 440, NULL, NULL, 1, '2025-10-27 23:27:04', 1),
(1470, 42, 2, 142, 434, NULL, NULL, 0, '2025-10-27 23:27:04', 1),
(1471, 42, 2, 132, 399, NULL, NULL, 0, '2025-10-27 23:27:04', 1),
(1472, 42, 2, 129, 391, NULL, NULL, 0, '2025-10-27 23:27:04', 1),
(1473, 42, 2, 126, 380, NULL, NULL, 1, '2025-10-27 23:27:04', 1),
(1474, 42, 2, 138, 422, NULL, NULL, 0, '2025-10-27 23:27:04', 1),
(1475, 42, 2, 123, 369, NULL, NULL, 0, '2025-10-27 23:27:04', 1),
(1476, 42, 2, 130, 394, NULL, NULL, 0, '2025-10-27 23:27:04', 1),
(1477, 42, 2, 131, 395, NULL, NULL, 0, '2025-10-27 23:27:04', 1),
(1478, 42, 2, 127, 385, NULL, NULL, 1, '2025-10-27 23:27:04', 1),
(1479, 42, 2, 145, 441, NULL, NULL, 0, '2025-10-27 23:27:04', 1),
(1480, 42, 2, 143, 436, NULL, NULL, 0, '2025-10-27 23:27:04', 1),
(1481, 49, 2, 144, 439, NULL, NULL, 0, '2025-10-27 23:27:04', 1),
(1482, 49, 2, 136, 415, NULL, NULL, 0, '2025-10-27 23:27:04', 1),
(1483, 49, 2, 124, 373, NULL, NULL, 0, '2025-10-27 23:27:04', 1),
(1484, 49, 2, 134, 407, NULL, NULL, 0, '2025-10-27 23:27:04', 1),
(1485, 49, 2, 130, 393, NULL, NULL, 1, '2025-10-27 23:27:04', 1),
(1486, 49, 2, 128, 387, NULL, NULL, 1, '2025-10-27 23:27:04', 1),
(1487, 49, 2, 142, 434, NULL, NULL, 0, '2025-10-27 23:27:04', 1),
(1488, 49, 2, 135, 413, NULL, NULL, 1, '2025-10-27 23:27:04', 1),
(1489, 49, 2, 145, 441, NULL, NULL, 0, '2025-10-27 23:27:04', 1),
(1490, 49, 2, 132, 399, NULL, NULL, 0, '2025-10-27 23:27:04', 1),
(1491, 49, 2, 133, 402, NULL, NULL, 0, '2025-10-27 23:27:04', 1),
(1492, 49, 2, 140, 429, NULL, NULL, 1, '2025-10-27 23:27:04', 1),
(1493, 49, 2, 143, 437, NULL, NULL, 1, '2025-10-27 23:27:04', 1),
(1494, 49, 2, 141, 432, NULL, NULL, 1, '2025-10-27 23:27:04', 1),
(1495, 49, 2, 137, 420, NULL, NULL, 0, '2025-10-27 23:27:04', 1),
(1496, 49, 2, 125, 377, NULL, NULL, 1, '2025-10-27 23:27:04', 1),
(1497, 49, 2, 138, 422, NULL, NULL, 0, '2025-10-27 23:27:04', 1),
(1498, 49, 2, 139, 426, NULL, NULL, 1, '2025-10-27 23:27:04', 1),
(1499, 49, 2, 147, 448, NULL, NULL, 1, '2025-10-27 23:27:04', 1),
(1500, 49, 2, 146, 443, NULL, NULL, 0, '2025-10-27 23:27:04', 1),
(1501, 63, 2, 124, 373, NULL, NULL, 0, '2025-10-27 23:28:06', 1),
(1502, 63, 2, 133, 403, NULL, NULL, 1, '2025-10-27 23:28:06', 1),
(1503, 63, 2, 128, 387, NULL, NULL, 1, '2025-10-27 23:28:06', 1),
(1504, 63, 2, 130, 392, NULL, NULL, 0, '2025-10-27 23:28:06', 1),
(1505, 63, 2, 143, 437, NULL, NULL, 1, '2025-10-27 23:28:06', 1),
(1506, 63, 2, 123, 371, NULL, NULL, 0, '2025-10-27 23:28:06', 1),
(1507, 63, 2, 140, 428, NULL, NULL, 0, '2025-10-27 23:28:06', 1),
(1508, 63, 2, 126, 382, NULL, NULL, 0, '2025-10-27 23:28:06', 1),
(1509, 63, 2, 129, 391, NULL, NULL, 0, '2025-10-27 23:28:06', 1),
(1510, 63, 2, 136, 414, NULL, NULL, 0, '2025-10-27 23:28:06', 1),
(1511, 63, 2, 138, 422, NULL, NULL, 0, '2025-10-27 23:28:06', 1),
(1512, 63, 2, 144, 440, NULL, NULL, 1, '2025-10-27 23:28:06', 1),
(1513, 63, 2, 139, 424, NULL, NULL, 0, '2025-10-27 23:28:06', 1),
(1514, 63, 2, 137, 419, NULL, NULL, 0, '2025-10-27 23:28:06', 1),
(1515, 63, 2, 145, 442, NULL, NULL, 1, '2025-10-27 23:28:06', 1),
(1516, 63, 2, 147, 448, NULL, NULL, 1, '2025-10-27 23:28:06', 1),
(1517, 63, 2, 146, 446, NULL, NULL, 0, '2025-10-27 23:28:06', 1),
(1518, 63, 2, 141, 431, NULL, NULL, 0, '2025-10-27 23:28:06', 1),
(1519, 63, 2, 135, 412, NULL, NULL, 0, '2025-10-27 23:28:06', 1),
(1520, 63, 2, 127, 385, NULL, NULL, 1, '2025-10-27 23:28:06', 1),
(1521, 47, 2, 146, 444, NULL, NULL, 0, '2025-10-27 23:28:18', 1),
(1522, 47, 2, 127, 385, NULL, NULL, 1, '2025-10-27 23:28:18', 1),
(1523, 47, 2, 136, 414, NULL, NULL, 0, '2025-10-27 23:28:18', 1),
(1524, 47, 2, 135, 410, NULL, NULL, 0, '2025-10-27 23:28:18', 1),
(1525, 47, 2, 139, 425, NULL, NULL, 0, '2025-10-27 23:28:18', 1),
(1526, 47, 2, 137, 418, NULL, NULL, 1, '2025-10-27 23:28:18', 1),
(1527, 47, 2, 143, 437, NULL, NULL, 1, '2025-10-27 23:28:18', 1),
(1528, 47, 2, 147, 449, NULL, NULL, 0, '2025-10-27 23:28:18', 1),
(1529, 47, 2, 124, 373, NULL, NULL, 0, '2025-10-27 23:28:18', 1),
(1530, 47, 2, 126, 380, NULL, NULL, 1, '2025-10-27 23:28:18', 1),
(1531, 47, 2, 145, 441, NULL, NULL, 0, '2025-10-27 23:28:18', 1),
(1532, 47, 2, 131, 396, NULL, NULL, 0, '2025-10-27 23:28:18', 1),
(1533, 47, 2, 134, 409, NULL, NULL, 0, '2025-10-27 23:28:18', 1),
(1534, 47, 2, 141, 431, NULL, NULL, 0, '2025-10-27 23:28:18', 1),
(1535, 47, 2, 125, 377, NULL, NULL, 1, '2025-10-27 23:28:18', 1),
(1536, 47, 2, 129, 389, NULL, NULL, 1, '2025-10-27 23:28:18', 1),
(1537, 47, 2, 132, 401, NULL, NULL, 1, '2025-10-27 23:28:18', 1),
(1538, 47, 2, 130, 394, NULL, NULL, 0, '2025-10-27 23:28:18', 1),
(1539, 47, 2, 123, 369, NULL, NULL, 0, '2025-10-27 23:28:18', 1),
(1540, 47, 2, 138, 423, NULL, NULL, 0, '2025-10-27 23:28:18', 1),
(1541, 70, 5, 159, 483, NULL, NULL, 1, '2025-10-27 23:28:26', 1),
(1542, 70, 5, 161, 487, NULL, NULL, 1, '2025-10-27 23:28:26', 1),
(1543, 70, 5, 152, 462, NULL, NULL, 0, '2025-10-27 23:28:26', 1),
(1544, 70, 5, 160, 485, NULL, NULL, 1, '2025-10-27 23:28:26', 1),
(1545, 70, 5, 163, 493, NULL, NULL, 0, '2025-10-27 23:28:26', 1),
(1546, 70, 5, 156, 476, NULL, NULL, 1, '2025-10-27 23:28:26', 1),
(1547, 70, 5, 164, 496, NULL, NULL, 0, '2025-10-27 23:28:26', 1),
(1548, 70, 5, 150, 455, NULL, NULL, 0, '2025-10-27 23:28:26', 1),
(1549, 70, 5, 167, 506, NULL, NULL, 0, '2025-10-27 23:28:26', 1),
(1550, 70, 5, 149, 453, NULL, NULL, 0, '2025-10-27 23:28:26', 1),
(1551, 70, 5, 154, 468, NULL, NULL, 0, '2025-10-27 23:28:26', 1),
(1552, 70, 5, 148, 451, NULL, NULL, 1, '2025-10-27 23:28:26', 1),
(1553, 70, 5, 169, 513, NULL, NULL, 0, '2025-10-27 23:28:26', 1),
(1554, 70, 5, 158, 481, NULL, NULL, 1, '2025-10-27 23:28:26', 1),
(1555, 70, 5, 151, 458, NULL, NULL, 0, '2025-10-27 23:28:26', 1),
(1556, 70, 5, 157, 479, NULL, NULL, 1, '2025-10-27 23:28:26', 1),
(1557, 70, 5, 162, 492, NULL, NULL, 0, '2025-10-27 23:28:26', 1),
(1558, 70, 5, 153, 465, NULL, NULL, 0, '2025-10-27 23:28:26', 1),
(1559, 70, 5, 172, 520, NULL, NULL, 0, '2025-10-27 23:28:26', 1),
(1560, 70, 5, 165, 499, NULL, NULL, 1, '2025-10-27 23:28:26', 1),
(1561, 50, 2, 131, 396, NULL, NULL, 0, '2025-10-27 23:28:35', 1),
(1562, 50, 2, 126, 380, NULL, NULL, 1, '2025-10-27 23:28:35', 1),
(1563, 50, 2, 141, 432, NULL, NULL, 1, '2025-10-27 23:28:35', 1),
(1564, 50, 2, 145, 441, NULL, NULL, 0, '2025-10-27 23:28:35', 1),
(1565, 50, 2, 124, 374, NULL, NULL, 1, '2025-10-27 23:28:35', 1),
(1566, 50, 2, 139, 424, NULL, NULL, 0, '2025-10-27 23:28:35', 1),
(1567, 50, 2, 130, 393, NULL, NULL, 1, '2025-10-27 23:28:35', 1),
(1568, 50, 2, 125, 378, NULL, NULL, 0, '2025-10-27 23:28:35', 1),
(1569, 50, 2, 127, 385, NULL, NULL, 1, '2025-10-27 23:28:35', 1),
(1570, 50, 2, 133, 403, NULL, NULL, 1, '2025-10-27 23:28:35', 1),
(1571, 50, 2, 138, 421, NULL, NULL, 1, '2025-10-27 23:28:35', 1),
(1572, 50, 2, 123, 370, NULL, NULL, 1, '2025-10-27 23:28:35', 1),
(1573, 50, 2, 132, 401, NULL, NULL, 1, '2025-10-27 23:28:35', 1),
(1574, 50, 2, 140, 428, NULL, NULL, 0, '2025-10-27 23:28:35', 1),
(1575, 50, 2, 144, 440, NULL, NULL, 1, '2025-10-27 23:28:35', 1),
(1576, 50, 2, 143, 437, NULL, NULL, 1, '2025-10-27 23:28:35', 1),
(1577, 50, 2, 135, 410, NULL, NULL, 0, '2025-10-27 23:28:35', 1),
(1578, 50, 2, 129, 390, NULL, NULL, 0, '2025-10-27 23:28:35', 1),
(1579, 50, 2, 136, 416, NULL, NULL, 1, '2025-10-27 23:28:35', 1),
(1580, 50, 2, 137, 418, NULL, NULL, 1, '2025-10-27 23:28:35', 1),
(1581, 54, 2, 137, 418, NULL, NULL, 1, '2025-10-27 23:28:45', 1);
INSERT INTO `respuestas_alumnos` (`id`, `alumno_user_id`, `encuesta_id`, `pregunta_id`, `respuesta_id`, `respuesta_texto`, `respuesta_numero`, `es_correcta`, `respondido_at`, `activo`) VALUES
(1582, 54, 2, 142, 435, NULL, NULL, 0, '2025-10-27 23:28:45', 1),
(1583, 54, 2, 146, 443, NULL, NULL, 0, '2025-10-27 23:28:45', 1),
(1584, 54, 2, 135, 410, NULL, NULL, 0, '2025-10-27 23:28:45', 1),
(1585, 54, 2, 134, 407, NULL, NULL, 0, '2025-10-27 23:28:45', 1),
(1586, 54, 2, 125, 376, NULL, NULL, 0, '2025-10-27 23:28:45', 1),
(1587, 54, 2, 127, 385, NULL, NULL, 1, '2025-10-27 23:28:45', 1),
(1588, 54, 2, 136, 414, NULL, NULL, 0, '2025-10-27 23:28:45', 1),
(1589, 54, 2, 138, 422, NULL, NULL, 0, '2025-10-27 23:28:45', 1),
(1590, 54, 2, 129, 390, NULL, NULL, 0, '2025-10-27 23:28:45', 1),
(1591, 54, 2, 131, 395, NULL, NULL, 0, '2025-10-27 23:28:45', 1),
(1592, 54, 2, 132, 401, NULL, NULL, 1, '2025-10-27 23:28:45', 1),
(1593, 54, 2, 123, 369, NULL, NULL, 0, '2025-10-27 23:28:45', 1),
(1594, 54, 2, 128, 387, NULL, NULL, 1, '2025-10-27 23:28:45', 1),
(1595, 54, 2, 130, 394, NULL, NULL, 0, '2025-10-27 23:28:45', 1),
(1596, 54, 2, 141, 431, NULL, NULL, 0, '2025-10-27 23:28:45', 1),
(1597, 54, 2, 145, 442, NULL, NULL, 1, '2025-10-27 23:28:45', 1),
(1598, 54, 2, 144, 438, NULL, NULL, 0, '2025-10-27 23:28:45', 1),
(1599, 54, 2, 139, 424, NULL, NULL, 0, '2025-10-27 23:28:45', 1),
(1600, 54, 2, 124, 373, NULL, NULL, 0, '2025-10-27 23:28:45', 1),
(1601, 58, 2, 132, 399, NULL, NULL, 0, '2025-10-27 23:29:16', 1),
(1602, 58, 2, 124, 373, NULL, NULL, 0, '2025-10-27 23:29:16', 1),
(1603, 58, 2, 144, 440, NULL, NULL, 1, '2025-10-27 23:29:16', 1),
(1604, 58, 2, 141, 430, NULL, NULL, 0, '2025-10-27 23:29:16', 1),
(1605, 58, 2, 123, 371, NULL, NULL, 0, '2025-10-27 23:29:16', 1),
(1606, 58, 2, 128, 386, NULL, NULL, 0, '2025-10-27 23:29:16', 1),
(1607, 58, 2, 147, 449, NULL, NULL, 0, '2025-10-27 23:29:16', 1),
(1608, 58, 2, 139, 426, NULL, NULL, 1, '2025-10-27 23:29:16', 1),
(1609, 58, 2, 126, 380, NULL, NULL, 1, '2025-10-27 23:29:16', 1),
(1610, 58, 2, 146, 446, NULL, NULL, 0, '2025-10-27 23:29:16', 1),
(1611, 58, 2, 143, 437, NULL, NULL, 1, '2025-10-27 23:29:16', 1),
(1612, 58, 2, 130, 393, NULL, NULL, 1, '2025-10-27 23:29:16', 1),
(1613, 58, 2, 131, 397, NULL, NULL, 1, '2025-10-27 23:29:16', 1),
(1614, 58, 2, 140, 428, NULL, NULL, 0, '2025-10-27 23:29:16', 1),
(1615, 58, 2, 129, 389, NULL, NULL, 1, '2025-10-27 23:29:16', 1),
(1616, 58, 2, 134, 408, NULL, NULL, 1, '2025-10-27 23:29:16', 1),
(1617, 58, 2, 136, 416, NULL, NULL, 1, '2025-10-27 23:29:16', 1),
(1618, 58, 2, 133, 403, NULL, NULL, 1, '2025-10-27 23:29:16', 1),
(1619, 58, 2, 142, 434, NULL, NULL, 0, '2025-10-27 23:29:16', 1),
(1620, 58, 2, 137, 418, NULL, NULL, 1, '2025-10-27 23:29:16', 1),
(1621, 60, 2, 128, 387, NULL, NULL, 1, '2025-10-27 23:30:07', 1),
(1622, 60, 2, 144, 440, NULL, NULL, 1, '2025-10-27 23:30:07', 1),
(1623, 60, 2, 123, 369, NULL, NULL, 0, '2025-10-27 23:30:07', 1),
(1624, 60, 2, 140, 428, NULL, NULL, 0, '2025-10-27 23:30:07', 1),
(1625, 60, 2, 136, 416, NULL, NULL, 1, '2025-10-27 23:30:07', 1),
(1626, 60, 2, 138, 423, NULL, NULL, 0, '2025-10-27 23:30:07', 1),
(1627, 60, 2, 125, 377, NULL, NULL, 1, '2025-10-27 23:30:07', 1),
(1628, 60, 2, 126, 380, NULL, NULL, 1, '2025-10-27 23:30:07', 1),
(1629, 60, 2, 129, 390, NULL, NULL, 0, '2025-10-27 23:30:07', 1),
(1630, 60, 2, 141, 432, NULL, NULL, 1, '2025-10-27 23:30:07', 1),
(1631, 60, 2, 142, 434, NULL, NULL, 0, '2025-10-27 23:30:07', 1),
(1632, 60, 2, 139, 424, NULL, NULL, 0, '2025-10-27 23:30:07', 1),
(1633, 60, 2, 146, 444, NULL, NULL, 0, '2025-10-27 23:30:07', 1),
(1634, 60, 2, 134, 409, NULL, NULL, 0, '2025-10-27 23:30:07', 1),
(1635, 60, 2, 127, 385, NULL, NULL, 1, '2025-10-27 23:30:07', 1),
(1636, 60, 2, 130, 392, NULL, NULL, 0, '2025-10-27 23:30:07', 1),
(1637, 60, 2, 145, 441, NULL, NULL, 0, '2025-10-27 23:30:07', 1),
(1638, 60, 2, 124, 372, NULL, NULL, 0, '2025-10-27 23:30:07', 1),
(1639, 60, 2, 135, 411, NULL, NULL, 0, '2025-10-27 23:30:07', 1),
(1640, 60, 2, 143, 437, NULL, NULL, 1, '2025-10-27 23:30:07', 1),
(1641, 41, 2, 128, 387, NULL, NULL, 1, '2025-10-27 23:31:00', 1),
(1642, 41, 2, 143, 437, NULL, NULL, 1, '2025-10-27 23:31:00', 1),
(1643, 41, 2, 134, 409, NULL, NULL, 0, '2025-10-27 23:31:00', 1),
(1644, 41, 2, 147, 449, NULL, NULL, 0, '2025-10-27 23:31:00', 1),
(1645, 41, 2, 135, 413, NULL, NULL, 1, '2025-10-27 23:31:00', 1),
(1646, 41, 2, 126, 380, NULL, NULL, 1, '2025-10-27 23:31:00', 1),
(1647, 41, 2, 140, 429, NULL, NULL, 1, '2025-10-27 23:31:00', 1),
(1648, 41, 2, 130, 392, NULL, NULL, 0, '2025-10-27 23:31:00', 1),
(1649, 41, 2, 136, 414, NULL, NULL, 0, '2025-10-27 23:31:00', 1),
(1650, 41, 2, 141, 430, NULL, NULL, 0, '2025-10-27 23:31:00', 1),
(1651, 41, 2, 146, 444, NULL, NULL, 0, '2025-10-27 23:31:00', 1),
(1652, 41, 2, 133, 403, NULL, NULL, 1, '2025-10-27 23:31:00', 1),
(1653, 41, 2, 142, 433, NULL, NULL, 1, '2025-10-27 23:31:00', 1),
(1654, 41, 2, 123, 369, NULL, NULL, 0, '2025-10-27 23:31:00', 1),
(1655, 41, 2, 137, 418, NULL, NULL, 1, '2025-10-27 23:31:00', 1),
(1656, 41, 2, 145, 441, NULL, NULL, 0, '2025-10-27 23:31:00', 1),
(1657, 41, 2, 131, 395, NULL, NULL, 0, '2025-10-27 23:31:00', 1),
(1658, 41, 2, 132, 401, NULL, NULL, 1, '2025-10-27 23:31:00', 1),
(1659, 41, 2, 127, 385, NULL, NULL, 1, '2025-10-27 23:31:00', 1),
(1660, 41, 2, 138, 423, NULL, NULL, 0, '2025-10-27 23:31:00', 1),
(1661, 94, 2, 124, 374, NULL, NULL, 1, '2025-10-27 23:31:37', 1),
(1662, 94, 2, 144, 440, NULL, NULL, 1, '2025-10-27 23:31:37', 1),
(1663, 94, 2, 135, 410, NULL, NULL, 0, '2025-10-27 23:31:37', 1),
(1664, 94, 2, 141, 432, NULL, NULL, 1, '2025-10-27 23:31:37', 1),
(1665, 94, 2, 136, 416, NULL, NULL, 1, '2025-10-27 23:31:37', 1),
(1666, 94, 2, 142, 433, NULL, NULL, 1, '2025-10-27 23:31:37', 1),
(1667, 94, 2, 130, 393, NULL, NULL, 1, '2025-10-27 23:31:37', 1),
(1668, 94, 2, 138, 421, NULL, NULL, 1, '2025-10-27 23:31:37', 1),
(1669, 94, 2, 127, 385, NULL, NULL, 1, '2025-10-27 23:31:37', 1),
(1670, 94, 2, 129, 389, NULL, NULL, 1, '2025-10-27 23:31:37', 1),
(1671, 94, 2, 146, 444, NULL, NULL, 0, '2025-10-27 23:31:37', 1),
(1672, 94, 2, 131, 396, NULL, NULL, 0, '2025-10-27 23:31:37', 1),
(1673, 94, 2, 126, 380, NULL, NULL, 1, '2025-10-27 23:31:37', 1),
(1674, 94, 2, 139, 426, NULL, NULL, 1, '2025-10-27 23:31:37', 1),
(1675, 94, 2, 147, 449, NULL, NULL, 0, '2025-10-27 23:31:37', 1),
(1676, 94, 2, 140, 429, NULL, NULL, 1, '2025-10-27 23:31:37', 1),
(1677, 94, 2, 143, 437, NULL, NULL, 1, '2025-10-27 23:31:37', 1),
(1678, 94, 2, 137, 418, NULL, NULL, 1, '2025-10-27 23:31:37', 1),
(1679, 94, 2, 123, 369, NULL, NULL, 0, '2025-10-27 23:31:37', 1),
(1680, 94, 2, 134, 406, NULL, NULL, 0, '2025-10-27 23:31:37', 1),
(1681, 65, 2, 127, 383, NULL, NULL, 0, '2025-10-27 23:33:01', 1),
(1682, 65, 2, 137, 418, NULL, NULL, 1, '2025-10-27 23:33:01', 1),
(1683, 65, 2, 139, 426, NULL, NULL, 1, '2025-10-27 23:33:01', 1),
(1684, 65, 2, 124, 372, NULL, NULL, 0, '2025-10-27 23:33:01', 1),
(1685, 65, 2, 125, 376, NULL, NULL, 0, '2025-10-27 23:33:01', 1),
(1686, 65, 2, 123, 371, NULL, NULL, 0, '2025-10-27 23:33:01', 1),
(1687, 65, 2, 130, 393, NULL, NULL, 1, '2025-10-27 23:33:01', 1),
(1688, 65, 2, 126, 380, NULL, NULL, 1, '2025-10-27 23:33:01', 1),
(1689, 65, 2, 134, 409, NULL, NULL, 0, '2025-10-27 23:33:01', 1),
(1690, 65, 2, 128, 386, NULL, NULL, 0, '2025-10-27 23:33:01', 1),
(1691, 65, 2, 147, 447, NULL, NULL, 0, '2025-10-27 23:33:01', 1),
(1692, 65, 2, 131, 396, NULL, NULL, 0, '2025-10-27 23:33:01', 1),
(1693, 65, 2, 141, 432, NULL, NULL, 1, '2025-10-27 23:33:01', 1),
(1694, 65, 2, 132, 401, NULL, NULL, 1, '2025-10-27 23:33:01', 1),
(1695, 65, 2, 144, 440, NULL, NULL, 1, '2025-10-27 23:33:01', 1),
(1696, 65, 2, 146, 443, NULL, NULL, 0, '2025-10-27 23:33:01', 1),
(1697, 65, 2, 133, 403, NULL, NULL, 1, '2025-10-27 23:33:01', 1),
(1698, 65, 2, 142, 433, NULL, NULL, 1, '2025-10-27 23:33:01', 1),
(1699, 65, 2, 143, 437, NULL, NULL, 1, '2025-10-27 23:33:01', 1),
(1700, 65, 2, 135, 410, NULL, NULL, 0, '2025-10-27 23:33:01', 1),
(1701, 53, 2, 133, 404, NULL, NULL, 0, '2025-10-27 23:33:02', 1),
(1702, 53, 2, 136, 414, NULL, NULL, 0, '2025-10-27 23:33:02', 1),
(1703, 53, 2, 137, 417, NULL, NULL, 0, '2025-10-27 23:33:02', 1),
(1704, 53, 2, 141, 430, NULL, NULL, 0, '2025-10-27 23:33:02', 1),
(1705, 53, 2, 130, 394, NULL, NULL, 0, '2025-10-27 23:33:02', 1),
(1706, 53, 2, 147, 447, NULL, NULL, 0, '2025-10-27 23:33:02', 1),
(1707, 53, 2, 126, 379, NULL, NULL, 0, '2025-10-27 23:33:02', 1),
(1708, 53, 2, 123, 371, NULL, NULL, 0, '2025-10-27 23:33:02', 1),
(1709, 53, 2, 125, 378, NULL, NULL, 0, '2025-10-27 23:33:02', 1),
(1710, 53, 2, 138, 421, NULL, NULL, 1, '2025-10-27 23:33:02', 1),
(1711, 53, 2, 134, 407, NULL, NULL, 0, '2025-10-27 23:33:02', 1),
(1712, 53, 2, 132, 401, NULL, NULL, 1, '2025-10-27 23:33:02', 1),
(1713, 53, 2, 135, 410, NULL, NULL, 0, '2025-10-27 23:33:02', 1),
(1714, 53, 2, 146, 443, NULL, NULL, 0, '2025-10-27 23:33:02', 1),
(1715, 53, 2, 143, 436, NULL, NULL, 0, '2025-10-27 23:33:02', 1),
(1716, 53, 2, 127, 385, NULL, NULL, 1, '2025-10-27 23:33:02', 1),
(1717, 53, 2, 139, 424, NULL, NULL, 0, '2025-10-27 23:33:02', 1),
(1718, 53, 2, 142, 433, NULL, NULL, 1, '2025-10-27 23:33:02', 1),
(1719, 53, 2, 129, 389, NULL, NULL, 1, '2025-10-27 23:33:02', 1),
(1720, 53, 2, 140, 428, NULL, NULL, 0, '2025-10-27 23:33:02', 1),
(1721, 59, 2, 131, 397, NULL, NULL, 1, '2025-10-27 23:33:19', 1),
(1722, 59, 2, 127, 385, NULL, NULL, 1, '2025-10-27 23:33:19', 1),
(1723, 59, 2, 139, 426, NULL, NULL, 1, '2025-10-27 23:33:19', 1),
(1724, 59, 2, 123, 370, NULL, NULL, 1, '2025-10-27 23:33:19', 1),
(1725, 59, 2, 132, 401, NULL, NULL, 1, '2025-10-27 23:33:19', 1),
(1726, 59, 2, 134, 407, NULL, NULL, 0, '2025-10-27 23:33:19', 1),
(1727, 59, 2, 147, 448, NULL, NULL, 1, '2025-10-27 23:33:19', 1),
(1728, 59, 2, 135, 410, NULL, NULL, 0, '2025-10-27 23:33:19', 1),
(1729, 59, 2, 137, 419, NULL, NULL, 0, '2025-10-27 23:33:19', 1),
(1730, 59, 2, 128, 387, NULL, NULL, 1, '2025-10-27 23:33:19', 1),
(1731, 59, 2, 125, 378, NULL, NULL, 0, '2025-10-27 23:33:19', 1),
(1732, 59, 2, 140, 429, NULL, NULL, 1, '2025-10-27 23:33:19', 1),
(1733, 59, 2, 143, 437, NULL, NULL, 1, '2025-10-27 23:33:19', 1),
(1734, 59, 2, 141, 432, NULL, NULL, 1, '2025-10-27 23:33:19', 1),
(1735, 59, 2, 136, 414, NULL, NULL, 0, '2025-10-27 23:33:19', 1),
(1736, 59, 2, 142, 433, NULL, NULL, 1, '2025-10-27 23:33:19', 1),
(1737, 59, 2, 144, 438, NULL, NULL, 0, '2025-10-27 23:33:19', 1),
(1738, 59, 2, 126, 380, NULL, NULL, 1, '2025-10-27 23:33:19', 1),
(1739, 59, 2, 130, 392, NULL, NULL, 0, '2025-10-27 23:33:19', 1),
(1740, 59, 2, 146, 443, NULL, NULL, 0, '2025-10-27 23:33:19', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seccion`
--

CREATE TABLE `seccion` (
  `id` int(11) NOT NULL,
  `Nombre` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `seccion`
--

INSERT INTO `seccion` (`id`, `Nombre`) VALUES
(1, 'A'),
(2, 'B'),
(3, 'C'),
(4, 'D'),
(5, 'TI');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` bigint(20) NOT NULL,
  `password_hash` varchar(512) NOT NULL,
  `rol` enum('ADMIN','DIRECTOR','DOCENTE','ALUMNO') NOT NULL,
  `nombres` varchar(120) NOT NULL,
  `apellidos` varchar(120) NOT NULL,
  `institucion_id` bigint(20) DEFAULT NULL,
  `grado_id` bigint(20) DEFAULT NULL,
  `seccion` int(11) DEFAULT NULL,
  `codigo` varchar(40) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `creado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `password_hash`, `rol`, `nombres`, `apellidos`, `institucion_id`, `grado_id`, `seccion`, `codigo`, `activo`, `creado_en`) VALUES
(1, '$2y$10$8xyWG2JYR6xs6AyRUNOc7.OWdCw2HepmdWC0LRdAq6FNeDrMNvluS', 'ADMIN', 'Wilfredo', 'Piox Tecú', 13, 4, 5, 'Supervision090401', 1, '2025-10-27 11:56:42'),
(2, '$2y$10$B.QeypR6T8EXx2e6AT91pO0nz3VCxPblT4IwPEEh2HkUBT.AFSO/i', 'DIRECTOR', 'Alma Consuelo', 'Reyes Maldonado', 12, 4, NULL, 'CM0904012025', 1, '2025-10-27 12:38:32'),
(3, '$2y$10$7E1y52Jgih9.2Oezzg3NQu4Q48yP1B83xZ4Gnth1rnzXarNnD2/Ju', 'ALUMNO', 'Karla Daniela', 'Abac Vicente', 12, 1, 1, 'XYZ0904012025A', 1, '2025-10-27 12:52:37'),
(4, '$2y$10$gQ6jVp/cGbR6KR4fgCjHU.G/nsCsvZE9cSY0m/2uIBtvdlLLdjZ4a', 'ALUMNO', 'Deidy Guadalupe', 'Abac Vicente', 12, 1, 1, 'ABC0904012025D', 1, '2025-10-27 12:55:42'),
(5, '$2y$10$fkFpvBuvGgo4Bt5hK4F.CeWl5spwFfb/XGrmeIqfcJzXDHcOfdd1y', 'DIRECTOR', 'Maynor Saul', 'Santos Díaz', 6, 4, NULL, '0904004745', 1, '2025-10-27 19:34:24'),
(6, '$2y$10$.vu7NGbAihC/fImBslX5R.5OZKHxNCv3J97HiHdeyLvubmMeZWxwy', 'ALUMNO', 'Meisy Natalia', 'Cuyuch Pérez', 6, 1, 1, 'I134LSS090401', 1, '2025-10-27 19:46:51'),
(7, '$2y$10$IqD7/nISYoUeT1rmfxyh/.Vz4prvI2MXg4WjINj/DOmc4Nexwa8Ou', 'ALUMNO', 'Clarisa Evanely', 'De León García', 6, 1, 1, 'J943FJ090401', 1, '2025-10-27 20:00:43'),
(8, '$2y$10$xPcYSZ1t5ric1pjS58Sr6O11JTpUdo1vGwUjyavg5.dWKJnQ8GyvC', 'ALUMNO', 'Clarisa Evanely', 'De León García', 6, 1, 1, 'J943FJ090401', 1, '2025-10-27 20:00:43'),
(9, '$2y$10$KbmSrflNIbt/GlBH/AaSWuWNA7pMa2eHIV3ArFDIlnvjmne.qr9UW', 'ALUMNO', 'Yasmy Nataly', 'Gonzalez Sontay', 6, 1, 1, 'J545XMJ090401', 1, '2025-10-27 20:02:27'),
(10, '$2y$10$7/shyOWqrjuG.FX24g.ifO.fhZjyHFLU.FK2lJCtkmfdwaRfZuUWC', 'ALUMNO', 'Robinson Anastacio', 'Gónzalez Vicente', 6, 1, 1, 'I140TEW090401', 1, '2025-10-27 20:04:58'),
(11, '$2y$10$YblgCY8Xonz.rolJLLsViuBr4B9afXoYvOIvLZPCypzhk3aXKJ01O', 'ALUMNO', 'Emely Dayana', 'Hernández Pérez', 6, 1, 1, 'J206KVW090401', 1, '2025-10-27 20:06:25'),
(12, '$2y$10$tNRHrdQwT68ph5nl5fwd2O/AI9GeKt5NtRw8Nhz712t2xdsnXBb6C', 'ALUMNO', 'Marilyn Julissa', 'Itzep Tzún', 6, 1, 1, 'J743LBE090401', 1, '2025-10-27 20:07:50'),
(13, '$2y$10$E.R9uurC01TcSdG1M9hu7OmSDWy7RWMvIGh.r9GG0xGgI3341m5Hi', 'ALUMNO', 'Misaél Narciso', 'López Gónzales', 6, 1, 1, 'J844QEQ090401', 1, '2025-10-27 20:09:10'),
(14, '$2y$10$JSONUxnzjOV/3ULpOwjXyeCxMjy77SGLzMwgEA3/oisinB.EBDw52', 'ALUMNO', 'Federico Armando', 'Pérez Vicente', 6, 1, 1, 'I033JKI090401', 1, '2025-10-27 20:10:21'),
(15, '$2y$10$MonBwAOXNsBqEuXsc4qxOusGUONEAR1hrDJ7IjFebToXWa/.8chSe', 'ALUMNO', 'Isaac Lázaro', 'Pérez Vicente', 6, 1, 1, 'K701JWW090401', 1, '2025-10-27 20:11:56'),
(16, '$2y$10$LiCtO7LDCFyx9f/sOIZ91OmWNyZtmclPUNSWZgUP71qCFBq92XKOy', 'ALUMNO', 'Isaac Lázaro', 'Pérez Vicente', 6, 1, 1, 'K701JWW090401', 0, '2025-10-27 20:11:56'),
(17, '$2y$10$1JF39h7.LtRvWV5/6OIHg.pvmdwb2F3QFpPVDop3I7cXN/B2vpJBa', 'ALUMNO', 'Isaac Lázaro', 'Pérez Vicente', 6, 1, 1, 'K701JWW090401', 0, '2025-10-27 20:11:56'),
(18, '$2y$10$q7avO5xhJy3ohjhSr4JoLejxn.4gWh1Zl7fjOKMAlOZaet0hnt2Oq', 'ALUMNO', 'Isaac Lázaro', 'Pérez Vicente', 6, 1, 1, 'K701JWW090401', 0, '2025-10-27 20:11:56'),
(19, '$2y$10$Of7tG7uwlu2skdqmlXbw9ubDoixTiIWnOlsdSsmD7pTDeB3UQ3NM6', 'ALUMNO', 'Isaac Lazaro', 'Pérez Vicente', 6, 1, 1, 'K701JWW090401', 0, '2025-10-27 20:13:15'),
(20, '$2y$10$J7ODMLeR4H3Fi5vVPrQ.0eHzdEEi5Hn1vTAOnJpy00RtmJ69bSNgO', 'ALUMNO', 'Jimena Karina', 'Pérez Vicente', 6, 1, 1, 'K100PTW090401', 1, '2025-10-27 20:15:26'),
(21, '$2y$10$wFRFOSErD2GvZPNP2AOl7.letRalbw./WyvKZ8Z0KRMUX9Bt8tuWW', 'ALUMNO', 'Celsa Lorena', 'Pérez Xiloj', 6, 1, 1, 'I134BWY090401', 1, '2025-10-27 20:16:47'),
(22, '$2y$10$PZCcotgYX5VliPdi/1bYzOdumQS/k.YpAbhD8Pm67pX6TnPoCFA9.', 'ALUMNO', 'Yadira María', 'Sontay Chaj', 6, 1, 1, 'I938KIC090401', 1, '2025-10-27 20:17:54'),
(23, '$2y$10$.U/JuYzp4P7ghvpGHR50uO73nJau3lrwcoJNCSFrOGJ/qBGZlMZ1q', 'ALUMNO', 'Damaris Emerilsa', 'Sontay Vicente', 6, 1, 1, 'K601HFP090401', 1, '2025-10-27 20:19:32'),
(24, '$2y$10$p8feuNyUSCFhItAK7L1f6.01SQrKL8Ug3/PrzOK.8IkYqg1Yizb9C', 'ALUMNO', 'Damaris Emerilsa', 'Sontay Vicente', 6, 1, 1, 'K601HFP090401', 0, '2025-10-27 20:19:32'),
(25, '$2y$10$3uE/5xw9d/O9Cw9iikLHueLN7jfMtAjNdCoLhK8Gdl3kFC6UCQ9Ry', 'ALUMNO', 'Damaris Emerilsa', 'Sontay Vicente', 6, 1, 1, 'K601HFP090401', 0, '2025-10-27 20:19:32'),
(26, '$2y$10$WVbisw1eEpga8nfuL3a10ehA/rVb2FixUuVucvo8VduwJ91Yj.jr.', 'ALUMNO', 'Damaris Emerilsa', 'Sontay Vicente', 6, 1, 1, 'K601HFP090401', 0, '2025-10-27 20:19:32'),
(27, '$2y$10$.1Wrik3W6HPJMawDlEqqzuR.xCmQkl17CQDm9xyU9tVChPHaaA7VG', 'ALUMNO', 'Kery Margarita', 'Sontay Vicente', 6, 1, 1, 'K801AQT090401', 1, '2025-10-27 20:21:38'),
(28, '$2y$10$dnNZnAGrVQrrRZ29xkJO4.wGmNK1jeeB63Xyu8WpvAFRps69evR9S', 'ALUMNO', 'María Zuleima', 'Sontay Xiloj', 6, 1, 1, 'J745UIU090401', 1, '2025-10-27 20:22:33'),
(29, '$2y$10$i5cZfYlCenSk1Q3G9j7XA..C02LscxCQDIB9UovBX4UEKB1hzUpYK', 'ALUMNO', 'Juana Azucely', 'Tzún López', 6, 1, 1, 'K501MYC090401', 1, '2025-10-27 20:24:05'),
(30, '$2y$10$Lfyqm4eL5D9vBlV9TKbpQOsDW2oIwFUZCr3aIMqtUrNTr38TcGL1.', 'ALUMNO', 'Luciano Alejandro', 'Vicente Abac', 6, 1, 1, 'J643ATY090401', 1, '2025-10-27 20:25:55'),
(31, '$2y$10$wNa0xR3irUIc5Eo0WDQkQOFfDYXEyqTQ8RnWykkjTjBxXoJy/rjj.', 'ALUMNO', 'Yulissa Josefina', 'Vicente Ixmay', 6, 1, 1, 'K800GTV090401', 1, '2025-10-27 20:27:25'),
(32, '$2y$10$zRWaymj8wmyE6xKotUl0oefOC4zPeNZOJx2AzY/bbnp0uqg3p2NI2', 'ALUMNO', 'Carlos Selvin', 'Vicente López', 6, 1, 1, 'H251AVN090401', 1, '2025-10-27 20:28:56'),
(33, '$2y$10$N3RPEtnwoqclm.8C72UNGuH9ToqCFfqL1RtT83mX87h/p9ONJMrC2', 'ALUMNO', 'Sofia Miranda', 'Vicente Pelicó', 6, 1, 1, 'K701RBS090401', 1, '2025-10-27 20:29:50'),
(34, '$2y$10$nBgYmHSWa8E6Hesfn1oyGOV9F1F2g5kBFdosr7q62eXt7lTjr8phu', 'ALUMNO', 'Saidia Inayelin Inés', 'Vicente Sontay', 6, 1, 1, 'H947DGN090401', 1, '2025-10-27 20:31:11'),
(35, '$2y$10$FhJb6y7PS7.SUEErub1CouNP2RRKN2M/VvaXPnUngVhDJGBuq7BSq', 'ALUMNO', 'Jostin Félix', 'Vicente Velásquez', 6, 1, 1, 'K200NAK090401', 1, '2025-10-27 20:32:16'),
(36, '$2y$10$6KkBPPER2sR5v/wJyn6SWuiPlyAxPjJjtG0ncl4erLNmVC/e.73ve', 'ALUMNO', 'Elmer Alejo', 'Vicente Vicente', 6, 1, 1, 'J246IKC090401', 1, '2025-10-27 20:33:14'),
(37, '$2y$10$px/xvt3zk/mGyky.pF2/YetSc0ITrZDWfwwL0BjpmtOjtm5Ke48KG', 'ALUMNO', 'Harold Abraham', 'Vicente Vicente', 6, 1, 1, 'I534SVF090401', 1, '2025-10-27 20:34:29'),
(38, '$2y$10$BdgJseXVjCEhp9luXWuac.vXOcjkJtnJOW0IdQ/gMR6uV99okI/Ta', 'ALUMNO', 'Sebastiana Aleida', 'Vicente Vicente', 6, 1, 1, 'J745IRL090401', 1, '2025-10-27 20:35:38'),
(39, '$2y$10$NVO82fTvGnav8i3LoNkpduBB0yWNKGqx9r0Xd6w4F8DBmuE1C0HEC', 'ALUMNO', 'Duany Obispo', 'Xiloj Vicente', 6, 1, 1, 'J044UCQ090401', 1, '2025-10-27 20:36:58'),
(40, '$2y$10$y0S5g1Tko1VujUxdUia01.BBVgtGqgV2Sz2pE0Lx44cJ5w480sVsq', 'ALUMNO', 'Hermelindo', 'Xiloj Vicente', 6, 1, 1, 'H249PAH090401', 1, '2025-10-27 20:37:59'),
(41, '$2y$10$ovJw3BSurETwjz9CwOsxceKnjWO/h72aivShTkKNSRkaGV/rAJKBK', 'ALUMNO', 'Christian Ramón', 'Chaj Ixmay', 6, 2, 1, 'J545CQX090401', 1, '2025-10-27 20:42:48'),
(42, '$2y$10$rmJ8/NFdW.oFn.HDaIrGOeV85wRHcEKqpnfYiWYBQxQDUtw3QHvQK', 'ALUMNO', 'Enrique', 'Chaj Vicente', 6, 1, 1, 'J245GFH090401', 1, '2025-10-27 20:45:24'),
(43, '$2y$10$9V18N2ybWIv1LSWF3/CGDuDhHE1XkP8B9EQctf2wRsjNCtkXETECW', 'ALUMNO', 'Jonatan Adrian', 'Chaj Xiloj', 6, 2, 1, 'H186UTY090401', 1, '2025-10-27 20:47:34'),
(44, '$2y$10$0fTom1K5hCJYSWAeo0r6bOosVWq7PhmNe.8yO8QaAo1//fID7txRC', 'ALUMNO', 'Anlleli Jazmín', 'Cuyuch Abac', 6, 2, 1, 'J246KDH090401', 1, '2025-10-27 20:49:01'),
(45, '$2y$10$si6N1sPscVSWKdM20WTqZ.f4ZnSmG6Mc/2T22tUadKgPbKYndCSza', 'ALUMNO', 'Erick Maudilio', 'De León García', 6, 2, 1, 'J944FNE090401', 1, '2025-10-27 20:50:12'),
(46, '$2y$10$8gDVl4h0IlZczjIBu7EV1eCkNem/ckXySzmghVZNQzbu4XcUJGeQi', 'ALUMNO', 'Abner Santos', 'Gonzalez Sontay', 6, 2, 1, 'I632HNG090401', 1, '2025-10-27 20:51:22'),
(47, '$2y$10$NigLXx7x.ABjyFieIuCPG.LqpfF0JfmmgH1P6k.Dt9bfVp60kjtlC', 'ALUMNO', 'Yessy Pahola', 'Itzep Vicente', 6, 2, 1, 'I432YWW090401', 1, '2025-10-27 20:53:24'),
(48, '$2y$10$hGhkmHX5IOE835nfHWidQuC6kocgvQvbtuMk6XNUHbhpelNSAJLeO', 'ALUMNO', 'Iván Luis Josué', 'Ordóñez Xiloj', 6, 2, 1, 'I333KKD090401', 1, '2025-10-27 20:55:25'),
(49, '$2y$10$1fghoVie2YzuphoXbcqFPu5ReckzoWdcGU4gBLcSFIbo2TTE4RoeO', 'ALUMNO', 'Juan Isaías', 'Paxtor López', 6, 2, 1, 'I234XXK090401', 1, '2025-10-27 20:56:36'),
(50, '$2y$10$LgHQnjCRp6AAJwFH04zVYefslfDzX.6Rhi.biARiHHlj53KfuDmr6', 'ALUMNO', 'Alex Aurelio', 'Perucho Vicente', 6, 2, 1, 'I132QWW090401', 1, '2025-10-27 20:57:38'),
(51, '$2y$10$X4uDCeAZnGIJotj7wfKWvexsvPDXKd5LpAGtpUzhahVYrGtm7damq', 'ALUMNO', 'Elíu Alberto', 'Poroj Sontay', 6, 2, 1, 'I739EUW090401', 1, '2025-10-27 20:59:53'),
(52, '$2y$10$k.C.HdVQI9.VHHOfSK7qJ.s0c.0dIpyQdyE5YRdBztBQFaujlUBmy', 'ALUMNO', 'Azael Yovany', 'Pérez Vicente', 6, 2, 1, 'I430FBD090401', 1, '2025-10-27 21:00:55'),
(53, '$2y$10$TrzDOdsK8XffbBp9vbDJeuzwxnktgVRnISTa6ZqvIXbf3J913.asy', 'ALUMNO', 'Juana Yeimi', 'Pérez Vicente', 6, 2, 1, 'F930SPK090401', 1, '2025-10-27 21:03:00'),
(54, '$2y$10$V.WwTuRnpsP0LrLqmlg3I.XZX6GZfbOfVtfDeIi140wlOxdauCdte', 'ALUMNO', 'Elder Ambrocio', 'Pérez Xiloj', 6, 2, 1, 'I041AJY090401', 1, '2025-10-27 21:04:22'),
(55, '$2y$10$xNMLmZbm22lCHm9JGbrXbOuxsa4UmWohfO2ByrWxYNPwwSuM08HDO', 'ALUMNO', 'Mádaly Hilaria', 'Sontay López', 6, 2, 1, 'I031YMC090401', 1, '2025-10-27 21:06:07'),
(56, '$2y$10$QNAT9PrSLhi8HDkizJfWe.Mk06ztMTR/ORSwy8vog0EwvdCUPG0me', 'ALUMNO', 'Marcos Franky', 'Sontay Ordoñez', 6, 2, 1, 'J743VUI090401', 1, '2025-10-27 21:07:38'),
(57, '$2y$10$oUEgSCAtwbuLmp7Q.2W1Ke6MQrC0PI9weH9DBnBWpaj6uYJ29Ptx6', 'ALUMNO', 'Aislin Florentina', 'Sontay Velasquez', 6, 2, 1, 'J046MYJ090401', 1, '2025-10-27 21:09:10'),
(58, '$2y$10$vMG7WZz3jQFGOZ1Jgdq4heJQrszM3IGnLYqS/jF6OBS5ZawtGQ.iG', 'ALUMNO', 'Melissa Carmen', 'Vicente Abac', 6, 2, 1, 'I931XZE090401', 1, '2025-10-27 21:10:10'),
(59, '$2y$10$Pg4oJXkcx9afftuesIq01er3H8GWaxMshtTXDA6eweQcK9ZLZgXb2', 'ALUMNO', 'Ádelyn Angelina Lissbeth', 'Vicente Abac', 6, 2, 1, 'I340BJT090401', 1, '2025-10-27 21:11:41'),
(60, '$2y$10$T3uIFMiA0e.1nGd/LMz9RudSdaDFm.ofg0zPYtfYLOLqh5wYxl9Wq', 'ALUMNO', 'Juan Carlos', 'Vicente Chanchavan', 6, 2, 1, 'H888KJE090401', 1, '2025-10-27 21:12:38'),
(61, '$2y$10$qCCvnoDh/x.EfSbhQMAfk.I/b/fWEIsBmXoNFvJxElqk18AiZZAF6', 'ALUMNO', 'Emmanuel', 'Vicente López', 6, 2, 1, 'I132TZR090401', 1, '2025-10-27 21:13:43'),
(62, '$2y$10$fC4aG6/uj9liHF.KbiTDleagYd1oRyokRN2PsjADDsZ5C.HErxFOe', 'ALUMNO', 'Reyna Saray', 'Vicente Tzún', 6, 2, 1, 'J446CZZ090401', 1, '2025-10-27 21:14:51'),
(63, '$2y$10$n9pNyIqJDjU44ut/pXO.Te6NdpVNmWuFqMEU8Gvr67mqO0GmAyIXy', 'ALUMNO', 'Adolfo Gabriel', 'Vicente Vicente', 6, 2, 1, 'H947ZKD090401', 1, '2025-10-27 21:15:45'),
(64, '$2y$10$2/MuvkEBmZHVSexq7J4eV.KPNzT3ucDDIUE/s5/i/tGKAVFPC3tRS', 'ALUMNO', 'Adriana Margarita', 'Vicente Vicente', 6, 2, 1, 'J242EMB090401', 1, '2025-10-27 21:16:50'),
(65, '$2y$10$mFsyvWD.Nt0AE4Ke.TTEFOGW2bgSCH4WhwqbS64tIQbriIjMB02HK', 'ALUMNO', 'Carmen Keimelyn', 'Vicente Vicente', 6, 2, 1, 'I832FZZ090401', 1, '2025-10-27 21:17:45'),
(66, '$2y$10$f521cxxytiebK6pAEsGl3OpjBdMmHsZm1DQAidGDVfkdMkyM2/6V2', 'ALUMNO', 'Ciria Esmeralda', 'Vicente Vicente', 6, 2, 1, 'H927ELK090401', 1, '2025-10-27 21:18:50'),
(67, '$2y$10$GyXkeyBIgRCznoMorZ5BvuT9Cql.6xLHcLrfkIaucAbHRmXX.bFuq', 'ALUMNO', 'Leticia Rafaela', 'Vicente Vicente', 6, 2, 1, 'F064IRR090401', 1, '2025-10-27 21:19:45'),
(68, '$2y$10$NE6FpaHleOi4xy.CSXi.Uu3BB0h4oUjKZ98Nikv8qcwWCGP41/2Wa', 'ALUMNO', 'Emanuel Anderson', 'Vicente Xiloj', 6, 2, 1, 'H295URW090401', 1, '2025-10-27 21:20:37'),
(69, '$2y$10$Yomio/HRWycrwmvZNnZg8.csl29yZMFEODO30bTKsrHjExwuVmJVe', 'ALUMNO', 'Alveyro Alejandro', 'Vicente Y Vicente', 6, 2, 1, 'F941MKS090401', 1, '2025-10-27 21:21:43'),
(70, '$2y$10$Twtr/lgIblaFOEyjdYoaVOoFeT7.UyzElOaAMGvKsPgborV0SeyDi', 'ALUMNO', 'Neida Yohana', 'Xiloj Vicente', 6, 2, 1, 'H659DTI090401', 1, '2025-10-27 21:22:58'),
(71, '$2y$10$mSeKodb9u1Z7yF.DYdy.l..bj/hxZKMmTp7Nuh4W20DR8qzPMAHJa', 'ALUMNO', 'Wualter Ramiro', 'Zarat Díaz', 6, 2, 1, 'I434VWS090401', 1, '2025-10-27 21:24:43'),
(72, '$2y$10$FTIpvmHA.lcQvu1TZJ5dT.bllAOIW9AUpQicZ24Sgg5EX5iXm7kK2', 'ALUMNO', 'Meyra Roselia', 'Abac Vicente', 6, 3, 1, 'F482GNI090401', 1, '2025-10-27 21:25:53'),
(73, '$2y$10$6pJ8UEgGJReP8FB/b8Y/b.V47.1AXIFDJg6K8rFT/75MEDcvMrmXm', 'ALUMNO', 'Pricila Julia', 'Chaj Baten', 6, 3, 1, 'H826ZNI090401', 1, '2025-10-27 21:26:50'),
(74, '$2y$10$hkUNpUV7WE4tlvD7kz/MYeHQ9lXIIhD2YXI2RZA/sRYIdr2py7AoC', 'ALUMNO', 'Brayan Ramon', 'Chaj Xiloj', 6, 3, 1, 'H842NYN090401', 1, '2025-10-27 21:27:46'),
(75, '$2y$10$cX23Owm6jx7u55MA1gjUoe9xcoM7vx5SbGpb.fcKeUyzLqgWqmyBi', 'ALUMNO', 'Cindy Vanesa', 'Gonzalez Sontay', 6, 3, 1, 'F692YIH090401', 1, '2025-10-27 21:28:51'),
(76, '$2y$10$qa/XXkRQsX7OiMJHiikivumKRDByixYXREkg.LbALJD7rXPtZJAHW', 'ALUMNO', 'Brayan Sergio', 'González Poroj', 6, 3, 1, 'H343HRU090401', 1, '2025-10-27 21:30:05'),
(77, '$2y$10$vFWIkbdE0YNfJBdvdIBz0up92krsgX3bHDYcyjEz1TrVergWlrjlq', 'ALUMNO', 'Belinda Gimena', 'Itzep Vicente', 6, 3, 1, 'F852PWZ090401', 1, '2025-10-27 21:31:02'),
(78, '$2y$10$vTT2qaTd/EFoDIST87mZA.zYmzNp8QpLl4fHNXdoe1/xccJUdQB.C', 'ALUMNO', 'Ester Maritza', 'Ixmay Ixmay', 6, 3, 1, 'G089UYY090401', 1, '2025-10-27 21:32:03'),
(79, '$2y$10$57yYDB9venIu13yhL.EzKuvx/ChgPMeiQyp0uOzFroHGPnVsxRow.', 'ALUMNO', 'Juana Beatriz', 'Ixmay Vicente', 6, 3, 1, 'I041AZS090401', 1, '2025-10-27 21:33:24'),
(80, '$2y$10$aV7IYPq3ET1fsCpVeEE1RONrWyAjLz.N7Lq6W.twKeUvLQgu.ie1a', 'ALUMNO', 'Suleima Nohemí', 'López Ordoñez', 6, 3, 1, 'I833KGD090401', 1, '2025-10-27 21:35:09'),
(81, '$2y$10$dBER.5pUj4MH4/wCgaD6s.O0TrmR6s0fdHaOEL/PxSZzDrEE15dN2', 'ALUMNO', 'Derick Francisco', 'López Velasquez', 6, 3, 1, 'F037RVG090401', 1, '2025-10-27 21:36:28'),
(82, '$2y$10$jVIxiCNmONJn5NLVs5nDgOM64FrlEMz65w/SupD9QXY18HTve3rmq', 'ALUMNO', 'Santa Marianela', 'López Velasquez', 6, 3, 1, 'I933FQX090401', 1, '2025-10-27 21:37:57'),
(83, '$2y$10$52ypUjmxlX9l5HrjWfGmXORlkXUH.rJ/TSXT1isefQB2wGzo9Urq6', 'ALUMNO', 'Brenda Marcela', 'López Xiloj', 6, 3, 1, 'E072VNR090401', 1, '2025-10-27 21:39:32'),
(84, '$2y$10$i/YsUlXXiLkR273iPvUvYOqtgZa1W5l5O1peM9prOuUPMfZf5bcu2', 'ALUMNO', 'Estela', 'Pelicó López', 6, 3, 1, 'H476ZAU090401', 1, '2025-10-27 21:42:58'),
(85, '$2y$10$vYGcKjXp6.VEgw7ycNyVgORnLgeFou9DUjxjGPp5ssbMkf.zdymKi', 'ALUMNO', 'Sergio Victoriano', 'Perucho Vicente', 6, 3, 1, 'I830LMV090401', 1, '2025-10-27 21:44:26'),
(86, '$2y$10$n6LPYUR1V6lsYgZ9CKBh7Ox.uuiZgHcPvr751OP.zpJDxpNn5u/9C', 'ALUMNO', 'Carmen Dinora', 'Pérez Cuyuch', 6, 3, 1, 'F344FST090401', 1, '2025-10-27 21:46:34'),
(87, '$2y$10$dTjuHJoEpVLwIDBANx1qguuIhy4A/RUfR6W9ac8hwP/y3vRfKYkb.', 'ALUMNO', 'Floridalma Ambrocia', 'Sontay Pérez', 6, 3, 1, 'F747MSE090401', 1, '2025-10-27 21:47:47'),
(88, '$2y$10$Bxh8yi9.CaqmjxqBZCwbIeQVjLKBQD9PqapgWj1pduCtozL0zdqDe', 'ALUMNO', 'Wendy Lucia', 'Tzún Ixmay', 6, 3, 1, 'I337JDJ090401', 1, '2025-10-27 21:49:53'),
(89, '$2y$10$1Y3ACAX0qXGmlcIIzX3gH.IoR4LfOUzYkLycfz/nq9gdumUS7k57.', 'ALUMNO', 'Ricky', 'Tzún Vicente', 6, 3, 1, 'H053XIN090401', 1, '2025-10-27 21:52:28'),
(90, '$2y$10$kGvebVhNKa43oY6rdxdFteF4HoryZFybC6KA.LGQYHyrOLapq.Tjq', 'ALUMNO', 'Victor David', 'Vicente Ajtum', 6, 3, 1, 'H635VCR090401', 1, '2025-10-27 21:54:00'),
(91, '$2y$10$ZgNxsaZtQsNYiZLLtZkjFOT2ijsSjFgkOeyqPf1fe10pMUtrrGNSW', 'ALUMNO', 'Kendell Agustin', 'Vicente Sontay', 6, 3, 1, 'H458MYJ090401', 1, '2025-10-27 21:55:23'),
(92, '$2y$10$cy6EzLzUzI6PV6pwCQqTu.tLcGVtNEpD67.ZOattaICdsBUFpF.ZO', 'ALUMNO', 'Israeldin Leocadio', 'Vicente Vicente', 6, 3, 1, 'H934DJL090401', 1, '2025-10-27 21:57:24'),
(93, '$2y$10$cvZsr8usm.uN80IfmgqMA.eRGH0DctdXoVOjpHieJ3CodpBZLuCCK', 'ALUMNO', 'Rodesa Lucrecia', 'Vicente Xiloj', 6, 3, 1, 'G756SHR090401', 1, '2025-10-27 21:59:01'),
(94, '$2y$10$Q/e/Zd2WaiqmmJsEELYLNOGNN00gleoD2Dxoh8vVI0b2GziD3i.VG', 'ALUMNO', 'Elmer Alex', 'Vicente Vicente', 6, 2, 1, 'J146DBE090401', 1, '2025-10-27 22:53:49');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vw_fact_calificaciones`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vw_fact_calificaciones` (
`calif_id` bigint(20)
,`alumno_user_id` bigint(20)
,`curso_id` bigint(20)
,`institucion_id` bigint(20)
,`grado_id` bigint(20)
,`periodo` varchar(48)
,`puntaje` decimal(6,2)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vw_fact_respuestas`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vw_fact_respuestas` (
`respuesta_alumno_id` bigint(20)
,`alumno_user_id` bigint(20)
,`nombres` varchar(120)
,`apellidos` varchar(120)
,`rol` enum('ADMIN','DIRECTOR','DOCENTE','ALUMNO')
,`seccion` int(11)
,`institucion_id` bigint(20)
,`grado_id` bigint(20)
,`institucion_nombre` varchar(160)
,`institucion_tipo` varchar(60)
,`distrito_id` bigint(20)
,`distrito_nombre` varchar(100)
,`encuesta_id` bigint(20)
,`encuesta_titulo` varchar(160)
,`curso_id` bigint(20)
,`curso_nombre` varchar(80)
,`encuesta_grado_id` bigint(20)
,`encuesta_grado_nombre` varchar(60)
,`pregunta_id` bigint(20)
,`pregunta_tipo` enum('opcion_unica','opcion_multiple','abierta','numerica')
,`pregunta_ponderacion` decimal(5,2)
,`respuesta_id` bigint(20)
,`respuesta_texto` text
,`respuesta_numero` decimal(12,4)
,`es_correcta` tinyint(1)
,`respondido_at` timestamp
,`acierto` int(1)
,`es_abierta` int(1)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vw_puntaje_alumno_encuesta`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vw_puntaje_alumno_encuesta` (
`encuesta_id` bigint(20)
,`curso_id` bigint(20)
,`grado_id` bigint(20)
,`institucion_id` bigint(20)
,`alumno_user_id` bigint(20)
,`alumno` varchar(241)
,`puntaje` decimal(27,2)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vw_top_alumnos_institucion`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vw_top_alumnos_institucion` (
`institucion_id` bigint(20)
,`institucion_nombre` varchar(160)
,`periodo` varchar(48)
,`curso_id` bigint(20)
,`curso_nombre` varchar(80)
,`grado_id` bigint(20)
,`grado_nombre` varchar(60)
,`alumno_user_id` bigint(20)
,`alumno` varchar(241)
,`puntaje` decimal(6,2)
,`rn` bigint(22)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vw_top_alumnos_resp`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vw_top_alumnos_resp` (
`encuesta_id` bigint(20)
,`curso_id` bigint(20)
,`grado_id` bigint(20)
,`institucion_id` bigint(20)
,`alumno_user_id` bigint(20)
,`alumno` varchar(241)
,`puntaje` decimal(27,2)
,`rn` bigint(22)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `dim_encuestas`
--
DROP TABLE IF EXISTS `dim_encuestas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`system`@`127.0.0.1` SQL SECURITY DEFINER VIEW `dim_encuestas`  AS  select `e`.`id` AS `id`,`e`.`titulo` AS `titulo`,`e`.`curso_id` AS `curso_id`,`c`.`nombre` AS `curso_nombre`,`e`.`grado_id` AS `grado_id`,`g`.`nombre` AS `grado_nombre`,`e`.`institucion_id` AS `institucion_id`,`e`.`estado` AS `estado`,`e`.`fecha_inicio` AS `fecha_inicio`,`e`.`fecha_fin` AS `fecha_fin`,`e`.`creado_por` AS `creado_por` from ((`encuestas` `e` join `cursos` `c` on((`c`.`id` = `e`.`curso_id`))) join `grados` `g` on((`g`.`id` = `e`.`grado_id`))) where (`e`.`activo` = 1) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `dim_usuarios`
--
DROP TABLE IF EXISTS `dim_usuarios`;

CREATE ALGORITHM=UNDEFINED DEFINER=`system`@`127.0.0.1` SQL SECURITY DEFINER VIEW `dim_usuarios`  AS  select `u`.`id` AS `id`,`u`.`nombres` AS `nombres`,`u`.`apellidos` AS `apellidos`,`u`.`rol` AS `rol`,`u`.`seccion` AS `seccion`,`u`.`institucion_id` AS `institucion_id`,`u`.`grado_id` AS `grado_id`,`u`.`codigo` AS `codigo`,`u`.`creado_en` AS `creado_en` from `usuarios` `u` where (`u`.`activo` = 1) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vw_fact_calificaciones`
--
DROP TABLE IF EXISTS `vw_fact_calificaciones`;

CREATE ALGORITHM=UNDEFINED DEFINER=`system`@`127.0.0.1` SQL SECURITY DEFINER VIEW `vw_fact_calificaciones`  AS  select `ca`.`id` AS `calif_id`,`ca`.`alumno_user_id` AS `alumno_user_id`,`ca`.`curso_id` AS `curso_id`,`ca`.`institucion_id` AS `institucion_id`,`ca`.`grado_id` AS `grado_id`,`ca`.`periodo` AS `periodo`,`ca`.`puntaje` AS `puntaje` from `calificaciones` `ca` where (`ca`.`activo` = 1) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vw_fact_respuestas`
--
DROP TABLE IF EXISTS `vw_fact_respuestas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`system`@`127.0.0.1` SQL SECURITY DEFINER VIEW `vw_fact_respuestas`  AS  select `ra`.`id` AS `respuesta_alumno_id`,`ra`.`alumno_user_id` AS `alumno_user_id`,`u`.`nombres` AS `nombres`,`u`.`apellidos` AS `apellidos`,`u`.`rol` AS `rol`,`u`.`seccion` AS `seccion`,`u`.`institucion_id` AS `institucion_id`,`u`.`grado_id` AS `grado_id`,`i`.`nombre` AS `institucion_nombre`,`i`.`tipo` AS `institucion_tipo`,`i`.`distrito_id` AS `distrito_id`,`d`.`nombre` AS `distrito_nombre`,`ra`.`encuesta_id` AS `encuesta_id`,`e`.`titulo` AS `encuesta_titulo`,`e`.`curso_id` AS `curso_id`,`c`.`nombre` AS `curso_nombre`,`e`.`grado_id` AS `encuesta_grado_id`,`g`.`nombre` AS `encuesta_grado_nombre`,`ra`.`pregunta_id` AS `pregunta_id`,`p`.`tipo` AS `pregunta_tipo`,`p`.`ponderacion` AS `pregunta_ponderacion`,`ra`.`respuesta_id` AS `respuesta_id`,`ra`.`respuesta_texto` AS `respuesta_texto`,`ra`.`respuesta_numero` AS `respuesta_numero`,`ra`.`es_correcta` AS `es_correcta`,`ra`.`respondido_at` AS `respondido_at`,(`ra`.`es_correcta` = 1) AS `acierto`,isnull(`ra`.`respuesta_id`) AS `es_abierta` from ((((((((`respuestas_alumnos` `ra` join `usuarios` `u` on((`u`.`id` = `ra`.`alumno_user_id`))) left join `instituciones` `i` on((`i`.`id` = `u`.`institucion_id`))) left join `distritos` `d` on((`d`.`id` = `i`.`distrito_id`))) join `encuestas` `e` on((`e`.`id` = `ra`.`encuesta_id`))) join `cursos` `c` on((`c`.`id` = `e`.`curso_id`))) join `grados` `g` on((`g`.`id` = `e`.`grado_id`))) join `preguntas` `p` on((`p`.`id` = `ra`.`pregunta_id`))) left join `respuestas` `r` on((`r`.`id` = `ra`.`respuesta_id`))) where ((`ra`.`activo` = 1) and (`u`.`activo` = 1) and (`e`.`activo` = 1)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vw_puntaje_alumno_encuesta`
--
DROP TABLE IF EXISTS `vw_puntaje_alumno_encuesta`;

CREATE ALGORITHM=UNDEFINED DEFINER=`system`@`127.0.0.1` SQL SECURITY DEFINER VIEW `vw_puntaje_alumno_encuesta`  AS  select `r`.`encuesta_id` AS `encuesta_id`,`r`.`curso_id` AS `curso_id`,`r`.`encuesta_grado_id` AS `grado_id`,`r`.`institucion_id` AS `institucion_id`,`r`.`alumno_user_id` AS `alumno_user_id`,concat(`r`.`nombres`,' ',`r`.`apellidos`) AS `alumno`,sum((case when (`r`.`acierto` = 1) then `r`.`pregunta_ponderacion` else 0 end)) AS `puntaje` from `vw_fact_respuestas` `r` where (`r`.`rol` = 'ALUMNO') group by `r`.`encuesta_id`,`r`.`curso_id`,`r`.`encuesta_grado_id`,`r`.`institucion_id`,`r`.`alumno_user_id`,concat(`r`.`nombres`,' ',`r`.`apellidos`) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vw_top_alumnos_institucion`
--
DROP TABLE IF EXISTS `vw_top_alumnos_institucion`;

CREATE ALGORITHM=UNDEFINED DEFINER=`system`@`127.0.0.1` SQL SECURITY DEFINER VIEW `vw_top_alumnos_institucion`  AS  select `ca`.`institucion_id` AS `institucion_id`,`i`.`nombre` AS `institucion_nombre`,`ca`.`periodo` AS `periodo`,`ca`.`curso_id` AS `curso_id`,`c`.`nombre` AS `curso_nombre`,`ca`.`grado_id` AS `grado_id`,`g`.`nombre` AS `grado_nombre`,`ca`.`alumno_user_id` AS `alumno_user_id`,concat(`u`.`nombres`,' ',`u`.`apellidos`) AS `alumno`,`ca`.`puntaje` AS `puntaje`,(1 + (select count(distinct `cb`.`puntaje`) from `vw_fact_calificaciones` `cb` where ((`cb`.`institucion_id` = `ca`.`institucion_id`) and (`cb`.`periodo` = `ca`.`periodo`) and (`cb`.`curso_id` = `ca`.`curso_id`) and (`cb`.`grado_id` = `ca`.`grado_id`) and (`cb`.`puntaje` > `ca`.`puntaje`)))) AS `rn` from ((((`vw_fact_calificaciones` `ca` join `usuarios` `u` on(((`u`.`id` = `ca`.`alumno_user_id`) and (`u`.`activo` = 1)))) join `instituciones` `i` on((`i`.`id` = `ca`.`institucion_id`))) join `cursos` `c` on((`c`.`id` = `ca`.`curso_id`))) join `grados` `g` on((`g`.`id` = `ca`.`grado_id`))) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vw_top_alumnos_resp`
--
DROP TABLE IF EXISTS `vw_top_alumnos_resp`;

CREATE ALGORITHM=UNDEFINED DEFINER=`system`@`127.0.0.1` SQL SECURITY DEFINER VIEW `vw_top_alumnos_resp`  AS  select `p`.`encuesta_id` AS `encuesta_id`,`p`.`curso_id` AS `curso_id`,`p`.`grado_id` AS `grado_id`,`p`.`institucion_id` AS `institucion_id`,`p`.`alumno_user_id` AS `alumno_user_id`,`p`.`alumno` AS `alumno`,`p`.`puntaje` AS `puntaje`,(1 + (select count(distinct `q`.`puntaje`) from `vw_puntaje_alumno_encuesta` `q` where ((`q`.`encuesta_id` = `p`.`encuesta_id`) and (`q`.`curso_id` = `p`.`curso_id`) and (`q`.`grado_id` = `p`.`grado_id`) and (`q`.`institucion_id` = `p`.`institucion_id`) and (`q`.`puntaje` > `p`.`puntaje`)))) AS `rn` from `vw_puntaje_alumno_encuesta` `p` ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_calif_alumno_periodo` (`alumno_user_id`,`periodo`),
  ADD UNIQUE KEY `uq_calif` (`alumno_user_id`,`curso_id`,`institucion_id`,`grado_id`,`periodo`,`activo`),
  ADD KEY `idx_calif_curso` (`curso_id`),
  ADD KEY `idx_calif_inst` (`institucion_id`),
  ADD KEY `idx_calif_grado` (`grado_id`),
  ADD KEY `idx_activo` (`activo`);

--
-- Indices de la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_activo` (`activo`);

--
-- Indices de la tabla `distritos`
--
ALTER TABLE `distritos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_activo` (`activo`);

--
-- Indices de la tabla `encuestas`
--
ALTER TABLE `encuestas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_enc_curso` (`curso_id`),
  ADD KEY `idx_enc_grado` (`grado_id`),
  ADD KEY `idx_enc_inst` (`institucion_id`),
  ADD KEY `idx_enc_creado` (`creado_por`),
  ADD KEY `idx_activo` (`activo`);

--
-- Indices de la tabla `grados`
--
ALTER TABLE `grados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_activo` (`activo`);

--
-- Indices de la tabla `instituciones`
--
ALTER TABLE `instituciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `idx_activo` (`activo`),
  ADD KEY `fk_inst_distr` (`distrito_id`);

--
-- Indices de la tabla `materiales`
--
ALTER TABLE `materiales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_mat_docente` (`docente_user_id`),
  ADD KEY `idx_mat_curso` (`curso_id`),
  ADD KEY `idx_mat_grado` (`grado_id`),
  ADD KEY `idx_activo` (`activo`);

--
-- Indices de la tabla `material_archivos`
--
ALTER TABLE `material_archivos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ma_material` (`material_id`),
  ADD KEY `idx_activo` (`activo`);

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `idx_pr_user` (`usuario_id`),
  ADD KEY `idx_activo` (`activo`);

--
-- Indices de la tabla `preguntas`
--
ALTER TABLE `preguntas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_preg_enc` (`encuesta_id`),
  ADD KEY `idx_activo` (`activo`);

--
-- Indices de la tabla `respuestas`
--
ALTER TABLE `respuestas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `respuestas_alumnos`
--
ALTER TABLE `respuestas_alumnos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_respuesta_unica` (`alumno_user_id`,`encuesta_id`,`pregunta_id`,`activo`),
  ADD KEY `idx_ra_alumno` (`alumno_user_id`),
  ADD KEY `idx_ra_encuesta` (`encuesta_id`),
  ADD KEY `idx_ra_pregunta` (`pregunta_id`),
  ADD KEY `idx_ra_respuesta` (`respuesta_id`);

--
-- Indices de la tabla `seccion`
--
ALTER TABLE `seccion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_usuarios_seccion` (`seccion`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;
--
-- AUTO_INCREMENT de la tabla `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `distritos`
--
ALTER TABLE `distritos`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `encuestas`
--
ALTER TABLE `encuestas`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT de la tabla `grados`
--
ALTER TABLE `grados`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT de la tabla `instituciones`
--
ALTER TABLE `instituciones`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT de la tabla `materiales`
--
ALTER TABLE `materiales`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `material_archivos`
--
ALTER TABLE `material_archivos`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `preguntas`
--
ALTER TABLE `preguntas`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=205;
--
-- AUTO_INCREMENT de la tabla `respuestas`
--
ALTER TABLE `respuestas`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=615;
--
-- AUTO_INCREMENT de la tabla `respuestas_alumnos`
--
ALTER TABLE `respuestas_alumnos`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1741;
--
-- AUTO_INCREMENT de la tabla `seccion`
--
ALTER TABLE `seccion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  ADD CONSTRAINT `fk_calificaciones_alumno` FOREIGN KEY (`alumno_user_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_calificaciones_cursos` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_calificaciones_grados` FOREIGN KEY (`grado_id`) REFERENCES `grados` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `instituciones`
--
ALTER TABLE `instituciones`
  ADD CONSTRAINT `fk_instituciones_distritos` FOREIGN KEY (`distrito_id`) REFERENCES `distritos` (`id`);

--
-- Filtros para la tabla `materiales`
--
ALTER TABLE `materiales`
  ADD CONSTRAINT `fk_materiales_curso` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_materiales_docente` FOREIGN KEY (`docente_user_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_materiales_grado` FOREIGN KEY (`grado_id`) REFERENCES `grados` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `material_archivos`
--
ALTER TABLE `material_archivos`
  ADD CONSTRAINT `fk_material_archivos_material` FOREIGN KEY (`material_id`) REFERENCES `materiales` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `respuestas_alumnos`
--
ALTER TABLE `respuestas_alumnos`
  ADD CONSTRAINT `fk_ra_alumno` FOREIGN KEY (`alumno_user_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_ra_encuesta` FOREIGN KEY (`encuesta_id`) REFERENCES `encuestas` (`id`),
  ADD CONSTRAINT `fk_ra_opcion` FOREIGN KEY (`respuesta_id`) REFERENCES `respuestas` (`id`),
  ADD CONSTRAINT `fk_ra_pregunta` FOREIGN KEY (`pregunta_id`) REFERENCES `preguntas` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_seccion` FOREIGN KEY (`seccion`) REFERENCES `seccion` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
