<?php
	
	class ReportesController {
		
		public function __construct(){
			date_default_timezone_set('America/Guatemala');
			require_once "models/Reportes.php";
			require_once "res/plugins/fpdf/fpdf.php";
		}

		private function mostrarError($mensaje) {
			ob_end_clean();
			?>
			<!DOCTYPE html>
			<html lang="es">
			<head>
				<meta charset="UTF-8">
				<meta name="viewport" content="width=device-width, initial-scale=1.0">
				<title>Error en Reporte</title>
				<link rel="stylesheet" href="res/plugins/bootstrap/css/bootstrap.min.css">
				<link rel="stylesheet" href="res/plugins/fontawesome-free/css/all.min.css">
				<style>
					body {
						background-color: #f8f9fa;
						height: 100vh;
						display: flex;
						align-items: center;
						justify-content: center;
						margin: 0;
						font-family: Arial, sans-serif;
					}
					.error-container {
						background: white;
						padding: 2rem;
						border-radius: 10px;
						box-shadow: 0 0 15px rgba(0,0,0,0.1);
						text-align: center;
						max-width: 500px;
						width: 90%;
					}
					.error-icon {
						color: #6c63ff;
						font-size: 3rem;
						margin-bottom: 1rem;
					}
					.error-title {
						font-size: 1.5rem;
						margin-bottom: 1rem;
						color: #333;
					}
					.error-message {
						color: #666;
						margin-bottom: 1.5rem;
					}
					.btn-cerrar {
						background-color: #6c63ff;
						border: none;
						color: white;
						padding: 10px 20px;
						border-radius: 5px;
						cursor: pointer;
						font-size: 1rem;
						display: inline-flex;
						align-items: center;
						gap: 8px;
					}
					.btn-cerrar:hover {
						background-color: #5a52cc;
					}
				</style>
			</head>
			<body>
				<div class="error-container">
					<div class="error-icon">
						<i class="fas fa-exclamation-circle"></i>
					</div>
					<h4 class="error-title">Error en la Generación del Reporte</h4>
					<p class="error-message"><?php echo $mensaje; ?></p>
					<button onclick="window.close()" class="btn-cerrar">
						<i class="fas fa-times"></i>
						Cerrar
					</button>
				</div>
				<script src="res/plugins/jquery/jquery.min.js"></script>
				<script src="res/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
				<script>
					// Si window.close() no funciona (algunos navegadores lo bloquean),
					// intentamos cerrar usando window.open('', '_self')
					document.querySelector('button').addEventListener('click', function() {
						window.close();
						// Alternativa por si window.close() no funciona
						window.open('', '_self').close();
					});
				</script>
			</body>
			</html>
			<?php
			exit;
		}
		
		public function index(){
			@session_start();
			require_once "views/Reportes/Reportes.php";	
		}

        //metodo para listar productos en el reporte de productos usando fpdf
		public function ReporteProductos() {
			try {
				ob_start();
				
				$Ejecuta = new Reportes_model();
				$orden = isset($_REQUEST['ordenar']) ? $_REQUEST['ordenar'] : 'nombre'; 
				$datos = $Ejecuta->ObtenerProductos($orden);
				
				if ($datos) {
					ob_clean();
					
					$pdf = new FPDF('P', 'mm', 'Letter');
					$pdf->AddPage();
					
					$pdf->SetFont('Arial', 'B', 16);
					
					$pdf->Cell(0, 10, 'Reporte de Productos', 0, 1, 'C');
					$pdf->Ln(5);
					
					$pdf->SetFont('Arial', 'B', 10);
					
					$pdf->SetFillColor(105, 108, 255);
					$pdf->SetTextColor(255, 255, 255);
					$pdf->Cell(15, 8, 'ID', 1, 0, 'C', true);
					$pdf->Cell(60, 8, 'Nombre', 1, 0, 'C', true);
					$pdf->Cell(30, 8, 'P. Costo', 1, 0, 'C', true);
					$pdf->Cell(30, 8, 'P. Venta', 1, 0, 'C', true);
					$pdf->Cell(30, 8, 'Estado', 1, 0, 'C', true);
					$pdf->Cell(30, 8, 'Imagen', 1, 1, 'C', true);
					
					$pdf->SetFont('Arial', '', 9);
					$pdf->SetTextColor(0, 0, 0);
					
					foreach ($datos as $producto) {
						if ($pdf->GetY() > 250) {
							$pdf->AddPage();
							$pdf->SetFont('Arial', 'B', 10);
							$pdf->SetFillColor(105, 108, 255);
							$pdf->SetTextColor(255, 255, 255);
							$pdf->Cell(15, 8, 'ID', 1, 0, 'C', true);
							$pdf->Cell(60, 8, 'Nombre', 1, 0, 'C', true);
							$pdf->Cell(30, 8, 'P. Costo', 1, 0, 'C', true);
							$pdf->Cell(30, 8, 'P. Venta', 1, 0, 'C', true);
							$pdf->Cell(30, 8, 'Estado', 1, 0, 'C', true);
							$pdf->Cell(30, 8, 'Imagen', 1, 1, 'C', true);
							$pdf->SetFont('Arial', '', 9);
							$pdf->SetTextColor(0, 0, 0);
						}
						
						$estado = $producto->Estado == 1 ? 'Activo' : 'Inactivo';
						
						$pdf->Cell(15, 6, $producto->IdProducto, 1, 0, 'C');
						$pdf->Cell(60, 6, utf8_decode($producto->Nombre), 1, 0, 'L');
						$pdf->Cell(30, 6, 'Q. ' . number_format($producto->PrecioCosto, 2), 1, 0, 'R');
						$pdf->Cell(30, 6, 'Q. ' . number_format($producto->PrecioVenta, 2), 1, 0, 'R');
						$pdf->Cell(30, 6, $estado, 1, 0, 'C');
						$pdf->Cell(30, 6, $producto->Imagen, 1, 1, 'C');
					}
					
					$pdf->Ln(10);
					$pdf->SetFont('Arial', 'I', 8);
					$pdf->Cell(0, 5, 'Fecha de generación: ' . date('d/m/Y H:i:s'), 0, 0, 'R');
					
					ob_end_clean();
					$pdf->Output('ReporteProductos.pdf', 'I');
					exit;
				} else {
					$this->mostrarError("No se encontraron productos para generar el reporte.");
				}
			} catch (Exception $e) {
				$this->mostrarError("Error al generar el reporte: " . $e->getMessage());
			}
		}

		//metodo para reporte de asistencia de usuarios usando fpdf
		public function ReporteAsistencia() {
			try {
				ob_start();
				
				$Ejecuta = new Reportes_model();
				$fechaInicio = isset($_REQUEST['fecha_inicio']) ? $_REQUEST['fecha_inicio'] : date('Y-m-01');
				$fechaFin = isset($_REQUEST['fecha_fin']) ? $_REQUEST['fecha_fin'] : date('Y-m-d');
				
				$datos = $Ejecuta->ObtenerAsistencia($fechaInicio, $fechaFin);
				
				if ($datos) {
					ob_clean();
					
					$pdf = new FPDF('P', 'mm', 'Letter');
					$pdf->AddPage();
					
					$pdf->SetFont('Arial', 'B', 16);
					
					$pdf->Cell(0, 10, 'Reporte de Asistencia de Usuarios', 0, 1, 'C');
					$pdf->SetFont('Arial', '', 10);
					$pdf->Cell(0, 6, 'Periodo: ' . date('d/m/Y', strtotime($fechaInicio)) . ' - ' . date('d/m/Y', strtotime($fechaFin)), 0, 1, 'C');
					$pdf->Ln(5);
					
					$pdf->SetFont('Arial', 'B', 10);
					
					$pdf->SetFillColor(105, 108, 255);
					$pdf->SetTextColor(255, 255, 255);
					$pdf->Cell(20, 8, 'ID', 1, 0, 'C', true);
					$pdf->Cell(80, 8, 'Nombre Completo', 1, 0, 'C', true);
					$pdf->Cell(45, 8, 'Tipo', 1, 0, 'C', true);
					$pdf->Cell(45, 8, 'Hora', 1, 1, 'C', true);
					
					$pdf->SetFont('Arial', '', 9);
					$pdf->SetTextColor(0, 0, 0);
					
					foreach ($datos as $asistencia) {
						if ($pdf->GetY() > 250) {
							$pdf->AddPage();
							$pdf->SetFont('Arial', 'B', 10);
							$pdf->SetFillColor(105, 108, 255);
							$pdf->SetTextColor(255, 255, 255);
							$pdf->Cell(20, 8, 'ID', 1, 0, 'C', true);
							$pdf->Cell(80, 8, 'Nombre Completo', 1, 0, 'C', true);
							$pdf->Cell(45, 8, 'Tipo', 1, 0, 'C', true);
							$pdf->Cell(45, 8, 'Hora', 1, 1, 'C', true);
							$pdf->SetFont('Arial', '', 9);
							$pdf->SetTextColor(0, 0, 0);
						}
						
						$pdf->Cell(20, 6, $asistencia->IdUsuario, 1, 0, 'C');
						$pdf->Cell(80, 6, utf8_decode($asistencia->Nombre), 1, 0, 'L');
						$pdf->Cell(45, 6, utf8_decode($asistencia->Tipo), 1, 0, 'C');
						$pdf->Cell(45, 6, $asistencia->Hora, 1, 1, 'C');
					}
					
					$pdf->Ln(10);
					$pdf->SetFont('Arial', 'I', 8);
					$pdf->Cell(0, 5, 'Fecha de generación: ' . date('d/m/Y H:i:s'), 0, 0, 'R');
					
					ob_end_clean();
					$pdf->Output('ReporteAsistencia.pdf', 'I');
					exit;
				} else {
					$this->mostrarError("No se encontraron registros de asistencia para el período seleccionado.");
				}
			} catch (Exception $e) {
				$this->mostrarError("Error al generar el reporte: " . $e->getMessage());
			}
		}

		//metodo para reporte de usuarios basado en fpdf
		public function ReporteUsuarios() {
			try {
				ob_start();
				
				$Ejecuta = new Reportes_model();
				$estado = isset($_REQUEST['estado']) ? $_REQUEST['estado'] : 'Activo';
				$datos = $Ejecuta->ObtenerUsuarios($estado);

				if ($datos) {
					ob_clean();
					
					$pdf = new FPDF('P', 'mm', 'Letter');
					$pdf->AddPage();
					
					$pdf->SetFont('Arial', 'B', 16);
					
					$pdf->Cell(0, 10, 'Reporte de Usuarios', 0, 1, 'C');
					$pdf->Ln(5);
					
					$pdf->SetFont('Arial', 'B', 10);
					
					$pdf->SetFillColor(105, 108, 255);
					$pdf->SetTextColor(255, 255, 255);
					$pdf->Cell(20, 8, 'ID', 1, 0, 'C', true);
					$pdf->Cell(80, 8, 'Nombre Completo', 1, 0, 'C', true);
					$pdf->Cell(45, 8, 'DPI', 1, 0, 'C', true);
					$pdf->Cell(45, 8, 'Estado', 1, 1, 'C', true);
					
					$pdf->SetFont('Arial', '', 9);
					$pdf->SetTextColor(0, 0, 0);
					
					foreach ($datos as $usuario) {
						if ($pdf->GetY() > 250) {
							$pdf->AddPage();
							$pdf->SetFont('Arial', 'B', 10);
							$pdf->SetFillColor(105, 108, 255);
							$pdf->SetTextColor(255, 255, 255);
							$pdf->Cell(20, 8, 'ID', 1, 0, 'C', true);
							$pdf->Cell(80, 8, 'Nombre Completo', 1, 0, 'C', true);
							$pdf->Cell(45, 8, 'DPI', 1, 0, 'C', true);
							$pdf->Cell(45, 8, 'Estado', 1, 1, 'C', true);
							$pdf->SetFont('Arial', '', 9);
							$pdf->SetTextColor(0, 0, 0);
						}
						
						$pdf->Cell(20, 6, $usuario->IdUsuario, 1, 0, 'C');
						$pdf->Cell(80, 6, utf8_decode($usuario->Nombre), 1, 0, 'L');
						$pdf->Cell(45, 6, $usuario->Dpi, 1, 0, 'R');
						$pdf->Cell(45, 6, $usuario->Estado, 1, 1, 'C');
					}
					
					$pdf->Ln(10);
					$pdf->SetFont('Arial', 'I', 8);
					$pdf->Cell(0, 5, 'Fecha de generación: ' . date('d/m/Y H:i:s'), 0, 0, 'R');
					
					ob_end_clean();
					$pdf->Output('ReporteUsuarios.pdf', 'I');
					exit;
				} else {
					$this->mostrarError("No se encontraron usuarios para generar el reporte.");
				}
			} catch (Exception $e) {
				$this->mostrarError("Error al generar el reporte: " . $e->getMessage());
			}
		}
	}
?>