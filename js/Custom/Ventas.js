/*window.onload = function () 
{ 
    Cargar();
}*/


function obtenerProductos() {
    // Agregar estilo global para las tarjetas si no existe
    if (!document.getElementById('producto-card-style')) {
        const style = document.createElement('style');
        style.id = 'producto-card-style';
        style.innerHTML = `
            .producto-card {
                flex: 1;
                margin: 10px;
                border-radius: 12px;
                padding: 15px;
                text-align: center;
                font-size: 14px;
                background: #fff;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1), 0 4px 8px rgba(0, 0, 0, 0.1);
                transition: transform 0.1s, box-shadow 0.1s, background-color 0.1s;
                cursor: pointer;
                user-select: none;
            }
            .producto-card img {
                width: 100px;
                height: 100px;
                object-fit: cover;
                border-radius: 50%;
                margin-bottom: 10px;
            }
            .producto-card h5 {
                margin: 5px 0;
                font-weight: 500;
                color: #333;
            }
            .producto-card p {
                margin: 0;
                color: #666;
            }
            .producto-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2), 0 8px 16px rgba(0, 0, 0, 0.2);
                background-color:rgba(114, 243, 179, 0.47); /* Verde */
            }
        `;
        document.head.appendChild(style);
    }

    // Usar AJAX en lugar de fetch
    $.ajax({
        url: '?c=Ventas&a=ObtenerProductos',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.success) {
                let productosContainer = document.getElementById('productosContainer');
                productosContainer.innerHTML = '';

                let fila;
                data.productos.forEach((producto, index) => {
                    if (index % 3 === 0) {
                        // Crear una nueva fila cada 3 productos
                        fila = document.createElement('div');
                        fila.className = 'fila-productos';
                        fila.style.display = 'flex';
                        fila.style.justifyContent = 'space-between';
                        productosContainer.appendChild(fila);
                    }

                    let productoCard = `
                        <div class="producto-card" data-id="${producto.IdProducto}" onclick="seleccionarProducto(${producto.IdProducto}, '${producto.Nombre}', ${producto.PrecioVenta}, ${producto.Cantidad})">
                            <img src="${producto.Imagen}" alt="${producto.Nombre}" class="producto-imagen">
                            <h5>${producto.Nombre}</h5>
                            <p>Precio: ${producto.PrecioVenta}</p>
                            <p>Stock: ${producto.Cantidad}</p>
                        </div>
                    `;

                    fila.innerHTML += productoCard;
                });
            } else {
                console.error(data.msj);
            }
        },
        error: function(error) {
            console.error('Error al obtener los productos:', error);
        }
    });
}


/*
function seleccionarProducto(id, nombre, precio, stock) {
    // Verificar si el stock es cero
    if (stock === 0) {
        alertify.error('El producto está agotado y no se puede agregar.');
        return; // Salir de la función si no hay stock
    }

    // Verificar si ya existe este producto en la tabla
    const listaOrdenes = document.getElementById('listaOrdenes');
    const filaExistente = listaOrdenes.querySelector(`tr[data-id='${id}']`);

    if (filaExistente) {
        // Actualizar la cantidad
        const cantidadInput = filaExistente.querySelector('.cantidad');
        cantidadInput.value = parseInt(cantidadInput.value) + 1;

        // Actualizar el total
        const totalCell = filaExistente.querySelector('.total');
        const nuevaCantidad = parseInt(cantidadInput.value);
        totalCell.textContent = (precio * nuevaCantidad).toFixed(2);
    } else {
        // Si el producto no existe, agregar una nueva fila
        const cantidad = 1;
        const total = precio * cantidad;

        // Crear nueva fila con DOM nativo
        const nuevaFila = document.createElement('tr');
        nuevaFila.setAttribute('data-id', id); // Asegurar que el atributo data-id se asigna correctamente

        // Celda nombre
        const tdNombre = document.createElement('td');
        tdNombre.textContent = nombre;
        nuevaFila.appendChild(tdNombre);

        // Celda precio
        const tdPrecio = document.createElement('td');
        tdPrecio.textContent = precio.toFixed(2);
        nuevaFila.appendChild(tdPrecio);

        // Celda cantidad con botones
        const tdCantidad = document.createElement('td');

        const btnMas = document.createElement('button');
        btnMas.className = 'btn btn-success btn-sm';
        btnMas.textContent = '+';
        btnMas.onclick = function() { actualizarCantidad(this, 1, precio); };
        tdCantidad.appendChild(btnMas);

        const inputCantidad = document.createElement('input');
        inputCantidad.type = 'number';
        inputCantidad.className = 'cantidad';
        inputCantidad.value = cantidad;
        inputCantidad.min = 1;
        inputCantidad.style.width = '50px';
        inputCantidad.style.textAlign = 'center';
        inputCantidad.onchange = function() { actualizarCantidadInput(this, precio); };
        tdCantidad.appendChild(inputCantidad);

        const btnMenos = document.createElement('button');
        btnMenos.className = 'btn btn-warning btn-sm';
        btnMenos.textContent = '-';
        btnMenos.onclick = function() { actualizarCantidad(this, -1, precio); };
        tdCantidad.appendChild(btnMenos);

        nuevaFila.appendChild(tdCantidad);

        // Celda total
        const tdTotal = document.createElement('td');
        tdTotal.className = 'total';
        tdTotal.textContent = total.toFixed(2);
        nuevaFila.appendChild(tdTotal);

        // Celda acción (botón eliminar)
        const tdAccion = document.createElement('td');
        const btnEliminar = document.createElement('button');
        btnEliminar.className = 'btn btn-danger btn-sm';
        btnEliminar.textContent = 'x';
        btnEliminar.onclick = function() { eliminarProducto(this); };
        tdAccion.appendChild(btnEliminar);
        nuevaFila.appendChild(tdAccion);

        // Añadir la fila a la tabla
        listaOrdenes.appendChild(nuevaFila);
    }

    // Actualizar el total general
    actualizarTotalGeneral();
}*/

