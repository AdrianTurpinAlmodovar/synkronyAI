<?php
// Configuración de Base de Datos - USAR VARIABLES DE ENTORNO
$servername = getenv('DB_HOST') ?? 'localhost';
$username   = getenv('DB_USER') ?? 'username';
$password   = getenv('DB_PASS') ?? 'password';
$dbname     = getenv('DB_NAME') ?? 'database';

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>