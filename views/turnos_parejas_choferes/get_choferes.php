<?php
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once ROOT_PATH . '/includes/functions.php';

if(isset($_GET['pareja_id'])) {
    $pareja_id = $_GET['pareja_id'];
    $pareja = getParejaChoferesById($pareja_id);
    
    $chofer1 = getPersonalById($pareja['id_chofer']);
    $chofer2 = getPersonalById($pareja['id_chofer2']);
    
    $response = [
        'chofer1' => ['id' => $chofer1['id'], 'nombre' => $chofer1['nombre_personal']],
        'chofer2' => ['id' => $chofer2['id'], 'nombre' => $chofer2['nombre_personal']]
    ];
    
    echo json_encode($response);
}