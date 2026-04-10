<?php
// documentacion/index.php

// 1. Configuramos las variables para la cabecera (SEO)
$pageTitle = "Documentación Técnica - SynkronyAI";
$metaDescription = "Explora la documentación técnica, arquitectura y manuales de usuario de la plataforma SynkronyAI.";

// 2. Incluimos tu cabecera global (ajusta la ruta '../' según tu estructura)
require_once '../includes/header.php'; 
?>

<main style="padding: 100px 0 50px 0; background-color: #f9f9f9; min-height: 100vh;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        
        <div style="text-align: center; margin-bottom: 40px;">
            <h1 style="font-family: 'Poppins', sans-serif; color: #333;">Documentación Oficial</h1>
            <p style="color: #666; font-size: 1.1rem;">Arquitectura, integraciones y manual de uso del sistema.</p>
        </div>

        <div style="border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); background: #fff;">
            <iframe 
                src="https://grizzled-chord-74d.notion.site/ebd//33cfe980e83e805cbb49edaee9843e72" 
                width="100%" 
                style="height: 75vh; border: none; display: block;" 
                allowfullscreen>
            </iframe>
        </div>

    </div>
</main>

<?php 
// 3. Incluimos tu pie de página global
require_once '../includes/footer.php'; 
?>