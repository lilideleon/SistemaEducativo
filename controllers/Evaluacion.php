<?php

class EvaluacionController
{
    public function __construct()
    {
        @session_start();
        require_once "models/Encuestas.php";
        require_once "models/Preguntas.php";
        require_once "models/Respuestas.php";
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
}
?>