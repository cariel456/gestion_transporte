<?php
session_start();
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/functions.php';
require_once ROOT_PATH . '/lib/fpdf.php';

class PDF extends FPDF
{
    function Header()
    {
        $this->Image(ROOT_PATH . '/extras/lgh.jpg', 14, 10, 25);
        $this->SetFont('Arial','B',15);
        $this->SetXY(40, 15);
        $this->Cell(0,10,'Horarios Interurbanos',0,1,'C');
        $this->Ln(10);
        
        $this->SetFont('Arial','B',9);
        $headers = ['ID', 'Servicio', 'Terminal Salida', 'Terminal Llegada', 'Hora 1', 'Hora 2'];
        $widths = [15, 85, 55, 55, 30, 30];
        $this->SetFillColor(200,220,255);
        foreach($headers as $index => $header) {
            $this->Cell($widths[$index], 7, utf8_decode($header), 1, 0, 'C', true);
        }
        $this->Ln();
    }
    
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ' . $this->PageNo() . '/{nb}'), 0, 0, 'C');
    }
}

$pdf = new PDF('L', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',9);

$pageWidth = $pdf->GetPageWidth();
$tableWidth = 270;
$margin = ($pageWidth - $tableWidth) / 2;
$pdf->SetLeftMargin($margin);

$widths = [15, 85, 55, 55, 30, 30];

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $horario = getHorarioDetails($id);
    $detalles = getHorarioDetalles($id);
    if ($horario) {
        printHorario($pdf, $horario, $detalles, $widths, true);
    }
} else {
    $horarios = getAllHorariosInterurbanos();
    $lastId = null;
    foreach($horarios as $horario) {
        $detalles = getHorarioDetalles($horario['id']);
        $newGroup = $lastId != $horario['id'];
        printHorario($pdf, $horario, $detalles, $widths, $newGroup);
        $lastId = $horario['id'];
    }
}

$pdf->Output('horarios_interurbanos.pdf', 'I');

function printHorario($pdf, $horario, $detalles, $widths, $newGroup) {
    if ($newGroup) {
        $pdf->SetLineWidth(0.5);
        $pdf->SetDrawColor(100,100,100);
        $pdf->Rect($pdf->GetX(), $pdf->GetY(), array_sum($widths), 18 * (count($detalles) ?: 1));
        $pdf->SetLineWidth(0.2);
        $pdf->SetDrawColor(0,0,0);
    }
    
    if (empty($detalles)) {
        printRow($pdf, $horario, ['hora1' => '', 'hora2' => ''], $widths, $newGroup);
    } else {
        foreach ($detalles as $index => $detalle) {
            printRow($pdf, $horario, $detalle, $widths, $newGroup && $index == 0);
        }
    }
    
    if ($newGroup) {
        $pdf->Ln(2);
    }
}

function printRow($pdf, $horario, $detalle, $widths, $printIdAndServices) {
    if ($printIdAndServices) {
        $pdf->SetFillColor(240,240,240);
        $pdf->Cell($widths[0], 18, $horario['id'], 1, 0, 'C', true);
        $servicios = $horario['servicio1_nombre'] . "\n" . $horario['servicio2_nombre'] . "\n" . $horario['servicio3_nombre'];
        $pdf->MultiCell($widths[1], 6, utf8_decode($servicios), 1, 'L', true);
        $pdf->SetXY($pdf->GetX() + array_sum(array_slice($widths, 0, 2)), $pdf->GetY() - 18);
    } else {
        $pdf->Cell($widths[0], 18, '', 0);
        $pdf->Cell($widths[1], 18, '', 0);
    }
    $pdf->Cell($widths[2], 18, utf8_decode($horario['terminal_salida_nombre']), 1, 0, 'C');
    $pdf->Cell($widths[3], 18, utf8_decode($horario['terminal_llegada_nombre']), 1, 0, 'C');
    $pdf->Cell($widths[4], 18, $detalle['hora1'], 1, 0, 'C');
    $pdf->Cell($widths[5], 18, $detalle['hora2'], 1, 0, 'C');
    $pdf->Ln();
}