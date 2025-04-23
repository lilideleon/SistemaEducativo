<?php 
   include 'views/Content/header.php';
?>
 
 <?php 
                           
    if($_SESSION['TipoUsuario'] == '')
    {
        print "<script>
            window.location='?c=Login'; 
            </script>";
    }
    else if($_SESSION['TipoUsuario'] == 1)
    {
        require 'views/Content/sidebar.php'; 
            print "<script>
                console.log($TipoUsuario);
            </script>";
    }



?>

<style>
    .stats-card {
        transition: all 0.3s ease;
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }
    
    .stats-icon {
        width: 48px;
        height: 48px;
        margin-bottom: 1rem;
    }
    
    .stats-title {
        color: #566a7f;
        font-size: 0.9rem;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
    }
    
    .stats-value {
        color: #566a7f;
        font-size: 1.5rem;
        font-weight: bold;
    }
    
    .chart-container {
        position: relative;
        margin: 20px 0;
        height: 300px;
    }

    .trend-indicator {
        font-size: 0.8rem;
        padding: 2px 8px;
        border-radius: 12px;
    }

    .trend-up {
        background-color: #e8f5e9;
        color: #2e7d32;
    }

    .trend-down {
        background-color: #ffebee;
        color: #c62828;
    }
</style>

<div class="content-wrapper" style="padding: 20px;">
    <div class="container-fluid">
        <!-- Tarjetas de Estadísticas -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="stats-title mb-2">Ventas del Día</h6>
                                <h3 class="stats-value mb-2" id="ventasHoy">Q. 0.00</h3>
                                <small class="trend-indicator trend-up" id="ventasTrend">
                                    <i class="bx bx-up-arrow-alt"></i> vs ayer
                                </small>
                            </div>
                            <div class="avatar bg-light-primary p-2">
                                <i class="bx bx-cart text-primary" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="stats-title mb-2">Productos Activos</h6>
                                <h3 class="stats-value mb-2" id="productosActivos">0</h3>
                                <small class="trend-indicator trend-up" id="productosTrend">
                                    <i class="bx bx-box"></i> en stock
                                </small>
                            </div>
                            <div class="avatar bg-light-success p-2">
                                <i class="bx bx-box text-success" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="stats-title mb-2">Usuarios Activos</h6>
                                <h3 class="stats-value mb-2" id="usuariosActivos">0</h3>
                                <small class="trend-indicator trend-up" id="usuariosTrend">
                                    <i class="bx bx-user"></i> registrados
                                </small>
                            </div>
                            <div class="avatar bg-light-info p-2">
                                <i class="bx bx-user text-info" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="stats-title mb-2">Compras del Mes</h6>
                                <h3 class="stats-value mb-2" id="comprasMes">Q. 0.00</h3>
                                <small class="trend-indicator trend-down" id="comprasTrend">
                                    <i class="bx bx-down-arrow-alt"></i> vs mes anterior
                                </small>
                            </div>
                            <div class="avatar bg-light-warning p-2">
                                <i class="bx bx-shopping-bag text-warning" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficas -->
        <div class="row g-4">
            <!-- Gráfica de Ventas -->
            <div class="col-12 col-xl-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Ventas vs Tiempo</h5>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="ventasRange" data-bs-toggle="dropdown">
                                Últimos 7 días
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="cambiarRangoVentas('7')">Últimos 7 días</a></li>
                                <li><a class="dropdown-item" href="#" onclick="cambiarRangoVentas('30')">Último mes</a></li>
                                <li><a class="dropdown-item" href="#" onclick="cambiarRangoVentas('90')">Último trimestre</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="ventasChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Gráfica de Productos -->
            <div class="col-12 col-xl-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Top Productos Vendidos</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="productosChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Tabla de Últimas Transacciones -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Últimas Transacciones</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Fecha</th>
                                        <th>Tipo</th>
                                        <th>Cliente/Proveedor</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody id="ultimasTransacciones">
                                    <!-- Se llenará dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Incluir Chart.js -->
<script src="res/plugins/chart.js/Chart.min.js"></script>

<script>
window.onload = function() {
    cargarDashboard();
    inicializarGraficas();
};

