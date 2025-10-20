<?php 
   require_once 'core/AuthValidation.php';
   validarRol(['DIRECTOR','ADMIN']);
   include 'views/Menu/Aside.php';
?>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Usuarios</title>
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

    /* contenido */
    section.main-content{margin-left:260px;width:calc(100% - 260px);padding:2rem;min-height:100vh;display:flex;flex-direction:column}
    .content-panel{flex:1;background:#fff;border-radius:.5rem;padding:1.5rem;box-shadow:0 .125rem .25rem rgba(0,0,0,.075)}
    .form-title{font-weight:700;margin-bottom:.75rem}

    /* móvil: sin sidebar fijo */
    @media (max-width:767.98px){
      section.main-content{margin-left:0 !important;width:100% !important;padding:1rem !important;min-height:calc(100vh - 56px)}
    }

    /* Si NO quieres sidebar en alguna página, añade class="no-sidebar" al body */
    body.no-sidebar section.main-content{margin-left:0;width:100%}

    /* ===== Enhancements scoped to this page ===== */
    #frmAlumno .form-control, #frmAlumno .form-select{
      border-radius: .5rem;
      border-color: rgba(0,0,0,.15);
      transition: box-shadow .15s ease, border-color .15s ease;
    }
    #frmAlumno .form-control:focus, #frmAlumno .form-select:focus{
      border-color: #0f766e;
      box-shadow: 0 0 0 .2rem rgba(15,118,110,.15);
    }
    #frmAlumno .input-group .btn{ border-radius: .5rem; }
    .btn-registrar{
      border: 0; border-radius: .6rem; padding: .5rem 1rem; font-weight: 700; color: #fff;
      background: linear-gradient(135deg,#117867,#15a085);
      box-shadow: 0 8px 18px rgba(17,120,103,.25);
      transition: transform .12s ease, box-shadow .12s ease;
    }
    .btn-registrar:hover{ transform: translateY(-1px); box-shadow: 0 10px 22px rgba(17,120,103,.3) }

    #tblAlumnos{ border-color: rgba(0,0,0,.08); }
    #tblAlumnos thead th{
      background: linear-gradient(180deg,#0f1c2e,#1f3554);
      color: #fff; border-color: #14253f;
    }
    #tblAlumnos tbody tr:hover{ background: #f5faf9 }
    #tblAlumnos.table > :not(caption) > * > *{ vertical-align: middle }
    #tblAlumnos td:nth-child(7){ font-weight: 600; color: #0b4f44; background: rgba(17,120,103,.06) }
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
    <div data-include="sidebar.html"></div>

    <section class="col-12 main-content">
      <header class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="page-title">Sistema Educativo</h1>
        <a href="?c=Login&a=Logout" class="btn btn-outline-light btn-sm" title="Cerrar Sesión">
          <i class="bi bi-box-arrow-right"></i> cerrar sesion
        </a>
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
                </select>
              </div>
              <div class="col-md-3">
                <label for="seccion" class="form-label">Sección</label>
                <select class="form-select form-select-sm" id="seccion" required>
                  <option value="" selected disabled>Seleccione una sección</option>
                </select>
              </div>
              <div class="col-md-3">
                <label for="instituto" class="form-label">Instituto</label>
                <select class="form-select form-select-sm" id="instituto" required>
                  <option value="" selected disabled>Seleccione un instituto</option>
                </select>
              </div>
              <div class="col-md-3">
                <label for="password" class="form-label">Contraseña</label>
                <div class="input-group input-group-sm">
                  <input type="password" class="form-control form-control-sm" id="password" placeholder="Requerida al crear">
                  <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
              </div>
              <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                <button type="button" class="btn btn-secondary btn-sm" id="btnNuevo">
                  <i class="bi bi-file-earmark-plus"></i> Nuevo
                </button>
                <button type="button" class="btn btn-primary btn-sm btn-registrar" id="btnGuardar">
                  <i class="bi bi-save"></i> Guardar
                </button>
                <button type="button" class="btn btn-success btn-sm" id="btnActualizar">
                  <i class="bi bi-arrow-repeat"></i> Actualizar
                </button>
              </div>
            </form>
          </div>

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
                    <th>Rol</th>
                    <th style="width:90px">Modificar</th>
                    <th style="width:90px">Eliminar</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>

          <div style="height:10px; background:#2e655f; border-bottom-left-radius:.5rem; border-bottom-right-radius:.5rem;"></div>
        </div>
      </div>
    </section>
  </main>

  <script src="js2/vendor/jquery-2.2.4.min.js"></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
  <script>
    // Toggle password visibility
    document.addEventListener('click', function(e){
      if(e.target.closest('#togglePassword')){
        const input = document.getElementById('password');
        const icon = e.target.closest('#togglePassword').querySelector('i');
        if(input.type === 'password'){ input.type = 'text'; icon.classList.replace('bi-eye','bi-eye-slash'); }
        else { input.type = 'password'; icon.classList.replace('bi-eye-slash','bi-eye'); }
      }
    });

    (function(){
      const frm = document.getElementById('frmAlumno');
      const btnGuardar = document.getElementById('btnGuardar');
      const btnActualizar = document.getElementById('btnActualizar');
      const btnNuevo = document.getElementById('btnNuevo');
      let editId = null;

      // URLs con fallback como en vista original
      const baseCandidates = ['?c=Usuarios', 'index.php?c=Usuarios'];
      let currentBase = baseCandidates[0];
      const makeUrl = (a) => `${currentBase}&a=${a}`;
      let urlAgregar = makeUrl('Agregar');
      let urlActualizar = makeUrl('Actualizar');
      let urlEliminar = makeUrl('Eliminar');
      let urlTabla = makeUrl('Tabla');
      let urlObtener = makeUrl('Obtener');
      let urlListarInstituciones = makeUrl('ListarInstituciones');
      let urlListarGrados = makeUrl('ListarGrados');
      let urlListarSecciones = makeUrl('ListarSecciones');

      const switchToFallbackBase = () => {
        if (currentBase === baseCandidates[0]) {
          currentBase = baseCandidates[1];
          urlAgregar = makeUrl('Agregar');
          urlActualizar = makeUrl('Actualizar');
          urlEliminar = makeUrl('Eliminar');
          urlTabla = makeUrl('Tabla');
          urlObtener = makeUrl('Obtener');
          urlListarInstituciones = makeUrl('ListarInstituciones');
          urlListarGrados = makeUrl('ListarGrados');
          urlListarSecciones = makeUrl('ListarSecciones');
        }
      };

      const setMode = (mode) => {
        if (mode === 'nuevo') {
          btnGuardar.disabled = false;  // activo
          btnActualizar.disabled = true; // bloqueado
          btnNuevo.disabled = true;      // bloqueado (siguiendo patrón actual)
        } else {
          btnGuardar.disabled = true;
          btnActualizar.disabled = false;
          btnNuevo.disabled = true;
        }
      };

      const clearForm = () => {
        frm.reset();
        ['grado','instituto','seccion'].forEach(id=>{ const el = document.getElementById(id); if(el) el.selectedIndex = 0; });
        editId = null;
      };

      const cargarSelect = (url, $sel, placeholder) => {
        $sel.prop('disabled', true).empty().append(`<option value="" selected disabled>${placeholder}</option>`);
        $.ajax({ url, dataType: 'json' })
          .done((resp, _ts, jq) => {
            $sel.empty().append(`<option value="" selected disabled>${placeholder.replace('Cargando','Seleccione')}</option>`);
            if (resp && resp.success && Array.isArray(resp.data)) {
              resp.data.forEach(it => $sel.append($('<option>', { value: it.id, text: it.nombre })));
              $sel.prop('disabled', false);
            }
          })
          .fail((jqXHR) => {
            if (jqXHR.status === 404 || jqXHR.status === 0) {
              switchToFallbackBase();
              cargarSelect(url.replace(baseCandidates[0], baseCandidates[1]), $sel, placeholder);
            } else {
              $sel.empty().append('<option value="" disabled>Error al cargar</option>');
            }
          });
      };

      $(document).ready(() => {
        cargarSelect(urlListarInstituciones, $('#instituto'), 'Cargando instituciones...');
        cargarSelect(urlListarGrados, $('#grado'), 'Cargando grados...');
        cargarSelect(urlListarSecciones, $('#seccion'), 'Cargando secciones...');
        setMode('nuevo');

        $('#tblAlumnos').DataTable({
          processing: true,
          serverSide: true,
          searching: true,
          lengthChange: true,
          dom: 'Bfrtip',
          buttons: [
            { extend: 'copy', text: 'Copiar' },
            { extend: 'csv', text: 'CSV' },
            { extend: 'excel', text: 'Excel' },
            { extend: 'pdf', text: 'PDF' },
            { extend: 'print', text: 'Imprimir' }
          ],
          language: {
            decimal: ',',
            thousands: '.',
            processing: 'Procesando...',
            search: 'Buscar:',
            lengthMenu: 'Mostrar _MENU_ registros',
            info: 'Mostrando de _START_ a _END_ de _TOTAL_ registros',
            infoEmpty: 'Mostrando 0 a 0 de 0 registros',
            infoFiltered: '(filtrado de _MAX_ registros totales)',
            loadingRecords: 'Cargando...',
            zeroRecords: 'No se encontraron resultados',
            emptyTable: 'Ningún dato disponible en esta tabla',
            paginate: { first: 'Primero', previous: 'Anterior', next: 'Siguiente', last: 'Último' },
            aria: { sortAscending: ': activar para ordenar la columna de manera ascendente', sortDescending: ': activar para ordenar la columna de manera descendente' },
            buttons: { copy: 'Copiar', csv: 'CSV', excel: 'Excel', pdf: 'PDF', print: 'Imprimir', colvis: 'Visibilidad' }
          },
          ajax: {
            url: urlTabla,
            type: 'GET',
            dataSrc: 'aaData',
            data: function (d) {
              const legacy = {
                sEcho: d.draw,
                iDisplayStart: d.start,
                iDisplayLength: d.length,
                sSearch: d.search && d.search.value ? d.search.value : ''
              };
              if (Array.isArray(d.order) && d.order.length > 0) {
                legacy.iSortingCols = d.order.length;
                d.order.forEach((ord, idx) => {
                  legacy['iSortCol_' + idx] = ord.column;
                  legacy['sSortDir_' + idx] = ord.dir;
                });
              } else { legacy.iSortingCols = 0; }
              if (Array.isArray(d.columns)) {
                d.columns.forEach((c, idx) => { legacy['bSortable_' + idx] = c.orderable ? 'true' : 'false'; });
              }
              return legacy;
            },
            error: function (xhr) {
              if (xhr.status === 404 || xhr.status === 0) {
                switchToFallbackBase();
                this.url = urlTabla;
                $('#tblAlumnos').DataTable().ajax.reload();
              }
            }
          },
          order: [[0, 'asc']]
        });
      });

      const submitUsuario = (isEdit) => {
        const codigo = $('#codigo').val().trim();
        const nombres = $('#nombres').val().trim();
        const apellidos = $('#apellidos').val().trim();
        const grado = $('#grado').val()||'';
        const institucion = $('#instituto').val()||'';
        const seccion = $('#seccion').val()||'';
        const password = $('#password').val();
        if(!codigo || !nombres || !apellidos || !grado || !institucion || !seccion || (!isEdit && !password)){
          alert('Complete los campos obligatorios');
          return;
        }
        const payload = {
          Codigo: codigo,
          nombres, apellidos,
          Rol: 'ALUMNO',
          GradoId: grado,
          InstitucionId: institucion,
          Seccion: seccion
        };
        if(!isEdit){ payload['Contraseña'] = password; } else { payload['IdUsuario'] = editId; }

        $.ajax({ url: isEdit ? urlActualizar : urlAgregar, method:'POST', dataType:'json', data: payload })
          .done((resp)=>{
            if(resp && resp.success){
              $('#tblAlumnos').DataTable().ajax.reload(null,false);
              clearForm();
              setMode('nuevo');
            }
            alert(resp && resp.msj ? resp.msj.replace(/<[^>]+>/g, '') : (resp && resp.success ? 'Operación exitosa' : 'Ocurrió un error'));
          })
          .fail(()=> alert('Error de comunicación'));
      };

      btnNuevo.addEventListener('click', ()=>{ clearForm(); setMode('nuevo'); });
      btnGuardar.addEventListener('click', ()=>{ if(btnGuardar.disabled) return; editId = null; submitUsuario(false); });
      btnActualizar.addEventListener('click', ()=>{ if(btnActualizar.disabled) return; if(!editId){ alert('Seleccione un registro para actualizar'); return; } submitUsuario(true); });

      // Cargar usuario para edición
      window.DatosUsuario = function(id){
        $.ajax({url: urlObtener, dataType:'json', data:{id}})
          .done((r)=>{
            if(!r || !r.success || !r.data){ alert('No se pudo obtener el usuario'); return; }
            const u = r.data;
            $('#codigo').val(u.codigo);
            $('#nombres').val(u.nombres);
            $('#apellidos').val(u.apellidos);
            $('#grado').val(u.grado_id || '');
            $('#instituto').val(u.institucion_id || '');
            $('#seccion').val(u.seccion || '');
            editId = u.id;
            setMode('edicion');
          })
          .fail(()=> alert('Error al cargar usuario'));
      }

      window.EliminarDatos = function(id){
        if(!confirm('¿Eliminar este alumno?')) return;
        $.ajax({url: urlEliminar, method:'POST', dataType:'json', data:{IdUsuario: id}})
          .done((r)=>{
            if(r && r.success){ $('#tblAlumnos').DataTable().ajax.reload(null,false); }
            alert(r && r.msj ? r.msj.replace(/<[^>]+>/g, '') : (r && r.success ? 'Eliminado' : 'No se pudo eliminar'));
          })
          .fail(()=> alert('Error al eliminar'));
      }
    })();
  </script>
</body>
</html>
