<?php 
   // Validación de autenticación y permisos de docentes y administradores
   require_once 'core/AuthValidation.php';
   validarRol(['ADMIN', 'DOCENTE', 'DIRECTOR']); // Solo docentes y administradores pueden ver reportes
   
   include 'views/Menu/Aside.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sistema Educativo</title>

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


    :root{
      --bg-teal:#4f8f8a; --sidebar-header:#a8c0bb; --line:#2e655f; --title:#235c9c; --ink:#173a38;
    }
    html,body{height:100%;margin:0}
    body{background:var(--bg-teal);display:flex;flex-direction:column}
    .app-wrapper{display:flex;flex:1;height:100vh}

    /* Sidebar */
    .sidebar{
      width:260px;height:100vh;position:fixed;left:0;top:0;overflow-y:auto;
      background:#50938a;padding:1rem;box-shadow:2px 0 5px rgba(0,0,0,.1);z-index:1000;
    }
    .sidebar-box{background:rgba(255,255,255,.1);border-radius:.5rem;padding:.75rem}
    .sidebar-header{background:var(--sidebar-header);color:#1f2937;font-weight:600;
      padding:.5rem .75rem;border-radius:.35rem;margin-bottom:.75rem}
    .link-glow{
      display:block;text-decoration:none;color:#fff;font-weight:700;border-radius:1rem;
      padding:.9rem 1.1rem;text-align:left;
      background:
        radial-gradient(140% 120% at 50% 40%, rgba(255,255,255,.28) 0%, rgba(255,255,255,.08) 42%, rgba(0,0,0,.22) 75%),
        linear-gradient(180deg,#0f1c2e 0%,#1f3554 100%);
      box-shadow:0 8px 18px rgba(0,0,0,.25), inset 0 -2px 0 rgba(255,255,255,.1);
      transition:transform .12s ease, filter .12s ease;
    }
    .link-glow:hover{transform:translateY(-2px);filter:brightness(1.03)}
    .link-glow.active{outline:2px solid rgba(255,255,255,.35)}

    /* Contenido */
    section.main-content{
      margin-left:260px;width:calc(100% - 260px);
      padding:2rem;min-height:100vh;display:flex;flex-direction:column;
    }
    .content-panel{
      flex:1;display:flex;flex-direction:column;min-height:0;
      background:#fff;border-radius:.5rem;padding:1.25rem;
      box-shadow:0 .125rem .25rem rgba(0,0,0,.075);
    }
    .page-title{
      background:#fff;color:var(--title);font-weight:700;border-radius:.25rem;
      display:inline-block;padding:.35rem 1.25rem;box-shadow:0 2px 0 rgba(0,0,0,.25) inset;
    }

    /* Cintillo “Power BI” */
    .gwrap{border:2px solid var(--line);border-radius:.5rem;background:rgba(79,143,138,.15);padding:1rem}
    .divider-foot{height:10px;background:var(--line);border-bottom-left-radius:.5rem;border-bottom-right-radius:.5rem}

    /* Filtros */
    .filters{gap: .75rem}
    .filters .form-select{min-width:220px}
    .filters a{ text-decoration:none }

    /* KPIs */
    .kpi{
      background:#f8fafb;border:1px solid #e8eef0;border-radius:.75rem;padding:1rem;
      box-shadow:0 1px 2px rgba(0,0,0,.05); height:100%;
    }
    .kpi-title{font-size:.9rem;color:#506b69;margin-bottom:.25rem}
    .kpi-value{font-size:1.6rem;font-weight:800;color:#0d2b2a;line-height:1}
    .kpi-diff{font-size:.85rem}

    /* Tabla estilo */
    .table thead th{white-space:nowrap;background:#e9f2f1}
    .table-wrap{max-height:260px;overflow:auto;border:1px solid #e8eef0;border-radius:.5rem}
    .tag-reset{
      display:inline-block;border:1px dashed #6c9ea0;border-radius:.35rem;padding:.25rem .5rem;color:#134c4a;
    }

    @media (max-width: 767.98px){
      .sidebar{display:none}
      section.main-content{margin-left:0 !important;width:100% !important;padding:1rem !important}
    }
  </style>
</head>
<body>

  <!-- toggle móvil -->
  <div class="container d-md-none my-2">
    <a href="#" class="btn btn-light" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar">
      <i class="bi bi-list"></i> Menú
    </a>
  </div>

  <main class="app-wrapper">


    <!-- Contenido -->
    <section class="main-content">
      <header class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="page-title">Sistema Educativo</h1>
        <div>
          <a href="?c=Reportes&a=usuarios" class="btn btn-primary me-2">
            <i class="bi bi-people"></i> Reporte de Usuarios
          </a>
          <a href="?c=Login&a=Logout" class="btn btn-outline-light btn-sm" title="Cerrar Sesión">
            <i class="bi bi-box-arrow-right"></i> Salir
          </a>
        </div>
      </header>

      <div class="content-panel">
        <div class="gwrap">
   
        



          <div class="divider-foot mt-3"></div>
        </div>
      </div>
    </section>
  </main>
</body>
</html>
