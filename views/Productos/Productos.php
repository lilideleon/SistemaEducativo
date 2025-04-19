<?php
require 'views/Content/header.php';
?>

<?php
if ($_SESSION['TipoUsuario'] == '') {
    print "<script>
        window.location='?c=Login'; 
    </script>";
} else if ($_SESSION['TipoUsuario'] == 1) {
    require 'views/Content/sidebar.php';
} else if ($_SESSION['TipoUsuario'] == 2) {
    require 'views/Content/sidebar2.php';
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
</style>

<div class="content-wrapper">
    <div class="row move-left move-down make-larger">
        <!-- Listado de Productos -->
        <div class="col-12 col-md-6 col-lg-11">
            <div class="card mb-4">
                <center>
                    <h1 class="card-header">Listado de Productos</h1>
                </center>
                <div class="card-body demo-vertical-spacing demo-only-element">
                    <div class="card-body">
                        <small class="text fw-semibold">En este módulo puede visualizar, registrar y modificar productos.</small>
                        <div class="demo-inline-spacing d-flex justify-content-end">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#NuevoProductoModal">Nuevo Producto</button>
                        </div>
                    </div>

                    <!-- Tabla de Productos -->
                    <div class="card">
                        <h5 class="card-header">Listado de Productos</h5>
                        <div class="table-responsive text-nowrap">
                            <table class="table" id="Tabla_Productos">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Nombre</th>
                                        <th>Precio Costo</th>
                                        <th>Precio Venta</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    <!-- Aquí se llenarán los datos dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PARA REGISTRAR NUEVO PRODUCTO -->
<div class="modal fade" id="NuevoProductoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Nuevo Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="NuevoProductoForm">
                    <div class="mb-3">
                        <label for="NombreProducto" class="form-label">Nombre del Producto:</label>
                        <input type="text" id="Nombre" name="Nombre" class="form-control" placeholder="Ej: Bebida, Pechuga, Ala" required>
                    </div>
                    <div class="mb-3">
                        <label for="PrecioCosto" class="form-label">Precio Costo (Q):</label>
                        <input type="number" id="PrecioCosto" name="PrecioCosto" class="form-control" placeholder="Ej: 10.00" min="0" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="PrecioVenta" class="form-label">Precio Venta (Q):</label>
                        <input type="number" id="PrecioVenta" name="PrecioVenta" class="form-control" placeholder="Ej: 15.00" min="0" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="DescripcionProducto" class="form-label">Descripción:</label>
                        <textarea id="DescripcionProducto" name="Descripcion" class="form-control" placeholder="Ej: Producto de alta calidad" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="ImagenProducto" class="form-label">Imagen:</label>
                        <input type="file" id="ImagenProducto" name="Imagen" class="form-control" accept="image/*">
                    </div>
                    <button type="button" class="btn btn-primary" onclick="RegistrarProducto()">Registrar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PARA EDITAR PRODUCTO -->
<div class="modal fade" id="EditarProductoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" id="EditarProductoTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab" aria-controls="info" aria-selected="true">Información</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="imagen-tab" data-bs-toggle="tab" data-bs-target="#imagen" type="button" role="tab" aria-controls="imagen" aria-selected="false">Imagen</button>
                    </li>
                </ul>
                <div class="tab-content" id="EditarProductoTabsContent">
                    <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
                        <form id="EditarProductoForm">
                            <input type="hidden" name="Codigo" id="EditarIdProducto">
                            <div class="mb-3">
                                <label for="EditarNombreProducto" class="form-label">Nombre del Producto:</label>
                                <input type="text" name="Nombre" id="EditarNombreProducto" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="EditarPrecioCosto" class="form-label">Precio Costo (Q):</label>
                                <input type="number" name="PrecioCosto" id="EditarPrecioCosto" class="form-control" min="0" step="0.01" required>
                            </div>
                            <div class="mb-3">
                                <label for="EditarPrecioVenta" class="form-label">Precio Venta (Q):</label>
                                <input type="number" name="PrecioVenta" id="EditarPrecioVenta" class="form-control" min="0" step="0.01" required>
                            </div>
                            <div class="mb-3">
                                <label for="EditarDescripcionProducto" class="form-label">Descripción:</label>
                                <textarea name="Descripcion" id="EditarDescripcionProducto" class="form-control" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="EditarImagenProducto" class="form-label">Imagen:</label>
                                <input type="file" name="Imagen" id="EditarImagenProducto" class="form-control" accept="image/*">
                            </div>
                            <input type="hidden" name="ImagenActual" id="ImagenActual">
                            <button type="button" class="btn btn-primary" onclick="ActualizarProducto()">Actualizar</button>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="imagen" role="tabpanel" aria-labelledby="imagen-tab">
                        <div class="text-center">
                            <img id="ProductoImagenPreview" src="" alt="Imagen del Producto" class="img-fluid" style="max-height: 300px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="js/Custom/Productos.js"></script>

<?php
require 'views/Content/footer.php';
?>