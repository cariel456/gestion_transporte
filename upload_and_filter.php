<?php
/**
 * Script para análisis de auditorías de transacciones
 * Procesa archivos Excel y genera reportes estadísticos con filtros temporales
 * @version 2.0 - Optimizado para Bootstrap 5
 */

// Importar configuración de términos de reportes
$TERMINOS_REPORTES = include 'terminos_reportes.php';
$timeFilter = 'all';
$termFilter = 'all';
$allTermCounts = [];
$errorMessage = '';

// Procesar la solicitud POST cuando se suben archivos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_files'])) {
    require 'vendor/autoload.php';

    // Obtener filtros seleccionados
    $timeFilter = $_POST['time_filter'] ?? 'all';
    $termFilter = $_POST['term_filter'] ?? 'all';
    $now = new DateTime();

    // Procesar cada archivo Excel subido
    foreach ($_FILES['excel_files']['tmp_name'] as $index => $file) {
        if (!empty($file)) {
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray();
                
                // Definir índices de columnas importantes
                $fechaColumnIndex = 1;    // Columna B: fecha_importacion
                $reporteColumnIndex = 5;  // Columna F: tipo de reporte
                
                // Eliminar fila de encabezados
                array_shift($rows);

                // Arrays para almacenar datos procesados
                $registrosPorMes = [];
                $filasFiltradasPorTiempo = [];

                // Procesar cada fila del archivo
                foreach ($rows as $row) {
                    if (!empty($row[$fechaColumnIndex])) {
                        try {
                            // Parsear fecha en diferentes formatos
                            $fechaFila = DateTime::createFromFormat('Y-m-d H:i:s', $row[$fechaColumnIndex]) ?: 
                                        DateTime::createFromFormat('d/m/Y H:i:s', $row[$fechaColumnIndex]);
                            
                            if (!$fechaFila) continue;

                            $mesAnio = $fechaFila->format('Y-m');

                            // Procesar según filtro de tiempo
                            if ($timeFilter === 'group_by_month') {
                                $registrosPorMes[$mesAnio][] = $row;
                            } else {
                                $incluirFila = match($timeFilter) {
                                    'hour' => $now->diff($fechaFila)->h === 0 && $now->diff($fechaFila)->d === 0,
                                    'day' => $fechaFila->format('Y-m-d') === $now->format('Y-m-d'),
                                    'week' => $fechaFila->format('W') === $now->format('W'),
                                    'month' => $fechaFila->format('Y-m') === $now->format('Y-m'),
                                    default => true
                                };

                                if ($incluirFila) {
                                    $filasFiltradasPorTiempo[] = $row;
                                }
                            }
                        } catch (Exception $e) {
                            continue;
                        }
                    }
                }

                // Función auxiliar para contar términos
                $contarTerminos = function($filas) use ($reporteColumnIndex, $TERMINOS_REPORTES, $termFilter) {
                    $conteo = array_fill_keys($TERMINOS_REPORTES, 0);
                    
                    foreach ($filas as $fila) {
                        if (!empty($fila[$reporteColumnIndex])) {
                            $termino = strtoupper(trim($fila[$reporteColumnIndex]));
                            
                            foreach ($TERMINOS_REPORTES as $terminoPermitido) {
                                if (strpos($termino, $terminoPermitido) !== false) {
                                    $conteo[$terminoPermitido]++;
                                    break;
                                }
                            }
                        }
                    }
                    
                    // Filtrar por término si se seleccionó uno específico
                    if ($termFilter !== 'all') {
                        $conteo = array_filter($conteo, fn($k) => $k === $termFilter, ARRAY_FILTER_USE_KEY);
                    } else {
                        $conteo = array_filter($conteo);
                    }
                    
                    arsort($conteo);
                    return $conteo;
                };

                // Procesar datos según tipo de filtro
                if ($timeFilter === 'group_by_month') {
                    $conteoPorMes = [];
                    
                    foreach ($registrosPorMes as $mesAnio => $filasDelMes) {
                        $conteoPorMes[$mesAnio] = $contarTerminos($filasDelMes);
                    }
                    
                    ksort($conteoPorMes);
                    
                    $allTermCounts[] = [
                        'nombreArchivo' => $_FILES['excel_files']['name'][$index],
                        'conteoTerminos' => $conteoPorMes
                    ];
                } else {
                    $allTermCounts[] = [
                        'nombreArchivo' => $_FILES['excel_files']['name'][$index],
                        'conteoTerminos' => $contarTerminos($filasFiltradasPorTiempo)
                    ];
                }
                
            } catch (Exception $e) {
                $errorMessage .= "Error en archivo {$_FILES['excel_files']['name'][$index]}: " . $e->getMessage() . "<br>";
            }
        }
    }
}

