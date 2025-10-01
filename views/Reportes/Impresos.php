<?php 
   // Validación de autenticación y permisos
   require_once 'core/AuthValidation.php';
   validarRol(['ADMIN', 'DOCENTE', 'DIRECTOR']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Reportes Impresos - Sistema Educativo</title>

  <!-- Bootstrap 5 + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />

  <style>
    :root {
      --primary-gradient: linear-gradient(135deg, #117867 0%, #0d5d52 100%);
      --success-gradient: linear-gradient(135deg, #15a085 0%, #0d7f6f 100%);
      --info-gradient: linear-gradient(135deg, #1abc9c 0%, #16a085 100%);
      --warning-gradient: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
      --dark-gradient: linear-gradient(135deg, #0b4f44 0%, #073832 100%);
      --purple-gradient: linear-gradient(135deg, #48c9b0 0%, #1abc9c 100%);
      
      --shadow-sm: 0 2px 4px rgba(0,0,0,0.08);
      --shadow-md: 0 4px 12px rgba(0,0,0,0.1);
      --shadow-lg: 0 8px 24px rgba(0,0,0,0.12);
      --shadow-hover: 0 12px 32px rgba(0,0,0,0.15);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      min-height: 100vh;
      padding: 0;
      margin: 0;
    }

    /* Header moderno */
    .header-section {
      background: linear-gradient(135deg, #117867 0%, #0d5d52 100%);
      padding: 2rem 0;
      box-shadow: var(--shadow-md);
      position: relative;
      overflow: hidden;
    }

    .header-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
      opacity: 0.5;
    }

    .header-content {
      position: relative;
      z-index: 1;
    }

    .back-btn {
      background: rgba(255, 255, 255, 0.2);
      border: 2px solid rgba(255, 255, 255, 0.3);
      color: white;
      padding: 0.75rem 1.5rem;
      border-radius: 50px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      font-weight: 600;
      transition: all 0.3s ease;
      backdrop-filter: blur(10px);
    }

    .back-btn:hover {
      background: rgba(255, 255, 255, 0.3);
      transform: translateX(-5px);
      color: white;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    .page-title {
      color: white;
      font-size: 2.5rem;
      font-weight: 800;
      margin: 1rem 0;
      text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .page-subtitle {
      color: rgba(255, 255, 255, 0.9);
      font-size: 1.1rem;
      font-weight: 400;
    }

    /* Contenedor principal */
    .main-container {
      max-width: 1400px;
      margin: -3rem auto 2rem;
      padding: 0 1.5rem;
      position: relative;
      z-index: 10;
    }

    /* Cards de reportes */
    .reports-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 1.5rem;
      margin-top: 2rem;
    }

    .report-card {
      background: white;
      border-radius: 20px;
      padding: 2rem;
      box-shadow: var(--shadow-sm);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
      border: 1px solid rgba(0,0,0,0.05);
    }

    .report-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 5px;
      background: var(--card-gradient, var(--primary-gradient));
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .report-card:hover {
      transform: translateY(-8px);
      box-shadow: var(--shadow-hover);
      border-color: transparent;
    }

    .report-card:hover::before {
      opacity: 1;
    }

    .report-icon {
      width: 70px;
      height: 70px;
      border-radius: 20px;
      background: var(--card-gradient, var(--primary-gradient));
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 1.5rem;
      box-shadow: var(--shadow-md);
      transition: all 0.3s ease;
    }

    .report-card:hover .report-icon {
      transform: scale(1.1) rotate(5deg);
    }

    .report-icon i {
      font-size: 2rem;
      color: white;
    }

    .report-title {
      font-size: 1.4rem;
      font-weight: 700;
      color: #2d3748;
      margin-bottom: 0.75rem;
    }

    .report-description {
      color: #718096;
      font-size: 0.95rem;
      line-height: 1.6;
      margin-bottom: 1.5rem;
    }

    .report-meta {
      display: flex;
      gap: 1rem;
      margin-bottom: 1.5rem;
      flex-wrap: wrap;
    }

    .meta-item {
      display: flex;
      align-items: center;
      gap: 0.4rem;
      color: #a0aec0;
      font-size: 0.85rem;
    }

    .meta-item i {
      font-size: 1rem;
    }

    .report-actions {
      display: flex;
      gap: 0.75rem;
    }

    .btn-generate {
      flex: 1;
      padding: 0.875rem 1.5rem;
      border: none;
      border-radius: 12px;
      font-weight: 600;
      font-size: 0.95rem;
      cursor: pointer;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      background: var(--card-gradient, var(--primary-gradient));
      color: white;
      text-decoration: none;
      box-shadow: var(--shadow-sm);
    }

    .btn-generate:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-md);
      color: white;
    }

    .btn-preview {
      padding: 0.875rem;
      border: 2px solid #e2e8f0;
      border-radius: 12px;
      background: white;
      color: #718096;
      cursor: pointer;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .btn-preview:hover {
      background: #f7fafc;
      border-color: #cbd5e0;
      color: #2d3748;
    }

    /* Sección de filtros rápidos */
    .filters-section {
      background: white;
      border-radius: 20px;
      padding: 2rem;
      box-shadow: var(--shadow-sm);
      margin-bottom: 2rem;
    }

    .filters-title {
      font-size: 1.2rem;
      font-weight: 700;
      color: #2d3748;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .filters-title i {
      color: #117867;
    }

    .filters-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
    }

    .filter-item {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }

    .filter-label {
      font-size: 0.9rem;
      font-weight: 600;
      color: #4a5568;
    }

    .filter-select {
      padding: 0.875rem 1rem;
      border: 2px solid #e2e8f0;
      border-radius: 12px;
      background: white;
      color: #2d3748;
      font-size: 0.95rem;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .filter-select:focus {
      outline: none;
      border-color: #117867;
      box-shadow: 0 0 0 3px rgba(17, 120, 103, 0.1);
    }

    .btn-apply-filters {
      padding: 0.875rem 2rem;
      background: linear-gradient(135deg, #117867 0%, #0d5d52 100%);
      color: white;
      border: none;
      border-radius: 12px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: var(--shadow-sm);
    }

    .btn-apply-filters:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-md);
    }

    /* Stats cards */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .stat-card {
      background: white;
      border-radius: 16px;
      padding: 1.5rem;
      box-shadow: var(--shadow-sm);
      display: flex;
      align-items: center;
      gap: 1rem;
      transition: all 0.3s ease;
    }

    .stat-card:hover {
      transform: translateY(-4px);
      box-shadow: var(--shadow-md);
    }

    .stat-icon {
      width: 50px;
      height: 50px;
      border-radius: 12px;
      background: var(--icon-gradient, var(--primary-gradient));
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .stat-icon i {
      font-size: 1.5rem;
      color: white;
    }

    .stat-info {
      flex: 1;
    }

    .stat-label {
      font-size: 0.85rem;
      color: #718096;
      margin-bottom: 0.25rem;
    }

    .stat-value {
      font-size: 1.75rem;
      font-weight: 800;
      color: #2d3748;
      line-height: 1;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .page-title {
        font-size: 1.75rem;
      }

      .reports-grid {
        grid-template-columns: 1fr;
      }

      .filters-grid {
        grid-template-columns: 1fr;
      }

      .main-container {
        margin-top: -2rem;
      }
    }

    /* Animaciones */
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .report-card {
      animation: fadeInUp 0.6s ease-out;
    }

    .report-card:nth-child(1) { animation-delay: 0.1s; }
    .report-card:nth-child(2) { animation-delay: 0.2s; }
    .report-card:nth-child(3) { animation-delay: 0.3s; }
    .report-card:nth-child(4) { animation-delay: 0.4s; }
    .report-card:nth-child(5) { animation-delay: 0.5s; }
    .report-card:nth-child(6) { animation-delay: 0.6s; }

    /* Tags de categoría */
    .category-tag {
      display: inline-block;
      padding: 0.35rem 0.875rem;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .tag-academic {
      background: rgba(17, 120, 103, 0.1);
      color: #117867;
    }

    .tag-administrative {
      background: rgba(21, 160, 133, 0.1);
      color: #15a085;
    }

    .tag-statistical {
      background: rgba(26, 188, 156, 0.1);
      color: #1abc9c;
    }
  </style>
</head>
<body>
  <!-- Header -->
  <div class="header-section">
    <div class="container header-content">
      <a href="?c=Reportes" class="back-btn">
        <i class="bi bi-arrow-left"></i>
        Volver al Menú
      </a>
      <h1 class="page-title">
        <i class="bi bi-file-earmark-text me-3"></i>Reportes Impresos
      </h1>
      
    </div>
  </div>

  <!-- Contenido Principal -->
  <div class="main-container">
    
    <!-- Stats Cards -->
    <div class="stats-grid">
      
    </div>

    <!-- Filtros Rápidos -->
    <div class="filters-section">
      <div class="filters-title">
        <i class="bi bi-funnel"></i>
        Filtros Rápidos
      </div>
      <div class="filters-grid">
        <div class="filter-item">
          <label class="filter-label">Período</label>
          <select class="filter-select">
            <option>Todos los períodos</option>
            <option>Último mes</option>
            <option>Últimos 3 meses</option>
            <option>Último semestre</option>
            <option>Último año</option>
            <option>Personalizado...</option>
          </select>
        </div>

        <div class="filter-item">
          <label class="filter-label">Institución</label>
          <select class="filter-select" id="filtroInstitucion">
            <option value="">Todas las instituciones</option>
            <?php
              // Usar instituciones pasadas desde el controlador
              if(isset($instituciones) && is_array($instituciones)) {
                foreach($instituciones as $inst) {
                  echo '<option value="' . $inst->id . '">' . htmlspecialchars($inst->nombre) . '</option>';
                }
              }
            ?>
          </select>
        </div>

        <div class="filter-item">
          <label class="filter-label">Formato</label>
          <select class="filter-select">
            <option>PDF (Recomendado)</option>
            <option>Excel (.xlsx)</option>
            <option>CSV</option>
          </select>
        </div>

        <div class="filter-item" style="display: flex; align-items: flex-end;">
          <button class="btn-apply-filters w-100">
            <i class="bi bi-check-circle me-2"></i>
            Aplicar Filtros
          </button>
        </div>
      </div>
    </div>

    <!-- Grid de Reportes -->
    <div class="reports-grid">
      
      <!-- Reporte 1: Usuarios -->
      <div class="report-card" style="--card-gradient: var(--primary-gradient);">
        <div class="report-icon">
          <i class="bi bi-people"></i>
        </div>
        <span class="category-tag tag-administrative">Administrativo</span>
        <h3 class="report-title mt-2">Reporte de Usuarios</h3>
        <p class="report-description">
          Listado completo de usuarios del sistema con roles, instituciones y estado de cuenta
        </p>
        <div class="report-meta">
          <span class="meta-item">
            <i class="bi bi-file-earmark"></i>
            PDF
          </span>
          <span class="meta-item">
            <i class="bi bi-clock"></i>
            2-3 páginas
          </span>
          <span class="meta-item">
            <i class="bi bi-graph-up"></i>
            Popular
          </span>
        </div>
        <div class="report-actions">
          <a href="?c=Reportes&a=generarPDFUsuarios" class="btn-generate" target="_blank">
            <i class="bi bi-download"></i>
            Generar Reporte
          </a>
          <button class="btn-preview" title="Vista previa">
            <i class="bi bi-eye"></i>
          </button>
        </div>
      </div>

      <!-- Reporte 2: Instituciones -->
      <div class="report-card" style="--card-gradient: var(--success-gradient);">
        <div class="report-icon">
          <i class="bi bi-building"></i>
        </div>
        <span class="category-tag tag-administrative">Administrativo</span>
        <h3 class="report-title mt-2">Reporte de Instituciones</h3>
        <p class="report-description">
          Información detallada de todas las instituciones educativas registradas en el sistema
        </p>
        <div class="report-meta">
          <span class="meta-item">
            <i class="bi bi-file-earmark"></i>
            PDF
          </span>
          <span class="meta-item">
            <i class="bi bi-clock"></i>
            1-2 páginas
          </span>
          <span class="meta-item">
            <i class="bi bi-check-circle"></i>
            Listo
          </span>
        </div>
        <div class="report-actions">
          <a href="?c=Reportes&a=generarPDFInstituciones" class="btn-generate" target="_blank">
            <i class="bi bi-download"></i>
            Generar Reporte
          </a>
          <button class="btn-preview" title="Vista previa">
            <i class="bi bi-eye"></i>
          </button>
        </div>
      </div>

      <!-- Reporte 3: Calificaciones -->
      <div class="report-card" style="--card-gradient: var(--info-gradient);">
        <div class="report-icon">
          <i class="bi bi-journal-text"></i>
        </div>
        <span class="category-tag tag-academic">Académico</span>
        <h3 class="report-title mt-2">Reporte de Calificaciones</h3>
        <p class="report-description">
          Concentrado de calificaciones por alumno, curso y período académico seleccionado
        </p>
        <div class="report-meta">
          <span class="meta-item">
            <i class="bi bi-file-earmark"></i>
            PDF/Excel
          </span>
          <span class="meta-item">
            <i class="bi bi-clock"></i>
            Variable
          </span>
          <span class="meta-item">
            <i class="bi bi-star"></i>
            Destacado
          </span>
        </div>
        <div class="report-actions">
          <a href="?c=Reportes&a=generarPDFCalificaciones" class="btn-generate" target="_blank">
            <i class="bi bi-download"></i>
            Generar Reporte
          </a>
          <button class="btn-preview" title="Vista previa">
            <i class="bi bi-eye"></i>
          </button>
        </div>
      </div>

      <!-- Reporte 4: Distritos -->
      <div class="report-card" style="--card-gradient: var(--dark-gradient);">
        <div class="report-icon">
          <i class="bi bi-geo-alt"></i>
        </div>
        <span class="category-tag tag-administrative">Administrativo</span>
        <h3 class="report-title mt-2">Reporte por Distrito</h3>
        <p class="report-description">
          Instituciones agrupadas por distrito con estadísticas de usuarios y rendimiento académico
        </p>
        <div class="report-meta">
          <span class="meta-item">
            <i class="bi bi-file-earmark"></i>
            PDF
          </span>
          <span class="meta-item">
            <i class="bi bi-clock"></i>
            2-4 páginas
          </span>
          <span class="meta-item">
            <i class="bi bi-star"></i>
            Nuevo
          </span>
        </div>
        <div class="report-actions">
          <a href="?c=Reportes&a=generarPDFDistritos" class="btn-generate" target="_blank">
            <i class="bi bi-download"></i>
            Generar Reporte
          </a>
          <button class="btn-preview" title="Vista previa">
            <i class="bi bi-eye"></i>
          </button>
        </div>
      </div>

      <!-- Reporte 5: Resumen Académico -->
      <div class="report-card" style="--card-gradient: var(--purple-gradient);">
        <div class="report-icon">
          <i class="bi bi-graph-up-arrow"></i>
        </div>
        <span class="category-tag tag-statistical">Estadístico</span>
        <h3 class="report-title mt-2">Resumen Académico</h3>
        <p class="report-description">
          Estadísticas completas por institución: alumnos, docentes, directores, calificaciones y encuestas
        </p>
        <div class="report-meta">
          <span class="meta-item">
            <i class="bi bi-file-earmark"></i>
            PDF
          </span>
          <span class="meta-item">
            <i class="bi bi-clock"></i>
            3-5 páginas
          </span>
          <span class="meta-item">
            <i class="bi bi-check-circle"></i>
            Destacado
          </span>
        </div>
        <div class="report-actions">
          <a href="?c=Reportes&a=generarPDFResumenAcademico" class="btn-generate" target="_blank">
            <i class="bi bi-download"></i>
            Generar Reporte
          </a>
          <button class="btn-preview" title="Vista previa">
            <i class="bi bi-eye"></i>
          </button>
        </div>
      </div>

    </div>

  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    // Animación suave al cargar
    document.addEventListener('DOMContentLoaded', function() {
      // Fade in de las cards
      const cards = document.querySelectorAll('.report-card');
      cards.forEach((card, index) => {
        card.style.opacity = '0';
        setTimeout(() => {
          card.style.transition = 'opacity 0.6s ease-out';
          card.style.opacity = '1';
        }, 100 * index);
      });

      // Handler para botones de generar
      const generateBtns = document.querySelectorAll('.btn-generate');
      generateBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
          const href = this.getAttribute('href');
          
          // Si el href es válido (no es # ni vacío), permitir la navegación
          if (href && href !== '#' && href.includes('?c=Reportes&a=generar')) {
            // Dejar que el navegador siga el enlace normalmente
            console.log('Generando reporte:', href);
            return true;
          }
          
          // Si es un botón placeholder (#), mostrar mensaje
          e.preventDefault();
          const card = this.closest('.report-card');
          const title = card.querySelector('.report-title').textContent;
          
          alert('Este reporte aún no está implementado.\n\nReporte: ' + title);
        });
      });

      // Handler para botones de vista previa
      const previewBtns = document.querySelectorAll('.btn-preview');
      previewBtns.forEach(btn => {
        btn.addEventListener('click', function() {
          const card = this.closest('.report-card');
          const title = card.querySelector('.report-title').textContent;
          console.log('Vista previa de:', title);
          // Aquí irá la lógica de vista previa
        });
      });

      // Handler para aplicar filtros
      const applyFiltersBtn = document.querySelector('.btn-apply-filters');
      if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', function() {
          console.log('Aplicando filtros...');
          // Aquí irá la lógica de filtrado
          
          // Feedback visual
          this.innerHTML = '<i class="bi bi-check-circle me-2"></i> ¡Filtros Aplicados!';
          setTimeout(() => {
            this.innerHTML = '<i class="bi bi-check-circle me-2"></i> Aplicar Filtros';
          }, 2000);
        });
      }
    });
  </script>
</body>
</html>
