<?php
// Tus configuraciones existentes
$SesionTimer = 3600; // Duración de la cookie en segundos (10 horas)
ini_set('session.gc_maxlifetime', $SesionTimer);
session_set_cookie_params($SesionTimer);

// Inicia la sesión
session_start();

// Actualizar el tiempo de actividad de la sesión
$_SESSION['last_activity'] = time();

// Extender la duración de la cookie de sesión con cada interacción del usuario
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), $_COOKIE[session_name()], time() + 3600, "/");
}

// Generar token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Verifica si el rol de usuario almacenado en la sesión es igual a 1
if (isset($_SESSION['rolUsuario']) && $_SESSION['rolUsuario'] === 1) {
    // Si el rol es igual a 1, habilita los ajustes del INI para mostrar errores en pantalla
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// Función para conectar a la base de datos de forma segura
function open_database_connection() {
    $host = 'localhost';
    $db = 'sic';
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
?>
