<?php
	
	class ProductosController {
		
		public function __construct(){
			@session_start();
			require_once "models/Productos.php";
			date_default_timezone_set('America/Guatemala'); // Configurar la zona horaria predeterminada
		}
		
		public function index(){
			$Ejecuta = new Productos_model();
			$data["titulo"] = "Productos";
			$data["productos"] = $Ejecuta->ConsultarProductos();
			require_once "views/Productos/Productos.php";	
		}


	public function GuardarProducto()
	{
		$Ejecuta = new Productos_model();

		try {
			// Obtener el próximo ID del producto
			$idProducto = $Ejecuta->ObtenerUltimoId() + 1;

			// Crear un directorio para las imágenes del producto si no existe
			$directorioImagen = "img/productos/" . $idProducto;
			if (!is_dir($directorioImagen)) {
				mkdir($directorioImagen, 0777, true);
			}

			// Manejo de la imagen
			$rutaImagen = null;
			if (isset($_FILES['Imagen']) && $_FILES['Imagen']['error'] == 0) {
				$extension = pathinfo($_FILES['Imagen']['name'], PATHINFO_EXTENSION);
				$rutaImagen = $directorioImagen . "/" . $idProducto . "." . $extension;
				move_uploaded_file($_FILES['Imagen']['tmp_name'], $rutaImagen);
			}

			// Configurar los valores del producto
			$Ejecuta->setNombre($_POST['Nombre']);
			$Ejecuta->setDescripcion($_POST['Descripcion']);
			$Ejecuta->setPrecioCosto($_POST['PrecioCosto']);
			$Ejecuta->setPrecioVenta($_POST['PrecioVenta']);
			$Ejecuta->setEstado(1);
			$Ejecuta->setAuditXML(json_encode([
				"fecha" => date("Y-m-d H:i:s"),
				"usuario" => $_SESSION['Usuario']
			]));
			$Ejecuta->setImagen($rutaImagen);

			// Insertar el producto en la base de datos
			$Ejecuta->InsertarProducto();

			$response['success'] = true;
			$response['msj'] = 'Producto registrado exitosamente.';
		} catch (Exception $e) {
			$response['success'] = false;
			$response['msj'] = 'Error al registrar el producto: ' . $e->getMessage();
		}

		header('Content-Type: application/json');
		echo json_encode($response);
	}


		//METODO PARA ACTUALIZAR

		public function ActualizarProducto()
		{
			$Ejecuta = new Productos_model();

			try {
				// Configurar los valores del producto desde el formulario
				$Ejecuta->setIdProducto($_POST['Codigo']);
				$Ejecuta->setNombre($_POST['Nombre']);
				$Ejecuta->setPrecioCosto($_POST['PrecioCosto']);
				$Ejecuta->setPrecioVenta($_POST['PrecioVenta']);
				$Ejecuta->setDescripcion($_POST['Descripcion']);
				$Ejecuta->setEstado(1); // Estado activo
				$Ejecuta->setAuditXML(null); // Puedes ajustar esto según sea necesario

				// Manejo de la imagen
				$idProducto = $_POST['Codigo']; // Obtener el ID del producto desde el formulario

				// Crear un directorio para las imágenes del producto si no existe
				$directorioImagen = "img/productos/" . $idProducto;
				if (!is_dir($directorioImagen)) {
					mkdir($directorioImagen, 0777, true);
				}

				// Mover la imagen subida al directorio del producto
				if (isset($_FILES['Imagen']) && $_FILES['Imagen']['error'] == 0) {
					$extension = pathinfo($_FILES['Imagen']['name'], PATHINFO_EXTENSION);
					$rutaImagen = $directorioImagen . "/" . $idProducto . "." . $extension;
					move_uploaded_file($_FILES['Imagen']['tmp_name'], $rutaImagen);
					$Ejecuta->setImagen($rutaImagen);
				} else {
					$Ejecuta->setImagen($_POST['ImagenActual']); // Mantener la imagen actual si no se sube una nueva
				}

				// Actualizar el producto
				$Ejecuta->ActualizarProducto();

				// Respuesta JSON de éxito
				echo json_encode([
					"success" => true,
					"msj" => "Producto actualizado exitosamente."
				]);
			} catch (Exception $e) {
				// Respuesta JSON de error
				echo json_encode([
					"success" => false,
					"msj" => "Error al actualizar el producto: " . $e->getMessage()
				]);
			}
		}


		//ELIMINAR REGISTRO

		public function Desactivar ()
		{
			$Ejecuta = new Productos_model();

			try {
				// Configurar el ID del producto a desactivar
				$Ejecuta->setIdProducto($_POST['Codigo']);

				// Desactivar el producto
				$Ejecuta->EliminarProducto();

				// Respuesta JSON de éxito
				echo json_encode([
					"success" => true,
					"msj" => "Producto eliminado exitosamente."
				]);
			} catch (Exception $e) {
				// Respuesta JSON de error
				echo json_encode([
					"success" => false,
					"msj" => "Error al eliminar el producto: " . $e->getMessage()
				]);
			}
		}

		// Método para listar del lado del servidor

	public function Tabla()
    {
        $Conexion = new ClaseConexion();
        $ConexionSql = $Conexion->CrearConexion();

        $sTabla = "productos";
        $aColumnas = array("IdProducto", "Nombre", "PrecioCosto", "PrecioVenta", "Estado");
        $sIndexColumn = "IdProducto";

        $sLimit = "";
        if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
            $sLimit = "LIMIT " . $_GET['iDisplayStart'] . ", " . $_GET['iDisplayLength'];
        }

        $sOrder = "";
        if (isset($_GET['iSortCol_0'])) {
            $sOrder = "ORDER BY  ";
            for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
                if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
                    $sOrder .= $aColumnas[intval($_GET['iSortCol_' . $i])] . " " . $_GET['sSortDir_' . $i] . ", ";
                }
            }
            $sOrder = substr_replace($sOrder, "", -2);
        }

        $sWhere = "WHERE Estado = 1";
        if ($_GET['sSearch'] != "") {
            $sWhere = "WHERE (";
            for ($i = 0; $i < count($aColumnas); $i++) {
                $sWhere .= $aColumnas[$i] . " LIKE '%" . $_GET['sSearch'] . "%' OR ";
            }
            $sWhere = substr_replace($sWhere, "", -3);
            $sWhere .= ') AND Estado = 1';
        }

        $sQuery = "
            SELECT SQL_CALC_FOUND_ROWS " . implode(", ", $aColumnas) . "
            FROM $sTabla
            $sWhere
            $sOrder
            $sLimit
        ";

        $rResult = $ConexionSql->prepare($sQuery);
        $rResult->execute();

        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => $rResult->rowCount(),
            "iTotalDisplayRecords" => $rResult->rowCount(),
            "aaData" => array()
        );

        while ($aRow = $rResult->fetch(PDO::FETCH_ASSOC)) {
            $row = array();
            foreach ($aColumnas as $col) {
                if ($col === "Estado") {
                    $row[] = $aRow[$col] == 1
                        ? '<span class="badge bg-success">Activo</span>'
                        : '<span class="badge bg-danger">Inactivo</span>';
                } else {
                    $row[] = $aRow[$col];
                }
            }

            // Agregar botones de Editar y Eliminar
            $row[] = '
                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#EditarProductoModal" onclick="DatosProducto(' . $aRow['IdProducto'] . ')">
                    <i class="fa fa-edit"></i> Editar
                </button>
                <button class="btn btn-danger btn-sm" onclick="EliminarProducto(' . $aRow['IdProducto'] . ')">
                    <i class="fa fa-trash"></i> Eliminar
                </button>
            ';

            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function DatosModal()
	{
		$Ejecuta = new Productos_model();
		$Ejecuta->setIdProducto($_POST['IdProducto']); // Recibir el ID del producto desde el frontend

		$datos = $Ejecuta->ConsultarProductoPorId(); // Llamar al método del modelo para obtener los datos

		// Agregar la ruta de la imagen al resultado
		if (!empty($datos['Imagen'])) {
			$datos['Imagen'] = "img/productos/" . $datos['IdProducto'] . "/" . basename($datos['Imagen']);
		} else {
			$datos['Imagen'] = null; // Si no hay imagen, devolver null
		}

		echo json_encode($datos); // Devolver los datos en formato JSON
	}

}
?>