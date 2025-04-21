<?php
// CLASE MODELO compras

class Compras_model
{
    private $ConexionSql;
    private $Conexion;
    private $SentenciaSql;
    private $Procedure;

    public function __construct()
    {
        $this->Conexion = new ClaseConexion();
    }

    public function InsertarCompra($Fecha, $Hora, $Proveedor, $UsuarioId, $Total, $Observaciones, $AuditXML)
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();

            $this->SentenciaSql = "CALL InsertarCompra(?, ?, ?, ?, ?, ?, ?, @CompraId)";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

            $this->Procedure->execute([$Fecha, $Hora, $Proveedor, $UsuarioId, $Total, $Observaciones, $AuditXML]);

            $result = $this->ConexionSql->query("SELECT @CompraId AS CompraId")->fetch();
            return $result['CompraId'];
        } catch (Exception $e) {
            throw new Exception("Error al insertar la compra: " . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    public function InsertarDetalleCompra($CompraId, $ProductoId, $Cantidad, $PrecioUnitario)
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();

            $this->SentenciaSql = "CALL InsertarDetalleCompra(?, ?, ?, ?)";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

            $this->Procedure->execute([$CompraId, $ProductoId, $Cantidad, $PrecioUnitario]);
        } catch (Exception $e) {
            throw new Exception("Error al insertar el detalle de la compra: " . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }


    //metodo para obtener un producto por su nombre
    //se le pasa el nombre del producto y se obtiene el idProducto y el nombre

    public function ObtenerProducto($nombreProducto)
    {
        $this->ConexionSql = $this->Conexion->CrearConexion();
        $this->SentenciaSql = "SELECT idProducto, Nombre FROM productos WHERE Nombre like ?";
        $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
        $this->Procedure->execute(['%' . $nombreProducto . '%']);
        return $this->Procedure->fetch(PDO::FETCH_ASSOC);
    }


}
