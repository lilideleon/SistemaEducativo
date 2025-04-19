window.onload = function () 
{ 
    Cargar();
    CargaFontaneros();
}

function CargaFontaneros ()
{
    $('#Fontanero').chosen({allow_single_deselect:true, width:"540px", search_contains: true});
        chosen_ajaxify('Fontanero', '?c=Pagos&a=getClientes&keyword=');

    $('#EFontanero').chosen({allow_single_deselect:true, width:"525px", search_contains: true});
    chosen_ajaxify('EFontanero', '?c=Pagos&a=getClientes&keyword=');
}


//PAGINACION DE LA TABLA DE DATOS

function Cargar ()
{
    var objetoDataTables_personal = $('#Tabla_Notificaciones').DataTable({
        "language": {
            "emptyTable":           "No hay datos disponibles en la tabla.",
            "info":                 "Del _START_ al _END_ de _TOTAL_ ",
            "infoEmpty":            "Mostrando 0 registros de un total de 0.",
            "infoFiltered":         "(filtrados de un total de _MAX_ registros)",
            "infoPostFix":          "(actualizados)",
            "lengthMenu":           "Mostrar _MENU_ registros",
            "loadingRecords":       "Cargando...",
            "processing":           "Procesando...",
            "search":               "Buscar:",
            "searchPlaceholder":    "Dato para buscar",
            "zeroRecords":          "No se han encontrado coincidencias.",
            "paginate": {
                "first":        "Primera",
                "last":         "Última",
                "next":         "Siguiente",
                "previous":     "Anterior"
            },
            "aria": {
                "sortAscending":    "Ordenación ascendente",
                "sortDescending":   "Ordenación descendente"
            }
        },
        "lengthMenu":               [[5,10,20,25,50,100], [5,10,20,25,50,100]],
        "iDisplayLength":           15,
        "bProcessing": true,
        "bServerSide": true,
        dom: 'Bfrtip',//definimos los elementos del control de la tabla
        buttons: [
                  'copyHtml5',
                  'excelHtml5',
                  'csvHtml5',
                  'pdf'
        ],
        "sAjaxSource": "?c=Notificaciones&a=Tabla"
      
    });

  

}
 
//FUNCION PARA ALMACENAR DATOS


function GuardarDatos ()
{
    var Titulo = $('#Titulo').val();
    var Mensaje = $('#Mensaje').val();
    var Importancia = $('#Importancia').val();
    var Fontanero = $('#Fontanero').val();
    var Fecha = $('#Fecha').val();
    var Estado = $('#Estado').val();


    if (Titulo=="" || Mensaje == "" || Fontanero =="" )
    {
        alertify.set('notifier','position','top-right');
        alertify.error('<font color="#fffff"><i class="fa fa-times"></i> LLENE LOS CAMPOS</font> ');
    }
    else
    {

        var formData = new FormData();
        formData.append('Titulo', Titulo);
        formData.append('Mensaje', Mensaje);
        formData.append('Importancia', Importancia);
        formData.append('Fontanero', Fontanero);
        formData.append('Fecha', Fecha);
        formData.append('Estado', Estado);

        $.ajax({
            url: '?c=Notificaciones&a=Agregar',
            type: 'POST',
            data: formData,
            processData: false,  // Importante para que jQuery no procese los datos
            contentType: false,  // Importante para que jQuery no establezca el tipo de contenido
            success: function(data) {

                var htmlMessage = '<span style="color: #000000;"><i class="fa fa-check"></i> Registrado </span>';

                alertify.set('notifier','position', 'top-left');
                alertify.alert(htmlMessage);
                // Muestra una alerta con alertify
                
                setTimeout(function() {
                    location.reload();
                }, 2000);  // 2000 milisegundos = 2 segundos
            },
            error: function(xhr, status, error) {
                alertify.error('Error: ' + error);  // Muestra un mensaje de error con alertify
            }
        });
    }
}

function ActualizarDatos ()
{
    var Codigo = $('#EId').val();
    var Titulo = $('#ETitulo').val();
    var Mensaje = $('#EMensaje').val();
    var Importancia = $('#EImportancia').val();
    var Fontanero = $('#EFontanero').val();
    var Fecha = $('#EFecha').val();
    var Estado = $('#EEstado').val();


    if (Titulo=="" || Mensaje == "" || Fontanero =="" )
    {
        alertify.set('notifier','position','top-right');
        alertify.error('<font color="#fffff"><i class="fa fa-times"></i> LLENE LOS CAMPOS</font> ');
    }
    else
    {

        var formData = new FormData();
        formData.append('Id', Codigo);
        formData.append('Titulo', Titulo);
        formData.append('Mensaje', Mensaje);
        formData.append('Importancia', Importancia);
        formData.append('Fontanero', Fontanero);
        formData.append('Fecha', Fecha);
        formData.append('Estado', Estado);

        $.ajax({
            url: '?c=Notificaciones&a=Actualizar',
            type: 'POST',
            data: formData,
            processData: false,  // Importante para que jQuery no procese los datos
            contentType: false,  // Importante para que jQuery no establezca el tipo de contenido
            success: function(data) {

                var htmlMessage = '<span style="color: #000000;"><i class="fa fa-check"></i> ACTUALIZADO </span>';

                alertify.set('notifier','position', 'top-left');
                alertify.alert(htmlMessage);
                // Muestra una alerta con alertify
                
                setTimeout(function() {
                    location.reload();
                }, 2000);  // 2000 milisegundos = 2 segundos
            },
            error: function(xhr, status, error) {
                alertify.error('Error: ' + error);  // Muestra un mensaje de error con alertify
            }
        });
    }
}

//DATOS DEL SERVICIO

function Datos (id)
{
    if (id) 
    {

        //MANDAR EL ID ELEGIDO MEDIANTE $.AJAX PARA PROCESAR LOS DATOS Y DEVOLVERLOS MEDIANTE JSON
        $.ajax({
        url: '?c=Notificaciones&a=Datos',
        type: 'post',
        data: {'Id': id},
        dataType: 'json',
        success:function(response) {   
        console.log(response);    
            $("#EId").val(response.Id);
            $("#ECodigo").val(response.Id);
            $("#ETitulo").val(response.Titulo);
            $("#EMensaje").val(response.Mensaje);
            $("#EImportancia").val(response.Importancia).trigger('change');
            $("#EFecha").val(response.Fecha);
            $("#EEstado").val(response.Estado).trigger('change');   
        } //success function
        })// /ajax to fetch product image

    }
    else
    {
        alert('error');
        alert("ERROR AL Mostrar DATOS");
    }
}

//ELIMINAR DATOS 

function EliminarDatos ()
{
    var Codigo = $('#ECodigo').val()

        $.ajax({
            url: '?c=Notificaciones&a=Desactivar',
            type: 'post',
            data: {
                'Codigo':Codigo
            },
            dataType: 'json',
            success: function(data) 
            {
                
                var htmlMessage = '<span style="color: #000000;"><i class="fa fa-check"></i> Registro eliminado </span>';

                alertify.set('notifier','position', 'top-left');
                alertify.alert(htmlMessage);
                // Muestra una alerta con alertify
                
                setTimeout(function() {
                    location.reload();
                }, 2000);  // 2000 milisegundos = 2 segundos
            },
            error: function(jqXHR, textStatus, error) 
            {
                alert('Error al eliminar');
            }
        });
}