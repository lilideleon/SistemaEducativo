window.onload = function () {
    Cargar();
};

// PAGINACIÓN DE LA TABLA DE DATOS
function Cargar() {
    var objetoDataTables_personal = $('#Tabla_Productos').DataTable({
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
        "iDisplayLength": 15,
        "bProcessing": true,
        "bServerSide": true,
        dom: 'Blfrtip',
        buttons: ['copyHtml5', 'excelHtml5', 'csvHtml5', 'pdf'],
        "sAjaxSource": "?c=Productos&a=Tabla"
    });
}



function DatosProducto(IdProducto) {
    $.ajax({
        url: '?c=Productos&a=DatosModal',
        type: 'POST',
        data: { IdProducto: IdProducto },
        dataType: 'json',
        success: function(response) {
            $("#EditarIdProducto").val(response.IdProducto);
            $("#EditarNombreProducto").val(response.Nombre);
            $("#EditarPrecioCosto").val(response.PrecioCosto);
            $("#EditarPrecioVenta").val(response.PrecioVenta);
            $("#EditarDescripcionProducto").val(response.Descripcion);
            // Update the image preview in the modal
            if (response.Imagen) {
                $("#ProductoImagenPreview").attr('src', response.Imagen);
            } else {
                $("#ProductoImagenPreview").attr('src', ''); // Clear the preview if no image
            }
        },
        error: function() {
            alert('Error al obtener los datos del producto.');
        }
    });
}

function RegistrarProducto() {
    const formData = new FormData(document.getElementById('NuevoProductoForm'));

    fetch('?c=Productos&a=GuardarProducto', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alertify.success(data.msj);
            document.getElementById('NuevoProductoForm').reset();
            $('#NuevoProductoModal').modal('hide');
            // Recargar la tabla de productos

            $('#Tabla_Productos').DataTable().ajax.reload();


        } else {
            alertify.error(data.msj);
        }
    })
    .catch(error => {
        alertify.error('Error en la solicitud: ' + error.message);
    });
}

function ActualizarProducto() {
    const formData = new FormData(document.getElementById('EditarProductoForm'));

    fetch('?c=Productos&a=ActualizarProducto', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alertify.success(data.msj);
            $('#EditarProductoModal').modal('hide');
            
            // Recargar la tabla de productos

            $('#Tabla_Productos').DataTable().ajax.reload();


        } else {
            alertify.error(data.msj);
        }
    })
    .catch(error => {
        alertify.error('Error en la solicitud: ' + error.message);
    });
}

function EliminarProducto(IdProducto) {
    alertify.confirm(
        'Confirmar Eliminación',
        '¿Está seguro de que desea eliminar este producto?',
        function() {
            // Si el usuario confirma, realizar la solicitud para eliminar el producto
            $.ajax({
                url: '?c=Productos&a=Desactivar',
                type: 'POST',
                data: { Codigo: IdProducto },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alertify.success(response.msj);
                        // Recargar la tabla de productos
                        $('#Tabla_Productos').DataTable().ajax.reload();
                    } else {
                        alertify.error(response.msj);
                    }
                },
                error: function() {
                    alertify.error('Error al intentar eliminar el producto.');
                }
            });
        },
        function() {
            // Si el usuario cancela, mostrar un mensaje
            alertify.error('Eliminación cancelada');
        }
    );
}




