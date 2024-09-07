<?php
$projectRoot = dirname(__FILE__, 3); 
require_once dirname(__DIR__, 2) . '/config/config.php'; 
require_once ROOT_PATH . '/sec/init.php';
require_once ROOT_PATH . '/includes/session.php';   
require_once ROOT_PATH . '/sec/auth_check.php';       
require_once $projectRoot . '/includes/functions.php'; 
requireLogin();

// Modificar la función getAllPersonal para aceptar un parámetro de búsqueda
function getAllPersonaless($busqueda = '') {
    global $conn;
    $sql = "SELECT * FROM personal WHERE nombre_personal LIKE ?";
    $stmt = $conn->prepare($sql);
    $busqueda = "%$busqueda%";
    $stmt->bind_param("s", $busqueda);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Obtener el término de búsqueda del formulario
$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';

// Obtener los resultados filtrados
$personal = getAllPersonaless($busqueda);
$categoriaPersonal = getAllCategoriasPersona();
$rol_id = $_SESSION['rol_id'];

include '../../includes/header.php';
?>

<div class="container mt-5">
    <h1>Lista de Personal</h1>
    <div class="col-md-6">
        <?php if (in_array('escritura', $_SESSION['permissions']) || in_array('total', $_SESSION['permissions'])): ?>
            <a href="create.php" class="btn btn-primary">Crear Nuevo</a>
        <?php endif; ?>
        <a href="<?php echo BASE_URL; ?>/includes/header.php" class="btn btn-secondary">Volver</a>
    </div>
    
    <!-- Formulario de búsqueda -->
    <form action="" method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="busqueda" class="form-control" placeholder="Buscar por nombre" value="<?php echo htmlspecialchars($busqueda); ?>">
            <div class="input-group-append">
                <button type="submit" class="btn btn-outline-secondary">Buscar</button>
            </div>
        </div>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Código</th>
                <th>Categoría</th>
                <th>Nombre</th>
                <th>Legajo</th>
                <th>Tarjeta</th>
                <th>Vencimiento Licencia</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($personal as $persona): ?>
                <tr>
                    <td><?php echo isset($persona['id']) ? htmlspecialchars($persona['id']) : ''; ?></td>
                    <td><?php echo isset($persona['codigo']) ? htmlspecialchars($persona['codigo']) : ''; ?></td>
                    <td><?php //echo isset($persona['categoria']) ? htmlspecialchars($persona['nombre_categoria']) : ''; ?></td>
                    <td><?php echo isset($persona['nombre_personal']) ? htmlspecialchars($persona['nombre_personal']) : ''; ?></td>
                    <td><?php echo isset($persona['legajo']) ? htmlspecialchars($persona['legajo']) : ''; ?></td>
                    <td><?php echo isset($persona['tarjeta']) ? htmlspecialchars($persona['tarjeta']) : ''; ?></td>
                    <td><?php echo isset($persona['vencimiento_licencia']) ? htmlspecialchars($persona['vencimiento_licencia']) : ''; ?></td>
                    <td>
                            <?php if (in_array('modificar', $_SESSION['permissions']) || in_array('total', $_SESSION['permissions'])): ?>
                                <a href="update.php?id=<?php echo $item['id']; ?>" class="btn btn-warning btn-sm">Actualizar</a>
                            <?php endif; ?>
                            <?php if (in_array('eliminar', $_SESSION['permissions']) || in_array('total', $_SESSION['permissions'])): ?>
                                <a href="delete.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
                            <?php endif; ?>
                        </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>