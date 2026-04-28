<?php
// index.php
session_start();
require_once 'includes/db_config.php';

// Verificación y configuración de enlaces en una sola línea
$is_logged_in = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$cta_link = $is_logged_in ? ($_SESSION['user_role'] === 'admin' ? 'admin/admin_dashboard.php' : 'dashboard/dashboard_normal.php') : 'login.html';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>SynkronyAI</title>
    <link rel="icon" type="image/png" href="assets/img/logo_sin_fondo.png">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/image-modal.css">
    <link rel="stylesheet" href="assets/css/demo-modal.css">
    <link rel="stylesheet" href="assets/css/hero-impact.css">
    <link rel="stylesheet" href="assets/css/agenda.css">
    <link rel="stylesheet" href="assets/css/home-features.css">
    <link rel="stylesheet" href="assets/css/solutions.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">

</head>
<body>

<?php include 'includes/user_widget.php'; ?>

<main>
    <?php include 'includes/hero_home.php'; ?>
    <?php include 'includes/home_sections.php'; ?>
    <?php include 'includes/footer.php'; ?>
</main>

<script src="assets/js/scripts.js"></script>
<script src="assets/js/home-features.js"></script>

<!-- Botpress Chat Widget -->
<script src="https://cdn.botpress.cloud/webchat/v3.6/inject.js"></script>
<script src="https://files.bpcontent.cloud/2026/03/18/14/20260318144925-98QS164S.js" defer></script>

</body>
</html>