<?php
// Conectar a la base de datos de forma segura
require '../PHP/ServerConnect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // El usuario no ha iniciado sesión, redirigirlo a la página de inicio de sesión
    header("Location: ../Login.php");
    exit();
}

// Obtener el nombre de usuario del usuario logeado desde la sesión
$username = $_SESSION['username'];

// Establece la conexión a la base de datos
$conn = open_database_connection();
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
// Consulta para obtener los datos del usuario logeado
$stmt = $conn->prepare("SELECT ID, Usuario, Rol_del_usuario, Apellido_Operador, Nombre_Operador, NI_Operador FROM sistema_usuarios WHERE Usuario = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user_data = $result->fetch_assoc();
    $rolUsuarioID = $user_data['Rol_del_usuario'];

switch ($rolUsuarioID) {
    case 1:
        $rolDelUsuario = "ADMINISTRADOR DEL SISTEMA";
        break;
    case 2:
        $rolDelUsuario = "SUPERVISOR";
        break;
    case 3:
        $rolDelUsuario = "ANALISTA";
        break;
    case 4:
        $rolDelUsuario = "INVITADO";
        break;
    default:
        $rolDelUsuario = "DESCONOCIDO";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de usuario</title>
    <!-- Favicon -->
    <link rel="icon" href="../CSS/Images/favicon.ico" type="Image/x-icon">
    <link rel="shortcut icon" href="../CSS/Images/favicon.ico" type="Image/x-icon">
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Popper.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.10.2/umd/popper.min.js"></script>
    <!-- Bootstrap -->
    <script src="../Bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../Bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../Bootstrap/Icons/font/bootstrap-icons.css">
</head>
<body class="bg-secondary">

<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark" style="height: 5vw;">
    <div class="container-fluid d-flex align-items-center justify-content-center">
        <!-- Imagen 1 -->
        <div>
            <img src="../CSS/Images/PSF.png" alt="Icono" style="width: 4vw; margin: 1vw;"><!-- Icono -->
        </div>

        <!-- Título centrado -->
        <div class="text-center">
            <h1 class="text-light">PANEL DE USUARIO</h1>
        </div>

        <!-- Imagen 2 -->
        <div>
            <img src="../CSS/Images/OJO.png" alt="Icono" style="width: 4vw; margin: 1vw;"><!-- Icono -->
        </div>

        <!-- Botón de navegación -->
        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <button type="button" class="btn btn-primary btn-lg" style="position: fixed; top: 0; left: 0; width: 12vw; height: 4vw; font-size: 1.5vw; margin: 0.5vw;" onclick="window.location.href='../Main.php'">
                <i class="bi bi-arrow-left-square-fill"></i> <b>VOLVER</b>
            </button>
        </div>

        <!-- Menú desplegable -->
        <div class="collapse navbar-collapse" id="navbarNavDarkDropdown" style="position: fixed; top: 1vw; right:10vw; width: 5vw;">
          <ul class="navbar-nav">
            <li class="nav-item dropdown">
              <button class="btn btn-dark btn-lg dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                MENÚ DESPLEGABLE
              </button>
              <ul class="dropdown-menu dropdown-menu-dark">
                <li><a class="dropdown-item fs-4" href="CambiarContraseña.php">Cambiar contraseña</a></li>
                <?php
                    // Verifica si el rol del usuario es "1" o "2" y muestra el botón "CrearUsuario" en consecuencia
                    if ($rolUsuarioID === 1 || $rolUsuarioID === 2) {
                        echo '<li><a class="dropdown-item fs-4" href=\'CrearUsuario.php\'">Crear usuario</a></li>';
                    }
                ?>
                <?php
                    // Verifica si el rol del usuario es "1" o "2" y muestra el botón "ListarUsuarios" en consecuencia
                    if ($rolUsuarioID === 1 || $rolUsuarioID === 2) {
                        echo '<li><a class="dropdown-item fs-4" href=\'TablaDeUsuarios.php\'">Tabla de usuarios</a></li>';
                    }
                ?>
              </ul>
            </li>
          </ul>
        </div>
    </div>
</nav>

<div class="container mt-6" style="margin-top: 7vw;">
    <div class="row justify-content-center">
        <div class="col-md-8 bg-white p-4 border border-black rounded">
            <p class="text-center fs-4 fw-bold">Nombre del usuario: <?php echo $user_data['Usuario']; ?></p>
            <p class="text-center fs-4 fw-bold">Rol del usuario: <?php echo $rolDelUsuario; ?></p>
            <p class="text-center fs-4 fw-bold">Apellido del operador: <?php echo $user_data['Apellido_Operador']; ?></p>
            <p class="text-center fs-4 fw-bold">Nombre del operador: <?php echo $user_data['Nombre_Operador']; ?></p>
            <p class="text-center fs-4 fw-bold">NI del operador: <?php echo $user_data['NI_Operador']; ?></p>
        </div>
    </div>
</div>






</body>
</html>
