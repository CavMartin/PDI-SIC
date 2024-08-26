<?php
// Conectar a la base de datos de forma segura
require '../PHP/ServerConnect.php';

// Verificar estado del login
checkLoginState();

// Obtener el nombre de usuario del usuario logeado desde la sesión
$username = $_SESSION['username'];
$userrole = $_SESSION['userrole'];

// Establece la conexión a la base de datos
$conn = open_database_connection('sistema_horus');
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Consulta para obtener los datos del usuario logeado
$stmt = $conn->prepare("SELECT ID, Usuario, Rol, Grupo, Apellido_Operador, Nombre_Operador, NI_Operador FROM sistema_usuarios WHERE Usuario = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user_data = $result->fetch_assoc();

switch ($userrole) {
    case 1:
        $rolDelUsuario = "ADMINISTRADOR";
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
  <script src="../Resources/SweetAlert2/sweetalert2.min.js"></script>
  <link rel="stylesheet" href="../Resources/SweetAlert2/sweetalert2.min.css">
  <!-- Popper.js -->
  <script src="../Resources/Popper/popper.min.js"></script>
  <!-- Bootstrap -->
  <script src="../Resources/Bootstrap/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="../Resources/Bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../Resources/Bootstrap/Icons/font/bootstrap-icons.css">
</head>
<body class="bg-secondary">

<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
    <div class="container-fluid d-flex justify-content-center position-relative">

        <!-- Botón de volver a la izquierda con posición absoluta -->
        <div style="position: absolute; left: 0;">
            <button type="button" class="btn btn-warning btn-lg mx-3" onclick="window.location.href='../index.php'">
                <i class="bi bi-arrow-left-square-fill"></i> <b>VOLVER</b>
            </button>
        </div>

        <!-- Bloque del título con imágenes, centrado en la totalidad de la pantalla -->
        <div class="d-flex justify-content-center align-items-center">
            <img src="../CSS/Images/PSF.png" class="mx-3" alt="Icono" style="width: 4rem;"><!-- Icono Izquierdo -->
            <h2 class="text-light text-center m-0">PANEL DE GESTIÓN DE USUARIO</h2>
            <img src="../CSS/Images/LOGO.png" class="mx-3" alt="Icono" style="width: 4rem;"><!-- Icono Derecho -->
        </div>

        <!-- Menú desplegable a la derecha -->
        <div style="position: absolute; right: 0;">
            <div class="collapse navbar-collapse" id="navbarNavDarkDropdown">
              <ul class="navbar-nav mx-3">
                <li class="nav-item dropdown">
                  <button class="btn btn-dark btn-lg dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    MENÚ DESPLEGABLE
                  </button>
                    <ul class="dropdown-menu dropdown-menu-dark">
                      <li><a class="dropdown-item fs-4" href="CambiarContraseña.php">Cambiar contraseña</a></li>
                      <?php
                        // Verifica si el rol del usuario es "1" o "2" y muestra los botones "CrearUsuario" y "Listar usuarios" en consecuencia
                        if ($userrole <= 2) {
                            echo '<li><a class="dropdown-item fs-4" href=\'CrearUsuario.php\'">Crear usuario</a></li>';
                            echo '<li><a class="dropdown-item fs-4" href=\'TablaDeUsuarios.php\'">Tabla de usuarios</a></li>';
                        }
                      ?>
                    </ul>
                </li>
              </ul>
            </div>
        </div>
    </div>
</nav>

<div class="container mt-6" style="margin-top: 7rem;">
    <div class="row justify-content-center">
        <div class="col-md-8 bg-white p-4 border border-black rounded">
            <p class="text-center fs-4 fw-bold">Nombre del usuario: <?php echo $user_data['Usuario']; ?></p>
            <p class="text-center fs-4 fw-bold">Rol del usuario: <?php echo $rolDelUsuario; ?></p>
            <p class="text-center fs-4 fw-bold">Grupo del usuario: <?php echo $user_data['Grupo']; ?></p>
            <p class="text-center fs-4 fw-bold">Apellido del operador: <?php echo $user_data['Apellido_Operador']; ?></p>
            <p class="text-center fs-4 fw-bold">Nombre del operador: <?php echo $user_data['Nombre_Operador']; ?></p>
            <p class="text-center fs-4 fw-bold">NI del operador: <?php echo $user_data['NI_Operador']; ?></p>
        </div>
    </div>
</div>

</body>
</html>
