<?php 
    require 'views/Content/header.php';
?>

<?php 
if ($_SESSION['TipoUsuario'] == '') {
    print "<script>window.location='?c=Login';</script>";
} else if ($_SESSION['TipoUsuario'] == 2) {
    require 'views/Content/sidebar.php';
} else if ($_SESSION['TipoUsuario'] == 3) {
    require 'views/Content/sidebar2.php';
} else if ($_SESSION['TipoUsuario'] == 4) {
    require 'views/Content/sidebar3.php';
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
        max-width: 1950px; 
        height: auto; 
        max-height: 800px; 
    }

    @media (min-width: 1024px) {
        .move-left {
            left: 28px;
        }
    }
</style>

<div class="content-wrapper">
    <div class="row move-left move-down make-larger">
        <!-- Listado de Categorías -->
        <div class="col-12 col-md-6 col-lg-11">
            <div class="card mb-4">
                <center>
                    <h1 class="card-header">Listado de Categorías</h1>
                </center>
                <div class="card-body demo-vertical-spacing demo-only-element">
                    <div class="card-body">
                        <small class="text fw-semibold">En este módulo puede visualizar, registrar y modificar categorías.</small>
                        <div class="demo-inline-spacing d-flex justify-content-end">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#NuevaCategoriaModal">Nueva Categoría</button>
                        </div>
                    </div>

                    <!-- Tabla de Categorías -->
                    <div class="card">
                        <h5 class="card-header">Listado de Categorías</h5>
                        <div class="table-responsive text-nowrap">
                            <table class="table" id="Tabla_Categorias">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Nombre</th>
                                        <th>Unidades</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    <!-- Datos ficticios -->

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
    // Función para registrar una nueva categoría usando jquery 

    function RegistrarCategoria() {
        const nombre = $('#NombreCategoria').val();
        const unidades = $('#UnidadesCategoria').val();

        if (!nombre || !unidades) {
            alertify.error('Por favor, complete todos los campos.');
            return;
        }

        $.ajax({
            url: '?c=Categorias&a=Registrar',
            type: 'POST',
            data: {
                Nombre: nombre,
                Unidades: unidades
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alertify.success(response.message);
                    $('#NuevaCategoriaForm')[0].reset();
                    $('#NuevaCategoriaModal').modal('hide');
                    // Recargar la tabla de categorías
                    $('#Tabla_Categorias').DataTable().ajax.reload(); // Recargar la tabla
                } else {
                    alertify.error(response.message);
                }
            },
            error: function(xhr, status, error) {
                const errorMessage = `Error: ${error}\nStatus: ${status}\nResponse: ${xhr.responseText}`;
                alertify.error(errorMessage);
                console.error(errorMessage);
            }
        });
    }

    // Función para cargar datos en el modal de edición
    function CargarDatosCategoria(id, nombre, unidades, estado) {
        $('#EditarIdCategoria').val(id);
        $('#EditarNombreCategoria').val(nombre);
        $('#EditarUnidadesCategoria').val(unidades);
        $('#EditarEstadoCategoria').val(estado);

        $('#EditarCategoriaModal').modal('show');
    }

    // Función para actualizar una categoría usando AJAX
    function ActualizarCategoria() {
        const id = $('#EditarIdCategoria').val();
        const nombre = $('#EditarNombreCategoria').val();
        const unidades = $('#EditarUnidadesCategoria').val();
        const estado = $('#EditarEstadoCategoria').val();

        if (!id || !nombre || !unidades || !estado) {
            alertify.error('Por favor, complete todos los campos.');
            return;
        }

        $.ajax({
            url: '?c=Categorias&a=Actualizar',
            type: 'POST',
            data: {
                IdCategoria: id,
                Nombre: nombre,
                Unidades: unidades,
                Estado: estado
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    alertify.success(response.message);
                    $('#EditarCategoriaModal').modal('hide');
                    $('#Tabla_Categorias').DataTable().ajax.reload(); // Recargar la tabla
                } else {
                    alertify.error(response.message);
                }
            },
            error: function (xhr, status, error) {
                const errorMessage = `Error: ${error}\nStatus: ${status}\nResponse: ${xhr.responseText}`;
                alertify.error(errorMessage);
                console.error(errorMessage);
            }
        });
    }


    // Método para eliminar categoría mostrando primero un modal
    function EliminarCategoria(id) {
        alertify.confirm(
            'Eliminar Categoría',
            '¿Está seguro de que desea eliminar esta categoría?',
            function () {
                $.ajax({
                    url: '?c=Categorias&a=Eliminar',
                    type: 'POST',
                    data: { IdCategoria: id },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            alertify.success(response.message);
                            $('#Tabla_Categorias').DataTable().ajax.reload(); // Recargar la tabla
                        } else {
                            alertify.error(response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        const errorMessage = `Error: ${error}\nStatus: ${status}\nResponse: ${xhr.responseText}`;
                        alertify.error(errorMessage);
                        console.error(errorMessage);
                    }
                });
            },
            function () {
                alertify.error('Operación cancelada.');
            }
        );
    }

    window.onload = function () {
        CargarCategorias();
    };

    // PAGINACIÓN DE LA TABLA DE CATEGORÍAS
    function CargarCategorias() {
        $('#Tabla_Categorias').DataTable({
            "language": {
                "emptyTable": "No hay datos disponibles en la tabla.",
                "info": "Del _START_ al _END_ de _TOTAL_ ",
                "infoEmpty": "Mostrando 0 registros de un total de 0.",
                "infoFiltered": "(filtrados de un total de _MAX_ registros)",
                "lengthMenu": "Mostrar _MENU_ registros",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscar:",
                "searchPlaceholder": "Dato para buscar",
                "zeroRecords": "No se han encontrado coincidencias.",
                "paginate": {
                    "first": "Primera",
                    "last": "Última",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "lengthMenu": [[5, 10, 20, 25, 50, 100], [5, 10, 20, 25, 50, 100]],
            "iDisplayLength": 10,
            "bProcessing": true,
            "bServerSide": true,
            "destroy": true, // Permite recargar la tabla sin errores
            "dom": 'Blfrtip',
            "buttons": ['copyHtml5', 'excelHtml5', 'csvHtml5', 'pdfHtml5'],
            "sAjaxSource": "?c=Categorias&a=Tabla", // URL del controlador para obtener los datos
            "columns": [
                { "data": "IdCategoria" },
                { "data": "Nombre" },
                { "data": "Unidades" },
                { "data": "Estado" },
                { "data": "Acciones", "orderable": false, "searchable": false }
            ]
        });
    }
</script>

<!-- MODAL PARA REGISTRAR NUEVA CATEGORÍA -->
<div class="modal fade" id="NuevaCategoriaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Nueva Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="NuevaCategoriaForm">
                    <div class="mb-3">
                        <label for="NombreCategoria" class="form-label">Nombre de la Categoría:</label>
                        <input type="text" id="NombreCategoria" class="form-control" placeholder="Ej: Bebidas, Pollo Frito" required>
                    </div>
                    <div class="mb-3">
                        <label for="UnidadesCategoria" class="form-label">Unidades:</label>
                        <input type="text" id="UnidadesCategoria" class="form-control" placeholder="Ej: Litros, Piezas" required>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="RegistrarCategoria()">Registrar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PARA EDITAR CATEGORÍA -->
<div class="modal fade" id="EditarCategoriaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="EditarCategoriaForm">
                    <input type="hidden" id="EditarIdCategoria">
                    <div class="mb-3">
                        <label for="EditarNombreCategoria" class="form-label">Nombre de la Categoría:</label>
                        <input type="text" id="EditarNombreCategoria" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="EditarUnidadesCategoria" class="form-label">Unidades:</label>
                        <input type="text" id="EditarUnidadesCategoria" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="EditarEstadoCategoria" class="form-label">Estado:</label>
                        <select id="EditarEstadoCategoria" class="form-control" required>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="ActualizarCategoria()">Actualizar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
    require 'views/Content/footer.php';
?>