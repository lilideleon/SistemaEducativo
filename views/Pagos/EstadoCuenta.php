<!DOCTYPE html>
<html lang="es">
<head>
    <title>Pagos/Multas</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <link rel="stylesheet" href="./css/main.css">
    <!-- DataTables -->
    <link href="res/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
        <link href="res/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
        <!-- Responsive datatable examples -->
        <link href="res/plugins/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />
        <!-- Multi Item Selection examples -->
        <link href="res/plugins/datatables/select.bootstrap4.min.css" rel="stylesheet" type="text/css" />
</head>
<body>

    <style>
        header {
                background-color: #ffffff; /* Color de fondo */
                color: black;              /* Color de texto */
                padding: 20px;             /* Espacio interior */
                text-align: center;        /* Centrar el texto */
            }

            /* Puedes agregar estilos adicionales para mejorar el diseño global */
            body {
                font-family: Arial, sans-serif;
                transform: scale(0.9);
    transform-origin: top center;
            }

    </style>

    <header>
        <h3>Estado de cuenta</h3>
    </header>
        
        <!-- Navegación de pestañas -->
    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#tab1">Mis pagos</a></li>
        <li><a data-toggle="tab" href="#tab2">Mis deudas</a></li>
    </ul>

    <!-- Contenido de las pestañas -->
    <div class="tab-content">
    <div id="tab1" class="tab-pane fade in active">
        <h3>Mis pagos</h3>
        <!-- Envolver la tabla en un div con la clase table-responsive -->
        <div class="table-responsive">
            <table class="table" id="TablePagos">
                <thead>
                    <tr>
                        <th>DPI</th>
                        <th>SERVICIO</th>
                        <th>FECHA</th>
                        <th>MES</th>
                        <th>AÑO</th>
                        <th>MONTO</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        </div> <!-- Fin table-responsive -->
    </div>
    <div id="tab2" class="tab-pane fade">
        <h3>Mis deudas</h3>
        <!-- Envolver la tabla en un div con la clase table-responsive -->
        <div class="table-responsive">
            <table class="table" id="TableDeudas" width="100%">
                <thead>
                    <tr>
                        <th>DPI</th>
                        <th>SERVICIO</th>
                        <th>FECHA</th>
                        <th>MES</th>
                        <th>AÑO</th>
                        <th>MONTO</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        </div> <!-- Fin table-responsive -->
    </div>
</div>


    <script>
        window.onload = function() {
            var Id  = <?php echo json_encode($IdAsociado); ?>;
            
            if (Id == "")
            {
                alert('INTENTELO DE NUEVO');
            }
            else
            {
                TablaPagos(Id);
                TablaDeudas(Id);
            }

        }


        function TablaPagos(Id)
        {
            $.ajax({
                type: "POST",
                url: "?c=Pagos&a=TablaPagos", 
                data: { Id: Id }, 
                dataType: "json",
                success: function(response) {
                    $('#TablePagos').DataTable({
                        data: response.data,
                        columns: [
                            { data: "Dpi" },
                            { data: "Descripcion" },
                            { data: "mes" },
                            { data: "anio" },
                            { data: "Fecha" },
                            { data: "Sub_Total" }
                        ]
                        ,
                        // Configuración de los botones
                        dom: 'Bfrtip',
                        buttons: [
                            'copyHtml5',
                            'excelHtml5',
                            'csvHtml5',
                            'pdfHtml5'
                        ]

                    });
                },
                error: function(error) {
                    console.error("Error en la solicitud AJAX: ", error);
                }
            });
        }

        function TablaDeudas (Id)
        {
            $.ajax({
                type: "POST",
                url: "?c=Pagos&a=TablaDeudas", 
                data: { Id: Id }, 
                dataType: "json",
                success: function(response) {
                    $('#TableDeudas').DataTable({
                        data: response.data,
                        columns: [
                            { data: "Dpi" },
                            { data: "Servicio" },
                            { data: "Fecha" },
                            { data: "MesNoPagado" },
                            { data: "AnioNoPagado" },
                            { data: "Monto" }
                        ]
                        ,
                        // Configuración de los botones
                        dom: 'Bfrtip',
                        buttons: [
                            'copyHtml5',
                            'excelHtml5',
                            'csvHtml5',
                            'pdfHtml5'
                        ],
                        lengthMenu: [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
                        pageLength: 5

                    });
                },
                error: function(error) {
                    console.error("Error en la solicitud AJAX: ", error);
                }
            });
        }
    </script>



   <!--====== Scripts -->
    <script src="./js/jquery-3.1.1.min.js"></script>
    <script src="./js/bootstrap.min.js"></script>
    <script src="./js/material.min.js"></script>
    <script src="./js/ripples.min.js"></script>
    <script src="./js/sweetalert2.min.js"></script>
    <script src="./js/jquery.mCustomScrollbar.concat.min.js"></script>
    <script src="./js/main.js"></script>
    <script>
        $.material.init();
    </script>


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
</body>
</html>