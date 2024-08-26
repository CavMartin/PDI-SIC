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
    $LugaresData = []; // Inicializar vacío

    // Recuperar valores por POST
    $ID = isset($_POST['ID']) ? $_POST['ID'] : '';
    $formularioID = isset($_POST['formularioID']) ? $_POST['formularioID'] : '';
    $FK_Encabezado = isset($_POST['FK_Encabezado']) ? $_POST['FK_Encabezado'] : '';
    $ClavePrimaria = isset($_POST['ClavePrimaria']) ? $_POST['ClavePrimaria'] : '';
    $NumeroDeOrden = isset($_POST['NumeroDeOrden']) ? $_POST['NumeroDeOrden'] : '';

    // Si ClavePrimaria no está vacío, recopila los datos usando DataFetcher
    if (!empty($ClavePrimaria)) {
        // Crear una LugaresData de DataFetcher pasándole la conexión a la base de datos
        $DataFetcher = new DataFetcher($conn);

        // Llamar al método fetchDataLugar para obtener los datos del lugar
        $LugaresData = $DataFetcher->fetchDataLugar($ClavePrimaria);
    }

    // Cierra la conexión a la base de datos
    $conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lugares</title>
  <!-- Favicon -->
  <link rel="icon" href="../CSS/Images/favicon.ico" type="Image/x-icon">
  <link rel="shortcut icon" href="../CSS/Images/favicon.ico" type="Image/x-icon">
  <!-- CSS -->
  <link rel="stylesheet" type="text/css" href="CSS/Main.css">
  <!-- JS Core -->
  <script src="JS/backToMain.js"></script>
  <script src="JS/Handler_AJAX.js"></script>
  <script src="JS/Handler_DatosComplementarios.js"></script>
  <script src="JS/ElementosOcultos.js"></script>
  <script src="../JS/ManejarImagenes.js"></script>
  <script src="../JS/TransformarDatos.js"></script>
  <!-- Datalist -->
  <script src="JS/InicializarDataList.js"></script>
  <datalist id="globalSugerenciasCiudades"></datalist>
  <datalist id="globalSugerenciasProvincias"></datalist>
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
            <h2 class="text-light text-center m-0">FORMULARIO "<?php echo $formularioID; ?>" - 
                <?php
                $NumeroDeOrden = intval($NumeroDeOrden);
                if ($NumeroDeOrden === 0) {
                    echo "AGREGAR NUEVO LUGAR DEL HECHO";
                } else {
                    echo "EDITAR LUGAR DEL HECHO";
                }
                ?>
            </h2>
            <img src="../CSS/Images/OJO.png" class="mx-3" alt="Icono" style="width: 4rem;"><!-- Icono Derecho -->
        </div>
    </div>
</nav>

<!-- Barra Lateral derecha -->
<div class="sidebar bg-dark" style="position: fixed; right: 0; top: 0; height: 100%; width: 11vw;">
    <div id="BotoneraLateral" class="d-flex flex-column"><!-- Botonera -->
        <div class="mt-2">
            <button type="button" id="AgregarDatoComplementario" class="btn btn-primary btn-lg" style="position: fixed; top: 4.5vw; width: 10vw; height:4vw; font-size: 1vw; margin: 0.5vw;">
                <i class="bi bi-file-earmark-text"></i> Agregar dato complementario
            </button>
        </div>
        <div class="mt-2">
            <button type="button" class="btn btn-success btn-lg" style="position: fixed; bottom: 0; right: 0; width: 10vw; height: 4vw; font-size: 1.5vw; margin: 0.5vw;" onclick="guardarCambiosLugar()">
                <i class="bi bi-floppy"></i> <b>Guardar</b>
            </button>
        </div>
    </div>
</div>

