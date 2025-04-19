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
                        <h4><center>Listado de codigos de barra</center></h4>
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
                                                <div class="row">
                                                    <div class="col">
                                                        <button id="crearpdf" onclick="printHTML()" class="btn btn-primary btn-rounded waves-effect waves-light float-right"><span class="btn-label"><i class="fa fa-plus"></i></span>IMPRIMIR</button>
                                                    </div>
                                                </div>
                                                <br>
                                                <div id="contenedor">
                                                <table id="tablejson" class="table" style="text-align:center">
                                                    <tr>
                                                        <th></th>
                                                        <th></th>
                                                    </tr>
                                                </table> 
                                                </div>
                                                <div id="print">
                                                    
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

<script type="text/javascript" src="js/JsBarcode.all.min.js"></script> 


<?
    require 'views/Content/footer.php';
?>
<script src="js/jquery.PrintArea.js"></script>

<script type="text/javascript">
 window.onload = function () 
{ 
    generarbarcode();
}

    function generarbarcode()
    {
        $.ajax({
            url: '?c=Codigos&a=Consulta',
            type: 'post',
            dataType: 'json',
            success:function(response) { 
            console.log(response);
            var len = response.length;
            var txt = "";
            //console.log(response.length);
            if(len > 0){
                for(var i=0;i<len;i++){
                    if(response[i].id && response[i].codigobarra){

                         

                        txt += "<tr><td align='center'>"+response[i].id+"</td>"+"<td>"+response[i].codigobarra+"</td><td>"+response[i].nombre+"</td><td>"+response[i].talla+'</td><td><svg id="barcode'+i+'"></svg> +</td></tr>';

                        
                       //  $("#print").show();
                    }
                }
                if(txt != ""){
                    $("#tablejson").empty();
                    $("#tablejson").append("<thead><th>Id</th><th>CodigoBarra</th><th>Nombre</th><th>Talla</th><th>Codigo</th></thead>");
                    $("#tablejson").append(txt).removeClass("hidden");
                }

                for(var i=0;i<len;i++){
                    JsBarcode("#barcode"+i,response[i].codigobarra);
                }
            }
                //Generar(response.CodigoBarra);
            } //success function
            ,error: function(jqXHR, textStatus, error) 
            {
                alert('Error'+error);
            }

        })

        //imprimir ();
    }


    function printHTML() { 
  
            $("div#contenedor").printArea();
    }


    

</script>