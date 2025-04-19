<? 
    require 'views/Content/header.php'
?>

<!--- Sidemenu aqui va el menu -->
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
            <h1 class="card-header">USUARIOS INSOLVENTES</h1></center>
            <div class="card-body demo-vertical-spacing demo-only-element">


              <div class="card-body">
                <small class="text fw-semibold">En este modulo puede asociar multas a usuarios</small>
                <div class="demo-inline-spacing d-flex justify-content-end">
                  <button type="button" class="btn btn-primary" data-bs-toggle="modal"data-bs-target="#exLargeModal">Nuevo</button>
                </div>
              </div>

              <!-- Basic Bootstrap Table -->

              <div class="card">
                <h5 class="card-header">Listado de usuarios</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table" id="Tabla_Usuarios">
                    <thead>
                      <tr>
                        <th>Id</th>
                        <th>DPI</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Sector</th>
                        <th>Estado</th>
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

       



<script type="text/javascript" src="js/Custom/Multas.js"></script>

<?
    require 'views/Content/footer.php';
?>