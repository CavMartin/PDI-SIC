<?php
// Conectar a la base de datos de forma segura
require 'PHP/ServerConnect.php';
require 'Formularios/Infractores/PHP/DataFetcherPDF.php'; // Clase para recopilar datos para el PDF

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // El usuario no ha iniciado sesión, redirigirlo a la página de inicio de sesión
    header("Location: Login.php");
    exit();
}

// Verifica si el rol de usuario almacenado en la sesión es igual a 1
if (isset($_SESSION['rolUsuario']) && $_SESSION['rolUsuario'] === 1) {
    // Si el rol es igual a 1, habilita los ajustes del INI para mostrar errores en pantalla
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// Establece la conexión a la base de datos
$conn = open_database_connection();
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
// Utilizar una sentencia preparada
$sql = "SELECT ID, 
               Tipo,
               Estado,
               FechaDeCreacion 
        FROM sistema_planilla_infractores
        WHERE Estado = 1
        ORDER BY FechaDeCreacion ASC 
        LIMIT 25";

$stmt = $conn->prepare($sql);

if ($stmt) {
    // Ejecutar la consulta
    $stmt->execute();

    // Obtener los resultados
    $result = $stmt->get_result();
    
    // Cerrar la sentencia preparada
    $stmt->close();
} else {
    // Manejo de errores si la preparación de la consulta falla
    echo "Error en la preparación de la consulta.";
}

// Función para cerrar o reabrir una incidencia
function actualizarEstadoIncidencia($incidenciaID, $nuevoEstado) {
    global $conn;
    
    // Actualiza el estado de la incidencia
    $sql = "UPDATE sistema_dispositivo_siacip SET Estado = ? WHERE ID = ?";
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
                header("Location: Main.php");
                exit();
            }
        }
    }
// Cerrar la conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina principal</title>
    <!-- Navicon -->
    <link rel="icon" href="CSS/Images/favicon.ico" type="Image/x-icon">
    <link rel="shortcut icon" href="CSS/Images/favicon.ico" type="Image/x-icon">
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="CSS/styles.css">
    <!-- JS -->
    <script src="JS/Main.js"></script>
    <!-- SweetAlert -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- PDFMake -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.9/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.9/vfs_fonts.js"></script>
    <script src="Formularios/Infractores/JS/HandlerPDFMake.js"></script>
    <script src="Formularios/Infractores/JS/GenerarPDFMake.js"></script>
    <!-- Popper.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.10.2/umd/popper.min.js"></script>
    <!-- Bootstrap -->
    <script src="Bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="Bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="Bootstrap/Icons/font/bootstrap-icons.css">
</head>
<body class="BodyMain">

<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark" style="height: 5vw;">
    <div class="container-fluid d-flex align-items-center justify-content-center">
        <!-- Imagen 1 -->
        <div>
            <img src="CSS/Images/PSF.png" alt="Icono" style="width: 4vw; margin: 1vw;"><!-- Icono -->
        </div>

        <!-- Título centrado -->
        <div class="text-center">
            <h2 class="text-light">SISTEMA DE INVESTIGACIÓN CRIMINAL</h1>
        </div>

        <!-- Imagen 2 -->
        <div>
            <img src="CSS/Images/OJO.png" alt="Icono" style="width: 4vw; margin: 1vw;"><!-- Icono -->
        </div>

        <!-- Botón de logout -->
        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <form method="post" action="Logout.php">
                <button type="submit" id="Logout" name="Logout" class="btn btn-danger btn-lg me-2 fs-4" style="position: fixed; top: 0; left: 0; width: 12vw; height: 4vw; font-size: 1.5vw; margin: 0.5vw;">CERRAR SESIÓN</button>
            </form>
        </div>

        <!-- Menú desplegable -->
        <div class="collapse navbar-collapse" id="navbarNavDarkDropdown" style="position: fixed; top: 1vw; right:10vw; width: 5vw;">
          <ul class="navbar-nav">
            <li class="nav-item dropdown">
              <button class="btn btn-dark btn-lg dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                MENÚ DESPLEGABLE
              </button>
              <ul class="dropdown-menu dropdown-menu-dark">
                <li><a class="dropdown-item fs-4" href="#" onclick="nuevoFormulario()">Nuevo formulario</a></li>
                <li><a class="dropdown-item fs-4" href="Usuarios/Main.php">Panel de usuario</a></li>
                <li><a class="dropdown-item fs-4" href="Consultas/Main.php">Sistema de consultas</a></li>
                <li><a class="dropdown-item fs-4" href="GIS/Main.php">SIG - En construcción</a></li>
              </ul>
            </li>
          </ul>
        </div>
    </div>
</nav>

<!-- Bandeja de entrada -->
<div class="MainTable">
<table>
    <thead>
        <tr>
            <th style="Width: 10vw;">ID</th>
            <th style="Width: 10vw;">ESTADO</th>
            <th style="Width: 20vw;">TIPO DE FORMULARIO</th>
            <th style="Width: 20vw;">FECHA DE CREACIÓN</th>
            <th style="Width: 40vw;">ACCIONES</th>
        </tr>
    </thead>
    <tbody>
        <?php
        while ($row = $result->fetch_assoc()) {
            // Formatea la fecha y hora en "DD/MM/AAAA Hora:Minuto:Segundo"
            $fechaFormateada = date("d/m/Y H:i:s", strtotime($row["FechaDeCreacion"]));
        
            echo '<tr>'; // Abrir tr de la fila dinamica
            echo '<td style="Width: 10vw;">' . htmlspecialchars($row["ID"], ENT_QUOTES, 'UTF-8') . '</td>';

            echo '<td style="Width: 10vw;">';
            // Formulario para Cerrar Incidencia
            echo '<form action="Main.php" method="POST" style="display:inline;" onsubmit="return ConfirmacionCerrarIncidencia(\'' . htmlspecialchars($row["ID"], ENT_QUOTES, 'UTF-8') . '\');">';
            echo '<input type="hidden" name="CerrarIncidencia" value="' . htmlspecialchars($row["ID"], ENT_QUOTES, 'UTF-8') . '">';
            echo '<button class="ESTADO_BTN ABIERTO"><span class="NORMAL_TEXT">ABIERTO</span><span class="ON_HOVER">CERRAR INCIDENCIA</span></button>';
            echo '</form>' . '</td>';            

            echo '<td style="Width: 20vw;">' . htmlspecialchars($row["Tipo"], ENT_QUOTES, 'UTF-8') . '</td>';
            echo '<td style="Width: 20vw;">' . htmlspecialchars($fechaFormateada, ENT_QUOTES, 'UTF-8') . '</td>';

            // Agregamos botones para cada fila
            echo '<td style="Width: 40vw;">';

                // Formulario para la carga del formulario
                echo '<form action="Formularios/Infractores/Main.php" method="POST" style="display:inline;">';
                echo '<input type="hidden" name="ID" value="' . htmlspecialchars($row["ID"], ENT_QUOTES, 'UTF-8') . '">';
                echo '<input type="submit" class="btn btn-lg btn-primary m-2" value="FORMULARIO">';
                echo '</form>';

                // Boton para general el PDF
                echo '<button type="button" class="btn btn-lg btn-danger m-2" id="generatePDF" onclick="generarPDF(\'' . htmlspecialchars($row["ID"], ENT_QUOTES, 'UTF-8') . '\')">GENERAR PDF</button>';

            echo '</td>';
            echo '</div>';

            echo '</tr>';// Cerrar tr de la fila dinamica
        }
        ?>
    </tbody>
</table>
</div>

</body>
</html>

