<?php
// admin/service_delete.php
session_start();

// Ruta adaptada: subimos un nivel para entrar en includes/
require_once '../includes/db_config.php';

// Verificación de seguridad
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    exit("Acceso denegado");
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Preparación de la consulta para eliminar
    $sql = "DELETE FROM services WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        // Redirige al listado que está en la misma carpeta /admin/
        header("Location: admin_services.php?status=deleted");
        exit();
    }
    $stmt->close();
}

$conn->close();
?>