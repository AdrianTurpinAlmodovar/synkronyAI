<?php
// admin/admin_users.php

// 1. Incluimos la configuración y las funciones (subiendo un nivel para llegar a /includes)
require_once '../includes/db_config.php';
require_once '../includes/funciones.php';

// 2. Seguridad centralizada: verifica si el usuario es administrador
check_admin_access();

// 3. Obtener el listado de todos los usuarios
$sql = "SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC";
$result = $conn->query($sql);

if (!$result) {
    display_error("Error al consultar la lista de usuarios: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - SynkronyAI</title>
    <!-- 4. Enlace al CSS del panel de administración -->
    <link rel="stylesheet" href="../assets/css/admin-styles.css">
</head>
<body>
    <header class="admin-header">
        <div>
            <h1>👥 Usuarios del Sistema</h1>
            <p>Gestiona los privilegios de acceso y las cuentas de usuario aquí.</p>
        </div>
        <nav>
            <a href="admin_dashboard.php" style="color: #0077FF; text-decoration: none; font-weight: bold;">← Volver al Panel</a>
        </nav>
    </header>

    <main>
        <div style="margin-bottom: 25px;">
            <!-- Enlace a user_add.php (está en la misma carpeta admin/) -->
            <a href="user_add.php" class="btn-admin" style="text-decoration: none;">+ Añadir Nuevo Usuario</a>
        </div>

        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($user = $result->fetch_assoc()): ?>
                        <tr>
                            <td style="color: #666;">#<?php echo $user['id']; ?></td>
                            <td style="font-weight: bold;"><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="badge <?php echo ($user['role'] === 'admin') ? 'badge-admin' : 'badge-user'; ?>" style="font-size: 0.8rem; padding: 4px 8px; border-radius: 4px; background: <?php echo ($user['role'] === 'admin') ? 'rgba(159, 64, 255, 0.2)' : 'rgba(0, 119, 255, 0.2)'; ?>; color: <?php echo ($user['role'] === 'admin') ? '#9F40FF' : '#0077FF'; ?>;">
                                    <?php echo strtoupper($user['role']); ?>
                                </span>
                            </td>
                            <td style="color: #888; font-size: 0.9rem;">
                                <?php echo date('d/m/Y', strtotime($user['created_at'])); ?>
                            </td>
                            <td class="action-links">
                                <!-- Enlaces a archivos dentro de la misma carpeta admin/ -->
                                <a href="user_edit.php?id=<?php echo $user['id']; ?>" class="edit">Editar</a> 
                                
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <span style="color: #333;">|</span>
                                    <a href="user_delete.php?id=<?php echo $user['id']; ?>" class="delete" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?')">Borrar</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 30px;">No hay otros usuarios registrados actualmente.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer style="margin-top: 40px; text-align: center; color: #555; font-size: 0.8rem;">
        SynkronyAI Security Tool &copy; <?php echo date('Y'); ?>
    </footer>
</body>
</html>