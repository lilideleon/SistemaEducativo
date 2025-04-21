<?php
require 'views/Content/header.php';
?>

<?php
if ($_SESSION['TipoUsuario'] == '') {
    print "<script>
        window.location='?c=Login'; 
    </script>";
} else if ($_SESSION['TipoUsuario'] == 1) {
    require 'views/Content/sidebar.php';
} else if ($_SESSION['TipoUsuario'] == 2) {
    require 'views/Content/sidebar2.php';
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
        max-width: 1950px; 
        height: auto; 
        max-height: 800px; 
    }

    @media (min-width: 1024px) {
        .move-left {
            left: 28px;
        }
    }
</style>

<div class="content-wrapper">
    <div class="row move-left move-down make-larger">
        <!-- Listado de Compras -->
        <div class="col-12 col-md-6 col-lg-11">
            <div class="card mb-4">
                <center>
                    <h1 class="card-header">Listado de Compras</h1>
                </center>
                <div class="card-body demo-vertical-spacing demo-only-element">
                    <div class="card-body">
                        <small class="text fw-semibold">En este módulo puede visualizar, registrar y modificar las compras.</small>
                        <div class="demo-inline-spacing d-flex justify-content-end">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#NuevaCompraModal">Nueva Compra</button>
                        </div>
                    </div>

                    <!-- Tabla de Compras -->
                    <div class="card">
                        <h5 class="card-header">Listado de Compras</h5>
                        <div class="table-responsive text-nowrap">
                            <table class="table" id="Tabla_Compras">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Fecha</th>
                                        <th>Hora</th>
                                        <th>Proveedor</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    <!-- Aquí se llenarán los datos dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PARA REGISTRAR NUEVA COMPRA -->
<div class="modal fade" id="NuevaCompraModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Nueva Compra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="NuevaCompraForm">
                    <div class="mb-3">
                        <label for="Fecha" class="form-label">Fecha:</label>
                        <input type="date" id="Fecha" name="Fecha" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="Hora" class="form-label">Hora:</label>
                        <input type="time" id="Hora" name="Hora" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="Proveedor" class="form-label">Proveedor:</label>
                        <select id="Proveedor" name="Proveedor" class="form-control" required>
                            <option value="1">General</option> <!-- Proveedor por defecto -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="UsuarioId" class="form-label">Usuario:</label>
                        <input type="text" id="UsuarioId" name="UsuarioId" class="form-control" required>
                    </div>

                    <!-- Tabla para los detalles de la compra -->
                    <div class="mb-3">
                        <label class="form-label">Detalles de la Compra:</label>
                        <table class="table table-bordered" id="DetallesCompraTable">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unitario</th>
                                    <th>Subtotal</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Filas dinámicas -->
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-success" onclick="AgregarFilaDetalle()">Agregar Producto</button>
                    </div>

                    <div class="mb-3">
                        <label for="Total" class="form-label">Total:</label>
                        <input type="number" id="Total" name="Total" class="form-control" step="0.01" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="Observaciones" class="form-label">Observaciones:</label>
                        <textarea id="Observaciones" name="Observaciones" class="form-control"></textarea>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="RegistrarCompra()">Registrar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PARA EDITAR COMPRA -->
<div class="modal fade" id="EditarCompraModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Compra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="EditarCompraForm">
                    <input type="hidden" name="Id" id="EditarId">
                    <div class="mb-3">
                        <label for="EditarFecha" class="form-label">Fecha:</label>
                        <input type="date" id="EditarFecha" name="Fecha" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="EditarHora" class="form-label">Hora:</label>
                        <input type="time" id="EditarHora" name="Hora" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="EditarProveedor" class="form-label">Proveedor:</label>
                        <select id="EditarProveedor" name="Proveedor" class="form-control" required>
                            <!-- Opciones cargadas dinámicamente -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="EditarUsuarioId" class="form-label">Usuario:</label>
                        <input type="text" id="EditarUsuarioId" name="UsuarioId" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="EditarTotal" class="form-label">Total:</label>
                        <input type="number" id="EditarTotal" name="Total" class="form-control" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="EditarObservaciones" class="form-label">Observaciones:</label>
                        <textarea id="EditarObservaciones" name="Observaciones" class="form-control"></textarea>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="ActualizarCompra()">Actualizar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="js/Custom/Compras.js"></script>
