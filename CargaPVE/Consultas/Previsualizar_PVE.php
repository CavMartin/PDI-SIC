<?php
require '../../PHP/ServerConnect.php'; // Conectar a la base de datos
require '../PHP/DataFetcher.php'; // Clase para recopilar datos

// Verificar estado del login
checkLoginState();

// Obtener el nombre de usuario del usuario logeado desde la sesión
$usergroup = $_SESSION['usergroup'];

// Verificar si el formularioPVE fue enviado por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['formularioPVE'])) {
    $formularioPVE = $_POST['formularioPVE'];
} else {
    // Manejar el caso en que no se haya recibido formularioPVE, por ejemplo, redirigir o mostrar un error
    die("Error: Valor de formularioPVE no recibido.");
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reporte PVE</title>
  <!-- Favicon -->
  <link rel="icon" href="../../CSS/Images/favicon.ico" type="Image/x-icon">
  <link rel="shortcut icon" href="../../CSS/Images/favicon.ico" type="Image/x-icon">
  <!-- CSS -->
  <link rel="stylesheet" type="text/css" href="../../CSS/Webkit.css">
  <!-- JS -->
  <script src="JS/handlerReporte.js"></script>
  <!-- jQuery -->
  <script src="../../Resources/JQuery/jquery-3.7.1.min.js"></script>
  <!-- SweetAlert -->
  <script src="../../Resources/SweetAlert2/sweetalert2.min.js"></script>
  <link rel="stylesheet" href="../../Resources/SweetAlert2/sweetalert2.min.css">
  <!-- Bootstrap -->
  <script src="../../Resources/Bootstrap/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="../../Resources/Bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../Resources/Bootstrap/Icons/font/bootstrap-icons.css">
</head>

<body class="bg-secondary">

<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <!-- Bloque del título con imágenes, centrado -->
        <div class="d-flex justify-content-center align-items-center flex-grow-1">
            <img src="../../CSS/Images/LOGO2.png" class="mx-3" alt="Icono" style="width: 4rem;"><!-- Icono Izquierdo -->
            <h2 class="text-light text-center m-0">UNIDAD DE ANÁLISIS DE INTELIGENCIA CRIMINAL</h2>
            <img src="../../CSS/Images/LOGO.png" class="mx-3" alt="Icono" style="width: 4rem;"><!-- Icono Derecho -->
        </div>
    </div>
</nav>

<script>
    // Definir formularioPVE como una variable global
    window.formularioPVE = <?php echo json_encode($formularioPVE); ?>;
</script>


<!-- Espacio para el contenido principal de la página -->
<div class="container mt-5 pt-5">
    <!-- Los datos de la incidencia priorizada se insertarán aquí dinámicamente -->
    <div id="encabezadoContainer" class="border border-black rounded bg-light p-3 m-3">
    </div>
    <div id="lugaresContainer" class="border border-black rounded bg-light p-3 m-3">
    </div>
    <div id="personasContainer" class="border border-black rounded bg-light p-3 m-3">
    </div>
    <div id="vehiculosContainer" class="border border-black rounded bg-light p-3 m-3">
    </div>
    <div id="disclaimerContainer" class="border border-black rounded bg-light p-3 m-3">
    </div>
</div>

<!-- Botón de cerrar a la izquierda con posición absoluta -->
<button type="button" class="btn btn-danger btn-lg fs-4 mx-3" onclick="window.close()" style="position: fixed; bottom: 10px; left: 10px;">
    <i class="bi bi-backspace-reverse-fill"></i> <b>CERRAR</b>
</button>

<?php
    // Verifica si el rol del usuario es igual a 'ADMINISTRADOR' o 'UAIC - PVE' o 'URII'
    if ($usergroup == 'ADMINISTRADOR' || $usergroup == 'UAIC - PVE' || $usergroup == 'URII') {
        echo '<div>
                  <form id="editarForm" action="../Formulario.php" method="POST" style="display: none;">
                      <input type="hidden" name="formularioPVE" id="formularioPVEInput">
                  </form>
                      
                  <!-- Botón de editar -->
                  <button type="button" class="btn btn-primary btn-lg fs-4 mx-3" style="position: fixed; bottom: 10px; right: 10px;" onclick="submitForm()">
                      <i class="bi bi-search"></i> <b>EDITAR</b>
                  </button>
                      
                  <script>
                      function submitForm() {
                          // Asigna el valor del parámetro `formularioPVE` al campo oculto del formulario
                          document.getElementById(\'formularioPVEInput\').value = formularioPVE;
                          // Envía el formulario
                          document.getElementById(\'editarForm\').submit();
                      }
                  </script>
              </div>';
      }
  ?>

</body>
</html>