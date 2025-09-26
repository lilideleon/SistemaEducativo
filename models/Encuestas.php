<?php

class Encuestas_model
{
    private $Conexion;
    private $ConexionSql;

    public function __construct()
    {
        $this->Conexion = new ClaseConexion();
    }

    // Lista encuestas, por defecto solo activas (y opcionalmente estado ACTIVA). Puede filtrar por vigencia en fecha_inicio/fecha_fin
    public function Listar($soloActivas = true, $soloEstadoActiva = false, $soloVigentes = false)
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $sql = "SELECT 
                        e.id,
                        e.titulo,
                        c.nombre AS curso,
                        g.nombre AS grado,
                        COALESCE(e.unidad_numero, 1) as unidad_numero,
                        i.nombre AS institucion,
                        e.estado,
                        e.fecha_inicio AS inicio,
                        e.fecha_fin AS fin
                    FROM encuestas e
                    JOIN cursos c        ON c.id = e.curso_id
                    JOIN grados g        ON g.id = e.grado_id
                    LEFT JOIN instituciones i ON i.id = e.institucion_id";
            $conds = [];
            if ($soloActivas) { $conds[] = "e.activo = 1"; }
            if ($soloEstadoActiva) { $conds[] = "e.estado = 'ACTIVA'"; }
            if ($soloVigentes) {
                $conds[] = "( (e.fecha_inicio IS NULL OR NOW() >= e.fecha_inicio) AND (e.fecha_fin IS NULL OR NOW() <= e.fecha_fin) )";
            }
            if (!empty($conds)) {
                $sql .= " WHERE " . implode(' AND ', $conds);
            }
            $sql .= " ORDER BY e.fecha_inicio DESC";

