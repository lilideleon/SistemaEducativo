
//CARGAR LA PAGINACION A LA TABLA

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
    "sAjaxSource": "index.php?view=FetchNinos"
});


//CARGAR LOS CHOSEEN


$('#Encargado').chosen({allow_single_deselect:true, width:"200px", search_contains: true});
        chosen_ajaxify('Encargado', 'index.php?view=AutoCompleteEncargado&keyword=');

//SEGUNDO CHOOSEEN

$('#EEncargado').chosen({allow_single_deselect:true, width:"200px", search_contains: true});
            chosen_ajaxify('EEncargado', 'index.php?view=AutoCompleteEncargado&keyword=');


//METODO PARA MANDAR A ALMACENAR LOS DATOS DE LOS NIÑOS

function GuardarDatos ()
{
    var Cui = $('#Cui').val();
    var PrimerNombre = $('#PrimerNombre').val();
    var SegundoNombre = $('#SegundoNombre').val();
    var PrimerApellido = $('#PrimerApellido').val();
    var SegundoApellido = $('#SegundoApellido').val();
    var Nacimiento = $('#FechaNaciemiento').val();
    var Edad = $('#Edad').val();
    var Sexo = $('#Sexo option:selected').attr('id');
    var Comunidad = $('#Comunidad option:selected').attr('id');;
    var Encargado = $('#Encargado').val();
   
    var date = new Date($('#FechaNaciemiento').val());
    //day = date.getDate();
    //month = date.getMonth() + 1;
    year = date.getFullYear();

    var prueba = new Date();
    var valor= prueba.getFullYear();
  


    if (Cui=="" || PrimerNombre=="" || SegundoNombre =="" || PrimerApellido=="" || SegundoApellido=="" || Nacimiento == "" || Sexo== "" || Comunidad=="" || Encargado=="")
    {
        alertify.set('notifier','position','top-right');
            alertify.error('<font color="#fffff"><i class="fa fa-times"></i> LLENE LOS CAMPOS</font> ');
    }
    else
    {
        var Calculo;
        Calculo =  valor- year;
        $('#Edad').val(Calculo);

        $.ajax({
            url: 'index.php?view=AddNino',
            type: 'post',
            data: {
                'Cui': Cui, 'PrimerNombre':PrimerNombre, 'SegundoNombre':SegundoNombre,'PrimerApellido':PrimerApellido,'SegundoApellido':SegundoApellido, 'Nacimiento':Nacimiento, 'Edad':Calculo, 'Sexo':Sexo, 'Comunidad':Comunidad, 'Encargado':Encargado
            },
            dataType: 'json',
            success: function(data) 
            {
                alert('Registro exitoso');
                $(location).attr('href','index.php?view=Ninos')
            },
            error: function(jqXHR, textStatus, error) 
            {
                alert('Error');
            }
        });
    }
}


//METODO PARA ACTUALIZAR DATOS


function ActualizarDatos()
{
    var IdNino = $('#EId').val();
    var Cui = $('#ECui').val();
    var PrimerNombre = $('#EPrimerNombre').val();
    var SegundoNombre = $('#ESegundoNombre').val();
    var PrimerApellido = $('#EPrimerApellido').val();
    var SegundoApellido = $('#ESegundoApellido').val();
    var Nacimiento = $('#EFechaNaciemiento').val();
    var Edad = $('#EEdad').val();
    var Sexo = $('#ESexo').val();
    var Comunidad = $('#EComunidad').val();
    var Encargado = $('#EEncargado').val();
    var date = new Date($('#EFechaNaciemiento').val());
    //day = date.getDate();
    //month = date.getMonth() + 1;
    year = date.getFullYear();

    var prueba = new Date();
    var valor= prueba.getFullYear();


    if (Cui=="" || PrimerNombre=="" || SegundoNombre =="" || PrimerApellido=="" || SegundoApellido=="" || Nacimiento == "" || Sexo== "" || Comunidad=="" || Encargado=="")
    {
        alertify.set('notifier','position','top-right');
            alertify.error('<font color="#fffff"><i class="fa fa-times"></i> LLENE LOS CAMPOS</font> ');
    }
    else
    {
        var Calculo;
        Calculo =  valor- year;
        $('#Edad').val(Calculo);

        $.ajax({
            url: 'index.php?view=UpdateNinos',
            type: 'post',
            data: {
                'Id':IdNino,'Cui': Cui, 'PrimerNombre':PrimerNombre, 'SegundoNombre':SegundoNombre,'PrimerApellido':PrimerApellido,'SegundoApellido':SegundoApellido, 'Nacimiento':Nacimiento, 'Edad':Calculo, 'Sexo':Sexo, 'Comunidad':Comunidad, 'Encargado':Encargado
            },
            dataType: 'json',
            success: function(data) 
            {
                alert('Registro exitoso');
                $(location).attr('href','index.php?view=Ninos')
            },
            error: function(jqXHR, textStatus, error) 
            {
                alert('Error');
            }
        });
    }
}


