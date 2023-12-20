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

// Función para cerrar o reabrir una incidencia
function actualizarEstadoIncidencia($incidenciaID, $nuevoEstado) {
    global $conn;
    
    // Actualiza el estado de la incidencia
    $sql = "UPDATE sistema_dispositivo_siacip SET Estado = ? WHERE DispositivoSIACIP = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $nuevoEstado, $incidenciaID);
    
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

// Verificar si se envió el formulario para cerrar o reabrir incidencia
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['CerrarIncidencia'])) {
        $incidenciaID = $_POST['CerrarIncidencia'];
            if (actualizarEstadoIncidencia($incidenciaID, 0)) {
                header("Location: Consultas_Bandeja.php");
                exit();
            }
    } elseif (isset($_POST['ReabrirIncidencia'])) {
        $incidenciaID = $_POST['ReabrirIncidencia'];
            if (actualizarEstadoIncidencia($incidenciaID, 1)) {
                header("Location: Consultas_Bandeja.php");
                exit();
            }
        }
    }

// Consulta para obtener los datos
$sql = "SELECT DispositivoSIACIP, 
               TipoDeHecho AS TipoHecho, 
               Estado,
               RP_Creado,
               IP_Creada,
               FechaDeCreacion 
        FROM sistema_dispositivo_siacip d
        INNER JOIN lista_tipo_de_hecho TipoDeHecho ON IncidenciaTipo = TipoDeHecho.ID
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
  <link rel="stylesheet" href="../Bootstrap/css/bootstrap.min.css">
  <script src="../Bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <link href="https://cdn.datatables.net/v/dt/dt-1.13.8/datatables.min.css" rel="stylesheet">
  <script src="https://cdn.datatables.net/v/dt/dt-1.13.8/datatables.min.js"></script>
</head>
<body class="BodyFondo3" style="background-color: rgb(183, 179, 179);">

<button type="button" class="CustomButton VolverBlack" onclick="window.location.href='Main.php'">Volver</button>

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
    <table id="UserTable" style="display: block; overflow: auto; max-height: 30vw; background-color: rgb(211, 216, 223);">
      <thead>
        <tr>
            <th style="width: 10vw; position: sticky; top: -1px; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">ID</th>
            <th style="width: 10vw; position: sticky; top: -1px; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">ESTADO</th>
            <th style="width: 20vw; position: sticky; top: -1px; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">TIPO DE HECHO</th>
            <th style="width: 20vw; position: sticky; top: -1px; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">FECHA DE CREACIÓN</th>
            <th style="width: 40vw; position: sticky; top: -1px; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">ACCIONES</th>
        </tr>
    </thead>
    </tbody>
        <?php
        while ($row = $result->fetch_assoc()) {
            // Formatea la fecha y hora en "DD/MM/AAAA Hora:Minuto:Segundo"
            $fechaFormateada = date("d/m/Y H:i:s", strtotime($row["FechaDeCreacion"]));
        
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row["DispositivoSIACIP"], ENT_QUOTES, 'UTF-8') . '</td>';

            echo '<td>';
            // Formulario para Cerrar Incidencia
            if ($row["Estado"] == 1) {
                echo '<form action="Consultas_Bandeja.php" method="POST" style="display:inline;" onsubmit="return confirm(\'¿Está seguro de que desea cerrar el dispositivo SIACIP?\');">';
                echo '<input type="hidden" name="CerrarIncidencia" value="' . $row["DispositivoSIACIP"] . '">';
                echo '<button class="ESTADO_BTN ABIERTO"><span class="NORMAL_TEXT">ABIERTO</span><span class="ON_HOVER">CERRAR DISPOSITIVO</span></button>';

            } else {
                echo '<form action="Consultas_Bandeja.php" method="POST" style="display:inline;" onsubmit="return confirm(\'¿Está seguro de que desea reabrir el dispositivo SIACIP?\');">';
                echo '<input type="hidden" name="ReabrirIncidencia" value="' . $row["DispositivoSIACIP"] . '">';
                echo '<button class="ESTADO_BTN CERRADO"><span class="NORMAL_TEXT">CERRADO</span><span class="ON_HOVER">REABRIR DISPOSITIVO</span></button>';
            }
            echo '</form>';
            echo '</td>';

            echo '<td>' . htmlspecialchars($row["TipoHecho"], ENT_QUOTES, 'UTF-8') . '</td>';
            echo '<td>' . htmlspecialchars($fechaFormateada, ENT_QUOTES, 'UTF-8') . '</td>';
            
            // Agregamos botones para cada fila
            echo '<td>';

            // Formulario para Editar tipo de incidencia
            echo '<form action="../IncidenciaPriorizada/NuevaIncidencia.php" method="POST" style="display:inline;">';
            echo '<input type="hidden" name="Incidencia_Numero" value="' . $row["DispositivoSIACIP"] . '">';
            echo '<input type="hidden" name="POST_Action" value="EditarIncidencia">';
            echo '<input type="submit" class="btn btn-warning" value="Modificar tipo">';
            echo '</form>';

            // Formulario para "Reporte preliminar"
            if ($row["RP_Creado"] != 0) {
            echo '<form action="../ReportePreliminar/Main.php" method="POST" style="display:inline;">';
            echo '<input type="hidden" name="DispositivoSIACIP" value="' . $row["DispositivoSIACIP"] . '">';
            echo '<input type="submit" class="btn btn-primary" value="Reporte preliminar">';
            echo '</form>';
            }

            // Formulario para "Incidencia Priorizada"
            echo '<form action="../IncidenciaPriorizada/Main.php" method="POST" style="display:inline;">';
            echo '<input type="hidden" name="DispositivoSIACIP" value="' . $row["DispositivoSIACIP"] . '">';
            echo '<input type="submit" class="btn btn-danger" value="Incidencia priorizada">';
            echo '</form>';

            echo '</td>';    
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