<?php
session_start();
$projectRoot = dirname(__FILE__, 3);
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once $projectRoot . '/includes/functions.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/lib/fpdf.php';

$turnosDistribucion=getTurnosDistribucion();

class PDF extends FPDF
{
    function Header()
    {
        // Logo
        $this->Image(ROOT_PATH . '/extras/lgh.jpg', 10, 6, 30);
        // Título
        $this->SetFont('Arial', 'B', 15);
        $this->SetXY(50, 15);
        $this->Cell(0, 10, utf8_decode('Reporte de Distribuciones de Turnos'), 0, 1, 'C');
        $this->Ln(20);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ' . $this->PageNo() . '/{nb}'), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Distribuciones de Turnos', 0, 1);
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 10, 'ID', 1);
$pdf->Cell(50, 10, 'Nombre', 1);
$pdf->Cell(70, 10, utf8_decode('Descripción'), 1);
$pdf->Cell(50, 10, 'Tipo de Servicio', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);
foreach ($turnosDistribucion as $distribucion) {
    $pdf->Cell(20, 10, $distribucion['id'], 1);
    $pdf->Cell(50, 10, utf8_decode($distribucion['nombre']), 1);
    $pdf->Cell(70, 10, utf8_decode($distribucion['descripcion']), 1);
    $pdf->Cell(50, 10, utf8_decode(getTurnosTipoServicioById($distribucion['tipo_servicio'])['nombre']), 1);
    $pdf->Ln();
}

$pdf->Output('I', 'reporte_turnos_distribucion.pdf');