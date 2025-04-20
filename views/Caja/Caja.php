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
        const montoInicial = document.getElementById('montoInicial').value;
        if (montoInicial === '' || montoInicial < 0) {
            alert('Por favor, ingrese un monto inicial válido.');
            return;
        }
        alert(`Caja aperturada con Q${montoInicial}.`);
        // Aquí puedes agregar la lógica para guardar el monto inicial en el servidor
    }

    function cerrarCaja() {
        const billetes200 = parseInt(document.getElementById('billetes200').value) || 0;
        const billetes100 = parseInt(document.getElementById('billetes100').value) || 0;
        const billetes50 = parseInt(document.getElementById('billetes50').value) || 0;
        const billetes20 = parseInt(document.getElementById('billetes20').value) || 0;
        const billetes10 = parseInt(document.getElementById('billetes10').value) || 0;
        const billetes5 = parseInt(document.getElementById('billetes5').value) || 0;
        const monedas1 = parseInt(document.getElementById('monedas1').value) || 0;
        const monedas050 = parseFloat(document.getElementById('monedas050').value) || 0;
        const monedas025 = parseFloat(document.getElementById('monedas025').value) || 0;

        const total = (billetes200 * 200) + (billetes100 * 100) + (billetes50 * 50) +
                      (billetes20 * 20) + (billetes10 * 10) + (billetes5 * 5) +
                      (monedas1 * 1) + (monedas050 * 0.50) + (monedas025 * 0.25);

        alert(`El total en caja es Q${total.toFixed(2)}.`);
        // Aquí puedes agregar la lógica para guardar el cierre de caja en el servidor
    }
</script>

<?php
    require 'views/Content/footer.php';
?>