<!-- Formulario de carga/edición de entidad -->
<div class="row p-2" style="top: 5.5vw; left: 1vw; position: absolute; width: 88vw;">
    <form id="CargarLugares" name="CargarLugares" enctype="multipart/form-data" method="post">

        <div><!-- Campos ocultos necesarios para el funcionamiento de la aplicacion -->
            <input type="hidden" id="ID" name="ID" value="<?php echo htmlspecialchars($ID, ENT_QUOTES, 'UTF-8'); ?>" readonly>
            <input type="hidden" id="formularioID" name="formularioID" value="<?php echo htmlspecialchars($formularioID, ENT_QUOTES, 'UTF-8'); ?>" readonly>
            <input type="hidden" id="FK_Encabezado" name="FK_Encabezado" value="<?php echo htmlspecialchars($FK_Encabezado, ENT_QUOTES, 'UTF-8'); ?>" readonly>
            <input type="hidden" id="ClavePrimaria" name="ClavePrimaria" value="<?php echo htmlspecialchars($ClavePrimaria, ENT_QUOTES, 'UTF-8'); ?>" readonly>
            <input type="hidden" id="NumeroDeOrden" name="NumeroDeOrden" value="<?php echo htmlspecialchars($NumeroDeOrden, ENT_QUOTES, 'UTF-8'); ?>" readonly>
        </div>

        <div id="EntidadPrincipal" class="border border-black rounded bg-light p-3 m-1"><!-- Entidad principal del formulario -->

            <div class="d-flex justify-content-between align-items-center mb-2">
                <h2 class="text-center">Detalles del lugar del hecho</h2>
                <?php
                    // Verificar si 'Action' ha sido enviado y su valor no es 'NuevaEntidad'
                    if (!isset($_POST['Action']) || $_POST['Action'] != 'NuevaEntidad') {
                        // Mostrar el botón si la condición es verdadera
                        echo '<div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                  <button type="button" id="quitarLugar" class="btn btn-danger btn-lg me-md-2 fs-4" onclick="eliminarLugar(\'\')">
                                  <i class="bi bi-house-x"></i> Eliminar</button>
                              </div>';
                    }
                ?>
            </div>

            <div class="row">
                <div class="col">
                    <div class="input-group mb-3">
                        <label class="input-group-text fw-bold" for="L_Rol">ROL DEL LUGAR:</label>
                        <select id="L_Rol" class="form-select" name="L_Rol" required>
                        <?php
                            // Valor seleccionado por defecto
                            $selectedValue = isset($LugaresData['L_Rol']) ? $LugaresData['L_Rol'] : null;

                            echo generarOpcionesSelect($Array_RolLugar, $selectedValue);
                        ?>
                        </select>            
                    </div>
                </div>
                <div class="col" id="DIV_Especifique" style="display: none;">
                <div class="input-group mb-3">
                    <span class="input-group-text fw-bold" id="basic-addon1">ESPECIFIQUE:</span>
                    <input type="text" class="form-control" id="L_RolEspecifique" name="L_RolEspecifique" value="<?php echo isset($LugaresData['L_Rol']) ? $LugaresData['L_Rol'] : ''; ?>" maxlength="100" placeholder="Especifique el tipo de lugar">
                </div>
                </div>
                <div class="col">
                    <div class="input-group mb-3">
                        <label class="input-group-text fw-bold" for="L_TipoLugar">TIPO DE LUGAR:</label>
                        <select class="form-select" id="L_TipoLugar" name="L_TipoLugar" required>
                        <?php
                            // Valor seleccionado por defecto
                            $selectedValue = isset($LugaresData['L_TipoLugar']) ? $LugaresData['L_TipoLugar'] : null;
            
                            echo generarOpcionesSelect($Array_TipoLugar, $selectedValue);
                        ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="col">
                    <div class="input-group mb-3">
                        <span class="input-group-text fw-bold" id="basic-addon1">NOMBRE DEL LUGAR:</span>
                        <input type="text" class="form-control" id="L_NombreLugarEspecifico" name="L_NombreLugarEspecifico" value="<?php echo isset($LugaresData['L_NombreLugarEspecifico']) ? $LugaresData['L_NombreLugarEspecifico'] : ''; ?>" maxlength="50" placeholder="Sí el lugar posee alguna denominación particular. Ejemplo: Comisaria 21 o Supermercado Arcoiris">
                    </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="input-group mb-3">
                        <span class="input-group-text fw-bold" id="basic-addon1">CALLE:</span>
                        <input type="text" class="form-control" id="L_Calle" name="L_Calle" value="<?php echo isset($LugaresData['L_Calle']) ? $LugaresData['L_Calle'] : ''; ?>" maxlength="50" placeholder="Nombre de la calle / ruta" onchange="transformarDatosNompropio('L_Calle')" required>
                    </div>
                </div>
                <div class="col">
                    <div class="input-group mb-3">
                        <span class="input-group-text fw-bold" id="basic-addon1">ALTURA CATASTRAL:</span>
                        <input type="text" class="form-control" id="L_AlturaCatastral" name="L_AlturaCatastral" value="<?php echo isset($LugaresData['L_AlturaCatastral']) ? $LugaresData['L_AlturaCatastral'] : ''; ?>" maxlength="5" placeholder="Número correspondiente a la altura catastral deldomicilio" onchange="transformarDatosNumerico('L_AlturaCatastral')">
                    </div>
                </div>
                <div class="col">
                    <div class="input-group mb-3">
                        <span class="input-group-text fw-bold" id="basic-addon1">DETALLE:</span>
                        <input type="text" class="form-control" id="L_CalleDetalle" name="L_CalleDetalle" value="<?php echo isset($LugaresData['L_CalleDetalle']) ? $LugaresData['L_CalleDetalle'] : ''; ?>" maxlength="50" placeholder="Detalle adicional del domicilio. Ej: Bis / Piso N° / Depto N°">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="input-group mb-3">
                        <span class="input-group-text fw-bold" id="basic-addon1">ENTRE CALLE:</span>
                        <input type="text" class="form-control" id="L_Interseccion1" name="L_Interseccion1" value="<?php echo isset($LugaresData['L_Interseccion1']) ? $LugaresData['L_Interseccion1'] : ''; ?>" maxlength="50" placeholder="Primera intersección de la calle, si corresponde" onchange="transformarDatosNompropio('L_Interseccion1')">
                    </div>
                </div>
                <div class="col">
                    <div class="input-group mb-3">
                        <span class="input-group-text fw-bold" id="basic-addon1">Y CALLE:</span>
                        <input type="text" class="form-control" id="L_Interseccion2" name="L_Interseccion2" value="<?php echo isset($LugaresData['L_Interseccion2']) ? $LugaresData['L_Interseccion2'] : ''; ?>" maxlength="50" placeholder="Segunda intersección de la calle, si corresponde" onchange="transformarDatosNompropio('L_Interseccion2')">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="input-group mb-3">
                        <span class="input-group-text fw-bold" id="basic-addon1">BARRIO:</span>
                        <input type="text" class="form-control" id="L_Barrio" name="L_Barrio" value="<?php echo isset($LugaresData['L_Barrio']) ? $LugaresData['L_Barrio'] : ''; ?>" onchange="transformarDatosNompropio('L_Barrio')">
                    </div>
                </div>
                <div class="col">
                    <div class="input-group mb-3">
                        <span class="input-group-text fw-bold" id="basic-addon1">LOCALIDAD:</span>
                        <input type="text" class="form-control" id="L_Localidad" name="L_Localidad" value="<?php echo isset($LugaresData['L_Localidad']) ? $LugaresData['L_Localidad'] : 'Rosario'; ?>" list="globalSugerenciasCiudades" maxlength="50">
                        <datalist id="sugerenciasCiudades">
                        </datalist>
                    </div>
                </div>
                <div class="col">
                    <div class="input-group mb-3">
                        <span class="input-group-text fw-bold" id="basic-addon1">PROVINCIA:</span>
                        <input type="text" class="form-control" id="L_Provincia" name="L_Provincia" value="<?php echo isset($LugaresData['L_Provincia']) ? $LugaresData['L_Provincia'] : 'Santa Fe'; ?>" list="globalSugerenciasProvincias" maxlength="50">
                        <datalist id="sugerenciasProvincias">
                        </datalist>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="input-group mb-3">
                        <span class="input-group-text fw-bold" id="basic-addon1">PAÍS:</span>
                        <input type="text" class="form-control" id="L_Pais" name="L_Pais" value="<?php echo isset($LugaresData['L_Pais']) ? $LugaresData['L_Pais'] : 'ARGENTINA'; ?>" maxlength="50" value="" onchange="transformarDatosMayusculas('L_Pais')">
                    </div>
                </div>
                <div class="col">
                    <div class="input-group mb-3">
                        <span class="input-group-text fw-bold" id="basic-addon1">COORDENADAS:</span>
                        <input type="text" class="form-control" id="L_Coordenadas" name="L_Coordenadas" value="<?php echo isset($LugaresData['L_Coordenadas']) ? $LugaresData['L_Coordenadas'] : ''; ?>" required>
                    </div>
                </div>
            </div>
        </div>
 

        <div id="DatosComplementarios"><!-- Entidad secundaria - Datos complementarios -->
        </div>

    </form>
</div>

<script>
        // Ejecutar la función al cargar el DOM
        document.addEventListener("DOMContentLoaded", function() {
            mostrarElementoOculto("L_Rol", "DIV_Especifique");

        // Agregar el evento onchange al elemento select
        document.getElementById("L_Rol").onchange = function() {
            mostrarElementoOculto("L_Rol", "DIV_Especifique");
        };
    });
</script>

</body>
</html>
