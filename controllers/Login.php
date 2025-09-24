<?php
class LoginController {
    public function __construct(){
        require_once "models/Login.php";
        require_once "models/Usuarios.php";
        require_once "config/database.php";
    }

    public function index(){
        $vehiculos = new Login_Model();
        $data["titulo"] = "Login";
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
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }
            $codigo = isset($_POST['username']) ? trim($_POST['username']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            if (empty($codigo) || empty($password)) {
                throw new Exception('Usuario y contraseña son requeridos');
            }

            $loginModel = new Login_model();
            $usuario = $loginModel->ValidarCredenciales($codigo, $password);

            if ($usuario) {
                session_start();
                $_SESSION['user_id'] = $usuario['id'];
                $_SESSION['user_codigo'] = $usuario['codigo'];
                $_SESSION['user_nombres'] = $usuario['nombres'];
                $_SESSION['user_apellidos'] = $usuario['apellidos'];
                $_SESSION['user_rol'] = $usuario['rol'];
                $_SESSION['user_nombre_completo'] = $usuario['nombres'] . ' ' . $usuario['apellidos'];
                $_SESSION['authenticated'] = true;
                $_SESSION['login_time'] = time();

                $usuarioCompleto = $loginModel->ObtenerUsuarioCompleto($usuario['id']);
                if ($usuarioCompleto) {
                    $_SESSION['user_institucion_id'] = $usuarioCompleto['institucion_id'];
                    $_SESSION['user_institucion_nombre'] = $usuarioCompleto['institucion_nombre'];
                    $_SESSION['user_grado_id'] = $usuarioCompleto['grado_id'];
                    $_SESSION['user_grado_nombre'] = $usuarioCompleto['grado_nombre'];
                    $_SESSION['user_seccion'] = $usuarioCompleto['seccion'];
                }

                // Redirección según rol
                $redirect = '?c=Menu';
                switch (strtoupper($usuario['rol'])) {
                    case 'ALUMNO':
                        $redirect = '?c=Evaluacion';
                        break;
                    case 'DOCENTE':
                        $redirect = '?c=Material';
                        break;
                    case 'DIRECTOR':
                    case 'ADMIN':
                    default:
                        $redirect = '?c=Menu';
                }

                echo json_encode([
                    'success' => true,
                    'message' => 'Autenticación exitosa',
                    'redirect' => $redirect,
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
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        header('Location: ?c=Login');
        exit();
    }

    public function recoverPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = isset($_POST['email']) ? $_POST['email'] : null;

            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                // Generate a unique token
                $token = bin2hex(pack('H*', hash('sha256', mt_rand() . microtime())));

                // Save the token in the database
                $Conexion = new ClaseConexion();
                $ConexionSql = $Conexion->CrearConexion();
                $stmt = $ConexionSql->prepare("INSERT INTO password_resets (email, token, created_at) VALUES (?, ?, NOW())");
                $stmt->execute([$email, $token]);

                try {
                    // Configuración del correo con PHPMailer
                    require 'PHPMailer/PHPMailer.php';
                    require 'PHPMailer/SMTP.php';
                    require 'PHPMailer/Exception.php';

                    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host = 'smtp.hostinger.com';  // Servidor SMTP de Hostinger
                    $mail->SMTPAuth = true;
                    $mail->Username = 'supervisor0904@hostinger.com'; // Tu correo de Hostinger
                    $mail->Password = 'Super09*04S';  // Tu contraseña de SMTP
                    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Configuración del correo
                    $mail->setFrom('supervisor0904@hostinger.com', 'Sistema Educativo');
                    $mail->addAddress($email);
                    $mail->Subject = 'Recuperación de contraseña - Sistema Educativo';
                    
                    // Contenido HTML del correo
                    $mail->isHTML(true);
                    $mail->CharSet = 'UTF-8';
                    $mail->Body = "
                        <html>
                        <body style='font-family: Arial, sans-serif;'>
                            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                                <h2 style='color: #50938a;'>Recuperación de Contraseña</h2>
                                <p>Has solicitado recuperar tu contraseña en el Sistema Educativo.</p>
                                <div style='background-color: #f8f9fa; padding: 15px; margin: 20px 0; border-left: 4px solid #50938a;'>
                                    <p style='margin: 0;'><strong>Tu código de verificación es:</strong></p>
                                    <h3 style='color: #50938a; margin: 10px 0;'>$token</h3>
                                </div>
                                <p>Si no solicitaste este código, puedes ignorar este correo.</p>
                                <p>Este código expirará en 30 minutos por seguridad.</p>
                                <hr style='border: 1px solid #eee; margin: 20px 0;'>
                                <p style='color: #666; font-size: 12px;'>Este es un correo automático, por favor no responder.</p>
                            </div>
                        </body>
                        </html>";

                    if ($mail->send()) {
                        echo json_encode([
                            "success" => true, 
                            "message" => "Se ha enviado un código de verificación a tu correo. Por favor revisa tu bandeja de entrada."
                        ]);
                    } else {
                        throw new Exception("Error al enviar el correo: " . $mail->ErrorInfo);
                    }
                } catch (Exception $e) {
                    echo json_encode([
                        "success" => false, 
                        "message" => "Error al enviar el correo. Por favor intenta más tarde o contacta al administrador.",
                        "debug" => $e->getMessage()
                    ]);
                }
            } else {
                echo json_encode(["success" => false, "message" => "Correo electrónico no válido."]);
            }
        } else {
            // Show the recovery form
            include 'views/Login/recoverPassword.php';
        }
    }
}
?>
