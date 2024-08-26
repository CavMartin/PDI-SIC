<?php
    require '../PHP/ServerConnect.php'; // Conectar a la base de datos
    require 'PHP/ArraysManager.php'; // Manejador de arrays
    require 'PHP/DataFetcher.php'; // Clase para recopilar datos

    // Verificar estado del login
    checkLoginState();

    // Si el método para acceder a la pagina no es POST, redirige a index.php
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        header("Location: index.php");
        exit();
    }

    // Conexión a la base de datos
    $conn = open_database_connection('sic');
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
    
    // Recoger valores de POST
    $EncabezadoData = []; // Inicializar vacío
    
    $ID = isset($_POST['ID']) ? $_POST['ID'] : '';
    $formularioID = isset($_POST['formularioID']) ? $_POST['formularioID'] : '';

    // Si ID no está vacío, recopila los datos usando DataFetcher
    if (!empty($ID)) {
        // Crear una EncabezadoData de DataFetcher pasándole la conexión a la base de datos
        $DataFetcher = new DataFetcher($conn);
    
        // Llamar al método fetchDataEncabezado para obtener los datos del formulario
        $EncabezadoData = $DataFetcher->fetchDataEncabezado($ID);
    }
    
    // Cierra la conexión a la base de datos
    $conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Encabezado</title>
  <!-- Favicon -->
  <link rel="icon" href="../CSS/Images/favicon.ico" type="Image/x-icon">
  <link rel="shortcut icon" href="../CSS/Images/favicon.ico" type="Image/x-icon">
  <!-- CSS -->
  <link rel="stylesheet" type="text/css" href="CSS/Main.css">
  <!-- JS -->
  <script src="JS/backToMain.js"></script>
  <script src="JS/Handler_AJAX.js"></script>
  <script src="JS/ElementosOcultos.js"></script>
  <script src="../JS/TransformarDatos.js"></script>
  <!-- jQuery -->
  <script src="../Resources/JQuery/jquery-3.7.1.min.js"></script>
  <!-- SweetAlert -->
  <script src="../Resources/SweetAlert2/sweetalert2.min.js"></script>
  <link rel="stylesheet" href="../Resources/SweetAlert2/sweetalert2.min.css">
  <!-- Bootstrap -->
  <script src="../Resources/Bootstrap/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="../Resources/Bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../Resources/Bootstrap/Icons/font/bootstrap-icons.css">
</head>

<body class="bg-secondary">

<!-- Barra de navegación -->
<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
    <div class="container-fluid d-flex justify-content-center position-relative">        

        <!-- Botón de navegación a la página principal -->
        <div style="position: absolute; left: 0;">
            <button type="button" id="VolverButton" class="btn btn-warning btn-lg m-3" onclick="backToMain()">
                <i class="bi bi-arrow-left-square-fill"></i> <b>VOLVER</b>
            </button>
        </div>

        <!-- Bloque del título con imágenes, centrado -->
        <div class="d-flex justify-content-center align-items-center flex-grow-1">
            <img src="../CSS/Images/PSF.png" class="mx-3" alt="Icono" style="width: 4rem;"><!-- Icono Izquierdo -->
            <h1 class="text-light text-center m-0">FORMULARIO #<?php echo htmlspecialchars($formularioID); ?></h1>
            <img src="../CSS/Images/OJO.png" class="mx-3" alt="Icono" style="width: 4rem;"><!-- Icono Derecho -->
        </div>
    </div>
</nav>

<!-- Barra Lateral derecha -->
<div class="sidebar bg-dark" style="position: fixed; right: 0; top: 0; height: 100%; width: 11vw;">
    <div class="mt-2">
        <button type="button" class="btn btn-success btn-lg" style="position: fixed; bottom: 0; right: 0; width: 10vw; height: 4vw; font-size: 1.5vw; margin: 0.5vw;" onclick="guardarCambiosEncabezado()">
            <i class="bi bi-floppy"></i> <b>Guardar</b>
        </button>
    </div>
</div>

