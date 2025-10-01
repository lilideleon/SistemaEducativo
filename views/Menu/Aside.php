<!-- Estilos del menú responsivo mejorado -->
<style>
:root {
    --sidebar-width: 190px;
    --sidebar-collapsed-width: 70px;
    --sidebar-bg: linear-gradient(180deg, #117867 0%, #0d5d52 100%);
    --sidebar-hover: rgba(255, 255, 255, 0.1);
    --sidebar-active: rgba(255, 255, 255, 0.2);
    --transition-speed: 0.3s;
}

/* Botón hamburguesa mejorado */
.sidebar-toggle {
    position: fixed;
    top: 0.75rem;
    left: 0.75rem;
    z-index: 1050;
    width: 45px;
    height: 45px;
    border: none;
    border-radius: 10px;
    background: linear-gradient(135deg, #117867 0%, #15a085 100%);
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(17, 120, 103, 0.3);
    transition: all var(--transition-speed) ease;
}

.sidebar-toggle:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 20px rgba(17, 120, 103, 0.4);
}

.sidebar-toggle i {
    font-size: 1.3rem;
    transition: transform var(--transition-speed) ease;
}

.sidebar-toggle:hover i {
    transform: rotate(90deg);
}

/* Sidebar mejorado */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    width: var(--sidebar-width);
    background: var(--sidebar-bg);
    box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
    transition: transform var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 1040;
    overflow-y: auto;
    overflow-x: hidden;
}

.sidebar::-webkit-scrollbar {
    width: 6px;
}

.sidebar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
}

.sidebar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 3px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.3);
}

/* Estado colapsado */
.sidebar.collapsed {
    transform: translateX(calc(-100% + var(--sidebar-collapsed-width)));
}

.sidebar.collapsed .sidebar-header,
.sidebar.collapsed .sidebar-text {
    opacity: 0;
    pointer-events: none;
}

.sidebar.collapsed .btn-glow {
    justify-content: center;
    padding: 1rem;
}

.sidebar.collapsed .btn-glow i {
    margin: 0 !important;
}

/* Header del sidebar */
.sidebar-header {
    padding: 4rem 1rem 1rem 1rem;
    background: rgba(0, 0, 0, 0.1);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    transition: opacity var(--transition-speed) ease;
}

.sidebar-header small {
    display: block;
    color: rgba(255, 255, 255, 0.9);
    font-weight: 500;
    font-size: 0.8rem;
    margin-bottom: 0.25rem;
    line-height: 1.3;
}

.sidebar-header .opacity-75 {
    opacity: 0.7 !important;
}

/* Contenedor de la sidebar box */
.sidebar-box {
    padding: 0;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.sidebar-box .d-grid {
    padding: 1rem 0.85rem;
    flex: 0 0 auto;
    display: flex;
    flex-direction: column;
}

/* Espaciado mejorado para botones */
.d-grid.gap-3 {
    gap: 0.6rem !important;
}

/* Botones mejorados */
.btn-glow {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.15);
    color: white !important;
    padding: 0.6rem 0.85rem;
    border-radius: 8px;
    transition: all 0.3s ease;
    font-weight: 500;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    white-space: nowrap;
    overflow: hidden;
    height: 44px;
    max-height: 44px;
    flex: 0 0 auto;
}

