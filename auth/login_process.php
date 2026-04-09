<?php
// auth/login_process.php
session_start();

require_once '../includes/db_config.php';
require_once '../includes/funciones.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT id, name, password_hash, role FROM users WHERE email = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $email);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            // Cerramos el statement aquí, ya que tenemos los datos en $result
            $stmt->close(); 

            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                
                if (password_verify($password, $user['password_hash'])) {
                    $_SESSION['loggedin'] = true;
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_role'] = $user['role'];
                    
                    if ($_SESSION['user_role'] === 'admin') {
                        header("location: ../admin/admin_dashboard.php");
                    } else {
                        header("location: ../dashboard/dashboard_normal.php"); 
                    }
                    exit;

                } else {
                    display_error("La contraseña no es válida.", false, "../login.html");
                }
            } else {
                display_error("No se encontró una cuenta con ese email.", false, "../login.html");
            }
        } else {
            // Si el execute falla, cerramos y mostramos error
            $stmt->close();
            display_error("Ocurrió un error en la consulta al sistema.", false, "../login.html");
        }
    }
}

$conn->close();
header("location: ../login.html");
exit;
?>