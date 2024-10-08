<?php
// Tus configuraciones existentes
$SesionTimer = 36000; // Duración de la cookie en segundos (10 horas)
ini_set('session.gc_maxlifetime', $SesionTimer);
session_set_cookie_params($SesionTimer);

// Inicia la sesión
session_start();

// Actualizar el tiempo de actividad de la sesión
$_SESSION['last_activity'] = time();

// Extender la duración de la cookie de sesión con cada interacción del usuario
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), $_COOKIE[session_name()], time() + 36000, "/");
}

// Generar token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Función para conectar a la base de datos de forma segura
function open_database_connection($db) {
    $host = 'localhost';
    $user = 'root';
    $password = '';

    $conn = new mysqli($host, $user, $password, $db);

    // Verifica si hay un error al conectar
    if ($conn->connect_error) {
        die('Error al conectar a la base de datos: ' . $conn->connect_error);
    }

    // Establece la codificación de caracteres a UTF-8
    $conn->set_charset("utf8");

    return $conn;
}

// Función para verificar el estado del login
function checkLoginState() {
    // Verifica si el usuario ha iniciado sesión
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        // El usuario no ha iniciado sesión, redirigirlo a la página de inicio de sesión
        header("Location: http://10.100.32.86/siacip/Login.php");
        exit();
    }

    // Verifica si el rol de usuario almacenado en la sesión es igual a 1
    if (isset($_SESSION['rolUsuario']) && $_SESSION['rolUsuario'] === 1) {
        // Si el rol es igual a 1, habilita los ajustes del INI para mostrar errores en pantalla
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }
}

?>
