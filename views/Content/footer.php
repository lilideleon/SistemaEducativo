        <!-- MODAL PARA ELIMINAR -->
        <div class="modal fade none-border" id="Cerrar">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <center><h5 class="modal-title">DESEA CERRAR SESION</h5></center>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>
                    <div>
                        <br>
                        <div class="col">
                            <div class="col-8"><h5>Confirme si desea salir ?</h5></div>
                        </div>
                        <hr>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">CANCELAR</button>
                            <button type="button" onclick="CerrarSesion()" class="btn btn-danger save-event waves-effect waves-light">CERRAR</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            function CerrarSesion() {   
                $.ajax({
                    url: '?c=Login&a=Destruir',
                    type: 'post',
                    dataType: 'json',
                    success: function(data) {
                        window.location = '?c=Login';
                    },
                    error: function(jqXHR, textStatus, error) {
                        alert('Error' + error);
                    }
                });
            }
        </script>

        <script>
            var resizefunc = [];
        </script>

        <!-- jQuery -->
        <script src="res/assets/js/jquery.min.js"></script>
        <script src="res/assets/js/bootstrap.bundle.min.js"></script>
        <script src="res/assets/js/detect.js"></script>
        <script src="res/assets/js/fastclick.js"></script>
        <script src="res/assets/js/jquery.blockUI.js"></script>
        <script src="res/assets/js/waves.js"></script>
        <script src="res/assets/js/jquery.nicescroll.js"></script>
        <script src="res/assets/js/jquery.scrollTo.min.js"></script>
        <script src="res/assets/js/jquery.slimscroll.js"></script>
        <script src="res/plugins/switchery/switchery.min.js"></script>

        <!-- Required datatable js -->
        <script src="res/plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="res/plugins/datatables/dataTables.bootstrap4.min.js"></script>

        <!-- Buttons examples -->
        <script src="res/plugins/datatables/dataTables.buttons.min.js"></script>
        <script src="res/plugins/datatables/buttons.bootstrap4.min.js"></script>
        <script src="res/plugins/datatables/jszip.min.js"></script>
        <script src="res/plugins/datatables/pdfmake.min.js"></script>
        <script src="res/plugins/datatables/vfs_fonts.js"></script>
        <script src="res/plugins/datatables/buttons.html5.min.js"></script>
        <script src="res/plugins/datatables/buttons.print.min.js"></script>

        <!-- Key Tables -->
        <script src="res/plugins/datatables/dataTables.keyTable.min.js"></script>

        <!-- Responsive examples -->
        <script src="res/plugins/datatables/dataTables.responsive.min.js"></script>
        <script src="res/plugins/datatables/responsive.bootstrap4.min.js"></script>

        <!-- Selection table -->
        <script src="res/plugins/datatables/dataTables.select.min.js"></script>

        <!-- App js -->
        <script src="res/assets/js/jquery.core.js"></script>
        <script src="res/assets/js/jquery.app.js"></script>

        <!-- Chosen plugin -->
        <script type="text/javascript" src="res/plugins/chosen/chosen.jquery.js"></script>
        <script type="text/javascript" src="res/plugins/gofrendi.chosen.ajaxify.js"></script>

        <!-- Alertify -->
        <script src="res/plugins/Alertify/alertify.min.js"></script>

        <!-- Core JS -->
        <script src="assets/vendor/libs/popper/popper.js"></script>
        <script src="assets/vendor/js/bootstrap.js"></script>
        <script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
        <script src="assets/vendor/js/menu.js"></script>

        <!-- Vendors JS -->
        <script src="assets/vendor/libs/apex-charts/apexcharts.js"></script>

        <!-- Main JS -->
        <script src="assets/js/main.js"></script>

        <!-- Page JS -->
        <script src="assets/js/dashboards-analytics.js"></script>

        <!-- FullCalendar JS -->
        <link href="assets/calendar/css/fullcalendar.css" rel="stylesheet" />
        <link href="assets/calendar/css/fullcalendar.print.css" rel="stylesheet" media="print" />
        <script src="assets/calendar/js/fullcalendar.js" type="text/javascript"></script>
