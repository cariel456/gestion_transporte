<?php
session_start();
$projectRoot = dirname(__FILE__, 3);
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once $projectRoot . '/includes/functions.php';
require_once $projectRoot . '/views/tcpdf.php'; // Asumiendo que tienes la librería TCPDF instalada

use \views\turnos_distribucion\tcpdf;

// Verificar si el usuario está autenticado
requireLogin();

// Obtener los parámetros de búsqueda (si los hay)
$nombre = $_GET['nombre'] ?? '';
$descripcion = $_GET['descripcion'] ?? '';
$tipoServicio = $_GET['tipo_servicio'] ?? 0;

// Obtener los registros filtrados
$turnosDistribucion = searchTurnosDistribucion($nombre, $descripcion, $tipoServicio);

// Generar el PDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sistema de Distribución de Turnos');
$pdf->SetTitle('Reporte de Distribuciones de Turnos');
$pdf->SetHeaderData('', 0, 'Reporte de Distribuciones de Turnos', '');
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->AddPage();

// Agregar el contenido del reporte
$html = '<h1>Reporte de Distribuciones de Turnos</h1>';
$html .= '<table border="1" cellpadding="4" cellspacing="0">';
$html .= '<tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Tipo de Servicio</th></tr>';
foreach ($turnosDistribucion as $distribucion) {
    $html .= '<tr>';
    $html .= '<td>' . $distribucion['id'] . '</td>';
    $html .= '<td>' . $distribucion['nombre'] . '</td>';
    $html .= '<td>' . $distribucion['descripcion'] . '</td>';
    $html .= '<td>' . getTurnosTipoServicioById($distribucion['tipo_servicio'])['nombre'] . '</td>';
    $html .= '</tr>';
}
$html .= '</table>';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('reporte_turnos_distribucion.pdf', 'I');