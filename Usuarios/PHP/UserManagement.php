<?php
class UserManagement {
    private $conn;

    public function __construct() {
        $this->conn = open_database_connection('sistema_usuarios');
    }

    private function isCurrentPasswordValid($username, $current_password) {
        $stmt = $this->conn->prepare("SELECT Contraseña FROM sistema_usuarios WHERE Usuario = ?");
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

    private function updatePassword($username, $new_password) {
        $newHashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
        $updateStmt = $this->conn->prepare("UPDATE sistema_usuarios SET Contraseña = ? WHERE Usuario = ?");
        $updateStmt->bind_param("ss", $newHashedPassword, $username);
        $result = $updateStmt->execute();
        $updateStmt->close();
        return $result;
    }

    public function changePassword($username, $current_password, $new_password, $confirmPassword) {
        if ($new_password !== $confirmPassword) {
            return ['success' => false, 'message' => 'Las contraseñas no coinciden. Intente nuevamente.'];
        }

        $passwordCheck = $this->isCurrentPasswordValid($username, $current_password);

        if ($passwordCheck['isPasswordValid']) {
            if ($current_password !== $new_password) {
                if ($this->updatePassword($username, $new_password)) {
                    return ['success' => true, 'message' => 'Contraseña cambiada con éxito.'];
                } else {
                    return ['success' => false, 'message' => 'Error al cambiar la contraseña. Intente nuevamente.'];
                }
            } else {
                return ['success' => false, 'message' => 'La nueva contraseña debe ser diferente de la contraseña actual.'];
            }
        } else {
            return ['success' => false, 'message' => 'La contraseña actual es incorrecta. Intente nuevamente.'];
        }
    }

    public function createUser($username, $password, $confirmPassword, $rol, $grupo, $apellido, $nombre, $ni) {
        if ($password !== $confirmPassword) {
            return ['success' => false, 'message' => 'Las contraseñas no coinciden'];
        }

        $stmt = $this->conn->prepare("SELECT Usuario FROM sistema_usuarios WHERE Usuario = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->close();
            return ['success' => false, 'message' => 'El nombre de usuario ya está en uso. Inténtalo con otro nombre.'];
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insertStmt = $this->conn->prepare("INSERT INTO sistema_usuarios (Usuario, Contraseña, Rol, Grupo, Apellido_Operador, Nombre_Operador, NI_Operador) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $insertStmt->bind_param("ssisssi", $username, $hashedPassword, $rol, $grupo, $apellido, $nombre, $ni);

            if ($insertStmt->execute()) {
                $insertStmt->close();
                return ['success' => true, 'message' => 'Usuario registrado exitosamente'];
            } else {
                error_log("Error al ejecutar la consulta: " . $insertStmt->error); // Loguear el error
                $insertStmt->close();
                return ['success' => false, 'message' => 'Error al registrar el usuario. Inténtalo de nuevo.'];
            }
        }

        $stmt->close();
    }
}
?>