<?php
require_once '../config/db_config.php'; //CONEXION A LA DB

$username = 'optaller';     //NOMBRE DEL USUARIO
$password = 'optaller';     //CLAVE DEL USUARIO
$rol_id = 2;            //PONER EL id DEL ROL(DE LA TABLA ROLES ELGIR QUE ROL DARLE AL USUARIO A CREAR, el 1 es ROOT POR EJ.)

$hashed_password = password_hash($password, PASSWORD_DEFAULT); //ENCRIPTACION DE LA CLAVE

$sql = "INSERT INTO usuarios (nombre_usuario, password, rol_id) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $username, $hashed_password, $rol_id); //CREACION DEL USUARIO

if ($stmt->execute()) {
    echo "Usuario de prueba creado con éxito.";
} else {
    echo "Error al crear el usuario de prueba: " . $conn->error;
}
?>