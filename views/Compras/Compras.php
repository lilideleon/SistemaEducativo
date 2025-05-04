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

<!-- MODAL PARA DETALLE DE COMPRA -->
<!-- Modal -->
<div class="modal fade" id="DetalleCompraModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detalle de la Compra</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="infoCompraCabecera"></div>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Producto</th>
              <th>Cantidad</th>
              <th>Subtotal</th>
            </tr>
          </thead>
          <tbody id="DetalleCompraBody">
            <!-- Aquí se llenarán los detalles dinámicamente -->
          </tbody>
        </table>
      </div>
       <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="ImprimirDetalleCompra()">Imprimir</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
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
            <td>
                <select class="form-control chosen-select" name="ProductoId[]" required onchange="actualizarPrecioCosto(this)">
                    <option value="">Seleccione un producto</option>
                </select>
            </td>
            <td><input type="number" class="form-control" name="Cantidad[]" placeholder="Cantidad" required onchange="ActualizarSubtotal(this)"></td>
            <td><input type="number" class="form-control" name="PrecioUnitario[]" placeholder="Precio Unitario" step="0.01" required onchange="ActualizarSubtotal(this)"></td>
            <td><input type="number" class="form-control" name="Subtotal[]" placeholder="Subtotal" step="0.01" readonly></td>
            <td><button type="button" class="btn btn-danger" onclick="EliminarFilaDetalle(this)">Eliminar</button></td>
        `;

        tableBody.appendChild(row);

        // Initialize Chosen plugin for the new select element
        $(row).find('.chosen-select').chosen();

        // Fetch product options dynamically
        fetch('?c=Compras&a=ObtenerProducto', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'nombreProducto='
        })
        .then(response => response.json())
        .then(data => {
            const select = row.querySelector('select[name="ProductoId[]"]');
            data.forEach(producto => {
                const option = document.createElement('option');
                option.value = producto.idProducto;
                option.textContent = producto.Nombre;
                option.dataset.precioCosto = producto.PrecioCosto;
                select.appendChild(option);
            });
            $(select).trigger('chosen:updated');
        })
        .catch(error => console.error('Error fetching products:', error));
    }

    function actualizarPrecioCosto(select) {
        const row = select.closest('tr');
        const selectedOption = select.options[select.selectedIndex];
        const precioCosto = selectedOption.dataset.precioCosto;
        const precioUnitarioInput = row.querySelector('input[name="PrecioUnitario[]"]');
        
        if (precioCosto) {
            precioUnitarioInput.value = precioCosto;
            ActualizarSubtotal(precioUnitarioInput);
        }
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
                Proveedor: 1, 
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

   
    // Mostrar el detalle de la compra en un modal

    function VerDetallesCompra(compraId) {
        $.ajax({
            url: '?c=Compras&a=DetalleCompra',
            type: 'POST',
            data: { compraId: compraId },
            dataType: 'json',
            success: function (datos) {
                console.log("Datos:", datos);

                $('#DetalleCompraModal').modal('show');
                $('#DetalleCompraBody').empty();

                if (datos.length === 0) {
                    $('#infoCompraCabecera').html('No se encontraron datos.');
                    return;
                }

                // Tomamos la primera fila para la cabecera
                const cabecera = datos[0];
                let info = `<strong>ID:</strong> ${cabecera.Id}<br>`;
                info += `<strong>Fecha:</strong> ${cabecera.Fecha}<br>`;
                info += `<strong>Hora:</strong> ${cabecera.Hora}<br>`;

                // si elproveedor es uno mostrarlo como "General"
                if (cabecera.Proveedor === "1") {
                    info += `<strong>Proveedor:</strong> General<br>`;
                } else {
                    info += `<strong>Proveedor:</strong> ${cabecera.Proveedor}<br>`;
                }
               

                
                info += `<strong>Usuario:</strong> ${cabecera.UsuarioId}<br>`;
                info += `<strong>Total:</strong> Q${parseFloat(cabecera.Total).toFixed(2)}<br>`;
                if (cabecera.Observaciones)
                    info += `<strong>Observaciones:</strong> ${cabecera.Observaciones}<br>`;

                $('#infoCompraCabecera').html(info);

                // Llenar tabla con cada producto
                datos.forEach(function (item) {
                    $('#DetalleCompraBody').append(`
                        <tr>
                            <td>${item.Nombre}</td>
                            <td>${item.Cantidad}</td>
                            <td>Q ${parseFloat(item.Subtotal).toFixed(2)}</td>
                        </tr>
                    `);
                });
            },
            error: function (xhr, status, error) {
                console.error("Error:", error);
                alert('Error al obtener los detalles de la compra.');
            }
        });
    }

    // Función para imprimir el contenido del modal DetalleCompraModal
    function ImprimirDetalleCompra() {
        var printContents = document.querySelector('#DetalleCompraModal .modal-content').innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        location.reload(); // Recargar para restaurar eventos y estado
    }


    // Función para eliminar una compra

    function EliminarCompra(compraId) {

        alertify.confirm('Eliminar Compra', '¿Está seguro de que desea eliminar esta compra?', function() {
            $.ajax({
                url: '?c=Compras&a=AnularCompra',
                type: 'POST',
                data: { CompraId: compraId },
                success: function(response) {
                    if (response.success) {
                        alertify.success(response.msj);
                        $('#Tabla_Compras').DataTable().ajax.reload(); // Recargar la tabla
                    } else {
                        alertify.error('Error: ' + response.msj);
                    }
                },
                error: function(error) {
                    console.error('Error al eliminar la compra:', error);
                    alertify.error('Error al eliminar la compra.');
                }
            });
        }, function() {
            alertify.error('Cancelado');
        });
    }

    // funcion para procesar la compra

    function ProcesarCompra(compraId) {
        alertify.confirm('Procesar Compra', '¿Está seguro de que desea procesar esta compra?', function() {
            $.ajax({
                url: '?c=Compras&a=ProcesarCompra',
                type: 'POST',
                data: { CompraId: compraId },
                success: function(response) {
                    if (response.success) {
                        alertify.success(response.msj);
                        $('#Tabla_Compras').DataTable().ajax.reload(); // Recargar la tabla
                    } else {
                        alertify.error('Error: ' + response.msj);
                    }
                },
                error: function(error) {
                    console.error('Error al procesar la compra:', error);
                    alertify.error('Error al procesar la compra.');
                }
            });
        }, function() {
            alertify.error('Cancelado');
        });
    }

</script>

<?php
require 'views/Content/footer.php';
?>