function seleccionarProducto(id, nombre, precio, stock) {
    $.ajax({
        url: '?c=Ventas&a=VerificarCombo',
        method: 'POST',
        data: { idProducto: id },
        dataType: 'json',
        success: function(data) {
            if (!data.permitido) {
                alertify.error(data.msj || 'No se puede vender este producto.');
                return;
            }

            // Si todo está bien, agregar producto a la orden
            agregarProductoAVenta(id, nombre, precio);
        },
        error: function(error) {
            console.error('Error al verificar combo:', error);
            alertify.error('No se pudo verificar el producto.');
        }
    });
}

function agregarProductoAVenta(id, nombre, precio) {
    const listaOrdenes = document.getElementById('listaOrdenes');
    const filaExistente = listaOrdenes.querySelector(`tr[data-id='${id}']`);

    if (filaExistente) {
        const cantidadInput = filaExistente.querySelector('.cantidad');
        cantidadInput.value = parseInt(cantidadInput.value) + 1;

        const totalCell = filaExistente.querySelector('.total');
        const nuevaCantidad = parseInt(cantidadInput.value);
        totalCell.textContent = (precio * nuevaCantidad).toFixed(2);
    } else {
        const cantidad = 1;
        const total = precio * cantidad;

        const nuevaFila = document.createElement('tr');
        nuevaFila.setAttribute('data-id', id);

        const tdNombre = document.createElement('td');
        tdNombre.textContent = nombre;
        nuevaFila.appendChild(tdNombre);

        const tdPrecio = document.createElement('td');
        tdPrecio.textContent = precio.toFixed(2);
        nuevaFila.appendChild(tdPrecio);

        const tdCantidad = document.createElement('td');
        const btnMas = document.createElement('button');
        btnMas.className = 'btn btn-success btn-sm';
        btnMas.textContent = '+';
        btnMas.onclick = function () { actualizarCantidad(this, 1, precio); };
        tdCantidad.appendChild(btnMas);

        const inputCantidad = document.createElement('input');
        inputCantidad.type = 'number';
        inputCantidad.className = 'cantidad';
        inputCantidad.value = cantidad;
        inputCantidad.min = 1;
        inputCantidad.style.width = '50px';
        inputCantidad.style.textAlign = 'center';
        inputCantidad.onchange = function () { actualizarCantidadInput(this, precio); };
        tdCantidad.appendChild(inputCantidad);

        const btnMenos = document.createElement('button');
        btnMenos.className = 'btn btn-warning btn-sm';
        btnMenos.textContent = '-';
        btnMenos.onclick = function () { actualizarCantidad(this, -1, precio); };
        tdCantidad.appendChild(btnMenos);

        nuevaFila.appendChild(tdCantidad);

        const tdTotal = document.createElement('td');
        tdTotal.className = 'total';
        tdTotal.textContent = total.toFixed(2);
        nuevaFila.appendChild(tdTotal);

        const tdAccion = document.createElement('td');
        const btnEliminar = document.createElement('button');
        btnEliminar.className = 'btn btn-danger btn-sm';
        btnEliminar.textContent = 'x';
        btnEliminar.onclick = function () { eliminarProducto(this); };
        tdAccion.appendChild(btnEliminar);
        nuevaFila.appendChild(tdAccion);

        listaOrdenes.appendChild(nuevaFila);
    }

    actualizarTotalGeneral();
}




