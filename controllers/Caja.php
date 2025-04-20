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

		//metodo para insetar un detalle a la caja

		public function insertarDetalleCaja() {
			try {
				$caja = new Caja_model();
				$caja->setCajaId($_POST["cajaId"]);
				$caja->setDenominacion($_POST["denominacion"]);
				$caja->setCantidad($_POST["cantidad"]);
				$caja->setTotal($_POST["total"]);
				$caja->InsertarDetalleCaja();
				
				echo json_encode(array("status" => "success", "message" => "Detalle de caja insertado con éxito."));
			} catch (Exception $e) {
				echo json_encode(array("status" => "error", "message" => "Error al insertar el detalle de la caja: " . $e->getMessage()));
			}
		}


			
	}
		
?>