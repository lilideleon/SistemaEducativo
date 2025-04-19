window.onload = function () 
{ 
    Cargar();
}


//PAGINACION DE LA TABLA DE DATOS

function Cargar ()
{
    var objetoDataTables_personal = $('#Tabla_Servicios').DataTable({
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
        "sAjaxSource": "?c=Servicios&a=Tabla"
      
    });

  

}

//FUNCION PARA ALMACENAR DATOS


function GuardarDatos ()
{
    var Servicio = $('#Servicio').val();
    var Cantidad = $('#Cantidad').val();
    var Monto = $('#Monto').val();
    var Descripcion = $('#Descripcion').val();


    if (Servicio=="" || Cantidad=="" || Monto =="" || Descripcion=="")
    {
        alertify.set('notifier','position','top-right');
        alertify.error('<font color="#fffff"><i class="fa fa-times"></i> LLENE LOS CAMPOS</font> ');
    }
    else
    {

        var formData = new FormData();
        formData.append('Servicio', Servicio);
        formData.append('Cantidad', Cantidad);
        formData.append('Monto', Monto);
        formData.append('Descripcion', Descripcion);

        $.ajax({
            url: '?c=Servicios&a=Agregar',
            type: 'POST',
            data: formData,
            processData: false,  // Importante para que jQuery no procese los datos
            contentType: false,  // Importante para que jQuery no establezca el tipo de contenido
            success: function(data) {

                var htmlMessage = '<span style="color: #000000;"><i class="fa fa-check"></i> Registrado</span>';

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

//METODO PARA ACTUALIZAR

function ActualizarDatos ()
{
    var Codigo = $('#Id').val();
    var Servicio = $('#EServicio').val();
    var Cantidad = $('#ECantidad').val();
    var Monto = $('#EMonto').val();
    var Descripcion = $('#EDescripcion').val();


    if (Codigo == "" || Servicio=="" || Cantidad=="" || Monto =="" || Descripcion=="")
    {
        alertify.set('notifier','position','top-right');
        alertify.error('<font color="#fffff"><i class="fa fa-times"></i> LLENE LOS CAMPOS</font> ');
    }
    else
    {

        var formData = new FormData();
        formData.append('Id', Codigo);
        formData.append('Servicio', Servicio);
        formData.append('Cantidad', Cantidad);
        formData.append('Monto', Monto);
        formData.append('Descripcion', Descripcion);

        $.ajax({
            url: '?c=Servicios&a=Actualizar',
            type: 'POST',
            data: formData,
            processData: false,  // Importante para que jQuery no procese los datos
            contentType: false,  // Importante para que jQuery no establezca el tipo de contenido
            success: function(data) {

                var htmlMessage = '<span style="color: #000000;"><i class="fa fa-check"></i> Actualizado </span>';

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

function DatosServicio (id)
{
    if (id) 
    {

        //MANDAR EL ID ELEGIDO MEDIANTE $.AJAX PARA PROCESAR LOS DATOS Y DEVOLVERLOS MEDIANTE JSON
        $.ajax({
        url: '?c=Servicios&a=getDatos',
        type: 'post',
        data: {'Id': id},
        dataType: 'json',
        success:function(response) {   
        console.log(response);    
            $("#Id").val(response.Id);
            $("#ECodigo").val(response.Id);
            $("#EServicio").val(response.Servicio);
            $("#ECantidad").val(response.Cantidad);
            $("#EDescripcion").val(response.Descripcion);
            $("#EMonto").val(response.Monto); 
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
            url: '?c=Servicios&a=Desactivar',
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