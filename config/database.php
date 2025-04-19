<?
	
	//CLASE PARA CREAR Y CERRAR LAS CONEXIONES A LA BASE DE DATOS

	class ClaseConexion
	{
		//ATRIBUTOS DE LA CLASE

		private $Servidor;
		private $Usuario;
		private $Contraseña;
		private $DataBase;
		private $ConexionSql;
		private $JuegoCaracteres;

		//CONSTRUCTOR DE LA CLASE PARA INICIALIZAR TODOS LOS ATRIBUTOS

		public function __construct ()
		{
		    $this->Usuario = 'system';
			$this->Contraseña = '31107449';
			$this->DataBase = 'dbname=rapicomida;';
			$this->Servidor = 'mysql:host=localhost;';
			$this->JuegoCaract = 'charset=utf8';
		}

		//METODO PARA CREAR LA CONEXION A LA BASE DE DATOS

    	public function CrearConexion ()
        {
            try
            {
                $this->ConexionSql = new PDO(
                    $this->Servidor.$this->DataBase.$this->JuegoCaract,
                    $this->Usuario,
                    $this->Contraseña,
                    array(
                        PDO::ATTR_PERSISTENT => true,
                        PDO::MYSQL_ATTR_MULTI_STATEMENTS => true  // Habilita múltiples declaraciones
                    )
                );
            }
            catch (PDOException $e)
            {
                echo "Error al crear la conexion ".$e->getMessage();
            }
            return $this->ConexionSql;
        }


		//METODO PARA CERRAR LA CONEXION

		public function CerrarConexion ()
		{
			try
			{
				$this->ConexionSql = null;
			}
			catch (Exception $e)
			{
				echo "Error al cerrar la conexion".$e->getMessage();
			}
		}

		//FIN DE LA LOGICA
	}
?>