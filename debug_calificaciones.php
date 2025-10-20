<?php
// Archivo temporal de depuración - ELIMINAR después de resolver el problema
@session_start();

require_once 'config/database.php';

header('Content-Type: text/html; charset=utf-8');

$conexion = new ClaseConexion();
$conexionSql = $conexion->CrearConexion();

// Consulta 1: Ver todas las calificaciones
$sql1 = "SELECT * FROM calificaciones ORDER BY id DESC LIMIT 20";
$stmt1 = $conexionSql->query($sql1);
$calificaciones = $stmt1->fetchAll(PDO::FETCH_ASSOC);

// Consulta 2: Contar calificaciones por institución
$sql2 = "SELECT 
            i.id,
            i.nombre as institucion,
            COUNT(c.id) as total_calificaciones,
            ROUND(AVG(c.puntaje), 2) as promedio,
            MIN(c.puntaje) as min_puntaje,
            MAX(c.puntaje) as max_puntaje,
            COUNT(CASE WHEN c.activo = 1 THEN 1 END) as activas,
            COUNT(CASE WHEN c.activo = 0 THEN 1 END) as inactivas
        FROM instituciones i
        LEFT JOIN calificaciones c ON c.institucion_id = i.id
        GROUP BY i.id, i.nombre
        ORDER BY promedio DESC";
$stmt2 = $conexionSql->query($sql2);
$porInstitucion = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// Consulta 3: Ver respuestas de alumnos
$sql3 = "SELECT COUNT(*) as total FROM respuestas_alumnos WHERE activo = 1";
$stmt3 = $conexionSql->query($sql3);
$respuestas = $stmt3->fetch(PDO::FETCH_ASSOC);

// Consulta 4: Calificaciones con activo = 1
$sql4 = "SELECT COUNT(*) as total FROM calificaciones WHERE activo = 1";
$stmt4 = $conexionSql->query($sql4);
$calificacionesActivas = $stmt4->fetch(PDO::FETCH_ASSOC);

// Consulta 5: Ver alumnos y sus instituciones
$sql5 = "SELECT u.id, u.nombres, u.apellidos, u.institucion_id, i.nombre as institucion 
         FROM usuarios u 
         LEFT JOIN instituciones i ON u.institucion_id = i.id 
         WHERE u.rol = 'ALUMNO' 
         LIMIT 10";
