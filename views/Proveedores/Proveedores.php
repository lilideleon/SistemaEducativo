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
            <? require 'views/Content/sidebar.php'; ?>
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
                        <h4><center>Registro de proveedores</center></h4>
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
                                                        <button type="button" data-toggle="modal" data-target="#AddUser" class="btn btn-primary btn-rounded waves-effect waves-light float-right"><span class="btn-label"><i class="fa fa-plus"></i></span>Nuevo</button>
                                                    </div>
                                                </div>
                                                    
                                                <center>
                                                <h4 class="m-t-0 header-title">Listado de los Proveedores existentes</h4>
                                                <p class="text-muted font-14 m-b-30">
                                                    En este apartado podra listar, registrar, modificar y eliminar 
                                                </p>

                                                </center>


                                                <table id="miTabla1" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                    <thead>
                                                    <tr>
                                                        <th width="50px">Id</th>
                                                        <th>Nombre</th>
                                                        <th>Documento</th>
                                                        <th>Correo</th>
                                                        <th>Telefono</th>
                                                        <th width="70px">Modificar</th>
                                                        <th width="70px">Desactivar</th>
                                                    </tr>
                                                    </thead>


                                                    <tbody>
                                                
                                                    </tbody>
                                                </table>
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


<!-- ============================================================== -->
<!-- End Right content here -->
<!-- ============================================================== -->

<footer class="footer">
    2022.
</footer>

</div>
<!-- END wrapper -->

<div class="modal fade none-border" id="AddUser">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Indique datos del proveedor:</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div>
            <br></br>
    

             <div class="form-group row">&nbsp;&nbsp;&nbsp;
                <label for="example-email-input" class="col-sm-2 col-form-label">NOMBRE</label>
                <div class="row col-sm-4">
                    <input class="form-control" type="text"   id="Nombre" placeholder="Nombre1">
                </div>

                <label for="example-email-input" class="col-sm-2 col-form-label">TELEFONO</label>
                <div class="row col-sm-4">
                    <input class="form-control" type="number"   id="Telefono" placeholder="31107506">
                </div>
            </div>

            <div class="form-group row">&nbsp;&nbsp;&nbsp;
                <label for="example-email-input" class="col-sm-2 col-form-label">CORREO</label>
                <div class="row col-sm-4">
                    <input class="form-control" type="text"   id="Correo" placeholder="a@gmail.com">
                </div>

                <label for="example-email-input" class="col-sm-2 col-form-label">DPI</label>
                <div class="row col-sm-4">
                    <input class="form-control" type="text"  id="Dpi" placeholder="316666">
                </div>
            </div>

            <hr></hr>


            <div class="modal-footer">
                <button type="button"  class="btn btn-danger waves-effect" data-dismiss="modal">Cerrar</button>
                <button type="button" onclick="GuardarDatos()" class="btn btn-primary save-event waves-effect waves-light">Guardar</button>
            </div>
        </div>
    </div>
</div>
</div>




<div class="modal fade none-border" id="EditUser">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Actualice los datos:</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div>
            <br></br>
        
            <div class="form-group row">&nbsp;&nbsp;&nbsp;
                
                 <label for="example-email-input" class="col-sm-2 col-form-label">CODIGO</label>
                <div class="row col-sm-4">
                    <input class="form-control" type="text"   id="MCodigo" placeholder="Nombre1" readonly="">
                </div>

            </div>

             <div class="form-group row">&nbsp;&nbsp;&nbsp;
                <label for="example-email-input" class="col-sm-2 col-form-label">NOMBRE</label>
                <div class="row col-sm-4">
                    <input class="form-control" type="text"   id="ENombre" placeholder="Nombre1">
                </div>

                <label for="example-email-input" class="col-sm-2 col-form-label">TELEFONO</label>
                <div class="row col-sm-4">
                    <input class="form-control" type="number"   id="ETelefono" placeholder="31107506">
                </div>
            </div>

            <div class="form-group row">&nbsp;&nbsp;&nbsp;
                <label for="example-email-input" class="col-sm-2 col-form-label">CORREO</label>
                <div class="row col-sm-4">
                    <input class="form-control" type="text"   id="ECorreo" placeholder="a@gmail.com">
                </div>

                <label for="example-email-input" class="col-sm-2 col-form-label">DPI</label>
                <div class="row col-sm-4">
                    <input class="form-control" type="text"  id="EDpi" placeholder="316666">
                </div>
            </div>


            <hr></hr>


            <div class="modal-footer">
                <button type="button"  class="btn btn-danger waves-effect" data-dismiss="modal">Cerrar</button>
                <button type="button" onclick="ActualizarUsuario()" class="btn btn-primary save-event waves-effect waves-light">Actualizar</button>
            </div>
        </div>
    </div>
</div></div>




<!-- MODAL PARA ELIMINAR -->

<div class="modal fade none-border" id="DeleteUser">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Desactivar proveedor</h5>
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

<script type="text/javascript" src="js/Custom/Proveedores.js"></script>

<?
    require 'views/Content/footer.php';
?>