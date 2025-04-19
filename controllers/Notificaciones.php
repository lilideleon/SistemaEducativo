<?php
	
	class NotificacionesController 
	{
		
		public function __construct(){
			@session_start();
			require_once "models/Notificaciones.php";
			$data["titulo"] = "Notificaciones";
		}
		
		public function index(){
			require_once "views/Notificaciones/Notificaciones.php";	
		}

		public function Agregar ()
		{
			
			$model = new Notificaciones_model();

			// Seteamos todos los campos basados en los datos que vienen por POST

			$model->setTitulo($_POST['Titulo']);
			$model->setMensaje($_POST['Mensaje']);
			$model->setImportancia($_POST['Importancia']);
			$model->setUsuario_Envia(1);
			$model->setUsuario_Atiende($_POST['Fontanero']);
			$model->setFecha($_POST['Fecha']);
			$model->setEstado($_POST['Estado']);

			$json = array();

			try {
				$model->Insertar();

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

		public function Datos()
		{
			//INSTANCIA DE LA CLASE 

			$model = new Notificaciones_model();

			//RECIBIR EL ID DE FILA ELEGIDA POR EL METODO POST

			$IdNino = $_POST['Id'];



			//DIRECTORIO EN DONDE SE ENCUENTRA LA IMAGEN MAS EL NUMERO DE CARPETA EN VASE A LA FILA ELEGIDA

			//MANDAR LOS DATOS DEL RESULSET A UN OBJECT

			foreach ($model->ObtenerDatosModal($IdNino) as $row):
			endforeach;

		    //MANDAR LA RESPUESTA DEL TEXTO JSON

			echo json_encode($row);
		}
 
		//METODO PARA ACTUALIZAR

		public function Actualizar() 
		{
			$model = new Notificaciones_model();

			// Seteamos todos los campos basados en los datos que vienen por POST

			$model->setCodigo($_POST['Id']);
			$model->setTitulo($_POST['Titulo']);
			$model->setMensaje($_POST['Mensaje']);
			$model->setImportancia($_POST['Importancia']);
			$model->setUsuario_Envia(1);
			$model->setUsuario_Atiende($_POST['Fontanero']);
			$model->setFecha($_POST['Fecha']);
			$model->setEstado($_POST['Estado']);

			$json = array();

			try {
				$model->Actualizar();

				$json['name'] = 'position';
				$json['defaultValue'] = 'top-right';
				$json['msj'] = '<font color="#ffffff"><i class="fa fa-check"></i> ACTUALIZADO </font>';
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
			$Ejecuta = new Notificaciones_model();

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
		    $sTabla = " notificaciones";
		    
		    /* Array que contiene los nombres de las columnas de la tabla*/
		    $aColumnas = array( "Id", "Titulo","Importancia","Fecha","Estado");
		    
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
		    $sWhere = "where Estado != 0";
		    if ( $_GET['sSearch'] != "" )
		    {
		        $sWhere = "WHERE (";
		        for ( $i=0 ; $i<count($aColumnas) ; $i++ )
		        {
		            $sWhere .= $aColumnas[$i]." LIKE '%".$_GET['sSearch']."%' OR ";
		        }
		        $sWhere = substr_replace( $sWhere, "", -3 );
		        $sWhere .= ') and Estado != 0';
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

                if ($row[2] == 1)
                {
                    $row[2] = '<span class="badge badge-danger">ALTA</span>';
                }
				if ($row[2] == 2)
                {
                    $row[2] = '<span class="badge badge-warning">MEDIA</span>';
                }
				if ($row[2] == 3)
                {
                    $row[2] = '<span class="badge badge-success">BAJA</span>';
                }


                if ($row[4] == 1)
                {
                    $row[4] = '<span class="badge badge-primary">NOTIFICADO</span>';
                }
				if ($row[4] == 2)
                {
                    $row[4] = '<span class="badge badge-info">INICIADO</span>';
                }
				if ($row[4] == 3)
                {
                    $row[4] = '<span class="badge badge-success">FINALIZADO</span>';
                }
				if ($row[4] == 4)
                {
                    $row[4] = '<span class="badge badge-danger">INACTIVO</span>';
                }

	 
		        $row[] = '<td><center><a href="#" data-toggle="modal" data-target="#Actualizar" onclick="Datos('.$ProductId.')"><i class="fa fa-edit"></i></a></center></td>';

		        $row[] = '<td><center><a href="#" data-toggle="modal" data-target="#DeleteNotification" onclick="Datos(' . $ProductId .')"><i class="fa fa-trash"></i></a></center></td>';

		        
		        $output['aaData'][] = $row;
		    }
		    
		    echo json_encode( $output );
		}
			
	}
		
?>