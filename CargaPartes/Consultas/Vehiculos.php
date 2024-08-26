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
  <script src="JS/queryVehiculos.js"></script>
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
            <h2 class="text-light text-center m-0 px-3">SISTEMA DE CONSULTAS - VEHÍCULOS</h2>
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
                        <label class="input-group-text fw-bold" for="V_Rol">ROL DE VEHÍCULO:</label>
                        <select class="form-select" id="V_Rol" name="V_Rol">
                            <?php
                                echo optionsSearchSelect($Array_RolVehiculo);
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col">
                    <div class="input-group">
                        <label class="input-group-text fw-bold" for="V_TipoVehiculo">TIPO DE VEHÍCULO:</label>
                        <select id="V_TipoVehiculo" class="form-select" name="V_TipoVehiculo">
                            <?php
                                echo optionsSearchSelect($Array_TipoVehiculo);
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <!-- Segundo bloque de inputs -->
            <div class="row mb-3">
                <div class="col">
                    <div class="input-group">
                        <span class="input-group-text fw-bold">MARCA:</span>
                        <input type="text" class="form-control" id="V_Marca" name="V_Marca" maxlength="50" onchange="transformarDatosNompropio('V_Marca')">
                    </div>
                </div>
                <div class="col">
                    <div class="input-group">
                        <span class="input-group-text fw-bold">MODELO:</span>
                        <input type="text" class="form-control" id="V_Modelo" name="V_Modelo" maxlength="50" onchange="transformarDatosNompropio('V_Modelo')">
                    </div>
                </div>
                <div class="col">
                    <div class="input-group">
                        <span class="input-group-text fw-bold">AÑO:</span>
                        <input type="text" class="form-control" id="V_Año" name="V_Año" maxlength="10" onchange="transformarDatosNumerico('V_Año')">
                    </div>
                </div>
                <div class="col">
                    <div class="input-group">
                        <span class="input-group-text fw-bold">COLOR:</span>
                        <input type="text" class="form-control" id="V_Color" name="V_Color" maxlength="50" onchange="transformarDatosNompropio('V_Color')">
                    </div>
                </div>
            </div>
            <!-- Tercer bloque de inputs -->
            <div class="row">
                <div class="col">
                    <div class="input-group">
                        <span class="input-group-text fw-bold">DOMINIO:</span>
                        <input type="text" class="form-control" id="V_Dominio" name="V_Dominio" maxlength="50" onchange="transformarDatosAlfaNumerico('V_Dominio')">
                    </div>
                </div>
                <div class="col">
                    <div class="input-group">
                        <span class="input-group-text fw-bold">NÚMERO DEL CHASIS:</span>
                        <input type="text" class="form-control" id="V_NumeroChasis" name="V_NumeroChasis" maxlength="50" onchange="transformarDatosAlfaNumerico('V_NumeroChasis')">
                    </div>
                </div>
                <div class="col">
                    <div class="input-group">
                        <span class="input-group-text fw-bold">NÚMERO DEL MOTOR:</span>
                        <input type="text" class="form-control" id="V_NumeroMotor" name="V_NumeroMotor" maxlength="50" onchange="transformarDatosAlfaNumerico('V_NumeroMotor')">
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
                            <th>TIPO</th>
                            <th>MARCA</th>
                            <th>MODELO</th>
                            <th>AÑO</th>
                            <th>COLOR</th>
                            <th>DOMINIO</th>
                            <th>NÚMERO DE CHASIS</th>
                            <th>NÚMERO DE MOTOR</th>
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
        <h3 class="modal-title text-white"><i class="bi bi-eye"></i> PREVISUALIZACIÓN - Detalles del vehículo</h3>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
            <!-- Detalles del vehículo -->
            <div class="col">
                <div class="fs-5 border border-black rounded mb-2 mx-2 p-2">
                  <div class="row fs-5">
                    <p class="col" id="ModalRol"></p>
                  </div>
                  <div class="row fs-5">
                    <p class="col" id="ModalTipo"></p>
                    <p class="col" id="ModalMarca"></p>
                    <p class="col" id="ModalModelo"></p>
                  </div>
                  <div class="row fs-5">
                    <p class="col" id="ModalAño"></p>
                    <p class="col" id="ModalColor"></p>
                    <p class="col" id="ModalDominio"></p>
                  </div>
                  <div class="row fs-5">
                    <p class="col" id="ModalMotor"></p>
                    <p class="col" id="ModalChasis"></p>
                  </div>
                </div>
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
