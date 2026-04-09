<?php
// includes/user_widget_soluciones.php
session_start();

$is_logged = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$name = $_SESSION['user_name'] ?? 'Invitado';
$role = $_SESSION['user_role'] ?? 'guest';

// Rutas fijas para soluciones (siempre desde subcarpeta)
$login_url = '../login.html';
$logout_url = '../logout.php';
$dashboard_url = ($role === 'admin') 
    ? '../admin/admin_dashboard.php' 
    : '../dashboard/dashboard_normal.php';
?>

<!-- Estilos del widget -->
<link rel="stylesheet" href="../assets/css/user-widget.css">

<div class="user-widget-fixed" id="userWidget">
    <div class="user-widget-avatar" onclick="toggleUserMenu()">
        <svg viewBox="0 0 24 24">
            <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z" />
        </svg>
    </div>

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
            
            <a href="<?php echo $login_url; ?>register.html" class="user-widget-link">
                <span>📝</span> Registrarse
            </a>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleUserMenu() {
    const widget = document.getElementById('userWidget');
    widget.classList.toggle('active');
}

document.addEventListener('click', function(event) {
    const widget = document.getElementById('userWidget');
    const isClickInside = widget.contains(event.target);
    if (!isClickInside && widget.classList.contains('active')) {
        widget.classList.remove('active');
    }
});
</script>
