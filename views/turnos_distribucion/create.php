<?php
$projectRoot = dirname(__FILE__, 3); 
require_once dirname(__DIR__, 2) . '/config/config.php'; 
require_once ROOT_PATH . '/sec/init.php';
require_once ROOT_PATH . '/includes/session.php';   
require_once ROOT_PATH . '/sec/auth_check.php';       
require_once $projectRoot . '/includes/functions.php'; 

requireLogin();

// Obtener los tipos de servicio
$tiposServicio = getTurnosTiposServicios();
// Obtener la lista de turnos
$turnos = getTurnos();
// Obtener la lista de servicios de turnos
$turnosServicios = getTurnosServicios();
// Obtener la lista de personal
$personal = getPersonal();

// Manejar el envío del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $tipoServicio = $_POST['tipo_servicio'];

    // Insertar en la tabla maestro
    $id_distribucion = createTurnosDistribucion($nombre, $descripcion, $tipoServicio);
    
    if ($id_distribucion) {
        // Insertar detalles
        $turnos = $_POST['turnos'];
        $turnosServicios = $_POST['turnos_servicios'];
        $personals = $_POST['personal'];
        $fechas = $_POST['fechas'];
        
        if (createTurnosDistribucionDetalles($id_distribucion, $turnos, $turnosServicios, $personals, $fechas)) {
            $_SESSION['message'] = "Distribución de turnos creada exitosamente.";
            header("Location: read.php");
            exit();
        } else {
            $error = "Error al crear los detalles de la distribución de turnos.";
        }
    } else {
        $error = "Error al crear la distribución de turnos.";
    }
}
include ROOT_PATH . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Distribución de Turnos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Crear Distribución de Turnos</h1>
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre turno o servicio</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="tipo_servicio" class="form-label">Tipo de Servicio</label>
                <select class="form-select" id="tipo_servicio" name="tipo_servicio" required>
                    <option value="">Seleccione un tipo de servicio</option>
                    <?php foreach ($tiposServicio as $tipoServicio) : ?>
                        <option value="<?php echo $tipoServicio['id']; ?>"><?php echo $tipoServicio['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <h2>Detalles de Turnos</h2>
            <table class="table table-striped table-hover" id="detalles-table">
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
                    <tr>
                        <td>
                            <select class="form-select" name="turnos[]" required>
                                <option value="">Seleccione un turno</option>
                                <?php foreach ($turnos as $turno) : ?>
                                    <option value="<?php echo $turno['id']; ?>"><?php echo $turno['nombre']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <select class="form-select" name="turnos_servicios[]" required>
                                <option value="">Seleccione un servicio</option>
                                <?php foreach ($turnosServicios as $servicio) : ?>
                                    <option value="<?php echo $servicio['id']; ?>"><?php echo $servicio['nombre']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <select class="form-select" name="personal[]" required>
                                <option value="">Seleccione un personal</option>
                                <?php foreach ($personal as $p) : ?>
                                    <option value="<?php echo $p['id']; ?>"><?php echo $p['nombre_personal']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <input type="date" class="form-control" name="fechas[]" required>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-row">Eliminar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <button type="button" class="btn btn-secondary mb-3" id="add-row">Agregar Fila</button>
            <div>
                <button type="submit" class="btn btn-primary">Guardar Distribución de Turnos</button>
                <a href="read.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
   <script>
        // Generar las opciones de los elementos <select> una vez
        var turnosOptions = '';
        <?php foreach ($turnos as $turno) { ?>
            turnosOptions += '<option value="<?php echo $turno['id']; ?>"><?php echo $turno['nombre']; ?></option>';
        <?php } ?>

        var turnosServiciosOptions = '';
        <?php foreach ($turnosServicios as $servicio) { ?>
            turnosServiciosOptions += '<option value="<?php echo $servicio['id']; ?>"><?php echo $servicio['nombre']; ?></option>';
        <?php } ?>

        var personalOptions = '';
        <?php foreach ($personal as $p) { ?>
            personalOptions += '<option value="<?php echo $p['id']; ?>"><?php echo $p['nombre_personal']; ?></option>';
        <?php } ?>

        // Agregar una nueva fila a la tabla
        document.getElementById('add-row').addEventListener('click', function() {
            var table = document.getElementById('detalles-table');
            var row = table.insertRow(-1);
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            var cell4 = row.insertCell(3);
            var cell5 = row.insertCell(4);

            cell1.innerHTML = '<select class="form-select" name="turnos[]" required><option value="">Seleccione un turno</option>' + turnosOptions + '</select>';
            cell2.innerHTML = '<select class="form-select" name="turnos_servicios[]" required><option value="">Seleccione un servicio</option>' + turnosServiciosOptions + '</select>';
            cell3.innerHTML = '<select class="form-select" name="personal[]" required><option value="">Seleccione un personal</option>' + personalOptions + '</select>';
            cell4.innerHTML = '<input type="date" class="form-control" name="fechas[]" required>';
            cell5.innerHTML = '<button type="button" class="btn btn-danger btn-sm remove-row">Eliminar</button>';
        });

        // Eliminar una fila de la tabla
        document.querySelectorAll('.remove-row').forEach(function(button) {
            button.addEventListener('click', function() {
                this.closest('tr').remove();
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>