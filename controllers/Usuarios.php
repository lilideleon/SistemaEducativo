<?php

class UsuariosController
{
    public function __construct()
    {
        @session_start();
        require_once "models/Usuarios.php";
        $data["titulo"] = "Usuarios";
    }

    public function index()
    {
        require_once "views/Usuarios/Usuarios.php";
    }

    

    public function Agregar()
    {
        $usuario = new Usuarios_model();

        // Seteamos todos los campos basados en los datos que vienen por POST
        $usuario->setCodigo($_POST['Codigo']);
        $usuario->setNombres(trim($_POST['PrimerNombre'] . ' ' . $_POST['SegundoNombre']));
        $usuario->setApellidos(trim($_POST['PrimerApellido'] . ' ' . $_POST['SegundoApellido']));
        $usuario->setRol($_POST['Rol']);
        
        // Si es estudiante, establecer grado e institución
        if ($_POST['Rol'] === 'ALUMNO') {
            $usuario->setGradoId($_POST['GradoId']);
            $usuario->setInstitucionId($_POST['InstitucionId']);
            // Sección obligatoria y numérica para alumnos
            if (isset($_POST['Seccion']) && $_POST['Seccion'] !== '') {
                if (!ctype_digit((string)$_POST['Seccion'])) {
                    throw new Exception('La sección debe ser numérica');
                }
                $usuario->setSeccion($_POST['Seccion']);
            } else {
                throw new Exception('La sección es requerida para alumnos');
            }
        }
        else {
            // Para roles no alumno, limpiar sección
            $usuario->setSeccion(null);
        }
        
        // Establecer contraseña (el modelo se encarga de hashearla)
        if (!empty($_POST['Contraseña'])) {
            $usuario->setPassword($_POST['Contraseña']);
        }

        $json = array();

        try {
            $usuario->InsertarUsuario();

            $json['name'] = 'position';
            $json['defaultValue'] = 'top-right';
            $json['msj'] = '<font color="#ffffff"><i class="fa fa-check"></i> Usuario registrado correctamente</font>';
            $json['success'] = true;
        } catch (Exception $e) {
            $json['name'] = 'position';
            $json['defaultValue'] = 'top-right';
            $json['msj'] = '<font color="#ffffff"><i class="fa fa-exclamation-triangle"></i> Error al registrar: ' . $e->getMessage() . '</font>';
            $json['success'] = false;
        }

        echo json_encode($json);
    }

    public function Actualizar()
    {
        $usuario = new Usuarios_model();

        // Obtener el ID del usuario que se está actualizando
        $usuario->setId($_POST['IdUsuario']);
        
        // Actualizar solo los campos que se envían
        if (isset($_POST['Codigo'])) {
            $usuario->setCodigo($_POST['Codigo']);
        }
        
        if (isset($_POST['PrimerNombre']) && isset($_POST['SegundoNombre'])) {
            $usuario->setNombres(trim($_POST['PrimerNombre'] . ' ' . $_POST['SegundoNombre']));
        }
        
        if (isset($_POST['PrimerApellido']) && isset($_POST['SegundoApellido'])) {
            $usuario->setApellidos(trim($_POST['PrimerApellido'] . ' ' . $_POST['SegundoApellido']));
        }
        
        if (isset($_POST['Rol'])) {
            $usuario->setRol($_POST['Rol']);
            
            // Actualizar grado e institución solo si es estudiante
            if ($_POST['Rol'] === 'ALUMNO') {
                $usuario->setGradoId(isset($_POST['GradoId']) ? $_POST['GradoId'] : null);
                $usuario->setInstitucionId(isset($_POST['InstitucionId']) ? $_POST['InstitucionId'] : null);
                // Actualizar sección si es proporcionada
                if (isset($_POST['Seccion'])) {
                    if ($_POST['Seccion'] === '' || $_POST['Seccion'] === null) {
                        $usuario->setSeccion(null);
                    } else if (!ctype_digit((string)$_POST['Seccion'])) {
                        throw new Exception('La sección debe ser numérica');
                    } else {
                        $usuario->setSeccion($_POST['Seccion']);
                    }
                }
            } else {
                // Limpiar grado e institución si no es estudiante
                $usuario->setGradoId(null);
                $usuario->setInstitucionId(null);
                $usuario->setSeccion(null);
            }
        }
        // Si no viene Rol pero sí Seccion, permitir actualización directa de sección manteniendo rol actual
        elseif (isset($_POST['Seccion'])) {
            if ($_POST['Seccion'] === '' || $_POST['Seccion'] === null) {
                $usuario->setSeccion(null);
            } else if (!ctype_digit((string)$_POST['Seccion'])) {
                throw new Exception('La sección debe ser numérica');
            } else {
                $usuario->setSeccion($_POST['Seccion']);
            }
        }
        
        // Actualizar contraseña solo si se proporciona una nueva (el modelo la hashea)
        if (!empty($_POST['Contraseña'])) {
            $usuario->setPassword($_POST['Contraseña']);
        }

        $json = array();

        try {
            $usuario->ActualizarUsuario();

            $json['name'] = 'position';
            $json['defaultValue'] = 'top-right';
            $json['msj'] = '<font color="#ffffff"><i class="fa fa-check"></i> Usuario actualizado correctamente</font>';
            $json['success'] = true;
        } catch (Exception $e) {
            $json['name'] = 'position';
            $json['defaultValue'] = 'top-right';
            $json['msj'] = '<font color="#ffffff"><i class="fa fa-exclamation-triangle"></i> Error al actualizar: ' . $e->getMessage() . '</font>';
            $json['success'] = false;
        }

        echo json_encode($json);
    }

