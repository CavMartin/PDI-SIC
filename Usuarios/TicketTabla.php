<?php
// Conectar a la base de datos de forma segura
require '../ServerConnect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // El usuario no ha iniciado sesión, redirigirlo a la página de inicio de sesión
    header("Location: Login.php");
    exit();
}

// Establece la conexión a la base de datos
$conn = open_database_connection();
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener variables desde la sesión
$rolUsuario = $_SESSION['rolUsuario'];
$username = $_SESSION['username'];

// Consulta SQL base
$sql = "SELECT ID,
               Tipo,
               Solicitud,
               UsuarioCreador,
               FechaDeCreacion,
               Prioridad,
               Estado,
               Respuesta,
               FechaDeRespuesta,
               RespondidoPor
        FROM sistema_tickets";

// Agregar condición WHERE si el usuario no es administrador
if ($rolUsuario != 1) {
    $sql .= " WHERE UsuarioCreador = '" . $conn->real_escape_string($username) . "'";
}

// Continuar con la consulta SQL
$sql .= " ORDER BY ID ASC";

$result = $conn->query($sql);

// Marcar ticket como resuelto
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['POST_Action']) && $_POST['POST_Action'] == 'Resuelto') {
    $ID = $_POST['ID'];

    // Establecer conexión
    $conn = open_database_connection();

    // Preparar consulta
    $stmt = $conn->prepare("UPDATE sistema_tickets SET Estado = 'RESUELTO' WHERE ID = ?");
    $stmt->bind_param("i", $ID);

    // Ejecutar consulta
    if ($stmt->execute()) {
        // Redirigir o manejar el éxito de la actualización
        header("Location: TicketTabla.php");
    }

    // Cerrar declaración y conexión
    $stmt->close();
    $conn->close();
}


// Cerrar conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Usuarios - Tabla de usuarios</title>
  <link rel="icon" href="../css/Images/favicon.ico" type="Image/x-icon">
  <link rel="shortcut icon" href="../css/Images/favicon.ico" type="Image/x-icon">
  <link rel="stylesheet" type="text/css" href="../css/styles.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <link href="https://cdn.datatables.net/v/dt/dt-1.13.8/datatables.min.css" rel="stylesheet">
  <script src="https://cdn.datatables.net/v/dt/dt-1.13.8/datatables.min.js"></script>
</head>
<body class="BodyFondo3" style="background-color: rgb(183, 179, 179);">

<div id="search-container" style="max-width: 20%;">
  <label for="CustomSearch" style="color: rgb(0, 0, 0);">Buscar en la tabla:</label>
  <input type="text" id="CustomSearch" style="color: rgb(0, 0, 0);" placeholder="Ingrese el valor a buscar...">
</div>

<div id="length-container" style="max-width: 20%;">
  <label for="CustomLength" style="color: rgb(0, 0, 0);">Cantidad a mostrar:</label>
  <select id="CustomLength" style="color: rgb(0, 0, 0);">
    <option value="10">10 registros</option>
    <option value="25">25 registros</option>
    <option value="50">50 registros</option>
    <option value="100">100 registros</option>
  </select>
</div>

<div class="TopCenterDiv">
    <img src="../css/Images/PSF.png" alt="Texto alternativo" width="100%">
</div>

<!-- Tabla de usuarios -->
<div class="SearchTable">
<div style="width: 100%;">
<table id="UserTable" style="display: block; table-layout: fixed; overflow: auto; max-height: 30vw; max-width: 100vw; background-color: rgb(211, 216, 223);">
    <thead>
        <tr>
            <th style="min-width: 5vw; position: sticky; top: -1px; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">ID</th>
            <th style="min-width: 20vw; position: sticky; top: -1px; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">TIPO DE TICKET</th>
            <th style="min-width: 10vw; position: sticky; top: -1px; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">USUARIO CREADOR</th>
            <th style="min-width: 10vw; position: sticky; top: -1px; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">FECHA DE CREACIÓN</th>
            <th style="min-width: 40vw; position: sticky; top: -1px; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">DESCRIPCIÓN / SOLICITUD</th>
            <th style="min-width: 10vw; position: sticky; top: -1px; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">PRIORIDAD</th>
            <th style="min-width: 10vw; position: sticky; top: -1px; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">ESTADO</th>
            <th style="min-width: 10vw; position: sticky; top: -1px; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">RESPONDIDO POR</th>
            <th style="min-width: 40vw; position: sticky; top: -1px; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">RESPUESTA DEL ADMINISTRADOR</th>
            <th style="min-width: 10vw; position: sticky; top: -1px; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">FECHA DE CIERRE</th>
        <?php if ($rolUsuario === 1): // Fila adicional para administradores?>
            <th style="min-width: 10vw; position: sticky; top: -1px; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">ACCIONES</th>
        <?php endif; ?>
        </tr>
    </thead>
    </tbody>
