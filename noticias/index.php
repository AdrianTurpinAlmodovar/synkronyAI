<?php
// noticias/index.php
$csvUrl = "https://docs.google.com/spreadsheets/d/e/2PACX-1vQY_vlEwbRQnqwysKIZoj2Z09mQvRefr1sbBjWG8vETxz5Jinr61TJWDD0A4Xr4vyKzdCeRtkNBYZzj/pub?gid=0&single=true&output=csv&t=" . time();
$noticias = [];

if (($handle = fopen($csvUrl, "r")) !== FALSE) {
    fgetcsv($handle); 
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if (count($data) >= 6) {
            $noticias[] = [
                'titulo_original' => $data[0] ?? '',
                'resumen_ia'      => $data[1] ?? '',
                'categoria'       => $data[2] ?? 'General',
                'enlace_original' => $data[3] ?? '#',
                'fecha'           => $data[4] ?? '',
                'imagen_url'      => $data[5] ?? ''
            ];
        }
    }
    fclose($handle);
}
$noticias = array_reverse($noticias);

function obtenerTiempoTranscurrido($fecha) {
    $fecha_noticia = new DateTime($fecha);
    $fecha_actual = new DateTime();
    $diferencia = $fecha_actual->diff($fecha_noticia);

    // Listado de unidades de tiempo para la comparación
    if ($diferencia->y > 0) {
        return "Hace " . $diferencia->y . ($diferencia->y == 1 ? " año" : " años");
    }
    if ($diferencia->m > 0) {
        return "Hace " . $diferencia->m . ($diferencia->m == 1 ? " mes" : " meses");
    }
    if ($diferencia->d > 0) {
        if ($diferencia->d == 1) return "Ayer";
        return "Hace " . $diferencia->d . " días";
    }
    if ($diferencia->h > 0) {
        return "Hace " . $diferencia->h . ($diferencia->h == 1 ? " hora" : " horas");
    }
    if ($diferencia->i > 0) {
        return "Hace " . $diferencia->i . ($diferencia->i == 1 ? " minuto" : " minutos");
    }
    return "Recién publicado";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noticias IA - Synkrony</title>
<style>
        body { background-color: #121212; color: #ffffff; font-family: sans-serif; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { margin-bottom: 40px; text-align: center; }
        .back-link { color: #9F40FF; text-decoration: none; display: block; margin-bottom: 20px; }
        .noticias-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); 
            gap: 20px; 
            align-items: stretch; /* Obliga a todas las tarjetas de la fila a medir igual */
        }
        
        /* Convertimos la tarjeta en un contenedor Flex */
        .noticia-card { 
            background: #1e1e1e; 
            border: 1px solid #333; 
            padding: 0; 
            border-radius: 8px; 
            transition: border-color 0.3s; 
            overflow: hidden; 
            display: flex; 
            flex-direction: column; 
            height: 100%; 
        }
        
        .noticia-card:hover { border-color: #9F40FF; }
        .noticia-imagen { width: 100%; height: 200px; object-fit: cover; border-radius: 8px 8px 0 0; display: block; }
        
        /* El contenedor del texto también es Flex para poder empujar elementos */
        .noticia-content { 
            padding: 20px; 
            display: flex; 
            flex-direction: column; 
            flex-grow: 1; /* Hace que este bloque ocupe todo el espacio sobrante de la tarjeta */
        }
        
        .noticia-categoria { background: #9F40FF; color: white; display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; margin-bottom: 10px; align-self: flex-start; }
        .noticia-titulo { font-size: 1.2rem; margin-bottom: 10px; }
        
        /* flex-grow: 1 en el resumen es el truco clave: empuja lo que hay debajo hacia el fondo */
        .noticia-resumen { font-size: 0.95rem; color: #cccccc; margin-bottom: 15px; flex-grow: 1; }
        
        /* Estilos añadidos para la fecha que faltaban en tu CSS */
        .noticia-fecha { font-size: 0.85rem; color: #888; margin-bottom: 15px; }
        
        .btn-leer { color: #9F40FF; text-decoration: none; font-weight: bold; margin-top: auto; }
    </style>
    <link rel="stylesheet" href="../assets/css/solutions.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
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
                        <li><a href="../soluciones/">Soluciones</a></li>
                        <li><a href="#" class="active">Noticias</a></li>
                    </ul>
                </nav>
                
                <?php 
                // Verificamos si el archivo existe antes de incluirlo
                if(file_exists('../includes/user_widget_soluciones.php')){
                    include '../includes/user_widget_soluciones.php'; 
                }
                ?>
            </div>
        </div>
    </header>


    <div class="container">
        <div class="header">
            <h1>Noticias de Inteligencia Artificial</h1>
            <p>Contenido curado automáticamente mediante modelos de lenguaje.</p>
        </div>

        <div class="noticias-grid">
            <?php if (!empty($noticias)): ?>
                <?php foreach ($noticias as $noticia): ?>
                    <article class="noticia-card">
                        <?php if (!empty($noticia['imagen_url'])): ?>
                            <img src="<?php echo htmlspecialchars($noticia['imagen_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($noticia['titulo_original']); ?>" 
                                 class="noticia-imagen">
                        <?php endif; ?>
                        
                        <div class="noticia-content">
                            <div class="noticia-categoria">
                                <?php echo htmlspecialchars($noticia['categoria']); ?>
                            </div>
                            <h2 class="noticia-titulo">
                                <?php echo htmlspecialchars($noticia['titulo_original']); ?>
                            </h2>
                            <p class="noticia-resumen">
                                <?php echo htmlspecialchars($noticia['resumen_ia']); ?>
                            </p>
                            <p class="noticia-fecha">
                                <?php echo obtenerTiempoTranscurrido($noticia['fecha']); ?>
                            </p>
                            <a href="<?php echo htmlspecialchars($noticia['enlace_original']); ?>" target="_blank" rel="noopener noreferrer" class="btn-leer">
                                Leer artículo completo
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay noticias disponibles.</p>
            <?php endif; ?>
        </div>
    </div>

<?php include '../includes/footer.php'; ?>
</body>
</html>