<?php

class AuthMiddleware
{
    /**
     * Verifica si el usuario está autenticado
     * @return bool
     */
    public static function isAuthenticated()
    {
        session_start();
        return isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
    }

    /**
     * Verifica si el usuario tiene un rol específico
     * @param string|array $roles Rol o roles permitidos
     * @return bool
     */
    public static function hasRole($roles)
    {
        if (!self::isAuthenticated()) {
            return false;
        }

        $userRole = isset($_SESSION['user_rol']) ? $_SESSION['user_rol'] : '';
        
        if (is_array($roles)) {
            return in_array($userRole, $roles);
        }
        
        return $userRole === $roles;
    }

    /**
     * Requiere autenticación para acceder a una página
     * @param string $redirectUrl URL a donde redirigir si no está autenticado
     */
    public static function requireAuth($redirectUrl = '?c=Login')
    {
        if (!self::isAuthenticated()) {
            header('Location: ' . $redirectUrl);
            exit();
        }
    }

    /**
     * Requiere un rol específico para acceder a una página
     * @param string|array $roles Rol o roles permitidos
     * @param string $redirectUrl URL a donde redirigir si no tiene permisos
     */
    public static function requireRole($roles, $redirectUrl = '?c=Login')
    {
        self::requireAuth($redirectUrl);
        
        if (!self::hasRole($roles)) {
            // Redirigir a una página de acceso denegado o al login
            header('Location: ' . $redirectUrl);
            exit();
        }
    }

    /**
     * Obtiene información del usuario actual
     * @return array|null
     */
    public static function getCurrentUser()
    {
        if (!self::isAuthenticated()) {
            return null;
        }

        return [
            'id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null,
            'codigo' => isset($_SESSION['user_codigo']) ? $_SESSION['user_codigo'] : null,
            'nombres' => isset($_SESSION['user_nombres']) ? $_SESSION['user_nombres'] : null,
            'apellidos' => isset($_SESSION['user_apellidos']) ? $_SESSION['user_apellidos'] : null,
            'rol' => isset($_SESSION['user_rol']) ? $_SESSION['user_rol'] : null,
            'nombre_completo' => isset($_SESSION['user_nombre_completo']) ? $_SESSION['user_nombre_completo'] : null,
            'institucion_id' => isset($_SESSION['user_institucion_id']) ? $_SESSION['user_institucion_id'] : null,
            'institucion_nombre' => isset($_SESSION['user_institucion_nombre']) ? $_SESSION['user_institucion_nombre'] : null,
            'grado_id' => isset($_SESSION['user_grado_id']) ? $_SESSION['user_grado_id'] : null,
            'grado_nombre' => isset($_SESSION['user_grado_nombre']) ? $_SESSION['user_grado_nombre'] : null,
            'seccion' => isset($_SESSION['user_seccion']) ? $_SESSION['user_seccion'] : null
        ];
    }

    /**
     * Verifica si la sesión ha expirado
     * @param int $timeout Tiempo de expiración en segundos (por defecto 2 horas)
     * @return bool
     */
    public static function isSessionExpired($timeout = 7200)
    {
        if (!self::isAuthenticated()) {
            return true;
        }

        $loginTime = isset($_SESSION['login_time']) ? $_SESSION['login_time'] : 0;
        return (time() - $loginTime) > $timeout;
    }

    /**
     * Renueva la sesión
     */
    public static function renewSession()
    {
        if (self::isAuthenticated()) {
            $_SESSION['login_time'] = time();
        }
    }

    /**
     * Cierra la sesión
     */
    public static function logout()
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
    }
}
?>
