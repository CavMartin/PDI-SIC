<?php
// Conectar a la base de datos de forma segura
require '../PHP/ServerConnect.php';

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
                header("Location: BandejaDeEntrada.php");
                exit();
            }
    } elseif (isset($_POST['ReabrirIncidencia'])) {
        $incidenciaID = $_POST['ReabrirIncidencia'];
            if (actualizarEstadoIncidencia($incidenciaID, 1)) {
                header("Location: BandejaDeEntrada.php");
                exit();
            }
        }
    }

// Consulta para obtener los datos
$sql = "SELECT DispositivoSIACIP, 
               IncidenciaTipo, 
               Estado,
               FechaDeCreacion 
        FROM sistema_dispositivo_siacip
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
  <title>Consultas - Main</title>
  <!-- Favicon -->
  <link rel="icon" href="../CSS/Images/favicon.ico" type="Image/x-icon">
  <link rel="shortcut icon" href="../CSS/Images/favicon.ico" type="Image/x-icon">
  <!-- CSS -->
  <link rel="stylesheet" type="text/css" href="../CSS/styles.css">
  <link rel="stylesheet" type="text/css" href="../CSS/Webkit.css">
  <!-- JQuery -->
  <script src="../JQuery/jquery-3.7.1.min.js"></script>
  <!-- SweetAlert -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Bootstrap -->
  <script src="../Bootstrap/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="../Bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../Bootstrap/Icons/font/bootstrap-icons.css">
  <!-- DataTables -->
  <link href="https://cdn.datatables.net/v/dt/dt-1.13.8/datatables.min.css" rel="stylesheet">
  <script src="https://cdn.datatables.net/v/dt/dt-1.13.8/datatables.min.js"></script>
</head>
<body class="bg-secondary" style="overflow-x: hidden;">

<!-- Barra de navegación -->
<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark" style="height: 5vw;">
    <div class="container-fluid d-flex align-items-center justify-content-center">
        
        <!-- Imagen 1 -->
        <div>
            <img src="../CSS/Images/PSF.png" alt="Icono" style="width: 4vw; margin: 1vw;"><!-- Icono -->
        </div>

        <!-- Título centrado -->
        <div class="text-center">
            <h1 class="text-warning">DISPOSITO SIACIP - BANDEJA DE ENTRADA</h1>
        </div>

        <!-- Imagen 2 -->
        <div>
            <img src="../CSS/Images/OJO.png" alt="Icono" style="width: 4vw; margin: 1vw;"><!-- Icono -->
        </div>

        <!-- Botón de navegación a la página principal -->
        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <button type="button" id="VolverButton" class="btn btn-warning btn-lg" style="position: fixed; top: 0; left: 0; width: 12vw; height:4vw; font-size: 1.5vw; margin: 0.5vw;" onclick="window.location.href='Main.php'">
                <i class="bi bi-arrow-left-square-fill"></i> <b>VOLVER</b>
            </button>
        </div>
    </div>
</nav>

<div class="row border border-black rounded bg-light p-1" style="position: fixed; top: 6vw;">
    <div class="col">
        <div class="input-group my-2">
            <span class="input-group-text fw-bold">Buscar en la tabla:</span>
            <input type="text" class="form-control" id="CustomSearch" name="CustomSearch" placeholder="Ingrese el valor a buscar...">
        </div>
    </div>

    <div class="input-group mb-2">
        <label for="CustomLength" class="input-group-text fw-bold">Cantidad de registros:</label>
        <select id="CustomLength" class="form-select" name="CustomLength">
            <option value="10">10 registros</option>
            <option value="25" selected>25 registros</option>
            <option value="50">50 registros</option>
            <option value="100">100 registros</option>
        </select>
    </div>
</div>

