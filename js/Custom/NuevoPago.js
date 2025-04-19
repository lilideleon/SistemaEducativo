
window.onload = function()
{
   getDateToday();
   Cargar();
   Seleccionado();
   Seleccionado2 ();
   cargarMeses();
   estadoFila ();
   //UsuarioElegido();
   //CargaAnios();
   consultarAnio();
   deshabilitarTodos();
}


function deudas()
{
    var Asociado = $('#Asociado').val ();
    
    if (Asociado == '')
    {
        alert ("SELECCIONE EL SOCIADO");
    }
    else
    {
        $.ajax({
            url: '?c=Pagos&a=deudas',
            type: 'post',
            data: {Id: Asociado},
            dataType: 'json',
            success: function(response) {        
                console.log(response);
                        // Limpiamos el cuerpo de la tabla
                var tbody = $('#miModal2 .table tbody');
                tbody.empty();

                // Iteramos a través de la respuesta y construimos las filas
                $.each(response, function(index, item) {
                    var tr = $('<tr>');
                    tr.append('<td>' + item.Anio + '</td>');
                    tr.append('<td>' + item.Mes + '</td>');
                    tr.append('<td>' + 'SERVICIO DE AGUA ' + '</td>');
                    tr.append('<td>' + '35' + '</td>');
                    tbody.append(tr);
                });

                // Si la respuesta contiene más de dos ítems, agregar fila adicional
                if (response.length > 3) {
                    var trExtra = $('<tr>');
                    trExtra.append('<td>' + '--' + '</td>');  // Cambia 'VALOR FIJO' por lo que necesites
                    trExtra.append('<td>' + '--' + '</td>');
                    trExtra.append('<td>' + 'MULTA DE PAGO ATRASADO' + '</td>');
                    trExtra.append('<td>' + '100' + '</td>');
                    tbody.append(trExtra);
                }

            },
            error: function(jqXHR, textStatus, error) {
                alert('Error' + error);
            }
        });
        deudasporservicio();
    }
}



function deudasporservicio()
{
    var Asociado = $('#Asociado').val ();
    //alert ('prueba');

        $.ajax({
            url: '?c=Pagos&a=deudasporservicio',
            type: 'post',
            data: {Id: Asociado},
            dataType: 'json',
            success: function(response) {        
                console.log(response);
                        // Limpiamos el cuerpo de la tabla
                var tbody = $('#miModal2 .table tbody');

                // Iteramos a través de la respuesta y construimos las filas
                $.each(response, function(index, item) {
                    var tr = $('<tr>');
                    tr.append('<td>' + "C." + item.Cantidad + '</td>');
                    tr.append('<td>' + "M." + item.Monto + '</td>');
                    tr.append('<td>' + item.Servicio + '</td>');
                    tr.append('<td>' + (item.Monto * item.Cantidad) + '</td>');
                    tbody.append(tr);
                });

            },
            error: function(jqXHR, textStatus, error) {
                alert('Error' + error);
            }
        });
}


function CargaAnios(idAsociado) {
    limpiarCombo();
    var Asociado = idAsociado;

    $.ajax({
        url: '?c=Pagos&a=getAnioIngreso',
        type: 'post',
        data: {Asociado: Asociado},
        dataType: 'json',
        success: function(response) {        
            console.log(response);
            var AnioInicial = response[0].AnioContratacion;
            //alert(AnioInicial);

            // Obtiene el año actual
            var yearNow = new Date().getFullYear();

            // Recorre desde el año 2000 al 2100
            for (var i = AnioInicial; i <= 2050; i++) {
                $('#selectAnios').append($('<option>', {
                    value: i,
                    text: i,
                    selected: (i === yearNow)  // Si el año del bucle coincide con el año actual, marca la opción como seleccionada
                }));
            }
        },
        error: function(jqXHR, textStatus, error) {
            alert('Error' + error);
        }
    });
    CargaMesPrimerPago(idAsociado);
}

