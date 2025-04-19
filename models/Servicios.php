<?

	//CLASE MODELO 

	class Servicios_model
	{
        //ATRIBUTOS DE LA CLASE

        private $Codigo;
        private $Servicio,$Cantidad,$Monto,$Descripcion,$Estado;
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
                $this->SentenciaSql = "INSERT INTO servicios VALUES (null,?,?,?,?,?)"; 
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
        
                $this->Procedure->bindParam(1, $this->getServicio());
                $this->Procedure->bindParam(2, $this->getCantidad());
                $this->Procedure->bindParam(3, $this->getMonto());
                $this->Procedure->bindParam(4, $this->getDescripcion());
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


        public function Actualizar()
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion();
                $this->SentenciaSql = "UPDATE servicios SET 
                                        servicio = ?, 
                                        cantidad = ?, 
                                        monto = ?, 
                                        descripcion = ?
                                        WHERE Id = ?"; 
                
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                
                $this->Procedure->bindParam(1, $this->getServicio());
                $this->Procedure->bindParam(2, $this->getCantidad());
                $this->Procedure->bindParam(3, $this->getMonto());
                $this->Procedure->bindParam(4, $this->getDescripcion());
                $this->Procedure->bindParam(5, $this->getCodigo()); 

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
                $this->SentenciaSql = "UPDATE servicios SET Estado = '0' WHERE id=?";
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
                $this->SentenciaSql = "SELECT * FROM servicios WHERE id = '".$Cod."'";
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

        //METODO PARA BUSCAR CLIENTES

        public function buscarClientes ($Producto)
        {
            try
            {
                $datos = array();
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "SELECT Id,Nombre FROM clientes WHERE Nombre LIKE CONCAT('%".$Producto."%') and Estado = '1' limit 30 ";
                $this->Stm = $this->ConexionSql->prepare($this->SentenciaSql);
                $this->Stm->execute();
                $this->Result = $this->Stm->fetchAll();
                foreach ($this->Result as $key => $value) {
                    $datos[] = array("value" => $value['Id'],
                        "caption" => $value['Nombre']
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

        
        public function getCodigo()
        {
            return $this->Codigo;
        }
        
        public function setCodigo($Codigo)
        {
            $this->Codigo = $Codigo;
        }

       // Getter y Setter para Servicio
        public function getServicio() {
            return $this->Servicio;
        }

        public function setServicio($Servicio) {
            $this->Servicio = $Servicio;
        }

        // Getter y Setter para Cantidad
        public function getCantidad() {
            return $this->Cantidad;
        }

        public function setCantidad($Cantidad) {
            $this->Cantidad = $Cantidad;
        }

        // Getter y Setter para Monto
        public function getMonto() {
            return $this->Monto;
        }

        public function setMonto($Monto) {
            $this->Monto = $Monto;
        }

        // Getter y Setter para Descripcion
        public function getDescripcion() {
            return $this->Descripcion;
        }

        public function setDescripcion($Descripcion) {
            $this->Descripcion = $Descripcion;
        }

        
        // Getter para Estado
        public function getEstado() {
            return $this->Estado;
        }

        // Setter para Estado
        public function setEstado($Estado) {
            $this->Estado = $Estado;
        }
        //FIN DE LA LOGICA
	}
?>

