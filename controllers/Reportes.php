<?php
require_once 'models/Reportes.php';
require_once 'FPDF/fpdf.php';

class ReportesController {
    private $modelo;

    public function __construct() {
        @session_start();
        require_once "core/AuthValidation.php";
        validarRol(['ADMIN','DIRECTOR','DOCENTE']); // Permitir también a docentes
        
        // Asegurar que la zona horaria esté configurada
        if (!ini_get('date.timezone')) {
            date_default_timezone_set('America/El_Salvador');
        }
        
        $this->modelo = new ReportesModel();
    }

    public function index() {
        try {
            $data = [
                'usuarios' => $this->modelo->obtenerUsuarios(),
                'instituciones' => $this->modelo->obtenerInstituciones(),
                'grados' => $this->modelo->obtenerGrados()
            ];
            // Extraer variables para la vista
            extract($data);
            require_once "views/Reportes/Reportes.php";
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function usuarios() {
        try {
            $data = [
                'usuarios' => $this->modelo->obtenerUsuarios(),
                'instituciones' => $this->modelo->obtenerInstituciones(),
                'grados' => $this->modelo->obtenerGrados()
            ];
            // Extraer variables para la vista
            extract($data);
            require_once "views/Reportes/usuarios.php";
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function filtrarUsuarios() {
        try {
            $filtros = [
                'rol' => isset($_POST['rol']) ? $_POST['rol'] : '',
                'institucion_id' => isset($_POST['institucion_id']) ? $_POST['institucion_id'] : '',
                'grado_id' => isset($_POST['grado_id']) ? $_POST['grado_id'] : '',
                'activo' => isset($_POST['activo']) ? $_POST['activo'] : '',
                'busqueda' => isset($_POST['busqueda']) ? $_POST['busqueda'] : ''
            ];

            // Procesar rango de fechas
            if (!empty($_POST['fecha_rango'])) {
                $fechas = explode(' - ', $_POST['fecha_rango']);
                if (count($fechas) == 2) {
                    $filtros['fecha_inicio'] = date('Y-m-d', strtotime(str_replace('/', '-', $fechas[0])));
                    $filtros['fecha_fin'] = date('Y-m-d', strtotime(str_replace('/', '-', $fechas[1])));
                }
            }

            $data = [
                'usuarios' => $this->modelo->obtenerUsuarios($filtros),
                'instituciones' => $this->modelo->obtenerInstituciones(),
                'grados' => $this->modelo->obtenerGrados()
            ];
            // Extraer variables para la vista
            extract($data);
            require_once "views/Reportes/usuarios.php";
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function exportarUsuarios() {
        $formato = isset($_GET['formato']) ? $_GET['formato'] : 'pdf';
        $filtros = [
            'rol' => isset($_GET['rol']) ? $_GET['rol'] : '',
            'institucion_id' => isset($_GET['institucion_id']) ? $_GET['institucion_id'] : '',
            'grado_id' => isset($_GET['grado_id']) ? $_GET['grado_id'] : '',
            'activo' => isset($_GET['activo']) ? $_GET['activo'] : '',
            'busqueda' => isset($_GET['busqueda']) ? $_GET['busqueda'] : ''
        ];

        if (!empty($_GET['fecha_rango'])) {
            $fechas = explode(' - ', $_GET['fecha_rango']);
            if (count($fechas) == 2) {
                $filtros['fecha_inicio'] = date('Y-m-d', strtotime(str_replace('/', '-', $fechas[0])));
                $filtros['fecha_fin'] = date('Y-m-d', strtotime(str_replace('/', '-', $fechas[1])));
            }
        }

        $usuarios = $this->modelo->obtenerUsuarios($filtros);

        if ($formato === 'pdf') {
            $this->exportarPDF($usuarios);
        } else {
            $this->exportarExcel($usuarios);
        }
    }

    private function exportarPDF($usuarios) {
        $pdf = new FPDF('L', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 14);
        
        // Título
        $pdf->Cell(0, 10, 'Reporte de Usuarios', 0, 1, 'C');
        $pdf->Ln(5);

        // Encabezados
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(30, 7, utf8_decode('Código'), 1);
        $pdf->Cell(50, 7, 'Nombres', 1);
        $pdf->Cell(50, 7, 'Apellidos', 1);
        $pdf->Cell(25, 7, 'Rol', 1);
        $pdf->Cell(50, 7, utf8_decode('Institución'), 1);
        $pdf->Cell(30, 7, 'Grado', 1);
        $pdf->Cell(20, 7, utf8_decode('Sección'), 1);
        $pdf->Cell(20, 7, 'Estado', 1);
        $pdf->Ln();

        // Datos
        $pdf->SetFont('Arial', '', 9);
        foreach($usuarios as $usuario) {
            $pdf->Cell(30, 6, $usuario->codigo, 1);
            $pdf->Cell(50, 6, utf8_decode($usuario->nombres), 1);
            $pdf->Cell(50, 6, utf8_decode($usuario->apellidos), 1);
            $pdf->Cell(25, 6, $usuario->rol, 1);
            $pdf->Cell(50, 6, utf8_decode(isset($usuario->institucion_nombre) ? $usuario->institucion_nombre : '-'), 1);
            $pdf->Cell(30, 6, utf8_decode(isset($usuario->grado_nombre) ? $usuario->grado_nombre : '-'), 1);
            $pdf->Cell(20, 6, $usuario->seccion ? $usuario->seccion : '-', 1);
            $pdf->Cell(20, 6, $usuario->activo ? 'Activo' : 'Inactivo', 1);
            $pdf->Ln();
        }

        $pdf->Output('Reporte_Usuarios.pdf', 'D');
    }

    private function exportarExcel($usuarios) {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="Reporte_Usuarios.xls"');
        
        echo '
        <table border="1">
            <tr>
                <th colspan="8" style="text-align: center; font-size: 16px; background-color: #4F81BD; color: white;">
                    Reporte de Usuarios
                </th>
            </tr>
            <tr style="background-color: #D0D8E8;">
                <th>Código</th>
                <th>Nombres</th>
                <th>Apellidos</th>
                <th>Rol</th>
                <th>Institución</th>
                <th>Grado</th>
                <th>Sección</th>
                <th>Estado</th>
            </tr>';
        
        foreach($usuarios as $usuario) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($usuario->codigo) . '</td>';
            echo '<td>' . htmlspecialchars($usuario->nombres) . '</td>';
            echo '<td>' . htmlspecialchars($usuario->apellidos) . '</td>';
            echo '<td>' . htmlspecialchars($usuario->rol) . '</td>';
            echo '<td>' . htmlspecialchars(isset($usuario->institucion_nombre) ? $usuario->institucion_nombre : '-') . '</td>';
            echo '<td>' . htmlspecialchars(isset($usuario->grado_nombre) ? $usuario->grado_nombre : '-') . '</td>';
            echo '<td>' . ($usuario->seccion ? $usuario->seccion : '-') . '</td>';
            echo '<td>' . ($usuario->activo ? 'Activo' : 'Inactivo') . '</td>';
            echo '</tr>';
        }
        
        echo '</table>';
        exit;
    }

    public function dashboard() {
        try {
            error_log("=== CONTROLADOR: Dashboard iniciado ===");
            $cursosGrados = $this->modelo->obtenerCursosYGrados();
            
            // Obtener mejoresAlumnos con debugging
            error_log("CONTROLADOR: Obteniendo mejores alumnos...");
            $mejoresAlumnos = $this->modelo->obtenerMejoresAlumnos();
            error_log("CONTROLADOR: Mejores alumnos obtenidos: " . json_encode($mejoresAlumnos));
            
            $data = [
                'totalUsuarios' => $this->modelo->contarUsuarios(),
                'totalInstituciones' => $this->modelo->contarInstituciones(),
                'totalEncuestas' => $this->modelo->contarEncuestas(),
                'totalCalificaciones' => $this->modelo->contarCalificaciones(),
                'usuariosPorRol' => $this->modelo->obtenerUsuariosPorRol(),
                'institucionesPorDistrito' => $this->modelo->obtenerInstitucionesPorDistrito(),
                'encuestasPorEstado' => $this->modelo->obtenerEncuestasPorEstado(),
                'promedioCalificaciones' => $this->modelo->obtenerPromedioCalificaciones(),
                'promediosPorInstitucion' => $this->modelo->obtenerPromediosPorInstitucion(),
                'mejoresAlumnos' => $mejoresAlumnos,
                'cursos' => $cursosGrados['cursos'],
                'grados' => $cursosGrados['grados'],
                'actividadReciente' => $this->modelo->obtenerActividadReciente()
            ];
            
            error_log("CONTROLADOR: Data completa preparada, mejoresAlumnos count: " . count($mejoresAlumnos));
            
            // Extraer variables para la vista
            extract($data);
            require_once "views/Reportes/Dashboard.php";
        } catch (Exception $e) {
            error_log("ERROR en dashboard: " . $e->getMessage());
            echo "Error: " . $e->getMessage();
        }
    }

    // Nuevo endpoint para filtrar datos del dashboard via AJAX
    public function filtrarDashboard() {
        try {
            // Sólo aceptar peticiones POST
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
                return;
            }

            // Leer filtros desde POST
            $filtros = [];
            $filtros['periodo'] = isset($_POST['periodo']) ? trim($_POST['periodo']) : '';
            $filtros['institucion_id'] = isset($_POST['institucion_id']) && $_POST['institucion_id'] !== '' ? intval($_POST['institucion_id']) : '';
            $filtros['roles'] = isset($_POST['roles']) ? $_POST['roles'] : [];
            // Si roles viene como JSON string, decodificarlo
            if (is_string($filtros['roles'])) {
                $decodedRoles = json_decode($filtros['roles'], true);
                if (is_array($decodedRoles)) {
                    $filtros['roles'] = $decodedRoles;
                } else {
                    $filtros['roles'] = [];
                }
            }
            $filtros['curso_id'] = isset($_POST['curso_id']) && $_POST['curso_id'] !== '' ? intval($_POST['curso_id']) : '';
            $filtros['grado_id'] = isset($_POST['grado_id']) && $_POST['grado_id'] !== '' ? intval($_POST['grado_id']) : '';

            // Si periodo viene en formato rango 'dd/mm/yyyy - dd/mm/yyyy', convertir a fechas (para filtros que lo requieran)
            if (!empty($filtros['periodo']) && strpos($filtros['periodo'], ' - ') !== false) {
                $fechas = explode(' - ', $filtros['periodo']);
                if (count($fechas) == 2) {
                    $filtros['fecha_inicio'] = date('Y-m-d', strtotime(str_replace('/', '-', $fechas[0])));
                    $filtros['fecha_fin'] = date('Y-m-d', strtotime(str_replace('/', '-', $fechas[1])));
                }
            }

            // Llamar a los métodos del modelo. Algunos métodos no aceptan filtros actualmente,
            // por lo que se hará una llamada estándar. Para obtener datos filtrados por curso/grado
            // en mejoresAlumnos, se ha adaptado el modelo para usar los filtros si están presentes.

            $mejoresAlumnos = $this->modelo->obtenerMejoresAlumnos($filtros);
            $promedios = $this->modelo->obtenerPromediosPorInstitucion($filtros);
            $roles = $this->modelo->obtenerUsuariosPorRol($filtros);
            $distritos = $this->modelo->obtenerInstitucionesPorDistrito($filtros);

            $response = [
                'mejoresAlumnos' => $mejoresAlumnos,
                'promediosPorInstitucion' => $promedios,
                'usuariosPorRol' => $roles,
                'institucionesPorDistrito' => $distritos
            ];

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($response);
        } catch (Exception $e) {
            http_response_code(500);
            error_log('ERROR filtrarDashboard: ' . $e->getMessage());
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function impresos() {
        try {
            // Obtener instituciones para los filtros
            $instituciones = $this->modelo->obtenerInstituciones();
            
            // Extraer variable para la vista
            extract(['instituciones' => $instituciones]);
            
            // Cargar la vista de reportes impresos
            require_once "views/Reportes/Impresos.php";
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    // ==================== GENERACIÓN DE REPORTES PDF ====================

    public function generarPDFUsuarios() {
        try {
            // Obtener filtros de la URL
            $filtros = [
                'rol' => isset($_GET['rol']) ? $_GET['rol'] : '',
                'institucion_id' => isset($_GET['institucion_id']) ? $_GET['institucion_id'] : '',
                'grado_id' => isset($_GET['grado_id']) ? $_GET['grado_id'] : '',
                'activo' => isset($_GET['activo']) ? $_GET['activo'] : '1'
            ];

            // Obtener usuarios
            $usuarios = $this->modelo->obtenerUsuarios($filtros);

            // Crear PDF con diseño profesional
            $pdf = new PDF_Usuarios();
            $pdf->SetTitle('Reporte de Usuarios');
            $pdf->SetAuthor('Sistema Educativo');
            $pdf->AliasNbPages();
            $pdf->AddPage('L'); // Landscape para más espacio
            
            // Información de filtros aplicados
            $pdf->SetFont('Arial', '', 9);
            $pdf->SetTextColor(100, 100, 100);
            $fechaActual = date('d/m/Y H:i:s');
            $pdf->Cell(0, 5, 'Fecha de generacion: ' . $fechaActual, 0, 1, 'L');
            $pdf->Cell(0, 5, 'Total de registros: ' . count($usuarios), 0, 1, 'L');
            $pdf->Ln(5);

            // Encabezados de tabla con color verde
            $pdf->SetFillColor(17, 120, 103); // Verde del sistema
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetDrawColor(17, 120, 103);
            $pdf->SetLineWidth(0.3);
            $pdf->SetFont('Arial', 'B', 9);

            // Anchos de columnas
            $w = array(25, 40, 40, 25, 55, 30, 20, 20);
            
            $headers = array('Codigo', 'Nombres', 'Apellidos', 'Rol', 'Institucion', 'Grado', 'Seccion', 'Estado');
            for($i = 0; $i < count($headers); $i++) {
                $pdf->Cell($w[$i], 7, utf8_decode($headers[$i]), 1, 0, 'C', true);
            }
            $pdf->Ln();

            // Datos con colores alternados
            $pdf->SetFillColor(240, 255, 250); // Verde muy claro
            $pdf->SetTextColor(50, 50, 50);
            $pdf->SetFont('Arial', '', 8);

            $fill = false;
            foreach($usuarios as $usuario) {
                $pdf->Cell($w[0], 6, utf8_decode($usuario->codigo), 'LR', 0, 'L', $fill);
                $pdf->Cell($w[1], 6, utf8_decode(substr($usuario->nombres, 0, 25)), 'LR', 0, 'L', $fill);
                $pdf->Cell($w[2], 6, utf8_decode(substr($usuario->apellidos, 0, 25)), 'LR', 0, 'L', $fill);
                $pdf->Cell($w[3], 6, utf8_decode($usuario->rol), 'LR', 0, 'C', $fill);
                $institucion = isset($usuario->institucion_nombre) ? substr($usuario->institucion_nombre, 0, 35) : '-';
                $pdf->Cell($w[4], 6, utf8_decode($institucion), 'LR', 0, 'L', $fill);
                $grado = isset($usuario->grado_nombre) ? substr($usuario->grado_nombre, 0, 20) : '-';
                $pdf->Cell($w[5], 6, utf8_decode($grado), 'LR', 0, 'L', $fill);
                $pdf->Cell($w[6], 6, $usuario->seccion ? utf8_decode($usuario->seccion) : '-', 'LR', 0, 'C', $fill);
                $pdf->Cell($w[7], 6, $usuario->activo ? 'Activo' : 'Inactivo', 'LR', 0, 'C', $fill);
                $pdf->Ln();
                $fill = !$fill;
            }

            // Línea de cierre
            $pdf->Cell(array_sum($w), 0, '', 'T');

            // Output
            $pdf->Output('D', 'Reporte_Usuarios_' . date('Ymd_His') . '.pdf');

        } catch (Exception $e) {
            echo "Error al generar PDF: " . $e->getMessage();
        }
    }

    public function generarPDFInstituciones() {
        try {
            // Obtener instituciones
            $instituciones = $this->modelo->obtenerInstituciones();

            // Crear PDF con diseño profesional
            $pdf = new PDF_Instituciones();
            $pdf->SetTitle('Reporte de Instituciones');
            $pdf->SetAuthor('Sistema Educativo');
            $pdf->AliasNbPages();
            $pdf->AddPage();
            
            // Información de generación
            $pdf->SetFont('Arial', '', 9);
            $pdf->SetTextColor(100, 100, 100);
            $fechaActual = date('d/m/Y H:i:s');
            $pdf->Cell(0, 5, 'Fecha de generacion: ' . $fechaActual, 0, 1, 'L');
            $pdf->Cell(0, 5, 'Total de instituciones: ' . count($instituciones), 0, 1, 'L');
            $pdf->Ln(5);

            // Encabezados de tabla con color verde
            $pdf->SetFillColor(17, 120, 103); // Verde del sistema
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetDrawColor(17, 120, 103);
            $pdf->SetLineWidth(0.3);
            $pdf->SetFont('Arial', 'B', 10);

            $w = array(20, 170);
            $pdf->Cell($w[0], 7, 'ID', 1, 0, 'C', true);
            $pdf->Cell($w[1], 7, utf8_decode('Nombre de la Institucion'), 1, 0, 'C', true);
            $pdf->Ln();

            // Datos con colores alternados
            $pdf->SetFillColor(240, 255, 250); // Verde muy claro
            $pdf->SetTextColor(50, 50, 50);
            $pdf->SetFont('Arial', '', 9);

            $fill = false;
            foreach($instituciones as $inst) {
                $pdf->Cell($w[0], 6, $inst->id, 'LR', 0, 'C', $fill);
                $pdf->Cell($w[1], 6, utf8_decode($inst->nombre), 'LR', 0, 'L', $fill);
                $pdf->Ln();
                $fill = !$fill;
            }

            // Línea de cierre
            $pdf->Cell(array_sum($w), 0, '', 'T');

            // Output
            $pdf->Output('D', 'Reporte_Instituciones_' . date('Ymd_His') . '.pdf');

        } catch (Exception $e) {
            echo "Error al generar PDF: " . $e->getMessage();
        }
    }

    public function generarPDFCalificaciones() {
        try {
            $filtros = [
                'institucion_id' => isset($_GET['institucion_id']) ? $_GET['institucion_id'] : '',
                'curso_id' => isset($_GET['curso_id']) ? $_GET['curso_id'] : '',
                'grado_id' => isset($_GET['grado_id']) ? $_GET['grado_id'] : '',
                'periodo' => isset($_GET['periodo']) ? $_GET['periodo'] : ''
            ];

            $calificaciones = $this->modelo->obtenerCalificaciones($filtros);

            $pdf = new PDF_Calificaciones();
            $pdf->SetTitle('Reporte de Calificaciones');
            $pdf->SetAuthor('Sistema Educativo');
            $pdf->AliasNbPages();
            $pdf->AddPage('L');
            
            $pdf->SetFont('Arial', '', 9);
            $pdf->SetTextColor(100, 100, 100);
            $fechaActual = date('d/m/Y H:i:s');
            $pdf->Cell(0, 5, 'Fecha de generacion: ' . $fechaActual, 0, 1, 'L');
            $pdf->Cell(0, 5, 'Total de registros: ' . count($calificaciones), 0, 1, 'L');
            $pdf->Ln(5);

            // Encabezados
            $pdf->SetFillColor(26, 188, 156); // Verde info
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetDrawColor(26, 188, 156);
            $pdf->SetLineWidth(0.3);
            $pdf->SetFont('Arial', 'B', 9);

            $w = array(25, 50, 45, 40, 45, 30, 25);
            $headers = array('Codigo', 'Alumno', 'Curso', 'Grado', 'Institucion', 'Periodo', 'Puntaje');
            for($i = 0; $i < count($headers); $i++) {
                $pdf->Cell($w[$i], 7, utf8_decode($headers[$i]), 1, 0, 'C', true);
            }
            $pdf->Ln();

            // Datos
            $pdf->SetFillColor(240, 255, 250);
            $pdf->SetTextColor(50, 50, 50);
            $pdf->SetFont('Arial', '', 8);

            $fill = false;
            foreach($calificaciones as $cal) {
                $pdf->Cell($w[0], 6, utf8_decode($cal->codigo_alumno), 'LR', 0, 'L', $fill);
                $pdf->Cell($w[1], 6, utf8_decode(substr($cal->alumno, 0, 30)), 'LR', 0, 'L', $fill);
                $pdf->Cell($w[2], 6, utf8_decode(substr($cal->curso, 0, 25)), 'LR', 0, 'L', $fill);
                $pdf->Cell($w[3], 6, utf8_decode(substr($cal->grado, 0, 20)), 'LR', 0, 'L', $fill);
                $pdf->Cell($w[4], 6, utf8_decode(substr($cal->institucion, 0, 25)), 'LR', 0, 'L', $fill);
                $pdf->Cell($w[5], 6, utf8_decode($cal->periodo), 'LR', 0, 'C', $fill);
                $pdf->Cell($w[6], 6, number_format($cal->puntaje, 2), 'LR', 0, 'C', $fill);
                $pdf->Ln();
                $fill = !$fill;
            }

            $pdf->Cell(array_sum($w), 0, '', 'T');
            $pdf->Output('D', 'Reporte_Calificaciones_' . date('Ymd_His') . '.pdf');

        } catch (Exception $e) {
            echo "Error al generar PDF: " . $e->getMessage();
        }
    }

    public function generarPDFDistritos() {
        try {
            $filtros = [
                'distrito_id' => isset($_GET['distrito_id']) ? $_GET['distrito_id'] : ''
            ];

            $datos = $this->modelo->obtenerInstitucionesPorDistritoDetallado($filtros);

            $pdf = new PDF_Distritos();
            $pdf->SetTitle('Reporte por Distrito');
            $pdf->SetAuthor('Sistema Educativo');
            $pdf->AliasNbPages();
            $pdf->AddPage('L');
            
            $pdf->SetFont('Arial', '', 9);
            $pdf->SetTextColor(100, 100, 100);
            $fechaActual = date('d/m/Y H:i:s');
            $pdf->Cell(0, 5, 'Fecha de generacion: ' . $fechaActual, 0, 1, 'L');
            $pdf->Cell(0, 5, 'Total de registros: ' . count($datos), 0, 1, 'L');
            $pdf->Ln(5);

            // Encabezados
            $pdf->SetFillColor(46, 204, 113); // Verde advertencia
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetDrawColor(46, 204, 113);
            $pdf->SetLineWidth(0.3);
            $pdf->SetFont('Arial', 'B', 9);

            $w = array(40, 70, 40, 30, 30, 30, 30);
            $headers = array('Distrito', 'Institucion', 'Tipo', 'Usuarios', 'Alumnos', 'Docentes', 'Promedio');
            for($i = 0; $i < count($headers); $i++) {
                $pdf->Cell($w[$i], 7, utf8_decode($headers[$i]), 1, 0, 'C', true);
            }
            $pdf->Ln();

            // Datos agrupados por distrito
            $pdf->SetFillColor(240, 255, 250);
            $pdf->SetTextColor(50, 50, 50);
            $pdf->SetFont('Arial', '', 8);

            $fill = false;
            $distrito_actual = '';
            
            foreach($datos as $dato) {
                // Si cambia el distrito, agregar subtotal o separador
                if ($distrito_actual != $dato->distrito && $distrito_actual != '') {
                    $pdf->Ln(2);
                }
                $distrito_actual = $dato->distrito;
                
                $pdf->Cell($w[0], 6, utf8_decode(substr($dato->distrito, 0, 25)), 'LR', 0, 'L', $fill);
                $pdf->Cell($w[1], 6, utf8_decode(substr($dato->institucion, 0, 45)), 'LR', 0, 'L', $fill);
                $tipo_value = isset($dato->tipo) ? $dato->tipo : 'N/D';
                $pdf->Cell($w[2], 6, utf8_decode(substr($tipo_value, 0, 20)), 'LR', 0, 'C', $fill);
                $pdf->Cell($w[3], 6, $dato->total_usuarios, 'LR', 0, 'C', $fill);
                $pdf->Cell($w[4], 6, $dato->total_alumnos, 'LR', 0, 'C', $fill);
                $pdf->Cell($w[5], 6, $dato->total_docentes, 'LR', 0, 'C', $fill);
                $promedio = $dato->promedio_institucion > 0 ? number_format($dato->promedio_institucion, 2) : 'N/D';
                $pdf->Cell($w[6], 6, $promedio, 'LR', 0, 'C', $fill);
                $pdf->Ln();
                $fill = !$fill;
            }

            $pdf->Cell(array_sum($w), 0, '', 'T');
            $pdf->Output('D', 'Reporte_Distritos_' . date('Ymd_His') . '.pdf');

        } catch (Exception $e) {
            echo "Error al generar PDF: " . $e->getMessage();
        }
    }

    public function generarPDFResumenAcademico() {
        try {
            $filtros = [
                'institucion_id' => isset($_GET['institucion_id']) ? $_GET['institucion_id'] : '',
                'distrito_id' => isset($_GET['distrito_id']) ? $_GET['distrito_id'] : ''
            ];

            $resumen = $this->modelo->obtenerResumenAcademicoDetallado($filtros);

            $pdf = new PDF_ResumenAcademico();
            $pdf->SetTitle('Resumen Academico');
            $pdf->SetAuthor('Sistema Educativo');
            $pdf->AliasNbPages();
            $pdf->AddPage('L');
            
            $pdf->SetFont('Arial', '', 9);
            $pdf->SetTextColor(100, 100, 100);
            $fechaActual = date('d/m/Y H:i:s');
            $pdf->Cell(0, 5, 'Fecha de generacion: ' . $fechaActual, 0, 1, 'L');
            $pdf->Cell(0, 5, 'Total de instituciones: ' . count($resumen), 0, 1, 'L');
            $pdf->Ln(5);

            // Encabezados
            $pdf->SetFillColor(72, 201, 176); // Verde claro
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetDrawColor(72, 201, 176);
            $pdf->SetLineWidth(0.3);
            $pdf->SetFont('Arial', 'B', 8);

            $w = array(55, 35, 22, 22, 22, 22, 28, 28, 26);
            $headers = array('Institucion', 'Distrito', 'Alumnos', 'Docentes', 'Direct.', 'Total', 'Calif.', 'Promedio', 'Encuestas');
            for($i = 0; $i < count($headers); $i++) {
                $pdf->Cell($w[$i], 7, utf8_decode($headers[$i]), 1, 0, 'C', true);
            }
            $pdf->Ln();

            // Datos
            $pdf->SetFillColor(240, 255, 250);
            $pdf->SetTextColor(50, 50, 50);
            $pdf->SetFont('Arial', '', 7);

            $fill = false;
            $totales = [
                'alumnos' => 0,
                'docentes' => 0,
                'directores' => 0,
                'usuarios' => 0,
                'calificaciones' => 0,
                'encuestas' => 0
            ];

            foreach($resumen as $res) {
                $pdf->Cell($w[0], 6, utf8_decode(substr($res->institucion, 0, 35)), 'LR', 0, 'L', $fill);
                $distrito_value = isset($res->distrito) ? $res->distrito : 'N/D';
                $pdf->Cell($w[1], 6, utf8_decode(substr($distrito_value, 0, 20)), 'LR', 0, 'L', $fill);
                $pdf->Cell($w[2], 6, $res->total_alumnos, 'LR', 0, 'C', $fill);
                $pdf->Cell($w[3], 6, $res->total_docentes, 'LR', 0, 'C', $fill);
                $pdf->Cell($w[4], 6, $res->total_directores, 'LR', 0, 'C', $fill);
                $pdf->Cell($w[5], 6, $res->total_usuarios, 'LR', 0, 'C', $fill);
                $pdf->Cell($w[6], 6, $res->total_calificaciones, 'LR', 0, 'C', $fill);
                $pdf->Cell($w[7], 6, number_format($res->promedio_general, 2), 'LR', 0, 'C', $fill);
                $pdf->Cell($w[8], 6, $res->total_encuestas, 'LR', 0, 'C', $fill);
                $pdf->Ln();
                $fill = !$fill;

                // Acumular totales
                $totales['alumnos'] += $res->total_alumnos;
                $totales['docentes'] += $res->total_docentes;
                $totales['directores'] += $res->total_directores;
                $totales['usuarios'] += $res->total_usuarios;
                $totales['calificaciones'] += $res->total_calificaciones;
                $totales['encuestas'] += $res->total_encuestas;
            }

            // Línea de totales
            $pdf->Cell(array_sum($w), 0, '', 'T');
            $pdf->Ln();
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetFillColor(72, 201, 176);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell($w[0] + $w[1], 6, 'TOTALES', 1, 0, 'C', true);
            $pdf->Cell($w[2], 6, $totales['alumnos'], 1, 0, 'C', true);
            $pdf->Cell($w[3], 6, $totales['docentes'], 1, 0, 'C', true);
            $pdf->Cell($w[4], 6, $totales['directores'], 1, 0, 'C', true);
            $pdf->Cell($w[5], 6, $totales['usuarios'], 1, 0, 'C', true);
            $pdf->Cell($w[6], 6, $totales['calificaciones'], 1, 0, 'C', true);
            $pdf->Cell($w[7], 6, '-', 1, 0, 'C', true);
            $pdf->Cell($w[8], 6, $totales['encuestas'], 1, 0, 'C', true);

            $pdf->Output('D', 'Reporte_Resumen_Academico_' . date('Ymd_His') . '.pdf');

        } catch (Exception $e) {
            echo "Error al generar PDF: " . $e->getMessage();
        }
    }

    // ==================== MÉTODOS PARA GENERAR EXCEL ====================

    public function generarExcelUsuarios() {
        try {
            $filtros = [
                'rol' => isset($_GET['rol']) ? $_GET['rol'] : '',
                'institucion_id' => isset($_GET['institucion_id']) ? $_GET['institucion_id'] : '',
                'activo' => isset($_GET['activo']) ? $_GET['activo'] : ''
            ];

            $usuarios = $this->modelo->obtenerUsuarios($filtros);

            header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
            header("Content-Disposition: attachment; filename=Reporte_Usuarios_" . date('Ymd_His') . ".xls");
            header("Pragma: no-cache");
            header("Expires: 0");

            echo "\xEF\xBB\xBF"; // UTF-8 BOM

            echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
            echo '<head>';
            echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
            echo '<xml>';
            echo '<x:ExcelWorkbook>';
            echo '<x:ExcelWorksheets>';
            echo '<x:ExcelWorksheet>';
            echo '<x:Name>Usuarios</x:Name>';
            echo '<x:WorksheetOptions>';
            echo '<x:Print><x:ValidPrinterInfo/></x:Print>';
            echo '</x:WorksheetOptions>';
            echo '</x:ExcelWorksheet>';
            echo '</x:ExcelWorksheets>';
            echo '</x:ExcelWorkbook>';
            echo '</xml>';
            echo '</head>';
            echo '<body>';
            echo '<table border="1">';
            echo '<tr style="background-color: #117867; color: white; font-weight: bold;">';
            echo '<td colspan="9" style="text-align: center; font-size: 16px; padding: 10px;">REPORTE DE USUARIOS</td>';
            echo '</tr>';
            echo '<tr style="background-color: #117867; color: white; font-weight: bold;">';
            echo '<td>Código</td>';
            echo '<td>Nombres</td>';
            echo '<td>Apellidos</td>';
            echo '<td>Rol</td>';
            echo '<td>Institución</td>';
            echo '<td>Grado</td>';
            echo '<td>Sección</td>';
            echo '<td>Estado</td>';
            echo '<td>Fecha Registro</td>';
            echo '</tr>';

            foreach($usuarios as $usuario) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($usuario->codigo ? $usuario->codigo : 'N/A') . '</td>';
                echo '<td>' . htmlspecialchars($usuario->nombres) . '</td>';
                echo '<td>' . htmlspecialchars($usuario->apellidos) . '</td>';
                echo '<td>' . htmlspecialchars($usuario->rol) . '</td>';
                $institucion = $usuario->institucion_nombre ? $usuario->institucion_nombre : 'Sin asignar';
                echo '<td>' . htmlspecialchars($institucion) . '</td>';
                $grado = $usuario->grado_nombre ? $usuario->grado_nombre : 'N/A';
                echo '<td>' . htmlspecialchars($grado) . '</td>';
                echo '<td>' . ($usuario->seccion ? $usuario->seccion : '-') . '</td>';
                echo '<td>' . ($usuario->activo == 1 ? 'Activo' : 'Inactivo') . '</td>';
                echo '<td>' . date('d/m/Y H:i', strtotime($usuario->creado_en)) . '</td>';
                echo '</tr>';
            }

            echo '<tr style="background-color: #f0f0f0; font-weight: bold;">';
            echo '<td colspan="9">Total de registros: ' . count($usuarios) . '</td>';
            echo '</tr>';
            echo '</table>';
            echo '</body>';
            echo '</html>';
            exit;

        } catch (Exception $e) {
            echo "Error al generar Excel: " . $e->getMessage();
        }
    }

    public function generarExcelInstituciones() {
        try {
            $instituciones = $this->modelo->obtenerInstituciones();

            header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
            header("Content-Disposition: attachment; filename=Reporte_Instituciones_" . date('Ymd_His') . ".xls");
            header("Pragma: no-cache");
            header("Expires: 0");

            echo "\xEF\xBB\xBF";

            echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
            echo '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head>';
            echo '<body>';
            echo '<table border="1">';
            echo '<tr style="background-color: #15a085; color: white; font-weight: bold;">';
            echo '<td colspan="2" style="text-align: center; font-size: 16px; padding: 10px;">REPORTE DE INSTITUCIONES</td>';
            echo '</tr>';
            echo '<tr style="background-color: #15a085; color: white; font-weight: bold;">';
            echo '<td>ID</td>';
            echo '<td>Nombre</td>';
            echo '</tr>';

            foreach($instituciones as $inst) {
                echo '<tr>';
                echo '<td>' . $inst->id . '</td>';
                echo '<td>' . htmlspecialchars($inst->nombre) . '</td>';
                echo '</tr>';
            }

            echo '<tr style="background-color: #f0f0f0; font-weight: bold;">';
            echo '<td colspan="2">Total de instituciones: ' . count($instituciones) . '</td>';
            echo '</tr>';
            echo '</table>';
            echo '</body>';
            echo '</html>';
            exit;

        } catch (Exception $e) {
            echo "Error al generar Excel: " . $e->getMessage();
        }
    }

    public function generarExcelCalificaciones() {
        try {
            $filtros = [
                'institucion_id' => isset($_GET['institucion_id']) ? $_GET['institucion_id'] : '',
                'curso_id' => isset($_GET['curso_id']) ? $_GET['curso_id'] : '',
                'grado_id' => isset($_GET['grado_id']) ? $_GET['grado_id'] : ''
            ];

            $calificaciones = $this->modelo->obtenerCalificaciones($filtros);

            header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
            header("Content-Disposition: attachment; filename=Reporte_Calificaciones_" . date('Ymd_His') . ".xls");
            header("Pragma: no-cache");
            header("Expires: 0");

            echo "\xEF\xBB\xBF";

            echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
            echo '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head>';
            echo '<body>';
            echo '<table border="1">';
            echo '<tr style="background-color: #1abc9c; color: white; font-weight: bold;">';
            echo '<td colspan="7" style="text-align: center; font-size: 16px; padding: 10px;">REPORTE DE CALIFICACIONES</td>';
            echo '</tr>';
            echo '<tr style="background-color: #1abc9c; color: white; font-weight: bold;">';
            echo '<td>Alumno</td>';
            echo '<td>Curso</td>';
            echo '<td>Grado</td>';
            echo '<td>Institución</td>';
            echo '<td>Puntaje</td>';
            echo '<td>Nota</td>';
            echo '<td>Fecha</td>';
            echo '</tr>';

            foreach($calificaciones as $calif) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($calif->alumno) . '</td>';
                echo '<td>' . htmlspecialchars($calif->curso) . '</td>';
                echo '<td>' . htmlspecialchars($calif->grado) . '</td>';
                echo '<td>' . htmlspecialchars($calif->institucion) . '</td>';
                echo '<td>' . number_format($calif->puntaje, 2) . '</td>';
                echo '<td>' . number_format($calif->nota, 2) . '</td>';
                echo '<td>' . date('d/m/Y', strtotime($calif->fecha_registro)) . '</td>';
                echo '</tr>';
            }

            echo '<tr style="background-color: #f0f0f0; font-weight: bold;">';
            echo '<td colspan="7">Total de calificaciones: ' . count($calificaciones) . '</td>';
            echo '</tr>';
            echo '</table>';
            echo '</body>';
            echo '</html>';
            exit;

        } catch (Exception $e) {
            echo "Error al generar Excel: " . $e->getMessage();
        }
    }

    public function generarExcelDistritos() {
        try {
            $filtros = ['distrito_id' => isset($_GET['distrito_id']) ? $_GET['distrito_id'] : ''];
            $datos = $this->modelo->obtenerInstitucionesPorDistritoDetallado($filtros);

            header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
            header("Content-Disposition: attachment; filename=Reporte_Distritos_" . date('Ymd_His') . ".xls");
            header("Pragma: no-cache");
            header("Expires: 0");

            echo "\xEF\xBB\xBF";

            echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
            echo '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head>';
            echo '<body>';
            echo '<table border="1">';
            echo '<tr style="background-color: #0b4f44; color: white; font-weight: bold;">';
            echo '<td colspan="7" style="text-align: center; font-size: 16px; padding: 10px;">REPORTE POR DISTRITO</td>';
            echo '</tr>';
            echo '<tr style="background-color: #0b4f44; color: white; font-weight: bold;">';
            echo '<td>Distrito</td>';
            echo '<td>Institución</td>';
            echo '<td>Tipo</td>';
            echo '<td>Total Usuarios</td>';
            echo '<td>Total Alumnos</td>';
            echo '<td>Total Docentes</td>';
            echo '<td>Promedio</td>';
            echo '</tr>';

            foreach($datos as $dato) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($dato->distrito) . '</td>';
                echo '<td>' . htmlspecialchars($dato->institucion) . '</td>';
                $tipo_value = isset($dato->tipo) ? $dato->tipo : 'N/D';
                echo '<td>' . htmlspecialchars($tipo_value) . '</td>';
                echo '<td>' . $dato->total_usuarios . '</td>';
                echo '<td>' . $dato->total_alumnos . '</td>';
                echo '<td>' . $dato->total_docentes . '</td>';
                $promedio = $dato->promedio_institucion > 0 ? number_format($dato->promedio_institucion, 2) : 'N/D';
                echo '<td>' . $promedio . '</td>';
                echo '</tr>';
            }

            echo '<tr style="background-color: #f0f0f0; font-weight: bold;">';
            echo '<td colspan="7">Total de registros: ' . count($datos) . '</td>';
            echo '</tr>';
            echo '</table>';
            echo '</body>';
            echo '</html>';
            exit;

        } catch (Exception $e) {
            echo "Error al generar Excel: " . $e->getMessage();
        }
    }

    public function generarExcelResumenAcademico() {
        try {
            $filtros = [
                'institucion_id' => isset($_GET['institucion_id']) ? $_GET['institucion_id'] : '',
                'distrito_id' => isset($_GET['distrito_id']) ? $_GET['distrito_id'] : ''
            ];
            $resumen = $this->modelo->obtenerResumenAcademicoDetallado($filtros);

            header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
            header("Content-Disposition: attachment; filename=Reporte_Resumen_Academico_" . date('Ymd_His') . ".xls");
            header("Pragma: no-cache");
            header("Expires: 0");

            echo "\xEF\xBB\xBF";

            echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
            echo '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head>';
            echo '<body>';
            echo '<table border="1">';
            echo '<tr style="background-color: #48c9b0; color: white; font-weight: bold;">';
            echo '<td colspan="9" style="text-align: center; font-size: 16px; padding: 10px;">RESUMEN ACADÉMICO</td>';
            echo '</tr>';
            echo '<tr style="background-color: #48c9b0; color: white; font-weight: bold;">';
            echo '<td>Institución</td>';
            echo '<td>Distrito</td>';
            echo '<td>Alumnos</td>';
            echo '<td>Docentes</td>';
            echo '<td>Directores</td>';
            echo '<td>Total Usuarios</td>';
            echo '<td>Calificaciones</td>';
            echo '<td>Promedio</td>';
            echo '<td>Encuestas</td>';
            echo '</tr>';

            $totales = [
                'alumnos' => 0,
                'docentes' => 0,
                'directores' => 0,
                'usuarios' => 0,
                'calificaciones' => 0,
                'encuestas' => 0
            ];

            foreach($resumen as $res) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($res->institucion) . '</td>';
                $distrito_value = isset($res->distrito) ? $res->distrito : 'N/D';
                echo '<td>' . htmlspecialchars($distrito_value) . '</td>';
                echo '<td>' . $res->total_alumnos . '</td>';
                echo '<td>' . $res->total_docentes . '</td>';
                echo '<td>' . $res->total_directores . '</td>';
                echo '<td>' . $res->total_usuarios . '</td>';
                echo '<td>' . $res->total_calificaciones . '</td>';
                echo '<td>' . number_format($res->promedio_general, 2) . '</td>';
                echo '<td>' . $res->total_encuestas . '</td>';
                echo '</tr>';

                $totales['alumnos'] += $res->total_alumnos;
                $totales['docentes'] += $res->total_docentes;
                $totales['directores'] += $res->total_directores;
                $totales['usuarios'] += $res->total_usuarios;
                $totales['calificaciones'] += $res->total_calificaciones;
                $totales['encuestas'] += $res->total_encuestas;
            }

            echo '<tr style="background-color: #48c9b0; color: white; font-weight: bold;">';
            echo '<td colspan="2">TOTALES</td>';
            echo '<td>' . $totales['alumnos'] . '</td>';
            echo '<td>' . $totales['docentes'] . '</td>';
            echo '<td>' . $totales['directores'] . '</td>';
            echo '<td>' . $totales['usuarios'] . '</td>';
            echo '<td>' . $totales['calificaciones'] . '</td>';
            echo '<td>-</td>';
            echo '<td>' . $totales['encuestas'] . '</td>';
            echo '</tr>';
            echo '</table>';
            echo '</body>';
            echo '</html>';
            exit;

        } catch (Exception $e) {
            echo "Error al generar Excel: " . $e->getMessage();
        }
    }
}

// ==================== CLASES PERSONALIZADAS PARA PDFs ====================

class PDF_Usuarios extends FPDF {
    // Cabecera
    function Header() {
        // Logo o imagen (opcional)
        // $this->Image('logo.png', 10, 6, 30);
        
        // Fondo verde para el header
        $this->SetFillColor(17, 120, 103);
        $this->Rect(0, 0, 297, 35, 'F');
        
        // Título
        $this->SetY(10);
        $this->SetFont('Arial', 'B', 18);
        $this->SetTextColor(255, 255, 255);
        $this->Cell(0, 10, 'REPORTE DE USUARIOS', 0, 1, 'C');
        
        // Subtítulo
        $this->SetFont('Arial', '', 11);
        $this->Cell(0, 6, 'Sistema Educativo - Listado Completo', 0, 1, 'C');
        
        $this->Ln(8);
    }
    
    // Pie de página
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(128, 128, 128);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
        $this->SetX(10);
        $this->Cell(0, 10, 'Sistema Educativo ' . date('Y'), 0, 0, 'L');
        $this->Cell(0, 10, 'Generado: ' . date('d/m/Y H:i'), 0, 0, 'R');
    }
}

class PDF_Instituciones extends FPDF {
    // Cabecera
    function Header() {
        // Fondo verde para el header
        $this->SetFillColor(21, 160, 133); // Verde más claro
        $this->Rect(0, 0, 210, 35, 'F');
        
        // Título
        $this->SetY(10);
        $this->SetFont('Arial', 'B', 18);
        $this->SetTextColor(255, 255, 255);
        $this->Cell(0, 10, utf8_decode('REPORTE DE INSTITUCIONES'), 0, 1, 'C');
        
        // Subtítulo
        $this->SetFont('Arial', '', 11);
        $this->Cell(0, 6, utf8_decode('Sistema Educativo - Catálogo de Instituciones'), 0, 1, 'C');
        
        $this->Ln(8);
    }
    
    // Pie de página
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(128, 128, 128);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
        $this->SetX(10);
        $this->Cell(0, 10, 'Sistema Educativo ' . date('Y'), 0, 0, 'L');
        $this->Cell(0, 10, 'Generado: ' . date('d/m/Y H:i'), 0, 0, 'R');
    }
}

class PDF_Calificaciones extends FPDF {
    function Header() {
        $this->SetFillColor(26, 188, 156);
        $this->Rect(0, 0, 297, 35, 'F');
        $this->SetY(10);
        $this->SetFont('Arial', 'B', 18);
        $this->SetTextColor(255, 255, 255);
        $this->Cell(0, 10, 'REPORTE DE CALIFICACIONES', 0, 1, 'C');
        $this->SetFont('Arial', '', 11);
        $this->Cell(0, 6, utf8_decode('Sistema Educativo - Concentrado de Calificaciones'), 0, 1, 'C');
        $this->Ln(8);
    }
    
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(128, 128, 128);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
        $this->SetX(10);
        $this->Cell(0, 10, 'Sistema Educativo ' . date('Y'), 0, 0, 'L');
        $this->Cell(0, 10, 'Generado: ' . date('d/m/Y H:i'), 0, 0, 'R');
    }
}

class PDF_Distritos extends FPDF {
    function Header() {
        $this->SetFillColor(11, 79, 68); // Verde oscuro
        $this->Rect(0, 0, 297, 35, 'F');
        $this->SetY(10);
        $this->SetFont('Arial', 'B', 18);
        $this->SetTextColor(255, 255, 255);
        $this->Cell(0, 10, 'REPORTE POR DISTRITO', 0, 1, 'C');
        $this->SetFont('Arial', '', 11);
        $this->Cell(0, 6, utf8_decode('Sistema Educativo - Instituciones Agrupadas por Distrito'), 0, 1, 'C');
        $this->Ln(8);
    }
    
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(128, 128, 128);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
        $this->SetX(10);
        $this->Cell(0, 10, 'Sistema Educativo ' . date('Y'), 0, 0, 'L');
        $this->Cell(0, 10, 'Generado: ' . date('d/m/Y H:i'), 0, 0, 'R');
    }
}

class PDF_ResumenAcademico extends FPDF {
    function Header() {
        $this->SetFillColor(72, 201, 176); // Verde claro
        $this->Rect(0, 0, 297, 35, 'F');
        $this->SetY(10);
        $this->SetFont('Arial', 'B', 18);
        $this->SetTextColor(255, 255, 255);
        $this->Cell(0, 10, utf8_decode('RESUMEN ACADÉMICO'), 0, 1, 'C');
        $this->SetFont('Arial', '', 11);
        $this->Cell(0, 6, utf8_decode('Sistema Educativo - Estadísticas Completas por Institución'), 0, 1, 'C');
        $this->Ln(8);
    }
    
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(128, 128, 128);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
        $this->SetX(10);
        $this->Cell(0, 10, 'Sistema Educativo ' . date('Y'), 0, 0, 'L');
        $this->Cell(0, 10, 'Generado: ' . date('d/m/Y H:i'), 0, 0, 'R');
    }
}
?>