<?php
	
	class LoginController {
		
		public function __construct(){
			require_once "models/Login.php";
			require_once "models/Usuarios.php";
		}
		
		public function index(){
			
			
			$vehiculos = new Login_Model();
			$data["titulo"] = "Login";
			//$data["Login"] = $vehiculos->get_vehiculos();
			
			require_once "views/Login/Login.php";	
		}
		
		public function nuevo(){
			
			$data["titulo"] = "Login";
			require_once "views/Login/Login.php";
		}
	
		public function Validate()
		{
			// Instancia de la clase
			$model = new Usuarios_model();

			// Obtener los datos enviados por POST
			$User = $_POST['UserName'];
			$Pass = $_POST['UserPass'];

			// Obtener el usuario desde el modelo
			$row = $model->ValidarUsuario($User);

			$response = array();

			if ($row) {
				// Verificar la contraseña encriptada
				if (password_verify($Pass, $row['Contraseña'])) {
					// Iniciar sesión
					session_start();
					$_SESSION["TipoUsuario"] = $row['Rol']; // Cambiado de Perfil a Rol
					$_SESSION["Codigo"] = $row['IdUsuario'];
					$_SESSION["PrimerNombre"] = $row['PrimerNombre'];
					$_SESSION["PrimerApellido"] = $row['PrimerApellido'];

					$response['success'] = true;
				} else {
					$response['success'] = false;
				}
			} else {
				$response['success'] = false;
			}

			echo json_encode($response);
		}

		public function Destruir(){
			@session_start();
			session_destroy();

			$json['name'] = 'position';
			$json['defaultValue'] = 'top-right';
			$json['msj'] = '<font color="#ffffff"><i class="fa fa-check"></i> Producto Agregado</font>';
			$json['success'] = true;

			echo json_encode($json);
		}

			
	}
?>