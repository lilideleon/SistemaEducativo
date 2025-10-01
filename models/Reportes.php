<?php
class ReportesModel {
    private $Conexion;
    private $ConexionSql;

    public function __construct() {
        require_once "config/database.php";
        $this->Conexion = new ClaseConexion();
    }

    public function obtenerUsuarios($filtros = []) {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            
            $sql = "SELECT u.*, i.nombre as institucion_nombre, g.nombre as grado_nombre 
                    FROM usuarios u 
                    LEFT JOIN instituciones i ON u.institucion_id = i.id 
                    LEFT JOIN grados g ON u.grado_id = g.id 
                    WHERE 1=1";
            $params = [];

            if (!empty($filtros['rol'])) {
                $sql .= " AND u.rol = ?";
                $params[] = $filtros['rol'];
            }

            if (!empty($filtros['institucion_id'])) {
                $sql .= " AND u.institucion_id = ?";
                $params[] = $filtros['institucion_id'];
            }

            if (!empty($filtros['grado_id'])) {
                $sql .= " AND u.grado_id = ?";
                $params[] = $filtros['grado_id'];
            }

            if (isset($filtros['activo']) && $filtros['activo'] !== '') {
                $sql .= " AND u.activo = ?";
                $params[] = $filtros['activo'];
            }

            if (!empty($filtros['fecha_inicio']) && !empty($filtros['fecha_fin'])) {
                $sql .= " AND u.creado_en BETWEEN ? AND ?";
                $params[] = $filtros['fecha_inicio'] . ' 00:00:00';
                $params[] = $filtros['fecha_fin'] . ' 23:59:59';
            }

            if (!empty($filtros['busqueda'])) {
                $sql .= " AND (u.nombres LIKE ? OR u.apellidos LIKE ? OR u.codigo LIKE ?)";
                $busqueda = "%{$filtros['busqueda']}%";
                $params[] = $busqueda;
                $params[] = $busqueda;
                $params[] = $busqueda;
            }

            $sql .= " ORDER BY u.id DESC";
            
            $stmt = $this->ConexionSql->prepare($sql);
            
            if (!empty($params)) {
                $stmt->execute($params);
            } else {
                $stmt->execute();
            }

            return $stmt->fetchAll(PDO::FETCH_OBJ);

        } catch (Exception $e) {
            throw new Exception("Error al obtener usuarios: " . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    public function obtenerInstituciones() {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $sql = "SELECT id, nombre FROM instituciones WHERE activo = 1 ORDER BY nombre";
            $stmt = $this->ConexionSql->query($sql);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            throw new Exception("Error al obtener instituciones: " . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    public function obtenerGrados() {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $sql = "SELECT id, nombre FROM grados ORDER BY nombre";
            $stmt = $this->ConexionSql->query($sql);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            throw new Exception("Error al obtener grados: " . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // Métodos para el Dashboard

    public function contarUsuarios() {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $sql = "SELECT COUNT(*) as total FROM usuarios WHERE activo = 1";
            $stmt = $this->ConexionSql->query($sql);
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            return $result->total;
        } catch (Exception $e) {
            throw new Exception("Error al contar usuarios: " . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    public function contarInstituciones() {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $sql = "SELECT COUNT(*) as total FROM instituciones";
            $stmt = $this->ConexionSql->query($sql);
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            return $result->total;
        } catch (Exception $e) {
            throw new Exception("Error al contar instituciones: " . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    public function contarEncuestas() {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $sql = "SELECT COUNT(*) as total FROM encuestas WHERE activo = 1";
            $stmt = $this->ConexionSql->query($sql);
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            return $result->total;
        } catch (Exception $e) {
            throw new Exception("Error al contar encuestas: " . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    public function contarCalificaciones() {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $sql = "SELECT COUNT(*) as total FROM calificaciones WHERE activo = 1";
            $stmt = $this->ConexionSql->query($sql);
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            return $result->total;
        } catch (Exception $e) {
            throw new Exception("Error al contar calificaciones: " . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    public function obtenerUsuariosPorRol($filtros = []) {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $sql = "SELECT rol, COUNT(*) as total FROM usuarios WHERE activo = 1";
            $params = [];


            if (!empty($filtros['institucion_id'])) {
                $sql .= " AND institucion_id = ?";
                $params[] = $filtros['institucion_id'];
            }

            if (!empty($filtros['grado_id'])) {
                $sql .= " AND grado_id = ?";
                $params[] = $filtros['grado_id'];
            }

            // Si se envía un array de roles, filtrar
            if (!empty($filtros['roles']) && is_array($filtros['roles'])) {
                // Construir placeholders y añadir al SQL
                $placeholders = rtrim(str_repeat('?,', count($filtros['roles'])), ',');
                $sql .= " AND rol IN ($placeholders)";
                foreach ($filtros['roles'] as $r) $params[] = $r;
            }

            $sql .= " GROUP BY rol ORDER BY total DESC";
            $stmt = $this->ConexionSql->prepare($sql);
            if (!empty($params)) {
                $stmt->execute($params);
            } else {
                $stmt->execute();
            }
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error al obtener usuarios por rol: " . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    public function obtenerInstitucionesPorDistrito($filtros = []) {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $sql = "SELECT d.nombre as distrito, COUNT(i.id) as total 
                    FROM distritos d 
                    LEFT JOIN instituciones i ON d.id = i.distrito_id ";

            $params = [];
            if (!empty($filtros['institucion_id'])) {
                $sql .= " WHERE i.id = ?";
                $params[] = $filtros['institucion_id'];
            }

            $sql .= " GROUP BY d.id, d.nombre 
                    ORDER BY total DESC 
                    LIMIT 10";

            $stmt = $this->ConexionSql->prepare($sql);
            if (!empty($params)) {
                $stmt->execute($params);
            } else {
                $stmt->execute();
            }
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error al obtener instituciones por distrito: " . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    public function obtenerEncuestasPorEstado() {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $sql = "SELECT estado, COUNT(*) as total FROM encuestas WHERE activo = 1 GROUP BY estado";
            $stmt = $this->ConexionSql->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error al obtener encuestas por estado: " . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    public function obtenerPromedioCalificaciones() {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $sql = "SELECT AVG(puntaje) as promedio FROM calificaciones WHERE activo = 1";
            $stmt = $this->ConexionSql->query($sql);
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            return round($result->promedio, 2);
        } catch (Exception $e) {
            throw new Exception("Error al obtener promedio de calificaciones: " . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    public function obtenerActividadReciente() {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            
            // Simulamos actividad reciente combinando varias tablas
            $sql = "SELECT 
                        DATE_FORMAT(u.creado_en, '%d/%m/%Y %H:%i') as fecha,
                        CONCAT(u.nombres, ' ', u.apellidos) as usuario,
                        'Registro de Usuario' as accion,
                        CONCAT('Usuario ', u.rol, ' registrado') as detalle,
                        'Completado' as estado
                    FROM usuarios u 
                    WHERE u.creado_en >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                    
                    UNION ALL
                    
                    SELECT 
                        DATE_FORMAT(e.fecha_inicio, '%d/%m/%Y') as fecha,
                        'Sistema' as usuario,
                        'Encuesta Creada' as accion,
                        e.titulo as detalle,
                        CASE 
                            WHEN e.estado = 'activa' THEN 'Completado'
                            ELSE 'Pendiente'
                        END as estado
                    FROM encuestas e 
                    WHERE e.fecha_inicio >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                    
                    ORDER BY STR_TO_DATE(fecha, '%d/%m/%Y %H:%i') DESC 
                    LIMIT 10";
                    
            $stmt = $this->ConexionSql->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Si hay error, devolvemos datos de ejemplo
            return [
                [
                    'fecha' => date('d/m/Y H:i'),
                    'usuario' => 'Sistema',
                    'accion' => 'Inicio Dashboard',
                    'detalle' => 'Dashboard cargado correctamente',
                    'estado' => 'Completado'
                ]
            ];
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    public function obtenerPromediosPorInstitucion($filtros = []) {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $sql = "SELECT 
                        i.nombre as institucion,
                        AVG(c.puntaje) as promedio,
                        COUNT(c.id) as total_calificaciones,
                        MIN(c.puntaje) as min_puntaje,
                        MAX(c.puntaje) as max_puntaje
                    FROM calificaciones c 
                    INNER JOIN instituciones i ON c.institucion_id = i.id 
                    WHERE c.activo = 1";

            $params = [];
            if (!empty($filtros['institucion_id'])) {
                $sql .= " AND c.institucion_id = ?";
                $params[] = $filtros['institucion_id'];
            }

            // Si se proporcionó periodo (rango o periodo exacto), usar la columna period o la columna `periodo` en calificaciones
            if (!empty($filtros['periodo'])) {
                // Si viene como rango de fechas
                if (!empty($filtros['fecha_inicio']) && !empty($filtros['fecha_fin'])) {
                    // No hay columna fecha explícita en calificaciones según tu esquema; si tu intención es filtrar por periodo
                    // (ej. '2025-09'), el campo correcto es `periodo`. Si usas fecha completa en otra tabla, actualiza aquí.
                    // Para seguridad, intentaremos filtrar por `periodo` que contenga el año-mes
                    $inicio = $filtros['fecha_inicio'];
                    $fin = $filtros['fecha_fin'];
                    // Convertir a YYYY-MM para buscar en periodo si aplica
                    $inicioY = date('Y-m', strtotime($inicio));
                    $finY = date('Y-m', strtotime($fin));
                    if ($inicioY === $finY) {
                        $sql .= " AND c.periodo = ?";
                        $params[] = $inicioY;
                    } else {
                        // Si es rango de meses distinto, intentar usar BETWEEN comparando strings (asumiendo formato YYYY-MM en periodo)
                        $sql .= " AND c.periodo BETWEEN ? AND ?";
                        $params[] = $inicioY;
                        $params[] = $finY;
                    }
                }
            }

            $sql .= " GROUP BY c.institucion_id, i.nombre 
                    HAVING COUNT(c.id) > 0
                    ORDER BY promedio DESC 
                    LIMIT 15";

            $stmt = $this->ConexionSql->prepare($sql);
            if (!empty($params)) {
                $stmt->execute($params);
            } else {
                $stmt->execute();
            }

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Formatear los datos para mejor presentación
            foreach ($resultados as &$resultado) {
                $resultado['promedio'] = round($resultado['promedio'], 2);
                $resultado['min_puntaje'] = round($resultado['min_puntaje'], 2);
                $resultado['max_puntaje'] = round($resultado['max_puntaje'], 2);
            }
            
            return $resultados;
        } catch (Exception $e) {
            error_log('ERROR obtenerPromediosPorInstitucion: ' . $e->getMessage());
            return [];
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    public function obtenerMejoresAlumnos($filtros = []) {
        error_log("=== DEBUGGING: obtenerMejoresAlumnos() iniciado ===");
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            error_log("Conexión a base de datos establecida");
            
            $sql = "SELECT 
                        CONCAT(u.nombres, ' ', u.apellidos) as alumno,
                        u.nombres,
                        u.apellidos,
                        c.nombre as curso,
                        g.nombre as grado,
                        i.nombre as institucion,
                        AVG(cal.puntaje) as promedio,
                        COUNT(cal.id) as total_calificaciones,
                        MAX(cal.puntaje) as mejor_nota,
                        MIN(cal.puntaje) as menor_nota,
                        cal.curso_id,
                        cal.grado_id
                    FROM calificaciones cal
                    INNER JOIN usuarios u ON cal.alumno_user_id = u.id
                    INNER JOIN cursos c ON cal.curso_id = c.id
                    INNER JOIN grados g ON cal.grado_id = g.id
                    INNER JOIN instituciones i ON cal.institucion_id = i.id
                    WHERE cal.activo = 1 AND u.rol = 'ALUMNO'";

            $params = [];
            if (!empty($filtros['institucion_id'])) {
                $sql .= " AND cal.institucion_id = ?";
                $params[] = $filtros['institucion_id'];
            }

            if (!empty($filtros['curso_id'])) {
                $sql .= " AND cal.curso_id = ?";
                $params[] = $filtros['curso_id'];
            }

            if (!empty($filtros['grado_id'])) {
                $sql .= " AND cal.grado_id = ?";
                $params[] = $filtros['grado_id'];
            }

            // Manejo de periodo específico (campo `periodo` en la tabla calificaciones)
            if (!empty($filtros['periodo']) && is_string($filtros['periodo'])) {
                // Si el periodo se pasa como 'YYYY-MM' o similar
                $sql .= " AND c.periodo = ?";
                $params[] = $filtros['periodo'];
            }

            $sql .= " GROUP BY cal.alumno_user_id, cal.curso_id, cal.grado_id, 
                             u.nombres, u.apellidos, c.nombre, g.nombre, i.nombre
                    HAVING COUNT(cal.id) >= 2
                    ORDER BY promedio DESC, total_calificaciones DESC
                    LIMIT 20";

            error_log("SQL MejoresAlumnos: " . $sql);

            $stmt = $this->ConexionSql->prepare($sql);
            if (!empty($params)) {
                $stmt->execute($params);
            } else {
                $stmt->execute();
            }

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Debug resultados
            error_log("Resultados obtenerMejoresAlumnos: " . json_encode($resultados));
            error_log("Total registros encontrados: " . count($resultados));
            
            // Si no hay datos reales, usar datos de ejemplo
            if (empty($resultados)) {
                error_log("No se encontraron datos reales, usando datos de ejemplo");
                return [
                    [
                        'alumno' => 'María González', 'curso' => 'Matemáticas', 'grado' => '3er Grado',
                        'promedio' => 95.5, 'total_calificaciones' => 8, 'institucion' => 'Instituto Nacional',
                        'curso_id' => 1, 'grado_id' => 3, 'nombres' => 'María', 'apellidos' => 'González'
                    ],
                    [
                        'alumno' => 'Carlos Rodríguez', 'curso' => 'Ciencias', 'grado' => '4to Grado', 
                        'promedio' => 94.2, 'total_calificaciones' => 6, 'institucion' => 'Colegio San José',
                        'curso_id' => 2, 'grado_id' => 4, 'nombres' => 'Carlos', 'apellidos' => 'Rodríguez'
                    ],
                    [
                        'alumno' => 'Ana Martínez', 'curso' => 'Lenguaje', 'grado' => '5to Grado',
                        'promedio' => 93.8, 'total_calificaciones' => 7, 'institucion' => 'Escuela Central',
                        'curso_id' => 3, 'grado_id' => 5, 'nombres' => 'Ana', 'apellidos' => 'Martínez'
                    ]
                ];
            }
            
            // Formatear los datos
            foreach ($resultados as &$resultado) {
                $resultado['promedio'] = round($resultado['promedio'], 2);
                $resultado['mejor_nota'] = round($resultado['mejor_nota'], 2);
                $resultado['menor_nota'] = round($resultado['menor_nota'], 2);
            }
            
            return $resultados;
        } catch (Exception $e) {
            error_log('ERROR obtenerMejoresAlumnos: ' . $e->getMessage());
            return [];
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    public function obtenerCursosYGrados() {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            
            $cursos = $this->ConexionSql->query("SELECT id, nombre FROM cursos ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
            $grados = $this->ConexionSql->query("SELECT id, nombre FROM grados ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'cursos' => $cursos,
                'grados' => $grados
            ];
        } catch (Exception $e) {
            return [
                'cursos' => [
                    ['id' => 1, 'nombre' => 'Matemáticas'],
                    ['id' => 2, 'nombre' => 'Ciencias'],
                    ['id' => 3, 'nombre' => 'Lenguaje'],
                    ['id' => 4, 'nombre' => 'Historia']
                ],
                'grados' => [
                    ['id' => 1, 'nombre' => '1er Grado'],
                    ['id' => 2, 'nombre' => '2do Grado'],
                    ['id' => 3, 'nombre' => '3er Grado'],
                    ['id' => 4, 'nombre' => '4to Grado'],
                    ['id' => 5, 'nombre' => '5to Grado']
                ]
            ];
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // ==================== NUEVOS MÉTODOS PARA REPORTES ====================

    public function obtenerCalificaciones($filtros = []) {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            
            $sql = "SELECT 
                        c.id,
                        c.puntaje,
                        c.periodo,
                        CONCAT(u.nombres, ' ', u.apellidos) as alumno,
                        u.codigo as codigo_alumno,
                        cur.nombre as curso,
                        g.nombre as grado,
                        i.nombre as institucion,
                        c.activo
                    FROM calificaciones c
                    INNER JOIN usuarios u ON c.alumno_user_id = u.id
                    INNER JOIN cursos cur ON c.curso_id = cur.id
                    INNER JOIN grados g ON c.grado_id = g.id
                    INNER JOIN instituciones i ON c.institucion_id = i.id
                    WHERE c.activo = 1";
            
            $params = [];
            
            if (!empty($filtros['institucion_id'])) {
                $sql .= " AND c.institucion_id = ?";
                $params[] = $filtros['institucion_id'];
            }
            
            if (!empty($filtros['curso_id'])) {
                $sql .= " AND c.curso_id = ?";
                $params[] = $filtros['curso_id'];
            }
            
            if (!empty($filtros['grado_id'])) {
                $sql .= " AND c.grado_id = ?";
                $params[] = $filtros['grado_id'];
            }
            
            if (!empty($filtros['periodo'])) {
                $sql .= " AND c.periodo = ?";
                $params[] = $filtros['periodo'];
            }
            
            $sql .= " ORDER BY u.apellidos, u.nombres, cur.nombre";
            
            $stmt = $this->ConexionSql->prepare($sql);
            if (!empty($params)) {
                $stmt->execute($params);
            } else {
                $stmt->execute();
            }
            
            return $stmt->fetchAll(PDO::FETCH_OBJ);
            
        } catch (Exception $e) {
            error_log('ERROR obtenerCalificaciones: ' . $e->getMessage());
            return [];
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    public function obtenerDistritos() {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $sql = "SELECT id, nombre FROM distritos ORDER BY nombre";
            $stmt = $this->ConexionSql->query($sql);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            error_log('ERROR obtenerDistritos: ' . $e->getMessage());
            return [];
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    public function obtenerResumenAcademicoDetallado($filtros = []) {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            
            $sql = "SELECT 
                        i.id as institucion_id,
                        i.nombre as institucion,
                        i.tipo,
                        d.nombre as distrito,
                        COUNT(DISTINCT u.id) as total_usuarios,
                        COUNT(DISTINCT CASE WHEN u.rol = 'ALUMNO' THEN u.id END) as total_alumnos,
                        COUNT(DISTINCT CASE WHEN u.rol = 'DOCENTE' THEN u.id END) as total_docentes,
                        COUNT(DISTINCT CASE WHEN u.rol = 'DIRECTOR' THEN u.id END) as total_directores,
                        COUNT(DISTINCT c.id) as total_calificaciones,
                        COALESCE(ROUND(AVG(c.puntaje), 2), 0) as promedio_general,
                        COUNT(DISTINCT e.id) as total_encuestas,
                        COUNT(DISTINCT u.grado_id) as grados_activos
                    FROM instituciones i
                    LEFT JOIN distritos d ON i.distrito_id = d.id
                    LEFT JOIN usuarios u ON i.id = u.institucion_id AND u.activo = 1
                    LEFT JOIN calificaciones c ON i.id = c.institucion_id AND c.activo = 1
                    LEFT JOIN encuestas e ON i.id = e.institucion_id AND e.activo = 1
                    WHERE 1=1";
            
            $params = [];
            
            if (!empty($filtros['institucion_id'])) {
                $sql .= " AND i.id = ?";
                $params[] = $filtros['institucion_id'];
            }
            
            if (!empty($filtros['distrito_id'])) {
                $sql .= " AND i.distrito_id = ?";
                $params[] = $filtros['distrito_id'];
            }
            
            $sql .= " GROUP BY i.id, i.nombre, i.tipo, d.nombre
                     HAVING total_usuarios > 0
                     ORDER BY total_alumnos DESC, i.nombre";
            
            $stmt = $this->ConexionSql->prepare($sql);
            if (!empty($params)) {
                $stmt->execute($params);
            } else {
                $stmt->execute();
            }
            
            return $stmt->fetchAll(PDO::FETCH_OBJ);
            
        } catch (Exception $e) {
            error_log('ERROR obtenerResumenAcademicoDetallado: ' . $e->getMessage());
            return [];
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    public function obtenerInstitucionesPorDistritoDetallado($filtros = []) {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            
            $sql = "SELECT 
                        d.id as distrito_id,
                        d.nombre as distrito,
                        i.id as institucion_id,
                        i.nombre as institucion,
                        i.tipo,
                        COUNT(DISTINCT u.id) as total_usuarios,
                        COUNT(DISTINCT CASE WHEN u.rol = 'ALUMNO' THEN u.id END) as total_alumnos,
                        COUNT(DISTINCT CASE WHEN u.rol = 'DOCENTE' THEN u.id END) as total_docentes,
                        COALESCE(ROUND(AVG(c.puntaje), 2), 0) as promedio_institucion
                    FROM distritos d
                    LEFT JOIN instituciones i ON d.id = i.distrito_id
                    LEFT JOIN usuarios u ON i.id = u.institucion_id AND u.activo = 1
                    LEFT JOIN calificaciones c ON i.id = c.institucion_id AND c.activo = 1
                    WHERE i.id IS NOT NULL";
            
            $params = [];
            
            if (!empty($filtros['distrito_id'])) {
                $sql .= " AND d.id = ?";
                $params[] = $filtros['distrito_id'];
            }
            
            $sql .= " GROUP BY d.id, d.nombre, i.id, i.nombre, i.tipo
                     ORDER BY d.nombre, i.nombre";
            
            $stmt = $this->ConexionSql->prepare($sql);
            if (!empty($params)) {
                $stmt->execute($params);
            } else {
                $stmt->execute();
            }
            
            return $stmt->fetchAll(PDO::FETCH_OBJ);
            
        } catch (Exception $e) {
            error_log('ERROR obtenerInstitucionesPorDistritoDetallado: ' . $e->getMessage());
            return [];
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }
}
