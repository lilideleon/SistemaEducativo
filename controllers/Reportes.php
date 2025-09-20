<?php

class ReportesController
{
    public function __construct()
    {
        @session_start();
        require_once "core/AuthValidation.php";
        validarRol(['ADMIN','DIRECTOR']);
        //require_once "models/Alumnos.php";
        $data["titulo"] = "Reportes";
    }

    public function index()
    {
        require_once "views/Reportes/Reportes.php";
    }

    
}
?>
