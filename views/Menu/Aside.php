<!-- Estilos del menú responsivo -->
<style>
.menu-button {
    position: fixed;
    top: 1rem;
    left: 1rem;
    z-index: 1030;
    padding: 0.5rem 1rem;
    background-color: var(--bg-teal, #4f8f8a);
    color: white;
    border: none;
    border-radius: 0.25rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.menu-button:hover {
    background-color: var(--line, #2e655f);
}

.offcanvas {
    max-width: 280px;
}

.offcanvas-backdrop.show {
    opacity: 0.5;
}

@media (max-width: 767.98px) {
    .main-content {
        margin-left: 0 !important;
        padding-top: 4rem;
    }
}
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
        <a href="?c=Contenido" class="btn btn-glow fs-5 text-decoration-none"><i class="bi bi-bar-chart-fill me-2"></i>Contenido</a>
        <a href="?c=Preguntas" class="btn btn-glow fs-5 text-decoration-none"><i class="bi bi-bar-chart-fill me-2"></i>Preguntas</a>
      <?php elseif ($rol === 'ALUMNO'): ?>
        <a href="?c=Evaluacion" class="btn btn-glow fs-5 text-decoration-none"><i class="bi bi-clipboard-check-fill me-2"></i>Evaluación</a>
        <a href="?c=MaterialAlumno" class="btn btn-glow fs-5 text-decoration-none"><i class="bi bi-folder2-open me-2"></i>Material</a>
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
        <a href="?c=Contenido" class="btn btn-glow fs-5 text-decoration-none" data-bs-dismiss="offcanvas"><i class="bi bi-bar-chart-fill me-2"></i>Contenido</a>
        <a href="?c=Preguntas" class="btn btn-glow fs-5 text-decoration-none" data-bs-dismiss="offcanvas"><i class="bi bi-bar-chart-fill me-2"></i>Preguntas</a>
      <?php elseif ($rol === 'ALUMNO'): ?>
        <a href="?c=Evaluacion" class="btn btn-glow fs-5 text-decoration-none" data-bs-dismiss="offcanvas"><i class="bi bi-clipboard-check-fill me-2"></i>Evaluación</a>
        <a href="?c=MaterialAlumno" class="btn btn-glow fs-5 text-decoration-none" data-bs-dismiss="offcanvas"><i class="bi bi-folder2-open me-2"></i>Material</a>
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
