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
  <title>Consultas - Lugares</title>
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
  <script src="JS/queryLugares.js"></script>
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
            <h2 class="text-light text-center m-0 px-3">SISTEMA DE CONSULTAS - LUGARES</h2>
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
            <div class="row">
                <div class="col-md-4">
                    <div class="input-group mb-3">
                        <label class="input-group-text fw-bold" for="L_Rol">ROL DEL LUGAR:</label>
                        <select class="form-select" id="L_Rol" name="L_Rol">
                            <option value="">Ninguna opción especificada</option>
                            <option value="Lugar del hecho">Lugar del hecho</option>
                            <option value="Lugar de finalización del hecho">Lugar de finalización del hecho</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group mb-3">
                        <span class="input-group-text fw-bold">CALLE:</span>
                        <input type="text" class="form-control" id="L_Calle" name="L_Calle" maxlength="50" onchange="transformarDatosNompropio('L_Calle')">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <span class="input-group-text fw-bold">DESDE:</span>
                                <input type="text" class="form-control" id="L_AlturaDesde" name="L_AlturaDesde" maxlength="5" onchange="transformarDatosNumerico('L_AlturaDesde')">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <span class="input-group-text fw-bold">HASTA:</span>
                                <input type="text" class="form-control" id="L_AlturaHasta" name="L_AlturaHasta" maxlength="5" onchange="transformarDatosNumerico('L_AlturaHasta')">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Segundo bloque de inputs -->
            <div class="row">
                <div class="col">
                    <div class="input-group">
                        <label class="input-group-text fw-bold" for="L_TipoLugar">CLASIFICACIÓN DEL LUGAR:</label>
                        <select id="L_TipoLugar" class="form-select" name="L_TipoLugar">
                            <?php
                                echo optionsSearchSelect($Array_TipoLugar);
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col">
                    <div class="input-group">
                        <span class="input-group-text fw-bold">BARRIO:</span>
                        <input type="text" class="form-control" id="L_Barrio" name="L_Barrio" maxlength="50" onchange="transformarDatosNompropio('L_Barrio')">
                    </div>
                </div>
                <div class="col">
                    <div class="input-group">
                        <span class="input-group-text fw-bold">LOCALIDAD:</span>
                        <input type="text" class="form-control" id="L_Localidad" name="L_Localidad" maxlength="50" onchange="transformarDatosNompropio('L_Localidad')">
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
                            <th style="min-width: 8rem;">ROL</th>
                            <th style="min-width: 15rem;">DIRECCIÓN</th>
                            <th>BARRIO</th>
                            <th>LOCALIDAD</th>
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

<!-- Ventana modal para lugares del hecho -->
<div id="ventanaModalEntidad" class="modal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-success">
        <h3 class="modal-title text-white"><i class="bi bi-eye"></i> PREVISUALIZACIÓN - Detalles del lugar</h3>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
            <!-- Detalles del lugar del hecho -->
            <div class="col">
                <div class="fs-5 border border-black rounded mb-2 mx-2 p-2">
                    <div class="row fs-5">
                        <p class="col" id="ModalRol"></p>
                        <p class="col" id="ModalTipo"></p>
                    </div>
                    <div class="row fs-5">
                        <p class="col" id="ModalDomicilio"></p>
                    </div>
                    <div class="row fs-5">
                        <p class="col" id="ModalBarrio"></p>
                        <p class="col" id="ModalLocalidad"></p>
                        <p class="col" id="ModalProvincia"></p>
                    </div>
                </div>
            </div>
            <!-- Contenedor para entidad relacionada -->
            <div class="col-12">
                <div id="contenedorSecundario"></div>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
