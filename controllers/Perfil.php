<?php
class PerfilController
{
    public function __construct()
    {
        require_once "core/AuthMiddleware.php";
        // Require authentication for any perfil action
        AuthMiddleware::requireAuth();
        require_once "models/Usuarios.php";
        require_once "models/Login.php"; // to verify current password if needed
    }

    public function Index()
    {
        // Render the perfil view (located under views/Usuarios/Perfil.php)
        require_once "views/Usuarios/Perfil.php";
    }

    public function ActualizarPassword()
    {
        header('Content-Type: application/json');
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }

            session_start();
            $userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
            if (!$userId) throw new Exception('Usuario no autenticado');

            $current = isset($_POST['current_password']) ? $_POST['current_password'] : '';
            $new = isset($_POST['new_password']) ? $_POST['new_password'] : '';
            $confirm = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

            if (empty($new) || empty($confirm)) {
                throw new Exception('La nueva contraseña y su confirmación son requeridas');
            }
            if ($new !== $confirm) {
                throw new Exception('La confirmación no coincide con la nueva contraseña');
            }
            if (strlen($new) < 6) {
                throw new Exception('La contraseña debe tener al menos 6 caracteres');
            }

            $usuarios = new Usuarios_model();
            $login = new Login_model();

            // Verify current password if provided (recommended)
            if (!empty($current)) {
                // Use login model to validate credentials safely
                $userRow = $usuarios->ObtenerUsuario($userId);
                if (!$userRow) throw new Exception('Usuario no encontrado');

                // Fetch full record including password_hash
                $Conexion = new ClaseConexion();
                $ConexionSql = $Conexion->CrearConexion();
                $stmt = $ConexionSql->prepare("SELECT password_hash FROM usuarios WHERE id = ? AND activo = 1");
                $stmt->bindParam(1, $userId, PDO::PARAM_INT);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $Conexion->CerrarConexion();

                $hash = isset($row['password_hash']) ? (string)$row['password_hash'] : '';
                if (!password_verify($current, $hash)) {
                    throw new Exception('Contraseña actual inválida');
                }
            }

            // Update password via model
            $usuarios->setId($userId);
            $usuarios->setPassword($new);
            $ok = $usuarios->ActualizarUsuario();

            if (!$ok) throw new Exception('No se pudo actualizar la contraseña');

            echo json_encode(['success' => true, 'message' => 'Contraseña actualizada correctamente']);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}

?>
