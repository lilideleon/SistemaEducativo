<!-- Estilos del menú responsivo mejorado -->
<style>
:root {
    --sidebar-width: 280px;
    --sidebar-collapsed-width: 70px;
    --sidebar-bg: linear-gradient(180deg, #117867 0%, #0d5d52 100%);
    --sidebar-hover: rgba(255, 255, 255, 0.1);
    --sidebar-active: rgba(255, 255, 255, 0.2);
    --transition-speed: 0.3s;
}

/* Botón hamburguesa mejorado */
.sidebar-toggle {
    position: fixed;
    top: 1rem;
    left: 1rem;
    z-index: 1050;
    width: 50px;
    height: 50px;
    border: none;
    border-radius: 12px;
    background: linear-gradient(135deg, #117867 0%, #15a085 100%);
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(17, 120, 103, 0.3);
    transition: all var(--transition-speed) ease;
}

.sidebar-toggle:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 20px rgba(17, 120, 103, 0.4);
}

.sidebar-toggle i {
    font-size: 1.5rem;
    transition: transform var(--transition-speed) ease;
}

.sidebar-toggle:hover i {
    transform: rotate(90deg);
}

/* Sidebar mejorado */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    width: var(--sidebar-width);
    background: var(--sidebar-bg);
    box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
    transition: transform var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 1040;
    overflow-y: auto;
    overflow-x: hidden;
}

.sidebar::-webkit-scrollbar {
    width: 6px;
}

.sidebar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
}

.sidebar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 3px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.3);
}

/* Estado colapsado */
.sidebar.collapsed {
    transform: translateX(calc(-1 * var(--sidebar-width) + var(--sidebar-collapsed-width)));
}

.sidebar.collapsed .sidebar-header,
.sidebar.collapsed .sidebar-text {
    opacity: 0;
    pointer-events: none;
}

.sidebar.collapsed .btn-glow {
    justify-content: center;
    padding: 1rem;
}

.sidebar.collapsed .btn-glow i {
    margin: 0 !important;
}

/* Header del sidebar */
.sidebar-header {
    padding: 5rem 1.5rem 1.5rem 1.5rem;
    background: rgba(0, 0, 0, 0.1);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    transition: opacity var(--transition-speed) ease;
}

.sidebar-header small {
    display: block;
    color: rgba(255, 255, 255, 0.9);
    font-weight: 500;
    margin-bottom: 0.25rem;
}

.sidebar-header .opacity-75 {
    opacity: 0.7 !important;
}

/* Contenedor de la sidebar box */
.sidebar-box {
    padding: 0;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.sidebar-box .d-grid {
    padding: 1rem;
    flex: 1;
}

/* Botones mejorados */
.btn-glow {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.15);
    color: white !important;
    padding: 1rem 1.25rem;
    border-radius: 12px;
    transition: all 0.3s ease;
    font-weight: 500;
    display: flex;
    align-items: center;
    white-space: nowrap;
    overflow: hidden;
}

