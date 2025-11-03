<?php
$projectRoot = dirname(__FILE__, 3); 
require_once dirname(__DIR__, 2) . '/config/config.php'; 
require_once ROOT_PATH . '/sec/init.php';
require_once ROOT_PATH . '/includes/session.php';   
require_once ROOT_PATH . '/sec/auth_check.php';       
require_once $projectRoot . '/includes/functions.php'; 

requireLogin();

// Obtener datos
$provincias = getAllProvincias();
$paises = getAllPaises();

// Incluir header
include ROOT_PATH . '/includes/header.php';

// Configuración de la vista
$pageTitle = '🗺️ Provincias';
$createUrl = 'create.php';
$backUrl = BASE_URL . '/includes/header.php';
$emptyIcon = '🗺️';

$columns = [
    'id' => 'ID',
    'nombre_provincia' => 'Nombre',
    'descripcion_provincia' => 'Descripción',
    'pais' => 'País'
];

$data = array_values($provincias);

// Función personalizada para renderizar celdas
function renderTableCells($item, $columns) {
    global $paises;
    
    foreach (array_keys($columns) as $key) {
        $value = $item[$key] ?? 'N/A';
        
        // Formateo especial según la columna
        if ($key === 'id') {
            echo '<td><strong>#' . str_pad($value, 3, '0', STR_PAD_LEFT) . '</strong></td>';
        } elseif ($key === 'pais') {
            $paisNombre = isset($paises[$value]) ? $paises[$value]['nombre_pais'] : 'N/A';
            echo '<td>' . htmlspecialchars($paisNombre) . '</td>';
        } else {
            echo '<td>' . htmlspecialchars($value) . '</td>';
        }
    }
}

// Incluir plantilla base
include dirname(__DIR__) . '/_base_crud_read.php';
?>