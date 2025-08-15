<?php

class UsuariosController
{
    public function __construct()
    {
        @session_start();
        require_once "models/Usuarios.php";
        $data["titulo"] = "Usuarios";
    }

    public function index()
    {
        require_once "views/Usuarios/Usuarios.php";
    }

    
}
?>