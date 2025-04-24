<? 
    require 'views/Content/header.php'
?>

<?php 
                           
    if($_SESSION['TipoUsuario'] == '')
    {
        print "<script>
            window.location='?c=Login'; 
            </script>";
    }
    else if($_SESSION['TipoUsuario'] == 1)
    {
    // @session_start();
        require 'views/Content/sidebar.php'; 
            print "<script>
                console.log($TipoUsuario);
            </script>";
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
        <!-- Basic -->
        <div class="col-12 col-md-6 col-lg-11">
          <div class="card mb-4">
            <center>
            <h1 class="card-header">Listado de ventas del día</h1></center>
            <div class="card-body demo-vertical-spacing demo-only-element">

              <div class="card-body">
                <small class="text fw-semibold">En este módulo puede visualizar las ventas realizadas en el día, incluyendo las facturas generadas.</small>
                <div class="demo-inline-spacing d-flex justify-content-end">
                  <a href="?c=ventas&a=Nuevo" class="btn btn-primary" title="Atajo: Alt + 1">Nueva Venta</a>
                </div>
              </div>

              <!-- Basic Bootstrap Table -->

              <div class="card">
                <h5 class="card-header">Listado de facturas de ventas del día</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table" id="Tabla_Ventas">
                    <thead>
                      <tr>
                        <th>Id</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Accion</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      
                    </tbody>
                  </table>
                </div>
              </div>
              <!--/ Basic Bootstrap Table -->

            </div>
          </div>
        </div>
    </div>
</div>

<!-- MODAL PARA VER FACTURA -->
<div class="modal fade" id="VerFacturaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de la Factura</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Id:</strong> <span id="FacturaId"></span></p>
                <p><strong>Cliente:</strong> <span id="FacturaCliente"></span></p>
                <p><strong>Fecha:</strong> <span id="FacturaFecha"></span></p>
                <p><strong>Hora:</strong> <span id="FacturaHora"></span></p>
                <p><strong>Total:</strong> Q<span id="FacturaTotal"></span></p>
                <p><strong>Estado:</strong> <span id="FacturaEstado"></span></p>

                <h5>Detalle de Productos</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Producto ID</th>
                                <th>Nombre</th>
                                <th>Precio Venta</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="FacturaDetalle">
                            <!-- Los detalles de los productos se llenarán dinámicamente aquí -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="ImprimirTicket()">Imprimir</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PARA ELIMINAR -->
<div class="modal fade none-border" id="confirm-delete">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">¿Desea eliminar esta factura?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div>
                <hr>
                <div class="form-group row">&nbsp;&nbsp;&nbsp;
                    <label for="example-email-input" class="col-sm-2 col-form-label">Código:</label>
                    <div class="row col-sm-4">
                        <input class="form-control" type="text" id="ECodigo" placeholder="ECodigo" readonly="readonly">
                    </div>
                </div>
                <hr>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Cerrar</button>
                    <button type="button" onclick="EliminarDatos()" class="btn btn-danger save-event waves-effect waves-light">Confirmar</button>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    
    document.addEventListener('DOMContentLoaded', function() {
        Cargar();
    });

    
    function Cargar ()
    {
    var objetoDataTables_personal = $('#Tabla_Ventas').DataTable({
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
            "sAjaxSource": "?c=ventas&a=Tabla"
        });
    }

    function DatosFactura(idFactura) {
        $.ajax({
            url: '?c=ventas&a=ObtenerFacturaPorId',
            type: 'GET',
            data: { IdFactura: idFactura },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const factura = response.factura[0];
                    $('#FacturaId').text(factura.Id);
                    $('#FacturaCliente').text(factura.ClienteId);
                    $('#FacturaFecha').text(factura.Fecha);
                    $('#FacturaHora').text(factura.Hora);
                    $('#FacturaTotal').text(factura.Total);
                    $('#FacturaEstado').text(factura.Estado == 1 ? 'Activo' : 'Inactivo');

                    // Limpiar el cuerpo de la tabla de detalles
                    $('#FacturaDetalle').empty();

                    // Agregar los detalles de los productos
                    response.factura.forEach(function(detalle) {
                        $('#FacturaDetalle').append(`
                            <tr>
                                <td>${detalle.ProductoId}</td>
                                <td>${detalle.Nombre}</td>
                                <td>Q${detalle.PrecioVenta}</td>
                                <td>${detalle.Cantidad}</td>
                                <td>Q${detalle.Subtotal}</td>
                            </tr>
                        `);
                    });

                    // Mostrar el modal
                    $('#VerFacturaModal').modal('show');
                } else {
                    alert('Error: ' + response.msj);
                }
            },
            error: function(xhr, status, error) {
                alert('Error al realizar la solicitud: ' + error);
            }
        });
    }

    function AnularFactura(idFactura) {
        alertify.confirm(
            'Confirmación',
            '¿Está seguro de que desea anular esta factura?',
            function() {
                $.ajax({
                    url: '?c=ventas&a=AnularFactura',
                    type: 'POST',
                    data: { IdFactura: idFactura },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alertify.success(response.msj); // Mostrar mensaje de éxito con Alertify
                            $('#Tabla_Ventas').DataTable().ajax.reload(); // Recargar la tabla
                        } else {
                            alertify.error('Error: ' + response.msj); // Mostrar mensaje de error con Alertify
                        }
                    },
                    error: function(xhr, status, error) {
                        alertify.error('Error al realizar la solicitud: ' + error); // Mostrar mensaje de error con Alertify
                    }
                });
            },
            function() {
                alertify.error('Acción cancelada'); // Mostrar mensaje de cancelación con Alertify
            }
        );
    }

    function ImprimirTicket() {

        //obtener el id de la factura

        var idFactura = $('#FacturaId').text();

        $.ajax({
            url: '?c=ventas&a=ImprimirTicket',
            type: 'POST',
            data: { id: idFactura },
            dataType: 'json',
            success: function(response) {
                console.log(response);
                if (response.status === 'ok') {
                    window.print();
                } else {
                    alert('Error al imprimir el ticket: ' + response.error);
                }
            },
            error: function(xhr, status, error) {
                alert('Error al realizar la solicitud: ' + error);
            }
        });
    }
</script>

<? 
    require 'views/Content/footer.php';
?>