//CARGAR ITEMS A LOS CHOOSEN

$('#Departamento').chosen({allow_single_deselect:true, width:"200px", search_contains: true});
            chosen_ajaxify('Departamento', 'index.php?view=AutoCompleteDeptos&keyword=');

$('#Municipio').chosen({allow_single_deselect:true, width:"200px", search_contains: true});
        chosen_ajaxify('Municipio', 'index.php?view=AutoCompleteMunicipios&keyword=');

$('#Aldeas').chosen({allow_single_deselect:true, width:"200px", search_contains: true});
        chosen_ajaxify('Aldeas', 'index.php?view=AutoCompleteAldeas&keyword=');

$('#Caserio').chosen({allow_single_deselect:true, width:"200px", search_contains: true});
        chosen_ajaxify('Caserio', 'index.php?view=AutoCompleteCaserios&keyword=');

$('#Aldea').chosen({allow_single_deselect:true, width:"200px", search_contains: true});
        chosen_ajaxify('Aldea', 'index.php?view=AutoCompleteAldeas&keyword=');


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
    "sAjaxSource": "index.php?view=FetchEncargados"
  
});


//MMETODO PARA ALMACENAR LOS DATOS

function GuardarDatos ()
{
    var Cui = $('#Dpi').val();
    var PrimerNombre = $('#PrimerNombre').val();
    var SegundoNombre = $('#SegundoNombre').val();
    var PrimerApellido = $('#PrimerApellido').val();
    var SegundoApellido = $('#SegundoApellido').val();
    var Nacimiento = $('#FechaNaciemiento').val();
    var Telefono = $('#Telefono').val();
    var Departamento = $('#Departamento').val();
    var Municipio = $('#Municipio').val();
    var Aldea = $('#Aldeas').val();
    var Caserio = $('#Caserio').val();


    if (Cui=="" || PrimerNombre=="" || SegundoNombre =="" || PrimerApellido=="" || SegundoApellido=="" || Nacimiento == "" || Telefono =="" || Caserio=="")
    {
        alertify.set('notifier','position','top-right');
            alertify.error('<font color="#fffff"><i class="fa fa-times"></i> LLENE LOS CAMPOS</font> ');
    }
    else
    {
        $.ajax({
            url: 'index.php?view=AddEncargado',
            type: 'post',
            data: {
                'Dpi': Cui, 'PrimerNombre':PrimerNombre, 'SegundoNombre':SegundoNombre,'PrimerApellido':PrimerApellido,'SegundoApellido':SegundoApellido, 'Nacimiento':Nacimiento, 'Telefono':Telefono, 'Direccion':Caserio 
            },
            dataType: 'json',
            success: function(data) 
            {
                alert('Registro exitoso');
                $(location).attr('href','index.php?view=Padres')
            },
            error: function(jqXHR, textStatus, error) 
            {
                alert('Error al guardar los datos');
            }
        });
    }
}


function GuardarDepartamento ()
{
    var Nombre = $('#NombreDep').val();
    
    $.ajax({
        url: 'index.php?view=AddDepto',
        type: 'post',
        data: {
            'Nombre': Nombre
        },
        dataType: 'json',
        success: function(data) 
        {
            alert('Registro exitoso');
        },
        error: function(jqXHR, textStatus, error) 
        {
            alert('Error');
        }
    });
    $('#RegistrarDepto').modal('hide');
}


function GuardarMunicipio()
{
    var Nombre = $('#NombreMun').val();
    var Departamento = $('#Departamento').val();

    if (Departamento == '')
    {
        alert("PRIMERO INDIQUE EL DEPARTAMENTO")
    }
    else
    {
        $.ajax({
        url: 'index.php?view=AddMunicipio',
        type: 'post',
        data: {
            'Nombre': Nombre,'Depto':Departamento
        },
        dataType: 'json',
        success: function(data) 
        {
            alert('Registro exitoso');
        },
        error: function(jqXHR, textStatus, error) 
        {
            alert('Error');
        }
    });
    }
    $('#RegistrarMuni').modal('hide');
}


