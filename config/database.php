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
			$this->DataBase = 'dbname=educativo';
			$this->Servidor = 'mysql:host=localhost;';
			$this->JuegoCaracteres = 'charset=utf8';
		}

		//METODO PARA CREAR LA CONEXION A LA BASE DE DATOS

    	public function CrearConexion ()
        {
            try
            {
                $this->ConexionSql = new PDO(
                    $this->Servidor.$this->DataBase.';'.$this->JuegoCaracteres,
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
				// No imprimir directamente: lanzar excepción para que el caller la maneje
				throw new Exception("Error al crear la conexion: " . $e->getMessage());
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
				// Registrar en log en lugar de imprimir
				error_log("Error al cerrar la conexion: " . $e->getMessage());
			}
		}

		//metodo para realizar backup de la base de datos o exportar

		public function realizarBackup($ruta)
		{
			if (!is_dir($ruta)) {
				mkdir($ruta, 0777, true);
			}
		
			$fecha = date('Y-m-d_His');
			$basededatos = str_replace(['dbname=', ';'], '', $this->DataBase);
			$nombrearchivo = rtrim($ruta, '/\\') . "/backup_" . $basededatos . "_" . $fecha . ".sql";
			$mysqldump = "C:\\AppServ\\MySQL\\bin\\mysqldump.exe";
			$comando = "\"$mysqldump\" --user=$this->Usuario --password=$this->Contraseña --databases $basededatos > \"$nombrearchivo\"";
		
			exec($comando, $output, $retval);
		
			if ($retval !== 0 || !file_exists($nombrearchivo)) {
				throw new Exception("Error al realizar el backup de la base de datos.");
			}
		
			// Si todo va bien, puedes opcionalmente devolver el nombre del archivo
			return $nombrearchivo;
		}
		

		

		//FIN DE LA LOGICA
	}

	// Conexión PDO global para modelos modernos
	if (!isset($pdo)) {
	    $conexion = new ClaseConexion();
	    $pdo = $conexion->CrearConexion();
	}
?>