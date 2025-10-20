<?php
// Archivo temporal de depuración - ELIMINAR después de resolver el problema
@session_start();

require_once 'config/database.php';
require_once 'models/Reportes.php';

header('Content-Type: text/html; charset=utf-8');

$modelo = new ReportesModel();
$promedios = $modelo->obtenerPromediosPorInstitucion([]);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug - Promedios por Institución</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #117867;
            padding-bottom: 10px;
        }
        .summary {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th {
            background: #117867;
            color: white;
            padding: 12px;
            text-align: left;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        tr:hover {
            background: #f5f5f5;
        }
        .con-datos {
            background: #c8e6c9;
        }
        .sin-datos {
            background: #ffcdd2;
        }
        pre {
            background: #263238;
            color: #aed581;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 DEBUG: Promedios por Institución</h1>
        
        <div class="summary">
            <h2>Resumen</h2>
            <p><strong>Total de instituciones:</strong> <?= count($promedios) ?></p>
            <p><strong>Con datos (promedio > 0):</strong> <?= count(array_filter($promedios, function($p) { return $p['promedio'] > 0; })) ?></p>
            <p><strong>Sin datos (promedio = 0):</strong> <?= count(array_filter($promedios, function($p) { return $p['promedio'] == 0; })) ?></p>
        </div>

        <h2>📊 Tabla de Datos</h2>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>ID</th>
                    <th>Institución</th>
                    <th>Promedio</th>
                    <th>Total Calificaciones</th>
                    <th>Min</th>
                    <th>Max</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($promedios as $idx => $inst): ?>
                <tr class="<?= $inst['promedio'] > 0 ? 'con-datos' : 'sin-datos' ?>">
                    <td><?= $idx + 1 ?></td>
                    <td><?= $inst['institucion_id'] ?></td>
                    <td><?= htmlspecialchars($inst['institucion']) ?></td>
                    <td><strong><?= number_format($inst['promedio'], 2) ?>%</strong></td>
                    <td><?= $inst['total_calificaciones'] ?></td>
                    <td><?= number_format($inst['min_puntaje'], 2) ?></td>
                    <td><?= number_format($inst['max_puntaje'], 2) ?></td>
                    <td><?= $inst['promedio'] > 0 ? '✅ Con datos' : '❌ Sin datos' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>📝 JSON Raw Data</h2>
        <pre><?= json_encode($promedios, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?></pre>

        <div style="margin-top: 30px; padding: 15px; background: #fff3cd; border-radius: 4px;">
            <p><strong>⚠️ NOTA:</strong> Este archivo es solo para depuración. Elimínalo después de resolver el problema.</p>
            <p><strong>Ubicación:</strong> <code>debug_promedios.php</code></p>
        </div>
    </div>
</body>
</html>
