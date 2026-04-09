<?php
// admin/request_edit.php
session_start();
require_once '../includes/db_config.php';
require_once '../includes/funciones.php'; // Incluimos funciones para verificar admin

// Verificación de seguridad
if (!isset($_SESSION['loggedin']) || $_SESSION['user_role'] !== 'admin') {
    exit("No autorizado");
}

$id = $_GET['id'] ?? $_POST['id'] ?? null;
if (!$id) {
    // Redirige al listado de citas
    header("Location: admin_dashboard_requests.php");
    exit();
}

$message = "";

// 1. PROCESAR ACTUALIZACIÓN
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = $_POST['status'];
    $new_date = $_POST['date'];
    $new_time = $_POST['time'];

    // Actualizamos la tabla appointments
    $sql = "UPDATE appointments SET status = ?, date = ?, time = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $new_status, $new_date, $new_time, $id);
    
    if ($stmt->execute()) {
        // Redirigir con mensaje de éxito
        header("Location: admin_dashboard_requests.php?msg=updated");
        exit();
    } else {
        $message = "Error al actualizar: " . $conn->error;
    }
}

// 2. OBTENER DATOS ACTUALES
// Usamos el JOIN para poder mostrar el nombre del cliente en el formulario
$sql = "SELECT a.*, u.name as nombre_usuario, u.email as email_usuario 
        FROM appointments a 
        LEFT JOIN users u ON a.user_id = u.id 
        WHERE a.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$cita = $res->fetch_assoc();

if (!$cita) {
    exit("Cita no encontrada.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Cita - Admin</title>
    <link rel="stylesheet" href="../assets/css/admin-styles.css">
    <style>
        body { padding: 50px; background: #0A0A10; color: white; display: flex; justify-content: center; font-family: sans-serif; }
        .edit-card { background: #14141d; padding: 30px; border-radius: 12px; width: 400px; border: 1px solid #333; }
        label { display: block; margin-bottom: 5px; color: #0077FF; font-weight: bold; }
        input, select { width: 100%; padding: 10px; margin: 5px 0 20px 0; background: #20202D; border: 1px solid #444; color: white; border-radius: 4px; box-sizing: border-box; }
        /* Estilo especial para inputs deshabilitados (solo lectura) */
        input:disabled { background: #1a1a25; color: #777; border-color: #333; cursor: not-allowed; }
        button { width: 100%; padding: 12px; background: #9F40FF; border: none; color: white; font-weight: bold; cursor: pointer; border-radius: 4px; }
        button:hover { background: #8a35e0; }
    </style>
</head>
<body>
    <div class="edit-card">
        <h2 style="margin-top:0;">Editar Cita #<?php echo htmlspecialchars($id); ?></h2>
        <?php if($message) echo "<p style='color:#FF6B6B'>$message</p>"; ?>
        
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
            
            <label>Cliente (Solo Lectura)</label>
            <input type="text" value="<?php echo htmlspecialchars($cita['nombre_usuario'] ?? 'Desconocido'); ?>" disabled>

            <label>Estado de la Cita</label>
            <select name="status">
                <option value="pending" <?php if($cita['status'] == 'pending') echo 'selected'; ?>>Pendiente</option>
                <option value="confirmed" <?php if($cita['status'] == 'confirmed') echo 'selected'; ?>>Confirmada</option>
                <option value="cancelled" <?php if($cita['status'] == 'cancelled') echo 'selected'; ?>>Cancelada</option>
                <option value="completed" <?php if($cita['status'] == 'completed') echo 'selected'; ?>>Finalizada</option>
            </select>
            
            <label>Fecha Programada</label>
            <input type="date" name="date" value="<?php echo htmlspecialchars($cita['date']); ?>" required>

            <label>Hora Programada</label>
            <input type="time" name="time" value="<?php echo htmlspecialchars($cita['time']); ?>" required>
            
            <button type="submit">Guardar Cambios</button>
            <p style="text-align: center; margin-top: 15px;">
                <a href="admin_dashboard_requests.php" style="color: #aaa; text-decoration: none; font-size: 0.9rem;">← Cancelar y volver</a>
            </p>
        </form>
    </div>
</body>
</html>