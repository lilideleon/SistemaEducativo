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

		public function aperturarCaja(){

			//try catch para manejar las excepcion 


			$caja = new Caja_model();
			$caja->setUsuarioId($_SESSION["Codigo"]);
			$caja->setMontoInicial($_POST["montoInicial"]);
			$caja->InsertarCaja();
			
			echo json_encode(array("status" => "success", "message" => "Caja aperturada con éxito."));
		}

		//metodo para insetar un detalle a la caja

		public function insertarDetalleCaja(){
			$caja = new Caja_model();
			$caja->setCajaId($_POST["cajaId"]);
			$caja->setDenominacion($_POST["denominacion"]);
			$caja->setCantidad($_POST["cantidad"]);
			$caja->setTotal($_POST["total"]);
			$caja->InsertarDetalleCaja();
			
			echo json_encode(array("status" => "success", "message" => "Detalle de caja insertado con éxito."));
		}


			
	}
		
?>