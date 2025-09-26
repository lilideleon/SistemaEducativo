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
            $sql = "SELECT id, nombre FROM grados WHERE activo = 1 ORDER BY nombre";
            $stmt = $this->ConexionSql->query($sql);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            throw new Exception("Error al obtener grados: " . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }
}
