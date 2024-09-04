<?php
$projectRoot = dirname(__FILE__, 3); 
require_once dirname(__DIR__, 2) . '/config/config.php'; 
require_once ROOT_PATH . '/sec/init.php';
require_once ROOT_PATH . '/includes/session.php';   
require_once ROOT_PATH . '/sec/auth_check.php';       
require_once $projectRoot . '/includes/functions.php'; 

requireLogin();

// Obtener el ID del personal seleccionado
$id_personal = $_GET['id'];

// Consulta a la tabla personal
$sql_personal = "SELECT * FROM personal WHERE id = $id_personal";
$resultado_personal = $conexion->query($sql_personal);
$personal = $resultado_personal->fetch_assoc();

// Consulta a la tabla detalle
$sql_detalle = "SELECT * FROM detalle WHERE id_personal = $id_personal";
$resultado_detalle = $conexion->query($sql_detalle);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Búsqueda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Formulario de Búsqueda</h1>
        <form action="procesar_busqueda.php" method="post">
            <label for="nombre_personal">Nombre del Personal:</label>
            <input type="text" id="nombre_personal" name="nombre_personal" value="<?php echo $personal['nombre_personal']; ?>" readonly>
            <input type="hidden" name="id_personal" value="<?php echo $personal['id']; ?>">
            <br><br>
            <h2>Detalles Relacionados</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID Detalle</th>
                        <th>Descripción</th>
                        <!-- Añade más columnas según sea necesario -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($resultado_detalle->num_rows > 0) {
                        while($fila_detalle = $resultado_detalle->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $fila_detalle["id"] . "</td>";
                            echo "<td>" . $fila_detalle["descripcion"] . "</td>";
                            // Añade más celdas según sea necesario
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2'>No hay detalles relacionados</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <br>
            <input type="submit" value="Buscar" class="btn btn-primary">
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conexion->close();
?>
