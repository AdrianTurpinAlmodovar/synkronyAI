<?php
// auth/register_process.php
session_start();

// Ruta adaptada: subimos un nivel para entrar en includes/
require_once '../includes/db_config.php';
require_once '../includes/funciones.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Obtener y sanear las entradas
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = 'user'; // Rol por defecto

    // 2. Validaciones (Usando display_error centralizada)
    if (empty($name)) display_error("El nombre es obligatorio.", false, "../register.html");
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) display_error("El formato del correo es inválido.", false, "../register.html");
    if (strlen($password) < 6) display_error("La contraseña debe tener al menos 6 caracteres.", false, "../register.html");
    if ($password !== $confirm_password) display_error("Las contraseñas no coinciden.", false, "../register.html");

    // 3. Verificar si el email ya existe
    $sql_check = "SELECT id FROM users WHERE email = ?";
    if ($stmt_check = $conn->prepare($sql_check)) {
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $stmt_check->store_result();
        
        if ($stmt_check->num_rows > 0) {
            $stmt_check->close();
            display_error(
                "Este email ya está registrado. Por favor, <a href='../login.html' style='color: #0077FF;'>inicia sesión</a>.", 
                true, 
                "../register.html"
            );
        }
        $stmt_check->close();
    }
    
    // 4. Crear el hash seguro
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // 5. Insertar el nuevo usuario
    $sql_insert = "INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)";
    
    if ($stmt_insert = $conn->prepare($sql_insert)) {
        $stmt_insert->bind_param("ssss", $name, $email, $password_hash, $role);

        if ($stmt_insert->execute()) {
            // Éxito: Redirigir al login fuera de la carpeta auth
            $stmt_insert->close();
            $conn->close();
            header("location: ../login.html?status=registered");
            exit;
        } else {
            display_error("Error al registrar el usuario.", false, "../register.html");
        }
    } else {
        display_error("Error de preparación SQL.", false, "../register.html");
    }

} else {
    header("location: ../register.html");
    exit;
}
?>