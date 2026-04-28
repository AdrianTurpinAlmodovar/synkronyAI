<?php
// dashboard/dashboard_normal.php
require_once '../includes/db_config.php'; 
require_once '../includes/funciones.php';

check_login_access();

$user_name = htmlspecialchars($_SESSION['user_name'] ?? 'Usuario');
$user_id = htmlspecialchars($_SESSION['user_id']);
$user_role = htmlspecialchars($_SESSION['user_role']);

$role_dashboard_link = ($user_role === 'admin') ? '../admin/admin_dashboard.php' : '../index.php';
$role_dashboard_text = ($user_role === 'admin') ? 'Ir al Panel Admin' : 'Volver a la Home';

// ==========================================================
// AUTOMATIZACIÓN DE ESTADOS
// ==========================================================

// 1. LIMPIEZA DE CANCELADAS (Tu lógica anterior)
// Borra citas canceladas cuya fecha ya pasó hace más de 2 horas
$sql_clean = "DELETE FROM appointments WHERE status = 'cancelled' AND CONCAT(date, ' ', time) < NOW() - INTERVAL 2 HOUR";
$conn->query($sql_clean);

// 2. NUEVO: AUTO-COMPLETAR CITAS PASADAS
// Si la cita está 'confirmed' Y la fecha/hora es menor a AHORA, la pasamos a 'completed'
$sql_complete = "UPDATE appointments SET status = 'completed' WHERE status = 'confirmed' AND CONCAT(date, ' ', time) < NOW()";
$conn->query($sql_complete);

// ==========================================================

// OBTENER CITAS
$sql_citas = "SELECT * FROM appointments WHERE user_id = ? ORDER BY date ASC, time ASC";
$stmt = $conn->prepare($sql_citas);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$mis_citas = $stmt->get_result();