    public function Desactivar()
    {
        $usuario = new Usuarios_model();
        $usuario->setId($_POST['IdUsuario']);

        $json = array();

        try {
            if ($usuario->EliminarUsuario()) { // eliminación lógica (activo=0)
                $json['name'] = 'position';
                $json['defaultValue'] = 'top-right';
                $json['msj'] = '<font color="#ffffff"><i class="fa fa-check"></i> Usuario desactivado correctamente</font>';
                $json['success'] = true;
            } else {
                throw new Exception('No se pudo desactivar el usuario');
            }
        } catch (Exception $e) {
            $json['name'] = 'position';
            $json['defaultValue'] = 'top-right';
            $json['msj'] = '<font color="#ffffff"><i class="fa fa-exclamation-triangle"></i> Error al desactivar: ' . $e->getMessage() . '</font>';
            $json['success'] = false;
        }

        echo json_encode($json);
    }

    public function Eliminar()
    {
        $usuario = new Usuarios_model();
        $usuario->setId($_POST['IdUsuario']);

        $json = array();

        try {
            if ($usuario->EliminarUsuario()) {
                $json['name'] = 'position';
                $json['defaultValue'] = 'top-right';
                $json['msj'] = '<font color="#ffffff"><i class="fa fa-check"></i> Usuario eliminado correctamente</font>';
                $json['success'] = true;
            } else {
                throw new Exception('No se pudo eliminar el usuario');
            }
        } catch (Exception $e) {
            $json['name'] = 'position';
            $json['defaultValue'] = 'top-right';
            $json['msj'] = '<font color="#ffffff"><i class="fa fa-exclamation-triangle"></i> Error al eliminar: ' . $e->getMessage() . '</font>';
            $json['success'] = false;
        }

        echo json_encode($json);
    }

