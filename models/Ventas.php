<?

	//CLASE MODELO 

	class Ventas_model
	{
        //ATRIBUTOS DE LA CLASE

        private $ConexionSql;
        private $Conexion;
        private $SentenciaSql;
        private $Procedure;
        private $Result;
        private $IdFactura;
        private $IdCliente;
        private $IdArticulo;
        private $Cantidad;
        private $PrecioVenta;
        private $AuditXML;


        //CONSTRUCTOR DE LA CLASE

        public function __Construct ()
        {
            $this->Conexion = new ClaseConexion();
        }



        // INSERTAR FACTURA USANDO PROCEDIMIENTO ALMACENADO
        public function insertarFactura() {
            try {
                $this->ConexionSql = $this->Conexion->CrearConexion();
                $this->SentenciaSql = "CALL InsertFactura(?, ?, ?, ?, @p_FacturaId)";
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

                $this->Procedure->bindParam(1, $this->getIdCliente());
                $this->Procedure->bindParam(2, $this->getFechaHora());
                $this->Procedure->bindParam(3, $this->getHora());
                $this->Procedure->bindParam(4, $this->getAuditXML());

                $this->Procedure->execute();

                // Obtener el ID de la factura generada
                $result = $this->ConexionSql->query("SELECT @p_FacturaId AS FacturaId")->fetch(PDO::FETCH_ASSOC);
                return $result['FacturaId'];
            } catch (Exception $e) {
                echo "ERROR AL INSERTAR FACTURA: " . $e->getMessage();
            } finally {
                $this->Conexion->CerrarConexion();
            }
        }

        // INSERTAR DETALLE DE FACTURA Y DESCONTAR INVENTARIO USANDO PROCEDIMIENTO ALMACENADO
        public function insertarDetalleFacturaYDescontar() {
            try {
                $this->ConexionSql = $this->Conexion->CrearConexion();
                $this->SentenciaSql = "CALL InsertDetalleFacturaYDescontar(?, ?, ?, ?, ?)";
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

                $this->Procedure->bindParam(1, $this->getIdFactura());
                $this->Procedure->bindParam(2, $this->getIdArticulo());
                $this->Procedure->bindParam(3, $this->getCantidad());
                $this->Procedure->bindParam(4, $this->getPrecioVenta());
                $this->Procedure->bindParam(5, $this->getAuditXML());

                $this->Procedure->execute();
            } catch (Exception $e) {
                echo "ERROR AL INSERTAR DETALLE Y DESCONTAR INVENTARIO: " . $e->getMessage();
            } finally {
                $this->Conexion->CerrarConexion();
            }
        }

        // MÉTODO PARA OBTENER PRODUCTOS
        /*public function obtenerProductos() {
            try {
                $this->ConexionSql = $this->Conexion->CrearConexion();
                $this->SentenciaSql = "SELECT 
                                            IdProducto, 
                                            Nombre, 
                                            PrecioCosto, 
                                            PrecioVenta, 
                                            Imagen,
                                            c.Cantidad
                                        FROM productos a
                                        inner join despiecesproducto b
                                            on b.ProductoResultadoId = a.IdProducto
                                        inner join inventario c
                                            on c.ProductoId = a.idProducto";
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                $this->Procedure->execute();
                return $this->Procedure->fetchAll(PDO::FETCH_OBJ);
            } catch (Exception $e) {
                echo "ERROR AL OBTENER PRODUCTOS: " . $e->getMessage();
            } finally {
                $this->Conexion->CerrarConexion();
            }
        }*/


        public function obtenerProductos() {
            try {
                $this->ConexionSql = $this->Conexion->CrearConexion();
        
                // Ejecutar el SET por separado
                $this->ConexionSql->exec("SET @rn := 0, @prev := NULL;");
        
                // Ahora ejecutar el SELECT
                $this->SentenciaSql = "
                    SELECT IdProducto, Nombre, PrecioCosto, PrecioVenta, Imagen, Cantidad
                    FROM (
                        SELECT 
                            p.IdProducto, 
                            p.Nombre, 
                            p.PrecioCosto, 
                            p.PrecioVenta, 
                            p.Imagen,
                            IFNULL(i.Cantidad, 0) AS Cantidad,
                            @rn := IF(@prev = p.IdProducto, @rn + 1, 1) AS row_num,
                            @prev := p.IdProducto
                        FROM (
                            SELECT DISTINCT
                                p.IdProducto, 
                                p.Nombre, 
                                p.PrecioCosto, 
                                p.PrecioVenta, 
                                p.Imagen
                            FROM despiecesproducto d
                            INNER JOIN productos p ON p.IdProducto = d.ProductoOrigenId
                            WHERE p.Estado = 1 AND d.Estado = 1
        
                            UNION ALL
        
                            SELECT DISTINCT
                                p.IdProducto, 
                                p.Nombre, 
                                p.PrecioCosto, 
                                p.PrecioVenta, 
                                p.Imagen
                            FROM despiecesproducto d
                            INNER JOIN productos p ON p.IdProducto = d.ProductoResultadoId
                            WHERE p.Estado = 1 AND d.Estado = 1
                        ) AS p
                        LEFT JOIN inventario i ON i.ProductoId = p.IdProducto AND i.Estado = 1
                        ORDER BY p.IdProducto, Cantidad DESC
                    ) AS final
                    WHERE row_num = 1;
                ";
        
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                $this->Procedure->execute();
                return $this->Procedure->fetchAll(PDO::FETCH_OBJ);
        
            } catch (Exception $e) {
                echo "ERROR AL OBTENER PRODUCTOS: " . $e->getMessage();
            } finally {
                $this->Conexion->CerrarConexion();
            }
        }
        
        public function verificarCombo($idProducto) {
            try {
                $conn = $this->Conexion->CrearConexion();
        
                $sql = "SELECT dp.ProductoResultadoId, dp.Cantidad, IFNULL(i.Cantidad, 0) AS StockDisponible
                        FROM despiecesproducto dp
                        LEFT JOIN inventario i ON i.ProductoId = dp.ProductoResultadoId AND i.Estado = 1
                        WHERE dp.ProductoOrigenId = ? AND dp.Estado = 1";
        
                $stmt = $conn->prepare($sql);
                $stmt->execute([$idProducto]);
                $componentes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
                if (count($componentes) === 0) {
                    return ['esCombo' => false, 'permitido' => true];
                }
        
                foreach ($componentes as $comp) {
                    if ($comp['StockDisponible'] < $comp['Cantidad']) {
                        return [
                            'esCombo' => true,
                            'permitido' => false,
                            'msj' => 'Falta stock para el componente ID ' . $comp['ProductoResultadoId']
                        ];
                    }
                }
        
                return ['esCombo' => true, 'permitido' => true];
            } catch (Exception $e) {
                return ['esCombo' => null, 'permitido' => false, 'msj' => 'Error: ' . $e->getMessage()];
            } finally {
                $this->Conexion->CerrarConexion();
            }
        }
        


        public function getId() {
            return $this->Id;
        }

        public function setId($Id) {
            $this->Id = $Id;
        }

        public function getIdCliente() {
            return $this->IdCliente;
        }

        public function setIdCliente($IdCliente) {
            $this->IdCliente = $IdCliente;
        }

        public function getFechaHora() {
            return $this->FechaHora;
        }

        public function setFechaHora($FechaHora) {
            $this->FechaHora = $FechaHora;
        }

        public function getHora() {
            return $this->Hora;
        }

        public function setHora($Hora) {
            $this->Hora = $Hora;
        }

        public function getAuditXML() {
            return $this->AuditXML;
        }

        public function setAuditXML($AuditXML) {
            $this->AuditXML = $AuditXML;
        }

        public function getIdFactura() {
            return $this->IdFactura;
        }

        public function setIdFactura($IdFactura) {
            $this->IdFactura = $IdFactura;
        }

        public function getIdArticulo() {
            return $this->IdArticulo;
        }

        public function setIdArticulo($IdArticulo) {
            $this->IdArticulo = $IdArticulo;
        }

        public function getCantidad() {
            return $this->Cantidad;
        }

        public function setCantidad($Cantidad) {
            $this->Cantidad = $Cantidad;
        }

        public function getPrecioVenta() {
            return $this->PrecioVenta;
        }

        public function setPrecioVenta($PrecioVenta) {
            $this->PrecioVenta = $PrecioVenta;
        }

        // MÉTODO PARA OBTENER INFORMACIÓN DE UNA FACTURA POR ID
        public function obtenerFacturaPorId($idFactura) {
            try {
                $this->ConexionSql = $this->Conexion->CrearConexion();
                $this->SentenciaSql = "SELECT 
                                            a.Id,
                                            a.Fecha,
                                            a.Hora,
                                            a.ClienteId,
                                            a.Estado,
                                            b.ProductoId,
                                            c.Nombre,
                                            c.PrecioVenta,
                                            b.Cantidad,
                                            b.Subtotal,
                                            a.Total
                                        FROM factura a
                                        INNER JOIN detallefactura b
                                            ON b.FacturaId = a.Id 
                                        INNER JOIN productos c
                                            ON c.IdProducto = b.ProductoId
                                        WHERE a.Id = ?";
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                $this->Procedure->bindParam(1, $idFactura, PDO::PARAM_INT);
                $this->Procedure->execute();
                return $this->Procedure->fetchAll(PDO::FETCH_OBJ);
            } catch (Exception $e) {
                echo "ERROR AL OBTENER INFORMACIÓN DE LA FACTURA: " . $e->getMessage();
            } finally {
                $this->Conexion->CerrarConexion();
            }
        }

        // MÉTODO PARA ANULAR UNA FACTURA USANDO PROCEDIMIENTO ALMACENADO
        public function anularFactura($idFactura, $auditXML) {
            try {
                $this->ConexionSql = $this->Conexion->CrearConexion();
                $this->SentenciaSql = "CALL AnularFactura(?, ?)";
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

                $this->Procedure->bindParam(1, $idFactura, PDO::PARAM_INT);
                $this->Procedure->bindParam(2, $auditXML, PDO::PARAM_STR);

                $this->Procedure->execute();

                return true; // Indicar que la operación fue exitosa
            } catch (Exception $e) {
                echo "ERROR AL ANULAR FACTURA: " . $e->getMessage();
                return false; // Indicar que hubo un error
            } finally {
                $this->Conexion->CerrarConexion();
            }
        }

        //FIN DE LA LOGICA
	}
?>

