<?php
$projectRoot = dirname(__FILE__, 3); 
require_once dirname(__DIR__, 2) . '/config/config.php'; 
require_once ROOT_PATH . '/sec/init.php';
require_once ROOT_PATH . '/includes/session.php';   
require_once ROOT_PATH . '/sec/auth_check.php';       
require_once $projectRoot . '/includes/functions.php'; 

requireLogin();

// Obtener datos
$localidades = getAllLocalidades();
$provincias = getAllProvincias();

// Incluir header
include ROOT_PATH . '/includes/header.php';

// Configuración de la vista
$pageTitle = '🏘️ Localidades';
$createUrl = 'create.php';
$backUrl = BASE_URL . '/includes/header.php';
$emptyIcon = '🏘️';

$columns = [
    'id' => 'ID',
    'nombre_localidad' => 'Nombre',
    'descripcion_localidad' => 'Descripción',
    'provincia' => 'Provincia'
];

$data = $localidades;

// Función personalizada para renderizar celdas
function renderTableCells($item, $columns) {
    global $provincias;
    
    foreach (array_keys($columns) as $key) {
        $value = $item[$key] ?? 'N/A';
        
        // Formateo especial según la columna
        if ($key === 'id') {
            echo '<td><strong>#' . str_pad($value, 3, '0', STR_PAD_LEFT) . '</strong></td>';
        } elseif ($key === 'provincia') {
            $provinciaNombre = isset($provincias[$value]) ? $provincias[$value]['nombre_provincia'] : 'N/A';
            echo '<td>' . htmlspecialchars($provinciaNombre) . '</td>';
        } else {
            echo '<td>' . htmlspecialchars($value) . '</td>';
        }
    }
}

// Incluir plantilla base
include dirname(__DIR__) . '/_base_crud_read.php';
?>