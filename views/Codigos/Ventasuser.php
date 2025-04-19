<? 
    require 'views/Content/header.php'
?>
<body class="fixed-left">
<!-- Begin page -->
<div id="wrapper">

    <?
        include 'views/Content/toopbar.php';
    ?>

    <!-- ========== Left Sidebar Start ========== -->
    <div class="left side-menu">
        <div class="sidebar-inner slimscrollleft">

            <!--- Sidemenu aqui va el menu -->
                <? 
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
                   else if($_SESSION['TipoUsuario'] == 2)
                   {
                       require 'views/Content/sidebar2.php'; 
                            print "<script>
                             console.log($TipoUsuario);
                          </script>";
                   }
                ?>
            <!-- Sidebar -->
            <div class="clearfix"></div>
        </div>

    </div>
    <!-- Left Sidebar End -->

    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="content-page">
        <!-- Start content -->
        <div class="content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-xl-12">
                        <div class="page-title-box">
                        <h4><center>Reporte de ventas por Año/Mes/Semana/Dia por usuario</center></h4>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
                <!-- end row -->
                <p></p>
                
                <div class="row">
                <div class="col-xl-12">
                    <div class="page-title-box">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="page-title-box">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card-box table-responsive">

                                                <div class="box-header with-border">
                                                   <div class="row">
                                                    Usuario:
                                                   <div class="col-xl-3 col-md-2">
                                                        <select class="select-box" id="Usuario" name="Usuario" required="">
                                                            <option></option>
                                                        </select>
                                                    </div>

                                                    FECHA INICIO:
                                                    <div class="col-xl-3 col-md-2">
                                                    <input class="form-control" type="date" name="FechaInicio" id="FechaInicio">
                                                    </div>
                                                     FECHA FIN:
                                                    <div class="col-xl-3 col-md-2">
                                                    <input type="date" name="FechaFin" id="FechaFin" class="form-control">
                                                    </div>
                                                    
                                                    &nbsp;&nbsp;
                                                    <button class="btn btn-success" onclick="listar()"><i class="fa fa-eye"></i> Mostrar</button>
                                                    </div>
                                                  <div class="box-tools pull-right">
                                                    
                                                  </div>
                                                </div>

                                                <br>
                                                    
                                                <div class="panel-body table-responsive" id="listadoregistros">
                                                  <table id="tbllistado2" class="table table-striped table-bordered table-condensed table-hover">
                                                    <thead>
                                                      <th>Factura</th>
                                                      <th>Fecha</th>
                                                      <th>Cant</th>
                                                      <th>Nombre</th>
                                                      <th>Costo</th>
                                                      <th>Venta</th>
                                                      <th width="200px">Ganancia</th>
                                                    </thead>
                                                    <tbody>
                                                    </tbody> 
                                                    <tfoot>
                                                            <tr>
                                                                <th colspan="6" style="text-align:right">Total:</th>
                                                                <th></th>
                                                            </tr>
                                                        </tfoot>
                                                  </table>
                                                  </table>
                                                </div>


                                            </div>
                                        </div>
                                    </div> <!-- end row -->
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->    
                    </div>
                </div>
            </div>
            <!-- end row -->

            </div> <!-- container -->

        </div> <!-- content -->
    </div>
    <!-- End content-page -->



</div>

<?
    require 'views/Content/footer.php';
?>


<script type="text/javascript">



$('#Usuario').chosen({allow_single_deselect:true, width:"200px", search_contains: true});
        chosen_ajaxify('Usuario', '?c=Ventasuser&a=buscarUsuario&keyword=');





    //funcion listar
function listar(){

    var User = $('#Usuario').val ();

    if (User == '')
    {
        alertify.set('notifier','position','top-right');
        alertify.error('<font color="#fffff"><i class="fa fa-times"></i> INDIQUE EL USUARIO </font> ');
    }
    else
    {
        var FechaInicio;
        var FechaFin;
        FechaInicio = $('#FechaInicio').val();
        FechaFin = $('#FechaFin').val();

    // alert (FechaInicio + " " + FechaFin);

        if (FechaInicio == '' || FechaFin =='')
        {
            alertify.set('notifier','position','top-right');
            alertify.error('<font color="#fffff"><i class="fa fa-times"></i> ELIJA EL RANGO DE FECHAS </font> ');
        }
        else
        {
            //alert(FechaInicio + " " + FechaFin);
            tabla=$('#tbllistado2').dataTable({

                footerCallback: function (row, data, start, end, display) {
                var api = this.api();
    
                // Remove the formatting to get integer data for summation
                var intVal = function (i) {
                    return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                };

    
                // Total over all pages
                total = api
                    .column(6)
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
    
                // Total over this page
                pageTotal = api
                    .column(6, { page: 'current' })
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
    
                // Update footer
                $(api.column(6).footer()).html('Q.' + pageTotal.toFixed(2) + ' ( Q.' + total.toFixed(2) + ' total)');
            },

                "aProcessing": true,//activamos el procedimiento del datatable
                "aServerSide": true,//paginacion y filrado realizados por el server
                dom: 'Bfrtip',//definimos los elementos del control de la tabla
                buttons: [
                        'copyHtml5',
                        'excelHtml5',
                        'csvHtml5',
                        'pdf'
                ],
                "ajax":
                {
                    url:'?c=Reportes&a=ConsultaporUsuario',
                    type: "post",
                    data: {'FechaInicio': FechaInicio,'FechaFin':FechaFin, 'Usuario':User},
                    dataType : "json",
                    error:function(e){
                        console.log(e.responseText);
                    }
                },
                "bDestroy":true,
                "iDisplayLength":15,//paginacion
                "order":[[0,"desc"]]//ordenar (columna, orden)
            }).DataTable();
        }
    }
}

</script>