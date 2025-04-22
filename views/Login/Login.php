<!DOCTYPE html>
<html lang="es">
<head>
    <title>Login</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <link rel="stylesheet" href="./css/main.css">
    <!-- Alertify local CSS -->
    <link rel="stylesheet" href="res/plugins/Alertify/css/alertify.min.css"/>
    <link rel="stylesheet" href="res/plugins/Alertify/css/alertify.rtl.min.css"/>
    <link rel="stylesheet" href="res/plugins/Alertify/css/themes/default.rtl.min.css"/>
    <link rel="stylesheet" href="res/plugins/Alertify/css/themes/semantic.rtl.min.css"/>
    <link rel="stylesheet" href="res/plugins/Alertify/css/themes/bootstrap.rtl.min.css"/>
    <style>
    body {
        background: #fff !important;
        color: #000 !important;
    }
    .full-box.login-container.cover {
        position: relative;
        background-color: #fff;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .login-form-box {
        background: #f9f9f9;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
        border-radius: 16px;
        padding: 40px 30px 30px 30px;
        max-width: 400px;
        width: 100%;
        margin: 40px auto;
        z-index: 2;
        color: #000;
        min-height: 520px; /* Aumenta el alto mínimo del contenedor */
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .login-form-box label,
    .login-form-box p,
    .login-form-box .form-control {
        color: #000 !important;
    }
    .form-control::placeholder {
        color: #888 !important;
        opacity: 1;
    }
    .btn-info {
        background-color: #17a2b8;
        border: none;
    }
    .btn-info:hover {
        background-color: #138496;
    }
    </style>
</head>
<body>
    <div class="full-box login-container cover">
        <div class="login-form-box">
            <form action="#" method="post" autocomplete="off" class="logInForm">
                <div class="text-center mb-3">
                    <img src="img/logo.png" alt="Logo" style="max-width:120px; max-height:120px; margin-bottom:10px;">
                </div>
                <p class="text-center text-muted text-uppercase">Inicia sesión con tu cuenta</p>
                <div class="form-group label-floating">
                    <label class="control-label" for="UserName">Usuario</label>
                    <input class="form-control" id="UserName" name="UserName" type="text" required placeholder="Usuario">
                    <p class="help-block">Escribe tú nombre de usuario</p>
                </div>
                <div class="form-group label-floating">
                    <label class="control-label" for="UserPass">Contraseña</label>
                    <input class="form-control" id="UserPass" name="UserPass" type="password" required placeholder="Contraseña">
                    <p class="help-block">Escribe tú contraseña</p>
                </div>
                <div class="form-group text-center">
                    <button type="button" class="btn btn-info" style="color: #FFF;" onclick="iniciarSesion()">Iniciar sesión</button>
                </div>
            </form>
        </div>
    </div>

    <!--====== Scripts: Core JS -->
    <script src="./js/jquery-3.1.1.min.js"></script>
    <script src="./js/bootstrap.min.js"></script>
    <script src="./js/material.min.js"></script>
    <script src="./js/ripples.min.js"></script>
    <script src="./js/sweetalert2.min.js"></script>
    <script src="./js/jquery.mCustomScrollbar.concat.min.js"></script>
    <script src="./js/main.js"></script>
    <!-- Alertify local JS -->
    <script src="res/plugins/Alertify/alertify.min.js"></script>
    <script>
        $.material.init();
        function iniciarSesion() {
            // Obtener los datos del formulario
            var formData = {
                UserName: $('#UserName').val(),
                UserPass: $('#UserPass').val()
            };
            // Validar que los campos no estén vacíos
            if (!formData.UserName || !formData.UserPass) {
                alertify.error('Por favor, completa todos los campos');
                return;
            }
            // Enviar los datos al controlador mediante AJAX
            $.ajax({
                url: '?c=Login&a=Validate',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alertify.success('Bienvenido al Sistema');
                        setTimeout(function () {
                            window.location.href = '?c=Menu';
                        }, 2000);
                    } else {
                        alertify.error('Usuario o contraseña incorrectos');
                    }
                },
                error: function (xhr, status, error) {
                    alertify.error('Error al iniciar sesión: ' + error);
                }
            });
        }
    </script>
</body>
</html>
