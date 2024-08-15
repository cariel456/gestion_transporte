<?php
require_once 'config/config.php';                  //CONEXION A LA BASE DE DATOS
require_once ROOT_PATH . '/sec/init.php';          //GESTION DE SESION
require_once ROOT_PATH . '/sec/error_handler.php'; //MANEJO DE ERRORES
require_once ROOT_PATH . '/sec/auth_check.php';    //AUTENTICACION DE USUARIOS

// Actualizar la última actividad
$_SESSION['last_activity'] = time();

// Verificar si el usuario está autenticado
checkAuthentication();
?>