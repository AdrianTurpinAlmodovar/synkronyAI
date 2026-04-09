<?php
// admin/api_stats.php
header('Content-Type: application/json');

require_once '../includes/db_config.php';
require_once '../includes/funciones.php';

session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso denegado']);
    exit;
}

$response = [];

try {
    // 1. KPI: Usuarios Totales
    $res = $conn->query("SELECT COUNT(*) FROM users");
    $response['total_users'] = $res ? $res->fetch_row()[0] : 0;

    // 2. KPI: Citas Totales (Histórico)
    $res = $conn->query("SELECT COUNT(*) FROM appointments");
    $response['total_appointments'] = $res ? $res->fetch_row()[0] : 0;

    // 3. KPI: PRÓXIMAS CITAS (FUTURAS) -- CAMBIO IMPORTANTE --
    // Contamos citas 'confirmed' cuya fecha+hora sea mayor o igual a AHORA
    $sql_upcoming = "SELECT COUNT(*) FROM appointments WHERE status = 'confirmed' AND CONCAT(date, ' ', time) >= NOW()";
    $res = $conn->query($sql_upcoming);
    $response['upcoming_appointments'] = $res ? $res->fetch_row()[0] : 0;

    // 4. GRÁFICO: Citas por Estado
    $sql = "SELECT status, COUNT(*) as count FROM appointments GROUP BY status";
    $result = $conn->query($sql);
    
    $appt_labels = []; $appt_data = []; $appt_colors = [];
    $color_map = ['confirmed' => '#00ff99', 'cancelled' => '#FF6B6B', 'completed' => '#0077FF', 'pending' => '#FFD700'];

    while ($row = $result->fetch_assoc()) {
        $st = $row['status'];
        // Traducción visual
        $label = ($st == 'confirmed') ? 'Confirmadas' : (($st == 'cancelled') ? 'Canceladas' : (($st == 'completed') ? 'Finalizadas' : $st));
        $appt_labels[] = $label;
        $appt_data[] = $row['count'];
        $appt_colors[] = $color_map[$st] ?? '#9F40FF';
    }

    $response['chart_appointments'] = [
        'labels' => $appt_labels,
        'data' => $appt_data,
        'colors' => $appt_colors
    ];

    // 5. GRÁFICO: Crecimiento Usuarios
    $dates = []; $counts = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $dates[] = date('d/m', strtotime($date));
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE DATE(created_at) = ?");
        $stmt->bind_param("s", $date);
        $stmt->execute();
        $counts[] = $stmt->get_result()->fetch_row()[0];
        $stmt->close();
    }

    $response['chart_users'] = ['labels' => $dates, 'data' => $counts];

} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
$conn->close();
?>