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
        max-width: 1950px;
        height: auto;
        max-height: 800px;
    }

    @media (min-width: 1024px) {
        .move-left {
            left: 28px;
        }
    }

    .report-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
    }

    .report-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .report-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: #696cff;
    }

    .report-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .report-description {
        color: #697a8d;
        font-size: 0.875rem;
    }

    .modal-header {
        background-color: #696cff;
        color: white;
    }

    .modal-title {
        color: white;
    }

    .btn-close {
        background-color: white;
    }
</style>

<div class="content-wrapper">
    <div class="row move-left move-down make-larger">
        <div class="col-12">
            <div class="card mb-4">
                <center>
                    <h1 class="card-header">Generación de Reportes</h1>
                </center>
                <div class="card-body">
                    <div class="row g-4">
                        <!-- Reporte de Ventas -->
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card report-card h-100" onclick="openModal('ventas')">
                                <div class="card-body text-center">
                                    <i class="bx bx-cart report-icon"></i>
                                    <h5 class="report-title">Reporte de Ventas</h5>
                                    <p class="report-description">Genera un informe detallado de todas las ventas realizadas, incluyendo totales, productos y fechas.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Reporte de Inventario -->
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card report-card h-100" onclick="openModal('inventario')">
                                <div class="card-body text-center">
                                    <i class="bx bx-box report-icon"></i>
                                    <h5 class="report-title">Reporte de Inventario</h5>
                                    <p class="report-description">Visualiza el estado actual del inventario, stock disponible y productos próximos a agotarse.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Reporte de Compras -->
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card report-card h-100" onclick="openModal('compras')">
                                <div class="card-body text-center">
                                    <i class="bx bx-shopping-bag report-icon"></i>
                                    <h5 class="report-title">Reporte de Compras</h5>
                                    <p class="report-description">Resumen de todas las compras realizadas a proveedores y gastos asociados.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Reporte de Productos -->
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card report-card h-100" onclick="openModal('productos')">
                                <div class="card-body text-center">
                                    <i class="bx bx-food-menu report-icon"></i>
                                    <h5 class="report-title">Reporte de Productos</h5>
                                    <p class="report-description">Catálogo completo de productos con detalles de precios y categorías.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Reporte de Usuarios -->
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card report-card h-100" onclick="openModal('usuarios')">
                                <div class="card-body text-center">
                                    <i class="bx bx-user report-icon"></i>
                                    <h5 class="report-title">Reporte de Usuarios</h5>
                                    <p class="report-description">Lista de usuarios del sistema y sus roles asignados.</p>
                                </div>
                            </div>
                        </div>


                        <!-- Reporte de asistencia de usuarios -->
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card report-card h-100" onclick="openModal('asistencia')">
                                <div class="card-body text-center">
                                    <i class="bx bx-user report-icon"></i>
                                    <h5 class="report-title">Reporte de Asistencia</h5>
                                    <p class="report-description">Lista de asistencias de los usuarios del sistema.</p>
                                </div>
                            </div>
                        </div>
                        

                        <!-- Reporte de caja -->
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card report-card h-100" onclick="openModal('caja')">
                                <div class="card-body text-center">
                                    <i class="bx bx-credit-card report-icon"></i>
                                    <h5 class="report-title">Reporte de Caja</h5>
                                    <p class="report-description">Resumen de todas las transacciones de caja, incluyendo ingresos y egresos.</p>
                                </div>
                            </div>
                        </div>


                        <!-- Reporte de costos -->
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card report-card h-100" onclick="openModal('costos')">
                                <div class="card-body text-center">
                                    <i class="bx bx-credit-card report-icon"></i>
                                    <h5 class="report-title">Reporte de Costos</h5>
                                    <p class="report-description">Resumen de todas las transacciones de caja, incluyendo ingresos y egresos.</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modales para cada reporte -->
<!-- Modal Ventas -->
<div class="modal fade" id="modalVentas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reporte de Ventas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formVentas">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha Inicio</label>
                            <input type="date" class="form-control" name="fecha_inicio" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha Fin</label>
                            <input type="date" class="form-control" name="fecha_fin" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="generarReporte('ventas')">Generar Reporte</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Inventario -->
