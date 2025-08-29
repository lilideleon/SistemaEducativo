<!-- sidebar.html -->
<!-- Botón menú móvil -->
<div class="container d-md-none mb-2">
  <button class="btn btn-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar">
    <i class="bi bi-list"></i> Menú
  </button>
</div>

<!-- Sidebar desktop -->
<aside class="sidebar d-none d-md-block">
  <div class="sidebar-box">
    <div class="sidebar-header">Principal</div>
    <div class="d-grid gap-3">
      <a href="?c=Menu" class="btn btn-glow fs-5 text-decoration-none"><i class="bi bi-grid-fill me-2"></i>Menú</a>
      <a href="?c=Usuarios" class="btn btn-glow fs-5 text-decoration-none"><i class="bi bi-people-fill me-2"></i>Usuarios</a>
      <a href="?c=Evaluacion" class="btn btn-glow fs-5 text-decoration-none"><i class="bi bi-clipboard-check-fill me-2"></i>Evaluación</a>
      <a href="?c=Reportes" class="btn btn-glow fs-5 text-decoration-none"><i class="bi bi-bar-chart-fill me-2"></i>Reportes</a>
      <a href="?c=Material" class="btn btn-glow fs-5 text-decoration-none"><i class="bi bi-folder2-open me-2"></i>Material</a>
      <a href="?c=Contenido" class="btn btn-glow fs-5 text-decoration-none"><i class="bi bi-bar-chart-fill me-2"></i>Contenido</a>
      <a href="?c=Preguntas" class="btn btn-glow fs-5 text-decoration-none"><i class="bi bi-bar-chart-fill me-2"></i>Preguntas</a>
    </div>
  </div>
</aside>

<!-- Offcanvas móvil -->
<div class="offcanvas offcanvas-start text-bg-dark" tabindex="-1" id="offcanvasSidebar">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">Principal</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <div class="d-grid gap-3">
      <a href="?c=Menu" class="btn btn-glow fs-5 text-decoration-none" data-bs-dismiss="offcanvas"><i class="bi bi-grid-fill me-2"></i>Menú</a>
      <a href="?c=Usuarios" class="btn btn-glow fs-5 text-decoration-none" data-bs-dismiss="offcanvas"><i class="bi bi-people-fill me-2"></i>Usuarios</a>
      <a href="?c=Evaluacion" class="btn btn-glow fs-5 text-decoration-none" data-bs-dismiss="offcanvas"><i class="bi bi-clipboard-check-fill me-2"></i>Evaluación</a>
      <a href="?c=Reportes" class="btn btn-glow fs-5 text-decoration-none" data-bs-dismiss="offcanvas"><i class="bi bi-bar-chart-fill me-2"></i>Reportes</a>
      <a href="?c=Material" class="btn btn-glow fs-5 text-decoration-none" data-bs-dismiss="offcanvas"><i class="bi bi-folder2-open me-2"></i>Material</a>
      <a href="?c=Contenido" class="btn btn-glow fs-5 text-decoration-none" data-bs-dismiss="offcanvas"><i class="bi bi-bar-chart-fill me-2"></i>Contenido</a>
      <a href="?c=Preguntas" class="btn btn-glow fs-5 text-decoration-none" data-bs-dismiss="offcanvas"><i class="bi bi-bar-chart-fill me-2"></i>Preguntas</a>
    </div>
  </div>
</div>
