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
            $pregs = $pregModel->ListarPorEncuestaRandom($encuesta_id, true);
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
            
            if ($encuesta_id <= 0) {
                throw new Exception('encuesta_id invÃ¡lido');
            }

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

            // Calcular y guardar calificación de la encuesta para el alumno (nota 0..100)
            try {
                $nota = $respAlumnosModel->GuardarCalificacionEncuesta($alumno_user_id, $encuesta_id);
                error_log('[Evaluacion::GuardarRespuestas] Nota calculada: ' . $nota);
            } catch (Exception $e) {
                // No detener el flujo si falla el cálculo de calificación, registrar para revisión
                error_log('[Evaluacion::GuardarRespuestas] Error al calcular calificación: ' . $e->getMessage());
                $nota = null;
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

