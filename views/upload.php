<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['excelFile'])) {
    $uploadDir = 'uploads/';
    $uploadFile = $uploadDir . basename($_FILES['excelFile']['name']);

    if (move_uploaded_file($_FILES['excelFile']['tmp_name'], $uploadFile)) {
        echo "Archivo subido exitosamente.<br>";
        header('Location: process.php?file=' . urlencode($uploadFile));
    } else {
        echo "Error al subir el archivo.";
    }
}
?>
