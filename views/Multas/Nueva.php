<? 
    require 'views/Content/header.php';
    session_start();
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

    #TablaMeses tr {
        height: 15px;  /* O el alto que prefieras */
    }

    #TablaMeses td, #TablaMeses th {
        padding: 5px 10px;  /* Ajusta como prefieras */
    }


    #tablaDetalles tr {
        height: 15px;  /* O el alto que prefieras */
    }

    #tablaDetalles td, #tablaDetalles th {
        padding: 5px 10px;  /* Ajusta como prefieras */
    }

</style>


<div class="content-wrapper">
    <div class="row move-left move-down make-larger">
        <!-- Basic -->
        <div class="col-12 col-md-6 col-lg-12">
          <div class="card mb-4">
            <center>
            <h3 class="card-header">Nueva Multa</h3></center>
            <div class="card-body demo-vertical-spacing demo-only-element">
              <div class="panel-body"  id="formularioregistros">        
                

               <!-- Basic Bootstrap Table -->

               <div class="card">
                <h5 class="card-header">Listado de Multas</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table" id="Tabla_Multas">
                    <thead>
                      <tr>
                        <th>Id</th>
                        <th>Mes</th>
                        <th>Año</th>
                        <th>Motivo</th>
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


<script>
    window.onload = function () 
    { 
        Cargar();
    }

    //PAGINACION DE LA TABLA DE DATOS

function Cargar ()
{
    var objetoDataTables_personal = $('#Tabla_Multas').DataTable({
        "language": {
            "emptyTable":           "No hay datos disponibles en la tabla.",
            "info":                 "Del _START_ al _END_ de _TOTAL_ ",
            "infoEmpty":            "Mostrando 0 registros de un total de 0.",
            "infoFiltered":         "(filtrados de un total de _MAX_ registros)",
            "infoPostFix":          "(actualizados)",
            "lengthMenu":           "Mostrar _MENU_ registros",
            "loadingRecords":       "Cargando...",
            "processing":           "Procesando...",
            "search":               "Buscar:",
            "searchPlaceholder":    "Dato para buscar",
            "zeroRecords":          "No se han encontrado coincidencias.",
            "paginate": {
                "first":        "Primera",
                "last":         "Última",
                "next":         "Siguiente",
                "previous":     "Anterior"
            },
            "aria": {
                "sortAscending":    "Ordenación ascendente",
                "sortDescending":   "Ordenación descendente"
            }
        },
        "lengthMenu":               [[5,10,20,25,50,100], [5,10,20,25,50,100]],
        "iDisplayLength":           15,
        "bProcessing": true,
        "bServerSide": true,
        dom: 'Bfrtip',//definimos los elementos del control de la tabla
        buttons: [
                  'copyHtml5',
                  'excelHtml5',
                  'csvHtml5',
                  'pdf'
        ],
        "sAjaxSource": "?c=Multas&a=Tabla2"
      
    });

  

}
</script>

<?
    require 'views/Content/footer.php';

?>