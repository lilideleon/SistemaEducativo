<?php
header('Content-Type: text/html; charset=utf-8');
require_once 'core/AuthValidation.php';
validarAdmin();
include 'views/Menu/Aside.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sistema Educativo — Preguntas</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet" />

  <style>
    :root {
      --bg-teal:#4f8f8a; --sidebar-header:#a8c0bb; --line:#2e655f; --title:#235c9c;
      --ink:#173a38; --soft:#f5faf9;
    }
    html,body { height:100%; margin:0; }
    body { background:var(--bg-teal); display:flex; flex-direction:column; }
    .app-wrapper { display:flex; flex:1; height:100vh; }

    .sidebar { width:260px; height:100vh; position:fixed; left:0; top:0; overflow-y:auto;
      background:#50938a; padding:1rem; box-shadow:2px 0 5px rgba(0,0,0,.1); z-index:1000; }
    .sidebar-box { background:rgba(255,255,255,.1); border-radius:.5rem; padding:.75rem; }
    .sidebar-header { background:var(--sidebar-header); color:#1f2937; font-weight:600;
      padding:.5rem .75rem; border-radius:.35rem; margin-bottom:.75rem; }

    section.main-content { margin-left:260px; width:calc(100% - 260px); padding:2rem; min-height:100vh; display:flex; flex-direction:column; }
    .content-panel { flex:1; display:flex; flex-direction:column; min-height:0; background:#fff; border-radius:.5rem; padding:1.5rem; box-shadow:0 .125rem .25rem rgba(0,0,0,.075); }
    .page-title { background:#fff; color:var(--title); font-weight:700; border-radius:.25rem; display:inline-block; padding:.35rem 1.25rem; box-shadow:0 2px 0 rgba(0,0,0,.25) inset; }

    .wrap { border:2px solid var(--line); border-radius:.5rem; background:rgba(79,143,138,.15); padding:1rem; }
    .foot { height:10px; background:var(--line); border-bottom-left-radius:.5rem; border-bottom-right-radius:.5rem; }

    .link-btn { display:inline-block; text-decoration:none; background:#eef6ff; border:1px solid #9dc0ff; color:#143a6b;
      padding:.45rem .9rem; border-radius:.35rem; font-weight:600; }
    .link-btn:hover { filter:brightness(1.02); }
    .link-danger { background:#fff1f1; border-color:#ffc9c9; color:#8a1f1f; }
    .link-ghost { background:#f5faf9; border-color:#cfe7e3; color:#0f3b39; }

    .table-wrap { max-height:260px; overflow:auto; border:1px solid #e8eef0; border-radius:.5rem; }

    .btn-glow{border:0;border-radius:1rem;padding:.9rem 1.1rem;color:#fff;font-weight:700;background:
      radial-gradient(140% 120% at 50% 40%, rgba(255,255,255,.28) 0%, rgba(255,255,255,.08) 42%, rgba(0,0,0,.22) 75%),
      linear-gradient(180deg,#0f1c2e 0%,#1f3554 100%);box-shadow:0 8px 18px rgba(0,0,0,.25), inset 0 -2px 0 rgba(255,255,255,.1);
      transition:transform .12s ease, filter .12s ease; text-align:left}
    .btn-glow.active{outline:2px solid rgba(255,255,255,.35)}

    @media (max-width: 767.98px) {
      .sidebar { display:none; }
      section.main-content { margin-left:0 !important; width:100% !important; padding:1rem !important; min-height:calc(100vh - 56px); }
    }
    body.no-sidebar section.main-content{margin-left:0;width:100%}
  </style>
</head>
<body>
  <div class="container d-md-none my-2">
    <a href="#" class="btn btn-light" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar">
      <i class="bi bi-list"></i> Menú
    </a>
  </div>

  <!-- Modal: Editar Respuesta -->
  <div class="modal fade" id="editRespuestaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Editar respuesta</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <form id="frmEditResp">
            <div class="mb-3">
              <label class="form-label">Texto</label>
              <input type="text" id="edit_resp_texto" class="form-control form-control-sm" placeholder="Texto de la respuesta" />
            </div>
            <div class="mb-3">
              <label class="form-label">Número</label>
              <input type="number" step="0.0001" id="edit_resp_numero" class="form-control form-control-sm" placeholder="Ej.: 10.50" />
            </div>
            <div class="form-check form-switch mb-2">
              <input class="form-check-input" type="checkbox" id="edit_resp_correcta">
              <label class="form-check-label" for="edit_resp_correcta">Correcta</label>
            </div>
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="edit_resp_activa">
              <label class="form-check-label" for="edit_resp_activa">Activa</label>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-primary" id="btnSaveRespEdit">Guardar cambios</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="confirmDelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Eliminar pregunta</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
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
    <section class="main-content">
      <header class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="page-title">Gestión de Preguntas</h1>
        <a href="?c=Login&a=Logout" class="btn btn-outline-light btn-sm" title="Cerrar Sesión">
          <i class="bi bi-box-arrow-right"></i> Salir
        </a>
      </header>

      <div class="content-panel">
        <div class="wrap">
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
                <textarea id="enunciado" class="form-control" rows="2" placeholder="Escribe la pregunta..."></textarea>
              </div>
            </div>

            <div class="card mt-3">
              <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <h6 class="mb-0">Respuestas de la pregunta (borrador)</h6>
                  <small class="text-muted">Completa texto o úmero • Marca "Correcta" si aplica</small>
                </div>
                <div class="row g-2 align-items-end">
                  <div class="col-md-4">
                    <label class="form-label">Respuesta (Número)</label>
                    <input id="resp_texto" type="text" class="form-control form-control-sm" placeholder="Texto de la respuesta">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Respuesta (Número)</label>
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

          <hr class="my-4"/>
          <div class="d-flex align-items-center justify-content-between mb-2">
            <h5 class="mb-0">Preguntas registradas</h5>
            <small class="text-muted">Acciones: ver respuestas, editar, eliminar</small>
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

  <div class="modal fade" id="respuestasModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Respuestas</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
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
                  <th>Acciones</th>
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
  <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
  <script>
  (function() {
    "use strict";

    const q = sel => document.querySelector(sel);
    const byId = id => document.getElementById(id);

    let respuestasDraft = [];
    let respCache = [];
    const CONST_TIPO = "opcion_unica";
    const CONST_PONDERACION = 5;
    const CONST_ACTIVO = 1;

    let dtPreg = null;
    let delId = null;
    let editId = null;
    let modalRes = null;
    let modalEditResp = null;
    let modalDel = null;
    let currentPreguntaId = null;
    let currentRespEditId = null;

    const modalResEl = document.getElementById('respuestasModal');
    const modalDelEl = document.getElementById('confirmDelModal');

    function escapeHtml(str) {
      if (str === null || str === undefined) return '';
      return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
    }

    function clearRespFields() {
      byId('resp_texto').value = '';
      byId('resp_numero').value = '';
      byId('resp_correcta').checked = false;
      byId('resp_activa').checked = true;
    }

    function renderRespuestas() {
      const tbody = q('#tblResp tbody');
      if (!tbody) return;
      if (respuestasDraft.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Sin respuestas en borrador</td></tr>';
        return;
      }
      const rows = respuestasDraft.map((r, idx) => `
        <tr data-id="${escapeHtml(r.id)}">
          <td>${idx + 1}</td>
          <td>${escapeHtml(r.respuesta_texto ?? '')}</td>
          <td>${r.respuesta_numero ?? ''}</td>
          <td>${r.es_correcta ? 'Sí' : 'No'}</td>
          <td>${r.activo ? 'Sí' : 'No'}</td>
          <td class="text-nowrap">
            <a href="#" class="link-btn link-danger" data-action="del-resp">Quitar</a>
          </td>
        </tr>`).join('');
      tbody.innerHTML = rows;
    }

    function clearForm(keepEncuesta = false) {
      if (!keepEncuesta) {
        const sel = byId('encuesta_id');
        if (sel) sel.selectedIndex = 0;
      }
      byId('enunciado').value = '';
      respuestasDraft = [];
      renderRespuestas();
      editId = null;
      const $btn = $('#btnAgregar');
      $btn.text('Agregar pregunta');
      $btn.prop('disabled', false);
    }

    function addRespuesta() {
      const texto = byId('resp_texto').value.trim();
      const numeroStr = byId('resp_numero').value.trim();
      const numero = numeroStr !== '' ? Number(numeroStr) : null;
      const esCorrecta = byId('resp_correcta').checked ? 1 : 0;
      const activo = byId('resp_activa').checked ? 1 : 0;

      if (!texto && numero === null) {
        alert('Agrega texto o Número para la respuesta');
        return;
      }

      respuestasDraft.push({
        id: crypto.randomUUID ? crypto.randomUUID() : String(Date.now()) + Math.random(),
        respuesta_texto: texto || null,
        respuesta_numero: numero,
        es_correcta: esCorrecta,
        activo
      });

      clearRespFields();
      renderRespuestas();
    }

    function loadEncuestas() {
      const $sel = $('#encuesta_id');
      $sel.html('<option value="">Cargando encuestas...</option>');
      $.getJSON('?c=Preguntas&a=ListarEncuestas')
        .done(json => {
          if (json && json.success && Array.isArray(json.data)) {
            const opts = json.data.map(e => `<option value="${e.id}">${escapeHtml(e.titulo)}</option>`);
            $sel.html(['<option value="">Seleccione una encuesta...</option>', ...opts].join(''));
          } else {
            $sel.html('<option value="">No se pudieron cargar</option>');
          }
        })
        .fail(() => $sel.html('<option value="">Error al cargar</option>'));
    }

    function initDataTablePreguntas() {
      dtPreg = $('#tblPreg').DataTable({
        ajax: { url: '?c=Preguntas&a=Listar', dataSrc: 'data' },
        columns: [
          { data: null, render: (d, t, r, meta) => meta.row + 1 },
          { data: 'encuesta_id' },
          { data: 'enunciado', render: d => escapeHtml(d ?? '') },
          { data: 'total_respuestas' },
          {
            data: null,
            orderable: false,
            className: 'text-nowrap',
            render: d => `
              <button class="btn btn-sm btn-outline-primary me-1" data-action="ver-resp" data-id="${d.id}">
                <i class="bi bi-list-ul"></i> Respuestas
              </button>
              <button class="btn btn-sm btn-outline-secondary me-1" data-action="edit" data-id="${d.id}">
                <i class="bi bi-pencil-square"></i> Editar
              </button>
              <a class="link-danger px-1 py-0" href="#" data-action="del" data-id="${d.id}"><i class="bi bi-trash"></i></a>`
          }
        ],
        paging: true,
        searching: true,
        lengthChange: false,
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json' }
      });
    }

    function guardarRespuestasParaPregunta(preguntaId, answers) {
      const requests = answers.map(ans => $.ajax({
        url: '?c=Preguntas&a=AgregarRespuesta',
        method: 'POST',
        dataType: 'json',
        data: {
          pregunta_id: preguntaId,
          respuesta_texto: ans.respuesta_texto ?? '',
          respuesta_numero: ans.respuesta_numero ?? '',
          es_correcta: ans.es_correcta ? 1 : 0,
          activo: ans.activo ? 1 : 0
        }
      }).then(resp => (resp && resp.success) ? 1 : 0).catch(() => 0));

      return Promise.all(requests).then(results => results.reduce((a, b) => a + b, 0));
    }

    function addPregunta() {
      const encuesta_id = Number(byId('encuesta_id').value);
      const enunciado = byId('enunciado').value.trim();
      if (!encuesta_id) { alert('Selecciona una encuesta'); return; }
      if (!enunciado) { alert('El enunciado es obligatorio'); return; }

      const payload = {
        encuesta_id,
        enunciado,
        tipo: CONST_TIPO,
        ponderacion: CONST_PONDERACION,
        orden: respuestasDraft.length,
        activo: CONST_ACTIVO
      };

      const $btn = $('#btnAgregar');
      $btn.prop('disabled', true).text('Agregando...');

      const snapshot = [...respuestasDraft];

      $.ajax({
        url: '?c=Preguntas&a=Agregar',
        method: 'POST',
        dataType: 'json',
        data: payload
      }).done(resp => {
        if (resp && resp.success) {
          const afterSave = () => {
            alert('Pregunta guardada correctamente');
            if (dtPreg) dtPreg.ajax.reload(null, false);
            clearForm(true);
          };

          if (snapshot.length > 0) {
            guardarRespuestasParaPregunta(resp.id, snapshot)
              .then(afterSave)
              .catch(err => {
                console.error('Error guardando respuestas', err);
                alert('La pregunta se guardó, pero hubo errores guardando algunas respuestas.');
                afterSave();
              });
          } else {
            afterSave();
          }
        } else {
          alert((resp && resp.msj) || 'No se pudo guardar la pregunta');
        }
      }).fail(xhr => {
        const err = (xhr.responseJSON && xhr.responseJSON.msj) || xhr.responseText || 'Error al guardar';
        alert(err);
      }).always(() => $btn.prop('disabled', false).text('Agregar pregunta'));
    }

    function updatePregunta() {
      if (!editId) { alert('No hay pregunta seleccionada para editar'); return; }
      const encuesta_id = Number(byId('encuesta_id').value);
      const enunciado = byId('enunciado').value.trim();
      if (!encuesta_id) { alert('Selecciona una encuesta'); return; }
      if (!enunciado) { alert('El enunciado es obligatorio'); return; }

      const payload = {
        id: editId,
        encuesta_id,
        enunciado,
        tipo: CONST_TIPO,
        ponderacion: CONST_PONDERACION,
        orden: '',
        activo: CONST_ACTIVO
      };

      const $btn = $('#btnAgregar');
      $btn.prop('disabled', true).text('Actualizando...');

      $.ajax({
        url: '?c=Preguntas&a=Modificar',
        method: 'POST',
        dataType: 'json',
        data: payload
      }).done(resp => {
        if (resp && resp.success) {
          alert('Pregunta actualizada');
          if (dtPreg) dtPreg.ajax.reload(null, false);
          clearForm(true);
        } else {
          alert((resp && resp.msj) || 'No se pudo actualizar la pregunta');
        }
      }).fail(xhr => {
        const err = (xhr.responseJSON && xhr.responseJSON.msj) || xhr.responseText || 'Error al actualizar';
        alert(err);
      }).always(() => $btn.prop('disabled', false).text('Agregar pregunta'));
    }

    function loadRespuestas(preguntaId) {
      currentPreguntaId = preguntaId;
      if (modalResEl) modalResEl.dataset.pid = preguntaId;
      $('#respModalBody tbody').html('<tr><td colspan="6">Cargando...</td></tr>');
      $.getJSON(`?c=Preguntas&a=ListarRespuestas&pregunta_id=${preguntaId}`)
        .done(json => {
          if (json && json.success && Array.isArray(json.data)) {
            respCache = json.data;
            const rows = json.data.map((r, idx) => `
              <tr data-id="${r.id}">
                <td>${idx + 1}</td>
                <td>${escapeHtml(r.respuesta_texto ?? '')}</td>
                <td>${r.respuesta_numero ?? ''}</td>
                <td>${(Number(r.es_correcta) === 1) ? 'Sí' : 'No'}</td>
                <td>${(Number(r.activo) === 1) ? 'Sí' : 'No'}</td>
                <td class="text-nowrap">
                  <button class="btn btn-sm btn-outline-secondary" data-action="edit-resp" data-id="${r.id}">Editar</button>
                </td>
              </tr>`).join('');
            $('#respModalBody tbody').html(rows || '<tr><td colspan="6" class="text-center text-muted">Sin respuestas</td></tr>');
          } else {
            $('#respModalBody tbody').html('<tr><td colspan="6">No se pudieron cargar respuestas</td></tr>');
          }
        })
        .fail(() => $('#respModalBody tbody').html('<tr><td colspan="6">Error al cargar</td></tr>'));
    }

    function openRespuestasModal(preguntaId) {
      if (!modalRes && modalResEl) modalRes = new bootstrap.Modal(modalResEl);
      loadRespuestas(preguntaId);
      if (modalRes) modalRes.show();
    }

    function handleEditRespuesta(id) {
      const info = respCache.find(r => Number(r.id) === Number(id));
      if (!info) { alert('No se encontró la respuesta'); return; }
      currentRespEditId = id;
      // Poblar campos del modal
      const $txt = $('#edit_resp_texto');
      const $num = $('#edit_resp_numero');
      const $chkC = $('#edit_resp_correcta');
      const $chkA = $('#edit_resp_activa');
      $txt.val(info.respuesta_texto ?? '');
      $num.val(info.respuesta_numero ?? '');
      $chkC.prop('checked', Number(info.es_correcta) === 1);
      $chkA.prop('checked', Number(info.activo) === 1);
      if (!modalEditResp) {
        const el = document.getElementById('editRespuestaModal');
        if (el) modalEditResp = new bootstrap.Modal(el);
      }
      // Cerrar el modal de respuestas al abrir el de edición
      if (modalRes) modalRes.hide();
      if (modalEditResp) modalEditResp.show();
    }

    $('#btnAddResp').on('click', e => { e.preventDefault(); addRespuesta(); });
    $('#btnAgregar').on('click', e => {
      e.preventDefault();
      if (editId) updatePregunta(); else addPregunta();
    });
    $('#btnLimpiar').on('click', e => { e.preventDefault(); clearForm(); });

    $('#tblResp tbody').on('click', 'a[data-action="del-resp"]', function(e){
      e.preventDefault();
      const tr = this.closest('tr');
      const id = tr ? tr.getAttribute('data-id') : null;
      respuestasDraft = respuestasDraft.filter(r => String(r.id) !== String(id));
      renderRespuestas();
    });

    $('#tblPreg tbody').on('click', function(e){
      const target = e.target;
      const btnRespuestas = target.closest('button[data-action="ver-resp"]');
      if (btnRespuestas) {
        e.preventDefault();
        openRespuestasModal(btnRespuestas.getAttribute('data-id'));
        return;
      }
      const btnEditar = target.closest('button[data-action="edit"]');
      if (btnEditar) {
        e.preventDefault();
        const data = dtPreg.row(btnEditar.closest('tr')).data();
        if (!data) return;
        editId = data.id;
        byId('enunciado').value = data.enunciado || '';
        const sel = byId('encuesta_id');
        if (sel) sel.value = String(data.encuesta_id || '');
        $('#btnAgregar').text('Actualizar pregunta');
        document.getElementById('enunciado').scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
      }
      const linkEliminar = target.closest('a[data-action="del"]');
      if (linkEliminar) {
        e.preventDefault();
        delId = Number(linkEliminar.getAttribute('data-id'));
        if (!modalDel && modalDelEl) modalDel = new bootstrap.Modal(modalDelEl);
        if (modalDel) modalDel.show();
      }
    });

    $('#btnConfirmDel').on('click', function(){
      if (!delId) return;
      $.ajax({
        url: '?c=Preguntas&a=Eliminar',
        method: 'POST',
        dataType: 'json',
        data: { id: delId }
      }).done(resp => {
        if (resp && resp.success) {
          if (dtPreg) dtPreg.ajax.reload(null, false);
          delId = null;
          if (modalDel) modalDel.hide();
        } else {
          alert((resp && resp.msj) || 'No se pudo eliminar');
        }
      }).fail(xhr => {
        const err = (xhr.responseJSON && xhr.responseJSON.msj) || xhr.responseText || 'Error al eliminar';
        alert(err);
      });
    });

    $('#respModalBody tbody').on('click', 'button[data-action="edit-resp"]', function(){
      handleEditRespuesta(this.getAttribute('data-id'));
    });

    $(document).ready(() => {
      if (modalResEl) modalRes = new bootstrap.Modal(modalResEl);
      const modalEditRespEl = document.getElementById('editRespuestaModal');
      if (modalEditRespEl) modalEditResp = new bootstrap.Modal(modalEditRespEl);
      if (modalDelEl) modalDel = new bootstrap.Modal(modalDelEl);
      loadEncuestas();
      initDataTablePreguntas();
      renderRespuestas();
    });

    // Guardar cambios desde el modal de edición de respuesta
    $('#btnSaveRespEdit').on('click', function(){
      if (!currentRespEditId) return;
      const nuevoTexto = $('#edit_resp_texto').val().trim();
      const nuevoNumeroStr = $('#edit_resp_numero').val().trim();
      const esCorrecta = $('#edit_resp_correcta').is(':checked') ? 1 : 0;
      const activo = $('#edit_resp_activa').is(':checked') ? 1 : 0;

      $.ajax({
        url: '?c=Preguntas&a=ModificarRespuesta',
        method: 'POST',
        dataType: 'json',
        data: {
          id: currentRespEditId,
          respuesta_texto: nuevoTexto === '' ? '' : nuevoTexto,
          respuesta_numero: nuevoNumeroStr === '' ? '' : nuevoNumeroStr,
          es_correcta: esCorrecta,
          activo
        }
      }).done(resp => {
        if (resp && resp.success) {
          if (modalEditResp) modalEditResp.hide();
          currentRespEditId = null;
          if (currentPreguntaId) loadRespuestas(currentPreguntaId);
        } else {
          alert((resp && resp.msj) || 'No se pudo modificar');
        }
      }).fail(xhr => {
        const err = (xhr.responseJSON && xhr.responseJSON.msj) || xhr.responseText || 'Error al modificar';
        alert(err);
      });
    });

  })();
  </script>
</body>
</html>


