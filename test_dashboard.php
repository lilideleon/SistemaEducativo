<?php
// Test del controlador Dashboard
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'models/Reportes.php';
require_once 'controllers/Reportes.php';

echo "<h1>Test Dashboard Controller</h1>";

try {
    $reportesController = new ReportesController();
    
    // Simular el método dashboard pero con output debug
    $reportesModel = new ReportesModel();
    
    echo "<h2>Datos que obtiene el controlador:</h2>";
    
    $mejoresAlumnos = $reportesModel->obtenerMejoresAlumnos();
    echo "<h3>mejoresAlumnos:</h3>";
    echo "<pre>" . json_encode($mejoresAlumnos, JSON_PRETTY_PRINT) . "</pre>";
    echo "<p>Count: " . count($mejoresAlumnos) . "</p>";
    echo "<p>isset: " . (isset($mejoresAlumnos) ? 'true' : 'false') . "</p>";
    
    $cursosGrados = $reportesModel->obtenerCursosYGrados();
    echo "<h3>Cursos y Grados:</h3>";
    echo "<pre>" . json_encode($cursosGrados, JSON_PRETTY_PRINT) . "</pre>";
    
    // Test de la variable que se pasa a la vista
    echo "<h2>JavaScript que se generaría:</h2>";
    echo "<textarea rows='10' cols='80'>";
    echo "const alumnosData = " . (isset($mejoresAlumnos) ? json_encode($mejoresAlumnos) : '[]') . ";";
    echo "</textarea>";
    
} catch (Exception $e) {
    echo "<h2>Error:</h2>";
    echo $e->getMessage();
    echo "<br>Stack trace:<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>