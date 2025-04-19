window.onload = function () {
    Cargar();
};

// PAGINACIÓN DE LA TABLA DE DATOS
function Cargar() {
    var objetoDataTables_personal = $('#Tabla_Despieces').DataTable({
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
        "sAjaxSource": "?c=Despieces&a=Tabla"
    });
}

function RegistrarDespiece() {
    const formData = new FormData(document.getElementById('NuevoDespieceForm'));

    fetch('?c=Despieces&a=GuardarDespiece', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alertify.success(data.msj);
            document.getElementById('NuevoDespieceForm').reset();
            $('#NuevoDespieceModal').modal('hide');
            // Recargar la tabla de despieces
            $('#Tabla_Despieces').DataTable().ajax.reload();
        } else {
            alertify.error(data.msj);
        }
    })
    .catch(error => {
        alertify.error('Error en la solicitud: ' + error.message);
    });
}

function EliminarDespiece(Id) {
    alertify.confirm(
        'Confirmar Eliminación',
        '¿Está seguro de que desea eliminar este despiece?',
        function() {
            // Si el usuario confirma, realizar la solicitud para eliminar el despiece
            $.ajax({
                url: '?c=Despieces&a=EliminarDespiece',
                type: 'POST',
                data: { Id: Id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alertify.success(response.msj);
                        // Recargar la tabla de despieces
                        $('#Tabla_Despieces').DataTable().ajax.reload();
                    } else {
                        alertify.error(response.msj);
                    }
                },
                error: function() {
                    alertify.error('Error al intentar eliminar el despiece.');
                }
            });
        },
        function() {
            // Si el usuario cancela, mostrar un mensaje
            alertify.error('Eliminación cancelada');
        }
    );
}

function ActualizarDespiece() {
    const formData = new FormData(document.getElementById('EditarDespieceForm'));

    fetch('?c=Despieces&a=ActualizarDespiece', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alertify.success(data.msj);
            $('#EditarDespieceModal').modal('hide');
            // Recargar la tabla de despieces
            $('#Tabla_Despieces').DataTable().ajax.reload();
        } else {
            alertify.error(data.msj);
        }
    })
    .catch(error => {
        alertify.error('Error en la solicitud: ' + error.message);
    });
}

function EditarDespiece(Id) {

    $.ajax({
        url: '?c=Despieces&a=DatosModal',
        type: 'POST',
        data: { Id: Id },
        dataType: 'json',
        success: function(response) {
            if (response) {
                // Populate the modal fields with the response data
                $('#EditarIdDespiece').val(response.Id);
                $('#EditarProductoOrigenId').val(response.ProductoOrigenId);
                $('#EditarProductoResultadoId').val(response.ProductoResultadoId);
                $('#EditarCantidad').val(response.Cantidad);

                // Show the modal
                $('#EditarDespieceModal').modal('show');
            } else {
                alertify.error('No se encontraron datos para este despiece.');
            }
        },
        error: function(xhr, status, error) {
            // Log detailed error information
            console.error('Error details:', {
                status: status,
                error: error,
                responseText: xhr.responseText
            });

            // Display a more detailed error message
            alertify.error('Error al obtener los datos del despiece. Detalles: ' + error + ' (' + status + ')');

            console.error('Error al obtener los datos del despiece: ' + error + ' (' + status + ')');
        }
    });
}




