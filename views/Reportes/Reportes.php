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


    :root{
      --bg-teal:#4f8f8a; --sidebar-header:#a8c0bb; --line:#2e655f; --title:#235c9c; --ink:#173a38;
    }
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
      background:#fff;color:var(--title);font-weight:700;border-radius:.25rem;
      display:inline-block;padding:.35rem 1.25rem;box-shadow:0 2px 0 rgba(0,0,0,.25) inset;
    }

    /* Cintillo “Power BI” */
    .gwrap{border:2px solid var(--line);border-radius:.5rem;background:rgba(79,143,138,.15);padding:1rem}
    .divider-foot{height:10px;background:var(--line);border-bottom-left-radius:.5rem;border-bottom-right-radius:.5rem}

    /* Filtros */
    .filters{gap: .75rem}
    .filters .form-select{min-width:220px}
    .filters a{ text-decoration:none }

    /* KPIs */
    .kpi{
      background:#f8fafb;border:1px solid #e8eef0;border-radius:.75rem;padding:1rem;
      box-shadow:0 1px 2px rgba(0,0,0,.05); height:100%;
    }
    .kpi-title{font-size:.9rem;color:#506b69;margin-bottom:.25rem}
    .kpi-value{font-size:1.6rem;font-weight:800;color:#0d2b2a;line-height:1}
    .kpi-diff{font-size:.85rem}

    /* Tabla estilo */
    .table thead th{white-space:nowrap;background:#e9f2f1}
    .table-wrap{max-height:260px;overflow:auto;border:1px solid #e8eef0;border-radius:.5rem}
    .tag-reset{
      display:inline-block;border:1px dashed #6c9ea0;border-radius:.35rem;padding:.25rem .5rem;color:#134c4a;
    }

    @media (max-width: 767.98px){
      .sidebar{display:none}
      section.main-content{margin-left:0 !important;width:100% !important;padding:1rem !important}
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
        <div class="gwrap">
          <!-- Filtros -->
          <div class="d-flex flex-wrap align-items-center filters mb-3">
            <div class="d-flex align-items-center gap-2">
              <label class="fw-semibold">Instituto:</label>
              <select id="fInstituto" class="form-select form-select-sm">
                <option value="*">Todos</option>
              </select>
            </div>
            <div class="d-flex align-items-center gap-2">
              <label class="fw-semibold">Grado:</label>
              <select id="fGrado" class="form-select form-select-sm">
                <option value="*">Todos</option>
                <option>1ro Básico</option><option>2do Básico</option><option>3ro Básico</option>
                <option>4to Diversificado</option><option>5to Diversificado</option>
              </select>
            </div>
            <a href="#" id="lnkReset" class="tag-reset ms-auto"><i class="bi bi-arrow-counterclockwise me-1"></i>Restablecer</a>
          </div>

          <!-- KPIs -->
          <div class="row g-3 mb-3">
            <div class="col-6 col-md-3"><div class="kpi">
              <div class="kpi-title">Total alumnos</div><div class="kpi-value" id="kTotal">0</div>
            </div></div>
            <div class="col-6 col-md-3"><div class="kpi">
              <div class="kpi-title">Promedio global</div><div class="kpi-value" id="kPromedio">0</div>
            </div></div>
            <div class="col-6 col-md-3"><div class="kpi">
              <div class="kpi-title">% Aprobación (≥60)</div><div class="kpi-value" id="kAprobacion">0%</div>
            </div></div>
            <div class="col-6 col-md-3"><div class="kpi">
              <div class="kpi-title">Mejor instituto</div><div class="kpi-value" id="kTopInst">—</div>
            </div></div>
          </div>

          <!-- Tabla -->
          <div class="table-wrap mb-3">
            <table class="table table-sm table-bordered mb-0" id="tbl">
              <thead>
                <tr>
                  <th>ID</th><th>Código</th><th>Instituto</th><th>Nombres</th><th>Apellidos</th>
                  <th>Grado</th><th>nota1</th><th>nota2</th><th>nota3</th><th>nota4</th><th>promedio</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>

          <!-- Gráficas -->
          <div class="row g-3">
            <div class="col-lg-6">
              <div class="card border-0 shadow-sm">
                <div class="card-body">
                  <h6 class="card-title mb-2">Distribución de alumnos por instituto</h6>
                  <canvas id="pieInstitutos" height="200"></canvas>
                </div>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="card border-0 shadow-sm">
                <div class="card-body">
                  <h6 class="card-title mb-2">Promedio por instituto</h6>
                  <canvas id="barPromedios" height="200"></canvas>
                </div>
              </div>
            </div>
          </div>

          <div class="divider-foot mt-3"></div>
        </div>
      </div>
    </section>
  </main>

  <script>
    // ================== Datos de ejemplo ==================
    const ALUMNOS = [
      {id:1,codigo:'A-001',inst:'Instituto Ricardo',nombres:'Ana',apellidos:'López',grado:'3ro Básico',notas:[72,65,81,77]},
      {id:2,codigo:'A-002',inst:'Instituto Caal',nombres:'Luis',apellidos:'Hernández',grado:'2do Básico',notas:[90,85,91,88]},
      {id:3,codigo:'A-003',inst:'Instituto La Fruta',nombres:'María',apellidos:'Gómez',grado:'3ro Básico',notas:[55,61,58,62]},
      {id:4,codigo:'A-004',inst:'INEBO',nombres:'Carlos',apellidos:'García',grado:'1ro Básico',notas:[78,82,75,80]},
      {id:5,codigo:'A-005',inst:'INED',nombres:'Diana',apellidos:'Pereira',grado:'4to Diversificado',notas:[88,93,90,95]},
      {id:6,codigo:'A-006',inst:'Instituto Santa Rosa',nombres:'Javier',apellidos:'Mendoza',grado:'2do Básico',notas:[67,70,64,72]},
      {id:7,codigo:'A-007',inst:'Instituto San Andrés',nombres:'Laura',apellidos:'Vargas',grado:'5to Diversificado',notas:[91,86,94,89]},
      {id:8,codigo:'A-008',inst:'Instituto Cristo Rey',nombres:'Pedro',apellidos:'Cano',grado:'3ro Básico',notas:[48,55,52,50]},
      {id:9,codigo:'A-009',inst:'Instituto Libertad',nombres:'Valeria',apellidos:'Jiménez',grado:'4to Diversificado',notas:[76,81,79,84]},
      {id:10,codigo:'A-010',inst:'Tel Secundaria La Dalia',nombres:'Sofía',apellidos:'Reyes',grado:'1ro Básico',notas:[69,72,71,70]},
      {id:11,codigo:'A-011',inst:'Tel Secundaria El Porvenir',nombres:'Diego',apellidos:'Rojas',grado:'2do Básico',notas:[62,64,60,66]},
      {id:12,codigo:'A-012',inst:'Instituto Ricardo',nombres:'Emilia',apellidos:'Caal',grado:'5to Diversificado',notas:[95,92,94,96]},
      {id:13,codigo:'A-013',inst:'Instituto Santa Rosa',nombres:'Noé',apellidos:'Lima',grado:'3ro Básico',notas:[73,70,76,74]},
      {id:14,codigo:'A-014',inst:'INEBO',nombres:'Paola',apellidos:'García',grado:'4to Diversificado',notas:[85,82,84,88]},
      {id:15,codigo:'A-015',inst:'Instituto La Fruta',nombres:'Kevin',apellidos:'Pérez',grado:'2do Básico',notas:[58,60,63,61]},
      {id:16,codigo:'A-016',inst:'Instituto Caal',nombres:'Brenda',apellidos:'Sic',grado:'3ro Básico',notas:[77,79,82,80]},
      {id:17,codigo:'A-017',inst:'Instituto Libertad',nombres:'Ángel',apellidos:'Solares',grado:'5to Diversificado',notas:[88,86,83,90]},
      {id:18,codigo:'A-018',inst:'Instituto Cristo Rey',nombres:'Nancy',apellidos:'Gómez',grado:'1ro Básico',notas:[61,59,63,65]},
      {id:19,codigo:'A-019',inst:'Tel Secundaria La Dalia',nombres:'Ricardo',apellidos:'Paz',grado:'3ro Básico',notas:[74,75,70,72]},
      {id:20,codigo:'A-020',inst:'Tel Secundaria El Porvenir',nombres:'Lina',apellidos:'Barrios',grado:'4to Diversificado',notas:[83,81,79,85]},
    ];

    // ================== Utilidades ==================
    const avg = arr => Math.round((arr.reduce((a,b)=>a+b,0)/arr.length) * 100)/100;

    function distinct(list, key){
      return [...new Set(list.map(x=>x[key]))];
    }

    function aprobaron(list){
      const a = list.filter(x => promedio(x) >= 60).length;
      return list.length ? Math.round((a/list.length)*100) : 0;
    }

    function promedio(item){ return avg(item.notas); }

    // ================== Estado de filtros ==================
    const fInstituto = document.getElementById('fInstituto');
    const fGrado = document.getElementById('fGrado');
    const lnkReset = document.getElementById('lnkReset');

    // Cargar opciones de institutos
    distinct(ALUMNOS,'inst').forEach(i=>{
      const o=document.createElement('option');o.value=i;o.textContent=i;fInstituto.appendChild(o);
    });

    // ================== Render principal ==================
    const tbody = document.querySelector('#tbl tbody');
    const kTotal = document.getElementById('kTotal');
    const kPromedio = document.getElementById('kPromedio');
    const kAprobacion = document.getElementById('kAprobacion');
    const kTopInst = document.getElementById('kTopInst');

    let pieChart, barChart;

    function getFiltered(){
      return ALUMNOS.filter(a =>
        (fInstituto.value==='*' || a.inst===fInstituto.value) &&
        (fGrado.value==='*'    || a.grado===fGrado.value)
      );
    }

    function renderTable(rows){
      tbody.innerHTML = rows.map(a=>{
        const p = promedio(a);
        return `<tr>
          <td>${a.id}</td><td>${a.codigo}</td><td>${a.inst}</td>
          <td>${a.nombres}</td><td>${a.apellidos}</td><td>${a.grado}</td>
          <td>${a.notas[0]}</td><td>${a.notas[1]}</td><td>${a.notas[2]}</td><td>${a.notas[3]}</td>
          <td>${p}</td>
        </tr>`;
      }).join('');
    }

    function renderKpis(rows){
      kTotal.textContent = rows.length;
      const prom = rows.length ? avg(rows.map(promedio)) : 0;
      kPromedio.textContent = prom.toFixed(1);
      kAprobacion.textContent = aprobaron(rows) + '%';

      // Mejor instituto por promedio
      const byInst = groupBy(rows,'inst');
      let bestName='—', bestAvg=-1;
      Object.entries(byInst).forEach(([name, list])=>{
        const v = avg(list.map(promedio));
        if(v>bestAvg){ bestAvg=v; bestName=name; }
      });
      kTopInst.textContent = rows.length ? bestName : '—';
    }

    function groupBy(arr, key){
      return arr.reduce((acc,cur)=>{
        (acc[cur[key]] = acc[cur[key]] || []).push(cur);
        return acc;
      },{});
    }

    function renderCharts(rows){
      const ctxPie = document.getElementById('pieInstitutos').getContext('2d');
      const ctxBar = document.getElementById('barPromedios').getContext('2d');

      const byInst = groupBy(rows,'inst');
      const labels = Object.keys(byInst);
      const counts = labels.map(l=>byInst[l].length);
      const avgs   = labels.map(l=>avg(byInst[l].map(promedio)));

      pieChart?.destroy();
      barChart?.destroy();

      pieChart = new Chart(ctxPie,{
        type:'pie',
        data:{ labels, datasets:[{ data:counts }]},
        options:{ plugins:{ legend:{ position:'right' } } }
      });

      barChart = new Chart(ctxBar,{
        type:'bar',
        data:{ labels, datasets:[{ label:'Promedio', data:avgs }]},
        options:{
          scales:{ y:{ beginAtZero:true, suggestedMax:100 }},
          plugins:{ legend:{ display:false } }
        }
      });
    }

    function renderAll(){
      const rows = getFiltered();
      renderTable(rows);
      renderKpis(rows);
      renderCharts(rows);
    }

    fInstituto.addEventListener('change', renderAll);
    fGrado.addEventListener('change', renderAll);
    lnkReset.addEventListener('click', (e)=>{ e.preventDefault(); fInstituto.value='*'; fGrado.value='*'; renderAll(); });

    // Inicial
    renderAll();
  </script>
</body>
</html>
