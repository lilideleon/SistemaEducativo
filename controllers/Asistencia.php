<?php
	
	class AsistenciaController 
	{
		
		public function __construct(){
			@session_start();
			require_once "models/Usuarios.php";
			$data["titulo"] = "Usuarios";
		}
		
		public function index(){
			require_once "views/Asistencia/Asistencia.php";	
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
		    $sTabla = " asistencia";
		    
		    /* Array que contiene los nombres de las columnas de la tabla*/
		    $aColumnas = array( "Id", "Dpi","Fecha","Hora","Estado","Evento");
		    
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



				$row[] = '<td><center><a href="#" data-toggle="modal" data-target="#CodeBar" onclick="PrintCode('.$ProductId.')"><i class="fa fa-print"></i></a></center></td>';

		        
		        $output['aaData'][] = $row;
		    }
		    
		    echo json_encode( $output );
		}
			
	}
		
?>