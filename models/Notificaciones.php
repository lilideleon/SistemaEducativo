<?
	//CLASE MODELO EMPLEADOS

	class Notificaciones_model
	{
        //ATRIBUTOS DE LA CLASE

        private $Codigo;
        private $Titulo,$Mensaje,$Importancia,$Usuario_Envia,$Usuario_Atiende,$Fecha,$Estado;
        private $ConexionSql;
        private $Conexion;
        private $SentenciaSql;
        private $Procedure;
        private $Result;
        private $correo;
        private $huellas;
        private $aldea;
        private $sector;

        //CONSTRUCTOR DE LA CLASE

        public function __Construct ()
        {
            $this->Conexion = new ClaseConexion();
        }

        //METODO PARA INSERTAR UN EPLEADO

        public function Insertar()
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion();
                $this->SentenciaSql = "INSERT INTO notificaciones (Titulo, Mensaje, Importancia, UsuarioEnvia, Usuario_Atiende, Fecha, Estado) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

                $this->Procedure->bindParam(1, $this->getTitulo());
                $this->Procedure->bindParam(2, $this->getMensaje());
                $this->Procedure->bindParam(3, $this->getImportancia());
                $this->Procedure->bindParam(4, $this->getUsuario_Envia());
                $this->Procedure->bindParam(5, $this->getUsuario_Atiende());
                $this->Procedure->bindParam(6, $this->getFecha());
                $this->Procedure->bindParam(7, $this->getEstado());

                $this->Procedure->execute();
            }
            catch (Exception $e)
            {
                echo "ERROR AL INSERTAR REGISTRO " . $e->getMessage();
            }
            finally
            {
                $this->Conexion->CerrarConexion();
            }
        }


        public function Actualizar()
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion();
                $this->SentenciaSql = "UPDATE notificaciones SET Titulo = ?, Mensaje = ?, Importancia = ?, UsuarioEnvia = ?, Usuario_Atiende = ?, Fecha = ?, Estado = ? WHERE id = ?";
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

                $this->Procedure->bindParam(1, $this->getTitulo());
                $this->Procedure->bindParam(2, $this->getMensaje());
                $this->Procedure->bindParam(3, $this->getImportancia());
                $this->Procedure->bindParam(4, $this->getUsuario_Envia());
                $this->Procedure->bindParam(5, $this->getUsuario_Atiende());
                $this->Procedure->bindParam(6, $this->getFecha());
                $this->Procedure->bindParam(7, $this->getEstado());
                $this->Procedure->bindParam(8, $this->getCodigo());

                $this->Procedure->execute();
            }
            catch (Exception $e)
            {
                echo "ERROR AL ACTUALIZAR REGISTRO " . $e->getMessage();
            }
            finally
            {
                $this->Conexion->CerrarConexion();
            }
        }


        public function Eliminar ()
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "UPDATE notificaciones SET Estado = '0' WHERE id=?";
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

                $this->Procedure->bindParam(1, $this->getCodigo());

                $this->Procedure->execute();
            }
            catch (Exception $e)
            {
                echo "ERROR AL INSERTAR REGISTRO ".$e->getMessage();
            }
            finally
            {
                $this->Conexion->CerrarConexion ();
            }
        }


        //METODO PARA DEVOLVER LA INFORMACION COMPLETA AL MODAL

        public function ObtenerDatosModal($Cod)
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "SELECT * FROM notificaciones WHERE id = '".$Cod."'";
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
        
        public function getCodigo()
        {
            return $this->Codigo;
        }
        
        public function setCodigo($Codigo)
        {
            $this->Codigo = $Codigo;
        }

        public function getTitulo ()
        {
            return $this->Titulo;
        }

        public function setTitulo($Titulo)
        {
            $this->Titulo = $Titulo;
        }

        public function getMensaje ()
        {
            return $this->Mensaje;
        }

        public function setMensaje($Mensaje)
        {
            $this->Mensaje = $Mensaje;
        }
       
        public function getImportancia ()
        {
            return $this->Importancia;
        }

        public function setImportancia($Importancia)
        {
            $this->Importancia = $Importancia;
        }

        public function getUsuario_Envia ()
        {
            return $this->Usuario_Envia;
        }

        public function setUsuario_Envia($Usuario_Envia)
        {
            $this->Usuario_Envia = $Usuario_Envia;
        }

        public function getUsuario_Atiende ()
        {
            return $this->Usuario_Atiende;
        }

        public function setUsuario_Atiende($Usuario_Atiende)
        {
            $this->Usuario_Atiende = $Usuario_Atiende;
        }


        public function getFecha ()
        {
            return $this->Fecha;
        }

        public function setFecha($Fecha)
        {
            $this->Fecha = $Fecha;
        }

        public function getEstado ()
        {
            return $this->Estado;
        }

        public function setEstado($Estado)
        {
            $this->Estado = $Estado;
        }
        //FIN DE LA LOGICA
	}
?>

