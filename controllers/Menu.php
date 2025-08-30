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

		//metodo para mostrar el total de usuarios

		public function TotalVentasPorDia()
		{
			$model = new Menu_model();

			$r = $model->getTotalVentasDia();
			

			echo json_encode($r);
		}


		//metodo para mostrar el total de usuarios activos

		public function TotalUsuariosActivos()
		{
			$model = new Menu_model();

			$r = $model->getTotalUsuariosActivos();
			
			echo json_encode($r);
		}
		//metodo para mostrar los productos activos

		public function TotalProductosActivos()
		{
			$model = new Menu_model();

			$r = $model->getTotalProductosActivos();
			echo json_encode($r);
		}

		//metodo para mostrar el total de compras del mes

		public function TotalComprasMes()
		{
			$model = new Menu_model();

			$r = $model->getTotalComprasMes();
			echo json_encode($r);
		}
		//metodo para mostrar el total de ventas

		public function TotalVentas()
		{
			$model = new Menu_model();

			foreach ($model->getTotalVentas() as $r):
				$datos[] = $r->TotalVentas;
			endforeach;

			echo json_encode($datos);
		}

		//metodo para mostrar el total de ventas por producto

		public function TotalVentasPorProducto()
		{
			$model = new Menu_model();

			foreach ($model->getTotalVentasPorProducto() as $r):
				$datos[] = $r->VentasPorProducto;
			endforeach;

			echo json_encode($datos);
		}

		//metodo para mostrar las transacciones

		public function getTransacciones()
		{
			$model = new Menu_model();

			foreach ($model->getTransacciones() as $r):
				$datos[] = $r;
			endforeach;

			echo json_encode($datos);
		}

		//metodo para mostrar los productos mas vendidos
		public function ProductosMasVendidos()
		{
			$model = new Menu_model();
			$dias = $_GET['dias'];
			$datos = $model->ProductosMasVendidos($dias);
			$labels = [];
			$valores = [];

			foreach ($datos as $item) {
				$labels[] = trim($item->Nombre);
				$valores[] = floatval($item->TotalVendido);
			}

			header('Content-Type: application/json');
			echo json_encode([
				'labels' => $labels,
				'datos' => $valores
			]);
		}

		// Dashboard: Top resultados por encuesta
		public function TopResultadosEncuesta()
		{
			$model = new Menu_model();
			$encuestaId = isset($_GET['encuestaId']) ? intval($_GET['encuestaId']) : 1;
			$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;

			$rows = $model->getTopResultadosEncuesta($encuestaId, $limit);
			header('Content-Type: application/json');
			echo json_encode($rows);
		}

	}
?>