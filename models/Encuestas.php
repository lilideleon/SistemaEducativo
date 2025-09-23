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
            $sql = "SELECT id, titulo, curso_id, grado_id, institucion_id, estado, activo, fecha_inicio, fecha_fin
                      FROM encuestas";
            $conds = [];
            if ($soloActivas) { $conds[] = "activo = 1"; }
            if ($soloEstadoActiva) { $conds[] = "estado = 'ACTIVA'"; }
            if ($soloVigentes) {
                $conds[] = "( (fecha_inicio IS NULL OR NOW() >= fecha_inicio) AND (fecha_fin IS NULL OR NOW() <= fecha_fin) )";
            }
            if (!empty($conds)) {
                $sql .= " WHERE " . implode(' AND ', $conds);
            }
            $sql .= " ORDER BY id DESC";

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
