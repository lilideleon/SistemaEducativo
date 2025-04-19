window.onload = function () {
    Cargar();

    // Llamar a la función al abrir los modales
$('#NuevoInventarioModal').on('show.bs.modal', CargarProductos);
$('#EditarInventarioModal').on('show.bs.modal', CargarProductos);
};

// PAGINACIÓN DE LA TABLA DE DATOS
function Cargar() {
    var objetoDataTables_inventario = $('#Tabla_Inventario').DataTable({
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
        "sAjaxSource": "?c=Inventario&a=Tabla"
    });
}

function DatosInventario(Id) {
    $.ajax({
        url: '?c=Inventario&a=ConsultarInventarioPorId',
        type: 'POST',
        data: { Id: Id },
        dataType: 'json',
        success: function(response) {
            $("#EditarId").val(response.Id);
            $("#EditarProductoId").val(response.ProductoId);
            $("#EditarCantidad").val(response.Cantidad);
        },
        error: function() {
            alert('Error al obtener los datos del inventario.');
        }
    });
}

function RegistrarInventario() {
    const formData = new FormData(document.getElementById('NuevoInventarioForm'));

    fetch('?c=Inventario&a=GuardarInventario', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alertify.success(data.msj);
            document.getElementById('NuevoInventarioForm').reset();
            $('#NuevoInventarioModal').modal('hide');
            $('#Tabla_Inventario').DataTable().ajax.reload();
        } else {
            alertify.error(data.msj);
        }
    })
    .catch(error => {
        alertify.error('Error en la solicitud: ' + error.message);
    });
}

function ActualizarInventario() {
    const formData = new FormData(document.getElementById('EditarInventarioForm'));

    fetch('?c=Inventario&a=ActualizarInventario', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alertify.success(data.msj);
            $('#EditarInventarioModal').modal('hide');
            $('#Tabla_Inventario').DataTable().ajax.reload();
        } else {
            alertify.error(data.msj);
        }
    })
    .catch(error => {
        alertify.error('Error en la solicitud: ' + error.message);
    });
}

function EliminarInventario(Id) {
    alertify.confirm(
        'Confirmar Eliminación',
        '¿Está seguro de que desea eliminar este registro de inventario?',
        function() {
            $.ajax({
                url: '?c=Inventario&a=DesactivarInventario',
                type: 'POST',
                data: { Id: Id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alertify.success(response.msj);
                        $('#Tabla_Inventario').DataTable().ajax.reload();
                    } else {
                        alertify.error(response.msj);
                    }
                },
                error: function() {
                    alertify.error('Error al intentar eliminar el registro de inventario.');
                }
            });
        },
        function() {
            alertify.error('Eliminación cancelada');
        }
    );
}

function CargarProductos() {
    fetch('?c=Inventario&a=ConsultarProductosActivos')
        .then(response => response.json())
        .then(data => {
            const productoSelect = document.getElementById('ProductoId');
            const editarProductoSelect = document.getElementById('EditarProductoId');

            productoSelect.innerHTML = '<option value="">Seleccione un producto</option>';
            editarProductoSelect.innerHTML = '<option value="">Seleccione un producto</option>';

            data.forEach(producto => {
                const option = `<option value="${producto.IdProducto}">${producto.Nombre}</option>`;
                productoSelect.innerHTML += option;
                editarProductoSelect.innerHTML += option;
            });
        })
        .catch(error => {
            console.error('Error al cargar los productos:', error);
        });
}



