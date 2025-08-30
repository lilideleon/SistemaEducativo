<?php

class Evaluacion_model
{
    private $Conexion;
    private $ConexionSql;

    public function __construct()
    {
        $this->Conexion = new ClaseConexion();
    }

    // Lista encuestas, por defecto solo activas (y opcionalmente estado ACTIVA)
    /*public function evaluacion metodo ejemplo($soloActivas = true, $soloEstadoActiva = false)
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $sql = "SELECT id, titulo, curso_id, grado_id, institucion_id, estado, activo
                      FROM encuestas";
            $conds = [];
            if ($soloActivas) { $conds[] = "activo = 1"; }
            if ($soloEstadoActiva) { $conds[] = "estado = 'ACTIVA'"; }
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
            throw new Exception('Error al listar encuestas: ' . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }*/
}
?>
