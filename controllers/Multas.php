<?php
	
	class MultasController 
	{
		
		public function __construct(){
			@session_start();
			require_once "models/Usuarios.php";
			$data["titulo"] = "Usuarios";
		}
		
		public function index(){
			require_once "views/Multas/Multas.php";	
		}

		public function Agregar ()
		{
			
			$usuario = new Usuarios_model();

			// Seteamos todos los campos basados en los datos que vienen por POST

			$usuario->setDpi($_POST['Dpi']);
			$usuario->setPrimerNombre($_POST['Primer_Nombre']);
			$usuario->setSegundoNombre($_POST['Segundo_Nombre']);
			$usuario->setPrimerApellido($_POST['Primer_Apellido']);
			$usuario->setSegundoApellido($_POST['Segundo_Apellido']);
			$usuario->setCorreo($_POST['Correo']);
			$usuario->setPerfil($_POST['Perfil']);
			$usuario->setUsuario($_POST['Usuario']);
			$usuario->setPassword($_POST['Contraseña']);
			$usuario->setHuellas($_POST['Huellas']);
			$usuario->setAldea($_POST['Aldea']);
			$usuario->setSector($_POST['Sector']);
			$usuario->setEstado(1);

			$result = $usuario->getId ();
			$lastId = $result[0]->id;


			// Procesar y guardar la foto en el servidor
			if(isset($_FILES['foto'])) {
				$directorio_destino = "img/" . $lastId . "/";
	
				// Crea el directorio si no existe
				if(!is_dir($directorio_destino)){
					mkdir($directorio_destino, 0755, true);
				}
	
				$archivo_destino = $directorio_destino . basename($_FILES["foto"]["name"]);
	
				if (!move_uploaded_file($_FILES["foto"]["tmp_name"], $archivo_destino)) {
					$json['name'] = 'position';
					$json['defaultValue'] = 'top-right';
					$json['msj'] = '<font color="#ffffff"><i class="fa fa-exclamation-triangle"></i> Error al guardar la foto.</font>';
					$json['success'] = false;
					echo json_encode($json);
					return;
				}
			}
			

			$json = array();

			try {
				$usuario->InsertarUsuario();

				$json['name'] = 'position';
				$json['defaultValue'] = 'top-right';
				$json['msj'] = '<font color="#ffffff"><i class="fa fa-check"></i> Registrado</font>';
				$json['success'] = true;

			} catch (Exception $e) {
				
				$json['name'] = 'position';
				$json['defaultValue'] = 'top-right';
				$json['msj'] = '<font color="#ffffff"><i class="fa fa-exclamation-triangle"></i> Error al registrar: ' . $e->getMessage() . '</font>';
				$json['success'] = false;

			}

			echo json_encode($json);
		}

 

		public function getDatos()
		{
			$model = new Usuarios_model();

			if(isset($_POST['Id'])) {
				$Id = $_POST['Id'];
				
				$row = $model->ObtenerDatosModal($Id);
				
				// Suponiendo que solo tienes una imagen por ID en la carpeta:
				$directorio = "img/" . $Id;
				
				if (is_dir($directorio)) {
					$imagenes = glob($directorio . "/*.*"); // Esto obtendrá todos los archivos en el directorio
					if (count($imagenes) > 0) {
						$row['imagen'] = $imagenes[0]; // Solo tomamos la primera imagen
					}
				}

				echo json_encode($row);
			} else {
				echo json_encode(['error' => 'No se proporcionó ID']);
			}
		}

		public function CerrarSesion()
		{
			if (session_status() == PHP_SESSION_NONE) {
				session_start();
			}

			$_SESSION = array();

			if (ini_get("session.use_cookies")) {
				$params = session_get_cookie_params();
				setcookie(session_name(), '', time() - 42000,
					$params["path"], $params["domain"],
					$params["secure"], $params["httponly"]
				);
			}

			session_destroy();
			header("Location: ?c=Login");
			exit;
		}

		

		//METODO PARA ACTUALIZAR

		public function Actualizar() 
		{
			$usuario = new Usuarios_model();
		
			// Obtener el ID del usuario que se está actualizando
			$usuario->setCodigo($_POST['id']);
			
			// Setear todos los campos basados en los datos que vienen por POST
			$usuario->setDpi($_POST['Dpi']);
			$usuario->setPrimerNombre($_POST['Primer_Nombre']);
			$usuario->setSegundoNombre($_POST['Segundo_Nombre']);
			$usuario->setPrimerApellido($_POST['Primer_Apellido']);
			$usuario->setSegundoApellido($_POST['Segundo_Apellido']);
			$usuario->setCorreo($_POST['Correo']);
			$usuario->setPerfil($_POST['Perfil']);
			$usuario->setUsuario($_POST['Usuario']);
			$usuario->setPassword($_POST['Contraseña']);
			$usuario->setHuellas($_POST['Huellas']);
			$usuario->setAldea($_POST['Aldea']);
			$usuario->setSector($_POST['Sector']);
			//$usuario->setEstado($_POST['Estado']);


			$directorio_destino = "img/" . $_POST['id'] . "/";
			
			if (isset($_FILES['Efoto'])) {
				// Si el directorio no existe, créalo
				if (!is_dir($directorio_destino)) {
					mkdir($directorio_destino, 0755, true);
				}
				
				$archivo_destino = $directorio_destino . basename($_FILES["Efoto"]["name"]);
				
				if (!move_uploaded_file($_FILES["Efoto"]["tmp_name"], $archivo_destino)) {
					$json['name'] = 'position';
					$json['defaultValue'] = 'top-right';
					$json['msj'] = '<font color="#ffffff"><i class="fa fa-exclamation-triangle"></i> Error al guardar la foto.</font>';
					$json['success'] = false;
					echo json_encode($json);
					return;
				}
			}
			
			$json = array();
			
			try {
				$usuario->Actualizar(); // Asumiendo que tienes un método llamado ActualizarUsuario en Usuarios_model
				
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
		

		public function Desactivar ()
		{
			$Ejecuta = new Usuarios_model();

			//MANDAR LOS VALORES RECIBIDOS DEL FORMULARIO ATRAVEZ DEL METODO POST

			$Ejecuta->setCodigo($_POST['Codigo']);


			//INSERTAR LOS DATOS INGRESADOS

			$Ejecuta->Eliminar ();


			$json = array();
			$json['name'] = 'position';
			$json['defaultValue'] = 'top-right';
			$json['msj'] = '<font color="#ffffff"><i class="fa fa-check"></i> Desactivado</font>';
			$json['success'] = true;

			echo json_encode($json);
		}


		public function Nueva ()
		{
			$id = $_REQUEST['id'];
			require_once "views/Multas/Nueva.php";	
		}



	public function ejecutarTareaInicial()
	{
		$conexion = new ClaseConexion();

	    try {
                $this->ConexionSql = $this->Conexion->CrearConexion();

                // Ejecutar el primer procedimiento
                $consulta1 = "CALL insertarnuevosusuarios();";
                if ($this->ConexionSql->query($consulta1) === false) {
                    throw new Exception("Error al ejecutar el procedimiento InsertarNuevosUsuarios: " . $this->ConexionSql->error);
                }

                // Ejecutar el segundo procedimiento
                $consulta2 = "CALL Actualizar_morosos();";
                if ($this->ConexionSql->query($consulta2) === false) {
                    throw new Exception("Error al ejecutar el procedimiento Actualizar_morosos: " . $this->ConexionSql->error);
                }

                echo "Procedimientos ejecutados correctamente";
            } catch (Exception $exc) {
                echo "ERROR AL EJECUTAR LOS PROCEDIMIENTOS: " . $exc->getMessage();
            }
            finally {
                $this->Conexion->CerrarConexion();
            }
            
          
	}




		//METODO PARA CARGAR TODO

	public function Tabla()
	{
	//	ejecutarTareaInicial();
				$Conexion = new ClaseConexion();

	    try {
               $ConexionSql = $Conexion->CrearConexion();

                // Ejecutar el primer procedimiento
                $consulta1 = "CALL insertarnuevosusuarios();";
                if ($ConexionSql->query($consulta1) === false) {
                    throw new Exception("Error al ejecutar el procedimiento InsertarNuevosUsuarios: " . $this->ConexionSql->error);
                }

                // Ejecutar el segundo procedimiento
                $consulta2 = "CALL Actualizar_morosos();";
                if ($ConexionSql->query($consulta2) === false) {
                    throw new Exception("Error al ejecutar el procedimiento Actualizar_morosos: " . $this->ConexionSql->error);
                }

                //echo "Procedimientos ejecutados correctamente";
            } catch (Exception $exc) {
                echo "ERROR AL EJECUTAR LOS PROCEDIMIENTOS: " . $exc->getMessage();
            }
   
		
		$Conexion = new ClaseConexion();
		$ConexionSql = $Conexion->CrearConexion();

		// Realizar la consulta SQL
		$sql = "SELECT id, dpi, Primer_Nombre, Primer_Apellido, sector FROM usuarios WHERE id IN (SELECT Usuario FROM log_multas WHERE Estado = 1)";
		$resultado = $ConexionSql->query($sql);

		// Verificar si hay resultados
		if ($resultado && $resultado->rowCount() > 0) {
			$output = array();
			$totalRegistros = $resultado->rowCount(); // Obtener el total de registros

			// Recorrer los resultados y añadirlos al array de salida
			while ($fila = $resultado->fetch(PDO::FETCH_ASSOC)) {
				// Agregar una columna HTML adicional
				$botonEliminar = '<button class="btn btn-danger">INSOLVENTE</button>';
				$fila['acciones'] = $botonEliminar;

				$output[] = $fila;
			}

			// Devolver los datos en formato JSON junto con el total de registros
			echo json_encode(array(
				"data" => $output,
				"recordsTotal" => $totalRegistros,
				"recordsFiltered" => $totalRegistros // En este caso, no se está aplicando ningún filtro, por lo que el total filtrado es igual al total de registros
			));
		} else {
			// No se encontraron resultados, devolver un array vacío
			echo json_encode(array(
				"data" => array(),
				"recordsTotal" => 0,
				"recordsFiltered" => 0
			));
		}
	}


		

		public function Tabla2 ()
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
		    $sTabla = " multas";
		    
		    /* Array que contiene los nombres de las columnas de la tabla*/
		    $aColumnas = array( "Id", "Mes","Año","Motivo");
		    
		    /* columna indexada */
		    $sIndexColumn = "Id";
		    
		    // Paginacion
		    $sLimit = "";
		    if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
		    {
		        $sLimit = "LIMIT ".$_GET['iDisplayStart'].", ".$_GET['iDisplayLength'];
		    }
    
    
		    //Ordenacion
		    if ( isset( $_GET['iSortCol_0'] ) )
		    {
		        $sOrder = "ORDER BY  ";
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
		            $sOrder = "";
		        }
		    }
    
		    //Filtracion
		    $sWhere = "where Estado = 1";
		    if ( $_GET['sSearch'] != "" )
		    {
		        $sWhere = "WHERE (";
		        for ( $i=0 ; $i<count($aColumnas) ; $i++ )
		        {
		            $sWhere .= $aColumnas[$i]." LIKE '%".$_GET['sSearch']."%' OR ";
		        }
		        $sWhere = substr_replace( $sWhere, "", -3 );
		        $sWhere .= ') and Estado = 1';
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
		    SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumnas))."
		    FROM $sTabla $sWhere $sOrder $sLimit";
			
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
		    SELECT COUNT(".$sIndexColumn.")
		    FROM   $sTabla
		     where estado = 1";
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

		    $ProductId = '';
		    
		    while ( $aRow = $rResult->fetch())
		    {
		        $row = array();
		        for ( $i=0 ; $i<count($aColumnas) ; $i++ )
		        {
		            if ( $aColumnas[$i] == "version" )
		            {
		                /* Special output formatting for 'version' column */
		                $row[] = ($aRow[ $aColumnas[$i] ]=="0") ? '-' : $aRow[ $aColumnas[$i] ];
		            }
		            else if ( $aColumnas[$i] != ' ' )
		            {
		                /* General output */
		                $row[] = $aRow[ $aColumnas[$i] ];
		                $ProductId = $aRow[0];
						$Nombre = $aRow[2];
		            }
		        }

		        $row[] = '<td><center><a href="#" data-toggle="modal" data-target="#EditUser" onclick="DatosUsuario('.$ProductId.')"><i class="fa fa-edit"></i></a></center></td>';

		        $row[] = '<td><center><a href="#" data-toggle="modal" data-target="#DeleteUser" onclick="DatosUsuario(' . $ProductId .')"><i class="fa fa-trash"></i></a></center></td>';


		        $output['aaData'][] = $row;
		    }
		    
		    echo json_encode( $output );
		}
			
	}
		
?>