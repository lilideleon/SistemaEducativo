<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet">
    <style>
        .filter-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .btn-filter {
            background-color: #50938a;
            color: white;
        }
        .btn-filter:hover {
            background-color: #3a6b64;
            color: white;
        }
        .btn-export {
            background-color: #6c757d;
            color: white;
        }
        .btn-export:hover {
            background-color: #5a6268;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <h2 class="mb-4">Reporte de Usuarios</h2>
        
        <!-- Filtros -->
        <div class="filter-card p-3 mb-4">
            <form id="filterForm" method="POST" action="?c=Reportes&a=filtrarUsuarios">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Rol</label>
                        <select name="rol" class="form-select">
                            <option value="">Todos</option>
                            <option value="ADMIN">Administrador</option>
                            <option value="DIRECTOR">Director</option>
                            <option value="DOCENTE">Docente</option>
                            <option value="ALUMNO">Alumno</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Institución</label>
                        <select name="institucion_id" class="form-select">
                            <option value="">Todas</option>
                            <?php if (!empty($instituciones)): ?>
                                <?php foreach($instituciones as $inst): ?>
                                    <option value="<?= $inst->id ?>"><?= htmlspecialchars($inst->nombre) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Grado</label>
                        <select name="grado_id" class="form-select">
                            <option value="">Todos</option>
                            <?php if (!empty($grados)): ?>
                                <?php foreach($grados as $grado): ?>
                                    <option value="<?= $grado->id ?>"><?= htmlspecialchars($grado->nombre) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Estado</label>
                        <select name="activo" class="form-select">
                            <option value="">Todos</option>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Fecha Creación</label>
                        <input type="text" name="fecha_rango" class="form-control" id="fecha_rango">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Búsqueda</label>
                        <input type="text" name="busqueda" class="form-control" placeholder="Nombre, apellido o código">
                    </div>
                    <div class="col-md-8 d-flex align-items-end">
                        <button type="submit" class="btn btn-filter me-2">
                            <i class="bi bi-search"></i> Filtrar
                        </button>
                        <button type="button" class="btn btn-export me-2" onclick="exportarPDF()">
                            <i class="bi bi-file-pdf"></i> Exportar PDF
                        </button>
                        <button type="button" class="btn btn-export" onclick="exportarExcel()">
                            <i class="bi bi-file-excel"></i> Exportar Excel
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabla de Resultados -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Código</th>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>Rol</th>
                        <th>Institución</th>
                        <th>Grado</th>
                        <th>Sección</th>
                        <th>Estado</th>
                        <th>Fecha Creación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($usuarios)): ?>
                        <?php foreach($usuarios as $usuario): ?>
                        <tr>
                        <td><?= htmlspecialchars($usuario->codigo) ?></td>
                        <td><?= htmlspecialchars($usuario->nombres) ?></td>
                        <td><?= htmlspecialchars($usuario->apellidos) ?></td>
                        <td><?= htmlspecialchars($usuario->rol) ?></td>
                        <td><?= htmlspecialchars(isset($usuario->institucion_nombre) ? $usuario->institucion_nombre : '-') ?></td>
                        <td><?= htmlspecialchars(isset($usuario->grado_nombre) ? $usuario->grado_nombre : '-') ?></td>
                        <td><?= $usuario->seccion ? $usuario->seccion : '-' ?></td>
                        <td>
                            <span class="badge <?= $usuario->activo ? 'bg-success' : 'bg-danger' ?>">
                                <?= $usuario->activo ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td><?= !empty($usuario->creado_en) ? date('d/m/Y', strtotime($usuario->creado_en)) : '-' ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center">No se encontraron usuarios</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inicializar el selector de rango de fechas
            $('#fecha_rango').daterangepicker({
                locale: {
                    format: 'DD/MM/YYYY',
                    applyLabel: 'Aplicar',
                    cancelLabel: 'Cancelar',
                    fromLabel: 'Desde',
                    toLabel: 'Hasta',
                    customRangeLabel: 'Rango personalizado',
                    daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
                    monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
                    ]
                }
            });
        });

        function exportarPDF() {
            const form = document.getElementById('filterForm');
            const formData = new FormData(form);
            formData.append('formato', 'pdf');
            
            window.location.href = `?c=Reportes&a=exportarUsuarios&${new URLSearchParams(formData)}`;
        }

        function exportarExcel() {
            const form = document.getElementById('filterForm');
            const formData = new FormData(form);
            formData.append('formato', 'excel');
            
            window.location.href = `?c=Reportes&a=exportarUsuarios&${new URLSearchParams(formData)}`;
        }
    </script>
</body>
</html>