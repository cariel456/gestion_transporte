<?php
require_once '../config/db_config.php'; //CONEXION A LA DB

$username = 'rocio';     //NOMBRE DEL USUARIO
$password = 'rocio';     //CLAVE DEL USUARIO
$rol_id = 2;            //PONER EL id DEL ROL(DE LA TABLA ROLES ELGIR QUE ROL DARLE AL USUARIO A CREAR, el 1 es ROOT POR EJ.)

$hashed_password = password_hash($password, PASSWORD_DEFAULT); //ENCRIPTACION DE LA CLAVE

$sql = "INSERT INTO usuarios (nombre_usuario, password, rol_id) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $username, $hashed_password, $rol_id); //CREACION DEL USUARIO

if ($stmt->execute()) {
    echo "Usuario de prueba creado con éxito.";
} else {
    echo "Error al crear el usuario de prueba: " . $conn->error;
}
?>

<!--
ESTE SCRIPT SE USA PARA CREAR UN USUARIO Y CONTRASEÑA YA ENCRIPTADO EN LA BASE DE DATOS
-->

require_once dirname(__DIR__, 2) . '/config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/functions.php';

function escape($value) {
    global $conn;
    return $conn->real_escape_string($value);
}

// Construir la consulta SQL base
$sql = "SELECT s.*, p1.nombre_personal AS solicitante_nombre, 
               p2.nombre_personal AS conductor_nombre,
               p3.nombre_personal AS mantenimiento_nombre,
               u.codigo_interno AS unidad_codigo,
               l.nombre_localidad
        FROM solicitudes_pedidos_reparaciones s
        LEFT JOIN personal p1 ON s.solicitante = p1.id
        LEFT JOIN personal p2 ON s.nombre_completo_conductor = p2.id
        LEFT JOIN personal p3 ON s.nombre_completo_mantenimiento = p3.id
        LEFT JOIN unidades u ON s.numero_unidad = u.id
        LEFT JOIN localidades l ON s.ubicacion = l.id
        WHERE 1=1";

$whereClause = [];

// Aplicar filtros si se han enviado
if ($_SERVER["REQUEST_METHOD"] == "GET" && !empty($_GET)) {
    if (!empty($_GET['numero_solicitud'])) {
        $whereClause[] = "s.numero_solicitud = " . escape($_GET['numero_solicitud']);
    }
    if (!empty($_GET['fecha_inicio']) && !empty($_GET['fecha_fin'])) {
        $whereClause[] = "s.fecha_solicitud BETWEEN '" . escape($_GET['fecha_inicio']) . "' AND '" . escape($_GET['fecha_fin']) . "'";
    }
    if (!empty($_GET['solicitante'])) {
        $whereClause[] = "p1.nombre_personal LIKE '%" . escape($_GET['solicitante']) . "%'";
    }
    if (!empty($_GET['ubicacion'])) {
        $whereClause[] = "l.nombre_localidad LIKE '%" . escape($_GET['ubicacion']) . "%'";
    }
    if (!empty($_GET['numero_unidad'])) {
        $whereClause[] = "u.codigo_interno LIKE '%" . escape($_GET['numero_unidad']) . "%'";
    }
    if (!empty($_GET['nivel_urgencia'])) {
        $whereClause[] = "s.nivel_urgencia = " . escape($_GET['nivel_urgencia']);
    }
    if (!empty($_GET['grupo_funcion'])) {
        $whereClause[] = "s.grupo_funcion LIKE '%" . escape($_GET['grupo_funcion']) . "%'";
    }
    if (!empty($_GET['especialidades'])) {
        $whereClause[] = "s.especialidades LIKE '%" . escape($_GET['especialidades']) . "%'";
    }
    if (!empty($_GET['conductor'])) {
        $whereClause[] = "p2.nombre_personal LIKE '%" . escape($_GET['conductor']) . "%'";
    }
    if (!empty($_GET['mantenimiento'])) {
        $whereClause[] = "p3.nombre_personal LIKE '%" . escape($_GET['mantenimiento']) . "%'";
    }
    if (isset($_GET['habilitado']) && $_GET['habilitado'] !== '') {
        $whereClause[] = "s.habilitado = " . (int)$_GET['habilitado'];
    }
}

