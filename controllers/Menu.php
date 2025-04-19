<?php
	class MenuController {
		
		public function __construct(){
			require_once "models/Menu.php";
			@session_start();
			$data["titulo"] = "Menu";
		}
		
		public function index(){
			
			include "views/Menu/Menu.php";
		

		}
		
		public function Destruir(){
			session_destroy();

			echo json_encode('true');
		}	

		public function TotalUsuarios()
		{
			$model = new Menu_model();

			foreach ($model->getTotalUsuarios() as $r):
				$datos[] = $r->Total;
			endforeach;

			echo json_encode($datos);
		}

		public function TotalPagosMensual()
		{
			$model = new Menu_model();

			foreach ($model->getTotalPagosMensual() as $r):
				$datos[] = $r->Total;
			endforeach;

			echo json_encode($datos);
		}
	}
?>