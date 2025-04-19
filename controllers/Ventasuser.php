<?php
	
	class VentasuserController {
		
		public function __construct(){
			require_once "models/Ventas.php";
			//require_once "models/Usuarios.php";
		}
		
		public function index(){
			
			@session_start();
			//$vehiculos = new Login_Model();
			//$data["titulo"] = "Login";
			//$data["Login"] = $vehiculos->get_vehiculos();
			
			require_once "views/Codigos/Ventasuser.php";	
		}
		
	
		public function buscarUsuario()
		{
			$model = new Ventas_model();

			echo json_encode($model->buscarUsuarios ($_GET['keyword']));
		}
		
	}
?>