<?php
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td style="min-width: 5vw;">' . ($row["ID"] !== null ? htmlspecialchars($row["ID"], ENT_QUOTES, 'UTF-8') : '') . '</td>';
            echo '<td style="min-width: 20vw;">' . ($row["Tipo"] !== null ? htmlspecialchars($row["Tipo"], ENT_QUOTES, 'UTF-8') : '') . '</td>';
            echo '<td style="min-width: 10vw;">' . ($row["UsuarioCreador"] !== null ? htmlspecialchars($row["UsuarioCreador"], ENT_QUOTES, 'UTF-8') : '') . '</td>';
            echo '<td style="min-width: 10vw;">' . ($row["FechaDeCreacion"] !== null ? htmlspecialchars($row["FechaDeCreacion"], ENT_QUOTES, 'UTF-8') : '') . '</td>';
            echo '<td style="min-width: 40vw; text-align: justify;">' . ($row["Solicitud"] !== null ? htmlspecialchars($row["Solicitud"], ENT_QUOTES, 'UTF-8') : '') . '</td>';

            // PRIORIDAD
            switch ($row["Prioridad"]) {
                case 'ALTA':
                    $Color = "red";
                    break;
                case 'MEDIA':
                    $Color = "orange";
                    break;
                case 'BAJA':
                    $Color = "green";
                    break;
                default:
                    $Color = "black";
            }
    
            echo '<td style="min-width: 10vw; color: ' . $Color . ';">' . ($row["Prioridad"] !== null ? htmlspecialchars($row["Prioridad"], ENT_QUOTES, 'UTF-8') : '') . '</td>';

            // ESTADO
            switch ($row["Estado"]) {
                case 'DESESTIMADO':
                    $Color = "red";
                    break;
                case 'RESUELTO':
                    $Color = "BLUE";
                    break;
                case 'EN PROCESO':
                    $Color = "green";
                    break;
                default:
                    $Color = "black";
            }
    
            echo '<td style="min-width: 10vw; color: ' . $Color . ';">' . ($row["Estado"] !== null ? htmlspecialchars($row["Estado"], ENT_QUOTES, 'UTF-8') : '') . '</td>';

            echo '<td style="min-width: 10vw;">' . ($row["RespondidoPor"] !== null ? htmlspecialchars($row["RespondidoPor"], ENT_QUOTES, 'UTF-8') : '') . '</td>';
            echo '<td style="min-width: 40vw; text-align: justify;">' . ($row["Respuesta"] !== null ? htmlspecialchars($row["Respuesta"], ENT_QUOTES, 'UTF-8') : '') . '</td>';
            echo '<td style="min-width: 10vw;">' . ($row["FechaDeRespuesta"] !== null ? htmlspecialchars($row["FechaDeRespuesta"], ENT_QUOTES, 'UTF-8') : '') . '</td>';

            if ($rolUsuario === 1) { // Celda adicional si el usuario es administrador
                echo '<td style="min-width: 25vw;">';
                // Boton para cambiar el estado de los Tickets
                echo '<form action="TicketTabla.php" method="POST" style="display:inline;">';
                echo '<input type="hidden" name="ID" value="' . htmlspecialchars($row["ID"], ENT_QUOTES, 'UTF-8') . '">';
                echo '<input type="hidden" name="POST_Action" value="Resuelto">';
                echo '<input type="submit" class="BTN_Custom BTN-Blue" value="Marcar como resuelto">';
                echo '</form>';

                // Boton para responder los Tickets
                echo '<form action="TicketRespuesta.php" method="POST" style="display:inline;">';
                echo '<input type="hidden" name="ID" value="' . htmlspecialchars($row["ID"], ENT_QUOTES, 'UTF-8') . '">';
                echo '<input type="hidden" name="POST_Action" value="Responder">';
                echo '<input type="submit" class="BTN_Custom BTN-Green" value="Responder ticket">';
                echo '</form>';

                echo '</td>';
            }
            echo '</tr>';
        }
        ?>
    </tbody>
</table>
</div>
</div>

<script>
$(document).ready(function() {
  var table = $('#UserTable').DataTable({
    searching: true, // Habilita el campo de búsqueda de DataTables
    lengthChange: false, // Desactiva el selector de cantidad de registros por página de DataTables
    pageLength: 10, // Establece la cantidad de registros por página predeterminada
    lengthMenu: [10, 25, 50, 100], // Define las opciones del selector de cantidad de registros por página
    dom: 'lBfrtip' // Personaliza la disposición de los elementos de DataTables
  });

  $('#CustomSearch').on('input', function() {
    table.search($(this).val()).draw();
  });

  $('#CustomLength').change(function(){
    table.page.len($(this).val()).draw();
  });
});
</script>

<button type="button" class="CustomButton Volver" onclick="window.location.href='Main.php'">Volver</button>

</body>
</html>
