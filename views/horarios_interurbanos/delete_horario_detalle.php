<?php
session_start();
$projectRoot = dirname(__FILE__, 3);
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once $projectRoot . '/includes/functions.php';
require_once ROOT_PATH . '/includes/auth.php';

$id = $_GET['id'] ?? null;
$horario_id = $_GET['horario_id'] ?? null;

if (!$id || !$horario_id) {
    header('Location: read.php');
    exit;
}

if (deleteHorarioInterurbanoDetalle($id)) {
    $_SESSION['message'] = "Detalle de horario eliminado exitosamente.";
} else {
    $_SESSION['message'] = "Error al eliminar el detalle de horario.";
}

header("Location: view_horario.php?id=$horario_id");
exit;