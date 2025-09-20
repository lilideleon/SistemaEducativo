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
        
        // Verificar autenticación
        validarRol(['ALUMNO']);
        
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
            $rows = $model->Listar($soloActivas, $soloEstadoActiva);
            echo json_encode([ 'success' => true, 'data' => $rows ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([ 'success' => false, 'data' => [], 'msj' => 'Error: ' . $e->getMessage() ]);
        }
    }

    // Cargar evaluación: preguntas aleatorias con sus respuestas por encuesta
    public function CargarEvaluacion()
    {
        header('Content-Type: application/json');
        try {
            $encuesta_id = isset($_GET['encuesta_id']) ? (int)$_GET['encuesta_id'] : 0;
            if ($encuesta_id <= 0) { throw new Exception('encuesta_id inválido'); }

            $pregModel = new Preguntas_model();
            $respModel = new Respuestas_model();
            $pregs = $pregModel->ListarPorEncuestaRandom($encuesta_id, true);
            // Adjuntar respuestas activas
            foreach ($pregs as &$p) {
                $p['respuestas'] = $respModel->ListarPorPregunta($p['id'], true);
            }
            unset($p);

            echo json_encode([ 'success' => true, 'data' => $pregs ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([ 'success' => false, 'data' => [], 'msj' => 'Error: ' . $e->getMessage() ]);
        }
    }

    // Guardar respuestas de la encuesta
    public function GuardarRespuestas()
    {
        header('Content-Type: application/json');
        try {
            // Verificar que sea una petición POST
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }

            // Obtener datos del POST
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) {
                throw new Exception('Datos JSON inválidos');
            }

            $encuesta_id = isset($input['encuesta_id']) ? (int)$input['encuesta_id'] : 0;
            $respuestas = isset($input['respuestas']) ? $input['respuestas'] : [];
            
            if ($encuesta_id <= 0) {
                throw new Exception('encuesta_id inválido');
            }

            if (empty($respuestas)) {
                throw new Exception('No hay respuestas para guardar');
            }

            // Obtener información del usuario desde la sesión
            if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
                throw new Exception('Usuario no autenticado');
            }
            
            $alumno_user_id = (int)$_SESSION['user_id'];
            $user_rol = $_SESSION['user_rol'];
            $user_nombre = $_SESSION['user_nombre_completo'];
            
            // Verificar que el usuario sea un alumno (opcional, puedes ajustar según tus necesidades)
            if ($user_rol !== 'ALUMNO') {
                throw new Exception('Solo los alumnos pueden responder evaluaciones');
            }
            
            if ($alumno_user_id <= 0) {
                throw new Exception('ID de usuario inválido');
            }

            // Verificar si el alumno ya respondió esta encuesta
            $respAlumnosModel = new RespuestasAlumnos_model();
            if ($respAlumnosModel->AlumnoYaRespondio($alumno_user_id, $encuesta_id)) {
                throw new Exception('Ya has respondido esta encuesta anteriormente');
            }

            // Guardar todas las respuestas
            $ids_guardados = $respAlumnosModel->GuardarRespuestasEncuesta($alumno_user_id, $encuesta_id, $respuestas);

            echo json_encode([
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
                'msj' => 'Encuesta completada exitosamente'
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false, 
                'data' => [], 
                'msj' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
}
?>
