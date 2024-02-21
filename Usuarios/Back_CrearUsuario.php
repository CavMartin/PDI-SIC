<?php
// Conectar a la base de datos de forma segura
require '../PHP/ServerConnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm_password"];
    $rol = $_POST["Rol_del_usuario"];
    $apellido = $_POST["Apellido_Operador"];
    $nombre = $_POST["Nombre_Operador"];
    $ni = $_POST["NI_Operador"];

    // Verificar si las contraseñas coinciden
    if ($password !== $confirmPassword) {
        echo json_encode(['success' => false, 'message' => 'Las contraseñas no coinciden']);
        exit;
    }
        // Verificar si el nombre de usuario ya existe en la base de datos
        $conn = open_database_connection();
        if ($conn->connect_error) {
            error_log("Error de conexión: " . $conn->connect_error); // Loguear el error real
            die("Hubo un problema al conectar con la base de datos. Por favor intenta más tarde."); // Mostrar mensaje amigable
        }
        
        $stmt = $conn->prepare("SELECT Usuario FROM sistema_usuarios WHERE Usuario = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
    
        if ($stmt->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'El nombre de usuario ya está en uso. Inténtalo con otro nombre.']);
            $stmt->close();
            $conn->close();
            exit; // Asegúrate de salir del script aquí
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insertStmt = $conn->prepare("INSERT INTO sistema_usuarios (Usuario, Contraseña, Rol_del_usuario, Apellido_Operador, Nombre_Operador, NI_Operador) VALUES (?, ?, ?, ?, ?, ?)");
            $insertStmt->bind_param("ssissi", $username, $hashedPassword, $rol, $apellido, $nombre, $ni);
    
            if ($insertStmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Usuario registrado exitosamente']);
            } else {
                error_log("Error al ejecutar la consulta: " . $insertStmt->error); // Loguear el error
                echo json_encode(['success' => false, 'message' => 'Error al registrar el usuario. Inténtalo de nuevo.']);
            }
        }

        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    }
?>
