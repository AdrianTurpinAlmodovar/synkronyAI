<?php
// sitemap.php
require_once 'includes/db_config.php';
$conn->set_charset("utf8mb4");

// Le decimos al navegador y a Google que este archivo es un XML, no HTML
header('Content-Type: text/xml; charset=utf-8');

// Tu dominio real (Asegúrate de que es correcto y termina sin barra)
$base_url = 'https://synkronyai.xo.je'; 

// Función para generar slugs limpios (Misma que usas en la web)
function generateSlug($title) {
    $slug = strtolower($title);
    $slug = str_replace(
        ['á', 'à', 'ä', 'â', 'ã', 'å', 'é', 'è', 'ë', 'ê', 'í', 'ì', 'ï', 'î', 'ó', 'ò', 'ö', 'ô', 'õ', 'ø', 'ú', 'ù', 'ü', 'û', 'ñ', 'ç', 'ý', 'ÿ'],
        ['a', 'a', 'a', 'a', 'a', 'a', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'n', 'c', 'y', 'y'],
        $slug
    );
    $slug = preg_replace('/[^a-zA-Z0-9]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    return trim($slug, '-');
}

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

// 1. Añadir páginas estáticas principales
$paginas_estaticas = [
    '/' => '1.0',
    '/soluciones/' => '0.9',
    '/noticias/' => '0.8',
    '/#agenda' => '0.8',
    '/login.php' => '0.5'
];

foreach ($paginas_estaticas as $url => $prioridad) {
    echo '  <url>';
    echo '      <loc>' . htmlspecialchars($base_url . $url) . '</loc>';
    echo '      <changefreq>weekly</changefreq>';
    echo '      <priority>' . $prioridad . '</priority>';
    echo '  </url>';
}

// 2. Añadir páginas dinámicas (Catálogo de Soluciones)
$sql = "SELECT title FROM services";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $slug = generateSlug($row['title']);
        echo '  <url>';
        // Aquí construimos la URL amigable perfecta
        echo '      <loc>' . htmlspecialchars($base_url . '/soluciones/' . $slug) . '</loc>';
        echo '      <changefreq>monthly</changefreq>';
        echo '      <priority>0.8</priority>';
        echo '  </url>';
    }
}

// 3. (Opcional) Si tuvieras noticias dinámicas en la BBDD, harías otro bucle aquí

echo '</urlset>';
$conn->close();
?>