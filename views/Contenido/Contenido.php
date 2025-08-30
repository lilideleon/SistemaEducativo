
<?php 
   // Validación de autenticación y permisos de docentes y administradores
   require_once 'core/AuthValidation.php';
   validarRol(['ADMIN', 'DOCENTE', 'DIRECTOR']); // Solo docentes y administradores pueden gestionar contenido
   
   include 'views/Menu/Aside.php';
?>



<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Mi página con sidebar reusable</title>

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

  <!-- Toggle móvil -->
  <div class="container d-md-none my-2">
    <a href="#" class="btn btn-light" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar">
      <i class="bi bi-list"></i> Menú
    </a>
  </div>

  <main class="app-wrapper">


    <!-- Contenido -->
    <section class="main-content">
      <header class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="page-title">Sistema Educativo</h1>
        <a href="?c=Login&a=Logout" class="btn btn-outline-light btn-sm" title="Cerrar Sesión">
          <i class="bi bi-box-arrow-right"></i> Salir
        </a>
      </header>

      <div class="content-panel">
        <div class="wrap">
          <!-- Formulario de carga -->
          <form id="frmUpload" class="mb-3">
            <div class="row g-3 align-items-end">
              <div class="col-md-3">
                <label class="form-label fw-semibold">Curso</label>
                <select id="curso" class="form-select form-select-sm">
                  <option>Matemática</option>
                  <option>Computación</option>
                  <option>Lenguaje</option>
                  <option>Historia</option>
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label fw-semibold">Grado</label>
                <select id="grado" class="form-select form-select-sm">
                  <option value="1°">1°</option>
                  <option value="2°">2°</option>
                  <option value="3°">3°</option>
                  <option value="4°">4°</option>
                  <option value="5°">5°</option>
                  <option value="6°">6°</option>
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label fw-semibold">Unidad</label>
                <select id="unidad" class="form-select form-select-sm">
                  <option value="Unidad 1">Unidad 1</option>
                  <option value="Unidad 2">Unidad 2</option>
                  <option value="Unidad 3">Unidad 3</option>
                  <option value="Unidad 4">Unidad 4</option>
                  <option value="Unidad 5">Unidad 5</option>
                  <option value="Unidad 6">Unidad 6</option>
                  <option value="Unidad 7">Unidad 7</option>
                  <option value="Unidad 8">Unidad 8</option>
                  <option value="Unidad 9">Unidad 9</option>
                  <option value="Unidad 10">Unidad 10</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label fw-semibold">Título del material</label>
                <input id="titulo" class="form-control form-control-sm" placeholder="Ej.: Unidad 2 — Álgebra / Guía y diapositivas">
              </div>
              <div class="col-md-2 text-md-end">
                <a href="#" id="lnkPublicar" class="link-btn">Publicar</a>
              </div>
            </div>

            <div class="mt-3">
              <label class="form-label fw-semibold">Descripción (opcional)</label>
              <textarea id="desc" class="form-control" rows="2" placeholder="Breve contexto del material..."></textarea>
            </div>

            
          </form>


          <!-- Publicados -->
          <hr class="my-4"/>
          <div class="d-flex align-items-center justify-content-between mb-2">
            <h5 class="mb-0">Material publicado</h5>
            <small class="text-muted">Acciones: <i class="bi bi-trash"></i> eliminar</small>
          </div>

          <div class="table-wrap">
            <table class="table table-sm table-bordered mb-0" id="tblPub">
              <thead>
                <tr>
                  <th>ID</th><th>Título</th><th>Curso</th><th>Grado</th><th>Unidad</th><th>Fecha</th><th>Acciones</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>

          <div class="foot mt-3"></div>
        </div>
      </div>
    </section>
  </main>

  <script>
    // ======= Estado =======
    const q = sel => document.querySelector(sel);
    const byId = id => document.getElementById(id);

    let published = []; // materiales publicados
    let seq = 1;

    // ======= Utilidades =======
    

    // ======= Manejo de selección =======
    

    // ======= Publicación =======
    function publish(){
      const curso = byId('curso').value;
      const grado = byId('grado').value;
      const unidad = byId('unidad')?.value || '';
      const titulo = byId('titulo').value.trim() || 'Material sin título';
      const desc = byId('desc').value.trim();

      const item = {
        pid: seq++,
        titulo,
        curso,
        grado,
        unidad,
        fecha: new Date(),
        desc
      };
      published.unshift(item);
      renderPublished();
    }

    function renderPublished(){
      const tbody = q('#tblPub tbody');
      tbody.innerHTML = published.map(p => `
        <tr data-id="${p.pid}">
          <td>${p.pid}</td>
          <td>${p.titulo}</td>
          <td>${p.curso}</td>
          <td>${p.grado || '-'}</td>
          <td>${p.unidad || '-'}</td>
          <td>${p.fecha.toLocaleString()}</td>
          <td class="text-nowrap">
            <a class="link-danger px-1 py-0" href="#" data-action="del" title="Eliminar"><i class="bi bi-trash"></i></a>
          </td>
        </tr>
      `).join('');
    }

    // ======= Eventos =======
    byId('lnkPublicar').addEventListener('click', e => { e.preventDefault(); publish(); });
    

    // Eliminar publicado
    q('#tblPub tbody').addEventListener('click', e => {
      const a = e.target.closest('a[data-action="del"]'); if(!a) return;
      e.preventDefault();
      const tr = a.closest('tr'); const id = Number(tr.dataset.id);
      published = published.filter(x => x.pid !== id);
      renderPublished();
    });

    // Demo: un registro inicial
    published.push({
      pid: seq++, titulo:'Sílabos y guía de ejercicios — Unidad 1',
      curso:'Matemática', grado:'-', unidad:'Unidad 1', fecha: new Date(), desc:''
    });
    renderPublished();
  </script>
</body>
</html>
