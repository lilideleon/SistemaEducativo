<?

	//CLASE MODELO 

	class Proveedores_model
	{
        //ATRIBUTOS DE LA CLASE

        private $Codigo;
        private $Nombre,$Documento,$Correo,$Telefono,$Estado;
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

        //METODO PARA INSERTAR UN EPLEADO

        public function Insertar()
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "INSERT INTO proveedores VALUES (null,?,?,?,?,?)";
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

                $this->Procedure->bindParam(1, $this->getNombre());
                $this->Procedure->bindParam(2, $this->getDocumento());
                $this->Procedure->bindParam(3, $this->getCorreo());
                $this->Procedure->bindParam(4, $this->getTelefono());
                $this->Procedure->bindParam(5, $this->getEstado());
  

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


        public function Actualizar ()
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "UPDATE proveedores SET Nombre = ?, Documento = ?, Correo = ?, Telefono = ?, Estado = ? WHERE id = ?";
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

                $this->Procedure->bindParam(1, $this->getNombre());
                $this->Procedure->bindParam(2, $this->getDocumento());
                $this->Procedure->bindParam(3, $this->getCorreo());
                $this->Procedure->bindParam(4, $this->getTelefono());
                $this->Procedure->bindParam(5, $this->getEstado());
                $this->Procedure->bindParam(6, $this->getCodigo());
         

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


        public function Eliminar ()
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "UPDATE proveedores SET Estado = '0' WHERE id=?";
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
                $this->SentenciaSql = "SELECT * FROM proveedores WHERE id = '".$Cod."'";
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

        public function getNombre()
        {
            return $this->Nombre;
        }
        
        public function setNombre($Nombre)
        {
            $this->Nombre = $Nombre;
        }

        public function getDocumento()
        {
            return $this->Documento;
        }
        
        public function setDocumento($Documento)
        {
            $this->Documento = $Documento;
        }

        public function getCorreo()
        {
            return $this->Correo;
        }
        
        public function setCorreo($Correo)
        {
            $this->Correo = $Correo;
        }

        public function getTelefono()
        {
            return $this->Telefono;
        }
        
        public function setTelefono($Telefono)
        {
            $this->Telefono = $Telefono;
        }

        public function getEstado()
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

