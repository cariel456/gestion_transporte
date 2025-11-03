<?php
/**
 * Plantilla Base para Vistas CRUD - Read
 * 
 * Variables requeridas:
 * - $pageTitle: Título de la página
 * - $createUrl: URL para crear nuevo registro
 * - $columns: Array con las columnas de la tabla ['label' => 'valor']
 * - $data: Array con los datos a mostrar
 * - $entityName: Nombre de la entidad (singular)
 * - $emptyIcon: Icono para estado vacío (opcional)
 */

// Valores por defecto
$emptyIcon = $emptyIcon ?? '📋';
$backUrl = $backUrl ?? BASE_URL . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <!-- FIX: No cargar Bootstrap aquí, ya se carga en header.php -->
    <link href="<?php echo BASE_URL; ?>/public/assets/css/crud-style.css" rel="stylesheet">
</head>
<body>
    <div class="main-container content">
        <!-- Header -->
        <div class="page-header">
            <h1 class="page-title"><?php echo htmlspecialchars($pageTitle); ?></h1>
            
            <div class="action-buttons">
                <?php if (in_array('escritura', $_SESSION['permissions']) || in_array('total', $_SESSION['permissions'])): ?>
                    <a href="<?php echo htmlspecialchars($createUrl); ?>" class="btn btn-custom btn-create">
                        ➕ Crear Nuevo
                    </a>
                <?php endif; ?>
                <a href="<?php echo htmlspecialchars($backUrl); ?>" class="btn btn-custom btn-back">
                    ← Volver al Menú
                </a>
            </div>
        </div>
        
        <!-- Tabla -->
        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-custom table-hover">
                    <thead>
                        <tr>
                            <?php foreach ($columns as $label): ?>
                                <th><?php echo htmlspecialchars($label); ?></th>
                            <?php endforeach; ?>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data)): ?>
                            <tr>
                                <td colspan="<?php echo count($columns) + 1; ?>">
                                    <div class="empty-state">
                                        <div class="empty-state-icon"><?php echo $emptyIcon; ?></div>
                                        <h3>No hay <?php echo strtolower($pageTitle); ?> registrados</h3>
                                        <p class="text-muted">Comienza creando un nuevo registro</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($data as $item): ?>
                                <tr>
                                    <?php 
                                    // Renderizar celdas personalizadas si existe la función
                                    if (function_exists('renderTableCells')) {
                                        renderTableCells($item, $columns);
                                    } else {
                                        // Renderizado por defecto
                                        foreach (array_keys($columns) as $key) {
                                            echo '<td>' . htmlspecialchars($item[$key] ?? 'N/A') . '</td>';
                                        }
                                    }
                                    ?>
                                    <td class="actions-cell">
                                        <?php if (in_array('modificar', $_SESSION['permissions']) || in_array('total', $_SESSION['permissions'])): ?>
                                            <a href="update.php?id=<?php echo $item['id']; ?>" 
                                               class="btn btn-action btn-edit"
                                               title="Editar">
                                                ✏️ Editar
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if (in_array('eliminar', $_SESSION['permissions']) || in_array('total', $_SESSION['permissions'])): ?>
                                            <a href="delete.php?id=<?php echo $item['id']; ?>" 
                                               class="btn btn-action btn-delete"
                                               title="Eliminar"
                                               onclick="return confirm('¿Está seguro de eliminar este registro?');">
                                                🗑️ Eliminar
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Información adicional -->
        <div class="mt-3 text-center" style="color: var(--text-muted);">
            <small>
                Total de registros: <strong><?php echo count($data); ?></strong>
            </small>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>