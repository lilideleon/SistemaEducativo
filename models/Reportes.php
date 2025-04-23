<?php
    class reportes_model
    {
        private $ConexionSql;
        private $SentenciaSql;
        private $Procedure;
        private $Conexion;

        public function __construct() {
            $this->Conexion = new ClaseConexion();
        }

        //metodo para listar productos en el reporte de productos
        public function ObtenerProductos($orden) 
        {
            try {
                $this->ConexionSql = $this->Conexion->CrearConexion();

                // Validar el orden para evitar SQL injection
                $ordenPermitido = ($orden === 'id') ? 'IdProducto' : 'Nombre';

                $this->SentenciaSql = "SELECT 
                        IdProducto,
                        Nombre, 
                        PrecioCosto,
                        PrecioVenta,
                        Descripcion,
                        Estado,
                        CASE
                            WHEN Imagen IS NOT NULL THEN 'TIENE IMAGEN'
                            ELSE 'SIN IMAGEN'
                        END as Imagen
                    FROM Productos
                    ORDER BY {$ordenPermitido}"; // Corrected string concatenation and SQL syntax

                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                $this->Procedure->execute();

                return $this->Procedure->fetchAll(PDO::FETCH_OBJ);
            } catch (Exception $e) {
                error_log("ERROR AL OBTENER INFORMACIÓN DE PRODUCTOS: " . $e->getMessage());
                return false;
            } finally {
                $this->Conexion->CerrarConexion();
            }
        }


        //metodo para reporte de asistencia de usuarios

        public function ObtenerAsistencia($fechaInicio, $fechaFin) 
        {
            try {
                $this->ConexionSql = $this->Conexion->CrearConexion();
                $this->SentenciaSql = "SELECT 
                                        a.IdUsuario,
                                        concat(a.PrimerNombre,' ',a.SegundoNombre,' ',a.PrimerApellido,' ',a.SegundoApellido) as Nombre,
                                        b.Tipo,
                                        b.Hora,
                                        b.Fecha
                                    from usuarios a
                                        inner join asistencia b 
                                            on b.Usuarioid = a.IdUsuario
                                    where b.Fecha between :fechaInicio and :fechaFin";
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                $this->Procedure->execute([
                    'fechaInicio' => $fechaInicio,
                    'fechaFin' => $fechaFin
                ]);
                return $this->Procedure->fetchAll(PDO::FETCH_OBJ);
            } catch (Exception $e) {
                error_log("ERROR AL OBTENER INFORMACIÓN DE ASISTENCIA: " . $e->getMessage());
                return false;
            } finally {
                $this->Conexion->CerrarConexion();
            }
        }

        //metodo para reporte de usuarios

        public function ObtenerUsuarios($estado) 
        {

            if($estado == 'activo') {
                $estado = 1;
            } else if($estado == 'inactivo') {
                $estado = 0;
            }
            else {
                $estado = 1;
            }

            try {
                $this->ConexionSql = $this->Conexion->CrearConexion();
                $this->SentenciaSql = "SELECT 
                                        a.IdUsuario,
                                        Dpi,
                                        concat(a.PrimerNombre,' ',a.SegundoNombre,' ',a.PrimerApellido,' ',a.SegundoApellido) as Nombre,
                                        correo,
                                        usuario,
                                        case when Estado = 1 then
                                            'activo'
                                        else 
                                            'inactivo' 
                                        end Estado
                                    from usuarios a
                                    where a.Estado = :estado";
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                $this->Procedure->execute([
                    'estado' => $estado
                ]);
                return $this->Procedure->fetchAll(PDO::FETCH_OBJ);
            } catch (Exception $e) {
                error_log("ERROR AL OBTENER INFORMACIÓN DE USUARIOS: " . $e->getMessage());
                return false;
            } finally {
                $this->Conexion->CerrarConexion();
            }
        }

        //metodo para reporte de caja 

        public function ObtenerCaja($fechaInicio, $fechaFin) 
        {
            try {
                $this->ConexionSql = $this->Conexion->CrearConexion();
                $this->SentenciaSql = "SELECT 
                                            a.id,
                                            concat(b.`PrimerNombre`,' ',b.`SegundoNombre`,' ',b.`PrimerApellido`,' ',b.`SegundoApellido`) as `Usuario`,
                                            `Fecha`,
                                            `HoraApertura`,
                                            `HoraCierre`,
                                            `MontoInicial`,
                                            `MontoFinal`,
                                            `MontoSistema`,
                                            `Diferencia`,
                                            a.Estado
                                        FROM CAJA a
                                        inner join usuarios B
                                        on a.`UsuarioId` = b.`IdUsuario`
                                        WHERE a.`UsuarioId` = 5
                                        and a.`Fecha` BETWEEN :fechaInicio AND :fechaFin";
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                $this->Procedure->execute([
                    'fechaInicio' => $fechaInicio,
                    'fechaFin' => $fechaFin
                ]);
                return $this->Procedure->fetchAll(PDO::FETCH_OBJ);
            } catch (Exception $e) {
                error_log("ERROR AL OBTENER INFORMACIÓN DE CAJA: " . $e->getMessage());
                return false;
            } finally {
                $this->Conexion->CerrarConexion();
            }
        }   


        //metodo para reporte de ventas

        public function ObtenerVentas($fechaInicio, $fechaFin) 
        {
            try {
                $this->ConexionSql = $this->Conexion->CrearConexion();
                $this->SentenciaSql = "select 
                                            `Id`,
                                            `Fecha`,
                                            case 
                                                when ClienteId = 1 THEN
                                                    'GENERAL'
                                                else 
                                                    ClienteId 
                                            end as `Cliente`,
                                            `Hora`,
                                            `Total`,
                                            case when `Estado` = 1 THEN
                                                'Activo'
                                            else 
                                                'anulado'
                                            end as `Estado`
                                        from factura a
                                        where `Fecha` BETWEEN :fechaInicio and :fechaFin
                                        and `Estado` = 1";
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                $this->Procedure->execute([
                    'fechaInicio' => $fechaInicio,
                    'fechaFin' => $fechaFin
                ]);
                return $this->Procedure->fetchAll(PDO::FETCH_OBJ);
            } catch (Exception $e) {
                error_log("ERROR AL OBTENER INFORMACIÓN DE CAJA: " . $e->getMessage());
                return false;
            } finally {
                $this->Conexion->CerrarConexion();
            }
        }


        //metodo para reporte de inventario 

        public function ObtenerInventario() 
        {
            try {
                $this->ConexionSql = $this->Conexion->CrearConexion();
                $this->SentenciaSql = "SELECT 
                                        a.`Id`,
                                        b.`Nombre`,
                                        b.`PrecioCosto`,
                                        b.`PrecioVenta`,
                                        a.`Cantidad`,
                                        case when b.estado = 1 then 'Activo' else 'Inactivo' end as estado
                                    FROM inventario a
                                    inner join productos b 
                                    ON a.`Id` = b.`IdProducto`";
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                $this->Procedure->execute();
                return $this->Procedure->fetchAll(PDO::FETCH_OBJ);
            } catch (Exception $e) {
                error_log("ERROR AL OBTENER INFORMACIÓN DE INVENTARIO: " . $e->getMessage());
                return false;
            } finally {
                $this->Conexion->CerrarConexion();
            }
        }   

        //metodo para reporte de compras

        public function ObtenerCompras($fechaInicio, $fechaFin) 
        {
            try {
                $this->ConexionSql = $this->Conexion->CrearConexion();
                $this->SentenciaSql = "select
                                            a.`Id`,
                                            concat (b.`PrimerNombre`,' ', b.`SegundoNombre`,' ', b.`PrimerApellido`,' ', b.`SegundoApellido`) as `Usuario`,
                                            a.`Fecha`,
                                            a.`HORA`,
                                            case when a.`Proveedor` = 1 then 'GENERAL' else 'ESPECIAL' end as `Proveedor`,
                                            a.`Total`,
                                            case 
                                                when a.`Estado` = 1 THEN
                                                    'activa'
                                                when a.`Estado` = 0 THEN
                                                    'anulada'
                                                WHEN a.`Estado` = 3 THEN
                                                    'procesada'
                                            end as `Estado`
                                        from compras a
                                        INNER JOIN usuarios b on a.`UsuarioId` = b.`IdUsuario`
                                        where a.`Fecha` BETWEEN :fechaInicio and :fechaFin";
                    $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                $this->Procedure->execute([
                    'fechaInicio' => $fechaInicio,
                    'fechaFin' => $fechaFin
                ]);
                return $this->Procedure->fetchAll(PDO::FETCH_OBJ);
            } catch (Exception $e) {
                error_log("ERROR AL OBTENER INFORMACIÓN DE COMPRAS: " . $e->getMessage());
                return false;
            } finally {
                $this->Conexion->CerrarConexion();
            }
        }

        // metodo para reporte de costos

        public function ObtenerCostos($fechaInicio, $fechaFin) 
        {
            try {
                $this->ConexionSql = $this->Conexion->CrearConexion();
                $this->SentenciaSql = "SELECT 
                                            f.Id AS FacturaId,
                                            f.Fecha,
                                            p.IdProducto,
                                            p.Nombre,
                                            p.PrecioCosto,
                                            p.PrecioVenta,
                                            df.Cantidad AS CantidadVendida,
                                            (p.PrecioVenta - p.PrecioCosto) * df.Cantidad AS Ganancia
                                        FROM 
                                            detallefactura df
                                        INNER JOIN 
                                            productos p ON df.ProductoId = p.IdProducto
                                        INNER JOIN 
                                            factura f ON df.FacturaId = f.Id
                                        WHERE 
                                            f.Estado = 1
                                            AND f.Fecha BETWEEN :fechaInicio and :fechaFin
                                        ORDER BY 
                                            f.Id, Ganancia DESC;";
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                $this->Procedure->execute([
                    'fechaInicio' => $fechaInicio,
                    'fechaFin' => $fechaFin
                ]);
                return $this->Procedure->fetchAll(PDO::FETCH_OBJ);
            } catch (Exception $e) {
                error_log("ERROR AL OBTENER INFORMACIÓN DE PRODUCTOS: " . $e->getMessage());
                return false;
            } finally {
                $this->Conexion->CerrarConexion();
            }
        }

    }
?>
