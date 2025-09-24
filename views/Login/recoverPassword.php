<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Sistema Educativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #50938a, #3a6b64);
            min-height: 100vh;
        }
        .recovery-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }
        .form-control:focus {
            border-color: #50938a;
            box-shadow: 0 0 0 0.2rem rgba(80, 147, 138, 0.25);
        }
        .btn-recover {
            background-color: #50938a;
            border: none;
            padding: 12px;
            transition: all 0.3s ease;
        }
        .btn-recover:hover {
            background-color: #3a6b64;
            transform: translateY(-2px);
        }
        .instructions {
            background-color: rgba(80, 147, 138, 0.1);
            border-left: 4px solid #50938a;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 0 8px 8px 0;
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100 py-5">
        <div class="recovery-card p-4 p-md-5" style="max-width: 500px; width: 100%;">
            <div class="text-center mb-4">
                <i class="bi bi-key-fill fs-1 text-primary mb-3"></i>
                <h2 class="mb-3" style="color: #50938a;">Recuperar Contraseña</h2>
                <p class="text-muted">¿Olvidaste tu contraseña? No te preocupes, te ayudaremos a recuperarla.</p>
            </div>

            <div class="instructions mb-4">
                <h5 class="mb-3">Instrucciones:</h5>
                <ol class="mb-0">
                    <li>Ingresa tu correo electrónico registrado</li>
                    <li>Te enviaremos un código de verificación</li>
                    <li>Usa el código para crear una nueva contraseña</li>
                </ol>
            </div>

            <form method="POST" action="?c=Login&a=recoverPassword">
                <div class="mb-4">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" id="email" class="form-control" 
                               placeholder="usuario@ejemplo.com" required>
                    </div>
                    <div class="form-text">Ingresa el correo asociado a tu cuenta.</div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-recover btn-lg text-white">
                        Enviar Código de Recuperación
                    </button>
                    <a href="?c=Login" class="btn btn-light">Volver al Inicio de Sesión</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>