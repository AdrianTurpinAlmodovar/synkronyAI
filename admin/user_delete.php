<?php
// admin/user_delete.php
session_start();

// Ruta adaptada: subimos un nivel (../) para entrar en la carpeta includes
require_once '../includes/db_config.php';

// Verificación de seguridad (mantenida igual para no cambiar tu lógica)
if (!isset($_SESSION['loggedin']) || $_SESSION['user_role'] !== 'admin') {
    exit("Acceso prohibido");
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // No permitir que el administrador se borre a sí mismo
    if ($id === $_SESSION['user_id']) {
        die("No puedes eliminar tu propia cuenta.");
    }

    // Preparación de la consulta para eliminar de la tabla users
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        // Redirige al listado de usuarios que está en la misma carpeta /admin/
        header("Location: admin_users.php?msg=deleted");
        exit();
    } else {
        echo "Error al eliminar usuario.";
    }
    $stmt->close();
}

$conn->close();
?>