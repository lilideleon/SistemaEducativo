<?php

class EvaluacionController
{
    public function __construct()
    {
        @session_start();
        //require_once "models/Alumnos.php";
        $data["titulo"] = "Evaluacion";
    }

    public function index()
    {
        require_once "views/Evaluacion/Evaluacion.php";
    }

    
}
?>