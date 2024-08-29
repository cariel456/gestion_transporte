<?php
$projectRoot = dirname(__FILE__, 3); 
require_once dirname(__DIR__, 2) . '/config/config.php'; 
require_once ROOT_PATH . '/sec/init.php';
require_once ROOT_PATH . '/includes/session.php';   
require_once ROOT_PATH . '/sec/auth_check.php';       
require_once $projectRoot . '/includes/functions.php'; 

// Actualizar la última actividad
$_SESSION['last_activity'] = time();

requireLogin();

$solicitud_id = $_GET['id'] ?? null;
if (!$solicitud_id) {
    header("Location: read.php");
    exit();
}

$estados = getAllEstados();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_estado_id = $_POST['nuevo_estado'];
    $personal_id = $_SESSION['user_id'];

    if (actualizarEstadoSolicitud($solicitud_id, $nuevo_estado_id, $personal_id)) {
        header("Location: read.php");
        exit();
    } else {
        $error = "Error al actualizar el estado de la solicitud";
    }
}

include ROOT_PATH . '/includes/header.php';
?>

<div class="container mt-5">
    <h2>Actualizar Estado de la Solicitud</h2>
    <form method="POST">
        <div class="form-group">
            <label for="nuevo_estado">Nuevo Estado</label>
            <select class="form-control" id="nuevo_estado" name="nuevo_estado" style="margin-bottom: 15px; margin-top: 15px" required>
                <?php foreach ($estados as $estado): ?>
                    <option value="<?php echo $estado['id']; ?>"><?php echo $estado['nombre_estado']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar Estado</button>
        <a href="read.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>