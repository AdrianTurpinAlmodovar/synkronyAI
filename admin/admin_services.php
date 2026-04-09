<?php
// admin/admin_services.php

// 1. Incluimos la configuración y las funciones (subiendo un nivel para llegar a /includes)
require_once '../includes/db_config.php';
require_once '../includes/funciones.php';

// 2. Seguridad: Verifica si el usuario es administrador
check_admin_access();

// 3. Consulta de servicios
$sql = "SELECT * FROM services ORDER BY created_at DESC";
$services = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Servicios - SynkronyAI</title>
    <!-- 4. Enlace al CSS del panel de administración -->
    <link rel="stylesheet" href="../assets/css/admin-styles.css">
</head>
<body>
    <header class="admin-header">
        <div>
            <h1>🛠️ Gestión de Servicios</h1>
            <p>Administra las automatizaciones que aparecen en la página principal.</p>
        </div>
        <nav>
            <a href="admin_dashboard.php" style="color: #0077FF; text-decoration: none; font-weight: bold;">← Volver al Panel</a>
        </nav>
    </header>

    <main>
        <div style="margin-bottom: 25px;">
            <!-- Enlace a service_add.php (está en la misma carpeta admin/) -->
            <a href="service_add.php" class="btn-admin" style="text-decoration: none;">+ Añadir Nuevo Servicio</a>
        </div>

        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Icono</th>
                        <th>Título</th>
                        <th>Categoría</th>
                        <th>Tipo</th>
                        <th>Tiempo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($services && $services->num_rows > 0): ?>
                        <?php while($s = $services->fetch_assoc()): ?>
                        <tr>
                            <td style="font-size: 1.5rem;"><?php echo $s['icon']; ?></td>
                            <td style="font-weight: bold;"><?php echo htmlspecialchars($s['title']); ?></td>
                            <td style="color: #0077FF;"><?php echo htmlspecialchars($s['categoria'] ?? 'Sin categoría'); ?></td>
                            <td style="color: #9F40FF;"><?php echo htmlspecialchars($s['tipo'] ?? 'Sin tipo'); ?></td>
                            <td style="color: #28a745;"><?php echo htmlspecialchars($s['tiempo_implementacion'] ?? 'No definido'); ?></td>
                            <td class="action-links">
                                <!-- Enlaces a archivos dentro de la misma carpeta admin/ -->
                                <a href="service_edit.php?id=<?php echo $s['id']; ?>" class="edit">Editar</a> 
                                <span style="color: #333;">|</span>
                                <a href="service_delete.php?id=<?php echo $s['id']; ?>" class="delete" onclick="return confirm('¿Estás seguro de que deseas eliminar este servicio?')">Borrar</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 30px;">No hay servicios registrados actualmente.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer style="margin-top: 40px; text-align: center; color: #555; font-size: 0.8rem;">
        SynkronyAI Admin Tool &copy; <?php echo date('Y'); ?>
    </footer>
</body>
</html>