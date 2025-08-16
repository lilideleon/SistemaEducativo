<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sistema Educativo — Material Didáctico</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>

  <style>
    :root{
      --bg-teal:#4f8f8a; --sidebar-header:#a8c0bb; --line:#2e655f; --title:#235c9c;
      --ink:#173a38; --soft:#f5faf9;
    }
    html,body{height:100%;margin:0}
    body{background:var(--bg-teal);display:flex;flex-direction:column}
    .app-wrapper{display:flex;flex:1;height:100vh}

    /* Sidebar (igual a pantallas anteriores) */
    .sidebar{width:260px;height:100vh;position:fixed;left:0;top:0;overflow-y:auto;
      background:#50938a;padding:1rem;box-shadow:2px 0 5px rgba(0,0,0,.1);z-index:1000}
    .sidebar-box{background:rgba(255,255,255,.1);border-radius:.5rem;padding:.75rem}
    .sidebar-header{background:var(--sidebar-header);color:#1f2937;font-weight:600;
      padding:.5rem .75rem;border-radius:.35rem;margin-bottom:.75rem}
    .link-glow{display:block;text-decoration:none;color:#fff;font-weight:700;border-radius:1rem;
      padding:.9rem 1.1rem;text-align:left;
      background:radial-gradient(140% 120% at 50% 40%, rgba(255,255,255,.28) 0%, rgba(255,255,255,.08) 42%, rgba(0,0,0,.22) 75%),
                 linear-gradient(180deg,#0f1c2e 0%,#1f3554 100%);
      box-shadow:0 8px 18px rgba(0,0,0,.25), inset 0 -2px 0 rgba(255,255,255,.1);
      transition:transform .12s ease, filter .12s ease}
    .link-glow:hover{transform:translateY(-2px);filter:brightness(1.03)}
    .link-glow.active{outline:2px solid rgba(255,255,255,.35)}

    /* Contenido */
    section.main-content{margin-left:260px;width:calc(100% - 260px);padding:2rem;min-height:100vh;display:flex;flex-direction:column}
    .content-panel{flex:1;display:flex;flex-direction:column;min-height:0;background:#fff;border-radius:.5rem;padding:1.25rem;box-shadow:0 .125rem .25rem rgba(0,0,0,.075)}
    .page-title{background:#fff;color:var(--title);font-weight:700;border-radius:.25rem;display:inline-block;padding:.35rem 1.25rem;box-shadow:0 2px 0 rgba(0,0,0,.25) inset}

    .wrap{border:2px solid var(--line);border-radius:.5rem;background:rgba(79,143,138,.15);padding:1rem}
    .foot{height:10px;background:var(--line);border-bottom-left-radius:.5rem;border-bottom-right-radius:.5rem}

    /* Botones-enlace */
    .link-btn{display:inline-block;text-decoration:none;background:#eef6ff;border:1px solid #9dc0ff;color:#143a6b;
      padding:.45rem .9rem;border-radius:.35rem;font-weight:600}
    .link-btn:hover{filter:brightness(1.02)}
    .link-danger{background:#fff1f1;border-color:#ffc9c9;color:#8a1f1f}
    .link-ghost{background:#f5faf9;border-color:#cfe7e3;color:#0f3b39}

    /* Dropzone */
    .dropzone{border:2px dashed #96c8c2;border-radius:.75rem;background:#ffffff; padding:1.25rem; text-align:center; cursor:pointer}
    .dropzone.drag{background:#f0fbfa; border-color:#2aa59a}
    .dz-hint{color:#4a6f6d;font-size:.95rem}

    /* Lista por subir */
    .pending-item{display:grid;grid-template-columns:56px 1fr auto;gap:.75rem;align-items:center;border:1px solid #e9f2f1;border-radius:.5rem;background:#fff;padding:.5rem .75rem}
    .thumb{width:56px;height:56px;border-radius:.35rem;object-fit:cover;background:#f3f6f6;display:flex;align-items:center;justify-content:center;font-size:1.35rem;color:#6a8d8b}
    .file-title{font-weight:600}
    .file-meta{font-size:.85rem;color:#6c8784}

    /* Tabla publicados */
    .table thead th{white-space:nowrap;background:#e9f2f1}
    .table-wrap{max-height:260px;overflow:auto;border:1px solid #e8eef0;border-radius:.5rem}

    @media (max-width: 767.98px){
      .sidebar{display:none}
      section.main-content{margin-left:0 !important;width:100% !important;padding:1rem !important}
    }
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
    <!-- Sidebar -->
    <aside class="sidebar d-none d-md-block">
      <div class="sidebar-box">
        <div class="sidebar-header">Principal</div>
        <div class="d-grid gap-3">
          <a class="link-glow fs-5" href="?c=Menu"><i class="bi bi-grid-fill me-2"></i>Menú</a>
          <a class="link-glow fs-5" href="?c=Usuarios"><i class="bi bi-people-fill me-2"></i>Usuarios</a>
          <a class="link-glow fs-5" href="?c=Evaluacion"><i class="bi bi-clipboard-check-fill me-2"></i>Evaluación</a>
          <a class="link-glow fs-5 active" href="?c=Material"><i class="bi bi-folder2-open me-2"></i>Material</a>
          <a class="link-glow fs-5" href="?c=Reportes"><i class="bi bi-bar-chart-fill me-2"></i>Reportes</a>
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
          <a class="link-glow fs-5" data-bs-dismiss="offcanvas" href="?c=Menu">Menú</a>
          <a class="link-glow fs-5" data-bs-dismiss="offcanvas" href="?c=Usuarios">Usuarios</a>
          <a class="link-glow fs-5" data-bs-dismiss="offcanvas" href="?c=Evaluacion">Evaluación</a>
          <a class="link-glow fs-5" data-bs-dismiss="offcanvas" href="?c=Material">Material</a>
          <a class="link-glow fs-5" data-bs-dismiss="offcanvas" href="?c=Reportes">Reportes</a>
        </div>
      </div>
    </div>

    <!-- Contenido -->
    <section class="main-content">
      <header class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="page-title">Sistema Educativo</h1>
        <i class="bi bi-arrow-right-square-fill fs-2 text-light d-none d-md-inline"></i>
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
              <div class="col-md-5">
                <label class="form-label fw-semibold">Título del material</label>
                <input id="titulo" class="form-control form-control-sm" placeholder="Ej.: Unidad 2 — Álgebra / Guía y diapositivas">
              </div>
              <div class="col-md-4 text-md-end">
                <a href="#" id="lnkPublicar" class="link-btn">Publicar <span id="qtySel">0</span> archivo(s)</a>
                <a href="#" id="lnkLimpiar" class="link-btn link-ghost ms-2">Limpiar selección</a>
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
              <div class="dz-hint">o <a href="#" id="lnkElegir">haz clic para seleccionarlos</a></div>
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
                  <th>ID</th><th>Título</th><th>Curso</th><th>Tipo</th><th>Tamaño</th><th>Fecha</th><th>Acciones</th>
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
    function publish(){
      if(queue.length === 0) return;
      const curso = byId('curso').value;
      const titulo = byId('titulo').value.trim() || 'Material sin título';
      const desc = byId('desc').value.trim();

      for(const it of queue){
        const item = {
          pid: seq++,
          titulo,
          curso,
          tipo: typeLabel(it.ext),
          size: it.size,
          fecha: new Date(),
          url: it.url,
          nombreArchivo: it.name,
          desc
        };
        published.unshift(item);
      }
      queue = [];
      renderPending();
      renderPublished();
    }

    function renderPublished(){
      const tbody = q('#tblPub tbody');
      tbody.innerHTML = published.map(p => `
        <tr data-id="${p.pid}">
          <td>${p.pid}</td>
          <td>${p.titulo}</td>
          <td>${p.curso}</td>
          <td>${p.tipo}</td>
          <td>${fmtBytes(p.size)}</td>
          <td>${p.fecha.toLocaleString()}</td>
          <td class="text-nowrap">
            <a class="me-2" href="${p.url}" target="_blank" title="Ver"><i class="bi bi-eye"></i></a>
            <a class="me-2" href="${p.url}" download="${p.nombreArchivo}" title="Descargar"><i class="bi bi-download"></i></a>
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
    drop.addEventListener('click', () => input.click());

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
    q('#tblPub tbody').addEventListener('click', e => {
      const a = e.target.closest('a[data-action="del"]'); if(!a) return;
      e.preventDefault();
      const tr = a.closest('tr'); const id = Number(tr.dataset.id);
      published = published.filter(x => x.pid !== id);
      renderPublished();
    });

    // Demo: un registro inicial (sin archivo real)
    published.push({
      pid: seq++, titulo:'Sílabos y guía de ejercicios — Unidad 1',
      curso:'Matemática', tipo:'PDF', size: 534000, fecha: new Date(),
      url:'#', nombreArchivo:'silabos-unidad1.pdf', desc:''
    });
    renderPublished();
  </script>
</body>
</html>
