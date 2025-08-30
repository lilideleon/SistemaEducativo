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
		header('Content-Type: application/json');
		
		try {
			// Verificar que sea una petición POST
			if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
				throw new Exception('Método no permitido');
			}

			// Obtener datos del formulario
			$codigo = isset($_POST['username']) ? trim($_POST['username']) : '';
			$password = isset($_POST['password']) ? $_POST['password'] : '';

			// Validar que se proporcionen los datos
			if (empty($codigo) || empty($password)) {
				throw new Exception('Usuario y contraseña son requeridos');
			}

			// Validar credenciales
			$loginModel = new Login_model();
			$usuario = $loginModel->ValidarCredenciales($codigo, $password);

			if ($usuario) {
				// Iniciar sesión
				session_start();
				
				// Guardar datos del usuario en la sesión
				$_SESSION['user_id'] = $usuario['id'];
				$_SESSION['user_codigo'] = $usuario['codigo'];
				$_SESSION['user_nombres'] = $usuario['nombres'];
				$_SESSION['user_apellidos'] = $usuario['apellidos'];
				$_SESSION['user_rol'] = $usuario['rol'];
				$_SESSION['user_nombre_completo'] = $usuario['nombres'] . ' ' . $usuario['apellidos'];
				$_SESSION['authenticated'] = true;
				$_SESSION['login_time'] = time();

				// Obtener información adicional del usuario
				$usuarioCompleto = $loginModel->ObtenerUsuarioCompleto($usuario['id']);
				if ($usuarioCompleto) {
					$_SESSION['user_institucion_id'] = $usuarioCompleto['institucion_id'];
					$_SESSION['user_institucion_nombre'] = $usuarioCompleto['institucion_nombre'];
					$_SESSION['user_grado_id'] = $usuarioCompleto['grado_id'];
					$_SESSION['user_grado_nombre'] = $usuarioCompleto['grado_nombre'];
					$_SESSION['user_seccion'] = $usuarioCompleto['seccion'];
				}

				echo json_encode([
					'success' => true,
					'message' => 'Autenticación exitosa',
					'user' => [
						'id' => $usuario['id'],
						'codigo' => $usuario['codigo'],
						'nombres' => $usuario['nombres'],
						'apellidos' => $usuario['apellidos'],
						'rol' => $usuario['rol'],
						'nombre_completo' => $_SESSION['user_nombre_completo']
					]
				]);
			} else {
				throw new Exception('Credenciales inválidas');
			}

		} catch (Exception $e) {
			http_response_code(400);
			echo json_encode([
				'success' => false,
				'message' => $e->getMessage()
			]);
		}
	}

	public function Logout()
	{
		session_start();
		
		// Destruir todas las variables de sesión
		$_SESSION = array();
		
		// Si se desea destruir la sesión completamente, borrar también la cookie de sesión
		if (ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000,
				$params["path"], $params["domain"],
				$params["secure"], $params["httponly"]
			);
		}
		
		// Finalmente, destruir la sesión
		session_destroy();
		
		// Redirigir al login
		header('Location: ?c=Login');
		exit();
	}


			
	}
?>