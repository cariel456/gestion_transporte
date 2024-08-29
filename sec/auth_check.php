<?php
require_once 'error_handler.php';
function checkAuthentication() {
    if (isset($_SESSION['user_id'])) {
        header("Location: includes/header.php");
        exit();
    } else {
        header("Location: views/login.php");
        exit();
    }
}
?>