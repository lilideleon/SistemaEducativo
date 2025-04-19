<?php

class CategoriasController
{
    public function __construct()
    {
        @session_start();
        require_once "models/Categorias.php"; // Cargar el modelo de Categorías
        date_default_timezone_set('America/Mexico_City'); // Establecer la zona horaria
    }

    // Método para mostrar la vista principal de categorías
    public function index()
    {
        $data["titulo"] = "Categorías";
        require_once "views/Categorias/Categorias.php";
    }

    // Método para mostrar el formulario de nueva categoría
    public function Nuevo()
    {
        $data["titulo"] = "Nueva Categoría";
        require_once "views/Categorias/Categorias_Nuevo.php";
    }

    // Método para registrar una nueva categoría con un AuditXML predefinido
    public function Registrar()
    {
        $categoria = new Categorias_model();

        // Asignar los valores recibidos del formulario
        $categoria->setNombre($_POST['Nombre']);
        $categoria->setUnidades($_POST['Unidades']);
        $auditXML = '<audit>';
        $auditXML .= '<fecha>' . date('Y-m-d H:i:s') . '</fecha>';
        $auditXML .= '<usuario>' . $_SESSION['IdUsuario'] . '</usuario>';
        $auditXML .= '<accion>Registro de categoría</accion>';
        $auditXML .= '</audit>';
        $categoria->setAuditXML($auditXML);

        $response = array();

        try {
            // Insertar la categoría
            $categoria->InsertarCategoria();

            // Respuesta de éxito
            $response['success'] = true;
            $response['message'] = 'Categoría registrada exitosamente.';
        } catch (Exception $e) {
            // Respuesta de error
            $response['success'] = false;
            $response['message'] = 'Error al registrar la categoría: ' . $e->getMessage();
        }

        echo json_encode($response);
    }

    // Método para mostrar los datos de una categoría antes de actualizar
    public function ObtenerCategoria()
    {
        $categoria = new Categorias_model();
        $idCategoria = $_GET['IdCategoria'];

        $response = array();

        try {
            // Obtener los datos de la categoría
            $data = $categoria->ObtenerCategoriaPorId($idCategoria);

            if ($data) {
                $response['success'] = true;
                $response['data'] = $data;
            } else {
                $response['success'] = false;
                $response['message'] = 'Categoría no encontrada.';
            }
        } catch (Exception $e) {
            $response['success'] = false;
            $response['message'] = 'Error al obtener la categoría: ' . $e->getMessage();
        }

        echo json_encode($response);
    }

    // Método para actualizar una categoría
    public function ActualizarCategoria()
    {
        $categoria = new Categorias_model();

        // Asignar los valores recibidos del formulario
        $categoria->setIdCategoria($_POST['IdCategoria']);
        $categoria->setNombre($_POST['Nombre']);
        $categoria->setUnidades($_POST['Unidades']);
        $categoria->setEstado($_POST['Estado']);
        $categoria->setAuditXML($_POST['AuditXML']); // AuditXML opcional

        $response = array();

        try {
            // Actualizar la categoría
            $categoria->ActualizarCategoria();

            // Respuesta de éxito
            $response['success'] = true;
            $response['message'] = 'Categoría actualizada exitosamente.';
        } catch (Exception $e) {
            // Respuesta de error
            $response['success'] = false;
            $response['message'] = 'Error al actualizar la categoría: ' . $e->getMessage();
        }

        echo json_encode($response);
    }

    // Método para actualizar una categoría con un AuditXML predefinido
    public function Actualizar()
    {
        $categoria = new Categorias_model();

        // Asignar los valores recibidos del formulario
        $categoria->setIdCategoria($_POST['IdCategoria']);
        $categoria->setNombre($_POST['Nombre']);
        $categoria->setUnidades($_POST['Unidades']);
        $categoria->setEstado($_POST['Estado']);
        $categoria->setAuditXML('<audit>Actualización de categoría</audit>'); // Ejemplo de AuditXML

        $response = array();

        try {
            // Actualizar la categoría
            $categoria->ActualizarCategoria();

            // Respuesta de éxito
            $response['success'] = true;
            $response['message'] = 'Categoría actualizada exitosamente.';
        } catch (Exception $e) {
            // Respuesta de error
            $response['success'] = false;
            $response['message'] = 'Error al actualizar la categoría: ' . $e->getMessage();
        }

        echo json_encode($response);
    }

