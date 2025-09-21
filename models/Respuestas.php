<?php

class Respuestas_model
{
    private $Conexion;
    private $ConexionSql;

    public function __construct()
    {
        $this->Conexion = new ClaseConexion();
    }

    // AGREGAR respuesta y devolver ID nuevo
    public function Agregar($pregunta_id, $respuesta_texto = null, $respuesta_numero = null, $es_correcta = 0, $activo = 1)
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();

            $stmt = $this->ConexionSql->prepare("CALL sp_respuestas_agregar(?, ?, ?, ?, ?, @p_id_nuevo)");
            // pregunta id
            $stmt->bindValue(1, (int)$pregunta_id, PDO::PARAM_INT);
            // texto puede ser null
            if ($respuesta_texto === '' || $respuesta_texto === null) {
                $stmt->bindValue(2, null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(2, $respuesta_texto, PDO::PARAM_STR);
            }
            // numero puede ser null
            if ($respuesta_numero === '' || $respuesta_numero === null) {
                $stmt->bindValue(3, null, PDO::PARAM_NULL);
            } else {
                // permitir float
                $stmt->bindValue(3, $respuesta_numero);
            }
            $stmt->bindValue(4, (int)$es_correcta, PDO::PARAM_INT);
            $stmt->bindValue(5, (int)$activo, PDO::PARAM_INT);
            $stmt->execute();
            while ($stmt->nextRowset()) { /* no-op */ }
            $stmt->closeCursor();

            $res = $this->ConexionSql->query("SELECT @p_id_nuevo AS id");
            $row = $res->fetch(PDO::FETCH_ASSOC);
            return isset($row['id']) ? (int)$row['id'] : null;
        } catch (Exception $e) {
            throw new Exception('Error al agregar respuesta: ' . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // LISTAR por pregunta
    public function ListarPorPregunta($pregunta_id, $soloActivas = true)
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $sql = "SELECT r.id, r.pregunta_id, r.respuesta_texto, r.respuesta_numero, r.es_correcta, r.activo
                      FROM respuestas r
                     WHERE r.pregunta_id = :pid" . ($soloActivas ? " AND r.activo = 1" : "") . "
                     ORDER BY r.id";
            $stmt = $this->ConexionSql->prepare($sql);
            $stmt->bindValue(':pid', (int)$pregunta_id, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $rows;
        } catch (Exception $e) {
            throw new Exception('Error al listar respuestas: ' . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // MODIFICAR respuesta existente
    public function Modificar($id, $respuesta_texto = null, $respuesta_numero = null, $es_correcta = 0, $activo = 1)
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $sql = "UPDATE respuestas
                       SET respuesta_texto = :texto,
                           respuesta_numero = :numero,
                           es_correcta = :correcta,
                           activo = :activo
                     WHERE id = :id";
            $stmt = $this->ConexionSql->prepare($sql);
            $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
            if ($respuesta_texto === '' || $respuesta_texto === null) {
                $stmt->bindValue(':texto', null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(':texto', $respuesta_texto, PDO::PARAM_STR);
            }
            if ($respuesta_numero === '' || $respuesta_numero === null) {
                $stmt->bindValue(':numero', null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(':numero', $respuesta_numero);
            }
            $stmt->bindValue(':correcta', (int)$es_correcta, PDO::PARAM_INT);
            $stmt->bindValue(':activo', (int)$activo, PDO::PARAM_INT);
            $stmt->execute();
            $rc = $stmt->rowCount();
            error_log('[Respuestas_model::Modificar] id='.(int)$id.' correcta='.(int)$es_correcta.' activo='.(int)$activo.' rowCount='.$rc);
            return $rc >= 0; // true incluso si los valores no cambian
        } catch (Exception $e) {
            throw new Exception('Error al modificar respuesta: ' . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // Establecer es_correcta y, si se marca como 1, desmarcar otras respuestas de la misma pregunta
    public function SetCorrecta($id, $valor)
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->ConexionSql->beginTransaction();

            // Obtener pregunta_id de esta respuesta
            $stmt = $this->ConexionSql->prepare("SELECT pregunta_id FROM respuestas WHERE id = :id LIMIT 1");
            $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row || !isset($row['pregunta_id'])) {
                throw new Exception('Respuesta no encontrada');
            }
            $pregunta_id = (int)$row['pregunta_id'];

            // Si valor = 1, desmarcar todas las otras respuestas de la misma pregunta
            if ((int)$valor === 1) {
                $stmtOff = $this->ConexionSql->prepare("UPDATE respuestas SET es_correcta = 0 WHERE pregunta_id = :pid");
                $stmtOff->bindValue(':pid', $pregunta_id, PDO::PARAM_INT);
                $stmtOff->execute();
            }

            // Marcar esta respuesta con el valor especificado (0 o 1)
            $stmtOn = $this->ConexionSql->prepare("UPDATE respuestas SET es_correcta = :val WHERE id = :id");
            $stmtOn->bindValue(':val', (int)$valor, PDO::PARAM_INT);
            $stmtOn->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $stmtOn->execute();

            $this->ConexionSql->commit();
            error_log('[Respuestas_model::SetCorrecta] id='.(int)$id.' -> correcta='.(int)$valor.' (pregunta_id='.$pregunta_id.')');
            return true;
        } catch (Exception $e) {
            if ($this->ConexionSql && $this->ConexionSql->inTransaction()) {
                $this->ConexionSql->rollBack();
            }
            throw new Exception('Error al actualizar correcta: ' . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }
}
?>
