<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Inicializar variables
$allTermCounts = [];
$timeFilter = 'all';
$fechaColumnIndex = 1;    // Columna B (índice 1) para fecha_importacion
$reporteColumnIndex = 5;  // Columna F (índice 5) para reporte

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_files'])) {
    $files = $_FILES['excel_files'];
    $timeFilter = $_POST['time_filter'] ?? 'all';

    $tiposPermitidos = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'text/csv'];

    foreach ($files['tmp_name'] as $index => $filePath) {
        if ($files['error'][$index] === UPLOAD_ERR_OK) {
            if (!in_array($files['type'][$index], $tiposPermitidos)) {
                echo "<div class='alert alert-danger'>Tipo de archivo no permitido. Solo archivos Excel y CSV son aceptados.</div>";
                continue;
            }

            try {
                $spreadsheet = IOFactory::load($filePath);
                $sheet = $spreadsheet->getActiveSheet();
                $datos = $sheet->toArray();
                $filas = array_slice($datos, 1); // Saltar la fila de encabezado

                // Encontrar la fecha más reciente en el archivo
                $fechaMasReciente = null;
                foreach ($filas as $fila) {
                    if (!empty($fila[$fechaColumnIndex])) {
                        $fechaActual = DateTime::createFromFormat('Y-m-d H:i:s', $fila[$fechaColumnIndex]);
                        if ($fechaActual && ($fechaMasReciente === null || $fechaActual > $fechaMasReciente)) {
                            $fechaMasReciente = $fechaActual;
                        }
                    }
                }

                // Si no hay fechas válidas, continuar con el siguiente archivo
                if ($fechaMasReciente === null) {
                    echo "<div class='alert alert-warning'>No se encontraron fechas válidas en el archivo.</div>";
                    continue;
                }

                // Filtrar filas basadas en el intervalo de tiempo seleccionado
                $filasFiltradasPorTiempo = array_filter($filas, function ($fila) use ($timeFilter, $fechaColumnIndex, $fechaMasReciente) {
                    if (empty($fila[$fechaColumnIndex])) {
                        return false;
                    }

                    $fechaFila = DateTime::createFromFormat('Y-m-d H:i:s', $fila[$fechaColumnIndex]);
                    if (!$fechaFila) {
                        return false;
                    }

                    switch ($timeFilter) {
                        case 'hour':
                            $limiteInferior = clone $fechaMasReciente;
                            $limiteInferior->modify('-1 hour');
                            return $fechaFila >= $limiteInferior && $fechaFila <= $fechaMasReciente;
                        
                        case 'day':
                            return $fechaFila->format('Y-m-d') === $fechaMasReciente->format('Y-m-d');
                        
                        case 'week':
                            $limiteInferior = clone $fechaMasReciente;
                            $limiteInferior->modify('-7 days');
                            return $fechaFila >= $limiteInferior && $fechaFila <= $fechaMasReciente;
                        
                        case 'month':
                            return $fechaFila->format('Y-m') === $fechaMasReciente->format('Y-m');
                        
                        default:
                            return true;
                    }
                });

                // Contar términos de la columna reporte
                $conteoTerminosPorArchivo = [];
                foreach ($filasFiltradasPorTiempo as $fila) {
                    if (isset($fila[$reporteColumnIndex]) && !empty($fila[$reporteColumnIndex])) {
                        $termino = strtoupper(trim($fila[$reporteColumnIndex]));
                        if (!empty($termino)) {
                            $conteoTerminosPorArchivo[$termino] = ($conteoTerminosPorArchivo[$termino] ?? 0) + 1;
                        }
                    }
                }

                // Ordenar términos por frecuencia en orden descendente
                arsort($conteoTerminosPorArchivo);

                $allTermCounts[] = [
                    'nombreArchivo' => $files['name'][$index],
                    'conteoTerminos' => $conteoTerminosPorArchivo,
                    'fechaMasReciente' => $fechaMasReciente->format('Y-m-d H:i:s')
                ];

            } catch (Exception $e) {
                echo "<div class='alert alert-danger'>Error al procesar el archivo: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Análisis de Archivos Excel - Reportes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Los estilos permanecen igual... -->
</head>
<body class="bg-light">
    <div class="contenedor-personalizado mt-4">
        <h1 class="text-center mb-4">Análisis de Reportes en Archivos Excel</h1>
        
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="contenedor-carga-archivos mb-4">
                <label for="excel_files" class="form-label fw-bold">Selecciona archivos Excel</label>
                <input type="file" name="excel_files[]" id="excel_files" 
                       class="form-control" multiple required>
                <small class="text-muted d-block mt-2">Se analizará la fecha de importación (columna B)</small>
                <small class="text-muted d-block">Se contabilizarán los tipos de reporte (columna F)</small>
            </div>
            
            <div class="mb-4">
                <label for="time_filter" class="form-label fw-bold">Filtro de tiempo</label>
                <select name="time_filter" id="time_filter" class="form-select">
                    <option value="all" <?= $timeFilter === 'all' ? 'selected' : '' ?>>Todos los registros</option>
                    <option value="hour" <?= $timeFilter === 'hour' ? 'selected' : '' ?>>Última hora</option>
                    <option value="day" <?= $timeFilter === 'day' ? 'selected' : '' ?>>Hoy</option>
                    <option value="week" <?= $timeFilter === 'week' ? 'selected' : '' ?>>Esta semana</option>
                    <option value="month" <?= $timeFilter === 'month' ? 'selected' : '' ?>>Este mes</option>
                </select>
            </div>
            
            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg px-4">
                    Analizar archivos
                </button>
            </div>
        </form>

        <?php if (!empty($allTermCounts)): ?>
            <div class="contenedor-resultados">
                <h2 class="text-center mb-4">Resultados del Análisis de Reportes</h2>
                
                <?php foreach ($allTermCounts as $resultadoArchivo): ?>
                    <div class="mb-5">
                        <h3 class="border-bottom pb-2">
                            📊 <?= htmlspecialchars($resultadoArchivo['nombreArchivo']) ?>
                        </h3>
                        <p class="text-muted">
                            Fecha más reciente: <?= htmlspecialchars($resultadoArchivo['fechaMasReciente']) ?>
                        </p>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tipo de Reporte</th>
                                        <th>Frecuencia</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($resultadoArchivo['conteoTerminos'] as $termino => $conteo): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($termino) ?></td>
                                            <td><?= $conteo ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="contenedor-grafica">
                            <canvas id="grafica_<?= preg_replace('/[^a-zA-Z0-9]/', '_', $resultadoArchivo['nombreArchivo']) ?>"></canvas>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            <?php foreach ($allTermCounts as $resultadoArchivo): ?>
                const ctx_<?= preg_replace('/[^a-zA-Z0-9]/', '_', $resultadoArchivo['nombreArchivo']) ?> = 
                    document.getElementById('grafica_<?= preg_replace('/[^a-zA-Z0-9]/', '_', $resultadoArchivo['nombreArchivo']) ?>').getContext('2d');
                
                const etiquetas_<?= preg_replace('/[^a-zA-Z0-9]/', '_', $resultadoArchivo['nombreArchivo']) ?> = 
                    <?= json_encode(array_keys(array_slice($resultadoArchivo['conteoTerminos'], 0, 5, true))) ?>;
                
                const datos_<?= preg_replace('/[^a-zA-Z0-9]/', '_', $resultadoArchivo['nombreArchivo']) ?> = 
                    <?= json_encode(array_values(array_slice($resultadoArchivo['conteoTerminos'], 0, 5, true))) ?>;
                
                new Chart(ctx_<?= preg_replace('/[^a-zA-Z0-9]/', '_', $resultadoArchivo['nombreArchivo']) ?>, {
                    type: 'bar',
                    data: {
                        labels: etiquetas_<?= preg_replace('/[^a-zA-Z0-9]/', '_', $resultadoArchivo['nombreArchivo']) ?>,
                        datasets: [{
                            label: 'Frecuencia de Reportes (Top 5)',
                            data: datos_<?= preg_replace('/[^a-zA-Z0-9]/', '_', $resultadoArchivo['nombreArchivo']) ?>,
                            backgroundColor: [
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                                'rgba(255, 159, 64, 0.2)',
                                'rgba(255, 99, 132, 0.2)'
                            ],
                            borderColor: [
                                'rgba(75, 192, 192, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)',
                                'rgba(255, 99, 132, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            title: {
                                display: true,
                                text: 'Top 5 Tipos de Reportes más Frecuentes'
                            }
                        }
                    }
                });
            <?php endforeach; ?>
        });
    </script>
</body>
</html>