function GuardarAldea()
{
    var Nombre = $('#NombreCom').val();
    var Municipio = $('#Municipio').val();

    if (Municipio == '')
    {
        alert("PRIMERO INDIQUE EL MUNICIPIO")
    }
    else
    {
        $.ajax({
        url: 'index.php?view=AddComunity',
        type: 'post',
        data: {
            'Nombre': Nombre,'Muni':Municipio
        },
        dataType: 'json',
        success: function(data) 
        {
            alert('Registro exitoso');
        },
        error: function(jqXHR, textStatus, error) 
        {
            alert('Error');
        }
    });
    }
    $('#AddComunity').modal('hide');
}

function GuardarPueblo()
{
    var Nombre = $('#NombrePueb').val();
    var Aldea = $('#Aldeas').val();

    if (Aldea == '')
    {
        alert("PRIMERO INDIQUE LA ALDEA")
    }
    else
    {
        $.ajax({
        url: 'index.php?view=AddCaserio',
        type: 'post',
        data: {
            'Nombre': Nombre,'Aldea':Aldea
        },
        dataType: 'json',
        success: function(data) 
        {
            alert('Registro exitoso');
        },
        error: function(jqXHR, textStatus, error) 
        {
            alert('Error');
        }
    });
    }
    $('#AddPueblo').modal('hide');
}


function DatosEncargado (id)
{
    if (id) 
    {
        //MANDAR EL ID ELEGIDO MEDIANTE $.AJAX PARA PROCESAR LOS DATOS Y DEVOLVERLOS MEDIANTE JSON
        $.ajax({
        url: 'index.php?view=DataEncargado',
        type: 'post',
        data: {'IdEncargado': id},
        dataType: 'json',
        success:function(response) {   
        console.log(response);    
            $("#ECodigo").val(response.id);
            $("#Codigo").val(response.id);
            $("#Cui").val(response.NoDpi);
            $("#PrimerNombre").val(response.PrimerNombre);
            $("#SegundoNombre").val(response.SegundoNombre);
            $("#PrimerApellido").val(response.PrimerApellido);
            $("#SegundoApellido").val(response.SegundoApellido);
            $("#FechaNaciemiento").val(response.Nacimiento);
            $("#Telefono").val(response.Telefono);
                            
        } //success function
        })// /ajax to fetch product image

    }
    else
    {
        alert("ERROR AL Mostrar DATOS");
    }
}



//MMETODO PARA ACTUALIZAR LOS DATOS

function ActualizarDatos ()
{
    var Codigo = $('#Codigo').val();
    var Cui = $('#Cui').val();
    var PrimerNombre = $('#PrimerNombre').val();
    var SegundoNombre = $('#SegundoNombre').val();
    var PrimerApellido = $('#PrimerApellido').val();
    var SegundoApellido = $('#SegundoApellido').val();
    var Nacimiento = $('#FechaNaciemiento').val();
    var Telefono = $('#Telefono').val();
    var Caserio = $('#Caserio').val();
    var Estado = $('#Estado').val();


    if (Cui=="" || PrimerNombre=="" || SegundoNombre =="" || PrimerApellido=="" || SegundoApellido=="" || Nacimiento == "" || Telefono =="" || Caserio=="")
    {
        alertify.set('notifier','position','top-right');
            alertify.error('<font color="#fffff"><i class="fa fa-times"></i> LLENE LOS CAMPOS</font> ');
    }
    else
    {
        $.ajax({
            url: 'index.php?view=UpdatePadre',
            type: 'post',
            data: {
                'Codigo':Codigo,'Dpi': Cui, 'PrimerNombre':PrimerNombre, 'SegundoNombre':SegundoNombre,'PrimerApellido':PrimerApellido,'SegundoApellido':SegundoApellido, 'Nacimiento':Nacimiento, 'Telefono':Telefono, 'Direccion':Caserio,'Estado':Estado 
            },
            dataType: 'json',
            success: function(data) 
            {
                alert('Registro Actualizado');
                window.location.reload();
            },
            error: function(jqXHR, textStatus, error) 
            {
                alert('Error al guardar los datos');
            }
        });
    }
}




function EliminarDatos ()
{
    var Codigo = $('#ECodigo').val()

    $.ajax({
        url: 'index.php?view=DeletePadre',
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
            alert('Error al actualizar');
        }
    });
}