<?php
// auth/demo_request_create.php - CON VERIFICACIÓN DE EMAIL DUPLICADO
// Adaptado para la nueva estructura de carpetas

// Ruta corregida: subimos un nivel (../) para entrar en la carpeta includes/
require_once '../includes/db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Obtener y sanear los datos
    $name = isset($_POST['name']) ? $conn->real_escape_string(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? $conn->real_escape_string(trim($_POST['email'])) : '';
    $message = isset($_POST['message']) ? $conn->real_escape_string(trim($_POST['message'])) : '';
    
    // Validación mínima: Si faltan campos obligatorios
    if (empty($name) || empty($email)) {
        // Ruta corregida: subimos un nivel para volver a la raíz (index.php)
        header("Location: ../index.php?status=error&msg=missing_fields"); 
        exit;
    }

    // =======================================================
    // 2. VERIFICACIÓN: Evitar Solicitudes Duplicadas por Email
    // =======================================================
    $check_sql = "SELECT id FROM demo_requests WHERE email = ?";
    
    if ($check_stmt = $conn->prepare($check_sql)) {
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            // ¡Email ya existe! Redirigir y salir.
            $check_stmt->close();
            $conn->close();
            
            // Ruta corregida para volver a la raíz
            header("Location: ../index.php?status=error&msg=already_requested");
            exit;
        }
        $check_stmt->close();
    } else {
        error_log("Error al preparar la verificación de email: " . $conn->error);
    }
    
    // =======================================================
    // 3. PROCESO DE INSERCIÓN
    // =======================================================
    $sql = "INSERT INTO demo_requests (name, email, message) VALUES (?, ?, ?)";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sss", $name, $email, $message);

        if ($stmt->execute()) {
            // Éxito
            $stmt->close();
            $conn->close();
            
            // Ruta corregida para volver a la raíz
            header("Location: ../index.php?status=demo_success");
            exit;
        } else {
            error_log("Error al ejecutar la inserción: " . $stmt->error);
            die("Error interno. Intente de nuevo más tarde.");
        }
    } else {
        die("Error de preparación SQL: " . $conn->error);
    }
} else {
    // Si se accede directamente, redirigir a la raíz
    header("Location: ../index.php");
    exit;
}
?>