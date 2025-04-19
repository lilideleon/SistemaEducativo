//CARGAR LOS CHOSEEN

$('#Color').chosen({allow_single_deselect:true, width:"245px", search_contains: true});
        chosen_ajaxify('Color', '?c=Productos&a=CargaColores&keyword=');


window.onload = function () 
{ 
    generarbarcode();
    //genera();
    var CodeBar = $('#CodigoBarra').val();
    var code = $("#Codigo").val();
    try{
        $("#Nombre").on('keyup', function(){
          
            var value = $(this).val().length;
            var Datos = $("#Nombre").val();
            var CodeB = $("#CodigoBarra").val();

            const n = 2;
            var Ultimas = Datos.substring(Datos.length - n);
            
            var Talla = $('#Talla').val ();
            var Palabra = Datos.substring(0,3);
                $('#CodigoBarra').val (code +"/" +Palabra + Ultimas + "-" + Talla); 
                generarbarcode();
        }).keyup();
    }catch(e){};

    try{
        $("#Talla").on('keyup', function(){
            var value = $(this).val().length;
            var Datos = $("#Nombre").val();
            var CodeB = $("#CodigoBarra").val();

            const n = 2;
            var Ultimas = Datos.substring(Datos.length - n);
            
            var Talla = $('#Talla').val ();
            var Palabra = Datos.substring(0,3);
                $('#CodigoBarra').val (code +"/"+ Palabra + Ultimas + "-" + Talla); 
                generarbarcode();
        }).keyup();
    }catch(e){};
}


function generarbarcode()
{
	codigo=$("#CodigoBarra").val();
    if(codigo != '')
    {
        JsBarcode("#barcode",codigo);
        $("#print").show();
    }
    else{
        alertify.set('notifier','position','top-right');
        alertify.error('<font color="#fffff"><i class="fa fa-times"></i> INGRESE CODIGO </font> ');
    }

}

function imprimir(){
	$("#print").printArea();
}


function GuardarColor ()
{
    var Nombrecolor = $("#NameColor").val();
    if (Nombrecolor == '')
    {
        alertify.set('notifier','position','top-right');
        alertify.error('<font color="#fffff"><i class="fa fa-times"></i> INDIQUE EL COLOR </font> ');
    }
    else
    {
        $.ajax({
            url: '?c=Productos&a=RegistrarColor',
            type: 'post',
            data: {
                'NameColor': Nombrecolor
            },
            dataType: 'json',
            success: function(data) 
            {
                alertify.set('notifier','position','top-right');
                alertify.set('notifier','delay', 5);
                alertify.success('<font color="#fffff">COLOR REGISTRADO EXITOSAMENTE.!</font>'); 
                $('#NameColor').val('');
                $('#AddColor').modal('hide');
            },
            error: function(jqXHR, textStatus, error) 
            {
                alert('Error'+error);
            }
        });
    }
}

