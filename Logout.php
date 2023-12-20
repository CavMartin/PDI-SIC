<?php
session_start(); // Iniciar o reanudar la sesión actual

// Verifica si la solicitud es POST y si se envió el formulario de cierre de sesión
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['Logout'])) {
    // Limpiar datos de sesión
    $_SESSION = array();

    // Eliminar la cookie de sesión si existe
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Destruir la sesión
    session_destroy();

    // Redirigir al usuario a la página de inicio de sesión
    header("Location: Login.php");
    exit();
} else {
    // Redirigir a Main.php si el acceso al script no es apropiado
    header("Location: Main.php");
    exit();
}
?>
