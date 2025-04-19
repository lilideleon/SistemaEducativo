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
                <h3 class="card-header">Reporte de ventas</h3></center>
                <div class="card-body demo-vertical-spacing demo-only-element">

                
                    <div class="row">
                    
                        
                        <div class="col-lg-8 col-md-8 order-1">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="FechaInicio">Fecha de Inicio:</label>
                                    <input type="date" id="FechaInicio" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label for="FechaFin">Fecha de Fin:</label>
                                    <input type="date" id="FechaFin" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label for="">&nbsp;</label> <!-- Espacio en blanco para mantener el diseño -->
                                    <button id="cargarDataTable" onclick="validaciones()" class="btn btn-primary btn-block">Mostrar</button>
                                </div>
                            </div>
                        </div>

                        <br>


                        <div class="col-md-12 order-2">
                            <br>
                        <div class="table-responsive text-nowrap">
                            <table class="table" id="Tabla_Pagos">
                                <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Asociado</th>
                                    <th>Fecha</th>
                                    <th>Mes pago</th>
                                    <th>Total</th>
                                </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                
                                </tbody>
                            </table>
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
     // TotalUsuarios();
     // TotalPagosMensual ();

    // validaciones();
  }

  function validaciones ()
  {
    var FechaInicio = $('#FechaInicio').val ();
    var FechaFin = $('#FechaFin').val();

    if (FechaInicio == '' || FechaFin == '')
    {
        alert("NO PUEDE DEJAR VACIAS LAS FECHAS");
    }
    else
    {
        if (FechaFin < FechaInicio)
        {
            alert ("LA FECHA DE IINICIO DEBE SER MENOR A LA FECHA FIN");
        }
        else
        {
            if ($.fn.DataTable.isDataTable('#Tabla_pagos')) {
                $('#Tabla_pagos').DataTable().destroy();
            }
            cargarDatos(FechaInicio,FechaFin);
        }
    }

  }


  function cargarDatos(FechaInicio, FechaFin) {


    $.ajax({
            url: '?c=Reportes&a=Tabla',
            type: 'post',
            data: {
                'fechaInicio':FechaInicio,'fechaFin':FechaFin
            },
            dataType: 'json',
            success: function(data) 
            {
                //console.log (data);
                $('#Tabla_Pagos').DataTable().clear().destroy();

        
                $.each(data, function(index, pago) {
                    $('#Tabla_Pagos tbody').append(
                        '<tr>' +
                        '<td>' + pago.Id + '</td>' +
                        '<td>' + pago.NombreCompleto + '</td>' +
                        '<td>' + pago.Fecha + '</td>' +
                        '<td>' + pago.Mes + '</td>' +
                        '<td>' + pago.Total + '</td>' +
                        '</tr>'
                    );
                });

    
                $('#Tabla_Pagos').DataTable({
                    "lengthMenu": [[10, 25, 50, 100,500,1000], [10, 25, 50,100,500,1000]], 
                    "pageLength": 20,
                    dom: 'Blfrtip',
                    buttons: [
                  'copyHtml5',
                  'excelHtml5',
                  'csvHtml5',
                  'pdf'
        ],
                });
            },
            error: function(jqXHR, textStatus, error) 
            {
                alert('Error'+error);
                console.log (error);
            }
        });
}





</script>

<?
    require 'views/Content/footer.php';
?>

