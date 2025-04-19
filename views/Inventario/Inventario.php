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
        <!-- Listado de Inventario -->
        <div class="col-12 col-md-6 col-lg-11">
            <div class="card mb-4">
                <center>
                    <h1 class="card-header">Listado de Inventario</h1>
                </center>
                <div class="card-body demo-vertical-spacing demo-only-element">
                    <div class="card-body">
                        <small class="text fw-semibold">En este módulo puede visualizar, registrar y modificar el inventario.</small>
                        <div class="demo-inline-spacing d-flex justify-content-end">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#NuevoInventarioModal">Nuevo Registro</button>
                        </div>
                    </div>

                    <!-- Tabla de Inventario -->
                    <div class="card">
                        <h5 class="card-header">Listado de Inventario</h5>
                        <div class="table-responsive text-nowrap">
                            <table class="table" id="Tabla_Inventario">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>ProductoId</th>
                                        <th>Cantidad</th>
                                        <th>Fecha</th>
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

<!-- MODAL PARA REGISTRAR NUEVO REGISTRO DE INVENTARIO -->
<div class="modal fade" id="NuevoInventarioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Nuevo Inventario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="NuevoInventarioForm">
                    <div class="mb-3">
                        <label for="ProductoId" class="form-label">Producto:</label>
                        <select id="ProductoId" name="ProductoId" class="form-control" required>
                            <!-- Opciones cargadas dinámicamente -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="Cantidad" class="form-label">Cantidad:</label>
                        <input type="number" id="Cantidad" name="Cantidad" class="form-control" placeholder="Ej: 10.00" min="0"  required>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="RegistrarInventario()">Registrar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PARA EDITAR REGISTRO DE INVENTARIO -->
<div class="modal fade" id="EditarInventarioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Inventario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="EditarInventarioForm">
                    <input type="hidden" name="Id" id="EditarId">
                    <div class="mb-3">
                        <label for="EditarProductoId" class="form-label">Producto:</label>
                        <select name="ProductoId" id="EditarProductoId" class="form-control" required>
                            <!-- Opciones cargadas dinámicamente -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="EditarCantidad" class="form-label">Cantidad:</label>
                        <input type="number" name="Cantidad" id="EditarCantidad" class="form-control" min="0" required>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="ActualizarInventario()">Actualizar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="js/Custom/Inventario.js"></script>
<script>
    // Cargar productos en los selects al abrir los modales
    function CargarProductos() {
        fetch('?c=Inventario&a=ConsultarProductosActivos')
            .then(response => response.json())
            .then(data => {
                const productoSelect = document.getElementById('ProductoId');
                const editarProductoSelect = document.getElementById('EditarProductoId');

                productoSelect.innerHTML = '<option value="">Seleccione un producto</option>';
                editarProductoSelect.innerHTML = '<option value="">Seleccione un producto</option>';

                data.forEach(producto => {
                    const option = `<option value="${producto.IdProducto}">${producto.Nombre}</option>`;
                    productoSelect.innerHTML += option;
                    editarProductoSelect.innerHTML += option;
                });
            })
            .catch(error => console.error('Error al cargar los productos:', error));
    }

    // Llamar a la función al abrir los modales
    $('#NuevoInventarioModal').on('show.bs.modal', CargarProductos);
    $('#EditarInventarioModal').on('show.bs.modal', CargarProductos);
</script>

<?php
require 'views/Content/footer.php';
?>