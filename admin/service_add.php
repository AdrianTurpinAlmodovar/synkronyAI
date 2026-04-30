<?php
// admin/service_add.php
session_start();
require_once '../includes/db_config.php';
require_once '../includes/funciones.php';

check_admin_access();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $icon = $conn->real_escape_string($_POST['icon']);
    $title = $conn->real_escape_string($_POST['title']);
    $desc = $conn->real_escape_string($_POST['description']);
    $resumen = $conn->real_escape_string($_POST['resumen']); // NUEVO CAMPO CAPTURADO
    
    // RESTO DE CAMPOS
    $description_extended = $conn->real_escape_string($_POST['description_extended']);
    $video = $conn->real_escape_string($_POST['video_url']);
    $categoria = $conn->real_escape_string($_POST['categoria']);
    $tipo = $conn->real_escape_string($_POST['tipo']);
    $tiempo_implementacion = $conn->real_escape_string($_POST['tiempo_implementacion']);
    $caracteristicas_principales = $conn->real_escape_string($_POST['caracteristicas_principales']);
    
    // PROCESAR SUBIDA DE IMAGEN
    $image_url = null;
    if (isset($_FILES['image_upload']) && $_FILES['image_upload']['error'] == 0) {
        $upload_dir = '../assets/uploads/services/';
        
        // Crear directorio si no existe
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_name = time() . '_' . basename($_FILES['image_upload']['name']);
        $target_file = $upload_dir . $file_name;
        
        // Validar tipo de archivo
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = mime_content_type($_FILES['image_upload']['tmp_name']);
        
        if (in_array($file_type, $allowed_types) && $_FILES['image_upload']['size'] <= 2097152) { // 2MB max
            if (move_uploaded_file($_FILES['image_upload']['tmp_name'], $target_file)) {
                $image_url = 'assets/uploads/services/' . $file_name;
            }
        }
    }

    // SQL ACTUALIZADO CON EL CAMPO 'resumen' (11 interrogantes en total)
    $stmt = $conn->prepare("INSERT INTO services (icon, title, description, description_extended, resumen, image_url, video_url, categoria, tipo, tiempo_implementacion, caracteristicas_principales) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    // BIND_PARAM ACTUALIZADO (11 's' y añadida la variable $resumen en su posición correcta)
    $stmt->bind_param("sssssssssss", $icon, $title, $desc, $description_extended, $resumen, $image_url, $video, $categoria, $tipo, $tiempo_implementacion, $caracteristicas_principales);
    
    if ($stmt->execute()) {
        header("Location: admin_services.php?status=success");
        exit;
    } else {
        display_error("Error al guardar el servicio: " . $conn->error);
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Servicio - SynkronyAI</title>
    <link rel="stylesheet" href="../assets/css/admin-styles.css">
    <style>
        .admin-form-container { max-width: 500px; margin: 40px auto; background: #14141d; padding: 30px; border-radius: 12px; border: 1px solid #333; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #0077FF; font-weight: bold; }
        input, textarea, select { width: 100%; padding: 12px; background: #0A0A10; border: 1px solid #333; color: white; border-radius: 6px; box-sizing: border-box; }
        .btn-submit { width: 100%; padding: 14px; background: #9F40FF; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; transition: background 0.3s; }
        .btn-submit:hover { background: #b066ff; }
    </style>
</head>
<body>
    <div class="admin-form-container">
        <h2>✨ Nueva Automatización</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Icono (Emoji)</label>
                <input type="text" name="icon" placeholder="🤖" required>
            </div>
            <div class="form-group">
                <label>Título</label>
                <input type="text" name="title" required>
            </div>
            <div class="form-group">
                <label>Descripción Corta</label>
                <textarea name="description" rows="3" placeholder="Descripción breve que aparecerá en listados..."></textarea>
            </div>
            
            <div class="form-group">
                <label>Resumen</label>
                <textarea name="resumen" rows="3" placeholder="Resumen rápido del servicio..."></textarea>
            </div>

            <div class="form-group">
                <label>Descripción Extendida</label>
                <textarea name="description_extended" rows="4" placeholder="Descripción detallada del servicio, características, beneficios, etc..."></textarea>
            </div>
            
            <div class="form-group">
                <label>Subir Imagen (Captura de pantalla)</label>
                <input type="file" name="image_upload" accept="image/jpeg,image/png,image/gif,image/webp">
                <small style="color: #666; display: block; margin-top: 5px;">Formatos: JPG, PNG, GIF, WebP. Máximo 2MB. Si se añade, se mostrará como imagen principal del servicio.</small>
            </div>
            
            <div class="form-group">
                <label>URL del Video Demo (YouTube/Vimeo)</label>
                <input type="url" name="video_url" placeholder="https://www.youtube.com/embed/XXXXXX">
                <small style="color: #666; display: block; margin-top: 5px;">Usa el enlace de "Insertar" (embed) .</small>
            </div>
            
            <div class="form-group">
                <label>Categoría</label>
                <select name="categoria" required>
                    <option value="">Selecciona una categoría</option>
                    <option value="Automatización">Automatización</option>
                    <option value="Inteligencia Artificial">Inteligencia Artificial</option>
                    <option value="Comunicación">Comunicación</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Tipo de Solución</label>
                <input type="text" name="tipo" placeholder="Ej: Automatización Inteligente" required>
            </div>
            
            <div class="form-group">
                <label>Tiempo de Implementación</label>
                <select name="tiempo_implementacion" required>
                    <option value="">Selecciona tiempo</option>
                    <option value="1-2 semanas">1-2 semanas</option>
                    <option value="2-4 semanas">2-4 semanas</option>
                    <option value="3-5 semanas">3-5 semanas</option>
                    <option value="4-6 semanas">4-6 semanas</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Características Principales (JSON)</label>
                <textarea name="caracteristicas_principales" rows="3" placeholder='["Característica 1", "Característica 2", "Característica 3"]'>["Implementación rápida", "Soporte técnico", "Analytics detallados"]</textarea>
                <small style="color: #666; display: block; margin-top: 5px;">Formato JSON: ["Característica 1", "Característica 2", "Característica 3"]</small>
            </div>
            
            <button type="submit" class="btn-submit">Guardar Automatización</button>
            <p style="text-align:center; margin-top: 15px;"><a href="admin_services.php" style="color:#888; text-decoration: none;">Cancelar</a></p>
        </form>
    </div>
</body>
</html>