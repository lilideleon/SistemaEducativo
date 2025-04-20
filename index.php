<?
	
	require_once "config/config.php";
	require_once "core/routes.php";
	require_once "config/database.php";
	require_once "controllers/Login.php";
	require_once "controllers/Menu.php";
	require_once "controllers/Usuarios.php";
	require_once "controllers/Productos.php";
	require_once "controllers/ventas.php";
	require_once "controllers/Notificaciones.php";
	require_once "controllers/Multas.php";
	require_once "controllers/Asistencia.php";
	require_once "controllers/caja.php";
	require_once "controllers/Categorias.php";
	require_once "controllers/Despieces.php";
	
	
	if(isset($_GET['c']))
	{
		$controlador = cargarControlador($_GET['c']);
		
		if(isset($_GET['a']))
		{
			if(isset($_GET['id']))
			{
				cargarAccion($controlador, $_GET['a'], $_GET['id']);
			} 
			else 
			{
				cargarAccion($controlador, $_GET['a']);
			}
		} 
		else 
		{
			cargarAccion($controlador, ACCION_PRINCIPAL);
		}
		
	} 
	else 
	{	
		$controlador = cargarControlador(CONTROLADOR_PRINCIPAL);
		$accionTmp = ACCION_PRINCIPAL;
		$controlador->$accionTmp();
	}
?>