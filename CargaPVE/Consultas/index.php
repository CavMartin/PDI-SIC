<?php
// Conectar a la base de datos de forma segura
require '../../PHP/ServerConnect.php';

// Verificar estado del login
checkLoginState();

// Cargar la variable de sesión "usergroup"
$usergroup = $_SESSION['usergroup'];
$userrole = isset($_SESSION['userrole']) ? $_SESSION['userrole'] : 4;

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Consultas - PVE</title>
  <!-- Favicon -->
  <link rel="icon" href="../../CSS/Images/favicon.ico" type="Image/x-icon">
  <link rel="shortcut icon" href="../../CSS/Images/favicon.ico" type="Image/x-icon">
  <!-- CSS -->
  <link rel="stylesheet" type="text/css" href="../../CSS/Webkit.css">
  <link rel="stylesheet" type="text/css" href="../../CSS/Card.scss">
  <!-- JQuery -->
  <script src="../../Resources/JQuery/jquery-3.7.1.min.js"></script>
  <!-- SweetAlert -->
  <script src="../../Resources/SweetAlert2/sweetalert2.min.js"></script>
  <link rel="stylesheet" href="../../Resources/SweetAlert2/sweetalert2.min.css">
  <!-- Popper.js -->
  <script src="../../Resources/Popper/popper.min.js"></script>
  <!-- Bootstrap -->
  <script src="../../Resources/Bootstrap/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="../../Resources/Bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../Resources/Bootstrap/Icons/font/bootstrap-icons.css">
  <!-- DataTables -->
  <script src="../../Resources/DataTables/datatables.min.js"></script>
  <!-- JS -->
  <script src="JS/queryPVE.js"></script>
  <script src="JS/listasMultiples.js"></script>
  <script src="JS/entidadesSecundarias.js"></script>
  <script src="JS/handlerSections.js"></script>
  <script src="JS/handlerTable.js"></script>
  <script src="JS/handlerMap.js"></script>
  <script src="JS/previsualizacion.js"></script>
  <!-- Selectize -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/selectize/dist/css/selectize.css" />
  <script src="https://cdn.jsdelivr.net/npm/selectize/dist/js/standalone/selectize.min.js"></script>
  <!-- Leaflet -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"/>
  <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
  <!-- Leaflet - Bootstrap-dropdowns -->
  <script src="../../Resources/Leaflet/BootstrapDropdown/leaflet-bootstrap-dropdowns.min.js"></script>
  <!-- Leaflet - EasyPrint -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet-easyprint@2.1.9/libs/leaflet.min.css"/>
  <script src="https://cdn.jsdelivr.net/npm/leaflet-easyprint@2.1.9/dist/bundle.min.js"></script>
  <!-- Leaflet - MarkerCluster -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css"/>
  <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css"/>
  <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
  <!-- Leaflet - HeatMap -->
  <script src="https://cdn.jsdelivr.net/npm/heatmap.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/leaflet-heatmap/leaflet-heatmap.js"></script>
  <!-- Leaflet - LayerControl -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet-groupedlayercontrol/0.6.1/leaflet.groupedlayercontrol.min.css"/>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-groupedlayercontrol/0.6.1/leaflet.groupedlayercontrol.min.js"></script>
  <!-- Leaflet - Copy-Coordinates-Control -->
  <script src="../../Resources/Leaflet/CopyCoordinates/controlCoordinates.js"></script>
  <link rel="stylesheet" href="../../Resources/Leaflet/CopyCoordinates/controlCoordinates.css">
  <!-- Leaflet - FullScreen -->
  <script src='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js'></script>
  <link href='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css' rel='stylesheet'/>
  <!-- Leaflet - Search -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet-search/dist/leaflet-search.min.css" />
  <script src="https://unpkg.com/leaflet-search/dist/leaflet-search.min.js"></script>
  <!-- Leaflet - Draw -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css"/>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
  <!-- Leaflet - ContextMenu -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet-contextmenu/1.4.0/leaflet.contextmenu.min.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-contextmenu/1.4.0/leaflet.contextmenu.min.js"></script>
  <!-- Exportar Datos CSV / XLSX / DOC-->
  <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.21/lodash.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/xlsx@0.17.0/dist/xlsx.full.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/docx@8.5.0/build/index.umd.min.js"></script>
  
  <style>
    .dt-search {
        display: none; /* Esto ocultará el cuadro de búsqueda predeterminado de dataTables */
    }
    td.details-control {
        background: url('https://datatables.net/examples/resources/details_open.png') no-repeat center center;
        cursor: pointer;
    }
    tr.shown td.details-control {
        background: url('https://datatables.net/examples/resources/details_close.png') no-repeat center center;
    }
    #mapContainer {
        height: calc(100vh - 5rem); /* Asegurar que el contenedor tenga un tamaño fijo */
        margin-top: 5rem;
        width: 100%; /* Asegurar que el contenedor del mapa ocupe todo el ancho */
        position: relative;
    }
    .leaflet-popup-content {
        width: 33rem !important;
    }
    .dropdown-menu{
        --bs-dropdown-link-active-bg: #6c757d !important;
    }
    .leaflet-bootstrap-dropdowns .btn {
        display: flex;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
        font-size: 1.375rem;
        width: 2.5rem;
        height: 2.5rem;
        padding: 0;
        margin: 0;
        color: white;
        border: .2rem solid black !important;
        border-radius: 2.5rem;
        box-shadow: rgba(0, 0, 0, .33) 0 .125rem .375rem;
        opacity: .5;
        transition: opacity .2s;
        cursor: pointer;
    }
    </style>
