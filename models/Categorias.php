<?
	//CLASE categorias

    class Categorias_model
    {
        private $ConexionSql;
        private $Conexion;
        private $SentenciaSql;
        private $Procedure;

        private $IdCategoria;
        private $Nombre;
        private $Unidades;
        private $Estado;
        private $AuditXML;

        public function __construct()
        {
            $this->Conexion = new ClaseConexion();
        }

        // Método para insertar una categoría
        public function InsertarCategoria()
        {
            try {
                $this->ConexionSql = $this->Conexion->CrearConexion();
                $this->SentenciaSql = "CALL InsertarCategoria(?, ?, ?)";
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

                $this->Procedure->bindParam(1, $this->getNombre());
                $this->Procedure->bindParam(2, $this->getUnidades());
                $this->Procedure->bindParam(3, $this->getAuditXML());

                $this->Procedure->execute();
            } catch (Exception $e) {
                echo "ERROR AL INSERTAR CATEGORÍA: " . $e->getMessage();
            } finally {
                $this->Conexion->CerrarConexion();
            }
        }

        // Método para actualizar una categoría
        public function ActualizarCategoria()
        {
            try {
                $this->ConexionSql = $this->Conexion->CrearConexion();
                $this->SentenciaSql = "CALL ActualizarCategoria(?, ?, ?, ?, ?)";
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

                $this->Procedure->bindParam(1, $this->getIdCategoria());
                $this->Procedure->bindParam(2, $this->getNombre());
                $this->Procedure->bindParam(3, $this->getUnidades());
                $this->Procedure->bindParam(4, $this->getEstado());
                $this->Procedure->bindParam(5, $this->getAuditXML());

                $this->Procedure->execute();
            } catch (Exception $e) {
                echo "ERROR AL ACTUALIZAR CATEGORÍA: " . $e->getMessage();
            } finally {
                $this->Conexion->CerrarConexion();
            }
        }

        // Método para eliminar una categoría
        public function EliminarCategoria()
        {
            try {
                $this->ConexionSql = $this->Conexion->CrearConexion();
                $this->SentenciaSql = "CALL EliminarCategoria(?, ?)";
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

                $this->Procedure->bindParam(1, $this->getIdCategoria());
                $this->Procedure->bindParam(2, $this->getAuditXML());

                $this->Procedure->execute();
            } catch (Exception $e) {
                echo "ERROR AL ELIMINAR CATEGORÍA: " . $e->getMessage();
            } finally {
                $this->Conexion->CerrarConexion();
            }
        }

        // Getters y Setters
        public function getIdCategoria()
        {
            return $this->IdCategoria;
        }

        public function setIdCategoria($IdCategoria)
        {
            $this->IdCategoria = $IdCategoria;
            return $this;
        }

        public function getNombre()
        {
            return $this->Nombre;
        }

        public function setNombre($Nombre)
        {
            $this->Nombre = $Nombre;
            return $this;
        }

        public function getUnidades()
        {
            return $this->Unidades;
        }

        public function setUnidades($Unidades)
        {
            $this->Unidades = $Unidades;
            return $this;
        }

        public function getEstado()
        {
            return $this->Estado;
        }

        public function setEstado($Estado)
        {
            $this->Estado = $Estado;
            return $this;
        }

        public function getAuditXML()
        {
            return $this->AuditXML;
        }

        public function setAuditXML($AuditXML)
        {
            $this->AuditXML = $AuditXML;
            return $this;
        }
    }
?>

