<?php
/**
 * Plantilla Base para Vistas CRUD - Create
 * 
 * Variables requeridas:
 * - $pageTitle: Título de la página
 * - $formAction: URL donde se envía el formulario (opcional, default: mismo archivo)
 * - $formFields: Array con definición de campos del formulario
 * - $error: Mensaje de error (opcional)
 * - $success: Mensaje de éxito (opcional)
 * - $backUrl: URL para volver (default: read.php)
 */

// Valores por defecto
$formAction = $formAction ?? '';
$backUrl = $backUrl ?? 'read.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>/public/assets/css/crud-style.css" rel="stylesheet">
    <style>
        /* Estilos específicos para formularios */
        .form-container {
            background: linear-gradient(to bottom right, var(--bg-card), var(--bg-dark-secondary));
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
            border: 1px solid var(--border-subtle);
            max-width: 800px;
            margin: 0 auto;
        }
        
        .form-label {
            color: var(--text-light);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .form-control,
        .form-select {
            background-color: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--text-light);
            padding: 0.75rem;
            border-radius: 8px;
        }
        
        .form-control:focus,
        .form-select:focus {
            background-color: rgba(255, 255, 255, 0.12);
            border-color: var(--green-start);
            color: var(--text-light);
            box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
        }
        
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }
        
        .form-select option {
            background-color: var(--bg-dark-secondary);
            color: var(--text-light);
        }
        
        .required-indicator {
            color: #ff6b6b;
            margin-left: 0.25rem;
        }
    </style>
</head>
<body>
    <div class="main-container content">
        <!-- Header -->
        <div class="page-header">
            <h1 class="page-title"><?php echo htmlspecialchars($pageTitle); ?></h1>
        </div>
        
        <!-- Mensajes -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>⚠️ Error:</strong> <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>✅ Éxito:</strong> <?php echo htmlspecialchars($success); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- Formulario -->
        <div class="form-container">
            <form method="POST" action="<?php echo htmlspecialchars($formAction); ?>">
                <?php
                // Renderizar campos del formulario
                if (isset($formFields) && is_array($formFields)) {
                    foreach ($formFields as $field) {
                        renderFormField($field);
                    }
                }
                ?>
                
                <!-- Botones de acción -->
                <div class="action-buttons mt-4">
                    <button type="submit" class="btn btn-custom btn-create">
                        ✅ Guardar
                    </button>
                    <a href="<?php echo htmlspecialchars($backUrl); ?>" class="btn btn-custom btn-back">
                        ← Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

<?php
/**
 * Función helper para renderizar campos del formulario
 */
function renderFormField($field) {
    $type = $field['type'] ?? 'text';
    $name = $field['name'] ?? '';
    $label = $field['label'] ?? ucfirst($name);
    $required = $field['required'] ?? false;
    $placeholder = $field['placeholder'] ?? '';
    $value = $field['value'] ?? '';
    $options = $field['options'] ?? [];
    $rows = $field['rows'] ?? 3;
    
    echo '<div class="mb-3">';
    echo '<label for="' . htmlspecialchars($name) . '" class="form-label">';
    echo htmlspecialchars($label);
    if ($required) {
        echo '<span class="required-indicator">*</span>';
    }
    echo '</label>';
    
    switch ($type) {
        case 'textarea':
            echo '<textarea class="form-control" id="' . htmlspecialchars($name) . '" name="' . htmlspecialchars($name) . '" rows="' . $rows . '"';
            if ($required) echo ' required';
            if ($placeholder) echo ' placeholder="' . htmlspecialchars($placeholder) . '"';
            echo '>' . htmlspecialchars($value) . '</textarea>';
            break;
            
        case 'select':
            echo '<select class="form-select" id="' . htmlspecialchars($name) . '" name="' . htmlspecialchars($name) . '"';
            if ($required) echo ' required';
            echo '>';
            echo '<option value="">Seleccione una opción</option>';
            foreach ($options as $optValue => $optLabel) {
                $selected = ($value == $optValue) ? ' selected' : '';
                echo '<option value="' . htmlspecialchars($optValue) . '"' . $selected . '>';
                echo htmlspecialchars($optLabel);
                echo '</option>';
            }
            echo '</select>';
            break;
            
        default:
            echo '<input type="' . htmlspecialchars($type) . '" class="form-control" id="' . htmlspecialchars($name) . '" name="' . htmlspecialchars($name) . '" value="' . htmlspecialchars($value) . '"';
            if ($required) echo ' required';
            if ($placeholder) echo ' placeholder="' . htmlspecialchars($placeholder) . '"';
            echo '>';
            break;
    }
    
    echo '</div>';
}
?>