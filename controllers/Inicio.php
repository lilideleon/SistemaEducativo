<?php

class InicioController {
    public function __construct() {
        // Cargar modelos si se necesitan en el futuro
    }

    public function index() {
        $data["titulo"] = "Bienvenido";
        require_once "views/Inicio/Inicio.php";
    }
}
