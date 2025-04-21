<?php
define('METHOD', 'AES-256-CBC');
define('SECRET_KEY', '$Eli@2021');
define('SECRET_IV', '101712');

class Usuarios_model
{
    // Atributos de la clase
    private $Codigo;
    private $PrimerNombre, $SegundoNombre, $PrimerApellido, $SegundoApellido, $Dpi, $Usuario, $Password, $TipoUser, $Estado;
    private $Correo, $Foto, $Auditxml;
    private $ConexionSql;
    private $Conexion;
    private $SentenciaSql;
    private $Procedure;

    // Constructor de la clase
    public function __construct()
    {
        $this->Conexion = new ClaseConexion();
    }

    // Método para insertar un usuario
    public function InsertarUsuario()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "CALL InsertarUsuario(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

            $this->Procedure->bindParam(1, $this->getDpi());
            $this->Procedure->bindParam(2, $this->getPrimerNombre());
            $this->Procedure->bindParam(3, $this->getSegundoNombre());
            $this->Procedure->bindParam(4, $this->getPrimerApellido());
            $this->Procedure->bindParam(5, $this->getSegundoApellido());
            $this->Procedure->bindParam(6, $this->getCorreo());
            $this->Procedure->bindParam(7, $this->getPerfil()); // Rol
            $this->Procedure->bindParam(8, $this->getUsuario());
            $this->Procedure->bindParam(9, $this->getPassword());
            $this->Procedure->bindParam(10, $this->getFoto()); // Ruta de la foto
            $this->Procedure->bindParam(11, $this->getAuditxml());



            $this->Procedure->execute();
        } catch (Exception $e) {
            echo "ERROR AL INSERTAR REGISTRO: " . $e->getMessage();
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // Método para actualizar un usuario
    public function ActualizarUsuario()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "CALL ActualizarUsuario(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

            $this->Procedure->bindParam(1, $this->getCodigo()); // IdUsuario
            $this->Procedure->bindParam(2, $this->getDpi());
            $this->Procedure->bindParam(3, $this->getPrimerNombre());
            $this->Procedure->bindParam(4, $this->getSegundoNombre());
            $this->Procedure->bindParam(5, $this->getPrimerApellido());
            $this->Procedure->bindParam(6, $this->getSegundoApellido());
            $this->Procedure->bindParam(7, $this->getCorreo());
            $this->Procedure->bindParam(8, $this->getPerfil()); // Rol
            $this->Procedure->bindParam(9, $this->getUsuario());
            $this->Procedure->bindParam(10, $this->getPassword());
            $this->Procedure->bindParam(11, $this->getFoto()); // Ruta de la foto
            $this->Procedure->bindParam(12, $this->getEstado());
            $this->Procedure->bindParam(13, $this->getAuditxml());

            $this->Procedure->execute();
        } catch (Exception $e) {
            echo "ERROR AL ACTUALIZAR REGISTRO: " . $e->getMessage();
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // Método para eliminar un usuario
    public function EliminarUsuario()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "CALL EliminarUsuario(?, ?)";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

            $this->Procedure->bindParam(1, $this->getCodigo()); // IdUsuario
            $this->Procedure->bindParam(2, $this->getAuditxml()); // Audit XML

            $this->Procedure->execute();
        } catch (Exception $e) {
            echo "ERROR AL ELIMINAR REGISTRO: " . $e->getMessage();
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // Método para obtener un usuario por código
    public function ObtenerUsuarioPorCodigo()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "SELECT IdUsuario, Dpi, PrimerNombre, SegundoNombre, PrimerApellido, SegundoApellido, Correo, Rol, Usuario, Foto, Estado 
                                   FROM Usuarios 
                                   WHERE IdUsuario = ?";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

            $this->Procedure->bindParam(1, $this->getCodigo()); // IdUsuario

            $this->Procedure->execute();

            // Retornar el resultado como un array asociativo
            return $this->Procedure->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo "ERROR AL OBTENER USUARIO: " . $e->getMessage();
            return null;
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }



    
    // Método para validar un usuario
    public function ValidarUsuario($User)
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "SELECT IdUsuario, PrimerNombre, PrimerApellido, Rol, Usuario, Contraseña 
                                   FROM Usuarios 
                                   WHERE Usuario = ?";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
            $this->Procedure->bindParam(1, $User);
            $this->Procedure->execute();

            // Retornar el resultado como un array asociativo
            return $this->Procedure->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo "ERROR AL VALIDAR USUARIO: " . $e->getMessage();
            return null;
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // Método para registrar la asistencia de un usuario
    public function RegistrarAsistencia($Usuarioid, $Tipo, $AuditXML)
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "CALL InsertarAsistenciaActual(?, ?, ?)";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

            $this->Procedure->bindParam(1, $Usuarioid);
            $this->Procedure->bindParam(2, $Tipo);
            $this->Procedure->bindParam(3, $AuditXML);

            $this->Procedure->execute();
        } catch (Exception $e) {
            echo "ERROR AL REGISTRAR ASISTENCIA: " . $e->getMessage();
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // Getters y setters necesarios
    public function getCodigo()
    {
        return $this->Codigo;
    }

    public function setCodigo($Codigo)
    {
        $this->Codigo = $Codigo;
    }

    public function getDpi()
    {
        return $this->Dpi;
    }

    public function setDpi($Dpi)
    {
        $this->Dpi = $Dpi;
    }

    public function getPrimerNombre()
    {
        return $this->PrimerNombre;
    }

    public function setPrimerNombre($PrimerNombre)
    {
        $this->PrimerNombre = $PrimerNombre;
    }

    public function getSegundoNombre()
    {
        return $this->SegundoNombre;
    }

    public function setSegundoNombre($SegundoNombre)
    {
        $this->SegundoNombre = $SegundoNombre;
    }

    public function getPrimerApellido()
    {
        return $this->PrimerApellido;
    }

    public function setPrimerApellido($PrimerApellido)
    {
        $this->PrimerApellido = $PrimerApellido;
    }

    public function getSegundoApellido()
    {
        return $this->SegundoApellido;
    }

    public function setSegundoApellido($SegundoApellido)
    {
        $this->SegundoApellido = $SegundoApellido;
    }

    public function getCorreo()
    {
        return $this->Correo;
    }

    public function setCorreo($Correo)
    {
        $this->Correo = $Correo;
    }

    public function getUsuario()
    {
        return $this->Usuario;
    }

    public function setUsuario($Usuario)
    {
        $this->Usuario = $Usuario;
    }

    public function getPassword()
    {
        return $this->Password;
    }

    public function setPassword($Password)
    {
        $this->Password = $Password;
    }

    public function getPerfil()
    {
        return $this->TipoUser; // Si "TipoUser" representa el rol
    }

    public function setPerfil($Rol)
    {
        $this->TipoUser = $Rol; // Cambiado de Perfil a Rol
    }

    public function getEstado()
    {
        return $this->Estado;
    }

    public function setEstado($Estado)
    {
        $this->Estado = $Estado;
    }

    public function getFoto()
    {
        return $this->Foto;
    }

    public function setFoto($Foto)
    {
        $this->Foto = $Foto;
    }

    public function getAuditxml()
    {
        return $this->Auditxml;
    }

    public function setAuditxml($Auditxml)
    {
        $this->Auditxml = $Auditxml;
    }
}
?>

