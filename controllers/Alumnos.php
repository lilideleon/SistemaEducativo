<?php

class AlumnosController
{
    public function __construct()
    {
        @session_start();
        //require_once "models/Alumnos.php";
        $data["titulo"] = "Alumnos";
    }

    public function index()
    {
        require_once "views/Alumnos/Alumnos.php";
    }

    
}
?>