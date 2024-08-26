<?php
    // Importar configuraciones y funciones necesarias
    require '../PHP/ServerConnect.php';
    require 'PHP/MainPageHandler.php';

    // Verificar sesión iniciada
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        // El usuario no ha iniciado sesión, redirigirlo a la página de inicio de sesión
        header("Location: Login.php");
        exit();
    }

    // Verificar rol de usuario
    if (isset($_SESSION['userrole']) && $_SESSION['userrole'] === 1) {
        // Si el rol es igual a 1, habilita los ajustes del INI para mostrar errores en pantalla
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }

    // Obtener el nombre de usuario y grupo al que pertenece el usuario logeado desde la sesión
    $userrole = $_SESSION['userrole'];
    $usergroup = $_SESSION['usergroup'];

    // Conexión a la base de datos
    $conn = open_database_connection('sic');
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    // Instanciar MainPageHandler con la conexión a la base de datos
    $mainPageHandler = new MainPageHandler($conn);

    // Obtener los datos para la página principal
    $datosMainPage = $mainPageHandler->fetchDataForMainPage();
    $tablaHTML = $mainPageHandler->generateTableForMainPage($datosMainPage);

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
  <link rel="stylesheet" type="text/css" href="../CSS/CustomButtons.css">
  <!-- JS -->
  <script src="JS/mainPage.js"></script>
  <!-- jQuery -->
  <script src="../Resources/JQuery/jquery-3.7.1.min.js"></script>
  <!-- PDFMake -->
  <script src="../Resources/PDFMake/pdfmake.min.js"></script>
  <script src="../Resources/PDFMake/vfs_fonts.js"></script>
  <script src="JS/HandlerPDFMake.js"></script>
  <script src="JS/GenerarPDFMake.js"></script>
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

        <!-- Botón de grupo a la izquierda -->
        <div style="position: absolute; left: 0;">
            <button type="button" id="VolverButton" class="btn btn-warning btn-lg mx-3" onclick="window.location.href='../index.php'">
                <i class="bi bi-arrow-left-square-fill"></i> <b>VOLVER</b>
            </button>
        </div>

        <!-- Bloque del título con imágenes, centrado -->
        <div class="d-flex justify-content-center align-items-center flex-grow-1">
            <img src="../CSS/Images/PSF.png" class="mx-3" alt="Icono" style="width: 4rem;"><!-- Icono Izquierdo -->
            <h2 class="text-light text-center m-0">SISTEMA DE INVESTIGACIÓN CRIMINAL - PARTES</h2>
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
                    <?php
                      // Verifica si el rol del usuario es igual o menor a 3 y muestra el boton nuevo formulario
                      if ($userrole <= 3 ) {
                          echo '<li><a class="dropdown-item fs-4" href="#" onclick="formNuevaIncidencia()">Nuevo formulario</a></li>';
                      }
                    ?>
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

