<?php
/**
 * Validación de Autenticación para Vistas
 * Este archivo debe ser incluido al inicio de cada vista para validar la sesión del usuario
 */

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Función para validar autenticación
function validarAutenticacion() {
    // Verificar si el usuario está autenticado
    if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
        // Usuario no autenticado, redirigir al login
        echo "<script>
            alert('Debe iniciar sesión para acceder a esta página');
            window.location='?c=Login'; 
        </script>";
        exit();
    }
    
    // Verificar si la sesión no ha expirado (2 horas por defecto)
    $tiempoExpiracion = 7200; // 2 horas en segundos
    if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > $tiempoExpiracion) {
        // Sesión expirada, destruir sesión y redirigir
        session_destroy();
        echo "<script>
            alert('Su sesión ha expirado. Por favor, inicie sesión nuevamente.');
            window.location='?c=Login'; 
        </script>";
        exit();
    }
    
    // Renovar el tiempo de sesión
    $_SESSION['login_time'] = time();
}

// Función para validar rol específico
function validarRol($rolesPermitidos) {
    // Primero validar autenticación
    validarAutenticacion();

    // Normalizar comparación de roles a mayúsculas para evitar problemas de casing
    $rolSesion = isset($_SESSION['user_rol']) ? strtoupper($_SESSION['user_rol']) : null;
    $permitidos = array_map('strtoupper', (array)$rolesPermitidos);

    if (!$rolSesion || !in_array($rolSesion, $permitidos, true)) {
        echo "<script>
            alert('No tiene permisos para acceder a esta página');
            window.location='?c=Menu'; 
        </script>";
        exit();
    }
}

// Función para validar si es administrador (ID = 1)
function validarAdmin() {
    validarAutenticacion();
    
    if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
        echo "<script>
            alert('Acceso restringido. Solo los administradores pueden acceder a esta página.');
            window.location='?c=Menu'; 
        </script>";
        exit();
    }
}

// Función para obtener información del usuario actual
function obtenerUsuarioActual() {
    if (isset($_SESSION['user_id'])) {
        return [
            'id' => $_SESSION['user_id'],
            'codigo' => isset($_SESSION['user_codigo']) ? $_SESSION['user_codigo'] : '',
            'nombres' => isset($_SESSION['user_nombres']) ? $_SESSION['user_nombres'] : '',
            'apellidos' => isset($_SESSION['user_apellidos']) ? $_SESSION['user_apellidos'] : '',
            'rol' => isset($_SESSION['user_rol']) ? $_SESSION['user_rol'] : '',
            'nombre_completo' => isset($_SESSION['user_nombre_completo']) ? $_SESSION['user_nombre_completo'] : '',
            'institucion_id' => isset($_SESSION['user_institucion_id']) ? $_SESSION['user_institucion_id'] : null,
            'institucion_nombre' => isset($_SESSION['user_institucion_nombre']) ? $_SESSION['user_institucion_nombre'] : '',
            'grado_id' => isset($_SESSION['user_grado_id']) ? $_SESSION['user_grado_id'] : null,
            'grado_nombre' => isset($_SESSION['user_grado_nombre']) ? $_SESSION['user_grado_nombre'] : '',
            'seccion' => isset($_SESSION['user_seccion']) ? $_SESSION['user_seccion'] : ''
        ];
    }
    return null;
}

// Función para verificar si el usuario es administrador
function esAdmin() {
    return isset($_SESSION['user_id']) && $_SESSION['user_id'] == 1;
}

// Función para verificar si el usuario tiene un rol específico
function tieneRol($rol) {
    return isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === $rol;
}

// Función para verificar si el usuario tiene alguno de los roles especificados
function tieneAlgunRol($roles) {
    return isset($_SESSION['user_rol']) && in_array($_SESSION['user_rol'], $roles);
}

// Función para cerrar sesión
function cerrarSesion() {
    session_start();
    session_destroy();
    echo "<script>
        alert('Sesión cerrada exitosamente');
        window.location='?c=Login'; 
    </script>";
    exit();
}

// Función para mostrar información del usuario en el header
function mostrarInfoUsuario() {
    $usuario = obtenerUsuarioActual();
    if ($usuario) {
        echo '<div class="user-info">';
        echo '<small class="text-light">Usuario: ' . htmlspecialchars($usuario['nombre_completo']) . '</small><br>';
        echo '<small class="text-light opacity-75">Rol: ' . htmlspecialchars($usuario['rol']) . '</small>';
        echo '</div>';
    }
}

// Función para mostrar botón de logout
function mostrarBotonLogout() {
    echo '<a href="?c=Login&a=Logout" class="btn btn-outline-light btn-sm" title="Cerrar Sesión">';
    echo '<i class="bi bi-box-arrow-right"></i> cerrar sesion';
    echo '</a>';
}

// Validación automática al incluir este archivo
// Comentar la siguiente línea si no quieres validación automática
// validarAutenticacion();
?>