function cargarDashboard() {
    // Cargar estadísticas
    obtenerEstadistica('TotalVentasHoy', '#ventasHoy', 'Q. ');
    obtenerEstadistica('TotalProductos', '#productosActivos', '');
    obtenerEstadistica('TotalUsuarios', '#usuariosActivos', '');
    obtenerEstadistica('TotalComprasMes', '#comprasMes', 'Q. ');
    
    // Cargar últimas transacciones
    cargarUltimasTransacciones();
}

function obtenerEstadistica(accion, selector, prefijo) {
    $.ajax({
        url: '?c=Menu&a=' + accion,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response && response.data !== undefined) {
                $(selector).text(prefijo + formatearNumero(response.data));
                
                // Actualizar indicador de tendencia
                if (response.tendencia > 0) {
                    $(selector).siblings('.trend-indicator')
                        .removeClass('trend-down')
                        .addClass('trend-up')
                        .find('i')
                        .removeClass('bx-down-arrow-alt')
                        .addClass('bx-up-arrow-alt');
                } else if (response.tendencia < 0) {
                    $(selector).siblings('.trend-indicator')
                        .removeClass('trend-up')
                        .addClass('trend-down')
                        .find('i')
                        .removeClass('bx-up-arrow-alt')
                        .addClass('bx-down-arrow-alt');
                }
            }
        },
        error: function() {
            console.error('Error al obtener estadística:', accion);
        }
    });
}

function inicializarGraficas() {
    // Gráfica de Ventas
    const ctxVentas = document.getElementById('ventasChart').getContext('2d');
    window.ventasChart = new Chart(ctxVentas, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Ventas',
                data: [],
                borderColor: '#696cff',
                tension: 0.4,
                fill: true,
                backgroundColor: 'rgba(105, 108, 255, 0.1)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Q. ' + value;
                        }
                    }
                }
            }
        }
    });

    // Gráfica de Productos
    const ctxProductos = document.getElementById('productosChart').getContext('2d');
    window.productosChart = new Chart(ctxProductos, {
        type: 'doughnut',
        data: {
            labels: [],
            datasets: [{
                data: [],
                backgroundColor: [
                    '#696cff',
                    '#71dd37',
                    '#03c3ec',
                    '#ffab00',
                    '#ff3e1d'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Cargar datos iniciales
    actualizarGraficaVentas('7');
    actualizarGraficaProductos();
}

function actualizarGraficaVentas(dias) {
    $.ajax({
        url: '?c=Menu&a=VentasPorPeriodo',
        type: 'GET',
        data: { dias: dias },
        dataType: 'json',
        success: function(response) {
            if (response && response.labels && response.datos) {
                window.ventasChart.data.labels = response.labels;
                window.ventasChart.data.datasets[0].data = response.datos;
                window.ventasChart.update();
            }
        }
    });
}

function actualizarGraficaProductos() {
    $.ajax({
        url: '?c=Menu&a=ProductosMasVendidos',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response && response.labels && response.datos) {
                window.productosChart.data.labels = response.labels;
                window.productosChart.data.datasets[0].data = response.datos;
                window.productosChart.update();
            }
        }
    });
}

function cargarUltimasTransacciones() {
    $.ajax({
        url: '?c=Menu&a=UltimasTransacciones',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response && response.length > 0) {
                const tbody = $('#ultimasTransacciones');
                tbody.empty();
                
                response.forEach(trans => {
                    tbody.append(`
                        <tr>
                            <td>${trans.id}</td>
                            <td>${trans.fecha}</td>
                            <td>${trans.tipo}</td>
                            <td>${trans.nombre}</td>
                            <td>Q. ${formatearNumero(trans.total)}</td>
                            <td><span class="badge bg-${trans.estado === 'Activo' ? 'success' : 'danger'}">${trans.estado}</span></td>
                        </tr>
                    `);
                });
            }
        }
    });
}

function cambiarRangoVentas(dias) {
    $('#ventasRange').text(dias === '7' ? 'Últimos 7 días' : 
                          dias === '30' ? 'Último mes' : 'Último trimestre');
    actualizarGraficaVentas(dias);
}

function formatearNumero(numero) {
    return new Intl.NumberFormat('es-GT', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(numero);
}

// Actualizar datos cada 5 minutos
setInterval(cargarDashboard, 300000);
</script>

<?php require 'views/Content/footer.php'; ?>
