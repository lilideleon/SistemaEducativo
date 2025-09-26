<?php
class EncuestasController
{
    public function __construct()
    {
        @session_start();
        require_once "core/AuthValidation.php";
        validarRol(['ADMIN']);
        require_once "models/Encuestas.php";
        $data["titulo"] = "Encuestas";
    }

    public function index()
    {
        require_once "views/Encuestas/Encuestas.php";
    }

    public function Listar()
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $m = new Encuestas_model();
            $rows = $m->Listar();
            echo json_encode(['success' => true, 'data' => $rows]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'msj' => $e->getMessage()]);
        }
    }

    public function Obtener()
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);
            if ($id <= 0) throw new Exception('ID inválido');
            $m = new Encuestas_model();
            $row = $m->Obtener($id);
            if (!$row) throw new Exception('Encuesta no encontrada');
            echo json_encode(['success' => true, 'data' => $row]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'msj' => $e->getMessage()]);
        }
    }

    public function Agregar()
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Método no permitido');
            // Extraer y validar campos sin usar operador ternario
            $titulo = '';
            if (isset($_POST['titulo'])) { $titulo = trim($_POST['titulo']); }

            $curso_id = 0;
            if (isset($_POST['curso_id'])) { $curso_id = (int)$_POST['curso_id']; }

            $grado_id = 0;
            if (isset($_POST['grado_id'])) { $grado_id = (int)$_POST['grado_id']; }

            $institucion_id = null;
            if (isset($_POST['institucion_id']) && $_POST['institucion_id'] !== '') { $institucion_id = (int)$_POST['institucion_id']; }

            $descripcion = '';
            if (isset($_POST['descripcion'])) { $descripcion = trim($_POST['descripcion']); }

            $fecha_inicio = null;
            if (isset($_POST['fecha_inicio'])) { $fecha_inicio = $_POST['fecha_inicio']; }

            $fecha_fin = null;
            if (isset($_POST['fecha_fin'])) { $fecha_fin = $_POST['fecha_fin']; }

            $estado = 'ACTIVA';
            if (isset($_POST['estado'])) { $estado = strtoupper($_POST['estado']); }

            $creado_por = 0;
            if (isset($_SESSION['user_id'])) { $creado_por = (int)$_SESSION['user_id']; }

            $unidad_numero = 1;
            if (isset($_POST['unidad_numero'])) { 
                $unidad_numero = (int)$_POST['unidad_numero']; 
                if ($unidad_numero < 1 || $unidad_numero > 4) { 
                    $unidad_numero = 1; 
                }
            }

            $data = [
                'titulo' => $titulo,
                'curso_id' => $curso_id,
                'grado_id' => $grado_id,
                'unidad_numero' => $unidad_numero,
                'institucion_id' => $institucion_id,
                'descripcion' => $descripcion,
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin,
                'estado' => $estado,
                'creado_por' => $creado_por,
            ];
            if ($data['titulo'] === '') throw new Exception('El título es requerido');
            if ($data['curso_id'] <= 0) throw new Exception('curso_id requerido');
            if ($data['grado_id'] <= 0) throw new Exception('grado_id requerido');
            if ($data['creado_por'] <= 0) throw new Exception('Usuario no válido');

            $m = new Encuestas_model();
            $id = $m->Agregar($data);
            echo json_encode(['success' => true, 'msj' => 'Encuesta creada', 'id' => $id]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'msj' => $e->getMessage()]);
        }
    }

    public function Actualizar()
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Método no permitido');
            $id = 0;
            if (isset($_POST['id'])) { $id = (int)$_POST['id']; }
            if ($id <= 0) throw new Exception('ID inválido');
            $titulo = '';
            if (isset($_POST['titulo'])) { $titulo = trim($_POST['titulo']); }

            $curso_id = 0;
            if (isset($_POST['curso_id'])) { $curso_id = (int)$_POST['curso_id']; }

            $grado_id = 0;
            if (isset($_POST['grado_id'])) { $grado_id = (int)$_POST['grado_id']; }

            $institucion_id = null;
            if (isset($_POST['institucion_id']) && $_POST['institucion_id'] !== '') { $institucion_id = (int)$_POST['institucion_id']; }

            $descripcion = '';
            if (isset($_POST['descripcion'])) { $descripcion = trim($_POST['descripcion']); }

            $fecha_inicio = null;
            if (isset($_POST['fecha_inicio'])) { $fecha_inicio = $_POST['fecha_inicio']; }

            $fecha_fin = null;
            if (isset($_POST['fecha_fin'])) { $fecha_fin = $_POST['fecha_fin']; }

            $estado = 'ACTIVA';
            if (isset($_POST['estado'])) { $estado = strtoupper($_POST['estado']); }

            $unidad_numero = 1;
            if (isset($_POST['unidad_numero'])) { 
                $unidad_numero = (int)$_POST['unidad_numero']; 
                if ($unidad_numero < 1 || $unidad_numero > 4) { 
                    $unidad_numero = 1; 
                }
            }

            $data = [
                'titulo' => $titulo,
                'curso_id' => $curso_id,
                'grado_id' => $grado_id,
                'unidad_numero' => $unidad_numero,
                'institucion_id' => $institucion_id,
                'descripcion' => $descripcion,
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin,
                'estado' => $estado,
            ];
            $m = new Encuestas_model();
            $m->Actualizar($id, $data);
            echo json_encode(['success' => true, 'msj' => 'Encuesta actualizada']);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'msj' => $e->getMessage()]);
        }
    }

    public function Eliminar()
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Método no permitido');
            $id = 0;
            if (isset($_POST['id'])) { $id = (int)$_POST['id']; }
            if ($id <= 0) throw new Exception('ID inválido');
            $m = new Encuestas_model();
            $m->Eliminar($id);
            echo json_encode(['success' => true, 'msj' => 'Encuesta eliminada']);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'msj' => $e->getMessage()]);
        }
    }

    // Dropdown helpers
    public function ListarCursos()
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $m = new Encuestas_model();
            echo json_encode(['success' => true, 'data' => $m->ListarCursos()]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'msj' => $e->getMessage()]);
        }
    }
    public function ListarGrados()
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $m = new Encuestas_model();
            echo json_encode(['success' => true, 'data' => $m->ListarGrados()]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'msj' => $e->getMessage()]);
        }
    }
    public function ListarInstituciones()
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $m = new Encuestas_model();
            echo json_encode(['success' => true, 'data' => $m->ListarInstituciones()]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'msj' => $e->getMessage()]);
        }
    }
}
?>
