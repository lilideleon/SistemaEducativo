<? 
    require 'views/Content/header.php';
    session_start();
?>


<?php 
                           
    if($_SESSION['TipoUsuario'] == '')
    {
        print "<script>
            window.location='?c=Login'; 
            </script>";
    }
    else if($_SESSION['TipoUsuario'] == 1)
    {
        require 'views/Content/sidebar.php'; 
            print "<script>
                console.log($TipoUsuario);
            </script>";
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

    #TablaMeses tr {
        height: 15px;  /* O el alto que prefieras */
    }

    #TablaMeses td, #TablaMeses th {
        padding: 5px 10px;  /* Ajusta como prefieras */
    }


    #tablaDetalles tr {
        height: 15px;  /* O el alto que prefieras */
    }

    #tablaDetalles td, #tablaDetalles th {
        padding: 5px 10px;  /* Ajusta como prefieras */
    }

</style>


<style>
    #productosContainer::-webkit-scrollbar {
        width: 8px;
    }

    #productosContainer::-webkit-scrollbar-track {
        background: transparent;
    }

    #productosContainer::-webkit-scrollbar-thumb {
        background-color: #90caf9; /* color celeste tipo Material */
        border-radius: 10px;
        border: 2px solid transparent;
        background-clip: content-box;
    }

    #productosContainer:hover::-webkit-scrollbar-thumb {
        background-color: #42a5f5; /* color más fuerte al pasar el mouse */
    }

    #productosContainer {
        scrollbar-width: thin;
        scrollbar-color: #90caf9 transparent; /* Firefox */
    }
</style>



<div class="content-wrapper">
    <div class="row move-left move-down make-larger">
        <!-- Sección de categorías y productos -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>🍗 Menú de Pollo Frito</h5>
                </div>
                <div class="card-body">
                    <!-- Categorías -->
                    <div class="row mb-4">
                        <!-- Categorías
                        <button class="btn btn-primary col-md-3 mx-2">categorias</button>  -->
                 
                    </div>
                    <!-- Productos -->
                   <div id="productosContainer" class="row" 
                        style="max-height: 450px; overflow-y: auto; padding: 10px; border: 1px solid #ccc; border-radius: 8px;">
                        <!-- Products will be dynamically loaded here -->
                    </div>

                    
                </div>
            </div>
        </div>

        <!-- Sección de lista de órdenes -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>📋 Lista de Órdenes</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Precio</th>
                                <th>Cant</th>
                                <th>Total (Q)</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody id="listaOrdenes">
                           
                        </tbody>
                    </table>

                     <!-- TOTAL GENERAL AQUÍ -->
                    <div class="text-end my-3">
                        <h4>Total General: Q <span id="totalGeneral">0.00</span></h4>
                    </div>

                    <div class="text-right">
                        <button class="btn btn-success" onclick="finalizarOrden()">Finalizar Orden</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    
    document.addEventListener('DOMContentLoaded', function() {
        obtenerProductos();
    });
</script>

<script src="js/quagga.min.js"></script>
<script type="text/javascript" src="js/Custom/Ventas.js"></script> 

<?
    require 'views/Content/footer.php';

?>