</head>
<body style="overflow-x: hidden;">

<input type="hidden" id="userRole" value="<?php echo htmlspecialchars($userrole, ENT_QUOTES, 'UTF-8'); ?>">

<!-- Sección: Formulario de Consulta -->
<section id="formSection" style="display: block;">
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark" style="z-index: 1; height: 5rem;">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <!-- Botón de volver a la izquierda -->
            <div style="position: fixed; top:0; left: 0; z-index: 9;">
                <button type="button" class="btn btn-warning btn-lg m-3" onclick="window.location.href='../index.php'">
                    <i class="bi bi-arrow-left-square-fill"></i> <b>VOLVER</b>
                </button>
            </div>

            <!-- Bloque del título con imágenes, centrado en la totalidad de la pantalla -->
            <div class="d-flex justify-content-center align-items-center flex-grow-1">
                <img src="../../CSS/Images/PSF.png" class="mx-3" alt="Icono" style="width: 4rem;"><!-- Icono Izquierdo -->
                <h2 class="text-light text-center m-0">SISTEMA DE CONSULTAS - PVE</h2>
                <img src="../../CSS/Images/LOGO.png" class="mx-3" alt="Icono" style="width: 4rem;"><!-- Icono Derecho -->
            </div>

            <!-- Botón quitar filtros -->
            <div style="position: fixed; top:0; right: 17rem; z-index: 9;">
                <button type="button" class="btn btn-danger btn-lg m-3" onclick="removeAllFilters()">
                    <i class="bi bi-x-circle-fill"></i> <b>QUITAR FILTROS</b>
                </button>
            </div>

            <!-- Botón de busqueda a la derecha -->
            <div style="position: fixed; top:0; right: 0; z-index: 9;">
                <button type="button" class="btn btn-primary btn-lg m-3" id="consultaBtn">
                    <i class="bi bi-search"></i> <b>REALIZAR CONSULTA</b>
                </button>
            </div>
        </div>
    </nav>

    <!-- Formulario de consulta -->
    <div class="container-fluid p-3" style="margin-top: 5rem;">
        <div class="row">
            <div class="col-12">
                <div class="row mb-2">
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

                    <?php
                    if ($usergroup == 'URII') {
                        // Si el grupo de usuario es URII, la opción se oculta
                        echo '<input type="text" id="Fuente" name="Fuente" value="URII" hidden>';
                    } else {
                        // Para cualquier otro grupo de usuario, muestra las opciones de selección
                        echo '
                    <div class="col">
                        <div class="input-group">
                            <label for="Fuente" class="input-group-text fw-bold">FUENTE:</label>
                            <select id="Fuente" class="form-select text-center" name="Fuente" required>
                                <option value="" selected>Indistinto</option>
                                <option value="URII">URII</option>
                                <option value="0800">0800</option>
                                <option value="911">911</option>
                            </select>
                        </div>
                    </div>';
                    }
                ?>
                    <div class="col">
                        <div class="input-group">
                            <span class="input-group-text fw-bold">REPORTE ASOCIADO:</span>
                            <input type="text" class="form-control" id="ReporteAsociado" name="ReporteAsociado" maxlength="50">
                        </div>
                    </div>
                    <div class="col">
                        <div class="input-group">
                            <label class="input-group-text fw-bold" for="Relevancia">RELEVANCIA:</label>
                            <select class="form-select text-center" id="Relevancia" name="Relevancia" required>
                                <option value="" selected>Indistinto</option>
                                <option value="BAJA">BAJA</option>
                                <option value="MEDIA">MEDIA</option>
                                <option value="ALTA">ALTA</option>
                            </select>
                        </div>
                    </div>
                    <div class="col">
                        <div class="input-group">
                            <label class="input-group-text fw-bold" for="OperadorSQL">OPERADOR SQL:</label>
                            <select class="form-select text-center" id="OperadorSQL" name="OperadorSQL" required>
                                <option value="AND">AND</option>
                                <option value="OR">OR</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col">
                        <div class="input-group">
                            <label for="Tipologia" class="input-group-text fw-bold">TIPIFICACIÓN:</label>
                            <select id="Tipologia" class="form-control" name="Tipologia[]" multiple="multiple" required></select>
                        </div>
                    </div>
                    <div class="col">
                        <div class="input-group">
                            <label for="ModalidadComisiva" class="input-group-text fw-bold">MODALIDAD COMISIVA:</label>
                            <select id="ModalidadComisiva" class="form-control" name="ModalidadComisiva[]" multiple="multiple" required></select>
                        </div>
                    </div>
                    <div class="col">
                        <div class="input-group">
                            <label for="TipoEstupefaciente" class="input-group-text fw-bold">TIPO DE ESTUPEFACIENTE:</label>
                            <select id="TipoEstupefaciente" class="form-control" name="TipoEstupefaciente[]" multiple="multiple" required></select>
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col">
                        <div class="input-group">
                            <label class="input-group-text fw-bold col-8" for="ConnivenciaPolicial">¿SE MENCIONA CONNIVENCIA POLICIAL?:</label>
                            <select class="form-select text-center col-4" id="ConnivenciaPolicial" name="ConnivenciaPolicial" required>
                                <option value="" selected>Indistinto</option>
                                <option value="1">SÍ</option>
                                <option value="0">NO</option>
                            </select>
                        </div>
                    </div>
                    <div class="col">
                        <div class="input-group">
                            <label class="input-group-text fw-bold col-8" for="PosiblesUsurpaciones">¿SE MENCIONAN POSIBLES USURPACIONES?:</label>
                            <select class="form-select text-center col-4" id="PosiblesUsurpaciones" name="PosiblesUsurpaciones" required>
                                <option value="" selected>Indistinto</option>
                                <option value="1">SÍ</option>
                                <option value="0">NO</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col">
                        <div class="input-group">
                            <label class="input-group-text fw-bold col-8" for="UsoAF">¿SE MENCIONA EL USO DE ARMAS DE FUEGO?:</label>
                            <select class="form-select text-center col-4" id="UsoAF" name="UsoAF" required>
                                <option value="" selected>Indistinto</option>
                                <option value="1">SÍ</option>
                                <option value="0">NO</option>
                            </select>
                        </div>
                    </div>
                    <div class="col">
                        <div class="input-group">
                            <label class="input-group-text fw-bold col-8" for="ParticipacionDeMenores">¿SE MENCIONA LA PARTICIPACIÓN DE MENORES?:</label>
                            <select class="form-select text-center col-4" id="ParticipacionDeMenores" name="ParticipacionDeMenores" required>
                                <option value="" selected>Indistinto</option>
                                <option value="1">SÍ</option>
                                <option value="0">NO</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col">
                        <div class="input-group">
                            <label class="input-group-text fw-bold col-8" for="ParticipacionOrgCrim">¿SE MENCIONA LA PARTICIPACIÓN DE ALGUNA ORGANIZACIÓN CRIMINAL?:</label>
                            <select class="form-select text-center col-4" id="ParticipacionOrgCrim" name="ParticipacionOrgCrim" required>
                                <option value="" selected>Indistinto</option>
                                <option value="1">SÍ</option>
                                <option value="0">NO</option>
                            </select>
                        </div>
                    </div>
                    <div class="col">
                        <div class="input-group">
                            <span class="input-group-text fw-bold">ORGANIZACIÓN MENCIONADA:</span>
                            <input type="text" class="form-control" id="OrganizacionCriminal" name="OrganizacionCriminal" maxlength="50">
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col">
                        <div class="input-group">
                            <span class="input-group-text fw-bold col-2">RELATO DEL HECHO:</span>
                            <input type="text" class="form-control" id="Relato" name="Relato" maxlength="50">
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cartas de entidades secundarias -->
    <div class="container-fluid p-3 row justify-content-center">
        <div class="col-md-2">
            <div class="Card" id="joinLugaresParams" style="border: 0.3rem solid #ffffff; box-shadow: 0 7px 50px 10px #ffffff;">
              <div class='main'>
                <h3 class="text-center" style="color: #ffffff;">LUGARES</h3>
                <img src="../../CSS/Images/Lugar.png" alt="Lugar">
              </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="Card" id="joinPersonasParams" style="border: 0.3rem solid #ffffff; box-shadow: 0 7px 50px 10px #ffffff;">
              <div class='main'>
                <h3 class="text-center" style="color: #ffffff;">PERSONAS</h3>
                <img src="../../CSS/Images/Personas.png" alt="Personas">
              </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="Card" id="joinVehiculosParams" style="border: 0.3rem solid #ffffff; box-shadow: 0 7px 50px 10px #ffffff;">
              <div class='main'>
                <h3 class="text-center" style="color: #ffffff;">VEHÍCULOS</h3>
                <img src="../../CSS/Images/Vehiculos.png" alt="Vehiculos">
              </div>
            </div>
        </div>
    </div>

    <div class="container-fluid p-3">
        <div class="row justify-content-center">
            <div class="col-md-2">
                <button type="button" class="btn btn-info btn-lg w-100" onclick="checkAndShowSectionMap()">
                    <i class="bi bi-map"></i> <b>VER MAPA</b>
                </button>
            </div>

            <div class="col-md-2">
                <button type="button" class="btn btn-success btn-lg w-100" onclick="checkAndShowSectionTable()">
                    <i class="bi bi-table"></i> <b>VER TABLA</b>
                </button>
            </div>
        </div>
    </div>