$stmt5 = $conexionSql->query($sql5);
$alumnos = $stmt5->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug - Calificaciones</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 1400px;
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
        h2 {
            color: #117867;
            margin-top: 30px;
        }
        .summary {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .summary strong {
            color: #0d47a1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 13px;
        }
        th {
            background: #117867;
            color: white;
            padding: 10px;
            text-align: left;
            position: sticky;
            top: 0;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        tr:hover {
            background: #f5f5f5;
        }
        .activo-si {
            color: green;
            font-weight: bold;
        }
        .activo-no {
            color: red;
            font-weight: bold;
        }
        .con-datos {
            background: #c8e6c9;
        }
        .sin-datos {
            background: #ffcdd2;
        }
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }
        .error {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 20px 0;
        }
        .success {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 DEBUG: Calificaciones y Respuestas</h1>
        
        <div class="summary">
            <h2>📊 Resumen General</h2>
            <p><strong>Total de respuestas de alumnos:</strong> <?= $respuestas['total'] ?></p>
            <p><strong>Total de calificaciones activas:</strong> <?= $calificacionesActivas['total'] ?></p>
            <p><strong>Instituciones en la BD:</strong> <?= count($porInstitucion) ?></p>
        </div>

        <?php if ($respuestas['total'] > 0 && $calificacionesActivas['total'] == 0): ?>
        <div class="error">
            <strong>⚠️ PROBLEMA DETECTADO:</strong> Hay <?= $respuestas['total'] ?> respuestas de alumnos pero NO hay calificaciones activas.
            Esto significa que las evaluaciones se están guardando pero NO se están generando las calificaciones.
        </div>
        <?php elseif ($calificacionesActivas['total'] > 0): ?>
        <div class="success">
            <strong>✅ BIEN:</strong> Hay <?= $calificacionesActivas['total'] ?> calificaciones registradas.
        </div>
        <?php endif; ?>

        <h2>🏫 Calificaciones por Institución</h2>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>ID</th>
                    <th>Institución</th>
                    <th>Total Calificaciones</th>
                    <th>Activas</th>
                    <th>Inactivas</th>
                    <th>Promedio</th>
                    <th>Min</th>
                    <th>Max</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($porInstitucion as $idx => $inst): ?>
                <tr class="<?= $inst['total_calificaciones'] > 0 ? 'con-datos' : 'sin-datos' ?>">
                    <td><?= $idx + 1 ?></td>
                    <td><?= $inst['id'] ?></td>
                    <td><?= htmlspecialchars($inst['institucion']) ?></td>
                    <td><strong><?= $inst['total_calificaciones'] ?></strong></td>
                    <td style="color: green;"><?= $inst['activas'] ?></td>
                    <td style="color: red;"><?= $inst['inactivas'] ?></td>
                    <td><?= isset($inst['promedio']) ? $inst['promedio'] : 0 ?>%</td>
                    <td><?= isset($inst['min_puntaje']) ? $inst['min_puntaje'] : 0 ?></td>
                    <td><?= isset($inst['max_puntaje']) ? $inst['max_puntaje'] : 0 ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>📋 Últimas 20 Calificaciones</h2>
        <?php if (count($calificaciones) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Alumno ID</th>
                    <th>Curso ID</th>
                    <th>Institución ID</th>
                    <th>Grado ID</th>
                    <th>Periodo</th>
                    <th>Puntaje</th>
                    <th>Activo</th>
                    <th>Creado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($calificaciones as $cal): ?>
                <tr>
                    <td><?= $cal['id'] ?></td>
                    <td><?= $cal['alumno_user_id'] ?></td>
                    <td><?= $cal['curso_id'] ?></td>
                    <td><?= $cal['institucion_id'] ?></td>
                    <td><?= isset($cal['grado_id']) ? $cal['grado_id'] : 'NULL' ?></td>
                    <td><?= $cal['periodo'] ?></td>
                    <td><strong><?= $cal['puntaje'] ?>%</strong></td>
                    <td class="<?= $cal['activo'] == 1 ? 'activo-si' : 'activo-no' ?>">
                        <?= $cal['activo'] == 1 ? '✅ Sí' : '❌ No' ?>
                    </td>
                    <td><?= isset($cal['creado_en']) ? $cal['creado_en'] : 'N/A' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="warning">
            <strong>⚠️ NO HAY CALIFICACIONES:</strong> La tabla `calificaciones` está vacía.
        </div>
        <?php endif; ?>

        <h2>👨‍🎓 Primeros 10 Alumnos y sus Instituciones</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Institución ID</th>
                    <th>Institución</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($alumnos as $al): ?>
                <tr>
                    <td><?= $al['id'] ?></td>
                    <td><?= htmlspecialchars($al['nombres'] . ' ' . $al['apellidos']) ?></td>
                    <td><?= isset($al['institucion_id']) ? $al['institucion_id'] : '<span style="color:red">NULL</span>' ?></td>
                    <td><?= htmlspecialchars(isset($al['institucion']) ? $al['institucion'] : 'Sin asignar') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="margin-top: 30px; padding: 15px; background: #fff3cd; border-radius: 4px;">
            <p><strong>⚠️ NOTA:</strong> Este archivo es solo para depuración. Elimínalo después de resolver el problema.</p>
            <p><strong>Ubicación:</strong> <code>debug_calificaciones.php</code></p>
        </div>
    </div>
</body>
</html>
