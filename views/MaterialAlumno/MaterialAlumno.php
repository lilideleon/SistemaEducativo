<?php 
   // Solo alumnos (vista de solo lectura)
   require_once 'core/AuthValidation.php';
   validarRol(['ALUMNO']);
   include 'views/Menu/Aside.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Material - Alumno</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
  <!-- DataTables -->
  <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
  <style>
    :root{ --bg-teal:#4f8f8a; --sidebar-header:#a8c0bb; }
    html,body{height:100%;margin:0}
    body{background-color:var(--bg-teal);display:flex;flex-direction:column}
    .app-wrapper{display:flex;flex:1;height:100vh}

    /* Sidebar */
    .sidebar{width:260px;height:100vh;position:fixed;left:0;top:0;overflow-y:auto;background:#50938a;padding:1rem;box-shadow:2px 0 5px rgba(0,0,0,.1);z-index:1000}
    .sidebar-box{background:rgba(255,255,255,.1);border-radius:.5rem;padding:.75rem}
    .sidebar-header{background:var(--sidebar-header);color:#1f2937;font-weight:600;padding:.5rem .75rem;border-radius:.35rem;margin-bottom:.75rem}
    .btn-glow{border:0;border-radius:1rem;padding:.9rem 1.1rem;color:#fff;font-weight:700;background:
      radial-gradient(140% 120% at 50% 40%, rgba(255,255,255,.28) 0%, rgba(255,255,255,.08) 42%, rgba(0,0,0,.22) 75%),
      linear-gradient(180deg,#0f1c2e 0%,#1f3554 100%);box-shadow:0 8px 18px rgba(0,0,0,.25), inset 0 -2px 0 rgba(255,255,255,.1);
      transition:transform .12s ease, filter .12s ease; text-align:left}

    /* Contenido */
    section.main-content{margin-left:260px;width:calc(100% - 260px);padding:2rem;min-height:100vh;display:flex;flex-direction:column}
    .content-panel{flex:1;background:#fff;border-radius:.5rem;padding:1.5rem;box-shadow:0 .125rem .25rem rgba(0,0,0,.075)}
    .table-wrap{background:#fff;border-radius:.5rem;border:1px solid rgba(0,0,0,.1)}
    /* ===== Enhancements (scoped to this page) ===== */
    #cursoFiltro.form-select{ border-radius:.5rem; border-color:rgba(0,0,0,.15) }
    #cursoFiltro.form-select:focus{ border-color:#0f766e; box-shadow:0 0 0 .2rem rgba(15,118,110,.15) }
    #btnFiltrar.btn{ border-radius:.5rem }

    #tblPubAlu thead th{
      background: linear-gradient(180deg,#0f1c2e,#1f3554);
      color:#fff; border-color:#14253f;
    }
    #tblPubAlu tbody tr:hover{ background:#f6fbfa }
    #tblPubAlu td:nth-child(3), /* Curso */
    #tblPubAlu td:nth-child(4), /* Grado */
    #tblPubAlu td:nth-child(5){ /* Unidad */
      white-space: nowrap;
    }
    .badge-soft{ display:inline-block; padding:.2rem .45rem; border-radius:999px; font-size:.8rem; font-weight:600 }
    .badge-soft-green{ background:rgba(17,120,103,.10); color:#0b4f44 }
    .badge-soft-blue{ background:#eef5ff; color:#204a7a }
    .badge-soft-gray{ background:#f4f6f8; color:#374151 }

    /* Acciones de archivos */
    .action-icon{ display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; color:#1f3554; text-decoration:none; transition:background-color .12s ease }
    .action-icon:hover{ background:#eef5fb }
  </style>
</head>
<body>

  <!-- Toggle móvil -->
  <div class="container d-md-none my-2">
    <button class="btn btn-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar">
      <i class="bi bi-list"></i> Menú
    </button>
  </div>

  <main class="app-wrapper">
    <section class="main-content">
      <header class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="page-title h4 mb-0">Material disponible</h1>
        <a href="?c=Inicio&a=Logout" class="btn btn-outline-light btn-sm" title="Cerrar Sesión">
          <i class="bi bi-box-arrow-right"></i> cerrar sesion
        </a>
      </header>

      <div class="content-panel">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="fw-semibold">Listado de materiales</div>
          <div class="d-flex gap-2">
            <select id="cursoFiltro" class="form-select form-select-sm" style="min-width:240px">
              <option value="">Todos los cursos</option>
            </select>
            <button id="btnFiltrar" class="btn btn-sm btn-primary">Filtrar</button>
          </div>
        </div>

        <div class="table-wrap">
          <table class="table table-sm table-bordered table-striped table-hover align-middle mb-0" id="tblPubAlu">
            <thead>
              <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Curso</th>
                <th>Grado</th>
                <th>Unidad</th>
                <th>Institución</th>
                <th>Fecha</th>
                <th>Archivos</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </section>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- jQuery y DataTables -->
  <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
  <script>
    // Variable para la instancia de DataTable
    let dataTable;

    async function api(url){
      const r = await fetch(url);
      return r.json();
    }

    async function cargarCursos(){
      try{
        const res = await api('?c=MaterialAlumno&a=ListarCursos');
        if(res.success && Array.isArray(res.data)){
          const sel = document.querySelector('#cursoFiltro');
          sel.innerHTML = '<option value="">Todos los cursos</option>' + res.data.map(c=>`<option value="${c.id}">${c.nombre}</option>`).join('');
        }
      }catch(e){}
    }

    async function listar(){
      const curso = document.querySelector('#cursoFiltro').value;
      const url = curso ? `?c=MaterialAlumno&a=Listar&curso_id=${encodeURIComponent(curso)}` : '?c=MaterialAlumno&a=Listar';
      const res = await api(url);
      
      // Destruir DataTable existente si hay uno
      if(dataTable) {
        dataTable.destroy();
      }

      const tbody = document.querySelector('#tblPubAlu tbody');
      if(!res.success){ 
        tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Error</td></tr>'; 
        return; 
      }
      
      const rows = res.data || [];
      tbody.innerHTML = rows.map(p=>`
        <tr>
          <td>${p.id}</td>
          <td>${p.titulo}</td>
          <td>${p.curso_nombre ? `<span class="badge-soft badge-soft-blue">${p.curso_nombre}</span>` : ''}</td>
          <td>${p.grado_nombre ? `<span class="badge-soft badge-soft-gray">${p.grado_nombre}</span>` : ''}</td>
          <td>${p.unidad_numero ? `<span class=\"badge-soft badge-soft-green\">Unidad ${p.unidad_numero}</span>` : '-'}</td>
          <td>${p.institucion_nombre||''}</td>
          <td>${p.publicado_at ? new Date(p.publicado_at).toLocaleString() : ''}</td>
          <td>${(p.archivos||[]).map(a=>`
              <a class='action-icon me-1' href='${a.url}' target='_blank' title='Ver' aria-label='Ver'>
                <i class='bi bi-eye'></i>
              </a>
              <a class='action-icon me-1' href='${a.url}' download='${a.nombre_archivo}' title='Descargar' aria-label='Descargar'>
                <i class='bi bi-download'></i>
              </a>`).join('')}
          </td>
        </tr>`).join('');

      // Inicializar DataTable
      dataTable = $('#tblPubAlu').DataTable({
        language: {
          url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        order: [[0, 'desc']], // Ordenar por ID descendente por defecto
        pageLength: 10,
        responsive: true,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]]
      });
    }

    document.querySelector('#btnFiltrar').addEventListener('click', listar);

    cargarCursos().then(listar);
  </script>
</body>
</html>
