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
            $descripcion = $_POST['descripcion'];
            
            if (updateHorarioInterurbano($id, $servicio1, $servicio2, $servicio3, $terminal_salida, $terminal_llegada, $descripcion)) {
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
            
            if (deleteHorarioInterurbanoDetalles($detalle_id)) {
                $_SESSION['message'] = "Detalle eliminado exitosamente.";
            } else {
                $_SESSION['error'] = "Error al eliminar el detalle.";
            }
        } elseif ($_POST['action'] === 'add_detail') {
            $hora1 = $_POST['new_hora1'];
            $hora2 = $_POST['new_hora2'];
            
            if (insertHorarioInterurbanoDetalle($id, $hora1, $hora2)) {
                $_SESSION['message'] = "Nuevo detalle agregado exitosamente.";
            } else {
                $_SESSION['error'] = "Error al agregar el nuevo detalle.";
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1 class="mb-4">Detalles del Horario Interurbano</h1>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="mb-4">
            <input type="hidden" name="action" value="update_master">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="servicio1" class="form-label">Servicio 1</label>
                    <select class="form-select" id="servicio1" name="servicio1" required>
                        <?php foreach ($servicios as $servicio): ?>
                            <option value="<?php echo $servicio['id']; ?>" <?php echo ($servicio['id'] == $horario['servicio1']) ? 'selected' : ''; ?>><?php echo $servicio['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="servicio2" class="form-label">Servicio 2</label>
                    <select class="form-select" id="servicio2" name="servicio2">
                        <option value="">Ninguno</option>
                        <?php foreach ($servicios as $servicio): ?>
                            <option value="<?php echo $servicio['id']; ?>" <?php echo ($servicio['id'] == $horario['servicio2']) ? 'selected' : ''; ?>><?php echo $servicio['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="servicio3" class="form-label">Servicio 3</label>
                    <select class="form-select" id="servicio3" name="servicio3">
                        <option value="">Ninguno</option>
                        <?php foreach ($servicios as $servicio): ?>
                            <option value="<?php echo $servicio['id']; ?>" <?php echo ($servicio['id'] == $horario['servicio3']) ? 'selected' : ''; ?>><?php echo $servicio['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="terminal_salida" class="form-label">Terminal de Salida</label>
                    <select class="form-select" id="terminal_salida" name="terminal_salida" required>
                        <?php foreach ($terminales as $terminal): ?>
                            <option value="<?php echo $terminal['id']; ?>" <?php echo ($terminal['id'] == $horario['terminal_salida']) ? 'selected' : ''; ?>><?php echo $terminal['nombre_terminal']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="terminal_llegada" class="form-label">Terminal de Llegada</label>
                    <select class="form-select" id="terminal_llegada" name="terminal_llegada" required>
                        <?php foreach ($terminales as $terminal): ?>
                            <option value="<?php echo $terminal['id']; ?>" <?php echo ($terminal['id'] == $horario['terminal_llegada']) ? 'selected' : ''; ?>><?php echo $terminal['nombre_terminal']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <input type="text" class="form-control" id="descripcion" name="descripcion" value="<?php echo htmlspecialchars($horario['descripcion']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Actualizar Horario
            </button>
        </form>
        
        <h2 class="mt-4 mb-3">Detalles de Horarios</h2>
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="table-dark">
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
                                <div class="btn-group" role="group">
                                    <button type="submit" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="submit" class="btn btn-danger btn-sm" name="action" value="delete_detail" onclick="return confirm('¿Está seguro de que desea eliminar este detalle?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </form>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <form method="POST" class="mt-3">
            <input type="hidden" name="action" value="add_detail">
            <div class="row">
                <div class="col-md-5">
                    <input type="time" class="form-control" name="new_hora1" required placeholder="Nueva Hora Salida">
                </div>
                <div class="col-md-5">
                    <input type="time" class="form-control" name="new_hora2" required placeholder="Nueva Hora Llegada">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-plus-circle me-2"></i>Agregar
                    </button>
                </div>
            </div>
        </form>
        
        <div class="mt-4">
            <form method="POST" class="d-inline">
                <input type="hidden" name="action" value="delete_master">
                <button type="submit" class="btn btn-danger" onclick="return confirm('¿Está seguro de que desea eliminar este horario y todos sus detalles?')">
                    <i class="fas fa-trash-alt me-2"></i>Eliminar Horario Completo
                </button>
            </form>
            
            <a href="read.php" class="btn btn-secondary ms-2">
                <i class="fas fa-arrow-left me-2"></i>Volver a la lista
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>