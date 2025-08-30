<?php 
   // Validación de autenticación y permisos de administrador
   require_once 'core/AuthValidation.php';
   validarAdmin(); // Solo administradores pueden gestionar preguntas
   
   include 'views/Menu/Aside.php';
?>


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

  <!-- Modal: Confirmar eliminación lógica de pregunta -->
  <div class="modal fade" id="confirmDelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Eliminar pregunta</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Esta acción desactivará la pregunta (activo = 0). ¿Deseas continuar?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-danger" id="btnConfirmDel">Eliminar</button>
        </div>
      </div>
    </div>
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
          <!-- Formulario de preguntas -->
          <form id="frmPreg" class="mb-3">
            <div class="row g-3">
              <div class="col-md-5">
                <label class="form-label fw-semibold">Encuesta</label>
                <select id="encuesta_id" class="form-select form-select-sm">
                  <option value="">Cargando encuestas...</option>
                </select>
              </div>

              <div class="col-12">
                <label class="form-label fw-semibold">Enunciado</label>
                <textarea id="enunciado" class="form-control" rows="2" placeholder="Escribe la pregunta..." ></textarea>
              </div>
            </div>

            <!-- Respuestas de la pregunta (borrador) -->
            <div class="card mt-3">
              <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <h6 class="mb-0">Respuestas de la pregunta (borrador)</h6>
                  <small class="text-muted">Completa texto o número • Marca "Correcta" si aplica</small>
                </div>
                <div class="row g-2 align-items-end">
                  <div class="col-md-4">
                    <label class="form-label">Respuesta (texto)</label>
                    <input id="resp_texto" type="text" class="form-control form-control-sm" placeholder="Texto de la respuesta">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Respuesta (número)</label>
                    <input id="resp_numero" type="number" step="0.0001" class="form-control form-control-sm" placeholder="Ej.: 10.50">
                  </div>
                  <div class="col-md-2 form-check mt-4">
                    <input class="form-check-input" type="checkbox" id="resp_correcta">
                    <label class="form-check-label" for="resp_correcta">Correcta</label>
                  </div>
                  <div class="col-md-2 form-check mt-4">
                    <input class="form-check-input" type="checkbox" id="resp_activa" checked>
                    <label class="form-check-label" for="resp_activa">Activa</label>
                  </div>
                  <div class="col-md-1 text-md-end">
                    <a href="#" id="btnAddResp" class="link-btn">Agregar</a>
                  </div>
                </div>

                <div class="table-wrap mt-3">
                  <table class="table table-sm table-bordered mb-0" id="tblResp">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Texto</th>
                        <th>Número</th>
                        <th>Correcta</th>
                        <th>Activa</th>
                        <th>Acciones</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div>
              </div>
            </div>

            <div class="mt-3 d-flex gap-2 justify-content-end">
              <a href="#" id="btnAgregar" class="link-btn">Agregar pregunta</a>
              <a href="#" id="btnLimpiar" class="link-btn link-ghost">Limpiar</a>
            </div>
          </form>

          <!-- Publicados -->
          <hr class="my-4"/>
          <div class="d-flex align-items-center justify-content-between mb-2">
            <h5 class="mb-0">Preguntas en borrador</h5>
            <small class="text-muted">Acciones: <i class="bi bi-trash"></i> eliminar</small>
          </div>

          <div class="table-wrap">
            <table class="table table-sm table-bordered mb-0" id="tblPreg">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Encuesta</th>
                  <th>Enunciado</th>
                  <th>Total respuestas</th>
                  <th>Acciones</th>
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

  <!-- Modal: Respuestas de la pregunta -->
  <div class="modal fade" id="respuestasModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Respuestas</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="respModalBody">
          <div class="table-wrap">
            <table class="table table-sm table-bordered mb-0">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Texto</th>
                  <th>Número</th>
                  <th>Correcta</th>
                  <th>Activa</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
  <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
  <script>
    // ======= Estado =======
    const q = sel => document.querySelector(sel);
    const byId = id => document.getElementById(id);

    let respuestasDraft = [];

    // ======= Constantes para campos no presentes en el diseño =======
    const CONST_TIPO = 'opcion_unica';   // tipo de pregunta
    const CONST_PONDERACION = 5;          // punteo / ponderación
    const CONST_ACTIVO = 1;               // 1 = activo, 0 = inactivo

    function clearForm(keepEncuesta=false){
      if(!keepEncuesta) byId('encuesta_id').value = '';
      byId('enunciado').value = '';
      clearRespFields();
      respuestasDraft = [];
      renderRespuestas();
    }

    function clearRespFields(){
      byId('resp_texto').value = '';
      byId('resp_numero').value = '';
      byId('resp_correcta').checked = false;
      byId('resp_activa').checked = true;
    }

    // ======= Respuestas (borrador de la pregunta actual) =======
    function addRespuesta(){
      const respuesta_texto = byId('resp_texto').value.trim();
      const numeroVal = byId('resp_numero').value;
      const respuesta_numero = numeroVal !== '' ? Number(numeroVal) : null;
      const es_correcta = byId('resp_correcta').checked ? 1 : 0;
      const activo = byId('resp_activa').checked ? 1 : 0;

      if(respuesta_texto === '' && respuesta_numero === null){
        alert('Agrega texto o número para la respuesta');
        return;
      }

      respuestasDraft.push({
        id: crypto.randomUUID?.() || (Date.now()+Math.random()), // temporal local
        intento_id: null,
        pregunta_id: null,
        opcion_id: null,
        respuesta_texto: respuesta_texto || null,
        respuesta_numero,
        es_correcta,
        activo
      });
      clearRespFields();
      renderRespuestas();
    }

    function renderRespuestas(){
      const tbody = q('#tblResp tbody');
      tbody.innerHTML = respuestasDraft.map((r, idx) => `
        <tr data-id="${r.id}">
          <td>${idx+1}</td>
          <td>${r.respuesta_texto ?? ''}</td>
          <td>${r.respuesta_numero ?? ''}</td>
          <td>${r.es_correcta ? 'Sí' : 'No'}</td>
          <td>${r.activo ? 'Sí' : 'No'}</td>
          <td class="text-nowrap">
            <a class="link-danger px-1 py-0" href="#" data-action="del-resp"><i class="bi bi-trash"></i></a>
          </td>
        </tr>
      `).join('');
    }

    // ======= Cargar encuestas (select) =======
    function loadEncuestas(){
      const $sel = $('#encuesta_id');
      $sel.html('<option value="">Cargando encuestas...</option>');
      $.getJSON('?c=Preguntas&a=ListarEncuestas')
        .done(json => {
          if(json && json.success){
            const opts = ['<option value="">Seleccione una encuesta...</option>']
              .concat(json.data.map(e => `<option value="${e.id}">${e.titulo} (ID: ${e.id})</option>`));
            $sel.html(opts.join(''));
          } else {
            $sel.html('<option value="">No se pudieron cargar</option>');
          }
        })
        .fail(()=>{
          $sel.html('<option value="">Error al cargar</option>');
        });
    }

    // ======= Preguntas (DataTable) =======
    let dtPreg = null;
    let delId = null;
    function initDataTablePreguntas(){
      dtPreg = $('#tblPreg').DataTable({
        ajax: { url: '?c=Preguntas&a=Listar', dataSrc: 'data' },
        columns: [
          { data: null, render: (d,t,r,meta) => meta.row + 1 },
          { data: 'encuesta_id' },
          { data: 'enunciado' },
          { data: 'total_respuestas' },
          { data: null, orderable:false, className:'text-nowrap', render: (d) => `
              <button class="btn btn-sm btn-outline-primary me-1" data-action="ver-resp" data-id="${d.id}">
                <i class="bi bi-list-ul"></i> Respuestas
              </button>
              <a class="link-danger px-1 py-0" href="#" data-action="del" data-id="${d.id}"><i class="bi bi-trash"></i></a>
            ` }
        ],
        paging: true,
        searching: true,
        lengthChange: false,
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json' }
      });
    }
    function addPregunta(){
      const encuesta_id = Number(byId('encuesta_id').value);
      const enunciado = byId('enunciado').value.trim();
      if(!encuesta_id){ alert('Ingresa un Encuesta ID válido'); return; }
      if(!enunciado){ alert('El enunciado es obligatorio'); return; }

      // Construir payload con constantes
      const payload = {
        encuesta_id,
        enunciado,
        tipo: CONST_TIPO,
        ponderacion: CONST_PONDERACION,
        orden: ($('#tblPreg tbody tr').length + 1),
        activo: CONST_ACTIVO
      };

      const $btn = $('#btnAgregar');
      $btn.prop('disabled', true).text('Agregando...');

      // Snapshot de respuestas del borrador antes de limpiar
      const answersSnapshot = [...respuestasDraft];

      $.ajax({
        url: '?c=Preguntas&a=Agregar',
        method: 'POST',
        data: payload,
        dataType: 'json'
      }).done(resp => {
        if(resp && resp.success){
          // Si hay respuestas en el borrador, guardarlas asociadas a la pregunta creada
          if(answersSnapshot.length > 0){
            guardarRespuestasParaPregunta(resp.id, answersSnapshot).then(guardadas => {
              // Recargar DataTable para reflejar conteo
              if(dtPreg){ dtPreg.ajax.reload(null,false); }
            }).catch(err => {
              console.error('Error guardando respuestas:', err);
              alert('La pregunta se guardó, pero hubo errores guardando algunas respuestas.');
            }).finally(() => {
              // Limpiar UI del borrador
              respuestasDraft = [];
              renderRespuestas();
              byId('enunciado').value = '';
              if(dtPreg){ dtPreg.ajax.reload(null,false); }
            });
          } else {
            // Limpiar UI si no hay respuestas
            respuestasDraft = [];
            renderRespuestas();
            byId('enunciado').value = '';
            if(dtPreg){ dtPreg.ajax.reload(null,false); }
          }
          alert('Pregunta guardada correctamente');
        } else {
          alert((resp && resp.msj) || 'No se pudo guardar la pregunta');
        }
      }).fail(xhr => {
        const err = (xhr.responseJSON && xhr.responseJSON.msj) || xhr.responseText || 'Error al guardar';
        alert(err);
      }).always(() => {
        $btn.prop('disabled', false).text('Agregar pregunta');
      });
    }

    // Guarda un arreglo de respuestas para una pregunta dada. Retorna una Promesa que resuelve con el número de respuestas guardadas.
    function guardarRespuestasParaPregunta(preguntaId, answers){
      const requests = answers.map(ans => {
        const payload = {
          pregunta_id: preguntaId,
          respuesta_texto: ans.respuesta_texto ?? '',
          respuesta_numero: (ans.respuesta_numero ?? '') === '' ? '' : ans.respuesta_numero,
          es_correcta: ans.es_correcta ? 1 : 0,
          activo: ans.activo ? 1 : 0
        };
        return $.ajax({
          url: '?c=Preguntas&a=AgregarRespuesta',
          method: 'POST',
          data: payload,
          dataType: 'json'
        }).then(resp => (resp && resp.success) ? 1 : 0).catch(() => 0);
      });

      return Promise.all(requests).then(results => results.reduce((a,b) => a+b, 0));
    }

    // Se elimina almacenamiento local y guardado masivo. Cada clic en "Agregar pregunta" guarda directo en BD.

    // ======= Eventos =======
    byId('btnAddResp').addEventListener('click', e => { e.preventDefault(); addRespuesta(); });
    byId('btnAgregar').addEventListener('click', e => { e.preventDefault(); addPregunta(); });
    byId('btnLimpiar').addEventListener('click', e => { e.preventDefault(); clearForm(); });
    // Inicializar Select y DataTable al cargar
    document.addEventListener('DOMContentLoaded', () => { loadEncuestas(); initDataTablePreguntas(); });
  
  // quitar botón Guardar todo (ya no existe) y su handler

    // Eliminar respuesta del borrador actual
    q('#tblResp tbody').addEventListener('click', e => {
      const a = e.target.closest('a[data-action="del-resp"]'); if(!a) return;
      e.preventDefault();
      const id = a.closest('tr').dataset.id;
      respuestasDraft = respuestasDraft.filter(r => String(r.id) !== String(id));
      renderRespuestas();
    });

    // Eliminar / Ver respuestas desde la tabla
    q('#tblPreg tbody').addEventListener('click', e => {
      const btnVer = e.target.closest('button[data-action="ver-resp"]');
      if(btnVer){
        e.preventDefault();
        const id = Number(btnVer.getAttribute('data-id'));
        openRespuestasModal(id);
        return;
      }
      const aDel = e.target.closest('a[data-action="del"]');
      if(aDel){
        e.preventDefault();
        const id = Number(aDel.getAttribute('data-id'));
        if(!id){ alert('No se encontró el ID de la pregunta para eliminar.'); return; }
        delId = id;
        const modal = new bootstrap.Modal(document.getElementById('confirmDelModal'));
        modal.show();
      }
    });

    // Confirmar eliminación lógica
    byId('btnConfirmDel').addEventListener('click', () => {
      if(!delId){ return; }
      $.ajax({
        url: '?c=Preguntas&a=Eliminar',
        method: 'POST',
        data: { id: delId },
        dataType: 'json'
      }).done(resp => {
        if(resp && resp.success){
          if(dtPreg){ dtPreg.ajax.reload(null,false); }
          delId = null;
          bootstrap.Modal.getInstance(document.getElementById('confirmDelModal')).hide();
        } else {
          alert((resp && resp.msj) || 'No se pudo eliminar en BD');
        }
      }).fail(xhr => {
        const err = (xhr.responseJSON && xhr.responseJSON.msj) || xhr.responseText || 'Error al eliminar';
        alert(err);
      });
    });

    // Modal de respuestas
    function openRespuestasModal(preguntaId){
      $('#respuestasModal').data('pid', preguntaId);
      $('#respModalBody tbody').html('<tr><td colspan="5">Cargando...</td></tr>');
      const url = `?c=Preguntas&a=ListarRespuestas&pregunta_id=${preguntaId}`;
      $.getJSON(url).done(json => {
        if(json && json.success){
          const rows = json.data.map((r,idx)=> `
            <tr>
              <td>${idx+1}</td>
              <td>${r.respuesta_texto ?? ''}</td>
              <td>${r.respuesta_numero ?? ''}</td>
              <td>${r.es_correcta ? 'Sí':'No'}</td>
              <td>${r.activo ? 'Sí':'No'}</td>
            </tr>`).join('');
          $('#respModalBody tbody').html(rows || '<tr><td colspan="5">Sin respuestas</td></tr>');
        } else {
          $('#respModalBody tbody').html('<tr><td colspan="5">No se pudieron cargar respuestas</td></tr>');
        }
      }).fail(()=>{
        $('#respModalBody tbody').html('<tr><td colspan="5">Error al cargar</td></tr>');
      });
      const modal = new bootstrap.Modal(document.getElementById('respuestasModal'));
      modal.show();
    }
  </script>
</body>
</html>
<?php 
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
