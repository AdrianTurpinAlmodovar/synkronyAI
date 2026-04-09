<?php
// admin/user_add.php
session_start();
require_once '../includes/db_config.php';
require_once '../includes/funciones.php';

// Seguridad: Solo administradores
check_admin_access();

$message = "";
$error = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // 1. Verificar si el email ya existe
    $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $result = $check_email->get_result();

    if ($result->num_rows > 0) {
        $message = "Error: El correo electrónico ya está registrado.";
        $error = true;
    } else {
        // 2. Cifrar contraseña
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // 3. Insertar usuario
        $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $password_hash, $role);
        
        if ($stmt->execute()) {
            header("Location: admin_users.php?msg=created");
            exit();
        } else {
            $message = "Error en el sistema: " . $conn->error;
            $error = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Usuario - Admin</title>
    <link rel="stylesheet" href="../assets/css/admin-styles.css">
    <style>
        body { padding: 50px; background: #0A0A10; color: white; display: flex; justify-content: center; font-family: sans-serif; }
        .add-card { background: #14141d; padding: 35px; border-radius: 15px; width: 450px; border: 1px solid #333; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        label { display: block; margin-bottom: 8px; color: #0077FF; font-weight: bold; font-size: 0.9rem; }
        input, select { width: 100%; padding: 12px; margin-bottom: 20px; background: #0A0A10; border: 1px solid #333; color: white; border-radius: 6px; box-sizing: border-box; }
        input:focus { border-color: #9F40FF; outline: none; }
        button { width: 100%; padding: 14px; background: #9F40FF; border: none; color: white; font-weight: bold; cursor: pointer; border-radius: 6px; font-size: 1rem; }
        button:hover { background: #8a35e0; }
        .alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem; }
        .alert-error { background: rgba(255, 107, 107, 0.1); border: 1px solid #FF6B6B; color: #FF6B6B; }
    </style>
</head>
<body>
    <div class="add-card">
        <h2 style="margin-top:0; color: #fff;">➕ Registrar Nuevo Usuario</h2>
        <p style="color: #888; margin-bottom: 25px;">Completa los datos para crear una nueva cuenta en el sistema.</p>
        
        <?php if($message): ?>
            <div class="alert <?php echo $error ? 'alert-error' : ''; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <label>Nombre Completo</label>
            <input type="text" name="name" placeholder="Ej: Juan Pérez" required>
            
            <label>Correo Electrónico</label>
            <input type="email" name="email" placeholder="ejemplo@correo.com" required>
            
            <label>Contraseña Temporal</label>
            <input type="password" name="password" placeholder="Mínimo 6 caracteres" required minlength="6">
            
            <label>Rol del Sistema</label>
            <select name="role">
                <option value="user">Usuario Estándar</option>
                <option value="admin">Administrador</option>
            </select>
            
            <button type="submit">Crear Usuario</button>
            <p style="text-align: center; margin-top: 20px;">
                <a href="admin_users.php" style="color: #aaa; text-decoration: none; font-size: 0.85rem;">← Volver al listado</a>
            </p>
        </form>
    </div>
</body>
</html>