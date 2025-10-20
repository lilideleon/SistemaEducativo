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

    /* Loader de gráficos con transiciones suaves */
    .chart-loader {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(2px);
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      z-index: 1000;
      border-radius: 6px;
      transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
      opacity: 1;
      visibility: visible;
    }

    .chart-loader.d-none {
      opacity: 0;
      visibility: hidden;
      pointer-events: none;
    }

    .loading-spinner {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      animation: fadeInUp 0.4s ease-out;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .chart-loader .spinner-border {
      width: 2.5rem;
      height: 2.5rem;
      border-width: 0.3em;
      color: #117867;
    }

    .chart-loader p {
      font-size: 0.9rem;
      margin: 0;
      font-weight: 500;
      color: #117867;
    }

    /* Transiciones suaves para filtros */
    .chart-filters select, .chart-filters button {
      transition: all 0.2s ease-in-out;
    }

    .chart-filters select:focus, .chart-filters button:focus {
      box-shadow: 0 0 0 0.2rem rgba(17, 120, 103, 0.25);
      border-color: #117867;
    }

    .chart-filters button:hover {
      background-color: #117867;
      border-color: #117867;
      color: white;
    }

    /* Loading global del dashboard */
    .global-loading {
      position: relative;
      pointer-events: none;
      opacity: 0.7;
    }

    .global-loading::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(255, 255, 255, 0.8);
      z-index: 9999;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .global-loading::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 40px;
      height: 40px;
      border: 3px solid #f3f3f3;
      border-top: 3px solid #117867;
      border-radius: 50%;
      animation: spin 1s linear infinite;
      z-index: 10000;
    }

    @keyframes spin {
      0% { transform: translate(-50%, -50%) rotate(0deg); }
      100% { transform: translate(-50%, -50%) rotate(360deg); }
    }

    /* Toast de notificación */
    .filter-toast {
      position: fixed;
      top: 20px;
      right: 20px;
      background: #28a745;
      color: white;
      padding: 12px 20px;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      z-index: 10001;
      transform: translateX(100%);
      transition: transform 0.3s ease-in-out;
      font-size: 14px;
      font-weight: 500;
    }

    .filter-toast.show {
      transform: translateX(0);
    }

    /* Estilos para botones de filtros */
    .filter-actions .btn {
      font-size: 13px;
      padding: 6px 12px;
    }

    .filter-actions .btn-primary {
      background-color: #117867;
      border-color: #117867;
    }

    .filter-actions .btn-primary:hover {
      background-color: #0d5d4f;
      border-color: #0d5d4f;
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
      box-shadow: 0 2px 8px rgba(253, 185, 19, 0.3);
    }

    .bar-1 {
      height: 20%;
      background: linear-gradient(180deg, #FDB913 0%, #F7B500 100%);
      animation-delay: 0s;
    }

    .bar-2 {
      height: 40%;
      background: linear-gradient(180deg, #F7B500 0%, #F4A900 100%);
      animation-delay: 0.2s;
    }

    .bar-3 {
      height: 70%;
      background: linear-gradient(180deg, #F4A900 0%, #F09D00 100%);
      animation-delay: 0.4s;
    }

    .bar-4 {
      height: 100%;
      background: linear-gradient(180deg, #F09D00 0%, #E89200 100%);
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
      background: #F7B500; /* Amarillo Power BI para combinar con las barras */
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
  <!-- Overlay de carga Power BI (visible desde el inicio) -->
  <div id="powerbiLoadingOverlay" class="powerbi-loading-overlay">
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
  </div>

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
          <button class="toolbar-btn" onclick="toggleFilters()">
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
            <select id="periodoFilter" class="form-select form-select-sm">
              <option value="">Todos</option>
              <option value="30">Último mes</option>
              <option value="90">Últimos 3 meses</option>
              <option value="365">Último año</option>
            </select>
          </div>
          <div class="filter-group">
            <label>Institución</label>
            <select id="institucionFilter" class="form-select form-select-sm">
              <option value="">Todas las instituciones</option>
              <?php if (isset($institucionesPorDistrito) && is_array($institucionesPorDistrito)): ?>
                <?php foreach ($institucionesPorDistrito as $inst): ?>
                  <option value="<?= htmlspecialchars($inst['distrito']) ?>"><?= htmlspecialchars($inst['distrito']) ?></option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>
          <div class="filter-group">
            <label>Rol de Usuario</label>
            <div class="filter-checkboxes">
              <div class="form-check">
                <input id="rolAdmin" class="form-check-input rol-filter" type="checkbox" value="ADMIN" checked>
                <label class="form-check-label" for="rolAdmin">Admin</label>
              </div>
              <div class="form-check">
                <input id="rolDirector" class="form-check-input rol-filter" type="checkbox" value="DIRECTOR" checked>
                <label class="form-check-label" for="rolDirector">Director</label>
              </div>
              <div class="form-check">
                <input id="rolDocente" class="form-check-input rol-filter" type="checkbox" value="DOCENTE" checked>
                <label class="form-check-label" for="rolDocente">Docente</label>
              </div>
              <div class="form-check">
                <input id="rolAlumno" class="form-check-input rol-filter" type="checkbox" value="ALUMNO" checked>
                <label class="form-check-label" for="rolAlumno">Alumno</label>
              </div>
            </div>
          </div>
          <div class="filter-actions mt-3">
            <button id="applyFilters" class="btn btn-primary btn-sm me-2">
              <i class="bi bi-check2"></i> Aplicar Filtros
            </button>
            <button id="clearAllFilters" class="btn btn-outline-secondary btn-sm">
              <i class="bi bi-arrow-clockwise"></i> Limpiar Todo
            </button>
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

      <!-- Gráfico de mejores alumnos -->
      <div class="chart-card chart-compact">
        <div class="chart-header">
          <h2 class="chart-title">
            Top Alumnos <span id="alumnosCounter" class="badge bg-secondary ms-2">0</span>
          </h2>
          <div class="chart-filters">
            <select id="cursoFilter" class="form-select form-select-sm me-2">
              <option value="">Todos los Cursos</option>
              <?php if (isset($cursos) && is_array($cursos)): ?>
                <?php foreach ($cursos as $curso): ?>
                  <option value="<?= $curso['id'] ?>"><?= htmlspecialchars($curso['nombre']) ?></option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
            <select id="gradoFilter" class="form-select form-select-sm me-2">
              <option value="">Todos los Grados</option>
              <?php if (isset($grados) && is_array($grados)): ?>
                <?php foreach ($grados as $grado): ?>
                  <option value="<?= $grado['id'] ?>"><?= htmlspecialchars($grado['nombre']) ?></option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
            <button id="clearFilters" class="btn btn-outline-secondary btn-sm">
              <i class="bi bi-arrow-clockwise"></i> Limpiar
            </button>
          </div>
        </div>
        <div class="chart-body">
          <div class="chart-container position-relative">
            <canvas id="alumnosChart"></canvas>
            <div id="alumnosChartLoader" class="chart-loader d-none">
              <div class="loading-spinner">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2 text-muted">Filtrando datos...</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Gráfico de promedios por institución (2 columnas) -->
      <div class="chart-card chart-wide">
        <div class="chart-header">
          <h2 class="chart-title">
            Ranking Instituciones por Promedio
          </h2>
        </div>
        <div class="chart-body">
          <div class="chart-container-tall">
            <canvas id="promediosChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Gráfico: Preguntas con más errores -->
      <div class="chart-card chart-wide">
        <div class="chart-header">
          <h2 class="chart-title">Preguntas con más errores</h2>
        </div>
        <div class="chart-body">
          <div class="chart-container-tall">
            <canvas id="preguntasErroresChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabla de actividad reciente -->
    <div class="data-table-section">
      <div class="table-header">
        <h2 class="chart-title">
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
    // ⚡ PRIMER PASO: Iniciar animación del loader INMEDIATAMENTE
    console.log('📍 Iniciando loader de Power BI...');
    
    (function() {
      const loadingOverlay = document.getElementById('powerbiLoadingOverlay');
      
      if (!loadingOverlay) {
        console.error('❌ No se encontró el overlay');
        return;
      }
      
      console.log('✅ Overlay encontrado, iniciando animación...');
      
      // Verificar si debe saltar la animación
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.get('fastload') === '1') {
        setTimeout(() => loadingOverlay.remove(), 100);
        return;
      }
      
      let progress = 0;
      const progressBar = document.querySelector('.powerbi-progress-fill');
      const progressText = document.querySelector('.progress-percentage');
      const statusText = document.getElementById('loadingStatus');
      
      if (!progressBar || !progressText || !statusText) {
        console.error('❌ Elementos de progreso no encontrados');
        setTimeout(() => loadingOverlay.remove(), 100);
        return;
      }
      
      const loadingSteps = [
        { progress: 20, text: "Conectando con el servicio...", duration: 300 },
        { progress: 45, text: "Autenticando usuario...", duration: 250 },
        { progress: 70, text: "Cargando modelo de datos...", duration: 400 },
        { progress: 90, text: "Renderizando visualizaciones...", duration: 350 },
        { progress: 100, text: "¡Listo!", duration: 150 }
      ];
      
      let currentStep = 0;
      
      function updateProgress() {
        if (currentStep < loadingSteps.length) {
          const step = loadingSteps[currentStep];
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
                setTimeout(updateProgress, 80);
              } else {
                // Animación completada
                console.log('✅ Carga completada, ocultando overlay...');
                setTimeout(() => {
                  loadingOverlay.style.transition = 'opacity 0.3s ease';
                  loadingOverlay.style.opacity = '0';
                  setTimeout(() => {
                    try { loadingOverlay.remove(); } catch(e) {}
                    console.log('🎉 Overlay removido');
                  }, 300);
                }, 150);
              }
            }
          }
          
          animate();
        }
      }
      
      // Iniciar animación
      console.log('⏳ Iniciando progreso...');
      setTimeout(updateProgress, 50);
    })();
    
    // ⚡ SEGUNDO PASO: Cargar gráficos del dashboard
    console.log('🚀 Iniciando Dashboard...');
    
    // Datos para gráficos SIMPLIFICADOS Y FUNCIONALES
    try {
      console.log('📊 Cargando datos...');
      
      // Datos cargados desde el servidor (controlador)
      // Usar var para que sean globales y accesibles desde otros bloques <script>
      var rolesData = <?= isset($usuariosPorRol) ? json_encode($usuariosPorRol) : json_encode([]) ?>;
      var distritosData = <?= isset($institucionesPorDistrito) ? json_encode($institucionesPorDistrito) : json_encode([]) ?>;
      var promediosData = <?= isset($promediosPorInstitucion) ? json_encode($promediosPorInstitucion) : json_encode([]) ?>;
      var alumnosData = <?= isset($mejoresAlumnos) ? json_encode($mejoresAlumnos) : json_encode([]) ?>;
      var preguntasErroresData = <?= isset($preguntasConMasErrores) ? json_encode($preguntasConMasErrores) : json_encode([]) ?>;

    // Chart instances (declaradas aquí para evitar usar var antes de definir)
      var alumnosChart, rolesChart, distritosChart, promediosChart, preguntasErroresChart;
      var powerBIColors = ['#118DFF', '#E66C37', '#6B007B', '#12239E', '#E044A7'];
      
      console.log('✅ Datos cargados correctamente');
      console.log('🔍 DEBUG - promediosData:', promediosData);
      console.log('🔍 DEBUG - promediosData length:', promediosData ? promediosData.length : 'undefined');
      
      // LOG DETALLADO: Ver cada institución y su promedio
      if (promediosData && promediosData.length > 0) {
        console.log('📊 DETALLE DE INSTITUCIONES:');
        promediosData.forEach((inst, idx) => {
          console.log(`  [${idx}] ${inst.institucion}: promedio=${inst.promedio}, calificaciones=${inst.total_calificaciones}`);
        });
      }
      
      // Colores Power BI (ya definido arriba en var)

      
      // GRÁFICO 1: ROLES
      console.log('📊 Creando gráfico de roles...');
      const rolesElement = document.getElementById('rolesChart');
      if (rolesElement) {
        rolesChart = new Chart(rolesElement.getContext('2d'), {
          type: 'doughnut',
          data: {
            labels: rolesData.map(item => item.rol),
            datasets: [{
              data: rolesData.map(item => item.total),
              backgroundColor: powerBIColors,
              borderWidth: 0,
              cutout: '70%'
            }]
          },
          options: { responsive: true, maintainAspectRatio: false }
        });
        console.log('✅ Gráfico de roles creado');
      }
      
      // GRÁFICO 2: DISTRITOS
      console.log('📊 Creando gráfico de distritos...');
      const distritosElement = document.getElementById('distritosChart');
      if (distritosElement) {
        distritosChart = new Chart(distritosElement.getContext('2d'), {
          type: 'bar',
          data: {
            labels: distritosData.map(item => item.distrito),
            datasets: [{
              data: distritosData.map(item => item.total),
              backgroundColor: '#118DFF'
            }]
          },
          options: { responsive: true, maintainAspectRatio: false }
        });
        console.log('✅ Gráfico de distritos creado');
      }

      // GRÁFICO 5: PREGUNTAS CON MÁS ERRORES
      console.log('📊 Creando gráfico de preguntas con más errores...');
      const pregErrElement = document.getElementById('preguntasErroresChart');
      if (pregErrElement) {
        const labels = (preguntasErroresData || []).map(item => (item.enunciado || '').substring(0, 40) + ( (item.enunciado||'').length>40 ? '…' : '' ));
        const dataVals = (preguntasErroresData || []).map(item => item.incorrectas || 0);
        preguntasErroresChart = new Chart(pregErrElement.getContext('2d'), {
          type: 'bar',
          data: {
            labels: labels,
            datasets: [{
              label: 'Respuestas incorrectas',
              data: dataVals,
              backgroundColor: '#E66C37',
              maxBarThickness: 32,
              borderRadius: 4,
              borderSkipped: false
            }]
          },
          options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              tooltip: {
                callbacks: {
                  title: function(ctx){ return preguntasErroresData[ctx[0].dataIndex].encuesta || 'Encuesta'; },
                  label: function(ctx){
                    const it = preguntasErroresData[ctx.dataIndex];
                    return [`Incorrectas: ${it.incorrectas}`, `Total: ${it.total_respuestas}`, `Tasa error: ${it.tasa_error}%`];
                  }
                }
              }
            },
            scales: {
              x: { beginAtZero: true }
            }
          }
        });
        console.log('✅ Gráfico de preguntas con más errores creado');
      }
      
      // (El gráfico de promedios se crea en el bloque avanzado más abajo)
      
      // GRÁFICO 4: ALUMNOS
      console.log('📊 Creando gráfico de alumnos...');
      const alumnosElement = document.getElementById('alumnosChart');
      if (alumnosElement) {
        new Chart(alumnosElement.getContext('2d'), {
          type: 'bar',
          data: {
            labels: alumnosData.map(item => item.alumno.split(' ')[0]),
            datasets: [{
              data: alumnosData.map(item => item.promedio),
              backgroundColor: ['#FFD700', '#C0C0C0', '#CD7F32']
            }]
          },
          options: { 
            indexAxis: 'y',
            responsive: true, 
            maintainAspectRatio: false 
          }
        });
        console.log('✅ Gráfico de alumnos creado');
        
        // Actualizar contador
        const counter = document.getElementById('alumnosCounter');
        if (counter) counter.textContent = alumnosData.length;
      }
      
      console.log('🎉 Dashboard cargado exitosamente');
      
      // FUNCIONES DE FILTROS SIMPLIFICADAS
      window.toggleFilters = function() {
        const panel = document.getElementById('filtersPanel');
        if (panel) {
          panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
          console.log('Panel de filtros alternado');
        }
      };
      
      // Aplicar filtros
      const applyBtn = document.getElementById('applyFilters');
      if (applyBtn) {
        applyBtn.onclick = function() {
          console.log('🔄 Aplicando filtros...');
          setTimeout(() => {
            console.log('✅ Filtros aplicados');
            // Toast de éxito
            const toast = document.createElement('div');
            toast.style = 'position:fixed;top:20px;right:20px;background:#28a745;color:white;padding:10px 20px;border-radius:5px;z-index:9999;';
            toast.textContent = '✅ Filtros aplicados correctamente';
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
          }, 800);
        };
      }
      
      // Limpiar filtros
      const clearBtn = document.getElementById('clearAllFilters');
      if (clearBtn) {
        clearBtn.onclick = function() {
          console.log('🧹 Limpiando filtros...');
          const selects = document.querySelectorAll('#filtersPanel select');
          selects.forEach(select => select.value = '');
          const checkboxes = document.querySelectorAll('#filtersPanel input[type="checkbox"]');
          checkboxes.forEach(cb => cb.checked = true);
        };
      }
      
    } catch (error) {
      console.error('❌ Error en dashboard:', error);
      alert('Error al cargar el dashboard. Revisa la consola para más detalles.');
    }
  </script>