// Agregar esta función para eliminar productos y actualizar el total
function eliminarProducto(button) {
    button.closest('tr').remove();
    actualizarTotalGeneral();
}

// Modificar funciones de actualización para que también actualicen el total general
function actualizarCantidad(button, cambio, precio) {
    const cantidadInput = button.closest('td').querySelector('.cantidad');
    let cantidad = parseInt(cantidadInput.value) + cambio;

    if (cantidad < 1) return; // Evitar cantidades menores a 1

    cantidadInput.value = cantidad;

    const totalCell = button.closest('tr').querySelector('.total');
    const total = (cantidad * precio).toFixed(2);
    totalCell.textContent = total;
    
    actualizarTotalGeneral();
}

function actualizarCantidadInput(input, precio) {
    let cantidad = parseInt(input.value);

    if (isNaN(cantidad) || cantidad < 1) {
        input.value = 1; // Restablecer a 1 si el valor es inválido
        cantidad = 1;
    }

    const totalCell = input.closest('tr').querySelector('.total');
    const total = (cantidad * precio).toFixed(2);
    totalCell.textContent = total;
    
    actualizarTotalGeneral();
}

// Agregar esta función para calcular el total general de todos los productos
function actualizarTotalGeneral() {
    const totales = document.querySelectorAll('#listaOrdenes .total');
    let totalGeneral = 0;
    
    totales.forEach(cell => {
        totalGeneral += parseFloat(cell.textContent);
    });
    
    // Asumiendo que tienes un elemento para mostrar el total general
    const totalGeneralElement = document.getElementById('totalGeneral');
    if (totalGeneralElement) {
        totalGeneralElement.textContent = totalGeneral.toFixed(2);
    }
}

// funcion para finalizar la orden

function finalizarOrden() {
    // Obtener los datos de la tabla de órdenes
    const filas = document.querySelectorAll('#listaOrdenes tr');
    if (filas.length === 0) {
        alertify.error('No hay productos en la orden.');
        return;
    }

    // Crear un objeto para la factura
    const factura = {
        IdCliente: 1, // Cambiar según el cliente seleccionado
        Detalles: []
    };

    // Recorrer las filas para obtener los detalles
    filas.forEach(fila => {
        const idProducto = fila.getAttribute('data-id');
        const cantidad = fila.querySelector('.cantidad').value;
        const precio = fila.querySelector('td:nth-child(2)').textContent;

        factura.Detalles.push({
            IdArticulo: idProducto,
            Cantidad: cantidad,
            PrecioVenta: precio
        });
    });

    // Enviar la factura al servidor usando AJAX
    $.ajax({
        url: '?c=Ventas&a=GuardarFactura',
        method: 'POST',
        data: { IdCliente: factura.IdCliente },
        dataType: 'json',
        success: function(data) {
            if (data.success) {
                const facturaId = data.facturaId;

                // Enviar los detalles de la factura
                factura.Detalles.forEach(detalle => {
                    detalle.IdFactura = facturaId;
                    $.ajax({
                        url: '?c=Ventas&a=Guardardetallefactura',
                        method: 'POST',
                        data: detalle,
                        dataType: 'json',
                        success: function(detalleData) {
                            if (!detalleData.success) {
                                console.error('Error al guardar el detalle:', detalleData.msj);
                            }
                        }
                    });
                });

                //alertify para preguntar si desea imprimir el ticket

                alertify.confirm('¿Desea imprimir el ticket?', function(e) {
                    if (e) {
                        ImprimirTicket(facturaId);
                    }
                });

                alertify.success('Orden finalizada y factura generada exitosamente.');
                document.getElementById('listaOrdenes').innerHTML = ''; // Limpiar la tabla
                actualizarTotalGeneral();

                // Volver a cargar los productos
                obtenerProductos();
            } else {
                alertify.error('Error al generar la factura: ' + data.msj);
            }
        },
        error: function(error) {
            console.error('Error al finalizar la orden:', error);
            alertify.error('Ocurrió un error al finalizar la orden.');
        }
    });
}

function ImprimirTicket(idFac) {

    //obtener el id de la factura

    var idFactura = idFac;

    $.ajax({
        url: '?c=ventas&a=ImprimirTicket',
        type: 'POST',
        data: { id: idFactura },
        dataType: 'json',
        success: function(response) {
            console.log(response);
            if (response.status === 'ok') {
                window.print();
            } else {
                alert('Error al imprimir el ticket: ' + response.error);
            }
        },
        error: function(xhr, status, error) {
            alert('Error al realizar la solicitud: ' + error);
        }
    });
}

