<?php
session_start();
$projectRoot = dirname(__FILE__, 3);
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once $projectRoot . '/includes/functions.php';
require_once ROOT_PATH . '/includes/auth.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: read.php');
    exit;
}

if (deleteHorarioInterurbano($id)) {
    $_SESSION['message'] = "Horario interurbano eliminado exitosamente.";
} else {
    $_SESSION['message'] = "Error al eliminar el horario interurbano.";
}

header('Location: read.php');
exit;