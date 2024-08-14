<?php
session_start();
require_once dirname(__DIR__) . '/config/config.php';
require_once ROOT_PATH . '/includes/auth.php';

requireLogin();

include ROOT_PATH . '/includes/header.php';
?>

<h1 style="text-align: center;">Bienvenido, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
<p style="text-align: center; font-size:20px">Este es el panel de control. Utiliza el menú de navegación para acceder a las diferentes secciones.</p>