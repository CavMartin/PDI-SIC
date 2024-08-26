<?php
class UserAuthentication {
    private $conn;
    private $maxAttempts = 3;
    private $lockoutTime = 60; // En segundos

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function login($username, $password) {
        if ($this->isLockedOut()) {
            $remainingTime = $this->remainingLockoutTime();
            return ['success' => false, 'blocked' => true, 'remainingTime' => $remainingTime];
        }

        $stmt = $this->conn->prepare("SELECT ID, Contraseña, Rol, Estado, Grupo FROM sistema_usuarios WHERE Usuario = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['Contraseña'])) {
                if ($row['Estado'] === '0') {
                    return ['success' => false, 'message' => 'Cuenta deshabilitada. Contacte al administrador.'];
                } else {
                    $this->clearFailedAttempts();
                    session_regenerate_id();
                    $_SESSION['loggedin'] = true;
                    $_SESSION['username'] = $username;
                    $_SESSION['userrole'] = $row['Rol'];
                    $_SESSION['usernameID'] = $row['ID'];
                    $_SESSION['usergroup'] = $row['Grupo'];
                    return ['success' => true];
                }
            } else {
                $this->recordFailedAttempt();
                return ['success' => false, 'message' => 'Usuario o contraseña incorrectos.'];
            }
        } else {
            $this->recordFailedAttempt();
            return ['success' => false, 'message' => 'Usuario o contraseña incorrectos.'];
        }
    }

    public function logout() {
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

        // Enviar una respuesta JSON indicando el éxito
        echo json_encode(['success' => true]);
        exit();
    }

    private function isLockedOut() {
        if (!isset($_SESSION['intentos_fallidos'])) {
            $_SESSION['intentos_fallidos'] = 0;
            $_SESSION['ultimo_intento'] = time();
        }
        return ($_SESSION['intentos_fallidos'] >= $this->maxAttempts) &&
               ((time() - $_SESSION['ultimo_intento']) < $this->lockoutTime);
    }

    private function remainingLockoutTime() {
        return $this->lockoutTime - (time() - $_SESSION['ultimo_intento']);
    }

    private function recordFailedAttempt() {
        $_SESSION['intentos_fallidos']++;
        $_SESSION['ultimo_intento'] = time();
    }

    private function clearFailedAttempts() {
        $_SESSION['intentos_fallidos'] = 0;
        $_SESSION['ultimo_intento'] = time();
    }
}
?>
