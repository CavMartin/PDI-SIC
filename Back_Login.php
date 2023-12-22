<?php
require 'ServerConnect.php';

header('Content-Type: application/json');

// Verifica si el usuario ya ha iniciado sesión
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    echo json_encode(['success' => true, 'redirect' => 'Main.php']);
    exit;
}

// Definir límites de intentos de inicio de sesión
$max_intentos = 3;
$tiempo_bloqueo = 60; // segundos

// Inicializar o actualizar el contador de intentos fallidos
if (!isset($_SESSION['intentos_fallidos'])) {
    $_SESSION['intentos_fallidos'] = 0;
    $_SESSION['ultimo_intento'] = time();
}

// Verificar si se ha alcanzado el límite de intentos
if ($_SESSION['intentos_fallidos'] >= $max_intentos) {
    $tiempo_restante = ($_SESSION['ultimo_intento'] + $tiempo_bloqueo) - time();
    if ($tiempo_restante > 0) {
        echo json_encode(['success' => false, 'bloqueado' => true, 'tiempo_restante' => $tiempo_restante]);
        exit;
    } else {
        $_SESSION['intentos_fallidos'] = 0;
    }
}

// Verifica si se han enviado los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars(strip_tags(trim($_POST["username"])));
    $user_password = htmlspecialchars(strip_tags(trim($_POST["password"])));

    $conn = open_database_connection();
    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos.']);
        exit;
    }

    $stmt = $conn->prepare("SELECT ID, Contraseña, Rol_del_usuario, Estado, Region FROM sistema_usuarios WHERE Usuario = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $stored_password = $row['Contraseña'];
        $userEstado = $row['Estado'];

        if (password_verify($user_password, $stored_password)) {
            if ($userEstado === 0) {
                echo json_encode(['success' => false, 'message' => 'Cuenta deshabilitada. Si cree que se trata de un error, contacte al administrador.']);
            } else {
                session_regenerate_id();
                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $username;
                $_SESSION['rolUsuario'] = $row['Rol_del_usuario'];
                $_SESSION['usernameID'] = $row['ID'];
                $_SESSION['Region'] = $row['Region'];

                echo json_encode(['success' => true]);
            }
        } else {
            $_SESSION['intentos_fallidos']++;
            $_SESSION['ultimo_intento'] = time();
            echo json_encode(['success' => false, 'message' => 'Nombre de usuario o contraseña incorrectos.']);
        }
    } else {
        $_SESSION['intentos_fallidos']++;
        $_SESSION['ultimo_intento'] = time();
        echo json_encode(['success' => false, 'message' => 'Nombre de usuario o contraseña incorrectos.']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
?>
