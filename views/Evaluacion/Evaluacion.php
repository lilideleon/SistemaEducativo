<?php 
   // Validación de autenticación y permisos de alumno y admin
   require_once 'core/AuthValidation.php';
   validarRol(['ALUMNO','ADMIN']); // Alumnos y Admin pueden acceder a evaluaciones (Admin en modo visualización)
   
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

    :root{ --bg-teal:#4f8f8a; --sidebar-header:#a8c0bb; --line:#2e655f; --ink:#173a38; }
    html,body{height:100%;margin:0}
    body{background:var(--bg-teal);display:flex;flex-direction:column}
    .app-wrapper{display:flex;flex:1;height:100vh}

    /* Sidebar */
    .sidebar{
      width:260px;height:100vh;position:fixed;left:0;top:0;overflow-y:auto;
      background:#50938a;padding:1rem;box-shadow:2px 0 5px rgba(0,0,0,.1);z-index:1000;
    }
    .sidebar-box{background:rgba(255,255,255,.1);border-radius:.5rem;padding:.75rem}
    .sidebar-header{background:var(--sidebar-header);color:#1f2937;font-weight:600;
      padding:.5rem .75rem;border-radius:.35rem;margin-bottom:.75rem}
    .link-glow{
      display:block;text-decoration:none;color:#fff;font-weight:700;border-radius:1rem;
      padding:.9rem 1.1rem;text-align:left;
      background:
        radial-gradient(140% 120% at 50% 40%, rgba(255,255,255,.28) 0%, rgba(255,255,255,.08) 42%, rgba(0,0,0,.22) 75%),
        linear-gradient(180deg,#0f1c2e 0%,#1f3554 100%);
      box-shadow:0 8px 18px rgba(0,0,0,.25), inset 0 -2px 0 rgba(255,255,255,.1);
      transition:transform .12s ease, filter .12s ease;
    }
    .link-glow:hover{transform:translateY(-2px);filter:brightness(1.03)}
    .link-glow.active{outline:2px solid rgba(255,255,255,.35)}

    /* Contenido */
    section.main-content{
      margin-left:260px;width:calc(100% - 260px);
      padding:2rem;min-height:100vh;display:flex;flex-direction:column;
    }
    .content-panel{
      flex:1;display:flex;flex-direction:column;min-height:0;
      background:#fff;border-radius:.5rem;padding:1.25rem;
      box-shadow:0 .125rem .25rem rgba(0,0,0,.075);
    }
    .page-title{
      background:#fff;color:#235c9c;font-weight:700;border-radius:.25rem;
      display:inline-block;padding:.35rem 1.25rem;box-shadow:0 2px 0 rgba(0,0,0,.25) inset;
    }
    /* Timers más grandes */
    .timer-badge{ font-size:1.25rem; padding:.55rem .9rem; border-radius:.6rem; box-shadow:0 2px 6px rgba(0,0,0,.2) }
    #totalTime{ font-weight:700 }
    #qTime{ font-weight:600 }

    /* Contenedor del test */
    .gform-wrap{
      border:2px solid var(--line);border-radius:.5rem;background:rgba(79,143,138,.15);
      padding:1rem 1rem 0;
    }

    /* === Combobox fuera del card (alineado al ancho del card) === */
    .gform-toolbar{
      max-width:820px; margin:0 auto .5rem;
      display:flex; justify-content:flex-end; align-items:center; gap:.5rem;
    }
    .gform-toolbar .form-select{max-width:260px}

    /* Card del test */
    .gform-card{
      max-width:820px;margin:0 auto 1rem;background:#fff;border-radius:.5rem;
      box-shadow:0 2px 8px rgba(0,0,0,.12); padding:1.25rem 1.25rem 1rem;
    }
    .gform-title{font-weight:700;color:#1b3a4a;text-decoration:underline;text-underline-offset:3px;margin:0;text-align:center}
    .gform-subtle{color:#415b59;font-size:.95rem;text-align:center}

    .gq{display:grid;grid-template-columns:40px 1fr;gap:1rem;align-items:start;padding:1rem 0;border-top:1px solid #eaeff0}
    .gq:first-of-type{border-top:0}
    .gq-number{font-weight:600;color:#234c4a}
    .gq-statement b{color:#0e2d2b}
    .form-check-input{width:1.15em;height:1.15em}

    .link-next{
      display:inline-block;text-decoration:none;background:#f0f4fb;border:2px solid #4a86c7;
      color:#1f3554;font-weight:600;padding:.5rem 1.1rem;border-radius:.35rem;
    }
    .link-next:hover{filter:brightness(1.02)}
    .gform-footer{display:flex;justify-content:flex-end;margin-top:.5rem}

    @media (max-width: 767.98px){
      .sidebar{display:none}
      section.main-content{margin-left:0 !important;width:100% !important;padding:1rem !important}
      .gform-card{margin:.5rem auto; padding:1rem}
      
      /* Header responsivo */
      header{flex-direction:column !important; gap:.75rem; align-items:stretch !important}
      header .page-title{text-align:center; font-size:1.25rem}
      header > div{flex-wrap:wrap; justify-content:center !important}
      
      /* Timers más pequeños en móvil */
      .timer-badge{font-size:0.9rem; padding:.4rem .6rem}
      
      /* Toolbar responsivo */
      .gform-toolbar{flex-direction:column; align-items:stretch !important}
      .gform-toolbar .form-select{max-width:100%}
      
      /* Grid de preguntas en móvil */
      .gq{grid-template-columns:35px 1fr; gap:.75rem; padding:.75rem .25rem}
      .gq-number{width:35px; height:35px; font-size:0.9rem}
      .gq-statement{font-size:0.95rem}
      
      /* Opciones más espaciadas */
      .gq .form-check{padding:.5rem .35rem}
      .gq .form-check-label{font-size:0.9rem}
      
      /* Botón siguiente */
      .link-next{width:100%; text-align:center; padding:.65rem 1rem}
      .gform-footer{margin-top:1rem}
      
      /* Modal responsivo */
      #resultadoModal .modal-dialog{margin:.5rem}
      #resultadoModal .table{font-size:0.85rem}
      #resultadoModal .table th, #resultadoModal .table td{padding:.5rem .35rem}
      
      /* Mejor experiencia de scroll en modal */
      #resultadoModal .modal-body{
        max-height:calc(100vh - 200px);
        overflow-y:auto;
      }
      
      /* Tabla de resultados más compacta */
      #resultadoModal .table th:first-child,
      #resultadoModal .table td:first-child{
        width:30px;
      }
    }

    /* Tablets (768px - 991px) */
    @media (min-width: 768px) and (max-width: 991.98px){
      section.main-content{padding:1.5rem}
      .gform-card{max-width:680px; padding:1.25rem}
      .timer-badge{font-size:1.1rem}
      .gq{gap:.85rem}
    }

    /* Pantallas muy pequeñas (menos de 375px) */
    @media (max-width: 374.98px){
      section.main-content{padding:.75rem !important}
      .gform-wrap{padding:.75rem .5rem 0}
      .gform-card{padding:.75rem}
      .gform-title{font-size:1.1rem}
      .timer-badge{font-size:0.8rem; padding:.35rem .5rem}
      .gq{grid-template-columns:30px 1fr; gap:.5rem}
      .gq-number{width:30px; height:30px; font-size:0.85rem}
    }

    /* Mejoras táctiles para móviles */
    @media (max-width: 767.98px){
      /* Áreas táctiles más grandes para botones radio/checkbox */
      .gq .form-check-input{
        width:1.3em;
        height:1.3em;
        margin-top:.2em;
      }
      
      /* Mejor espaciado para tocar */
      .gq .form-check{
        padding:.6rem .5rem;
        margin-bottom:.25rem;
      }
      
      /* Botón cerrar sesión más visible */
      header .btn-outline-light{
        padding:.5rem .75rem;
        font-size:0.9rem;
      }
      
      /* Select más grande y fácil de tocar */
      .gform-toolbar .form-select{
        padding:.6rem .75rem;
        font-size:1rem;
      }
      
      /* Textarea y inputs numéricos más grandes */
      .gq textarea.form-control{
        min-height:100px;
        font-size:1rem;
      }
      
      .gq input[type="number"].form-control{
        font-size:1rem;
        padding:.6rem;
      }
    }

    /* Optimización para landscape en móviles */
    @media (max-width: 767.98px) and (orientation: landscape){
      .timer-badge{font-size:0.85rem; padding:.35rem .55rem}
      .page-title{font-size:1.1rem}
      .gq{padding:.6rem .25rem}
    }



    /* ================= UI enhancements for encuestas (scoped) ================= */
    /* Toolbar select */
    .gform-toolbar label{ color:#0d3b35 }
    #encuesta_id.form-select{
      border-radius:10px; border-color:rgba(0,0,0,.15);
      transition: box-shadow .15s ease, border-color .15s ease;
    }
    #encuesta_id.form-select:focus{
      border-color:#15a085; box-shadow:0 0 0 .2rem rgba(21,160,133,.15)
    }

    /* Card */
    .gform-card{
      border:1px solid rgba(17,120,103,.10);
      background: linear-gradient(180deg,#ffffff 0%, #fbfefe 100%);
    }
    .gform-title{
      color:#0f2f2c; text-decoration:none; letter-spacing:.2px;
    }
    .gform-subtle{ color:#2c5a56 }

    /* Question block */
    .gq{ gap:1rem; padding:1rem .5rem; border-top: none; border-bottom:1px dashed #e1efed }
    .gq:last-child{ border-bottom:0 }
    .gq-number{
      width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;
      color:#0b4f44;background:rgba(17,120,103,.08);font-weight:700
    }
    .gq-statement{ color:#0e2d2b; font-weight:600 }

    /* Options */
    .gq .form-check{ padding:.35rem .5rem; border-radius:8px; transition: background-color .12s ease }
    .gq .form-check:hover{ background:#f6fbfa }
    .gq .form-check-input{ width:1.1em;height:1.1em; margin-top:.25em; accent-color:#117867 }
    .gq .form-check-label{ margin-left:.25rem }
    .gq textarea.form-control, .gq input[type="number"].form-control{ border-radius:10px }
    .gq textarea.form-control:focus, .gq input[type="number"].form-control:focus{ border-color:#15a085; box-shadow:0 0 0 .2rem rgba(21,160,133,.12) }

    /* Next button */
    .link-next{
      background: linear-gradient(135deg,#117867,#15a085);
      border:0; color:#fff; font-weight:700; border-radius:10px;
      box-shadow:0 8px 18px rgba(17,120,103,.25);
    }
    .link-next:hover{ filter:brightness(1.03); transform: translateY(-1px) }

    /* Timers */
    .timer-badge{ background: linear-gradient(180deg,#102536,#1f3b58) !important }

    /* Modal result table */
    #resultadoModal .table thead th{ background:#0f1c2e; color:#fff; border-color:#14253f }
    #resultadoModal .table tbody tr:hover{ background:#f7fbfa }
  </style>
</head>
<body>

  <!-- toggle móvil -->
  <div class="container d-md-none my-2">
    <a href="#" class="btn btn-light" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar">
      <i class="bi bi-list"></i> Menú
    </a>
  </div>

  <main class="app-wrapper">


    <!-- Contenido -->
    <section class="main-content">
      <header class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between mb-3 gap-2">
        <h1 class="page-title mb-2 mb-md-0">Sistema Educativo</h1>
        <div class="d-flex flex-wrap align-items-center gap-2 justify-content-center justify-content-md-end">
          <span class="badge text-bg-dark timer-badge" title="Tiempo total restante">
            <i class="bi bi-clock-history"></i> <span id="totalTime">--:--:--</span>
          </span>
          <span class="badge text-bg-secondary timer-badge" title="Tiempo para la pregunta actual">
            <i class="bi bi-stopwatch"></i> <span id="qTime">--:--</span>
          </span>
          <a href="?c=Inicio&a=Logout" class="btn btn-outline-light btn-sm" title="Cerrar Sesión">
            <i class="bi bi-box-arrow-right"></i> cerrar sesion
          </a>
        </div>

      <!-- Modal resultados -->
      <div class="modal fade" id="resultadoModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Resultados de la evaluación</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div id="resumenCalificacion" class="mb-3"></div>
              <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Pregunta</th>
                      <th>Tu respuesta</th>
                      <th>Correcta</th>
                      <th>Resultado</th>
                    </tr>
                  </thead>
                  <tbody id="resultadoBody"></tbody>
                </table>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
          </div>
        </div>
      </div>
      </header>

      <div class="content-panel">
        <div class="gform-wrap">
          <!-- Combo FUERA del card -->
          <div class="gform-toolbar">
            <label for="encuesta_id" class="fw-semibold">Evaluación:</label>
            <select id="encuesta_id" class="form-select form-select-sm">
              <option value="">Cargando evaluaciones...</option>
            </select>
          </div>

          <!-- Card del test -->
          <div class="gform-card">
            <h2 class="gform-title">Bienvenido <?php echo isset($_SESSION['user_nombre_completo']) ? $_SESSION['user_nombre_completo'] : 'Usuario'; ?></h2>
            <div class="gform-subtle mb-2"><strong id="tema">Selecciona una evaluación para iniciar</strong></div>

            <form id="frmEval">
              <div id="qContainer"><!-- preguntas dinámicas --></div>
              <div class="gform-footer">
                <a href="#" class="link-next" id="btnNext" role="button">Siguiente</a>
              </div>
            </form>
          </div>

          <div style="height:10px;background:var(--line);border-bottom-left-radius:.5rem;border-bottom-right-radius:.5rem"></div>
        </div>
      </div>
    </section>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const selEncuesta = document.getElementById('encuesta_id');
    const tema  = document.getElementById('tema');
    const qContainer = document.getElementById('qContainer');
    const btnNext = document.getElementById('btnNext');
    const totalTimeEl = document.getElementById('totalTime');
    const qTimeEl = document.getElementById('qTime');

    let preguntas = [];
    let idx = -1;
    let respuestasGuardadas = []; // Array para almacenar las respuestas temporalmente
    // Temporizadores
    const TOTAL_SECONDS = 60 * 60; // 60 minutos (1 hora)
    let totalRemaining = TOTAL_SECONDS;
    let perQuestionSeconds = 0;
    let perRemaining = 0;
    let totalTimer = null;
    let perTimer = null;
    let envioRealizado = false;

    function fmtHMS(sec){
      const h = Math.floor(sec/3600);
      const m = Math.floor((sec%3600)/60);
      const s = sec%60;
      const pad = n => String(n).padStart(2,'0');
      return `${pad(h)}:${pad(m)}:${pad(s)}`;
    }
    function fmtMS(sec){
      const m = Math.floor(sec/60);
      const s = sec%60;
      const pad = n => String(n).padStart(2,'0');
      return `${pad(m)}:${pad(s)}`;
    }
    function actualizarUIReloj(){
      totalTimeEl.textContent = fmtHMS(Math.max(0,totalRemaining));
      qTimeEl.textContent = fmtMS(Math.max(0,perRemaining));
    }
    function detenerTimers(){
      if(totalTimer){ clearInterval(totalTimer); totalTimer = null; }
      if(perTimer){ clearInterval(perTimer); perTimer = null; }
    }

    // Cargar encuestas para el combo
    function loadEncuestas(){
      selEncuesta.innerHTML = '<option value="">Cargando evaluaciones...</option>';
      fetch('?c=Evaluacion&a=ListarEncuestas', { credentials: 'same-origin' })
          .then(r => {
            // Registrar status y texto crudo para diagnóstico
            return r.text().then(text => ({ status: r.status, ok: r.ok, text }));
          })
          .then(obj => {
            console.log('[ListarEncuestas] status=', obj.status, ' ok=', obj.ok, ' raw=', obj.text);
            // Intentar parsear JSON, si falla mostrar texto crudo en el select
            try {
              const json = JSON.parse(obj.text);
              if (json && json.success) {
                const opts = ['<option value="">Seleccione evaluación...</option>']
                  .concat(json.data.map(e => `<option value="${e.id}" data-title="${(e.titulo||'').replace(/"/g,'&quot;')}">${e.titulo}</option>`));
                selEncuesta.innerHTML = opts.join('');
                return;
              }
              selEncuesta.innerHTML = '<option value="">No se pudo cargar</option>';
            } catch (err) {
              console.error('[ListarEncuestas] JSON parse error:', err);
              selEncuesta.innerHTML = `<option value="">Error al cargar: ${obj.status} - ${obj.text.replace(/"/g,'&quot;').slice(0,200)}</option>`;
            }
          })
          .catch((err) => {
            console.error('[ListarEncuestas] fetch error:', err);
            selEncuesta.innerHTML = '<option value="">Error al cargar</option>';
          });
    }

    function renderPregunta(){
      if(idx < 0 || idx >= preguntas.length){
        // Evaluación finalizada, mostrar botón para enviar
        qContainer.innerHTML = `
          <div class="alert alert-success">
            <h4>¡Evaluación completada!</h4>
            <p>Has respondido todas las preguntas. Haz clic en "Enviar Respuestas" para finalizar.</p>
            <button type="button" class="btn btn-primary" id="btnEnviarRespuestas">
              <i class="bi bi-send"></i> Enviar Respuestas
            </button>
          </div>`;
        btnNext.classList.add('disabled');
        btnNext.style.display = 'none';
        
        // Agregar evento al botón de enviar
        document.getElementById('btnEnviarRespuestas').addEventListener('click', enviarRespuestas);
        return;
      }
      const p = preguntas[idx];
      const numero = idx + 1;
      let opciones = '';
      if(p.tipo === 'opcion_unica' || p.tipo === 'opcion_multiple'){
        const tipoCtrl = (p.tipo === 'opcion_unica') ? 'radio' : 'checkbox';
        const list = Array.isArray(p.respuestas) ? p.respuestas : [];
        if(list.length === 0){
          opciones = '<div class="alert alert-warning">Esta pregunta no tiene opciones activas configuradas.</div>';
        } else {
          opciones = '<div class="gq-options">' + list.map((r,i)=>{
            const inputId = `p${p.id}_r${r.id}`;
            const name = `q_${p.id}` + (tipoCtrl==='checkbox' ? '[]' : '');
            const label = (r.respuesta_texto != null && r.respuesta_texto !== ''
                         ? r.respuesta_texto
                         : (r.respuesta_numero != null ? r.respuesta_numero : '')).toString();
            return `
              <div class="form-check">
                <input class="form-check-input" type="${tipoCtrl}" name="${name}" id="${inputId}" value="${r.id}">
                <label class="form-check-label" for="${inputId}">${label}</label>
              </div>`;
          }).join('') + '</div>';
        }
      } else if (p.tipo === 'abierta'){
        opciones = '<textarea class="form-control" name="q_'+p.id+'" rows="3" placeholder="Escribe tu respuesta..."></textarea>';
      } else if (p.tipo === 'numerica'){
        opciones = '<input class="form-control" type="number" step="any" name="q_'+p.id+'" placeholder="Ingresa un número">';
      }
      qContainer.innerHTML = `
        <div class="gq">
          <div class="gq-number">${numero}</div>
          <div>
            <div class="gq-statement mb-3">${p.enunciado}</div>
            ${opciones}
          </div>
        </div>`;
    }

    function startEval(encuestaId, title){
      if(!encuestaId){ qContainer.innerHTML=''; return; }
      tema.textContent = 'Evaluación: ' + (title || ('ID ' + encuestaId));
      qContainer.innerHTML = '<div class="text-center py-3">Cargando preguntas...</div>';
      btnNext.classList.remove('disabled');
      btnNext.style.display = 'inline-block';
      respuestasGuardadas = []; // Limpiar respuestas anteriores
      envioRealizado = false;
      // Reiniciar timers visuales
      detenerTimers();
      totalRemaining = TOTAL_SECONDS;
      actualizarUIReloj();
      fetch(`?c=Evaluacion&a=CargarEvaluacion&encuesta_id=${encodeURIComponent(encuestaId)}`)
        .then(r => r.text().then(t => ({ status: r.status, ok: r.ok, text: t })))
        .then(obj => {
          console.log('[CargarEvaluacion] status=', obj.status, ' ok=', obj.ok, ' raw=', obj.text);
          try {
            const json = JSON.parse(obj.text);
            try { console.log('Evaluacion data:', json); } catch(_){ }
            if(json && json.success){
              // Si el servidor indica que el alumno ya respondió, bloquear inmediatamente
              if (json.responded === true) {
                preguntas = []; idx = -1;
                btnNext.classList.add('disabled');
                btnNext.style.display = 'none';
                qContainer.innerHTML = `
                  <div class="alert alert-info">
                    <h5>Evaluación no disponible</h5>
                    <p>${json.msj || 'Ya has respondido esta evaluación anteriormente.'}</p>
                  </div>`;
                return;
              }
              preguntas = json.data || [];
              idx = 0;
              // Configurar tiempos por pregunta de forma equitativa (1 hora / total de preguntas)
              // Tiempo fijo por pregunta: 3 minutos = 180 segundos
              perQuestionSeconds = 180;
              perRemaining = perQuestionSeconds;
              // Iniciar timers
              totalTimer = setInterval(()=>{
                totalRemaining -= 1;
                if(totalRemaining <= 0){ totalRemaining = 0; actualizarUIReloj(); onTiempoAgotado(); return; }
                actualizarUIReloj();
              }, 1000);
              perTimer = setInterval(()=>{
                perRemaining -= 1;
                if(perRemaining <= 0){
                  // tiempo de la pregunta agotado: guardar y pasar a la siguiente
                  guardarRespuestaActual();
                  idx += 1;
                  if(idx >= preguntas.length){
                    renderPregunta();
                    onTiempoAgotado();
                    return;
                  }
                  perRemaining = perQuestionSeconds;
                  renderPregunta();
                }
                actualizarUIReloj();
              }, 1000);
              renderPregunta();
            } else {
              preguntas = []; idx = -1;
              qContainer.innerHTML = '<div class="alert alert-warning">No hay preguntas disponibles.</div>';
            }
          } catch (err) {
            console.error('[CargarEvaluacion] JSON parse error:', err);
            preguntas = []; idx = -1;
            qContainer.innerHTML = `<div class="alert alert-danger">Error cargando la evaluación: ${obj.status} - ${obj.text.replace(/"/g,'&quot;').slice(0,200)}</div>`;
          }
        })
        .catch((err) => {
          console.error('[CargarEvaluacion] fetch error:', err);
          preguntas = []; idx = -1;
          qContainer.innerHTML = '<div class="alert alert-danger">Error cargando la evaluación.</div>';
        });
    }

    selEncuesta.addEventListener('change', e => {
      const opt = selEncuesta.options[selEncuesta.selectedIndex];
      const title = opt ? opt.getAttribute('data-title') : '';
      startEval(e.target.value, title);
    });

    btnNext.addEventListener('click', function(e){
      e.preventDefault();
      if(idx < 0) return;
      
      // Guardar respuesta actual antes de continuar
      guardarRespuestaActual();
      
      // Validación mínima: exigir selección/entrada en opcion_unica/multiple
      const p = preguntas[idx];
      let ok = true;
      if(p.tipo === 'opcion_unica'){
        ok = !!document.querySelector('input[name="q_'+p.id+'"]:checked');
      } else if (p.tipo === 'opcion_multiple'){
        ok = document.querySelectorAll('input[name="q_'+p.id+'[]"]:checked').length > 0;
      } else if (p.tipo === 'abierta' || p.tipo === 'numerica'){
        const el = document.querySelector('[name="q_'+p.id+'"]');
        ok = el && el.value.trim() !== '';
      }
      if(!ok){ alert('Responde la pregunta antes de continuar.'); return; }

      idx += 1;
      perRemaining = perQuestionSeconds; // reiniciar tiempo por pregunta al avanzar manualmente
      renderPregunta();
    });

    // Función para guardar la respuesta actual
    function guardarRespuestaActual() {
      if(idx < 0 || idx >= preguntas.length) return;
      
      const p = preguntas[idx];
      let respuesta = {
        pregunta_id: p.id
      };
      
      if(p.tipo === 'opcion_unica'){
        const checked = document.querySelector('input[name="q_'+p.id+'"]:checked');
        if(checked) {
          respuesta.respuesta_id = parseInt(checked.value);
        }
      } else if (p.tipo === 'opcion_multiple'){
        const checked = document.querySelectorAll('input[name="q_'+p.id+'[]"]:checked');
        if(checked.length > 0) {
          respuesta.respuesta_id = Array.from(checked).map(el => parseInt(el.value));
        }
      } else if (p.tipo === 'abierta'){
        const el = document.querySelector('[name="q_'+p.id+'"]');
        if(el && el.value.trim() !== '') {
          respuesta.respuesta_texto = el.value.trim();
        }
      } else if (p.tipo === 'numerica'){
        const el = document.querySelector('[name="q_'+p.id+'"]');
        if(el && el.value.trim() !== '') {
          respuesta.respuesta_numero = parseFloat(el.value);
        }
      }
      
      // Agregar o actualizar en el array de respuestas
      const existingIndex = respuestasGuardadas.findIndex(r => r.pregunta_id === p.id);
      if(existingIndex >= 0) {
        respuestasGuardadas[existingIndex] = respuesta;
      } else {
        respuestasGuardadas.push(respuesta);
      }
    }

    // Función para enviar todas las respuestas al servidor
    function onTiempoAgotado(){
      if(envioRealizado) return;
      btnNext.classList.add('disabled');
      detenerTimers();
      enviarRespuestas();
    }

    function evaluarLocalmente() {
      // Intenta evaluar usando las respuestas correctas si vienen en el payload de preguntas
      let correctas = 0;
      const detalle = [];
      const mapResp = new Map();
      respuestasGuardadas.forEach(r => { mapResp.set(r.pregunta_id, r); });

      preguntas.forEach((p, i) => {
        const r = mapResp.get(p.id);
        // Obtener texto de tu respuesta
        let tuTexto = '';
        if (r) {
          if (p.tipo === 'opcion_unica') {
            const opt = (p.respuestas||[]).find(o => o.id == r.respuesta_id);
            tuTexto = opt ? (opt.respuesta_texto || ('Opción '+opt.id)) : '';
          } else if (p.tipo === 'opcion_multiple') {
            const ids = r.respuestas_ids || [];
            tuTexto = (p.respuestas||[]).filter(o => ids.indexOf(String(o.id))>=0 || ids.indexOf(o.id)>=0).map(o=>o.respuesta_texto).join(', ');
          } else if (p.tipo === 'abierta') {
            tuTexto = r.respuesta_texto || '';
          } else if (p.tipo === 'numerica') {
            tuTexto = (r.respuesta_numero!=null ? r.respuesta_numero : '');
          }
        }

        // Obtener texto de la respuesta correcta (si está disponible)
        let corrTexto = '';
        let esCorrecto = false;
        const correctasArr = (p.respuestas||[]).filter(o => String(o.es_correcta)==='1');
        if (p.tipo === 'opcion_unica') {
          const corr = correctasArr[0];
          if (corr) corrTexto = corr.respuesta_texto || '';
          esCorrecto = !!(r && r.respuesta_id && corr && String(r.respuesta_id) === String(corr.id));
        } else if (p.tipo === 'opcion_multiple') {
          const corrIds = new Set(correctasArr.map(o=>String(o.id)));
          corrTexto = correctasArr.map(o=>o.respuesta_texto).join(', ');
          const marcadas = new Set((r && r.respuestas_ids ? r.respuestas_ids.map(x=>String(x)) : []));
          if (corrIds.size>0) {
            esCorrecto = (corrIds.size === marcadas.size) && Array.from(corrIds).every(id => marcadas.has(id));
          }
        } else if (p.tipo === 'abierta' || p.tipo === 'numerica') {
          // Sin regla local fiable: marcar como no evaluable si no hay bandera es_correcta
          if (correctasArr.length>0) {
            corrTexto = correctasArr.map(o=> o.respuesta_texto || o.respuesta_numero || '').join(', ');
            // Comparación exacta simple
            if (p.tipo === 'abierta') esCorrecto = (tuTexto.trim().toLowerCase() === (corrTexto||'').trim().toLowerCase());
            if (p.tipo === 'numerica') esCorrecto = (String(tuTexto) === String(corrTexto));
          } else {
            corrTexto = '(No disponible)';
            esCorrecto = false;
          }
        }

        if (esCorrecto) correctas += 1;
        detalle.push({ idx:i+1, enunciado:p.enunciado, tuTexto, corrTexto, esCorrecto });
      });

      // 5 puntos por acierto, 20 preguntas = 100
      const puntaje = correctas * 5;
      return { correctas, puntaje, detalle };
    }

    function mostrarResultados(){
      const r = evaluarLocalmente();
      const resumen = `<div class="alert alert-info">
        <strong>Correctas:</strong> ${r.correctas} / ${preguntas.length} • <strong>Puntaje:</strong> ${r.puntaje} / 100
      </div>`;
      document.getElementById('resumenCalificacion').innerHTML = resumen;
      const tbody = document.getElementById('resultadoBody');
      tbody.innerHTML = r.detalle.map(d=>`
        <tr>
          <td>${d.idx}</td>
          <td>${d.enunciado}</td>
          <td>${d.tuTexto || '<em>Sin respuesta</em>'}</td>
          <td>${d.corrTexto}</td>
          <td>${d.esCorrecto ? '<span class="badge text-bg-success">Correcta</span>' : '<span class="badge text-bg-danger">Incorrecta</span>'}</td>
        </tr>
      `).join('');
      const modal = new bootstrap.Modal(document.getElementById('resultadoModal'));
      modal.show();
    }

    function enviarRespuestas() {
      if(envioRealizado) return;
      const encuestaId = selEncuesta.value;
      if(!encuestaId) {
        alert('No hay encuesta seleccionada');
        return;
      }
      
      if(respuestasGuardadas.length === 0) {
        alert('No hay respuestas para enviar');
        return;
      }
      
      // Verificar que el usuario esté autenticado (opcional, ya que el servidor también valida)
      const userInfo = <?php echo json_encode(isset($_SESSION['user_id']) ? [
        'id' => $_SESSION['user_id'],
        'nombre' => isset($_SESSION['user_nombre_completo']) ? $_SESSION['user_nombre_completo'] : 'Usuario',
        'rol' => isset($_SESSION['user_rol']) ? $_SESSION['user_rol'] : 'ALUMNO'
      ] : null); ?>;
      
      if (!userInfo) {
        alert('Debes iniciar sesión para enviar la evaluación');
        window.location.href = '?c=Login';
        return;
      }
      
      // Deshabilitar botón para evitar doble envío
      const btnEnviar = document.getElementById('btnEnviarRespuestas');
      btnEnviar.disabled = true;
      btnEnviar.innerHTML = '<i class="bi bi-hourglass-split"></i> Enviando...';
      
      // Calcular puntaje final
      const resultadoFinal = evaluarLocalmente();
      const puntajeCalculado = resultadoFinal.puntaje;
      
      // Validar que el puntaje sea un número válido entre 0 y 100
      if (typeof puntajeCalculado !== 'number' || isNaN(puntajeCalculado) || puntajeCalculado < 0 || puntajeCalculado > 100) {
        console.error('Puntaje inválido:', puntajeCalculado);
        throw new Error('Error al calcular el puntaje de la evaluación');
      }
      
      // Preparar datos para enviar
      const data = {
        encuesta_id: parseInt(encuestaId),
        respuestas: respuestasGuardadas,
        puntaje: puntajeCalculado // Incluir puntaje validado
      };
      
      fetch('?c=Evaluacion&a=GuardarRespuestas', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
      })
      .then(response => response.json())
      .then(result => {
        if(result.success) {
          envioRealizado = true;
          detenerTimers();
          const usuario = result.data.usuario;
          // Deshabilitar el botón Siguiente
          btnNext.classList.add('disabled');
          qContainer.innerHTML = `
            <div class="alert alert-success">
              <h4>¡Encuesta enviada exitosamente!</h4>
              <p>${result.msj}</p>
              <hr>
              <div class="row">
                <div class="col-md-6">
                  <p><strong>Estudiante:</strong> ${usuario.nombre}</p>
                  <p><strong>Institución:</strong> ${usuario.institucion}</p>
                </div>
                <div class="col-md-6 text-end">
                  <button type="button" class="btn btn-outline-primary" onclick="event.preventDefault(); mostrarResultados();"><i class="bi bi-bar-chart-fill"></i> Ver resultados</button>
                </div>
              </div>
            </div>`;
          // Ocultar botón Siguiente
          btnNext.style.display = 'none';
          // Abrir modal de resultados automáticamente
          setTimeout(mostrarResultados, 200);
        } else {
          throw new Error(result.msj || 'Error desconocido');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        qContainer.innerHTML = `
          <div class="alert alert-danger">
            <h4>Error al enviar respuestas</h4>
            <p>${error.message}</p>
            <button type="button" class="btn btn-primary" onclick="enviarRespuestas()">
              <i class="bi bi-arrow-repeat"></i> Reintentar
            </button>
          </div>`;
      });
    }

    // Inicial: cargar encuestas
    loadEncuestas();
  </script>
</body>
</html>
