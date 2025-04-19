<?php
	
	class DespiecesController {

    public function __construct() {
        @session_start();
        require_once "models/Despieces.php";
        date_default_timezone_set('America/Guatemala');
    }

    public function index() {
        $Ejecuta = new Despieces_model();
        $data["titulo"] = "Despieces";
        require_once "views/Despieces/Despieces.php";
    }

    public function GuardarDespiece() {
        $Ejecuta = new Despieces_model();

        try {
            $Ejecuta->setProductoOrigenId($_POST['ProductoOrigenId']);
            $Ejecuta->setProductoResultadoId($_POST['ProductoResultadoId']);
            $Ejecuta->setCantidad($_POST['Cantidad']);
            $Ejecuta->setEstado(1);
            $Ejecuta->setAuditXML(json_encode([
                "fecha" => date("Y-m-d H:i:s"),
                "usuario" => $_SESSION['Usuario']
            ]));

            $Ejecuta->InsertarDespiece();

            $response['success'] = true;
            $response['msj'] = 'Despiece registrado exitosamente.';
        } catch (Exception $e) {
            $response['success'] = false;
            $response['msj'] = 'Error al registrar el despiece: ' . $e->getMessage();
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function ActualizarDespiece() {
        $Ejecuta = new Despieces_model();

        try {
            $Ejecuta->setId($_POST['EditarIdDespiece']);
            $Ejecuta->setProductoOrigenId($_POST['ProductoOrigenId']);
            $Ejecuta->setProductoResultadoId($_POST['ProductoResultadoId']);
            $Ejecuta->setCantidad($_POST['Cantidad']);
            $Ejecuta->setEstado(1);
            $Ejecuta->setAuditXML(json_encode([
                "fecha" => date("Y-m-d H:i:s"),
                "usuario" => $_SESSION['Usuario']
            ]));

            $Ejecuta->ActualizarDespiece();

            $response['success'] = true;
            $response['msj'] = 'Despiece actualizado exitosamente.';
        } catch (Exception $e) {
            $response['success'] = false;
            $response['msj'] = 'Error al actualizar el despiece: ' . $e->getMessage();
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function EliminarDespiece() {
        $Ejecuta = new Despieces_model();

        try {
            $Ejecuta->setId($_POST['Id']);
            $Ejecuta->EliminarDespiece();

            $response['success'] = true;
            $response['msj'] = 'Despiece eliminado exitosamente.';
        } catch (Exception $e) {
            $response['success'] = false;
            $response['msj'] = 'Error al eliminar el despiece: ' . $e->getMessage();
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    /*
    public function Tabla() {
        $Conexion = new ClaseConexion();
        $ConexionSql = $Conexion->CrearConexion();

        $sTabla = "DespiecesProducto";
        $aColumnas = array("Id", "ProductoOrigenId", "ProductoResultadoId", "Cantidad", "Estado");
        $sIndexColumn = "Id";

        $sLimit = "";
        if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
            $sLimit = "LIMIT " . $_GET['iDisplayStart'] . ", " . $_GET['iDisplayLength'];
        }

        $sOrder = "";
        if (isset($_GET['iSortCol_0'])) {
            $sOrder = "ORDER BY  ";
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

        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => $rResult->rowCount(),
            "iTotalDisplayRecords" => $rResult->rowCount(),
            "aaData" => array()
        );

        while ($aRow = $rResult->fetch(PDO::FETCH_ASSOC)) {
            $row = array();
            foreach ($aColumnas as $col) {
                $row[] = $aRow[$col];
            }

            // Add Edit and Delete buttons
            $row[] = '
                <button class="btn btn-warning btn-sm" onclick="EditarDespiece(' . $aRow['Id'] . ')">
                    <i class="fa fa-edit"></i> Editar
                </button>
                <button class="btn btn-danger btn-sm" onclick="EliminarDespiece(' . $aRow['Id'] . ')">
                    <i class="fa fa-trash"></i> Eliminar
                </button>
            ';

            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }*/

    public function Tabla() {
    $Conexion = new ClaseConexion();
    $ConexionSql = $Conexion->CrearConexion();

    $aColumnas = array("b.Id", "po.Nombre AS ProductoOrigen", "pr.Nombre AS ProductoResultado", "b.Cantidad", "b.Estado");
    $sIndexColumn = "b.Id";

    $sLimit = "";
    if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
        $sLimit = "LIMIT " . $_GET['iDisplayStart'] . ", " . $_GET['iDisplayLength'];
    }

    $sOrder = "";
    if (isset($_GET['iSortCol_0'])) {
        $sOrder = "ORDER BY  ";
        for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
            if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
                $columnaOrden = explode(" AS ", $aColumnas[intval($_GET['iSortCol_' . $i])])[0]; // evitar alias
                $sOrder .= $columnaOrden . " " . $_GET['sSortDir_' . $i] . ", ";
            }
        }
        $sOrder = rtrim($sOrder, ", ");
    }

    $sWhere = "WHERE b.Estado = 1";
    if (!empty($_GET['sSearch'])) {
        $sWhere = "WHERE (";
        foreach ($aColumnas as $col) {
            $columnaLimpia = explode(" AS ", $col)[0];
            $sWhere .= "$columnaLimpia LIKE '%" . $_GET['sSearch'] . "%' OR ";
        }
        $sWhere = rtrim($sWhere, " OR ");
        $sWhere .= ") AND b.Estado = 1";
    }

    $sQuery = "
        SELECT SQL_CALC_FOUND_ROWS 
            b.Id, 
            po.Nombre AS ProductoOrigen, 
            pr.Nombre AS ProductoResultado, 
            b.Cantidad, 
            b.Estado
        FROM DespiecesProducto b
        INNER JOIN Productos po ON b.ProductoOrigenId = po.IdProducto
        INNER JOIN Productos pr ON b.ProductoResultadoId = pr.IdProducto
        $sWhere
        $sOrder
        $sLimit
    ";

    $rResult = $ConexionSql->prepare($sQuery);
    $rResult->execute();

    $output = array(
        "sEcho" => intval($_GET['sEcho']),
        "iTotalRecords" => $rResult->rowCount(),
        "iTotalDisplayRecords" => $rResult->rowCount(),
        "aaData" => array()
    );

    while ($aRow = $rResult->fetch(PDO::FETCH_ASSOC)) {
        $row = array();
        $row[] = $aRow['Id'];
        $row[] = $aRow['ProductoOrigen'];
        $row[] = $aRow['ProductoResultado'];
        $row[] = $aRow['Cantidad'];
        $row[] = $aRow['Estado'] == 1 ? 'Activo' : 'Inactivo';

        $row[] = '
            <button class="btn btn-warning btn-sm" onclick="EditarDespiece(' . $aRow['Id'] . ')">
                <i class="fa fa-edit"></i> Editar
            </button>
            <button class="btn btn-danger btn-sm" onclick="EliminarDespiece(' . $aRow['Id'] . ')">
                <i class="fa fa-trash"></i> Eliminar
            </button>
        ';

        $output['aaData'][] = $row;
    }

    echo json_encode($output);
}


    public function DatosModal() {
        $Ejecuta = new Despieces_model();

        try {
            $Ejecuta->setId($_POST['Id']); // Recibir el ID del despiece desde el frontend

            $datos = $Ejecuta->ConsultarDespiecePorId(); // Llamar al método del modelo para obtener los datos

            echo json_encode($datos); // Devolver los datos en formato JSON
        } catch (Exception $e) {
            echo json_encode([
                "success" => false,
                "msj" => "Error al obtener los datos del despiece: " . $e->getMessage()
            ]);
        }
    }

    public function ObtenerProductosOrigen() {
        $Ejecuta = new Despieces_model();

        try {
            $productosOrigen = $Ejecuta->ObtenerProductosOrigen();

            echo json_encode([
                "success" => true,
                "data" => $productosOrigen
            ]);
        } catch (Exception $e) {
            echo json_encode([
                "success" => false,
                "msj" => "Error al obtener productos origen: " . $e->getMessage()
            ]);
        }
    }

    public function ObtenerProductosResultado() {
        $Ejecuta = new Despieces_model();

        try {
            $productosResultado = $Ejecuta->ObtenerProductosResultado();

            echo json_encode([
                "success" => true,
                "data" => $productosResultado
            ]);
        } catch (Exception $e) {
            echo json_encode([
                "success" => false,
                "msj" => "Error al obtener productos resultado: " . $e->getMessage()
            ]);
        }
    }
}
?>