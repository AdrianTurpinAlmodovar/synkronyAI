<?php
// admin/user_edit.php
session_start();

// Ruta adaptada: subimos un nivel para entrar en la carpeta includes
require_once '../includes/db_config.php';

// Verificación de seguridad
if (!isset($_SESSION['loggedin']) || $_SESSION['user_role'] !== 'admin') {
    exit("No autorizado");
}

$id = $_GET['id'] ?? $_POST['id'] ?? null;
if (!$id) {
    // Redirige al listado que está en la misma carpeta /admin/
    header("Location: admin_users.php");
    exit();
}

$message = "";

// Procesar Actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $role = $_POST['role'];

    $sql = "UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $name, $email, $role, $id);
    
    if ($stmt->execute()) {
        header("Location: admin_users.php?msg=updated");
        exit();
    } else {
        $message = "Error al actualizar: " . $conn->error;
    }
}

// Obtener datos actuales
$stmt = $conn->prepare("SELECT name, email, role FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario - Admin</title>
    <!-- Ruta adaptada: subimos un nivel para entrar en assets/css/ -->
    <link rel="stylesheet" href="../assets/css/admin-styles.css">
    <style>
        body { padding: 50px; background: #0A0A10; color: white; display: flex; justify-content: center; font-family: sans-serif; }
        .edit-card { background: #14141d; padding: 30px; border-radius: 12px; width: 400px; border: 1px solid #333; }
        label { display: block; margin-bottom: 5px; color: #0077FF; font-weight: bold; }
        input, select { width: 100%; padding: 10px; margin: 5px 0 20px 0; background: #20202D; border: 1px solid #444; color: white; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #9F40FF; border: none; color: white; font-weight: bold; cursor: pointer; border-radius: 4px; }
        button:hover { background: #8a35e0; }
    </style>
</head>
<body>
    <div class="edit-card">
        <h2 style="margin-top:0;">Editar Usuario #<?php echo htmlspecialchars($id); ?></h2>
        <?php if($message) echo "<p style='color:#FF6B6B'>$message</p>"; ?>
        
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
            
            <label>Nombre Completo</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            
            <label>Correo Electrónico</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            
            <label>Rol del Sistema</label>
            <select name="role">
                <option value="user" <?php if($user['role'] == 'user') echo 'selected'; ?>>Usuario Estándar</option>
                <option value="admin" <?php if($user['role'] == 'admin') echo 'selected'; ?>>Administrador</option>
            </select>
            
            <button type="submit">Guardar Cambios</button>
            <p style="text-align: center; margin-top: 15px;">
                <a href="admin_users.php" style="color: #aaa; text-decoration: none; font-size: 0.9rem;">← Cancelar y volver</a>
            </p>
        </form>
    </div>
</body>
</html>