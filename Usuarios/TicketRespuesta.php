<?php
// Conectar a la base de datos de forma segura
require '../ServerConnect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // El usuario no ha iniciado sesión, redirigirlo a la página de inicio de sesión
    header("Location: ../Login.php");
    exit();
}

// Verificar el rol del usuario
if ((int)$_SESSION['rolUsuario'] > 1) {
    // Si el rol es mayor a 1, el usuario no tiene permiso para acceder al formulario
    header("Location: Main.php");
    exit();
}

// Conexión a la base de datos
$conn = open_database_connection();
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Verifica si la solicitud es POST y si POST_Action está establecida y no está vacía
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['POST_Action']) || empty($_POST['POST_Action'])) {
    header("Location: Main.php");
    exit;
}
    switch ($_POST['POST_Action']) {
        case 'Responder':
            $ID = $_POST['ID'] ?? '';
            $stmt = $conn->prepare("SELECT * FROM sistema_tickets WHERE ID = ?");
            $stmt->bind_param("s", $ID);
            $stmt->execute();
            $result = $stmt->get_result();
            $TicketData = $result->fetch_assoc();
            $stmt->close();
            break;

        case 'Update':
            $ID = $_POST['ID'];
            $Prioridad = $_POST['Prioridad'];
            $Estado = $_POST['Estado'];
            $Respuesta = $_POST['Respuesta'];
            $username = $_SESSION['username'];
            $FechaDeRespuesta = date("Y-m-d H:i:s"); // Fecha y hora actual

            $stmt = $conn->prepare("UPDATE sistema_tickets SET Prioridad=?, Estado=?, Respuesta=?, RespondidoPor=?, FechaDeRespuesta=? WHERE ID=?");
            $stmt->bind_param("sssssi", $Prioridad, $Estado, $Respuesta, $username, $FechaDeRespuesta, $ID);

            if (!$stmt->execute()) {
                die("Error al actualizar los datos del reporte: " . $stmt->error);
            }

            $stmt->close();
            header("Location: TicketTabla.php");
            exit();
            break;

            default:
            // Si POST_Action no coincide con ningún caso conocido
            header("Location: Main.php");
            exit;
    }

// Cierra la conexión a la base de datos
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sistema de tickets</title>
  <link rel="stylesheet" type="text/css" href="../css/styles.css">
  <link rel="icon" href="../css/Images/favicon.ico" type="Image/x-icon">
  <link rel="shortcut icon" href="../css/Images/favicon.ico" type="Image/x-icon">
  <script src="../Scripts/ManejarImagenes.js"></script>
</head>
<body class="BodyFondo1">

<div class="Formulario">
    <form id="Ticket" method="post" action="TicketRespuesta.php">
        <h1>TICKET N° "<?php echo isset($TicketData['ID']) ? $TicketData['ID'] : ''; ?>"</h1>

        <h3><u>Tipo de ticket:</u> "<?php echo isset($TicketData['Tipo']) ? $TicketData['Tipo'] : ''; ?>"</h3>

        <h4><u>Usuario creador:</u> "<?php echo isset($TicketData['UsuarioCreador']) ? $TicketData['UsuarioCreador'] : ''; ?>"</h4>

        <h4><u>Fecha de creacion:</u> "<?php echo isset($TicketData['FechaDeCreacion']) ? $TicketData['FechaDeCreacion'] : ''; ?>"</h4>

        <textarea type="text" id="Solicitud" name="Solicitud" required readonly><?php echo isset($TicketData['Solicitud']) ? $TicketData['Solicitud'] : ''; ?></textarea>

        <input type="file" id="Imagen" name="Imagen" accept="image/*" onchange="procesarImagen(event, 'previewImagen', 'Base64_Imagen')" hidden>
        <img id="previewImagen" class="preview2" src="" alt="Previsualización de imagen">
        <textarea id="Base64_Imagen" name="Base64_Imagen" hidden><?php echo isset($TicketData['Imagen']) ? $TicketData['Imagen'] : ''; ?></textarea>

        <h2>FORMULARIO DE RESPUESTA</h2>

        <input type="hidden" name="ID" value="<?php echo isset($TicketData['ID']) ? $TicketData['ID'] : ''; ?>">

        <label for="Prioridad" style="text-align: center;">Prioridad del ticket:</label>
        <select id="Prioridad" name="Prioridad" style="text-align: center;" required>
            <option value="EN ESPERA" disabled <?php if ($TicketData['Prioridad'] == 'EN ESPERA') echo ' selected'; ?>>EN ESPERA</option>
            <option value="ALTA"<?php if ($TicketData['Prioridad'] == 'ALTA') echo ' selected'; ?>>ALTA</option>
            <option value="MEDIA"<?php if ($TicketData['Prioridad'] == 'MEDIA') echo ' selected'; ?>>MEDIA</option>
            <option value="BAJA"<?php if ($TicketData['Prioridad'] == 'BAJA') echo ' selected'; ?>>BAJA</option>
            <option value="DESESTIMADO"<?php if ($TicketData['Prioridad'] == 'DESESTIMADO') echo ' selected'; ?>>DESESTIMADO</option>
        </select>

        <label for="Estado" style="text-align: center;">Estado del ticket:</label>
        <select id="Estado" name="Estado" style="text-align: center;" required>
            <option value="EN ESPERA" disabled <?php if ($TicketData['Estado'] == 'EN ESPERA') echo ' selected'; ?>>EN ESPERA</option>
            <option value="EN PROCESO"<?php if ($TicketData['Estado'] == 'EN PROCESO	') echo ' selected'; ?>>EN PROCESO	</option>
            <option value="RESUELTO"<?php if ($TicketData['Estado'] == 'RESUELTO	') echo ' selected'; ?>>RESUELTO	</option>
        </select>

        <label for="Respuesta">Respuesta:</label>
        <textarea type="text" id="Respuesta" name="Respuesta" required><?php echo isset($TicketData['Respuesta']) ? $TicketData['Respuesta'] : ''; ?></textarea>

        <input type="hidden" name="POST_Action" value="Update">

        <button type="submit" class="CustomLargeButton" style="text-align: center; margin-top: 1vw;">Responder ticket</button>
    </form>
</div>

<button type="button" class="CustomButton Volver" onclick="window.location.href='TicketTabla.php'">Volver</button>

</body>
</html>
