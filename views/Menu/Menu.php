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
  <div class="container-fluid py-4">
    <div class="row g-4">
      <!-- Total Ventas -->
      <div class="col-12 col-md-6 col-lg-3">
        <div class="card shadow-sm border-0">
          <div class="card-body text-center">
            <div class="mb-2">
              <img src="assets/img/icons/unicons/wallet-info.png" alt="Ventas" style="width:48px;">
            </div>
            <h6 class="text-uppercase text-muted">Total Ventas</h6>
            <h3 class="fw-bold mb-0" id="totalVentas">-</h3>
            <small class="text-success"><i class="bx bx-up-arrow-alt"></i> Este mes</small>
          </div>
        </div>
      </div>
      <!-- Total Compras -->
      <div class="col-12 col-md-6 col-lg-3">
        <div class="card shadow-sm border-0">
          <div class="card-body text-center">
            <div class="mb-2">
              <img src="assets/img/icons/unicons/chart-success.png" alt="Compras" style="width:48px;">
            </div>
            <h6 class="text-uppercase text-muted">Total Compras</h6>
            <h3 class="fw-bold mb-0" id="totalCompras">-</h3>
            <small class="text-primary"><i class="bx bx-down-arrow-alt"></i> Este mes</small>
          </div>
        </div>
      </div>
      <!-- Usuarios Registrados -->
      <div class="col-12 col-md-6 col-lg-3">
        <div class="card shadow-sm border-0">
          <div class="card-body text-center">
            <div class="mb-2">
              <img src="assets/img/icons/unicons/user.png" alt="Usuarios" style="width:48px;">
            </div>
            <h6 class="text-uppercase text-muted">Usuarios Registrados</h6>
            <h3 class="fw-bold mb-0" id="totalUsuarios">-</h3>
            <small class="text-info"><i class="bx bx-user"></i> Activos</small>
          </div>
        </div>
      </div>
      <!-- Productos en Stock -->
      <div class="col-12 col-md-6 col-lg-3">
        <div class="card shadow-sm border-0">
          <div class="card-body text-center">
            <div class="mb-2">
              <img src="assets/img/icons/unicons/box.png" alt="Stock" style="width:48px;">
            </div>
            <h6 class="text-uppercase text-muted">Productos en Stock</h6>
            <h3 class="fw-bold mb-0" id="totalStock">-</h3>
            <small class="text-warning"><i class="bx bx-cube"></i> Disponibles</small>
          </div>
        </div>
      </div>
    </div>
    <!-- Aquí puedes agregar más widgets o gráficas -->
  </div>
</div>

<script>
window.onload = function () {
  cargarDashboard();
};

function cargarDashboard() {
  obtenerTotal('Menu&a=TotalVentas', '#totalVentas', 'Q. ');
  obtenerTotal('Menu&a=TotalCompras', '#totalCompras', 'Q. ');
  obtenerTotal('Menu&a=TotalUsuarios', '#totalUsuarios', '');
  obtenerTotal('Menu&a=TotalStock', '#totalStock', '');
}

function obtenerTotal(action, selector, prefix) {
  $.ajax({
    url: '?c=' + action,
    type: 'POST',
    dataType: 'json',
    success: function(data) {
      $(selector).text(prefix + (data[0] ?? '-'));
    },
    error: function() {
      $(selector).text('-');
    }
  });
}
</script>

<?
    require 'views/Content/footer.php';
?>

