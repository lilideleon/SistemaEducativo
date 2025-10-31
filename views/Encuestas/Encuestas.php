<?php 
   // Solo administradores
   require_once 'core/AuthValidation.php';
   validarRol(['ADMIN']);
   include 'views/Menu/Aside.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Encuestas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    :root{ --bg-teal:#4f8f8a; --sidebar-header:#a8c0bb; }
    html,body{height:100%;margin:0}
    body{background-color:var(--bg-teal);display:flex;flex-direction:column}
    .app-wrapper{display:flex;flex:1;height:100vh}

    /* Sidebar (igual que otros módulos) */
    .sidebar{
      width:260px;height:100vh;position:fixed;left:0;top:0;overflow-y:auto;
      background:#50938a;padding:1rem;box-shadow:2px 0 5px rgba(0,0,0,.1);z-index:1000;
    }
    .sidebar-box{background:rgba(255,255,255,.1);border-radius:.5rem;padding:.75rem}
    .sidebar-header{background:var(--sidebar-header);color:#1f2937;font-weight:600;
      padding:.5rem .75rem;border-radius:.35rem;margin-bottom:.75rem}
    .btn-glow{border:0;border-radius:1rem;padding:.9rem 1.1rem;color:#fff;font-weight:700;background:
      radial-gradient(140% 120% at 50% 40%, rgba(255,255,255,.28) 0%, rgba(255,255,255,.08) 42%, rgba(0,0,0,.22) 75%),
      linear-gradient(180deg,#0f1c2e 0%,#1f3554 100%);
      box-shadow:0 8px 18px rgba(0,0,0,.25), inset 0 -2px 0 rgba(255,255,255,.1);
      transition:transform .12s ease, filter .12s ease; text-align:left
    }
    .btn-glow:hover{transform:translateY(-2px);filter:brightness(1.03)}
    .btn-glow.active{outline:2px solid rgba(255,255,255,.35)}

    /* Contenido principal */
    section.main-content{margin-left:260px;width:calc(100% - 260px);padding:2rem;min-height:100vh;display:flex;flex-direction:column}
    .content-panel{flex:1;background:#fff;border-radius:.5rem;padding:1.5rem;box-shadow:0 .125rem .25rem rgba(0,0,0,.075)}
    @media (max-width:767.98px){ section.main-content{margin-left:0 !important;width:100% !important;padding:1rem !important;min-height:calc(100vh - 56px)} }
    .form-title{font-weight:700;color:#234c4a;margin-bottom:.5rem}
    .btn-registrar{background:#1f3554;color:#fff;border:0;border-radius:.25rem;padding:.45rem .9rem}
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
        <h1 class="page-title h4 mb-0">Encuestas</h1>
        <a href="?c=Inicio&a=Logout" class="btn btn-outline-light btn-sm" title="Cerrar Sesión">
          <i class="bi bi-box-arrow-right"></i> cerrar sesion
        </a>
      </header>

      <div class="content-panel">
        <div class="form-title">Crear / Editar Encuesta</div>
        <form id="frmEncuesta" class="row g-3 align-items-end mb-3">
          <input type="hidden" id="enc_id" />
          <div class="col-md-4">
            <label class="form-label">Título</label>
            <input type="text" id="titulo" class="form-control form-control-sm" required />
          </div>
          <div class="col-md-3">
            <label class="form-label">Curso</label>
            <select id="curso_id" class="form-select form-select-sm" required></select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Grado</label>
            <select id="grado_id" class="form-select form-select-sm" required></select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Unidad</label>
            <select id="unidad_numero" class="form-select form-select-sm" required>
              <option value="1">Primera unidad</option>
              <option value="2">Segunda unidad</option>
              <option value="3">Tercera unidad</option>
              <option value="4">Cuarta unidad</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Institución (opcional)</label>
            <select id="institucion_id" class="form-select form-select-sm"></select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Descripción</label>
            <input type="text" id="descripcion" class="form-control form-control-sm" />
          </div>
          <div class="col-md-3">
            <label class="form-label">Inicio</label>
            <input type="datetime-local" id="fecha_inicio" class="form-control form-control-sm" />
          </div>
          <div class="col-md-3">
            <label class="form-label">Fin</label>
            <input type="datetime-local" id="fecha_fin" class="form-control form-control-sm" />
          </div>
          <div class="col-md-2">
            <label class="form-label">Estado</label>
            <select id="estado" class="form-select form-select-sm">
              <option value="ACTIVA" selected>ACTIVA</option>
              <option value="BORRADOR">BORRADOR</option>
              <option value="CERRADA">CERRADA</option>
            </select>
          </div>
          <div class="col-12 text-end">
            <button class="btn btn-registrar" type="submit" id="btnGuardar"><i class="bi bi-save"></i> Guardar</button>
          </div>
        </form>

        <div class="table-responsive">
          <table class="table table-sm table-bordered mb-0" id="tblEncuestas">
            <thead>
              <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Curso</th>
                <th>Grado</th>
                <th>Unidad</th>
                <th>Institución</th>
                <th>Estado</th>
                <th>Inicio</th>
                <th>Fin</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </section>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const $ = (s) => document.querySelector(s);
    const $$ = (s) => Array.from(document.querySelectorAll(s));

    async function api(url, opts){
      const r = await fetch(url, opts);
      return r.json();
    }

    async function cargarCombos(){
      try {
        const [cursos, grados, insts] = await Promise.all([
          api('?c=Encuestas&a=ListarCursos'),
          api('?c=Encuestas&a=ListarGrados'),
          api('?c=Encuestas&a=ListarInstituciones')
        ]);
        const fill = (sel, data, placeholder) => {
          sel.innerHTML = placeholder ? `<option value="">${placeholder}</option>` : '';
          if (data && data.success && Array.isArray(data.data)){
            sel.innerHTML += data.data.map(d=>`<option value="${d.id}">${d.nombre}</option>`).join('');
          }
        };
        fill($('#curso_id'), cursos, 'Seleccione curso');
        fill($('#grado_id'), grados, 'Seleccione grado');
        fill($('#institucion_id'), insts, 'Todas');
      } catch(e){ console.error(e); }
    }

    async function listar(){
      const res = await api('?c=Encuestas&a=Listar');
      const tbody = $('#tblEncuestas tbody');
      if(!res.success){ tbody.innerHTML = '<tr><td colspan="9" class="text-center text-danger">Error</td></tr>'; return; }
      const rows = res.data || [];
      tbody.innerHTML = rows.map(r => `
        <tr data-id="${r.id}">
          <td>${r.id}</td>
          <td>${r.titulo||''}</td>
          <td>${r.curso||''}</td>
          <td>${r.grado||''}</td>
          <td>Unidad ${r.unidad_numero || 1}</td>
          <td>${r.institucion||''}</td>
          <td>${r.estado||''}</td>
          <td>${r.inicio||''}</td>
          <td>${r.fin||''}</td>
          <td class="text-nowrap">
            <button class="btn btn-sm btn-outline-primary me-1" onclick="editar(${r.id})"><i class="bi bi-pencil-square"></i></button>
            <button class="btn btn-sm btn-outline-danger" onclick="eliminar(${r.id})"><i class="bi bi-trash"></i></button>
          </td>
        </tr>`).join('');
    }

    async function editar(id){
      const res = await api(`?c=Encuestas&a=Obtener&id=${id}`);
      if(!res.success || !res.data){ alert(res.msj||'No encontrado'); return; }
      $('#enc_id').value = res.data.id;
      $('#titulo').value = res.data.titulo||'';
      $('#curso_id').value = res.data.curso_id||'';
      $('#grado_id').value = res.data.grado_id||'';
      $('#unidad_numero').value = res.data.unidad_numero || '1';
      $('#institucion_id').value = res.data.institucion_id||'';
      $('#descripcion').value = res.data.descripcion||'';
      if(res.data.fecha_inicio) $('#fecha_inicio').value = res.data.fecha_inicio.replace(' ','T').slice(0,16);
      if(res.data.fecha_fin) $('#fecha_fin').value = res.data.fecha_fin.replace(' ','T').slice(0,16);
      $('#estado').value = res.data.estado||'ACTIVA';
      $('#titulo').focus();
    }

    async function eliminar(id){
      if(!confirm('¿Eliminar encuesta?')) return;
      const form = new FormData(); form.append('id', id);
      const res = await api('?c=Encuestas&a=Eliminar', { method:'POST', body: form });
      alert(res.msj || (res.success ? 'Eliminado' : 'Error'));
      if(res.success) listar();
    }

    $('#frmEncuesta').addEventListener('submit', async (e)=>{
      e.preventDefault();
      const id = $('#enc_id').value.trim();
      const data = new FormData();
      data.append('titulo', $('#titulo').value.trim());
      data.append('curso_id', $('#curso_id').value);
      data.append('grado_id', $('#grado_id').value);
      data.append('unidad_numero', $('#unidad_numero').value);
      data.append('institucion_id', $('#institucion_id').value);
      data.append('descripcion', $('#descripcion').value.trim());
      data.append('fecha_inicio', $('#fecha_inicio').value);
      data.append('fecha_fin', $('#fecha_fin').value);
      data.append('estado', $('#estado').value);
      const url = id ? '?c=Encuestas&a=Actualizar' : '?c=Encuestas&a=Agregar';
      if(id) data.append('id', id);
      const res = await api(url, { method: 'POST', body: data });
      alert(res.msj || (res.success ? 'Guardado' : 'Error'));
      if(res.success){
        e.target.reset();
        $('#enc_id').value = '';
        listar();
      }
    });

    cargarCombos().then(listar);
  </script>
</body>
</html>
