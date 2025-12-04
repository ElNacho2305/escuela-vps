<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "192.168.137.208";  //  Pon la ip el de la bd
$username = "scuela";       //nombre del user
$password = "Max123456@";   //poner contra del user
$database = "escuela_bd";
$port = 3306;

// Crear conexión
$conexion = new mysqli($servername, $username, $password, $database, $port);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");
?>
