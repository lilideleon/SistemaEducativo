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
            <h1 class="card-header">Formulario de servicios </h1></center>
            <div class="card-body demo-vertical-spacing demo-only-element">


              <div class="card-body">
                <small class="text fw-semibold">En este modulo puede dar de alta , baja, Modificar servicios como tambien sus costos o descripciones </small>
                <div class="demo-inline-spacing d-flex justify-content-end">
                  <button type="button" class="btn btn-primary" data-bs-toggle="modal"data-bs-target="#Nuevo">Nuevo</button>
                </div>
              </div>

              <!-- Basic Bootstrap Table -->

              <div class="card">
                <h5 class="card-header">Listado de servicios</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table" id="Tabla_Servicios">
                    <thead>
                      <tr>
                        <th>Id</th>
                        <th>Servicio</th>
                        <th>Monto</th>
                        <th>Descripcion</th>
                        <th>M</th>
                        <th>E</th>
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




<div class="modal fade" id="Nuevo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" style="max-width: 30%;">
      <div class="modal-content">
        <div class="modal-header">
       
          <h3 class="modal-title" id="exampleModalLabel4">
            Formulario para ingreso de servicios
          </h3>
       
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body">

          <hr>
          <br>

          <div class="row g-2">
              <div class="col mb-0">
                <label for="nameExLarge" class="form-label">Servicio</label>
                <input type="text" id="Servicio" class="form-control" placeholder="EJ: dia de trabajo" />
              </div>
          </div>

          <div class="row g-2">
              <div class="col mb-0">
                <label for="nameExLarge" class="form-label">Cantidad</label>
                <input type="text" id="Cantidad" class="form-control" placeholder="EJ: 2" />
              </div>
          </div>

          <div class="row g-2">
              <div class="col mb-0">
                <label for="nameExLarge" class="form-label">Monto</label>
                <input type="text" id="Monto" class="form-control" placeholder="EJ: 100" />
              </div>
          </div>

          <div class="row g-2">
            <div class="col mb-0">
              <label for="emailExLarge" class="form-label">Descripcion</label>
              <textarea name="" id="Descripcion" cols="30" rows="10" class="form-control"></textarea>
            </div>
          </div>




        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            Cancelar
          </button>
          <button type="button" class="btn btn-primary" onclick="GuardarDatos()">Registrar</button>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- Modal de Actualización de Servicio -->
<div class="modal fade none-border" id="Actualizar">
    <div class="modal-dialog modal-lg" style="max-width: 35%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">FORMULARIO DE ACTUALIZACIÓN DE SERVICIOS</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            
            <!-- Cuerpo del Modal -->
            <div class="modal-body p-4">
                <!-- Código (solo lectura) -->
                <div class="mb-3">
                    <label for="Codigo" class="form-label">Codigo</label>
                    <input type="text" id="Id" class="form-control" readonly="" placeholder="EJ: S001" />
                </div>

                <!-- Servicio -->
                <div class="mb-3">
                    <label for="EServicio" class="form-label">Servicio</label>
                    <input type="text" id="EServicio" class="form-control" placeholder="EJ: Mantenimiento" />
                </div>

                <!-- Cantidad -->
                <div class="mb-3">
                    <label for="ECantidad" class="form-label">Cantidad</label>
                    <input type="number" id="ECantidad" class="form-control" placeholder="EJ: 2" />
                </div>

                <!-- Monto -->
                <div class="mb-3">
                    <label for="EMonto" class="form-label">Monto</label>
                    <input type="number" id="EMonto" class="form-control" placeholder="EJ: 100.50" />
                </div>

                <!-- Descripción -->
                <div class="mb-3">
                    <label for="EDescripcion" class="form-label">Descripción</label>
                    <textarea id="EDescripcion" cols="30" rows="5" class="form-control"></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Cerrar</button>
                <button type="button" onclick="ActualizarDatos()" class="btn btn-primary save-event waves-effect waves-light">Actualizar</button>
            </div>
        </div>
    </div>
</div>


<!-- MODAL PARA ELIMINAR -->

<div class="modal fade none-border" id="DeleteUser">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Eliminar servicio</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div>
                <hr></hr>
            <div class="form-group row">&nbsp;&nbsp;&nbsp;
                <label for="example-email-input" class="col-sm-2 col-form-label">Codigo:</label>
                <div class="row col-sm-4">
                    <input class="form-control" type="text"   id="ECodigo" placeholder="ECodigo" readonly="readonly">
                </div>
            </div>
            <hr></hr>
    

            <div class="modal-footer">
                <button type="button"  class="btn btn-secondary waves-effect" data-dismiss="modal">Cerrar</button>
                <button type="button" onclick="EliminarDatos()" class="btn btn-danger save-event waves-effect waves-light">Desactivar</button>
            </div>
        </div>
    </div>
</div></div>




<script type="text/javascript" src="js/Custom/Servicios.js"></script>

<?
    require 'views/Content/footer.php';
?>