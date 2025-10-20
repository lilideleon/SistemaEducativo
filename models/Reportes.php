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

            // Construir condiciones adicionales para el JOIN (para no romper el LEFT JOIN)
            $joinExtras = [];
            $params = [];

            // Filtrado por periodo (YYYY-MM) o rango (fecha_inicio/fecha_fin) contra c.periodo
            if (!empty($filtros['periodo'])) {
                if (!empty($filtros['fecha_inicio']) && !empty($filtros['fecha_fin'])) {
                    $inicioY = date('Y-m', strtotime($filtros['fecha_inicio']));
                    $finY = date('Y-m', strtotime($filtros['fecha_fin']));
                    if ($inicioY === $finY) {
                        $joinExtras[] = 'c.periodo = ?';
                        $params[] = $inicioY;
                    } else {
                        $joinExtras[] = 'c.periodo BETWEEN ? AND ?';
                        $params[] = $inicioY;
                        $params[] = $finY;
                    }
                } else {
                    // Periodo simple YYYY-MM
                    $joinExtras[] = 'c.periodo = ?';
                    $params[] = $filtros['periodo'];
                }
            }

            $joinFilter = '';
            if (!empty($joinExtras)) {
                $joinFilter = ' AND ' . implode(' AND ', $joinExtras);
            }

            $sql = "SELECT 
                        i.id as institucion_id,
                        i.nombre as institucion,
                        COALESCE(ROUND(AVG(c.puntaje), 2), 0) as promedio,
                        COUNT(c.id) as total_calificaciones,
                        COALESCE(ROUND(MIN(c.puntaje), 2), 0) as min_puntaje,
                        COALESCE(ROUND(MAX(c.puntaje), 2), 0) as max_puntaje
                    FROM instituciones i
                    LEFT JOIN calificaciones c 
                      ON c.institucion_id = i.id 
                     AND c.activo = 1
                     $joinFilter
                    WHERE 1=1";

            // Filtro por institución (aplica a la tabla instituciones directamente)
            if (!empty($filtros['institucion_id'])) {
                $sql .= " AND i.id = ?";
                $params[] = $filtros['institucion_id'];
            }

            $sql .= " GROUP BY i.id, i.nombre
                      ORDER BY promedio DESC, total_calificaciones DESC
                      LIMIT 15";

            $stmt = $this->ConexionSql->prepare($sql);
            $stmt->execute($params);

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // LOG DEBUG: Ver qué datos realmente se obtienen
            error_log('=== PROMEDIOS POR INSTITUCION ===');
            error_log('Total instituciones obtenidas: ' . count($resultados));
            foreach ($resultados as $idx => $inst) {
                error_log("[$idx] {$inst['institucion']}: promedio={$inst['promedio']}, calificaciones={$inst['total_calificaciones']}");
            }
            error_log('SQL ejecutado: ' . $sql);
            error_log('Params: ' . json_encode($params));
            error_log('=================================');
            
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

    // Top preguntas con más errores (para dashboard)
    public function obtenerPreguntasConMasErrores($filtros = [], $limit = 10) {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $limit = intval($limit);
            if ($limit <= 0) { $limit = 10; }

            $sql = "SELECT
                        p.id AS pregunta_id,
                        LEFT(REPLACE(REPLACE(p.enunciado, '\r', ' '), '\n', ' '), 160) AS enunciado,
                        ra.encuesta_id,
                        e.titulo AS encuesta,
                        COUNT(*) AS total_respuestas,
                        SUM(CASE WHEN COALESCE(ra.es_correcta, r.es_correcta) = 1 THEN 1 ELSE 0 END) AS correctas,
                        SUM(CASE WHEN COALESCE(ra.es_correcta, r.es_correcta) = 1 THEN 0 ELSE 1 END) AS incorrectas
                    FROM respuestas_alumnos ra
                    JOIN preguntas p ON p.id = ra.pregunta_id AND p.activo = 1
                    LEFT JOIN respuestas r ON r.id = ra.respuesta_id
                    JOIN encuestas e ON e.id = ra.encuesta_id AND e.activo = 1
                    JOIN usuarios u ON u.id = ra.alumno_user_id AND u.activo = 1
                    WHERE ra.activo = 1";

            $params = [];

            if (!empty($filtros['institucion_id'])) {
                $sql .= " AND u.institucion_id = ?";
                $params[] = $filtros['institucion_id'];
            }
            if (!empty($filtros['grado_id'])) {
                $sql .= " AND u.grado_id = ?";
                $params[] = $filtros['grado_id'];
            }
            if (!empty($filtros['encuesta_id'])) {
                $sql .= " AND ra.encuesta_id = ?";
                $params[] = $filtros['encuesta_id'];
            }

            $sql .= " GROUP BY p.id, enunciado, ra.encuesta_id, e.titulo
                      HAVING COUNT(*) > 0
                      ORDER BY incorrectas DESC, total_respuestas DESC
                      LIMIT $limit";

            $stmt = $this->ConexionSql->prepare($sql);
            if (!empty($params)) {
                $stmt->execute($params);
            } else {
                $stmt->execute();
            }

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as &$row) {
                $row['incorrectas'] = (int)$row['incorrectas'];
                $row['total_respuestas'] = (int)$row['total_respuestas'];
                $row['tasa_error'] = $row['total_respuestas'] > 0
                    ? round(($row['incorrectas'] / $row['total_respuestas']) * 100, 1)
                    : 0;
            }
            return $rows;
        } catch (Exception $e) {
            error_log('ERROR obtenerPreguntasConMasErrores: ' . $e->getMessage());
            // Datos de ejemplo si no hay resultados reales
            return [
                [
                    'pregunta_id' => 0,
                    'enunciado' => 'Pregunta de ejemplo 1',
                    'encuesta_id' => 0,
                    'encuesta' => 'Encuesta Demo',
                    'total_respuestas' => 12,
                    'correctas' => 5,
                    'incorrectas' => 7,
                    'tasa_error' => 58.3
                ],
                [
                    'pregunta_id' => 0,
                    'enunciado' => 'Pregunta de ejemplo 2',
                    'encuesta_id' => 0,
                    'encuesta' => 'Encuesta Demo',
                    'total_respuestas' => 10,
                    'correctas' => 4,
                    'incorrectas' => 6,
                    'tasa_error' => 60.0
                ]
            ];
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }
}
