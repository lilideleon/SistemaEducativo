<?php
	
	class PagosController 
	{
		
		public function __construct(){
			@session_start();
			require_once "models/Servicios.php";
			require_once "models/Pagos.php";
			$data["titulo"] = "Ventas";
		}
		
		public function index(){
			require_once "views/Pagos/Pagos.php";	
		}

		public function Nuevo ()
		{
			require_once "views/Pagos/NuevoPago.php";
		}

		public function BarCode ()
		{

			require_once "views/Ventas/Leer.php";
		}
		


		//METODO PARA AUTOCOMPLETAR CLIENTES EN VENTAS

		public function getClientes ()
		{
			$model = new Pagos_model();

			echo json_encode($model->buscarClientes ($_GET['keyword']));

		}
		
		//metodo par obtener las multas 
		
		public function deudas ()
		{
		    
		    $model = new Pagos_model();
			$Codigo = $_POST['Id'];
			echo json_encode($model->verDeudas ($Codigo));
		}


		public function deudasporservicio ()
		{
		    
		    $model = new Pagos_model();
			$Codigo = $_POST['Id'];
			echo json_encode($model->verdeudasporservicio ($Codigo));
		}

		//METODO DATOS DE ASOCIADOS

		public function getDatosUser ()
		{
			$model = new Pagos_model();
			$Codigo = $_POST['Id'];
			echo json_encode($model->datosUsuario ($Codigo));
		}

		//METODO DATOS DE ASOCIADOS

		public function getDatosporDpi ()
		{
			$model = new Pagos_model();
			$Dpi = $_POST['Dpi'];
			echo json_encode($model->getDatosDpi ($Dpi));
		}
		


		//METODO DATOS PARA OBTENER EL AÑO INICIAL

		public function getAnioIngreso ()
		{
			$model = new Pagos_model();
			$Codigo = $_POST['Asociado'];
			echo json_encode($model->ObtenerAnioInicial ($Codigo));
		}


		//METODO DATOS PARA OBTENER FECHA PRIMER PAGO

		public function getFechaPrimerPago ()
		{
			$model = new Pagos_model();
			$Codigo = $_POST['Asociado'];
			echo json_encode($model->ObtenerFechaPrimerPago ($Codigo));
		}

		public function getDescripcion ()
		{
			$model = new Pagos_model();
			$Codigo = $_POST['Servicio'];
			echo json_encode($model->ObtenerDescripcion ($Codigo));
		}


		public function getMonto ()
		{
			$model = new Pagos_model();
			$Codigo = $_POST['IdServicio'];
			echo json_encode($model->getMonto ($Codigo));
		}



		public function getUltimoPago ()
		{
			$model = new Pagos_model();
			echo json_encode($model->getUltimoPago ());
		}

		public function getMensualidades ()
		{
			$model = new Pagos_model();
 
			$Asociado =  $_POST['Usuario'];
			$Anio = $_POST['Anio'];
			$Servicio = $_POST['Servicio'];

			echo json_encode($model->VerificaMensualidad ($Asociado,$Anio,$Servicio));
		}

		public function getServicio ()
		{
			$model = new Pagos_model();

			echo json_encode($model->buscarServicio ($_GET['keyword']));
		}
		
		public function AnularRecibo() 
		{
			$model = new Pagos_model();
		
			$idrecibo = $_POST['idrecibo'];
			try {
				$model->AnularRecibo($idrecibo); // Asumiendo que tienes un método llamado ActualizarUsuario en Usuarios_model
				
				$json['name'] = 'position';
				$json['defaultValue'] = 'top-right';
				$json['msj'] = '<font color="#ffffff"><i class="fa fa-check"></i> Actualizado</font>';
				$json['success'] = true;
		
			} catch (Exception $e) {
				$json['name'] = 'position';
				$json['defaultValue'] = 'top-right';
				$json['msj'] = '<font color="#ffffff"><i class="fa fa-exclamation-triangle"></i> Error al actualizar: ' . $e->getMessage() . '</font>';
				$json['success'] = false;
			}
		
			echo json_encode($json);
		}

		public function Tabla ()
		{
			// Establecemos la codificacion para las llamadas y respuestas HTTP
			mb_internal_encoding ('UTF-8');

			/* CREAMOS LA CONEXION A LA BASE DE DATOS, O BIEN LA IMPORTAMOS 
			DESDE UN ARCHIVO EXTERNO DE CONFIGURACION. */
		 
			$Conexion = new ClaseConexion ();
			$conexion = $Conexion->CrearConexion ();
			 
			/* RECUPERAMOS TODOS LOS PARAMETROS DE $_GET. LOS QUE NO APAREZCAN EN LA CONSULTA 
			RECIBIRAN UN VALOR PREDETERMINADO. ESTOS DATOS SON LOS QUE SE RECIBEN CADA VEZ QUE EL 
			PLUGIN DATATABLES LLAMA A ESTE SCRIPT. */
			$datosDeLlamada = $_GET;
		 
			/* SE INDICA(N) LA(S) TABLA(S) QUE SE VA(N) A USAR EN LA CONSULTA. */
			$tablasDeBBDD = array(
				'pagos', 
			);
			 
			/* SE DEFINE LA LISTA DE COLUMNAS QUE SE DEVOLVERON PARA SER MOSTRADAS EN 
			LA TABLA HTML. 
			LOS NOMBRES DE ESTAS COLUMNAS DEBEN COINCIDIR CON LOS DE LAS COLUMNAS DE 
			LA(S) TABLA(S) AFECTADA(S) POR LA CONSULTA. */
		 
			$columnasParaRetorno = array(
				$tablasDeBBDD[0].'.Id', 
				$tablasDeBBDD[0].'.Fecha', 
				//'DATE_FORMAT('.$tablasDeBBDD[0].'.fecha_hora','"%e/%c/%Y")',
				$tablasDeBBDD[0].'.Hora',
				$tablasDeBBDD[0].'.Total',
				$tablasDeBBDD[0].'.Estado',
			);
		 
			$columnasParaRetorno_1 = array(
				$tablasDeBBDD[0].'.Id', 
				//$tablasDeBBDD[0].'.fecha_hora', 
				'DATE_FORMAT('.$tablasDeBBDD[0].'.Fecha','"%e-%c-%Y")', 
				$tablasDeBBDD[0].'.Hora',
				$tablasDeBBDD[0].'.Total',
				$tablasDeBBDD[0].'.Estado',
			);
		 
			$numeroDeColumnas = count($columnasParaRetorno);
		 
			//////////////////////////////////////////////// REGLAS DE FILTRADO ////////////////////////////
			/* PREPARAMOS EL FILTRADO POR COLUMNAS PARA LA CAJA DE BUSQUEDA */
		 
			$reglasDeFiltradoDeUsuario = array ();
			if (isset($datosDeLlamada['sSearch']) && $datosDeLlamada['sSearch'] !== "") {
				for($i = 0; $i < $numeroDeColumnas; $i++) {
					if (isset ($datosDeLlamada['bSearchable_'.$i]) && $datosDeLlamada['bSearchable_'.$i] == 'true') {
						$reglasDeFiltradoDeUsuario[] = $columnasParaRetorno[$i]." LIKE '%".addslashes($datosDeLlamada['sSearch'])."%'";
					}
				}
			}
			if (!empty($reglasDeFiltradoDeUsuario)){
				$reglasDeFiltradoDeUsuario = ' ('.implode(" OR ", $reglasDeFiltradoDeUsuario).') ';
			} else {
				$reglasDeFiltradoDeUsuario = '';
			}
		 
			/* PREPARAMOS LAS REGLAS DE FILTRADO DE RELACIONES ENTRE TABLAS. 
			ESTAS SE PROGRAMAN AQUI A MANO, PORQUE PUEDEN EXISTIR O NO, 
			DEPENDIENDO DE QUE SE USE UNA TABLA O MAS DE UNA. */
		 
			$reglasDeFiltradoDeRelaciones = '';
			$reglasDeFiltradoDeRelaciones .= " (".$tablasDeBBDD[0].".Estado is not null and pagos.total != 0)";
			 
			/* SE COMPONE TODA LA REGLA DE FILTRADO. EN ESTE CASO INCLUYE LAS 
			CLAÚSULAS DE BÚSQUEDA, Y LAS RELACIONES ENTRE TABLAS. 
			SIGUE SIENDO UN EJEMPLO SIMPLE, PERO MÁS ELABORADO QUE EL ANTERIOR. 
			MÁS ADELANTE VEREMOS OTROS USOS. 
			LO IMPORTANTE AQUI ES QUE, ADEMÁS DE LAS CLAUSULAS DE BÚSQUEDA 
			(VARIABLE $reglasDeFiltradoDeUsuario, QUE PUEDEN EXISTIR O NO) 
			TAMBIÉN HAY UNA CLAÚSULA DE RELACIONES ENTRE LAS TABLAS. SI HAY MÁS 
			DE UNA TABLA SIEMPRE HABRÁ UNA CLAÚSULA DE RELACIONES ($reglasDeFiltradoDeRelaciones). 
			LAS IMPLEMENTAMOS COMO UNA MATRIZ PARA PODER COMPROBAR LAS QUE EXISTEN Y LAS QUE NO, 
			Y LUEGO LAS UNIMOS CON EL OPERADOR AND, SI HAY MÁS DE UNA CLAÚSULA DE FILTRADO. */
			 
			$reglasDeFiltrado = array();
			if ($reglasDeFiltradoDeUsuario > '') $reglasDeFiltrado[] = $reglasDeFiltradoDeUsuario;
			if ($reglasDeFiltradoDeRelaciones > '') $reglasDeFiltrado[] = $reglasDeFiltradoDeRelaciones;
			$reglasDeFiltrado = implode(" AND ", $reglasDeFiltrado);
		 
		 	//////////////////////////////////////////// FIN DE REGLAS DE FILTRADO ///////////////////////////
			 
		 	/////////////////////////// REGLAS DE ORDENACION ////////////////////////
			/* SE COMPONE LA REGLA DE ORDENACION. */

			$reglasDeOrdenacion = array ();
			if (isset($datosDeLlamada['iSortCol_0'] )) {
				$columnasDeOrdenacion = intval($datosDeLlamada['iSortingCols']);
				for($i = 0; $i < $columnasDeOrdenacion; $i ++) {
					if ($datosDeLlamada['bSortable_'.intval($datosDeLlamada['iSortCol_'.$i])] == 'true') {
						$reglasDeOrdenacion [] = $columnasParaRetorno[intval($datosDeLlamada['iSortCol_'.$i])].($datosDeLlamada['sSortDir_'.$i] === 'asc'?' asc':' desc');
					}
				}
			}
		 
			if (!empty($reglasDeOrdenacion)) {
				$reglasDeOrdenacion = " ORDER BY ".implode(", ", $reglasDeOrdenacion);
			} else {
				$reglasDeOrdenacion = "";
			}
			 
			/* SE COMPONE LA REGLA DE LIMITACION DE REGISTROS. */
			$reglaDeLimitacion = ($datosDeLlamada['iDisplayLength'] > 0)?' LIMIT '.$datosDeLlamada['iDisplayStart'].', '.$datosDeLlamada['iDisplayLength'].';':';';
			/////////////////////////////////////// FIN DE REGLAS DE ORDENACION ////////////////////
		
			/* SE PREPARA LA CONSULTA DE RECUPERACION DEL DATASET SOLICITADO. */

			$consulta = "SELECT ".implode(', ', $columnasParaRetorno_1)." ";
			$consulta .= "FROM ".implode(', ', $tablasDeBBDD)." ";
			$consulta .= "WHERE 1 ";
			if ($reglasDeFiltrado > "") $consulta .= "AND (".$reglasDeFiltrado.") ";
			$consulta .= $reglasDeOrdenacion;
			$consulta .= $reglaDeLimitacion;

			//echo $consulta."<br></br>";

			$hacerConsulta = $conexion->prepare($consulta);
			$hacerConsulta->execute();
			$DataSet = $hacerConsulta->fetchAll(PDO::FETCH_ASSOC);
			$hacerConsulta->closeCursor();
			 
			/* SI ES NECESARIO ADAPTAR ALGUN DATO PARA PRESENTACION, SE ADAPTA AQUI. 
			SI ES NECESARIOS AGREGAR ENLACES, REFERENCIAS A IMAGENES, O CUALQUIER OTRA COSA, 
			SE INCLUYE EN ESTA SECCION. 
			EN ESTE CASO, LO ÚNICO QUE VAMOS A HACER ES DARLE FORMATO AL SALARIO ANUAL. 
			foreach ($DataSet as $keyDL=>$DL){
				$DataSet[$keyDL]['fecha_de_ingreso'] = date("d-m-Y", strtotime($DL['fecha_de_ingreso']));
				$DataSet[$keyDL]['salario_bruto_anual'] = number_format($DL['salario_bruto_anual'], 2, ",", ".").' €';
			}*/
			 
			/* CALCULO DE DATOS INFORMATIVOS DE REGISTROS. */
			$numeroDeRegistrosDelDataSet = count($DataSet);
			 
			/* CALCULO DEL TOTAL DE REGISTROS QUE CUMPLEN LAS REGLAS 
			DE FILTRADO SIN ORDENACION NI LIMITACION. */

			$consulta = "SELECT COUNT(".$columnasParaRetorno[0].") ";
			$consulta .= "FROM ".implode(', ', $tablasDeBBDD)." ";
			$consulta .= "WHERE 1 ";
			
			
			if ($reglasDeFiltrado > "") $consulta .= "AND (".$reglasDeFiltrado.") ";
			$hacerConsulta = $conexion->prepare($consulta);
			$hacerConsulta->execute();
			$totalDeRegistrosConFiltrado = $hacerConsulta->fetch(PDO::FETCH_NUM)[0];
			$hacerConsulta->closeCursor();
			/* TOTAL DE REGISTROS DE LA TABLA PRIMARIA (O UNICA, SI SOLO HAY UNA). */
			$consulta = "SELECT COUNT(".$columnasParaRetorno[0].") ";
			$consulta .= "FROM ".$tablasDeBBDD[0].";";
			$hacerConsulta = $conexion->prepare($consulta);
			$hacerConsulta->execute();
			$totalDeRegistrosEnBruto = $hacerConsulta->fetch(PDO::FETCH_NUM)[0];
			$hacerConsulta->closeCursor();
		 
			// SE PREPARA UNA MATRIZ CON TODOS LOS DATOS NECESARIOS PARA LA SALIDA.
			$matrizDeSalida = array(
				"sEcho" => intval($datosDeLlamada['sEcho']),
				"iTotalRecords" => strval($totalDeRegistrosEnBruto),
				"iTotalDisplayRecords" => strval($totalDeRegistrosConFiltrado),
				"aaData" => array () 
			);

			foreach ($DataSet as $DL){
				$registro = array();
				foreach ($DL as $dato) $registro[] = $dato;
				$ProductId = $registro[0];
				$Tipo_pago = $registro[4];
				

				if ($Tipo_pago == 1)
				{
				    $registro[4] = '<td><center><span class="badge badge-success"> PAGADO </span></center></td>';
				}
				else
				{
				    $registro[4] = '<td><center><span class="badge badge-danger"> ANULADO </span></center></td>';
				}

				$registro[5] = '<td><center><a href="?c=Pagos&a=PrintComprobante&id=' . $ProductId . '"><i class="fa fa-print"></i></a></center></td>';


				$registro[6] = '<td><center><a href="#" data-toggle="modal" data-target="#infoProduct" onclick="DataFacture('.$ProductId.')"><i class="fa fa-eye"></i></a></center></td>';

				$registro[7] = '<td><center><a href="#" data-toggle="modal" data-target="#EditUser" onclick="DatosUsuario('.$ProductId.')"><i class="fa fa-edit"></i></a></center></td>';

			    $registro[8] = '<td><center><a href="#" onclick="Eliminar('.$ProductId.')" data-toggle="modal" data-target="#confirm-delete"><i class="fa fa-trash"></i></a></td></td>';
		
		
		
				$matrizDeSalida['aaData'][] = $registro;
				unset($registro);
			}
		 
			$salidaDeDataSet = json_encode ($matrizDeSalida, JSON_HEX_QUOT);
			
			/* SE DEVUELVE LA SALIDA */
			echo $salidaDeDataSet;
		}

		function Pagar() {
			$model = new Pagos_Model();

		
			$usuario = $_POST['usuario'];
			$fecha = $_POST['fecha'];
			$Aniox = $_POST['anio'];

			$model->setFechaHora($fecha);
			$model->setIdCliente($usuario);
			$model->CrearFactura();

			// Verificar si la variable 'tabla' está establecida
			if(isset($_POST['tabla'])) {
				$datosTabla = $_POST['tabla'];
		
				foreach ($datosTabla as $datoFila) {
					$id = $datoFila['id'];
					$descripcion = $datoFila['descripcion'];
					$cantidad = $datoFila['cantidad'];
					$subtotal = $datoFila['subtotal'];
					
			
					$mes = "";
					if ($descripcion == "Enero") {
						$mes = '1';
					} elseif ($descripcion == "Febrero") {
						$mes = '2';
					} elseif ($descripcion == "Marzo") {
						$mes = '3';
					} elseif ($descripcion == "Abril") {
						$mes = '4';
					} elseif ($descripcion == "Mayo") {
						$mes = '5';
					} elseif ($descripcion == "Junio") {
						$mes = '6';
					} elseif ($descripcion == "Julio") {
						$mes = '7';
					} elseif ($descripcion == "Agosto") {
						$mes = '8';
					} elseif ($descripcion == "Septiembre") {
						$mes = '9';
					} elseif ($descripcion == "Octubre") {
						$mes = '10';
					} elseif ($descripcion == "Noviembre") {
						$mes = '11';
					} elseif ($descripcion == "Diciembre") {
						$mes = '12';
					} else 
					{
						$mes = null;
					}

					$maxima = $model->Maxima();
					$model->setMax($maxima);
					$model->setIdServicio($id);
					$model->setCantidad($cantidad);
					$model->setMes(intval($mes));
					$model->setAño($Aniox);
					$model->setSubtotal($subtotal);
					$model->InsertarDetalle();
				}
		
			
		
				// Aquí puedes procesar las variables $usuario y $fecha si es necesario.
				// Por ejemplo, guardar en la base de datos quién hizo el pago y cuándo.
		
				// Devolver una respuesta al cliente
				echo json_encode(array('status' => 'success', 'message' => 'Datos guardados exitosamente!'));
			} else {
				// Devolver un mensaje de error en caso de que la variable 'tabla' no esté establecida
				echo json_encode(array('status' => 'error', 'message' => 'Datos no recibidos!'));
			}
		}


		function Max ()
		{
			$model = new Pagos_Model();
			$maxima = $model->Maxima();
			$model->setMax($maxima);
			$model->setIdServicio(1);
					$model->setCantidad(1);
					$model->setMes(5);
					$model->setAño(2023);
					$model->getSubtotal(35);
			$model->InsertarDetalle();
		}
		

		function EstadoCuenta ()
		{
			$IdAsociado = $_GET['id'];
			require_once "views/Pagos/EstadoCuenta.php";
		}


		function TablaPagos ()
		{

			$dpi = $_POST['Id'];
			$model = new Pagos_model(); 
			$data = $model->getTablaPagos($dpi); 
		
			header('Content-Type: application/json');
			echo json_encode(array("data" => $data));
		}


		function TablaDeudas ()
		{
			$dpi = $_POST['Id'];
			$model = new Pagos_model(); 
			$data = $model->getTablaDeudas($dpi); 
		
			header('Content-Type: application/json');
			echo json_encode(array("data" => $data));
		}


		public function PrintComprobante ()
		{


			$pdf = new PDF('P', 'mm', array(80, 200)); // Tamaño personalizado 80mm x 200mm
			$pdf->AddPage();
			$pdf->agregarEncabezadoFactura();
			$pdf->detalleFactura();
			$pdf->Output();

			$controller = new PagosController();
			$controller->PrintComprobante($data);
		}


	}



	require('FPDF/fpdf.php');
	require_once "models/Pagos.php";

	class PDF extends FPDF
	{
        function Header()
        {
            // Configurar la posición y tamaño de la imagen
            $this->Image('assets/logo.jpeg', 12, 2, 60);
        
            // Mover a la posición debajo de la imagen
            $this->SetY(25); // Ajusta el valor según sea necesario
        
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 10, '', 0, 1, 'C'); // Utilizo Cell con ancho 0 para ocupar el ancho completo sin texto
            $this->Ln(5); // Ajusta el espacio después de la imagen
        }
        
        function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, 'Pagina ' . $this->PageNo(), 0, 0, 'C');
            $this->Ln(5); // Ajustar el espacio entre las líneas
            $this->Cell(0, 10, 'MIGUEL BATEN RAMOS', 0, 0, 'C');
         
        }
        
        function agregarEncabezadoFactura()
        {
            $model = new Pagos_Model();
            $data = $model->PrintComprobante($_GET['id']);  
            
            
            $this->SetFont('Arial', '', 10);
            // Ajustar la posición vertical para comenzar debajo de la imagen
            $this->SetY(50); // Ajusta el valor según sea necesario
            $this->Cell(60, 6, 'NO. RECIBO: ' . $data[0]->Id);
            $this->Ln();
            $this->Cell(60, 6, 'DPI: ' . $data[0]->Dpi);
            $this->Ln();
            $this->Cell(60, 6, 'Nombres: ' . $data[0]->Nombre);
            $this->Ln();
            $this->Cell(60, 6, 'Apellidos: ' . $data[0]->Apellido);
            $this->Ln();
            $this->Cell(60, 6, 'Fecha y hora: ' . $data[0]->Fecha . ' ' . $data[0]->Hora);
            $this->Ln();
            $this->Cell(60, 6, 'Direccion: ' . $data[0]->Direccion);
            $this->Ln();
            $this->SetFont('Arial', 'B', 14);
            $this->Cell(60, 6, 'Total: ' . $data[0]->Total);
            $this->Ln();
            $this->Ln();  // Espacio adicional antes del detalle
            $this->SetFont('Arial', '', 10);
        }


		function detalleFactura()
		{
			$model = new Pagos_Model();
			$detalles = $model->PrintdetalleComprobante($_GET['id']);  

			$this->SetFont('Arial', '', 8);
			
			// Encabezados de la tabla
			
			$this->Cell(35, 6, 'Servicio', 1);
			//$this->Cell(8, 6, 'Anio', 1);
			$this->Cell(10, 6, 'Mes', 1);
			$this->Cell(8, 6, 'Cant', 1);
			$this->Cell(10, 6, 'Stotal', 1);
			$this->Ln();
			
			// Datos
			foreach ($detalles as $detalle) {
			
				$this->Cell(35, 6, $detalle->Servicio, 1);
				//$this->Cell(8, 6, $detalle->Anio, 1);
				$this->Cell(10, 6, $detalle->Mes, 1);
				$this->Cell(8, 6, $detalle->Cantidad, 1);
				$this->Cell(10, 6, $detalle->Sub_Total, 1);
				$this->Ln();
			}

		}
	}


		
?>