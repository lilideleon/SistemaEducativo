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
      --bg-teal: #4f8f8a;          /* fondo general estilo maqueta */
      --panel-border: #2e655f;     /* bordes áreas de contenido */
      --title-blue: #235c9c;       /* título superior */
      --sidebar-header: #a8c0bb;   /* cabecera "Principal" */
    }

    body {
      min-height: 100vh;
      background-color: var(--bg-teal);
      display: flex;
      flex-direction: column;
    }

    /* Título centrado dentro de una caja blanca, como en la imagen */
    .title-box {
      background: #ffffff;
      color: var(--title-blue);
      font-weight: 700;
      border-radius: .25rem;
      display: inline-block;
      padding: .4rem 1.5rem;
      box-shadow: 0 2px 0 rgba(0,0,0,.25) inset;
    }

    /* Layout principal */
    .app-wrapper { flex: 1; }

    /* Sidebar */
    .sidebar {
      max-width: 260px;
      width: 100%;
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
      text-align: left;
      margin-bottom: .75rem;
    }

    /* Botones estilo "capsula" con brillo azulado */
    .btn-glow {
      position: relative;
      border: 0;
      border-radius: 1rem;
      padding: .9rem 1.1rem;
      color: #fff;
      font-weight: 700;
      letter-spacing: .2px;
      background:
        radial-gradient(140% 120% at 50% 40%, rgba(255,255,255,.28) 0%, rgba(255,255,255,.08) 42%, rgba(0,0,0,.22) 75%),
        linear-gradient(180deg, #0f1c2e 0%, #1f3554 100%);
      box-shadow: 0 8px 18px rgba(0,0,0,.25), inset 0 -2px 0 rgba(255,255,255,.1);
      transition: transform .12s ease, box-shadow .12s ease, filter .12s ease;
      text-align: left;
    }
    .btn-glow:hover { transform: translateY(-2px); filter: brightness(1.02); }
    .btn-glow.active { outline: 2px solid rgba(255,255,255,.35); }

    /* Área de contenido con bordes marcados como en la maqueta */
    .content-panel {
      background: transparent;
      border: 2px solid var(--panel-border);
      border-radius: .25rem;
      min-height: 36vh;
    }

    @media (max-width: 767.98px) {
      .sidebar { max-width: none; }
    }
  </style>
</head>
<body>
  <!-- Encabezado -->
  <header class="container py-3">
    <div class="d-flex justify-content-center">
      <div class="title-box h5 mb-0">Sistema Educativo</div>
    </div>
  </header>

  <!-- Botón para abrir menú en móviles -->
  <div class="container d-md-none mb-2">
    <button class="btn btn-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar" aria-controls="offcanvasSidebar">
      <i class="bi bi-list"></i> Menú
    </button>
  </div>

  <!-- Contenido principal -->
  <main class="container app-wrapper pb-4">
    <div class="row g-3">
      <!-- Sidebar (desktop) -->
      <aside class="col-md-3 col-lg-2 d-none d-md-block sidebar">
        <div class="sidebar-box">
          <div class="sidebar-header">Principal</div>
          <div class="d-grid gap-3">
            <button class="btn btn-glow fs-5" data-section="menu"><i class="bi bi-grid-fill me-2"></i>Menú</button>
            <button class="btn btn-glow fs-5" data-section="alumnos"><i class="bi bi-people-fill me-2"></i>Alumnos</button>
            <button class="btn btn-glow fs-5" data-section="directores"><i class="bi bi-person-gear me-2"></i>Directores</button>
            <button class="btn btn-glow fs-5" data-section="evaluacion"><i class="bi bi-clipboard-check-fill me-2"></i>Evaluación</button>
            <button class="btn btn-glow fs-5" data-section="reportes"><i class="bi bi-bar-chart-fill me-2"></i>Reportes</button>
          </div>
        </div>
      </aside>

      <!-- Offcanvas (mobile) -->
      <div class="offcanvas offcanvas-start text-bg-dark" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
        <div class="offcanvas-header">
          <h5 class="offcanvas-title" id="offcanvasSidebarLabel">Principal</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
        </div>
        <div class="offcanvas-body">
          <div class="d-grid gap-3">
            <button class="btn btn-glow fs-5" data-bs-dismiss="offcanvas" data-section="menu"><i class="bi bi-grid-fill me-2"></i>Menú</button>
            <button class="btn btn-glow fs-5" data-bs-dismiss="offcanvas" data-section="alumnos"><i class="bi bi-people-fill me-2"></i>Alumnos</button>
            <button class="btn btn-glow fs-5" data-bs-dismiss="offcanvas" data-section="directores"><i class="bi bi-person-gear me-2"></i>Directores</button>
            <button class="btn btn-glow fs-5" data-bs-dismiss="offcanvas" data-section="evaluacion"><i class="bi bi-clipboard-check-fill me-2"></i>Evaluación</button>
            <button class="btn btn-glow fs-5" data-bs-dismiss="offcanvas" data-section="reportes"><i class="bi bi-bar-chart-fill me-2"></i>Reportes</button>
          </div>
        </div>
      </div>

      <!-- Área de contenido -->
      <section class="col-md-9 col-lg-10">
        <div class="content-panel mb-3 p-3" id="panelTop">
          <h2 class="h6 text-white-50 mb-3" id="contentTitle">Bienvenido</h2>
          <div id="contentArea" class="text-white">
            <p class="mb-0">Selecciona una opción del menú para cargar contenido aquí.</p>
          </div>
        </div>
        <div class="content-panel p-3" id="panelBottom">
          <h2 class="h6 text-white-50 mb-3">Panel secundario</h2>
          <p class="text-white-50 mb-0">Espacio para tablas, gráficos o formularios.</p>
        </div>
      </section>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Interacciones simples de ejemplo
    const items = document.querySelectorAll('[data-section]');
    const title = document.getElementById('contentTitle');
    const area  = document.getElementById('contentArea');

    const TEMPLATES = {
      menu: `<div class="row g-3">
              <div class="col-md-6">
                <div class="p-3 bg-dark bg-opacity-25 rounded">Acceso rápido a módulos.</div>
              </div>
              <div class="col-md-6">
                <div class="p-3 bg-dark bg-opacity-25 rounded">Atajos configurables.</div>
              </div>
            </div>`,
      alumnos: `<div class="bg-dark bg-opacity-25 rounded p-3">Listado, inscripción, historial académico…</div>`,
      directores: `<div class="bg-dark bg-opacity-25 rounded p-3">Gestión de directores y permisos.</div>`,
      evaluacion: `<div class="bg-dark bg-opacity-25 rounded p-3">Rubricas, calificaciones y reportes parciales.</div>`,
      reportes: `<div class="bg-dark bg-opacity-25 rounded p-3">KPIs, exportación y tableros.</div>`
    };

    function setActive(btn){
      items.forEach(i => i.classList.remove('active'));
      btn.classList.add('active');
    }

    items.forEach(btn => {
      btn.addEventListener('click', () => {
        const section = btn.getAttribute('data-section');
        setActive(btn);
        title.textContent = section.charAt(0).toUpperCase() + section.slice(1);
        area.innerHTML = TEMPLATES[section] || '';
      });
    });
  </script>
</body>
</html>
