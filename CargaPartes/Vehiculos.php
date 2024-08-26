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
    $VehiculosData = []; // Inicializar vacío

    // Recuperar valores por POST
    $ID = isset($_POST['ID']) ? $_POST['ID'] : '';
    $formularioID = isset($_POST['formularioID']) ? $_POST['formularioID'] : '';
    $FK_Encabezado = isset($_POST['FK_Encabezado']) ? $_POST['FK_Encabezado'] : '';
    $ClavePrimaria = isset($_POST['ClavePrimaria']) ? $_POST['ClavePrimaria'] : '';
    $NumeroDeOrden = isset($_POST['NumeroDeOrden']) ? $_POST['NumeroDeOrden'] : '';

    // Si ClavePrimaria no está vacío, recopila los datos usando DataFetcher
    if (!empty($ClavePrimaria)) {
        // Crear una VehiculosData de DataFetcher pasándole la conexión a la base de datos
        $DataFetcher = new DataFetcher($conn);

        // Llamar al método fetchDataLugar para obtener los datos del Vehiculo
        $VehiculosData = $DataFetcher->fetchDataVehiculo($ClavePrimaria);
    }

    // Cierra la conexión a la base de datos
    $conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vehículos</title>
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
                        echo "AGREGAR NUEVO VEHÍCULO";
                    } else {
                        echo "EDITAR VEHÍCULO";
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
            <button type="button" class="btn btn-success btn-lg" style="position: fixed; bottom: 0; right: 0; width: 10vw; height: 4vw; font-size: 1.5vw; margin: 0.5vw;" onclick="guardarCambiosVehiculo()">
                <i class="bi bi-floppy"></i> <b>Guardar</b>
            </button>
        </div>
    </div>
</div>

<!-- Formulario de carga/edición de entidad -->
<div class="row p-2" style="top: 5.5vw; left: 1vw; position: absolute; width: 88vw;">
    <form id="CargarVehiculos" name="CargarVehiculos" enctype="multipart/form-data" method="post">

        <div><!-- Campos ocultos necesarios para el funcionamiento de la aplicacion -->
            <input type="hidden" id="ID" name="ID" value="<?php echo htmlspecialchars($ID, ENT_QUOTES, 'UTF-8'); ?>" readonly>
            <input type="hidden" id="formularioID" name="formularioID" value="<?php echo htmlspecialchars($formularioID, ENT_QUOTES, 'UTF-8'); ?>" readonly>
            <input type="hidden" id="FK_Encabezado" name="FK_Encabezado" value="<?php echo htmlspecialchars($FK_Encabezado, ENT_QUOTES, 'UTF-8'); ?>" readonly>
            <input type="hidden" id="ClavePrimaria" name="ClavePrimaria" value="<?php echo htmlspecialchars($ClavePrimaria, ENT_QUOTES, 'UTF-8'); ?>" readonly>
            <input type="hidden" id="NumeroDeOrden" name="NumeroDeOrden" value="<?php echo htmlspecialchars($NumeroDeOrden, ENT_QUOTES, 'UTF-8'); ?>" readonly>
        </div>

        <div id="EntidadPrincipal" class="border border-black rounded bg-light p-3 m-1"><!-- Entidad principal del formulario -->

            <div class="d-flex justify-content-between align-items-center mb-2">
                <h2 class="text-center">Detalles del vehículo</h2>
                <?php
                    // Verificar si 'Action' ha sido enviado y su valor no es 'NuevaEntidad'
                    if (!isset($_POST['Action']) || $_POST['Action'] != 'NuevaEntidad') {
                        // Mostrar el botón si la condición es verdadera
                        echo '<div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                  <button type="button" id="quitarVehiculo" class="btn btn-danger btn-lg me-md-2 fs-4" onclick="eliminarVehiculo(\'\')">
                                  <i class="bi bi-car-front"></i> Eliminar</button>
                              </div>';
                    }
                ?>
            </div>

            <div class="row">
                <div class="col">
                    <div class="input-group mb-3">
                        <label class="input-group-text fw-bold" for="V_Rol">ROL DEL VEHÍCULO:</label>
                        <select class="form-select" id="V_Rol" name="V_Rol" required>
                        <?php
                            // Valor seleccionado por defecto
                            $selectedValue = isset($VehiculosData['V_Rol']) ? $VehiculosData['V_Rol'] : null;
            
                            echo generarOpcionesSelect($Array_RolVehiculo, $selectedValue);
                        ?>
                        </select>
                    </div>
                </div>
                <div class="col" id="DIV_Especifique" style="display: none;">
                    <div class="input-group mb-3">
                        <span class="input-group-text fw-bold" id="basic-addon1">ESPECIFIQUE:</span>
                        <input type="text" class="form-control" id="V_RolEspecifique" name="V_RolEspecifique" value="<?php echo isset($VehiculosData['V_Rol']) ? $VehiculosData['V_Rol'] : ''; ?>" maxlength="100" placeholder="Especifique el rol del vehículo">
                    </div>
                </div>
                <div class="col">
                    <div class="input-group mb-3">
                        <label class="input-group-text fw-bold" for="V_TipoVehiculo">TIPO DE VEHÍCULO:</label>
                        <select id="V_TipoVehiculo" class="form-select" name="V_TipoVehiculo" required>
                        <?php
                            // Valor seleccionado por defecto
                            $selectedValue = isset($VehiculosData['V_TipoVehiculo']) ? $VehiculosData['V_TipoVehiculo'] : null;

                            echo generarOpcionesSelect($Array_TipoVehiculo, $selectedValue);
                        ?>
                        </select>            
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="input-group mb-3">
                        <span class="input-group-text fw-bold" id="basic-addon1">COLOR:</span>
                        <input type="text" class="form-control" id="V_Color" name="V_Color" value="<?php echo isset($VehiculosData['V_Color']) ? $VehiculosData['V_Color'] : ''; ?>" list="colores" maxlength="50" placeholder="Color del vehículo" onchange="transformarDatosNompropio('V_Color')" required>
                        <datalist id="colores">
                            <option value="Sin Datos">Sin Datos</option>
                            <option value="Amarillo">Amarillo</option>
                            <option value="Anaranjado">Anaranjado</option>
                            <option value="Azul">Azul</option>
                            <option value="Blanco">Blanco</option>
                            <option value="Gris">Gris</option>
                            <option value="Marrón">Marrón</option>
                            <option value="Negro">Negro</option>
                            <option value="Rojo">Rojo</option>
                            <option value="Rosado">Rosado</option>
                            <option value="Verde">Verde</option>
                            <option value="Violeta">Violeta</option>
                        </datalist>  
                    </div>
                </div>
                <div class="col">
                    <div class="input-group mb-3">
                        <span class="input-group-text fw-bold" id="basic-addon1">MARCA:</span>
                        <input type="text" class="form-control" id="V_Marca" name="V_Marca" value="<?php echo isset($VehiculosData['V_Marca']) ? $VehiculosData['V_Marca'] : ''; ?>" maxlength="50" placeholder="Marca del vehículo" onchange="transformarDatosNompropio('V_Marca')">
                    </div>
                </div>
                <div class="col">
                    <div class="input-group mb-3">
                        <span class="input-group-text fw-bold" id="basic-addon1">MODELO:</span>
                        <input type="text" class="form-control" id="V_Modelo" name="V_Modelo" value="<?php echo isset($VehiculosData['V_Modelo']) ? $VehiculosData['V_Modelo'] : ''; ?>" maxlength="50" placeholder="Modelo del vehículo" onchange="transformarDatosNompropio('V_Modelo')">
                    </div>
                </div>
                <div class="col">
                    <div class="input-group mb-3">
                        <span class="input-group-text fw-bold" id="basic-addon1">AÑO:</span>
                        <input type="text" class="form-control" id="V_Año" name="V_Año" value="<?php echo isset($VehiculosData['V_Año']) ? $VehiculosData['V_Año'] : ''; ?>" maxlength="4" onchange="transformarDatosNompropio('V_Año')">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="input-group mb-3">
                        <span class="input-group-text fw-bold" id="basic-addon1">DOMINIO:</span>
                        <input type="text" class="form-control" id="V_Dominio" name="V_Dominio" value="<?php echo isset($VehiculosData['V_Dominio']) ? $VehiculosData['V_Dominio'] : ''; ?>" maxlength="50" onchange="transformarDatosAlfaNumerico('V_Dominio')">
                        <datalist id="sugerenciasCiudades">
                        </datalist>
                    </div>
                </div>
                <div class="col">
                    <div class="input-group mb-3">
                        <span class="input-group-text fw-bold" id="basic-addon1">NÚMERO DE CHASIS:</span>
                        <input type="text" class="form-control" id="V_NumeroChasis" name="V_NumeroChasis" value="<?php echo isset($VehiculosData['V_NumeroChasis']) ? $VehiculosData['V_NumeroChasis'] : ''; ?>" maxlength="50" onchange="transformarDatosAlfaNumerico('V_NumeroChasis')">
                        <datalist id="sugerenciasProvincias">
                        </datalist>
                    </div>
                </div>
                <div class="col">
                    <div class="input-group mb-3">
                        <span class="input-group-text fw-bold" id="basic-addon1">NÚMERO DE MOTOR:</span>
                        <input type="text" class="form-control" id="V_NumeroMotor" name="V_NumeroMotor" value="<?php echo isset($VehiculosData['V_NumeroMotor']) ? $VehiculosData['V_NumeroMotor'] : ''; ?>" maxlength="50" value="" onchange="transformarDatosAlfaNumerico('V_NumeroMotor')">
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
        mostrarElementoOculto("V_Rol", "DIV_Especifique");

    // Agregar el evento onchange al elemento select
    document.getElementById("V_Rol").onchange = function() {
        mostrarElementoOculto("V_Rol", "DIV_Especifique");
    };
});
</script>

</body>
</html>
