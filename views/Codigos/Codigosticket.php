<?php
date_default_timezone_set('America/Guatemala');
require_once dirname(__FILE__).'/PHPWord-master/src/PhpWord/Autoloader.php';
\PhpOffice\PhpWord\Autoloader::register();
require_once 'TCPDF-main/tcpdf_barcodes_1d.php';

use PhpOffice\PhpWord\PhpWord;

$phpWord = new PhpWord();

// Añadir una sección al documento con un tamaño de página personalizado
$sectionStyle = array(
  'pageSizeW' => 80 * 56.6929134, // Ancho de la página en twips0
  'pageSizeH' => 95 * 56.6929134, // Altura de la página en twips
  'marginLeft' => 600, // Margen izquierdo
  'marginRight' => 600, // Margen derecho
  'marginTop' => 600, // Margen superior
  'marginBottom' => 600, // Margen inferior
);
$section = $phpWord->addSection($sectionStyle);

$model = new Productos_model();
$barcodes = array();
//$section->addTextBreak(2);

$Cont = 1;

//$section->addTextBreak(2);
foreach ($model->Consulta() as $r):
    if($Cont % 2 != 0)
    {
        $section->addTextBreak(2);
    }
    $barcodeobj = new TCPDFBarcode($r->codigobarra, 'C128');
    $barcode = $barcodeobj->getBarcodePngData(2, 80, array(0,0,0));

    // Reemplaza los caracteres de barra en el código de barras con guiones
    $barcodeFilename = 'barcode_' . str_replace('/', '-', $r->codigobarra) . '.png';
    $barcodes[] = $barcodeFilename; // Guardar los nombres de archivo para luego eliminarlos
    
    // Guarda la imagen del código de barras en un archivo
    file_put_contents($barcodeFilename, $barcode);

    // Agregar la imagen del código de barras a la sección utilizando la tabla
    // Agrega dos saltos de línea
 

    $table = $section->addTable(array('width' => 100 * 50, 'unit' => 'pct', 'alignment' => 'center'));
    $table->addRow();
   
    $cell = $table->addCell(2500); // puedes ajustar el tamaño para mover la imagen

    $cell = $table->addCell(2500);
    $cell->addImage($barcodeFilename, array('width' => 180));
    $cell->addText(
        $r->codigobarra,
        array('name' => 'Arial', 'size' => 7, 'arial' => true),
        array('alignment' => 'center')
    );
    $cell = $table->addCell(2500);
    
    // Agrega un salto de línea
    $section->addTextBreak();
    $Cont = $Cont + 1;
endforeach;
//$section->addTextBreak();
// Guardar el documento como un archivo de Word
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
$filename = 'DocumentoConCodigosBarras.docx';
$objWriter->save($filename);

// Prepararse para descargar el archivo
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename=' . basename($filename));
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filename));

// Limpiar el búfer de salida
ob_clean();

// Descargar el archivo
readfile($filename);

// Eliminar los archivos de imagen de los códigos de barras
foreach ($barcodes as $barcodeFile) {
    unlink($barcodeFile);
}

// Eliminar el archivo después de la descarga
unlink($filename);

exit;
?>
