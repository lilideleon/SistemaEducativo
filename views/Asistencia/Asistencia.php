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
        <!-- Registro de Asistencia -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>🕒 Registro de Asistencia</h5>
                </div>
                <div class="card-body">
                    <form id="asistenciaForm">
                        <div class="mb-3">
                            <label for="empleadoDpi" class="form-label">DPI del Empleado:</label>
                            <input type="text" id="empleadoDpi" class="form-control" placeholder="Ingrese el DPI del empleado" required>
                        </div>
                        <div class="mb-3">
                            <label for="tipoAsistencia" class="form-label">Tipo de Registro:</label>
                            <select id="tipoAsistencia" class="form-control" required>
                                <option value="Entrada">Entrada</option>
                                <option value="Salida">Salida</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="registrarAsistencia()">Registrar</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Historial de Asistencia -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>📋 Historial de Asistencia</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>DPI</th>
                                <th>Nombre</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Tipo</th>
                            </tr>
                        </thead>
                        <tbody id="historialAsistencia">
                            <!-- Aquí se llenará dinámicamente el historial -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Función para registrar asistencia
    function registrarAsistencia() {
        const dpi = document.getElementById('empleadoDpi').value;
        const tipo = document.getElementById('tipoAsistencia').value;

        if (!dpi || !tipo) {
            alert('Por favor, complete todos los campos.');
            return;
        }

        // Simulación de registro (puedes reemplazarlo con una llamada AJAX al servidor)
        const fecha = new Date();
        const fechaFormateada = fecha.toLocaleDateString();
        const horaFormateada = fecha.toLocaleTimeString();

        const nuevaFila = `
            <tr>
                <td>${dpi}</td>
                <td>Nombre Ejemplo</td> <!-- Reemplazar con el nombre real del empleado -->
                <td>${fechaFormateada}</td>
                <td>${horaFormateada}</td>
                <td>${tipo}</td>
            </tr>
        `;

        document.getElementById('historialAsistencia').insertAdjacentHTML('beforeend', nuevaFila);

        alert('Asistencia registrada con éxito.');
        document.getElementById('asistenciaForm').reset();
    }
</script>

<?php
    require 'views/Content/footer.php';
?>