include 'includes/header.php';
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Análisis de Auditorías - EldoWay</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    
    <style>
        :root {
            --bg-dark-primary: #1b1b1b;
            --bg-dark-secondary: #212121;
            --bg-card: #2d2d2d;
            --text-light: #f8f9fa;
            --text-muted: #adb5bd;
            --green-start: #28a745;
            --green-end: #1c7430;
            --accent-amber: #ffc107;
            --border-subtle: rgba(40, 167, 69, 0.3);
        }
        
        body {
            background-color: var(--bg-dark-primary);
            color: var(--text-light);
            font-family: 'Roboto', sans-serif;
            min-height: 100vh;
        }
        
        .main-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .page-title {
            font-family: 'Montserrat', sans-serif;
            font-size: clamp(1.75rem, 4vw, 2.5rem);
            font-weight: 700;
            background: linear-gradient(135deg, var(--green-start), var(--green-end));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-align: center;
            margin-bottom: 2rem;
            letter-spacing: 1px;
        }
        
        .form-card {
            background: linear-gradient(to bottom right, var(--bg-card), var(--bg-dark-secondary));
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
            border: 1px solid var(--border-subtle);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .form-label {
            color: var(--text-light);
            font-weight: 600;
            margin-bottom: 0.75rem;
            font-size: 1.1rem;
        }
        
        .form-control,
        .form-select {
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-light);
            padding: 0.75rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus,
        .form-select:focus {
            background-color: rgba(255, 255, 255, 0.08);
            border-color: var(--green-start);
            color: var(--text-light);
            box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
        }
        
        .form-control::placeholder {
            color: rgba(173, 181, 189, 0.5);
        }
        
        .file-info {
            background-color: rgba(255, 193, 7, 0.1);
            border-left: 3px solid var(--accent-amber);
            padding: 0.75rem 1rem;
            border-radius: 6px;
            margin-top: 1rem;
        }
        
        .file-info small {
            color: var(--accent-amber);
            font-size: 0.875rem;
        }
        
        .btn-analyze {
            background: linear-gradient(135deg, var(--green-start), var(--green-end));
            border: none;
            color: white;
            font-weight: 600;
            letter-spacing: 1px;
            padding: 0.875rem 3rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            text-transform: uppercase;
        }
        
        .btn-analyze:hover {
            background: linear-gradient(135deg, var(--green-end), var(--green-start));
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
            color: white;
        }
        
        .results-container {
            margin-top: 3rem;
        }
        
        .results-title {
            color: var(--accent-amber);
            font-size: 1.75rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 2rem;
            letter-spacing: 1px;
        }
        
        .file-result-card {
            background: linear-gradient(to bottom right, var(--bg-card), var(--bg-dark-secondary));
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.4);
            border: 1px solid var(--border-subtle);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .file-header {
            display: flex;
            align-items: center;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--border-subtle);
            margin-bottom: 1.5rem;
        }
        
        .file-icon {
            font-size: 1.5rem;
            margin-right: 0.75rem;
        }
        
        .file-name {
            color: var(--text-light);
            font-weight: 600;
            font-size: 1.2rem;
            margin: 0;
        }
        
        .month-section {
            background-color: rgba(255, 255, 255, 0.03);
            border-radius: 8px;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--green-start);
        }
        
        .month-title {
            color: var(--accent-amber);
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .table-custom {
            background-color: transparent;
            color: var(--text-light);
        }
        
        .table-custom thead {
            background: linear-gradient(135deg, var(--green-start), var(--green-end));
            color: white;
        }
        
        .table-custom thead th {
            border: none;
            padding: 1rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .table-custom tbody tr {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.2s ease;
        }
        
        .table-custom tbody tr:hover {
            background-color: rgba(40, 167, 69, 0.1);
        }
        
        .table-custom tbody td {
            padding: 0.875rem 1rem;
            color: var(--text-light);
        }
        
        .frequency-badge {
            background: linear-gradient(135deg, var(--green-start), var(--green-end));
            color: white;
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-weight: 600;
            display: inline-block;
            min-width: 50px;
            text-align: center;
        }
        
        /* Select2 dark theme customization */
        .select2-container--bootstrap-5 .select2-dropdown {
            background-color: var(--bg-card);
            border-color: var(--border-subtle);
        }
        
        .select2-container--bootstrap-5 .select2-results__option {
            color: var(--text-light);
        }
        
        .select2-container--bootstrap-5 .select2-results__option--highlighted {
            background-color: var(--green-start);
        }
        
        .select2-container--bootstrap-5 .select2-selection {
            background-color: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
        }
        
        /* Animaciones */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .form-card,
        .file-result-card {
            animation: fadeInUp 0.5s ease-out;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .form-card {
                padding: 1.5rem;
            }
            
            .btn-analyze {
                width: 100%;
                padding: 1rem;
            }
            
            .file-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
<div class="main-container">
    <h1 class="page-title">Análisis de Auditorías de Transacciones</h1>
    
    <!-- Mensajes de error -->
    <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>⚠️ Errores encontrados:</strong><br>
            <?= $errorMessage ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <!-- Formulario de análisis -->
    <div class="form-card">
        <form action="" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
            
            <!-- Selección de archivos -->
            <div class="mb-4">
                <label for="excel_files" class="form-label">
                    📁 Archivos Excel a Analizar
                </label>
                <input type="file" 
                       class="form-control form-control-lg" 
                       id="excel_files" 
                       name="excel_files[]" 
                       multiple 
                       required
                       accept=".xlsx,.xls">
                <div class="file-info">
                    <small>
                        <strong>Nota:</strong> Las fechas se procesan desde la columna <strong>B</strong> (fecha_importacion) de los archivos Excel.
                    </small>
                </div>
            </div>
            
            <!-- Filtro de período -->
            <div class="mb-4">
                <label for="time_filter" class="form-label">
                    📅 Período de Análisis
                </label>
                <select class="form-select form-select-lg" id="time_filter" name="time_filter">
                    <option value="all" <?= $timeFilter === 'all' ? 'selected' : '' ?>>Todos los registros</option>
                    <option value="hour" <?= $timeFilter === 'hour' ? 'selected' : '' ?>>Última hora</option>
                    <option value="day" <?= $timeFilter === 'day' ? 'selected' : '' ?>>Hoy</option>
                    <option value="week" <?= $timeFilter === 'week' ? 'selected' : '' ?>>Esta semana</option>
                    <option value="month" <?= $timeFilter === 'month' ? 'selected' : '' ?>>Este mes</option>
                    <option value="group_by_month" <?= $timeFilter === 'group_by_month' ? 'selected' : '' ?>>Separar por mes</option>
                </select>
            </div>
            
            <!-- Filtro de tipo de reporte -->
            <div class="mb-4">
                <label for="term_filter" class="form-label">
                    🏷️ Filtrar por Tipo de Reporte
                </label>
                <select class="form-select form-select-lg select2" 
                        id="term_filter" 
                        name="term_filter" 
                        data-placeholder="Seleccione un tipo de reporte">
                    <option></option>
                    <option value="all" <?= $termFilter === 'all' ? 'selected' : '' ?>>Todos los tipos</option>
                    <?php foreach ($TERMINOS_REPORTES as $term): ?>
                        <option value="<?= htmlspecialchars($term) ?>" 
                                <?= $termFilter === $term ? 'selected' : '' ?>>
                            <?= htmlspecialchars($term) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Botón de envío -->
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-analyze btn-lg">
                    🚀 Iniciar Análisis
                </button>
            </div>
        </form>
    </div>
    
    <!-- Resultados -->
    <?php if (!empty($allTermCounts)): ?>
    <div class="results-container">
        <h2 class="results-title">📊 Resultados del Análisis</h2>

        <?php foreach ($allTermCounts as $resultadoArchivo): ?>
            <div class="file-result-card">
                <div class="file-header">
                    <span class="file-icon">📄</span>
                    <h3 class="file-name"><?= htmlspecialchars($resultadoArchivo['nombreArchivo']) ?></h3>
                </div>
                
                <?php if ($timeFilter === 'group_by_month'): ?>
                    <!-- Vista agrupada por mes -->
                    <?php foreach ($resultadoArchivo['conteoTerminos'] as $mes => $terminos): ?>
                        <div class="month-section">
                            <h4 class="month-title">📆 Mes: <?= htmlspecialchars($mes) ?></h4>
                            <div class="table-responsive">
                                <table class="table table-custom table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">Tipo de Reporte</th>
                                            <th scope="col" class="text-center">Frecuencia</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (is_array($terminos) && !empty($terminos)): ?>
                                            <?php foreach ($terminos as $termino => $conteo): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($termino) ?></td>
                                                    <td class="text-center">
                                                        <span class="frequency-badge"><?= $conteo ?></span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="2" class="text-center text-muted">
                                                    No hay datos disponibles para este mes
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Vista general -->
                    <div class="table-responsive">
                        <table class="table table-custom table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">Tipo de Reporte</th>
                                    <th scope="col" class="text-center">Frecuencia</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($resultadoArchivo['conteoTerminos'])): ?>
                                    <?php foreach ($resultadoArchivo['conteoTerminos'] as $termino => $conteo): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($termino) ?></td>
                                            <td class="text-center">
                                                <span class="frequency-badge"><?= $conteo ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">
                                            No se encontraron resultados para los filtros seleccionados
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="<?php echo BASE_URL; ?>/vendor/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    // Inicializar Select2
    $("#term_filter").select2({
        theme: "bootstrap-5",
        width: "100%",
        language: {
            noResults: () => "No se encontraron resultados",
            searching: () => "Buscando..."
        },
        placeholder: "Seleccione un tipo de reporte",
        allowClear: true
    });
    
    // Validación de formulario
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
    
    // Mostrar nombres de archivos seleccionados
    $('#excel_files').on('change', function() {
        const files = Array.from(this.files);
        if (files.length > 0) {
            const fileNames = files.map(f => f.name).join(', ');
            console.log('Archivos seleccionados:', fileNames);
        }
    });
});
</script>
</body>
</html>