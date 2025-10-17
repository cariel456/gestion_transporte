<?php
/**
 * Script para análisis de auditorías de transacciones
 * Procesa archivos Excel y genera reportes estadísticos con filtros temporales
 */

// Importar configuración de términos de reportes
$TERMINOS_REPORTES = include 'terminos_reportes.php';
$timeFilter = 'all';
$termFilter = 'all';
$files;

// Procesar la solicitud POST cuando se suben archivos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_files'])) {
    require 'vendor/autoload.php';

    // Obtener filtros seleccionados
    $timeFilter = $_POST['time_filter'] ?? 'all';
    $termFilter = $_POST['term_filter'] ?? 'all';
    $allTermCounts = [];
    $now = new DateTime();

    // Procesar cada archivo Excel subido
    foreach ($_FILES['excel_files']['tmp_name'] as $index => $file) {
        if (!empty($file)) {
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray();
                
                // Definir índices de columnas importantes
                $fechaColumnIndex = 1;    // Columna para fecha
                $reporteColumnIndex = 5;  // Columna para tipo de reporte
                $serieColumnIndex = isset($rows[0][6]) ? 6 : null;
                
                // Eliminar fila de encabezados
                array_shift($rows);

                // Arrays para almacenar datos procesados
                $registrosPorMes = [];
                $filasFiltradasPorTiempo = [];

                // Procesar cada fila del archivo
                foreach ($rows as $row) {
                    if (!empty($row[$fechaColumnIndex])) {
                        try {
                            // Intentar parsear la fecha en diferentes formatos
                            $fechaFila = DateTime::createFromFormat('Y-m-d H:i:s', $row[$fechaColumnIndex]) ?: 
                                        DateTime::createFromFormat('d/m/Y H:i:s', $row[$fechaColumnIndex]);
                            
                            if (!$fechaFila) {
                                throw new Exception("Fecha no válida: " . $row[$fechaColumnIndex]);
                            }

                            $mesAnio = $fechaFila->format('Y-m');

                            // Procesar según el filtro de tiempo seleccionado
                            if ($timeFilter === 'group_by_month') {
                                $registrosPorMes[$mesAnio][] = $row;
                            } else {
                                $incluirFila = false;
                                switch ($timeFilter) {
                                    case 'hour':
                                        $diff = $now->diff($fechaFila);
                                        $incluirFila = ($diff->h === 0 && $diff->d === 0);
                                        break;
                                    case 'day':
                                        $incluirFila = ($fechaFila->format('Y-m-d') === $now->format('Y-m-d'));
                                        break;
                                    case 'week':
                                        $incluirFila = ($fechaFila->format('W') === $now->format('W'));
                                        break;
                                    case 'month':
                                        $incluirFila = ($fechaFila->format('Y-m') === $now->format('Y-m'));
                                        break;
                                    default:
                                        $incluirFila = true;
                                }

                                if ($incluirFila) {
                                    $filasFiltradasPorTiempo[] = $row;
                                }
                            }
                        } catch (Exception $e) {
                            continue;
                        }
                    }
                }

                // Procesar datos según el tipo de filtro
                if ($timeFilter === 'group_by_month') {
                    $conteoPorMes = [];
                    
                    // Procesar registros de cada mes
                    foreach ($registrosPorMes as $mesAnio => $filasDelMes) {
                        $conteoTerminosMes = array_fill_keys($TERMINOS_REPORTES, 0);
                        
                        // Contar términos para el mes actual
                        foreach ($filasDelMes as $fila) {
                            if (!empty($fila[$reporteColumnIndex])) {
                                $termino = strtoupper(trim($fila[$reporteColumnIndex]));
                                foreach ($TERMINOS_REPORTES as $terminoPermitido) {
                                    if (strpos($termino, $terminoPermitido) !== false) {
                                        $conteoTerminosMes[$terminoPermitido]++;
                                        break;
                                    }
                                }
                            }
                        }
                        
                        // Filtrar términos sin ocurrencias y ordenar
                        $conteoTerminosMes = array_filter($conteoTerminosMes);
                        arsort($conteoTerminosMes);
                        
                        // Almacenar resultados del mes
                        $conteoPorMes[$mesAnio] = $conteoTerminosMes;
                    }
                    
                    // Ordenar meses cronológicamente
                    ksort($conteoPorMes);
                    
                    // Agregar al resultado general
                    $allTermCounts[] = [
                        'nombreArchivo' => $_FILES['excel_files']['name'][$index],
                        'conteoTerminos' => $conteoPorMes
                    ];
                } else {
                    // Procesamiento para otros filtros de tiempo
                    $conteoTerminosPorArchivo = array_fill_keys($TERMINOS_REPORTES, 0);

                    foreach ($filasFiltradasPorTiempo as $fila) {
                        if (!empty($fila[$reporteColumnIndex])) {
                            $termino = strtoupper(trim($fila[$reporteColumnIndex]));
                            foreach ($TERMINOS_REPORTES as $terminoPermitido) {
                                if (strpos($termino, $terminoPermitido) !== false) {
                                    $conteoTerminosPorArchivo[$terminoPermitido]++;
                                    break;
                                }
                            }
                        }
                    }

                    $conteoTerminosPorArchivo = array_filter($conteoTerminosPorArchivo);
                    arsort($conteoTerminosPorArchivo);

                    $allTermCounts[] = [
                        'nombreArchivo' => $_FILES['excel_files']['name'][$index],
                        'conteoTerminos' => $conteoTerminosPorArchivo
                    ];
                }
            } catch (Exception $e) {
                echo "<div class='alert alert-danger'>Error al procesar el archivo: " . htmlspecialchars($e->getMessage()) . "</div>";
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
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <title>Análisis de Auditorías de Transacciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <link href="uploadandfiltercss.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="contenedor-principal">
    <h1 class="text-center titulo-principal">Análisis de Auditorias de Transacciones</h1>
    <div class="card-formulario">
        <form action="" enctype="multipart/form-data" method="POST">
            <div class="mb-4 custom-file-upload">
                <label class="form-label h5 mb-3" for="excel_files">Archivos Excel a Analizar</label>
                <input class="form-control" id="excel_files" multiple name="excel_files[]" required type="file">
                <div class="mt-3 text-muted">
                    <small>• La fecha o fechas es en base a la columna "fecha_importacion" (columna B) de los archivos excel subidos</small><br>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label h5" for="time_filter">Período de Análisis</label>
                <select class="form-select" id="time_filter" name="time_filter">
                    <option value="all" <?=$timeFilter === 'all' ? 'selected' : ''?>>Todos los registros</option>
                    <option value="hour" <?=$timeFilter === 'hour' ? 'selected' : ''?>>Última hora</option>
                    <option value="day" <?=$timeFilter === 'day' ? 'selected' : ''?>>Hoy</option>
                    <option value="week" <?=$timeFilter === 'week' ? 'selected' : ''?>>Esta semana</option>
                    <option value="month" <?=$timeFilter === 'month' ? 'selected' : ''?>>Este mes</option>
                    <option value="group_by_month" <?=$timeFilter === 'group_by_month' ? 'selected' : ''?>>Separar por Mes</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="form-label h5" for="term_filter">Filtrar por Tipo de Reporte</label>
                <select class="form-select select2" id="term_filter" name="term_filter" data-placeholder="Seleccione un tipo de reporte">
                    <option></option>
                    <option value="all">Todos los tipos</option>
                    <?php foreach ($TERMINOS_REPORTES as $term): ?>
                        <option value="<?=htmlspecialchars($term)?>" <?=$termFilter === $term ? 'selected' : ''?>><?=htmlspecialchars($term)?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="text-center">
                <button class="btn btn-lg btn-primary px-5 py-2" type="submit">Iniciar Análisis</button>
            </div>
        </form>
    </div>
    <?php if (!empty($allTermCounts)): ?>
    <div class="contenedor-resultados">
        <h2 class="mb-4 text-center">Resultados del Análisis</h2>

        <!-- Iteración sobre los resultados de análisis por archivo -->
        <?php foreach ($allTermCounts as $resultadoArchivo): ?>
            <div class="mb-5">
                <!-- Encabezado para cada archivo procesado -->
                <div class="align-items-center border-bottom d-flex justify-content-between pb-2">
                    <h3 class="h4 mb-0">📊<?=htmlspecialchars($resultadoArchivo['nombreArchivo'])?></h3>
                </div>
                
                <?php if ($timeFilter === 'group_by_month'): ?>
                    <!-- Vista de resultados agrupados por mes -->
                    <?php foreach ($resultadoArchivo['conteoTerminos'] as $mes => $terminos): ?>
                        <div class="mb-4">
                            <h4 class="h5 mt-3 mb-2">Mes: <?=htmlspecialchars($mes)?></h4>
                            <div class="tabla-resultados table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">Tipo de Reporte</th>
                                            <th scope="col">Frecuencia</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (is_array($terminos)): ?>
                                            <?php foreach ($terminos as $termino => $conteo): ?>
                                                <tr>
                                                    <td><?=htmlspecialchars($termino)?></td>
                                                    <td><?=$conteo?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Vista de resultados generales -->
                    <div class="tabla-resultados table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">Tipo de Reporte</th>
                                    <th scope="col">Frecuencia</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($resultadoArchivo['conteoTerminos'] as $termino => $conteo): ?>
                                    <tr>
                                        <td><?=htmlspecialchars($termino)?></td>
                                        <td><?=$conteo?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
</div>

<!-- Scripts para funcionalidad -->
<script>
    $(document).ready(function() {
        $("#term_filter").select2({
            theme: "bootstrap-5",
            width: "100%",
            language: {
                noResults: function() {
                    return "No se encontraron resultados";
                },
                searching: function() {
                    return "Buscando...";
                }
            },
            placeholder: "Seleccione un tipo de reporte",
            allowClear: true,
            minimumInputLength: 0,
            maximumInputLength: 20,
            minimumResultsForSearch: 0
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>