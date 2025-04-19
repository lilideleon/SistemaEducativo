<?php
// CLASE MODELO PRODUCTOS

class Productos_model
{
    // ATRIBUTOS DE LA CLASE
    private $IdProducto;
    private $Nombre;
    private $PrecioCosto;
    private $PrecioVenta;
    private $Descripcion;
    private $Estado;
    private $AuditXML;
    private $Imagen; // Nuevo atributo

    private $ConexionSql;
    private $Conexion;
    private $SentenciaSql;
    private $Procedure;
    private $Result;

    // CONSTRUCTOR DE LA CLASE
    public function __construct()
    {
        $this->Conexion = new ClaseConexion();
    }

    // MÉTODO PARA INSERTAR UN PRODUCTO
    public function InsertarProducto()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "INSERT INTO Productos (Nombre, PrecioCosto, PrecioVenta, Descripcion, Estado, AuditXML, Imagen) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

            $this->Procedure->bindParam(1, $this->getNombre());
            $this->Procedure->bindParam(2, $this->getPrecioCosto());
            $this->Procedure->bindParam(3, $this->getPrecioVenta());
            $this->Procedure->bindParam(4, $this->getDescripcion());
            $this->Procedure->bindParam(5, $this->getEstado());
            $this->Procedure->bindParam(6, $this->getAuditXML());
            $this->Procedure->bindParam(7, $this->getImagen()); // Nuevo campo

            $this->Procedure->execute();
        } catch (Exception $e) {
            echo "ERROR AL INSERTAR REGISTRO " . $e->getMessage();
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // MÉTODO PARA ACTUALIZAR UN PRODUCTO
    public function ActualizarProducto()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "UPDATE Productos SET Nombre = ?, PrecioCosto = ?, PrecioVenta = ?, Descripcion = ?, Estado = ?, AuditXML = ?, Imagen = ? WHERE IdProducto = ?";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

            $this->Procedure->bindParam(1, $this->getNombre());
            $this->Procedure->bindParam(2, $this->getPrecioCosto());
            $this->Procedure->bindParam(3, $this->getPrecioVenta());
            $this->Procedure->bindParam(4, $this->getDescripcion());
            $this->Procedure->bindParam(5, $this->getEstado());
            $this->Procedure->bindParam(6, $this->getAuditXML());
            $this->Procedure->bindParam(7, $this->getImagen()); // Nuevo campo
            $this->Procedure->bindParam(8, $this->getIdProducto());

            $this->Procedure->execute();
        } catch (Exception $e) {
            echo "ERROR AL ACTUALIZAR REGISTRO " . $e->getMessage();
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // MÉTODO PARA ELIMINAR UN PRODUCTO (CAMBIAR ESTADO A 0)
    public function EliminarProducto()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "UPDATE Productos SET Estado = 0 WHERE IdProducto = ?";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

            $this->Procedure->bindParam(1, $this->getIdProducto());

            $this->Procedure->execute();
        } catch (Exception $e) {
            echo "ERROR AL ELIMINAR REGISTRO " . $e->getMessage();
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // MÉTODO PARA CONSULTAR TODOS LOS PRODUCTOS ACTIVOS
    public function ConsultarProductos()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "SELECT IdProducto, Nombre, PrecioCosto, PrecioVenta, Descripcion, Estado, AuditXML, Imagen FROM Productos WHERE Estado = 1";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
            $this->Procedure->execute();
            return $this->Procedure->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    public function ConsultarProductoPorId()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "SELECT IdProducto, Nombre, PrecioCosto, PrecioVenta, Descripcion, Estado, AuditXML, Imagen FROM Productos WHERE IdProducto = ?";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
            $this->Procedure->bindParam(1, $this->getIdProducto());
            $this->Procedure->execute();
            return $this->Procedure->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die($e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // MÉTODO PARA OBTENER EL ÚLTIMO ID INSERTADO
    public function ObtenerUltimoId()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "SELECT MAX(IdProducto) as UltimoId FROM Productos";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
            $this->Procedure->execute();
            $resultado = $this->Procedure->fetch(PDO::FETCH_ASSOC);
            return $resultado['UltimoId'];
        } catch (Exception $e) {
            die($e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // GETTERS Y SETTERS
    public function getIdProducto()
    {
        return $this->IdProducto;
    }

    public function setIdProducto($IdProducto)
    {
        $this->IdProducto = $IdProducto;
        return $this;
    }

    public function getNombre()
    {
        return $this->Nombre;
    }

    public function setNombre($Nombre)
    {
        $this->Nombre = $Nombre;
        return $this;
    }

    public function getPrecioCosto()
    {
        return $this->PrecioCosto;
    }

    public function setPrecioCosto($PrecioCosto)
    {
        $this->PrecioCosto = $PrecioCosto;
        return $this;
    }

    public function getPrecioVenta()
    {
        return $this->PrecioVenta;
    }

    public function setPrecioVenta($PrecioVenta)
    {
        $this->PrecioVenta = $PrecioVenta;
        return $this;
    }

    public function getDescripcion()
    {
        return $this->Descripcion;
    }

    public function setDescripcion($Descripcion)
    {
        $this->Descripcion = $Descripcion;
        return $this;
    }

    public function getEstado()
    {
        return $this->Estado;
    }

    public function setEstado($Estado)
    {
        $this->Estado = $Estado;
        return $this;
    }

    public function getAuditXML()
    {
        return $this->AuditXML;
    }

    public function setAuditXML($AuditXML)
    {
        $this->AuditXML = $AuditXML;
        return $this;
    }

    public function getImagen()
    {
        return $this->Imagen;
    }

    public function setImagen($Imagen)
    {
        $this->Imagen = $Imagen;
        return $this;
    }
}
?>
