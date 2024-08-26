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
    $datosPersona = []; // Inicializar vacío

    // Recuperar valores por POST
    $ID = isset($_POST['ID']) ? $_POST['ID'] : '';
    $formularioID = isset($_POST['formularioID']) ? $_POST['formularioID'] : '';
    $FK_Encabezado = isset($_POST['FK_Encabezado']) ? $_POST['FK_Encabezado'] : '';
    $ClavePrimaria = isset($_POST['ClavePrimaria']) ? $_POST['ClavePrimaria'] : '';
    $NumeroDeOrden = isset($_POST['NumeroDeOrden']) ? $_POST['NumeroDeOrden'] : '';

    // Si ClavePrimaria no está vacío, recopila los datos usando DataFetcher
    if (!empty($ClavePrimaria)) {
        // Crear una instancia de DataFetcher pasándole la conexión a la base de datos
        $DataFetcher = new DataFetcher($conn);

        // Llamar al método fetchDataPersona para obtener los datos de la persona
        $datosPersona = $DataFetcher->fetchDataPersona($ClavePrimaria);
    }

    // Cierra la conexión a la base de datos
    $conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Personas</title>
  <!-- Favicon -->
  <link rel="icon" href="../CSS/Images/favicon.ico" type="Image/x-icon">
  <link rel="shortcut icon" href="../CSS/Images/favicon.ico" type="Image/x-icon">
  <!-- CSS -->
  <link rel="stylesheet" type="text/css" href="CSS/Main.css">
  <!-- JS -->
  <script src="JS/backToMain.js"></script>
  <script src="JS/Handler_AJAX.js"></script>
  <script src="JS/Handler_Domicilios.js"></script>
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
                    echo "AGREGAR NUEVA PERSONA";
                } else {
                    echo "EDITAR PERSONA";
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
            <button type="button" id="AgregarDomicilio" class="btn btn-secondary btn-lg" style="position: fixed; top: 4.5vw; width: 10vw; height:4vw; font-size: 1.1vw; margin: 0.5vw;">
                <i class="bi bi-house-add"></i> Agregar domicilio
            </button>
        </div>
        <div class="mt-2">
            <button type="button" id="AgregarDatoComplementario" class="btn btn-primary btn-lg" style="position: fixed; top: 10vw; width: 10vw; height:4vw; font-size: 1vw; margin: 0.5vw;">
                <i class="bi bi-file-earmark-text"></i> Agregar dato complementario
            </button>
        </div>
        <div class="mt-2">
            <button type="button" class="btn btn-success btn-lg" style="position: fixed; bottom: 0; right: 0; width: 10vw; height: 4vw; font-size: 1.5vw; margin: 0.5vw;" onclick="guardarCambiosPersona()">
                <i class="bi bi-floppy"></i> <b>Guardar</b>
            </button>
        </div>
    </div>
</div>

<!-- Formulario de carga/edición de entidad -->
<div class="row p-2" style="top: 5.5vw; left: 1vw; position: absolute; width: 88vw;">
    <form id="CargarPersonas" name="CargarPersonas" enctype="multipart/form-data" method="post">

        <div><!-- Campos ocultos necesarios para el funcionamiento de la aplicacion -->
            <input type="hidden" id="ID" name="ID" value="<?php echo htmlspecialchars($ID, ENT_QUOTES, 'UTF-8'); ?>" readonly>
            <input type="hidden" id="formularioID" name="formularioID" value="<?php echo htmlspecialchars($formularioID, ENT_QUOTES, 'UTF-8'); ?>" readonly>
            <input type="hidden" id="FK_Encabezado" name="FK_Encabezado" value="<?php echo htmlspecialchars($FK_Encabezado, ENT_QUOTES, 'UTF-8'); ?>" readonly>
            <input type="hidden" id="ClavePrimaria" name="ClavePrimaria" value="<?php echo htmlspecialchars($ClavePrimaria, ENT_QUOTES, 'UTF-8'); ?>" readonly>
            <input type="hidden" id="NumeroDeOrden" name="NumeroDeOrden" value="<?php echo htmlspecialchars($NumeroDeOrden, ENT_QUOTES, 'UTF-8'); ?>" readonly>
        </div>

        <div id="EntidadPrincipal" class="row border border-black rounded bg-light p-1 m-1"><!-- Entidad principal del formulario -->
            <div class="col-md-4"><!-- Columna izquierda -->
                <div class="ImagenEntidadPersona">
                    <div class="upload">
                        <?php
                            $base64Image_Persona = !empty($datosPersona['P_FotoPersona']) ? $datosPersona['P_FotoPersona'] : '../CSS/Images/PersonaDefault.jpg';
                        ?>                           
                    <img id="previewP_FotoPersona" src="<?php echo $base64Image_Persona; ?>" alt="Previsualización de imagen">
                    <div class="round">
                            <input type="file" id="P_FotoPersona" name="P_FotoPersona" accept="image/*" onchange="procesarImagen(event, 'previewP_FotoPersona', 'DataURL_P_FotoPersona')">
                            <textarea id="DataURL_P_FotoPersona" name="DataURL_P_FotoPersona" hidden><?php echo isset($datosPersona['P_FotoPersona']) ? $datosPersona['P_FotoPersona'] : ''; ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8"><!-- Columna derecha -->
                <div class="d-flex justify-content-between align-items-center my-2">
                    <h2 class="text-center">Datos personales</h2>
                    <?php
                        // Verificar si 'Action' ha sido enviado y su valor no es 'NuevaEntidad'
                        if (!isset($_POST['Action']) || $_POST['Action'] != 'NuevaEntidad') {
                            // Mostrar el botón si la condición es verdadera
                            echo '<div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                      <button type="button" id="quitarPersona" class="btn btn-danger btn-lg me-md-2 fs-4" onclick="eliminarPersona(\'\')">
                                      <i class="bi bi-file-earmark-person"></i> Eliminar</button>
                                  </div>';
                        }
                    ?>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="input-group mb-3">
                            <label class="input-group-text fw-bold" for="P_Rol">ROL:</label>
                            <select class="form-select" id="P_Rol" name="P_Rol" required>
                                <?php
                                    // Valor seleccionado por defecto
                                    $selectedValue = isset($datosPersona['P_Rol']) ? $datosPersona['P_Rol'] : null;
            
                                    echo generarOpcionesSelect($Array_PersonaRol, $selectedValue);
                                ?>
                            </select>
                        </div>
                    </div>
                    <div id="DIV_Especifique" class="col" style="display: none;">
                        <div class="input-group mb-3">
                            <span class="input-group-text fw-bold">ESPECIFIQUE:</span>
                            <input type="text" class="form-control" id="P_RolEspecifique" name="P_RolEspecifique" maxlength="100" placeholder="Especifique el tipo de rol" value="<?php echo isset($datosPersona['P_Rol']) ? $datosPersona['P_Rol'] : ''; ?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="input-group mb-3">
                            <span class="input-group-text fw-bold">APELLIDO:</span>
                            <input type="text" class="form-control" id="P_Apellido" name="P_Apellido" maxlength="50" value="<?php echo isset($datosPersona['P_Apellido']) ? $datosPersona['P_Apellido'] : ''; ?>" onchange="transformarDatosMayusculas('P_Apellido')">
                        </div>
                    </div>
                    <div class="col">
                        <div class="input-group mb-3">
                            <span class="input-group-text fw-bold">NOMBRE:</span>
                            <input type="text" class="form-control" id="P_Nombre" name="P_Nombre" maxlength="50" value="<?php echo isset($datosPersona['P_Nombre']) ? $datosPersona['P_Nombre'] : ''; ?>" onchange="transformarDatosNompropio('P_Nombre')">
                        </div>
                    </div>
                    <div class="col">
                        <div class="input-group mb-3">
                            <span class="input-group-text fw-bold">ALIAS:</span>
                            <input type="text" class="form-control" id="P_Alias" name="P_Alias" maxlength="50" value="<?php echo isset($datosPersona['P_Alias']) ? $datosPersona['P_Alias'] : ''; ?>" onchange="transformarDatosNompropio('P_Alias')">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="input-group mb-3">
                            <span class="input-group-text fw-bold">DNI:</span>
                            <input type="text" class="form-control" id="P_DNI" name="P_DNI" maxlength="10" value="<?php echo isset($datosPersona['P_DNI']) ? $datosPersona['P_DNI'] : ''; ?>" onchange="transformarDatosNumerico('P_DNI')">
                        </div>
                    </div>
                    <div class="col">
                        <div class="input-group mb-3">
                            <span class="input-group-text fw-bold">EDAD:</span>
                            <input type="text" class="form-control" id="P_Edad" name="P_Edad" maxlength="3" value="<?php echo isset($datosPersona['P_Edad']) ? $datosPersona['P_Edad'] : ''; ?>" onchange="transformarDatosNumerico('P_Edad')">
                        </div>
                    </div>
                    <div class="col">
                        <div class="input-group mb-3">
                            <label class="input-group-text fw-bold" for="P_Genero">GÉNERO:</label>
                            <select id="P_Genero" class="form-select" name="P_Genero" required>
                                <?php
                                    // Valor seleccionado por defecto
                                    $selectedValue = isset($datosPersona['P_Genero']) ? $datosPersona['P_Genero'] : null;
            
                                    echo generarOpcionesSelect($Array_Genero, $selectedValue);
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="input-group mb-3">
                            <label class="input-group-text fw-bold" for="P_EstadoCivil">ESTADO CIVIL:</label>
                            <select id="P_EstadoCivil" class="form-select" name="P_EstadoCivil" required>
                                <?php
                                    // Valor seleccionado por defecto
                                    $selectedValue = isset($datosPersona['P_EstadoCivil']) ? $datosPersona['P_EstadoCivil'] : null;
            
                                    echo generarOpcionesSelect($Array_EstadoCivil, $selectedValue);
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col">
                        <div class="input-group mb-3">
                            <span class="input-group-text fw-bold">PAÍS:</span>
                            <input type="text" class="form-control" id="P_Pais" name="P_Pais" maxlength="50" value="<?php echo isset($datosPersona['P_Pais']) ? $datosPersona['P_Pais'] : 'ARGENTINA'; ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
        <div id="DomiciliosRelacionados"><!-- Entidad secundaria - Domicilios -->
        </div>

        <div id="DatosComplementarios"><!-- Entidad secundaria - Datos complementarios -->
        </div>
    </form>
</div>

<script>
        // Ejecutar la función al cargar el DOM
        document.addEventListener("DOMContentLoaded", function() {
            mostrarElementoOculto("P_Rol", "DIV_Especifique");

        // Agregar el evento onchange al elemento select
        document.getElementById("P_Rol").onchange = function() {
            mostrarElementoOculto("P_Rol", "DIV_Especifique");
        };
    });
</script>

</body>
</html>
