<?php

class InventarioController {

    public function __construct() {
        @session_start();
        require_once "models/Inventario.php";
        date_default_timezone_set('America/Guatemala'); // Configurar la zona horaria predeterminada
    }

    public function index() {
        $Ejecuta = new Inventario_model();
        $data["titulo"] = "Inventario";
        require_once "views/Inventario/Inventario.php";
    }

    public function GuardarInventario() {
        $Ejecuta = new Inventario_model();

        try {
            $Ejecuta->setProductoId($_POST['ProductoId']);
            $Ejecuta->setCantidad($_POST['Cantidad']);
            $Ejecuta->setAuditXML(json_encode([
                "fecha" => date("Y-m-d H:i:s"),
                "usuario" => $_SESSION['Usuario']
            ]));

            $Ejecuta->InsertarInventario();

            $response['success'] = true;
            $response['msj'] = 'Registro de inventario guardado exitosamente.';
        } catch (Exception $e) {
            $response['success'] = false;
            $response['msj'] = 'Error al guardar el registro de inventario: ' . $e->getMessage();
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function ActualizarInventario() {
        $Ejecuta = new Inventario_model();

        try {
            $Ejecuta->setId($_POST['Id']);
            $Ejecuta->setProductoId($_POST['ProductoId']);
            $Ejecuta->setCantidad($_POST['Cantidad']);
            $Ejecuta->setAuditXML(json_encode([
                "fecha" => date("Y-m-d H:i:s"),
                "usuario" => $_SESSION['Usuario']
            ]));

            $Ejecuta->ActualizarInventario();

            echo json_encode([
                "success" => true,
                "msj" => "Registro de inventario actualizado exitosamente."
            ]);
        } catch (Exception $e) {
            echo json_encode([
                "success" => false,
                "msj" => "Error al actualizar el registro de inventario: " . $e->getMessage()
            ]);
        }
    }

    public function DesactivarInventario() {
        $Ejecuta = new Inventario_model();

        try {
            $Ejecuta->setId($_POST['Id']);
            $Ejecuta->EliminarInventario();

            echo json_encode([
                "success" => true,
                "msj" => "Registro de inventario desactivado exitosamente."
            ]);
        } catch (Exception $e) {
            echo json_encode([
                "success" => false,
                "msj" => "Error al desactivar el registro de inventario: " . $e->getMessage()
            ]);
        }
    }

    public function ConsultarInventarioPorId() {
        $Ejecuta = new Inventario_model();

        try {
            $Ejecuta->setId($_POST['Id']);
            $datos = $Ejecuta->ConsultarInventarioPorId();

            echo json_encode($datos);
        } catch (Exception $e) {
            echo json_encode([
                "success" => false,
                "msj" => "Error al consultar el registro de inventario: " . $e->getMessage()
            ]);
        }
    }

    public function ConsultarProductosActivos() {
        $Ejecuta = new Inventario_model();

        try {
            $productos = $Ejecuta->ConsultarProductosActivos();
            echo json_encode($productos);
        } catch (Exception $e) {
            echo json_encode([
                "success" => false,
                "msj" => "Error al consultar los productos activos: " . $e->getMessage()
            ]);
        }
    }

   public function Tabla()
{
    $Conexion = new ClaseConexion();
    $ConexionSql = $Conexion->CrearConexion();

    $aColumnas = array("i.Id", "p.Nombre AS Producto", "i.Cantidad", "i.Fecha", "i.Estado");
    $sIndexColumn = "i.Id";

    $sLimit = "";
    if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
        $sLimit = "LIMIT " . $_GET['iDisplayStart'] . ", " . $_GET['iDisplayLength'];
    }

    $sOrder = "";
    if (isset($_GET['iSortCol_0'])) {
        $sOrder = "ORDER BY  ";
        for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
            if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
                $colOrden = explode(" AS ", $aColumnas[intval($_GET['iSortCol_' . $i])])[0];
                $sOrder .= $colOrden . " " . $_GET['sSortDir_' . $i] . ", ";
            }
        }
        $sOrder = rtrim($sOrder, ", ");
    }

    $sWhere = "WHERE i.Estado = 1";
    if (!empty($_GET['sSearch'])) {
        $sWhere = "WHERE (";
        foreach ($aColumnas as $col) {
            $colLimpio = explode(" AS ", $col)[0];
            $sWhere .= "$colLimpio LIKE '%" . $_GET['sSearch'] . "%' OR ";
        }
        $sWhere = rtrim($sWhere, " OR ");
        $sWhere .= ") AND i.Estado = 1";
    }

    $sQuery = "
        SELECT SQL_CALC_FOUND_ROWS 
            i.Id,
            p.Nombre AS Producto,
            i.Cantidad,
            i.Fecha,
            i.Estado
        FROM inventario i
        INNER JOIN productos p ON i.ProductoId = p.IdProducto
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
        $row[] = $aRow['Producto'];
        $row[] = $aRow['Cantidad'];
        $row[] = $aRow['Fecha'];
        $row[] = $aRow['Estado'] == 1
            ? '<span class="badge bg-success">Activo</span>'
            : '<span class="badge bg-danger">Inactivo</span>';

        $row[] = '
            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#EditarInventarioModal" onclick="DatosInventario(' . $aRow['Id'] . ')">
                <i class="fa fa-edit"></i> Editar
            </button>
            <button class="btn btn-danger btn-sm" onclick="EliminarInventario(' . $aRow['Id'] . ')">
                <i class="fa fa-trash"></i> Eliminar
            </button>
        ';

        $output['aaData'][] = $row;
    }

    echo json_encode($output);
}

}