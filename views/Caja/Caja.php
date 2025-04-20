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
                }
            },
            error: function() {
                alertify.error("Error al aperturar la caja.");
  
            }
        });
    }


    //metodo para insertar un detalle a la caja

    function insertarDetalleCaja() {
        var cajaId = document.getElementById("cajaId").value;
        var denominacion = document.getElementById("denominacion").value;
        var cantidad = document.getElementById("cantidad").value;
        var total = document.getElementById("total").value;

        $.ajax({
            url: "?c=Caja&a=insertarDetalleCaja",
            type: "POST",
            data: { cajaId: cajaId, denominacion: denominacion, cantidad: cantidad, total: total },
            success: function(response) {
                var data = JSON.parse(response);
                alert(data.message);
                if (data.status === "success") {
                    document.getElementById("detalleCajaForm").reset();
                }
            },
            error: function() {
                alertify.error("Error al insertar el detalle de la caja.");
            }
        });
    }

</script>

<?php
    require 'views/Content/footer.php';
?>