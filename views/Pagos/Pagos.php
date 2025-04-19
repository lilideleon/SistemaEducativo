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
                           else if($_SESSION['TipoUsuario'] == 2)
                           {
                           // @session_start();
                               require 'views/Content/sidebar.php'; 
                                   print "<script>
                                       console.log($TipoUsuario);
                                   </script>";
                           }
                           else if($_SESSION['TipoUsuario'] == 3)
                           {
                               require 'views/Content/sidebar2.php'; 
                                   print "<script>
                                       console.log($TipoUsuario);
                                   </script>";
                           }
                           else if($_SESSION['TipoUsuario'] == 4)
                           {
                               require 'views/Content/sidebar3.php'; 
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
                  <a href="?c=Pagos&a=Nuevo" class="btn btn-primary">Nueva Venta</a>
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
                        <th>Ver</th>
                        <th>Imprimir</th>
                        <th>Eliminar</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <!-- Facturas ficticias -->
                      <tr>
                        <td>1</td>
                        <td>2025-04-16</td>
                        <td>10:30 AM</td>
                        <td>Juan Pérez</td>
                        <td>Q150.00</td>
                        <td>Pagada</td>
                        <td><button class="btn btn-info btn-sm" onclick="VerFactura(1, 'Juan Pérez', '2025-04-16', '10:30 AM', 150.00, 'Pagada')">Ver</button></td>
                        <td><button class="btn btn-secondary btn-sm" onclick="ImprimirFactura(1)">Imprimir</button></td>
                        <td><button class="btn btn-danger btn-sm" onclick="EliminarFactura(1)">Eliminar</button></td>
                      </tr>
                      <tr>
                        <td>2</td>
                        <td>2025-04-16</td>
                        <td>11:15 AM</td>
                        <td>María López</td>
                        <td>Q200.00</td>
                        <td>Pagada</td>
                        <td><button class="btn btn-info btn-sm" onclick="VerFactura(2, 'María López', '2025-04-16', '11:15 AM', 200.00, 'Pagada')">Ver</button></td>
                        <td><button class="btn btn-secondary btn-sm" onclick="ImprimirFactura(2)">Imprimir</button></td>
                        <td><button class="btn btn-danger btn-sm" onclick="EliminarFactura(2)">Eliminar</button></td>
                      </tr>
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
            </div>
            <div class="modal-footer">
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
    // Función para ver la factura
    function VerFactura(id, cliente, fecha, hora, total, estado) {
        document.getElementById('FacturaId').textContent = id;
        document.getElementById('FacturaCliente').textContent = cliente;
        document.getElementById('FacturaFecha').textContent = fecha;
        document.getElementById('FacturaHora').textContent = hora;
        document.getElementById('FacturaTotal').textContent = total.toFixed(2);
        document.getElementById('FacturaEstado').textContent = estado;

        // Mostrar el modal
        $('#VerFacturaModal').modal('show');
    }

    // Función para imprimir la factura (simulación)
    function ImprimirFactura(id) {
        alert(`Simulación: Generando PDF para la factura con ID ${id}.`);
        // Aquí puedes agregar la lógica para generar un PDF real
    }

    // Función para eliminar una factura (simulación)
    function EliminarFactura(id) {
        alert(`Factura con ID ${id} eliminada.`);
        // Aquí puedes agregar la lógica para eliminar la factura del servidor
    }
</script>

<script type="text/javascript" src="js/Custom/Ventas.js"></script>

<? 
    require 'views/Content/footer.php';
?>