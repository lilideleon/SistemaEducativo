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
					$pdf->Cell(50, 8, 'Nombre Completo', 1, 0, 'C', true);
					$pdf->Cell(30, 8, 'Tipo', 1, 0, 'C', true);
					$pdf->Cell(30, 8, 'Fecha', 1, 0, 'C', true);
					$pdf->Cell(30, 8, 'Hora', 1, 1, 'C', true);
					
					$pdf->SetFont('Arial', '', 9);
					$pdf->SetTextColor(0, 0, 0);
					
					foreach ($datos as $asistencia) {
						if ($pdf->GetY() > 250) {
							$pdf->AddPage();
							$pdf->SetFont('Arial', 'B', 10);
							$pdf->SetFillColor(105, 108, 255);
							$pdf->SetTextColor(255, 255, 255);
							$pdf->Cell(20, 8, 'ID', 1, 0, 'C', true);
							$pdf->Cell(50, 8, 'Nombre Completo', 1, 0, 'C', true);
							$pdf->Cell(30, 8, 'Tipo', 1, 0, 'C', true);
							$pdf->Cell(30, 8, 'Fecha', 1, 0, 'C', true);
							$pdf->Cell(30, 8, 'Hora', 1, 1, 'C', true);
							$pdf->SetFont('Arial', '', 9);
							$pdf->SetTextColor(0, 0, 0);
						}
						
						$pdf->Cell(20, 6, $asistencia->IdUsuario, 1, 0, 'C');
						$pdf->Cell(50, 6, utf8_decode($asistencia->Nombre), 1, 0, 'L');
						$pdf->Cell(30, 6, utf8_decode($asistencia->Tipo), 1, 0, 'C');
						$pdf->Cell(30, 6, $asistencia->Fecha, 1, 0, 'C');
						$pdf->Cell(30, 6, $asistencia->Hora, 1, 1, 'C');
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


		//metodo para reporte de caja usando fpdf hoja horizontal

		public function ReporteCaja() {
			try {
				ob_start();

				$Ejecuta = new Reportes_model();
				$fechaInicio = isset($_REQUEST['fecha_inicio']) ? $_REQUEST['fecha_inicio'] : date('Y-m-01');
				$fechaFin = isset($_REQUEST['fecha_fin']) ? $_REQUEST['fecha_fin'] : date('Y-m-d');
				$datos = $Ejecuta->ObtenerCaja($fechaInicio, $fechaFin);
				
				if ($datos) {
					ob_clean();
					
					$pdf = new FPDF('L', 'mm', 'A4');
					$pdf->AddPage();
					
					$pdf->SetFont('Arial', 'B', 16);
					
					$pdf->Cell(0, 10, 'Reporte de Caja', 0, 1, 'C');
					$pdf->SetFont('Arial', '', 10);
					$pdf->Cell(0, 6, 'Periodo: ' . date('d/m/Y', strtotime($fechaInicio)) . ' - ' . date('d/m/Y', strtotime($fechaFin)), 0, 1, 'C');
					$pdf->Ln(5);
					
					$pdf->SetFont('Arial', 'B', 10);
					
					$pdf->SetFillColor(105, 108, 255);
					$pdf->SetTextColor(255, 255, 255);
					$pdf->Cell(20, 8, 'ID', 1, 0, 'C', true);
					$pdf->Cell(40, 8, 'Usuario', 1, 0, 'C', true);
					$pdf->Cell(20, 8, 'Fecha', 1, 0, 'C', true);
					$pdf->Cell(30, 8, 'Hora Apertura', 1, 0, 'C', true);
					$pdf->Cell(30, 8, 'Hora Cierre', 1, 0, 'C', true);
					$pdf->Cell(25, 8, 'Monto Inicial', 1, 0, 'C', true);
					$pdf->Cell(25, 8, 'Monto Final', 1, 0, 'C', true);
					$pdf->Cell(25, 8, 'Monto Sistema', 1, 0, 'C', true);
					$pdf->Cell(25, 8, 'Diferencia', 1, 0, 'C', true);
					$pdf->Cell(25, 8, 'Estado', 1, 1, 'C', true);
					
					$pdf->SetFont('Arial', '', 9);
					$pdf->SetTextColor(0, 0, 0);
					
					foreach ($datos as $caja) {
						if ($pdf->GetY() > 250) {
							$pdf->AddPage();
							$pdf->SetFont('Arial', 'B', 10);
							$pdf->SetFillColor(105, 108, 255);
							$pdf->SetTextColor(255, 255, 255);
							$pdf->Cell(20, 8, 'ID', 1, 0, 'C', true);
							$pdf->Cell(40, 8, 'Usuario', 1, 0, 'C', true);
							$pdf->Cell(20, 8, 'Fecha', 1, 0, 'C', true);
							$pdf->Cell(30, 8, 'Hora Apertura', 1, 0, 'C', true);
							$pdf->Cell(30, 8, 'Hora Cierre', 1, 0, 'C', true);
							$pdf->Cell(25, 8, 'Monto Inicial', 1, 0, 'C', true);
							$pdf->Cell(25, 8, 'Monto Final', 1, 0, 'C', true);
							$pdf->Cell(25, 8, 'Monto Sistema', 1, 0, 'C', true);
							$pdf->Cell(25, 8, 'Diferencia', 1, 0, 'C', true);
							$pdf->Cell(25, 8, 'Estado', 1, 1, 'C', true);
							$pdf->SetFont('Arial', '', 9);
							$pdf->SetTextColor(0, 0, 0);
						}
						
						$pdf->Cell(20, 6, $caja->id, 1, 0, 'C');
						$pdf->Cell(40, 6, utf8_decode($caja->Usuario), 1, 0, 'L');
						$pdf->Cell(20, 6, $caja->Fecha, 1, 0, 'R');
						$pdf->Cell(30, 6, $caja->HoraApertura, 1, 0, 'R');
						$pdf->Cell(30, 6, $caja->HoraCierre, 1, 0, 'R');
						$pdf->Cell(25, 6, 'Q. ' . number_format($caja->MontoInicial, 2), 1, 0, 'R');
						$pdf->Cell(25, 6, 'Q. ' . number_format($caja->MontoFinal, 2), 1, 0, 'R');
						$pdf->Cell(25, 6, 'Q. ' . number_format($caja->MontoSistema, 2), 1, 0, 'R');
						$pdf->Cell(25, 6, 'Q. ' . number_format($caja->Diferencia, 2), 1, 0, 'R');
						$pdf->Cell(25, 6, $caja->Estado, 1, 1, 'C');
					}
					
					$pdf->Ln(10);
					$pdf->SetFont('Arial', 'I', 8);
					$pdf->Cell(0, 5, 'Fecha de generación: ' . date('d/m/Y H:i:s'), 0, 0, 'R');
					
					ob_end_clean();
					$pdf->Output('ReporteCaja.pdf', 'I');
					exit;
				} else {
					$this->mostrarError("No se encontraron registros de caja para el período seleccionado.");
				}
			} catch (Exception $e) {
				$this->mostrarError("Error al generar el reporte: " . $e->getMessage());
			}
		}

		//metodo para reporte de ventas usando fpdf y con total
		
		public function ReporteVentas() {
			try {
				// Iniciar buffer de salida
				ob_start();

				// Obtener fechas del request o usar valores por defecto
				$fechaInicio = isset($_REQUEST['fecha_inicio']) ? $_REQUEST['fecha_inicio'] : date('Y-m-01');
				$fechaFin = isset($_REQUEST['fecha_fin']) ? $_REQUEST['fecha_fin'] : date('Y-m-d');

				// Obtener datos del modelo
				$Ejecuta = new Reportes_model();
				$datos = $Ejecuta->ObtenerVentas($fechaInicio, $fechaFin);

				if ($datos) {
					// Limpiar buffer antes de generar PDF
					ob_clean();

					// Configuración inicial del PDF
					$pdf = new FPDF('P', 'mm', 'Letter');
					$pdf->AddPage();

					// Encabezado del reporte
					$pdf->SetFont('Arial', 'B', 16);
					$pdf->Cell(0, 10, 'Reporte de Ventas', 0, 1, 'C');
					$pdf->SetFont('Arial', '', 10);
					$pdf->Cell(0, 6, 'Periodo: ' . date('d/m/Y', strtotime($fechaInicio)) . ' - ' . date('d/m/Y', strtotime($fechaFin)), 0, 1, 'C');
					$pdf->Ln(5);

					// Encabezados de la tabla
					$pdf->SetFont('Arial', 'B', 10);
					$pdf->SetFillColor(105, 108, 255);
					$pdf->SetTextColor(255, 255, 255);
					
					// Definir columnas
					$columnas = [
						['texto' => 'ID', 'ancho' => 20],
						['texto' => 'Fecha', 'ancho' => 30],
						['texto' => 'Cliente', 'ancho' => 40],
						['texto' => 'Hora', 'ancho' => 30],
						['texto' => 'Total', 'ancho' => 40],
						['texto' => 'Estado', 'ancho' => 30]
					];

					// Imprimir encabezados
					foreach ($columnas as $col) {
						$pdf->Cell($col['ancho'], 8, $col['texto'], 1, 0, 'C', true);
					}
					$pdf->Ln();

					// Configurar estilo para los datos
					$pdf->SetFont('Arial', '', 9);
					$pdf->SetTextColor(0, 0, 0);

					// Variables para el total
					$total = 0;

					// Imprimir datos
					foreach ($datos as $venta) {
						// Verificar si necesitamos una nueva página
						if ($pdf->GetY() > 250) {
							$pdf->AddPage();
							// Reimprimir encabezados
							$pdf->SetFont('Arial', 'B', 10);
							$pdf->SetFillColor(105, 108, 255);
							$pdf->SetTextColor(255, 255, 255);
							foreach ($columnas as $col) {
								$pdf->Cell($col['ancho'], 8, $col['texto'], 1, 0, 'C', true);
							}
							$pdf->Ln();
							$pdf->SetFont('Arial', '', 9);
							$pdf->SetTextColor(0, 0, 0);
						}

						// Imprimir fila de datos
						$pdf->Cell(20, 6, $venta->Id, 1, 0, 'C');
						$pdf->Cell(30, 6, $venta->Fecha, 1, 0, 'L');
						$pdf->Cell(40, 6, utf8_decode($venta->Cliente), 1, 0, 'L');
						$pdf->Cell(30, 6, $venta->Hora, 1, 0, 'R');
						$pdf->Cell(40, 6, 'Q. ' . number_format($venta->Total, 2), 1, 0, 'R');
						$pdf->Cell(30, 6, $venta->Estado, 1, 1, 'C');

						$total += $venta->Total;
					}

					// Imprimir total general
					$pdf->SetFont('Arial', 'B', 12);
					$pdf->SetFillColor(105, 108, 255);
					$pdf->SetTextColor(255, 255, 255);
					$pdf->Cell(0, 8, 'Total: Q. ' . number_format($total, 2), 0, 1, 'R', true);

					// Pie de página con fecha de generación
					$pdf->Ln(10);
					$pdf->SetFont('Arial', 'I', 8);
					$pdf->Cell(0, 5, 'Fecha de generación: ' . date('d/m/Y H:i:s'), 0, 0, 'R');

					// Limpiar buffer y generar PDF
					ob_end_clean();
					$pdf->Output('ReporteVentas.pdf', 'I');
					exit;
				} else {
					$this->mostrarError("No se encontraron ventas para el período seleccionado.");
				}
			} catch (Exception $e) {
				$this->mostrarError("Error al generar el reporte: " . $e->getMessage());
			}
		}	

		//metodo para reporte de inventario usando fpdf

		public function ReporteInventario() {
			try {
				ob_start();

				$Ejecuta = new Reportes_model();
				$datos = $Ejecuta->ObtenerInventario();

				if ($datos) {
					ob_clean();
					
					$pdf = new FPDF('P', 'mm', 'Letter');
					$pdf->AddPage();
					
					$pdf->SetFont('Arial', 'B', 16);
					
					$pdf->Cell(0, 10, 'Reporte de Inventario', 0, 1, 'C');
					$pdf->Ln(5);
					
					$pdf->SetFont('Arial', 'B', 10);
					
					$pdf->SetFillColor(105, 108, 255);
					$pdf->SetTextColor(255, 255, 255);
					$pdf->Cell(20, 8, 'ID', 1, 0, 'C', true);
					$pdf->Cell(60, 8, 'Nombre', 1, 0, 'C', true);
					$pdf->Cell(30, 8, 'Stock', 1, 0, 'C', true);
					$pdf->Cell(30, 8, 'Precio Costo', 1, 0, 'C', true);
					$pdf->Cell(30, 8, 'Precio Venta', 1, 0, 'C', true);
					$pdf->Cell(30, 8, 'Estado', 1, 1, 'C', true);
					
					$pdf->SetFont('Arial', '', 9);
					$pdf->SetTextColor(0, 0, 0);
					
					foreach ($datos as $inventario) {
						if ($pdf->GetY() > 250) {
							$pdf->AddPage();
							$pdf->SetFont('Arial', 'B', 10);
							$pdf->SetFillColor(105, 108, 255);
							$pdf->SetTextColor(255, 255, 255);
							$pdf->Cell(20, 8, 'ID', 1, 0, 'C', true);
							$pdf->Cell(60, 8, 'Nombre', 1, 0, 'C', true);
							$pdf->Cell(30, 8, 'Stock', 1, 0, 'C', true);
							$pdf->Cell(30, 8, 'Precio Costo', 1, 0, 'C', true);
							$pdf->Cell(30, 8, 'Precio Venta', 1, 0, 'C', true);
							$pdf->Cell(30, 8, 'Estado', 1, 1, 'C', true);
							$pdf->SetFont('Arial', '', 9);
							$pdf->SetTextColor(0, 0, 0);
						}
						
						$pdf->Cell(20, 6, $inventario->Id, 1, 0, 'C');
						$pdf->Cell(60, 6, utf8_decode($inventario->Nombre), 1, 0, 'L');
						$pdf->Cell(30, 6, $inventario->Cantidad, 1, 0, 'C');
						$pdf->Cell(30, 6, 'Q. ' . number_format($inventario->PrecioCosto, 2), 1, 0, 'C');
						$pdf->Cell(30, 6, 'Q. ' . number_format($inventario->PrecioVenta, 2), 1, 0, 'C');
						$pdf->Cell(30, 6, $inventario->estado, 1, 1, 'C');
					}
					
					$pdf->Ln(10);
					$pdf->SetFont('Arial', 'I', 8);
					$pdf->Cell(0, 5, 'Fecha de generación: ' . date('d/m/Y H:i:s'), 0, 0, 'R');
					
					ob_end_clean();
					$pdf->Output('ReporteInventario.pdf', 'I');
					exit;
				} else {
					$this->mostrarError("No se encontraron productos en el inventario.");
				}
			} catch (Exception $e) {
				$this->mostrarError("Error al generar el reporte: " . $e->getMessage());
			}
		}
	

		//metodo para reporte de compras usando fpdf con total

		public function ReporteCompras() {
			try {
				ob_start();
				
				$Ejecuta = new Reportes_model();
				$fechaInicio = isset($_REQUEST['fecha_inicio']) ? $_REQUEST['fecha_inicio'] : date('Y-m-d');
				$fechaFin = isset($_REQUEST['fecha_fin']) ? $_REQUEST['fecha_fin'] : date('Y-m-d');
				$datos = $Ejecuta->ObtenerCompras($fechaInicio, $fechaFin);
				
				if ($datos) {
					ob_clean();
					
					$pdf = new FPDF('P', 'mm', 'Letter');
					$pdf->AddPage();
					
					$pdf->SetFont('Arial', 'B', 16);
					
					$pdf->Cell(0, 10, 'Reporte de Compras', 0, 1, 'C');
					$pdf->Ln(5);
					
					$pdf->SetFont('Arial', 'B', 10);
					
					$pdf->SetFillColor(105, 108, 255);
					$pdf->SetTextColor(255, 255, 255);
					$pdf->Cell(20, 8, 'ID', 1, 0, 'C', true);
					$pdf->Cell(30, 8, 'Fecha', 1, 0, 'C', true);
					$pdf->Cell(30, 8, 'Hora', 1, 0, 'C', true);
					$pdf->Cell(45, 8, 'Proveedor', 1, 0, 'C', true);
					$pdf->Cell(30, 8, 'Total', 1, 0, 'C', true);
					$pdf->Cell(30, 8, 'Estado', 1, 1, 'C', true);
					
					$pdf->SetFont('Arial', '', 9);
					$pdf->SetTextColor(0, 0, 0);
					
					foreach ($datos as $compra) {
						if ($pdf->GetY() > 250) {
							$pdf->AddPage();
							$pdf->SetFont('Arial', 'B', 10);
							$pdf->SetFillColor(105, 108, 255);
							$pdf->SetTextColor(255, 255, 255);
							$pdf->Cell(20, 8, 'ID', 1, 0, 'C', true);
							$pdf->Cell(30, 8, 'Fecha', 1, 0, 'C', true);
							$pdf->Cell(30, 8, 'Hora', 1, 0, 'C', true);
							$pdf->Cell(45, 8, 'Proveedor', 1, 0, 'C', true);
							$pdf->Cell(30, 8, 'Total', 1, 0, 'C', true);
							$pdf->Cell(30, 8, 'Estado', 1, 1, 'C', true);
							$pdf->SetFont('Arial', '', 9);
							$pdf->SetTextColor(0, 0, 0);
						}
						
						$pdf->Cell(20, 6, $compra->Id, 1, 0, 'C');
						$pdf->Cell(30, 6, $compra->Fecha, 1, 0, 'C');
						$pdf->Cell(30, 6, $compra->HORA, 1, 0, 'C');
						$pdf->Cell(45, 6, utf8_decode($compra->Proveedor), 1, 0, 'L');
						$pdf->Cell(30, 6, 'Q. ' . number_format($compra->Total, 2), 1, 0, 'C');
						$pdf->Cell(30, 6, $compra->Estado, 1, 1, 'C');
					}
					
					$pdf->Ln(10);
					$pdf->SetFont('Arial', 'I', 8);
					$pdf->Cell(0, 5, 'Fecha de generación: ' . date('d/m/Y H:i:s'), 0, 0, 'R');
					
					ob_end_clean();
					$pdf->Output('ReporteCompras.pdf', 'I');
					exit;
				} else {
					$this->mostrarError("No se encontraron compras en el sistema.");
				}
			} catch (Exception $e) {
				$this->mostrarError("Error al generar el reporte: " . $e->getMessage());
			}
		}


		//metodo para reporte de costos usando fpdr
		public function ReporteCostos() {
			try {
				ob_start();
				
				$Ejecuta = new Reportes_model();
				$fechaInicio = isset($_REQUEST['fechaInicio']) ? $_REQUEST['fechaInicio'] : date('Y-m-d');
				$fechaFin = isset($_REQUEST['fechaFin']) ? $_REQUEST['fechaFin'] : date('Y-m-d');
				$datos = $Ejecuta->ObtenerCostos($fechaInicio, $fechaFin);
				
				if ($datos) {
					ob_clean();
					
					$pdf = new FPDF('L', 'mm', 'A4');
					$pdf->AddPage();
					
					$pdf->SetFont('Arial', 'B', 16);
					
					$pdf->Cell(0, 10, 'Reporte de Costos', 0, 1, 'C');
					$pdf->Ln(5);
					
					$pdf->SetFont('Arial', 'B', 10);
					
					$pdf->SetFillColor(105, 108, 255);
					$pdf->SetTextColor(255, 255, 255);
					$pdf->Cell(20, 8, 'FacturaId', 1, 0, 'C', true);
					$pdf->Cell(20, 8, 'Fecha', 1, 0, 'C', true);
					$pdf->Cell(20, 8, 'IDproducto', 1, 0, 'C', true);
					$pdf->Cell(80, 8, 'Nombre', 1, 0, 'C', true);
					$pdf->Cell(30, 8, 'PrecioCosto', 1, 0, 'C', true);
					$pdf->Cell(30, 8, 'PrecioVenta', 1, 0, 'C', true);
					$pdf->Cell(30, 8, 'CantidadVendida', 1, 0, 'C', true);
					$pdf->Cell(30, 8, 'Ganancia', 1, 1, 'C', true);
					
					$pdf->SetFont('Arial', '', 9);
					$pdf->SetTextColor(0, 0, 0);
					
					$totalVendido = 0;
					$ganancia = 0;
					
					foreach ($datos as $costo) {
						if ($pdf->GetY() > 250) {
							$pdf->AddPage();
							$pdf->SetFont('Arial', 'B', 10);
							$pdf->SetFillColor(105, 108, 255);
							$pdf->SetTextColor(255, 255, 255);
							$pdf->Cell(20, 8, 'FacturaId', 1, 0, 'C', true);
							$pdf->Cell(20, 8, 'Fecha', 1, 0, 'C', true);
							$pdf->Cell(20, 8, 'IDproducto', 1, 0, 'C', true);
							$pdf->Cell(80, 8, 'Nombre', 1, 0, 'C', true);
							$pdf->Cell(30, 8, 'PrecioCosto', 1, 0, 'C', true);
							$pdf->Cell(30, 8, 'PrecioVenta', 1, 0, 'C', true);
							$pdf->Cell(30, 8, 'CantidadVendida', 1, 0, 'C', true);
							$pdf->Cell(30, 8, 'Ganancia', 1, 1, 'C', true);
							$pdf->SetFont('Arial', '', 9);
							$pdf->SetTextColor(0, 0, 0);
						}
						
						$pdf->Cell(20, 6, $costo->FacturaId, 1, 0, 'C');
						$pdf->Cell(20, 6, $costo->Fecha, 1, 0, 'C');
						$pdf->Cell(20, 6, $costo->IdProducto, 1, 0, 'C');
						$pdf->Cell(80, 6, utf8_decode($costo->Nombre), 1, 0, 'L');
						$pdf->Cell(30, 6, 'Q. ' . number_format($costo->PrecioCosto, 2), 1, 0, 'C');
						$pdf->Cell(30, 6, 'Q. ' . number_format($costo->PrecioVenta, 2), 1, 0, 'C');
						$pdf->Cell(30, 6, '' . number_format($costo->CantidadVendida, 2), 1, 0, 'C');
						$pdf->Cell(30, 6, 'Q. ' . number_format($costo->Ganancia, 2), 1, 1, 'C');
						
						$totalVendido += $costo->CantidadVendida;
						$ganancia += $costo->Ganancia;
					}
					
					$pdf->SetFont('Arial', 'B', 10);
					$pdf->SetFillColor(105, 108, 255);
					$pdf->SetTextColor(255, 255, 255);
					$pdf->Cell(200, 8, 'Total Vendido', 1, 0, 'C', true);
					$pdf->Cell(30, 8, '' . number_format($totalVendido, 2), 1, 0, 'C', true);
					$pdf->Cell(30, 8, 'Q. ' . number_format($ganancia, 2), 1, 1, 'C', true);
					
					$pdf->Ln(10);
					$pdf->SetFont('Arial', 'I', 8);
					$pdf->Cell(0, 5, 'Fecha de generación: ' . date('d/m/Y H:i:s'), 0, 0, 'R');
					
					ob_end_clean();
					$pdf->Output('ReporteCostos.pdf', 'I');
					exit;
				} else {
					$this->mostrarError("No se encontraron costos en el sistema.");
				}
			} catch (Exception $e) {
				$this->mostrarError("Error al generar el reporte: " . $e->getMessage());
			}
		}
	}
?>