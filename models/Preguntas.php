<?php

class Preguntas_model
{
    // Conexión
    private $Conexion;
    private $ConexionSql;

    public function __construct()
    {
        // Se asume existencia de ClaseConexion (ya usada en otros modelos del proyecto)
        $this->Conexion = new ClaseConexion();
    }

    // AGREGAR: llama a sp_preguntas_agregar y retorna el ID nuevo
    public function Agregar($encuesta_id, $enunciado, $tipo, $ponderacion = 1.00, $orden = null, $activo = 1)
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();

            // Usamos variable de sesión para OUT param
            $stmt = $this->ConexionSql->prepare("CALL sp_preguntas_agregar(?, ?, ?, ?, ?, ?, @p_id_nuevo)");
            $stmt->bindParam(1, $encuesta_id, PDO::PARAM_INT);
            $stmt->bindParam(2, $enunciado, PDO::PARAM_STR);
            $stmt->bindParam(3, $tipo, PDO::PARAM_STR);
            // PDO enviará null si corresponde
            $stmt->bindParam(4, $ponderacion);
            $stmt->bindParam(5, $orden, PDO::PARAM_INT);
            $stmt->bindParam(6, $activo, PDO::PARAM_INT);
            $stmt->execute();
            // Vaciar más resultados si el driver lo requiere
            while ($stmt->nextRowset()) { /* no-op */ }
            $stmt->closeCursor();

            $res = $this->ConexionSql->query("SELECT @p_id_nuevo AS id");
            $row = $res->fetch(PDO::FETCH_ASSOC);
            return isset($row['id']) ? (int)$row['id'] : null;
        } catch (Exception $e) {
            throw new Exception('Error al agregar pregunta: ' . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // MODIFICAR: llama a sp_preguntas_modificar, retorna true si ok
    public function Modificar($id, $encuesta_id, $enunciado, $tipo, $ponderacion = 1.00, $orden = null, $activo = 1)
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $stmt = $this->ConexionSql->prepare("CALL sp_preguntas_modificar(?, ?, ?, ?, ?, ?, ?)");
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->bindParam(2, $encuesta_id, PDO::PARAM_INT);
            $stmt->bindParam(3, $enunciado, PDO::PARAM_STR);
            $stmt->bindParam(4, $tipo, PDO::PARAM_STR);
            $stmt->bindParam(5, $ponderacion);
            $stmt->bindParam(6, $orden, PDO::PARAM_INT);
            $stmt->bindParam(7, $activo, PDO::PARAM_INT);
            $ok = $stmt->execute();
            while ($stmt->nextRowset()) { /* no-op */ }
            $stmt->closeCursor();
            return $ok;
        } catch (Exception $e) {
            throw new Exception('Error al modificar pregunta: ' . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // ELIMINAR LÓGICO: llama a sp_preguntas_eliminar_logico, retorna true si ok
    public function EliminarLogico($id)
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $stmt = $this->ConexionSql->prepare("CALL sp_preguntas_eliminar_logico(?)");
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $ok = $stmt->execute();
            while ($stmt->nextRowset()) { /* no-op */ }
            $stmt->closeCursor();
            return $ok;
        } catch (Exception $e) {
            throw new Exception('Error al eliminar lógicamente la pregunta: ' . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // LISTAR: devuelve preguntas activas (o todas si se requiere) con conteo de respuestas
    public function Listar($soloActivas = true)
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $sql = "SELECT p.id, p.encuesta_id, p.enunciado, p.tipo, p.ponderacion, p.orden, p.activo,
                           (SELECT COUNT(1) FROM respuestas r WHERE r.pregunta_id = p.id AND (r.activo = 1 OR r.activo IS NULL)) AS total_respuestas
                      FROM preguntas p";
            if ($soloActivas) {
                $sql .= " WHERE p.activo = 1";
            }
            $sql .= " ORDER BY COALESCE(p.orden, p.id)";

            $stmt = $this->ConexionSql->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $rows;
        } catch (Exception $e) {
            throw new Exception('Error al listar preguntas: ' . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // LISTAR por encuesta en orden aleatorio (solo activas por defecto)
    public function ListarPorEncuestaRandom($encuesta_id, $soloActivas = true)
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $sql = "SELECT p.id, p.encuesta_id, p.enunciado, p.tipo, p.ponderacion, p.orden, p.activo
                      FROM preguntas p
                     WHERE p.encuesta_id = :eid" . ($soloActivas ? " AND p.activo = 1" : "") . "
                     ORDER BY RAND()";
            $stmt = $this->ConexionSql->prepare($sql);
            $stmt->bindValue(':eid', (int)$encuesta_id, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $rows;
        } catch (Exception $e) {
            throw new Exception('Error al listar preguntas por encuesta: ' . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }
}
?>
