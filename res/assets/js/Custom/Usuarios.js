//PAGINACION DE LA TABLA DE DATOS

var objetoDataTables_personal = $('#miTabla1').DataTable({
    "language": {
        "emptyTable":           "No hay datos disponibles en la tabla.",
        "info":             "Del _START_ al _END_ de _TOTAL_ ",
        "infoEmpty":            "Mostrando 0 registros de un total de 0.",
        "infoFiltered":         "(filtrados de un total de _MAX_ registros)",
        "infoPostFix":          "(actualizados)",
        "lengthMenu":           "Mostrar _MENU_ registros",
        "loadingRecords":       "Cargando...",
        "processing":           "Procesando...",
        "search":           "Buscar:",
        "searchPlaceholder":        "Dato para buscar",
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
    "iDisplayLength":           10,
    
    "bProcessing": true,
    "bServerSide": true,
    "sAjaxSource": "index.php?view=FetchUsers"
  
});


//FUNCION PARA ALMACENAR DATOS


 function GuardarDatos ()
    {
        var Cui = $('#Cui').val();
        var PrimerNombre = $('#PrimerNombre').val();
        var SegundoNombre = $('#SegundoNombre').val();
        var PrimerApellido = $('#PrimerApellido').val();
        var SegundoApellido = $('#SegundoApellido').val();
        var Usuario = $('#Usuario').val();
        var Contra = $('#Contra').val();
        var TipoUsuario = $('#TipoUsuario').val();
      
    

        if (Cui=="" || PrimerNombre=="" || SegundoNombre =="" || PrimerApellido=="" || SegundoApellido=="" || Usuario == "" || Contra =="" || TipoUsuario =="")
        {
            alertify.set('notifier','position','top-right');
                alertify.error('<font color="#fffff"><i class="fa fa-times"></i> LLENE LOS CAMPOS</font> ');
        }
        else
        {

            $.ajax({
                url: 'index.php?view=AddUser',
                type: 'post',
                data: {
                    'Cui': Cui, 'PrimerNombre':PrimerNombre, 'SegundoNombre':SegundoNombre,'PrimerApellido':PrimerApellido,'SegundoApellido':SegundoApellido, 'Usuario':Usuario,'Contra':Contra,'TipoUsuario':TipoUsuario
                },
                dataType: 'json',
                success: function(data) 
                {
                    alert('Registro exitoso');
                    window.location.reload();
                },
                error: function(jqXHR, textStatus, error) 
                {
                    alert('Error');
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
            url: 'index.php?view=DataUser',
            type: 'post',
            data: {'IdNino': id},
            dataType: 'json',
            success:function(response) {   
            console.log(response);    
                $("#ECodigo").val(response.id);
                $("#MCodigo").val(response.id);
                $("#ECui").val(response.Dpi);
                $("#EPrimerNombre").val(response.PrimerNombre);
                $("#ESegundoNombre").val(response.SegundoNombre);
                $("#EPrimerApellido").val(response.PrimerApellido);
                $("#ESegundoApellido").val(response.SegundoApellido);
                $("#EUsuario").val(response.Usuario);
                //$("#EContra").val(response.contraseña);
                $("#ETipoUsuario").val(response.TipoUsuario);       
                                
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
    var Cui = $('#ECui').val();
    var PrimerNombre = $('#EPrimerNombre').val();
    var SegundoNombre = $('#ESegundoNombre').val();
    var PrimerApellido = $('#EPrimerApellido').val();
    var SegundoApellido = $('#ESegundoApellido').val();
    var Usuario = $('#EUsuario').val();
    var Contra = $('#EContra').val();
    var TipoUsuario = $('#ETipoUsuario').val();
  


    if (Cui=="" || PrimerNombre=="" || SegundoNombre =="" || PrimerApellido=="" || SegundoApellido=="" || Usuario == "" || Contra =="" || TipoUsuario =="")
    {
        alertify.set('notifier','position','top-right');
            alertify.error('<font color="#fffff"><i class="fa fa-times"></i> LLENE LOS CAMPOS</font> ');
    }
    else
    {

        $.ajax({
            url: 'index.php?view=UpdateUser',
            type: 'post',
            data: {
                'Codigo':Codigo,'Cui': Cui, 'PrimerNombre':PrimerNombre, 'SegundoNombre':SegundoNombre,'PrimerApellido':PrimerApellido,'SegundoApellido':SegundoApellido, 'Usuario':Usuario,'Contra':Contra,'TipoUsuario':TipoUsuario
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
            url: 'index.php?view=DeleteUser',
            type: 'post',
            data: {
                'Codigo':Codigo
            },
            dataType: 'json',
            success: function(data) 
            {
                alert('Registro Eliminado');
                window.location.reload();
            },
            error: function(jqXHR, textStatus, error) 
            {
                alert('Error al eliminar');
            }
        });
}

