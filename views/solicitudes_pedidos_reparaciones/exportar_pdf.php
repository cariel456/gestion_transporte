<?php
$projectRoot = dirname(__FILE__, 3); 
require_once dirname(__DIR__, 2) . '/config/config.php'; 
require_once ROOT_PATH . '/sec/init.php';
require_once ROOT_PATH . '/includes/session.php';   
require_once ROOT_PATH . '/sec/auth_check.php';       
require_once $projectRoot . '/includes/functions.php'; 
require_once ROOT_PATH . '/lib/fpdf.php';

// Actualizar la última actividad
$_SESSION['last_activity'] = time();

function escape($value) {
    global $conn;
    return $conn->real_escape_string($value);
}

function getSelectedFields() {
    $allFields = ['fecha_solicitud', 'solicitante_nombre', 'unidad_codigo', 'observaciones', 'estado'];
    $selectedFields = [];

    $filtersApplied = false;
    foreach ($allFields as $field) {
        if (!empty($_GET[$field])) {
            $selectedFields[] = $field;
            $filtersApplied = true;
        }
    }

    if (!$filtersApplied) {
        $selectedFields = $allFields;
    }

    return array_unique($selectedFields);
}

$selectedFields = getSelectedFields();
$sqlFields = implode(', ', array_map(function($field) {
    switch ($field) {
        case 'solicitante_nombre':
            return "p1.nombre_personal AS solicitante_nombre";
        case 'unidad_codigo':
            return "u.codigo_interno AS unidad_codigo";
        case 'estado':
            return "ultimo_estado.nombre AS estado";
        default:
            return "s.$field";
    }
}, $selectedFields));

$sql = "SELECT $sqlFields
        FROM solicitudes_pedidos_reparaciones s
        LEFT JOIN personal p1 ON s.solicitante = p1.id
        LEFT JOIN unidades u ON s.numero_unidad = u.id
        LEFT JOIN (
            SELECT hes.solicitud_id, es.nombre, hes.id
            FROM historial_estados_solicitud hes
            INNER JOIN estados_solicitud es ON hes.estado_id = es.id
            WHERE hes.id = (
                SELECT MAX(id)
                FROM historial_estados_solicitud
                WHERE solicitud_id = hes.solicitud_id
            )
        ) AS ultimo_estado ON s.id = ultimo_estado.solicitud_id
        WHERE 1=1";

$whereClause = [];

if (!empty($_GET)) {
    if (!empty($_GET['fecha_inicio']) && !empty($_GET['fecha_fin'])) {
        $whereClause[] = "s.fecha_solicitud BETWEEN '" . escape($_GET['fecha_inicio']) . "' AND '" . escape($_GET['fecha_fin']) . "'";
    }
    if (!empty($_GET['solicitante'])) {
        $whereClause[] = "s.solicitante = " . escape($_GET['solicitante']);
    }
    if (!empty($_GET['numero_unidad'])) {
        $whereClause[] = "s.numero_unidad = " . escape($_GET['numero_unidad']);
    }
    if (!empty($_GET['observaciones'])) {
        $whereClause[] = "s.observaciones LIKE '%" . escape($_GET['observaciones']) . "%'";
    }
    if (!empty($_GET['estado'])) {
        $whereClause[] = "ultimo_estado.nombre = '" . escape($_GET['estado']) . "'";
    }
}

if (!empty($whereClause)) {
    $sql .= " AND " . implode(" AND ", $whereClause);
}

$sql .= " GROUP BY s.id";

$result = $conn->query($sql);

class PDF extends FPDF
{
    function Header()
    {
        $this->Image('../../extras/lgh.jpg', 8, 2, 25);
        $this->SetFont('Arial','B',15);
        $this->Cell(0,10,'Solicitudes de Pedidos y Reparaciones',0,1,'C');
        $this->Ln(5);
        
        //$this->SetFont('Arial','B',10);
        $this->SetFillColor(200,220,255);
        $this->SetTextColor(0);
        $this->SetDrawColor(128,0,0);
        $this->SetLineWidth(.3);
        
        $headers = [
            'fecha_solicitud' => ['Fecha', 35],
            'solicitante_nombre' => ['Solicitante', 50],
            'unidad_codigo' => ['Unidad', 35],
            'estado' => ['Estado', 40],
            'observaciones' => ['Detalle Pedido', 110]
        ];

        foreach ($headers as $field => $header) {
            $this->Cell($header[1], 7, $header[0], 1, 0, 'C', true);
        }
        $this->Ln();
    }

    function CheckPageBreak($h)
    {
        if($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }
}

$pdf = new PDF();
$pdf->AddPage('L');

$pdf->SetFont('Arial','',9);
$pdf->SetFillColor(224,235,255);
$pdf->SetTextColor(0);

$headers = [
    'fecha_solicitud' => ['Fecha', 35],
    'solicitante_nombre' => ['Solicitante', 50],
    'unidad_codigo' => ['Unidad', 35],
    'estado' => ['Estado', 40],
    'observaciones' => ['Detalle Pedido', 110]
];

if ($result && $result->num_rows > 0) {
    $fill = false;
    while($row = $result->fetch_assoc()) {
        $pdf->SetFillColor($fill ? 224 : 255, $fill ? 235 : 255, 255);
        
        // Calcular la altura necesaria para la fila
        $height = 6;
        $observaciones = $row['observaciones'] ?? '';
        $nb = max(1, ceil(strlen($observaciones) / 70)); // Aproximadamente 70 caracteres por línea
        $height = max($height, $nb * 5); // 5 mm por línea de texto
        
        $pdf->CheckPageBreak($height);
        
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        
        foreach ($headers as $field => $header) {
            $value = $row[$field] ?? '';
            if ($field == 'observaciones') {
                $pdf->MultiCell($header[1], 5, utf8_decode($value), 'LRB', 'L', $fill);
                $pdf->SetXY($x + $header[1], $y);
            } else {
                $pdf->Cell($header[1], $height, utf8_decode($value), 'LRB', 0, 'L', $fill);
            }
            $x += $header[1];
        }
        
        $pdf->Ln($height);
        $fill = !$fill;
    }
} else {
    $pdf->Cell(0, 10, 'No se encontraron resultados', 1, 1, 'C');
}

$pdf->Output('solicitudes.pdf', 'I');