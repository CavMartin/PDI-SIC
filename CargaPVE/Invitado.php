<?php
require '../PHP/ServerConnect.php';

// Verificar estado del login
checkLoginState();

// Obtener el nombre del usuario
$username = $_SESSION['username'];
$usergroup = $_SESSION['usergroup'];

// Consulta para obtener los datos a mostrar en la tabla
function fetchDataForTable($conn) {
    $sql = "SELECT Formulario, Tipologia, Fuente, ReporteAsociado, FechaDeCreacion FROM entidad_encabezado ORDER BY FechaDeCreacion DESC LIMIT 10";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    $stmt->close();
    return $data;
}

// Función para armar la tabla a mostrar a partir de los datos obtenidos
function generateTable($datosMainPage) {
    $html = '<div class="MainTable">
    <table class="table table-bordered table-hover">
        <thead class="table-dark fs-5" style="vertical-align: middle;">
            <tr>
                <th>ID</th>
                <th>FECHA DE CREACIÓN</th>
                <th>TIPOLOGÍA</th>
                <th>FUENTE</th>
                <th>REPORTE ASOCIADO</th>
                <th></th>
            </tr>
        </thead>
        <tbody style="vertical-align: middle;">';

    foreach ($datosMainPage as $row) {
        if (!isset($row["FechaDeCreacion"]) || !isset($row["Formulario"]) || !isset($row["Tipologia"])) {
            continue; // Saltar filas con datos incompletos
        }

        $fechaFormateada = htmlspecialchars(date("d/m/Y - H:i", strtotime($row["FechaDeCreacion"])), ENT_QUOTES, 'UTF-8');
        $formulario = htmlspecialchars($row["Formulario"], ENT_QUOTES, 'UTF-8');
        $tipologia = htmlspecialchars($row["Tipologia"], ENT_QUOTES, 'UTF-8');
        $fuente = htmlspecialchars($row["Fuente"], ENT_QUOTES, 'UTF-8');
        $reporteAsociado = htmlspecialchars($row["ReporteAsociado"], ENT_QUOTES, 'UTF-8');

        $html .= '<tr id="fila-' . $formulario . '">
                      <td>' . $formulario . '</td>
                      <td>' . $fechaFormateada . '</td>
                      <td>' . $tipologia . '</td>
                      <td>' . $fuente . '</td>
                      <td>' . $reporteAsociado . '</td>

                      <td>
                          <div class="containerCustomBTN">
                              <div class="cbtn">
                                  <form action="Consultas/Previsualizar_PVE.php" method="POST" target="_blank" style="display:inline;">
                                      <input type="hidden" name="formularioPVE" value="' . $formulario . '">
                                      <input type="submit" class="cbtn cbtnCustom" style="background-color: rgba(30, 255, 69, 0.3);" value="Formulario PVE">
                                  </form>
                              </div>
                          </div>
                      </td>
                  </tr>';
    }

    $html .= '</tbody>
    </table>
    </div>';

    return $html;
}

// Conexión a la base de datos
$conn = open_database_connection('carga_pve');
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener los datos para la página principal
$datosMainPage = fetchDataForTable($conn);
$tablaHTML = generateTable($datosMainPage);

// Cerrar la conexión a la base de datos
$conn->close();
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pagina principal</title>
  <!-- Navicon -->
  <link rel="icon" href="../CSS/Images/favicon.ico" type="Image/x-icon">
  <link rel="shortcut icon" href="../CSS/Images/favicon.ico" type="Image/x-icon">
  <!-- CSS -->
  <link rel="stylesheet" type="text/css" href="../CSS/styles.css">
  <link rel="stylesheet" type="text/css" href="../CSS/WebKit.css">
  <link rel="stylesheet" type="text/css" href="CSS/Button.css">
  <!-- JS -->
  <script src="../Usuarios/JS/logout.js"></script>
  <!-- jQuery -->
  <script src="../Resources/JQuery/jquery-3.7.1.min.js"></script>
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
<body class="bg-secondary" style="background-image: url('../CSS/Images/MainBG.png'); background-size: cover; background-attachment: fixed;">

<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
    <div class="container-fluid d-flex justify-content-between align-items-center">

    <!-- Botón condicional a la izquierda -->
    <?php if ($usergroup == 'ADMINISTRADOR'): ?>
        <div style="position: absolute; left: 0;">
            <button type="button" id="VolverButton" class="btn btn-warning btn-lg mx-3" onclick="window.location.href='../index.php'">
                <i class="bi bi-arrow-left-square-fill"></i> <b>VOLVER</b>
            </button>
        </div>
    <?php else: ?>
        <div style="position: absolute; left: 0;">
            <button type="button" class="btn btn-outline-primary btn-lg fs-4 mx-3">
                <i class="bi bi-person-badge"></i><?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>
            </button>
        </div>
    <?php endif; ?>

        <!-- Bloque del título con imágenes, centrado -->
        <div class="d-flex justify-content-center align-items-center flex-grow-1">
            <img src="../CSS/Images/PSF.png" class="mx-3" alt="Icono" style="width: 4rem;"><!-- Icono Izquierdo -->
            <h2 class="text-light text-center m-0">UNIDAD DE ANÁLISIS DE INTELIGENCIA CRIMINAL</h2>
            <img src="../CSS/Images/LOGO.png" class="mx-3" alt="Icono" style="width: 4rem;"><!-- Icono Derecho -->
        </div>

        <!-- Menú desplegable a la derecha -->
        <div style="position: absolute; right: 0;">
            <div class="collapse navbar-collapse" id="navbarNavDarkDropdown">
              <ul class="navbar-nav">
                <li class="nav-item dropdown">
                  <button class="btn btn-dark btn-lg dropdown-toggle mx-3" data-bs-toggle="dropdown" aria-expanded="false">
                  MENÚ DESPLEGABLE
                  </button>
                  <ul class="dropdown-menu dropdown-menu-dark">
                    <li><a class="dropdown-item fs-4" href="Consultas/index.php">Sistema de consultas</a></li>
                  </ul>
                </li>
              </ul>
            </div>
        </div>
    </div>
</nav>

<!-- Bandeja de entrada -->
<?php echo $tablaHTML; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let tiempoRestante = 300; // 5 minutos en segundos

        const intervalo = setInterval(() => {
            tiempoRestante -= 1;
            const minutos = Math.floor(tiempoRestante / 60);
            const segundos = tiempoRestante % 60;

            if (tiempoRestante <= 0) {
                clearInterval(intervalo);
                window.location.reload(); // Recargar la página
            }
        }, 1000);
    });
</script>

</body>
</html>
