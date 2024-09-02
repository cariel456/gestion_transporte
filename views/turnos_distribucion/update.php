<?php
$projectRoot = dirname(__FILE__, 3);
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once ROOT_PATH . '/sec/init.php';
require_once ROOT_PATH . '/includes/session.php';
require_once ROOT_PATH . '/sec/auth_check.php';
require_once $projectRoot . '/includes/functions.php';

requireLogin();

$tiposServicio = getTurnosTiposServicios();
$turnos = getTurnos();
$turnosServicios = getTurnosServicios();
$personal = getPersonal();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_distribucion = $_POST['id_distribucion'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $tipoServicio = $_POST['tipo_servicio'];

    if (updateTurnosDistribucion($id_distribucion, $nombre, $descripcion, $tipoServicio)) {
        $turnos = $_POST['turnos'];
        $turnosServicios = $_POST['turnos_servicios'];
        $personals = $_POST['personal'];
        $fechas = $_POST['fechas'];

        if (updateTurnosDistribucionDetalles($id_distribucion, $turnos, $turnosServicios, $personals, $fechas)) {
            $_SESSION['message'] = "Distribución de turnos actualizada exitosamente.";
            header("Location: read.php");
            exit();
        } else {
            $error = "Error al actualizar los detalles de la distribución de turnos.";
        }
    } else {
        $error = "Error al actualizar la distribución de turnos.";
    }
} else {
    $id_distribucion = $_GET['id'];
    $distribucion = getTurnosDistribucionById($id_distribucion);
    $detalles = getTurnosDistribucionDetallesById($id_distribucion);
}
include ROOT_PATH . '/includes/header.php';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <title>Actualizar Distribución de Turnos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Actualizar Distribución de Turnos</h1>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="POST">
        <input type="hidden" name="id_distribucion" value="<?php echo $distribucion['id']; ?>">
        <div class="mb-3">
            <label class="form-label" for="nombre">Nombre</label>
            <input class="form-control" name="nombre" required id="nombre" value="<?php echo $distribucion['nombre']; ?>">
        </div>
        <div class="mb-3">
            <label class="form-label" for="descripcion">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" required rows="3"><?php echo $distribucion['descripcion']; ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label" for="tipo_servicio">Tipo de Servicio</label>
            <select class="form-select" name="tipo_servicio" required id="tipo_servicio">
                <option value="">Seleccione un tipo de servicio</option>
                <?php foreach ($tiposServicio as $tipoServicio): ?>
                    <option value="<?php echo $tipoServicio['id']; ?>" <?php echo $distribucion['tipo_servicio'] == $tipoServicio['id'] ? 'selected' : ''; ?>>
                        <?php echo $tipoServicio['nombre']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <h2>Detalles de Turnos</h2>
        <table class="table table-hover table-striped" id="detalles-table">
            <thead>
            <tr>
                <th>Turno</th>
                <th>Servicio</th>
                <th>Personal</th>
                <th>Fecha</th>
                <th>Acción</th>
            </tr>
            </thead>
            <tbody>
<?php foreach ($detalles as $detalle): ?>
    <tr>
        <td>
            <select class="form-select" name="turnos[]" required>
                <option value="">Seleccione un turno</option>
                <?php foreach ($turnos as $turno): ?>
                    <option value="<?php echo $turno['id']; ?>" <?php echo (int)$detalle['turno_id'] === (int)$turno['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($turno['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <select class="form-select" name="turnos_servicios[]" required>
                <option value="">Seleccione un servicio</option>
                <?php foreach ($turnosServicios as $servicio): ?>
                    <option value="<?php echo $servicio['id']; ?>" <?php echo (int)$detalle['servicio_id'] === (int)$servicio['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($servicio['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <select class="form-select" name="personal[]" required>
                <option value="">Seleccione un personal</option>
                <?php foreach ($personal as $p): ?>
                    <option value="<?php echo $p['id']; ?>" <?php echo (int)$detalle['personal_id'] === (int)$p['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($p['nombre_personal']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <input class="form-control" name="fechas[]" required type="date" value="<?php echo htmlspecialchars($detalle['fecha']); ?>">
        </td>
        <td>
            <button class="btn btn-danger btn-sm remove-row" type="button">Eliminar</button>
        </td>
    </tr>
<?php endforeach; ?>
</tbody>

        </table>
        <button class="btn btn-secondary mb-3" type="button" id="add-row">Agregar Fila</button>
        <div>
            <button class="btn btn-primary" type="submit">Guardar Distribución de Turnos</button>
            <a class="btn btn-secondary" href="read.php">Cancelar</a>
        </div>
    </form>
</div>

</body>
</html>
