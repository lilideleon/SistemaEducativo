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
                                        b.Hora
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
    }
?>
