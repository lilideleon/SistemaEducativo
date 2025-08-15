<?php
	
	class Login_model {
		
		private $db;
		private $instance;
		private $vehiculos;
		private $Valor;
		
		public function __construct(){
			$this->Conexion = new ClaseConexion();
		}
		
        public function hola()
        {
            
        }


		public function InsertarToken()
        {
            try
            {


				$this->ConexionSql = $this->Conexion->CrearConexion();
                $this->SentenciaSql = "TRUNCATE TABLE temp;";
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

                $this->Procedure->execute();



                $this->SentenciaSql = "INSERT INTO temp (valor) VALUES (?);";
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

                $this->Procedure->bindParam(1, $this->getValor());

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

		public function ObtenetToken()
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "SELECT * FROM temp";
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



		public function ActualizarPass($pass,$correo)
        {
            try
            {
                $this->ConexionSql = $this->Conexion->CrearConexion ();
                $this->SentenciaSql = "update usuarios set Contraseña = '".$pass."' where  correo = '".$correo."'";
                $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

         
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


		public function getValor ()
		{
			return $this->Valor;
		}

		public function setValor ($valor)
		{
			$this->Valor = $valor; 
		}
	} 
?>