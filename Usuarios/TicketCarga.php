<?php
// Conectar a la base de datos de forma segura
require '../ServerConnect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // El usuario no ha iniciado sesión, redirigirlo a la página de inicio de sesión
    header("Location: ../Login.php");
    exit();
}

// Obtener la información de SESION
$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['POST_Action'] === "EnviarTicket") {
    // Obtiene la conexión a la base de datos
    $conn = open_database_connection();

    $Tipo = $_POST["Tipo"];
    $Solicitud = $_POST["Solicitud"];
    $Base64_Imagen = $_POST["Base64_Imagen"];

    // Preparar y ejecutar la consulta SQL para insertar los datos en la base de datos
    $stmt = $conn->prepare("INSERT INTO sistema_tickets (Tipo, Solicitud, Imagen, UsuarioCreador) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $Tipo, $Solicitud, $Base64_Imagen, $username);

    // Ejecutar inserción en la tabla sistema_tickets
    if ($stmt->execute()) {
        // El ticket se ha insertado correctamente
        // Redirigir al usuario a una página de confirmación o a la página de inicio
        header("Location: Main.php");
        exit();
    } else {
        // Manejo de error en caso de que la inserción falle
        // Puedes registrar el error o mostrar un mensaje de error al usuario
        mysqli_rollback($conn);
        die("Error al enviar el ticket: " . $stmt->error);
    }

    // Cierra la conexión a la base de datos
    $stmt->close();
    $conn->close();
}
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

<button type="button" class="CustomButton Volver" onclick="window.location.href='Main.php'">Volver</button>

<div class="Formulario">
    <form id="Ticket" method="post" action="TicketCarga.php">
        <h1>SISTEMA DE TICKETS</h1>

        <label for="Tipo" style="text-align: center;">Tipo de ticket:</label>
        <select id="Tipo" name="Tipo" style="text-align: center;" required>
            <option value="" disabled selected>Seleccione un tipo de ticket</option>
            <option value="Solicitud de nueva funcionalidad">Solicitud de nueva funcionalidad</option>
            <option value="Solicitud de modificación de funcionalidad">Solicitud de modificación de funcionalidad</option>
            <option value="Solicitud de modificación de diseño (Apartado visual)">Solicitud de modificación de diseño (Apartado visual)</option>
            <option value="Reporte de errores o fallas del sistema">Reporte de error</option>
            <option value="Otra - Especifique en la solicitud">Otra - Especifique en la solicitud</option>
        </select>

        <label for="Solicitud">Descripción / Solicitud:</label>
        <textarea type="text" id="Solicitud" name="Solicitud" required></textarea>

        <label for="Imagen" style="text-align: center;">Imagen adjunta: (Opcional)</label>
        <input type="file" id="Imagen" name="Imagen" accept="image/*" onchange="procesarImagen(event, 'previewImagen', 'Base64_Imagen')">
        <img id="previewImagen" class="preview2" src="" alt="Previsualización de imagen">
        <textarea id="Base64_Imagen" name="Base64_Imagen" hidden></textarea>

        <input type="hidden" name="POST_Action" value="EnviarTicket">

        <button type="submit" class="CustomLargeButton" style="text-align: center; margin-top: 1vw;">Enviar ticket</button>

    </form>
</div>

<?php
$Chance = 5; // Probabilidad del 5%
$RandomNumber = rand(1, 100); // Genera un número aleatorio entre 1 y 100

if ($RandomNumber <= $Chance) {
    // Si el número generado está dentro del rango de probabilidad, muestra la imagen
    echo '<div class="MaquinaEscribir">
        <img src="../CSS/Images/MaquinaEscribir.png" alt="">
        </div>
        ';
}
?>
</body>
</html>