if (!empty($whereClause)) {
    $sql .= " AND " . implode(" AND ", $whereClause);
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitudes de Pedidos y Reparaciones</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Solicitudes de Pedidos y Reparaciones</h2>
        
        <!-- Formulario de filtro -->
        <form method="GET" class="mb-4">
            <div class="form-row">
                <div class="col-md-2 mb-3">
                    <label for="numero_solicitud">Número de Solicitud</label>
                    <input type="number" class="form-control" id="numero_solicitud" name="numero_solicitud" value="<?php echo isset($_GET['numero_solicitud']) ? htmlspecialchars($_GET['numero_solicitud']) : ''; ?>">
                </div>
                <div class="col-md-2 mb-3">
                    <label for="fecha_inicio">Fecha Inicio</label>
                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?php echo isset($_GET['fecha_inicio']) ? htmlspecialchars($_GET['fecha_inicio']) : ''; ?>">
                </div>
                <div class="col-md-2 mb-3">
                    <label for="fecha_fin">Fecha Fin</label>
                    <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="<?php echo isset($_GET['fecha_fin']) ? htmlspecialchars($_GET['fecha_fin']) : ''; ?>">
                </div>
                <div class="col-md-2 mb-3">
                    <label for="solicitante">Solicitante</label>
                    <input type="text" class="form-control" id="solicitante" name="solicitante" value="<?php echo isset($_GET['solicitante']) ? htmlspecialchars($_GET['solicitante']) : ''; ?>">
                </div>
                <div class="col-md-2 mb-3">
                    <label for="ubicacion">Ubicación</label>
                    <input type="text" class="form-control" id="ubicacion" name="ubicacion" value="<?php echo isset($_GET['ubicacion']) ? htmlspecialchars($_GET['ubicacion']) : ''; ?>">
                </div>
                <div class="col-md-2 mb-3">
                    <label for="numero_unidad">Número de Unidad</label>
                    <input type="text" class="form-control" id="numero_unidad" name="numero_unidad" value="<?php echo isset($_GET['numero_unidad']) ? htmlspecialchars($_GET['numero_unidad']) : ''; ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-2 mb-3">
                    <label for="nivel_urgencia">Nivel de Urgencia</label>
                    <input type="number" class="form-control" id="nivel_urgencia" name="nivel_urgencia" value="<?php echo isset($_GET['nivel_urgencia']) ? htmlspecialchars($_GET['nivel_urgencia']) : ''; ?>">
                </div>
                <div class="col-md-2 mb-3">
                    <label for="grupo_funcion">Grupo Función</label>
                    <input type="text" class="form-control" id="grupo_funcion" name="grupo_funcion" value="<?php echo isset($_GET['grupo_funcion']) ? htmlspecialchars($_GET['grupo_funcion']) : ''; ?>">
                </div>
                <div class="col-md-2 mb-3">
                    <label for="especialidades">Especialidades</label>
                    <input type="text" class="form-control" id="especialidades" name="especialidades" value="<?php echo isset($_GET['especialidades']) ? htmlspecialchars($_GET['especialidades']) : ''; ?>">
                </div>
                <div class="col-md-2 mb-3">
                    <label for="conductor">Conductor</label>
                    <input type="text" class="form-control" id="conductor" name="conductor" value="<?php echo isset($_GET['conductor']) ? htmlspecialchars($_GET['conductor']) : ''; ?>">
                </div>
                <div class="col-md-2 mb-3">
                    <label for="mantenimiento">Mantenimiento</label>
                    <input type="text" class="form-control" id="mantenimiento" name="mantenimiento" value="<?php echo isset($_GET['mantenimiento']) ? htmlspecialchars($_GET['mantenimiento']) : ''; ?>">
                </div>
                <div class="col-md-2 mb-3">
                    <label for="habilitado">Habilitado</label>
                    <select class="form-control" id="habilitado" name="habilitado">
                        <option value="">Todos</option>
                        <option value="1" <?php echo (isset($_GET['habilitado']) && $_GET['habilitado'] === '1') ? 'selected' : ''; ?>>Sí</option>
                        <option value="0" <?php echo (isset($_GET['habilitado']) && $_GET['habilitado'] === '0') ? 'selected' : ''; ?>>No</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Filtrar</button>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-secondary">Limpiar filtros</a>
        </form>

        <!-- Tabla de resultados -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Número de Solicitud</th>
                    <th>Fecha de Solicitud</th>
                    <th>Solicitante</th>
                    <th>Ubicación</th>
                    <th>Unidad</th>
                    <th>Nivel de Urgencia</th>
                    <th>Grupo Función</th>
                    <th>Especialidades</th>
                    <th>Conductor</th>
                    <th>Mantenimiento</th>
                    <th>Habilitado</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["numero_solicitud"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["fecha_solicitud"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["solicitante_nombre"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["nombre_localidad"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["unidad_codigo"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["nivel_urgencia"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["grupo_funcion"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["especialidades"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["conductor_nombre"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["mantenimiento_nombre"]) . "</td>";
                        echo "<td>" . ($row["habilitado"] ? "Sí" : "No") . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='12'>No se encontraron resultados</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>