function CargaMesPrimerPago(idAsociado) {
    var Asociado = idAsociado;
    $.ajax({
        url: '?c=Pagos&a=getFechaPrimerPago',
        type: 'post',
        data: {Asociado: Asociado},
        dataType: 'json',
        success: function(response) {        
            console.log(response);
            var MesPrimerPago = response[0].MesPrimerPago;
        },
        error: function(jqXHR, textStatus, error) {
            alert('Error' + error);
        }
    });
}

function limpiarCombo() {
    $('#selectAnios').empty();
}

function consultarAnio ()
{
  $('#selectAnios').on('mouseup', function() 
  {
    limpiarTabla();
    var Asoc = $('#Asociado').val();
    var selectedValue = $(this).val();   
    //alert(Asoc + ', ' + selectedValue);
     MensualidadPorAnio(Asoc,selectedValue);
  });

}

function MensualidadPorAnio(IdUser,Anio)
{
  //alert(IdUser+ ' ' +Anio);
  desmarcarTodos();
  const fechaActual = new Date();
  var Usuario = IdUser;
  var Servicio = $("#Servicio").val();


  const meses = [
      "", // Puesto que los meses empiezan desde 1, dejamos un espacio vacío en el índice 0
      "Enero",
      "Febrero",
      "Marzo",
      "Abril",
      "Mayo",
      "Junio",
      "Julio",
      "Agosto",
      "Septiembre",
      "Octubre",
      "Noviembre",
      "Diciembre"
  ];


   //alert('entre' + IdUser + ' '+Anio+ ' '+Servicio);
    if (Usuario) {
        //MANDAR EL ID ELEGIDO MEDIANTE $.AJAX PARA PROCESAR LOS DATOS Y DEVOLVERLOS MEDIANTE JSON
        $.ajax({
            url: '?c=Pagos&a=getMensualidades',
            type: 'post',
            data: {Usuario: Usuario, Anio:Anio, Servicio:Servicio},
            dataType: 'json',
            success:function(response) {        
                console.log(response);
 
                for(let entry of response) {
                  const mesNombre = meses[parseInt(entry.Mes)];
                  const checkbox = $(`input.filaCheckbox[value="${mesNombre}"]`);
                  if(checkbox.length) {
                      checkbox.prop('checked', true).trigger('change'); // Aquí se dispara el evento change
                  }
              }

                // Luego, llama a la función estadoFila para colorear las filas según los checkboxes
                estadoFila();
            } //success function
            ,error: function(jqXHR, textStatus, error) 
            {
                alert('Error'+error);
            }

        })//  

    }else{
        alert("ERROR AL MOSTRAR DATOS PRIMERO SELECCIONE EL SERVICIO Y EL ASOCIADO ");
    }
}



    function obtenerCantidadCheckboxesMarcados() {
        var checkboxes = document.getElementsByClassName('filaCheckbox');
        var cantidadMarcados = 0;

        // Iterar sobre los checkboxes y contar los marcados
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].checked) {
                cantidadMarcados++;
            }
        }

        // Devolver la cantidad de checkboxes marcados como resultado
        return cantidadMarcados;
    }



