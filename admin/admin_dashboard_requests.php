<?php
// admin/admin_dashboard_requests.php
require_once '../includes/db_config.php';
require_once '../includes/funciones.php';

// Seguridad: Solo administradores
check_admin_access();

// --- CORRECCIÓN SQL IMPORTANTE ---
// 1. Seleccionamos todo de appointments (a.*)
// 2. Unimos con la tabla users (u) usando el user_id para sacar nombre y email
// 3. Si tu tabla de usuarios no se llama 'users', cambia la palabra 'users' abajo.
$sql = "SELECT a.*, u.name as nombre_usuario, u.email as email_usuario 
        FROM appointments a 
        LEFT JOIN users u ON a.user_id = u.id 
        ORDER BY a.created_at DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Citas - Admin</title>
    <link rel="stylesheet" href="../assets/css/admin-styles.css">
</head>
<body>
    <header class="admin-header">
        <div>
            <h1>📅 Citas Agendadas</h1>
            <p>Gestiona las citas reservadas por los usuarios.</p>
        </div>
        <nav>
            <a href="admin_dashboard.php" style="color: #0077FF; text-decoration: none; font-weight: bold;">← Volver al Panel</a>
        </nav>
    </header>

    <main class="admin-main-content">
        <div class="header-actions" style="margin-bottom: 20px;">
            <h2 style="color: #aaa; font-size: 1.1rem;">Listado de Citas</h2>
        </div>

        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Estado</th>
                        <th>Fecha de la Cita</th> <th>Solicitado el</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($req = $result->fetch_assoc()): 
                            // Lógica de colores para los estados
                            $bg_color = 'rgba(255, 255, 255, 0.1)';
                            $text_color = '#aaa';
                            
                            $status = isset($req['status']) ? $req['status'] : 'pending';

                            if ($status == 'cancelled') {
                                $bg_color = 'rgba(255, 77, 77, 0.2)'; $text_color = '#FF4D4D';
                            } elseif ($status == 'confirmed') {
                                $bg_color = 'rgba(0, 255, 153, 0.2)'; $text_color = '#00FF99';
                            } elseif ($status == 'pending') {
                                $bg_color = 'rgba(255, 165, 0, 0.2)'; $text_color = '#FFA500';
                            }
                        ?>
                        <tr>
                            <td style="color: #666;">#<?php echo $req['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($req['nombre_usuario'] ?? 'Usuario Desconocido'); ?></strong><br>
                                <span style="font-size: 0.85rem; color: #888;"><?php echo htmlspecialchars($req['email_usuario'] ?? 'Sin email'); ?></span>
                            </td>
                            <td>
                                <span class="badge" style="background: <?php echo $bg_color; ?>; color: <?php echo $text_color; ?>; padding: 5px 10px; border-radius: 4px; font-size: 0.8rem;">
                                    <?php echo htmlspecialchars($status); ?>
                                </span>
                            </td>
                            <td style="color: #ccc; font-size: 0.95rem;">
                                📅 <?php echo date('d/m/Y', strtotime($req['date'])); ?> <br>
                                ⏰ <?php echo date('H:i', strtotime($req['time'])); ?>
                            </td>
                            <td style="font-size: 0.9rem; color: #888;">
                                <?php echo date('d/m/Y', strtotime($req['created_at'])); ?>
                            </td>
                            <td class="action-links">
                                <a href="request_edit.php?id=<?php echo $req['id']; ?>" class="edit">Editar</a> 
                                <span style="color: #333;">|</span>
                                <a href="request_delete.php?id=<?php echo $req['id']; ?>" class="delete" onclick="return confirm('¿Borrar esta cita permanentemente?')">Borrar</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align: center; padding: 40px; color: #666;">No hay citas registradas.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>