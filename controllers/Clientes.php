<?php
	
	class ClientesController 
	{
		
		public function __construct(){
			@session_start();
			require_once "models/Clientes.php";
			$data["titulo"] = "Clientes";
		}
		
		public function index(){
			require_once "views/Clientes/Clientes.php";	
		}

		public function Agregar ()
		{
			
			$Ejecuta = new Clientes_model();

			//MANDAR LOS VALORES RECIBIDOS DEL FORMULARIO ATRAVEZ DEL METODO POST

			$Ejecuta->setNombre($_POST['Nombre']);
			$Ejecuta->setDocumento($_POST['Dpi']);
			$Ejecuta->setCorreo($_POST['Correo']);
			$Ejecuta->setTelefono($_POST['Telefono']);
			$Ejecuta->setEstado(1); //ESTADO ACTIVO

			//INSERTAR LOS DATOS INGRESADOS

			$Ejecuta->Insertar ();

			$json = array();
			$json['name'] = 'position';
			$json['defaultValue'] = 'top-right';
			$json['msj'] = '<font color="#ffffff"><i class="fa fa-check"></i>regisrado</font>';
			$json['success'] = true; 
			echo json_encode($json);
		}

		//METODO PARA UN REGISTRO

		public function getDatos ()
		{
			//INSTANCIA DE LA CLASE 

			$model = new Clientes_model();

			//RECIBIR EL ID DE FILA ELEGIDA POR EL METODO POST

			$IdNino = $_POST['Codigo'];



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
			$Ejecuta = new Clientes_model();

			//MANDAR LOS VALORES RECIBIDOS DEL FORMULARIO ATRAVEZ DEL METODO POST

			$Ejecuta->setCodigo($_POST['Codigo']);
			$Ejecuta->setNombre($_POST['Nombre']);
			$Ejecuta->setDocumento($_POST['Dpi']);
			$Ejecuta->setCorreo($_POST['Correo']);
			$Ejecuta->setTelefono($_POST['Telefono']);
			$Ejecuta->setEstado(1); //ESTADO ACTIVO

			//INSERTAR LOS DATOS INGRESADOS

			$Ejecuta->Actualizar ();

			$json = array();
			$json['name'] = 'position';
			$json['defaultValue'] = 'top-right';
			$json['msj'] = '<font color="#ffffff"><i class="fa fa-check"></i>regisrado</font>';
			$json['success'] = true;

			echo json_encode($json);
		}

		public function Desactivar ()
		{
			$Ejecuta = new Clientes_model();

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
		    $sTabla = " clientes";
		    
		    /* Array que contiene los nombres de las columnas de la tabla*/
		    $aColumnas = array( 'Id', 'Nombre','Documento','Correo','Telefono');
		    
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
		            }
		        }


		        $row[] = '<td><center><a href="#" data-toggle="modal" data-target="#EditUser" onclick="DatosUsuario('.$ProductId.')"><i class="fa fa-edit"></i></a></center></td>';

		        $row[] = '<td><center><a href="#" data-toggle="modal" data-target="#DeleteUser" onclick="DatosUsuario('.$ProductId.')"><i class="fa fa-trash"></i></a></center></td>';
		        

		        $output['aaData'][] = $row;
		    }
		    
		    echo json_encode( $output );
		}
			
	}
		
?>