<!-- Tabla de usuarios -->
<div class="SearchTable">
    <table id="IPTable" style="display: block; overflow: auto; max-height: 33vw; min-height: 33vw; background-color: rgb(211, 216, 223);">
      <thead>
        <tr>
            <th style="width: 20vw; position: sticky; top: -1px; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">FECHA DE CREACIÓN</th>
            <th style="width: 10vw; position: sticky; top: -1px; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">ID</th>
            <th style="width: 10vw; position: sticky; top: -1px; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">ESTADO</th>
            <th style="width: 20vw; position: sticky; top: -1px; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">TIPO DE HECHO</th>
            <th style="width: 40vw; position: sticky; top: -1px; text-align: center; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">ACCIONES</th>
        </tr>
    </thead>
    </tbody>
        <?php
        while ($row = $result->fetch_assoc()) {
            // Formatea la fecha y hora en "DD/MM/AAAA Hora:Minuto:Segundo"
            $fechaFormateada = date("d/m/Y H:i:s", strtotime($row["FechaDeCreacion"]));

            echo '<tr>';
            echo '<td>' . htmlspecialchars($fechaFormateada, ENT_QUOTES, 'UTF-8') . '</td>';

            echo '<td>' . htmlspecialchars($row["DispositivoSIACIP"], ENT_QUOTES, 'UTF-8') . '</td>';

            echo '<td>';
            // Formulario para Cerrar Incidencia
            if ($row["Estado"] == 1) {
                echo '<form action="BandejaDeEntrada.php" method="POST" style="display:inline;" onsubmit="return confirm(\'¿Está seguro de que desea cerrar el dispositivo SIACIP?\');">';
                echo '<input type="hidden" name="CerrarIncidencia" value="' . $row["DispositivoSIACIP"] . '">';
                echo '<button class="ESTADO_BTN ABIERTO"><span class="NORMAL_TEXT">ABIERTO</span><span class="ON_HOVER">CERRAR DISPOSITIVO</span></button>';

            } else {
                echo '<form action="BandejaDeEntrada.php" method="POST" style="display:inline;" onsubmit="return confirm(\'¿Está seguro de que desea reabrir el dispositivo SIACIP?\');">';
                echo '<input type="hidden" name="ReabrirIncidencia" value="' . $row["DispositivoSIACIP"] . '">';
                echo '<button class="ESTADO_BTN CERRADO"><span class="NORMAL_TEXT">CERRADO</span><span class="ON_HOVER">REABRIR DISPOSITIVO</span></button>';
            }
            echo '</form>';
            echo '</td>';

            echo '<td>' . htmlspecialchars($row["IncidenciaTipo"], ENT_QUOTES, 'UTF-8') . '</td>';

            // Agregamos botones para cada fila
            echo '<td>';

            // Formulario para Editar tipo de incidencia
            echo '<form action="../DispositivoSIACIP/IncidenciaPriorizada/NuevaIncidencia.php" method="POST" style="display:inline;">';
            echo '<input type="hidden" name="Incidencia_Numero" value="' . $row["DispositivoSIACIP"] . '">';
            echo '<input type="hidden" name="POST_Action" value="EditarIncidencia">';
            echo '<input type="submit" class="btn btn-warning m-2" value="Modificar tipo">';
            echo '</form>';

            // Formulario para "Reporte preliminar"
            echo '<form action="../DispositivoSIACIP/ReportePreliminar/Main.php" method="POST" style="display:inline;">';
            echo '<input type="hidden" name="DispositivoSIACIP" value="' . $row["DispositivoSIACIP"] . '">';
            echo '<input type="submit" class="btn btn-primary m-2" value="Reporte preliminar">';
            echo '</form>';

            // Formulario para "Incidencia Priorizada"
            echo '<form action="../DispositivoSIACIP/IncidenciaPriorizada/Main.php" method="POST" style="display:inline;">';
            echo '<input type="hidden" name="DispositivoSIACIP" value="' . $row["DispositivoSIACIP"] . '">';
            echo '<input type="submit" class="btn btn-danger m-2" value="Incidencia priorizada">';
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
      var table = $('#IPTable').DataTable({
        language: {
        url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
        },
        searching: true, // Habilita el campo de búsqueda de DataTables
        lengthChange: false, // Desactiva el selector de cantidad de registros por página de DataTables
        pageLength: 25, // Establece la cantidad de registros por página predeterminada
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