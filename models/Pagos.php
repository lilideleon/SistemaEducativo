<?

	//CLASE MODELO 

	class Pagos_model
	{
        //ATRIBUTOS DE LA CLASE

        private $ConexionSql;
        private $Conexion;
        private $SentenciaSql;
        private $Procedure;
        private $Result;
        private $Id,$IdVenta,$IdArticulo,$Cantidad,$PrecioVenta,$Descuento;
        private $IdFactura,$IdCliente,$IdUsuario,$TipoComprobante,$Serie,$num_comprobante,$FechaHora,$Impuesto,$total,$Estado,$Uuid,$subtotal;
        private $Mes,$Año,$IdServicio,$Max;

        //CONSTRUCTOR DE LA CLASE

        public function __Construct ()
        {
            $this->Conexion = new ClaseConexion();
        }

        //METODO PARA BUSCAR CLIENTES

        public function buscarClientes($Asociado)
        {
            try
            {
                $datos = array();
                $this->ConexionSql = $this->Conexion->CrearConexion();
                $this->SentenciaSql = "
                    SELECT 
                        Id, 
                        Dpi, 
                        CONCAT(
                            COALESCE(Primer_Nombre, ''), ' ', 
                            COALESCE(Segundo_Nombre, ''), ' ',
                            COALESCE(Primer_Apellido, ''), ' ', 
                            COALESCE(Segundo_Apellido, '')
                        ) AS Nombre 
                    FROM usuarios
                    WHERE 
                        Estado != 0 
                        AND CONCAT(
                            Dpi, ' ', 
                            COALESCE(Primer_Nombre, ''), ' ', 
                            COALESCE(Segundo_Nombre, ''), ' ', 
                            COALESCE(Primer_Apellido, ''), ' ', 
                            COALESCE(Segundo_Apellido, '')
                        ) LIKE CONCAT('%".$Asociado."%')  
                    LIMIT 30";
        
                $this->Stm = $this->ConexionSql->prepare($this->SentenciaSql);
                $this->Stm->execute();
                $this->Result = $this->Stm->fetchAll();
                foreach ($this->Result as $key => $value) {
                    $datos[] = array(
                        "value" => $value['Id'],
                        "caption" => "DPI:  ".$value['Dpi']." Nombre:  ".$value['Nombre'] 
                    );
                }
                return $datos;
            }
            catch(Exception $e)
            {
                echo "Error al buscar".$e->getMessage();
            }
            finally
            {
                $this->Conexion->CerrarConexion();
            }
        }
        

        public function buscarServicio ($Servicio)
        {
            try
            {
                $datos = array();
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "SELECT  
                                            Id, 
                                            Servicio
                                        FROM 
                                            servicios 
                                        WHERE 
                                            Estado != 0 AND
                                            Servicio 
                                            LIKE CONCAT('%".$Servicio."%')  limit 30 ";
                $this->Stm = $this->ConexionSql->prepare($this->SentenciaSql);
                $this->Stm->execute();
                $this->Result = $this->Stm->fetchAll();
                foreach ($this->Result as $key => $value) {
                    $datos[] = array("value" => $value['Id'],
                        "caption" => $value['Servicio'] 
                );}
                return $datos;
            }
            catch(Exception $e)
            {
                echo "Error al buscar".$e->getMessage();
            }
            finally
            {
                $this->Conexion->CerrarConexion ();
            }
        }

        //9220951

        //METODO PARA VERIFICAR ya se ralizaron los pagos de un usuario

        public function VerificaMensualidad ($Asociado,$Anio,$Servicio)
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "SELECT 
                                            a.id as idpago,
                                            b.id as iddetallepago,
                                            c.Id as IdUsuario,
                                            CONCAT(c.primer_nombre,' ',c.primer_apellido) as Nombre,
                                            b.Mes,
                                            b.Anio,
                                            b.Sub_Total
                                        FROM pagos a
                                            LEFT JOIN detalle_pago b
                                                on b.idPago = a.id
                                            LEFT JOIN usuarios c
                                                on a.Cliente = c.Id
                                        where
                                            b.anio = '".$Anio."'
                                            and c.Id = '".$Asociado."'
                                            and a.Estado != 0
                                            and b.Servicio = '".$Servicio."'";
                $this->Result = array();
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                $this->Procedure->execute();
                return $this->Procedure->fetchAll(PDO::FETCH_OBJ);
            }
            catch (Exception $e)
            {
                die($e->getMessage());
            }
            finally
            {
                $this->Conexion->CerrarConexion ();
            }
        }



                //METODO PARA VERIFICAR ya se ralizaron los pagos de un usuario

        public function ObtenerAnioInicial($Asociado)
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "SELECT 
                                            YEAR(FECHAINICIO) AS AnioContratacion 
                                        FROM 
                                            usuarios 
                                        WHERE 
                                            Estado != 0 and id = '".$Asociado."'";
                $this->Result = array();
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                $this->Procedure->execute();
                return $this->Procedure->fetchAll(PDO::FETCH_OBJ);
            }
            catch (Exception $e)
            {
                die($e->getMessage());
            }
            finally
            {
                $this->Conexion->CerrarConexion ();
            }
        }



        public function ObtenerFechaPrimerPago($Asociado)
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "SELECT 
                                            YEAR(FechaPrimerPago) AS AnioPrimerPago,
                                            MONTH(FechaPrimerPago) AS MesPrimerPago 
                                        FROM 
                                            usuarios 
                                        WHERE Estado != 0 
                                            and id = '".$Asociado."'";
                $this->Result = array();
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                $this->Procedure->execute();
                return $this->Procedure->fetchAll(PDO::FETCH_OBJ);
            }
            catch (Exception $e)
            {
                die($e->getMessage());
            }
            finally
            {
                $this->Conexion->CerrarConexion ();
            }
        }


        public function ObtenerDescripcion ($Codigo)
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "SELECT
                                            Id,Servicio
                                        FROM servicios
                                        WHERE Estado != 0 
                                            and id = '".$Codigo."'";
                $this->Result = array();
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                $this->Procedure->execute();
                return $this->Procedure->fetchAll(PDO::FETCH_OBJ);
            }
            catch (Exception $e)
            {
                die($e->getMessage());
            }
            finally
            {
                $this->Conexion->CerrarConexion ();
            }
        }


        //OBTENER EL MONTO 

        public function getMonto ($Codigo)
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "SELECT 
                                            Id,Cantidad,Monto 
                                        FROM 
                                            servicios 
                                        WHERE 
                                            Estado != 0
                                            AND Id = '".$Codigo."'";
                $this->Result = array();
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                $this->Procedure->execute();
                return $this->Procedure->fetchAll(PDO::FETCH_OBJ);
            }
            catch (Exception $e)
            {
                die($e->getMessage());
            }
            finally
            {
                $this->Conexion->CerrarConexion ();
            }
        }

        //OBTENER PRODUCTO PARA FACTURACION

        public function datosUsuario($Cod)
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "
                                        SELECT 
                                            NULLIF(Id, '') AS Id, 
                                            NULLIF(Sector, '') AS Sector, 
                                            CONCAT(
                                                COALESCE(Primer_Nombre, ''), ' ', 
                                                COALESCE(Segundo_Nombre, ''), ' ',
                                                COALESCE(Primer_Apellido, ''), ' ', 
                                                COALESCE(Segundo_Apellido, ''), '') AS Nombre 
                                        FROM 
                                            usuarios 
                                        WHERE 
                                            Estado != 0 
                                            AND Id = '".$Cod."'";
                $this->Result = array();
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                $this->Procedure->execute();
                return $this->Procedure->fetchAll(PDO::FETCH_OBJ);
            }
            catch(Exception $e)
            {
                die($e->getMessage());
            }
            finally
            {
                $this->Conexion->CerrarConexion ();
            }  
        }



        //INSERTA LA VENTA

        public function CrearFactura() {
            try {
                $this->ConexionSql = $this->Conexion->CrearConexion();
                $this->SentenciaSql = "INSERT INTO pagos values (null,?,?,?,?,?,?,?)";
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                
                // Obtener la fecha y hora actuales
                date_default_timezone_set('America/Mexico_City');
                $fechaActual = date('Y-m-d');  // Formato 'año-mes-día'
                $horaActual = date('H:i:s');   // Formato 'horas:minutos:segundos'
                
                $valor1 = 1;
                $valor2 = 0;
                $valor3 = 0;
                $valor4 = 1;
        
                $this->Procedure->bindParam(1, $fechaActual);
                $this->Procedure->bindParam(2, $horaActual);
                $this->Procedure->bindParam(3, $this->getIdCliente());
                $this->Procedure->bindParam(4, $valor1);
                $this->Procedure->bindParam(5, $valor2);
                $this->Procedure->bindParam(6, $valor3);
                $this->Procedure->bindParam(7, $valor4);
                
                $this->Procedure->execute();
            } catch (Exception $exc) {
                echo "ERROR AL INSERTAR " . $exc->getMessage();
            } finally {
                $this->Conexion->CerrarConexion();
            }
        }
        
        

        //INSERTA EL DETALLE


        public function InsertarDetalle ()
        {
            try 
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "INSERT INTO detalle_pago values (null,?,?,?,?,?,?)";
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                
                $this->Procedure->bindParam(1, $this->getMax());
                $this->Procedure->bindParam(2,  $this->getIdServicio());
                $this->Procedure->bindParam(3,  $this->getCantidad());
                $this->Procedure->bindParam(4,  $this->getMes());
                $this->Procedure->bindParam(5,  $this->getAño ());
                $this->Procedure->bindParam(6,  $this->getSubtotal ());

                
                $this->Procedure->execute();
            } 
            catch (Exception $exc) 
            {
                echo "ERROR AL INSERTAR ".$exc->getMessage();
            }
            finally 
            {
                $this->Conexion->CerrarConexion ();
            }
        }

        //OBTENER ULTIMA FACTURA REGISTRADA

        public function Maxima() {
            try {
                $this->ConexionSql = $this->Conexion->CrearConexion();
                $this->SentenciaSql = "SELECT max(id) as id FROM pagos";
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                $this->Procedure->execute();
        
                // Retorna directamente el valor del ID máximo
                return $this->Procedure->fetchColumn();
            } catch(Exception $e) {
                die($e->getMessage());
            } finally {
                $this->Conexion->CerrarConexion();
            }  
        }
        


        //OBTENER PRODUCTO PARA FACTURACION

        public function CrearItems($Cod)
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "SELECT
                                            a.Id,
                                            a.CodigoBarra,
                                            a.Nombre,
                                            b.Nombre as Color,
                                            a.PrecioCosto,
                                            a.PrecioVenta
                                        FROM
                                            productos a
                                            left join colores b
                                            on a.color = b.id
                                        WHERE
                                            a.Id = '".$Cod."'";
                $this->Result = array();
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                $this->Procedure->execute();
                return $this->Procedure->fetchAll(PDO::FETCH_OBJ);
            }
            catch(Exception $e)
            {
                die($e->getMessage());
            }
            finally
            {
                $this->Conexion->CerrarConexion ();
            }  
        }


        public function ListarDetalleVenta($Cod)
        {  
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "SELECT
                                            a.idventa,
                                            a.idcliente,
                                            a.fecha_hora,
                                            a.total_venta,
                                            b.idarticulo,
                                            CONCAT(c.Nombre,' talla ',c.Talla,' color ',d.Nombre) as Nombre,
                                            c.codigobarra,
                                            b.cantidad,
                                            b.precio_venta,
                                            b.subtotal
                                        FROM 
                                            venta a 
                                            left join detalle_venta b 
                                                on b.idventa = a.idventa
                                            left join 
                                                productos c
                                                on c.id = b.idarticulo
                                            left join colores d
                                                on d.id = c.Color
                                        WHERE
                                            a.idventa = '".$Cod."'";
                $this->Result = array();
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                $this->Procedure->execute();
                return $this->Procedure->fetchAll(PDO::FETCH_OBJ);
            }
            catch(Exception $e)
            {
                die($e->getMessage());
            }
            finally
            {
                $this->Conexion->CerrarConexion ();
            }
        }


        //METODO PARA VERIFICAR por codigo de barra

        public function buscarPorCodigoBarra ($Cod)
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "SELECT * FROM productos WHERE CodigoBarra = '".$Cod."'";
                $this->Result = array();
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                $this->Procedure->execute();
                return $this->Procedure->fetchAll(PDO::FETCH_OBJ);
            }
            catch (Exception $e)
            {
                die($e->getMessage());
            }
            finally
            {
                $this->Conexion->CerrarConexion ();
            }
        }
        
        //METODO PARA VERIFICAR por codigo de barra

        public function buscarPorCodigoBarra2 ($Cod)
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "SELECT Id as Codigo,Nombre,Color,Talla,PrecioCosto,PrecioVenta,Existencia,CodigoBarra FROM productos WHERE CodigoBarra = '".$Cod."'";
                $this->Result = array();
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                $this->Procedure->execute();
                return $this->Procedure->fetchAll(PDO::FETCH_OBJ);
            }
            catch (Exception $e)
            {
                die($e->getMessage());
            }
            finally
            {
                $this->Conexion->CerrarConexion ();
            }
        }


        //GANANCIA

        public function Ganancia ($FechaInicio,$FechaFin)
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "SELECT
                                            a.idventa as Factura,
                                            DATE_FORMAT(a.fecha_hora, '%d-%m-%Y') as Fecha,
                                            b.cantidad as Cantidad,
                                            CONCAT(c.Nombre,' color ',d.Nombre,' talla ',c.Talla) as Nombre,
                                            c.PrecioCosto,
                                            CONCAT(c.PrecioVenta,' (',b.descuento,')') as PrecioVenta,
                                            ROUND((((b.precio_venta - c.PrecioCosto) * b.cantidad)-b.descuento),2) as ganancia
                                        FROM 
                                            venta a
                                            left join detalle_venta b
                                                on b.idventa = a.idventa
                                            left join productos c
                                                on c.id = b.idarticulo
                                            left join colores d
                                                on d.id = c.Color
                                        WHERE
                                            a.fecha_hora between '".$FechaInicio."' and '".$FechaFin."'
                                            and a.estado = 1";
                $this->Result = array();
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                $this->Procedure->execute();
                return $this->Procedure->fetchAll(PDO::FETCH_OBJ);
            }
            catch (Exception $e)
            {
                die($e->getMessage());
            }
            finally
            {
                $this->Conexion->CerrarConexion ();
            }
        }


         //GANANCIA

         public function Ganancia2 ($FechaInicio,$FechaFin,$Usuario)
         {
             try
             {
                 $this->ConexionSql = $this->Conexion->CrearConexion ();
                 $this->SentenciaSql = "SELECT
                                             a.idventa as Factura,
                                             CONCAT (e.PrimerNombre,' ',e.PrimerApellido) as Usuario,
                                             DATE_FORMAT(a.fecha_hora, '%d-%m-%Y') as Fecha,
                                             b.cantidad as Cantidad,
                                             CONCAT(c.Nombre,' color ',d.Nombre,' talla ',c.Talla) as Nombre,
                                             c.PrecioCosto,
                                             CONCAT(c.PrecioVenta,' (',b.descuento,')') as PrecioVenta,
                                             ROUND((((b.precio_venta - c.PrecioCosto) * b.cantidad)-b.descuento),2) as ganancia
                                         FROM 
                                             venta a
                                             left join detalle_venta b
                                                 on b.idventa = a.idventa
                                             left join productos c
                                                 on c.id = b.idarticulo
                                             left join colores d
                                                 on d.id = c.Color
                                             left join usuarios e
                                                on e.id = a.idusuario
                                         WHERE
                                             a.fecha_hora between '".$FechaInicio."' and '".$FechaFin."'
                                             and a.estado = 1
                                             and idusuario = '".$Usuario."'";
                 $this->Result = array();
                 $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                 $this->Procedure->execute();
                 return $this->Procedure->fetchAll(PDO::FETCH_OBJ);
             }
             catch (Exception $e)
             {
                 die($e->getMessage());
             }
             finally
             {
                 $this->Conexion->CerrarConexion ();
             }
         }


        //METODO PARA VERIFICAR SI EXISTE EXISTENCIA SUFUCIENTE

        public function VentasAnio ()
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "call VentasAnio()";
                $this->Result = array();
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                $this->Procedure->execute();
                return $this->Procedure->fetchAll(PDO::FETCH_OBJ);
            }
            catch (Exception $e)
            {
                die($e->getMessage());
            }
            finally
            {
                $this->Conexion->CerrarConexion ();
            }
        }


        //GUARDAR EL UUID

        public function GuardarUuidBd ()
        {
            try 
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "UPDATE venta SET Uuid = ? WHERE idventa = ?";
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                
                $this->Procedure->bindParam(1,  $this->getUuid());
                $this->Procedure->bindParam(2,  $this->getIdVenta ());
  
                $this->Procedure->execute();
            } 
            catch (Exception $exc) 
            {
                echo "ERROR AL INSERTAR ".$exc->getMessage();
            }
            finally 
            {
                $this->Conexion->CerrarConexion ();
            }
        }



         public function DatosAnulacion ($ProductId)
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "SELECT Idventa,Uuid,idcliente,fecha_hora 
                FROM venta where Idventa = '".$ProductId."'";
                $this->Result = array();
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                $this->Procedure->execute();
                return $this->Procedure->fetchAll(PDO::FETCH_OBJ);
            }
            catch (Exception $e)
            {
                die($e->getMessage());
            }
            finally
            {
                $this->Conexion->CerrarConexion ();
            }
        }


        public function AnularFactura ($IdFactura)
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "UPDATE venta SET Estado = 0 where idventa = ?";
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

                $this->Procedure->bindParam(1, $IdFactura);

                $this->Procedure->execute();
            }
            catch (Exception $e)
            {
                echo "ERROR AL ANULAR ".$e->getMessage();
            }
            finally
            {
                $this->Conexion->CerrarConexion ();
            }
        }



        public function buscarUsuarios ($Producto)
        {
            try
            {
                $datos = array();
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "SELECT * FROM usuarios where PrimerNombre LIKE CONCAT('%".$Producto."%') ";
                $this->Stm = $this->ConexionSql->prepare($this->SentenciaSql);
                $this->Stm->execute();
                $this->Result = $this->Stm->fetchAll();
                foreach ($this->Result as $key => $value) {
                    $datos[] = array("value" => $value['id'],
                        "caption" => $value['PrimerNombre']." ".$value['PrimerApellido']
                );}
                return $datos;
            }
            catch(Exception $e)
            {
                echo "Error al buscar".$e->getMessage();
            }
            finally
            {
                $this->Conexion->CerrarConexion ();
            }
        }




        public function PrintComprobante ($Id)
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
               $this->SentenciaSql = "SELECT 
                            a.Id,
                            b.Dpi,
                            CONCAT(COALESCE(b.Primer_Nombre, ''), ' ', COALESCE(b.segundo_nombre, '')) AS Nombre,
                            CONCAT(COALESCE(b.primer_apellido, ''), ' ', COALESCE(b.segundo_apellido, '')) AS Apellido,
                            a.Fecha,
                            a.Hora,
                            a.Total,
                            CASE 
                                WHEN b.aldea = 1 THEN
                                    CASE
                                        WHEN b.sector = 0 THEN CONCAT('El rincon Sector ', b.sector) 
                                        ELSE CONCAT('Corrales Sector ', b.sector) 
                                    END
                                ELSE
                                    CONCAT('El rincon Sector ', b.sector) 
                            END AS Direccion
                        FROM 
                            pagos a 
                            LEFT JOIN usuarios b ON a.Cliente = b.Id
                        WHERE 
                            a.Estado != 0
                            AND a.Id = '".$Id."'";

                $this->Result = array();
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                $this->Procedure->execute();
                return $this->Procedure->fetchAll(PDO::FETCH_OBJ);
            }
            catch (Exception $e)
            {
                die($e->getMessage());
            }
            finally
            {
                $this->Conexion->CerrarConexion ();
            }
        }


        public function PrintdetalleComprobante ($Id)
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "SELECT 
                                            a.Id,
                                            c.Servicio,
                                            b.Anio,
                                           CASE
                                                WHEN b.Mes = 1 THEN 'Ene'
                                                WHEN b.Mes = 2 THEN 'Feb'
                                                WHEN b.Mes = 3 THEN 'Mar'
                                                WHEN b.Mes = 4 THEN 'Abr'
                                                WHEN b.Mes = 5 THEN 'May'
                                                WHEN b.Mes = 6 THEN 'Jun'
                                                WHEN b.Mes = 7 THEN 'Jul'
                                                WHEN b.Mes = 8 THEN 'Ago'
                                                WHEN b.Mes = 9 THEN 'Sep'
                                                WHEN b.Mes = 10 THEN 'Oct'
                                                WHEN b.Mes = 11 THEN 'Nov'
                                                WHEN b.Mes = 12 THEN 'Dic'
                                                ELSE 'otro'
                                            END AS Mes,
                                            b.Cantidad,
                                            b.Sub_Total
                                    FROM pagos a 
                                        left join detalle_pago b
                                            on a.id = b.IdPago
                                        left join servicios c
                                            on c.id = b.Servicio
                                    where	
                                        a.Estado != 0
                                        and a.Id = '".$Id."'";
                $this->Result = array();
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                $this->Procedure->execute();
                return $this->Procedure->fetchAll(PDO::FETCH_OBJ);
            }
            catch (Exception $e)
            {
                die($e->getMessage());
            }
            finally
            {
                $this->Conexion->CerrarConexion ();
            }
        }
        
        
        
        public function verDeudas($Id)
        {
            try
            {
                date_default_timezone_set('America/Mexico_City');

                $this->ConexionSql = $this->Conexion->CrearConexion();
            
                // Crea las tablas temporales
                $sqlCreateTables = "
                    CREATE TEMPORARY TABLE TempYears (Anio INT);
                    CREATE TEMPORARY TABLE TempMonths (Mes INT DEFAULT 1);
                    INSERT INTO TempMonths (Mes) VALUES (1),(2),(3),(4),(5),(6),(7),(8),(9),(10),(11),(12);
                ";
                $this->ConexionSql->exec($sqlCreateTables);
                
                // Obtener el año inicial basado en detalle_pago
                $sqlYear = "
                    SELECT MIN(b.Anio) 
                    FROM pagos a
                    JOIN detalle_pago b ON a.id = b.IdPago
                    WHERE a.Cliente IN (SELECT id FROM usuarios WHERE id = :userId);
                ";
                $stmtYear = $this->ConexionSql->prepare($sqlYear);
                $stmtYear->bindParam(':userId', $Id, PDO::PARAM_INT);
                $stmtYear->execute();
                $AnioInicial = $stmtYear->fetchColumn();
                
                $AnioActual = date('Y');
                
                // Inserta los años en TempYears usando PHP
                $stmtInsertYear = $this->ConexionSql->prepare("INSERT INTO TempYears (Anio) VALUES (:anio)");
                while ($AnioInicial <= $AnioActual) {
                    $stmtInsertYear->bindParam(':anio', $AnioInicial, PDO::PARAM_INT);
                    $stmtInsertYear->execute();
                    $AnioInicial++;
                }
    
                // LEFT JOIN para encontrar los meses faltantes
                $sqlMain = "
                    SELECT ty.Anio, tm.Mes
                    FROM TempYears ty
                    CROSS JOIN TempMonths tm
                    LEFT JOIN (
                        SELECT b.mes, b.Anio
                        FROM pagos a
                        JOIN detalle_pago b ON a.id = b.IdPago
                        JOIN usuarios c ON c.id = a.Cliente
                        WHERE c.id = :userId2
                    ) AS PagosRegistrados ON ty.Anio = PagosRegistrados.Anio AND tm.Mes = PagosRegistrados.mes
                    WHERE PagosRegistrados.mes IS NULL AND (ty.Anio < YEAR(CURDATE()) OR (ty.Anio = YEAR(CURDATE()) AND tm.Mes <= MONTH(CURDATE())))
                    ORDER BY ty.Anio, tm.Mes;
                ";
                
                $stmtMain = $this->ConexionSql->prepare($sqlMain);
                $stmtMain->bindParam(':userId2', $Id, PDO::PARAM_INT);
                $stmtMain->execute();
                $this->Result = $stmtMain->fetchAll(PDO::FETCH_OBJ);
            
                // Limpieza: Eliminar tablas temporales
                $this->ConexionSql->exec("DROP TEMPORARY TABLE IF EXISTS TempYears, TempMonths");
            
                return $this->Result;
            }
            catch (Exception $e)
            {
                die($e->getMessage());
            }
            finally
            {
                $this->Conexion->CerrarConexion();
            }
        }

        public function verdeudasporservicio ($Id)
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "SELECT
                                            a.Servicio,
                                            b.Cantidad,
                                            b.Estado,
                                            a.Monto
                                        FROM servicios a
                                        left join servicioporusuario b on a.Id = b.Servicio
                                        WHERE	
                                            a.Estado = 1
                                            and b.Usuario = '".$Id."'";
                $this->Result = array();
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                $this->Procedure->execute();
                return $this->Procedure->fetchAll(PDO::FETCH_OBJ);
            }
            catch (Exception $e)
            {
                die($e->getMessage());
            }
            finally
            {
                $this->Conexion->CerrarConexion ();
            }
        }


        
        public function getDatosDpi ($Dpi)
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "SELECT
                                            a.id,
                                            a.dpi
                                        FROM usuarios a
                                        WHERE	
                                            a.estado != 0
                                            and a.dpi = '".$Dpi."'";
                $this->Result = array();
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                $this->Procedure->execute();
                return $this->Procedure->fetchAll(PDO::FETCH_OBJ);
            }
            catch (Exception $e)
            {
                die($e->getMessage());
            }
            finally
            {
                $this->Conexion->CerrarConexion ();
            }
        }

        public function getTablaPagos($Dpi) {
            try {
                $this->ConexionSql = $this->Conexion->CrearConexion();
        
                // Utiliza la consulta que proporcionaste, pero con un placeholder para el DPI
                $this->SentenciaSql = "
                    SELECT 
                        a.Dpi,
                        d.Descripcion,
                        c.mes,
                        c.anio,
                        b.Fecha,
                        c.Sub_Total
                    FROM usuarios a
                    LEFT JOIN pagos b ON b.Cliente = a.id
                    LEFT JOIN detalle_pago c ON c.IdPago = b.id
                    LEFT JOIN servicios d ON d.Id = c.Servicio
                    WHERE a.Id = :dpiPlaceholder";  // Aquí está el placeholder
        
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
        
                // Asigna el valor al placeholder
                $this->Procedure->bindParam(':dpiPlaceholder', $Dpi, PDO::PARAM_INT);  // Asumo que DPI es un entero. Si es una cadena, utiliza PDO::PARAM_STR
        
                $this->Procedure->execute();
        
                return $this->Procedure->fetchAll(PDO::FETCH_OBJ);
        
            } catch (Exception $e) {
                die($e->getMessage());
            } finally {
                $this->Conexion->CerrarConexion();
            }
        }


        public function getTablaDeudas ($Id)
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "
                SELECT 
                    @row_number:=@row_number+1 AS row_number,
                    ua.Dpi,
                    'servicio mensual de agua' as Servicio,
                    '--' as Fecha,
                    MONTH(DATE_ADD(CONCAT(MinAnio, '-', MinMes, '-01'), INTERVAL a.i + b.i*10 + c.i*100 MONTH)) AS MesNoPagado,
                    YEAR(DATE_ADD(CONCAT(MinAnio, '-', MinMes, '-01'), INTERVAL a.i + b.i*10 + c.i*100 MONTH)) AS AnioNoPagado,
                    '35.00' as Monto
                FROM 
                    (SELECT MIN(c.anio) as MinAnio, MIN(c.mes) as MinMes
                     FROM detalle_pago c
                     WHERE c.IdPago IN (SELECT b.id FROM pagos b JOIN usuarios a ON b.Cliente = a.id WHERE a.Dpi = 3166)) AS MinDate,
                    (SELECT 0 AS i UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS a,
                    (SELECT 0 AS i UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS b,
                    (SELECT 0 AS i UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS c,
                    usuarios ua
                WHERE 
                    ua.id = '".$Id."' AND
                    DATE_ADD(CONCAT(MinAnio, '-', MinMes, '-01'), INTERVAL a.i + b.i*10 + c.i*100 MONTH) <= NOW() 
                AND NOT EXISTS (
                    SELECT 1
                    FROM pagos pb
                    LEFT JOIN detalle_pago pc ON pc.IdPago = pb.id
                    WHERE pb.Cliente = ua.id AND pc.mes = MONTH(DATE_ADD(CONCAT(MinAnio, '-', MinMes, '-01'), INTERVAL a.i + b.i*10 + c.i*100 MONTH)) AND pc.anio = YEAR(DATE_ADD(CONCAT(MinAnio, '-', MinMes, '-01'), INTERVAL a.i + b.i*10 + c.i*100 MONTH))
                )
                
                UNION ALL
                SELECT 
                    0 AS row_number,
                    '---' AS Dpi,
                    'Multa por pago atrasado del servicio' AS Servicio,
                    '--' AS Fecha,
                    0 AS MesNoPagado,
                    0 AS AnioNoPagado,
                    '100' AS Monto
                FROM DUAL
                WHERE 
                    (SELECT COUNT(*) 
                     FROM (
                        SELECT 
                            1
                        FROM 
                            usuarios ua,
                            (SELECT MIN(c.anio) as MinAnio, MIN(c.mes) as MinMes
                             FROM detalle_pago c
                             WHERE c.IdPago IN (SELECT b.id FROM pagos b JOIN usuarios a ON b.Cliente = a.id WHERE a.Dpi = 3166)) AS MinDate,
                            (SELECT 0 AS i UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS a,
                            (SELECT 0 AS i UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS b,
                            (SELECT 0 AS i UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS c
                        WHERE 
                            ua.id = '".$Id."' AND
                            DATE_ADD(CONCAT(MinAnio, '-', MinMes, '-01'), INTERVAL a.i + b.i*10 + c.i*100 MONTH) <= NOW() 
                        AND NOT EXISTS (
                            SELECT 1
                            FROM pagos pb
                            LEFT JOIN detalle_pago pc ON pc.IdPago = pb.id
                            WHERE pb.Cliente = ua.id AND pc.mes = MONTH(DATE_ADD(CONCAT(MinAnio, '-', MinMes, '-01'), INTERVAL a.i + b.i*10 + c.i*100 MONTH)) AND pc.anio = YEAR(DATE_ADD(CONCAT(MinAnio, '-', MinMes, '-01'), INTERVAL a.i + b.i*10 + c.i*100 MONTH))
                        )
                     ) AS count_query
                    ) > 3
                
                
                UNION ALL
                
                select 
                    0 as num,
                    u.dpi,
                    ser.Descripcion,
                    '---' as fecha,
                    '---' as Mes,
                    '---' as Año,
                    CONCAT('Cant: ',s.cantidad,' Monto: ',ser.Monto,' Total: ', (s.cantidad * ser.Monto)) as Monto
                from 
                    servicioporusuario s
                    left join usuarios u
                        on s.usuario = u.id
                    left join servicios ser
                        on ser.id = s.Servicio
                WHERE
                    u.id = '".$Id."'";
                $this->Result = array();
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                $this->Procedure->execute();
                return $this->Procedure->fetchAll(PDO::FETCH_OBJ);
            }
            catch (Exception $e)
            {
                die($e->getMessage());
            }
            finally
            {
                $this->Conexion->CerrarConexion ();
            }
        }



        public function getUltimoPago ()
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "SELECT
                                            max(Id) as Id FROM pagos";
                $this->Result = array();
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                $this->Procedure->execute();
                return $this->Procedure->fetchAll(PDO::FETCH_OBJ);
            }
            catch (Exception $e)
            {
                die($e->getMessage());
            }
            finally
            {
                $this->Conexion->CerrarConexion ();
            }
        }
        
        public function AnularRecibo ($idrecibo)
        {
            try 
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "UPDATE pagos SET Estado = 0 WHERE id = ?";
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

                $this->Procedure->bindParam(1, $idrecibo);
  
                $this->Procedure->execute();
            } 
            catch (Exception $exc) 
            {
                echo "ERROR AL INSERTAR ".$exc->getMessage();
            }
            finally 
            {
                $this->Conexion->CerrarConexion ();
            }
        }
        




        public function getId()
        {
            return $this->Id;
        }
        
        public function setId($Id)
        {
            $this->Id = $Id;
        }

        public function getIdVenta()
        {
            return $this->IdVenta;
        }
        
        public function setIdVenta($IdVenta)
        {
            $this->IdVenta = $IdVenta;
        }

        public function getIdArticulo()
        {
            return $this->IdArticulo;
        }
        
        public function setIdArticulo($IdArticulo)
        {
            $this->IdArticulo = $IdArticulo;
        }

        public function getCantidad()
        {
            return $this->Cantidad;
        }
        
        public function setCantidad($Cantidad)
        {
            $this->Cantidad = $Cantidad;
        }

        public function getPrecioVenta()
        {
            return $this->PrecioVenta;
        }
        
        public function setPrecioVenta($PrecioVenta)
        {
            $this->PrecioVenta = $PrecioVenta;
        }

        public function getDescuento()
        {
            return $this->Descuento;
        }
        
        public function setDescuento($Descuento)
        {
            $this->Descuento = $Descuento;
        }
        public function getIdFactura()
        {
            return $this->IdFactura;
        }
        
        public function setIdFactura($IdFactura)
        {
            $this->IdFactura = $IdFactura;
        }

        public function getIdCliente()
        {
            return $this->IdCliente;
        }
        
        public function setIdCliente($IdCliente)
        {
            $this->IdCliente = $IdCliente;
        }

        public function getIdUsuario()
        {
            return $this->IdUsuario;
        }
        
        public function setIdUsuario($IdUsuario)
        {
            $this->IdUsuario = $IdUsuario;
        }

        public function getTipoComprobante()
        {
            return $this->TipoComprobante;
        }
        
        public function setTipoComprobante($TipoComprobante)
        {
            $this->TipoComprobante = $TipoComprobante;
        }

        public function getSerie()
        {
            return $this->Serie;
        }
        
        public function setSerie($Serie)
        {
            $this->Serie = $Serie;
        }

        public function getNum_comprobante()
        {
            return $this->num_comprobante;
        }
        
        public function setNum_comprobante($num_comprobante)
        {
            $this->num_comprobante = $num_comprobante;
        }

        public function getFechaHora()
        {
            return $this->FechaHora;
        }
        
        public function setFechaHora($FechaHora)
        {
            $this->FechaHora = $FechaHora;
        }

        public function getImpuesto()
        {
            return $this->Impuesto;
        }
        
        public function setImpuesto($Impuesto)
        {
            $this->Impuesto = $Impuesto;
        }

        public function getTotal()
        {
            return $this->total;
        }
        
        public function setTotal($total)
        {
            $this->total = $total;
        }

        public function getEstado()
        {
            return $this->Estado;
        }
        
        public function setEstado($Estado)
        {
            $this->Estado = $Estado;
        }

        public function getUuid()
        {
            return $this->Uuid;
        }
        
        public function setUuid($Uuid)
        {
            $this->Uuid = $Uuid;
        }


        public function getSubtotal()
        {
            return $this->subtotal;
        }
        
        public function setSubtotal($subtotal)
        {
            $this->subtotal = $subtotal;
        }


        public function getMes() {
            return $this->Mes;
        }
    
        // Setter para $Mes
        public function setMes($Mes) {
            $this->Mes = $Mes;
        }
    
        // Getter para $Año
        public function getAño() {
            return $this->Año;
        }
    
        // Setter para $Año
        public function setAño($Año) {
            $this->Año = $Año;
        }

        // Getter para $IdServicio
        public function getIdServicio() {
            return $this->IdServicio;
        }

        // Setter para $IdServicio
        public function setIdServicio($IdServicio) {
            $this->IdServicio = $IdServicio;
        }

        public function getMax() {
            return $this->Max;
        }
        
        // Setter para $Max
        public function setMax($Max) {
            $this->Max = $Max;
        }

        //FIN DE LA LOGICA
	}
?>

