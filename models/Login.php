<?php

class Login_model {

    private $db;
    private $instance;
    private $vehiculos;
    private $Valor;
    private $Conexion;
    private $ConexionSql;

    public function __construct(){
        $this->Conexion = new ClaseConexion();
    }

    // Método para validar credenciales de usuario
    public function ValidarCredenciales($codigo, $password)
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();

            $sql = "SELECT id, codigo, nombres, apellidos, password_hash, rol, activo
                    FROM usuarios
                    WHERE codigo = ? AND activo = 1";
            $stmt = $this->ConexionSql->prepare($sql);
            $stmt->bindParam(1, $codigo, PDO::PARAM_STR);
            $stmt->execute();

            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$usuario) {
                throw new Exception('Usuario no existe o inactivo');
            }

            $hash = (string)$usuario['password_hash'];
            $ok = false;
            // bcrypt/argon2
            if (preg_match('/^\$2[aby]\$|^\$argon2/', $hash)) {
                $ok = password_verify($password, $hash);
            } else {
                // Compatibilidad con hash legacy o texto plano
                $pass = (string)$password;
                if (strlen($hash) === 32 && ctype_xdigit($hash)) {
                    $ok = hash_equals(strtolower($hash), md5($pass));
                } elseif (strlen($hash) === 40 && ctype_xdigit($hash)) {
                    $ok = hash_equals(strtolower($hash), sha1($pass));
                } else {
                    $ok = hash_equals($hash, $pass);
                }
            }

            if (!$ok) {
                throw new Exception('Contraseña incorrecta');
            }

            unset($usuario['password_hash']);
            return $usuario;

        } catch (Exception $e) {
            throw new Exception("Error al validar credenciales: " . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // Método para obtener información completa del usuario
    public function ObtenerUsuarioCompleto($id)
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();

            $sql = "SELECT u.id, u.codigo, u.nombres, u.apellidos, u.rol, u.institucion_id, u.grado_id, u.seccion,
                           i.nombre as institucion_nombre, g.nombre as grado_nombre
                    FROM usuarios u
                    LEFT JOIN instituciones i ON u.institucion_id = i.id
                    LEFT JOIN grados g ON u.grado_id = g.id
                    WHERE u.id = ? AND u.activo = 1";

            $stmt = $this->ConexionSql->prepare($sql);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            throw new Exception("Error al obtener usuario: " . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    public function InsertarToken()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "TRUNCATE TABLE temp;";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
            $this->Procedure->execute();

            $this->SentenciaSql = "INSERT INTO temp (valor) VALUES (?);";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
            $this->Procedure->bindParam(1, $this->getValor());
            $this->Procedure->execute();
        }
        catch (Exception $e) {
            echo "ERROR AL INSERTAR REGISTRO " . $this->e->getMessage();
        }
        finally {
            $this->Conexion->CerrarConexion();
        }
    }

    public function ObtenetToken()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "SELECT * FROM temp";
            $this->Result = array();
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
            $this->Procedure->execute();
            return $this->Procedure->fetchAll(PDO::FETCH_OBJ);
        }
        catch(Exception $e) {
            die($e->getMessage());
        }
        finally {
            $this->Conexion->CerrarConexion();
        }
    }

    public function ActualizarPass($pass,$correo)
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "update usuarios set Contraseña = '".$pass."' where  correo = '".$correo."'";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
            $this->Procedure->execute();
        }
        catch (Exception $e) {
            echo "ERROR AL INSERTAR REGISTRO " . $e->getMessage();
        }
        finally {
            $this->Conexion->CerrarConexion();
        }
    }

    public function getValor ()
    {
        return $this->Valor;
    }

    public function setValor ($valor)
    {
        $this->Valor = $valor; 
    }
}
?>

