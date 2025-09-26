<?php
require_once 'models/Reportes.php';
require_once 'FPDF/fpdf.php';

class ReportesController {
    private $modelo;

    public function __construct() {
        @session_start();
        require_once "core/AuthValidation.php";
        validarRol(['ADMIN','DIRECTOR']);
        
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
}