</section>

<section id="tableSection" style="display: none;">
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark" style="z-index: 1; height: 5rem;">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <!-- Botón de volver a la izquierda -->
            <div style="position: fixed; top:0; left: 0; z-index: 9;">
                <button type="button" class="btn btn-warning btn-lg m-3" onclick="showSectionAndWait('formSection')">
                    <i class="bi bi-arrow-left-square-fill"></i> <b>VOLVER</b>
                </button>
            </div>

            <!-- Botón de busqueda a la derecha -->
            <div class="d-flex justify-content-center align-items-center flex-grow-1">
                <div class="input-group m-3" style="max-width: 40rem;">
                    <span class="input-group-text fs-4 bg-primary text-white border-primary col-6">
                        <i class="bi bi-search"></i> <b>BUSCAR EN LA TABLA</b>
                    </span>
                    <input type="text" class="form-control border-primary col-6" id="CustomSearch" name="CustomSearch" placeholder="Ingrese el valor a buscar...">
                </div>
            </div>

            <!-- Botón para descargar los datos -->
            <div style="position: fixed; top:0; right: 0; z-index: 9;">
                <button type="button" class="btn btn-success btn-lg m-3 px-3" onclick="processData('formSection')">
                    <i class="bi bi-cloud-download"></i> <b>DESCARGAR</b>
                </button>
            </div>
        </div>
    </nav>
    <!-- Sección: Tabla de Resultados -->
    <div id="tableContainer"></div> <!-- Este div es donde se insertará la tabla desde JavaScript -->
