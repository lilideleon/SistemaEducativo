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
}
?>
