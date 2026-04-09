<?php
// includes/funciones.php

/**
 * Muestra un mensaje de error estilizado y detiene el script.
 * Diseñada para ser llamada desde archivos en subcarpetas.
 */
function display_error($message, $is_html = false, $back_link = '../index.php') {
    // Si la conexión global existe, la cerramos
    global $conn;
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
    
    $display_content = $is_html ? $message : htmlspecialchars($message);
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Error en la Operación</title>
        <!-- Ruta relativa: sube un nivel desde la subcarpeta y entra en assets -->
        <link rel="stylesheet" href="../assets/css/styles.css"> 
        <style>
            body { 
                background-color: #0A0A10; 
                color: white; 
                font-family: sans-serif; 
                display: flex; 
                align-items: center; 
                justify-content: center; 
                height: 100vh; 
                margin: 0; 
            }
            .error-container {
                max-width: 500px;
                padding: 40px;
                background-color: #14141d;
                border-radius: 12px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
                text-align: center;
                border: 1px solid #333;
            }
            .error-container h1 { color: #FF6B6B; margin-top: 0; font-size: 2rem; }
            .error-container p { color: #b0b0b0; margin-bottom: 30px; line-height: 1.6; font-size: 1.1rem; }
            .back-button {
                display: inline-block;
                padding: 12px 25px;
                background: linear-gradient(90deg, #0077FF, #9F40FF);
                color: white;
                text-decoration: none;
                border-radius: 50px;
                font-weight: bold;
                transition: transform 0.2s;
            }
            .back-button:hover { transform: scale(1.05); }
        </style>
    </head>
    <body>
        <div class="error-container">
            <h1>❌ Error</h1>
            <p><?php echo $display_content; ?></p>
            <a href="<?php echo $back_link; ?>" class="back-button">Volver atrás</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

/**
 * Verifica si el usuario está logueado (para usuarios normales).
 */
function check_login_access() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        // Redirige al login saliendo de la subcarpeta actual
        header("Location: ../login.html");
        exit;
    }
}

/**
 * Verifica si el usuario es administrador.
 */
function check_admin_access() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    
    if (!isset($_SESSION['loggedin']) || $_SESSION['user_role'] !== 'admin') {
        // Redirige al login si no tiene permisos de admin
        header("Location: ../login.html");
        exit;
    }
}
?>