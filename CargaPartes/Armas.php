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
    $ArmasDeFuegoData = []; // Inicializar vacío

    // Recuperar valores por POST
    $ID = isset($_POST['ID']) ? $_POST['ID'] : '';
    $formularioID = isset($_POST['formularioID']) ? $_POST['formularioID'] : '';
    $FK_Encabezado = isset($_POST['FK_Encabezado']) ? $_POST['FK_Encabezado'] : '';
    $ClavePrimaria = isset($_POST['ClavePrimaria']) ? $_POST['ClavePrimaria'] : '';
    $NumeroDeOrden = isset($_POST['NumeroDeOrden']) ? $_POST['NumeroDeOrden'] : '';

    // Si ClavePrimaria no está vacío, recopila los datos usando DataFetcher
    if (!empty($ClavePrimaria)) {
        // Crear una ArmasDeFuegoData de DataFetcher pasándole la conexión a la base de datos
        $DataFetcher = new DataFetcher($conn);

        // Llamar al método fetchDataLugar para obtener los datos del Vehiculo
        $ArmasDeFuegoData = $DataFetcher->fetchDataAF($ClavePrimaria);
    }

    // Cierra la conexión a la base de datos
    $conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Armas de fuego</title>
  <!-- Navicon -->
  <link rel="icon" href="../CSS/Images/favicon.ico" type="Image/x-icon">
  <link rel="shortcut icon" href="../CSS/Images/favicon.ico" type="Image/x-icon">
  <!-- CSS -->
  <link rel="stylesheet" type="text/css" href="CSS/Main.css">
  <!-- JS -->
  <script src="JS/backToMain.js"></script>
  <script src="JS/Handler_AJAX.js"></script>
  <script src="JS/Handler_DatosComplementarios.js"></script>
  <script src="JS/ElementosOcultos.js"></script>
  <script src="../JS/ManejarImagenes.js"></script>
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
            <h2 class="text-light text-center m-0">FORMULARIO "<?php echo $formularioID; ?>" - 
                <?php
                    $NumeroDeOrden = intval($NumeroDeOrden);
                    if ($NumeroDeOrden === 0) {
                        echo "AGREGAR NUEVA ARMA DE FUEGO";
                    } else {
                        echo "EDITAR ARMA DE FUEGO";
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
            <button type="button" class="btn btn-success btn-lg" style="position: fixed; bottom: 0; right: 0; width: 10vw; height: 4vw; font-size: 1.1vw; margin: 0.5vw;" onclick="guardarCambiosAF()">
                <i class="bi bi-floppy"></i> <b>Guardar cambios</b>
            </button>
        </div>
    </div>
</div>

<!-- Formulario de carga/edición de entidad -->
<div class="row p-2" style="top: 5.5vw; left: 1vw; position: absolute; width: 88vw;">
    <form id="CargarArmas" name="CargarArmas" enctype="multipart/form-data" method="post">

        <div><!-- Campos ocultos necesarios para el funcionamiento de la aplicacion -->
            <input type="hidden" id="ID" name="ID" value="<?php echo htmlspecialchars($ID, ENT_QUOTES, 'UTF-8'); ?>" readonly>
            <input type="hidden" id="formularioID" name="formularioID" value="<?php echo htmlspecialchars($formularioID, ENT_QUOTES, 'UTF-8'); ?>" readonly>
            <input type="hidden" id="FK_Encabezado" name="FK_Encabezado" value="<?php echo htmlspecialchars($FK_Encabezado, ENT_QUOTES, 'UTF-8'); ?>" readonly>
            <input type="hidden" id="ClavePrimaria" name="ClavePrimaria" value="<?php echo htmlspecialchars($ClavePrimaria, ENT_QUOTES, 'UTF-8'); ?>" readonly>
            <input type="hidden" id="NumeroDeOrden" name="NumeroDeOrden" value="<?php echo htmlspecialchars($NumeroDeOrden, ENT_QUOTES, 'UTF-8'); ?>" readonly>
        </div>

        <div id="EntidadPrincipal" class="border border-black rounded bg-light p-3 m-1"><!-- Entidad principal del formulario -->

            <div class="d-flex justify-content-between align-items-center mb-2">
                <h2 class="text-center">Detalles del arma de fuego</h2>
                <?php
                    // Verificar si 'Action' ha sido enviado y su valor no es 'NuevaEntidad'
                    if (!isset($_POST['Action']) || $_POST['Action'] != 'NuevaEntidad') {
                        // Mostrar el botón si la condición es verdadera
                        echo '<div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                  <button type="button" id="quitarAF" class="btn btn-danger btn-lg me-md-2 fs-4" onclick="eliminarAF(\'\')">
                                  <i class="bi bi-exclamation-diamond-fill"></i> Eliminar</button>
                              </div>';
                    }
                ?>
            </div>

            <div class="row">
                <div class="col">
                    <div class="input-group mb-3">
                        <label class="input-group-text fw-bold" for="AF_EsDeFabricacionCasera">¿ES DE FABRICACIÓN CASERA?:</label>
                        <select class="form-select" id="AF_EsDeFabricacionCasera" name="AF_EsDeFabricacionCasera" required>
                            <option value="" disabled selected>Seleccione una opción</option>
                            <option value="1"<?php if (isset($ArmasDeFuegoData['AF_EsDeFabricacionCasera']) && $ArmasDeFuegoData['AF_EsDeFabricacionCasera'] == 1) echo ' selected'; ?>>Sí</option>
                            <option value="0"<?php if (!isset($ArmasDeFuegoData['AF_EsDeFabricacionCasera']) || $ArmasDeFuegoData['AF_EsDeFabricacionCasera'] == 0) echo ' selected'; ?>>No</option>
                        </select>
                    </div>
                </div>
                <div class="col">
                    <div class="input-group mb-3">
                        <label class="input-group-text fw-bold" for="AF_TipoAF">TIPO DE ARMA DE FUEGO:</label>
                        <select id="AF_TipoAF" class="form-select" name="AF_TipoAF" required>
                        <option value="" disabled selected>Seleccione una opción</option>
                            <option value="Carabina"<?php if (isset($ArmasDeFuegoData['AF_TipoAF']) && $ArmasDeFuegoData['AF_TipoAF'] == 'Carabina') echo ' selected'; ?>>Carabina</option>
                            <option value="Escopeta"<?php if (isset($ArmasDeFuegoData['AF_TipoAF']) && $ArmasDeFuegoData['AF_TipoAF'] == 'Escopeta') echo ' selected'; ?>>Escopeta</option>
                            <option value="Fusil"<?php if (isset($ArmasDeFuegoData['AF_TipoAF']) && $ArmasDeFuegoData['AF_TipoAF'] == 'Fusil') echo ' selected'; ?>>Fusil</option>
                            <option value="Pistola"<?php if (isset($ArmasDeFuegoData['AF_TipoAF']) && $ArmasDeFuegoData['AF_TipoAF'] == 'Pistola') echo ' selected'; ?>>Pistola</option>
                            <option value="Pistola ametralladora"<?php if (isset($ArmasDeFuegoData['AF_TipoAF']) && $ArmasDeFuegoData['AF_TipoAF'] == 'Pistola ametralladora') echo ' selected'; ?>>Pistola ametralladora</option>
                            <option value="Pistolón"<?php if (isset($ArmasDeFuegoData['AF_TipoAF']) && $ArmasDeFuegoData['AF_TipoAF'] == 'Pistolón') echo ' selected'; ?>>Pistolón</option>
                            <option value="Revolver"<?php if (isset($ArmasDeFuegoData['AF_TipoAF']) && $ArmasDeFuegoData['AF_TipoAF'] == 'Revolver') echo ' selected'; ?>>Revolver</option>
                        </select>          
                    </div>
                </div>
            </div>

            <div class="row" id="DIV_FabricacionCasera1">
                <div class="col">
                    <div class="input-group mb-3">
                        <span class="input-group-text fw-bold" id="basic-addon1">MARCA:</span>
                        <input type="text" class="form-control" id="AF_Marca" name="AF_Marca" value="<?php echo isset($ArmasDeFuegoData['AF_Marca']) ? $ArmasDeFuegoData['AF_Marca'] : ''; ?>" maxlength="25" placeholder="Marca del arma de fuego" onchange="transformarDatosNompropio('AF_Marca')">
                    </div>
                </div>
                <div class="col">
                    <div class="input-group mb-3">
                        <span class="input-group-text fw-bold" id="basic-addon1">MODELO:</span>
                        <input type="text" class="form-control" id="AF_Modelo" name="AF_Modelo" value="<?php echo isset($ArmasDeFuegoData['AF_Modelo']) ? $ArmasDeFuegoData['AF_Modelo'] : ''; ?>" maxlength="25" placeholder="Modelo del arma de fuego" onchange="transformarDatosNompropio('AF_Modelo')">
                    </div>
                </div>
                <div class="col">
                    <div class="input-group mb-3">
                        <span class="input-group-text fw-bold" id="basic-addon1">CALIBRE:</span>
                        <input type="text" class="form-control" id="AF_Calibre" name="AF_Calibre" value="<?php echo isset($ArmasDeFuegoData['AF_Calibre']) ? $ArmasDeFuegoData['AF_Calibre'] : ''; ?>" maxlength="10" placeholder="Calibre del arma de fuego">
                    </div>
                </div>
            </div>

            <div class="row" id="DIV_FabricacionCasera2">
                <div class="col">
                    <div class="input-group mb-3">
                        <label class="input-group-text fw-bold" for="AF_PoseeNumeracionVisible">¿POSEE NUMERACIÓN VISIBLE?:</label>
                        <select class="form-select" id="AF_PoseeNumeracionVisible" name="AF_PoseeNumeracionVisible" required>
                            <option value="1"<?php if (!isset($ArmasDeFuegoData['AF_PoseeNumeracionVisible']) || $ArmasDeFuegoData['AF_PoseeNumeracionVisible'] == 1) echo ' selected'; ?>>Sí</option>
                            <option value="0"<?php if (isset($ArmasDeFuegoData['AF_PoseeNumeracionVisible']) && $ArmasDeFuegoData['AF_PoseeNumeracionVisible'] == 0) echo ' selected'; ?>>No</option>
                        </select>
                    </div>
                </div>
                <div class="col" id="DIV_Numeracion">
                    <div class="input-group mb-3">
                        <span class="input-group-text fw-bold" id="basic-addon1">NÚMERO DE SERIE:</span>
                        <input type="text" class="form-control" id="AF_NumeroDeSerie" name="AF_NumeroDeSerie" value="<?php echo isset($ArmasDeFuegoData['AF_NumeroDeSerie']) ? $ArmasDeFuegoData['AF_NumeroDeSerie'] : ''; ?>" maxlength="50" value="" onchange="transformarDatosAlfaNumerico('AF_NumeroDeSerie')">
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
        ocultarFabricacionCasera("AF_EsDeFabricacionCasera", "DIV_FabricacionCasera1", "DIV_FabricacionCasera2");
        mostrarElementoOculto("AF_PoseeNumeracionVisible", "DIV_Numeracion");

    // Agregar el evento onchange al elemento select
    document.getElementById("AF_EsDeFabricacionCasera").onchange = function() {
        ocultarFabricacionCasera("AF_EsDeFabricacionCasera", "DIV_FabricacionCasera1", "DIV_FabricacionCasera2");
    };

    // Agregar el evento onchange al elemento select
    document.getElementById("AF_PoseeNumeracionVisible").onchange = function() {
        mostrarElementoOculto("AF_PoseeNumeracionVisible", "DIV_Numeracion");
    };
});
</script>

</body>
</html>