    // Endpoints de datos para poblar selects
    public function ListarInstituciones()
    {
        header('Content-Type: application/json');
        try {
            $m = new Usuarios_model();
            $data = $m->ListarInstituciones();
            echo json_encode(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'msj' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function ListarGrados()
    {
        header('Content-Type: application/json');
        try {
            $m = new Usuarios_model();
            $data = $m->ListarGrados();
            echo json_encode(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'msj' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function ListarSecciones()
    {
        header('Content-Type: application/json');
        try {
            $m = new Usuarios_model();
            $data = $m->ListarSecciones();
            echo json_encode(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'msj' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function Obtener()
    {
        header('Content-Type: application/json');
        try {
            if (!isset($_GET['id']) && !isset($_POST['id'])) {
                throw new Exception('ID requerido');
            }
            $id = isset($_GET['id']) ? (int)$_GET['id'] : (int)$_POST['id'];
            $m = new Usuarios_model();
            $data = $m->ObtenerUsuario($id);
            if (!$data) {
                throw new Exception('Usuario no encontrado');
            }
            echo json_encode(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'msj' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function Tabla()
    {
        header('Content-Type: application/json');
        try {
            // Mantener estilo de conexión del ejemplo
            $Conexion = new ClaseConexion();
            $ConexionSql = $Conexion->CrearConexion();

            // Definiciones estilo legacy
            $sTabla = "FROM usuarios u
                       LEFT JOIN grados g ON g.id = u.grado_id AND g.activo = 1
                       LEFT JOIN instituciones i ON i.id = u.institucion_id AND i.activo = 1";

            // Columnas para ORDER y SEARCH (coinciden con índices de la DataTable)
            $aColumnas = array(
                "u.id",        // 0 id
                "u.codigo",    // 1 codigo
                "i.nombre",    // 2 instituto
                "u.nombres",   // 3 nombres
                "u.apellidos", // 4 apellidos
                "g.nombre",    // 5 grado
                "u.rol"        // 6 rol
            );

            // Columnas SELECT con alias para mostrar
            $aSelect = array(
                "u.id AS Id",
                "u.codigo AS Codigo",
                "COALESCE(i.nombre,'') AS Instituto",
                "u.nombres AS Nombres",
                "u.apellidos AS Apellidos",
                "COALESCE(g.nombre,'') AS Grado",
                "u.rol AS Rol"
            );

            $sIndexColumn = "u.id";

            // Paginación
            $sLimit = "";
            if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
                $start  = (int)$_GET['iDisplayStart'];
                $length = (int)$_GET['iDisplayLength'];
                $sLimit = "LIMIT $start, $length";
            }

            // Orden
            $sOrder = "";
            if (isset($_GET['iSortCol_0'])) {
                $sOrder = "ORDER BY ";
                $sortingCols = isset($_GET['iSortingCols']) ? (int)$_GET['iSortingCols'] : 1;
                for ($i = 0; $i < $sortingCols; $i++) {
                    $colIdx = (int)$_GET['iSortCol_' . $i];
                    $bSortable = isset($_GET['bSortable_' . $colIdx]) ? $_GET['bSortable_' . $colIdx] : "true";
                    if ($bSortable === "true" && isset($aColumnas[$colIdx])) {
                        $dir = (isset($_GET['sSortDir_' . $i]) && strtolower($_GET['sSortDir_' . $i]) === 'desc') ? 'DESC' : 'ASC';
                        $sOrder .= $aColumnas[$colIdx] . " " . $dir . ", ";
                    }
                }
                $sOrder = rtrim($sOrder, ", ");
            }

            // Filtro
            $sWhere = "WHERE u.activo = 1";
            if (!empty($_GET['sSearch'])) {
                $term = $_GET['sSearch'];
                $sWhere = "WHERE (";
                foreach ($aColumnas as $col) {
                    $sWhere .= $col . " LIKE '%" . str_replace("'", "''", $term) . "%' OR ";
                }
                $sWhere = substr_replace($sWhere, "", -4); // quitar último OR
                $sWhere .= ") AND u.activo = 1";
            }

            // Query principal al estilo legacy con SQL_CALC_FOUND_ROWS
            $sQuery = "
                SELECT SQL_CALC_FOUND_ROWS " . implode(", ", $aSelect) . "
                $sTabla
                $sWhere
                $sOrder
                $sLimit
            ";

            $rResult = $ConexionSql->prepare($sQuery);
            $rResult->execute();

            // Totales usando FOUND_ROWS() para filtrados
            $rFiltered = $ConexionSql->query("SELECT FOUND_ROWS() as cnt");
            $iTotalDisplayRecords = (int)$rFiltered->fetch(PDO::FETCH_ASSOC)['cnt'];

            // Total general (solo activos)
            $rTotal = $ConexionSql->query("SELECT COUNT(" . $sIndexColumn . ") AS cnt FROM usuarios u WHERE u.activo = 1");
            $iTotalRecords = (int)$rTotal->fetch(PDO::FETCH_ASSOC)['cnt'];

            // Respuesta
            $sEcho = isset($_GET['sEcho']) ? (int)$_GET['sEcho'] : 1;
            $output = array(
                // Moderno
                'draw' => $sEcho,
                'recordsTotal' => $iTotalRecords,
                'recordsFiltered' => $iTotalDisplayRecords,
                // Legacy
                'sEcho' => $sEcho,
                'iTotalRecords' => $iTotalRecords,
                'iTotalDisplayRecords' => $iTotalDisplayRecords,
                'aaData' => array()
            );

            while ($aRow = $rResult->fetch(PDO::FETCH_ASSOC)) {
                $row = array();
                // Orden de presentación según aliases en $aSelect
                $row[] = htmlspecialchars(isset($aRow['Id']) ? $aRow['Id'] : '');
                $row[] = htmlspecialchars(isset($aRow['Codigo']) ? $aRow['Codigo'] : '');
                $row[] = htmlspecialchars(isset($aRow['Instituto']) ? $aRow['Instituto'] : '');
                $row[] = htmlspecialchars(isset($aRow['Nombres']) ? $aRow['Nombres'] : '');
                $row[] = htmlspecialchars(isset($aRow['Apellidos']) ? $aRow['Apellidos'] : '');
                $row[] = htmlspecialchars(isset($aRow['Grado']) ? $aRow['Grado'] : '');
                // Rol con badge
                $rol = isset($aRow['Rol']) ? $aRow['Rol'] : '';
                switch ($rol) {
                    case 'ADMIN':    $row[] = '<span class="badge bg-primary">Admin</span>'; break;
                    case 'DIRECTOR': $row[] = '<span class="badge bg-info text-dark">Director</span>'; break;
                    case 'DOCENTE':  $row[] = '<span class="badge bg-secondary">Docente</span>'; break;
                    case 'ALUMNO':   $row[] = '<span class="badge bg-success">Alumno</span>'; break;
                    default:         $row[] = htmlspecialchars($rol); break;
                }

                // Acciones
                $id = (int)(isset($aRow['Id']) ? $aRow['Id'] : 0);
                $row[] = '<button class="btn btn-warning btn-sm" onclick="DatosUsuario(' . $id . ')"><i class="fa fa-edit"></i> Editar</button>'; 
                $row[] = '<button class="btn btn-danger btn-sm" onclick="EliminarDatos(' . $id . ')"><i class="fa fa-trash"></i> Eliminar</button>';

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
        } catch (Exception $e) {
            echo json_encode(array(
                'draw' => 1,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'sEcho' => 1,
                'iTotalRecords' => 0,
                'iTotalDisplayRecords' => 0,
                'aaData' => array(),
                'error' => 'Error en Tabla(): ' . $e->getMessage()
            ));
        }
    }

}
?>