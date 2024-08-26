<?php
// Esta clase es el punto de entrada para todas las solicitudes AJAX relacionadas al login y al control de usuarios
require '../../PHP/ServerConnect.php';
require 'UserAuthentication.php';
require 'UserManagement.php';

class UserEndPoint { 
    private $conn;
    private $userAuthentication;
    private $userManagement;

    public function __construct() {
        $this->conn = open_database_connection('sistema_usuarios');
        $this->userAuthentication = new UserAuthentication($this->conn);
        $this->userManagement = new UserManagement($this->conn);
    }

    public function handleRequest() {
        header('Content-Type: application/json');
        if (!isset($_REQUEST['action'])) {
            echo json_encode(['status' => 'error', 'message' => 'Sin acción especificada']);
            return;
        }

        $action = $_REQUEST['action'];
    
        switch ($action) {
            case 'login': // Intento de logeo a la aplicación
                $this->login();
                break;
            case 'logout': // Deslogeo a la aplicación
                $this->logout();
                break;
            case 'changePassword': // Cambio de contraseña
                $this->changePassword();
                break;
            case 'createUser': // Creación de un nuevo usuario
                $this->createUser();
                break;
            default:
                echo json_encode(['status' => 'error', 'message' => 'Acción especificada desconocida']);
        }
    }

    private function login() {
        $username = $_POST['username'] ?? null;
        $password = $_POST['password'] ?? null;
    
        // Asegurarse de que $username y $password no sean null
        if ($username !== null && $password !== null) {
            $response = $this->userAuthentication->login($username, $password);
    
            if ($response['success']) {
                echo json_encode(['success' => true]);
            } else {
                // Si hay un tiempo de espera restante, envíalo en la respuesta.
                if (isset($response['blocked']) && $response['blocked']) {
                    echo json_encode(['blocked' => true, 'remainingTime' => $response['remainingTime']]);
                } else {
                    echo json_encode(['success' => false, 'message' => $response['message']]);
                }
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Usuario y/o contraseña no especificados']);
        }
    }

    private function logout() {
        $this->userAuthentication->logout();
    }

    private function changePassword() {
        // Validar token CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            echo json_encode(['success' => false, 'message' => 'CSRF token validation failed']);
            return;
        }

        $username = $_SESSION['username'];
        $current_password = trim($_POST["current_password"] ?? '');
        $new_password = trim($_POST["password"] ?? '');
        $confirmPassword = trim($_POST["confirm_password"] ?? '');

        $result = $this->userManagement->changePassword($username, $current_password, $new_password, $confirmPassword);
        echo json_encode($result);
    }

    private function createUser() {
        $username = $_POST["username"] ?? null;
        $password = $_POST["password"] ?? null;
        $confirmPassword = $_POST["confirm_password"] ?? null;
        $rol = $_POST["Rol"] ?? null;
        $grupo = $_POST["Grupo"] ?? null;
        $apellido = $_POST["Apellido_Operador"] ?? null;
        $nombre = $_POST["Nombre_Operador"] ?? null;
        $ni = $_POST["NI_Operador"] ?? null;

        // Asegurarse de que todos los campos requeridos no sean null
        if ($username !== null && $password !== null && $confirmPassword !== null && $rol !== null && $grupo !== null && $apellido !== null && $nombre !== null && $ni !== null) {
            $result = $this->userManagement->createUser($username, $password, $confirmPassword, $rol, $grupo, $apellido, $nombre, $ni);
            echo json_encode($result);
        } else {
            echo json_encode(['success' => false, 'message' => 'Faltan campos requeridos']);
        }
    }
}

$ajaxEndpoint = new UserEndPoint();
$ajaxEndpoint->handleRequest();
?>