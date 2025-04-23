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
    .help-section {
        padding: 20px;
    }

    .help-card {
        transition: transform 0.3s ease;
        cursor: pointer;
        height: 100%;
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .help-card:hover {
        transform: translateY(-5px);
    }

    .help-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: #696cff;
    }

    .help-title {
        color: #566a7f;
        margin-bottom: 1rem;
    }

    .help-description {
        color: #697a8d;
    }

    .accordion-button:not(.collapsed) {
        background-color: #696cff;
        color: white;
    }

    .video-container {
        position: relative;
        padding-bottom: 56.25%;
        height: 0;
        overflow: hidden;
    }

    .video-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .manual-section {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .manual-section h3 {
        color: #566a7f;
        border-bottom: 2px solid #696cff;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    .step-list {
        list-style: none;
        padding-left: 0;
    }

    .step-list li {
        margin-bottom: 15px;
        padding-left: 30px;
        position: relative;
    }

    .step-list li:before {
        content: '→';
        position: absolute;
        left: 0;
        color: #696cff;
    }
</style>

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
    
        <div class="row">
            <!-- Sección de Manual de Usuario -->
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Centro de Ayuda</h5>
                    </div>
                    <div class="card-body">
                        <div class="accordion" id="manualAccordion">
                            <!-- Inicio -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#inicioCollapse">
                                        <i class='bx bx-home-alt me-2'></i> Inicio y Acceso al Sistema
                                    </button>
                                </h2>
                                <div id="inicioCollapse" class="accordion-collapse collapse show" data-bs-parent="#manualAccordion">
                                    <div class="accordion-body">
                                        <div class="manual-section">
                                            <h3>¿Cómo iniciar sesión?</h3>
                                            <ol class="step-list">
                                                <li>Ingrese su nombre de usuario en el campo correspondiente</li>
                                                <li>Ingrese su contraseña en el campo de contraseña</li>
                                                <li>Haga clic en el botón "Iniciar Sesión"</li>
                                                <li>Si los datos son correctos, accederá al sistema</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Ventas -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#ventasCollapse">
                                        <i class='bx bx-cart me-2'></i> Gestión de Ventas
                                    </button>
                                </h2>
                                <div id="ventasCollapse" class="accordion-collapse collapse" data-bs-parent="#manualAccordion">
                                    <div class="accordion-body">
                                        <div class="manual-section">
                                            <h3>Realizar una Venta</h3>
                                            <ol class="step-list">
                                                <li>Acceda al módulo de Ventas desde el menú principal</li>
                                                <li>Seleccione los productos usando el buscador</li>
                                                <li>Ajuste las cantidades según necesite</li>
                                                <li>Verifique el total y complete la venta</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Inventario -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#inventarioCollapse">
                                        <i class='bx bx-box me-2'></i> Control de Inventario
                                    </button>
                                </h2>
                                <div id="inventarioCollapse" class="accordion-collapse collapse" data-bs-parent="#manualAccordion">
                                    <div class="accordion-body">
                                        <div class="manual-section">
                                            <h3>Gestión de Productos</h3>
                                            <ol class="step-list">
                                                <li>Ingrese al módulo de Inventario</li>
                                                <li>Para agregar productos, use el botón "Nuevo Producto"</li>
                                                <li>Complete la información requerida del producto</li>
                                                <li>Para actualizar existencias, use la opción "Actualizar Stock"</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Reportes -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#reportesCollapse">
                                        <i class='bx bx-line-chart me-2'></i> Generación de Reportes
                                    </button>
                                </h2>
                                <div id="reportesCollapse" class="accordion-collapse collapse" data-bs-parent="#manualAccordion">
                                    <div class="accordion-body">
                                        <div class="manual-section">
                                            <h3>Generar Reportes</h3>
                                            <ol class="step-list">
                                                <li>Acceda a la sección de Reportes</li>
                                                <li>Seleccione el tipo de reporte que necesita</li>
                                                <li>Configure los filtros de fecha si es necesario</li>
                                                <li>Haga clic en "Generar Reporte"</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección de Videos Tutoriales -->
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Videos Tutoriales</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="manual-section">
                                    <h3>Tutorial de Ventas</h3>
                                    <div class="video-container">
                                        <!-- Aquí puedes agregar el iframe de tu video tutorial -->
                                        <img src="assets/img/tutorial-placeholder.jpg" alt="Tutorial de Ventas" class="img-fluid">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="manual-section">
                                    <h3>Tutorial de Inventario</h3>
                                    <div class="video-container">
                                        <!-- Aquí puedes agregar el iframe de tu video tutorial -->
                                        <img src="assets/img/tutorial-placeholder.jpg" alt="Tutorial de Inventario" class="img-fluid">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección de Preguntas Frecuentes -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Preguntas Frecuentes</h5>
                    </div>
                    <div class="card-body">
                        <div class="accordion" id="faqAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                        ¿Cómo recupero mi contraseña?
                                    </button>
                                </h2>
                                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Contacte al administrador del sistema para restablecer su contraseña.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                        ¿Cómo anulo una venta?
                                    </button>
                                </h2>
                                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Para anular una venta, vaya al módulo de ventas, busque la venta específica y use la opción "Anular Venta". Solo usuarios con permisos pueden realizar esta acción.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                        ¿Cómo actualizo el inventario?
                                    </button>
                                </h2>
                                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        El inventario se actualiza automáticamente con las ventas y compras. Para ajustes manuales, use la opción "Ajuste de Inventario" en el módulo de Inventario.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    require 'views/Content/footer.php';
?>