<div class="row p-2" style="top: 5.5vw; left: 1vw; position: absolute; width: 88vw;">
    <form id="CargarEncabezado" name="CargarEncabezado" enctype="multipart/form-data" method="post">
        
        <input type="hidden" id="ID" name="ID" value="<?php echo htmlspecialchars($ID, ENT_QUOTES, 'UTF-8'); ?>" readonly>
        <input type="hidden" id="formularioID" name="formularioID" value="<?php echo htmlspecialchars($formularioID, ENT_QUOTES, 'UTF-8'); ?>" readonly>

        <div id="EntidadPrincipal" class="row border border-black rounded bg-light p-1 m-1"><!-- Entidad principal del formulario -->
            
            <div class="row mt-3">
                <div class="col">
                    <div class="input-group mb-3">
                        <span class="input-group-text fw-bold">FECHA:</span>
                        <input type="date" class="form-control" id="Fecha" name="Fecha" value="<?php echo isset($EncabezadoData['Fecha']) ? $EncabezadoData['Fecha'] : ''; ?>" required>
                    </div>
                </div>
                <div class="col">
                    <div class="input-group mb-3">
                        <span class="input-group-text fw-bold">HORA:</span>
                        <input type="time" class="form-control" id="Hora" name="Hora" value="<?php echo isset($EncabezadoData['Hora']) ? $EncabezadoData['Hora'] : ''; ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="input-group mb-3">
                        <label for="Clasificacion" class="input-group-text fw-bold">TIPO DE FORMULARIO:</label>
                        <select id="Clasificacion" class="form-select" name="Clasificacion" required>
                            <?php
                                // Valor seleccionado por defecto
                                $selectedValue = isset($EncabezadoData['Clasificacion']) ? $EncabezadoData['Clasificacion'] : null;
                                echo generarOpcionesSelect($Array_Clasificacion, $selectedValue);
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col" id="DIV_OtraClasificacion" style="display: none;">
                    <div class="input-group mb-3">
                        <span class="input-group-text fw-bold">ESPECIFIQUE:</span>
                        <input type="text" class="form-control" id="OtraClasificacion" name="OtraClasificacion" maxlength="100" value="<?php echo isset($EncabezadoData['Clasificacion']) ? $EncabezadoData['Clasificacion'] : ''; ?>">
                    </div>
                </div>
                <div class="col">
                    <div class="input-group mb-3">
                        <span class="input-group-text fw-bold">CAUSA:</span>
                        <input type="text" class="form-control" id="Causa" name="Causa" maxlength="50" value="<?php echo isset($EncabezadoData['Causa']) ? $EncabezadoData['Causa'] : ''; ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="input-group mb-3">
                        <span class="input-group-text fw-bold">DEPENDENCIA:</span>
                        <input type="text" class="form-control" id="Dependencia" name="Dependencia" maxlength="50" value="<?php echo isset($EncabezadoData['Dependencia']) ? $EncabezadoData['Dependencia'] : ''; ?>">
                    </div>
                </div>
                <div class="col">
                    <div class="input-group mb-3">
                        <span class="input-group-text fw-bold">JUZGADO:</span>
                        <input type="text" class="form-control" id="Juzgado" name="Juzgado" maxlength="50" value="<?php echo isset($EncabezadoData['Juzgado']) ? $EncabezadoData['Juzgado'] : ''; ?>">
                    </div>
                </div>
                <div class="col">
                    <div class="input-group mb-3">
                        <span class="input-group-text fw-bold">FISCAL A CARGO:</span>
                        <input type="text" class="form-control" id="Fiscal" name="Fiscal" maxlength="50" value="<?php echo isset($EncabezadoData['Fiscal']) ? $EncabezadoData['Juzgado'] : ''; ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="input-group mb-3">
                    <span class="input-group-text fw-bold">RESEÑA:</span>
                    <textarea id="Relato" class="form-control" name="Relato" rows="16" required><?php echo isset($EncabezadoData['Relato']) ? $EncabezadoData['Relato'] : ''; ?></textarea>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // Ejecutar la función al cargar el DOM
    document.addEventListener("DOMContentLoaded", function() {
        mostrarElementoOculto("Clasificacion", "DIV_OtraClasificacion");

    // Agregar el evento onchange al elemento select
    document.getElementById("Clasificacion").onchange = function() {
        mostrarElementoOculto("Clasificacion", "DIV_OtraClasificacion");
    };
});
</script>

</body>
</html>
