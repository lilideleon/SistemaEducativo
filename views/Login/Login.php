<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sistema Educativo — Login</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

  <!-- Icons (opcional) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />

  <style>
    :root {
      --brand: #0d6efd; /* primary */
      --brand-2: #6ea8fe; /* primary-300 approx */
    }

    body {
      min-height: 100vh;
      background: radial-gradient(1200px 1200px at 80% -10%, rgba(80, 147, 138, 0.3), transparent),
                  radial-gradient(900px 900px at -10% 110%, rgba(80, 147, 138, 0.2), transparent),
                  linear-gradient(180deg, #50938a, #3a6b64);
      display: grid;
      place-items: center;
    }

    .login-card {
      width: 100%;
      max-width: 440px;
      margin: 0 auto;
      background-color: #dee7e5;
      border: 1px solid rgba(80, 147, 138, 0.2);
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1), 0 6px 20px rgba(0, 0, 0, 0.08);
      border-radius: 1.25rem;
    }

    .brand-badge {
      width: 56px; height: 56px;
      display: grid; place-items: center;
      border-radius: 14px;
      background: linear-gradient(135deg, var(--brand), var(--brand-2));
      color: #fff; font-weight: 700;
      box-shadow: 0 10px 25px rgba(13,110,253,.35);
    }

    .form-check-input:checked {
      background-color: var(--brand);
      border-color: var(--brand);
    }

    .btn-brand {
      background: #888484;
      color: #212529;
      border: 1px solid #dee2e6;
      transition: all 0.2s ease;
    }
    .btn-brand:hover {
      background: #dee2e6;
      color: #212529;
    }

    .input-group .form-control:focus { z-index: 3; }

    .footer-links a { color: #6c757d; text-decoration: none; }
    .footer-links a:hover { color: #0d6efd; text-decoration: underline; }
  </style>
</head>
<body>

  <main class="container d-flex align-items-center justify-content-center min-vh-100 py-4">
    <div class="card login-card p-3 p-sm-4 p-md-5">
      <div class="d-flex align-items-center gap-3 mb-3">
        <div class="brand-badge">
          <i class="bi bi-mortarboard-fill fs-4" aria-hidden="true"></i>
        </div>
        <div>
          <h1 class="h4 mb-1">Sistema Educativo</h1>
          <p class="text-secondary mb-0">Inicia sesión con tu cuenta</p>
        </div>
      </div>

      <form class="needs-validation" novalidate id="loginForm">
        <div class="mb-3">
          <label for="username" class="form-label">Usuario</label>
          <input type="text" class="form-control" id="username" name="username" placeholder="admin" required autocomplete="username" />
          <div class="invalid-feedback">Ingresa tu usuario.</div>
        </div>

        <div class="mb-2">
          <label for="password" class="form-label">Contraseña</label>
          <div class="input-group">
            <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required autocomplete="current-password" />
            <button class="btn btn-outline-secondary" type="button" id="togglePass" aria-label="Mostrar u ocultar contraseña">
              <i class="bi bi-eye" id="eyeIcon" aria-hidden="true"></i>
            </button>
            <div class="invalid-feedback">Ingresa tu contraseña.</div>
          </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" value="1" id="rememberMe" />
            <label class="form-check-label" for="rememberMe">Recordarme</label>
          </div>
          <a href="#" class="small">Olvidé mi contraseña</a>
        </div>

        <div class="d-grid gap-2">
          <button class="btn btn-brand btn-lg" type="submit">Ingresar</button>
        </div>
      </form>

      <div class="mt-4 text-center footer-links">
        <small>
          ¿No tienes cuenta? <a href="#">Solicitar acceso</a>
        </small>
      </div>
    </div>
  </main>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Manejo del formulario de login
    (() => {
      const form = document.getElementById('loginForm');
      form.addEventListener('submit', async (event) => {
        event.preventDefault();
        event.stopPropagation();

        if (form.checkValidity()) {
          const btn = form.querySelector('button[type="submit"]');
          const original = btn.innerHTML;
          
          try {
            // Mostrar estado de carga
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Ingresando...';

            // Realizar la petición al servidor
            const response = await fetch('?c=Login&a=Validate', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
              },
              body: new URLSearchParams({
                username: document.getElementById('username').value,
                password: document.getElementById('password').value
              })
            });

            const data = await response.json();
            
            // Redirigir al menú si la respuesta es exitosa
            if (data.success) {
              window.location.href = '?c=Menu';
            } else {
              // Mostrar mensaje de error si es necesario
              alert('Error en las credenciales');
            }
          } catch (error) {
            console.error('Error:', error);
            // En caso de error, redirigir de todas formas (según lo solicitado)
            window.location.href = '?c=Menu';
          } finally {
            btn.disabled = false;
            btn.innerHTML = original;
          }
        }
        
        form.classList.add('was-validated');
      }, false);
    })();

    // Mostrar / ocultar contraseña
    const toggle = document.getElementById('togglePass');
    const pass = document.getElementById('password');
    const eye = document.getElementById('eyeIcon');
    toggle.addEventListener('click', () => {
      const isText = pass.type === 'text';
      pass.type = isText ? 'password' : 'text';
      eye.classList.toggle('bi-eye');
      eye.classList.toggle('bi-eye-slash');
    });
  </script>
</body>
</html>