<div class="modal fade" id="modalInventario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reporte de Inventario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formInventario">
                    <div class="mb-3">
                        <label class="form-label">Categoría</label>
                        <select class="form-select" name="categoria">
                            <option value="todos">Todos</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="generarReporte('inventario')">Generar Reporte</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Compras -->
<div class="modal fade" id="modalCompras" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reporte de Compras</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formCompras">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha Inicio</label>
                            <input type="date" class="form-control" name="fecha_inicio" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha Fin</label>
                            <input type="date" class="form-control" name="fecha_fin" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="generarReporte('compras')">Generar Reporte</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Productos -->
<div class="modal fade" id="modalProductos" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reporte de Productos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formProductos">
                    <div class="mb-3">
                        <label class="form-label">Categoría</label>
                        <select class="form-select" name="categoria">
                            <option value="todos">Todas las categorías</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ordenar por</label>
                        <select class="form-select" name="ordenar">
                            <option value="nombre">Nombre</option>
                            <option value="id">id</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="generarReporte('productos')">Generar Reporte</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Usuarios -->
<div class="modal fade" id="modalUsuarios" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reporte de Usuarios</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formUsuarios">
                    <div class="mb-3">
                        <label class="form-label">Tipo de Usuario</label>
                        <select class="form-select" name="tipo_usuario">
                            <option value="todos">Todos</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <select class="form-select" name="estado">
                            <option value="todos">Todos</option>
                            <option value="activo">Activos</option>
                            <option value="inactivo">Inactivos</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="generarReporte('usuarios')">Generar Reporte</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Asistencia de Usuarios -->
<div class="modal fade" id="modalAsistencia" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reporte de Asistencia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formAsistencia">
                    <div class="mb-3">
                        <label class="form-label">desde</label>
                        <input type="date" class="form-control" name="fecha_inicio" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">hasta</label>
                        <input type="date" class="form-control" name="fecha_fin" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="generarReporte('asistencia')">Generar Reporte</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Caja -->
<div class="modal fade" id="modalCaja" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reporte de Caja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formCaja">
                    <div class="mb-3">
                        <label class="form-label">desde</label>
                        <input type="date" class="form-control" name="fecha" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">hasta</label>
                        <input type="date" class="form-control" name="fecha" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="generarReporte('caja')">Generar Reporte</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal reporte costos diferencia entre vendido - precio de costo -->

<div class="modal fade" id="modalCostos" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reporte de Costos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formCostos">
                    <div class="mb-3">
                        <label class="form-label">desde</label>
                        <input type="date" class="form-control" name="fechaInicio" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">hasta</label>
                        <input type="date" class="form-control" name="fechaFin" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="generarReporte('costos')">Generar Reporte</button>
            </div>
        </div>
    </div>
</div>

<script>
function openModal(tipo) {
    // Cerrar cualquier modal abierto
    $('.modal').modal('hide');
    
    // Abrir el modal correspondiente
    $(`#modal${tipo.charAt(0).toUpperCase() + tipo.slice(1)}`).modal('show');
}

function generarReporte(tipo) {
    // Obtener los datos del formulario correspondiente
    const formData = new FormData(document.getElementById(`form${tipo.charAt(0).toUpperCase() + tipo.slice(1)}`));
    const params = new URLSearchParams(formData).toString();
    
    // Redirigir a la URL del reporte con los parámetros en nueva pestaña
    window.open(`?c=Reportes&a=Reporte${tipo.charAt(0).toUpperCase() + tipo.slice(1)}&${params}`, '_blank');
}

// Establecer las fechas por defecto
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date();
    const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
    
    // Formato YYYY-MM-DD para inputs date
    const formatDate = (date) => {
        return date.toISOString().split('T')[0];
    };
    
    // Establecer fechas por defecto en los formularios de ventas y compras
    document.querySelectorAll('input[type="date"][name="fecha_inicio"]').forEach(input => {
        input.value = formatDate(firstDayOfMonth);
    });
    
    document.querySelectorAll('input[type="date"][name="fecha_fin"]').forEach(input => {
        input.value = formatDate(today);
    });
});
</script>

<?php
require 'views/Content/footer.php';
?>