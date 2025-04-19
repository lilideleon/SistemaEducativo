<!DOCTYPE html>
<html lang="es">
<head>
    <title>Login</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <link rel="stylesheet" href="./css/main.css">
</head>
<body>
   <style>
    .full-box.login-container.cover {
    position: relative; /* Haz que el div sea un contenedor posicionado */
    background-color: #E5E5E5;
}

.estado-cuenta-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: #FFFFFF;   /* Color blanco de fondo */
    color: #000000;              /* Color negro para el texto */
    border: none;                /* Remueve bordes por defecto */
    padding: 10px 20px;          /* Espaciado interno para que el botón no sea demasiado pequeño */
    border-radius: 8px;          /* Esquinas ligeramente redondeadas */
    cursor: pointer;             /* Cambia el cursor al pasar sobre el botón */
    transition: 0.3s; 
}

.estado-cuenta-btnn {
    position: absolute;
    top: 10px;
    right: 180px;
    background-color: #FFFFFF;   /* Color blanco de fondo */
    color: #000000;              /* Color negro para el texto */
    border: none;                /* Remueve bordes por defecto */
    padding: 10px 20px;          /* Espaciado interno para que el botón no sea demasiado pequeño */
    border-radius: 8px;          /* Esquinas ligeramente redondeadas */
    cursor: pointer;             /* Cambia el cursor al pasar sobre el botón */
    transition: 0.3s; 
}

   </style>
    <div class="full-box login-container cover">

        <form action="#" method="post" autocomplete="off" class="logInForm">
            <p class="text-center text-muted"><i class="zmdi zmdi-account-circle zmdi-hc-5x"></i></p>
            <p class="text-center text-muted text-uppercase">Inicia sesión con tu cuenta</p>
            <div class="form-group label-floating">
                <label class="control-label" for="UserName">Usuario</label>
                <input class="form-control" id="UserName" name="UserName" type="text" required="" style="color:#FFFFFF">
                <p class="help-block">Escribe tú nombre de usuario</p>
            </div>
            <div class="form-group label-floating">
                <label class="control-label" for="UserPass">Contraseña</label>
                <input class="form-control" id="UserPass" name="UserPass" type="password" required="" style="color:#FFFFFF">
                <p class="help-block">Escribe tú contraseña</p>
            </div>
            <div class="form-group text-center">
                <button type="button" class="btn btn-info" style="color: #FFF;" onclick="iniciarSesion()">Iniciar sesión</button>
            </div>
        </form>
    </div>



    <!--====== Scripts -->
    <script src="./js/jquery-3.1.1.min.js"></script>
    <script src="./js/bootstrap.min.js"></script>
    <script src="./js/material.min.js"></script>
    <script src="./js/ripples.min.js"></script>
    <script src="./js/sweetalert2.min.js"></script>
    <script src="./js/jquery.mCustomScrollbar.concat.min.js"></script>
    <script src="./js/main.js"></script>
    <script>
        $.material.init();
    </script>

<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.rtl.min.css"/>
        <!-- Default theme -->
        <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.rtl.min.css"/>
        <!-- Semantic UI theme -->
        <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/semantic.rtl.min.css"/>
        <!-- Bootstrap theme -->
        <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/bootstrap.rtl.min.css"/>
        
           <!-- JavaScript -->
           <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>

    <script>
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
                url: '?c=Login&a=Validate', // URL del método Validate en el controlador
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        // Redirigir al menú si el inicio de sesión es exitoso
                        alertify.success('Bienvenido al Sistema');
                        setTimeout(function () {
                            window.location.href = '?c=Menu';
                        }, 2000);
                    } else {
                        // Mostrar mensaje de error si las credenciales son incorrectas
                        alertify.error('Usuario o contraseña incorrectos');
                    }
                },
                error: function (xhr, status, error) {
                    // Manejar errores de la solicitud
                    alertify.error('Error al iniciar sesión: ' + error);
                }
            });
        }
    </script>
</body>
</html>
