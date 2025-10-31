<?php

class InicioController {
    public function __construct() {
        // Cargar modelos si se necesitan en el futuro
    }

    public function index() {
        $data["titulo"] = "Bienvenido";
        require_once "views/Inicio/Inicio.php";
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
        header('Location: ?c=Inicio');
        exit();
    }
}
