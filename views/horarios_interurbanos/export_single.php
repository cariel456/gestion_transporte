<?php
session_start();
$projectRoot = dirname(__FILE__, 3);
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once $projectRoot . '/includes/functions.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/lib/fpdf.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID no proporcionado");
}

$horario = getHorarioDetails($id);
$detalles = getHorarioDetalles($id);

class PDF extends FPDF
{
    function Header()
    {
        // Logo
        $this->Image(ROOT_PATH . '/extras/lgh.jpg', 10, 6, 30);
        // Título
        $this->SetFont('Arial', 'B', 15);
        $this->SetXY(50, 15);
        $this->Cell(0, 10, 'Horario Interurbano', 0, 1, 'C');
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
$pdf->Cell(0, 10, 'Detalles del Horario', 0, 1);
$pdf->Ln(5);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(60, 10, 'Servicio 1:', 0);
$pdf->Cell(0, 10, utf8_decode($horario['servicio1_nombre']), 0, 1);
$pdf->Cell(60, 10, 'Servicio 2:', 0);
$pdf->Cell(0, 10, utf8_decode($horario['servicio2_nombre']), 0, 1);
$pdf->Cell(60, 10, 'Servicio 3:', 0);
$pdf->Cell(0, 10, utf8_decode($horario['servicio3_nombre']), 0, 1);
$pdf->Cell(60, 10, 'Terminal Salida:', 0);
$pdf->Cell(0, 10, utf8_decode($horario['terminal_salida_nombre']), 0, 1);
$pdf->Cell(60, 10, 'Terminal Llegada:', 0);
$pdf->Cell(0, 10, utf8_decode($horario['terminal_llegada_nombre']), 0, 1);
$pdf->Cell(60, 10, 'Descripcion:', 0);
$pdf->Cell(0, 10, utf8_decode($horario['descripcion']), 0, 1);

$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Horarios', 0, 1);
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(95, 10, 'Hora 1', 1, 0, 'C');
$pdf->Cell(95, 10, 'Hora 2', 1, 0, 'C');
$pdf->Ln();

$pdf->SetFont('Arial', '', 12);
foreach ($detalles as $detalle) {
    $pdf->Cell(95, 10, $detalle['hora1'], 1, 0, 'C');
    $pdf->Cell(95, 10, $detalle['hora2'], 1, 0, 'C');
    $pdf->Ln();
}

$pdf->Output('I', 'horario_interurbano_' . $id . '.pdf');