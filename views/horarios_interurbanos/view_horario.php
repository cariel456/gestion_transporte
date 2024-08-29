<?php
$projectRoot = dirname(__FILE__, 3); 
require_once dirname(__DIR__, 2) . '/config/config.php'; 
require_once ROOT_PATH . '/sec/init.php';
require_once ROOT_PATH . '/includes/session.php';   
require_once ROOT_PATH . '/sec/auth_check.php';       
require_once $projectRoot . '/includes/functions.php'; 

$id = $_GET['id'] ?? null;

if (!$id) {
    $_SESSION['error'] = "ID de horario no proporcionado.";
    header("Location: read.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'update_master') {
            $servicio1 = $_POST['servicio1'];
            $servicio2 = $_POST['servicio2'];
            $servicio3 = $_POST['servicio3'];
            $terminal_salida = $_POST['terminal_salida'];
            $terminal_llegada = $_POST['terminal_llegada'];
            
            if (updateHorarioInterurbano($id, $servicio1, $servicio2, $servicio3, $terminal_salida, $terminal_llegada)) {
                $_SESSION['message'] = "Horario actualizado exitosamente.";
            } else {
                $_SESSION['error'] = "Error al actualizar el horario.";
            }
        } elseif ($_POST['action'] === 'delete_master') {
            if (deleteHorarioInterurbano($id)) {
                $_SESSION['message'] = "Horario eliminado exitosamente.";
                header("Location: read.php");
                exit();
            } else {
                $_SESSION['error'] = "Error al eliminar el horario.";
            }
        } elseif ($_POST['action'] === 'update_detail') {
            $detalle_id = $_POST['detalle_id'];
            $hora1 = $_POST['hora1'];
            $hora2 = $_POST['hora2'];
            
            if (updateHorarioInterurbanoDetalle($detalle_id, $hora1, $hora2)) {
                $_SESSION['message'] = "Detalle actualizado exitosamente.";
            } else {
                $_SESSION['error'] = "Error al actualizar el detalle.";
            }
        } elseif ($_POST['action'] === 'delete_detail') {
            $detalle_id = $_POST['detalle_id'];
            
            if (deleteHorarioInterurbanoDetalle($detalle_id)) {
                $_SESSION['message'] = "Detalle eliminado exitosamente.";
            } else {
                $_SESSION['error'] = "Error al eliminar el detalle.";
            }
        }
    }
    
    // Recargar la página para mostrar los cambios
    header("Location: view_horario.php?id=$id");
    exit();
}

$horario = getHorarioDetails($id);
$detalles = getHorarioDetalles($id);
$servicios = getAllServicios();
$terminales = getAllTerminales();

include ROOT_PATH . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver y Editar Horario Interurbano</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Detalles del Horario Interurbano</h1>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="hidden" name="action" value="update_master">
            <div class="mb-3">
                <label for="servicio1" class="form-label">Servicio 1</label>
                <select class="form-select" id="servicio1" name="servicio1" required>
                    <?php foreach ($servicios as $servicio): ?>
                        <option value="<?php echo $servicio['id']; ?>" <?php echo ($servicio['id'] == $horario['servicio1']) ? 'selected' : ''; ?>><?php echo $servicio['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="servicio2" class="form-label">Servicio 2</label>
                <select class="form-select" id="servicio2" name="servicio2">
                    <option value="">Ninguno</option>
                    <?php foreach ($servicios as $servicio): ?>
                        <option value="<?php echo $servicio['id']; ?>" <?php echo ($servicio['id'] == $horario['servicio2']) ? 'selected' : ''; ?>><?php echo $servicio['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="servicio3" class="form-label">Servicio 3</label>
                <select class="form-select" id="servicio3" name="servicio3">
                    <option value="">Ninguno</option>
                    <?php foreach ($servicios as $servicio): ?>
                        <option value="<?php echo $servicio['id']; ?>" <?php echo ($servicio['id'] == $horario['servicio3']) ? 'selected' : ''; ?>><?php echo $servicio['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="terminal_salida" class="form-label">Terminal de Salida</label>
                <select class="form-select" id="terminal_salida" name="terminal_salida" required>
                    <?php foreach ($terminales as $terminal): ?>
                        <option value="<?php echo $terminal['id']; ?>" <?php echo ($terminal['id'] == $horario['terminal_salida']) ? 'selected' : ''; ?>><?php echo $terminal['nombre_terminal']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="terminal_llegada" class="form-label">Terminal de Llegada</label>
                <select class="form-select" id="terminal_llegada" name="terminal_llegada" required>
                    <?php foreach ($terminales as $terminal): ?>
                        <option value="<?php echo $terminal['id']; ?>" <?php echo ($terminal['id'] == $horario['terminal_llegada']) ? 'selected' : ''; ?>><?php echo $terminal['nombre_terminal']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar Horario</button>
        </form>
        
        <h2 class="mt-4">Detalles de Horarios</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Hora Salida</th>
                    <th>Hora Llegada</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detalles as $detalle): ?>
                <tr>
                    <form method="POST">
                        <input type="hidden" name="action" value="update_detail">
                        <input type="hidden" name="detalle_id" value="<?php echo $detalle['id']; ?>">
                        <td><input type="time" class="form-control" name="hora1" value="<?php echo $detalle['hora1']; ?>" required></td>
                        <td><input type="time" class="form-control" name="hora2" value="<?php echo $detalle['hora2']; ?>" required></td>
                        <td>
                            <button type="submit" class="btn btn-warning btn-sm">Actualizar</button>
                            <button type="submit" class="btn btn-danger btn-sm" name="action" value="delete_detail" onclick="return confirm('¿Está seguro de que desea eliminar este detalle?')">Eliminar</button>
                        </td>
                    </form>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <form method="POST" class="mt-3">
            <input type="hidden" name="action" value="delete_master">
            <button type="submit" class="btn btn-danger" onclick="return confirm('¿Está seguro de que desea eliminar este horario y todos sus detalles?')">Eliminar Horario Completo</button>
        </form>
        
        <a href="read.php" class="btn btn-secondary mt-3">Volver a la lista</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>