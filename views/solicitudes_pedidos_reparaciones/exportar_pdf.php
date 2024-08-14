<?php
session_start();
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/functions.php';
require_once ROOT_PATH . '/lib/fpdf.php';

function escape($value) {
    global $conn;
    return $conn->real_escape_string($value);
}

function getSelectedFields() {
    $allFields = ['numero_solicitud', 'fecha_solicitud', 'solicitante_nombre', 'nombre_localidad', 'unidad_codigo', 'nivel_urgencia', 'grupo_funcion', 'especialidades', 'conductor_nombre', 'mantenimiento_nombre', 'habilitado'];
    $selectedFields = [];

    $filtersApplied = false;
    foreach ($allFields as $field) {
        if (!empty($_GET[$field])) {
            $selectedFields[] = $field;
            $filtersApplied = true;
        }
    }

    // Si no se aplicaron filtros, seleccionar todos los campos
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
        case 'conductor_nombre':
            return "p2.nombre_personal AS conductor_nombre";
        case 'mantenimiento_nombre':
            return "p3.nombre_personal AS mantenimiento_nombre";
        case 'unidad_codigo':
            return "u.codigo_interno AS unidad_codigo";
        case 'nombre_localidad':
            return "l.nombre_localidad";
        case 'especialidades':
            return "GROUP_CONCAT(DISTINCT et.nombre_especialidad SEPARATOR ', ') AS especialidades";
        default:
            return "s.$field";
    }
}, $selectedFields));

$sql = "SELECT $sqlFields
        FROM solicitudes_pedidos_reparaciones s
        LEFT JOIN personal p1 ON s.solicitante = p1.id
        LEFT JOIN personal p2 ON s.nombre_completo_conductor = p2.id
        LEFT JOIN personal p3 ON s.nombre_completo_mantenimiento = p3.id
        LEFT JOIN unidades u ON s.numero_unidad = u.id
        LEFT JOIN localidades l ON s.ubicacion = l.id
        LEFT JOIN solicitud_especialidades se ON s.id = se.solicitud_id
        LEFT JOIN especialidades_talleres et ON se.especialidad_id = et.id
        WHERE 1=1";

$whereClause = [];

if (!empty($_GET)) {
    if (!empty($_GET['numero_solicitud'])) {
        $whereClause[] = "s.numero_solicitud = " . escape($_GET['numero_solicitud']);
    }
    if (!empty($_GET['fecha_inicio']) && !empty($_GET['fecha_fin'])) {
        $whereClause[] = "s.fecha_solicitud BETWEEN '" . escape($_GET['fecha_inicio']) . "' AND '" . escape($_GET['fecha_fin']) . "'";
    }
    if (!empty($_GET['solicitante'])) {
        $whereClause[] = "s.solicitante = " . escape($_GET['solicitante']);
    }
    if (!empty($_GET['ubicacion'])) {
        $whereClause[] = "s.ubicacion = " . escape($_GET['ubicacion']);
    }
    if (!empty($_GET['numero_unidad'])) {
        $whereClause[] = "s.numero_unidad = " . escape($_GET['numero_unidad']);
    }
    if (!empty($_GET['nivel_urgencia'])) {
        $whereClause[] = "s.nivel_urgencia = " . escape($_GET['nivel_urgencia']);
    }
    if (!empty($_GET['grupo_funcion'])) {
        $whereClause[] = "s.grupo_funcion LIKE '%" . escape($_GET['grupo_funcion']) . "%'";
    }
    if (!empty($_GET['especialidades'])) {
        $especialidades_seleccionadas = array_map('escape', $_GET['especialidades']);
        $whereClause[] = "et.id IN ('" . implode("','", $especialidades_seleccionadas) . "')";
    }
    if (!empty($_GET['conductor'])) {
        $whereClause[] = "s.nombre_completo_conductor = " . escape($_GET['conductor']);
    }
    if (!empty($_GET['mantenimiento'])) {
        $whereClause[] = "s.nombre_completo_mantenimiento = " . escape($_GET['mantenimiento']);
    }
    if (isset($_GET['habilitado']) && $_GET['habilitado'] !== '') {
        $whereClause[] = "s.habilitado = " . (int)$_GET['habilitado'];
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
        $this->SetFont('Arial','B',15);
        $this->Cell(0,10,'Solicitudes de Pedidos y Reparaciones',0,1,'C');
        $this->Ln(10);
    }
}

$pdf = new PDF();

$orientation = (count($selectedFields) > 5) ? 'L' : 'P';
$pdf->AddPage($orientation);

$fontSize = max(6, min(10, 14 - count($selectedFields)));
$pdf->SetFont('Arial','',$fontSize);

$headers = [
    'numero_solicitud' => ['N° Solicitud', 20],
    'fecha_solicitud' => ['Fecha', 25],
    'solicitante_nombre' => ['Solicitante', 40],
    'nombre_localidad' => ['Ubicación', 40],
    'unidad_codigo' => ['Unidad', 20],
    'nivel_urgencia' => ['Urgencia', 20],
    'grupo_funcion' => ['Grupo Función', 30],
    'especialidades' => ['Especialidades', 40],
    'conductor_nombre' => ['Conductor', 40],
    'mantenimiento_nombre' => ['Mantenimiento', 40],
    'habilitado' => ['Habilitado', 20]
];

$pageWidth = $pdf->GetPageWidth() - 20;
$totalWidth = 0;
foreach ($selectedFields as $field) {
    $totalWidth += $headers[$field][1];
}
$scaleFactor = $pageWidth / $totalWidth;

foreach ($selectedFields as $field) {
    $width = $headers[$field][1] * $scaleFactor;
    $pdf->Cell($width, 7, $headers[$field][0], 1, 0, 'C');
}
$pdf->Ln();

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        foreach ($selectedFields as $field) {
            $width = $headers[$field][1] * $scaleFactor;
            $value = $field === 'habilitado' ? ($row[$field] ? 'Sí' : 'No') : ($row[$field] ?? '');
            $pdf->Cell($width, 6, utf8_decode($value), 1);
        }
        $pdf->Ln();
    }
} else {
    $pdf->Cell(0, 10, 'No se encontraron resultados', 1, 1, 'C');
}

$pdf->Output('solicitudes.pdf', 'I');