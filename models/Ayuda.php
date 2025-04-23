<?php
// CLASE DE MODELO PARA LA GESTIÓN DE CAJA Y DETALLES EN EL SISTEMA
// Esta clase se encarga de realizar operaciones CRUD sobre las tablas Caja y CajaDetalle.

class Ayuda_model
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


    // Method to fetch the current state of the cash register
    public function obtenerEstadoActualCaja()
    {
        try {
            $this->ConexionSql = $this->Conexion->CrearConexion();
            $this->SentenciaSql = "SELECT a.Fecha, MAX(a.MontoInicial) AS MontoInicial, SUM(b.Total) AS TotalIngresos, 0 AS TotalEgresos, MAX(a.MontoInicial) + SUM(b.Total) AS SaldoActual FROM Caja a INNER JOIN factura b ON b.Fecha = a.Fecha WHERE DATE(a.Fecha) = CURDATE() GROUP BY a.Fecha";
            $this->Procedure = $this->ConexionSql->prepare($this->SentenciaSql);
            $this->Procedure->execute();

            return $this->Procedure->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo "ERROR AL OBTENER ESTADO ACTUAL DE LA CAJA: " . $e->getMessage();
        } finally {
            $this->Conexion->CerrarConexion();
        }
    }

   

}

