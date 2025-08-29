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
      .gform-card{margin:.5rem auto}
    }
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
      <header class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="page-title">Sistema Educativo</h1>
        <i class="bi bi-arrow-right-square-fill fs-2 text-light d-none d-md-inline"></i>
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
            <h2 class="gform-title">Bienvenido Carlos Gómez</h2>
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

    let preguntas = [];
    let idx = -1;

    // Cargar encuestas para el combo
    function loadEncuestas(){
      selEncuesta.innerHTML = '<option value="">Cargando evaluaciones...</option>';
      fetch('?c=Evaluacion&a=ListarEncuestas')
        .then(r => r.json())
        .then(json => {
          if(json && json.success){
            const opts = ['<option value="">Seleccione evaluación...</option>']
              .concat(json.data.map(e => `<option value="${e.id}" data-title="${e.titulo?.replaceAll('"','&quot;')}">${e.titulo} (ID: ${e.id})</option>`));
            selEncuesta.innerHTML = opts.join('');
          } else {
            selEncuesta.innerHTML = '<option value="">No se pudo cargar</option>';
          }
        })
        .catch(() => selEncuesta.innerHTML = '<option value="">Error al cargar</option>');
    }

    function renderPregunta(){
      if(idx < 0 || idx >= preguntas.length){
        qContainer.innerHTML = '<div class="alert alert-success">Evaluación finalizada. ¡Gracias!</div>';
        btnNext.classList.add('disabled');
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
      fetch(`?c=Evaluacion&a=CargarEvaluacion&encuesta_id=${encodeURIComponent(encuestaId)}`)
        .then(r=>r.json())
        .then(json=>{
          try { console.log('Evaluacion data:', json); } catch(_){ }
          if(json && json.success){
            preguntas = json.data || [];
            idx = 0;
            renderPregunta();
          } else {
            preguntas = []; idx = -1;
            qContainer.innerHTML = '<div class="alert alert-warning">No hay preguntas disponibles.</div>';
          }
        })
        .catch(()=>{
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
      renderPregunta();
    });

    // Inicial: cargar encuestas
    loadEncuestas();
  </script>
</body>
</html>
