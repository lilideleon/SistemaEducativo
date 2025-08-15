<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sistema Educativo — Registro de Alumnos</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />

  <style>
    :root{
      --bg-teal:#4f8f8a;
      --sidebar-header:#a8c0bb;
      --accent:#1f3554;  /* azul botones */
    }

    html,body{height:100%;margin:0}
    body{background-color:var(--bg-teal);display:flex;flex-direction:column}

    .app-wrapper{display:flex;flex:1;height:100vh}

    /* Sidebar */
    .sidebar{
      width:260px; height:100vh; position:fixed; left:0; top:0; overflow-y:auto;
      background:#50938a; padding:1rem; box-shadow:2px 0 5px rgba(0,0,0,.1); z-index:1000;
    }
    .sidebar-box{background:rgba(255,255,255,.1); border-radius:.5rem; padding:.75rem}
    .sidebar-header{background:var(--sidebar-header); color:#1f2937; font-weight:600; padding:.5rem .75rem; border-radius:.35rem; margin-bottom:.75rem}

    .btn-glow{
      border:0; border-radius:1rem; padding:.9rem 1.1rem; color:#fff; font-weight:700; text-align:left;
      background:
        radial-gradient(140% 120% at 50% 40%, rgba(255,255,255,.28) 0%, rgba(255,255,255,.08) 42%, rgba(0,0,0,.22) 75%),
        linear-gradient(180deg, #0f1c2e 0%, #1f3554 100%);
      box-shadow:0 8px 18px rgba(0,0,0,.25), inset 0 -2px 0 rgba(255,255,255,.1);
      transition:transform .12s ease, filter .12s ease;
    }
    .btn-glow:hover{transform:translateY(-2px); filter:brightness(1.02)}
    .btn-glow.active{outline:2px solid rgba(255,255,255,.35)}

    /* Contenido */
    section.main-content{
      margin-left:260px; width:calc(100% - 260px);
      padding:2rem; min-height:100vh; display:flex; flex-direction:column;
    }
    .content-panel{
      flex:1; display:flex; flex-direction:column; min-height:0;
      background:#fff; border-radius:.5rem; padding:1.25rem 1.25rem 1rem;
      box-shadow:0 .125rem .25rem rgba(0,0,0,.075);
    }
    #contentArea{flex:1; overflow:auto}

    /* Encabezado estilo maqueta */
    .page-title{
      background:#ffffff; color:#235c9c; font-weight:700;
      border-radius:.25rem; display:inline-block; padding:.35rem 1.25rem;
      box-shadow:0 2px 0 rgba(0,0,0,.25) inset;
    }

    /* Bloque del formulario */
    .form-block{
      border:2px solid #2e655f; border-radius:.5rem;
      background:rgba(79,143,138,.15);
    }
    .form-title{
      background:rgba(79,143,138,.35);
      border-bottom:2px solid #2e655f;
      border-top-left-radius:.5rem; border-top-right-radius:.5rem;
      padding:.5rem .75rem; font-weight:600; color:#173a38;
    }

    .btn-registrar{
      background:#f4f5e6; border:2px solid #2e655f; color:#173a38; font-weight:600;
      padding:.45rem 1rem; border-radius:.35rem;
    }
    .btn-registrar:hover{filter:brightness(1.02)}

    /* Tabla */
    .table thead th{white-space:nowrap; background:#e9f2f1}
    .table tbody td{vertical-align:middle}

    @media (max-width: 767.98px){
      .sidebar{display:none}
      section.main-content{margin-left:0 !important; width:100% !important; padding:1rem !important}
    }
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
    <!-- Sidebar -->
    <aside class="sidebar d-none d-md-block">
      <div class="sidebar-box">
        <div class="sidebar-header">Principal</div>
        <div class="d-grid gap-3">
          <a href="?c=Menu" class="btn btn-glow fs-5 text-decoration-none"><i class="bi bi-grid-fill me-2"></i>Menú</a>
          <a href="?c=Alumnos" class="btn btn-glow fs-5 text-decoration-none active"><i class="bi bi-people-fill me-2"></i>Alumnos</a>
          <a href="#" class="btn btn-glow fs-5 text-decoration-none"><i class="bi bi-person-gear me-2"></i>Directores</a>
          <a href="#" class="btn btn-glow fs-5 text-decoration-none"><i class="bi bi-clipboard-check-fill me-2"></i>Evaluación</a>
          <a href="#" class="btn btn-glow fs-5 text-decoration-none"><i class="bi bi-bar-chart-fill me-2"></i>Reportes</a>
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
          <a href="?c=Menu" class="btn btn-glow fs-5 text-decoration-none" data-bs-dismiss="offcanvas"><i class="bi bi-grid-fill me-2"></i>Menú</a>
          <a href="?c=Alumnos" class="btn btn-glow fs-5 text-decoration-none" data-bs-dismiss="offcanvas"><i class="bi bi-people-fill me-2"></i>Alumnos</a>
          <a href="#" class="btn btn-glow fs-5 text-decoration-none" data-bs-dismiss="offcanvas"><i class="bi bi-person-gear me-2"></i>Directores</a>
          <a href="#" class="btn btn-glow fs-5 text-decoration-none" data-bs-dismiss="offcanvas"><i class="bi bi-clipboard-check-fill me-2"></i>Evaluación</a>
          <a href="#" class="btn btn-glow fs-5 text-decoration-none" data-bs-dismiss="offcanvas"><i class="bi bi-bar-chart-fill me-2"></i>Reportes</a>
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
        <div class="form-block mb-3">
          <div class="form-title">Registro de Alumnos</div>
          <div class="p-3">
            <form id="frmAlumno" class="row g-3 align-items-end">
              <div class="col-md-3">
                <label for="codigo" class="form-label">Código</label>
                <input type="text" class="form-control form-control-sm" id="codigo" required>
              </div>
              <div class="col-md-3">
                <label for="nombres" class="form-label">Nombres</label>
                <input type="text" class="form-control form-control-sm" id="nombres" required>
              </div>
              <div class="col-md-3">
                <label for="apellidos" class="form-label">Apellidos</label>
                <input type="text" class="form-control form-control-sm" id="apellidos" required>
              </div>
              <div class="col-md-3">
                <label for="grado" class="form-label">Grado</label>
                <select class="form-select form-select-sm" id="grado" required>
                  <option value="" selected disabled>Seleccione un grado</option>
                  <option value="1ro Básico">1ro Básico</option>
                  <option value="2do Básico">2do Básico</option>
                  <option value="3ro Básico">3ro Básico</option>
                  <option value="4to Básico">4to Básico</option>
                  <option value="5to Básico">5to Básico</option>
                  <option value="6to Básico">6to Básico</option>
                  <option value="1ro Diversificado">1ro Diversificado</option>
                  <option value="2do Diversificado">2do Diversificado</option>
                  <option value="3ro Diversificado">3ro Diversificado</option>
                  <option value="4to Diversificado">4to Diversificado</option>
                </select>
              </div>
              <div class="col-md-3">
                <label for="instituto" class="form-label">Instituto</label>
                <select class="form-select form-select-sm" id="instituto" required>
                  <option value="" selected disabled>Seleccione un instituto</option>
                  <option value="Instituto Nacional de Educación Básica">Instituto Nacional de Educación Básica</option>
                  <option value="Colegio Mixto Bilingüe">Colegio Mixto Bilingüe</option>
                  <option value="Centro Educativo Técnico">Centro Educativo Técnico</option>
                  <option value="Academia Cristiana de Educación">Academia Cristiana de Educación</option>
                  <option value="Instituto Técnico Vocacional">Instituto Técnico Vocacional</option>
                  <option value="Colegio Privado Mixto">Colegio Privado Mixto</option>
                </select>
              </div>
              <div class="col-md-3 ms-auto text-md-end">
                <button type="submit" class="btn-registrar mt-2" id="btnGuardar">
                  Registrar
                </button>
              </div>
            </form>
          </div>

          <!-- Tabla -->
          <div class="px-3 pb-3">
            <div class="table-responsive">
              <table class="table table-sm table-bordered mb-0" id="tblAlumnos">
                <thead>
                  <tr>
                    <th style="width:60px">ID</th>
                    <th>Código</th>
                    <th>Instituto</th>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Grado</th>
                    <th style="width:90px">Modificar</th>
                    <th style="width:90px">Eliminar</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>

          <!-- Pie decorativo como en maqueta -->
          <div style="height:10px; background:#2e655f; border-bottom-left-radius:.5rem; border-bottom-right-radius:.5rem;"></div>
        </div>

        <div id="contentArea"></div>
      </div>
    </section>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    (() => {
      const frm = document.getElementById('frmAlumno');
      const tbody = document.querySelector('#tblAlumnos tbody');
      const btnGuardar = document.getElementById('btnGuardar');

      let editIndex = null;   // null = creando, número = editando
      let sec = 1;            // ID incremental simple

      const getForm = () => ({
        codigo:   document.getElementById('codigo').value.trim(),
        nombres:  document.getElementById('nombres').value.trim(),
        apellidos:document.getElementById('apellidos').value.trim(),
        grado:    document.getElementById('grado').value,
        instituto:document.getElementById('instituto').value
      });

      const setForm = (o) => {
        document.getElementById('codigo').value = o.codigo || '';
        document.getElementById('nombres').value = o.nombres || '';
        document.getElementById('apellidos').value = o.apellidos || '';
        
        if (o.grado) {
          const gradoSelect = document.getElementById('grado');
          const option = Array.from(gradoSelect.options).find(opt => opt.value === o.grado);
          if (option) option.selected = true;
        }
        
        if (o.instituto) {
          const institutoSelect = document.getElementById('instituto');
          const option = Array.from(institutoSelect.options).find(opt => opt.value === o.instituto);
          if (option) option.selected = true;
        }
      };

      const clearForm = () => setForm({});

      const rowHTML = (id, d) => `
        <tr data-id="${id}">
          <td class="text-center">${id}</td>
          <td>${d.codigo}</td>
          <td>${d.instituto}</td>
          <td>${d.nombres}</td>
          <td>${d.apellidos}</td>
          <td>${d.grado}</td>
          <td class="text-center">
            <button class="btn btn-sm btn-outline-primary btn-edit"><i class="bi bi-pencil-square"></i></button>
          </td>
          <td class="text-center">
            <button class="btn btn-sm btn-outline-danger btn-del"><i class="bi bi-trash"></i></button>
          </td>
        </tr>`;

      frm.addEventListener('submit', (e) => {
        e.preventDefault();
        const d = getForm();
        if (!d.codigo || !d.nombres || !d.apellidos || !d.grado || !d.instituto) return;

        if (editIndex === null) {
          // Crear
          tbody.insertAdjacentHTML('beforeend', rowHTML(sec++, d));
        } else {
          // Actualizar
          const tr = tbody.querySelector(`tr[data-id="${editIndex}"]`);
          tr.outerHTML = rowHTML(editIndex, d);
          editIndex = null;
          btnGuardar.textContent = 'Registrar';
        }
        clearForm();
      });

      // Delegación para Editar/Eliminar
      tbody.addEventListener('click', (e) => {
        const btn = e.target.closest('button');
        if (!btn) return;

        const tr = btn.closest('tr');
        const id = Number(tr.dataset.id);

        if (btn.classList.contains('btn-del')) {
          tr.remove();
          if (editIndex === id) { editIndex = null; btnGuardar.textContent = 'Registrar'; clearForm(); }
          return;
        }
        if (btn.classList.contains('btn-edit')) {
          const tds = tr.querySelectorAll('td');
          setForm({
            codigo: tds[1].textContent,
            instituto: tds[2].textContent,
            nombres: tds[3].textContent,
            apellidos: tds[4].textContent,
            grado: tds[5].textContent
          });
          editIndex = id;
          btnGuardar.textContent = 'Actualizar';
          window.scrollTo({ top: 0, behavior: 'smooth' });
        }
      });

      // Demo: fila inicial
      tbody.insertAdjacentHTML('beforeend', rowHTML(sec++, {
        codigo:'A-001', 
        instituto:'Instituto Nacional de Educación Básica', 
        nombres:'María', 
        apellidos:'Gómez', 
        grado:'3ro Básico'
      }));
    })();
  </script>
</body>
</html>
