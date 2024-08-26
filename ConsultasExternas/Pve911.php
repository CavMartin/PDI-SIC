<?php
    // Conectar a la base de datos de forma segura
    require '../PHP/ServerConnect.php';

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        // El usuario no ha iniciado sesión, redirigirlo a la página de inicio de sesión
        header("Location: ../Login.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Consultas - Pve 911</title>
  <!-- Favicon -->
  <link rel="icon" href="../CSS/Images/favicon.ico" type="Image/x-icon">
  <link rel="shortcut icon" href="../CSS/Images/favicon.ico" type="Image/x-icon">
  <!-- CSS -->
  <link rel="stylesheet" type="text/css" href="../CSS/Webkit.css">
  <!-- JQuery -->
  <script src="../Resources/JQuery/jquery-3.7.1.min.js"></script>
  <!-- SweetAlert -->
  <script src="../Resources/SweetAlert2/sweetalert2.min.js"></script>
  <link rel="stylesheet" href="../Resources/SweetAlert2/sweetalert2.min.css">
  <!-- Bootstrap -->
  <script src="../Resources/Bootstrap/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="../Resources/Bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../Resources/Bootstrap/Icons/font/bootstrap-icons.css">
  <!-- DataTables -->
  <script src="../Resources/DataTables/datatables.min.js"></script>
  <!-- JS -->
  <script src="JS/queryPve911.js"></script>
</head>
<body class="bg-secondary">

<!-- Barra de navegación -->
<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
    <div class="container-fluid d-flex justify-content-between align-items-center">

        <!-- Botón de volver a la izquierda con posición absoluta -->
        <div style="position: absolute; left: 0;">
            <button type="button" id="VolverButton" class="btn btn-warning btn-lg mx-3" onclick="window.location.href='index.php'">
                <i class="bi bi-arrow-left-square-fill"></i> <b>VOLVER</b>
            </button>
        </div>

        <!-- Bloque del título con imágenes, centrado en la totalidad de la pantalla -->
        <div class="d-flex justify-content-center align-items-center flex-grow-1">
            <img src="../CSS/Images/PSF.png" alt="Icono" style="width: 4rem;"><!-- Icono Izquierdo -->
            <h2 class="text-light text-center m-0 px-3">SISTEMA DE CONSULTAS - PVE 911</h2>
            <img src="../CSS/Images/LOGO.png" alt="Icono" style="width: 4rem;"><!-- Icono Derecho -->
        </div>

        <!-- Botón de busqueda a la derecha -->
        <button type="button" class="btn btn-primary btn-lg fs-4" id="consultaBtn">
            <i class="bi bi-search"></i> <b>BUSCAR</b>
        </button>
    </div>
</nav>

<!-- Contenido -->
<div class="container-fluid p-3" style="margin-top: 5rem;">
    <div class="row">
        <div class="col-12">
            <!-- Primer bloque de inputs -->
            <div class="row mb-3">
                <div class="col">
                    <div class="input-group">
                        <span class="input-group-text fw-bold">FECHA DESDE:</span>
                        <input type="date" class="form-control" id="FechaDesde" name="FechaDesde">
                    </div>
                </div>
                <div class="col">
                    <div class="input-group">
                        <span class="input-group-text fw-bold">FECHA HASTA:</span>
                        <input type="date" class="form-control" id="FechaHasta" name="FechaHasta">
                    </div>
                </div>
                <div class="col">
                    <div class="input-group">
                        <span class="input-group-text fw-bold">NOTA:</span>
                        <input type="text" class="form-control" id="Nota" name="Nota" maxlength="50">
                    </div>
                </div>
                <div class="col">
                    <div class="input-group">
                        <span class="input-group-text fw-bold">REGIÓN:</span>
                        <input type="text" class="form-control" id="Region" name="Region" maxlength="50">
                    </div>
                </div>
                <div class="col">
                    <div class="input-group">
                        <span class="input-group-text fw-bold">LOCALIDAD:</span>
                        <input type="text" class="form-control" id="Localidad" name="Localidad" maxlength="50">
                    </div>
                </div>
            </div>
            <!-- Segundo bloque de inputs -->
            <div class="row">
                <div class="col">
                    <div class="input-group">
                        <span class="input-group-text fw-bold">DIRECCIÓN:</span>
                        <input type="text" class="form-control" id="Direccion" name="Direccion" maxlength="50">
                    </div>
                </div>
                <div class="col">
                    <div class="input-group">
                        <span class="input-group-text fw-bold">TIPIFICACIÓN:</span>
                        <input type="text" class="form-control" id="Tipificacion" name="Tipificacion" maxlength="50">
                    </div>
                </div>
                <div class="col">
                    <div class="input-group">
                        <span class="input-group-text fw-bold">DENUNCIADOS:</span>
                        <input type="text" class="form-control" id="Denunciados" name="Denunciados" maxlength="50">
                    </div>
                </div>
                <div class="col">
                    <div class="input-group">
                        <span class="input-group-text fw-bold">RELATO:</span>
                        <input type="text" class="form-control" id="Relato" name="Relato" maxlength="50">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <!-- Tabla -->
            <div class="mt-3">
                <table id="queryTable" class="table table-bordered table-hover p-3" style="vertical-align: middle; width: 100%;">
                    <thead> 
                        <tr class="table-dark fs-5 text-center" style="vertical-align: middle;">
                            <th style="max-width: 4rem;">FECHA</th>
                            <th style="max-width: 4rem;">NOTA</th>
                            <th style="max-width: 8rem;">TIPIFICACIÓN</th>
                            <th style="max-width: 8rem;">DIRECCIÓN</th>
                            <th style="max-width: 6rem;">LOCALIDAD</th>
                            <th style="max-width: 6rem;">0800/911</th>
                            <th>DENUNCIADOS</th>
                            <th style="min-width: 40rem;">RELATO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Los datos de las filas se agregarán dinámicamente aquí con la solicitud AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>