</section>

<!-- Sección: Mapa -->
<section id="mapSection" style="display: none;">
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark" style="z-index: 1; height: 5rem;">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <!-- Botón de volver a la izquierda -->
            <div style="position: fixed; top:0; left: 0; z-index: 9;">
                <button type="button" class="btn btn-warning btn-lg m-3" onclick="showSectionAndWait('formSection')">
                    <i class="bi bi-arrow-left-square-fill"></i> <b>VOLVER</b>
                </button>
            </div>

            <!-- Bloque del título con imágenes, centrado en la totalidad de la pantalla -->
            <div class="d-flex justify-content-center align-items-center flex-grow-1">
                <img src="../../CSS/Images/LOGO2.png" class="mx-3" alt="Icono" style="width: 4rem;"><!-- Icono Izquierdo -->
                <h2 class="text-light text-center m-0">SISTEMA HORUS - MAPA PVE</h2>
                <img src="../../CSS/Images/LOGO.png" class="mx-3" alt="Icono" style="width: 4rem;"><!-- Icono Derecho -->
            </div>
        </div>
    </nav>
    <script>
        // Crear una instancia de MapHandler
        const mapHandler = new MapHandler('mapContainer');
    </script>

    <!-- Sección: Mapa -->
    <div id="mapContainer"></div><!-- Este div es donde se insertará el mapa JavaScript -->
</section>
  
</body>
</html>
  