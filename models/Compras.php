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
        return $this->Procedure->fetchAll(PDO::FETCH_ASSOC); // Cambiar a fetchAll
    }




    public function ObtenerCompraPorId($idCompra) {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();

            $this->SentenciaSql = "SELECT 
                                        c.Id,
                                        c.Fecha,
                                        c.Hora,
                                        c.Proveedor,
                                        concat(u.PrimerNombre,' ',u.SegundoNombre,' ',U.PrimerApellido,' ',u.SegundoApellido) UsuarioId,
                                        c.Observaciones,
                                        c.Estado,
                                        c.Total,
                                        d.ProductoId,
                                        p.Nombre,
                                        d.Cantidad,
                                        d.Subtotal
                                    FROM Compras c
                                    INNER JOIN CompraDetalle d ON d.CompraId = c.Id
                                    INNER JOIN Productos p ON p.IdProducto = d.ProductoId
                                    INNER JOIN Usuarios u on u.Idusuario = c.UsuarioId
                                    WHERE c.Id = ?";

            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
            $this->Procedure->bindParam(1, $idCompra, PDO::PARAM_INT);
            $this->Procedure->execute();

            return $this->Procedure->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            echo "ERROR AL OBTENER INFORMACIÓN DE LA COMPRA: " . $e->getMessage();
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }



    public function AnularCompra($CompraId, $AuditXml)
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();

            $this->SentenciaSql = "CALL AnularCompra(?, ?)";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

            $this->Procedure->execute([$CompraId, $AuditXml]);
        } catch (Exception $e) {
            throw new Exception("Error al anular la compra: " . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }


    //procesar la compra para aumentar 

    

        public function ProcesarCompra($CompraId, $AuditXml)
        {
            try {
                $this->ConexionSql = $this->Conexion->CrearConexion();

                $this->SentenciaSql = "CALL ProcesarCompraInventario(?, ?)";
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

                $this->Procedure->execute([$CompraId, $AuditXml]);
            } catch (Exception $e) {
                throw new Exception("Error al procesar la compra: " . $e->getMessage());
            } finally {
                $this->Conexion->CerrarConexion();
            }
        }

}
