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
                            <label for="Nombre" class="form-label">DPI del Empleado:</label>
                            <input type="text" id="Nombre" class="form-control" placeholder="Nombre del empleado" disabled>
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
                    <table class="table" id="Tabla_Asistencia">
                        <thead>
                            <tr>
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
        const tipoAsistencia = document.getElementById('tipoAsistencia').value;

        // Cambiar el tipo de contenido a application/x-www-form-urlencoded para que PHP lo procese correctamente
        $.ajax({
            url: '?c=Asistencia&a=InsertarAsistencia',
            type: 'POST',
            data: {
                Tipo: tipoAsistencia
            },
            success: function(data) {
                if (data.success) {
                    alertify.success(data.msj);
                    // ajax reload a la tabla de asistencia
                    $('#Tabla_Asistencia').DataTable().ajax.reload(); // false para no reiniciar la paginación

                } else {
                    alertify.error(data.msj);
                }
            },
            error: function(xhr, status, error) {
                alertify.error('Error en la solicitud: ' + error);
            }
        });
    }

    // Función opcional para recargar el historial de asistencia
    function cargarHistorialAsistencia() {
          var objetoDataTables_personal = $('#Tabla_Asistencia').DataTable({
            "language": {
                "emptyTable": "No hay datos disponibles en la tabla.",
                "info": "Del _START_ al _END_ de _TOTAL_ ",
                "infoEmpty": "Mostrando 0 registros de un total de 0.",
                "infoFiltered": "(filtrados de un total de _MAX_ registros)",
                "lengthMenu": "Mostrar _MENU_ registros",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscar:",
                "searchPlaceholder": "Dato para buscar",
                "zeroRecords": "No se han encontrado coincidencias.",
                "paginate": {
                    "first": "Primera",
                    "last": "Última",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "lengthMenu": [[5, 10, 20, 25, 50, 100], [5, 10, 20, 25, 50, 100]],
            "iDisplayLength": 15,
            "bProcessing": true,
            "bServerSide": true,
            dom: 'Blfrtip',
            buttons: ['copyHtml5', 'excelHtml5', 'csvHtml5', 'pdf'],
            "sAjaxSource": "?c=Asistencia&a=Tabla"
        });
    }

    // Función para obtener y mostrar el nombre del empleado
    function obtenerNombreEmpleado() {
        fetch('?c=Asistencia&a=ObtenerDatosUsuarioPorCodigo')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Colocar el nombre del empleado en el campo correspondiente
                    const nombreEmpleado = `${data.data.PrimerNombre} ${data.data.SegundoNombre} ${data.data.PrimerApellido} ${data.data.SegundoApellido}`;
                    document.getElementById('Nombre').value = nombreEmpleado;
                } else {
                    console.error('Error al obtener los datos del usuario:', data);
                }
            })
            .catch(error => console.error('Error en la solicitud:', error));
    }

    // Llamar a la función cargarHistorialAsistencia al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        obtenerNombreEmpleado();
        cargarHistorialAsistencia();
    });
</script>

<?php
    require 'views/Content/footer.php';
?>

