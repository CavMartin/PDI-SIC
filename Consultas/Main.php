<?php
// Conectar a la base de datos de forma segura
require '../ServerConnect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // El usuario no ha iniciado sesión, redirigirlo a la página de inicio de sesión
    header("Location: ../Login.php");
    exit();
}

// Establece la conexión a la base de datos
$conn = open_database_connection();
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Consulta para obtener los datos
$sql = "SELECT * FROM ficha_de_infractor
        ORDER BY FechaDeCreacion DESC";

$result = $conn->query($sql);

// Cerrar conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Consulta - Main</title>
  <link rel="stylesheet" type="text/css" href="../css/styles.css">
  <link rel="icon" href="../css/Images/favicon.ico" type="Image/x-icon">
  <link rel="shortcut icon" href="../css/Images/favicon.ico" type="Image/x-icon">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <link href="https://cdn.datatables.net/v/dt/dt-1.13.8/datatables.min.css" rel="stylesheet">
  <script src="https://cdn.datatables.net/v/dt/dt-1.13.8/datatables.min.js"></script>
</head>
<body>

<button type="button" class="CustomButton Volver" onclick="window.location.href='../Main.php'">Volver</button>

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

<!-- Tabla de usuarios -->
<div class="SearchTable">
    <table id="UserTable" style="display: block; overflow: auto; max-height: 30vw; background-color: rgb(211, 216, 223);">
      <thead>
        <tr>
            <th style="min-width: 5vw; position: sticky; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">ID</th>
            <th style="min-width: 10vw; position: sticky; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">REGIÓN</th>
            <th style="min-width: 10vw; position: sticky; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">FECHA DE CREACIÓN</th>
            <th style="min-width: 10vw; position: sticky; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">APELLIDO</th>
            <th style="min-width: 10vw; position: sticky; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">NOMBRE</th>
            <th style="min-width: 10vw; position: sticky; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">ALIAS</th>
            <th style="min-width: 10vw; position: sticky; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">DOCUMENTO</th>
            <th style="min-width: 10vw; position: sticky; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">PRONTUARIO</th>
            <th style="min-width: 10vw; position: sticky; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">GÉNERO</th>
            <th style="min-width: 10vw; position: sticky; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">FECHA DE NACIMIENTO</th>
            <th style="min-width: 10vw; position: sticky; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">LUGAR DE NACIMIENTO</th>
            <th style="min-width: 10vw; position: sticky; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">ESTADO CIVIL</th>
            <th style="min-width: 10vw; position: sticky; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">PROVINCIA</th>
            <th style="min-width: 10vw; position: sticky; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">PAÍS</th>
            <th style="min-width: 25vw; position: sticky; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">DOMICILIO</th>
            <th style="min-width: 10vw; position: sticky; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">FECHA DEL HECHO</th>
            <th style="min-width: 25vw; position: sticky; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">LUGAR DEL HECHO</th>
            <th style="min-width: 10vw; position: sticky; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">CAUSA</th>
            <th style="min-width: 10vw; position: sticky; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">JUZGADO</th>
            <th style="min-width: 10vw; position: sticky; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">FISCALIA</th>
            <th style="min-width: 10vw; position: sticky; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">DEPENDENCIA</th>
            <th style="min-width: 25vw; position: sticky; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">OBSERVACIONES</th>
            <th style="min-width: 25vw; position: sticky; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">RESEÑA</th>
            <th style="min-width: 25vw; position: sticky; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">SECUESTRO</th>


        </tr>
    </thead>
    </tbody>
        <?php
        while ($row = $result->fetch_assoc()) {
            // Formatea la fecha y hora en "DD/MM/AAAA Hora:Minuto:Segundo"
            $fechaFormateada = date("d/m/Y H:i:s", strtotime($row["FechaDeCreacion"]));
            $fechaNacimientoFormateada = date("d/m/Y", strtotime($row["FechaNacimiento"]));
            $fechaHechoFormateada = date("d/m/Y", strtotime($row["FechaHecho"]));

            // Dentro del bucle while
            $domiciliosJson = $row["Domicilio"];
            $domiciliosArray = json_decode($domiciliosJson, true);
            $domiciliosTexto = "";

            if (is_array($domiciliosArray)) {
                foreach ($domiciliosArray as $index => $domicilio) {
                    $numeroDomicilio = $index + 1; // Esto crea un número secuencial para cada domicilio
                    $domiciliosTexto .= "Domicilio " . $numeroDomicilio . ": " . htmlspecialchars($domicilio['Domicilio'], ENT_QUOTES, 'UTF-8') . "<br>";
                }
            } else {
                $domiciliosTexto = "No disponible";
            }

            echo '<tr>';

            echo '<td>' . htmlspecialchars($row["ID"], ENT_QUOTES, 'UTF-8') . '</td>';
            echo '<td>' . htmlspecialchars($row["Region"], ENT_QUOTES, 'UTF-8') . '</td>';
            echo '<td>' . htmlspecialchars($fechaFormateada, ENT_QUOTES, 'UTF-8') . '</td>';
            echo '<td>' . htmlspecialchars($row["Apellido"], ENT_QUOTES, 'UTF-8') . '</td>';
            echo '<td>' . htmlspecialchars($row["Nombre"], ENT_QUOTES, 'UTF-8') . '</td>';
            echo '<td>' . htmlspecialchars($row["Alias"], ENT_QUOTES, 'UTF-8') . '</td>';
            echo '<td>' . htmlspecialchars($row["DocumentoNumero"], ENT_QUOTES, 'UTF-8') . '</td>';
            echo '<td>' . htmlspecialchars($row["Prontuario"], ENT_QUOTES, 'UTF-8') . '</td>';
            echo '<td>' . htmlspecialchars($row["Genero"], ENT_QUOTES, 'UTF-8') . '</td>';
            echo '<td>' . htmlspecialchars($fechaNacimientoFormateada, ENT_QUOTES, 'UTF-8') . '</td>';
            echo '<td>' . htmlspecialchars($row["LugarNacimiento"], ENT_QUOTES, 'UTF-8') . '</td>';
            echo '<td>' . htmlspecialchars($row["EstadoCivil"], ENT_QUOTES, 'UTF-8') . '</td>';
            echo '<td>' . htmlspecialchars($row["Provincia"], ENT_QUOTES, 'UTF-8') . '</td>';
            echo '<td>' . htmlspecialchars($row["Pais"], ENT_QUOTES, 'UTF-8') . '</td>';
            echo '<td>' . $domiciliosTexto . '</td>';
            echo '<td>' . htmlspecialchars($fechaHechoFormateada, ENT_QUOTES, 'UTF-8') . '</td>';
            echo '<td>' . htmlspecialchars($row["LugarHecho"], ENT_QUOTES, 'UTF-8') . '</td>';
            echo '<td>' . htmlspecialchars($row["Causa"], ENT_QUOTES, 'UTF-8') . '</td>';
            echo '<td>' . htmlspecialchars($row["Juzgado"], ENT_QUOTES, 'UTF-8') . '</td>';
            echo '<td>' . htmlspecialchars($row["Fiscalia"], ENT_QUOTES, 'UTF-8') . '</td>';
            echo '<td>' . htmlspecialchars($row["Dependencia"], ENT_QUOTES, 'UTF-8') . '</td>';
            echo '<td>' . htmlspecialchars($row["Observaciones"], ENT_QUOTES, 'UTF-8') . '</td>';
            echo '<td>' . htmlspecialchars($row["Reseña"], ENT_QUOTES, 'UTF-8') . '</td>';
            echo '<td>' . htmlspecialchars($row["Secuestro"], ENT_QUOTES, 'UTF-8') . '</td>';

            echo '</tr>';
        }
        ?>
    </tbody>
</table>
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

</body>
</html>
