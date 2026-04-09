<?php
// soluciones/index.php
session_start();
require_once '../includes/db_config.php';

// Función para generar slug desde título
function generateSlug($title) {
    // Convertir a minúsculas
    $slug = strtolower($title);
    
    // Reemplazar caracteres especiales y acentos
    $slug = str_replace(
        ['á', 'à', 'ä', 'â', 'ã', 'å', 'é', 'è', 'ë', 'ê', 'í', 'ì', 'ï', 'î', 
         'ó', 'ò', 'ö', 'ô', 'õ', 'ø', 'ú', 'ù', 'ü', 'û', 'ñ', 'ç', 'ý', 'ÿ'],
        ['a', 'a', 'a', 'a', 'a', 'a', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 
         'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'n', 'c', 'y', 'y'],
        $slug
    );
    
    // Reemplazar caracteres no alfanuméricos con guiones
    $slug = preg_replace('/[^a-zA-Z0-9]/', '-', $slug);
    
    // Eliminar múltiples guiones consecutivos
    $slug = preg_replace('/-+/', '-', $slug);
    
    // Eliminar guiones al principio y final
    $slug = trim($slug, '-');
    
    return $slug;
}

// Conexión a base de datos
$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8");

// Consulta de servicios
$sql_serv = "SELECT * FROM services ORDER BY created_at ASC";
$result_serv = $conn->query($sql_serv);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Soluciones - SynkronyAI</title>
    <link rel="icon" type="image/png" href="../assets/img/logo_sin_fondo.png">
    
    <!-- CSS -->
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/video-modal.css">
    <link rel="stylesheet" href="../assets/css/image-modal.css">
    <link rel="stylesheet" href="../assets/css/demo-modal.css">
    <link rel="stylesheet" href="../assets/css/hero-impact.css">
    <link rel="stylesheet" href="../assets/css/agenda.css">
    <link rel="stylesheet" href="../assets/css/home-features.css">
    <link rel="stylesheet" href="../assets/css/solutions.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
</head>
<body>

<!-- Header de navegación -->
<header class="main-header">
    <div class="container">
        <div class="header-content">
            <!-- Logo -->
            <a href="../" class="logo">
                <img src="../assets/img/logo.png" alt="SynkronyAI">
            </a>
            
            <!-- Menú Principal -->
            <nav class="main-nav">
                <ul>
                    <li><a href="../">Inicio</a></li>
                    <li><a href="../#agenda">Agenda</a></li>
                    <li><a href="#" class="active">Soluciones</a></li>
                    <li><a href="../noticias/">Noticias</a></li>
                </ul>
            </nav>
            
            <!-- User Widget -->
            <?php include '../includes/user_widget_soluciones.php'; ?>
        </div>
    </div>
</header>

<?php include '../includes/modal_image_video.php'; ?>

<main>

    <!-- SECCIÓN DE SOLUCIONES BASE MEJORADA -->
    <section id="automatizaciones" class="solutions-section">
        <!-- Hero Section -->
        <div class="solutions-hero">
            <div class="hero-content">
                <h1 class="hero-title">Transformamos tu Operativa</h1>
                <p class="hero-subtitle">Soluciones inteligentes que automatizan, optimizan y escalan tu negocio</p>
            </div>
        </div>
        
        <!-- Filtros de Categorías -->
        <div class="categories-section">
            <div class="categories-filter">
                <button class="category-btn active" data-category="all">Todas las Soluciones</button>
                <button class="category-btn" data-category="Automatización">Automatización</button>
                <button class="category-btn" data-category="Inteligencia Artificial">Inteligencia Artificial</button>
                <button class="category-btn" data-category="Comunicación">Comunicación</button>
            </div>
        </div>
        
        <!-- Grid de Soluciones -->
        <div class="solutions-container">
            <div class="solutions-grid">
                <?php if ($result_serv && $result_serv->num_rows > 0): ?>
                    <?php while($service = $result_serv->fetch_assoc()): ?>
                        <div class="solution-card" data-category="<?php echo htmlspecialchars($service['categoria'] ?? 'all'); ?>">
                            <div class="solution-visual">
                                <?php if (!empty($service['image_url'])): ?>
                                    <?php 
                                    // Corregir ruta para que funcione desde /soluciones/
                                    $image_url = $service['image_url'];
                                    if (!str_starts_with($image_url, 'http') && !str_starts_with($image_url, '../')) {
                                        $image_url = '../' . $image_url;
                                    }
                                    ?>
                                    <img src="<?php echo htmlspecialchars($image_url); ?>" 
                                         alt="<?php echo htmlspecialchars($service['title']); ?>" 
                                         class="solution-image"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="solution-placeholder" style="display:none; width:100%; height:100%; background:linear-gradient(135deg, #1a1a2e, #2d2d44); display:flex; align-items:center; justify-content:center; color:#9F40FF; font-size:3rem;">
                                        🖼️
                                    </div>
                                <?php else: ?>
                                    <div class="solution-placeholder" style="width:100%; height:100%; background:linear-gradient(135deg, #1a1a2e, #2d2d44); display:flex; align-items:center; justify-content:center; color:#9F40FF; font-size:3rem;">
                                        🖼️
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="solution-content">
                                <div class="solution-category"><?php echo htmlspecialchars($service['categoria']); ?></div>
                                <h3><?php echo htmlspecialchars($service['title']); ?></h3>
                                <p class="solution-description"><?php echo htmlspecialchars($service['description']); ?></p>
                                
                                <div class="solution-features">
                                    <span class="feature-tag">Automatización</span>
                                    <span class="feature-tag">IA</span>
                                </div>
                                
                                <button class="solution-cta" 
                                        onclick="window.location.href='detail.php?slug=<?php echo generateSlug($service['title']); ?>'">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                        Ver detalles completos
                                    </button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>

<!-- Scripts simplificados - Sin modals -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../assets/js/soluciones.js"></script>
</body>
</html>
