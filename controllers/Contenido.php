<?php

class ContenidoController
{
    public function __construct()
    {
        @session_start();
        require_once "core/AuthValidation.php";
        validarRol(['ADMIN']);
        //require_once "models/Alumnos.php";
        $data["titulo"] = "Contenido";
    }

    public function index()
    {
        require_once "views/Contenido/Contenido.php";
    }

    
}
?>
