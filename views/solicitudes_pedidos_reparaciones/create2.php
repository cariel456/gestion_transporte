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
        <form method="POST">
            <div class="mb-3">
                <label for="solicitante" class="form-label">Solicitante</label>
                <select class="form-control select2" id="solicitante" name="solicitante">
                    <option value="">Seleccione un solicitante</option>
                    <?php foreach ($personal as $solicitante) : ?>
                        <option value="<?php echo $solicitante['id']; ?>"><?php echo htmlspecialchars($solicitante['nombre_personal']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
                        
            <div class="mb-3">
                <label for="nombre_completo_conductor" class="form-label">Conductor</label>
                <select class="form-control select2" id="nombre_completo_conductor" name="nombre_completo_conductor">
                    <option value="">Seleccione un Conductor</option>
                    <?php foreach ($personal as $persona) : ?>
                        <option value="<?php echo $persona['id']; ?>"><?php echo htmlspecialchars($persona['nombre_personal']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="nombre_completo_mantenimiento" class="form-label">Mantenimiento</label>
                <select class="form-control select2" id="nombre_completo_mantenimiento" name="nombre_completo_mantenimiento">
                    <option value="">Persona de Mantenimiento</option>
                    <?php foreach ($personal as $persona) : ?>
                        <option value="<?php echo $persona['id']; ?>"><?php echo htmlspecialchars($persona['nombre_personal']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="numero_unidad" class="form-label">Número de Unidad</label>
                <select class="form-control select2" id="numero_unidad" name="numero_unidad">
                    <option value="">Seleccione una unidad</option>
                    <?php foreach ($unidades as $unidad) : ?>
                        <option value="<?php echo $unidad['id']; ?>"><?php echo htmlspecialchars($unidad['codigo_interno']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Crear</button>
            <a href="read.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
</body>
</html>