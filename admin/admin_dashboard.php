<?php
// admin/admin_dashboard.php
require_once '../includes/funciones.php';
check_admin_access();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SynkronyAI</title>
    <link rel="stylesheet" href="../assets/css/admin-styles.css"> 
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .analytics-container { margin-bottom: 40px; }
        .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .kpi-card { background: #1c1c2b; padding: 25px; border-radius: 12px; border: 1px solid #333; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.3); transition: transform 0.2s; }
        .kpi-card:hover { transform: translateY(-3px); border-color: #9F40FF; }
        .kpi-title { color: #aaa; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; }
        .kpi-value { font-size: 2.5rem; font-weight: bold; color: #fff; line-height: 1.2; }
        .kpi-trend { font-size: 0.8rem; margin-top: 8px; }
        
        .charts-wrapper { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; }
        .chart-box { background: #1c1c2b; padding: 20px; border-radius: 12px; border: 1px solid #333; position: relative; }
        .chart-header { margin-top: 0; color: #fff; font-size: 1.1rem; margin-bottom: 15px; border-bottom: 1px solid #333; padding-bottom: 10px; }
        .canvas-container { position: relative; height: 300px; width: 100%; }
        .canvas-container-donut { position: relative; height: 250px; width: 100%; display: flex; justify-content: center; }
        @media (max-width: 900px) { .charts-wrapper { grid-template-columns: 1fr; } .canvas-container { height: 250px; } }
    </style>
</head>
<body>
    <header class="admin-header">
        <div>
            <h1>SynkronyAI <span style="color: #9F40FF;">Analytics</span></h1>
            <p>Conectado como: <strong><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Administrador'); ?></strong></p>
        </div>
        <nav>
            <a href="/" style="color: #e4d50d; margin-right: 20px; text-decoration: none; font-weight: bold;">🏠 Volver al Home</a>
            <a href="../dashboard/dashboard_normal.php" style="color: #0077FF; margin-right: 20px; text-decoration: none; font-weight: bold;">👁️ Vista Cliente</a>
            <a href="../logout.php" style="color: #FF6B6B; text-decoration: none; font-weight: bold;">Cerrar Sesión</a>
        </nav>
    </header>

    <main style="padding: 20px; max-width: 1200px; margin: 0 auto;">
        
        <section class="analytics-container">
            <!-- KPIs -->
            <div class="kpi-grid">
                <div class="kpi-card">
                    <div class="kpi-title">Usuarios Totales</div>
                    <div class="kpi-value" id="val-users">-</div>
                    <div class="kpi-trend" style="color: #00ff99;">Activos en plataforma</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-title">Citas Totales</div>
                    <div class="kpi-value" id="val-appts">-</div>
                    <div class="kpi-trend" style="color: #0077FF;">Histórico de reservas</div>
                </div>
                <!-- TARJETA ACTUALIZADA -->
                <div class="kpi-card">
                    <div class="kpi-title">Próximas Citas</div>
                    <div class="kpi-value" id="val-upcoming" style="color: #FFD700;">-</div>
                    <div class="kpi-trend" style="color: #aaa;">Pendientes de atender</div>
                </div>
            </div>

            <!-- Gráficos -->
            <div class="charts-wrapper">
                <div class="chart-box">
                    <h3 class="chart-header">📈 Nuevos Usuarios (Últimos 7 días)</h3>
                    <div class="canvas-container">
                        <canvas id="chartUsers"></canvas>
                    </div>
                </div>
                
                <div class="chart-box">
                    <h3 class="chart-header">📊 Estado de Citas</h3>
                    <div class="canvas-container-donut">
                        <canvas id="chartStatus"></canvas>
                    </div>
                </div>
            </div>
        </section>

        <h2 style="color: #fff; margin-bottom: 20px; border-bottom: 1px solid #333; padding-bottom: 10px;">Panel de Control</h2>
        
        <div class="admin-grid">
            <!-- Nota: Puedes crear un admin_appointments.php en el futuro para ver el calendario completo -->
            <div class="admin-card">
                <div>
                    <h3>📅 Citas y Agendamientos</h3>
                    <p>Gestiona las citas y horarios reservados.</p>
                </div>
                <a href="admin_dashboard_requests.php" class="btn-admin">Gestionar Citas</a>
            </div>
            <div class="admin-card">
                <div>
                    <h3>🛠️ Servicios y Home</h3>
                    <p>Edita el contenido público de la web.</p>
                </div>
                <a href="admin_services.php" class="btn-admin">Editar Servicios</a>
            </div>

            <div class="admin-card">
                <div>
                    <h3>👥 Usuarios del Sistema</h3>
                    <p>Administra cuentas y roles.</p>
                </div>
                <a href="admin_users.php" class="btn-admin">Administrar Usuarios</a>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('api_stats.php')
                .then(response => response.json())
                .then(data => {
                    if(data.error) { console.error(data.error); return; }

                    // Animación KPIs - AHORA USA upcoming_appointments
                    animateValue("val-users", 0, data.total_users, 1000);
                    animateValue("val-appts", 0, data.total_appointments, 1000);
                    animateValue("val-upcoming", 0, data.upcoming_appointments, 1000);

                    // Gráfico Usuarios
                    const ctxUsers = document.getElementById('chartUsers').getContext('2d');
                    new Chart(ctxUsers, {
                        type: 'line',
                        data: {
                            labels: data.chart_users.labels,
                            datasets: [{
                                label: 'Registros',
                                data: data.chart_users.data,
                                borderColor: '#9F40FF',
                                backgroundColor: 'rgba(159, 64, 255, 0.15)',
                                borderWidth: 2,
                                tension: 0.3, 
                                fill: true,
                                pointBackgroundColor: '#fff',
                                pointRadius: 3
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                y: { beginAtZero: true, ticks: { color: '#888', stepSize: 1 }, grid: { color: '#333' } },
                                x: { ticks: { color: '#888' }, grid: { display: false } }
                            }
                        }
                    });

                    // Gráfico Estado Citas
                    if (data.chart_appointments) {
                        const ctxStatus = document.getElementById('chartStatus').getContext('2d');
                        new Chart(ctxStatus, {
                            type: 'doughnut',
                            data: {
                                labels: data.chart_appointments.labels,
                                datasets: [{
                                    data: data.chart_appointments.data,
                                    backgroundColor: data.chart_appointments.colors,
                                    borderWidth: 0,
                                    hoverOffset: 8
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { position: 'right', labels: { color: '#fff', boxWidth: 12, font: { size: 11 } } }
                                },
                                cutout: '75%'
                            }
                        });
                    }
                })
                .catch(err => console.error("Error:", err));
        });

        function animateValue(id, start, end, duration) {
            if(end === 0) { document.getElementById(id).innerText = "0"; return; }
            const obj = document.getElementById(id);
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                obj.innerHTML = Math.floor(progress * (end - start) + start);
                if (progress < 1) window.requestAnimationFrame(step);
            };
            window.requestAnimationFrame(step);
        }
    </script>
</body>
</html>