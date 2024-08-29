<?php
session_start();
$projectRoot = dirname(__FILE__, 3);
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once $projectRoot . '/includes/functions.php';

// Verificar si el usuario está autenticado
requireLogin();

// Verificar si se ha proporcionado un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "ID de distribución de turnos no válido.";
    header("Location: read.php");
    exit();
}

$id_distribucion = $_GET['id'];

// Función para eliminar la distribución de turnos y sus detalles
function eliminarDistribucionTurnos($id_distribucion) {
    global $conn;
    
    // Iniciar transacción
    $conn->begin_transaction();
    
    try {
        // Eliminar detalles
        $sql_detalles = "DELETE FROM turnos_distribucion_detalles WHERE id_distribucion = ?";
        $stmt_detalles = $conn->prepare($sql_detalles);
        $stmt_detalles->bind_param("i", $id_distribucion);
        $stmt_detalles->execute();
        
        // Eliminar maestro
        $sql_maestro = "DELETE FROM turnos_distribucion WHERE id = ?";
        $stmt_maestro = $conn->prepare($sql_maestro);
        $stmt_maestro->bind_param("i", $id_distribucion);
        $stmt_maestro->execute();
        
        // Confirmar transacción
        $conn->commit();
        return true;
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $conn->rollback();
        return false;
    }
}

// Procesar la eliminación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (eliminarDistribucionTurnos($id_distribucion)) {
        $_SESSION['message'] = "Distribución de turnos eliminada exitosamente.";
        header("Location: read.php");
        exit();
    } else {
        $_SESSION['error'] = "Error al eliminar la distribución de turnos.";
    }
}

// Obtener información de la distribución de turnos
$distribucion = getDistribucionTurnos($id_distribucion);

if (!$distribucion) {
    $_SESSION['error'] = "Distribución de turnos no encontrada.";
    header("Location: read.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Distribución de Turnos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Eliminar Distribución de Turnos</h1>
        <?php if (isset($_SESSION['error'])) : ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <p>¿Está seguro de que desea eliminar la siguiente distribución de turnos?</p>
        <ul>
            <li><strong>Nombre:</strong> <?php echo htmlspecialchars($distribucion['nombre']); ?></li>
            <li><strong>Descripción:</strong> <?php echo htmlspecialchars($distribucion['descripcion']); ?></li>
            <li><strong>Tipo de Servicio:</strong> <?php echo htmlspecialchars($distribucion['tipo_servicio']); ?></li>
        </ul>
        <form method="POST">
            <button type="submit" class="btn btn-danger">Eliminar</button>
            <a href="read.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>