.btn-glow:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateX(5px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.btn-glow i {
    transition: transform 0.3s ease;
}

.btn-glow:hover i {
    transform: scale(1.2);
}

.sidebar-text {
    transition: opacity var(--transition-speed) ease;
}

/* Contenido principal ajustado */
.main-content {
    margin-left: var(--sidebar-width);
    transition: margin-left var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
    min-height: 100vh;
}

.main-content.expanded {
    margin-left: var(--sidebar-collapsed-width);
}

/* Backdrop para móvil */
.sidebar-backdrop {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1030;
    opacity: 0;
    transition: opacity var(--transition-speed) ease;
}

.sidebar-backdrop.show {
    display: block;
    opacity: 1;
}

/* Responsive */
@media (max-width: 767.98px) {
    .sidebar {
        transform: translateX(-100%);
    }
    
    .sidebar.show {
        transform: translateX(0);
    }
    
    .sidebar.collapsed {
        transform: translateX(-100%);
    }
    
    .main-content,
    .main-content.expanded {
        margin-left: 0 !important;
    }
    
    .sidebar-toggle {
        background: linear-gradient(135deg, #117867 0%, #15a085 100%);
    }
}

@media (min-width: 768px) {
    .sidebar-backdrop {
        display: none !important;
    }
}

/* Animaciones */
@keyframes slideIn {
    from {
        transform: translateX(-20px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.btn-glow {
    animation: slideIn 0.3s ease-out backwards;
}

.btn-glow:nth-child(1) { animation-delay: 0.05s; }
.btn-glow:nth-child(2) { animation-delay: 0.1s; }
.btn-glow:nth-child(3) { animation-delay: 0.15s; }
.btn-glow:nth-child(4) { animation-delay: 0.2s; }
.btn-glow:nth-child(5) { animation-delay: 0.25s; }
.btn-glow:nth-child(6) { animation-delay: 0.3s; }
.btn-glow:nth-child(7) { animation-delay: 0.35s; }
.btn-glow:nth-child(8) { animation-delay: 0.4s; }
</style>

<!-- Botón menú móvil -->
<div class="d-md-none">
    <button class="menu-button" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar" aria-label="Abrir menú">
        <i class="bi bi-list"></i> Menú
    </button>
</div>debar.html -->
<!-- BotÃƒÆ’Ã‚Â³n menÃƒÆ’Ã‚Âº mÃƒÆ’Ã‚Â³vil -->
<div class="container d-md-none mb-2">
  <button class="btn btn-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar">
    <i class="bi bi-list"></i> MenÃƒÆ’Ã‚Âº
  </button>
</div>


<?php
    // Incluir el middleware de autenticaciÃƒÆ’Ã‚Â³n
    require_once "core/AuthMiddleware.php";
    
    // Obtener informaciÃƒÆ’Ã‚Â³n del usuario actual
    $usuario = AuthMiddleware::getCurrentUser();
    $rol = $usuario && isset($usuario['rol']) ? strtoupper($usuario['rol']) : '';
    
    // Verificar si el usuario estÃƒÆ’Ã‚Â¡ autenticado y tiene ID 1 (administrador)
    $esAdmin = ($usuario && $usuario['id'] == 1);
?>



<!-- Sidebar desktop -->
<aside class="sidebar d-none d-md-block">
  <div class="sidebar-box">
    <?php if ($usuario): ?>
      <div class="sidebar-header">
        <small class="d-block text-light">Usuario: <?php echo htmlspecialchars(isset($usuario['nombre_completo']) ? $usuario['nombre_completo'] : 'Usuario'); ?></small>
        <small class="d-block text-light opacity-75">Rol: <?php echo htmlspecialchars(isset($usuario['rol']) ? $usuario['rol'] : 'N/A'); ?></small>
      </div>
    <?php else: ?>
      <div class="sidebar-header">Principal</div>
    <?php endif; ?>
    <div class="d-grid gap-3">
      <?php if ($rol === 'ADMIN'): ?>
        <a href="?c=Menu" class="btn btn-glow fs-5 text-decoration-none"><i class="bi bi-grid-fill me-2"></i>Menú</a>
        <a href="?c=Usuarios" class="btn btn-glow fs-5 text-decoration-none"><i class="bi bi-people-fill me-2"></i>Usuarios</a>
        <a href="?c=Evaluacion" class="btn btn-glow fs-5 text-decoration-none"><i class="bi bi-clipboard-check-fill me-2"></i>Evaluación</a>
        <a href="?c=Encuestas" class="btn btn-glow fs-5 text-decoration-none"><i class="bi bi-ui-checks-grid me-2"></i>Encuestas</a>
        <a href="?c=Reportes" class="btn btn-glow fs-5 text-decoration-none"><i class="bi bi-bar-chart-fill me-2"></i>Reportes</a>
        <a href="?c=Material" class="btn btn-glow fs-5 text-decoration-none"><i class="bi bi-folder2-open me-2"></i>Material</a>
        <?php /* Menú Contenido oculto
        <a href="?c=Contenido" class="btn btn-glow fs-5 text-decoration-none"><i class="bi bi-bar-chart-fill me-2"></i>Contenido</a>
        */ ?>
        <a href="?c=Preguntas" class="btn btn-glow fs-5 text-decoration-none"><i class="bi bi-bar-chart-fill me-2"></i>Preguntas</a>
      <?php elseif ($rol === 'ALUMNO'): ?>
        <a href="?c=Evaluacion" class="btn btn-glow fs-5 text-decoration-none"><i class="bi bi-clipboard-check-fill me-2"></i>Evaluación</a>
        <a href="?c=MaterialAlumno" class="btn btn-glow fs-5 text-decoration-none"><i class="bi bi-folder2-open me-2"></i>Material</a>
        <a href="?c=Perfil" class="btn btn-glow fs-5 text-decoration-none"><i class="bi bi-person-circle me-2"></i>Perfil</a>
      <?php elseif ($rol === 'DIRECTOR'): ?>
        <a href="?c=Menu" class="btn btn-glow fs-5 text-decoration-none"><i class="bi bi-grid-fill me-2"></i>Menú</a>
        <a href="?c=Usuarios" class="btn btn-glow fs-5 text-decoration-none"><i class="bi bi-people-fill me-2"></i>Usuarios</a>
        <a href="?c=Reportes" class="btn btn-glow fs-5 text-decoration-none"><i class="bi bi-bar-chart-fill me-2"></i>Reportes</a>
        <a href="?c=Material" class="btn btn-glow fs-5 text-decoration-none"><i class="bi bi-folder2-open me-2"></i>Material</a>
      <?php elseif ($rol === 'DOCENTE'): ?>
        <a href="?c=Material" class="btn btn-glow fs-5 text-decoration-none"><i class="bi bi-folder2-open me-2"></i>Material</a>
      <?php endif; ?>
    </div>
  </div>
</aside>

<!-- Offcanvas mÃƒÆ’Ã‚Â³vil -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar" style="background-color: var(--bg-teal, #4f8f8a);">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">Principal</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <div class="d-grid gap-3">
      <?php if ($rol === 'ADMIN'): ?>
        <a href="?c=Menu" class="btn btn-glow fs-5 text-decoration-none" data-bs-dismiss="offcanvas"><i class="bi bi-grid-fill me-2"></i>Menú</a>
        <a href="?c=Usuarios" class="btn btn-glow fs-5 text-decoration-none" data-bs-dismiss="offcanvas"><i class="bi bi-people-fill me-2"></i>Usuarios</a>
        <a href="?c=Evaluacion" class="btn btn-glow fs-5 text-decoration-none" data-bs-dismiss="offcanvas"><i class="bi bi-clipboard-check-fill me-2"></i>Evaluación</a>
        <a href="?c=Encuestas" class="btn btn-glow fs-5 text-decoration-none" data-bs-dismiss="offcanvas"><i class="bi bi-ui-checks-grid me-2"></i>Encuestas</a>
        <a href="?c=Reportes" class="btn btn-glow fs-5 text-decoration-none" data-bs-dismiss="offcanvas"><i class="bi bi-bar-chart-fill me-2"></i>Reportes</a>
        <a href="?c=Material" class="btn btn-glow fs-5 text-decoration-none" data-bs-dismiss="offcanvas"><i class="bi bi-folder2-open me-2"></i>Material</a>
        <?php /* Menú Contenido oculto
        <a href="?c=Contenido" class="btn btn-glow fs-5 text-decoration-none" data-bs-dismiss="offcanvas"><i class="bi bi-bar-chart-fill me-2"></i>Contenido</a>
        */ ?>
        <a href="?c=Preguntas" class="btn btn-glow fs-5 text-decoration-none" data-bs-dismiss="offcanvas"><i class="bi bi-bar-chart-fill me-2"></i>Preguntas</a>
      <?php elseif ($rol === 'ALUMNO'): ?>
        <a href="?c=Evaluacion" class="btn btn-glow fs-5 text-decoration-none" data-bs-dismiss="offcanvas"><i class="bi bi-clipboard-check-fill me-2"></i>Evaluación</a>
        <a href="?c=MaterialAlumno" class="btn btn-glow fs-5 text-decoration-none" data-bs-dismiss="offcanvas"><i class="bi bi-folder2-open me-2"></i>Material</a>
        <a href="?c=Perfil" class="btn btn-glow fs-5 text-decoration-none" data-bs-dismiss="offcanvas"><i class="bi bi-person-circle me-2"></i>Perfil</a>
      <?php elseif ($rol === 'DIRECTOR'): ?>
        <a href="?c=Menu" class="btn btn-glow fs-5 text-decoration-none" data-bs-dismiss="offcanvas"><i class="bi bi-grid-fill me-2"></i>Menú</a>
        <a href="?c=Usuarios" class="btn btn-glow fs-5 text-decoration-none" data-bs-dismiss="offcanvas"><i class="bi bi-people-fill me-2"></i>Usuarios</a>
        <a href="?c=Reportes" class="btn btn-glow fs-5 text-decoration-none" data-bs-dismiss="offcanvas"><i class="bi bi-bar-chart-fill me-2"></i>Reportes</a>
        <a href="?c=Material" class="btn btn-glow fs-5 text-decoration-none" data-bs-dismiss="offcanvas"><i class="bi bi-folder2-open me-2"></i>Material</a>
      <?php elseif ($rol === 'DOCENTE'): ?>
        <a href="?c=Material" class="btn btn-glow fs-5 text-decoration-none" data-bs-dismiss="offcanvas"><i class="bi bi-folder2-open me-2"></i>Material</a>
      <?php endif; ?>
    </div>
  </div>
</div>
