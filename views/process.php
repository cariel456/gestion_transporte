<?php
// Incluir archivos necesarios de PhpSpreadsheet
require 'PhpSpreadsheet/src/PhpSpreadsheet/IOFactory.php';
require 'PhpSpreadsheet/src/PhpSpreadsheet/Spreadsheet.php';
require 'PhpSpreadsheet/src/PhpSpreadsheet/Reader/Xlsx.php';

if (class_exists('PhpOffice\PhpSpreadsheet\IOFactory')) {
    echo "PhpSpreadsheet cargado correctamente.<br>";
} else {
    echo "Error al cargar PhpSpreadsheet.<br>";
}

try {
    if (isset($_GET['file'])) {
        $filePath = urldecode($_GET['file']);
        echo "Procesando archivo: " . $filePath . "<br>";
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $data = $worksheet->toArray();

        // Mostrar los datos leídos
        echo "<pre>";
        print_r($data);
        echo "</pre>";

        // Ejemplo de filtro: contar ocurrencias de un valor en una columna
        $columnIndex = 5; // Índice de la columna F (A = 0, B = 1, ..., F = 5)
        $valueToCount = 'TARJ. MONEDERO';
        $count = 0;

        // Iterar desde la fila 4 en adelante (índice 3)
        for ($i = 3; $i < count($data); $i++) {
            if (isset($data[$i][$columnIndex]) && $data[$i][$columnIndex] == $valueToCount) {
                $count++;
            }
        }

        if ($count > 0) {
            echo "El valor '$valueToCount' aparece $count veces en la columna F desde la fila 4 en adelante.";
        } else {
            echo "No se encontraron registros con el valor '$valueToCount' en la columna F desde la fila 4 en adelante.";
        }
    } else {
        echo "No se proporcionó ningún archivo.";
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
