<?php
    require '../../PHP/ServerConnect.php'; // Conectar a la base de datos
    require '../PHP/ArraysManager.php'; // Manejador de arrays
    require '../PHP/DataFetcher.php'; // Clase para recopilar datos

    // Verificar estado del login
    checkLoginState();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reporte</title>
  <!-- Favicon -->
  <link rel="icon" href="../../CSS/Images/favicon.ico" type="Image/x-icon">
  <link rel="shortcut icon" href="../../CSS/Images/favicon.ico" type="Image/x-icon">
  <!-- CSS -->
  <link rel="stylesheet" type="text/css" href="../../CSS/Webkit.css">
  <!-- JS -->
  <script src="JS/previsualizacion.js"></script>
  <script src="JS/handlerReporte.js"></script>
  <!-- jQuery -->
  <script src="../../Resources/JQuery/jquery-3.7.1.min.js"></script>
  <!-- SweetAlert -->
  <script src="../../Resources/SweetAlert2/sweetalert2.min.js"></script>
  <link rel="stylesheet" href="../../Resources/SweetAlert2/sweetalert2.min.css">
  <!-- PDFMake -->
  <script src="../../Resources/PDFMake/pdfmake.min.js"></script>
  <script src="../../Resources/PDFMake/vfs_fonts.js"></script>
  <script src="../JS/HandlerPDFMake.js"></script>
  <script src="../JS/GenerarPDFMake.js"></script>
  <!-- Popper.js -->
  <script src="../../Resources/Popper/popper.min.js"></script>
  <!-- Bootstrap -->
  <script src="../../Resources/Bootstrap/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="../../Resources/Bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../Resources/Bootstrap/Icons/font/bootstrap-icons.css">
</head>

<body class="bg-secondary">

<!-- Barra de navegación -->
<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
    <div class="container-fluid d-flex justify-content-between align-items-center">

        <!-- Bloque del título con imágenes, centrado en la totalidad de la pantalla -->
        <div class="d-flex justify-content-center align-items-center flex-grow-1">
            <img src="../../CSS/Images/PSF.png" alt="Icono" style="width: 4rem;"><!-- Icono Izquierdo -->
            <h2 class="text-light text-center m-0 px-3">SISTEMA DE INVESTIGACIÓN CRIMINAL</h2>
            <img src="../../CSS/Images/LOGO.png" alt="Icono" style="width: 4rem;"><!-- Icono Derecho -->
        </div>

    </div>
</nav>

<script>
    // Función para obtener parámetros de la URL
    function getParameterByName(name, url = window.location.href) {
        name = name.replace(/[\[\]]/g, '\\$&');
        let regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, ' '));
    }

    // Obtener el valor del parámetro `ID` de la URL
    let ID = getParameterByName('ID');
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
    <div id="armasContainer" class="border border-black rounded bg-light p-3 m-3">
    </div>
    <div id="secuestrosContainer" class="border border-black rounded bg-light p-3 m-3">
    </div>
    <div id="disclaimerContainer" class="border border-black rounded bg-light p-3 m-3">
    </div>
</div>

<!-- Botón de cerrar a la izquierda con posición absoluta -->
<button type="button" class="btn btn-danger btn-lg fs-4 mx-3" onclick="window.close()" style="position: fixed; bottom: 10px; left: 10px;">
    <i class="bi bi-backspace-reverse-fill"></i> <b>CERRAR</b>
</button>

<!-- Botón de cerrar a la izquierda con posición absoluta -->
<button type="button" class="btn btn-danger btn-lg fs-4 mx-3" onclick="generarPDF(ID)" style="position: fixed; bottom: 10px; right: 10px;">
    <i class="bi bi-file-earmark-pdf"></i> <b>Generar PDF</b>
</button>

</body>
</html>