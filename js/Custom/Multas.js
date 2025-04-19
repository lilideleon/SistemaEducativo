window.onload = function () 
{ 
    Cargar();
}


//PAGINACION DE LA TABLA DE DATOS

function Cargar ()
{
    $.ajax({
        url: '?c=Multas&a=Tabla', // Cambia 'tu_script.php' por la ruta de tu script PHP que contiene el método Tabla()
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log (response);
            // Inserta los datos en la tabla
            $('#Tabla_Usuarios').DataTable({
                data: response.data,
                columns: [
                    { data: 'id' },
                    { data: 'dpi' },
                    { data: 'Primer_Nombre' },
                    { data: 'Primer_Apellido' },
                    { data: 'sector' },
                    { data: 'acciones' }
                ]
            });
        },
        error: function(xhr, status, error) {
            // Maneja errores si es necesario
            console.error(xhr.responseText);
        }
    });
}

//FUNCION PARA ALMACENAR DATOS


function GuardarDatos ()
{
    var Cui = $('#Dpi').val();
    var PrimerNombre = $('#PrimerNombre').val();
    var SegundoNombre = $('#SegundoNombre').val();
    var PrimerApellido = $('#PrimerApellido').val();
    var SegundoApellido = $('#SegundoApellido').val();
    var Correo = $('#Correo').val();
    var Perfil = $('#Perfil').val();
    var Usuario = $('#Usuario').val();
    var Contra = $('#Contraseña').val();
    var Huellas = $('#TipoUsuario').val();
    var Aldea = $('#Aldea').val();
    var Sector = $('#Sector').val();
    var Estado = $('#Estado').val();
  


    if (Cui=="" || PrimerNombre=="" || PrimerApellido==""  )
    {
        alertify.set('notifier','position','top-right');
        alertify.error('<font color="#fffff"><i class="fa fa-times"></i> LLENE LOS CAMPOS</font> ');
    }
    else
    {

        var formData = new FormData();
        formData.append('Dpi', Cui);
        formData.append('Primer_Nombre', PrimerNombre);
        formData.append('Segundo_Nombre', SegundoNombre);
        formData.append('Primer_Apellido', PrimerApellido);
        formData.append('Segundo_Apellido', SegundoApellido);
        formData.append('Correo', Correo);
        formData.append('Perfil', Perfil);
        formData.append('Usuario', Usuario);
        formData.append('Contraseña', Contra);
        formData.append('Huellas', Huellas);
        formData.append('Aldea', Aldea);
        formData.append('Sector', Sector);
        formData.append('Estado', Estado);
        formData.append('foto', $('#foto')[0].files[0]);

        $.ajax({
            url: '?c=Usuarios&a=Agregar',
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


//MOSTRAR LA INFO DE LOS USUARIOS


function DatosUsuario (id)
{
    if (id) 
    {
        //MANDAR EL ID ELEGIDO MEDIANTE $.AJAX PARA PROCESAR LOS DATOS Y DEVOLVERLOS MEDIANTE JSON
        $.ajax({
        url: '?c=Usuarios&a=getDatos',
        type: 'post',
        data: {'Id': id},
        dataType: 'json',
        success:function(response) {   
        console.log(response);    
        var usuario = response[0];
            $("#Codigo").val(usuario.Id);
            $("#ECodigo").val(usuario.Id);
            $("#EDpi").val(usuario.Dpi);
            $("#EPrimerNombre").val(usuario.Primer_Nombre);
            $("#ESegundoNombre").val(usuario.Segundo_Nombre);
            $("#EPrimerApellido").val(usuario.Primer_Apellido);
            $("#ESegundoApellido").val(usuario.Segundo_Apellido);
            $("#ECorreo").val(usuario.Correo);
            $("#EUsuario").val(usuario.Usuario);
            $("#EContraseña").val(usuario.Contraseña);
            $("#EHuellas").val(usuario.Huellas);
            $("#ETipoUsuario").val(usuario.Perfil);       
            $("#EAldea").val(usuario.Aldea).trigger('change');   
            $("#ESector").val(usuario.Sector).trigger('change');     
  
            if (response.imagen) {
                $("#EimagenMostrada").attr('src', response.imagen);
            }   
        } //success function
        })// /ajax to fetch product image

    }
    else
    {
        alert('error');
        alert("ERROR AL Mostrar DATOS");
    }
}



function Multas ()
{
    
}