.btn-glow:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateX(5px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.btn-glow i {
    font-size: 1rem;
    transition: transform 0.3s ease;
    min-width: 1rem;
}

.btn-glow:hover i {
    transform: scale(1.15);
}

.sidebar-text {
    transition: opacity var(--transition-speed) ease;
}

/* Contenido principal ajustado */
.main-content {
    margin-left: var(--sidebar-width);
    transition: margin-left var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
    min-height: 100vh;
}

.main-content.expanded {
    margin-left: var(--sidebar-collapsed-width);
}

/* Backdrop para móvil */
.sidebar-backdrop {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1030;
    opacity: 0;
    transition: opacity var(--transition-speed) ease;
}

.sidebar-backdrop.show {
    display: block;
    opacity: 1;
}

/* Responsive */
@media (max-width: 767.98px) {
    .sidebar {
        transform: translateX(-100%);
    }
    
    .sidebar.show {
        transform: translateX(0);
    }
    
    .sidebar.collapsed {
        transform: translateX(-100%);
    }
    
    .main-content,
    .main-content.expanded {
        margin-left: 0 !important;
    }
    
    .sidebar-toggle {
        background: linear-gradient(135deg, #117867 0%, #15a085 100%);
    }
}

@media (min-width: 768px) {
    .sidebar-backdrop {
        display: none !important;
    }
}

/* Animaciones */
@keyframes slideIn {
    from {
        transform: translateX(-20px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.btn-glow {
    animation: slideIn 0.3s ease-out backwards;
}

.btn-glow:nth-child(1) { animation-delay: 0.05s; }
.btn-glow:nth-child(2) { animation-delay: 0.1s; }
.btn-glow:nth-child(3) { animation-delay: 0.15s; }
.btn-glow:nth-child(4) { animation-delay: 0.2s; }
.btn-glow:nth-child(5) { animation-delay: 0.25s; }
.btn-glow:nth-child(6) { animation-delay: 0.3s; }
.btn-glow:nth-child(7) { animation-delay: 0.35s; }
.btn-glow:nth-child(8) { animation-delay: 0.4s; }
</style>

<!-- Botón hamburguesa universal -->
<button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
    <i class="bi bi-list"></i>
</button>

<!-- Backdrop para móvil -->
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<?php
    // Incluir el middleware de autenticación
    require_once "core/AuthMiddleware.php";
    
    // Obtener información del usuario actual
    $usuario = AuthMiddleware::getCurrentUser();
    $rol = $usuario && isset($usuario['rol']) ? strtoupper($usuario['rol']) : '';
    
    // Verificar si el usuario está autenticado y tiene ID 1 (administrador)
    $esAdmin = ($usuario && $usuario['id'] == 1);
?>

<!-- Sidebar unificado (desktop + mobile) -->
<aside class="sidebar" id="sidebar">
  <div class="sidebar-box">
    <?php if ($usuario): ?>
      <div class="sidebar-header">
        <small class="d-block text-light">
          <i class="bi bi-person-circle me-2"></i>
          <span class="sidebar-text"><?php echo htmlspecialchars(isset($usuario['nombres']) ? $usuario['nombres'] . ' ' . $usuario['apellidos'] : 'Usuario'); ?></span>
        </small>
        <small class="d-block text-light opacity-75">
          <i class="bi bi-shield-check me-2"></i>
          <span class="sidebar-text"><?php echo htmlspecialchars(isset($usuario['rol']) ? $usuario['rol'] : 'N/A'); ?></span>
        </small>
      </div>
    <?php else: ?>
      <div class="sidebar-header">
        <small class="d-block text-light">
          <i class="bi bi-grid-3x3-gap me-2"></i>
          <span class="sidebar-text">Sistema Educativo</span>
        </small>
      </div>
    <?php endif; ?>
    
    <div class="d-grid gap-3">
      <?php if ($rol === 'ADMIN'): ?>
        <a href="?c=Menu" class="btn btn-glow text-decoration-none">
          <i class="bi bi-grid-fill me-2"></i>
          <span class="sidebar-text">Menú</span>
        </a>
        <a href="?c=Usuarios" class="btn btn-glow text-decoration-none">
          <i class="bi bi-people-fill me-2"></i>
          <span class="sidebar-text">Usuarios</span>
        </a>
        <a href="?c=Evaluacion" class="btn btn-glow text-decoration-none">
          <i class="bi bi-clipboard-check-fill me-2"></i>
          <span class="sidebar-text">Evaluación</span>
        </a>
        <a href="?c=Encuestas" class="btn btn-glow text-decoration-none">
          <i class="bi bi-ui-checks-grid me-2"></i>
          <span class="sidebar-text">Encuestas</span>
        </a>
        <a href="?c=Reportes" class="btn btn-glow text-decoration-none">
          <i class="bi bi-bar-chart-fill me-2"></i>
          <span class="sidebar-text">Reportes</span>
        </a>
        <a href="?c=Material" class="btn btn-glow text-decoration-none">
          <i class="bi bi-folder2-open me-2"></i>
          <span class="sidebar-text">Material</span>
        </a>
        <a href="?c=Contenido" class="btn btn-glow text-decoration-none">
          <i class="bi bi-book me-2"></i>
          <span class="sidebar-text">Contenido</span>
        </a>
        <a href="?c=Preguntas" class="btn btn-glow text-decoration-none">
          <i class="bi bi-question-circle me-2"></i>
          <span class="sidebar-text">Preguntas</span>
        </a>
      <?php elseif ($rol === 'ALUMNO'): ?>
        <a href="?c=Evaluacion" class="btn btn-glow text-decoration-none">
          <i class="bi bi-clipboard-check-fill me-2"></i>
          <span class="sidebar-text">Evaluación</span>
        </a>
        <a href="?c=MaterialAlumno" class="btn btn-glow text-decoration-none">
          <i class="bi bi-folder2-open me-2"></i>
          <span class="sidebar-text">Material</span>
        </a>
        <a href="?c=Perfil" class="btn btn-glow text-decoration-none">
          <i class="bi bi-person-circle me-2"></i>
          <span class="sidebar-text">Perfil</span>
        </a>
      <?php elseif ($rol === 'DIRECTOR'): ?>
        <a href="?c=Menu" class="btn btn-glow text-decoration-none">
          <i class="bi bi-grid-fill me-2"></i>
          <span class="sidebar-text">Menú</span>
        </a>
        <a href="?c=Usuarios" class="btn btn-glow text-decoration-none">
          <i class="bi bi-people-fill me-2"></i>
          <span class="sidebar-text">Usuarios</span>
        </a>
        <a href="?c=Reportes" class="btn btn-glow text-decoration-none">
          <i class="bi bi-bar-chart-fill me-2"></i>
          <span class="sidebar-text">Reportes</span>
        </a>
        <a href="?c=Material" class="btn btn-glow text-decoration-none">
          <i class="bi bi-folder2-open me-2"></i>
          <span class="sidebar-text">Material</span>
        </a>
      <?php elseif ($rol === 'DOCENTE'): ?>
        <a href="?c=Material" class="btn btn-glow text-decoration-none">
          <i class="bi bi-folder2-open me-2"></i>
          <span class="sidebar-text">Material</span>
        </a>
        <a href="?c=Reportes" class="btn btn-glow text-decoration-none">
          <i class="bi bi-bar-chart-fill me-2"></i>
          <span class="sidebar-text">Reportes</span>
        </a>
      <?php endif; ?>
    </div>
  </div>
</aside>

<!-- JavaScript para controlar el sidebar -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');
    const mainContent = document.querySelector('.main-content');
    
    // Verificar si existe el contenido principal
    if (!mainContent) {
        console.warn('No se encontró .main-content');
    }
    
    // Función para determinar si estamos en móvil
    function isMobile() {
        return window.innerWidth < 768;
    }
    
    // Cargar estado guardado (solo para desktop)
    if (!isMobile()) {
        const savedState = localStorage.getItem('sidebarCollapsed');
        if (savedState === 'true') {
            sidebar.classList.add('collapsed');
            if (mainContent) mainContent.classList.add('expanded');
        }
    }
    
    // Toggle del sidebar
    sidebarToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        
        if (isMobile()) {
            // Comportamiento móvil
            sidebar.classList.toggle('show');
            sidebarBackdrop.classList.toggle('show');
            document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
        } else {
            // Comportamiento desktop
            sidebar.classList.toggle('collapsed');
            if (mainContent) {
                mainContent.classList.toggle('expanded');
            }
            
            // Guardar estado
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        }
    });
    
    // Cerrar sidebar al hacer clic en el backdrop (móvil)
    sidebarBackdrop.addEventListener('click', function() {
        sidebar.classList.remove('show');
        sidebarBackdrop.classList.remove('show');
        document.body.style.overflow = '';
    });
    
    // Cerrar sidebar al hacer clic en un enlace (móvil)
    if (isMobile()) {
        const sidebarLinks = sidebar.querySelectorAll('a');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function() {
                sidebar.classList.remove('show');
                sidebarBackdrop.classList.remove('show');
                document.body.style.overflow = '';
            });
        });
    }
    
    // Ajustar en cambio de tamaño de ventana
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if (isMobile()) {
                // Modo móvil: cerrar sidebar y quitar clases de desktop
                sidebar.classList.remove('collapsed', 'show');
                if (mainContent) mainContent.classList.remove('expanded');
                sidebarBackdrop.classList.remove('show');
                document.body.style.overflow = '';
            } else {
                // Modo desktop: restaurar estado guardado
                sidebarBackdrop.classList.remove('show');
                document.body.style.overflow = '';
                
                const savedState = localStorage.getItem('sidebarCollapsed');
                if (savedState === 'true') {
                    sidebar.classList.add('collapsed');
                    if (mainContent) mainContent.classList.add('expanded');
                } else {
                    sidebar.classList.remove('collapsed');
                    if (mainContent) mainContent.classList.remove('expanded');
                }
            }
        }, 250);
    });
});
</script>
