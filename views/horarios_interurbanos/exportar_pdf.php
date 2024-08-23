<?php
session_start();
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/functions.php';
require_once ROOT_PATH . '/lib/fpdf.php';

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial','B',15);
        $this->Cell(0,10,'Horarios Interurbanos',0,1,'C');
        $this->Ln(10);
    }
}

$pdf = new PDF();
$pdf->AddPage('L');
$pdf->SetFont('Arial','',10);

$headers = ['ID', 'Servicio 1', 'Servicio 2', 'Servicio 3', 'Terminal Salida', 'Terminal Llegada', 'Hora 1', 'Hora 2'];
$widths = [10, 30, 30, 30, 40, 40, 30, 30];

foreach($headers as $index => $header) {
    $pdf->Cell($widths[$index], 7, $header, 1, 0, 'C');
}
$pdf->Ln();

if (isset($_GET['id'])) {
    // Exportar un solo registro
    $id = $_GET['id'];
    $horario = getHorarioDetails($id);
    $detalles = getHorarioDetalles($id);
    if ($horario) {
        foreach ($detalles as $detalle) {
            $pdf->Cell($widths[0], 6, $horario['id'], 1);
            $pdf->Cell($widths[1], 6, utf8_decode($horario['servicio1_nombre']), 1);
            $pdf->Cell($widths[2], 6, utf8_decode($horario['servicio2_nombre']), 1);
            $pdf->Cell($widths[3], 6, utf8_decode($horario['servicio3_nombre']), 1);
            $pdf->Cell($widths[4], 6, utf8_decode($horario['terminal_salida_nombre']), 1);
            $pdf->Cell($widths[5], 6, utf8_decode($horario['terminal_llegada_nombre']), 1);
            $pdf->Cell($widths[6], 6, $detalle['hora1'], 1);
            $pdf->Cell($widths[7], 6, $detalle['hora2'], 1);
            $pdf->Ln();
        }
    }
} else {
    // Exportar todos los registros
    $horarios = getAllHorariosInterurbanos();
    foreach($horarios as $horario) {
        $detalles = getHorarioDetalles($horario['id']);
        if (empty($detalles)) {
            $pdf->Cell($widths[0], 6, $horario['id'], 1);
            $pdf->Cell($widths[1], 6, utf8_decode($horario['servicio1_nombre']), 1);
            $pdf->Cell($widths[2], 6, utf8_decode($horario['servicio2_nombre']), 1);
            $pdf->Cell($widths[3], 6, utf8_decode($horario['servicio3_nombre']), 1);
            $pdf->Cell($widths[4], 6, utf8_decode($horario['terminal_salida_nombre']), 1);
            $pdf->Cell($widths[5], 6, utf8_decode($horario['terminal_llegada_nombre']), 1);
            $pdf->Cell($widths[6], 6, '', 1);
            $pdf->Cell($widths[7], 6, '', 1);
            $pdf->Ln();
        } else {
            foreach ($detalles as $detalle) {
                $pdf->Cell($widths[0], 6, $horario['id'], 1);
                $pdf->Cell($widths[1], 6, utf8_decode($horario['servicio1_nombre']), 1);
                $pdf->Cell($widths[2], 6, utf8_decode($horario['servicio2_nombre']), 1);
                $pdf->Cell($widths[3], 6, utf8_decode($horario['servicio3_nombre']), 1);
                $pdf->Cell($widths[4], 6, utf8_decode($horario['terminal_salida_nombre']), 1);
                $pdf->Cell($widths[5], 6, utf8_decode($horario['terminal_llegada_nombre']), 1);
                $pdf->Cell($widths[6], 6, $detalle['hora1'], 1);
                $pdf->Cell($widths[7], 6, $detalle['hora2'], 1);
                $pdf->Ln();
            }
        }
    }
}

$pdf->Output('horarios_interurbanos.pdf', 'I');