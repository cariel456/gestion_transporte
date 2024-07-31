
<?php
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/functions.php';
include ROOT_PATH . '/includes/header.php'; 


requireLogin();
// Para create.php
checkPermission('crear');


$personal = getAllPersonal();
$unidades = getAllUnidades();
$localidades = getAllLocalidades();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $especialidades = isset($_POST['especialidades']) ? implode(',', $_POST['especialidades']) : '';
    $data = [
        'especialidades' => $especialidades,
        'grupo_funcion' => $_POST['grupo_funcion'],
        'nivel_urgencia' => $_POST['nivel_urgencia'],
        'nombre_completo_conductor' => $_POST['nombre_completo_conductor'],
        'nombre_completo_mantenimiento' => $_POST['nombre_completo_mantenimiento'],
        'numero_solicitud' => $_POST['numero_solicitud'],
        'numero_unidad' => $_POST['numero_unidad'],
        'observaciones' => $_POST['observaciones'],
        'solicitante' => $_POST['solicitante'],
        'ubicacion' => $_POST['ubicacion'],
        'fecha_solicitud' => $_POST['fecha_solicitud']
    ];
    
    if (createSolicitudPedidoReparacion($data)) {
        header("Location: read.php");
        exit();
    } else {
        $error = "Error al crear la solicitud de pedido de reparación";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Solicitud de Pedido de Reparación</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">

        <h1>Crear Solicitud de Pedido de Reparación</h1>

        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="solicitante" class="form-label">Solicitante</label>
                <select class="form-select" id="solicitante" name="solicitante" >
                <option value="">Seleccione un solicitante</option>
                    <?php foreach ($personal as $solicitante) : ?>
                        <option value="<?php echo $solicitante['id']; ?>"><?php echo $solicitante['nombre_personal']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="fecha_solicitud">Fecha de Solicitud</label>
                <input type="datetime-local" class="form-control" id="fecha_solicitud" name="fecha_solicitud" value="<?php echo isset($solicitud['fecha_solicitud']) ? date('Y-m-d\TH:i', strtotime($solicitud['fecha_solicitud'])) : ''; ?>">
            </div>
            <div class="mb-3">
                <label for="nombre_completo_conductor" class="form-label">Conductor</label>
                <select class="form-select" id="nombre_completo_conductor" name="nombre_completo_conductor" >
                <option value="">Seleccione un Conductor</option>
                    <?php foreach ($personal as $persona) : ?>
                        <option value="<?php echo $persona['id']; ?>"><?php echo $persona['nombre_personal']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <div class="mb-3">
                <label class="form-label">Especialidades</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="especialidades[]" value="Mecanica" id="especialidad_mecanica">
                    <label class="form-check-label" for="especialidad_mecanica">Mecánica</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="especialidades[]" value="Electricidad" id="especialidad_electricidad">
                    <label class="form-check-label" for="especialidad_electricidad">Electricidad</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="especialidades[]" value="Chapista" id="especialidad_chapista">
                    <label class="form-check-label" for="especialidad_chapista">Chapista</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="especialidades[]" value="Otros" id="especialidad_otros">
                    <label class="form-check-label" for="especialidad_otros">Otros</label>
                </div>
            </div>

        <div class="mb-3">
            <label for="ubicacion" class="form-label">Ubicación</label>
            <select class="form-select" id="ubicacion" name="ubicacion" required>
                <option value="">Seleccione una ubicación</option>
                <?php foreach ($localidades as $localidad) : ?>
                    <option value="<?php echo $localidad['id']; ?>"><?php echo $localidad['nombre_localidad']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="grupo_funcion" class="form-label">Grupo Función</label>
            <input type="text" class="form-control" id="grupo_funcion" name="grupo_funcion">
        </div>
            <div class="mb-3">
                <label for="nivel_urgencia" class="form-label">Nivel de Urgencia</label>
                <input type="number" class="form-control" id="nivel_urgencia" name="nivel_urgencia" >
            </div>
            <div class="mb-3">
                <label for="nombre_completo_mantenimiento" class="form-label">Mantenimiento</label>
                <select class="form-select" id="nombre_completo_mantenimiento" name="nombre_completo_mantenimiento" >
                <option value="">Persona de Mantenimiento</option>
                    <?php foreach ($personal as $persona) : ?>
                        <option value="<?php echo $persona['id']; ?>"><?php echo $persona['nombre_personal']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="numero_solicitud" class="form-label">Número de Solicitud</label>
                <input type="number" class="form-control" id="numero_solicitud" name="numero_solicitud">
            </div>
            <div class="mb-3">
                <label for="numero_unidad" class="form-label">Número de Unidad</label>
                <select class="form-select" id="numero_unidad" name="numero_unidad" >
                <option value="">Seleccione una unidad</option>
                    <?php foreach ($unidades as $unidad) : ?>
                        <option value="<?php echo $unidad['id']; ?>"><?php echo $unidad['codigo_interno']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="observaciones" class="form-label">Observaciones</label>
                <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
            </div>
            <!--<div class="mb-3">
                <label for="numero_ot" class="form-label">Número OT</label>
                <input type="number" class="form-control" id="numero_ot" name="numero_ot">
            </div>-->
            <button type="submit" class="btn btn-primary">Crear</button>
            <a href="read.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>