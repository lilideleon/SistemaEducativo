<?php

class ComprasController {

    public function __construct() {
        @session_start();
        require_once "models/Compras.php";
        date_default_timezone_set('America/Guatemala'); // Configurar la zona horaria predeterminada
    }

    public function index() {
        $Ejecuta = new Compras_model();
        $data["titulo"] = "Compras";
        require_once "views/Compras/Compras.php";
    }

    public function GuardarCompra() {
        $Ejecuta = new Compras_model();

        try {
            $Detalles = json_decode($_POST['Detalles'], true); // Decodificar los detalles enviados como JSON

            $CompraId = $Ejecuta->InsertarCompra(
                $_POST['Fecha'],
                $_POST['Hora'],
                $_POST['Proveedor'],
                $_SESSION['Codigo'],
                $_POST['Total'],
                $_POST['Observaciones'],
                json_encode([
                    "fecha" => date("Y-m-d H:i:s"),
                    "usuario" => $_SESSION['Usuario']
                ])
            );

            foreach ($Detalles as $detalle) {
                $Ejecuta->InsertarDetalleCompra($CompraId, $detalle['ProductoId'], $detalle['Cantidad'], $detalle['PrecioUnitario']);
            }

            $response['success'] = true;
            $response['msj'] = 'Compra registrada exitosamente.';
        } catch (Exception $e) {
            $response['success'] = false;
            $response['msj'] = 'Error al registrar la compra: ' . $e->getMessage();
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    //metodo para obtener el id del producto y el nombre
    //se le pasa el nombre del producto y se obtiene el idProducto y el nombre
    
    
   public function ObtenerProducto() {
    $Ejecuta = new Compras_model();
    $nombreProducto = $_POST['nombreProducto'];
    $result = $Ejecuta->ObtenerProducto($nombreProducto);
    header('Content-Type: application/json');
    echo json_encode($result);
}


    public function Tabla()
    {
        $Conexion = new ClaseConexion();
        $ConexionSql = $Conexion->CrearConexion();

        $aColumnas = array("a.Id", "a.Fecha", "a.Hora", "a.Proveedor", "a.Total", "a.Estado");
        $sIndexColumn = "a.Id";

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

        $sWhere = "";
        if (!empty($_GET['sSearch'])) {
            $sWhere = "WHERE (";
            foreach ($aColumnas as $col) {
                $colLimpio = explode(" AS ", $col)[0];
                $sWhere .= "$colLimpio LIKE '%" . $_GET['sSearch'] . "%' OR ";
            }
            $sWhere = rtrim($sWhere, " OR ");
            $sWhere .= ")";
        }

        $sQuery = "
            SELECT SQL_CALC_FOUND_ROWS
                a.Id,
                a.Fecha,
                a.Hora,
                a.Proveedor,
                a.Total,
                a.Estado
            FROM Compras a
            $sWhere
            $sOrder
            $sLimit
        ";

        $rResult = $ConexionSql->prepare($sQuery);
        $rResult->execute();

        $sQueryContador = "SELECT FOUND_ROWS()";
        $rResultContador = $ConexionSql->prepare($sQueryContador);
        $rResultContador->execute();
        $iTotalRecords = $rResultContador->fetchColumn();
        $iTotalDisplayRecords = $iTotalRecords;

        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => $iTotalRecords,
            "iTotalDisplayRecords" => $iTotalDisplayRecords,
            "aaData" => array()
        );

        while ($aRow = $rResult->fetch(PDO::FETCH_ASSOC)) {
            $row = array();
            $row[] = $aRow['Id'];
            $row[] = $aRow['Fecha'];
            $row[] = $aRow['Hora'];
            // Validamos si el proveedor es igual a 1
            if ($aRow['Proveedor'] == 1) {
                $row[] = '<span class="badge bg-secondary">Proveedor General</span>';
            } else {
                $row[] = $aRow['Proveedor'];
            }
            $row[] = number_format($aRow['Total'], 2);

            // Validamos si el estado es igual a 1 (Activo) o 0 (Inactivo) si es estado 3 es compra procesada

            if ($aRow['Estado'] == 3) {
                $row[] = '<span class="badge bg-success">Procesada</span>';
            } else if ($aRow['Estado'] == 0) {
                $row[] = '<span class="badge bg-danger">Anulada</span>';
            } else if ($aRow['Estado'] == 1) {
                $row[] = '<span class="badge bg-info">Activa</span>';
            }
           

            $row[] = '
                    <button class="btn btn-info btn-sm" onclick="VerDetallesCompra(' . $aRow['Id'] . ')">
                        <i class="fa fa-eye"></i> Info
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="EliminarCompra(' . $aRow['Id'] . ')">
                        <i class="fa fa-trash"></i> Eliminar
                    </button>

                    <button class="btn btn-success btn-sm" onclick="ProcesarCompra(' . $aRow['Id'] . ')">
                        <i class="fa fa-ban"></i> Procesar
                    </button>
                ';




            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function DetalleCompra() {
        $modelo = new Compras_model();
        $id = $_POST['compraId'];
        $datos = $modelo->ObtenerCompraPorId($id);

        header('Content-Type: application/json');
        echo json_encode($datos);
    }


    public function AnularCompra() {
        $Ejecuta = new Compras_model();

        try {
            $CompraId = $_POST['CompraId'];
            $AuditXml = json_encode([
                "fecha" => date("Y-m-d H:i:s"),
                "usuario" => $_SESSION['Codigo'],
                "accion" => "anulacion"
            ]);

            $Ejecuta->AnularCompra($CompraId, $AuditXml);

            $response['success'] = true;
            $response['msj'] = 'Compra anulada correctamente.';
        } catch (Exception $e) {
            $response['success'] = false;
            $response['msj'] = 'Error al anular la compra: ' . $e->getMessage();
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    //procesar la compra para aumentar el inventario

    public function ProcesarCompra() {
        $Ejecuta = new Compras_model();

        try {
            $CompraId = $_POST['CompraId'];
            $AuditXml = json_encode([
                "fecha" => date("Y-m-d H:i:s"),
                "usuario" => $_SESSION['Codigo'],
                "accion" => "procesar"
            ]);

            $Ejecuta->ProcesarCompra($CompraId, $AuditXml);
            $response['success'] = true;
            $response['msj'] = 'Compra procesada correctamente.';
        } catch (Exception $e) {
            $response['success'] = false;
            $response['msj'] = 'Error al procesar la compra: ' . $e->getMessage();
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

}