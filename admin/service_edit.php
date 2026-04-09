<?php
// admin/service_edit.php
session_start();
require_once '../includes/db_config.php';
require_once '../includes/funciones.php';

// Seguridad: Solo administradores
// (Si usas check_admin_access() dentro de funciones.php, puedes usarlo aquí también)
if (!isset($_SESSION['loggedin']) || $_SESSION['user_role'] !== 'admin') {
    exit("No autorizado");
}

$id = $_GET['id'] ?? $_POST['id'] ?? null;
if (!$id) {
    header("Location: admin_services.php");
    exit();
}

$message = "";

// 1. PROCESAR EL FORMULARIO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $icon = $_POST['icon'];
    $description = $_POST['description'];
    
    // NUEVOS CAMPOS
    $description_extended = $_POST['description_extended'];
    $resumen = $_POST['resumen'];
    $video_url = $_POST['video_url'];
    $categoria = $_POST['categoria'];
    $tipo = $_POST['tipo'];
    $tiempo_implementacion = $_POST['tiempo_implementacion'];
    $caracteristicas_principales = $_POST['caracteristicas_principales'];

    // Obtener la imagen actual de la base de datos
    $sql_current = "SELECT image_url FROM services WHERE id = ?";
    $stmt_current = $conn->prepare($sql_current);
    $stmt_current->bind_param("i", $id);
    $stmt_current->execute();
    $result_current = $stmt_current->get_result();
    $current_service = $result_current->fetch_assoc();
    $image_url = $current_service['image_url'] ?? '';

    // PROCESAR SUBIDA DE IMAGEN si existe
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
                // Eliminar imagen anterior si existe
                if ($image_url && file_exists('../' . $image_url)) {
                    unlink('../' . $image_url);
                }
                $image_url = 'assets/uploads/services/' . $file_name;
            }
        }
    }

    // Actualizar base de datos
    $sql = "UPDATE services SET title = ?, icon = ?, description = ?, description_extended = ?, resumen = ?, image_url = ?, video_url = ?, categoria = ?, tipo = ?, tiempo_implementacion = ?, caracteristicas_principales = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    // CORRECCIÓN: Añadida una "s" adicional (ahora son 11 "s" y una "i")
    $stmt->bind_param("sssssssssssi", $title, $icon, $description, $description_extended, $resumen, $image_url, $video_url, $categoria, $tipo, $tiempo_implementacion, $caracteristicas_principales, $id);
    
    if ($stmt->execute()) {
        header("Location: admin_services.php?msg=updated");
        exit();
    } else {
        $message = "Error al actualizar: " . $stmt->error; // Usar $stmt->error da más detalles que $conn->error
    }
}

// 2. OBTENER DATOS ACTUALES
$sql = "SELECT * FROM services WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$service = $res->fetch_assoc();

