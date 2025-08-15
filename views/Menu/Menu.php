<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sistema Educativo — Menú Principal</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />

  <style>
    :root {
      --bg-teal: #4f8f8a;          /* fondo general */
      --title-blue: #235c9c;       /* título */
      --sidebar-header: #a8c0bb;   /* cabecera sidebar */
    }

    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
    }

    body {
      background-color: var(--bg-teal);
      display: flex;
      flex-direction: column;
    }

    /* Layout principal */
    .app-wrapper {
      display: flex;
      flex: 1;
      height: 100vh;
      margin: 0;
      padding: 0;
    }

    /* Sidebar */
    .sidebar {
      width: 260px;
      height: 100vh;
      position: fixed;
      left: 0;
      top: 0;
      overflow-y: auto;
      background-color: #50938a;
      padding: 1rem;
      box-shadow: 2px 0 5px rgba(0,0,0,0.1);
      z-index: 1000;
    }

    .sidebar-box {
      background: rgba(255,255,255,.1);
      border-radius: .5rem;
      padding: .75rem;
    }

    .sidebar-header {
      background: var(--sidebar-header);
      color: #1f2937;
      font-weight: 600;
      padding: .5rem .75rem;
      border-radius: .35rem;
      margin-bottom: .75rem;
    }

    /* Botones menú */
    .btn-glow {
      border: 0;
      border-radius: 1rem;
      padding: .9rem 1.1rem;
      color: #fff;
      font-weight: 700;
      background:
        radial-gradient(140% 120% at 50% 40%, rgba(255,255,255,.28) 0%, rgba(255,255,255,.08) 42%, rgba(0,0,0,.22) 75%),
        linear-gradient(180deg, #0f1c2e 0%, #1f3554 100%);
      box-shadow: 0 8px 18px rgba(0,0,0,.25), inset 0 -2px 0 rgba(255,255,255,.1);
      transition: transform .12s ease, filter .12s ease;
      text-align: left;
    }
    .btn-glow:hover { transform: translateY(-2px); filter: brightness(1.02); }
    .btn-glow.active { outline: 2px solid rgba(255,255,255,.35); }

    /* Contenido principal */
    section.main-content {
      margin-left: 260px;
      width: calc(100% - 260px);
      padding: 2rem;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* Panel blanco ocupa todo el alto */
    .content-panel {
      flex: 1;
      display: flex;
      flex-direction: column;
      background: white;
      border-radius: 0.5rem;
      padding: 1.5rem;
      margin: 0;
      box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
      min-height: 0;
    }

    #contentArea {
      flex: 1;
      overflow: auto;
    }

    /* Responsive móvil */
    @media (max-width: 767.98px) {
      .sidebar { display: none; }
      section.main-content {
        margin-left: 0 !important;
        width: 100% !important;
        padding: 1rem !important;
        min-height: calc(100vh - 56px);
      }
    }
  </style>
</head>
<body>

  <!-- Botón menú móvil -->
  <div class="container d-md-none mb-2">
    <button class="btn btn-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar">
      <i class="bi bi-list"></i> Menú
    </button>
  </div>

  <main class="app-wrapper">
    <!-- Sidebar -->
    <aside class="sidebar d-none d-md-block">
      <div class="sidebar-box">
        <div class="sidebar-header">Principal</div>
        <div class="d-grid gap-3">
          <a href="?c=Menu" class="btn btn-glow fs-5 text-decoration-none"><i class="bi bi-grid-fill me-2"></i>Menú</a>
          <a href="?c=Alumnos" class="btn btn-glow fs-5 text-decoration-none"><i class="bi bi-people-fill me-2"></i>Alumnos</a>
          <a href="#" class="btn btn-glow fs-5 text-decoration-none"><i class="bi bi-person-gear me-2"></i>Directores</a>
          <a href="#" class="btn btn-glow fs-5 text-decoration-none"><i class="bi bi-clipboard-check-fill me-2"></i>Evaluación</a>
          <a href="#" class="btn btn-glow fs-5 text-decoration-none"><i class="bi bi-bar-chart-fill me-2"></i>Reportes</a>
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
          <a href="?c=Alumnos" class="btn btn-glow fs-5 text-decoration-none" data-bs-dismiss="offcanvas"><i class="bi bi-people-fill me-2"></i>Alumnos</a>
          <a href="#" class="btn btn-glow fs-5 text-decoration-none" data-bs-dismiss="offcanvas"><i class="bi bi-person-gear me-2"></i>Directores</a>
          <a href="#" class="btn btn-glow fs-5 text-decoration-none" data-bs-dismiss="offcanvas"><i class="bi bi-clipboard-check-fill me-2"></i>Evaluación</a>
          <a href="#" class="btn btn-glow fs-5 text-decoration-none" data-bs-dismiss="offcanvas"><i class="bi bi-bar-chart-fill me-2"></i>Reportes</a>
        </div>
      </div>
    </div>

    <!-- Contenido -->
    <section class="col-12 main-content">
      <header class="mb-4">
        <h1 class="h3">Bienvenido al Sistema Educativo</h1>
        <p class="text-muted">Selecciona una opción del menú para comenzar</p>
      </header>
      <div class="content-panel" id="panelTop">
        <h2 class="h6 text-black-50 mb-3" id="contentTitle">Bienvenido</h2>
        <div id="contentArea">
          <p class="mb-0">Selecciona una opción del menú para cargar contenido aquí.</p>
        </div>
      </div>
    </section>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const items = document.querySelectorAll('[data-section]');
      const contentArea = document.getElementById('contentArea');

      const TEMPLATES = {
        menu: `
          <div class="row g-4">
            <div class="col-md-4">
              <div class="card border-0 shadow-sm">
                <div class="card-body">
                  <h5 class="card-title">Alumnos</h5>
                  <p class="card-text">Gestión de estudiantes y sus registros académicos.</p>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card border-0 shadow-sm">
                <div class="card-body">
                  <h5 class="card-title">Docentes</h5>
                  <p class="card-text">Administración del personal docente.</p>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card border-0 shadow-sm">
                <div class="card-body">
                  <h5 class="card-title">Reportes</h5>
                  <p class="card-text">Generación de informes y estadísticas.</p>
                </div>
              </div>
            </div>
          </div>
        `,
        alumnos: `<p>Módulo para administrar la información de los estudiantes.</p>`,
        directores: `<p>Gestión de usuarios con privilegios administrativos.</p>`,
        evaluacion: `<p>Sistema de evaluación y seguimiento académico.</p>`,
        reportes: `<p>Generación de informes detallados del sistema.</p>`
      };

      // Resaltar elemento activo
    function setActiveNav() {
      const currentUrl = window.location.search;
      const navLinks = document.querySelectorAll('.sidebar a, .offcanvas-body a');
      
      navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === currentUrl || 
            (currentUrl.includes('c=Menu') && link.getAttribute('href') === '?c=Menu') ||
            (currentUrl.includes('c=Alumnos') && link.getAttribute('href') === '?c=Alumnos')) {
          link.classList.add('active');
        }
      });
    }
    
    // Cargar contenido inicial si estamos en el menú principal
    if (window.location.search === '' || window.location.search.includes('c=Menu')) {
      contentArea.innerHTML = TEMPLATES.menu;
    }
    
    // Inicializar navegación activa
    setActiveNav();
    });
  </script>
</body>
</html>
