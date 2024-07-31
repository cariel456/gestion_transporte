<?php
$projectRoot = dirname(__FILE__, 3);
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once $projectRoot . '/includes/functions.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: read.php");
    exit();
}

if (deleteCategoriaPersona($id)) {
    header("Location: read.php");
    exit();
} else {
    echo "Error al eliminar la categoría de persona";
}

?>

