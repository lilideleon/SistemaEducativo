<?
 
	class Menu_model
	{
        //ATRIBUTOS DE LA CLASE

        private $Codigo;
        private $ConexionSql;
        private $Conexion;
        private $SentenciaSql;
        private $Procedure;
        private $Result;
 
        //CONSTRUCTOR DE LA CLASE

        public function __Construct ()
        {
            $this->Conexion = new ClaseConexion();
        }


 

        public function getTotalUsuarios ()
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "SELECT Max(id) as Total FROM usuarios ";
                $this->Procedure = $this->ConexionSql->prepare ($this->SentenciaSql);
                $this->Procedure->execute ();
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

        // metodo para totalizar las ventas del dia

        public function getTotalVentasDia ()
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "SELECT IFNULL(SUM(Total), 0) AS VentasDelDia FROM factura WHERE Fecha = CURDATE() AND Estado = 1";
                $this->Procedure = $this->ConexionSql->prepare ($this->SentenciaSql);
                $this->Procedure->execute ();
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

        // metodo para totalizar los productos activos

        public function getTotalProductosActivos ()
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "SELECT COUNT(*) AS ProductosActivos FROM productos WHERE Estado = 1";
                $this->Procedure = $this->ConexionSql->prepare ($this->SentenciaSql);
                $this->Procedure->execute ();
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

        //metodo para consultar usuarios activos

        public function getTotalUsuariosActivos ()
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "SELECT COUNT(*) AS UsuariosActivos FROM usuarios WHERE Estado = 1";
                $this->Procedure = $this->ConexionSql->prepare ($this->SentenciaSql);
                $this->Procedure->execute ();
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

        //metodo para mostrar el total de compras del mes

        public function getTotalComprasMes ()
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "SELECT IFNULL(SUM(Total), 0) AS ComprasDelMes FROM compras WHERE MONTH(Fecha) = MONTH(CURDATE()) AND YEAR(Fecha) = YEAR(CURDATE()) AND Estado = 3";
                $this->Procedure = $this->ConexionSql->prepare ($this->SentenciaSql);
                $this->Procedure->execute ();
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

        //metodo para mostrar el total de ventas

        public function getTotalVentas ()
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "SELECT IFNULL(SUM(Total), 0) AS TotalVentas FROM factura WHERE Fecha BETWEEN CURDATE() - INTERVAL 6 DAY AND CURDATE() AND Estado = 1";
                $this->Procedure = $this->ConexionSql->prepare ($this->SentenciaSql);
                $this->Procedure->execute ();
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


        //metodo para mostrar el total de ventas  por producto

        public function getTotalVentasPorProducto ()
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "SELECT IFNULL(SUM(Total), 0) AS ComprasDelMes
                                        FROM compras
                                        WHERE MONTH(Fecha) = MONTH(CURDATE()) 
                                        AND YEAR(Fecha) = YEAR(CURDATE()) 
                                        AND Estado = 1";
                $this->Procedure = $this->ConexionSql->prepare ($this->SentenciaSql);
                $this->Procedure->execute ();
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

      //metodo para mostrar Transacciones de ventas y compras

      public function getTransacciones ()
      {
          try
          {
              $this->ConexionSql = $this->Conexion->CrearConexion ();
              $this->SentenciaSql = "SELECT 
                Id,
                Fecha,
                Tipo,
                ClienteProveedor,
                    Total,
                    Estado
                FROM (
                    SELECT 
                        Id, 
                        Fecha, 
                        'VENTA' AS Tipo, 
                        ClienteId AS ClienteProveedor, 
                        Total, 
                        Estado
                    FROM factura
                    WHERE Estado = 1

                    UNION ALL

                    SELECT 
                        Id, 
                        Fecha, 
                        'COMPRA' AS Tipo, 
                        Proveedor AS ClienteProveedor, 
                        Total, 
                        Estado
                    FROM compras
                    WHERE Estado = 1
                ) AS Transacciones
                ORDER BY Fecha DESC
                LIMIT 10;";
                $this->Procedure = $this->ConexionSql->prepare ($this->SentenciaSql);
                $this->Procedure->execute ();
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


        //metodo para productos mas vendidos los ultimos 30 dias

        public function ProductosMasVendidos($numerodias)
        {
            try {
                $this->ConexionSql = $this->Conexion->CrearConexion();
                $this->SentenciaSql = "SELECT 
                                    p.Nombre,
                                    SUM(df.Cantidad) AS TotalVendido
                                FROM detallefactura df
                                INNER JOIN productos p ON p.IdProducto = df.ProductoId
                                INNER JOIN factura f ON f.Id = df.FacturaId
                                WHERE f.Fecha >= CURDATE() - INTERVAL :dias DAY AND f.Estado = 1
                                GROUP BY p.Nombre
                                ORDER BY TotalVendido DESC
                                LIMIT 5";
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                $this->Procedure->bindParam(':dias', $numerodias, PDO::PARAM_INT);
                $this->Procedure->execute();
                return $this->Procedure->fetchAll(PDO::FETCH_OBJ);
            } catch (Exception $e) {
                echo "ERROR AL OBTENER PRODUCTOS MAS VENDIDOS: " . $e->getMessage();
            } finally {
                $this->Conexion->CerrarConexion();
            }
        }

        // Top resultados por encuesta (para dashboard)
        public function getTopResultadosEncuesta($encuestaId, $limit = 10)
        {
            try {
                $this->ConexionSql = $this->Conexion->CrearConexion();
                $limit = intval($limit);
                if ($limit <= 0) { $limit = 10; }

                $this->SentenciaSql = "SELECT
  ra.encuesta_id,
  e.titulo AS encuesta_nombre,
  ra.alumno_user_id,
  CONCAT(u.nombres, ' ', u.apellidos) AS alumno_nombre,
  SUM(CASE WHEN COALESCE(ra.es_correcta, r.es_correcta) = 1 THEN 1 ELSE 0 END) AS correctas,
  COUNT(*) AS total,
  SUM(CASE WHEN COALESCE(ra.es_correcta, r.es_correcta) = 1 THEN 5 ELSE 0 END) AS puntaje
FROM respuestas_alumnos ra
JOIN preguntas p
  ON p.id = ra.pregunta_id
 AND p.encuesta_id = ra.encuesta_id
 AND p.activo = 1
LEFT JOIN respuestas r
  ON r.id = ra.respuesta_id
JOIN usuarios u
  ON u.id = ra.alumno_user_id
JOIN encuestas e
  ON e.id = ra.encuesta_id
WHERE ra.encuesta_id = :encuestaId
  AND ra.activo = 1
  AND u.activo = 1
  AND e.activo = 1
GROUP BY ra.encuesta_id, e.titulo, ra.alumno_user_id, alumno_nombre
ORDER BY puntaje DESC
LIMIT $limit";

                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                $this->Procedure->bindParam(':encuestaId', $encuestaId, PDO::PARAM_INT);
                $this->Procedure->execute();
                return $this->Procedure->fetchAll(PDO::FETCH_OBJ);
            } catch (Exception $e) {
                die($e->getMessage());
            } finally {
                $this->Conexion->CerrarConexion();
            }
        }
    }
?>