            $stmt = $this->ConexionSql->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $rows;
        } catch (Exception $e) {
            error_log('Error al listar encuestas: ' . $e->getMessage());
            throw new Exception('Error al listar encuestas: ' . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // Obtener una encuesta por ID (sin validar vigencia)
    public function Obtener($id)
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $sql = "SELECT id, titulo, curso_id, grado_id, COALESCE(unidad_numero, 1) as unidad_numero, institucion_id, descripcion, fecha_inicio, fecha_fin, estado, creado_por, activo
                      FROM encuestas
                     WHERE id = :id
                     LIMIT 1";
            $stmt = $this->ConexionSql->prepare($sql);
            $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $row ?: null;
        } catch (Exception $e) {
            throw new Exception('Error al obtener encuesta: ' . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // Crear encuesta
    public function Agregar($data)
    {
        try {
            error_log('Datos recibidos en Agregar: ' . print_r($data, true));
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $sql = "INSERT INTO encuestas (titulo, curso_id, grado_id, unidad_numero, institucion_id, descripcion, fecha_inicio, fecha_fin, estado, creado_por, activo)
                    VALUES (:titulo, :curso_id, :grado_id, :unidad_numero, :institucion_id, :descripcion, :fecha_inicio, :fecha_fin, :estado, :creado_por, 1)";
            $st = $this->ConexionSql->prepare($sql);
            
            // Asignar valores
            $st->bindValue(':titulo', $data['titulo']);
            $st->bindValue(':curso_id', (int)$data['curso_id'], PDO::PARAM_INT);
            $st->bindValue(':grado_id', (int)$data['grado_id'], PDO::PARAM_INT);
            
            // Manejo seguro de unidad_numero
            $unidad_numero = 1; // valor por defecto
            if (isset($data['unidad_numero'])) {
                $unidad_numero = (int)$data['unidad_numero'];
                // Asegurar que está entre 1 y 4
                if ($unidad_numero < 1 || $unidad_numero > 4) {
                    $unidad_numero = 1;
                }
            }
            $st->bindValue(':unidad_numero', $unidad_numero, PDO::PARAM_INT);
            
            // Manejo de institución
            if (isset($data['institucion_id']) && $data['institucion_id'] === null) {
                $st->bindValue(':institucion_id', null, PDO::PARAM_NULL);
            } else {
                $st->bindValue(':institucion_id', isset($data['institucion_id']) ? (int)$data['institucion_id'] : null, PDO::PARAM_INT);
            }
            
            $st->bindValue(':descripcion', $data['descripcion']);
            $st->bindValue(':fecha_inicio', $data['fecha_inicio']);
            $st->bindValue(':fecha_fin', $data['fecha_fin']);
            $st->bindValue(':estado', strtoupper($data['estado']));
            $st->bindValue(':creado_por', (int)$data['creado_por'], PDO::PARAM_INT);
            
            $st->execute();
            return $this->ConexionSql->lastInsertId();
        } catch (Exception $e) {
            error_log('Error al agregar encuesta: ' . $e->getMessage());
            throw new Exception('Error al agregar encuesta: ' . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // Actualizar encuesta
    public function Actualizar($id, $data)
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $sql = "UPDATE encuestas
                       SET titulo = :titulo,
                           curso_id = :curso_id,
                           grado_id = :grado_id,
                           unidad_numero = :unidad_numero,
                           institucion_id = :institucion_id,
                           descripcion = :descripcion,
                           fecha_inicio = :fecha_inicio,
                           fecha_fin = :fecha_fin,
                           estado = :estado
                     WHERE id = :id";
            $st = $this->ConexionSql->prepare($sql);
            
            // Valores básicos
            $st->bindValue(':titulo', $data['titulo']);
            $st->bindValue(':curso_id', (int)$data['curso_id'], PDO::PARAM_INT);
            $st->bindValue(':grado_id', (int)$data['grado_id'], PDO::PARAM_INT);
            
            // Manejo seguro de unidad_numero
            $unidad_numero = 1; // valor por defecto
            if (isset($data['unidad_numero'])) {
                $unidad_numero = (int)$data['unidad_numero'];
                // Asegurar que está entre 1 y 4
                if ($unidad_numero < 1 || $unidad_numero > 4) {
                    $unidad_numero = 1;
                }
            }
            $st->bindValue(':unidad_numero', $unidad_numero, PDO::PARAM_INT);
            
            // Manejo de institución
            if (isset($data['institucion_id']) && $data['institucion_id'] === null) {
                $st->bindValue(':institucion_id', null, PDO::PARAM_NULL);
            } else {
                $st->bindValue(':institucion_id', isset($data['institucion_id']) ? (int)$data['institucion_id'] : null, PDO::PARAM_INT);
            }
            
            // Otros valores
            $st->bindValue(':descripcion', $data['descripcion']);
            $st->bindValue(':fecha_inicio', $data['fecha_inicio']);
            $st->bindValue(':fecha_fin', $data['fecha_fin']);
            $st->bindValue(':estado', strtoupper($data['estado']));
            $st->bindValue(':id', (int)$id, PDO::PARAM_INT);
            
            $st->execute();
            return true;
        } catch (Exception $e) {
            throw new Exception('Error al actualizar encuesta: ' . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // Eliminación lógica
    public function Eliminar($id)
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $sql = "UPDATE encuestas SET activo = 0 WHERE id = :id";
            $st = $this->ConexionSql->prepare($sql);
            $st->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $st->execute();
            return true;
        } catch (Exception $e) {
            throw new Exception('Error al eliminar encuesta: ' . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // Helpers para combos
    public function ListarCursos()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $sql = "SELECT id, nombre FROM cursos WHERE activo = 1 ORDER BY nombre";
            $st = $this->ConexionSql->query($sql);
            return $st->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception('Error al listar cursos: ' . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    public function ListarGrados()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $sql = "SELECT id, nombre FROM grados WHERE activo = 1 ORDER BY id";
            $st = $this->ConexionSql->query($sql);
            return $st->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception('Error al listar grados: ' . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    public function ListarInstituciones()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $sql = "SELECT id, nombre FROM instituciones WHERE activo = 1 ORDER BY nombre";
            $st = $this->ConexionSql->query($sql);
            return $st->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception('Error al listar instituciones: ' . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // Obtener una encuesta vigente por ID. Opcionalmente exigir ACTIVO=1 y estado ACTIVA
    public function ObtenerVigentePorId($id, $requireActiva = true, $requireActivo = true)
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $sql = "SELECT id, titulo, curso_id, grado_id, institucion_id, estado, activo, fecha_inicio, fecha_fin
                      FROM encuestas
                     WHERE id = :id";
            if ($requireActivo) {
                $sql .= " AND activo = 1";
            }
            if ($requireActiva) {
                $sql .= " AND estado = 'ACTIVA'";
            }
            // Ventana de vigencia por fechas
            $sql .= " AND ( (fecha_inicio IS NULL OR NOW() >= fecha_inicio) AND (fecha_fin IS NULL OR NOW() <= fecha_fin) )";

            $sql .= " LIMIT 1";

            $stmt = $this->ConexionSql->prepare($sql);
            $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $row ?: null;
        } catch (Exception $e) {
            error_log('Error al obtener encuesta vigente: ' . $e->getMessage());
            throw new Exception('Error al obtener encuesta vigente: ' . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }
}
?>
