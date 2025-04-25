<?php
require 'views/Content/header.php';
?>

<?php
if ($_SESSION['TipoUsuario'] == '') {
    print "<script>
        window.location='?c=Login'; 
    </script>";
} else if ($_SESSION['TipoUsuario'] == 1) {
    require 'views/Content/sidebar.php';
} else if ($_SESSION['TipoUsuario'] == 2) {
    require 'views/Content/sidebar2.php';
}
?>

<style>
    .move-left {
        position: relative; 
        left: 0; 
    }

    .move-down {
        margin-top: 15px;
    }

    .make-larger {
        width: 100%; 
        max-width: 1950px; /* Permite crecer hasta 1950px, pero no más que eso */
        height: auto; /* Se ajusta según el contenido */
        max-height: 800px; /* Altura máxima de 800px */
    }

    /* Estilos para pantallas con un ancho mínimo de 1024px (ejemplo para pantallas grandes) */
    @media (min-width: 1024px) {
        .move-left {
            left: 28px;
        }
    }
</style>

<div class="content-wrapper">
    <div class="row move-left move-down make-larger">
        <div class="col-12 col-md-6 col-lg-11">
            <div class="card mb-4">
                <center>
                    <h3 class="card-header">Opciones</h3>
                </center>
                <div class="card-body demo-vertical-spacing demo-only-element">

                <!-- card de opcion para realizar backup -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Realizar Backup</h5>
                        <!-- elegir la ruta donde se guardara el backup -->
                        <div >
                            <div class="custom-file">
                            <input type="text" id="ruta" name="ruta" placeholder="C:/eli/backups/" style="width: 30%; padding: 8px; margin-top: 5px;" disabled value="C:/eli/backups/">
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="realizarBackup()">Realizar Backup</button>
                    </div>
                </div>
   
                <!-- card de opcion para cambiar el tema del sistema -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Cambiar Tema del Sistema</h5>
                        <p class="card-text">Cambia el tema del sistema.</p>
                        <!-- elegir paleta de colores -->
                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-3">
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5 class="card-title">Tema 1</h5>
                                        <p class="card-text">Tema 1</p>
                                        <button type="button" class="btn btn-primary">Seleccionar</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-3">
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5 class="card-title">Tema 2</h5>
                                        <p class="card-text">Tema 2</p>
                                        <button type="button" class="btn btn-primary">Seleccionar</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-3">
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5 class="card-title">Tema 3</h5>
                                        <p class="card-text">Tema 3</p>
                                        <button type="button" class="btn btn-primary">Seleccionar</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-3">
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5 class="card-title">Tema 4</h5>
                                        <p class="card-text">Tema 4</p>
                                        <button type="button" class="btn btn-primary">Seleccionar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function realizarBackup() {

        //se desactiva el boton hasta que termine el backup
        $("#realizarBackup").prop("disabled", true);
       
        //validar si la ruta es accesible
        var ruta = $("#ruta").val();

        if (ruta == "") {
            alertify.error("Debe especificar una ruta para el backup");
            return;
        }
        else{
            $.ajax({
                url: "?c=Opciones&a=realizarBackup",
                type: "POST",
                data: { ruta: ruta },
                success: function(response) {
                    try {
                        var data = JSON.parse(response); // 👈 convierte la respuesta en objeto
                        console.log("Success:", data.success);
                        console.log("Mensaje:", data.msj);

                        if (data.success) {
                            alertify.success(data.msj);
                        } else {
                            alertify.error(data.msj);
                        }
                    } catch (e) {
                        alertify.error("Respuesta inválida del servidor");
                        console.error("Error al parsear JSON:", e);
                    } finally {
                        $("#realizarBackup").prop("disabled", false);
                    }
                },
                error: function(xhr, status, error) {
                    alertify.error("Error al realizar el backup: " + error);
                    $("#realizarBackup").prop("disabled", false);
                }
            });

        }


    }
</script>

<?php
require 'views/Content/footer.php';
?>