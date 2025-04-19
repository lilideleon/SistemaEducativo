window.onload = function () {
    Cargar();
};

// PAGINACIÓN DE LA TABLA DE DATOS
function Cargar() {
    var objetoDataTables_personal = $('#Tabla_Usuarios').DataTable({
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
        "sAjaxSource": "?c=Usuarios&a=Tabla"
    });
}

// FUNCIÓN PARA GUARDAR DATOS
function GuardarDatos() {
    var formData = new FormData();
    formData.append('Dpi', $('#Dpi').val());
    formData.append('PrimerNombre', $('#PrimerNombre').val());
    formData.append('SegundoNombre', $('#SegundoNombre').val());
    formData.append('PrimerApellido', $('#PrimerApellido').val());
    formData.append('SegundoApellido', $('#SegundoApellido').val());
    formData.append('Correo', $('#Correo').val());
    formData.append('Rol', $('#Perfil').val());
    formData.append('Usuario', $('#Usuario').val());
    formData.append('Contraseña', $('#Contraseña').val());
    formData.append('Foto', $('#foto')[0].files[0]);

    $.ajax({
        url: '?c=Usuarios&a=Agregar',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (data) {
            alertify.set('notifier', 'position', 'top-right');
            alertify.success('<i class="fa fa-check"></i> Usuario registrado correctamente');
            setTimeout(function () {
                location.reload();
            }, 2000);
        },
        error: function (xhr, status, error) {
            alertify.error('Error: ' + error);
        }
    });
}

// FUNCIÓN PARA MOSTRAR DATOS DE UN USUARIO
function DatosUsuario(id) {
    if (id) {
        $.ajax({
            url: '?c=Usuarios&a=getDatos',
            type: 'POST',
            data: { 'IdUsuario': id },
            dataType: 'json',
            success: function (response) {
                // Cargar los datos en el formulario de edición
                $("#Codigo").val(response.IdUsuario);
                $("#EDpi").val(response.Dpi);
                $("#EPrimerNombre").val(response.PrimerNombre);
                $("#ESegundoNombre").val(response.SegundoNombre);
                $("#EPrimerApellido").val(response.PrimerApellido);
                $("#ESegundoApellido").val(response.SegundoApellido);
                $("#ECorreo").val(response.Correo);
                $("#EPerfil").val(response.Rol).trigger('change');
                $("#EUsuario").val(response.Usuario);
                if (response.Foto) {
                    $("#EimagenMostrada").attr('src', response.Foto);
                }
                // Abrir el modal de edición
                $('#EditUser').modal('show');
            },
            error: function () {
                alertify.error('Error al obtener los datos del usuario');
            }
        });
    } else {
        alertify.error('ID de usuario no válido');
    }
}

// FUNCIÓN PARA ACTUALIZAR DATOS DE UN USUARIO
function ActualizarUsuario() {
    var formData = new FormData();
    formData.append('IdUsuario', $('#Codigo').val());
    formData.append('Dpi', $('#EDpi').val());
    formData.append('PrimerNombre', $('#EPrimerNombre').val());
    formData.append('SegundoNombre', $('#ESegundoNombre').val());
    formData.append('PrimerApellido', $('#EPrimerApellido').val());
    formData.append('SegundoApellido', $('#ESegundoApellido').val());
    formData.append('Correo', $('#ECorreo').val());
    formData.append('Rol', $('#EPerfil').val());
    formData.append('Usuario', $('#EUsuario').val());
    formData.append('Contraseña', $('#EContraseña').val());
    formData.append('Foto', $('#Efoto')[0].files[0]);

    $.ajax({
        url: '?c=Usuarios&a=Actualizar',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (data) {
            alertify.set('notifier', 'position', 'top-right');
            alertify.success('<i class="fa fa-check"></i> Usuario actualizado correctamente');
            setTimeout(function () {
                location.reload();
            }, 2000);
        },
        error: function (xhr, status, error) {
            alertify.error('Error: ' + error);
        }
    });
}

// FUNCIÓN PARA ELIMINAR UN USUARIO
function EliminarDatos(id) {
    if (id) {
        // Almacenar el ID del usuario en un campo oculto
        $('#DeleteUserId').val(id);
        // Abrir el modal de confirmación
        $('#DeleteUserModal').modal('show');
    } else {
        alertify.error('ID de usuario no válido');
    }
}

// FUNCIÓN PARA CONFIRMAR LA ELIMINACIÓN DE UN USUARIO
function ConfirmarEliminar() {
    var Codigo = $('#DeleteUserId').val();

    $.ajax({
        url: '?c=Usuarios&a=Eliminar',
        type: 'POST',
        data: { 'IdUsuario': Codigo },
        dataType: 'json',
        success: function (data) {
            alertify.set('notifier', 'position', 'top-right');
            alertify.success('<i class="fa fa-check"></i> Usuario eliminado correctamente');
            setTimeout(function () {
                location.reload();
            }, 2000);
        },
        error: function (xhr, status, error) {
            alertify.error('Error: ' + error);
        }
    });

    // Cerrar el modal después de confirmar
    $('#DeleteUserModal').modal('hide');
}

