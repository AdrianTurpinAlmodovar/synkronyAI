<?php
require_once '../includes/db_config.php';
$conn->set_charset("utf8mb4");
session_start();

// Obtener slug desde URL y limpiar
$slug = $_GET['slug'] ?? '';
$slug = trim($slug, '/');
$slug = preg_replace('/[^a-zA-Z0-9\-]/', '', $slug);

if (empty($slug)) {
    header("Location: /soluciones/");
    exit;
}

// Buscar servicio en BBDD por slug (método simple y directo)
$sql = "SELECT * FROM services WHERE REPLACE(LOWER(title), ' ', '-') = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Método 3: Búsqueda por ID (fallback)
    if (is_numeric($slug)) {
        $sql = "SELECT * FROM services WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $slug);
        $stmt->execute();
        $result = $stmt->get_result();
    }
}

if ($result->num_rows === 0) {
    // Si no encuentra nada, redirigir
    header("Location: /soluciones/");
    exit;
}

$service = $result->fetch_assoc();

// Meta tags para SEO
$pageTitle = htmlspecialchars($service['title'] . " - SynkronyAI");
$pageDescription = htmlspecialchars($service['description'] ?? '');
$canonicalUrl = "https://synkrony-ai.xo.je/soluciones/" . $slug;

// Función para generar slug desde título (IGUAL que en index.php)
function generateSlug($title) {
    // Convertir a minúsculas y reemplazar caracteres especiales
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

// Función para extraer características de la descripción
function extractFeatures($description) {
    $features = [];
    
    // Palabras clave que indican características
    $keywords = ['automatiza', 'gestiona', 'detecta', 'confirma', 'transcribe', 'actualiza', 'integra', 'analiza', 'optimiza'];
    
    foreach ($keywords as $keyword) {
        if (stripos($description, $keyword) !== false) {
            // Extraer la frase completa que contiene la palabra clave
            $sentences = preg_split('/[.!?]/', $description);
            foreach ($sentences as $sentence) {
                if (stripos($sentence, $keyword) !== false) {
                    $features[] = trim($sentence);
                    break;
                }
            }
        }
    }
    
    // Si no hay características, dividir la descripción en frases importantes
    if (empty($features)) {
        $sentences = preg_split('/[.!?]/', $description);
        foreach ($sentences as $sentence) {
            $sentence = trim($sentence);
            if (strlen($sentence) > 20) {
                $features[] = $sentence;
                if (count($features) >= 3) break;
            }
        }
    }
    
    return array_slice($features, 0, 4);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <meta name="description" content="<?php echo $pageDescription; ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($service['title']); ?>, SynkronyAI, automatización, IA, soluciones empresariales">
    
    <meta property="og:title" content="<?php echo $pageTitle; ?>">
    <meta property="og:description" content="<?php echo $pageDescription; ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo $canonicalUrl; ?>">
    <meta property="og:image" content="<?php echo !empty($service['image_url']) ? htmlspecialchars('https://synkrony-ai.xo.je/' . ltrim($service['image_url'], '/')) : 'https://synkrony-ai.xo.je/assets/img/logo_sin_fondo.png'; ?>">
    
    <link rel="canonical" href="<?php echo $canonicalUrl; ?>">
    
    <link rel="icon" type="image/png" href="../assets/img/logo_sin_fondo.png">
    
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/solutions.css">
    <link rel="stylesheet" href="../assets/css/solution-detail.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
</head>
<body>

<header class="main-header">
    <div class="container">
        <div class="header-content">
            <a href="../" class="logo">
                <img src="../assets/img/logo.png" alt="SynkronyAI">
            </a>
            
            <nav class="main-nav">
                <ul>
                    <li><a href="../">Inicio</a></li>
                    <li><a href="../#agenda">Agenda</a></li>
                    <li><a href="/soluciones/" class="active">Soluciones</a></li>
                    <li><a href="../noticias/">Noticias</a></li>
                </ul>
            </nav>
            
            <?php include '../includes/user_widget_soluciones.php'; ?>
        </div>
    </div>
</header>

<main class="solution-detail">
    <div class="breadcrumb">
        <div class="container">
            <a href="../">Inicio</a> / <a href="/soluciones/">Soluciones</a> / <span><?php echo htmlspecialchars($service['title']); ?></span>
        </div>
    </div>

    <section class="solution-hero">
        <div class="container">
            <div class="hero-content">
                <div class="solution-icon"><?php echo htmlspecialchars($service['icon']); ?></div>
                <h1><?php echo htmlspecialchars($service['title']); ?></h1>
                <p class="hero-description"><?php echo htmlspecialchars($service['resumen']); ?></p>

                <div class="hero-actions">
                    <a href="../#agenda" class="cta-primary">Solicitar Demostración</a>
                    <a href="/soluciones/" class="cta-secondary">← Ver más soluciones</a>
                </div>
            </div>
            
            <div class="hero-visual">
                <?php if (!empty($service['video_url'])): ?>
                    <div class="video-container">
                        <iframe src="<?php echo htmlspecialchars($service['video_url']); ?>" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen>
                        </iframe>
                    </div>
                <?php elseif (!empty($service['image_url'])): ?>
                    <?php 
                    $image_url = $service['image_url'];
                    if (!str_starts_with($image_url, 'http') && !str_starts_with($image_url, '../')) {
                        $image_url = '../' . $image_url;
                    }
                    ?>
                    <img src="<?php echo htmlspecialchars($image_url); ?>" 
                         alt="<?php echo htmlspecialchars($service['title']); ?>" 
                         class="solution-hero-image">
                <?php else: ?>
                    <div class="placeholder-visual">
                        <div class="placeholder-icon"><?php echo htmlspecialchars($service['icon']); ?></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="solution-content">
        <div class="container">
            <div class="content-grid">
                <div class="content-main">
                    <div class="content-section">
                        <h2>Descripción Completa</h2>
                        <div class="description-text">
                            <p><?php echo nl2br(htmlspecialchars($service['description'] ?? '')); ?></p>
                            
                            <?php if (!empty($service['description_extended'])): ?>
                                <div class="extended-description">
                                    <h3>Características Avanzadas</h3>
                                    <p><?php echo nl2br(htmlspecialchars($service['description_extended'])); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="content-section">
                        <h2>Características Principales</h2>
                        <div class="features-grid" >
                            <?php
                            $raw_features = $service['caracteristicas_principales'] ?? '';

                            if (!empty($raw_features)) {
                                // 1. Limpiar codificación y forzar comillas dobles
                                $clean_features = html_entity_decode($raw_features, ENT_QUOTES, 'UTF-8');
                                $clean_features = str_replace("'", '"', $clean_features);
                                
                                // 2. Intentar decodificación estándar
                                $features = json_decode($clean_features, true);
                                
                                if (is_array($features) && !empty($features)) {
                                    // Lectura de matriz exitosa
                                    foreach ($features as $index => $feature):
                                    ?>
                                        <div class="feature-item" style="animation-delay: <?php echo $index * 0.1; ?>s">
                                            <span class="feature-icon">✓</span>
                                            <span class="feature-text"><?php echo htmlspecialchars(trim($feature)); ?></span>
                                        </div>
                                    <?php
                                    endforeach;
                                } else {
                                    // 3. Fallback: Extracción plana
                                    $clean_text = str_replace(['[', ']', '"', "'"], '', $raw_features);
                                    $features = explode(',', $clean_text);
                                    
                                    foreach ($features as $index => $feature):
                                        if (trim($feature) === '') continue;
                                    ?>
                                        <div class="feature-item" style="animation-delay: <?php echo $index * 0.1; ?>s">
                                            <span class="feature-icon">✓</span>
                                            <span class="feature-text"><?php echo htmlspecialchars(trim($feature)); ?></span>
                                        </div>
                                    <?php
                                    endforeach;
                                }
                            } else {
                                // 4. Fallback si no hay registro
                                $features = extractFeatures($service['description'] ?? '');
                                foreach ($features as $index => $feature):
                                ?>
                                    <div class="feature-item" style="animation-delay: <?php echo $index * 0.1; ?>s">
                                        <span class="feature-icon">✓</span>
                                        <span class="feature-text"><?php echo htmlspecialchars(trim($feature)); ?></span>
                                    </div>
                                <?php
                                endforeach;
                            }
                            ?>
                        </div>
                    </div>
                </div>
                
                <div class="content-sidebar">
                    <div class="sidebar-card">
                        <h3>Información Rápida</h3>
                        <div class="info-list">
                            <div class="info-item">
                                <span class="info-label">Categoría:</span>
                                <span class="info-value"><?php echo htmlspecialchars($service['categoria'] ?? 'Solución IA'); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Tipo:</span>
                                <span class="info-value"><?php echo htmlspecialchars($service['tipo'] ?? 'Automatización Inteligente'); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Tiempo de implementación:</span>
                                <span class="info-value"><?php echo htmlspecialchars($service['tiempo_implementacion'] ?? '2-4 semanas'); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="sidebar-card cta-card">
                        <h3>¿Interesado en esta solución?</h3>
                        <p>Hablemos de cómo podemos transformar tu negocio con esta tecnología.</p>
                        <a href="../#agenda" class="cta-primary full-width">Agendar Consulta</a>
                    </div>
                    
                    <div class="sidebar-card">
                        <h3>Otras Soluciones</h3>
                        <div class="related-solutions">
                            <?php
                            // Obtener 3 soluciones relacionadas (aleatorias)
                            $related_sql = "SELECT * FROM services WHERE id != ? ORDER BY RAND() LIMIT 3";
                            $related_stmt = $conn->prepare($related_sql);
                            $related_stmt->bind_param("i", $service['id']);
                            $related_stmt->execute();
                            $related_result = $related_stmt->get_result();
                            
                            while ($related_service = $related_result->fetch_assoc()):
                            ?>
                            <a href="detail.php?slug=<?php echo generateSlug($related_service['title']); ?>" class="related-solution">
                                <span class="related-icon"><?php echo htmlspecialchars($related_service['icon']); ?></span>
                                <span class="related-title"><?php echo htmlspecialchars($related_service['title']); ?></span>
                            </a>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>¿Listo para transformar tu operación?</h2>
                <p>Descubre cómo esta solución puede optimizar tus procesos y aumentar tu productividad.</p>
                <div class="cta-actions">
                    <a href="../#agenda" class="cta-primary">Solicitar Demostración</a>
                    <a href="tel:+34900123456" class="cta-secondary">Llamar ahora</a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
<script>
// Animaciones suaves para la página de detalle
document.addEventListener('DOMContentLoaded', function() {
    if (typeof gsap !== "undefined" && typeof ScrollTrigger !== "undefined") {
        gsap.registerPlugin(ScrollTrigger);
        
        // Animación de entrada
        gsap.from(".solution-icon", { 
            scale: 0, opacity: 0, duration: 0.8, ease: "back.out(1.7)" 
        });
        
        gsap.from(".hero-content h1", { 
            y: 50, opacity: 0, duration: 0.9, delay: 0.3, ease: "power2.out" 
        });
        
        gsap.from(".hero-description", { 
            y: 30, opacity: 0, duration: 0.8, delay: 0.5, ease: "power2.out" 
        });
        
        gsap.from(".hero-actions", { 
            y: 20, opacity: 0, duration: 0.7, delay: 0.7, ease: "power2.out" 
        });
        
        gsap.from(".hero-visual", { 
            scale: 0.9, opacity: 0, duration: 1, delay: 0.4, ease: "power2.out" 
        });
        
        // Animación de características al hacer scroll
        gsap.from(".feature-item", {
            y: 30, duration: 0.6, stagger: 0.1,
            scrollTrigger: {
                trigger: ".features-grid",
                start: "top 85%",
                toggleActions: "play none none reverse"
            }
        });
        
        // Animación de secciones
        gsap.from(".content-section", {
            y: 40, opacity: 0, duration: 0.8, stagger: 0.2,
            scrollTrigger: {
                trigger: ".solution-content",
                start: "top 80%",
                toggleActions: "play none none reverse"
            }
        });
    }
});
</script>
</body>
</html>