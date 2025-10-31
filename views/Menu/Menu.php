<?php 
   // Validación de autenticación
   require_once 'core/AuthValidation.php';
   validarAutenticacion();
   
   include 'views/Menu/Aside.php';
?>



<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Menu</title>

  <!-- Bootstrap 5 + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />

  <style>
    :root{ --bg-teal:#4f8f8a; --sidebar-header:#a8c0bb; }
    html,body{height:100%;margin:0}
    body{background-color:var(--bg-teal);display:flex;flex-direction:column}
    .app-wrapper{display:flex;flex:1;height:100vh}
    .sidebar{width:260px;height:100vh;position:fixed;left:0;top:0;overflow-y:auto;background:#50938a;padding:1rem;box-shadow:2px 0 5px rgba(0,0,0,.1);z-index:1000}
    .sidebar-box{background:rgba(255,255,255,.1);border-radius:.5rem;padding:.75rem}
    .sidebar-header{background:var(--sidebar-header);color:#1f2937;font-weight:600;padding:.5rem .75rem;border-radius:.35rem;margin-bottom:.75rem}
    .btn-glow{border:0;border-radius:1rem;padding:.9rem 1.1rem;color:#fff;font-weight:700;background:
      radial-gradient(140% 120% at 50% 40%, rgba(255,255,255,.28) 0%, rgba(255,255,255,.08) 42%, rgba(0,0,0,.22) 75%),
      linear-gradient(180deg,#0f1c2e 0%,#1f3554 100%);box-shadow:0 8px 18px rgba(0,0,0,.25), inset 0 -2px 0 rgba(255,255,255,.1);
      transition:transform .12s ease, filter .12s ease; text-align:left}
    .btn-glow.active{outline:2px solid rgba(255,255,255,.35)}

    /* contenido */
    section.main-content{margin-left:260px;width:calc(100% - 260px);padding:2rem;min-height:100vh;display:flex;flex-direction:column}
    .content-panel{flex:1;background:#fff;border-radius:.5rem;padding:1.5rem;box-shadow:0 .125rem .25rem rgba(0,0,0,.075)}

    /* móvil: sin sidebar fijo */
    @media (max-width:767.98px){
      section.main-content{margin-left:0 !important;width:100% !important;padding:1rem !important;min-height:calc(100vh - 56px)}
    }

    /* Si NO quieres sidebar en alguna página, añade class="no-sidebar" al body */
    body.no-sidebar section.main-content{margin-left:0;width:100%}

    /* ===== Scoped UI enhancements for Menu content ===== */
    section.main-content .page-title{font-weight:800;color:#1f2937;margin:0}
    section.main-content .page-title::after{content:"";display:block;width:56px;height:4px;border-radius:3px;margin-top:.35rem;background:linear-gradient(90deg,#117867,#15a085)}
    section.main-content .content-panel{border:1px solid rgba(17,120,103,.08);background:linear-gradient(180deg,#ffffff 0%,#fbfefe 100%)}
    .control-chip{display:inline-flex;align-items:center;gap:.4rem;background:#f3faf8;border:1px solid rgba(17,120,103,.12);padding:.4rem .6rem;border-radius:.5rem;color:#0b4f44;font-weight:600}
    .input-slim{border-radius:.5rem;border-color:rgba(0,0,0,.15)}
    .input-slim:focus{border-color:#15a085;box-shadow:0 0 0 .2rem rgba(21,160,133,.15)}
    .btn-run{border:0;border-radius:.5rem;background:linear-gradient(135deg,#117867,#15a085);font-weight:700}
    .btn-run:hover{filter:brightness(1.03)}
    .hint{color:#64748b}
    /* table */
    .results-table thead th{background:linear-gradient(180deg,#0f1c2e,#1f3554);color:#fff;border-color:#14253f}
    .results-table tbody tr:hover{background:#f6fbfa}
    .badge-score{min-width:64px}
    .rank-cell{display:flex;align-items:center;gap:.35rem}
    .rank-top{color:#eab308}
  </style>
  </style>
</head>
<body>
  <main class="app-wrapper">
    <!-- AQUÍ “se inyecta” el sidebar reutilizable -->
    <div data-include="sidebar.html"></div>

    <!-- Tu contenido propio -->
    <section class="col-12 main-content">
      <header class="d-flex align-items-center justify-content-between mb-4">
        <div>
          <h1 class="page-title h3">Sistema Educativo</h1>
          <p class="hint mb-0">Selecciona una opción del menú para comenzar</p>
        </div>
        <a href="?c=Inicio&a=Logout" class="btn btn-outline-light btn-sm" title="Cerrar Sesión">
          <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
        </a>
      </header>

      <div class="content-panel">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <h2 class="h6 text-black-50 mb-0">Alumnos con mejor promedio</h2>
          <div class="d-flex align-items-center gap-2">
            <span class="control-chip" title="Encuesta">
              <i class="bi bi-clipboard-check"></i>
              <input type="number" min="1" class="form-control form-control-sm input-slim" id="encuestaId" value="1" title="ID de Encuesta" style="width:120px;background:transparent;border:0;outline:none" />
            </span>
            <span class="control-chip" title="Límite de filas">
              <i class="bi bi-list-ol"></i>
              <input type="number" min="1" class="form-control form-control-sm input-slim" id="limit" value="10" title="Límite" style="width:100px;background:transparent;border:0;outline:none" />
            </span>
            <button class="btn btn-sm btn-run" id="btnCargar"><i class="bi bi-arrow-repeat"></i> Cargar</button>
          </div>
        </div>

        <div class="d-flex align-items-center p-3 mb-3 border rounded" style="background:linear-gradient(180deg,#eef7f5,#f9fdfc);border-color:rgba(17,120,103,.12)">
          <i class="bi bi-trophy-fill text-warning me-2 fs-5" aria-hidden="true"></i>
          <div>
            <div class="fw-semibold mb-0">Alumnos con mejor promedio</div>
            <small class="text-secondary">Listado de los alumnos con mejor promedio para la encuesta seleccionada.</small>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-sm table-striped align-middle results-table">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Alumno</th>
                <th>Correctas</th>
                <th>Total</th>
                <th>Puntaje</th>
              </tr>
            </thead>
            <tbody id="tablaResultados">
              <tr><td colspan="5" class="text-center text-muted">Sin datos</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </main>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Opción A: usar include.js (archivo externo) -->
  <script src="include.js"></script>

  <script>
    async function cargarResultados() {
      const encuestaId = document.getElementById('encuestaId').value || 1;
      const limit = document.getElementById('limit').value || 10;
      const url = `?c=Menu&a=TopResultadosEncuesta&encuestaId=${encodeURIComponent(encuestaId)}&limit=${encodeURIComponent(limit)}`;
      const tbody = document.getElementById('tablaResultados');
      tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted">Cargando...</td></tr>`;

      try {
        const res = await fetch(url);
        const data = await res.json();
        if (!Array.isArray(data) || data.length === 0) {
          tbody.innerHTML = `<tr><td colspan=\"5\" class=\"text-center text-muted\">Sin datos</td></tr>`;
          return;
        }

        // Tabla
        tbody.innerHTML = data.map((r, idx) => `
          <tr>
            <td>${idx + 1}</td>
            <td>${r.alumno_nombre ?? ''}</td>
            <td>${r.correctas ?? 0}</td>
            <td>${r.total ?? 0}</td>
            <td><span class="badge bg-primary">${r.puntaje ?? 0}</span></td>
          </tr>
        `).join('');
      } catch (e) {
        console.error(e);
        tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">Error al cargar datos</td></tr>`;
      }
    }

    document.getElementById('btnCargar').addEventListener('click', cargarResultados);
    // auto cargar al abrir
    cargarResultados();
  </script>

  <!-- Opción B (alternativa): snippet inline en lugar de include.js)
  <script>
    (async () => {
      const slots = document.querySelectorAll('[data-include]');
      await Promise.all([...slots].map(async el => {
        const url = el.getAttribute('data-include');
        const html = await fetch(url).then(r=>r.text());
        el.outerHTML = html;
      }));
      // marcar activo
      const current = location.pathname.split('/').pop();
      document.querySelectorAll('.sidebar a, .offcanvas-body a').forEach(a=>{
        if(a.getAttribute('href') === current) a.classList.add('active');
      });
    })();
  </script>
  -->
</body>
</html>
