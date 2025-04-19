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
            <h1 class="card-header">Notificaciones para la aplicacion</h1></center>
            <div class="card-body demo-vertical-spacing demo-only-element">


              <div class="card-body">
                <small class="text fw-semibold">En este modulo puede hacer envio de notificaciones al fontanero </small>
                <div class="demo-inline-spacing d-flex justify-content-end">
                  <button type="button" class="btn btn-primary" data-bs-toggle="modal"data-bs-target="#Nuevo">Nuevo</button>
                </div>
              </div>

              <!-- Basic Bootstrap Table -->

              <div class="card">
                <h5 class="card-header">Notificaciones de trabajos al fontanero</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table" id="Tabla_Notificaciones">
                    <thead>
                      <tr>
                        <th>Id</th>
                        <th>Titulo</th>
                        <th>Importancia</th>
                        <th>Fecha</th>
                        <th>Estado</th>
 
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

<!-- Modal para agregar -->

<div class="modal fade" id="Nuevo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" >
      <div class="modal-content">
        <div class="modal-header">
       
          <h3 class="modal-title" id="exampleModalLabel4">
            Formulario para ingreso de notificaciones
          </h3>
       
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body">

  
          <br>

          <div class="row g-2">
              <div class="col mb-0">
                <label for="nameExLarge" class="form-label">Titulo</label>
                <input type="text" id="Titulo" class="form-control" placeholder="EJ: dia de trabajo" />
              </div>
          </div>

          <div class="row g-2">
              <div class="col mb-0">
                <label for="nameExLarge" class="form-label">Mensaje</label>
                <textarea name="" id="Mensaje" cols="30" rows="10" class="form-control"></textarea>
              </div>
          </div>

          <div class="row g-2">
              <div class="col mb-0">
                <label for="nameExLarge" class="form-label">Importancia</label>
                <select name="Importancia" id="Importancia" class="form-control">
                  <option value="1">ALTA</option>
                  <option value="2">MEDIA</option>
                  <option value="3">BAJA</option>
                </select>
              </div>
          </div>
          <br>
          <div class="row g-2">
            <div class="col mb-0">
              <label for="emailExLarge" class="form-label">Fontanero:_ </label>
              <select name="Fontanero" id="Fontanero" class="form-control">
                  <option value="1"></option>
                </select>
            </div>
          </div>
          <br>

          <div class="row g-2">
            <div class="col mb-0">
              <label for="emailExLarge" class="form-label">Fecha</label>
              <input type="date" class="form-control" id="Fecha">
            </div>
          </div>

          <div class="row g-2">
            <div class="col mb-0">
              <label for="emailExLarge" class="form-label">Estado</label>
              <select name="Estado" id="Estado" class="form-control">
                  <option value="1">NOTIFICAR</option>
                  <option value="2">INICIADO</option>
                  <option value="3">FINALIZADO</option>
                  <option value="4">INACTIVO</option>
                </select>
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
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">FORMULARIO DE ACTUALIZACIÓN DE DATOS</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            
            <!-- Cuerpo del Modal -->
            <div class="modal-body p-4">
                <!-- Código (solo lectura) -->
                <div class="mb-3">
                    <label for="Codigo" class="form-label">Codigo</label>
                    <input type="text" id="EId" class="form-control" readonly="" placeholder="EJ: S001" />
                </div>

                <div class="row g-2">
              <div class="col mb-0">
                <label for="nameExLarge" class="form-label">Titulo</label>
                <input type="text" id="ETitulo" class="form-control" placeholder="EJ: dia de trabajo" />
              </div>
          </div>

          <div class="row g-2">
              <div class="col mb-0">
                <label for="nameExLarge" class="form-label">Mensaje</label>
                <textarea name="" id="EMensaje" cols="30" rows="10" class="form-control"></textarea>
              </div>
          </div>

          <div class="row g-2">
              <div class="col mb-0">
                <label for="nameExLarge" class="form-label">Importancia</label>
                <select name="Importancia" id="EImportancia" class="form-control">
                  <option value="1">ALTA</option>
                  <option value="2">MEDIA</option>
                  <option value="3">BAJA</option>
                </select>
              </div>
          </div>

          <br>
          <div class="row g-2">
            <div class="col mb-0">
              <label for="emailExLarge" class="form-label">Fontanero:_ </label>
              <select name="Fontanero" id="EFontanero" class="form-control">
                  <option value="1"></option>
                </select>
            </div>
          </div>
          <br>

          <div class="row g-2">
            <div class="col mb-0">
              <label for="emailExLarge" class="form-label">Fecha</label>
              <input type="date" class="form-control" id="EFecha">
            </div>
          </div>

          <div class="row g-2">
            <div class="col mb-0">
              <label for="emailExLarge" class="form-label">Estado</label>
              <select name="Estado" id="EEstado" class="form-control">
                  <option value="1">NOTIFICAR</option>
                  <option value="2">INICIADO</option>
                  <option value="3">FINALIZADO</option>
                  <option value="4">INACTIVO</option>
                </select>
            </div>
          </div>

               

            <div class="modal-footer">
                <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Cerrar</button>
                <button type="button" onclick="ActualizarDatos()" class="btn btn-primary save-event waves-effect waves-light">Actualizar</button>
            </div>
        </div>
    </div>
</div></div>


<!-- MODAL PARA ELIMINAR -->

<div class="modal fade none-border" id="DeleteNotification">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Eliminar notificacion</h5>
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

<script type="text/javascript" src="js/Custom/Notificaciones.js"></script>


<?
    require 'views/Content/footer.php';
?>