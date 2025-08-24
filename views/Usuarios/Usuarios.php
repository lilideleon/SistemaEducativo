<?php 
   include 'views/Menu/Aside.php';
?>



<!DOCTYPE html>
<html lang="es">
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
    <button class="btn btn-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar">
      <i class="bi bi-list"></i> Menú
    </button>
  </div>

  <main class="app-wrapper">
    <!-- AQUÍ "se inyecta" el sidebar reutilizable -->
    <div data-include="sidebar.html"></div>

    <!-- Tu contenido propio -->
    <section class="col-12 main-content">
      <header class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="page-title">Sistema Educativo</h1>
        <i class="bi bi-arrow-right-square-fill fs-2 text-light d-none d-md-inline"></i>
      </header>

      <div class="content-panel">
        <div class="form-block mb-3">
          <div class="form-title">Registro de Alumnos</div>
          <div class="p-3">
            <form id="frmAlumno" class="row g-3 align-items-end">
              <input type="hidden" id="idUsuario" />
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
                <select class="form-select form-select-sm" id="seccion">
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
                <label for="rol" class="form-label">Rol</label>
                <select class="form-select form-select-sm" id="rol" required>
                  <option value="" selected disabled>Seleccione un rol</option>
                  <option value="Alumno">Alumno</option>
                  <option value="Director">Director</option>
                  <option value="Secretario">Secretario</option>
                  <option value="Profesor">Profesor</option>
                </select>
              </div>
              <div class="col-md-3">
                <label for="password" class="form-label">Contraseña</label>
                <div class="input-group input-group-sm">
                  <input type="password" class="form-control form-control-sm" id="password" required>
                  <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
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
                    <th>Rol</th>
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
        
        <!-- Modal Confirmación Eliminar -->
        <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteLabel">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                ¿Está seguro que desea eliminar el usuario <strong>ID <span id="deleteUserIdSpan"></span></strong>?
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmDelete">Eliminar</button>
              </div>
            </div>
          </div>
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
    document.getElementById('togglePassword').addEventListener('click', function() {
      const passwordInput = document.getElementById('password');
      const icon = this.querySelector('i');
      
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
      } else {
        passwordInput.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
      }
    });
    (() => {
      const frm = document.getElementById('frmAlumno');
      const tbody = document.querySelector('#tblAlumnos tbody');
      const btnGuardar = document.getElementById('btnGuardar');

      // Intentar primero la URL corta y si falla, usar index.php como fallback
      const baseCandidates = ['?c=Usuarios', 'index.php?c=Usuarios'];
      let currentBase = baseCandidates[0];
      const makeUrl = (a) => `${currentBase}&a=${a}`;
      let urlAgregar = makeUrl('Agregar');
      let urlActualizar = makeUrl('Actualizar');
      let urlEliminar = makeUrl('Eliminar');
      let urlListarInstituciones = makeUrl('ListarInstituciones');
      let urlListarGrados = makeUrl('ListarGrados');
      let urlTabla = makeUrl('Tabla');
      let urlListarSecciones = makeUrl('ListarSecciones');
      let urlObtener = makeUrl('Obtener');

      const switchToFallbackBase = () => {
        if (currentBase === baseCandidates[0]) {
          console.warn('Cambiando a base URL fallback:', baseCandidates[1]);
          currentBase = baseCandidates[1];
          urlAgregar = makeUrl('Agregar');
          urlActualizar = makeUrl('Actualizar');
          urlEliminar = makeUrl('Eliminar');
          urlListarInstituciones = makeUrl('ListarInstituciones');
          urlListarGrados = makeUrl('ListarGrados');
          urlListarSecciones = makeUrl('ListarSecciones');
          urlObtener = makeUrl('Obtener');
        }
      };

      // Exponer función global para abrir modal de confirmación
      window.EliminarDatos = function(id) {
        deleteId = id;
        document.getElementById('deleteUserIdSpan').textContent = String(id);
        const confirmModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
        confirmModal.show();
      }

      const rolMap = {
        'Alumno': 'ALUMNO',
        'Director': 'DIRECTOR',
        'Profesor': 'DOCENTE'
      };

      const splitTwo = (str) => {
        const parts = (str || '').trim().split(/\s+/);
        const first = parts.shift() || '';
        return [first, parts.join(' ')];
      };

      let editId = null;
      let deleteId = null;

      const clearForm = () => {
        frm.reset();
        document.getElementById('grado').selectedIndex = 0;
        document.getElementById('instituto').selectedIndex = 0;
        document.getElementById('rol').selectedIndex = 0;
        const seccion = document.getElementById('seccion');
        if (seccion) seccion.value = '';
        document.getElementById('idUsuario').value = '';
        editId = null;
        btnGuardar.textContent = 'Registrar';
      };

      // Cargar catálogos desde el backend
      const cargarInstituciones = () => {
        const $sel = $('#instituto');
        $sel.prop('disabled', true)
            .empty()
            .append('<option value="" selected disabled>Cargando instituciones...</option>');
        console.time('AJAX instituciones');
        console.log('Solicitando instituciones a:', urlListarInstituciones);
        $.ajax({ url: urlListarInstituciones, dataType: 'json' })
          .done((resp, textStatus, jqXHR) => {
            console.timeEnd('AJAX instituciones');
            console.log('Respuesta instituciones status=', jqXHR.status, 'payload=', resp);
            $sel.empty().append('<option value="" selected disabled>Seleccione un instituto</option>');
            if (resp && resp.success && Array.isArray(resp.data)) {
              resp.data.forEach(it => {
                $sel.append($('<option>', { value: it.id, text: it.nombre }));
              });
              $sel.prop('disabled', false);
            } else {
              $sel.append('<option value="" disabled>Error: respuesta inválida</option>');
            }
          })
          .fail((jqXHR, textStatus, errorThrown) => {
            console.timeEnd('AJAX instituciones');
            console.error('Error instituciones:', textStatus, errorThrown, 'status=', jqXHR.status, 'resp=', jqXHR.responseText);
            // Intentar con base fallback una sola vez
            if (jqXHR.status === 404 || jqXHR.status === 0) {
              switchToFallbackBase();
              const retryUrl = urlListarInstituciones; // ya actualizada
              console.log('Reintentando instituciones con:', retryUrl);
              $.ajax({ url: retryUrl, dataType: 'json' })
                .done((resp2, _ts2, jqXHR2) => {
                  console.log('Respuesta instituciones (retry) status=', jqXHR2.status, 'payload=', resp2);
                  $sel.empty().append('<option value="" selected disabled>Seleccione un instituto</option>');
                  if (resp2 && resp2.success && Array.isArray(resp2.data)) {
                    resp2.data.forEach(it => $sel.append($('<option>', { value: it.id, text: it.nombre })));
                    $sel.prop('disabled', false);
                  } else {
                    $sel.append('<option value="" disabled>Error: respuesta inválida</option>');
                  }
                })
                .fail((jqXHR2, ts2, err2) => {
                  console.error('Retry instituciones falló:', ts2, err2, 'status=', jqXHR2.status, 'resp=', jqXHR2.responseText);
                  $sel.empty().append('<option value="" disabled>Error al cargar instituciones</option>');
                });
            } else {
              $sel.empty().append('<option value="" disabled>Error al cargar instituciones</option>');
            }
          });
      };

      const cargarSecciones = () => {
        const $sel = $('#seccion');
        $sel.prop('disabled', true)
            .empty()
            .append('<option value="" selected disabled>Cargando secciones...</option>');
        console.time('AJAX secciones');
        console.log('Solicitando secciones a:', urlListarSecciones);
        $.ajax({ url: urlListarSecciones, dataType: 'json' })
          .done((resp, textStatus, jqXHR) => {
            console.timeEnd('AJAX secciones');
            console.log('Respuesta secciones status=', jqXHR.status, 'payload=', resp);
            $sel.empty().append('<option value="" selected disabled>Seleccione una sección</option>');
            if (resp && resp.success && Array.isArray(resp.data)) {
              resp.data.forEach(it => {
                $sel.append($('<option>', { value: it.id, text: it.nombre }));
              });
              $sel.prop('disabled', false);
            } else {
              $sel.append('<option value="" disabled>Error: respuesta inválida</option>');
            }
          })
          .fail((jqXHR, textStatus, errorThrown) => {
            console.timeEnd('AJAX secciones');
            console.error('Error secciones:', textStatus, errorThrown, 'status=', jqXHR.status, 'resp=', jqXHR.responseText);
            if (jqXHR.status === 404 || jqXHR.status === 0) {
              switchToFallbackBase();
              const retryUrl = urlListarSecciones;
              console.log('Reintentando secciones con:', retryUrl);
              $.ajax({ url: retryUrl, dataType: 'json' })
                .done((resp2, _ts2, jqXHR2) => {
                  console.log('Respuesta secciones (retry) status=', jqXHR2.status, 'payload=', resp2);
                  $sel.empty().append('<option value="" selected disabled>Seleccione una sección</option>');
                  if (resp2 && resp2.success && Array.isArray(resp2.data)) {
                    resp2.data.forEach(it => $sel.append($('<option>', { value: it.id, text: it.nombre })));
                    $sel.prop('disabled', false);
                  } else {
                    $sel.append('<option value="" disabled>Error: respuesta inválida</option>');
                  }
                })
                .fail((jqXHR2, ts2, err2) => {
                  console.error('Retry secciones falló:', ts2, err2, 'status=', jqXHR2.status, 'resp=', jqXHR2.responseText);
                  $sel.empty().append('<option value="" disabled>Error al cargar secciones</option>');
                });
            } else {
              $sel.empty().append('<option value="" disabled>Error al cargar secciones</option>');
            }
          });
      };

      const cargarGrados = () => {
        const $sel = $('#grado');
        $sel.prop('disabled', true)
            .empty()
            .append('<option value="" selected disabled>Cargando grados...</option>');
        console.time('AJAX grados');
        console.log('Solicitando grados a:', urlListarGrados);
        $.ajax({ url: urlListarGrados, dataType: 'json' })
          .done((resp, textStatus, jqXHR) => {
            console.timeEnd('AJAX grados');
            console.log('Respuesta grados status=', jqXHR.status, 'payload=', resp);
            $sel.empty().append('<option value="" selected disabled>Seleccione un grado</option>');
            if (resp && resp.success && Array.isArray(resp.data)) {
              resp.data.forEach(it => {
                $sel.append($('<option>', { value: it.id, text: it.nombre }));
              });
              $sel.prop('disabled', false);
            } else {
              $sel.append('<option value="" disabled>Error: respuesta inválida</option>');
            }
          })
          .fail((jqXHR, textStatus, errorThrown) => {
            console.timeEnd('AJAX grados');
            console.error('Error grados:', textStatus, errorThrown, 'status=', jqXHR.status, 'resp=', jqXHR.responseText);
            // Intentar con base fallback una sola vez
            if (jqXHR.status === 404 || jqXHR.status === 0) {
              switchToFallbackBase();
              const retryUrl = urlListarGrados; // ya actualizada
              console.log('Reintentando grados con:', retryUrl);
              $.ajax({ url: retryUrl, dataType: 'json' })
                .done((resp2, _ts2, jqXHR2) => {
                  console.log('Respuesta grados (retry) status=', jqXHR2.status, 'payload=', resp2);
                  $sel.empty().append('<option value="" selected disabled>Seleccione un grado</option>');
                  if (resp2 && resp2.success && Array.isArray(resp2.data)) {
                    resp2.data.forEach(it => $sel.append($('<option>', { value: it.id, text: it.nombre })));
                    $sel.prop('disabled', false);
                  } else {
                    $sel.append('<option value="" disabled>Error: respuesta inválida</option>');
                  }
                })
                .fail((jqXHR2, ts2, err2) => {
                  console.error('Retry grados falló:', ts2, err2, 'status=', jqXHR2.status, 'resp=', jqXHR2.responseText);
                  $sel.empty().append('<option value="" disabled>Error al cargar grados</option>');
                });
            } else {
              $sel.empty().append('<option value="" disabled>Error al cargar grados</option>');
            }
          });
      };

      // Inicializar catálogos al cargar
      let dt = null;
      $(document).ready(() => {
        console.log('Init Usuarios: base candidates=', baseCandidates, 'currentBase=', currentBase);
        cargarInstituciones();
        cargarGrados();
        cargarSecciones();

        // Inicializar DataTable con server-side hacia el endpoint Tabla
        dt = $('#tblAlumnos').DataTable({
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
          ajax: {
            url: urlTabla,
            type: 'GET',
            dataSrc: 'aaData',
            data: function (d) {
              // Mapear parámetros modernos a legacy esperados por el backend
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
              } else {
                legacy.iSortingCols = 0;
              }
              // columnas ordenables
              if (Array.isArray(d.columns)) {
                d.columns.forEach((c, idx) => {
                  legacy['bSortable_' + idx] = c.orderable ? 'true' : 'false';
                });
              }
              console.log('DT request (legacy mapped):', legacy);
              return legacy;
            },
            error: function (xhr) {
              console.error('DT AJAX error:', xhr.status, xhr.responseText);
              // Fallback de base si 404/0
              if (xhr.status === 404 || xhr.status === 0) {
                switchToFallbackBase();
                this.url = urlTabla; // actualizar URL
                console.warn('DT reintentando con base fallback:', this.url);
                $('#tblAlumnos').DataTable().ajax.reload();
              }
            }
          },
          // Definir columnas: 7 datos + 2 acciones
          columns: [
            { title: 'ID', orderable: true },
            { title: 'Código', orderable: true },
            { title: 'Instituto', orderable: true },
            { title: 'Nombres', orderable: true },
            { title: 'Apellidos', orderable: true },
            { title: 'Grado', orderable: true },
            { title: 'Rol', orderable: true },
            { title: 'Editar', orderable: false, searchable: false },
            { title: 'Eliminar', orderable: false, searchable: false }
          ],
          order: [[0, 'asc']]
        });

        // Confirmar eliminación
        const modalEl = document.getElementById('confirmDeleteModal');
        const btnConfirmDelete = document.getElementById('btnConfirmDelete');
        const confirmModal = new bootstrap.Modal(modalEl);
        btnConfirmDelete.addEventListener('click', function() {
          if (!deleteId) return;
          $.ajax({
            url: urlEliminar,
            method: 'POST',
            dataType: 'json',
            data: { IdUsuario: deleteId }
          }).done((resp) => {
            if (resp && resp.success) {
              if (dt) dt.ajax.reload(null, false);
              clearForm();
            }
            confirmModal.hide();
            alert(resp && resp.msj ? resp.msj.replace(/<[^>]+>/g, '') : (resp && resp.success ? 'Eliminado' : 'No se pudo eliminar'));
          }).fail((jq, ts) => {
            confirmModal.hide();
            console.error('Eliminar error:', ts, jq.responseText);
            alert('Error de comunicación al eliminar');
          }).always(() => {
            deleteId = null;
          });
        });
      });

      // Helper: esperar a que un select tenga una opción y luego asignar su valor
      const setSelectValueWhenLoaded = (sel, value, maxRetries = 20) => {
        let attempts = 0;
        const trySet = () => {
          const $sel = $(sel);
          if ($sel.find(`option[value="${value}"]`).length > 0 || value === '' || value === null) {
            $sel.val(value || '');
          } else if (attempts < maxRetries) {
            attempts++;
            setTimeout(trySet, 150);
          }
        };
        trySet();
      };

      // Exponer función global usada por el botón Editar en la tabla
      window.DatosUsuario = function(id) {
        console.log('Cargando datos de usuario', id, 'desde:', urlObtener);
        $.ajax({ url: urlObtener, dataType: 'json', method: 'GET', data: { id } })
          .done((resp) => {
            if (!resp || !resp.success || !resp.data) {
              alert('No se pudo obtener el usuario');
              return;
            }
            const u = resp.data;
            // Poblar campos
            $('#idUsuario').val(u.id);
            $('#codigo').val(u.codigo);
            $('#nombres').val(u.nombres);
            $('#apellidos').val(u.apellidos);

            // Rol: backend usa ADMIN/DIRECTOR/DOCENTE/ALUMNO, mapear al texto del select
            const rolTxtMap = { 'ALUMNO': 'Alumno', 'DIRECTOR': 'Director', 'DOCENTE': 'Profesor', 'ADMIN': 'Admin' };
            const rolText = rolTxtMap[u.rol] || '';
            if (rolText) {
              $('#rol').val(rolText);
            }

            // Setear selects (esperar a que hayan cargado)
            setSelectValueWhenLoaded('#grado', u.grado_id);
            setSelectValueWhenLoaded('#instituto', u.institucion_id);
            setSelectValueWhenLoaded('#seccion', u.seccion);

            // Activar modo edición
            editId = u.id;
            btnGuardar.textContent = 'Actualizar';
          })
          .fail((jq, ts, err) => {
            console.error('Error Obtener usuario:', ts, err, jq.responseText);
            alert('Error al cargar el usuario');
          });
      };

      const rowHTML = (id, d) => `
        <tr data-id="${id}">
          <td class="text-center">${id}</td>
          <td>${d.Codigo}</td>
          <td>${d.Instituto || ''}</td>
          <td>${d.Nombres}</td>
          <td>${d.Apellidos}</td>
          <td>${d.Grado || ''}</td>
          <td>${d.Rol}</td>
          <td class="text-center">
            <button class="btn btn-sm btn-outline-primary btn-edit" disabled><i class="bi bi-pencil-square"></i></button>
          </td>
          <td class="text-center">
            <button class="btn btn-sm btn-outline-danger btn-del" disabled><i class="bi bi-trash"></i></button>
          </td>
        </tr>`;

      frm.addEventListener('submit', (e) => {
        e.preventDefault();
        const codigo = document.getElementById('codigo').value.trim();
        const nombresStr = document.getElementById('nombres').value.trim();
        const apellidosStr = document.getElementById('apellidos').value.trim();
        const grado = document.getElementById('grado').value || '';
        const gradoTexto = $('#grado option:selected').text() || '';
        const instituto = document.getElementById('instituto').value || '';
        const institutoTexto = $('#instituto option:selected').text() || '';
        const rolTxt = document.getElementById('rol').value;
        const password = document.getElementById('password').value;
        const seccionVal = (document.getElementById('seccion').value || '').trim();

        if (!codigo || !nombresStr || !apellidosStr || !rolTxt || !password) {
          alert('Complete los campos requeridos.');
          return;
        }

        const [PrimerNombre, SegundoNombre] = splitTwo(nombresStr);
        const [PrimerApellido, SegundoApellido] = splitTwo(apellidosStr);
        const Rol = rolMap[rolTxt];
        if (!Rol) {
          alert('Rol no válido. Use Alumno, Director o Profesor.');
          return;
        }

        // Validar sección si es alumno
        let seccionSafe = '';
        if (Rol === 'ALUMNO') {
          if (!/^\d+$/.test(seccionVal)) {
            alert('Ingrese una sección numérica válida para alumnos.');
            return;
          }
          seccionSafe = seccionVal;
        }

        // Asegurar IDs numéricos; si no son numéricos, enviar vacío para que sea NULL en el servidor
        const gradoIdSafe = /^\d+$/.test(grado) ? grado : '';
        const institucionIdSafe = /^\d+$/.test(instituto) ? instituto : '';

        const payload = {
          Codigo: codigo,
          PrimerNombre,
          SegundoNombre,
          PrimerApellido,
          SegundoApellido,
          Rol,
          'Contraseña': password,
          GradoId: Rol === 'ALUMNO' ? gradoIdSafe : '',
          InstitucionId: Rol === 'ALUMNO' ? institucionIdSafe : '',
          Seccion: Rol === 'ALUMNO' ? seccionSafe : ''
        };

        const isEdit = !!editId;
        const submitUrl = isEdit ? urlActualizar : urlAgregar;
        if (isEdit) {
          payload.IdUsuario = editId;
        }

        $.ajax({
          url: submitUrl,
          method: 'POST',
          data: payload,
          dataType: 'json'
        }).done((data) => {
          if (data && data.success) {
            // Recargar DataTable para reflejar el alta
            if (dt) {
              dt.ajax.reload(null, false);
            }
            clearForm();
          }
          alert(data && data.msj ? data.msj.replace(/<[^>]+>/g, '') : (data && data.success ? 'Operación exitosa' : 'Ocurrió un error'));
        }).fail((jqXHR, textStatus) => {
          console.error(textStatus, jqXHR.responseText);
          alert('Error de comunicación con el servidor');
        });
      });
    })();
  </script>
</body>
</html>
