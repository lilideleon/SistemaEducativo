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
    private $seccion;
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

    // Método para insertar un usuario (directo, sin procedimientos)
    public function InsertarUsuario()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $sql = "INSERT INTO usuarios (codigo, nombres, apellidos, rol, seccion, institucion_id, grado_id, activo, password_hash)
                    VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?)";
            $stmt = $this->ConexionSql->prepare($sql);
            $stmt->bindParam(1, $this->codigo, PDO::PARAM_STR);
            $stmt->bindParam(2, $this->nombres, PDO::PARAM_STR);
            $stmt->bindParam(3, $this->apellidos, PDO::PARAM_STR);
            $stmt->bindParam(4, $this->rol, PDO::PARAM_STR);
            $seccion = $this->getSeccion();
            if ($seccion === null) $seccion = null;
            $stmt->bindParam(5, $seccion, $seccion === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $institucion_id = $this->getInstitucionId();
            if ($institucion_id === null) $institucion_id = null;
            $stmt->bindParam(6, $institucion_id, $institucion_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $grado_id = $this->getGradoId();
            if ($grado_id === null) $grado_id = null;
            $stmt->bindParam(7, $grado_id, $grado_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(8, $this->password_hash, PDO::PARAM_STR);
            $stmt->execute();
            return $this->ConexionSql->lastInsertId();
        } catch (Exception $e) {
            throw new Exception("Error al insertar usuario: " . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // Método para actualizar un usuario (directo, sin procedimientos)
    public function ActualizarUsuario($updatePassword = false)
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            if ($updatePassword) {
                $sql = "UPDATE usuarios SET codigo = ?, nombres = ?, apellidos = ?, grado_id = ?, institucion_id = ?, rol = ?, seccion = ?, password_hash = ? WHERE id = ?";
            } else {
                $sql = "UPDATE usuarios SET codigo = ?, nombres = ?, apellidos = ?, grado_id = ?, institucion_id = ?, rol = ?, seccion = ? WHERE id = ?";
            }
            $stmt = $this->ConexionSql->prepare($sql);
            $stmt->bindParam(1, $this->codigo, PDO::PARAM_STR);
            $stmt->bindParam(2, $this->nombres, PDO::PARAM_STR);
            $stmt->bindParam(3, $this->apellidos, PDO::PARAM_STR);
            $grado_id = $this->getGradoId();
            if ($grado_id === null) $grado_id = null;
            $stmt->bindParam(4, $grado_id, $grado_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $institucion_id = $this->getInstitucionId();
            if ($institucion_id === null) $institucion_id = null;
            $stmt->bindParam(5, $institucion_id, $institucion_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(6, $this->rol, PDO::PARAM_STR);
            $seccion = $this->getSeccion();
            if ($seccion === null) $seccion = null;
            $stmt->bindParam(7, $seccion, $seccion === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            if ($updatePassword) {
                $stmt->bindParam(8, $this->password_hash, PDO::PARAM_STR);
                $stmt->bindParam(9, $this->id, PDO::PARAM_INT);
            } else {
                $stmt->bindParam(8, $this->id, PDO::PARAM_INT);
            }
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            throw new Exception("Error al actualizar usuario: " . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // Método para eliminar un usuario (eliminación lógica sin procedimientos)
    public function EliminarUsuario()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            // Eliminar lógicamente (activo = 0) de forma directa
            $sql = "UPDATE usuarios SET activo = 0 WHERE id = :id";
            $stmt = $this->ConexionSql->prepare($sql);
            $stmt->bindValue(':id', $this->getId(), PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount() > 0;
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

    public function ListarSecciones()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "SELECT id, nombre FROM seccion ORDER BY nombre ASC";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
            $this->Procedure->execute();

            return $this->Procedure->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error al listar secciones: " . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    public function ObtenerUsuario($id)
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "SELECT id, codigo, nombres, apellidos, grado_id, institucion_id, rol, seccion
                                   FROM usuarios
                                   WHERE id = ? AND activo = 1";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
            $this->Procedure->bindParam(1, $id, PDO::PARAM_INT);
            $this->Procedure->execute();
            return $this->Procedure->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error al obtener usuario: " . $e->getMessage());
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
        $n = trim((string)$nombres);
        // Normalizar respetando multibyte (tildes)
        if (!empty($n)) {
            $this->nombres = mb_convert_case(mb_strtolower($n, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
        } else {
            $this->nombres = '';
        }
    }

    public function getApellidos()
    {
        return $this->apellidos;
    }

    public function setApellidos($apellidos)
    {
        $a = trim((string)$apellidos);
        if (!empty($a)) {
            $this->apellidos = mb_convert_case(mb_strtolower($a, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
        } else {
            $this->apellidos = '';
        }
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

    public function getSeccion()
    {
        return $this->seccion;
    }

    public function setSeccion($seccion)
    {
        if ($seccion === '' || $seccion === null) {
            $this->seccion = null; // permitir NULL cuando no aplique (no alumno)
        } else {
            $this->seccion = (int)$seccion;
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
