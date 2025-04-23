<?php
	
	class AyudaController 
	{
		
		public function __construct(){
			@session_start();
			require_once "models/Ayuda.php";
			$data["titulo"] = "Ayuda";
		}
		
		public function index(){
			require_once "views/Ayuda/Ayuda.php";	
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

     

      
			
	}
		
?>