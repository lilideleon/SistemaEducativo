<?php

class RespuestasAlumnos_model
{
    private $Conexion;
    private $ConexionSql;

    public function __construct()
    {
        $this->Conexion = new ClaseConexion();
    }

    // Guardar respuesta del alumno usando el procedimiento almacenado
    public function GuardarRespuesta($alumno_user_id, $encuesta_id, $pregunta_id, $respuesta_id = null, $respuesta_texto = null, $respuesta_numero = null)
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();

            $stmt = $this->ConexionSql->prepare("CALL sp_respuestas_alumnos_agregar(?, ?, ?, ?, ?, ?, @p_id_nuevo)");
            
            // Parámetros de entrada
            $stmt->bindValue(1, (int)$alumno_user_id, PDO::PARAM_INT);
            $stmt->bindValue(2, (int)$encuesta_id, PDO::PARAM_INT);
            $stmt->bindValue(3, (int)$pregunta_id, PDO::PARAM_INT);
            
            // respuesta_id puede ser null
            if ($respuesta_id === '' || $respuesta_id === null) {
                $stmt->bindValue(4, null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(4, (int)$respuesta_id, PDO::PARAM_INT);
            }
            
            // respuesta_texto puede ser null
            if ($respuesta_texto === '' || $respuesta_texto === null) {
                $stmt->bindValue(5, null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(5, $respuesta_texto, PDO::PARAM_STR);
            }
            
            // respuesta_numero puede ser null
            if ($respuesta_numero === '' || $respuesta_numero === null) {
                $stmt->bindValue(6, null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(6, $respuesta_numero);
            }
            
            $stmt->execute();
            
            // Vaciar más resultados si el driver lo requiere
            while ($stmt->nextRowset()) { /* no-op */ }
            $stmt->closeCursor();

            // Obtener el ID generado
            $res = $this->ConexionSql->query("SELECT @p_id_nuevo AS id");
            $row = $res->fetch(PDO::FETCH_ASSOC);
            return isset($row['id']) ? (int)$row['id'] : null;
            
        } catch (Exception $e) {
            throw new Exception('Error al guardar respuesta del alumno: ' . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // Guardar múltiples respuestas de una encuesta
    public function GuardarRespuestasEncuesta($alumno_user_id, $encuesta_id, $respuestas)
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->ConexionSql->beginTransaction();
            
            $ids_guardados = [];
            
            foreach ($respuestas as $respuesta) {
                $pregunta_id = $respuesta['pregunta_id'];
                $respuesta_id = isset($respuesta['respuesta_id']) ? $respuesta['respuesta_id'] : null;
                $respuesta_texto = isset($respuesta['respuesta_texto']) ? $respuesta['respuesta_texto'] : null;
                $respuesta_numero = isset($respuesta['respuesta_numero']) ? $respuesta['respuesta_numero'] : null;
                
                // Manejar preguntas de opción múltiple (array de respuesta_id)
                if (is_array($respuesta_id)) {
                    foreach ($respuesta_id as $rid) {
                        $id_nuevo = $this->GuardarRespuesta(
                            $alumno_user_id, 
                            $encuesta_id, 
                            $pregunta_id, 
                            $rid, 
                            null, 
                            null
                        );
                        
                        if ($id_nuevo) {
                            $ids_guardados[] = $id_nuevo;
                        }
                    }
                } else {
                    // Pregunta de opción única, abierta o numérica
                    $id_nuevo = $this->GuardarRespuesta(
                        $alumno_user_id, 
                        $encuesta_id, 
                        $pregunta_id, 
                        $respuesta_id, 
                        $respuesta_texto, 
                        $respuesta_numero
                    );
                    
                    if ($id_nuevo) {
                        $ids_guardados[] = $id_nuevo;
                    }
                }
            }
            
            $this->ConexionSql->commit();
            return $ids_guardados;
            
        } catch (Exception $e) {
            if ($this->ConexionSql && $this->ConexionSql->inTransaction()) {
                $this->ConexionSql->rollBack();
            }
            throw new Exception('Error al guardar respuestas de la encuesta: ' . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // Verificar si un alumno ya respondió una encuesta
    public function AlumnoYaRespondio($alumno_user_id, $encuesta_id)
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $sql = "SELECT COUNT(1) as total FROM respuestas_alumnos 
                    WHERE alumno_user_id = :alumno_id AND encuesta_id = :encuesta_id AND activo = 1";
            
            $stmt = $this->ConexionSql->prepare($sql);
            $stmt->bindValue(':alumno_id', (int)$alumno_user_id, PDO::PARAM_INT);
            $stmt->bindValue(':encuesta_id', (int)$encuesta_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            
            return isset($row['total']) && (int)$row['total'] > 0;
            
        } catch (Exception $e) {
            throw new Exception('Error al verificar si el alumno ya respondió: ' . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // Obtener respuestas de un alumno para una encuesta
    public function ObtenerRespuestasAlumno($alumno_user_id, $encuesta_id)
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $sql = "SELECT ra.id, ra.pregunta_id, ra.respuesta_id, ra.respuesta_texto, 
                           ra.respuesta_numero, ra.es_correcta, ra.fecha_creacion,
                           p.enunciado, p.tipo
                    FROM respuestas_alumnos ra
                    INNER JOIN preguntas p ON p.id = ra.pregunta_id
                    WHERE ra.alumno_user_id = :alumno_id 
                    AND ra.encuesta_id = :encuesta_id 
                    AND ra.activo = 1
                    ORDER BY ra.fecha_creacion";
            
            $stmt = $this->ConexionSql->prepare($sql);
            $stmt->bindValue(':alumno_id', (int)$alumno_user_id, PDO::PARAM_INT);
            $stmt->bindValue(':encuesta_id', (int)$encuesta_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            
            return $rows;
            
        } catch (Exception $e) {
            throw new Exception('Error al obtener respuestas del alumno: ' . $e->getMessage());
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }
}
?>
