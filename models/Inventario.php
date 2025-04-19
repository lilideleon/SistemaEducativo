<?php
// CLASE MODELO INVENTARIO

class Inventario_model
{
    // ATRIBUTOS DE LA CLASE
    private $Id;
    private $ProductoId;
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

    // MÉTODO PARA INSERTAR UN REGISTRO EN INVENTARIO
    public function InsertarInventario()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "CALL InsertInventario(?, ?, ?)";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

            $this->Procedure->bindParam(1, $this->getProductoId());
            $this->Procedure->bindParam(2, $this->getCantidad());
            $this->Procedure->bindParam(3, $this->getAuditXML());

            $this->Procedure->execute();
        } catch (Exception $e) {
            echo "ERROR AL INSERTAR REGISTRO " . $e->getMessage();
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // MÉTODO PARA ACTUALIZAR UN REGISTRO EN INVENTARIO
    public function ActualizarInventario()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "CALL UpdateInventario(?, ?, ?, ?)";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

            $this->Procedure->bindParam(1, $this->getId());
            $this->Procedure->bindParam(2, $this->getProductoId());
            $this->Procedure->bindParam(3, $this->getCantidad());
            $this->Procedure->bindParam(4, $this->getAuditXML());

            $this->Procedure->execute();
        } catch (Exception $e) {
            echo "ERROR AL ACTUALIZAR REGISTRO " . $e->getMessage();
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // MÉTODO PARA ELIMINAR UN REGISTRO (CAMBIAR ESTADO A 0)
    public function EliminarInventario()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "UPDATE Inventario SET Estado = 0 WHERE Id = ?";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

            $this->Procedure->bindParam(1, $this->getId());

            $this->Procedure->execute();
        } catch (Exception $e) {
            echo "ERROR AL ELIMINAR REGISTRO " . $e->getMessage();
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }


    // MÉTODO PARA CONSULTAR UN REGISTRO POR ID
    public function ConsultarInventarioPorId()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "SELECT Id, ProductoId, Cantidad, Estado, Auditxml FROM Inventario WHERE Id = ?";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
            $this->Procedure->bindParam(1, $this->getId());
            $this->Procedure->execute();
            return $this->Procedure->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die($e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // MÉTODO PARA CONSULTAR PRODUCTOS ACTIVOS ORDENADOS POR NOMBRE
    public function ConsultarProductosActivos()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "SELECT IdProducto, Nombre FROM Productos WHERE Estado = 1 ORDER BY Nombre";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
            $this->Procedure->execute();
            return $this->Procedure->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die($e->getMessage());
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

    public function getProductoId()
    {
        return $this->ProductoId;
    }

    public function setProductoId($ProductoId)
    {
        $this->ProductoId = $ProductoId;
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
