function PesoTalla (id)
{
    if(id)
    {
        $(location).attr('href','index.php?view=MonPT&id='+id);

    }

}


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
    "sAjaxSource": "index.php?view=FetchPesos"
  
});
