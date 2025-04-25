<?php
	
	class OpcionesController 
	{
		
		public function __construct(){
			@session_start();
			require_once "models/Opciones.php";
			$data["titulo"] = "Opciones";
            date_default_timezone_set('America/Guatemala');
		}
		
		public function index(){
			require_once "views/Opciones/Opciones.php";	
		}

		//metodo para realizar el backup de la base de datos

        public function realizarBackup()
        {
            try {
                $model = new ClaseConexion();
                $ruta = $_POST['ruta'];
                $model->realizarBackup($ruta); // No hace echo
                echo json_encode([
                    'success' => true,
                    'msj' => 'Backup realizado exitosamente.'
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false, // <-- Cambiado para que sea consistente
                    'msj' => 'ERROR AL REALIZAR BACKUP: ' . $e->getMessage()
                ]);
            }
        }
        
        
        // Método para insertar un detalle de caja
        public function insertarDetalleCaja() {
            try {
                $caja = new Caja_model();
                $caja->insertarDetalleCaja();
            } catch (Exception $e) {
                echo json_encode(array("status" => "error", "message" => "Error al insertar el detalle de la caja: " . $e->getMessage()));
            }
        }
        
        // Method to fetch the current state of the cash register
        public function obtenerEstadoActualCaja() {
            try {
                $caja = new Caja_model();
                $estadoActual = $caja->obtenerEstadoActualCaja();
                
                echo json_encode(array("status" => "success", "data" => $estadoActual));
            } catch (Exception $e) {
                echo json_encode(array("status" => "error", "message" => "Error al obtener el estado actual de la caja: " . $e->getMessage()));
            }
        }
        
    }
    
		
?>