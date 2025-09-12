<?php
class Material_model {
    private $ConexionSql;
    private $Conexion;
    public function __construct() {
        $this->Conexion = new ClaseConexion();
    }

    public function ListarCursos() {
        $this->ConexionSql = $this->Conexion->CrearConexion();
        $sql = "SELECT id, nombre, area FROM cursos WHERE activo=1 ORDER BY nombre";
        $st = $this->ConexionSql->query($sql);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
    public function ListarGrados() {
        $this->ConexionSql = $this->Conexion->CrearConexion();
        $sql = "SELECT id, nombre FROM grados WHERE activo=1 ORDER BY id";
        $st = $this->ConexionSql->query($sql);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
    public function ListarMateriales($filtros=[]) {
        $this->ConexionSql = $this->Conexion->CrearConexion();
        $where = 'm.activo=1';
        $params = [];
        if (!empty($filtros['curso_id'])) {
            $where .= ' AND m.curso_id = :curso_id';
            $params[':curso_id'] = $filtros['curso_id'];
        }
        if (!empty($filtros['grado_id'])) {
            $where .= ' AND m.grado_id = :grado_id';
            $params[':grado_id'] = $filtros['grado_id'];
        }
        if (!empty($filtros['docente_user_id'])) {
            $where .= ' AND m.docente_user_id = :docente_user_id';
            $params[':docente_user_id'] = $filtros['docente_user_id'];
        }
        $sql = "SELECT m.*, c.nombre as curso_nombre, g.nombre as grado_nombre
                FROM materiales m
                JOIN cursos c ON c.id = m.curso_id
                JOIN grados g ON g.id = m.grado_id
                WHERE $where ORDER BY m.publicado_at DESC";
        $st = $this->ConexionSql->prepare($sql);
        $st->execute($params);
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);
        // Adjuntar archivos
        foreach($rows as &$mat) {
            $mat['archivos'] = $this->ListarArchivos($mat['id']);
        }
        return $rows;
    }
    public function GuardarMaterial($data) {
        $this->ConexionSql = $this->Conexion->CrearConexion();
        $sql = "INSERT INTO materiales (docente_user_id, curso_id, grado_id, unidad_numero, unidad_titulo, anio_lectivo, titulo, descripcion) VALUES (?,?,?,?,?,?,?,?)";
        $st = $this->ConexionSql->prepare($sql);
        $st->execute([
            $data['docente_user_id'], $data['curso_id'], $data['grado_id'],
            $data['unidad_numero'], $data['unidad_titulo'], $data['anio_lectivo'],
            $data['titulo'], $data['descripcion']
        ]);
        return $this->ConexionSql->lastInsertId();
    }
    public function GuardarArchivo($material_id, $nombre_archivo, $url) {
        $this->ConexionSql = $this->Conexion->CrearConexion();
        $sql = "INSERT INTO material_archivos (material_id, url, nombre_archivo) VALUES (?,?,?)";
        $st = $this->ConexionSql->prepare($sql);
        $st->execute([$material_id, $url, $nombre_archivo]);
        return $this->ConexionSql->lastInsertId();
    }
    public function ListarArchivos($material_id) {
        $this->ConexionSql = $this->Conexion->CrearConexion();
        $sql = "SELECT id, url, nombre_archivo FROM material_archivos WHERE material_id=? AND activo=1";
        $st = $this->ConexionSql->prepare($sql);
        $st->execute([$material_id]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
    public function EliminarArchivo($id, $logico=true) {
        $this->ConexionSql = $this->Conexion->CrearConexion();
        if($logico) {
            $sql = "UPDATE material_archivos SET activo=0 WHERE id=?";
            $st = $this->ConexionSql->prepare($sql);
            return $st->execute([$id]);
        } else {
            $sql = "DELETE FROM material_archivos WHERE id=?";
            $st = $this->ConexionSql->prepare($sql);
            return $st->execute([$id]);
        }
    }
    public function EliminarMaterial($id, $logico=true) {
        $this->ConexionSql = $this->Conexion->CrearConexion();
        if($logico) {
            $sql = "UPDATE materiales SET activo=0 WHERE id=?";
            $st = $this->ConexionSql->prepare($sql);
            return $st->execute([$id]);
        } else {
            $sql = "DELETE FROM materiales WHERE id=?";
            $st = $this->ConexionSql->prepare($sql);
            return $st->execute([$id]);
        }
    }
    public function ListarCursosPorNombre($nombre) {
        $this->ConexionSql = $this->Conexion->CrearConexion();
        $sql = "SELECT id, nombre FROM cursos WHERE LOWER(nombre) = LOWER(?) AND activo=1 LIMIT 1";
        $st = $this->ConexionSql->prepare($sql);
        $st->execute([$nombre]);
        return $st->fetch(PDO::FETCH_ASSOC);
    }
}