<?php
$projectRoot = dirname(__FILE__, 3); 
require_once dirname(__DIR__, 2) . '/config/config.php'; 
require_once ROOT_PATH . '/sec/init.php';
require_once ROOT_PATH . '/includes/session.php';   
require_once ROOT_PATH . '/sec/auth_check.php';       
require_once $projectRoot . '/includes/functions.php'; 

requireLogin();

// Obtener datos
$niveles_urgencias = getAllNivelesUrgencias();

// Incluir header
include ROOT_PATH . '/includes/header.php';

// Configuración de la vista
$pageTitle = '🚨 Niveles de Urgencia';
$createUrl = 'create.php';
$backUrl = BASE_URL . '/includes/header.php';
$emptyIcon = '🚨';

$columns = [
    'id' => 'ID',
    'nombre_urgencia' => 'Nombre',
    'descripcion_urgencias' => 'Descripción'
];

$data = $niveles_urgencias;

// Función personalizada para renderizar celdas
function renderTableCells($item, $columns) {
    foreach (array_keys($columns) as $key) {
        $value = $item[$key] ?? 'N/A';
        
        if ($key === 'id') {
            echo '<td><strong>#' . str_pad($value, 3, '0', STR_PAD_LEFT) . '</strong></td>';
        } elseif ($key === 'nombre_urgencia') {
            // Badge de urgencia con colores
            $urgencia_class = match(strtolower($value)) {
                'alta', 'crítica' => 'badge bg-danger',
                'media', 'moderada' => 'badge bg-warning text-dark',
                default => 'badge bg-success'
            };
            echo '<td><span class="' . $urgencia_class . '">' . htmlspecialchars($value) . '</span></td>';
        } else {
            echo '<td>' . htmlspecialchars($value) . '</td>';
        }
    }
}

// Incluir plantilla base
include dirname(__DIR__) . '/_base_crud_read.php';
?>