<?php
$projectRoot = dirname(__FILE__, 3); 
require_once dirname(__DIR__, 2) . '/config/config.php'; 
require_once ROOT_PATH . '/sec/init.php';
require_once ROOT_PATH . '/includes/session.php';   
require_once ROOT_PATH . '/sec/auth_check.php';       
require_once $projectRoot . '/includes/functions.php'; 

requireLogin();

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [/* tus campos */];
    if (createFuncion($data)) {
        header("Location: read.php");
        exit();
    } else {
        $error = "Error al crear";
    }
}

include ROOT_PATH . '/includes/header.php';

$pageTitle = '🌎 Crear País';
$backUrl = 'read.php';

$formFields = [
    [
        'type' => 'text',
        'name' => 'nombre_pais',
        'label' => 'Nombre del País',
        'required' => true
    ],
    [
        'type' => 'textarea',
        'name' => 'descripcion_pais',
        'label' => 'Descripción',
        'rows' => 3
    ]
];

include dirname(__DIR__) . '/_base_crud_create.php';
?>