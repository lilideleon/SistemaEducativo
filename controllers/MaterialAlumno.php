<?php
class MaterialAlumnoController
{
    public function __construct()
    {
        @session_start();
        require_once "core/AuthValidation.php";
        // Solo alumnos pueden acceder a esta vista
        validarRol(['ALUMNO']);
        require_once "models/Material.php";
    }

    public function index()
    {
        require_once "views/MaterialAlumno/MaterialAlumno.php";
    }

    // Lista materiales publicados para el alumno (solo lectura)
    public function Listar()
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $m = new Material_model();
            // Por defecto listar todo como en el módulo Material; permitir filtro por curso si viene
            $f = [];
            if (isset($_GET['curso_id']) && (int)$_GET['curso_id'] > 0) {
                $f['curso_id'] = (int)$_GET['curso_id'];
            }
            $rows = $m->ListarMateriales($f);
            echo json_encode(['success' => true, 'data' => $rows]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'msj' => $e->getMessage()]);
        }
    }

    public function ListarCursos()
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $m = new Material_model();
            $rows = $m->ListarCursos();
            echo json_encode(['success' => true, 'data' => $rows]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'msj' => $e->getMessage()]);
        }
    }
}
?>
