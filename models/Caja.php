<?php
// CLASE DE MODELO PARA LA GESTIÓN DE CAJA Y DETALLES EN EL SISTEMA
// Esta clase se encarga de realizar operaciones CRUD sobre las tablas Caja y CajaDetalle.

class Caja_model
{
    private $ConexionSql;
    private $Conexion;
    private $SentenciaSql;
    private $Procedure;

    // Propiedades para la tabla Caja
    private $Id;
    private $UsuarioId;
    private $Fecha;
    private $HoraApertura;
    private $HoraCierre;
    private $MontoInicial;
    private $MontoFinal;
    private $MontoSistema;
    private $Diferencia;
    private $Estado;
    private $AuditXML;

    // Propiedades para la tabla CajaDetalle
    private $CajaId;
    private $Denominacion;
    private $Cantidad;
    private $Total;

    public function __construct()
    {
        $this->Conexion = new ClaseConexion();
    }

    // Método para insertar una caja
    public function InsertarCaja()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "CALL InsertarCaja(?, ?)";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

            $this->Procedure->bindParam(1, $this->getUsuarioId());
            $this->Procedure->bindParam(2, $this->getMontoInicial());

            $this->Procedure->execute();
        } catch (Exception $e) {
            echo "ERROR AL INSERTAR CAJA: " . $e->getMessage();
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // Método para actualizar una caja
    public function ActualizarCaja()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "CALL ActualizarCaja(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

            $this->Procedure->bindParam(1, $this->getId());
            $this->Procedure->bindParam(2, $this->getUsuarioId());
            $this->Procedure->bindParam(3, $this->getFecha());
            $this->Procedure->bindParam(4, $this->getHoraApertura());
            $this->Procedure->bindParam(5, $this->getHoraCierre());
            $this->Procedure->bindParam(6, $this->getMontoInicial());
            $this->Procedure->bindParam(7, $this->getMontoFinal());
            $this->Procedure->bindParam(8, $this->getMontoSistema());
            $this->Procedure->bindParam(9, $this->getDiferencia());
            $this->Procedure->bindParam(10, $this->getAuditXML());

            $this->Procedure->execute();
        } catch (Exception $e) {
            echo "ERROR AL ACTUALIZAR CAJA: " . $e->getMessage();
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // Método para eliminar lógicamente una caja
    public function EliminarCaja()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "CALL EliminarCaja(?, ?)";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

            $this->Procedure->bindParam(1, $this->getId());
            $this->Procedure->bindParam(2, $this->getAuditXML());

            $this->Procedure->execute();
        } catch (Exception $e) {
            echo "ERROR AL ELIMINAR CAJA: " . $e->getMessage();
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // Métodos para CajaDetalle

    // Método para insertar un detalle de caja
    public function InsertarCajaDetalle()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "CALL InsertarCajaDetalle(?, ?, ?, ?, ?)";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

            $this->Procedure->bindParam(1, $this->getCajaId());
            $this->Procedure->bindParam(2, $this->getFacturaId());
            $this->Procedure->bindParam(3, $this->getMonto());
            $this->Procedure->bindParam(4, $this->getTipoMovimiento());
            $this->Procedure->bindParam(5, $this->getDescripcion());

            $this->Procedure->execute();
        } catch (Exception $e) {
            echo "ERROR AL INSERTAR DETALLE DE CAJA: " . $e->getMessage();
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // Método para actualizar un detalle de caja
    public function ActualizarCajaDetalle()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "CALL ActualizarCajaDetalle(?, ?, ?, ?, ?)";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

            $this->Procedure->bindParam(1, $this->getId());
            $this->Procedure->bindParam(2, $this->getCajaId());
            $this->Procedure->bindParam(3, $this->getDenominacion());
            $this->Procedure->bindParam(4, $this->getCantidad());
            $this->Procedure->bindParam(5, $this->getTotal());

            $this->Procedure->execute();
        } catch (Exception $e) {
            echo "ERROR AL ACTUALIZAR DETALLE DE CAJA: " . $e->getMessage();
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // Método para eliminar lógicamente un detalle de caja
    public function EliminarCajaDetalle()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "CALL EliminarCajaDetalle(?, ?)";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);

            $this->Procedure->bindParam(1, $this->getId());
            $this->Procedure->bindParam(2, $this->getAuditXML());

            $this->Procedure->execute();
        } catch (Exception $e) {
            echo "ERROR AL ELIMINAR DETALLE DE CAJA: " . $e->getMessage();
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

    // Getters y Setters para Caja
    public function getId() { return $this->Id; }
    public function setId($Id) { $this->Id = $Id; return $this; }

    public function getUsuarioId() { return $this->UsuarioId; }
    public function setUsuarioId($UsuarioId) { $this->UsuarioId = $UsuarioId; return $this; }

    public function getFecha() { return $this->Fecha; }
    public function setFecha($Fecha) { $this->Fecha = $Fecha; return $this; }

    public function getHoraApertura() { return $this->HoraApertura; }
    public function setHoraApertura($HoraApertura) { $this->HoraApertura = $HoraApertura; return $this; }

    public function getHoraCierre() { return $this->HoraCierre; }
    public function setHoraCierre($HoraCierre) { $this->HoraCierre = $HoraCierre; return $this; }

    public function getMontoInicial() { return $this->MontoInicial; }
    public function setMontoInicial($MontoInicial) { $this->MontoInicial = $MontoInicial; return $this; }

    public function getMontoFinal() { return $this->MontoFinal; }
    public function setMontoFinal($MontoFinal) { $this->MontoFinal = $MontoFinal; return $this; }

    public function getMontoSistema() { return $this->MontoSistema; }
    public function setMontoSistema($MontoSistema) { $this->MontoSistema = $MontoSistema; return $this; }

    public function getDiferencia() { return $this->Diferencia; }
    public function setDiferencia($Diferencia) { $this->Diferencia = $Diferencia; return $this; }

    public function getEstado() { return $this->Estado; }
    public function setEstado($Estado) { $this->Estado = $Estado; return $this; }

    public function getAuditXML() { return $this->AuditXML; }
    public function setAuditXML($AuditXML) { $this->AuditXML = $AuditXML; return $this; }

    // Getters y Setters para CajaDetalle
    public function getCajaId() { return $this->CajaId; }
    public function setCajaId($CajaId) { $this->CajaId = $CajaId; return $this; }

    public function getDenominacion() { return $this->Denominacion; }
    public function setDenominacion($Denominacion) { $this->Denominacion = $Denominacion; return $this; }

    public function getCantidad() { return $this->Cantidad; }
    public function setCantidad($Cantidad) { $this->Cantidad = $Cantidad; return $this; }

    public function getTotal() { return $this->Total; }
    public function setTotal($Total) { $this->Total = $Total; return $this; }
}

