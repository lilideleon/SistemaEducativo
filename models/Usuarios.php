<?php

class Usuarios_model
{
    // Atributos de la clase
    private $id;
    private $codigo;
    private $nombres;
    private $apellidos;
    private $grado_id;
    private $institucion_id;
    private $rol;
    private $password_hash;
    private $activo;
    private $creado_en;
    
    // Objetos de conexión
    private $ConexionSql;
    private $Conexion;
    private $SentenciaSql;
    private $Procedure;

    // Constructor de la clase
    public function __construct()
    {
        $this->Conexion = new ClaseConexion();
    }

    // Método para validar un usuario
    public function ValidarUsuario($username)
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "SELECT id, nombres, apellidos, rol, username, password_hash 
                                 FROM usuarios 
                                 WHERE username = ? AND activo = 1";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
            $this->Procedure->bindParam(1, $username);
            $this->Procedure->execute();

            // Retornar el resultado como un array asociativo
            return $this->Procedure->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error al validar usuario: " . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // Método para insertar un usuario
    public function InsertarUsuario()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "CALL sp_usuarios_insert_min(?, ?, ?, ?, ?, ?, ?, @id)";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

            $this->Procedure->bindParam(1, $this->getCodigo(), PDO::PARAM_STR);
            $this->Procedure->bindParam(2, $this->getNombres(), PDO::PARAM_STR);
            $this->Procedure->bindParam(3, $this->getApellidos(), PDO::PARAM_STR);
            $this->Procedure->bindParam(4, $this->getGradoId(), PDO::PARAM_INT);
            $this->Procedure->bindParam(5, $this->getInstitucionId(), PDO::PARAM_INT);
            $this->Procedure->bindParam(6, $this->getRol(), PDO::PARAM_STR);
            $this->Procedure->bindParam(7, $this->getPasswordHash(), PDO::PARAM_STR);

            $this->Procedure->execute();
            
            // Obtener el ID generado
            $result = $this->ConexionSql->query("SELECT @id as id");
            $row = $result->fetch(PDO::FETCH_ASSOC);
            return $row['id'];
            
        } catch (Exception $e) {
            throw new Exception("Error al insertar usuario: " . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // Método para actualizar un usuario (actualización parcial)
    public function ActualizarUsuario()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "CALL sp_usuarios_update_min(?, ?, ?, ?, ?, ?, ?, ?, @rows_affected)";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

            $this->Procedure->bindParam(1, $this->getId(), PDO::PARAM_INT);
            $this->Procedure->bindParam(2, $this->getCodigo(), PDO::PARAM_STR);
            $this->Procedure->bindParam(3, $this->getNombres(), PDO::PARAM_STR);
            $this->Procedure->bindParam(4, $this->getApellidos(), PDO::PARAM_STR);
            $this->Procedure->bindParam(5, $this->getGradoId(), PDO::PARAM_INT);
            $this->Procedure->bindParam(6, $this->getInstitucionId(), PDO::PARAM_INT);
            $this->Procedure->bindParam(7, $this->getRol(), PDO::PARAM_STR);
            $this->Procedure->bindParam(8, $this->getPasswordHash(), PDO::PARAM_STR);

            $this->Procedure->execute();
            
            // Obtener el número de filas afectadas
            $result = $this->ConexionSql->query("SELECT @rows_affected as rows_affected");
            $row = $result->fetch(PDO::FETCH_ASSOC);
            return $row['rows_affected'] > 0;
            
        } catch (Exception $e) {
            throw new Exception("Error al actualizar usuario: " . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // Método para eliminar un usuario (eliminación lógica)
    public function EliminarUsuario()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "CALL sp_usuarios_delete_logico_min(?, @rows_affected)";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
            $this->Procedure->bindParam(1, $this->getId(), PDO::PARAM_INT);
            $this->Procedure->execute();
            
            // Obtener el número de filas afectadas
            $result = $this->ConexionSql->query("SELECT @rows_affected as rows_affected");
            $row = $result->fetch(PDO::FETCH_ASSOC);
            return $row['rows_affected'] > 0;
        } catch (Exception $e) {
            throw new Exception("Error al eliminar usuario: " . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }


    public function ListarInstituciones()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "SELECT id, nombre, codigo, tipo, direccion 
                                FROM instituciones 
                                WHERE activo = 1
                                ORDER BY nombre ASC";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
            $this->Procedure->execute();

            // Retornar todos los resultados como arreglo asociativo
            return $this->Procedure->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error al listar instituciones: " . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    public function ListarGrados()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "SELECT id, nombre 
                                FROM grados 
                                WHERE activo = 1
                                ORDER BY id ASC";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
            $this->Procedure->execute();

            return $this->Procedure->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error al listar grados: " . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }



    // Getters y Setters
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = (int)$id;
    }

    public function getCodigo()
    {
        return $this->codigo;
    }

    public function setCodigo($codigo)
    {
        $this->codigo = trim($codigo);
    }

    public function getPasswordHash()
    {
        return $this->password_hash;
    }

    public function setPasswordHash($password_hash)
    {
        $this->password_hash = $password_hash;
    }

    // Alias para compatibilidad
    public function getPassword()
    {
        return $this->password_hash;
    }

    public function setPassword($password)
    {
        if (!empty($password)) {
            $this->password_hash = password_hash($password, PASSWORD_BCRYPT);
        }
    }

    public function getRol()
    {
        return $this->rol;
    }

    public function setRol($rol)
    {
        $roles_validos = ['ADMIN', 'DIRECTOR', 'DOCENTE', 'ALUMNO'];
        if (in_array(strtoupper($rol), $roles_validos)) {
            $this->rol = strtoupper($rol);
        } else {
            throw new Exception("Rol no válido");
        }
    }

    // Alias para compatibilidad
    public function getPerfil()
    {
        return $this->rol;
    }

    public function setPerfil($rol)
    {
        $this->rol = $rol;
    }

    public function getNombres()
    {
        return $this->nombres;
    }

    public function setNombres($nombres)
    {
        $this->nombres = ucwords(strtolower(trim($nombres)));
    }

    public function getApellidos()
    {
        return $this->apellidos;
    }

    public function setApellidos($apellidos)
    {
        $this->apellidos = ucwords(strtolower(trim($apellidos)));
    }

    // Alias para compatibilidad
    public function getPrimerApellido()
    {
        return $this->apellidos;
    }

    public function setPrimerApellido($apellidos)
    {
        $this->apellidos = $apellidos;
    }

    public function getInstitucionId()
    {
        return $this->institucion_id;
    }

    public function setInstitucionId($institucion_id)
    {
        if ($institucion_id === '' || $institucion_id === null) {
            $this->institucion_id = null;
        } else {
            $this->institucion_id = (int)$institucion_id;
        }
    }

    public function getGradoId()
    {
        return $this->grado_id;
    }

    public function setGradoId($grado_id)
    {
        if ($grado_id === '' || $grado_id === null) {
            $this->grado_id = null;
        } else {
            $this->grado_id = (int)$grado_id;
        }
    }

    public function getActivo()
    {
        return (bool)$this->activo;
    }

    public function setActivo($activo)
    {
        $this->activo = (bool)$activo;
    }

    public function getEstado()
    {
        return $this->activo ? 'Activo' : 'Inactivo';
    }

    public function setEstado($estado)
    {
        $this->activo = (bool)$estado;
    }
    
    public function getCreadoEn()
    {
        return $this->creado_en;
    }
}
?>
