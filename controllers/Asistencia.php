<?php
	
	class AsistenciaController 
	{
		
		public function __construct(){
			@session_start();
			require_once "models/Usuarios.php";
			$data["titulo"] = "Usuarios";
			date_default_timezone_set('America/Guatemala');
		}
		
		public function index(){
			require_once "views/Asistencia/Asistencia.php";	
		}

		//METODO PARA INSERTAR ASISTENCIA

		public function InsertarAsistencia()
		{
			$Asistencia = new Usuarios_model();

			try {
				$Asistencia->setCodigo($_SESSION['Codigo']);
				$Asistencia->setAuditxml(json_encode([
					"fecha" => date("Y-m-d H:i:s"),
					"usuario" => $_SESSION['Codigo']
				]));

				$Asistencia->RegistrarAsistencia($_SESSION['Codigo'], $_POST['Tipo'], $Asistencia->getAuditxml());

				$response['success'] = true;
				$response['msj'] = 'Asistencia registrada correctamente.';
			} catch (Exception $e) {
				$response['success'] = false;
				$response['msj'] = 'Error al registrar la asistencia: ' . $e->getMessage();
			}

			header('Content-Type: application/json');
			echo json_encode($response);
		}


		//metodo para obtener datos del usuario que se tomara asistencia por la sesion Codigo usando el metodo ObtenerUsuarioPorCodigo()
		public function ObtenerDatosUsuarioPorCodigo()
		{
			try {
				$Asistencia = new Usuarios_model();
				
				// Validar si el código de usuario está presente en la sesión
				if (!isset($_SESSION['Codigo'])) {
					throw new Exception("Código de usuario no proporcionado.");
				}
				
				// Intentar obtener los datos del usuario
				$Asistencia->setCodigo($_SESSION['Codigo']);
				$result = $Asistencia->ObtenerUsuarioPorCodigo();
				
				// Verificar si se encontraron datos
				if ($result) {
					echo json_encode([
						"status" => "success",
						"data" => $result
					]);
				} else {
					throw new Exception("No se encontraron datos para el código de usuario proporcionado.");
				}
			} catch (Exception $e) {
				// Manejar errores y devolver un JSON con el mensaje de error
				echo json_encode([
					"status" => "error",
					"message" => $e->getMessage()
				]);
			}
		}

		public function ObtenerDatosUsuario()
		{
			try {
				$Asistencia = new Usuarios_model();
				
				// Validar si el DPI está presente
				if (!isset($_POST['Dpi'])) {
					throw new Exception("DPI no proporcionado.");
				}
				
				// Intentar obtener los datos del usuario
				$result = $Asistencia->ObtenerDatosUsuario($_POST['Dpi']);
				
				// Verificar si se encontraron datos
				if ($result) {
					echo json_encode([
						"status" => "success",
						"data" => $result
					]);
				} else {
					throw new Exception("No se encontraron datos para el DPI proporcionado.");
				}
			} catch (Exception $e) {
				// Manejar errores y devolver un JSON con el mensaje de error
				echo json_encode([
					"status" => "error",
					"message" => $e->getMessage()
				]);
			}
		}

		//METODO PARA CARGAR TODO

		public function Tabla ()
		{
			/*
			* Script:    Tablas de multiples datos del lado del servidor para PHP y MySQL
			* Copyright: 2016 - Marko Robles
			* License:   GPL v2 or BSD (3-point)
			*/
			//session_start();
			$Conexion = new ClaseConexion ();
			$ConexionSql = $Conexion->CrearConexion ();

			/* Nombre de La Tabla */
			$sTabla = "asistencia a inner join usuarios b on b.IdUsuario = a.Usuarioid";

			/* Array que contiene los nombres de las columnas de la tabla (para DataTables) */
			$aColumnas = array( "Id", "Nombre", "Fecha", "Hora", "Tipo"); // Se agregó "Id" al inicio

			/* columna indexada */
			$sIndexColumn = "a.Id"; // Ahora la columna de índice es 'a.Id'

			// Paginacion
			$sLimit = "";
			if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
			{
				$sLimit = "LIMIT ".$_GET['iDisplayStart'].", ".$_GET['iDisplayLength'];
			}


			//Ordenacion
			$sOrder = "ORDER BY a.Id DESC"; // Orden por defecto
			if ( isset( $_GET['iSortCol_0'] ) )
			{
				$sOrder = "ORDER BY  a.id DESC,"; // Orden por defecto
				for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
				{
					if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
					{
						$sOrder .= $aColumnas[ intval( $_GET['iSortCol_'.$i] ) ]."
						".$_GET['sSortDir_'.$i] .", ";
					}
				}

				$sOrder = substr_replace( $sOrder, "", -2 );
				if ( $sOrder == "ORDER BY" )
				{
					$sOrder = "ORDER BY a.Id DESC"; // Si no hay ordenamiento por columna, se aplica el por defecto
				}
			}

			//Filtracion
			$sWhere = "WHERE a.Estado = 1";
			if ( $_GET['sSearch'] != "" )
			{
				$sWhere = "WHERE (";
				for ( $i=0 ; $i<count($aColumnas) ; $i++ )
				{
					$sWhere .= $aColumnas[$i]." LIKE '%".$_GET['sSearch']."%' OR ";
				}
				$sWhere = substr_replace( $sWhere, "", -3 );
				$sWhere .= ') and a.Estado = 1';
			}

			// Filtrado de columna individual
			for ( $i=0 ; $i<count($aColumnas) ; $i++ )
			{
				if ( $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
				{
					if ( $sWhere == "" )
					{
						$sWhere = "WHERE ";
					}
					else
					{
						$sWhere .= " AND ";
					}
					$sWhere .= $aColumnas[$i]." LIKE '%".$_GET['sSearch_'.$i]."%' ";
				}
			}


			//Obtener datos para mostrar SQL queries
			$sQuery = "
				SELECT SQL_CALC_FOUND_ROWS
					a.Id,
					CONCAT(b.PrimerNombre,' ',b.SegundoNombre,' ',b.PrimerApellido,' ',b.SegundoApellido) as Nombre,
					a.Fecha,
					a.Hora,
					a.Tipo
				FROM $sTabla
				$sWhere
				$sOrder
				$sLimit
			";

			//echo $sQuery;
			$rResult = $ConexionSql->prepare($sQuery);
			$rResult->execute ();

			/* Data set length after filtering */
			$sQuery = "
				SELECT FOUND_ROWS()
			";
			$rResultFilterTotal = $ConexionSql->prepare($sQuery);
			$rResultFilterTotal->execute();
			$aResultFilterTotal = $rResultFilterTotal->fetch();
			$iFilteredTotal = $aResultFilterTotal[0];

			/* Total data set length */
			$sQuery = "
				SELECT COUNT(a.".$sIndexColumn.")
				FROM   asistencia a
				where a.estado = 1
			";
			$rResultTotal = $ConexionSql->prepare($sQuery);
			$rResultTotal->execute ();
			$aResultTotal = $rResultTotal->fetch();
			$iTotal = $aResultTotal[0];

			/*
			* Output
			*/
			$output = array(
				"sEcho" => intval($_GET['sEcho']),
				"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => array()
			);

			while ( $aRow = $rResult->fetch(PDO::FETCH_ASSOC) )
			{
				$row = array();
				for ( $i=0 ; $i<count($aColumnas) ; $i++ )
				{
					/* General output */
					$row[] = $aRow[ $aColumnas[$i] ];
				}

				$ProductId = $aRow['Id']; // Ahora obtenemos el ID de la columna 'Id'

				$row[] = '<td><center><a href="#" data-toggle="modal" data-target="#CodeBar" onclick="PrintCode('.$ProductId.')"><i class="fa fa-print"></i></a></center></td>';

				$output['aaData'][] = $row;
			}

			echo json_encode( $output );
		}
			
	}
		
?>