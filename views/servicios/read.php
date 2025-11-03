<?php
$projectRoot = dirname(__FILE__, 3); 
require_once dirname(__DIR__, 2) . '/config/config.php'; 
require_once ROOT_PATH . '/sec/init.php';
require_once ROOT_PATH . '/includes/session.php';   
require_once ROOT_PATH . '/sec/auth_check.php';       
require_once $projectRoot . '/includes/functions.php'; 

requireLogin();

$servicios = getAllServicios();

include ROOT_PATH . '/includes/header.php';

// Configuración de la vista
$pageTitle = '🌎 Servicios o lineas';
$createUrl = 'create.php';
$backUrl = BASE_URL . '/includes/header.php';
$emptyIcon = '🌎';

$columns = [
    'id' => 'ID',
    'nombre' => 'Nombre',
    'descripcion' => 'Descripción'
];
$data = array_values($servicios);
// Función personalizada para renderizar celdas
function renderTableCells($item, $columns) {
    foreach (array_keys($columns) as $key) {
        $value = $item[$key] ?? 'N/A';
        
        // Formateo especial para ID
        if ($key === 'id') {
            echo '<td><strong>#' . str_pad($value, 3, '0', STR_PAD_LEFT) . '</strong></td>';
        } else {
            echo '<td>' . htmlspecialchars($value) . '</td>';
        }
    }
}

// Incluir plantilla base
include dirname(__DIR__) . '/_base_crud_read.php';
?>