<?php
// logout.php (Ubicación: Raíz del proyecto)

// 1. Iniciar sesión para poder identificar qué vamos a cerrar
session_start();

// 2. Limpiar todas las variables de la sesión
$_SESSION = array();

// 3. Destruir la cookie de sesión en el navegador (opcional, para mayor seguridad)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Destruir la sesión en el servidor
session_destroy();

// 5. Redirigir al formulario de login que está en esta misma carpeta raíz
header("location: login.html");
exit;
?>