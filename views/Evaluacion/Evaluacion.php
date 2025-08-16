<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sistema Educativo — Evaluación</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>

  <style>
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
    <!-- Sidebar -->
    <aside class="sidebar d-none d-md-block">
      <div class="sidebar-box">
        <div class="sidebar-header">Principal</div>
        <div class="d-grid gap-3">
          <a class="link-glow fs-5" href="?c=Menu"><i class="bi bi-grid-fill me-2"></i>Menú</a>
          <a class="link-glow fs-5" href="?c=Usuarios"><i class="bi bi-people-fill me-2"></i>Usuarios</a>
          <a class="link-glow fs-5 active" href="?c=Evaluacion"><i class="bi bi-clipboard-check-fill me-2"></i>Evaluación</a>
          <a class="link-glow fs-5" href="?c=Reportes"><i class="bi bi-bar-chart-fill me-2"></i>Reportes</a>
          <a class="link-glow fs-5" href="?c=Material"><i class="bi bi-bar-chart-fill me-2"></i>Material</a>
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
          <a class="link-glow fs-5" data-bs-dismiss="offcanvas" href="#">Menú</a>
          <a class="link-glow fs-5" data-bs-dismiss="offcanvas" href="#">Usuarios</a>
          <a class="link-glow fs-5" data-bs-dismiss="offcanvas" href="#">Evaluación</a>
          <a class="link-glow fs-5" data-bs-dismiss="offcanvas" href="#">Reportes</a>
          <a class="link-glow fs-5" data-bs-dismiss="offcanvas" href="#">Material</a>
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
        <div class="gform-wrap">
          <!-- Combo FUERA del card -->
          <div class="gform-toolbar">
            <label for="curso" class="fw-semibold">Curso:</label>
            <select id="curso" class="form-select form-select-sm">
              <option value="mat" selected>Matemática</option>
              <option value="comp">Computación</option>
            </select>
          </div>

          <!-- Card del test -->
          <div class="gform-card">
            <h2 class="gform-title">Bienvenido Carlos Gómez</h2>
            <div class="gform-subtle mb-2"><strong id="tema">Tema: Ecuaciones de Primer Grado</strong></div>

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
    // Temas por curso
    const THEMES = { mat:'Ecuaciones de Primer Grado', comp:'Fundamentos de Computación' };

    // Pregunta demo por curso
    const QUESTIONS = {
      mat: `
        <div class="gq">
          <div class="gq-number">1</div>
          <div>
            <div class="gq-statement mb-3">Resuelva la ecuación <b>3X - 5 = X + 3</b>.</div>
            <div class="gq-options">
              <div class="form-check"><input class="form-check-input" type="radio" name="q1" id="mat1a" value="2"><label class="form-check-label" for="mat1a">X = 2</label></div>
              <div class="form-check"><input class="form-check-input" type="radio" name="q1" id="mat1b" value="9"><label class="form-check-label" for="mat1b">X = 9</label></div>
              <div class="form-check"><input class="form-check-input" type="radio" name="q1" id="mat1c" value="4"><label class="form-check-label" for="mat1c">X = 4</label></div>
            </div>
          </div>
        </div>`,
      comp: `
        <div class="gq">
          <div class="gq-number">1</div>
          <div>
            <div class="gq-statement mb-3">En binario, ¿a qué número decimal equivale <b>1011</b>?</div>
            <div class="gq-options">
              <div class="form-check"><input class="form-check-input" type="radio" name="q1" id="comp1a" value="9"><label class="form-check-label" for="comp1a">9</label></div>
              <div class="form-check"><input class="form-check-input" type="radio" name="q1" id="comp1b" value="11"><label class="form-check-label" for="comp1b">11</label></div>
              <div class="form-check"><input class="form-check-input" type="radio" name="q1" id="comp1c" value="13"><label class="form-check-label" for="comp1c">13</label></div>
            </div>
          </div>
        </div>`
    };

    const curso = document.getElementById('curso');
    const tema  = document.getElementById('tema');
    const qContainer = document.getElementById('qContainer');

    function loadCourse(key){
      tema.textContent = 'Tema: ' + THEMES[key];
      qContainer.innerHTML = QUESTIONS[key];
      qContainer.closest('.gform-card').scrollIntoView({behavior:'smooth', block:'start'});
    }

    // Inicial
    loadCourse('mat');

    // Cambio de curso
    curso.addEventListener('change', e => loadCourse(e.target.value));

    // “Siguiente” de demo
    document.getElementById('btnNext').addEventListener('click', function(e){
      e.preventDefault();
      const choice = document.querySelector('#qContainer input[name="q1"]:checked');
      if(!choice){ alert('Selecciona una respuesta antes de continuar.'); return; }
      alert('Curso: ' + curso.options[curso.selectedIndex].text + '\nRespuesta: ' + choice.value + '\n(Continuar con la siguiente pregunta…)');
    });
  </script>
</body>
</html>
