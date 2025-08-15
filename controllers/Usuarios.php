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

		//quemamos los datos para validar que esta parte funcione con datos quemandos

        $usuario->setDpi($_POST['Dpi']);
        $usuario->setPrimerNombre($_POST['PrimerNombre']);
        $usuario->setSegundoNombre($_POST['SegundoNombre']);
        $usuario->setPrimerApellido($_POST['PrimerApellido']);
        $usuario->setSegundoApellido($_POST['SegundoApellido']);
        $usuario->setCorreo($_POST['Correo']);
        $usuario->setPerfil($_POST['Rol']); // Rol
        $usuario->setUsuario($_POST['Usuario']);
        $usuario->setPassword(password_hash($_POST['Contraseña'], PASSWORD_BCRYPT)); // Encriptar contraseña
        $usuario->setFoto(isset($_POST['Foto']) ? $_POST['Foto'] : null); // Ruta de la foto
        $usuario->setAuditxml(isset($_POST['Auditxml']) ? $_POST['Auditxml'] : null); // Audit XML opcional

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
        $usuario->setCodigo($_POST['IdUsuario']); // IdUsuario

        // Setear todos los campos basados en los datos que vienen por POST
        $usuario->setDpi($_POST['Dpi']);
        $usuario->setPrimerNombre($_POST['PrimerNombre']);
        $usuario->setSegundoNombre($_POST['SegundoNombre']);
        $usuario->setPrimerApellido($_POST['PrimerApellido']);
        $usuario->setSegundoApellido($_POST['SegundoApellido']);
        $usuario->setCorreo($_POST['Correo']);
        $usuario->setPerfil($_POST['Rol']); // Rol
        $usuario->setUsuario($_POST['Usuario']);
        
        // Validar si la contraseña fue enviada
        if (!empty($_POST['Contraseña'])) {
            $usuario->setPassword(password_hash($_POST['Contraseña'], PASSWORD_BCRYPT)); // Encriptar contraseña
        }

        // Validar si la foto fue enviada
        $usuario->setFoto(isset($_POST['Foto']) ? $_POST['Foto'] : null); // Ruta de la foto

        // Setear el estado
        $usuario->setEstado(1); // Activo por defecto

        // Validar si el Auditxml fue enviado
        $usuario->setAuditxml(isset($_POST['Auditxml']) ? $_POST['Auditxml'] : null); // Audit XML opcional

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

        // Mandar los valores recibidos del formulario a través del método POST
        $usuario->setIdUsuario($_POST['IdUsuario']);

        $json = array();

        try {
            $usuario->DesactivarUsuario();

            $json['name'] = 'position';
            $json['defaultValue'] = 'top-right';
            $json['msj'] = '<font color="#ffffff"><i class="fa fa-check"></i> Desactivado</font>';
            $json['success'] = true;
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

        // Obtener el ID del usuario que se está eliminando
        $usuario->setCodigo($_POST['IdUsuario']); // IdUsuario

        // Validar si el Auditxml fue enviado
        $usuario->setAuditxml(isset($_POST['Auditxml']) ? $_POST['Auditxml'] : null); // Audit XML opcional

        $json = array();

        try {
            $usuario->EliminarUsuario();

            $json['name'] = 'position';
            $json['defaultValue'] = 'top-right';
            $json['msj'] = '<font color="#ffffff"><i class="fa fa-check"></i> Usuario eliminado correctamente</font>';
            $json['success'] = true;
        } catch (Exception $e) {
            $json['name'] = 'position';
            $json['defaultValue'] = 'top-right';
            $json['msj'] = '<font color="#ffffff"><i class="fa fa-exclamation-triangle"></i> Error al eliminar: ' . $e->getMessage() . '</font>';
            $json['success'] = false;
        }

        echo json_encode($json);
    }

    public function Tabla()
    {
        $Conexion = new ClaseConexion();
        $ConexionSql = $Conexion->CrearConexion();

        $sTabla = "Usuarios";
        $aColumnas = array("IdUsuario", "Dpi", "PrimerNombre", "PrimerApellido", "Usuario", "Rol", "Estado");
        $sIndexColumn = "IdUsuario";

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
                if ($col === "Rol") {
                    // Mostrar texto según el valor del Rol
                    $row[] = $aRow[$col] == 1
                        ? '<span class="badge bg-primary">Administrador</span>'
                        : '<span class="badge bg-secondary">Vendedor</span>';
                } elseif ($col === "Estado") {
                    // Mostrar texto según el valor del Estado
                    $row[] = $aRow[$col] == 1
                        ? '<span class="badge bg-success">Activo</span>'
                        : '<span class="badge bg-danger">Inactivo</span>';
                } else {
                    $row[] = $aRow[$col];
                }
            }

            // Agregar botones de Editar y Eliminar
            $row[] = '
                <button class="btn btn-warning btn-sm" onclick="DatosUsuario(' . $aRow['IdUsuario'] . ')">
                    <i class="fa fa-edit"></i> Editar
                </button>
                <button class="btn btn-danger btn-sm" onclick="EliminarDatos(' . $aRow['IdUsuario'] . ')">
                    <i class="fa fa-trash"></i> Eliminar
                </button>
            ';

            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function getDatos()
    {
        $usuario = new Usuarios_model();
        $usuario->setCodigo($_POST['IdUsuario']); // Recibir el ID del usuario desde el frontend

        $datos = $usuario->ObtenerUsuarioPorCodigo();

        echo json_encode($datos); // Devolver los datos en formato JSON
    }
}
?>