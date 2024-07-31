<?php
$projectRoot = dirname(__FILE__, 3);
require_once $projectRoot . '/includes/functions.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: read.php");
    exit();
}

if (deleteUnidad($id)) {
    header("Location: read.php");
    exit();
} else {
    echo "Error al eliminar la unidad";
}
?>