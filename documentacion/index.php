<?php
// 1. Cargamos las funciones globales del sistema
require_once '../includes/funciones.php';

// 2. Activamos el escudo de seguridad: Solo Administradores
check_admin_access();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentación Técnica - SynkronyAI</title>
    <link rel="stylesheet" href="../assets/css/solutions.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Ajuste específico para que el contenido no se meta debajo del header fixed */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .doc-main-content {
            flex: 1;
            padding-top: 120px; /* Espacio para que el header no tape el título */
            padding-bottom: 50px;
        }
        .notion-container {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            border: 1px solid rgba(255,255,255,0.1);
            background: #fff; /* Notion suele ser blanco, así queda bien integrado */
        }
    </style>
</head>
<body class="bg-dark"> <header class="main-header">
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
                        <li><a href="../noticias/">Noticias</a></li>
                        <li><a href="#" class="active">Docs</a></li>
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

    <main class="doc-main-content">
        <div class="container">
            
            <div style="text-align: center; margin-bottom: 40px;">
                <h1 style="font-family: 'Poppins', sans-serif; color: #fff; font-size: 2.5rem;">Documentación Técnica</h1>
                <p style="color: #aaa; font-size: 1.1rem;">Todo sobre la arquitectura y el funcionamiento de SynkronyAI.</p>
            </div>

            <div class="notion-container">
                <iframe 
                    src="https://grizzled-chord-74d.notion.site/ebd//33cfe980e83e805cbb49edaee9843e72" 
                    width="100%" 
                    style="height: 100vh; border: none; display: block;" 
                    allowfullscreen>
                </iframe>
            </div>

        </div>
    </main>

    <?php 
    if (file_exists('../includes/footer.php')) {
        require_once '../includes/footer.php'; 
    }
    ?>

</body>
</html>