<?php

class EvaluacionController
{
    public function __construct()
    {
        require_once "core/AuthMiddleware.php";
        require_once "core/AuthValidation.php";
        require_once "models/Encuestas.php";
        require_once "models/Preguntas.php";
        require_once "models/Respuestas.php";
        require_once "models/RespuestasAlumnos.php";
        
        // Verificar autenticación: permitir ALUMNO y ADMIN (Admin en modo lectura)
        validarRol(['ALUMNO','ADMIN']);
        
        $data["titulo"] = "Evaluacion";
    }

    public function index()
    {
        require_once "views/Evaluacion/Evaluacion.php";
    }

    // Listar encuestas activas (por defecto estado ACTIVA) para el combo
    public function ListarEncuestas()
    {
        header('Content-Type: application/json');
        try {
            $soloActivas = !isset($_GET['todas']) || $_GET['todas'] !== '1';
            $soloEstadoActiva = !isset($_GET['estado']) || strtoupper($_GET['estado']) === 'ACTIVA';
            $model = new Encuestas_model();
            // Solo listar encuestas vigentes por fecha
            $rows = $model->Listar($soloActivas, $soloEstadoActiva, /*soloVigentes*/ true);
            // Log resultado para depuración
            error_log('[ListarEncuestas] filas recuperadas: ' . count($rows));
            if (empty($rows)) {
                error_log('[ListarEncuestas] No se encontraron filas, devolviendo respuesta de prueba temporal');
                $rows = [
                    [ 'id' => 0, 'titulo' => 'TEST - Encuesta de prueba', 'curso_id' => 0, 'grado_id' => 0, 'institucion_id' => 0, 'estado' => 'ACTIVA', 'activo' => 1 ]
                ];
            }
            // Preparar JSON
            $payload = json_encode([ 'success' => true, 'data' => $rows ]);
            // Limpiar cualquier salida previa (espacios, BOM, etc.)
            while (ob_get_level() > 0) { ob_end_clean(); }
            // Eliminar BOM UTF-8 si existe al inicio
            if (substr($payload, 0, 3) === "\xEF\xBB\xBF") {
                $payload = substr($payload, 3);
            }
            echo $payload;
        } catch (Exception $e) {
            // Registrar detalles del error y devolver JSON consistente
            error_log('[ListarEncuestas] Error: ' . $e->getMessage());
            http_response_code(400);
            $errPayload = json_encode([ 'success' => false, 'data' => [], 'msj' => 'Error: ' . $e->getMessage() ]);
            while (ob_get_level() > 0) { ob_end_clean(); }
            if (substr($errPayload, 0, 3) === "\xEF\xBB\xBF") { $errPayload = substr($errPayload, 3); }
            echo $errPayload;
        }
    }

    // Cargar evaluaciÃ³n: preguntas aleatorias con sus respuestas por encuesta
    public function CargarEvaluacion()
    {
        header('Content-Type: application/json');
        try {
            $encuesta_id = isset($_GET['encuesta_id']) ? (int)$_GET['encuesta_id'] : 0;
            if ($encuesta_id <= 0) { throw new Exception('encuesta_id inválido'); }

            // Validar que la encuesta esté vigente por fecha y activa
            $encModel = new Encuestas_model();
            $vigente = $encModel->ObtenerVigentePorId($encuesta_id, /*requireActiva*/ true, /*requireActivo*/ true);
            if (!$vigente) { throw new Exception('La evaluación no está vigente'); }

            $pregModel = new Preguntas_model();
            $respModel = new Respuestas_model();
            // Obtener hasta 20 preguntas aleatorias
            $pregs = $pregModel->ListarPorEncuestaRandom($encuesta_id, true, 20);
            if (count($pregs) > 20) {
                $pregs = array_slice($pregs, 0, 20);
            }
            // Adjuntar respuestas activas
            foreach ($pregs as &$p) {
                $p['respuestas'] = $respModel->ListarPorPregunta($p['id'], true);
                // Deduplicar por etiqueta visible (texto si existe, si no número)
                if (is_array($p['respuestas']) && !empty($p['respuestas'])) {
                    $seen = [];
                    $dedup = [];
                    foreach ($p['respuestas'] as $r) {
                        $label = null;
                        if (isset($r['respuesta_texto']) && $r['respuesta_texto'] !== null && $r['respuesta_texto'] !== '') {
                            // Normalizar para evitar duplicados por espacios/caso
                            $norm = function_exists('mb_strtolower') ? mb_strtolower($r['respuesta_texto'], 'UTF-8') : strtolower($r['respuesta_texto']);
                            $label = 'T|' . trim($norm);
                        } elseif (isset($r['respuesta_numero']) && $r['respuesta_numero'] !== null && $r['respuesta_numero'] !== '') {
                            $label = 'N|' . (string)$r['respuesta_numero'];
                        } else {
                            // Si no hay texto ni número, usar el id como fallback
                            $label = 'I|' . (isset($r['id']) ? (string)$r['id'] : uniqid('r_', true));
                        }
                        if (!isset($seen[$label])) {
                            $seen[$label] = true;
                            $dedup[] = $r;
                        }
                    }
                    $p['respuestas'] = $dedup;
                }
            }
            unset($p);

            $payload = json_encode([ 'success' => true, 'data' => $pregs ]);
            // Limpiar buffers y eliminar BOM si existe
            while (ob_get_level() > 0) { ob_end_clean(); }
            if (substr($payload, 0, 3) === "\xEF\xBB\xBF") { $payload = substr($payload, 3); }
            echo $payload;
        } catch (Exception $e) {
            error_log('[CargarEvaluacion] Error: ' . $e->getMessage());
            http_response_code(400);
            $errPayload = json_encode([ 'success' => false, 'data' => [], 'msj' => 'Error: ' . $e->getMessage() ]);
            while (ob_get_level() > 0) { ob_end_clean(); }
            if (substr($errPayload, 0, 3) === "\xEF\xBB\xBF") { $errPayload = substr($errPayload, 3); }
            echo $errPayload;
        }
    }

