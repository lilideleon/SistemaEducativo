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
          <h1 class="h3">Sistema Educativo</h1>
          <p class="text-muted">Selecciona una opción del menú para comenzar</p>
        </div>
        <a href="?c=Login&a=Logout" class="btn btn-outline-light btn-sm" title="Cerrar Sesión">
          <i class="bi bi-box-arrow-right"></i> Salir
        </a>
      </header>

      <div class="content-panel">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <h2 class="h6 text-black-50 mb-0">Dashboard de Resultados</h2>
          <div class="d-flex gap-2">
            <input type="number" min="1" class="form-control form-control-sm" id="encuestaId" value="1" title="ID de Encuesta" style="width:120px" />
            <input type="number" min="1" class="form-control form-control-sm" id="limit" value="10" title="Límite" style="width:100px" />
            <button class="btn btn-sm btn-primary" id="btnCargar"><i class="bi bi-arrow-repeat"></i> Cargar</button>
          </div>
        </div>

        <div class="row g-3 mb-3" id="cards">
          <div class="col-12 col-md-4">
            <div class="p-3 border rounded bg-light">
              <div class="text-secondary">Participantes</div>
              <div class="fs-4 fw-semibold" id="kpiParticipantes">-</div>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="p-3 border rounded bg-light">
              <div class="text-secondary">Puntaje promedio</div>
              <div class="fs-4 fw-semibold" id="kpiPromedio">-</div>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="p-3 border rounded bg-light">
              <div class="text-secondary">Respuestas correctas totales</div>
              <div class="fs-4 fw-semibold" id="kpiCorrectas">-</div>
            </div>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-sm table-striped align-middle">
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
          tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted">Sin datos</td></tr>`;
          document.getElementById('kpiParticipantes').textContent = '0';
          document.getElementById('kpiPromedio').textContent = '0';
          document.getElementById('kpiCorrectas').textContent = '0';
          return;
        }

        // KPIs
        const participantes = data.length;
        const sumPuntaje = data.reduce((acc, r) => acc + (parseFloat(r.puntaje) || 0), 0);
        const sumCorrectas = data.reduce((acc, r) => acc + (parseInt(r.correctas) || 0), 0);
        const promedio = participantes ? (sumPuntaje / participantes) : 0;

        document.getElementById('kpiParticipantes').textContent = participantes;
        document.getElementById('kpiPromedio').textContent = promedio.toFixed(2);
        document.getElementById('kpiCorrectas').textContent = sumCorrectas;

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
