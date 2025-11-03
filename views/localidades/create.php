<?php
$projectRoot = dirname(__FILE__, 3); 
require_once dirname(__DIR__, 2) . '/config/config.php'; 
require_once ROOT_PATH . '/sec/init.php';
require_once ROOT_PATH . '/includes/session.php';   
require_once ROOT_PATH . '/sec/auth_check.php';       
require_once $projectRoot . '/includes/functions.php'; 

requireLogin();

// Obtener datos relacionados
$provincias = getAllProvincias();

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nombre_localidad' => $_POST['nombre_localidad'],
        'descripcion_localidad' => $_POST['descripcion_localidad'],
        'provincia' => $_POST['provincia']
    ];
    
    if (createLocalidad($data)) {
        header("Location: read.php");
        exit();
    } else {
        $error = "Error al crear la localidad";
    }
}

// Incluir header
include ROOT_PATH . '/includes/header.php';

// Configuración de la vista
$pageTitle = '🏘️ Crear Localidad';
$backUrl = 'read.php';

// Preparar opciones para el select de provincias
$provinciasOptions = [];
foreach ($provincias as $provincia) {
    $provinciasOptions[$provincia['id']] = $provincia['nombre_provincia'];
}

// Definir campos del formulario
$formFields = [
    [
        'type' => 'text',
        'name' => 'nombre_localidad',
        'label' => 'Nombre de la Localidad',
        'required' => true,
        'placeholder' => 'Ej: San Carlos de Bariloche'
    ],
    [
        'type' => 'textarea',
        'name' => 'descripcion_localidad',
        'label' => 'Descripción',
        'rows' => 3,
        'placeholder' => 'Descripción de la localidad'
    ],
    [
        'type' => 'select',
        'name' => 'provincia',
        'label' => 'Provincia',
        'required' => true,
        'options' => $provinciasOptions
    ]
];

// Incluir plantilla base
include dirname(__DIR__) . '/_base_crud_create.php';
?>