if (!$service) {
    exit("Servicio no encontrado.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Servicio - Admin</title>
    <link rel="stylesheet" href="../assets/css/admin-styles.css">
    <style>
        body { padding: 50px; background: #0A0A10; color: white; display: flex; justify-content: center; font-family: sans-serif; }
        .edit-card { background: #14141d; padding: 30px; border-radius: 12px; width: 400px; border: 1px solid #333; }
        label { display: block; margin-bottom: 5px; color: #0077FF; font-weight: bold; }
        input, select, textarea { width: 100%; padding: 10px; margin: 5px 0 20px 0; background: #20202D; border: 1px solid #444; color: white; border-radius: 4px; box-sizing: border-box; }
        textarea { resize: vertical; min-height: 100px; font-family: sans-serif; }
        button { width: 100%; padding: 12px; background: #9F40FF; border: none; color: white; font-weight: bold; cursor: pointer; border-radius: 4px; }
        button:hover { background: #8a35e0; }
    </style>
</head>
<body>
    <div class="edit-card">
        <h2 style="margin-top:0;">Editar Servicio</h2>
        <?php if($message) echo "<p style='color:#FF6B6B'>$message</p>"; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
            
            <label>Icono (Emoji o Texto)</label>
            <input type="text" name="icon" value="<?php echo htmlspecialchars($service['icon']); ?>" required placeholder="Ej: 🤖">

            <label>Título del Servicio</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($service['title']); ?>" required>
            
            <label>Descripción Corta</label>
            <textarea name="description" rows="3" required><?php echo htmlspecialchars($service['description']); ?></textarea>
            
            <!-- NUEVOS CAMPOS -->
            <label>Descripción Extendida</label>
            <textarea name="description_extended" rows="4" placeholder="Descripción detallada del servicio..."><?php echo htmlspecialchars($service['description_extended'] ?? ''); ?></textarea>
            
            <label>Resumen</label>
            <textarea name="resumen" rows="3" placeholder="Resumen del servicio..."><?php echo htmlspecialchars($service['resumen'] ?? ''); ?></textarea>
            
            <label>Subir Imagen</label>
            <input type="file" name="image_upload" accept="image/jpeg,image/png,image/gif,image/webp">
            <small style="color: #666;">Formatos: JPG, PNG, GIF, WebP. Máximo 2MB.</small>
            
            <label>URL del Video Demo (YouTube/Vimeo)</label>
            <input type="url" name="video_url" value="<?php echo htmlspecialchars($service['video_url'] ?? ''); ?>" placeholder="https://www.youtube.com/embed/XXXXXX">
            <small style="color: #666;">Usa el enlace de "Insertar" (embed).</small>
            
            <!-- NUEVOS CAMPOS DINÁMICOS -->
            <label>Categoría</label>
            <select name="categoria" required>
                <option value="">Selecciona una categoría</option>
                <option value="Automatización" <?php echo ($service['categoria'] ?? '') == 'Automatización' ? 'selected' : ''; ?>>Automatización</option>
                <option value="Inteligencia Artificial" <?php echo ($service['categoria'] ?? '') == 'Inteligencia Artificial' ? 'selected' : ''; ?>>Inteligencia Artificial</option>
                <option value="Comunicación" <?php echo ($service['categoria'] ?? '') == 'Comunicación' ? 'selected' : ''; ?>>Comunicación</option>
            </select>
            
            <label>Tipo de Solución</label>
            <input type="text" name="tipo" value="<?php echo htmlspecialchars($service['tipo'] ?? ''); ?>" placeholder="Ej: Automatización Inteligente" required>
            
            <label>Tiempo de Implementación</label>
            <select name="tiempo_implementacion" required>
                <option value="">Selecciona tiempo</option>
                <option value="1-2 semanas" <?php echo ($service['tiempo_implementacion'] ?? '') == '1-2 semanas' ? 'selected' : ''; ?>>1-2 semanas</option>
                <option value="2-4 semanas" <?php echo ($service['tiempo_implementacion'] ?? '') == '2-4 semanas' ? 'selected' : ''; ?>>2-4 semanas</option>
                <option value="3-5 semanas" <?php echo ($service['tiempo_implementacion'] ?? '') == '3-5 semanas' ? 'selected' : ''; ?>>3-5 semanas</option>
                <option value="4-6 semanas" <?php echo ($service['tiempo_implementacion'] ?? '') == '4-6 semanas' ? 'selected' : ''; ?>>4-6 semanas</option>
            </select>
            
            <label>Características Principales (JSON)</label>
            <textarea name="caracteristicas_principales" rows="3" placeholder='["Característica 1", "Característica 2", "Característica 3"]'><?php echo htmlspecialchars($service['caracteristicas_principales'] ?? '["Implementación rápida", "Soporte técnico", "Analytics detallados"]'); ?></textarea>
            <small style="color: #666;">Formato JSON: ["Característica 1", "Característica 2", "Característica 3"]</small>
            
            <button type="submit">Guardar Cambios</button>
            <p style="text-align: center; margin-top: 15px;">
                <a href="admin_services.php" style="color: #aaa; text-decoration: none; font-size: 0.9rem;">← Cancelar y volver</a>
            </p>
        </form>
    </div>
</body>
</html>
