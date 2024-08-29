<?php
session_start();
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/functions.php';
include ROOT_PATH . '/includes/header.php';
$localidades = obtenerLocalidades();
$rangosHorarios = obtenerRangosHorarios();
$zonasHorarios = obtenerZonasHorarios();
$zonasHorarioDetalle = obtenerZonasHorariosDetalle();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Insertar en horarios_urbanos
    $localidad = $_POST['localidad'];
    $rangos_horarios = $_POST['rangos_horarios'];
    $zonas_horarios = $_POST['zonas_horarios'];
    $descripcion = $_POST['descripcion'];
    $horarioUrbanoId = crearHorarioUrbano($localidad, $rangos_horarios, $zonas_horarios, $descripcion);

    // Insertar en horarios_urbanos_detalle
    foreach ($_POST['detalles'] as $detalle) {
        crearHorarioUrbanoDetalle($horarioUrbanoId, $detalle['hora'], $detalle['zona_horario_detalle'], $detalle['hora2'], $detalle['zona_horario_detalle2']);
    }

    header("Location: index.php");
}
?>

<div class="container">
    <h2>Crear Nuevo Horario Urbano</h2>
    <form method="POST">
        <div class="form-group">
            <label for="localidad">Localidad</label>
            <select name="localidad" class="form-control">
                <?php foreach ($localidades as $localidad): ?>
                    <option value="<?php echo $localidad['id']; ?>"><?php echo $localidad['nombre_localidad']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="rangos_horarios">Rangos Horarios</label>
            <select name="rangos_horarios" class="form-control">
                <?php foreach ($rangosHorarios as $rango): ?>
                    <option value="<?php echo $rango['id']; ?>"><?php echo $rango['nombre']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="zonas_horarios">Zonas Horarios</label>
            <select name="zonas_horarios" class="form-control">
                <?php foreach ($zonasHorarios as $zona): ?>
                    <option value="<?php echo $zona['id']; ?>"><?php echo $zona['nombre']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="descripcion">Descripción</label>
            <input type="text" name="descripcion" class="form-control">
        </div>

        <h3>Detalles de Horarios Urbanos</h3>
        <table class="table" id="detallesTable">
            <thead>
                <tr>
                    <th>Hora</th>
                    <th>Zona Horario Detalle</th>
                    <th>Hora 2</th>
                    <th>Zona Horario Detalle 2</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="time" name="detalles[0][hora]" class="form-control"></td>
                    <td>
                        <select name="detalles[0][zona_horario_detalle]" class="form-control">
                            <?php foreach ($zonasHorarioDetalle as $zona): ?>
                                <option value="<?php echo $zona['id']; ?>"><?php echo $zona['nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><input type="time" name="detalles[0][hora2]" class="form-control"></td>
                    <td>
                        <select name="detalles[0][zona_horario_detalle2]" class="form-control">
                            <?php foreach ($zonasHorarioDetalle as $zona): ?>
                                <option value="<?php echo $zona['id']; ?>"><?php echo $zona['nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><button type="button" class="btn btn-danger" onclick="removeRow(this)">Eliminar</button></td>
                </tr>
            </tbody>
        </table>
        <button type="button" class="btn btn-secondary" onclick="addRow()">Agregar Detalle</button>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>

<script>
function addRow() {
    var table = document.getElementById('detallesTable').getElementsByTagName('tbody')[0];
    var rowCount = table.rows.length;
    var row = table.insertRow(rowCount);
    row.innerHTML = `
        <td><input type="time" name="detalles[${rowCount}][hora]" class="form-control"></td>
        <td>
            <select name="detalles[${rowCount}][zona_horario_detalle]" class="form-control">
                <?php foreach ($zonasHorarioDetalle as $zona): ?>
                    <option value="<?php echo $zona['id']; ?>"><?php echo $zona['nombre']; ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td><input type="time" name="detalles[${rowCount}][hora2]" class="form-control"></td>
        <td>
            <select name="detalles[${rowCount}][zona_horario_detalle2]" class="form-control">
                <?php foreach ($zonasHorarioDetalle as $zona): ?>
                    <option value="<?php echo $zona['id']; ?>"><?php echo $zona['nombre']; ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td><button type="button" class="btn btn-danger" onclick="removeRow(this)">Eliminar</button></td>
    `;
}

function removeRow(button) {
    var row = button.parentNode.parentNode;
    row.parentNode.removeChild(row);
}
</script>

<?php //include '../includes/footer.php'; ?>