    // Método para eliminar (desactivar) una categoría
    public function Eliminar()
    {
        $categoria = new Categorias_model();

        // Asignar los valores recibidos del formulario
        $categoria->setIdCategoria($_POST['IdCategoria']);
        $categoria->setAuditXML('<audit>Eliminación de categoría</audit>'); // Ejemplo de AuditXML

        $response = array();

        try {
            // Eliminar la categoría
            $categoria->EliminarCategoria();

            // Respuesta de éxito
            $response['success'] = true;
            $response['message'] = 'Categoría eliminada exitosamente.';
        } catch (Exception $e) {
            // Respuesta de error
            $response['success'] = false;
            $response['message'] = 'Error al eliminar la categoría: ' . $e->getMessage();
        }

        echo json_encode($response);
    }

    // Método para cargar las categorías en una tabla
    public function Tabla()
    {
        $Conexion = new ClaseConexion();
        $ConexionSql = $Conexion->CrearConexion();

        $sTabla = "Categorias";
        $aColumnas = array("IdCategoria", "Nombre", "Unidades", "Estado");

        $sLimit = "";
        if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
            $sLimit = "LIMIT " . $_GET['iDisplayStart'] . ", " . $_GET['iDisplayLength'];
        }

        $sOrder = "";
        if (isset($_GET['iSortCol_0'])) {
            $sOrder = "ORDER BY ";
            for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
                if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
                    $sOrder .= $aColumnas[intval($_GET['iSortCol_' . $i])] . " " . $_GET['sSortDir_' . $i] . ", ";
                }
            }
            $sOrder = substr_replace($sOrder, "", -2);
        }

        $sWhere = "WHERE Estado = 1";
        if ($_GET['sSearch'] != "") {
            $sWhere = "WHERE (";
            for ($i = 0; $i < count($aColumnas); $i++) {
                $sWhere .= $aColumnas[$i] . " LIKE '%" . $_GET['sSearch'] . "%' OR ";
            }
            $sWhere = substr_replace($sWhere, "", -3);
            $sWhere .= ') AND Estado = 1';
        }

        $sQuery = "
            SELECT SQL_CALC_FOUND_ROWS " . implode(", ", $aColumnas) . "
            FROM $sTabla
            $sWhere
            $sOrder
            $sLimit
        ";

        $rResult = $ConexionSql->prepare($sQuery);
        $rResult->execute();

        $iTotal = $ConexionSql->query("SELECT FOUND_ROWS()")->fetchColumn();

        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iTotal,
            "aaData" => array()
        );

        while ($aRow = $rResult->fetch(PDO::FETCH_ASSOC)) {
            $row = array();
            $row["IdCategoria"] = $aRow["IdCategoria"];
            $row["Nombre"] = $aRow["Nombre"];
            $row["Unidades"] = $aRow["Unidades"];
            $row["Estado"] = $aRow["Estado"] == 1
                ? '<span class="badge bg-success">Activo</span>'
                : '<span class="badge bg-danger">Inactivo</span>';
            $row["Acciones"] = '
                <button class="btn btn-warning btn-sm" onclick="CargarDatosCategoria(' . $aRow['IdCategoria'] . ', \'' . $aRow['Nombre'] . '\', \'' . $aRow['Unidades'] . '\', ' . $aRow['Estado'] . ')">
                    <i class="fa fa-edit"></i> Editar
                </button>
                <button class="btn btn-danger btn-sm" onclick="EliminarCategoria(' . $aRow['IdCategoria'] . ')">
                    <i class="fa fa-trash"></i> Eliminar
                </button>
            ';
            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }
}
?>