//METODO PARA OBTENER EL CODIGO

function ObtenerCodigo (id)
{
    if (id) 
    {
        //MANDAR EL ID ELEGIDO MEDIANTE $.AJAX PARA PROCESAR LOS DATOS Y DEVOLVERLOS MEDIANTE JSON
        $.ajax({
        url: 'index.php?view=DatosNinos',
        type: 'post',
        data: {'IdNino': id},
        dataType: 'json',
        success:function(response) {   
        console.log(response);    
            $("#ECodigo").val(response.id);
        } //success function
        })// /ajax to fetch product image
    }
    else
    {
        alert("ERROR AL Mostrar DATOS");
    }
}


//METODO PARA ELIMINAR UN NIÑO

function EliminarDatos ()
{
    var Codigo = $('#ECodigo').val()

    $.ajax({
        url: 'index.php?view=DeleteNino',
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


//METODO PARA OBTENER DATOS DE UN NIÑO, ENCARGADO Y DIRECCION EXACTA

function DataNino(id)
{
    if (id) 
    {
        //MANDAR EL ID ELEGIDO MEDIANTE $.AJAX PARA PROCESAR LOS DATOS Y DEVOLVERLOS MEDIANTE JSON
        $.ajax({
        url: 'index.php?view=DatosNinosC',
        type: 'post',
        data: {'IdNino': id},
        dataType: 'json',
        success:function(response) {   
        console.log(response);    
            $("#MId").val(response.Idnino);
            $("#MCui").val(response.cui);
            $("#MPrimerNombre").val(response.primernombre);
            $("#MSegundoNombre").val(response.segundonombre);
            $("#MPrimerApellido").val(response.primerapellido);
            $("#MSegundoApellido").val(response.segundoapellido);
            $("#MFechaNaciemiento").val(response.fechanacimiento);
            $("#MEdad").val(response.edad);
            $("#MSexo").val(response.sexo);
            $("#MComunidad").val(response.lenguaje);
            $("#MDepartamento").prepend("<option value='Auto 0' selected='selected'>"+response.departamento+"</option>");
            $("#MMunicipio").prepend("<option value='Auto 0' selected='selected'>"+response.municipio+"</option>");
            $("#MAldea").prepend("<option value='Auto 0' selected='selected'>"+response.aldea+"</option>");
            $("#MCaserio").prepend("<option value='Auto 0' selected='selected'>"+response.pueblo+"</option>");
            $("#MEFechaNaciemiento").val(response.NacimientoPadre);
            $("#MEPrimerNombre").val(response.Nombre1);
            $("#MESegundoNombre").val(response.Nombre2);
            $("#MEPrimerApellido").val(response.Apellido1);
            $("#MESegundoApellido").val(response.Apellido2);
            $("#MECui").val(response.NoDpi);
            $("#METelefono").val(response.Telefono);
                            
        } //success function
        })// /ajax to fetch product image

    }
    else
    {
        alert("ERROR AL Mostrar DATOS");
    }
}


 function DataNino2 (id)
    {
        if (id) 
        {
            //MANDAR EL ID ELEGIDO MEDIANTE $.AJAX PARA PROCESAR LOS DATOS Y DEVOLVERLOS MEDIANTE JSON
            $.ajax({
            url: 'index.php?view=DatosNinosE',
            type: 'post',
            data: {'IdNino': id},
            dataType: 'json',
            success:function(response) {   
            console.log(response);   
                $("#EId").val(response.Idnino);
                $("#ECui").val(response.cui);
                $("#EPrimerNombre").val(response.primernombre);
                $("#ESegundoNombre").val(response.segundonombre);
                $("#EPrimerApellido").val(response.primerapellido);
                $("#ESegundoApellido").val(response.segundoapellido);
                $("#EFechaNaciemiento").val(response.fechanacimiento);
                $("#EEdad").val(response.edad);
                $("#ESexo").val(response.sexo);
                $("#EComunidad").val(response.lenguaje);
                //ENCARGADO
                                    
            } //success function
            })// /ajax to fetch product image

        }
        else
        {
            alert("ERROR AL Mostrar DATOS");
        }
    }