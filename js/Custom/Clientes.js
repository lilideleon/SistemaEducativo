window.onload = function () 
{ 
    Cargar();
}


//PAGINACION DE LA TABLA DE DATOS

function Cargar ()
{
    var objetoDataTables_personal = $('#miTabla1').DataTable({
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
        "sAjaxSource": "?c=Clientes&a=Tabla"
      
    });
}

//FUNCION PARA ALMACENAR DATOS


function GuardarDatos ()
{
    var Nombre = $('#Nombre').val();
    var Telefono = $('#Telefono').val();
    var Correo = $('#Correo').val();
    var Dpi = $('#Dpi').val();

    if (Nombre =="")
    {
        alertify.set('notifier','position','top-right');
        alertify.error('<font color="#fffff"><i class="fa fa-times"></i> INDIQUE NOMBRE CLIENTE</font> ');
    }
    else
    {
        $.ajax({
            url: '?c=Clientes&a=Agregar',
            type: 'post',
            data: {
                'Nombre':Nombre,'Telefono':Telefono,'Correo':Correo,'Dpi':Dpi
            },
            dataType: 'json',
            success: function(data) 
            {
                alert('Registro exitoso');
                window.location.reload();
            },
            error: function(jqXHR, textStatus, error) 
            {
                alert('Error'+error);
            }
        });
    }
}


//MOSTRAR LA INFO DE LOS USUARIOS


function DatosUsuario (id)
{
    if (id) 
    {
        //MANDAR EL ID ELEGIDO MEDIANTE $.AJAX PARA PROCESAR LOS DATOS Y DEVOLVERLOS MEDIANTE JSON
        $.ajax({
        url: '?c=Clientes&a=getDatos',
        type: 'post',
        data: {'Codigo': id},
        dataType: 'json',
        success:function(response) {   
        console.log(response);    
            $("#ECodigo").val(response.Id);
            $("#MCodigo").val(response.Id);
            $("#ENombre").val(response.Nombre);     
            $("#ETelefono").val(response.Telefono);
            $("#ECorreo").val(response.Correo);
            $("#EDpi").val(response.Documento);                
        } //success function
        })// /ajax to fetch product image

    }
    else
    {
        alert('error');
        alert("ERROR AL Mostrar DATOS");
    }
}



//MOSTRAR LA INFO DE LOS USUARIOS


function ActualizarUsuario ()
{
    var Codigo = $('#MCodigo').val();
    var Nombre = $('#ENombre').val();
    var Telefono = $('#ETelefono').val();
    var Correo = $('#ECorreo').val();
    var Dpi = $('#EDpi').val();
  


    if (Nombre =="")
    {
        alertify.set('notifier','position','top-right');
        alertify.error('<font color="#fffff"><i class="fa fa-times"></i> LLENE LOS CAMPOS O CIERRE LA VENTANA </font> ');
    }
    else
    {

        $.ajax({
            url: '?c=Clientes&a=Actualizar',
            type: 'post',
            data: {
                'Codigo':Codigo,'Nombre':Nombre,'Telefono':Telefono,'Correo':Correo,'Dpi':Dpi
            },
            dataType: 'json',
            success: function(data) 
            {
                alert('Registro Actualizado');
                window.location.reload();
            },
            error: function(jqXHR, textStatus, error) 
            {
                alert('Error');
            }
        });
    }
}


function EliminarDatos ()
{
    var Codigo = $('#ECodigo').val()

        $.ajax({
            url: '?c=Clientes&a=Desactivar',
            type: 'post',
            data: {
                'Codigo':Codigo
            },
            dataType: 'json',
            success: function(data) 
            {
                alert('Usuario desactivado');
                window.location.reload();
            },
            error: function(jqXHR, textStatus, error) 
            {
                alert('Error al eliminar');
            }
        });
}

