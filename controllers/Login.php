<?php
	
	class LoginController {
		
		public function __construct(){
			require_once "models/Login.php";
			require_once "models/Usuarios.php";
		}
		
		public function index(){
			
			
			$vehiculos = new Login_Model();
			$data["titulo"] = "Login";
			//$data["Login"] = $vehiculos->get_vehiculos();
			
			require_once "views/Login/Login.php";	
		}
		
		public function nuevo(){
			
			$data["titulo"] = "Login";
			require_once "views/Login/Login.php";
		}
	
		public function Validate()
		{
			$response['success'] = true;

			echo json_encode($response);
		}


			
	}
?>