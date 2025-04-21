<?php
	
	class CajaController 
	{
		
		public function __construct(){
			@session_start();
			require_once "models/Caja.php";
			$data["titulo"] = "Caja";
		}
		
		public function index(){
			require_once "views/Caja/Caja.php";	
		}

		
		//metodo para aperturar caja

		public function aperturarCaja() {
			try {
				$caja = new Caja_model();
				$caja->setUsuarioId($_SESSION["Codigo"]);
				$caja->setMontoInicial($_POST["montoInicial"]);
				$caja->InsertarCaja();
				
				echo json_encode(array("status" => "success", "message" => "Caja aperturada con éxito."));
			} catch (Exception $e) {
				echo json_encode(array("status" => "error", "message" => "Error al aperturar la caja: " . $e->getMessage()));
			}
		}

		// Método para insertar un detalle de caja
        public function insertarDetalleCaja() {
            try {
                $caja = new Caja_model();
                $caja->setCajaId($_POST["cajaId"]);
                $caja->setDenominacion($_POST["denominacion"]);
                $caja->setCantidad($_POST["cantidad"]);
                $caja->InsertarCajaDetalle();

                echo json_encode(array("status" => "success", "message" => "Detalle de caja insertado con éxito."));
            } catch (Exception $e) {
                echo json_encode(array("status" => "error", "message" => "Error al insertar el detalle de la caja: " . $e->getMessage()));
            }
        }

		// Method to fetch the current state of the cash register
        public function obtenerEstadoActualCaja() {
            try {
                $caja = new Caja_model();
                $estadoActual = $caja->obtenerEstadoActualCaja();

                echo json_encode(array("status" => "success", "data" => $estadoActual));
            } catch (Exception $e) {
                echo json_encode(array("status" => "error", "message" => "Error al obtener el estado actual de la caja: " . $e->getMessage()));
            }
        }

        // Method to get the current Caja ID
        public function obtenerIdCajaActual() {
            try {
                $caja = new Caja_model();
                $idCaja = $caja->obtenerIdCajaActual();

                echo json_encode(array("status" => "success", "idCaja" => $idCaja));
            } catch (Exception $e) {
                echo json_encode(array("status" => "error", "message" => "Error al obtener el ID de la caja actual: " . $e->getMessage()));
            }
        }

        // Method to call the updated CerrarCaja stored procedure after inserting details
        public function cerrarCaja() {
            try {
                $caja = new Caja_model();
                $cajaData = $caja->obtenerIdCajaActual();

                if (!$cajaData) {
                    echo json_encode(array("status" => "error", "message" => "No se encontró una caja abierta para hoy."));
                    return;
                }

                if ($cajaData['Estado'] !== 'Abierta') {
                    echo json_encode(array("status" => "error", "message" => "La caja ya está cerrada."));
                    return;
                }

                $idCaja = $cajaData['id'];
                $detalles = $_POST['detalles']; // Array of details (denomination, quantity, total)

                foreach ($detalles as $detalle) {
                    $caja->setCajaId($idCaja);
                    $caja->setDenominacion($detalle['denominacion']);
                    $caja->setCantidad($detalle['cantidad']);
                    $caja->InsertarCajaDetalle();
                }

                // Call the updated stored procedure to close the Caja
                $montoFinal = $_POST['montoFinal'];
                $caja->cerrarCaja($idCaja, $montoFinal);

                echo json_encode(array("status" => "success", "message" => "Caja cerrada con éxito."));
            } catch (Exception $e) {
                echo json_encode(array("status" => "error", "message" => "Error al cerrar la caja: " . $e->getMessage()));
            }
        }
			
	}
		
?>