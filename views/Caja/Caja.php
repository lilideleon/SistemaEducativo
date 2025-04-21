<?php 
   include 'views/Content/header.php';
?>

<?php 
if ($_SESSION['TipoUsuario'] == '') {
    print "<script>
        window.location='?c=Login'; 
    </script>";
} else if ($_SESSION['TipoUsuario'] == 1) {
    require 'views/Content/sidebar.php'; 
} 
?>

<style>
    .move-left {
        position: relative; 
        left: 0; 
    }

    .move-down {
        margin-top: 15px;
    }

    .make-larger {
        width: 100%; 
        max-width: 1950px; /* Permite crecer hasta 1950px, pero no más que eso */
        height: auto; /* Se ajusta según el contenido */
        max-height: 800px; /* Altura máxima de 800px */
    }

    /* Estilos para pantallas con un ancho mínimo de 1024px (ejemplo para pantallas grandes) */
    @media (min-width: 1024px) {
        .move-left {
            left: 28px;
        }
    }
</style>

<div class="content-wrapper">
    <div class="row move-left move-down make-larger">
        <!-- Apertura de Caja -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>💵 Apertura de Caja</h5>
                </div>
                <div class="card-body">
                    <form id="aperturaCajaForm">
                        <div class="mb-3">
                            <label for="montoInicial" class="form-label">Monto Inicial (Q):</label>
                            <input type="number" id="montoInicial" class="form-control" placeholder="Ingrese el monto inicial" min="0" required>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="aperturarCaja()">Aperturar Caja</button>
                    </form>
                </div>
            </div>

            <!-- Card para mostrar el estado actual -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5>📊 Estado Actual de la Caja</h5>
                </div>
                <div class="card-body">
                    <p><strong>Fecha:</strong> <span id="Fecha">0.00</span></p>
                    <p><strong>Monto Inicial:</strong> Q<span id="estadoMontoInicial">0.00</span></p>
                    <p><strong>Total Ingresos:</strong> Q<span id="estadoIngresos">0.00</span></p>
                    <p><strong>Total Egresos:</strong> Q<span id="estadoEgresos">0.00</span></p>
                    <p><strong>Saldo Actual:</strong> Q<span id="estadoSaldo">0.00</span></p>
                </div>
            </div>



        </div>

        <!-- Cierre de Caja -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>🧾 Cierre de Caja</h5>
                </div>
                <div class="card-body">
                    <form id="cierreCajaForm">
                        <h6>Billetes:</h6>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label for="billetes200" class="form-label">Billetes de Q200:</label>
                                <input type="number" id="billetes200" class="form-control" value="0" min="0">
                            </div>
                            <div class="col-md-6">
                                <label for="billetes100" class="form-label">Billetes de Q100:</label>
                                <input type="number" id="billetes100" class="form-control" value="0" min="0">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label for="billetes50" class="form-label">Billetes de Q50:</label>
                                <input type="number" id="billetes50" class="form-control" value="0" min="0">
                            </div>
                            <div class="col-md-6">
                                <label for="billetes20" class="form-label">Billetes de Q20:</label>
                                <input type="number" id="billetes20" class="form-control" value="0" min="0">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label for="billetes10" class="form-label">Billetes de Q10:</label>
                                <input type="number" id="billetes10" class="form-control" value="0" min="0">
                            </div>
                            <div class="col-md-6">
                                <label for="billetes5" class="form-label">Billetes de Q5:</label>
                                <input type="number" id="billetes5" class="form-control" value="0" min="0">
                            </div>
                        </div>
                        <h6>Monedas:</h6>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label for="monedas1" class="form-label">Monedas de Q1:</label>
                                <input type="number" id="monedas1" class="form-control" value="0" min="0">
                            </div>
                            <div class="col-md-6">
                                <label for="monedas050" class="form-label">Monedas de Q0.50:</label>
                                <input type="number" id="monedas050" class="form-control" value="0" min="0">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label for="monedas025" class="form-label">Monedas de Q0.25:</label>
                                <input type="number" id="monedas025" class="form-control" value="0" min="0">
                            </div>
                        </div>
                        <button type="button" class="btn btn-success" onclick="cerrarCaja()">Cerrar Caja</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>


    function aperturarCaja() {
        var montoInicial = document.getElementById("montoInicial").value;

        if (montoInicial <= 0) {
            alertify.error("El monto inicial debe ser mayor que cero.");
            return;
        }

        $.ajax({
            url: "?c=Caja&a=aperturarCaja",
            type: "POST",
            data: { montoInicial: montoInicial },
            success: function(response) {
                var data = JSON.parse(response);
                
                alertify.success(data.message);
                if (data.status === "success") {
                    document.getElementById("aperturaCajaForm").reset();
                    actualizarEstadoCaja();
                }
            },
            error: function() {
                alertify.error("Error al aperturar la caja.");
  
            }
        });
    }


    //metodo para insertar un detalle a la caja

    function insertarDetalleCaja() {
        // Obtener los valores del formulario
        var cajaId = $("#cajaId").val();
        var denominacion = $("#denominacion").val();
        var cantidad = parseInt($("#cantidad").val());

        if (!cajaId || !denominacion || isNaN(cantidad) || cantidad <= 0) {
            alertify.error("Todos los campos son obligatorios y la cantidad debe ser mayor a cero.");
            return;
        }

        // Enviar los datos al servidor
        $.ajax({
            url: "?c=Caja&a=insertarDetalleCaja",
            type: "POST",
            data: { cajaId: cajaId, denominacion: denominacion, cantidad: cantidad },
            success: function(response) {
                var data = JSON.parse(response);

                if (data.status === "success") {
                    alertify.success(data.message);
                    $("#detalleCajaForm")[0].reset();
                } else {
                    alertify.error(data.message);
                }
            },
            error: function() {
                alertify.error("Error al insertar el detalle de la caja.");
            }
        });
    }

    function actualizarEstadoCaja() {

        $.ajax({
            url: "?c=Caja&a=obtenerEstadoActualCaja",
            type: "GET",
            success: function(response) {

                var data = JSON.parse(response);

                if (data.status === "success") {
                    var estado = data.data;
                    document.getElementById("Fecha").textContent = estado.Fecha;
                    document.getElementById("estadoMontoInicial").textContent = parseFloat(estado.MontoInicial).toFixed(2);
                    document.getElementById("estadoIngresos").textContent = parseFloat(estado.TotalIngresos).toFixed(2);
                    document.getElementById("estadoEgresos").textContent = parseFloat(estado.TotalEgresos).toFixed(2);
                    document.getElementById("estadoSaldo").textContent = parseFloat(estado.SaldoActual).toFixed(2);

                } else {
                    alertify.error(data.message);
                }
            },
            error: function() {
                alertify.error("Error al actualizar el estado de la caja.");
            }
        });
    }

    function cerrarCaja() {
        // Recopilar los datos de billetes y monedas
        var detalles = [];

        // Billetes
        detalles.push({ denominacion: 200, cantidad: parseInt($("#billetes200").val()), total: 200 * parseInt($("#billetes200").val()) });
        detalles.push({ denominacion: 100, cantidad: parseInt($("#billetes100").val()), total: 100 * parseInt($("#billetes100").val()) });
        detalles.push({ denominacion: 50, cantidad: parseInt($("#billetes50").val()), total: 50 * parseInt($("#billetes50").val()) });
        detalles.push({ denominacion: 20, cantidad: parseInt($("#billetes20").val()), total: 20 * parseInt($("#billetes20").val()) });
        detalles.push({ denominacion: 10, cantidad: parseInt($("#billetes10").val()), total: 10 * parseInt($("#billetes10").val()) });
        detalles.push({ denominacion: 5, cantidad: parseInt($("#billetes5").val()), total: 5 * parseInt($("#billetes5").val()) });

        // Monedas
        detalles.push({ denominacion: 1, cantidad: parseInt($("#monedas1").val()), total: 1 * parseInt($("#monedas1").val()) });
        detalles.push({ denominacion: 0.5, cantidad: parseInt($("#monedas050").val()), total: 0.5 * parseInt($("#monedas050").val()) });
        detalles.push({ denominacion: 0.25, cantidad: parseInt($("#monedas025").val()), total: 0.25 * parseInt($("#monedas025").val()) });

        // Obtener monto final desde el saldo actual
        var montoFinal = parseFloat($("#estadoSaldo").text());

        if (isNaN(montoFinal) || montoFinal <= 0) {
            alertify.error("El monto final debe ser un número mayor que cero.");
            return;
        }

        // Validar si la caja está abierta
        $.ajax({
            url: "?c=Caja&a=obtenerIdCajaActual",
            type: "GET",
            success: function(response) {
                var data = JSON.parse(response);

                if (data.status === "error") {
                    alertify.error(data.message);
                    return;
                }

                if (data.idCaja.Estado !== "Abierta") {
                    alertify.error("La caja ya está cerrada.");
                    return;
                }

                // Enviar los datos al servidor
                $.ajax({
                    url: "?c=Caja&a=cerrarCaja",
                    type: "POST",
                    data: { detalles: detalles, montoFinal: montoFinal },
                    success: function(response) {
                        var data = JSON.parse(response);

                        if (data.status === "success") {
                            alertify.success(data.message);
                            $("#cierreCajaForm")[0].reset();
                            actualizarEstadoCaja();
                        } else {
                            alertify.error(data.message);
                        }
                    },
                    error: function() {
                        alertify.error("Error al cerrar la caja.");
                    }
                });
            },
            error: function() {
                alertify.error("Error al validar el estado de la caja.");
            }
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        actualizarEstadoCaja();
        setInterval(actualizarEstadoCaja, 3000); // refresca cada 2 segundos
    });
</script>

<?php
    require 'views/Content/footer.php';
?>