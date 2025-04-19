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

 
        //METODO PARA DEVOLVER LA INFORMACION COMPLETA AL MODAL

        public function ObtenerDatosModal($Cod)
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "SELECT * FROM usuarios WHERE id = '".$Cod."'";
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


        public function getTotalPagosMensual ()
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "SELECT SUM(TOTAL) AS Total 
                                        FROM pagos 
                                        WHERE FECHA >= DATE_FORMAT(NOW(), '%Y-%m-01')
                                        AND Estado != 0
                                        ";
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


 
	}
?>

