<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sistema Educativo — Bienvenido</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    :root {
      --brand: #0d6efd;
      --brand-2: #6ea8fe;
    }
    body {
      min-height: 100vh;
      background:
        /* Capa de oscurecimiento suave para legibilidad */
        linear-gradient(rgba(0,0,0,0.25), rgba(0,0,0,0.25)),
        /* Gradientes decorativos existentes */
        radial-gradient(1200px 1200px at 80% -10%, rgba(80, 147, 138, 0.25), transparent),
        radial-gradient(900px 900px at -10% 110%, rgba(80, 147, 138, 0.2), transparent),
        /* Imagen de fondo */
        url('img/fondo.jpeg') center / cover no-repeat fixed;
      /* Mantener gradientes centrados y bajar ligeramente la imagen */
      background-position: center center, center center, center center, center 80px;
    }
    .hero {
      padding: 5rem 0 3rem;
    }
    .badge-logo {
      width: 60px; height: 60px;
      display: grid; place-items: center;
      border-radius: 16px;
      background: linear-gradient(135deg, var(--brand), var(--brand-2));
      color: #fff; font-weight: 700;
      box-shadow: 0 10px 25px rgba(13,110,253,.25);
    }
    .feature-icon {
      width: 48px; height: 48px;
      display: grid; place-items: center;
      border-radius: 12px;
      background: #e9f2ff;
      color: var(--brand);
    }
    .btn-brand {
      background: var(--brand);
      color: #fff;
      border: none;
    }
    .btn-brand:hover { background: #0b5ed7; }
    .card-soft {
      border: 1px solid rgba(13,110,253,.1);
      background: #fff;
      box-shadow: 0 10px 30px rgba(0,0,0,.06);
    }
  </style>
</head>
<body>
  <header class="py-3 border-bottom bg-white">
    <div class="container d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-2">
        <div class="badge-logo">
          <i class="bi bi-mortarboard-fill fs-4" aria-hidden="true"></i>
        </div>
        <strong class="fs-5 mb-0">Sistema Educativo</strong>
      </div>
      <div>
        <a href="?c=Login" class="btn btn-outline-primary"><i class="bi bi-box-arrow-in-right me-1"></i> Ingresar</a>
      </div>
    </div>
  </header>

  <main class="hero">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-12 col-lg-6">
          <h1 class="display-5 fw-semibold mb-3">Bienvenido al Sistema Educativo</h1>
          <p class="lead text-secondary mb-4">
            Plataforma para gestión de usuarios, evaluaciones y materiales académicos.
            Accede con tu cuenta para comenzar.
          </p>
      
        </div>
       
      </div>

      <section id="institucional" class="mt-5 pt-4">
        <div class="row g-4">
          <div class="col-12 col-lg-6">
            <div class="card card-soft h-100 p-4">
              <div class="d-flex align-items-center gap-3 mb-2">
                <div class="feature-icon"><i class="bi bi-bullseye"></i></div>
                <h2 class="h5 mb-0">Misión</h2>
              </div>
              <p class="mb-0 text-secondary">
                Somos una institución evolutiva, organizada eficiente y eficaz, generadora de oportunidades de enseñanza-aprendizaje, orientada a resultados, que aprovecha diligentemente las oportunidades que el siglo XXI le brinda y comprometida con una Guatemala mejor.
              </p>
            </div>
          </div>
          <div class="col-12 col-lg-6">
            <div class="card card-soft h-100 p-4">
              <div class="d-flex align-items-center gap-3 mb-2">
                <div class="feature-icon"><i class="bi bi-eye"></i></div>
                <h2 class="h5 mb-0">Visión</h2>
              </div>
              <p class="mb-0 text-secondary">
                Formar ciudadanos con carácter, capaces por aprender por si mismos, orgullosos de ser guatemaltecos, empeñados en conseguir su desarrollo integral, con principios, valores y convicciones que fundamentan su conducta.
              </p>
            </div>
          </div>
          <div class="col-12">
            <div class="card card-soft h-100 p-4">
              <div class="d-flex align-items-center gap-3 mb-3">
                <div class="feature-icon"><i class="bi bi-stars"></i></div>
                <h2 class="h5 mb-0">Valores</h2>
              </div>
              <div class="row g-3">
                <div class="col-6 col-md-3">
                  <div class="border rounded p-3 text-center">Honestidad</div>
                </div>
                <div class="col-6 col-md-3">
                  <div class="border rounded p-3 text-center">Justicia</div>
                </div>
                <div class="col-6 col-md-3">
                  <div class="border rounded p-3 text-center">Respeto</div>
                </div>
                <div class="col-6 col-md-3">
                  <div class="border rounded p-3 text-center">Paz</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </main>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
