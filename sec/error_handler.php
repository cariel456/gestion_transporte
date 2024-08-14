<?php
function handleError($error_message) {
    // Registrar el error en un archivo de log
    error_log($error_message, 3, 'errors.log');
    // Redirigir a la página de error genérica
    header("Location: error.php");
    exit();
}
?>