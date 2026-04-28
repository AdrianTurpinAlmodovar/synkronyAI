<?php
// dashboard/appointment_cancel.php
session_start();
require_once '../includes/db_config.php';
require_once '../includes/funciones.php';

// Verificar sesión
check_login_access();

if (isset($_GET['id']) && isset($_GET['reason'])) {
    $id = (int)$_GET['id'];
    $reason = $conn->real_escape_string($_GET['reason']);
    $user_id = $_SESSION['user_id'];

    // 1. Seguridad: Verificar que la cita pertenece a este usuario y extraer datos
    $check_sql = "SELECT a.id, a.date, u.name 
                  FROM appointments a 
                  JOIN users u ON a.user_id = u.id 
                  WHERE a.id = ? AND a.user_id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // Si intenta borrar la cita de otro, lo echamos
        header("Location: dashboard_normal.php?status=error&msg=unauthorized");
        exit;
    }
    
    // Almacenar los datos recuperados en variables
    $row = $result->fetch_assoc();
    $fecha_cita = $row['date'];
    $nombre_cliente = $row['name'];
    $stmt->close();

    // 2. Actualizar estado a 'cancelled' y guardar motivo
    $update_sql = "UPDATE appointments SET status = 'cancelled', cancellation_reason = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $reason, $id);
    
    if ($stmt->execute()) {
        
        // ============================================================
        // WEBHOOK MAESTRO A MAKE.COM - ACCIÓN CANCELAR
        // ============================================================
        
        $master_webhook_url = 'https://hook.eu1.make.com/r5gyxsson2oxskmqdqwlr0y6dso8a0rd';
        
        try {
            // Construir payload para Make.com incluyendo los nuevos parámetros
            $payload = [
                'accion' => 'cancelar',
                'id_cita' => $id,
                'nombre_cliente' => $nombre_cliente,
                'fecha' => $fecha_cita,
                'motivo' => $reason
            ];
            
            // Enviar petición POST a Make.com
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $master_webhook_url,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_TIMEOUT => 2,
                CURLOPT_RETURNTRANSFER => true
            ]);
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            // Log del webhook (opcional)
            error_log("Webhook Maestro - Acción cancelar - HTTP $http_code: " . $response);
            
        } catch (Exception $e) {
            error_log("Error enviando webhook maestro (cancelar): " . $e->getMessage());
        }
        // ============================================================
        
        header("Location: dashboard_normal.php?status=cancelled");
    } else {
        header("Location: dashboard_normal.php?status=error");
    }
    $stmt->close();
} else {
    header("Location: dashboard_normal.php");
}
$conn->close();
?>