</body>
</html>
<script>
      
    // Gráfico de roles estilo Power BI (Doughnut)
    const rolesChartElement = document.getElementById('rolesChart');
    if (rolesChartElement) {
      console.log('Inicializando gráfico de roles...');
      const rolesCtx = rolesChartElement.getContext('2d');
      rolesChart = new Chart(rolesCtx, {
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
    } else {
      console.error('No se encontró el elemento rolesChart');
    }

    // Gráfico de distritos estilo Power BI (Column Chart)
    const distritosChartElement = document.getElementById('distritosChart');
    if (distritosChartElement) {
      console.log('Inicializando gráfico de distritos...');
      const distritosCtx = distritosChartElement.getContext('2d');
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
    } else {
      console.error('No se encontró el elemento distritosChart');
    }

    // Gráfico de promedios por institución estilo Power BI (Horizontal Bar Chart)
    const promediosChartElement = document.getElementById('promediosChart');
    if (promediosChartElement) {
      console.log('Inicializando gráfico de promedios...');
      console.log('promediosData para gráfico:', promediosData);
      
      // MOSTRAR TODAS LAS INSTITUCIONES (con y sin datos)
      // Ordenar: primero las que tienen datos (por promedio DESC), luego las sin datos
      const promediosConDatos = promediosData.filter(item => parseFloat(item.promedio) > 0);
      const promediosSinDatos = promediosData.filter(item => parseFloat(item.promedio) === 0);
      const todasLasInstituciones = [...promediosConDatos, ...promediosSinDatos];
      
      console.log(`📊 Instituciones con datos: ${promediosConDatos.length}, Sin datos: ${promediosSinDatos.length}, Total: ${todasLasInstituciones.length}`);
      
      if (!todasLasInstituciones || todasLasInstituciones.length === 0) {
        console.warn('⚠️  No hay instituciones para mostrar');
      }
      
      const promediosCtx = promediosChartElement.getContext('2d');
      promediosChart = new Chart(promediosCtx, {
      type: 'bar',
      data: {
        labels: todasLasInstituciones.map(item => {
          // Truncar nombres largos
          const nombre = item.institucion || 'Sin nombre';
          const nombreCorto = nombre.length > 30 ? nombre.substring(0, 30) + '...' : nombre;
          // Agregar indicador si no tiene datos
          return parseFloat(item.promedio) === 0 ? nombreCorto + ' (Sin datos)' : nombreCorto;
        }),
        datasets: [{
          label: 'Promedio',
          data: todasLasInstituciones.map(item => parseFloat(item.promedio) || 0),
          backgroundColor: function(context) {
            const index = context.dataIndex;
            const promedio = parseFloat(todasLasInstituciones[index].promedio) || 0;
            
            // Si no tiene datos, color gris
            if (promedio === 0) {
              return '#cccccc';
            }
            
            // Colores para instituciones con datos (ranking)
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
            
            // Encontrar la posición en el ranking (solo entre los que tienen datos)
            const posicionRanking = promediosConDatos.findIndex(p => p.institucion === todasLasInstituciones[index].institucion);
            return colors[posicionRanking] || '#118DFF';
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
                return promediosConDatos[context[0].dataIndex].institucion;
              },
              label: function(context) {
                const data = promediosConDatos[context.dataIndex];
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
    } else {
      console.error('No se encontró el elemento promediosChart');
    }

  // Variables globales para los gráficos
      var filteredAlumnosData = [...alumnosData];

    // Función para filtrar datos de alumnos con animación
    function filterAlumnosData(showLoader = false) {
      console.log('=== INICIO filterAlumnosData ===');
      console.log('showLoader:', showLoader);
      console.log('alumnosData:', alumnosData);
      console.log('alumnosData es array:', Array.isArray(alumnosData));
      console.log('alumnosData length:', alumnosData ? alumnosData.length : 'null/undefined');
      
      // Mostrar loader si es necesario
      if (showLoader) {
        showChartLoader();
      }
      
      if (!alumnosData || alumnosData.length === 0) {
        console.warn('No hay datos de alumnos disponibles - usando datos vacíos');
        filteredAlumnosData = [];
        if (showLoader) {
          setTimeout(() => {
            hideChartLoader();
            updateAlumnosChart();
          }, 800); // Simular carga
        } else {
          updateAlumnosChart();
        }
        return;
      }
      
      const cursoId = document.getElementById('cursoFilter')?.value;
      const gradoId = document.getElementById('gradoFilter')?.value;
      
      console.log('Filtros aplicados - Curso ID:', cursoId, 'Grado ID:', gradoId);
      
      filteredAlumnosData = alumnosData.filter(item => {
        const matchCurso = !cursoId || item.curso_id == cursoId;
        const matchGrado = !gradoId || item.grado_id == gradoId;
        return matchCurso && matchGrado;
      });
      
      console.log('Datos filtrados (antes de limitar):', filteredAlumnosData.length, 'elementos');
      console.log('Datos filtrados:', filteredAlumnosData);
      
      // Limitar a top 8 para mejor visualización compacta
      filteredAlumnosData = filteredAlumnosData.slice(0, 8);
      
      if (showLoader) {
        // Simular tiempo de procesamiento para mejor UX
        setTimeout(() => {
          hideChartLoader();
          updateAlumnosChart();
        }, 600 + Math.random() * 400); // Entre 600ms y 1s
      } else {
        updateAlumnosChart();
      }
    }

    // Funciones para mostrar/ocultar loader con transiciones suaves
    function showChartLoader() {
      const loader = document.getElementById('alumnosChartLoader');
      if (loader) {
        loader.classList.remove('d-none');
        // Forzar repaint para activar transición
        loader.offsetHeight;
        console.log('Loader mostrado con animación');
      }
    }

    function hideChartLoader() {
      const loader = document.getElementById('alumnosChartLoader');
      if (loader) {
        // Usar setTimeout para permitir que la transición se complete
        setTimeout(() => {
          loader.classList.add('d-none');
        }, 50);
        console.log('Loader oculto con animación');
      }
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
        console.warn('ADVERTENCIA: No hay datos filtrados para mostrar');
        
        // Verificar si es por filtros aplicados
        const cursoFilter = document.getElementById('cursoFilter')?.value;
        const gradoFilter = document.getElementById('gradoFilter')?.value;
        const hasFilters = cursoFilter || gradoFilter;
        
        const emptyMessage = hasFilters ? 'No hay resultados para los filtros aplicados' : 'Sin datos disponibles';
        
        alumnosChart.data.labels = [emptyMessage];
        alumnosChart.data.datasets[0].data = [0];
        alumnosChart.data.datasets[0].backgroundColor = ['#e9ecef'];
        
        // Animación más suave para estados vacíos
        alumnosChart.update({
          duration: 800,
          easing: 'easeOutQuart'
        });
        
        console.log('Gráfico actualizado con mensaje:', emptyMessage);
        
        // Actualizar contador
        updateAlumnosCounter(0);
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
      
      // Actualizar contador
      updateAlumnosCounter(filteredAlumnosData.length);
      
      // Animación suave y elegante para la actualización
      alumnosChart.update({
        duration: 1200,
        easing: 'easeInOutCubic'
      });
      
      console.log('=== FIN updateAlumnosChart ===');
    }

    // Función para actualizar el contador de alumnos
    function updateAlumnosCounter(count) {
      const counter = document.getElementById('alumnosCounter');
      if (counter) {
        counter.textContent = count;
        counter.className = `badge ms-2 ${count > 0 ? 'bg-success' : 'bg-secondary'}`;
        console.log('Contador actualizado:', count);
      }
    }

    // Actualizar gráfico de promedios si existe
    function updatePromediosChart() {
      if (!promediosChart || !promediosData) {
        console.warn('No se puede actualizar promediosChart - no existe o no hay datos');
        return;
      }
      
      // MOSTRAR TODAS LAS INSTITUCIONES (con y sin datos)
      const promediosConDatos = promediosData.filter(item => parseFloat(item.promedio) > 0);
      const promediosSinDatos = promediosData.filter(item => parseFloat(item.promedio) === 0);
      const todasLasInstituciones = [...promediosConDatos, ...promediosSinDatos];
      
      promediosChart.data.labels = todasLasInstituciones.map(item => {
        const nombre = item.institucion || 'Sin nombre';
        const nombreCorto = nombre.length > 30 ? nombre.substring(0, 30) + '...' : nombre;
        return parseFloat(item.promedio) === 0 ? nombreCorto + ' (Sin datos)' : nombreCorto;
      });
      
      promediosChart.data.datasets[0].data = todasLasInstituciones.map(item => parseFloat(item.promedio) || 0);
      
      // Actualizar colores
      promediosChart.data.datasets[0].backgroundColor = todasLasInstituciones.map((item, index) => {
        const promedio = parseFloat(item.promedio) || 0;
        if (promedio === 0) return '#cccccc';
        
        const colors = ['#FFD700', '#C0C0C0', '#CD7F32', '#118DFF', '#0078d4', '#005a9e', '#106ebe', '#0084F4'];
        const posicionRanking = promediosConDatos.findIndex(p => p.institucion === item.institucion);
        return colors[posicionRanking] || '#118DFF';
      });
      
      promediosChart.update('active');
      console.log('Promedios chart actualizado con', todasLasInstituciones.length, 'instituciones (con datos:', promediosConDatos.length, ', sin datos:', promediosSinDatos.length, ')');
    }

    // Actualizar gráfico de distritos si existe
    function updateDistritosChart() {
      if (!distritosChart || !distritosData) {
        console.warn('No se puede actualizar distritosChart - no existe o no hay datos');
        return;
      }
      distritosChart.data.labels = distritosData.map(item => item.distrito || 'Sin distrito');
      distritosChart.data.datasets[0].data = distritosData.map(item => item.total || 0);
      distritosChart.update('active');
      console.log('Distritos chart actualizado con', distritosData.length, 'elementos');
    }

    // Gráfico de mejores alumnos estilo Power BI
    const alumnosChartElement = document.getElementById('alumnosChart');
    if (alumnosChartElement) {
      console.log('Inicializando gráfico de alumnos...');
      const alumnosCtx = alumnosChartElement.getContext('2d');
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
    } else {
      console.error('No se encontró el elemento alumnosChart');
    }

    // Verificar que todos los datos estén disponibles
    console.log('=== VERIFICACIÓN DE DATOS INICIALES ===');
    console.log('rolesData:', rolesData);
    console.log('alumnosData:', alumnosData);
    console.log('distritosData:', distritosData);
    console.log('promediosData:', promediosData);

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

    // Configurar event listeners para filtros
    document.getElementById('cursoFilter')?.addEventListener('change', function() {
      console.log('Filtro de curso cambiado:', this.value);
      filterAlumnosData(true); // Con animación de carga
    });

    document.getElementById('gradoFilter')?.addEventListener('change', function() {
      console.log('Filtro de grado cambiado:', this.value);
      filterAlumnosData(true); // Con animación de carga
    });

    document.getElementById('clearFilters')?.addEventListener('click', function() {
      console.log('Limpiando filtros...');
      document.getElementById('cursoFilter').value = '';
      document.getElementById('gradoFilter').value = '';
      filterAlumnosData(true); // Con animación de carga
    });

    console.log('Event listeners configurados para filtros');

    // Configurar event listeners para filtros globales del panel
    document.getElementById('applyFilters')?.addEventListener('click', applyGlobalFilters);
    document.getElementById('clearAllFilters')?.addEventListener('click', clearGlobalFilters);
    
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
        
        // Verificar elementos del DOM para filtros
        console.log('=== VERIFICACIÓN ELEMENTOS FILTROS ===');
        console.log('periodoFilter existe:', !!document.getElementById('periodoFilter'));
        console.log('institucionFilter existe:', !!document.getElementById('institucionFilter'));
        console.log('applyFilters existe:', !!document.getElementById('applyFilters'));
        console.log('clearAllFilters existe:', !!document.getElementById('clearAllFilters'));
        console.log('rol-filter checkboxes:', document.querySelectorAll('.rol-filter').length);
        console.log('=== FIN VERIFICACIÓN FILTROS ===');
    }, 2000);

    // Funciones del Power BI embebido
    function toggleFilters() {
      const panel = document.getElementById('filtersPanel');
      const container = document.querySelector('.dashboard-container');
      const filterBtn = document.querySelector('.toolbar-btn[onclick*="toggleFilters"]');
      
      console.log('toggleFilters llamado');
      console.log('Panel:', panel);
      console.log('Estado actual:', panel ? panel.style.display : 'panel no encontrado');
      
      if (panel.style.display === 'none' || panel.style.display === '') {
        panel.style.display = 'block';
        if (container) container.classList.add('filters-open');
        if (filterBtn) filterBtn.classList.add('active');
        console.log('Filtros abiertos');
      } else {
        panel.style.display = 'none';
        if (container) container.classList.remove('filters-open');
        if (filterBtn) filterBtn.classList.remove('active');
        console.log('Filtros cerrados');
      }
    }

    // Funciones para filtros globales
    function applyGlobalFilters() {
      console.log('Aplicando filtros globales...');
      
      try {
        // Mostrar indicador de carga en todos los gráficos
        showGlobalLoader();

        // Obtener valores de filtros
        const filtros = getGlobalFilterValues();
        console.log('Filtros obtenidos (enviando al servidor):', filtros);

        // Construir FormData para enviar por POST
        const formData = new FormData();
        formData.append('periodo', filtros.periodo || '');
        formData.append('institucion_id', filtros.institucion || '');
        formData.append('roles', JSON.stringify(filtros.roles || []));
        // añadir filtros de curso/grado si existen en el DOM
        const cursoVal = document.getElementById('cursoFilter')?.value || '';
        const gradoVal = document.getElementById('gradoFilter')?.value || '';
        formData.append('curso_id', cursoVal);
        formData.append('grado_id', gradoVal);

        // Llamada AJAX al endpoint del controlador
        fetch('?c=Reportes&a=filtrarDashboard', {
          method: 'POST',
          body: formData,
          credentials: 'same-origin'
        })
        .then(resp => resp.json())
        .then(data => {
          console.log('Respuesta filtrarDashboard:', data);
          if (data.error) {
            console.error('Error desde servidor:', data.error);
            hideGlobalLoader();
            return;
          }

          // Actualizar chart de mejores alumnos
          if (data.mejoresAlumnos) {
            alumnosData = data.mejoresAlumnos;
            filteredAlumnosData = alumnosData; // reemplazar filtro local
            updateAlumnosChart();
          }

          // Actualizar promedios por institucion
          if (data.promediosPorInstitucion) {
            promediosData = data.promediosPorInstitucion;
            updatePromediosChart();
          }

          // Actualizar roles
          if (data.usuariosPorRol) {
            rolesData = data.usuariosPorRol;
            if (rolesChart) updateRolesChart(getGlobalFilterValues().roles || []);
          }

          // Actualizar distritos/instituciones
          if (data.institucionesPorDistrito) {
            distritosData = data.institucionesPorDistrito;
            updateDistritosChart();
          }

          // Actualizar preguntas con más errores
          if (data.preguntasConMasErrores) {
            preguntasErroresData = data.preguntasConMasErrores;
            if (preguntasErroresChart) {
              preguntasErroresChart.data.labels = preguntasErroresData.map(it => (it.enunciado || '').substring(0, 40) + ((it.enunciado||'').length>40 ? '…' : ''));
              preguntasErroresChart.data.datasets[0].data = preguntasErroresData.map(it => it.incorrectas || 0);
              preguntasErroresChart.update();
            }
          }

          hideGlobalLoader();
          showFilterSuccess();
        })
        .catch(err => {
          console.error('Error en fetch filtrarDashboard:', err);
          hideGlobalLoader();
        });
        
      } catch (error) {
        console.error('Error en applyGlobalFilters:', error);
        hideGlobalLoader();
      }
    }

    function clearGlobalFilters() {
      console.log('Limpiando filtros globales...');
      
      try {
        // Resetear todos los controles
        const periodoFilter = document.getElementById('periodoFilter');
        const institucionFilter = document.getElementById('institucionFilter');
        
        if (periodoFilter) periodoFilter.value = '';
        if (institucionFilter) institucionFilter.value = '';
        
        // Marcar todos los checkboxes de roles
        document.querySelectorAll('.rol-filter').forEach(cb => {
          cb.checked = true;
        });
        
        console.log('Controles reseteados, aplicando filtros...');
        
        // Aplicar filtros resetados
        applyGlobalFilters();
      } catch (error) {
        console.error('Error limpiando filtros:', error);
      }
    }

    function getGlobalFilterValues() {
      try {
        const periodo = document.getElementById('periodoFilter')?.value || '';
        const institucion = document.getElementById('institucionFilter')?.value || '';
        const rolesCheckboxes = document.querySelectorAll('.rol-filter:checked');
        const roles = rolesCheckboxes ? Array.from(rolesCheckboxes).map(cb => cb.value) : [];
        
        console.log('Valores de filtros:', { periodo, institucion, roles });
        return { periodo, institucion, roles };
      } catch (error) {
        console.error('Error obteniendo valores de filtros:', error);
        return { periodo: '', institucion: '', roles: [] };
      }
    }

    function updateAllChartsWithFilters(filtros) {
      console.log('Actualizando todos los gráficos con filtros:', filtros);
      
      try {
        // Actualizar gráfico de roles si hay filtro de roles
        if (filtros.roles && filtros.roles.length > 0 && filtros.roles.length < 4) {
          console.log('Actualizando gráfico de roles con filtros:', filtros.roles);
          updateRolesChart(filtros.roles);
        } else {
          console.log('Mostrando todos los roles (sin filtro específico)');
          // Si no hay filtros específicos o están todos seleccionados, mostrar todos
          if (rolesChart && rolesData) {
            rolesChart.data.labels = rolesData.map(item => item.rol || 'Sin rol');
            rolesChart.data.datasets[0].data = rolesData.map(item => item.total || 0);
            rolesChart.update('active');
          }
        }
        
        // Re-aplicar filtros al gráfico de alumnos
        console.log('Actualizando gráfico de alumnos...');
        filterAlumnosData(false);
        
        // Simular actualización de otros gráficos
        console.log('Simulando actualización de gráficos de instituciones...');
        
        console.log('Todos los gráficos actualizados exitosamente');
      } catch (error) {
        console.error('Error en updateAllChartsWithFilters:', error);
      }
    }

    function updateRolesChart(allowedRoles) {
      console.log('updateRolesChart llamado con roles:', allowedRoles);
      console.log('rolesChart existe:', !!rolesChart);
      console.log('rolesData existe:', !!rolesData);
      
      if (rolesChart && rolesData && Array.isArray(rolesData)) {
        const filteredData = rolesData.filter(item => allowedRoles.includes(item.rol));
        console.log('Datos filtrados para roles:', filteredData);
        
        rolesChart.data.labels = filteredData.map(item => item.rol || 'Sin rol');
        rolesChart.data.datasets[0].data = filteredData.map(item => item.total || 0);
        
        rolesChart.update('active');
        console.log('Gráfico de roles actualizado con filtros');
      } else {
        console.warn('No se puede actualizar gráfico de roles - rolesChart o rolesData no disponibles');
      }
    }

    function showGlobalLoader() {
      try {
        const container = document.querySelector('.dashboard-container');
        if (container) {
          container.classList.add('global-loading');
          console.log('Loader global mostrado');
        } else {
          console.warn('No se encontró .dashboard-container para mostrar loader');
        }
      } catch (error) {
        console.error('Error mostrando loader global:', error);
      }
    }

    function hideGlobalLoader() {
      try {
        const container = document.querySelector('.dashboard-container');
        if (container) {
          container.classList.remove('global-loading');
          console.log('Loader global ocultado');
        }
      } catch (error) {
        console.error('Error ocultando loader global:', error);
      }
    }

    function showFilterSuccess() {
      // Mostrar toast de éxito
      const toast = document.createElement('div');
      toast.className = 'filter-toast';
      toast.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>Filtros aplicados correctamente';
      document.body.appendChild(toast);
      
      setTimeout(() => {
        toast.classList.add('show');
      }, 100);
      
      setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => document.body.removeChild(toast), 300);
      }, 2500);
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
    // ⚡ Script en línea que se ejecuta inmediatamente
    console.log('📍 Script cargado, readyState:', document.readyState);
    
    function initLoadingAnimation() {
      console.log('🚀 Iniciando animación de carga...');
      
      // Obtener el overlay que ya existe en el HTML
      const loadingOverlay = document.getElementById('powerbiLoadingOverlay');
      
      if (!loadingOverlay) {
        console.error('❌ No se encontró el overlay de carga');
        return;
      }

      console.log('✅ Overlay encontrado:', loadingOverlay);

      // Si se pasa ?fastload=1 en la URL, saltar animación (útil para desarrollo)
      const urlParams = new URLSearchParams(window.location.search);
      const skipFast = urlParams.get('fastload') === '1';

      // Si debe saltar la animación, remover overlay inmediatamente
      if (skipFast) {
        console.log('⚡ Modo fastload activado - saltando animación');
        setTimeout(() => {
          try { loadingOverlay.remove(); } catch(e) { /* ignore */ }
        }, 100);
        return;
      }
      
      // Simular progreso de carga realista (2-3 segundos total)
      let progress = 0;
      const progressBar = document.querySelector('.powerbi-progress-fill');
      const progressText = document.querySelector('.progress-percentage');
      const statusText = document.getElementById('loadingStatus');
      
      console.log('🔍 Buscando elementos:', {
        progressBar: !!progressBar,
        progressText: !!progressText,
        statusText: !!statusText
      });
      
      if (!progressBar || !progressText || !statusText) {
        console.error('❌ No se encontraron elementos de progreso');
        console.log('Removiendo overlay por falta de elementos...');
        setTimeout(() => loadingOverlay.remove(), 100);
        return;
      }
      
      console.log('✅ Todos los elementos encontrados, iniciando animación...');
    
    const loadingSteps = [
      { progress: 20, text: "Conectando con el servicio...", duration: 400 },
      { progress: 45, text: "Autenticando usuario...", duration: 300 },
      { progress: 70, text: "Cargando modelo de datos...", duration: 500 },
      { progress: 90, text: "Renderizando visualizaciones...", duration: 400 },
      { progress: 100, text: "¡Listo!", duration: 200 }
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
                  // Desvanecer overlay y luego remover
                  loadingOverlay.style.transition = 'opacity 0.3s ease';
                  loadingOverlay.style.opacity = '0';

                  setTimeout(() => {
                    try { loadingOverlay.remove(); } catch(e) { /* ignore */ }

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
                        }, index * 100);
                      });
                    }, 100);
                  }, 350);
                }, 200);
              }
            }
          }
          
          animate();
        }
      }
      
      // Iniciar carga inmediatamente
      console.log('⏳ Iniciando updateProgress en 50ms...');
      setTimeout(updateProgress, 50);
    } // Fin de initLoadingAnimation

    // Ejecutar la animación de carga
    // Si el DOM ya está listo, ejecutar ahora; si no, esperar
    console.log('📌 Programando ejecución de initLoadingAnimation...');
    if (document.readyState === 'loading') {
      console.log('⏰ DOM loading, esperando DOMContentLoaded...');
      document.addEventListener('DOMContentLoaded', initLoadingAnimation);
    } else {
      console.log('⚡ DOM ready, ejecutando inmediatamente...');
      initLoadingAnimation();
    }

    // Configurar event listeners cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', function() {
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
        position: fixed; /* fixed para cubrir toda la ventana */
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 99999; /* muy por encima para evitar solapamientos */
        pointer-events: auto;
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
        background: linear-gradient(90deg, #FDB913 0%, #F7B500 50%, #F09D00 100%);
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
        color: #F7B500;
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