window.onload = function () 
{ 
    Cargar();
}


//PAGINACION DE LA TABLA DE DATOS

function Cargar ()
{
    var objetoDataTables_personal = $('#Tabla_Pagos').DataTable({
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
        "order": [[ 0, "desc" ]],//Ordenar (columna,orden)
        "bProcessing": true,
        "bServerSide": true,
        dom: 'Bfrtip',//definimos los elementos del control de la tabla
        buttons: [
                  'copyHtml5',
                  'excelHtml5',
                  'csvHtml5',
                  'pdf'
        ],
        "sAjaxSource": "?c=Pagos&a=Tabla"
      
    });
}



function DataFacture(productId) 
{   
    if (productId) 
    {
        //MANDAR EL ID ELEGIDO MEDIANTE $.AJAX PARA PROCESAR LOS DATOS Y DEVOLVERLOS MEDIANTE JSON
        $.ajax({
            url: '?c=Vender&a=DetalleFactura',
            type: 'post',
            data: {productId: productId},
            dataType: 'json',
            success:function(response) {  
            console.log(response); 
            if(response){

            var len = response.length;
            var txt = "";
            if(len > 0){
                for(var i=0;i<len;i++){
                    if(response[i].Nombre && response[i].subtotal){
                        txt += "<tr><td>"+response[i].idventa+"</td><td>"+response[i].Nombre+"</td>"+"<td>"+response[i].cantidad+"</td>"+ "<td>"+response[i].precio_venta+"</td>" +"<td>"+response[i].subtotal+"</td></tr>";
                        $('#Total').val(response[i].total_venta);
                        $('#IdFactura').val(response[i].idventa);
                        $('#Nit').val(response[i].idcliente);
                        $('#FechaFactura').val(response[i].fecha_hora);
                    }
                }
                if(txt != ""){
                    $("#Detalle").empty();
                    $("#Detalle").append("<thead><th width='50px;'>NoFactura</th><th>Producto</th><th width='70px;'>Cantidad</th><th width='70px;'>P.Venta</th><th width='70px;'>SubTotal</th></thead>");
                    $("#Detalle").append(txt).removeClass("hidden");


                }
            }
        }  
                
           
        } //success function

    })// /ajax to fetch product image

    }
    else{
        alertify.set('notifier','position','top-right');
        alertify.error('<font color="#fffff"><i class="fa fa-times"></i> No hay detalle</font> ');
    }
    
}    


var UuIdGeneral;
var IdVentaGeneral;
var IdClienteG;
var fecha_horaG;


function FacturaA(ProductId)
{
    //alert(ProductId);
    $.ajax({
        url: '?c=Vender&a=DetallesAnulacion',
        type: 'post',
        data: {
            'FacturaId': ProductId
        },
        dataType: 'JSON',
        success: function(data) 
        {
            console.log(data+' DATOS');
            IdVentaGeneral = data.Idventa;
            UuIdGeneral = data.Uuid;
            IdClienteG = data.idcliente;
            //alert ('cliente'  + IdClienteG);
            fecha_horaG = data.fecha_hora;
            // alert(data.Idventa+' '+data.Uuid);
        },
        error: function(jqXHR, textStatus, error) 
        {
            alert('Error uno '+error);
        }
    });
}
    
function AnularFactura() 
{
    //alert ('');

    //alert(IdVentaGeneral+' '+UuIdGeneral);
    var numeroDocumentoAAnular = UuIdGeneral;
    var nitEmisor = '17436664';
    var idReceptor = IdClienteG;
    var fechaEmisionDocumentoAnular = fecha_horaG;
    var now = new Date();
    var isoString = now.toISOString();
    
    var DateAnular = new Date (fechaEmisionDocumentoAnular);
    var Isoanular = DateAnular.toISOString();
 
    
    var jsonObject = 
    {
        "anulacion": 
        {
            "datosGenerales": 
            {
                "numeroDocumentoAAnular": numeroDocumentoAAnular,
                "nitEmisor": nitEmisor,
                "idReceptor": idReceptor,
                "fechaEmisionDocumentoAnular": Isoanular,
                "fechaHoraAnulacion": isoString,
                "motivoAnulacion": "Anular Documento"
            }
        }
    };

    console.log(jsonObject);

    $.ajax({
        url: '?c=Vender&a=GenerarJsonAnulacion',
        type: 'post',
        data: {
            'jsonObject': JSON.stringify(jsonObject)
        },
        dataType: 'html',
        success: function(data) 
        {
            console.log(data+' BASE 64 GENERADO');
            ObjetoAfirmar = data;
            console.log(ObjetoAfirmar);
            FirmarA(ObjetoAfirmar);
        },
        error: function(jqXHR, textStatus, error) 
        {
            alert('Error uno '+error);
        }
    }); 
}


function FirmarA (base64)
{
    
    var Firmado;
      $.ajax({
          url: '?c=Vender&a=FirmarAnulacion',
          type: 'post',
          data: {
              'Base64': JSON.stringify(base64)
          },
          dataType: 'html',
          success: function(data) 
          {
              console.log(data);
              Firmado = data;
              CertificarA(Firmado);
          },
          error: function(jqXHR, textStatus, error) 
          {
              alert('Error'+error);
          }
      });
}


function CertificarA (base64)
{
  //  console.log (base64 + 'ESTE SE CERTIFICA');
    
    var objeto = JSON.parse(base64);
    
   // console.log(objeto.xmlSigned);
    
  $.ajax({
      url: '?c=Vender&a=CertificarAnulacion',
      type: 'post',
      data: {
          'Base64': objeto.xmlSigned
      },
      dataType: 'json',
      success: function(data) 
      {
          console.log(JSON.stringify(data) + 'ANULADA DESDE LA SAT');
          anularlocaldb();
      },
      error: function(jqXHR, textStatus, error) 
      {
          alert('Error'+error);
          console.log(error);
      }
  });
  
 
}

function anularlocaldb ()
{
    $.ajax({
      url: '?c=Vender&a=Anulardblocal',
      type: 'post',
      data: {
          'Codigo': IdVentaGeneral
      },
      dataType: 'json',
      success: function(data) 
      {
          console.log(JSON.stringify(data) + 'ANULADA LOCAL');
          alert ('FACTURA ANULADA EXITOSAMENTE')
          window.location.reload();
      },
      error: function(jqXHR, textStatus, error) 
      {
          alert('Error'+error);
          console.log(error);
      }
  });
}

function Eliminar (idrecibo)
{
    $('#ECodigo').val(idrecibo);
}


function EliminarDatos ()
{
    var idrecibo = $('#ECodigo').val();

    if (idrecibo =="")
    {
        alertify.set('notifier','position','top-right');
        alertify.error('<font color="#fffff"><i class="fa fa-times"></i> INDIQUE EL NUMERO DE RECIBO</font> ');
    }
    else
    {
        //alert(idrecibo);
        $.ajax({
            url: '?c=Pagos&a=AnularRecibo',
            type: 'post',
            data: {
                'idrecibo':idrecibo
            },
            dataType: 'json',
            success: function(data) 
            {
                alert('RECIBO ELIMINADO EXITOSAMENTE.');
                window.location.reload();
            },
            error: function(jqXHR, textStatus, error) 
            {
                alert('Error'+error);
            }
        });
    }
}
    
    
    
    
