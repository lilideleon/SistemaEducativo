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
    
        $aColumnas = array("i.Id", "p.Nombre", "i.Cantidad", "i.Fecha", "i.Estado");
        $sIndexColumn = "i.Id";
    
        // LIMIT
        $sLimit = "";
        if (isset($_GET['start']) && $_GET['length'] != '-1') {
            $sLimit = "LIMIT " . intval($_GET['start']) . ", " . intval($_GET['length']);
        }
    
        // ORDER BY
        $sOrder = "";
        if (isset($_GET['order'][0]['column'])) {
            $colIdx = intval($_GET['order'][0]['column']);
            $dir = $_GET['order'][0]['dir'];
            $colOrden = explode(" AS ", $aColumnas[$colIdx])[0];
            $sOrder = "ORDER BY $colOrden $dir";
        }
    
        // WHERE (filtro global)
        $sWhere = "WHERE i.Estado = 1";
        if (!empty($_GET['search']['value'])) {
            $search = $_GET['search']['value'];
            $sWhere .= " AND (";
            foreach ($aColumnas as $col) {
                $colLimpio = explode(" AS ", $col)[0];
                $sWhere .= "$colLimpio LIKE '%$search%' OR ";
            }
            $sWhere = rtrim($sWhere, " OR ") . ")";
        }
    
        // Consulta principal con FOUND_ROWS
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
    
        // Total de registros filtrados
        $rTotalFiltrado = $ConexionSql->query("SELECT FOUND_ROWS()")->fetchColumn();
    
        // Total de registros sin filtro
        $rTotal = $ConexionSql->query("SELECT COUNT(i.Id) FROM inventario i WHERE i.Estado = 1")->fetchColumn();
    
        // Formato de salida compatible con DataTables
        $output = array(
            "draw" => intval($_GET['draw']),
            "recordsTotal" => intval($rTotal),
            "recordsFiltered" => intval($rTotalFiltrado),
            "data" => array()
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
            $output['data'][] = $row;
        }
    
        header('Content-Type: application/json');
        echo json_encode($output);
    }
    
}