function cargarMeses() {
  var currentID = 0;
  var wasChangedByUser = false;
  var servicio = $('#Servicio').val ();

  // Detectar el clic del usuario para saber que el próximo cambio será causado por el usuario
  $("input[type='checkbox']").on('click', function() {
      wasChangedByUser = true;
      servicio = $('#Servicio').val();
      console.log (servicio);
  });

  $("input[type='checkbox']").change(function() {
      if (wasChangedByUser) {
           var resultado = obtenerCantidadCheckboxesMarcados();
            console.log(resultado);
            console.log ('test');
            currentID = resultado;
          if ($(this).prop('checked')) { 
              //currentID++;
              currentID = resultado+1;
              console.log(currentID);
              $(this).data('associatedID', currentID);
              var mesSeleccionado = $(this).val();   
              var fila = `
                  <tr>
                      <td>${servicio}</td>  
                      <td>${mesSeleccionado}</td>   
                      <td>1</td>   
                      <td>35</td>  <!-- Subtotal: 35 -->
                  </tr>
              `;

              // Agrega la fila específicamente a la tabla con id "tablaDetalles"
              $('#tablaDetalles tbody').append(fila);
          } else {
              // Si el checkbox ha sido desmarcado
              var mesDeseleccionado = $(this).val();

              // Elimina la fila correspondiente del mes desmarcado
              $("#tablaDetalles tbody tr").each(function() {
                  if ($(this).find("td:eq(1)").text() === mesDeseleccionado) {
                      $(this).remove();
                  }
              });
          }

          actualizarTotal();
          estadoFila();

          // Resetear la variable para la próxima vez
          wasChangedByUser = false;
      }
  });
}



function actualizarTotal() {

  var total = 0;
  // Sumar todos los subtotales de la tabla
  $("#tablaDetalles tbody tr").each(function() {
      total += parseFloat($(this).find("td:eq(3)").text());  // Asumiendo que el subtotal está en la cuarta columna (índice 3)
  });
  // Actualizar el total en el pie de página
  $("#totalSuma").text(total.toFixed(2));  // Redondear a 2 decimales
}

function estadoFila ()
{

    $(".filaCheckbox").each(function() {
      var $fila = $(this).closest("tr");
      if($(this).prop("checked")) {
          $fila.addClass("table-success");
      } else {
          $fila.addClass("table-danger");
      }
  });

  $(".filaCheckbox").on("change", function() {
    var $fila = $(this).closest("tr");
    if($(this).prop("checked")) {
        $fila.removeClass("table-danger").addClass("table-success");
    } else {
        $fila.removeClass("table-success").addClass("table-danger");
    }
});
habilitarTodos();

$('#TablaMeses .filaCheckbox:checked').each(function() {
    console.log("Deshabilitando checkbox:", $(this).val());
    $(this).attr('disabled', 'disabled');
});

}

function habilitarTodos() {
    $('#TablaMeses .filaCheckbox').removeAttr('disabled');
}

function deshabilitarTodos() {
    $('#TablaMeses .filaCheckbox').attr('disabled', 'disabled');
}

function Cargar ()
{
  $('#Asociado').chosen({allow_single_deselect:true, width:"250px", search_contains: true});
        chosen_ajaxify('Asociado', '?c=Pagos&a=getClientes&keyword=');

  $('#Servicio').chosen({allow_single_deselect:true, width:"250px", search_contains: true});
    chosen_ajaxify('Servicio', '?c=Pagos&a=getServicio&keyword=');
}

function Seleccionado ()
{
  $("#Asociado").chosen().change(function(event, params) {
     limpiarTabla();
     getDatosUsuario(params.selected);
     CargaAnios(params.selected);
  });

}

function Seleccionado2 ()
{
  $("#Servicio").chosen().change(function(event, params) {
     getMonto(params.selected);
  });
}

function getMonto(IdServicio)
{
  if (IdServicio) {
 
    //MANDAR EL ID ELEGIDO MEDIANTE $.AJAX PARA PROCESAR LOS DATOS Y DEVOLVERLOS MEDIANTE JSON
    $.ajax({
        url: '?c=Pagos&a=getMonto',
        type: 'post',
        data: {IdServicio: IdServicio},
        dataType: 'json',
        success:function(response) {        
            console.log(response);
            $("#Monto").val(response[0].Monto);
            $("#Cantidad").val(response[0].Cantidad);

        } //success function
        ,error: function(jqXHR, textStatus, error) 
        {
            alert('Error'+error);
        }

    })// /ajax to fetch product image

  }else{
      alert("ERROR AL Mostrar DATOS");
  }
}

