<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/functions.php';

if (isset($_GET['terminal_id'])) {
    $terminal_id = $_GET['terminal_id'];
    $lineas = getLineasPorTerminal($terminal_id);
    echo json_encode($lineas);
} else {
    echo json_encode([]);
}