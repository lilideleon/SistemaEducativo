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
        <!-- Reporte de Costos -->
        <div class="col-12 col-md-6 col-lg-11">
            <div class="card mb-4">
                <center>
                    <h3 class="card-header">Reporte de Costos y Ganancias</h3>
                </center>
                <div class="card-body demo-vertical-spacing demo-only-element">
                    <div class="row">
                        <!-- Filtros -->
                        <div class="col-lg-12 col-md-12 order-1">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="Producto">Producto:</label>
                                    <select name="Producto" id="Producto" class="form-control chosen-select">
                                        <option></option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="FechaInicio">Fecha de Inicio:</label>
                                    <input type="date" id="FechaInicio" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label for="FechaFin">Fecha de Fin:</label>
                                    <input type="date" id="FechaFin" class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <label for="">&nbsp;</label> <!-- Espacio en blanco para mantener el diseño -->
                                    <button id="cargarDataTable" onclick="validaciones()" class="btn btn-primary btn-block">Mostrar</button>
                                </div>
                            </div>
                        </div>

                        <br>

                        <!-- Tabla de Reporte -->
                        <div class="col-md-12 order-2">
                            <br>
                            <div class="table-responsive text-nowrap">
                                <table class="table" id="Tabla_Costos">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Producto</th>
                                            <th>Fecha</th>
                                            <th>Precio Costo</th>
                                            <th>Precio Venta</th>
                                            <th>Ganancia</th>
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
</div>

<script>
  window.onload = function () 
  { 
     Cargar ();
     cargarProductos();
     // TotalPagosMensual ();

    // validaciones();
  }

    function Cargar ()
    {
        $('#Servicio').chosen({allow_single_deselect:true, width:"300px", search_contains: true});
            chosen_ajaxify('Servicio', '?c=Pagos&a=getServicio&keyword=');
    }

    function cargarProductos() {
        $('#Producto').chosen({ allow_single_deselect: true, width: "300px", search_contains: true });
        chosen_ajaxify('Producto', '?c=Reportes&a=getProductos&keyword=');
    }

  function validaciones ()
  {
    var FechaInicio = $('#FechaInicio').val ();
    var FechaFin = $('#FechaFin').val();
    var Servicio = $('#Servicio').val();
    var Producto = $('#Producto').val();

    if ( FechaInicio == '' || FechaFin == '' || (Servicio == '' && Producto == ''))
    {
        alert("NO PUEDE DEJAR VACIAS LAS FECHAS O SELECCIONE EL SERVICIO O PRODUCTO");
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
            if ($.fn.DataTable.isDataTable('#Tabla_Costos')) {
                $('#Tabla_Costos').DataTable().destroy();
            }
            if (Servicio != '') {
                cargarDatos(FechaInicio,FechaFin,Servicio);
            }
            if (Producto != '') {
                cargarDatosCostos(FechaInicio, FechaFin, Producto);
            }
        }
    }

  }


  function cargarDatos(FechaInicio, FechaFin,Servicio) {


    $.ajax({
            url: '?c=Reportes&a=Tabla2',
            type: 'post',
            data: {
                'fechaInicio':FechaInicio,'fechaFin':FechaFin,'Servicio':Servicio
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
                        '<td>' + pago.sub_total + '</td>' +
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

function cargarDatosCostos(FechaInicio, FechaFin, Producto) {
        $.ajax({
            url: '?c=Reportes&a=TablaCostos',
            type: 'post',
            data: {
                'fechaInicio': FechaInicio,
                'fechaFin': FechaFin,
                'Producto': Producto
            },
            dataType: 'json',
            success: function (data) {
                $('#Tabla_Costos').DataTable().clear().destroy();

                $.each(data, function (index, item) {
                    const ganancia = item.PrecioVenta - item.PrecioCosto;
                    $('#Tabla_Costos tbody').append(
                        '<tr>' +
                        '<td>' + item.Id + '</td>' +
                        '<td>' + item.NombreProducto + '</td>' +
                        '<td>' + item.Fecha + '</td>' +
                        '<td>Q' + item.PrecioCosto.toFixed(2) + '</td>' +
                        '<td>Q' + item.PrecioVenta.toFixed(2) + '</td>' +
                        '<td>Q' + ganancia.toFixed(2) + '</td>' +
                        '</tr>'
                    );
                });

                $('#Tabla_Costos').DataTable({
                    "lengthMenu": [[10, 25, 50, 100, 500, 1000], [10, 25, 50, 100, 500, 1000]],
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
            error: function (jqXHR, textStatus, error) {
                alert('Error: ' + error);
                console.log(error);
            }
        });
    }

</script>

<?
    require 'views/Content/footer.php';
?>