function getDatosUsuario(Id)
{
  if (Id) {
 
    //MANDAR EL ID ELEGIDO MEDIANTE $.AJAX PARA PROCESAR LOS DATOS Y DEVOLVERLOS MEDIANTE JSON
    $.ajax({
        url: '?c=Pagos&a=getDatosUser',
        type: 'post',
        data: {Id: Id},
        dataType: 'json',
        success:function(response) {        
            console.log(response);
            $("#Id").val(response[0].Id);
            $("#Dpi").val(response[0].Sector);
            $("#NombreCliente").val(response[0].Nombre);
        } //success function
        ,error: function(jqXHR, textStatus, error) 
        {
            alert('Error'+error);
        }

    })// /ajax to fetch product image

  }else{
      alert("ERROR AL Mostrar DATOS");
  }
}

//AL SELECCIONAR UN PRODUCTO DEVOLVER LA INFORMACION
function Cambios()
{
  //alert('cambios');

  //AL SELECCIONAR UN PRODUCTO DEVOLVER LA INFORMACION

        $('select[name=Busqueda]').change(function()
        {
          var IdProd = $('#Busqueda').val();
          DataProduct_2(IdProd);
        });
}

function getDateToday ()
{
    var fecha = new Date(); //Fecha actual
    var mes = fecha.getMonth()+1; //obteniendo mes
    var dia = fecha.getDate(); //obteniendo dia
    var ano = fecha.getFullYear(); //obteniendo año
    if(dia<10)
      dia='0'+dia; //agrega cero si el menor de 10
    if(mes<10)
      mes='0'+mes //agrega cero si el menor de 10
    document.getElementById('fecha_hora').value=ano+"-"+mes+"-"+dia;
}

function UsuarioElegido ()
{
  $("#Asociado").on('change', function(evt, params) {
      MensualidadPorUsuario(params.selected);
  });
}

function MensualidadPorUsuario(IdUser)
{
  desmarcarTodos();
  const fechaActual = new Date();
  const añoActual = fechaActual.getFullYear();
  var Usuario = IdUser;
  var Anio = añoActual;
  var Servicio = $("#Servicio").val();


  const meses = [
      "", // Puesto que los meses empiezan desde 1, dejamos un espacio vacío en el índice 0
      "Enero",
      "Febrero",
      "Marzo",
      "Abril",
      "Mayo",
      "Junio",
      "Julio",
      "Agosto",
      "Septiembre",
      "Octubre",
      "Noviembre",
      "Diciembre"
  ];


   //alert('entre' + IdUser + ' '+Anio+ ' '+Servicio);
    if (Usuario) {
        //MANDAR EL ID ELEGIDO MEDIANTE $.AJAX PARA PROCESAR LOS DATOS Y DEVOLVERLOS MEDIANTE JSON
        $.ajax({
            url: '?c=Pagos&a=getMensualidades',
            type: 'post',
            data: {Usuario: Usuario, Anio:Anio, Servicio:Servicio},
            dataType: 'json',
            success:function(response) {        
                console.log(response);
 
                for(let entry of response) {
                  const mesNombre = meses[parseInt(entry.Mes)];
                  const checkbox = $(`input.filaCheckbox[value="${mesNombre}"]`);
                  if(checkbox.length) {
                      checkbox.prop('checked', true).trigger('change'); // Aquí se dispara el evento change
                  }
              }

                // Luego, llama a la función estadoFila para colorear las filas según los checkboxes
                estadoFila();
            } //success function
            ,error: function(jqXHR, textStatus, error) 
            {
                alert('Error'+error);
            }

        })//  

    }else{
        alert("ERROR AL Mostrar DATOS");
    }
}

function desmarcarTodos() {
  $(".filaCheckbox").prop("checked", false);
}

function limpiarTabla() {
  $("#tablaDetalles tbody tr").remove();

  $("#totalSuma").text('0');
}

function Actualizar ()
{
  var Asociado = $('#Asociado').val ();
  var Anio = $("#selectAnios").val();
  limpiarTabla();
  MensualidadPorAnio(Asociado,Anio);
}

