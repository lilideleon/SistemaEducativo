
window.onload = function () 
{ 
    Cargar();
    getFecha();
    getProducts();
    cambios();
}

function Cargar ()
{
  $('#Cliente').chosen({allow_single_deselect:true, width:"200px", search_contains: true});
        chosen_ajaxify('Cliente', '?c=Vender&a=getClientes&keyword=');
}


function getFecha()
{
  //obtenemos la fecha actual
  var now = new Date();
  var day =("0"+now.getDate()).slice(-2);
  var month=("0"+(now.getMonth()+1)).slice(-2);
  var today=now.getFullYear()+"-"+(month)+"-"+(day);
  $("#fecha_hora").val(today);
}

function getProducts()
{
  $('#Busqueda').chosen({allow_single_deselect:true, width:"200px", search_contains: true});
        chosen_ajaxify('Busqueda', '?c=Vender&a=getProducts&keyword=');

      
}


function cambios ()
{
  //alert('cambios');
}

