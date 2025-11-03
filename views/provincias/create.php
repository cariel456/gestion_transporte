<?php
$projectRoot = dirname(__FILE__, 3); 
require_once dirname(__DIR__, 2) . '/config/config.php'; 
require_once ROOT_PATH . '/sec/init.php';
require_once ROOT_PATH . '/includes/session.php';   
require_once ROOT_PATH . '/sec/auth_check.php';       
require_once $projectRoot . '/includes/functions.php'; 

requireLogin();

// Obtener datos relacionados
$paises = getAllPaises();

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nombre_provincia' => $_POST['nombre_provincia'],
        'descripcion_provincia' => $_POST['descripcion_provincia'],
        'pais' => $_POST['pais']
    ];
    
    if (createProvincia($data)) {
        header("Location: read.php");
        exit();
    } else {
        $error = "Error al crear la provincia";
    }
}

// Incluir header
include ROOT_PATH . '/includes/header.php';

// Configuración de la vista
$pageTitle = '🗺️ Crear Provincia';
$backUrl = 'read.php';

// Preparar opciones para el select de países
$paisesOptions = [];
foreach ($paises as $pais) {
    $paisesOptions[$pais['id']] = $pais['nombre_pais'];
}

// Definir campos del formulario
$formFields = [
    [
        'type' => 'text',
        'name' => 'nombre_provincia',
        'label' => 'Nombre de la Provincia',
        'required' => true,
        'placeholder' => 'Ej: Buenos Aires'
    ],
    [
        'type' => 'textarea',
        'name' => 'descripcion_provincia',
        'label' => 'Descripción',
        'rows' => 3,
        'placeholder' => 'Descripción de la provincia'
    ],
    [
        'type' => 'select',
        'name' => 'pais',
        'label' => 'País',
        'required' => true,
        'options' => $paisesOptions
    ]
];

// Incluir plantilla base
include dirname(__DIR__) . '/_base_crud_create.php';
?>