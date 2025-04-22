<?php
	
	class ReportesController {
		
		public function __construct(){
			require_once "models/Reportes.php";
		}
		
		public function index(){
			
			@session_start();
			require_once "views/Reportes/Reportes.php";	
		}


        public function Tabla()
		{
			$Conexion = new ClaseConexion();
			$conexion = $Conexion->CrearConexion();
		
			// Recibir los parámetros
			$fechaInicio = $_POST['fechaInicio'];//'2023-01-01';//$_POST['fechaInicio'];
			$fechaFin = $_POST['fechaFin'];//'2024-01-24';//$_POST['fechaFin'];


			//var_dump($fechaInicio);
			//var_dump($fechaFin);
		
			try {
				// Prevenir inyección SQL usando sentencias preparadas
				$consulta = $conexion->prepare("SELECT 
                                                    pagos.Id,
                                                    CONCAT(COALESCE(usuarios.primer_nombre, ''),' ',COALESCE(usuarios.segundo_nombre, ''),' ',COALESCE(usuarios.Primer_apellido, ''),' ',COALESCE(usuarios.Segundo_apellido, '')) AS NombreCompleto,
                                                    DATE_FORMAT(pagos.Fecha, '%e-%c-%Y') AS Fecha, 
                                                    detalle_pago.Mes, 
                                                    pagos.Total 
                                                FROM 
                                                    pagos 
                                                    LEFT JOIN detalle_pago ON detalle_pago.IdPago = pagos.Id
                                                    LEFT JOIN usuarios ON usuarios.Id = pagos.Cliente
                                                WHERE 
                                                    pagos.Fecha BETWEEN :fechaInicio AND :fechaFin 
                                                    AND pagos.Estado <> 0
                                                   -- AND pagos.Estado IS NOT NULL
                                                   -- AND Pagos.Total != 0
                                                    ");
		
				$consulta->bindParam(':fechaInicio', $fechaInicio);
				$consulta->bindParam(':fechaFin', $fechaFin);
		
				$consulta->execute();
		
				// Obtener los resultados
				$data = $consulta->fetchAll(PDO::FETCH_ASSOC);
		
				echo json_encode($data);
			} catch (PDOException $e) {
				echo "Error: " . $e->getMessage();
			}
		
			// Cierra la conexión a la base de datos
			$conexion = null;
		}
		

		public function Tabla2()
		{
			$Conexion = new ClaseConexion();
			$conexion = $Conexion->CrearConexion();
		
			// Recibir los parámetros
			$fechaInicio = $_POST['fechaInicio'];//'2023-01-01';//$_POST['fechaInicio'];
			$fechaFin = $_POST['fechaFin'];//'2024-01-24';//$_POST['fechaFin'];
			$Servicio = $_POST['Servicio'];


			//var_dump($fechaInicio);
			//var_dump($fechaFin);
		
			try {
				// Prevenir inyección SQL usando sentencias preparadas
				$consulta = $conexion->prepare("SELECT 
                                                    pagos.Id,
                                                    CONCAT(COALESCE(usuarios.primer_nombre, ''),' ',COALESCE(usuarios.segundo_nombre, ''),' ',COALESCE(usuarios.Primer_apellido, ''),' ',COALESCE(usuarios.Segundo_apellido, '')) AS NombreCompleto,
                                                    DATE_FORMAT(pagos.Fecha, '%e-%c-%Y') AS Fecha, 
                                                    detalle_pago.Mes, 
                                                     
                                                    detalle_pago.sub_total
                                                FROM 
                                                    pagos 
                                                    LEFT JOIN detalle_pago ON detalle_pago.IdPago = pagos.Id
                                                    LEFT JOIN usuarios ON usuarios.Id = pagos.Cliente
                                                WHERE 
                                                    pagos.Fecha BETWEEN :fechaInicio AND :fechaFin 
                                                    AND pagos.Total != 0
													AND detalle_pago.Servicio = :Servicio
                                                    AND pagos.Estado <> 0
                                                   -- AND Pagos.Total != 0
                                                    ");
		
				$consulta->bindParam(':fechaInicio', $fechaInicio);
				$consulta->bindParam(':fechaFin', $fechaFin);
				$consulta->bindParam(':Servicio', $Servicio);
		
				$consulta->execute();
		
				// Obtener los resultados
				$data = $consulta->fetchAll(PDO::FETCH_ASSOC);
		
				echo json_encode($data);
			} catch (PDOException $e) {
				echo "Error: " . $e->getMessage();
			}
		
			// Cierra la conexión a la base de datos
			$conexion = null;
		}
		
	}
?>