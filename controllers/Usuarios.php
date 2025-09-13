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
        header('Content-Type: application/json');
        $json = array();

        try {
            // --- DEBUG: registrar raw POST para diagnosticar campos faltantes ---
            $debugPath = __DIR__ . '/../res/logs/post_debug.txt';
            @mkdir(dirname($debugPath), 0777, true);
            file_put_contents($debugPath, date('c') . ' ' . json_encode($_POST, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);

            // Función auxiliar para leer posibles variantes de nombres de campo
            $get = function ($keys, $default = null) {
                foreach ((array)$keys as $k) {
                    if (isset($_POST[$k])) return $_POST[$k];
                }
                return $default;
            };

            $usuario = new Usuarios_model();

            // Leer exactamente los campos que espera el procedimiento almacenado
            $codigo = $get(array('Codigo', 'codigo'), '');
            $nombres = $get(array('nombres', 'Nombres'), '');
            $apellidos = $get(array('apellidos', 'Apellidos'), '');

            $rol = $get(array('Rol', 'rol'), 'ALUMNO');
            $rol = $rol === '' ? 'ALUMNO' : $rol;

            $gradoId = $get(array('GradoId', 'grado_id', 'Grado', 'grado'), '');
            $institucionId = $get(array('InstitucionId', 'institucion_id', 'Instituto', 'instituto'), null);
            $seccion = $get(array('Seccion', 'seccion'), '');
            $password = $get(array('password', 'contraseña', 'Contraseña', 'password_plain'), '');

            // Asignar al modelo (usar exactamente nombres/apellidos como en el SP)
            $usuario->setCodigo($codigo);
            $usuario->setNombres(trim($nombres));
            $usuario->setApellidos(trim($apellidos));
            $usuario->setRol($rol);

                if (strtoupper($rol) === 'ALUMNO') {
                    // Validaciones para alumno
                    if ($seccion === '' || $seccion === null) {
                        throw new Exception('La sección es requerida para alumnos');
                    }
                    if (!ctype_digit((string)$seccion)) {
                        throw new Exception('La sección debe ser numérica');
                    }
                    $usuario->setGradoId($gradoId);
                    $usuario->setInstitucionId($institucionId !== null ? $institucionId : 1);
                    $usuario->setSeccion($seccion);
                } else {
                    // Para otros roles, respetar valores enviados (si vienen vacíos, el modelo los convertirá a NULL)
                    $usuario->setGradoId($gradoId);
                    $usuario->setInstitucionId($institucionId);
                    // La columna seccion en la tabla es NOT NULL, usar 0 cuando no aplique
                    $usuario->setSeccion(0);
            }

            // Contraseña (el modelo la hashea)
            if (!empty($password)) {
                $usuario->setPassword($password);
            } else {
                // Si no viene, usar código como contraseña por defecto
                $usuario->setPassword($codigo);
            }

            // Ejecutar inserción
            $usuario->InsertarUsuario();

            $json['name'] = 'position';
            $json['defaultValue'] = 'top-right';
            $json['msj'] = '<font color="#ffffff"><i class="fa fa-check"></i> Usuario registrado correctamente</font>';
            $json['success'] = true;
        } catch (Exception $e) {
            // Asegurar que cualquier excepción devuelva JSON válido al cliente
            $json['name'] = 'position';
            $json['defaultValue'] = 'top-right';
            $json['msj'] = '<font color="#ffffff"><i class="fa fa-exclamation-triangle"></i> Error al registrar: ' . $e->getMessage() . '</font>';
            $json['success'] = false;
        }

    if (ob_get_length()) ob_clean();
    echo json_encode($json);
    }

    public function Actualizar()
    {
        header('Content-Type: application/json');
        $usuario = new Usuarios_model();

        // DEBUG: registrar raw POST de actualizaciones para diagnosticar problemas
        $debugPathUpd = __DIR__ . '/../res/logs/post_debug_update.txt';
        @mkdir(dirname($debugPathUpd), 0777, true);
        file_put_contents($debugPathUpd, date('c') . ' ' . json_encode($_POST, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);

        // Obtener el ID del usuario que se está actualizando
        $usuario->setId($_POST['IdUsuario']);
        
        // Actualizar solo los campos que se envían (usar 'nombres' y 'apellidos' como en el SP)
        if (isset($_POST['Codigo'])) {
            $usuario->setCodigo($_POST['Codigo']);
        }

        if (isset($_POST['nombres']) || isset($_POST['Nombres'])) {
            $n = isset($_POST['nombres']) ? $_POST['nombres'] : $_POST['Nombres'];
            $usuario->setNombres(trim($n));
        }

        if (isset($_POST['apellidos']) || isset($_POST['Apellidos'])) {
            $a = isset($_POST['apellidos']) ? $_POST['apellidos'] : $_POST['Apellidos'];
            $usuario->setApellidos(trim($a));
        }

        // Si el cliente envía GradoId / InstitucionId, respetarlos (no los sobreescribiremos luego)
        if (isset($_POST['GradoId'])) {
            $usuario->setGradoId($_POST['GradoId']);
        }
        if (isset($_POST['InstitucionId'])) {
            $usuario->setInstitucionId($_POST['InstitucionId']);
        }
        
        if (isset($_POST['Rol'])) {
            $usuario->setRol($_POST['Rol']);
            
            // Actualizar sección solo si es estudiante; los IDs ya fueron leídos si vinieron
                if ($_POST['Rol'] === 'ALUMNO') {
                // Actualizar sección si es proporcionada
                if (isset($_POST['Seccion'])) {
                    if ($_POST['Seccion'] === '' || $_POST['Seccion'] === null) {
                        // No permitir NULL en seccion, usar 0 por compatibilidad
                        $usuario->setSeccion(0);
                    } else if (!ctype_digit((string)$_POST['Seccion'])) {
                        throw new Exception('La sección debe ser numérica');
                    } else {
                        $usuario->setSeccion($_POST['Seccion']);
                    }
                }
            } else {
                // No cambiar GradoId/InstitucionId aquí: respetar valores ya establecidos
                // Usar 0 para seccion en roles no alumno
                $usuario->setSeccion(0);
            }
        }
        // Si no viene Rol pero sí Seccion, permitir actualización directa de sección manteniendo rol actual
        elseif (isset($_POST['Seccion'])) {
            if ($_POST['Seccion'] === '' || $_POST['Seccion'] === null) {
                // No permitir NULL en seccion
                $usuario->setSeccion(0);
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

    if (ob_get_length()) ob_clean();
    echo json_encode($json);
    }

    public function Desactivar()
    {
        header('Content-Type: application/json');
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

    if (ob_get_length()) ob_clean();
    echo json_encode($json);
    }

    public function Eliminar()
    {
        header('Content-Type: application/json');
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

    if (ob_get_length()) ob_clean();
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