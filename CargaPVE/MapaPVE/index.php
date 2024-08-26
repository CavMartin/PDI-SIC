<?php
// Conectar a la base de datos de forma segura
require '../../PHP/ServerConnect.php';
require '../PHP/DataFetcher.php'; // Clase para recopilar datos

// Verificar estado del login
checkLoginState();

// Conexión a la base de datos
$conn = open_database_connection('uaic');
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$DataFetcher = new DataFetcher($conn);

// Ejecutar la consulta estática para obtener los datos
$staticQuery = $DataFetcher->fetchDataQueryStaticMap();

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mapa PVE</title>
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
  <!-- Popper.js -->
  <script src="../../Resources/Popper/popper.min.js"></script>
  <!-- Bootstrap -->
  <script src="../../Resources/Bootstrap/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="../../Resources/Bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../Resources/Bootstrap/Icons/font/bootstrap-icons.css">
  <!-- DataTables -->
  <script src="../../Resources/DataTables/datatables.min.js"></script>
  <!-- JS -->
  <script src="JS/handlerMap.js"></script>
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

<!-- Barra de navegación -->
<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark" style="z-index: 1; height: 5rem;">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <!-- Botón de volver a la izquierda -->
        <div style="position: fixed; top:0; left: 0; z-index: 9;">
            <button type="button" class="btn btn-light btn-lg m-3" onclick="window.location.href='../../index.php'">
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
    const mapHandler = new StaticMapHandler('mapContainer');

    // Inicializar el mapa y cargar los datos obtenidos en PHP
    document.addEventListener('DOMContentLoaded', function() {
        mapHandler.initializeMap();
        const data = <?php echo json_encode($staticQuery); ?>;
        mapHandler.populateMap(data); // Cargar los datos en el mapa
    });
</script>

<!-- Sección: Mapa -->
<div id="mapContainer"></div><!-- Este div es donde se insertará el mapa JavaScript -->
  
</body>
</html>
