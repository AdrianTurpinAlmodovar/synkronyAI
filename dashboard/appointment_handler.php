<?php
// dashboard/appointment_handler.php
session_start();

// 1. CARGAR LIBRERÍAS DE PHPMAILER (Manual)
// Asegúrate de que subiste la carpeta 'PHPMailer' dentro de 'includes'
require_once '../includes/db_config.php';
require_once '../includes/funciones.php';
require_once '../includes/PHPMailer/Exception.php';
require_once '../includes/PHPMailer/PHPMailer.php';
require_once '../includes/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

check_login_access();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    // --- VALIDACIONES ---
    if (empty($date) || empty($time)) {
        header("Location: dashboard_normal.php?status=error&msg=campos_vacios");
        exit;
    }
    // ... (Resto de validaciones igual que antes) ...
    // Verificar límite
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM appointments WHERE user_id = ? AND status = 'confirmed'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $active_appointments = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    if ($active_appointments >= 4) {
        header("Location: dashboard_normal.php?status=error&msg=limit_reached");
        exit;
    }

    // Verificar disponibilidad
    $stmt = $conn->prepare("SELECT id FROM appointments WHERE date = ? AND time = ? AND status = 'confirmed'");
    $stmt->bind_param("ss", $date, $time);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        header("Location: dashboard_normal.php?status=error&msg=hora_ocupada");
        exit;
    }
    $stmt->close();

    // --- INSERTAR CITA ---
    $stmt = $conn->prepare("INSERT INTO appointments (user_id, date, time) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $date, $time);

    if ($stmt->execute()) {
        $appointment_id = $stmt->insert_id;
        
        // ============================================================
        // 5. WEBHOOK MAESTRO A MAKE.COM
        // ============================================================
        
        $master_webhook_url = getenv('MAKE_WEBHOOK_URL') ?? 'https://hook.eu1.make.com/TU_WEBHOOK';
        
        try {
            // Obtener datos del usuario para el webhook
            $stmt_u = $conn->prepare("SELECT email, name FROM users WHERE id = ?");
            $stmt_u->bind_param("i", $user_id);
            $stmt_u->execute();
            $user_data = $stmt_u->get_result()->fetch_assoc();
            $stmt_u->close();
            
            if ($user_data) {
                $to_email = $user_data['email'];
                $to_name = $user_data['name'];
                
                // Construir payload para Make.com con acción "crear"
                $payload = [
                    'accion' => 'crear',
                    'id_cita' => $appointment_id,
                    'nombre_cliente' => $to_name,
                    'email_cliente' => $to_email,
                    'fecha' => $date,
                    'hora' => $time
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
                error_log("Webhook Maestro - Acción crear - HTTP $http_code: " . $response);
            }
            
        } catch (Exception $e) {
            error_log("Error enviando webhook maestro (crear): " . $e->getMessage());
        }
        // ============================================================
        
        // ============================================================
        // 4. ENVÍO DE CORREO MEDIANTE SMTP (GMAIL)
        // ============================================================
        
        if ($user_data) {
            $to_email = $user_data['email'];
            $to_name = $user_data['name'];
            
            // Correo al usuario
            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'synkronyai@gmail.com';
                $mail->Password   = 'ceat pcmh rrho jjpr';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;
                $mail->setFrom('synkronyai@gmail.com', 'SynkronyAI');
                $mail->addAddress($to_email, $to_name);
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Subject = '✅ Confirmación de Cita - SynkronyAI';
                
                $bodyContent = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
                    <h2 style='color: #9F40FF;'>¡Cita Reservada!</h2>
                    <p>Hola <strong>$to_name</strong>,</p>
                    <p>Tu sesión ha sido confirmada en nuestro calendario.</p>
                    <div style='background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                        <p style='margin: 5px 0;'><strong>📅 Fecha:</strong> " . date('d/m/Y', strtotime($date)) . "</p>
                        <p style='margin: 5px 0;'><strong>⏰ Hora:</strong> " . substr($time, 0, 5) . "h</p>
                        <p style='margin: 5px 0;'><strong>📍 Enlace:</strong> Recibirá un correo y un recordatorio horas antes de la reunion</p>
                    </div>
                    <p style='font-size: 0.9em; color: #666;'>Si necesitas cancelar, entra a tu panel de usuario.</p>
                    <hr style='border: none; border-top: 1px solid #eee;'>
                    <p style='text-align: center; color: #aaa; font-size: 0.8em;'>© SynkronyAI</p>
                </div>";
                
                $mail->Body = $bodyContent;
                $mail->AltBody = "Hola $to_name, tu cita el $date a las $time ha sido confirmada.";
                $mail->send();
                
            } catch (Exception $e) {
                error_log("Error enviando correo a usuario: {$mail->ErrorInfo}");
            }
            
            // Correo al administrador
            try {
                $admin_mail = new PHPMailer(true);
                $admin_mail->isSMTP();
                $admin_mail->Host       = 'smtp.gmail.com';
                $admin_mail->SMTPAuth   = true;
                $admin_mail->Username   = 'synkronyai@gmail.com';
                $admin_mail->Password   = 'ceat pcmh rrho jjpr';
                $admin_mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $admin_mail->Port       = 587;
                $admin_mail->setFrom('synkronyai@gmail.com', 'SynkronyAI - Sistema');
                $admin_mail->addAddress('synkronyai@gmail.com', 'Administrador');
                $admin_mail->isHTML(true);
                $admin_mail->CharSet = 'UTF-8';
                $admin_mail->Subject = '🔔 Nueva Reserva - SynkronyAI';
                
                $adminBody = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
                    <h2 style='color: #9F40FF;'>🔔 Nueva Reserva Recibida</h2>
                    <p>Un usuario ha reservado una nueva cita en el sistema:</p>
                    <div style='background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                        <p style='margin: 5px 0;'><strong>👤 Cliente:</strong> $to_name</p>
                        <p style='margin: 5px 0;'><strong>📧 Email:</strong> $to_email</p>
                        <p style='margin: 5px 0;'><strong>📅 Fecha:</strong> " . date('d/m/Y', strtotime($date)) . "</p>
                        <p style='margin: 5px 0;'><strong>⏰ Hora:</strong> " . substr($time, 0, 5) . "h</p>
                    </div>
                    <p style='font-size: 0.9em; color: #666;'>Revisa el panel de administración para más detalles.</p>
                    <hr style='border: none; border-top: 1px solid #eee;'>
                    <p style='text-align: center; color: #aaa; font-size: 0.8em;'>© SynkronyAI - Sistema Automático</p>
                </div>";
                
                $admin_mail->Body = $adminBody;
                $admin_mail->AltBody = "Nueva reserva: $to_name ($to_email) ha reservado para el $date a las $time.";
                $admin_mail->send();
                
            } catch (Exception $e) {
                error_log("Error enviando correo a administrador: {$admin_mail->ErrorInfo}");
            }
        }
        // ============================================================

        header("Location: dashboard_normal.php?status=success_appointment");
        
    } else {
        header("Location: dashboard_normal.php?status=error");
    }
    $stmt->close();
}
$conn->close();
?>