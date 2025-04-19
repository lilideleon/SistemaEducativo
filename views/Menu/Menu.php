<?php 
   include 'views/Content/header.php';
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
        require 'views/Content/sidebar.php'; 
            print "<script>
                console.log($TipoUsuario);
            </script>";
    }
    else if($_SESSION['TipoUsuario'] == 2)
    // @session_start();
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
          <h3 class="card-header">Dashboard</h3>
        </center>
        <div class="card-body demo-vertical-spacing demo-only-element">
          <div class="row">
            <div class="col-lg-3 col-md-12 col-6 mb-4">
              <div class="card">
                <div class="card-body">
                  <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0">
                      <img
                        src="assets/img/icons/unicons/chart-success.png"
                        alt="chart success"
                        class="rounded"
                      />
                    </div>
                    <div class="dropdown">
                      <button
                        class="btn p-0"
                        type="button"
                        id="cardOpt3"
                        data-bs-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false"
                      >
                        <i class="bx bx-dots-vertical-rounded"></i>
                      </button>
                      <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                        <a class="dropdown-item" href="?c=Usuarios">Ver todos</a>
                      </div>
                    </div>
                  </div>
                  <span class="fw-semibold d-block mb-1">Usuarios</span>
                  <h3 class="card-title mb-2" id="totalUsuarios"> </h3>
                  <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i>total de usuarios</small>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-md-12 col-6 mb-4">
              <div class="card">
                <div class="card-body">
                  <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0">
                      <img
                        src="assets/img/icons/unicons/wallet-info.png"
                        alt="Credit Card"
                        class="rounded"
                      />
                    </div>
                    <div class="dropdown">
                      <button
                        class="btn p-0"
                        type="button"
                        id="cardOpt6"
                        data-bs-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false"
                      >
                        <i class="bx bx-dots-vertical-rounded"></i>
                      </button>
                      <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt6">
                        <a class="dropdown-item" href="?c=Pagos">Ver mas detalles</a>
                      </div>
                    </div>
                  </div>
                  <span class="fw-semibold d-block mb-1">Ventas</span>
                  <h3 class="card-title text-nowrap mb-1" id="totalpagosmensual"></h3>
                  <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> total de ventas </small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>



<script>
  window.onload = function () 
  { 
      TotalUsuarios();
      TotalPagosMensual ();
  }


  //PAGINACION DE LA TABLA DE DATOS

  function TotalUsuarios ()
  {
    $.ajax({
        url: '?c=Menu&a=TotalUsuarios',
        type: 'POST',
        dataType: 'json',
        success: function(data) {
            console.log(data); 
            $('#totalUsuarios').text(data[0]);
        },
        error: function(error) {
            console.log("Hubo un error:", error);
        }
    });
  }


  function TotalPagosMensual ()
  {
    $.ajax({
        url: '?c=Menu&a=TotalPagosMensual',
        type: 'POST',
        dataType: 'json',
        success: function(data) {
            console.log(data); 
            $('#totalpagosmensual').text('Q. ' + data[0]);
        },
        error: function(error) {
            console.log("Hubo un error:", error);
        }
    });
  }
</script>

<?
    require 'views/Content/footer.php';
?>

