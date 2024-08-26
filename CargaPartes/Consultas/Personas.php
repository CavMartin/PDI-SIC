<?php
    // Conectar a la base de datos de forma segura
    require '../../PHP/ServerConnect.php';
    require '../PHP/ArraysManager.php';

    // Verificar estado del login
    checkLoginState();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Consultas - Personas</title>
  <!-- Favicon -->
  <link rel="icon" href="../../CSS/Images/favicon.ico" type="Image/x-icon">
  <link rel="shortcut icon" href="../../CSS/Images/favicon.ico" type="Image/x-icon">
  <!-- CSS -->
  <link rel="stylesheet" type="text/css" href="../../CSS/Webkit.css">
  <!-- JQuery -->
  <script src="../../Resources/JQuery/jquery-3.7.1.min.js"></script>
  <!-- SweetAlert -->
  <script src="../../Resources/SweetAlert2/sweetalert2.min.js"></script>
  <link rel="stylesheet" href="../../Resources/SweetAlert2/sweetalert2.min.css">
  <!-- Bootstrap -->
  <script src="../../Resources/Bootstrap/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="../../Resources/Bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../Resources/Bootstrap/Icons/font/bootstrap-icons.css">
  <!-- DataTables -->
  <script src="../../Resources/DataTables/datatables.min.js"></script>
  <!-- JS -->
  <script src="JS/previsualizacion.js"></script>
  <script src="JS/queryPersonas.js"></script>
  <script src="../../JS/TransformarDatos.js"></script>
</head>
<body class="bg-secondary">

<!-- Barra de navegación -->
<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
    <div class="container-fluid d-flex justify-content-between align-items-center">

        <!-- Botón de navegación a la página principal -->
        <div style="position: absolute; left: 0;">
            <button type="button" id="VolverButton" class="btn btn-warning btn-lg m-3" onclick="window.location.href='index.php'">
                <i class="bi bi-arrow-left-square-fill"></i> <b>VOLVER</b>
            </button>
        </div>

        <!-- Bloque del título con imágenes, centrado en la totalidad de la pantalla -->
        <div class="d-flex justify-content-center align-items-center flex-grow-1">
            <img src="../../CSS/Images/PSF.png" alt="Icono" style="width: 4rem;"><!-- Icono Izquierdo -->
            <h2 class="text-light text-center m-0 px-3">SISTEMA DE CONSULTAS - PERSONAS</h2>
            <img src="../../CSS/Images/LOGO.png" alt="Icono" style="width: 4rem;"><!-- Icono Derecho -->
        </div>

        <!-- Botón de busqueda a la derecha -->
        <div style="position: absolute; right: 0;">
            <button type="button" class="btn btn-primary btn-lg mx-3" id="consultaBtn">
                <i class="bi bi-search"></i> <b>BUSCAR</b>
            </button>
        </div>

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
                        <span class="input-group-text fw-bold">APELLIDO:</span>
                        <input type="text" class="form-control" id="P_Apellido" name="P_Apellido" maxlength="50" onchange="transformarDatosMayusculas('P_Apellido')">
                    </div>
                </div>
                <div class="col">
                    <div class="input-group">
                        <span class="input-group-text fw-bold">NOMBRE:</span>
                        <input type="text" class="form-control" id="P_Nombre" name="P_Nombre" maxlength="50" onchange="transformarDatosNompropio('P_Nombre')">
                    </div>
                </div>
                <div class="col">
                    <div class="input-group">
                        <span class="input-group-text fw-bold">ALIAS:</span>
                        <input type="text" class="form-control" id="P_Alias" name="P_Alias" maxlength="50" onchange="transformarDatosNompropio('P_Alias')">
                    </div>
                </div>
                <div class="col">
                    <div class="input-group">
                        <span class="input-group-text fw-bold">DNI:</span>
                        <input type="text" class="form-control" id="P_DNI" name="P_DNI" maxlength="10" onchange="transformarDatosNumerico('P_DNI')">
                    </div>
                </div>
            </div>
            <!-- Segundo bloque de inputs -->
            <div class="row">
                <div class="col">
                    <div class="input-group">
                        <label class="input-group-text fw-bold" for="P_Rol">ROL:</label>
                        <select class="form-select" id="P_Rol" name="P_Rol">
                            <?php
                                echo optionsSearchSelect($Array_PersonaRol);
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col">
                    <div class="input-group">
                        <label class="input-group-text fw-bold" for="P_Genero">GÉNERO:</label>
                        <select id="P_Genero" class="form-select" name="P_Genero">
                            <?php
                                echo optionsSearchSelect($Array_Genero);
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col">
                    <div class="input-group">
                        <label class="input-group-text fw-bold" for="P_EstadoCivil">ESTADO CIVIL:</label>
                        <select id="P_EstadoCivil" class="form-select" name="P_EstadoCivil">
                            <?php
                                echo optionsSearchSelect($Array_EstadoCivil);
                            ?>
                        </select>
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
                            <th>INFORME</th>
                            <th>ROL</th>
                            <th>APELLIDO</th>
                            <th>NOMBRE</th>
                            <th>ALIAS</th>
                            <th>DNI</th>
                            <th>GÉNERO</th>
                            <th>ESTADO CIVIL</th>
                            <th>PREVISUALIZAR</th>
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

<!-- Estructura básica de la ventana modal -->
<div id="ventanaModalEntidad" class="modal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-success">
        <h3 class="modal-title text-white"><i class="bi bi-eye"></i> PREVISUALIZACIÓN - Detalles de la persona</h3>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row">
            <!-- Imagen de la persona centrada horizontalmente -->
            <div class="col-md-4 d-flex justify-content-center">
                <img id="ModalFotoPersona" src="" alt="Foto de la Persona" class="img-fluid" style="height: 300px; width: 300px; border-radius: 50%; border: 0.3vw solid rgb(0, 0, 0);">
            </div>
            <!-- Detalles de la persona -->
            <div class="col-md-7">
                <p class="fs-5 mb-3" id="ModalRol"></p>
                <p class="fs-5 mb-3" id="ModalApellido"></p>
                <p class="fs-5 mb-3" id="ModalNombre"></p>
                <p class="fs-5 mb-3" id="ModalAlias"></p>
                <p class="fs-5 mb-3" id="ModalDNI"></p>
                <p class="fs-5 mb-3" id="ModalGenero"></p>
                <p class="fs-5 mb-3" id="ModalEstadoCivil"></p>
            </div>
            <!-- Contenedor para Domicilios -->
            <div class="col-12">
                <div id="contenedorDomicilios"></div>
            </div>
            <!-- Contenedor para Datos Complementarios -->
            <div class="col-12">
                <div id="contenedorDatosComplementarios"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>




</body>
</html>
