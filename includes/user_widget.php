<?php
// includes/user_widget.php

// 1. Detectar el entorno para ajustar rutas (Si estamos en subcarpeta o raíz)
$in_subfolder = (basename(getcwd()) === 'admin' || basename(getcwd()) === 'dashboard' || basename(getcwd()) === 'auth');
$path_prefix = $in_subfolder ? '../' : '';

// 2. Verificar estado de sesión (sin iniciarla de nuevo si ya está activa)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$is_logged = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$name = $_SESSION['user_name'] ?? 'Invitado';
$role = $_SESSION['user_role'] ?? 'guest';

// 3. Enlaces dinámicos
$login_url = $path_prefix . 'login.html';
$logout_url = $path_prefix . 'logout.php';
$dashboard_url = ($role === 'admin') 
    ? $path_prefix . 'admin/admin_dashboard.php' 
    : $path_prefix . 'dashboard/dashboard_normal.php';
?>

<!-- Estilos del widget (Cargados dinámicamente) -->
<link rel="stylesheet" href="<?php echo $path_prefix; ?>assets/css/user-widget.css">

<div class="user-widget-fixed" id="userWidget">
    <!-- Icono Avatar (Click para toggle) -->
    <div class="user-widget-avatar" onclick="toggleUserMenu()">
        <!-- Icono de usuario SVG -->
        <svg viewBox="0 0 24 24">
            <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z" />
        </svg>
    </div>

    <!-- Menú Desplegable -->
    <div class="user-widget-dropdown">
        <?php if ($is_logged): ?>
            <div class="user-widget-info">
                <span class="user-widget-name"><?php echo htmlspecialchars($name); ?></span>
                <span class="user-widget-role"><?php echo strtoupper($role); ?></span>
            </div>
            
            <a href="<?php echo $dashboard_url; ?>" class="user-widget-link">
                <span>📊</span> Ir a mi Panel
            </a>
            
            <a href="<?php echo $logout_url; ?>" class="user-widget-link logout">
                <span>🚪</span> Cerrar Sesión
            </a>
        <?php else: ?>
            <div class="user-widget-info">
                <span class="user-widget-name">Hola 👋</span>
            </div>
            
            <a href="<?php echo $login_url; ?>" class="user-widget-link" style="color: #0077FF;">
                <span>🔐</span> Iniciar Sesión
            </a>
            <a href="<?php echo $path_prefix; ?>register.html" class="user-widget-link">
                <span>📝</span> Registrarse
            </a>
        <?php endif; ?>
    </div>
</div>

<script>
    // Script simple inline para no depender de archivos externos en este widget
    function toggleUserMenu() {
        const widget = document.getElementById('userWidget');
        widget.classList.toggle('active');
    }

    // Cerrar si se hace clic fuera
    document.addEventListener('click', function(event) {
        const widget = document.getElementById('userWidget');
        const isClickInside = widget.contains(event.target);
        if (!isClickInside && widget.classList.contains('active')) {
            widget.classList.remove('active');
        }
    });
</script>