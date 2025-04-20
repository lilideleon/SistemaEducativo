<?php
	
	class ventasController 
	{
		
		public function __construct(){
			@session_start();
			require_once "models/Ventas.php";
			$data["titulo"] = "Ventas";
			date_default_timezone_set('America/Guatemala'); // Configurar la zona horaria predeterminada
		}
		
		public function index(){
			require_once "views/ventas/ventas.php";	
		}

		public function Nuevo ()
		{
			require_once "views/ventas/Nuevaventa.php";
		}


		public function GuardarFactura() {
			$Ejecuta = new Ventas_model();

			try {
				// Obtener datos desde POST
				$Ejecuta->setIdCliente($_POST['IdCliente']);
				$Ejecuta->setFechaHora(date("Y-m-d"));
				$Ejecuta->setHora(date("H:i:s"));
				$Ejecuta->setAuditXML(json_encode([
					"fecha" => date("Y-m-d H:i:s"),
					"usuario" => $_SESSION['Usuario']
				]));

				$facturaId = $Ejecuta->insertarFactura();

				$response['success'] = true;
				$response['msj'] = 'Factura guardada exitosamente.';
				$response['facturaId'] = $facturaId;
			} catch (Exception $e) {
				$response['success'] = false;
				$response['msj'] = 'Error al guardar la factura: ' . $e->getMessage();
			}

			header('Content-Type: application/json');
			echo json_encode($response);
		}

		public function Guardardetallefactura() {
			$Ejecuta = new Ventas_model();

			try {
				// Obtener datos desde POST
				$Ejecuta->setIdFactura($_POST['IdFactura']);
				$Ejecuta->setIdArticulo($_POST['IdArticulo']);
				$Ejecuta->setCantidad($_POST['Cantidad']);
				$Ejecuta->setPrecioVenta($_POST['PrecioVenta']);
				$Ejecuta->setAuditXML(json_encode([
					"fecha" => date("Y-m-d H:i:s"),
					"usuario" => $_SESSION['Usuario']
				]));

				$Ejecuta->insertarDetalleFacturaYDescontar();

				$response['success'] = true;
				$response['msj'] = 'Detalle de factura guardado exitosamente.';
			} catch (Exception $e) {
				$response['success'] = false;
				$response['msj'] = 'Error al guardar el detalle de la factura: ' . $e->getMessage();
			}

			header('Content-Type: application/json');
			echo json_encode($response);
		}

        public function ObtenerProductos() {
            $Ejecuta = new Ventas_model();

            try {
                $productos = $Ejecuta->obtenerProductos();

                $response['success'] = true;
                $response['productos'] = $productos;
            } catch (Exception $e) {
                $response['success'] = false;
                $response['msj'] = 'Error al obtener los productos: ' . $e->getMessage();
            }

            header('Content-Type: application/json');
            echo json_encode($response);
        }

        public function ObtenerFacturaPorId() {
            $Ejecuta = new Ventas_model();

            try {
                // Obtener el ID de la factura desde GET
                $idFactura = $_GET['IdFactura'];

                // Llamar al método del modelo
                $factura = $Ejecuta->obtenerFacturaPorId($idFactura);

                $response['success'] = true;
                $response['factura'] = $factura;
            } catch (Exception $e) {
                $response['success'] = false;
                $response['msj'] = 'Error al obtener la factura: ' . $e->getMessage();
            }

            header('Content-Type: application/json');
            echo json_encode($response);
        }
		


		

	public function Tabla()
	{
		$Conexion = new ClaseConexion();
		$ConexionSql = $Conexion->CrearConexion();

		$sTabla = "factura";
		$aColumnas = array("Id", "Fecha", "Hora", "ClienteId", "Total", "Estado");
		$sIndexColumn = "Id";

		$sLimit = "";
		if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
			$sLimit = "LIMIT " . intval($_GET['iDisplayStart']) . ", " . intval($_GET['iDisplayLength']);
		}

		$sOrder = "";
		if (isset($_GET['iSortCol_0'])) {
			$sOrder = "ORDER BY Id DESC, "; // Ordenamos por Id por defecto y luego por las columnas solicitadas
			for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
				if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
					$sOrder .= $aColumnas[intval($_GET['iSortCol_' . $i])] . " " . $_GET['sSortDir_' . $i] . ", ";
				}
			}
			$sOrder = rtrim($sOrder, ", "); // Eliminar la coma final
		} else {
			$sOrder = "ORDER BY Id DESC"; // Orden por defecto si no se solicita otro
		}

		$sWhere = "";
		if (!empty($_GET['sSearch'])) {
			$sWhere = "WHERE (";
			for ($i = 0; $i < count($aColumnas); $i++) {
				$sWhere .= $aColumnas[$i] . " LIKE '%" . $_GET['sSearch'] . "%' OR ";
			}
			$sWhere = rtrim($sWhere, " OR ");
			$sWhere .= ')';
		}

		// Consulta para obtener los datos paginados y filtrados
		$sQuery = "
			SELECT SQL_CALC_FOUND_ROWS " . implode(", ", $aColumnas) . "
			FROM $sTabla
			$sWhere
			$sOrder
			$sLimit
		";

		$rResult = $ConexionSql->prepare($sQuery);
		$rResult->execute();
		$aResult = $rResult->fetchAll(PDO::FETCH_ASSOC);

		// Consulta para obtener el total de registros filtrados
		$sQueryFiltrado = "SELECT FOUND_ROWS()";
		$rResultFiltrado = $ConexionSql->prepare($sQueryFiltrado);
		$rResultFiltrado->execute();
		$iFilteredTotal = $rResultFiltrado->fetchColumn();

		// Consulta para obtener el total de registros sin filtrar
		$sQueryTotal = "SELECT COUNT(" . $sIndexColumn . ") FROM $sTabla";
		$rResultTotal = $ConexionSql->prepare($sQueryTotal);
		$rResultTotal->execute();
		$iTotal = $rResultTotal->fetchColumn();

		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $iTotal,
			"iTotalDisplayRecords" => $iFilteredTotal,
			"aaData" => array()
		);

		foreach ($aResult as $aRow) {
			$row = array();
			foreach ($aColumnas as $col) {
				if ($col === "Estado") {
					$row[] = $aRow[$col] == 1
						? '<span class="badge bg-success">Activo</span>'
						: '<span class="badge bg-danger">Inactivo</span>';
				} elseif ($col === "ClienteId" && $aRow[$col] == 1) {
					$row[] = '<span class="badge bg-info">Cliente General</span>';
				} else {
					$row[] = $aRow[$col];
				}
			}

			// Agregar botones de Editar y Eliminar
			$row[] = '
				<button class="btn btn-warning btn-sm" onclick="DatosFactura(' . $aRow['Id'] . ')">
					<i class="fa fa-eye"></i> Ver
				</button>
				<button class="btn btn-danger btn-sm" onclick="AnularFactura(' . $aRow['Id'] . ')">
					<i class="fa fa-trash"></i> Anular
				</button>
			';

			$output['aaData'][] = $row;
		}

		echo json_encode($output);
	}

	public function AnularFactura() {
            $Ejecuta = new Ventas_model();

            try {
                // Obtener datos desde POST
                $idFactura = $_POST['IdFactura'];
                $auditXML = json_encode([
                    "fecha" => date("Y-m-d H:i:s"),
                    "usuario" => $_SESSION['Usuario']
                ]);

                // Llamar al método del modelo
                $resultado = $Ejecuta->anularFactura($idFactura, $auditXML);

                if ($resultado) {
                    $response['success'] = true;
                    $response['msj'] = 'Factura anulada exitosamente.';
                } else {
                    $response['success'] = false;
                    $response['msj'] = 'Error al anular la factura.';
                }
            } catch (Exception $e) {
                $response['success'] = false;
                $response['msj'] = 'Error al anular la factura: ' . $e->getMessage();
            }

            header('Content-Type: application/json');
            echo json_encode($response);
        }
	}


		
?>