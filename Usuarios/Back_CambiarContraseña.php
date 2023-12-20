<?php
// Conectar a la base de datos de forma segura
require '../ServerConnect.php';

// Obtener la información del usuario
$username = $_SESSION['username'];

// Función para verificar la contraseña actual
function isCurrentPasswordValid($username, $current_password) {
    $conn = open_database_connection();
    $stmt = $conn->prepare("SELECT Contraseña FROM sistema_usuarios WHERE Usuario = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashedPassword);
        $stmt->fetch();
        $stmt->close();
        $isPasswordValid = password_verify($current_password, $hashedPassword);
        return ['isPasswordValid' => $isPasswordValid, 'hashedPassword' => $hashedPassword];
    }
    $stmt->close();
    return ['isPasswordValid' => false, 'hashedPassword' => null];
}

// Función para actualizar la contraseña
function updatePassword($username, $new_password) {
    $conn = open_database_connection();
    $newHashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
    $updateStmt = $conn->prepare("UPDATE sistema_usuarios SET Contraseña = ? WHERE Usuario = ?");
    $updateStmt->bind_param("ss", $newHashedPassword, $username);
    $result = $updateStmt->execute();
    $updateStmt->close();
    return $result;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['success' => false, 'message' => 'CSRF token validation failed']);
        exit();
    } else {
        // Usando trim() para limpiar los campos de entrada
        $current_password = trim($_POST["current_password"] ?? '');
        $password = trim($_POST["password"] ?? '');
        $confirmPassword = trim($_POST["confirm_password"] ?? '');

        $passwordCheck = isCurrentPasswordValid($username, $current_password);

        if ($password === $confirmPassword) {
            if ($passwordCheck['isPasswordValid']) {
                if ($current_password !== $password) {
                    if (updatePassword($username, $password)) {
                        echo json_encode(['success' => true, 'message' => 'Contraseña cambiada con éxito.']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Error al cambiar la contraseña. Intente nuevamente.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'La nueva contraseña debe ser diferente de la contraseña actual.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'La contraseña actual es incorrecta. Intente nuevamente.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Las contraseñas no coinciden. Intente nuevamente.']);
        }
        exit();
    }
}
?>