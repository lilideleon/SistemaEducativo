<?php
// Script de prueba para verificar que la función obtenerMejoresAlumnos() funciona
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'models/Reportes.php';

echo "<h1>Test de mejores alumnos</h1>";

try {
    $reportesModel = new ReportesModel();
    $mejoresAlumnos = $reportesModel->obtenerMejoresAlumnos();
    
    echo "<h2>Resultado:</h2>";
    echo "<pre>";
    var_dump($mejoresAlumnos);
    echo "</pre>";
    
    echo "<h2>JSON:</h2>";
    echo "<pre>" . json_encode($mejoresAlumnos, JSON_PRETTY_PRINT) . "</pre>";
    
    echo "<h2>Count:</h2>";
    echo count($mejoresAlumnos) . " elementos";
    
} catch (Exception $e) {
    echo "<h2>Error:</h2>";
    echo $e->getMessage();
}
?>