// OBTENER HORAS OCUPADAS (Para el formulario)
$sql_ocupadas = "SELECT date, time FROM appointments WHERE status = 'confirmed'";
$res_ocupadas = $conn->query($sql_ocupadas);
$bookedSlots = [];
while($row = $res_ocupadas->fetch_assoc()) {
    $bookedSlots[] = $row['date'] . '|' . $row['time'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SynkronyAI</title>
    <link rel="stylesheet" href="../assets/css/agenda.css"> 
    <link rel="stylesheet" href="../assets/css/styles.css"> 
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { padding: 50px; background-color: #0A0A10; color: white; font-family: 'Inter', sans-serif; }
        .dashboard-container { 
            max-width: 1100px; margin: 0 auto; padding: 40px; 
            background-color: #14141d; border-radius: 12px; border: 1px solid #333;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        .welcome-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .welcome-message { color: #0077FF; margin: 0; font-size: 2rem; }
        
        .role-badge { 
            background: rgba(0, 255, 153, 0.1); color: #00ff99; 
            padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; border: 1px solid rgba(0, 255, 153, 0.3);
            text-transform: uppercase; letter-spacing: 1px;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 30px;
        }

        /* CARD DE FORMULARIO */
        .appointment-form-card {
            background: linear-gradient(145deg, #1c1c2b, #252538);
            border: 1px solid #444;
            padding: 30px;
            border-radius: 10px;
            height: fit-content;
        }
        .appointment-form-card h3 { margin-top: 0; color: #fff; border-bottom: 1px solid #444; padding-bottom: 15px; margin-bottom: 25px; }
        
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #aaa; font-size: 0.95rem; }
        input[type="date"], select {
            width: 100%; padding: 12px; background: #0A0A10; border: 1px solid #555; 
            color: white; border-radius: 6px; box-sizing: border-box; font-size: 1rem;
            transition: border-color 0.3s;
        }
        input[type="date"]:focus, select:focus { border-color: #0077FF; outline: none; }
        
        .btn-schedule {
            background: linear-gradient(90deg, #0077FF, #9F40FF); color: white;
            padding: 16px; border: none; width: 100%;
            border-radius: 6px; font-weight: bold; cursor: pointer;
            transition: transform 0.2s, opacity 0.3s; font-size: 1rem;
        }
        .btn-schedule:hover:not(:disabled) { opacity: 0.9; transform: translateY(-2px); }
        .btn-schedule:disabled { background: #333; cursor: not-allowed; opacity: 0.5; }

        /* LISTADO DE CITAS */
        .appointments-list {
            background: #1c1c2b;
            padding: 30px;
            border-radius: 10px;
            border: 1px solid #333;
            max-height: 600px;
            overflow-y: auto;
        }
        .appointment-item {
            display: flex; justify-content: space-between; align-items: center;
            padding: 18px; border-bottom: 1px solid #333; transition: background 0.3s;
        }
        .appointment-item:hover { background: rgba(255, 255, 255, 0.03); }
        .appointment-item:last-child { border-bottom: none; }
        
        /* ESTILOS DE ESTADO */
        .appointment-item.cancelled { opacity: 0.5; border-left: 3px solid #FF6B6B; } 
        .appointment-item.completed { border-left: 3px solid #0077FF; background: rgba(0, 119, 255, 0.05); }

        .date-box { 
            background: #0A0A10; padding: 10px; border-radius: 8px; text-align: center; min-width: 70px; border: 1px solid #333; 
        }
        .date-day { display: block; font-size: 1.4rem; font-weight: bold; color: #fff; }
        .date-month { display: block; font-size: 0.85rem; color: #9F40FF; text-transform: uppercase; font-weight: 600; }
        
        .time-info { flex-grow: 1; margin-left: 25px; }
        .time-info strong { display: block; font-size: 1.15rem; color: #fff; margin-bottom: 4px; }
        .time-info span { color: #aaa; font-size: 0.95rem; }

        .btn-cancel {
            background: transparent; color: #FF6B6B; border: 1px solid #FF6B6B;
            padding: 8px 14px; border-radius: 4px; font-size: 0.85rem; cursor: pointer;
            transition: all 0.3s;
        }
        .btn-cancel:hover { background: #FF6B6B; color: white; }
        
        .status-badge { font-weight: bold; font-size: 0.85rem; padding: 5px 10px; border-radius: 4px; text-transform: uppercase; }
        .status-cancelled { color: #FF6B6B; border: 1px solid #FF6B6B; }
        .status-completed { color: #0077FF; border: 1px solid #0077FF; }

        .links { margin-top: 40px; display: flex; gap: 25px; align-items: center; padding-top: 25px; border-top: 1px solid #333; }
        .links a { text-decoration: none; font-weight: 600; font-size: 1rem; }

        @media (max-width: 900px) {
            .dashboard-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <?php include '../includes/user_widget.php'; ?>
    <?php if (isset($_GET['status'])): ?>
    <script>
        // 1. Capturamos los datos de la URL
        const status = "<?php echo $_GET['status']; ?>";
        const msg = "<?php echo $_GET['msg'] ?? ''; ?>";
        
        // 2. Mostramos la alerta correspondiente
        if (status === 'success_appointment') {
            Swal.fire({ title: '¡Reserva Confirmada!', text: 'Tu cita ha sido agendada con éxito.', icon: 'success', confirmButtonColor: '#9F40FF', background: '#14141d', color: '#fff' });
        } else if (status === 'cancelled') {
            Swal.fire({ title: 'Cita Cancelada', text: 'La sesión ha sido anulada.', icon: 'info', confirmButtonColor: '#0077FF', background: '#14141d', color: '#fff' });
        } else if (status === 'error') {
            let text = 'Ha ocurrido un error inesperado.';
            if (msg === 'limit_reached') text = 'No puedes tener más de 4 citas activas.';
            Swal.fire({ title: 'Error', text: text, icon: 'error', confirmButtonColor: '#FF6B6B', background: '#14141d', color: '#fff' });
        }

        // 3. LA CLAVE: Eliminamos los parámetros de la URL inmediatamente
        // Esto hace que la URL pase de "...?status=success" a simplemente "dashboard_normal.php"
        // Así, al pulsar F5, el parámetro ya no existe y la alerta no sale.
        window.history.replaceState({}, document.title, window.location.pathname);
    </script>
    <?php endif; ?>
    <div class="dashboard-container">
        <div class="welcome-header">
            <div>
                <h1 class="welcome-message">Hola, <?php echo $user_name; ?></h1>
                <p style="color: #aaa; margin: 5px 0 0 0;">Selecciona un día para ver las horas disponibles.</p>
            </div>
            <span class="role-badge"><?php echo $user_role; ?></span>
        </div>

        <div class="dashboard-grid">
            <!-- COLUMNA IZQUIERDA: AGENDAR -->
            <div class="dashboard-card">
                <div class="booking-widget">
                    <div class="calendar-column">
                        <div class="calendar-header">
                            <button type="button" id="prev-month" onclick="changeMonth(-1)">&#8592;</button>
                            <span id="current-month-display">Mes Año</span>
                            <button type="button" id="next-month" onclick="changeMonth(1)">&#8594;</button>
                        </div>
                        <div class="calendar-weekdays">
                            <div>L</div><div>M</div><div>X</div><div>J</div><div>V</div><div>S</div><div>D</div>
                        </div>
                        <div class="calendar-days" id="calendar-days">
                            </div>
                    </div>

                    <div class="time-column" id="time-column">
                        <h4 id="selected-date-title">Horas disponibles</h4>
                        <div class="time-slots" id="time-slots">
                            </div>
                        <button type="button" id="confirm-booking-btn" class="confirm-btn-premium" style="display: none;" onclick="submitBooking()">
                            <span>Confirmar Reserva</span>
                        </button>
                    </div>
                </div>

                <form id="real-booking-form" action="appointment_handler.php" method="POST" style="display: none;">
                    <input type="hidden" name="date" id="hidden-date" required>
                    <input type="hidden" name="time" id="hidden-time" required>
                </form>
            </div>

            <!-- COLUMNA DERECHA: LISTADO -->
            <div class="appointments-list">
                <h3 style="margin-top:0; color:#fff;">📂 Mis Sesiones</h3>
                
                <?php if ($mis_citas && $mis_citas->num_rows > 0): ?>
                    <?php while($cita = $mis_citas->fetch_assoc()): 
                        $fecha = strtotime($cita['date']);
                        $hora = strtotime($cita['time']);
                        $status = $cita['status'];
                        
                        // Determinar clase CSS para la fila
                        $rowClass = '';
                        if ($status === 'cancelled') $rowClass = 'cancelled';
                        if ($status === 'completed') $rowClass = 'completed';
                    ?>
                    <div class="appointment-item <?php echo $rowClass; ?>">
                        <div class="date-box">
                            <span class="date-day"><?php echo date('d', $fecha); ?></span>
                            <span class="date-month"><?php echo date('M', $fecha); ?></span>
                        </div>
                        <div class="time-info">
                            <strong>Consultoría IA</strong>
                            <span>⏰ <?php echo date('H:i', $hora); ?> - 60 min</span>
                            <?php if($status === 'cancelled'): ?>
                                <br><small style="color: #FF6B6B;">Motivo: <?php echo htmlspecialchars($cita['cancellation_reason']); ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="actions">
                            <?php if($status === 'confirmed'): ?>
                                <button class="btn-cancel" onclick="confirmCancel(<?php echo $cita['id']; ?>)">Cancelar</button>
                            <?php elseif($status === 'cancelled'): ?>
                                <span class="status-badge status-cancelled">CANCELADA</span>
                            <?php elseif($status === 'completed'): ?>
                                <span class="status-badge status-completed">✅ FINALIZADA</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 50px; color: #444;">
                        <span style="font-size: 3.5rem; display: block; margin-bottom: 15px;">📅</span>
                        No tienes citas programadas todavía.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="links">
            <a href="<?php echo $role_dashboard_link; ?>" style="color: #0077FF;"><?php echo $role_dashboard_text; ?></a>
            <a href="../logout.php" style="color: #FF6B6B; font-size: 0.95rem; opacity: 0.8;">Cerrar Sesión</a>
        </div>
    </div>
    <?php
        // Buscamos todas las citas confirmadas de hoy en adelante
        $booked_slots = [];
        $sql_booked = "SELECT date, time FROM appointments WHERE status = 'confirmed' AND date >= CURDATE()";
        $res_booked = $conn->query($sql_booked);
        if ($res_booked && $res_booked->num_rows > 0) {
            while($row = $res_booked->fetch_assoc()) {
                $d = $row['date'];
                $t = substr($row['time'], 0, 5); // Nos quedamos con "HH:MM"
                if(!isset($booked_slots[$d])) {
                    $booked_slots[$d] = [];
                }
                $booked_slots[$d][] = $t;
            }
        }
        ?>
        <script>
            // Guardamos las citas en una variable global para que agenda.js pueda leerla
            const bookedSlotsDB = <?php echo json_encode($booked_slots); ?>;
        </script>
        <script src="../assets/js/agenda.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </body>
    </html>