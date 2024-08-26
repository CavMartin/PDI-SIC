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
    $SecuestrosData = []; // Inicializar vacío

    // Obtener el ID de usuario del usuario logeado desde la sesión
    $usernameID = $_SESSION['usernameID'];

    // Recuperar valores por POST
    $ID = isset($_POST['ID']) ? $_POST['ID'] : '';
    $formularioID = isset($_POST['formularioID']) ? $_POST['formularioID'] : '';
    $FK_Encabezado = isset($_POST['FK_Encabezado']) ? $_POST['FK_Encabezado'] : '';
    $ClavePrimaria = isset($_POST['ClavePrimaria']) ? $_POST['ClavePrimaria'] : '';
    $NumeroDeOrden = isset($_POST['NumeroDeOrden']) ? $_POST['NumeroDeOrden'] : '';

    // Si ClavePrimaria no está vacío, recopila los datos usando DataFetcher
    if (!empty($ClavePrimaria)) {
        // Crear una SecuestrosData de DataFetcher pasándole la conexión a la base de datos
        $DataFetcher = new DataFetcher($conn);

        // Llamar al método fetchDataMensajes para obtener los datos del mensaje
        $SecuestrosData = $DataFetcher->fetchDataSecuestros($ClavePrimaria);
    }

    // Cierra la conexión a la base de datos
    $conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Secuestros</title>
  <!-- Favicon -->
  <link rel="icon" href="../CSS/Images/favicon.ico" type="Image/x-icon">
  <link rel="shortcut icon" href="../CSS/Images/favicon.ico" type="Image/x-icon">
  <!-- CSS -->
  <link rel="stylesheet" type="text/css" href="CSS/Main.css">
  <!-- JS  -->
  <script src="JS/backToMain.js"></script>
  <script src="JS/Handler_AJAX.js"></script>
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
                    $NumeroDeOrden = intval($NumeroDeOrden); // Convierte a entero si es una cadena
                    if ($NumeroDeOrden === 0) {
                      echo "AGREGAR NUEVO SECUESTRO";
                    } else {
                      echo "EDITAR SECUESTRO";
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
            <button type="button" class="btn btn-success btn-lg" style="position: fixed; bottom: 0; right: 0; width: 10vw; height: 4vw; font-size: 1.5vw; margin: 0.5vw;" onclick="guardarCambiosSecuestro()">
                <i class="bi bi-floppy"></i> <b>Guardar</b>
            </button>
        </div>
    </div>
</div>

<!-- Formulario de carga/edición de entidad -->
<div class="row p-2" style="top: 5.5vw; left: 1vw; position: absolute; width: 88vw;">
    <form id="CargarSecuestro" name="CargarSecuestro" enctype="multipart/form-data" method="post">

        <div><!-- Campos ocultos necesarios para el funcionamiento de la aplicacion -->
            <input type="hidden" id="ID" name="ID" value="<?php echo htmlspecialchars($ID, ENT_QUOTES, 'UTF-8'); ?>" readonly>
            <input type="hidden" id="formularioID" name="formularioID" value="<?php echo htmlspecialchars($formularioID, ENT_QUOTES, 'UTF-8'); ?>" readonly>
            <input type="hidden" id="FK_Encabezado" name="FK_Encabezado" value="<?php echo htmlspecialchars($FK_Encabezado, ENT_QUOTES, 'UTF-8'); ?>" readonly>
            <input type="hidden" id="ClavePrimaria" name="ClavePrimaria" value="<?php echo htmlspecialchars($ClavePrimaria, ENT_QUOTES, 'UTF-8'); ?>" readonly>
            <input type="hidden" id="NumeroDeOrden" name="NumeroDeOrden" value="<?php echo htmlspecialchars($NumeroDeOrden, ENT_QUOTES, 'UTF-8'); ?>" readonly>
        </div>

        <div id="EntidadPrincipal" class="border border-black rounded bg-light p-3 m-1"><!-- Entidad principal del formulario -->
        
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h2 class="text-center">Detalles del secuestro</h2>
                <?php
                    // Verificar si 'Action' ha sido enviado y su valor no es 'NuevaEntidad'
                    if (!isset($_POST['Action']) || $_POST['Action'] != 'NuevaEntidad') {
                        // Mostrar el botón si la condición es verdadera
                        echo '<div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                  <button type="button" id="quitarSecuestro" class="btn btn-danger btn-lg me-md-2 fs-4" onclick="eliminarSecuestro(\'\')">
                                  <i class="bi bi-envelope-dash"></i> Eliminar</button>
                              </div>';
                    }
                ?>
            </div>

            <div class="row">
                <div class="col">
                    <div class="input-group mb-3">
                        <label class="input-group-text fw-bold" for="DC_Tipo">TIPO DE SECUESTRO:</label>
                        <select id="DC_Tipo" class="form-select" name="DC_Tipo" required>
                        <?php
                            // Valor seleccionado por defecto
                            $selectedValue = isset($SecuestrosData['DC_Tipo']) ? $SecuestrosData['DC_Tipo'] : null;

                            echo generarOpcionesSelect($Array_TipoSecuestro, $selectedValue);
                        ?>
                     </select>
                    </div>
                </div>
                <div id="Div_Especifique" class="col" style="display: none;">
                    <div class="input-group mb-3">
                        <span class="input-group-text fw-bold">ESPECIFIQUE:</span>
                        <input type="text" id="DC_OtroTipo" class="form-control" name="DC_OtroTipo" maxlength="100" value="<?php echo isset($SecuestrosData['DC_Tipo']) ? $SecuestrosData['DC_Tipo'] : ''; ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="align-items-center">
                        <label for="DC_ImagenAdjunta" class="custom-file-upload" style="display: block; cursor: pointer;">
                            <span class="bi bi-file-earmark-image input-group-text fw-bold">IMAGEN DEL SECUESTRO:</span>
                            <input type="file" class="form-control mb-3" id="DC_ImagenAdjunta" name="DC_ImagenAdjunta" accept="image/*" onchange="procesarImagen(event, 'previewDC_ImagenAdjunta', 'Base64DC_ImagenAdjunta')" style="display: none;">
                            <?php
                                if (!empty($SecuestrosData['DC_ImagenAdjunta'])) {
                                    // Si existe una imagen, mostrarla
                                    $base64Image = ($SecuestrosData['DC_ImagenAdjunta']);
                                    echo '<img id="previewDC_ImagenAdjunta" class="Previsualizacion" src="' . $base64Image . '" alt="Previsualización de imagen">';
                                } else {
                                    // Si no existe una imagen, mostrar la imagen por defecto
                                    echo '<img id="previewDC_ImagenAdjunta" class="Previsualizacion" src="../CSS/Images/NoImage.jpeg" alt="Imagen por defecto">';
                                }
                            ?>
                        </label>
                        <textarea id="Base64DC_ImagenAdjunta" name="Base64DC_ImagenAdjunta" hidden><?php echo isset($SecuestrosData['DC_ImagenAdjunta']) ? $SecuestrosData['DC_ImagenAdjunta'] : ''; ?></textarea>
                    </div>
                </div>
                <div class="col-6">
                    <div class="input-group">
                        <span class="input-group-text fw-bold">DETALLES DEL SECUESTRO:</span>
                        <textarea id="DC_Comentario" name="DC_Comentario" class="form-control" rows="15"><?php echo isset($SecuestrosData['DC_Comentario']) ? $SecuestrosData['DC_Comentario'] : ''; ?></textarea>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
    // Ejecutar la función al cargar el DOM
    document.addEventListener("DOMContentLoaded", function() {
        mostrarElementoOculto("DC_Tipo", "Div_Especifique");

    // Agregar el evento onchange al elemento select
    document.getElementById("DC_Tipo").onchange = function() {
        mostrarElementoOculto("DC_Tipo", "Div_Especifique");
    };
});
</script>

</body>
</html>
