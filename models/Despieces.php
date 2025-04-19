<?php
// CLASE MODELO PRODUCTOS

class Despieces_model
{
    // ATRIBUTOS DE LA CLASE
    private $Id;
    private $ProductoOrigenId;
    private $ProductoResultadoId;
    private $Cantidad;
    private $Estado;
    private $AuditXML;

    private $ConexionSql;
    private $Conexion;
    private $SentenciaSql;
    private $Procedure;

    // CONSTRUCTOR DE LA CLASE
    public function __construct()
    {
        $this->Conexion = new ClaseConexion();
    }

    // MÉTODO PARA INSERTAR UN PRODUCTO
    public function InsertarDespiece()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "INSERT INTO DespiecesProducto (ProductoOrigenId, ProductoResultadoId, Cantidad, Estado, Auditxml) VALUES (?, ?, ?, ?, ?)";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

            $this->Procedure->bindParam(1, $this->ProductoOrigenId);
            $this->Procedure->bindParam(2, $this->ProductoResultadoId);
            $this->Procedure->bindParam(3, $this->Cantidad);
            $this->Procedure->bindParam(4, $this->Estado);
            $this->Procedure->bindParam(5, $this->AuditXML);

            $this->Procedure->execute();
        } catch (Exception $e) {
            echo "ERROR AL INSERTAR REGISTRO " . $e->getMessage();
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // MÉTODO PARA ACTUALIZAR UN PRODUCTO
    public function ActualizarDespiece()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "UPDATE DespiecesProducto SET ProductoOrigenId = ?, ProductoResultadoId = ?, Cantidad = ?, Estado = ?, Auditxml = ? WHERE Id = ?";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

            $this->Procedure->bindParam(1, $this->ProductoOrigenId);
            $this->Procedure->bindParam(2, $this->ProductoResultadoId);
            $this->Procedure->bindParam(3, $this->Cantidad);
            $this->Procedure->bindParam(4, $this->Estado);
            $this->Procedure->bindParam(5, $this->AuditXML);
            $this->Procedure->bindParam(6, $this->Id);

            $this->Procedure->execute();
        } catch (Exception $e) {
            echo "ERROR AL ACTUALIZAR REGISTRO " . $e->getMessage();
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // MÉTODO PARA ELIMINAR UN PRODUCTO (CAMBIAR ESTADO A 0)
    public function EliminarDespiece()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "UPDATE DespiecesProducto SET Estado = 0 WHERE Id = ?";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

            $this->Procedure->bindParam(1, $this->Id);

            $this->Procedure->execute();
        } catch (Exception $e) {
            echo "ERROR AL ELIMINAR REGISTRO " . $e->getMessage();
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // MÉTODO PARA CONSULTAR UN DESPIECE POR ID
    public function ConsultarDespiecePorId()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "SELECT * FROM DespiecesProducto WHERE Id = ? AND Estado = 1";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
            $this->Procedure->bindParam(1, $this->Id);
            $this->Procedure->execute();
            return $this->Procedure->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die($e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // MÉTODO PARA OBTENER PRODUCTOS ORIGEN
    public function ObtenerProductosOrigen()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "SELECT DISTINCT a.IdProducto, a.Nombre FROM Productos a ";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
            $this->Procedure->execute();
            return $this->Procedure->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo "ERROR AL OBTENER PRODUCTOS ORIGEN " . $e->getMessage();
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // MÉTODO PARA OBTENER PRODUCTOS RESULTADO
    public function ObtenerProductosResultado()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "SELECT DISTINCT a.IdProducto, a.Nombre FROM Productos a ";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
            $this->Procedure->execute();
            return $this->Procedure->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo "ERROR AL OBTENER PRODUCTOS RESULTADO " . $e->getMessage();
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // GETTERS Y SETTERS
    public function getId()
    {
        return $this->Id;
    }

    public function setId($Id)
    {
        $this->Id = $Id;
        return $this;
    }

    public function getProductoOrigenId()
    {
        return $this->ProductoOrigenId;
    }

    public function setProductoOrigenId($ProductoOrigenId)
    {
        $this->ProductoOrigenId = $ProductoOrigenId;
        return $this;
    }

    public function getProductoResultadoId()
    {
        return $this->ProductoResultadoId;
    }

    public function setProductoResultadoId($ProductoResultadoId)
    {
        $this->ProductoResultadoId = $ProductoResultadoId;
        return $this;
    }

    public function getCantidad()
    {
        return $this->Cantidad;
    }

    public function setCantidad($Cantidad)
    {
        $this->Cantidad = $Cantidad;
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
}
?>