//LEE CODIGO DE BARRA

function LeerCodigo ()
{
  Quagga.init({
            inputStream: {
                name: "Live",
                type: "LiveStream",
                target: document.querySelector('#camera')    // Or '#yourElement' (optional)
            },
            decoder: {
                readers: ["code_128_reader"]
            }
        }, function (err) {
            if (err) {
                console.log(err);
                return
            }
            console.log("Initialization finished. Ready to start");
            Quagga.start();
        });

        Quagga.onDetected(function (data) {
            console.log(data.codeResult.code);
            document.querySelector('#resultado').innerText = data.codeResult.code;
        });
}

/*
function Abonar() {

    $('#CantidadMulta').prop('disabled', true);

    $('#miModal').modal('hide');


    var Cantidad = $('#CantAbonada').val();
    var Monto = $('#Monto').val();
    var Servicio = $('#Servicio').val();

    $.ajax({
        url: '?c=Pagos&a=getDescripcion',
        type: 'post',
        data: {Servicio: Servicio},
        dataType: 'json',
        success: function(response) {        
            console.log(response);
            var Desc = response[0].Servicio;
            var idserv = response[0].Id;

            // Crear una nueva fila con los valores obtenidos
            var newRow = `
                <tr>
                    <td>${idserv}</td>
                    <td>${Desc}</td>
                    <td>${Monto}</td>
                    <td>${Monto * Cantidad}</td>
                </tr>
            `;

            // Agregar la nueva fila a la tabla
            $('#tablaDetalles tbody').append(newRow);

            // Actualizar el total en el pie de la tabla
            actualizarTotal();

            console.log ('entre');

        },
        error: function(jqXHR, textStatus, error) {
            alert('Error' + error);
        }
    });
}*/


function actualizarTotal() {
    var total = 0;

    // Iterar sobre todas las filas en el cuerpo de la tabla para sumar los subtotales
    $('#tablaDetalles tbody tr').each(function() {
        total += parseFloat($(this).find('td').eq(3).text()); // Selecciona la 4ta celda (Subtotal) y suma al total
    });

    // Actualizar el total en el pie de la tabla
    $('#totalSuma').text(total.toFixed(2)); // .toFixed(2) es para mostrar el resultado con 2 decimales
}

function Pagar() {
    var datosTabla = [];
    var usuario = $('#Asociado').val();  
    var fecha = $('#fecha_hora').val();
    var anio = $('#selectAnios').val();


    $('#tablaDetalles tbody tr').each(function() {
        var id = $(this).find('td').eq(0).text();
        var descripcion = $(this).find('td').eq(1).text();
        var cantidad = $(this).find('td').eq(2).text();
        var subtotal = $(this).find('td').eq(3).text();

        //alert(subtotal);

        var datoFila = {
            "id": id,
            "descripcion": descripcion,
            "cantidad": cantidad,
            "subtotal": subtotal
        };

        datosTabla.push(datoFila);

    });


    // Envía los datos recopilados al servidor
    $.ajax({
        url: '?c=Pagos&a=Pagar',
        type: 'POST',
        data: {
            tabla: datosTabla,  // Datos de la tabla
            usuario: usuario,   // Variable adicional
            fecha: fecha,        // Otra variable adicional
            anio : anio
        },
        dataType: 'json',
        success: function(response) {
           // alert('Pagos realizados con éxito!');
           // window.location.href = '?c=Pagos';

           $.ajax({
                url: '?c=Pagos&a=getUltimoPago',
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                        console.log (response);
                     //alert (response[0].Id);
                     alert ('PAGO REGISTRADO EXITOSAMENTE.');
                     window.location.href = '?c=Pagos&a=PrintComprobante&id=' + response[0].Id;
                },
                error: function(error) {
                    alert('Ocurrió un error al guardar los datos!');
                    console.log (error);
                }
            });
        },
        error: function(error) {
            alert('Ocurrió un error al guardar los datos!');
            console.log (error);
        }
    });
}

