<?php
// Conectar a la base de datos de forma segura
require '../ServerConnect.php';

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
  <link rel="stylesheet" type="text/css" href="../css/styles.css">
  <link rel="icon" href="../css/Images/favicon.ico" type="Image/x-icon">
  <link rel="shortcut icon" href="../css/Images/favicon.ico" type="Image/x-icon">
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="BodyFondo2">

<button type="button" class="CustomButton Volver" onclick="window.location.href='../Main.php'">Volver</button>

<div class="LoginForm">
    <p style="text-align: center;">Nombre del usuario: <?php echo $user_data['Usuario']; ?></p>
    <p style="text-align: center;">Rol del usuario: <?php echo $rolDelUsuario; ?></p>
    <p style="text-align: center;">Apellido del operador: <?php echo $user_data['Apellido_Operador']; ?></p>
    <p style="text-align: center;">Nombre del operador: <?php echo $user_data['Nombre_Operador']; ?></p>
    <p style="text-align: center;">NI del operador: <?php echo $user_data['NI_Operador']; ?></p>
</div>

<button type="button" class="CustomButton" style="left: 1%;top: 1%;" onclick="window.location.href='CambiarContraseña.php'">Cambiar contraseña</button>

<?php
// Verifica si el rol del usuario es "1"
if ($rolUsuarioID === 1) {
    // Si es administrador, muestra solo el botón de redirección directa
    echo '<button type="button" class="CustomButton" style="left: 1%;top: 13%;" onclick="window.location.href=\'TicketTabla.php\'">Sistema de tickets</button>';
} else {
    // Si no es administrador, muestra el botón que activa la ventana modal
    echo '<button type="button" class="CustomButton" style="left: 1%;top: 13%;" onclick="mostrarVentanaModal()">Sistema de tickets</button>';

    // Y también incluye la ventana modal
    echo '
    <script>
    function mostrarVentanaModal() {
        Swal.fire({
            title: "SISTEMA DE TICKETS",
            html: "<b style=\'color: Black; font-size: 1vw;\'>Seleccione a qué módulo del sistema de tickets desea dirigirse</b>",
            showCloseButton: true,
            showCancelButton: true,
            focusConfirm: false,
            allowOutsideClick: false,
            confirmButtonText: "Crear nuevo ticket",
            cancelButtonText: "Mis tickets enviados",
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "TicketCarga.php"; // URL para "Crear nuevo ticket"
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                window.location.href = "TicketTabla.php"; // URL para "Mis tickets enviados"
            }
        });
    }
    </script>
    ';
}
?>

<?php
// Verifica si el rol del usuario es "1" o "2" y muestra el botón "CrearUsuario" en consecuencia
if ($rolUsuarioID === 1 || $rolUsuarioID === 2) {
    echo '<button type="button" class="CustomButton" style="left: 1%;top: 25%;" onclick="window.location.href=\'CrearUsuario.php\'">Crear usuario</button>';
}
?>

<?php
// Verifica si el rol del usuario es "1" o "2" y muestra el botón "ListarUsuarios" en consecuencia
if ($rolUsuarioID === 1 || $rolUsuarioID === 2) {
    echo '<button type="button" class="CustomButton" style="left: 1%;top: 37%;" onclick="window.location.href=\'TablaDeUsuarios.php\'">Tabla de usuarios</button>';
}
?>

</body>
</html>
