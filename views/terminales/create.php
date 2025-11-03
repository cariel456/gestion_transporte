<?php
$projectRoot = dirname(__FILE__, 3); 
require_once dirname(__DIR__, 2) . '/config/config.php'; 
require_once ROOT_PATH . '/sec/init.php';
require_once ROOT_PATH . '/includes/session.php';   
require_once ROOT_PATH . '/sec/auth_check.php';       
require_once $projectRoot . '/includes/functions.php'; 

requireLogin();

// Obtener datos relacionados
$localidades = getAllLocalidades();

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nombre_terminal' => $_POST['nombre_terminal'],
        'descripcion_terminal' => $_POST['descripcion_terminal'],
        'localidad' => $_POST['localidad'],
        'telefono' => $_POST['telefono'] ?? '',
        'telefono2' => $_POST['telefono2'] ?? '',
        'correo' => $_POST['correo'] ?? '',
        'correo2' => $_POST['correo2'] ?? '',
        'web' => $_POST['web'] ?? ''
    ];
    
    if (createTerminal($data)) {
        header("Location: read.php");
        exit();
    } else {
        $error = "Error al crear la terminal";
    }
}

// Incluir header
include ROOT_PATH . '/includes/header.php';

// Configuración de la vista
$pageTitle = '🏢 Crear Terminal';
$backUrl = 'read.php';

// Preparar opciones para el select de localidades
$localidadesOptions = [];
foreach ($localidades as $localidad) {
    $localidadesOptions[$localidad['id']] = $localidad['nombre_localidad'];
}

// Definir campos del formulario
$formFields = [
    [
        'type' => 'text',
        'name' => 'nombre_terminal',
        'label' => 'Nombre de la Terminal',
        'required' => true,
        'placeholder' => 'Ej: Terminal Central'
    ],
    [
        'type' => 'textarea',
        'name' => 'descripcion_terminal',
        'label' => 'Descripción',
        'rows' => 3,
        'placeholder' => 'Descripción detallada de la terminal'
    ],
    [
        'type' => 'select',
        'name' => 'localidad',
        'label' => 'Localidad',
        'required' => true,
        'options' => $localidadesOptions
    ],
    [
        'type' => 'tel',
        'name' => 'telefono',
        'label' => 'Teléfono Principal',
        'placeholder' => 'Ej: +54 299 123-4567'
    ],
    [
        'type' => 'tel',
        'name' => 'telefono2',
        'label' => 'Teléfono Secundario',
        'placeholder' => 'Ej: +54 299 765-4321'
    ],
    [
        'type' => 'email',
        'name' => 'correo',
        'label' => 'Correo Electrónico Principal',
        'placeholder' => 'correo@ejemplo.com'
    ],
    [
        'type' => 'email',
        'name' => 'correo2',
        'label' => 'Correo Electrónico Secundario',
        'placeholder' => 'correo2@ejemplo.com'
    ],
    [
        'type' => 'url',
        'name' => 'web',
        'label' => 'Sitio Web',
        'placeholder' => 'https://www.ejemplo.com'
    ]
];

// Incluir plantilla base
include dirname(__DIR__) . '/_base_crud_create.php';
?>