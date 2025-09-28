<?php 
   // Validación de autenticación y permisos de docentes y administradores
   require_once 'core/AuthValidation.php';
   validarRol(['ADMIN', 'DOCENTE', 'DIRECTOR']); // Solo docentes y administradores pueden ver reportes
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard - Sistema Educativo</title>

  <!-- Bootstrap 5 + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
  
  <!-- Chart.js para gráficos -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    :root {
      /* Colores Power BI Web moderno con Material Design */
      --powerbi-bg: #fafafa;
      --powerbi-nav: #2d2d30;
      --powerbi-primary: #117867; /* Verde oscuro para animación y acentos */
      --powerbi-secondary: #0d5d52;
      --powerbi-card: #ffffff;
      --powerbi-border: #e0e0e0;
      --powerbi-text: #212121;
      --powerbi-text-light: #616161;
      --powerbi-text-lighter: #9e9e9e;
      --powerbi-hover: #f5f5f5;
      --powerbi-shadow: 0 2px 4px rgba(0,0,0,0.1); /* Sombras sutiles Material Design */
      --powerbi-shadow-hover: 0 4px 8px rgba(0,0,0,0.15);
      --powerbi-accent: #1976d2; /* Azul secundario */
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Roboto', 'Segoe UI', Tahoma, sans-serif;
      background: #f8f9fa;
      color: var(--powerbi-text);
      overflow-x: hidden;
      font-size: 14px;
      line-height: 1.5;
      padding: 0;
      margin: 0;
      position: relative;
    }

    /* Contenedor principal con franjas azules como en la imagen */
    .powerbi-embed-container {
      background: #f8f9fa;
      border: none;
      border-radius: 0;
      box-shadow: none;
      overflow: hidden;
      width: 100%;
      margin: 0;
      min-height: 100vh;
      position: relative;
    }

    /* Franja superior azul */
    .header-stripe {
      background: linear-gradient(90deg, #1e4a72 0%, #2d5aa0 50%, #1e4a72 100%);
      height: 60px;
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .header-stripe h1 {
      color: white;
      font-size: 20px;
      font-weight: 600;
      margin: 0;
      text-align: center;
      letter-spacing: 0.5px;
    }

    /* Franja inferior azul */
    .footer-stripe {
      background: linear-gradient(90deg, #1e4a72 0%, #2d5aa0 50%, #1e4a72 100%);
      height: 20px;
      width: 100%;
      position: fixed;
      bottom: 0;
      left: 0;
      border-top: 3px dotted rgba(255,255,255,0.3);
    }

    /* Botón flotante de regreso */
    .floating-back-btn {
      position: fixed;
      top: 20px;
      left: 20px;
      width: 50px;
      height: 50px;
      background: rgba(30, 74, 114, 0.9);
      border: none;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      z-index: 1000;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      transition: all 0.3s ease;
      backdrop-filter: blur(10px);
    }

    .floating-back-btn:hover {
      background: rgba(30, 74, 114, 1);
      transform: scale(1.1);
      box-shadow: 0 6px 20px rgba(0,0,0,0.25);
    }

    .floating-back-btn i {
      color: white;
      font-size: 20px;
    }

    /* Toolbar moderno Power BI Web */
    .powerbi-toolbar {
      background: linear-gradient(90deg, #ffffff 0%, #f8f9fa 100%);
      border-bottom: 1px solid var(--powerbi-border);
      padding: 8px 16px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
      justify-content: space-between;
      height: 40px;
    }

    .toolbar-left {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .powerbi-logo {
      font-size: 16px;
      font-weight: 600;
      color: #118dff;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .toolbar-controls {
      display: flex;
      align-items: center;
      gap: 4px;
    }

    .toolbar-btn {
      background: none;
      border: 1px solid transparent;
      padding: 4px 8px;
      border-radius: 2px;
      font-size: 12px;
      color: #605e5c;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 4px;
      transition: all 0.1s ease;
    }

    .toolbar-btn:hover {
      background: #f3f2f1;
      border-color: #e1dfdd;
    }

    .toolbar-btn.active {
      background: #deecf9;
      border-color: #106ebe;
      color: #106ebe;
    }

    .toolbar-right {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .embed-fullscreen-btn {
      background: none;
      border: 1px solid #8a8886;
      padding: 4px 8px;
      border-radius: 2px;
      font-size: 11px;
      color: #323130;
      cursor: pointer;
    }

    .embed-fullscreen-btn:hover {
      background: #f3f2f1;
    }

    /* Área de contenido del reporte */
    .powerbi-report-area {
      background: var(--powerbi-bg);
      min-height: calc(100vh - 200px);
      position: relative;
      display: flex;
    }

    /* Panel de filtros lateral */
    .powerbi-filters-panel {
      width: 280px;
      background: white;
      border-right: 1px solid #e1dfdd;
      padding: 0;
      box-shadow: 2px 0 4px rgba(0,0,0,0.1);
      position: relative;
      z-index: 10;
    }

    .filters-header {
      padding: 12px 16px;
      border-bottom: 1px solid #e1dfdd;
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: #faf9f8;
    }

    .filters-header h4 {
      margin: 0;
      font-size: 14px;
      font-weight: 600;
      color: #323130;
      display: flex;
      align-items: center;
    }

    .close-filters-btn {
      background: none;
      border: none;
      padding: 4px;
      cursor: pointer;
      color: #605e5c;
      border-radius: 2px;
    }

    .close-filters-btn:hover {
      background: #f3f2f1;
    }

    .filters-content {
      padding: 16px;
    }

    .filter-group {
      margin-bottom: 20px;
    }

    .filter-group label {
      display: block;
      font-size: 12px;
      font-weight: 600;
      color: #323130;
      margin-bottom: 6px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .filter-checkboxes {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .form-check {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .form-check-input {
      margin: 0;
    }

    .form-check-label {
      font-size: 13px;
      color: #323130;
      margin: 0;
      text-transform: none;
      letter-spacing: normal;
    }

    /* Header tipo Power BI */
    .powerbi-header {
      background: var(--powerbi-nav);
      color: white;
      padding: 8px 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 2px 4px rgba(0,0,0,0.14), 0 0 2px rgba(0,0,0,0.12);
      position: sticky;
      top: 0;
      z-index: 1000;
      min-height: 48px;
    }

    .header-left {
      display: flex;
      align-items: center;
      gap: 16px;
    }

    .back-btn {
      background: rgba(255,255,255,0.1);
      border: 1px solid rgba(255,255,255,0.2);
      color: white;
      padding: 8px 16px;
      border-radius: 4px;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: all 0.2s ease;
    }

    .back-btn:hover {
      background: rgba(255,255,255,0.2);
      color: white;
      text-decoration: none;
    }

    .dashboard-title {
      font-size: 18px;
      font-weight: 600;
      margin: 0;
    }

    /* Container principal con espacio para franjas */
    .main-content {
      padding: 20px;
      margin-top: 60px;
      margin-bottom: 40px;
      min-height: calc(100vh - 100px);
    }

    .dashboard-container {
      padding: 0;
      flex: 1;
      overflow-y: auto;
      transition: margin-left 0.3s ease;
      background: #f8f9fa;
    }

    .dashboard-container.filters-open {
      margin-left: 0;
    }

    /* Métricas principales estilo Power BI Desktop */
    
    .metrics-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 6px; /* Gap reducido para evitar scroll */ 
      margin-bottom: 10px;
      padding: 8px;
      background: transparent;
    }

    /* Métricas modernas Material Design */
    .metric-card {
      background: var(--powerbi-card);
      border: 1px solid var(--powerbi-border);
      border-radius: 12px; /* Bordes más redondeados */
      padding: 16px 20px; /* PADDING INTERNO: Más espacioso estilo Material */
      box-shadow: var(--powerbi-shadow);
      transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
      position: relative;
      min-height: 70px; /* Altura mínima reducida */
    }

    .metric-card:hover {
      border-color: var(--powerbi-primary);
      box-shadow: var(--powerbi-shadow-hover);
      transform: translateY(-1px);
    }

    .metric-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 2px;
      background: var(--powerbi-primary);
      border-radius: 2px 2px 0 0;
    }

    .metric-header {
      margin-bottom: 4px;
    }

    .metric-icon {
      display: none;
    }

    .metric-icon.users { background: linear-gradient(135deg, #118dff 0%, #0078d4 100%); }
    .metric-icon.institutions { background: linear-gradient(135deg, #00c851 0%, #107c10 100%); }
    .metric-icon.surveys { background: linear-gradient(135deg, #ffbb33 0%, #ff8800 100%); }
    .metric-icon.grades { background: linear-gradient(135deg, #9933cc 0%, #5c2d91 100%); }

    /* Títulos de métricas estilo Power BI Desktop */
    .metric-info h3 {
      font-size: 12px; /* TAMAÑO TÍTULO MÉTRICA: Tamaño auténtico Power BI */
      font-weight: 400;
      color: var(--powerbi-text-light);
      margin-bottom: 8px;
      text-transform: none;
      letter-spacing: normal;
      text-align: left;
    }

    .metric-value {
      font-size: 24px; /* TAMAÑO NÚMERO PRINCIPAL: Tamaño auténtico de Power BI */
      font-weight: 600;
      color: var(--powerbi-text);
      line-height: 1.1;
      text-align: left;
      margin: 4px 0;
    }

    .metric-change {
      font-size: 7px;
      margin-top: 2px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 2px;
    }

    .metric-change.positive { color: #107c10; }
    .metric-change.negative { color: #d13438; }

    /* Dashboard ultra compacto para evitar scroll */
    /* LAYOUT PRINCIPAL DASHBOARD: Distribución compacta sin scroll */
    .dashboard-grid {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr; /* COLUMNAS: 3 columnas */
      grid-template-rows: auto auto;
      gap: 6px; /* SEPARACIÓN GRÁFICAS: Espaciado mínimo para evitar scroll */
      margin-bottom: 8px;
      padding: 8px;
    }

    .chart-compact {
      grid-column: span 1;
    }

    .chart-wide {
      grid-column: span 2; /* Ahora ocupa 2 de 4 columnas en lugar de 2 de 3 */
    }

    .chart-full {
      grid-column: span 4; /* Ahora ocupa todas las 4 columnas */
    }

    /* Hacer gráficos ultra compactos para evitar scroll */
    /* ALTURA GRÁFICAS NORMALES: Cambiar aquí la altura de las gráficas principales */
    .chart-container {
      position: relative;
      height: 160px; /* ALTURA GRÁFICAS: Reducida para evitar scroll */
    }

    /* ALTURA GRÁFICAS GRANDES: Para gráficas que necesitan más espacio */
    .chart-container-tall {
      position: relative;
      height: 180px; /* ALTURA GRÁFICAS TALL: Reducida para evitar scroll */
    }

    /* Gráficas con altura ultra compacta */
    .chart-container canvas {
      max-height: 140px !important;
    }

    /* Ajustar gráficas específicas */
    .chart-card:nth-child(1) .chart-container,
    .chart-card:nth-child(2) .chart-container,
    .chart-card:nth-child(6) .chart-container {
      height: 160px !important;
    }

    .chart-filters {
      display: flex;
      gap: 6px;
      align-items: center;
    }

    .chart-filters select {
      font-size: 11px; /* Tamaño auténtico Power BI */
      padding: 4px 8px;
      width: auto !important;
      min-width: 80px;
      height: 24px; 
      border: 1px solid var(--powerbi-border);
      border-radius: 0px;
      background: white;
      color: var(--powerbi-text);
    }

    /* CONTENEDOR GRÁFICAS: Cards modernas Material Design */
    .chart-card {
      background: var(--powerbi-card);
      border: 1px solid var(--powerbi-border);
      border-radius: 8px; /* Bordes redondeados Material */
      padding: 0;
      box-shadow: var(--powerbi-shadow); /* Sombras Material Design */
      overflow: hidden;
      height: 220px;
      position: relative;
      transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    .chart-card:hover {
      box-shadow: var(--powerbi-shadow-hover);
      transform: translateY(-2px);
      border-color: var(--powerbi-primary);
    }

    /* HEADER GRÁFICAS: Headers auténticos de Power BI Desktop */
    .chart-header {
      padding: 4px 8px; /* PADDING HEADER: Más espaciado como Power BI real */
      border-bottom: none; /* Sin borde inferior como Power BI */
      background: transparent; /* Fondo transparente como Power BI */
      display: flex;
      justify-content: space-between;
      align-items: center;
      min-height: 28px;
    }

    .chart-title {
      font-size: 11px; /* TAMAÑO TÍTULO GRÁFICA: Tamaño auténtico de Power BI */
      font-weight: 600;
      color: var(--powerbi-text);
      margin: 0;
      display: flex;
      align-items: center;
      gap: 4px;
      letter-spacing: -0.01em;
    }

    .chart-body {
      padding: 8px; /* Padding más generoso como Power BI Desktop */
      height: calc(100% - 32px); /* Altura ajustada para gráficas más compactas */
      background: var(--powerbi-card);
    }

    .chart-container {
      position: relative;
      height: 300px;
    }

    /* Tabla de datos estilo Power BI */
    .data-table-section {
      background: var(--powerbi-card);
      border: 1px solid var(--powerbi-border);
      border-radius: 2px;
      padding: 0;
      box-shadow: var(--powerbi-shadow);
      overflow: hidden;
    }

    .table-header {
      padding: 4px 6px;
      border-bottom: 1px solid var(--powerbi-border);
    }

    .table-powerbi {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
      font-size: 13px;
    }

    .table-powerbi th {
      background: linear-gradient(90deg, #1e4a72 0%, #2d5aa0 100%);
      color: white;
      font-weight: 600;
      font-size: 8px;
      padding: 3px 4px;
      text-align: left;
      border-bottom: 1px solid #1e4a72;
      border-right: 1px solid rgba(255,255,255,0.2);
      white-space: nowrap;
      text-transform: uppercase;
      letter-spacing: 0.3px;
    }

    .table-powerbi th:last-child {
      border-right: none;
    }

    .table-powerbi td {
      padding: 3px 4px;
      border-bottom: 1px solid #e3f2fd;
      border-right: 1px solid #f0f8ff;
      font-size: 9px;
      vertical-align: middle;
    }

    .table-powerbi td:last-child {
      border-right: none;
    }

    .table-powerbi tbody tr {
      background: white;
      transition: background-color 0.1s ease;
    }

    .table-powerbi tbody tr:nth-child(even) {
      background: #f8fbff;
    }

    .table-powerbi tbody tr:hover {
      background: #e3f2fd !important;
      box-shadow: inset 0 0 0 1px rgba(30,74,114,0.2);
    }

    .table-powerbi tbody tr:last-child td {
      border-bottom: none;
    }

    /* Badge estilo Power BI */
    .badge-powerbi {
      display: inline-block;
      padding: 2px 8px;
      font-size: 11px;
      font-weight: 600;
      text-align: center;
      border-radius: 2px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .badge-success {
      background: #dff6dd;
      color: #0e5a0e;
      border: 1px solid #9fd89c;
    }

    .badge-warning {
      background: #fff4ce;
      color: #8a5a00;
      border: 1px solid #ffcc5a;
    }

    .badge-info {
      background: #cce7ff;
      color: #003e6b;
      border: 1px solid #66b3ff;
    }

    /* Responsive auténtico Power BI */
    @media (max-width: 768px) {
      .dashboard-grid {
        grid-template-columns: 1fr 1fr;
        gap: 6px;
        padding: 8px;
      }
      
      .chart-compact, .chart-wide, .chart-full {
        grid-column: span 1;
      }
      
      .dashboard-container {
        padding: 12px;
      }
      
      .metric-card {
        padding: 12px;
      }
      
      .chart-container, .chart-container-tall {
        height: 200px;
      }
    }

    /* Loading states - Animación verde moderna */
    .loading {
      display: flex;
      align-items: center;
      justify-content: center;
      height: 200px;
      color: var(--powerbi-text-light);
    }

    /* Overlay de carga centrado en pantalla completa */
    .powerbi-loading-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background: rgba(248, 249, 250, 0.95);
      backdrop-filter: blur(10px);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 10000;
    }

    .powerbi-loading-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }

    .powerbi-loading-logo {
      width: 120px;
      height: 80px;
      margin: 0;
      display: flex;
      align-items: end;
      justify-content: center;
    }

    /* Animación de barras amarilla/dorada centrada */
    .animated-bars {
      display: flex;
      align-items: end;
      gap: 6px;
      height: 64px;
    }

    .bar {
      width: 16px;
      border-radius: 3px 3px 0 0;
      animation: barGrow 1.5s ease-in-out infinite;
      box-shadow: 0 2px 8px rgba(255, 165, 0, 0.3);
    }

    .bar-1 {
      height: 20%;
      background: linear-gradient(180deg, #FFD700 0%, #FFA500 100%);
      animation-delay: 0s;
    }

    .bar-2 {
      height: 40%;
      background: linear-gradient(180deg, #FFA500 0%, #FF8C00 100%);
      animation-delay: 0.2s;
    }

    .bar-3 {
      height: 70%;
      background: linear-gradient(180deg, #FF8C00 0%, #FF7F50 100%);
      animation-delay: 0.4s;
    }

    .bar-4 {
      height: 100%;
      background: linear-gradient(180deg, #FF7F50 0%, #FF6347 100%);
      animation-delay: 0.6s;
    }

    @keyframes barGrow {
      0%, 100% { 
        transform: scaleY(0.6);
        opacity: 0.7;
      }
      50% { 
        transform: scaleY(1.2);
        opacity: 1;
      }
    }

    .loading-text {
      display: none;
    }

    .loading-dots {
      display: none;
    }

    .loading-dots span {
      display: inline-block;
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: #FFA500; /* Naranja dorado para combinar con las barras */
      margin: 0 2px;
      animation: loadingDots 1.4s ease-in-out infinite both;
    }

    .loading-dots span:nth-child(1) { animation-delay: -0.32s; }
    .loading-dots span:nth-child(2) { animation-delay: -0.16s; }

    @keyframes loadingDots {
      0%, 80%, 100% { transform: scale(0.8); opacity: 0.5; }
      40% { transform: scale(1.2); opacity: 1; }
    }

    .loading-status {
      display: none;
    }

    /* Barra de progreso oculta */
    .powerbi-progress-container {
      display: none;
    }

    .powerbi-progress-bar {
      width: 100%;
      height: 4px;
      background: #e0e0e0;
      border-radius: 2px;
      overflow: hidden;
      position: relative;
    }

    .powerbi-progress-fill {
      height: 100%;
      background: linear-gradient(90deg, #FFD700 0%, #FFA500 100%);
      border-radius: 2px;
      width: 0%;
      transition: width 0.3s ease;
      box-shadow: 0 0 10px rgba(255, 165, 0, 0.3);
    }

    .progress-percentage {
      text-align: center;
      font-size: 12px;
      color: var(--powerbi-text-light);
      margin-top: 8px;
      font-weight: 500;
    }

    /* Mejoras generales Material Design ultra compacto */
    .dashboard-grid {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      grid-template-rows: auto auto;
      gap: 6px; /* Espaciado mínimo para evitar scroll */
      margin-bottom: 8px;
      padding: 8px;
    }

    .metrics-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 6px; /* Gap mínimo para evitar scroll */ 
      margin-bottom: 10px;
      padding: 8px;
      background: transparent;
    }
  </style>
</head>
<body>
  <!-- Botón flotante de regreso -->
  <button class="floating-back-btn" onclick="window.location.href='?c=Reportes'">
    <i class="bi bi-arrow-left"></i>
  </button>

  <!-- Contenedor Power BI Embebido con franjas -->
  <div class="powerbi-embed-container">
    
    <!-- Franja superior azul -->
    <div class="header-stripe">
      <h1>Indicador del sistema educativo</h1>
    </div>
    
    <!-- Toolbar de Power BI -->
    <div class="powerbi-toolbar">
      <div class="toolbar-left">
        <div class="powerbi-logo">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
            <path d="M24,10V24H21V10H24M20,6V24H17V6H20M16,2V24H13V2H16M12,8V24H9V8H12M8,12V24H5V12H8M4,16V24H1V16H4Z"/>
          </svg>
          Power BI
        </div>
        <div class="toolbar-controls">
          <button class="toolbar-btn active">
            <i class="bi bi-grid-3x3-gap" style="font-size: 11px;"></i>
            Vista de reporte
          </button>
          <button class="toolbar-btn">
            <i class="bi bi-funnel" style="font-size: 11px;"></i>
            Filtros
          </button>
          <button class="toolbar-btn">
            <i class="bi bi-bookmark" style="font-size: 11px;"></i>
            Marcadores
          </button>
        </div>
      </div>
      <div class="toolbar-right">
        <button class="toolbar-btn">
          <i class="bi bi-arrow-clockwise" style="font-size: 11px;"></i>
          Actualizar
        </button>
        <button class="toolbar-btn">
          <i class="bi bi-download" style="font-size: 11px;"></i>
          Exportar
        </button>
        <button class="embed-fullscreen-btn">
          <i class="bi bi-fullscreen" style="font-size: 10px;"></i>
          Pantalla completa
        </button>
      </div>
    </div>

    <!-- Área del reporte Power BI -->
    <div class="powerbi-report-area">

      <!-- Panel de filtros Power BI (inicialmente oculto) -->
      <div class="powerbi-filters-panel" id="filtersPanel" style="display: none;">
        <div class="filters-header">
          <h4><i class="bi bi-funnel me-2"></i>Filtros</h4>
          <button class="close-filters-btn" onclick="toggleFilters()">
            <i class="bi bi-x"></i>
          </button>
        </div>
        <div class="filters-content">
          <div class="filter-group">
            <label>Período</label>
            <select class="form-select form-select-sm">
              <option>Último mes</option>
              <option>Últimos 3 meses</option>
              <option>Último año</option>
              <option>Todos</option>
            </select>
          </div>
          <div class="filter-group">
            <label>Institución</label>
            <select class="form-select form-select-sm">
              <option>Todas las instituciones</option>
              <option>Instituto A</option>
              <option>Instituto B</option>
            </select>
          </div>
          <div class="filter-group">
            <label>Rol de Usuario</label>
            <div class="filter-checkboxes">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" checked>
                <label class="form-check-label">Admin</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" checked>
                <label class="form-check-label">Director</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" checked>
                <label class="form-check-label">Docente</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" checked>
                <label class="form-check-label">Alumno</label>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Contenido principal envuelto -->
      <div class="main-content">
        <div class="dashboard-container">
    
    <!-- Métricas principales (ultra compactas) -->
    <div class="metrics-grid">
      <div class="metric-card">
        <div class="metric-header">
          <div class="metric-icon users">
            <i class="bi bi-people"></i>
          </div>
          <div class="metric-info">
            <h3>Total Usuarios</h3>
          </div>
        </div>
        <div class="metric-value"><?= isset($totalUsuarios) ? number_format($totalUsuarios) : '0' ?></div>
        <div class="metric-change positive">
          <i class="bi bi-arrow-up"></i>
          +12% este mes
        </div>
      </div>

      <div class="metric-card">
        <div class="metric-header">
          <div class="metric-icon institutions">
            <i class="bi bi-building"></i>
          </div>
          <div class="metric-info">
            <h3>Instituciones</h3>
          </div>
        </div>
        <div class="metric-value"><?= isset($totalInstituciones) ? number_format($totalInstituciones) : '0' ?></div>
        <div class="metric-change positive">
          <i class="bi bi-arrow-up"></i>
          +5% este mes
        </div>
      </div>

      <div class="metric-card">
        <div class="metric-header">
          <div class="metric-icon surveys">
            <i class="bi bi-clipboard-data"></i>
          </div>
          <div class="metric-info">
            <h3>Encuestas Activas</h3>
          </div>
        </div>
        <div class="metric-value"><?= isset($totalEncuestas) ? number_format($totalEncuestas) : '0' ?></div>
        <div class="metric-change positive">
          <i class="bi bi-arrow-up"></i>
          +8% este mes
        </div>
      </div>

      <div class="metric-card">
        <div class="metric-header">
          <div class="metric-icon grades">
            <i class="bi bi-graph-up"></i>
          </div>
          <div class="metric-info">
            <h3>Promedio General</h3>
          </div>
        </div>
        <div class="metric-value"><?= isset($promedioCalificaciones) ? number_format($promedioCalificaciones, 1) : '0.0' ?></div>
        <div class="metric-change positive">
          <i class="bi bi-arrow-up"></i>
          +2.5% este mes
        </div>
      </div>
    </div>

    <!-- Dashboard compacto estilo Power BI -->
    <div class="dashboard-grid">
      <!-- Gráfico de usuarios por rol -->
      <div class="chart-card chart-compact">
        <div class="chart-header">
          <h2 class="chart-title">
            <i class="bi bi-pie-chart text-primary"></i>
            Usuarios por Rol
          </h2>
        </div>
        <div class="chart-body">
          <div class="chart-container">
            <canvas id="rolesChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Gráfico de instituciones por distrito -->
      <div class="chart-card chart-compact">
        <div class="chart-header">
          <h2 class="chart-title">
            <i class="bi bi-bar-chart text-success"></i>
            Instituciones por Distrito
          </h2>
        </div>
        <div class="chart-body">
          <div class="chart-container">
            <canvas id="distritosChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Gráfico de mejores alumnos -->
      <div class="chart-card chart-compact">
        <div class="chart-header">
          <h2 class="chart-title">
            <i class="bi bi-star text-warning"></i>
            Top Alumnos
          </h2>
          <div class="chart-filters">
            <select id="cursoFilter" class="form-select form-select-sm">
              <option value="">Todos</option>
              <?php if (isset($cursos) && is_array($cursos)): ?>
                <?php foreach ($cursos as $curso): ?>
                  <option value="<?= $curso['id'] ?>"><?= htmlspecialchars($curso['nombre']) ?></option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>
        </div>
        <div class="chart-body">
          <div class="chart-container">
            <canvas id="alumnosChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Gráfico de promedios por institución (2 columnas) -->
      <div class="chart-card chart-wide">
        <div class="chart-header">
          <h2 class="chart-title">
            <i class="bi bi-trophy text-warning"></i>
            Ranking Instituciones por Promedio
          </h2>
        </div>
        <div class="chart-body">
          <div class="chart-container-tall">
            <canvas id="promediosChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabla de actividad reciente -->
    <div class="data-table-section">
      <div class="table-header">
        <h2 class="chart-title">
          <i class="bi bi-activity text-info"></i>
          Actividad Reciente del Sistema
        </h2>
      </div>
      
      <table class="table-powerbi">
        <thead>
          <tr>
            <th><i class="bi bi-calendar3 me-1"></i>Fecha</th>
            <th><i class="bi bi-person me-1"></i>Usuario</th>
            <th><i class="bi bi-lightning me-1"></i>Acción</th>
            <th><i class="bi bi-info-circle me-1"></i>Detalle</th>
            <th><i class="bi bi-check-circle me-1"></i>Estado</th>
          </tr>
        </thead>
        <tbody>
          <?php if (isset($actividadReciente) && is_array($actividadReciente)): ?>
            <?php foreach ($actividadReciente as $actividad): ?>
              <tr>
                <td>
                  <span style="font-weight: 500;">
                    <?= htmlspecialchars(isset($actividad['fecha']) ? $actividad['fecha'] : '') ?>
                  </span>
                </td>
                <td>
                  <div style="display: flex; align-items: center; gap: 6px;">
                    <div style="width: 24px; height: 24px; background: #e1f5fe; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                      <i class="bi bi-person" style="font-size: 12px; color: #0277bd;"></i>
                    </div>
                    <?= htmlspecialchars(isset($actividad['usuario']) ? $actividad['usuario'] : '') ?>
                  </div>
                </td>
                <td>
                  <span style="font-weight: 500; color: var(--powerbi-text);">
                    <?= htmlspecialchars(isset($actividad['accion']) ? $actividad['accion'] : '') ?>
                  </span>
                </td>
                <td style="color: var(--powerbi-text-light);">
                  <?= htmlspecialchars(isset($actividad['detalle']) ? $actividad['detalle'] : '') ?>
                </td>
                <td>
                  <span class="badge-powerbi <?php 
                    $estado = isset($actividad['estado']) ? $actividad['estado'] : '';
                    if ($estado == 'Completado') echo 'badge-success';
                    elseif ($estado == 'Pendiente') echo 'badge-warning';
                    else echo 'badge-info';
                  ?>">
                    <?= htmlspecialchars($estado) ?>
                  </span>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" style="text-align: center; color: var(--powerbi-text-lighter); padding: 40px; font-style: italic;">
                <i class="bi bi-inbox" style="font-size: 24px; margin-bottom: 8px; display: block;"></i>
                No hay actividad reciente disponible
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
        </div>
      </div>
    </div>
    
    <!-- Franja inferior azul -->
    <div class="footer-stripe"></div>
  </div>
  <!-- Fin contenedor Power BI embebido -->

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    // Datos para gráficos
    const rolesData = <?= isset($usuariosPorRol) ? json_encode($usuariosPorRol) : '[]' ?>;
    const distritosData = <?= isset($institucionesPorDistrito) ? json_encode($institucionesPorDistrito) : '[]' ?>;
    const promediosData = <?= isset($promediosPorInstitucion) ? json_encode($promediosPorInstitucion) : '[]' ?>;
    const alumnosData = <?= isset($mejoresAlumnos) ? json_encode($mejoresAlumnos) : '[]' ?>;
    console.log('=== DEBUG ALUMNOS DATA COMPLETO ===');
    console.log('Variable $mejoresAlumnos isset:', <?= isset($mejoresAlumnos) ? 'true' : 'false' ?>);
    console.log('Raw PHP data:', '<?= isset($mejoresAlumnos) ? json_encode($mejoresAlumnos) : "[]" ?>');
    console.log('Datos de mejores alumnos parseados:', alumnosData);
    console.log('Tipo de alumnosData:', typeof alumnosData);
    console.log('Es array?:', Array.isArray(alumnosData));
    console.log('Longitud de alumnosData:', alumnosData ? alumnosData.length : 'undefined');
    console.log('Primer elemento completo:', alumnosData && alumnosData.length > 0 ? alumnosData[0] : 'No hay elementos');
    if (alumnosData && alumnosData.length > 0) {
        console.log('Estructura primer elemento:');
        console.log('- alumno:', alumnosData[0].alumno);
        console.log('- promedio:', alumnosData[0].promedio);
        console.log('- curso:', alumnosData[0].curso);
        console.log('- grado:', alumnosData[0].grado);
    }
    console.log('==========================');

    // Configuración de Chart.js
    Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#323130';

    // Colores exactos de Power BI
    const powerBIColors = [
      '#118DFF', '#12239E', '#E66C37', '#6B007B', 
      '#E044A7', '#744EC2', '#D9B300', '#D64550'
    ];

    // Gráfico de roles estilo Power BI (Doughnut)
    const rolesCtx = document.getElementById('rolesChart').getContext('2d');
    new Chart(rolesCtx, {
      type: 'doughnut',
      data: {
        labels: rolesData.map(item => item.rol || 'Sin rol'),
        datasets: [{
          data: rolesData.map(item => item.total || 0),
          backgroundColor: powerBIColors.slice(0, rolesData.length),
          borderWidth: 0,
          cutout: '70%'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'right',
            labels: {
              padding: 15,
              usePointStyle: true,
              pointStyle: 'circle',
              font: {
                family: 'Segoe UI',
                size: 12
              },
              color: '#605e5c'
            }
          },
          tooltip: {
            backgroundColor: '#323130',
            titleColor: '#ffffff',
            bodyColor: '#ffffff',
            borderColor: '#8a8886',
            borderWidth: 1,
            cornerRadius: 2,
            displayColors: true,
            callbacks: {
              label: function(context) {
                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                const percentage = ((context.parsed / total) * 100).toFixed(1);
                return ` ${context.label}: ${context.parsed} (${percentage}%)`;
              }
            }
          }
        },
        elements: {
          arc: {
            borderWidth: 0
          }
        }
      }
    });

    // Gráfico de distritos estilo Power BI (Column Chart)
    const distritosCtx = document.getElementById('distritosChart').getContext('2d');
    new Chart(distritosCtx, {
      type: 'bar',
      data: {
        labels: distritosData.map(item => item.distrito || 'Sin distrito'),
        datasets: [{
          label: 'Instituciones',
          data: distritosData.map(item => item.total || 0),
          backgroundColor: '#118DFF',
          borderRadius: 2,
          borderSkipped: false,
          maxBarThickness: 40
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: '#f3f2f1',
              lineWidth: 1
            },
            ticks: {
              stepSize: 1,
              color: '#605e5c',
              font: {
                family: 'Segoe UI',
                size: 11
              }
            }
          },
          x: {
            grid: {
              display: false
            },
            ticks: {
              color: '#605e5c',
              font: {
                family: 'Segoe UI',
                size: 11
              },
              maxRotation: 45
            }
          }
        },
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            backgroundColor: '#323130',
            titleColor: '#ffffff',
            bodyColor: '#ffffff',
            borderColor: '#8a8886',
            borderWidth: 1,
            cornerRadius: 2,
            displayColors: false,
            callbacks: {
              title: function(context) {
                return context[0].label;
              },
              label: function(context) {
                return `Instituciones: ${context.parsed.y}`;
              }
            }
          }
        },
        interaction: {
          intersect: false,
          mode: 'index'
        }
      }
    });

    // Gráfico de promedios por institución estilo Power BI (Horizontal Bar Chart)
    const promediosCtx = document.getElementById('promediosChart').getContext('2d');
    new Chart(promediosCtx, {
      type: 'bar',
      data: {
        labels: promediosData.map(item => {
          // Truncar nombres largos
          const nombre = item.institucion || 'Sin nombre';
          return nombre.length > 25 ? nombre.substring(0, 25) + '...' : nombre;
        }),
        datasets: [{
          label: 'Promedio',
          data: promediosData.map(item => item.promedio || 0),
          backgroundColor: function(context) {
            // Gradiente de colores basado en el ranking
            const colors = [
              '#FFD700', // Oro para el primero
              '#C0C0C0', // Plata para el segundo  
              '#CD7F32', // Bronce para el tercero
              '#118DFF', // Azul Power BI para el resto
              '#0078d4',
              '#005a9e',
              '#106ebe',
              '#0084F4'
            ];
            return colors[context.dataIndex] || '#118DFF';
          },
          borderRadius: 4,
          borderSkipped: false,
          maxBarThickness: 40
        }]
      },
      options: {
        indexAxis: 'y', // Barras horizontales
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: {
            beginAtZero: true,
            max: 100,
            grid: {
              color: '#f3f2f1',
              lineWidth: 1
            },
            ticks: {
              color: '#605e5c',
              font: {
                family: 'Segoe UI',
                size: 11
              },
              callback: function(value) {
                return value + '%';
              }
            }
          },
          y: {
            grid: {
              display: false
            },
            ticks: {
              color: '#605e5c',
              font: {
                family: 'Segoe UI',
                size: 11
              }
            }
          }
        },
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            backgroundColor: '#323130',
            titleColor: '#ffffff',
            bodyColor: '#ffffff',
            borderColor: '#8a8886',
            borderWidth: 1,
            cornerRadius: 2,
            displayColors: false,
            callbacks: {
              title: function(context) {
                // Mostrar nombre completo en tooltip
                return promediosData[context[0].dataIndex].institucion;
              },
              label: function(context) {
                const data = promediosData[context.dataIndex];
                return [
                  `Promedio: ${data.promedio}%`,
                  `Total calificaciones: ${data.total_calificaciones || 0}`,
                  `Ranking: #${context.dataIndex + 1}`
                ];
              }
            }
          }
        },
        interaction: {
          intersect: false,
          mode: 'index'
        },
        animation: {
          duration: 2000,
          easing: 'easeInOutQuart'
        }
      }
    });

    // Variables globales para el gráfico de alumnos
    let alumnosChart;
    let filteredAlumnosData = [...alumnosData];

    // Función para filtrar datos de alumnos
    function filterAlumnosData() {
      console.log('=== INICIO filterAlumnosData ===');
      console.log('alumnosData:', alumnosData);
      console.log('alumnosData es array:', Array.isArray(alumnosData));
      console.log('alumnosData length:', alumnosData ? alumnosData.length : 'null/undefined');
      
      if (!alumnosData || alumnosData.length === 0) {
        console.warn('No hay datos de alumnos disponibles - usando datos vacíos');
        filteredAlumnosData = [];
        updateAlumnosChart();
        return;
      }
      
      const cursoId = document.getElementById('cursoFilter')?.value;
      
      filteredAlumnosData = alumnosData.filter(item => {
        const matchCurso = !cursoId || item.curso_id == cursoId;
        return matchCurso;
      });
      
      console.log('Datos filtrados:', filteredAlumnosData);
      
      // Limitar a top 5 para mejor visualización compacta
      filteredAlumnosData = filteredAlumnosData.slice(0, 5);
      
      updateAlumnosChart();
    }

    // Función para actualizar el gráfico de alumnos
    function updateAlumnosChart() {
      console.log('=== INICIO updateAlumnosChart ===');
      console.log('alumnosChart existe:', !!alumnosChart);
      console.log('filteredAlumnosData:', filteredAlumnosData);
      console.log('filteredAlumnosData es array:', Array.isArray(filteredAlumnosData));
      console.log('filteredAlumnosData length:', filteredAlumnosData ? filteredAlumnosData.length : 'null/undefined');
      
      if (!alumnosChart) {
        console.error('ERROR CRÍTICO: El gráfico alumnosChart no está inicializado');
        return;
      }

      if (!filteredAlumnosData || filteredAlumnosData.length === 0) {
        console.warn('ADVERTENCIA: No hay datos filtrados para mostrar, mostrando "Sin datos"');
        alumnosChart.data.labels = ['Sin datos'];
        alumnosChart.data.datasets[0].data = [0];
        alumnosChart.data.datasets[0].backgroundColor = ['#cccccc'];
        alumnosChart.update('active');
        console.log('Gráfico actualizado con "Sin datos"');
        return;
      }
      
      alumnosChart.data.labels = filteredAlumnosData.map(item => {
        // Mostrar nombre del alumno con curso y grado
        if (!item.alumno) return 'Sin nombre';
        const nombre = item.alumno.length > 15 ? item.alumno.substring(0, 15) + '...' : item.alumno;
        return `${nombre}\\n${item.curso || 'Sin curso'} - ${item.grado || 'Sin grado'}`;
      });
      
      alumnosChart.data.datasets[0].data = filteredAlumnosData.map(item => 
        parseFloat(item.promedio) || 0
      );
      
      // Actualizar colores basados en posición
      alumnosChart.data.datasets[0].backgroundColor = filteredAlumnosData.map((item, index) => {
        const colors = ['#FFD700', '#C0C0C0', '#CD7F32', '#118DFF', '#0078d4', '#005a9e', '#106ebe', '#0084F4', '#40E0D0', '#9370DB'];
        return colors[index] || '#118DFF';
      });
      
      console.log('ÉXITO: Labels configurados:', alumnosChart.data.labels);
      console.log('ÉXITO: Data configurada:', alumnosChart.data.datasets[0].data);
      console.log('ÉXITO: Colores configurados:', alumnosChart.data.datasets[0].backgroundColor);
      console.log('ÉXITO: Actualizando gráfico con', filteredAlumnosData.length, 'elementos');
      alumnosChart.update('active');
      console.log('=== FIN updateAlumnosChart ===');
    }

    // Gráfico de mejores alumnos estilo Power BI
    const alumnosCtx = document.getElementById('alumnosChart').getContext('2d');
    alumnosChart = new Chart(alumnosCtx, {
      type: 'bar',
      data: {
        labels: [],
        datasets: [{
          label: 'Promedio',
          data: [],
          backgroundColor: [],
          borderRadius: 4,
          borderSkipped: false,
          maxBarThickness: 50
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: {
            beginAtZero: true,
            max: 100,
            grid: {
              color: '#f3f2f1',
              lineWidth: 1
            },
            ticks: {
              color: '#605e5c',
              font: {
                family: 'Segoe UI',
                size: 11
              },
              callback: function(value) {
                return value + '%';
              }
            }
          },
          y: {
            grid: {
              display: false
            },
            ticks: {
              color: '#605e5c',
              font: {
                family: 'Segoe UI',
                size: 10
              },
              callback: function(value, index) {
                const label = this.getLabelForValue(value);
                // Dividir en líneas para mejor legibilidad
                const parts = label.split('\\n');
                return parts[0]; // Solo mostrar el nombre en el eje Y
              }
            }
          }
        },
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            backgroundColor: '#323130',
            titleColor: '#ffffff',
            bodyColor: '#ffffff',
            borderColor: '#8a8886',
            borderWidth: 1,
            cornerRadius: 2,
            displayColors: false,
            callbacks: {
              title: function(context) {
                const data = filteredAlumnosData[context[0].dataIndex];
                return data.alumno;
              },
              label: function(context) {
                const data = filteredAlumnosData[context.dataIndex];
                return [
                  `Promedio: ${data.promedio}%`,
                  `Curso: ${data.curso}`,
                  `Grado: ${data.grado}`,
                  `Institución: ${data.institucion}`,
                  `Total evaluaciones: ${data.total_calificaciones}`,
                  `Posición: #${context.dataIndex + 1}`
                ];
              }
            }
          }
        },
        interaction: {
          intersect: false,
          mode: 'index'
        },
        animation: {
          duration: 1500,
          easing: 'easeInOutCubic'
        }
      }
    });

    // Inicializar gráfico de alumnos con todos los datos
    console.log('=== INICIALIZANDO GRÁFICO DE ALUMNOS ===');
    console.log('alumnosChart inicializado:', !!alumnosChart);
    console.log('Llamando filterAlumnosData...');
    
    try {
        filterAlumnosData();
        console.log('filterAlumnosData ejecutado correctamente');
    } catch (error) {
        console.error('Error al ejecutar filterAlumnosData:', error);
        console.error('Stack trace:', error.stack);
    }
    
    console.log('=== FIN INICIALIZACIÓN ===');
    
    // Verificar estado después de 2 segundos
    setTimeout(() => {
        console.log('=== VERIFICACIÓN POST-CARGA ===');
        console.log('alumnosChart después de carga:', !!alumnosChart);
        console.log('Canvas element existe:', !!document.getElementById('alumnosChart'));
        console.log('filteredAlumnosData:', filteredAlumnosData);
        console.log('alumnosData original:', alumnosData);
        
        if (alumnosChart && alumnosChart.data) {
            console.log('Labels actuales del gráfico:', alumnosChart.data.labels);
            console.log('Datos actuales del gráfico:', alumnosChart.data.datasets[0].data);
        } else {
            console.error('El gráfico no tiene datos configurados');
        }
        console.log('=== FIN VERIFICACIÓN ===');
    }, 2000);

    // Funciones del Power BI embebido
    function toggleFilters() {
      const panel = document.getElementById('filtersPanel');
      const container = document.querySelector('.dashboard-container');
      
      if (panel.style.display === 'none' || panel.style.display === '') {
        panel.style.display = 'block';
        container.classList.add('filters-open');
        // Actualizar estado del botón en toolbar
        document.querySelector('.toolbar-btn[onclick*="Filtros"]')?.classList.add('active');
      } else {
        panel.style.display = 'none';
        container.classList.remove('filters-open');
        document.querySelector('.toolbar-btn[onclick*="Filtros"]')?.classList.remove('active');
      }
    }

    function refreshReport() {
      // Simular actualización del reporte
      const btn = event.target.closest('.toolbar-btn');
      const icon = btn.querySelector('i');
      
      icon.style.animation = 'spin 1s linear';
      setTimeout(() => {
        icon.style.animation = '';
      }, 1000);
    }

    function exportReport() {
      alert('Funcionalidad de exportación - Power BI');
    }

    function toggleFullscreen() {
      if (!document.fullscreenElement) {
        document.querySelector('.powerbi-embed-container').requestFullscreen();
      } else {
        document.exitFullscreen();
      }
    }

    // Animaciones y efectos adicionales
    document.addEventListener('DOMContentLoaded', function() {
      // Simular carga completa de Power BI como en la realidad
      const reportArea = document.querySelector('.powerbi-report-area');
      const dashboardContainer = document.querySelector('.dashboard-container');
      
      // Ocultar contenido inicialmente
      dashboardContainer.style.opacity = '0';
      dashboardContainer.style.visibility = 'hidden';
      
      // Crear overlay de carga Power BI realista
      const loadingOverlay = document.createElement('div');
      loadingOverlay.className = 'powerbi-loading-overlay';
      loadingOverlay.innerHTML = `
        <div class="powerbi-loading-container">
          <!-- Logo Power BI animado -->
          <div class="powerbi-loading-logo">
            <!-- Animación de barras amarilla/dorada moderna -->
            <div class="animated-bars">
              <div class="bar bar-1"></div>
              <div class="bar bar-2"></div>
              <div class="bar bar-3"></div>
              <div class="bar bar-4"></div>
            </div>
          </div>
          
          <!-- Texto de carga con animación de puntos -->
          <div class="loading-text">
            <span>Cargando Power BI</span>
            <span class="loading-dots">
              <span></span>
              <span></span>
              <span></span>
            </span>
          </div>
          
          <!-- Barra de progreso Power BI -->
          <div class="powerbi-progress-container">
            <div class="powerbi-progress-bar">
              <div class="powerbi-progress-fill"></div>
            </div>
            <div class="progress-percentage">0%</div>
          </div>
          
          <!-- Mensaje de estado -->
          <div class="loading-status">
            <span id="loadingStatus">Conectando con el servicio...</span>
          </div>
        </div>
      `;
      
      reportArea.appendChild(loadingOverlay);

      // Simular progreso de carga realista
      let progress = 0;
      const progressBar = document.querySelector('.powerbi-progress-fill');
      const progressText = document.querySelector('.progress-percentage');
      const statusText = document.getElementById('loadingStatus');
      
      const loadingSteps = [
        { progress: 15, text: "Conectando con el servicio...", duration: 800 },
        { progress: 35, text: "Autenticando usuario...", duration: 600 },
        { progress: 55, text: "Cargando modelo de datos...", duration: 900 },
        { progress: 75, text: "Renderizando visualizaciones...", duration: 700 },
        { progress: 90, text: "Aplicando formato...", duration: 500 },
        { progress: 100, text: "¡Listo!", duration: 300 }
      ];

      let currentStep = 0;
      
      function updateProgress() {
        if (currentStep < loadingSteps.length) {
          const step = loadingSteps[currentStep];
          
          // Animar progreso
          const startProgress = progress;
          const targetProgress = step.progress;
          const duration = step.duration;
          const startTime = Date.now();
          
          statusText.textContent = step.text;
          
          function animate() {
            const elapsed = Date.now() - startTime;
            const progressPercent = Math.min(elapsed / duration, 1);
            
            progress = startProgress + (targetProgress - startProgress) * progressPercent;
            progressBar.style.width = progress + '%';
            progressText.textContent = Math.round(progress) + '%';
            
            if (progressPercent < 1) {
              requestAnimationFrame(animate);
            } else {
              currentStep++;
              if (currentStep < loadingSteps.length) {
                setTimeout(updateProgress, 100);
              } else {
                // Carga completada
                setTimeout(() => {
                  loadingOverlay.style.opacity = '0';
                  loadingOverlay.style.transition = 'opacity 0.5s ease';
                  
                  setTimeout(() => {
                    loadingOverlay.remove();
                    dashboardContainer.style.visibility = 'visible';
                    dashboardContainer.style.opacity = '1';
                    dashboardContainer.style.transition = 'opacity 0.8s ease';
                    
                    // Animación de entrada para las métricas
                    setTimeout(() => {
                      const metricCards = document.querySelectorAll('.metric-card');
                      metricCards.forEach((card, index) => {
                        card.style.opacity = '0';
                        card.style.transform = 'translateY(20px)';
                        setTimeout(() => {
                          card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                          card.style.opacity = '1';
                          card.style.transform = 'translateY(0)';
                        }, index * 150);
                      });
                    }, 200);
                  }, 500);
                }, 500);
              }
            }
          }
          
          animate();
        }
      }
      
      // Iniciar carga después de un breve delay
      setTimeout(updateProgress, 500);

      // Añadir eventos a botones del toolbar
      document.querySelectorAll('.toolbar-btn').forEach(btn => {
        btn.addEventListener('click', function() {
          const text = this.textContent.trim();
          if (text.includes('Filtros')) {
            toggleFilters();
          } else if (text.includes('Actualizar')) {
            refreshReport();
          } else if (text.includes('Exportar')) {
            exportReport();
          }
        });
      });

      document.querySelector('.embed-fullscreen-btn')?.addEventListener('click', toggleFullscreen);

      // Event listeners para filtros de alumnos
      document.getElementById('cursoFilter')?.addEventListener('change', filterAlumnosData);
    });

    // CSS para animaciones
    const style = document.createElement('style');
    style.textContent = `
      @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
      }
      
      @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.8; transform: scale(1.05); }
      }
      
      @keyframes loadingDots {
        0%, 20% { opacity: 0; }
        50% { opacity: 1; }
        80%, 100% { opacity: 0; }
      }
      
      @keyframes slideInUp {
        0% { transform: translateY(20px); opacity: 0; }
        100% { transform: translateY(0); opacity: 1; }
      }
      
      /* Estilos para el overlay de carga Power BI */
      .powerbi-loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
      }
      
      .powerbi-loading-container {
        text-align: center;
        max-width: 400px;
        padding: 40px;
      }
      
      .powerbi-loading-logo {
        margin-bottom: 24px;
        animation: pulse 2s ease-in-out infinite;
      }
      
      .loading-text {
        font-size: 18px;
        font-weight: 600;
        color: #323130;
        margin-bottom: 32px;
        font-family: 'Segoe UI', sans-serif;
      }
      
      .loading-dots span {
        animation: loadingDots 1.5s ease-in-out infinite;
      }
      
      .loading-dots span:nth-child(1) { animation-delay: 0s; }
      .loading-dots span:nth-child(2) { animation-delay: 0.3s; }
      .loading-dots span:nth-child(3) { animation-delay: 0.6s; }
      
      .powerbi-progress-container {
        margin-bottom: 20px;
      }
      
      .powerbi-progress-bar {
        width: 100%;
        height: 4px;
        background: #e1dfdd;
        border-radius: 2px;
        overflow: hidden;
        margin-bottom: 8px;
      }
      
      .powerbi-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #118dff 0%, #0078d4 50%, #005a9e 100%);
        border-radius: 2px;
        width: 0%;
        transition: width 0.3s ease;
        position: relative;
      }
      
      .powerbi-progress-fill::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        animation: shimmer 1.5s ease-in-out infinite;
      }
      
      @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
      }
      
      .progress-percentage {
        font-size: 14px;
        font-weight: 600;
        color: #118dff;
        font-family: 'Segoe UI', sans-serif;
      }
      
      .loading-status {
        font-size: 13px;
        color: #605e5c;
        font-family: 'Segoe UI', sans-serif;
        animation: slideInUp 0.5s ease-out;
      }
    `;
    document.head.appendChild(style);
  </script>
</body>
</html>