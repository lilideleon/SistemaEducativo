<?php

class PreguntasController
{
    public function __construct()
    {
        @session_start();
    require_once "core/AuthValidation.php";
    // Permitir acceso a ADMIN y DIRECTOR al módulo de Preguntas
    validarRol(['ADMIN','DIRECTOR']);
        require_once "models/Preguntas.php";
        require_once "models/Respuestas.php";
        require_once "models/Encuestas.php";
        $data["titulo"] = "Preguntas";
    }

    public function index()
    {
        require_once "views/Preguntas/Preguntas.php";
    }

    // Listado de preguntas (para DataTables)
    public function Listar()
    {
        header('Content-Type: application/json');
        try {
            $soloActivas = !isset($_GET['todas']) || $_GET['todas'] !== '1';
            $model = new Preguntas_model();
            $rows = $model->Listar($soloActivas);
            echo json_encode([ 'data' => $rows ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([ 'data' => [], 'msj' => 'Error: ' . $e->getMessage() ]);
        }
    }

    public function Agregar()
    {
        header('Content-Type: application/json');
        $json = [];
        try {
            $encuesta_id = isset($_POST['encuesta_id']) ? (int)$_POST['encuesta_id'] : 0;
            $enunciado   = isset($_POST['enunciado']) ? trim($_POST['enunciado']) : '';
            $tipo        = isset($_POST['tipo']) ? trim($_POST['tipo']) : 'opcion_unica';
            $ponderacion = array_key_exists('ponderacion', $_POST) && $_POST['ponderacion'] !== '' ? (float)$_POST['ponderacion'] : 1.00;
            $orden       = array_key_exists('orden', $_POST) && $_POST['orden'] !== '' ? (int)$_POST['orden'] : null;
            $activo      = array_key_exists('activo', $_POST) ? (int)$_POST['activo'] : 1;

            if ($encuesta_id <= 0) { throw new Exception('encuesta_id inválido'); }
            if ($enunciado === '') { throw new Exception('enunciado es requerido'); }

            $model = new Preguntas_model();
            $idNuevo = $model->Agregar($encuesta_id, $enunciado, $tipo, $ponderacion, $orden, $activo);

            $json['success'] = true;
            $json['id'] = $idNuevo;
            $json['msj'] = 'Pregunta creada correctamente';
        } catch (Exception $e) {
            http_response_code(400);
            $json['success'] = false;
            $json['msj'] = 'Error: ' . $e->getMessage();
        }
        echo json_encode($json);
    }

    public function Modificar()
    {
        header('Content-Type: application/json');
        $json = [];
        try {
            $id          = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $encuesta_id = isset($_POST['encuesta_id']) ? (int)$_POST['encuesta_id'] : 0;
            $enunciado   = isset($_POST['enunciado']) ? trim($_POST['enunciado']) : '';
            $tipo        = isset($_POST['tipo']) ? trim($_POST['tipo']) : 'opcion_unica';
            $ponderacion = array_key_exists('ponderacion', $_POST) && $_POST['ponderacion'] !== '' ? (float)$_POST['ponderacion'] : 1.00;
            $orden       = array_key_exists('orden', $_POST) && $_POST['orden'] !== '' ? (int)$_POST['orden'] : null;
            $activo      = array_key_exists('activo', $_POST) ? (int)$_POST['activo'] : 1;

            if ($id <= 0) { throw new Exception('id inválido'); }
            if ($encuesta_id <= 0) { throw new Exception('encuesta_id inválido'); }
            if ($enunciado === '') { throw new Exception('enunciado es requerido'); }

            $model = new Preguntas_model();
            $ok = $model->Modificar($id, $encuesta_id, $enunciado, $tipo, $ponderacion, $orden, $activo);

            $json['success'] = (bool)$ok;
            $json['msj'] = $ok ? 'Pregunta modificada' : 'No se pudo modificar';
        } catch (Exception $e) {
            http_response_code(400);
            $json['success'] = false;
            $json['msj'] = 'Error: ' . $e->getMessage();
        }
        echo json_encode($json);
    }

    public function Eliminar()
    {
        header('Content-Type: application/json');
        $json = [];
        try {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            if ($id <= 0) { throw new Exception('id inválido'); }

            $model = new Preguntas_model();
            $ok = $model->EliminarLogico($id);
            $json['success'] = (bool)$ok;
            $json['msj'] = $ok ? 'Pregunta eliminada' : 'No se pudo eliminar';
        } catch (Exception $e) {
            http_response_code(400);
            $json['success'] = false;
            $json['msj'] = 'Error: ' . $e->getMessage();
        }
        echo json_encode($json);
    }

    public function AgregarRespuesta()
    {
        header('Content-Type: application/json');
        $json = [];
        try {
            $pregunta_id      = isset($_POST['pregunta_id']) ? (int)$_POST['pregunta_id'] : 0;
            $respuesta_texto  = isset($_POST['respuesta_texto']) && $_POST['respuesta_texto'] !== '' ? trim($_POST['respuesta_texto']) : null;
            $respuesta_numero = isset($_POST['respuesta_numero']) && $_POST['respuesta_numero'] !== '' ? (float)$_POST['respuesta_numero'] : null;
            $es_correcta      = array_key_exists('es_correcta', $_POST) ? (int)$_POST['es_correcta'] : 0;
            $activo           = array_key_exists('activo', $_POST) ? (int)$_POST['activo'] : 1;

            if ($pregunta_id <= 0) { throw new Exception('pregunta_id inválido'); }
            if ($respuesta_texto === null && $respuesta_numero === null) { throw new Exception('Debe enviar texto o número para la respuesta'); }

            $model = new Respuestas_model();
            $idNuevo = $model->Agregar($pregunta_id, $respuesta_texto, $respuesta_numero, $es_correcta, $activo);
            if (!$idNuevo) {
                throw new Exception('No se pudo crear la respuesta (verifique pregunta_id existente)');
            }
            $json['success'] = true;
            $json['id'] = $idNuevo;
            $json['msj'] = 'Respuesta creada correctamente';
        } catch (Exception $e) {
            http_response_code(400);
            $json['success'] = false;
            $json['msj'] = 'Error: ' . $e->getMessage();
        }
        echo json_encode($json);
    }

    // Listar respuestas de una pregunta
    public function ListarRespuestas()
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $pregunta_id = isset($_GET['pregunta_id']) ? (int)$_GET['pregunta_id'] : 0;
            if ($pregunta_id <= 0) { throw new Exception('pregunta_id inválido'); }
            $soloActivas = !isset($_GET['todas']) || $_GET['todas'] !== '1';
            $model = new Respuestas_model();
            $rows = $model->ListarPorPregunta($pregunta_id, $soloActivas);
            echo json_encode([ 'success' => true, 'data' => $rows ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([ 'success' => false, 'data' => [], 'msj' => 'Error: ' . $e->getMessage() ], JSON_UNESCAPED_UNICODE);
        }
    }

    // Modificar respuesta
    public function ModificarRespuesta()
    {
        header('Content-Type: application/json');
        $json = [];
        try {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            if ($id <= 0) { throw new Exception('id inválido'); }

            $respuesta_texto  = array_key_exists('respuesta_texto', $_POST) ? trim($_POST['respuesta_texto']) : null;
            $respuesta_numero = array_key_exists('respuesta_numero', $_POST) && $_POST['respuesta_numero'] !== '' ? (float)$_POST['respuesta_numero'] : null;
            $es_correcta      = array_key_exists('es_correcta', $_POST) ? (int)$_POST['es_correcta'] : 0;
            $activo           = array_key_exists('activo', $_POST) ? (int)$_POST['activo'] : 1;

            $model = new Respuestas_model();
            // Modificar campos de la respuesta
            $ok = $model->Modificar($id, $respuesta_texto, $respuesta_numero, $es_correcta, $activo);
            // Si se marca como correcta, asegurar que sea la única correcta para la pregunta
            if ($ok) {
                if ((int)$es_correcta === 1) {
                    $model->SetCorrecta($id, 1);
                } else {
                    // Si explícitamente se marcó como NO correcta, garantizar que quede en 0
                    $model->SetCorrecta($id, 0);
                }
            }

            $json['success'] = (bool)$ok;
            $json['msj'] = $ok ? 'Respuesta modificada' : 'No se pudo modificar';
        } catch (Exception $e) {
            http_response_code(400);
            $json['success'] = false;
            $json['msj'] = 'Error: ' . $e->getMessage();
        }
        echo json_encode($json);
    }

    // Listar encuestas para el select (solo activas y estado ACTIVA por defecto)
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
}
?>
