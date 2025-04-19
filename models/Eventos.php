<?
	//CLASE MODELO EMPLEADOS

	class Eventos_model
	{
        //ATRIBUTOS DE LA CLASE

        private $Codigo;
        private $Titulo,$Descripcion;
        private $fecha;
        private $estado;
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
                $this->SentenciaSql = "INSERT INTO eventos (Nombre, Descripcion, Fecha, Estado) VALUES (?, ?, ?, ?)";
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

                $this->Procedure->bindParam(1, $this->getTitulo());
                $this->Procedure->bindParam(2, $this->getDescripcion());
                $this->Procedure->bindParam(3, $this->getFecha());
                $this->Procedure->bindParam(4, $this->getEstado());

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


        public function ObtenerEventosCalendario()
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion();
                
                // Consulta SQL para obtener eventos
                $this->SentenciaSql = "SELECT * FROM eventos where estado != 0";
                
                $this->Result = array();
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
                $this->Procedure->execute();
                
                // Fetching data as objects
                return $this->Procedure->fetchAll(PDO::FETCH_OBJ);
            }
            catch(Exception $e)
            {
                die($e->getMessage());
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
                $this->SentenciaSql = "UPDATE eventos SET Estado = 0 WHERE Fecha=?";
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

                $this->Procedure->bindParam(1, $this->getFecha());

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


        // Getter para Titulo
        public function getTitulo() {
            return $this->Titulo;
        }

        // Setter para Titulo
        public function setTitulo($Titulo) {
            $this->Titulo = $Titulo;
        }

        // Getter para Descripcion
        public function getDescripcion() {
            return $this->Descripcion;
        }

        // Setter para Descripcion
        public function setDescripcion($Descripcion) {
            $this->Descripcion = $Descripcion;
        }
        

        // Getter para fecha
        public function getFecha() {
            return $this->fecha;
        }

        // Setter para fecha
        public function setFecha($fecha) {
            // Aquí puedes agregar validaciones adicionales para la fecha, si es necesario
            $this->fecha = $fecha;
        }

        // Getter para estado
        public function getEstado() {
            return $this->estado;
        }

        // Setter para estado
        public function setEstado($estado) {
            // Aquí puedes agregar validaciones adicionales para el estado, si es necesario
            $this->estado = $estado;
        }

       // Getter para $Codigo
        public function getCodigo() {
            return $this->Codigo;
        }

        // Setter para $Codigo
        public function setCodigo($Codigo) {
            $this->Codigo = $Codigo;
        }
	}
?>

