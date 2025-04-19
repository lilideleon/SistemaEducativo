<?php 
@session_start();
?>
<?php if(count($_SESSION['DetalleSanRafael'])>0){?>
	<table class="table" id="DetalleFactura">
	    <thead>
	        <tr>
	        	<th>Id</th>
	            <th>Descripci&oacute;n</th>
	            <th>Cantidad</th>
	            <th>Precio</th>
	            <th width="80px">Descuento</th>
	            <th>Subtotal</th>
				<th>Eliminar</th>
	        </tr>
	    </thead>
	   
	    <tbody>
	    	<?php 
	    	$desc = 0;
	    	$total = 0;
	    	$Canti = 0;

	    	foreach($_SESSION['DetalleSanRafael'] as $k => $detalle){ 
			$total += $detalle['subtotal'];
	    	?>
	        <tr>
	        	<td><?php echo $detalle['id'];?></td>
	            <td><?php echo $detalle['producto']." ".$detalle['color'];?></td>
	            <td><?php echo $detalle['cantidad'];?></td>
	            <td><?php echo $detalle['precio'];?></td>
	            <td><?php echo $detalle['descuento'];?></td>
				<td><?php echo $detalle['subtotal'];?></td>

	            <td><a href="#" id="Elimina" class="btn btn-danger" data-toggle="modal" onclick="EliminarDetalle(<?php echo $detalle['id'];?>)"><i class="fa fa-trash"></i></a></td>
	        </tr>
	        <?php }?>

	       <!-- <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
				<input value="<? echo $total; ?>" class="mdl-textfield__input" type="text"  id="datos1" readonly >
												
			</div> -->

	    </tbody>
	    <tfoot>
	    	 <tr>
	    	 	<td></td>
	    	 	<td></td>
	    	 	<td></td>
	    	 	<td></td>
	        	<td class="text-left"><font color="red"> Total</font></td>
	        	<td><?php echo $total;?></td>
	        
	      </tr>
	    </tfoot>
	</table>
<?php }else{?>





<div class="panel-body"> No hay productos agregados</div>
<?php }?>

<script type="text/javascript">
	//SCRIPT PARA QUITAR UN ELEMENTO DE LA LISTA

	function EliminarDetalle (IdProd)
	{
	  //alert('prueba'+id);
	  var id = IdProd;
	  $.ajax({
	    url: '?c=Vender&a=EliminarDetalle',
	    type: 'post',
	    data: {'id':id},
	    dataType: 'json'
	  }).done(function(data)
	  {
	    if(data.success == true)
	    {
	      alertify.success(data.msj);
	      $(".detalle-producto").load('?c=Vender&a=DetalleVenta');
	    }
	    else
	    {
	      alertify.error(data.msj);
	    }
	  })
	}
</script>