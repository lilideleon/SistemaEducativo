<?php
	
	class DetalladoController 
	{
		
		public function __construct(){
			@session_start();
			require_once "models/Productos.php";
			$data["titulo"] = "Usuarios";
		}
		
		public function index(){
			require_once "views/Reportes/Detallado.php";	
		}


			
	}
		
?>