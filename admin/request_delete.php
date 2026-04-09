<?php
// admin/request_delete.php
require_once '../includes/db_config.php';
require_once '../includes/funciones.php';

// Seguridad
check_admin_access();

// Verificar si viene el ID
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Aseguramos que sea un número
    
    // CAMBIO IMPORTANTE: Borramos de 'appointments'
    $sql = "DELETE FROM appointments WHERE id = $id";
    
    if ($conn->query($sql) === TRUE) {
        // Redirigir con éxito
        header("Location: admin_dashboard_requests.php?msg=deleted");
        exit;
    } else {
        echo "Error al borrar: " . $conn->error;
    }
} else {
    // Si no hay ID, volver
    header("Location: admin_dashboard_requests.php");
    exit;
}
?>