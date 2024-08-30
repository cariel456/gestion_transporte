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

include '../../includes/header.php';
?>

<div class="container mt-5">
    <h1>Lista de Personal</h1>
    <a href="create.php" class="btn btn-primary mb-3">Agregar Personal</a>
    <a href="../../index.php" class="btn btn-secondary mb-3">Volver</a>
    
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
                        <a href="update.php?id=<?php echo isset($persona['id']) ? $persona['id'] : ''; ?>" class="btn btn-sm btn-warning">Editar</a>
                        <a href="delete.php?id=<?php echo isset($persona['id']) ? $persona['id'] : ''; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de que desea eliminar este registro?')">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>