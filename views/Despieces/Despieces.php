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
        <!-- Listado de Despieces -->
        <div class="col-12 col-md-6 col-lg-11">
            <div class="card mb-4">
                <center>
                    <h1 class="card-header">Listado de Despieces</h1>
                </center>
                <div class="card-body demo-vertical-spacing demo-only-element">
                    <div class="card-body">
                        <small class="text fw-semibold">En este módulo puede visualizar, registrar y modificar despieces.</small>
                        <div class="demo-inline-spacing d-flex justify-content-end">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#NuevoDespieceModal">Nuevo Despiece</button>
                        </div>
                    </div>

                    <!-- Tabla de Despieces -->
                    <div class="card">
                        <h5 class="card-header">Listado de Despieces</h5>
                        <div class="table-responsive text-nowrap">
                            <table class="table" id="Tabla_Despieces">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Producto Origen</th>
                                        <th>Producto Resultado</th>
                                        <th>Cantidad</th>
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

<!-- MODAL PARA REGISTRAR NUEVO DESPIECE -->
<div class="modal fade" id="NuevoDespieceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Nuevo Despiece</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="NuevoDespieceForm">
                    <div class="mb-3">
                        <label for="ProductoOrigenId" class="form-label">Producto Origen:</label>
                        <select id="ProductoOrigenId" name="ProductoOrigenId" class="form-control" required>
                            <option value="">Seleccione un producto origen</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="ProductoResultadoId" class="form-label">Producto Resultado:</label>
                        <select id="ProductoResultadoId" name="ProductoResultadoId" class="form-control" required>
                            <option value="">Seleccione un producto resultado</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="Cantidad" class="form-label">Cantidad:</label>
                        <input type="number" id="Cantidad" name="Cantidad" class="form-control" placeholder="Cantidad" min="1" required>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="RegistrarDespiece()">Registrar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PARA EDITAR DESPIECE -->
<div class="modal fade" id="EditarDespieceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Despiece</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="EditarDespieceForm">
                    <input type="hidden" name="EditarIdDespiece" id="EditarIdDespiece">
                    <div class="mb-3">
                        <label for="EditarProductoOrigenId" class="form-label">Producto Origen:</label>
                        <select id="EditarProductoOrigenId" name="ProductoOrigenId" class="form-control" required>
                            <option value="">Seleccione un producto origen</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="EditarProductoResultadoId" class="form-label">Producto Resultado:</label>
                        <select id="EditarProductoResultadoId" name="ProductoResultadoId" class="form-control" required>
                            <option value="">Seleccione un producto resultado</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="EditarCantidad" class="form-label">Cantidad:</label>
                        <input type="number" id="EditarCantidad" name="Cantidad" class="form-control" placeholder="Cantidad" min="1" required>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="ActualizarDespiece()">Actualizar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="js/Custom/Despieces.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cargar productos origen
        fetch('?c=Despieces&a=ObtenerProductosOrigen')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const selectOrigen = document.getElementById('ProductoOrigenId');
                    data.data.forEach(producto => {
                        const option = document.createElement('option');
                        option.value = producto.IdProducto;
                        option.textContent = producto.Nombre;
                        selectOrigen.appendChild(option);
                    });
                } else {
                    console.error(data.msj);
                }
            });

        // Cargar productos resultado
        fetch('?c=Despieces&a=ObtenerProductosResultado')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const selectResultado = document.getElementById('ProductoResultadoId');
                    data.data.forEach(producto => {
                        const option = document.createElement('option');
                        option.value = producto.IdProducto;
                        option.textContent = producto.Nombre;
                        selectResultado.appendChild(option);
                    });
                } else {
                    console.error(data.msj);
                }
            });
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Cargar productos origen para el modal de edición
        fetch('?c=Despieces&a=ObtenerProductosOrigen')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const selectOrigen = document.getElementById('EditarProductoOrigenId');
                    data.data.forEach(producto => {
                        const option = document.createElement('option');
                        option.value = producto.IdProducto;
                        option.textContent = producto.Nombre;
                        selectOrigen.appendChild(option);
                    });
                } else {
                    console.error(data.msj);
                }
            });

        // Cargar productos resultado para el modal de edición
        fetch('?c=Despieces&a=ObtenerProductosResultado')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const selectResultado = document.getElementById('EditarProductoResultadoId');
                    data.data.forEach(producto => {
                        const option = document.createElement('option');
                        option.value = producto.IdProducto;
                        option.textContent = producto.Nombre;
                        selectResultado.appendChild(option);
                    });
                } else {
                    console.error(data.msj);
                }
            });
    });
</script>

<?php
require 'views/Content/footer.php';
?>