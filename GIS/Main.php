<?php
  // Conectar a la base de datos de forma segura
  require '../PHP/ServerConnect.php';

  if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
      // El usuario no ha iniciado sesión, redirigirlo a la página de inicio de sesión
      header("Location: ../Login.php");
      exit();
  }

  // Obtener el valor del rol del usuario desde la sesión
  $rolUsuario = $_SESSION['rolUsuario'];

  // Conexión a la base de datos
  $conn = open_database_connection();
  if ($conn->connect_error) {
      die("Error de conexión: " . $conn->connect_error);
  }

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GIS</title>
    <link rel="stylesheet" type="text/css" href="CSS/GIS.css">
    <!-- Navicon -->
    <link rel="icon" href="../CSS/Images/favicon.ico" type="Image/x-icon">
    <link rel="shortcut icon" href="../CSS/Images/favicon.ico" type="Image/x-icon">
    <!-- JQuery -->
    <script src="../JQuery/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap -->
    <script src="../Bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../Bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../Bootstrap/Icons/font/bootstrap-icons.css">
    <!-- Leaftlet MAIN-->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <!-- Leaflet Grouped Layer Control Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-groupedlayercontrol/0.6.1/leaflet.groupedlayercontrol.min.js" integrity="sha512-cdomNfv1IRJE1v+2/irZdkNU09XwtYP2bQ1qK1ybWF/vz+P3GTMZtrXwlxTU41ExwoWGvEf0njIak5l/ZQaIMA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet-groupedlayercontrol/0.6.1/leaflet.groupedlayercontrol.min.css" integrity="sha512-yP7tTm3rX87fOK3iDh9K2RJTHsq6BfGZ8sFqRMIfsmmZVakOw483WyOKlRWVLjHCHEDIcEkp/U+cO3TnZ8oiuw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Leaflet Search Plugin -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet-search/4.0.0/leaflet-search.min.css" integrity="sha512-+o26nsM883F01UlBWY09KgjDn7o1rgGFi1a+lX1zI7m0I2iIh4rckpQSKVnukvn8DKsb0A9hZHoUY7lBkvLdfA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-search/4.0.0/leaflet-search.min.js" integrity="sha512-nvy2tht2GE/ReEeu0Zu7/Y1IgKTTrX1r1MaROtD1HDol5WUvw7Ih29qA6JCNCgYURzPcBqaSv12ZnNS68Eaq6Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- Layer Manager -->
    <script src="JS/IPLayerManager.js"></script>
    <!-- Leaflet Grouped Layer Control Plugin -->
    <script src="Plugins/Panel-layers/leaflet-panel-layers.min.js"></script>
    <link rel="stylesheet" src="Plugins/Panel-layers/leaflet-panel-layers.min.js"/>

</head>

<body class="bg-secondary">

<!-- Barra de navegación -->
<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark" style="height: 5vw;">
    <div class="container-fluid d-flex align-items-center justify-content-center">
        <!-- Imagen 1 -->
        <div>
            <img src="../CSS/Images/PSF.png" alt="Icono" style="width: 4vw; margin: 1vw;"><!-- Icono -->
        </div>

        <!-- Título centrado -->
        <div class="text-center">
            <h1 class="text-warning">Sitio en construcción</h1>
        </div>

        <!-- Imagen 2 -->
        <div>
            <img src="../CSS/Images/OJO.png" alt="Icono" style="width: 4vw; margin: 1vw;"><!-- Icono -->
        </div>

        <!-- Boton Volver -->
        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <button type="button" class="btn btn-warning btn-lg" style="position: fixed; top: 0; left: 0; width: 12vw; height: 4vw; font-size: 1.5vw; margin: 0.5vw;" onclick="window.location.href='../Main.php'">
            <i class="bi bi-arrow-left-square-fill"></i> <b>VOLVER</b>
            </button>
        </div>
    </div>
</nav>

<!-- Contenedor del mapa -->
<div id="map" class="Leaflet"></div>

<script>
    // Inicializa el mapa Leaflet
    var map = L.map('map').setView([-31, -60], 7);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Inicializa el administrador de capas IPLayerManager
    var ipLayerManager = new IPLayerManager(map);

    // Crea un objeto para las capas agrupadas
    var groupedOverlays = {
        "Incidencias priorizadas": {}
    };

    // Cargar datos dinámicamente y agregarlos al mapa
    fetch('PHP/EndpointGIS.php')
        .then(response => response.json())
        .then(data => {
            ipLayerManager.addMarkerLayer(data);

            // Agrega las capas del ipLayerManager al objeto groupedOverlays
            Object.keys(ipLayerManager.layerGroups).forEach(groupName => {
                groupedOverlays["Incidencias priorizadas"][groupName] = ipLayerManager.layerGroups[groupName];
            });
        })
        .catch(error => {
            console.error("Error al cargar los datos:", error);
        });
        
</script>



</body>
</html>

