<?php
$projectRoot = dirname(__FILE__, 3); 
require_once dirname(__DIR__, 2) . '/config/config.php'; 
require_once ROOT_PATH . '/sec/init.php';
require_once ROOT_PATH . '/includes/session.php';   
require_once ROOT_PATH . '/sec/auth_check.php';       
require_once $projectRoot . '/includes/functions.php'; 
require_once ROOT_PATH . '/lib/fpdf.php';

// Verificar si el usuario está autenticado
requireLogin();

// Verificar si se proporcionó un ID
if (!isset($_GET['id'])) {
    die("Se requiere un ID para exportar a PDF.");
}

$id = $_GET['id'];

class PDF extends FPDF
{
    function Header()
    {
        $this->Image(ROOT_PATH . '/extras/lgh.jpg', 10, 6, 30);
        $this->SetFont('Arial','B',15);
        $this->Cell(80);
        $this->Cell(30,10,utf8_decode('Distribución de Turnos'),0,0,'C');
        $this->Ln(30);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'C');
    }
}

function generatePDF($id) {
    $distribucion = getTurnosDistribucionById($id);
    $detalles = getTurnosDistribucionDetalles($id);

    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',12);

    // Información de la tabla maestra
    $pdf->Cell(0,10,utf8_decode('Información de la Distribución'),0,1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(0,6,utf8_decode('Nombre: '.$distribucion['nombre']),0,1);
    $pdf->Cell(0,6,utf8_decode('Fecha: '.$distribucion['fecha']),0,1);
    $pdf->Cell(0,6,utf8_decode('Descripción: '.$distribucion['descripcion']),0,1);
    $pdf->Cell(0,6,utf8_decode('Tipo de Servicio: '.$distribucion['tipo_servicio']),0,1);

    $pdf->Ln(10);
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(0,10,utf8_decode('Detalles de la Distribución'),0,1);
    $pdf->SetFont('Arial','B',10);
    
    // Definimos el ancho de las columnas
    $w = array(30, 60, 60, 30);
    
    $pdf->Cell($w[0],7,utf8_decode('Turno'),1,0,'C');
    $pdf->Cell($w[1],7,utf8_decode('Servicio'),1,0,'C');
    $pdf->Cell($w[2],7,utf8_decode('Personal'),1,0,'C');
    $pdf->Cell($w[3],7,utf8_decode('Fecha'),1,0,'C');
    $pdf->Ln();

    $pdf->SetFont('Arial','',10);
    foreach ($detalles as $detalle) {
        $startY = $pdf->GetY();
        $currentY = $startY;

        // Turno
        $turnoInfo = getTurnosById($detalle['turno']);
        $pdf->Cell($w[0],6,utf8_decode($turnoInfo['nombre']),1,0,'L');
        
        // Servicio
        $servicioInfo = getTurnosServiciosById($detalle['turnos_servicios']);
        $pdf->MultiCell($w[1],6,utf8_decode($servicioInfo['nombre']),1,'L');
        $currentY = max($currentY, $pdf->GetY());
        
        // Volvemos a la posición correcta para las siguientes celdas
        $pdf->SetXY($pdf->GetX() + $w[0] + $w[1], $startY);

        // Personal
        $personalInfo = getPersonalById($detalle['personal']);
        $pdf->Cell($w[2],6,utf8_decode($personalInfo['nombre_personal']),1,0,'L');
        
        // Fecha (usando la fecha de la tabla maestra)
        $pdf->Cell($w[3],6,$distribucion['fecha'],1,0,'C');
        
        // Ajustamos la posición Y para la siguiente fila
        $pdf->SetY($currentY);
    }

    $pdf->Output('I', 'distribucion_turnos_'.$id.'.pdf');
    exit();
}

// Generar el PDF
generatePDF($id);