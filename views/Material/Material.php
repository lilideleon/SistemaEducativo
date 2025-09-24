<?php 
   // Validación de autenticación y permisos de docentes y administradores
   require_once 'core/AuthValidation.php';
   validarRol(['ADMIN', 'DOCENTE', 'DIRECTOR']); // Solo docentes y administradores pueden gestionar material
   
   include 'views/Menu/Aside.php';
?>





<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sistema Educativo</title>

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
                  <!-- Opciones cargadas dinámicamente -->
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label fw-semibold">Grado</label>
                <select id="grado" class="form-select form-select-sm">
                  <!-- Opciones cargadas dinámicamente -->
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label fw-semibold">Unidad</label>
                <select id="unidad" class="form-select form-select-sm">
                  <option value="1">Primera unidad</option>
                  <option value="2">Segunda unidad</option>
                  <option value="3">Tercera unidad</option>
                  <option value="4">Cuarta unidad</option>
                </select>
              </div>
              <div class="col-md-5">
                <label class="form-label fw-semibold">Título del material</label>
                <input id="titulo" class="form-control form-control-sm" placeholder="Ej.: Unidad 2 — Álgebra / Guía y diapositivas">
              </div>
              <div class="col-md-4 text-md-end">
                <a href="#" id="lnkPublicar" class="btn btn-primary btn-sm">
                  <i class="bi bi-cloud-upload me-1"></i>
                  Publicar <span id="qtySel">0</span> archivo(s)
                </a>
                <a href="#" id="lnkLimpiar" class="btn btn-outline-secondary btn-sm ms-2">
                  <i class="bi bi-x-circle me-1"></i>
                  Limpiar selección
                </a>
              </div>
            </div>

            <div class="mt-3">
              <label class="form-label fw-semibold">Descripción (opcional)</label>
              <textarea id="desc" class="form-control" rows="2" placeholder="Breve contexto del material..."></textarea>
            </div>

            <input id="fileInput" type="file" multiple class="d-none"
                   accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.zip,image/*,video/*" />

            <div id="drop" class="dropzone mt-3">
              <div class="mb-1">
                <i class="bi bi-cloud-arrow-up-fill me-1"></i>
                Arrastra y suelta archivos aquí
              </div>
              <div class="dz-hint">o <a href="#" id="lnkElegir" class="btn btn-outline-primary btn-sm ms-1"><i class="bi bi-folder2-open me-1"></i> Elegir archivos</a></div>
            </div>
          </form>

          <!-- Lista de pendientes -->
          <div id="pending" class="d-none">
            <h6 class="mb-2">Archivos seleccionados</h6>
            <div class="vstack gap-2" id="pendingList"></div>
          </div>

          <!-- Publicados -->
          <hr class="my-4"/>
          <div class="d-flex align-items-center justify-content-between mb-2">
            <h5 class="mb-0">Material publicado</h5>
            <small class="text-muted">Acciones: <i class="bi bi-eye"></i> ver • <i class="bi bi-download"></i> descargar • <i class="bi bi-trash"></i> eliminar</small>
          </div>

          <div class="table-wrap">
            <table class="table table-sm table-bordered mb-0" id="tblPub">
              <thead>
                <tr>
                  <th>ID</th><th>Título</th><th>Curso</th><th>Institución</th><th>Tipo</th><th>Tamaño</th><th>Fecha</th><th>Acciones</th>
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

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // ======= Estado =======
    const q = sel => document.querySelector(sel);
    const byId = id => document.getElementById(id);

    // ======= Cargar cursos y grados dinámicamente =======
    async function cargarCursos() {
      try {
        const resp = await fetch('?c=Material&a=ListarCursos');
        const data = await resp.json();
        if(data.success && Array.isArray(data.data)) {
          const sel = byId('curso');
          sel.innerHTML = data.data.map(c => `<option value="${c.nombre}">${c.nombre}</option>`).join('');
        }
      } catch(e) {
        // error
      }
    }
    async function cargarGrados() {
      try {
        const resp = await fetch('?c=Material&a=ListarGrados');
        const data = await resp.json();
        if(data.success && Array.isArray(data.data)) {
          const sel = byId('grado');
          sel.innerHTML = data.data.map(g => `<option value="${g.id}">${g.nombre}</option>`).join('');
        }
      } catch(e) {
        // error
      }
    }
    cargarCursos();
    cargarGrados();

    const drop = byId('drop'), input = byId('fileInput'), list = byId('pendingList');
    const pendingBox = byId('pending'), qtySel = byId('qtySel');

    let queue = [];     // archivos para publicar
    let published = []; // materiales publicados
    let seq = 1;

    // ======= Utilidades =======
    const fmtBytes = b => {
      if(!b && b !== 0) return '-';
      const u = ['B','KB','MB','GB']; let i = 0;
      while(b >= 1024 && i < u.length-1){ b/=1024; i++; }
      return `${b.toFixed(1)} ${u[i]}`;
    };
    const ext = name => (name.split('.').pop() || '').toLowerCase();

    const iconByExt = e => {
      if(['png','jpg','jpeg','gif','webp','bmp','svg'].includes(e)) return 'bi-image';
      if(['pdf'].includes(e))  return 'bi-file-earmark-pdf';
      if(['doc','docx','rtf'].includes(e)) return 'bi-file-earmark-word';
      if(['ppt','pptx','key'].includes(e)) return 'bi-file-earmark-slides';
      if(['xls','xlsx','csv'].includes(e)) return 'bi-file-earmark-excel';
      if(['zip','rar','7z'].includes(e))   return 'bi-file-zip';
      if(['mp4','mov','avi','mkv','webm'].includes(e)) return 'bi-file-earmark-play';
      return 'bi-file-earmark';
    };

    function typeLabel(e){
      if(['png','jpg','jpeg','gif','webp','bmp','svg'].includes(e)) return 'Imagen';
      if(['pdf'].includes(e))  return 'PDF';
      if(['doc','docx','rtf'].includes(e)) return 'Word';
      if(['ppt','pptx','key'].includes(e)) return 'Presentación';
      if(['xls','xlsx','csv'].includes(e)) return 'Excel';
      if(['zip','rar','7z'].includes(e))   return 'Comprimido';
      if(['mp4','mov','avi','mkv','webm'].includes(e)) return 'Video';
      return 'Archivo';
    }

    // ======= Manejo de selección =======
    function addFiles(files){
      for(const f of files){
        const id = crypto.randomUUID?.() || (Date.now() + '-' + Math.random());
        const item = { id, file:f, name:f.name, size:f.size, ext:ext(f.name), url: URL.createObjectURL(f) };
        queue.push(item);
      }
      renderPending();
    }

    function renderPending(){
      qtySel.textContent = queue.length;
      pendingBox.classList.toggle('d-none', queue.length === 0);
      list.innerHTML = queue.map(it => `
        <div class="pending-item" data-id="${it.id}">
          <div class="thumb">
            ${
              ['png','jpg','jpeg','gif','webp','bmp'].includes(it.ext)
              ? `<img src="${it.url}" class="w-100 h-100 rounded" style="object-fit:cover"/>`
              : `<i class="bi ${iconByExt(it.ext)}"></i>`
            }
          </div>
          <div>
            <div class="file-title">${it.name}</div>
            <div class="file-meta">${typeLabel(it.ext)} • ${fmtBytes(it.size)}</div>
          </div>
          <div class="text-end">
            <a href="#" class="link-btn link-danger" data-action="remove">Quitar</a>
          </div>
        </div>
      `).join('');
    }

    // ======= Publicación =======
    async function publish(){
      if(queue.length === 0) return;
      const curso = byId('curso').value;
      const grado = byId('grado').value;
      const unidad = byId('unidad').value;
      const titulo = byId('titulo').value.trim() || 'Material sin título';
      const desc = byId('desc').value.trim();
      const formData = new FormData();
      // Enviar el nombre del curso como curso_nombre (no el id)
      formData.append('curso_nombre', curso);
      formData.append('unidad_numero', unidad);
      formData.append('grado_id', grado);
      formData.append('titulo', titulo);
      formData.append('descripcion', desc);
      queue.forEach((it, idx) => {
        formData.append('archivos[]', it.file, it.name);
      });
      try {
        const resp = await fetch('?c=Material&a=Guardar', {
          method: 'POST',
          body: formData
        });
        const data = await resp.json();
        if(data.success){
          alert('Material publicado correctamente');
          queue = [];
          renderPending();
          cargarMateriales();
        } else {
          alert('Error: ' + (data.msj || 'No se pudo publicar.'));
        }
      } catch(e){
        alert('Error de red o servidor.');
      }
    }

    // Cargar materiales publicados desde el backend
    async function cargarMateriales(){
      try {
        const resp = await fetch('?c=Material&a=Listar');
        const data = await resp.json();
        if(data.success){
          renderPublished(data.data);
        }
      } catch(e){
        // error
      }
    }

    // Modificar renderPublished para aceptar lista
    function renderPublished(lista){
      const tbody = q('#tblPub tbody');
      if(!lista) lista = [];
      tbody.innerHTML = lista.map(p => `
        <tr data-id="${p.id}">
          <td>${p.id}</td>
          <td>${p.titulo}</td>
          <td>${p.curso_nombre || ''}</td>
          <td>${p.institucion_nombre || ''}</td>
          <td>${p.tipo || '-'}</td>
          <td>${fmtBytes(p.size || 0)}</td>
          <td>${p.publicado_at ? (new Date(p.publicado_at)).toLocaleString() : ''}</td>
          <td class="text-nowrap">
            ${(p.archivos||[]).map(a=>`<a class='me-2' href='${a.url}' target='_blank' title='Ver'><i class='bi bi-eye'></i></a><a class='me-2' href='${a.url}' download='${a.nombre_archivo}' title='Descargar'><i class='bi bi-download'></i></a>`).join('')}
            <a class="link-danger px-1 py-0" href="#" data-action="del" title="Eliminar"><i class="bi bi-trash"></i></a>
          </td>
        </tr>
      `).join('');
    }

    // ======= Eventos =======
    byId('lnkElegir').addEventListener('click', e => { e.preventDefault(); input.click(); });
    input.addEventListener('change', e => addFiles(e.target.files));

    ;['dragenter','dragover'].forEach(ev => drop.addEventListener(ev, e => { e.preventDefault(); drop.classList.add('drag'); }));
    ;['dragleave','drop'].forEach(ev => drop.addEventListener(ev, e => { e.preventDefault(); drop.classList.remove('drag'); }));
    drop.addEventListener('drop', e => addFiles(e.dataTransfer.files));

    byId('lnkPublicar').addEventListener('click', e => { e.preventDefault(); publish(); });
    byId('lnkLimpiar').addEventListener('click', e => { e.preventDefault(); queue = []; renderPending(); });

    // Quitar de lista pendiente
    list.addEventListener('click', e => {
      const a = e.target.closest('a[data-action="remove"]'); if(!a) return;
      e.preventDefault();
      const id = a.closest('.pending-item').dataset.id;
      queue = queue.filter(x => x.id !== id);
      renderPending();
    });

    // Eliminar publicado
    q('#tblPub tbody').addEventListener('click', async e => {
      const a = e.target.closest('a[data-action="del"]'); if(!a) return;
      e.preventDefault();
      const tr = a.closest('tr');
      const id = Number(tr.dataset.id);
      if(!id) return;
      if(!confirm('¿Está seguro que desea eliminar este material?')) return;
      try {
        const resp = await fetch('?c=Material&a=EliminarMaterial', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'id=' + encodeURIComponent(id)
        });
        const data = await resp.json();
        if(data.success){
          alert('Material eliminado correctamente');
          cargarMateriales();
        } else {
          alert('Error al eliminar: ' + (data.msj || 'No se pudo eliminar.'));
        }
      } catch(e){
        alert('Error de red o servidor.');
      }
    });

    // Demo: un registro inicial (sin archivo real)
    published.push({
      pid: seq++, titulo:'Sílabos y guía de ejercicios — Unidad 1',
      curso:'Matemática', tipo:'PDF', size: 534000, fecha: new Date(),
      url:'#', nombreArchivo:'silabos-unidad1.pdf', desc:''
    });
    renderPublished();

    // Al cargar la página, mostrar materiales publicados
    cargarMateriales();
  </script>
</body>
</html>
