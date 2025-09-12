<?php

class MaterialController
{
    public function __construct()
    {
        @session_start();
        require_once "core/AuthValidation.php";
        validarRol(['ADMIN', 'DOCENTE', 'DIRECTOR']);
        require_once "models/Material.php";
        $data["titulo"] = "Material";
    }

    public function index()
    {
        require_once "views/Material/Material.php";
    }

    public function ListarCursos()
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $model = new Material_model();
            $rows = $model->ListarCursos();
            echo json_encode(['success' => true, 'data' => $rows]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'msj' => $e->getMessage()]);
        }
    }

    public function ListarGrados()
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $model = new Material_model();
            $rows = $model->ListarGrados();
            echo json_encode(['success' => true, 'data' => $rows]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'msj' => $e->getMessage()]);
        }
    }

    public function Listar()
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $f = [
                'curso_id'        => isset($_GET['curso_id']) ? (int)$_GET['curso_id'] : null,
                'grado_id'        => isset($_GET['grado_id']) ? (int)$_GET['grado_id'] : null,
                'docente_user_id' => isset($_GET['docente_user_id']) ? (int)$_GET['docente_user_id'] : null,
            ];
            $model = new Material_model();
            $rows = $model->ListarMateriales($f);
            echo json_encode(['success' => true, 'data' => $rows]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'msj' => $e->getMessage()]);
        }
    }

    public function Guardar()
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }

            if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
                throw new Exception('Usuario no autenticado');
            }

            $docente_user_id = (int)$_SESSION['user_id'];
            if ($docente_user_id <= 0) {
                throw new Exception('ID de usuario inválido');
            }

            // Validar campos
            $curso_id      = isset($_POST['curso_id']) ? (int)$_POST['curso_id'] : 0;
            $grado_id      = isset($_POST['grado_id']) ? (int)$_POST['grado_id'] : 0;
            $unidad_numero = isset($_POST['unidad_numero']) && $_POST['unidad_numero'] !== '' ? (int)$_POST['unidad_numero'] : null;
            $unidad_titulo = isset($_POST['unidad_titulo']) ? trim($_POST['unidad_titulo']) : null;
            $anio_lectivo  = isset($_POST['anio_lectivo']) && $_POST['anio_lectivo'] !== '' ? (int)$_POST['anio_lectivo'] : null;
            $titulo        = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
            $descripcion   = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : null;

            // Si no viene curso_id pero sí nombre de curso, buscarlo
            if ($curso_id <= 0 && isset($_POST['curso_nombre'])) {
                $model = new Material_model();
                $curso = $model->ListarCursosPorNombre(trim($_POST['curso_nombre']));
                if ($curso && isset($curso['id'])) {
                    $curso_id = (int)$curso['id'];
                }
            }
            if ($curso_id <= 0)  throw new Exception('curso_id requerido');
            if ($grado_id <= 0)  throw new Exception('grado_id requerido');
            if ($titulo === '')  throw new Exception('titulo requerido');

            if (!isset($_FILES['archivos'])) {
                throw new Exception('Debe adjuntar al menos un archivo');
            }

            $files = $_FILES['archivos'];
            if (!is_array($files['name']) || count($files['name']) === 0) {
                throw new Exception('Debe adjuntar al menos un archivo');
            }

            // Guardar material
            $model = new Material_model();
            $material_id = $model->GuardarMaterial([
                'docente_user_id' => $docente_user_id,
                'curso_id'        => $curso_id,
                'grado_id'        => $grado_id,
                'unidad_numero'   => $unidad_numero,
                'unidad_titulo'   => $unidad_titulo,
                'anio_lectivo'    => $anio_lectivo,
                'titulo'          => $titulo,
                'descripcion'     => $descripcion,
            ]);

            // Carpeta de subida
            $baseDir = "uploads/materiales/" . $material_id;
            if (!is_dir($baseDir)) {
                @mkdir($baseDir, 0777, true);
            }

            $permitidas = [
                'pdf','doc','docx','ppt','pptx','xls','xlsx','txt','zip',
                'png','jpg','jpeg','gif','webp','bmp','svg',
                'mp4','mov','avi','mkv','webm'
            ];
            $maxSize = 20 * 1024 * 1024; // 20MB

            $guardados = [];
            for ($i=0; $i < count($files['name']); $i++) {
                if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;

                $name = $files['name'][$i];
                $size = (int)$files['size'][$i];
                $tmp  = $files['tmp_name'][$i];

                if ($size <= 0 || $size > $maxSize) continue;

                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                if (!in_array($ext, $permitidas)) continue;

                // Sanitizar nombre
                $nombreLimpio = preg_replace('/[^A-Za-z0-9._-]/', '_', $name);
                // Evitar colisión
                $unico = uniqid('', true);
                $destName = $unico . "_" . $nombreLimpio;

                $destRel = $baseDir . "/" . $destName;
                $destAbs = $destRel; // relativo sirve desde public root

                if (!@move_uploaded_file($tmp, $destAbs)) {
                    // intentar ruta absoluta basada en este archivo
                    $alt = __DIR__ . '/../' . $destRel;
                    if (!@move_uploaded_file($tmp, $alt)) {
                        // Si falla, saltar
                        continue;
                    }
                }

                $idArchivo = $model->GuardarArchivo($material_id, $name, $destRel);
                $guardados[] = [
                    'id' => $idArchivo,
                    'url' => $destRel,
                    'nombre_archivo' => $name
                ];
            }

            echo json_encode([
                'success' => true,
                'data' => [
                    'material_id' => $material_id,
                    'archivos' => $guardados
                ]
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'msj' => $e->getMessage()]);
        }
    }

    public function EliminarArchivo()
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            if ($id <= 0) { throw new Exception('id inválido'); }

            $model = new Material_model();
            $ok = $model->EliminarArchivo($id, true);
            echo json_encode(['success' => $ok]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'msj' => $e->getMessage()]);
        }
    }

    public function EliminarMaterial()
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            if ($id <= 0) { throw new Exception('id inválido'); }

            $model = new Material_model();
            $ok = $model->EliminarMaterial($id, true);
            echo json_encode(['success' => $ok]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'msj' => $e->getMessage()]);
        }
    }
}