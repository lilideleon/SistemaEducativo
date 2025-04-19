<? 
    require 'views/Content/header.php';
    session_start();
?>


<?php 
                           
                           if($_SESSION['TipoUsuario'] == '')
                           {
                               print "<script>
                                   window.location='?c=Login'; 
                                   </script>";
                           }
                           else if($_SESSION['TipoUsuario'] == 2)
                           {
                           // @session_start();
                               require 'views/Content/sidebar.php'; 
                                   print "<script>
                                       console.log($TipoUsuario);
                                   </script>";
                           }
                           else if($_SESSION['TipoUsuario'] == 3)
                           {
                               require 'views/Content/sidebar2.php'; 
                                   print "<script>
                                       console.log($TipoUsuario);
                                   </script>";
                           }
                           else if($_SESSION['TipoUsuario'] == 4)
                           {
                               require 'views/Content/sidebar3.php'; 
                                   print "<script>
                                       console.log($TipoUsuario);
                                   </script>";
                           }
                       
                       
                       ?>


<style>
    .move-left {
        position: relative; 
        left: 0; 
    }

    .move-down {
        margin-top: 15px;
    }

    .make-larger {
        width: 100%; 
        max-width: 1950px; /* Permite crecer hasta 1950px, pero no más que eso */
        height: auto; /* Se ajusta según el contenido */
        max-height: 800px; /* Altura máxima de 800px */
    }

    /* Estilos para pantallas con un ancho mínimo de 1024px (ejemplo para pantallas grandes) */
    @media (min-width: 1024px) {
        .move-left {
            left: 28px;
        }
    }

    #TablaMeses tr {
        height: 15px;  /* O el alto que prefieras */
    }

    #TablaMeses td, #TablaMeses th {
        padding: 5px 10px;  /* Ajusta como prefieras */
    }


    #tablaDetalles tr {
        height: 15px;  /* O el alto que prefieras */
    }

    #tablaDetalles td, #tablaDetalles th {
        padding: 5px 10px;  /* Ajusta como prefieras */
    }

</style>


