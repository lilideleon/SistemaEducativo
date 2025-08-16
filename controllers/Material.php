<?php

class MaterialController
{
    public function __construct()
    {
        @session_start();
        //require_once "models/Alumnos.php";
        $data["titulo"] = "Material";
    }

    public function index()
    {
        require_once "views/Material/Material.php";
    }

    
}
?>