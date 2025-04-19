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
                        <h4><center>Reporte de inversion en inventario</center></h4>
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
                                                 
                                                </div>

                                                <br>
                                                    
                                                <div class="panel-body table-responsive" id="listadoregistros">
                                                  <table id="miTabla1" class="table table-striped table-bordered table-condensed table-hover">
                                                    <thead>
                                                      <th>Id</th>
                                                      <th width="300">Nombre</th>
                                                      <th>Color</th>
                                                      <th>Talla</th>
                                                      <th>Existencia</th>
                                                      <th>PrecioC</th>
                                                      <th>PrecioV</th>
                                                    </thead>
                                                    <tbody>
                                                    </tbody> 
                                                    <tfoot>
                                                            <tr>
                                                                <th colspan="2" style="text-align:right">Total:</th>
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
    
window.onload = function () 
{ 
    listar();
}

function listar(){
    Cargar();
}



//PAGINACION DE LA TABLA DE DATOS

function Cargar ()
{


    //alert('hola');
    //alert(FechaInicio + " " + FechaFin);
    tabla=$('#miTabla1').dataTable({

footerCallback: function (row, data, start, end, display) {
var api = this.api();

// Remove the formatting to get integer data for summation
var intVal = function (i) {
    return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
};


// Total over all pages
total = api
    .column(3)
    .data()
    .reduce(function (a, b) {
        return intVal(a) + intVal(b);
    }, 0);

// Total over this page
pageTotal = api
    .column(3, { page: 'current' })
    .data()
    .reduce(function (a, b) {
        return intVal(a) + intVal(b);
    }, 0);

// Update footer
$(api.column(3).footer()).html('Q.' + pageTotal.toFixed(2) + ' ( Q.' + total.toFixed(2) + ' total)');
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
    url:'?c=Productos&a=Tabla',
    type: "post",
    data: {},
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
</script>