<div class="content-wrapper">
    <div class="row move-left move-down make-larger">
        <!-- Sección de categorías y productos -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>🍗 Menú de Pollo Frito</h5>
                </div>
                <div class="card-body">
                    <!-- Categorías -->
                    <div class="row mb-4">
                        <button class="btn btn-primary col-md-2 mx-2">Pollo</button>
                        <button class="btn btn-primary col-md-2 mx-2">Bebidas</button>
                        <button class="btn btn-primary col-md-2 mx-2">Complementos</button>
                        <button class="btn btn-primary col-md-2 mx-2">Postres</button>
                    </div>
                    <!-- Productos -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <button class="btn btn-warning w-100" onclick="seleccionarProducto('Cuadriles', 9.00)">
                                <img src="https://i0.wp.com/pollosuperrapidito.com.gt/wp-content/uploads/2019/11/cuadril.jpg?fit=1200%2C1200&ssl=1" alt="Cuadriles" class="img-fluid rounded mb-2" style="height: 100px;">
                                Cuadriles
                            </button>
                        </div>
                        <div class="col-md-6 mb-3">
                            <button class="btn btn-warning w-100" onclick="seleccionarProducto('Alas', 20.00)">
                                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRbRjZQmLDseKudRlSBCYECGBAUgB1LdIsxAA&s" alt="Alas" class="img-fluid rounded mb-2" style="height: 100px;">
                                Alas
                            </button>
                        </div>
                        <div class="col-md-6 mb-3">
                            <button class="btn btn-warning w-100" onclick="seleccionarProducto('Pechugas', 11.00)">
                                <img src="https://i0.wp.com/pollosuperrapidito.com.gt/wp-content/uploads/2019/11/pechuga.jpg?fit=1200%2C1200&ssl=1" alt="Pechugas" class="img-fluid rounded mb-2" style="height: 100px;">
                                Pechugas
                            </button>
                        </div>
                        <div class="col-md-6 mb-3">
                            <button class="btn btn-warning w-100" onclick="seleccionarProducto('Bebidas', 10.00)">
                                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTbsVGICaC3d2yobyre_4ZZBAG-o3Jy9uZWqg&s" alt="Bebidas" class="img-fluid rounded mb-2" style="height: 100px;">
                                Bebidas
                            </button>
                        </div>
                    </div>
                    <!-- Cantidad y botón de agregar -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="cantidad">Cantidad:</label>
                            <input type="number" id="cantidad" class="form-control" value="1" min="1">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button class="btn btn-success w-100" onclick="agregarProducto()">Agregar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de lista de órdenes -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>📋 Lista de Órdenes</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Precio</th>
                                <th>Cant</th>
                                <th>Total (Q)</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody id="listaOrdenes">
                            <!-- Orden ficticia -->
                            <tr>
                                <td>Cuadriles</td>
                                <td>25.00</td>
                                <td><input type="number" value="2" min="1" class="form-control" style="width: 70px;"></td>
                                <td>50.00</td>
                                <td><button class="btn btn-danger btn-sm" onclick="eliminarProducto(this)">❌</button></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="text-right">
                        <button class="btn btn-success" onclick="finalizarOrden()">Finalizar Orden</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let productoSeleccionado = null;
    let precioSeleccionado = 0;

    // Función para seleccionar un producto
    function seleccionarProducto(nombre, precio) {
        productoSeleccionado = nombre;
        precioSeleccionado = precio;

        // Mostrar el producto seleccionado en la consola (opcional)
        console.log(`Producto seleccionado: ${nombre} - Q${precio.toFixed(2)}`);

        // Opcional: Mostrar el producto seleccionado en un elemento HTML
        const productoInfo = document.getElementById('productoSeleccionado');
        if (productoInfo) {
            productoInfo.textContent = `Producto seleccionado: ${nombre} - Q${precio.toFixed(2)}`;
        }
    }

    // Función para agregar un producto a la lista de órdenes
    function agregarProducto() {
        const cantidad = document.getElementById('cantidad').value;

        if (!productoSeleccionado) {
            alert("Por favor, seleccione un producto.");
            return;
        }

        const total = cantidad * precioSeleccionado;

        const nuevaFila = `
            <tr>
                <td>${productoSeleccionado}</td>
                <td>${precioSeleccionado.toFixed(2)}</td>
                <td><input type="number" value="${cantidad}" min="1" class="form-control" style="width: 70px;"></td>
                <td>${total.toFixed(2)}</td>
                <td><button class="btn btn-danger btn-sm" onclick="eliminarProducto(this)">❌</button></td>
            </tr>
        `;

        document.getElementById('listaOrdenes').insertAdjacentHTML('beforeend', nuevaFila);
    }

    // Función para eliminar un producto de la lista de órdenes
    function eliminarProducto(boton) {
        const fila = boton.parentElement.parentElement;
        fila.remove();
    }

    // Función para finalizar la orden y generar el ticket
    function finalizarOrden() {
        const listaOrdenes = document.getElementById('listaOrdenes').children;
        if (listaOrdenes.length === 0) {
            alert("No hay productos en la orden.");
            return;
        }

        // Crear el contenido del ticket
        let ticketContent = `
            <div style="font-family: Arial, sans-serif; width: 300px;">
                <h3 style="text-align: center;">🍗 Comida Rápida</h3>
                <p style="text-align: center;">Gracias por su compra</p>
                <hr>
                <table style="width: 100%; font-size: 12px;">
                    <thead>
                        <tr>
                            <th style="text-align: left;">Producto</th>
                            <th style="text-align: right;">Cant</th>
                            <th style="text-align: right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        let totalOrden = 0;

        // Recorrer la lista de órdenes y agregar los productos al ticket
        for (let i = 0; i < listaOrdenes.length; i++) {
            const fila = listaOrdenes[i];
            const producto = fila.children[0].textContent;
            const cantidad = fila.children[2].children[0].value;
            const total = fila.children[3].textContent;

            ticketContent += `
                <tr>
                    <td>${producto}</td>
                    <td style="text-align: right;">${cantidad}</td>
                    <td style="text-align: right;">Q${total}</td>
                </tr>
            `;

            totalOrden += parseFloat(total);
        }

        ticketContent += `
                    </tbody>
                </table>
                <hr>
                <p style="text-align: right; font-size: 14px;"><strong>Total: Q${totalOrden.toFixed(2)}</strong></p>
                <hr>
                <p style="text-align: center; font-size: 12px;">¡Vuelva pronto!</p>
            </div>
        `;

        // Abrir una nueva ventana para imprimir el ticket
        const ticketWindow = window.open('', '_blank', 'width=300,height=600');
        ticketWindow.document.open();
        ticketWindow.document.write(`
            <html>
                <head>
                    <title>Ticket</title>
                </head>
                <body onload="window.print(); window.close();">
                    ${ticketContent}
                </body>
            </html>
        `);
        ticketWindow.document.close();
    }
</script>

<script src="js/quagga.min.js"></script>
<script type="text/javascript" src="js/Custom/NuevoPago.js"></script> 

<?
    require 'views/Content/footer.php';

?>