<?php
	
	class TicketController {
		
		public function __construct(){
			require_once "models/Productos.php";
			//require_once "models/Usuarios.php";
		}
		
		public function index(){
			
			@session_start();
			//$vehiculos = new Login_Model();
			//$data["titulo"] = "Login";
			//$data["Login"] = $vehiculos->get_vehiculos();
			
			require_once "views/Codigos/Codigosticket.php";	
			//require_once "views/Codigos/Vista.php";
		}
		
		public function Generar(){
						
			
		}

		public function Consulta ()
		{
			//INSTANCIA DE LA CLASE 

			$model = new Productos_model();

			//MANDAR LOS DATOS DEL RESULSET A UN OBJECT

			foreach ($model->Consulta() as $r):
				$datos[] = $r;
			endforeach;


			echo json_encode($datos);

			
		}
		
		
	}
?>