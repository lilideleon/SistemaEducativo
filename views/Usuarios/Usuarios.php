<!-- Vista de Usuarios -->
<?php require 'views/Content/header.php'; ?>

<?php 
if ($_SESSION['TipoUsuario'] == '') {
    print "<script>window.location='?c=Login';</script>";
} else if ($_SESSION['TipoUsuario'] == 1) {
    require 'views/Content/sidebar.php';
} else if ($_SESSION['TipoUsuario'] == 3) {
    require 'views/Content/sidebar2.php';
} else if ($_SESSION['TipoUsuario'] == 4) {
    require 'views/Content/sidebar3.php';
}
?>

<div class="content-wrapper" style="padding: 20px;">
    <div class="row">
        <!-- Formulario principal -->
        <div class="col-12">
            <div class="card mb-4">
                <center>
                    <h1 class="card-header">Formulario de usuarios</h1>
                </center>
                <div class="card-body">
                    <small class="text fw-semibold">En este módulo puede dar de alta usuarios que utilizan el sistema</small>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exLargeModal">Nuevo</button>
                    </div>
                </div>

                <!-- Tabla de usuarios -->
                <div class="card">
                    <h5 class="card-header">Listado de usuarios</h5>
                    <div class="table-responsive text-nowrap">
                        <table class="table" id="Tabla_Usuarios">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Dpi</th>
                                    <th>Primer Nombre</th>
                                    <th>Primer Apellido</th>
                                    <th>Usuario</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
                                    <th>Accion</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                <!-- Los datos se cargarán dinámicamente desde el archivo JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODALS -->

<!-- Modal para agregar usuarios -->
<div class="modal fade" id="exLargeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" style="max-width: 70%;">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Formulario para ingreso de datos de usuarios</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2">
                    <div class="col mb-0">
                        <label for="Dpi" class="form-label">DPI</label>
                        <input type="text" id="Dpi" class="form-control" placeholder="EJ: 12345678" />
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col mb-0">
                        <label for="PrimerNombre" class="form-label">Primer Nombre</label>
                        <input type="text" id="PrimerNombre" class="form-control" placeholder="EJ: Juan" />
                    </div>
                    <div class="col mb-0">
                        <label for="SegundoNombre" class="form-label">Segundo Nombre</label>
                        <input type="text" id="SegundoNombre" class="form-control" placeholder="Ej: Mario" />
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col mb-0">
                        <label for="PrimerApellido" class="form-label">Primer Apellido</label>
                        <input type="text" id="PrimerApellido" class="form-control" placeholder="EJ: Pérez" />
                    </div>
                    <div class="col mb-0">
                        <label for="SegundoApellido" class="form-label">Segundo Apellido</label>
                        <input type="text" id="SegundoApellido" class="form-control" placeholder="Ej: López" />
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col mb-0">
                        <label for="Correo" class="form-label">Correo</label>
                        <input type="email" id="Correo" class="form-control" placeholder="juan@gmail.com" />
                    </div>
                    <div class="col mb-0">
                        <label for="Perfil" class="form-label">Rol</label>
                        <select class="form-control" id="Perfil">
                            <option value="1">Administrador</option>
                            <option value="2">Vendedor</option>
                        </select>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col mb-0">
                        <label for="Usuario" class="form-label">Usuario</label>
                        <input type="text" id="Usuario" class="form-control" placeholder="EJ: juan123" />
                    </div>
                    <div class="col mb-0">
                        <label for="Contraseña" class="form-label">Contraseña</label>
                        <input type="password" id="Contraseña" class="form-control" placeholder="Ej: 123456" />
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col mb-0">
                        <label for="foto" class="form-label">Foto</label>
                        <input type="file" class="form-control" id="foto">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="GuardarDatos()">Registrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para actualizar usuarios -->
<div class="modal fade" id="EditUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="max-width: 70%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Formulario de actualización de datos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2">
                    <div class="col mb-0">
                        <label for="Codigo" class="form-label">Código</label>
                        <input type="text" id="Codigo" class="form-control" readonly />
                    </div>
                    <div class="col mb-0">
                        <label for="EDpi" class="form-label">DPI</label>
                        <input type="text" id="EDpi" class="form-control" />
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col mb-0">
                        <label for="EPrimerNombre" class="form-label">Primer Nombre</label>
                        <input type="text" id="EPrimerNombre" class="form-control" />
                    </div>
                    <div class="col mb-0">
                        <label for="ESegundoNombre" class="form-label">Segundo Nombre</label>
                        <input type="text" id="ESegundoNombre" class="form-control" />
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col mb-0">
                        <label for="EPrimerApellido" class="form-label">Primer Apellido</label>
                        <input type="text" id="EPrimerApellido" class="form-control" />
                    </div>
                    <div class="col mb-0">
                        <label for="ESegundoApellido" class="form-label">Segundo Apellido</label>
                        <input type="text" id="ESegundoApellido" class="form-control" />
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col mb-0">
                        <label for="ECorreo" class="form-label">Correo</label>
                        <input type="email" id="ECorreo" class="form-control" />
                    </div>
                    <div class="col mb-0">
                        <label for="EPerfil" class="form-label">Rol</label>
                        <select class="form-control" id="EPerfil">
                            <option value="1">Administrador</option>
                            <option value="2">Vendedor</option>
                        </select>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col mb-0">
                        <label for="EUsuario" class="form-label">Usuario</label>
                        <input type="text" id="EUsuario" class="form-control" />
                    </div>
                    <div class="col mb-0">
                        <label for="EContraseña" class="form-label">Contraseña</label>
                        <input type="password" id="EContraseña" class="form-control" />
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col mb-0">
                        <label for="Efoto" class="form-label">Foto</label>
                        <input type="file" class="form-control" id="Efoto">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="ActualizarUsuario()">Actualizar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para eliminar usuarios -->
<div class="modal fade" id="DeleteUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Desactivar usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2">
                    <div class="col mb-0">
                        <label for="ECodigo" class="form-label">Código</label>
                        <input type="text" id="ECodigo" class="form-control" readonly />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="EliminarDatos()">Desactivar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para confirmar eliminación -->
<div class="modal fade" id="DeleteUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar este usuario?</p>
                <input type="hidden" id="DeleteUserId" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="ConfirmarEliminar()">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="js/Custom/Usuarios.js"></script>

<?php require 'views/Content/footer.php'; ?>