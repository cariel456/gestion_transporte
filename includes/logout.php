<?php
require_once dirname(__DIR__) . '/config/config.php';
session_start();
session_destroy();
header("Location: " . BASE_URL . "/views/login.php");
exit();