<script>


    document.addEventListener('DOMContentLoaded', function() {
        Cargar();
    });

    
    function Cargar ()
    {
    var objetoDataTables_personal = $('#Tabla_Compras').DataTable({
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
            "sAjaxSource": "?c=Compras&a=Tabla"
        });
    }



    function AgregarFilaDetalle() {
        const tableBody = document.querySelector('#DetallesCompraTable tbody');
        const row = document.createElement('tr');

        row.innerHTML = `
            <td><input type="text" class="form-control" name="ProductoId[]" placeholder="Producto" required></td>
            <td><input type="number" class="form-control" name="Cantidad[]" placeholder="Cantidad" required onchange="ActualizarSubtotal(this)"></td>
            <td><input type="number" class="form-control" name="PrecioUnitario[]" placeholder="Precio Unitario" step="0.01" required onchange="ActualizarSubtotal(this)"></td>
            <td><input type="number" class="form-control" name="Subtotal[]" placeholder="Subtotal" step="0.01" readonly></td>
            <td><button type="button" class="btn btn-danger" onclick="EliminarFilaDetalle(this)">Eliminar</button></td>
        `;

        tableBody.appendChild(row);
    }

    function EliminarFilaDetalle(button) {
        const row = button.closest('tr');
        row.remove();
        ActualizarTotal();
    }

    function ActualizarSubtotal(input) {
        const row = input.closest('tr');
        const cantidad = parseFloat(row.querySelector('input[name="Cantidad[]"]').value) || 0;
        const precioUnitario = parseFloat(row.querySelector('input[name="PrecioUnitario[]"]').value) || 0;
        const subtotal = cantidad * precioUnitario;

        row.querySelector('input[name="Subtotal[]"]').value = subtotal.toFixed(2);
        ActualizarTotal();
    }

    function ActualizarTotal() {
        const subtotales = document.querySelectorAll('input[name="Subtotal[]"]');
        let total = 0;

        subtotales.forEach(input => {
            total += parseFloat(input.value) || 0;
        });

        document.getElementById('Total').value = total.toFixed(2);
    }

    // Registrar una nueva compra
    function RegistrarCompra() {
        const detalles = [];

        const productoIds = document.getElementsByName('ProductoId[]');
        const cantidades = document.getElementsByName('Cantidad[]');
        const preciosUnitarios = document.getElementsByName('PrecioUnitario[]');

        for (let i = 0; i < productoIds.length; i++) {
            if (productoIds[i].value && cantidades[i].value && preciosUnitarios[i].value) {
                detalles.push({
                    ProductoId: productoIds[i].value,
                    Cantidad: parseFloat(cantidades[i].value),
                    PrecioUnitario: parseFloat(preciosUnitarios[i].value)
                });
            }
        }

        if (detalles.length === 0) {
            alertify.error('Debe agregar al menos un producto al detalle de la compra.');
            return;
        }

        $.ajax({
            url: '?c=Compras&a=GuardarCompra',
            method: 'POST',
            data: {
                Fecha: $('#Fecha').val(),
                Hora: $('#Hora').val(),
                Proveedor: 1, // Proveedor general con ID 1
                UsuarioId: $('#UsuarioId').val(),
                Total: $('#Total').val(),
                Observaciones: $('#Observaciones').val(),
                Detalles: JSON.stringify(detalles)
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alertify.success(response.msj);
                    $('#NuevaCompraModal').modal('hide'); // Cerrar el modal
                    $('#Tabla_Compras').DataTable().ajax.reload(); // Recargar la tabla
                    LimpiarFormularioCompra(); // Limpiar el formulario
                } else {
                    alertify.error('Error: ' + response.msj);
                }
            },
            error: function(error) {
                console.error('Error al registrar la compra:', error);
                alertify.error('Error al registrar la compra.');
            }
        });
    }

    function LimpiarFormularioCompra() {
        $('#NuevaCompraForm')[0].reset(); // Reiniciar el formulario
        $('#DetallesCompraTable tbody').empty(); // Vaciar las filas de la tabla de detalles
        $('#Total').val(''); // Reiniciar el total
    }
</script>

<?php
require 'views/Content/footer.php';
?>