    // Guardar respuestas de la encuesta
    public function GuardarRespuestas()
    {
        header('Content-Type: application/json');
        try {
            // Verificar que sea una peticiÃ³n POST
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('MÃ©todo no permitido');
            }

            // Obtener datos del POST
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) {
                throw new Exception('Datos JSON invÃ¡lidos');
            }

            $encuesta_id = isset($input['encuesta_id']) ? (int)$input['encuesta_id'] : 0;
            $respuestas = isset($input['respuestas']) ? $input['respuestas'] : [];
            $puntaje_modal = isset($input['puntaje']) ? (float)$input['puntaje'] : null;
            
            if ($encuesta_id <= 0) {
                throw new Exception('encuesta_id invÃ¡lido');
            }
            
            error_log('[Evaluacion::GuardarRespuestas] Puntaje recibido del modal: ' . ($puntaje_modal !== null ? $puntaje_modal : 'no enviado'));

            if (empty($respuestas)) {
                throw new Exception('No hay respuestas para guardar');
            }

            // Obtener informaciÃ³n del usuario desde la sesiÃ³n
            if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
                throw new Exception('Usuario no autenticado');
            }
            
            $alumno_user_id = (int)$_SESSION['user_id'];
            $user_rol = $_SESSION['user_rol'];
            $user_nombre = $_SESSION['user_nombre_completo'];
            
            // Verificar que el usuario sea un alumno (opcional, puedes ajustar segÃºn tus necesidades)
            if ($user_rol !== 'ALUMNO') {
                throw new Exception('Solo los alumnos pueden responder evaluaciones');
            }
            
            if ($alumno_user_id <= 0) {
                throw new Exception('ID de usuario invÃ¡lido');
            }

            // Validar que la encuesta esté vigente por fecha y activa
            $encModel = new Encuestas_model();
            $vigente = $encModel->ObtenerVigentePorId($encuesta_id, /*requireActiva*/ true, /*requireActivo*/ true);
            if (!$vigente) {
                throw new Exception('La evaluación no está vigente');
            }

            // Verificar si el alumno ya respondió esta encuesta
            $respAlumnosModel = new RespuestasAlumnos_model();
            if ($respAlumnosModel->AlumnoYaRespondio($alumno_user_id, $encuesta_id)) {
                throw new Exception('Ya has respondido esta encuesta anteriormente');
            }

            // Guardar todas las respuestas
            $ids_guardados = $respAlumnosModel->GuardarRespuestasEncuesta($alumno_user_id, $encuesta_id, $respuestas);

            // Usar el puntaje del modal si está disponible, si no calcular uno nuevo
            try {
                if ($puntaje_modal !== null && $puntaje_modal >= 0 && $puntaje_modal <= 100) {
                    $nota = $puntaje_modal;
                    error_log('[Evaluacion::GuardarRespuestas] Usando puntaje del modal: ' . $nota);
                } else {
                    $nota = $respAlumnosModel->GuardarCalificacionEncuesta($alumno_user_id, $encuesta_id);
                    if ($nota === null || !is_numeric($nota)) {
                        error_log('[Evaluacion::GuardarRespuestas] Nota inválida calculada: ' . var_export($nota, true));
                        $nota = 0; // Valor por defecto si no se puede calcular
                    }
                    error_log('[Evaluacion::GuardarRespuestas] Nota calculada por el sistema: ' . $nota);
                }
            } catch (Exception $e) {
                // No detener el flujo si falla el cálculo de calificación, registrar para revisión
                error_log('[Evaluacion::GuardarRespuestas] Error al calcular calificación: ' . $e->getMessage());
                $nota = 0; // Valor por defecto en caso de error
            }

            // Obtener institucion_id del usuario si no está disponible en $vigente
            if (empty($vigente['institucion_id'])) {
                try {
                    $conexion = new ClaseConexion();
                    $conexionSql = $conexion->CrearConexion();
                    
                    $stmt = $conexionSql->prepare("SELECT institucion_id FROM usuarios WHERE id = :alumno_user_id");
                    $stmt->bindValue(':alumno_user_id', $alumno_user_id, PDO::PARAM_INT);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();

                    if ($result && !empty($result['institucion_id'])) {
                        $vigente['institucion_id'] = $result['institucion_id'];
                        error_log('[Evaluacion::GuardarRespuestas] institucion_id obtenido de usuarios: ' . $vigente['institucion_id']);
                    } else {
                        error_log('[Evaluacion::GuardarRespuestas] No se encontró institucion_id en usuarios para alumno_user_id: ' . $alumno_user_id);
                        throw new Exception('No se pudo obtener institucion_id desde la tabla usuarios.');
                    }
                } catch (Exception $e) {
                    error_log('[Evaluacion::GuardarRespuestas] Error al obtener institucion_id: ' . $e->getMessage());
                    throw new Exception('Error al obtener institucion_id: ' . $e->getMessage());
                }
            }

            // Validar que institucion_id sea válido antes de continuar
            if (empty($vigente['institucion_id'])) {
                throw new Exception('No se puede guardar la calificación sin un institucion_id válido.');
            }

            // Insertar calificación en la tabla `calificaciones`
            try {
                if (!isset($conexionSql)) {
                    $conexion = new ClaseConexion();
                    $conexionSql = $conexion->CrearConexion();
                }

                // Habilitar el modo estricto en PDO
                $conexionSql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $sql = "INSERT INTO calificaciones (alumno_user_id, curso_id, institucion_id, grado_id, periodo, puntaje, activo) 
                        VALUES (:alumno_user_id, :curso_id, :institucion_id, :grado_id, :periodo, :puntaje, :activo)
                        ON DUPLICATE KEY UPDATE puntaje = :puntaje, activo = :activo";

                $stmt = $conexionSql->prepare($sql);
                $stmt->bindValue(':alumno_user_id', $alumno_user_id, PDO::PARAM_INT);
                $stmt->bindValue(':curso_id', $vigente['curso_id'], PDO::PARAM_INT);
                $stmt->bindValue(':institucion_id', $vigente['institucion_id'], PDO::PARAM_INT);
                $stmt->bindValue(':grado_id', $vigente['grado_id'], PDO::PARAM_INT);
                $stmt->bindValue(':periodo', date('Y-m'), PDO::PARAM_STR);
                $stmt->bindValue(':puntaje', $nota, PDO::PARAM_STR);
                $stmt->bindValue(':activo', 1, PDO::PARAM_INT);

                $stmt->execute();
                $stmt->closeCursor();

                error_log('[Evaluacion::GuardarRespuestas] Calificación guardada exitosamente.');
            } catch (PDOException $e) {
                // Registrar el error SQL en el archivo de log
                $logDir = __DIR__ . '/../../logs';
                $logFile = $logDir . '/evaluacion_log.txt';

                if (!is_dir($logDir)) {
                    mkdir($logDir, 0777, true);
                }

                $logMessage = sprintf(
                    "[%s] Error SQL: %s\nConsulta: %s\nValores: Alumno ID: %d, Curso ID: %d, Institución ID: %d, Grado ID: %d, Periodo: %s, Puntaje: %s\n",
                    date('Y-m-d H:i:s'),
                    $e->getMessage(),
                    $sql,
                    $alumno_user_id,
                    $vigente['curso_id'],
                    $vigente['institucion_id'],
                    $vigente['grado_id'],
                    date('Y-m'),
                    $nota
                );

                file_put_contents($logFile, $logMessage, FILE_APPEND);
                error_log('[Evaluacion::GuardarRespuestas] Error al guardar calificación: ' . $e->getMessage());
            }

            // Registrar en un archivo de log para depuración
            try {
                $logDir = __DIR__ . '/../../logs';
                $logFile = $logDir . '/evaluacion_log.txt';

                // Crear el directorio si no existe
                if (!is_dir($logDir)) {
                    mkdir($logDir, 0777, true);
                }

                $logMessage = sprintf(
                    "[%s] Alumno ID: %d, Curso ID: %d, Institución ID: %d, Grado ID: %d, Periodo: %s, Puntaje: %s\n",
                    date('Y-m-d H:i:s'),
                    $alumno_user_id,
                    $vigente['curso_id'],
                    $vigente['institucion_id'],
                    $vigente['grado_id'],
                    date('Y-m'),
                    $nota
                );

                // Agregar detalles si hubo un error al insertar en la tabla calificaciones
                if (!isset($stmt) || !$stmt) {
                    $logMessage .= "Error: No se pudo preparar o ejecutar la consulta para insertar en la tabla calificaciones.\n";
                }

                file_put_contents($logFile, $logMessage, FILE_APPEND);
            } catch (Exception $e) {
                error_log('[Evaluacion::GuardarRespuestas] Error al escribir en el archivo de log: ' . $e->getMessage());
            }

            // Validar y asignar un valor por defecto a institucion_id si es inválido
            if ($vigente['institucion_id'] === 0 || $vigente['institucion_id'] === null) {
                $logDir = __DIR__ . '/../../logs';
                $logFile = $logDir . '/evaluacion_log.txt';

                if (!is_dir($logDir)) {
                    mkdir($logDir, 0777, true);
                }

                $logMessage = sprintf(
                    "[%s] institucion_id inválido detectado (valor: %s). Asignando valor por defecto.\n",
                    date('Y-m-d H:i:s'),
                    $vigente['institucion_id']
                );

                file_put_contents($logFile, $logMessage, FILE_APPEND);
                error_log('[Evaluacion::GuardarRespuestas] institucion_id inválido detectado. Asignando valor por defecto.');

                // Asignar un valor por defecto
                $vigente['institucion_id'] = 1; // Cambiar este valor según corresponda
            }

            // Obtener institucion_id directamente desde la tabla usuarios si no está disponible en $vigente
            if (empty($vigente['institucion_id'])) {
                try {
                    $stmt = $conexionSql->prepare("SELECT institucion_id FROM usuarios WHERE id = :alumno_user_id");
                    $stmt->bindValue(':alumno_user_id', $alumno_user_id, PDO::PARAM_INT);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();

                    if ($result && !empty($result['institucion_id'])) {
                        $vigente['institucion_id'] = $result['institucion_id'];
                    } else {
                        throw new Exception('No se pudo obtener institucion_id desde la tabla usuarios.');
                    }
                } catch (Exception $e) {
                    $logDir = __DIR__ . '/../../logs';
                    $logFile = $logDir . '/evaluacion_log.txt';

                    if (!is_dir($logDir)) {
                        mkdir($logDir, 0777, true);
                    }

                    $logMessage = sprintf(
                        "[%s] Error al obtener institucion_id: %s\n",
                        date('Y-m-d H:i:s'),
                        $e->getMessage()
                    );

                    file_put_contents($logFile, $logMessage, FILE_APPEND);
                    error_log('[Evaluacion::GuardarRespuestas] ' . $e->getMessage());

                    // Asignar un valor por defecto si no se puede obtener
                    $vigente['institucion_id'] = 1; // Cambiar este valor según corresponda
                }
            }

            $response = json_encode([
                'success' => true,
                'data' => [
                    'total_respuestas_guardadas' => count($ids_guardados),
                    'ids_guardados' => $ids_guardados,
                    'usuario' => [
                        'id' => $alumno_user_id,
                        'nombre' => $user_nombre,
                        'rol' => $user_rol,
                        'institucion' => isset($_SESSION['user_institucion_nombre']) ? $_SESSION['user_institucion_nombre'] : 'No especificada',
                        'grado' => isset($_SESSION['user_grado_nombre']) ? $_SESSION['user_grado_nombre'] : 'No especificado',
                        'seccion' => isset($_SESSION['user_seccion']) ? $_SESSION['user_seccion'] : 'No especificada'
                    ]
                ],
                'msj' => 'Encuesta completada exitosamente',
                'nota' => $nota
            ]);
            while (ob_get_level() > 0) { ob_end_clean(); }
            if (substr($response, 0, 3) === "\xEF\xBB\xBF") { $response = substr($response, 3); }
            echo $response;

        } catch (Exception $e) {
            http_response_code(400);
            $response = json_encode([
                'success' => false, 
                'data' => [], 
                'msj' => 'Error: ' . $e->getMessage()
            ]);
            while (ob_get_level() > 0) { ob_end_clean(); }
            if (substr($response, 0, 3) === "\xEF\xBB\xBF") { $response = substr($response, 3); }
            echo $response;
        }
    }
}
?>

