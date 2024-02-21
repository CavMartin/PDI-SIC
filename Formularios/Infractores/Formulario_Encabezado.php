<?php
    require '../../PHP/ServerConnect.php'; // Conectar a la base de datos
    require '../PHP/ArraysManager.php'; // Manejador de arrays
    require '../PHP/DataFetcher.php'; // Clase para recopilar datos
    
    // Sí el usuario no esta logeado, redirigirlo a la página de inicio de sesión
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header("Location: ../../Login.php");
        exit();
    }
    
    // Si el método para acceder a la pagina no es POST, redirige a Main.php
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        header("Location: ../../Main.php");
        exit();
    }
    
    // Verifica si el rol de usuario almacenado en la sesión es igual a 1
    if (isset($_SESSION['rolUsuario']) && $_SESSION['rolUsuario'] === 1) {
        // Si el rol es igual a 1, habilita los ajustes del INI para mostrar errores en pantalla
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }
    
    // Conexión a la base de datos
    $conn = open_database_connection();
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
    
    // Recoger valores de POST
    $IPData = []; // Inicializar vacío
    
    $ID = isset($_POST['ID']) ? $_POST['ID'] : '';
    
    // Si ID no está vacío, recopila los datos usando DataFetcher
    if (!empty($ID)) {
        // Crear una IPData de DataFetcher pasándole la conexión a la base de datos
        $DataFetcher = new DataFetcher($conn);
    
        // Llamar al método fetchDataEncabezado para obtener los datos de la IP
        $IPData = $DataFetcher->fetchDataEncabezado($ID);
    }
    
    // Cierra la conexión a la base de datos
    $conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IP - Encabezado</title>
    <!-- Favicon -->
    <link rel="icon" href="../../CSS/Images/favicon.ico" type="Image/x-icon">
    <link rel="shortcut icon" href="../../CSS/Images/favicon.ico" type="Image/x-icon">
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="CSS/Main.css">
    <!-- JS -->
    <script src="JS/Handler_AJAX.js"></script>
    <script src="../../JS/TransformarDatos.js"></script>
    <!-- JQuery -->
    <script src="../../JQuery/jquery-3.7.1.min.js"></script>
    <!-- SweetAlert -->
    <script src="JS/SweetAlert.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap -->
    <script src="../../Bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../../Bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../Bootstrap/Icons/font/bootstrap-icons.css">
</head>

<body class="bg-secondary">

<!-- Barra de navegación -->
<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark" style="height: 5vw;">
    <div class="container-fluid d-flex align-items-center justify-content-center">
        
        <!-- Imagen 1 -->
        <div>
            <img src="../../CSS/Images/PSF.png" alt="Icono" style="width: 4vw; margin: 1vw;"><!-- Icono -->
        </div>

        <!-- Título centrado -->
        <div class="text-center">
            <h1 class="text-light">FICHA DE INFRACTOR #<?php echo $ID; ?></h1>
        </div>

        <!-- Imagen 2 -->
        <div>
            <img src="../../CSS/Images/OJO.png" alt="Icono" style="width: 4vw; margin: 1vw;"><!-- Icono -->
        </div>

        <!-- Botón de navegación a la página principal -->
        <?php
            // Verificar si el campo IP_Existe se ha enviado y su valor es igual a '0'
            if (isset($_POST['IP_Existe']) && $_POST['IP_Existe'] == '0') {
                // Generar el botón de redirección
                echo '<div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="button" class="btn btn-primary btn-lg" onclick="window.location.href=\'../../Main.php\';" style="position: fixed; top: 0; left: 0; width: 12vw; height:4vw; font-size: 1.5vw; margin: 0.5vw;">
                            <i class="bi bi-arrow-left-square-fill"></i> <b>VOLVER</b>
                        </button>
                      </div>';
            } else {
                // Generar el botón normal
                echo '<div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="button" id="VolverButton" class="btn btn-primary btn-lg" style="position: fixed; top: 0; left: 0; width: 12vw; height:4vw; font-size: 1.5vw; margin: 0.5vw;">
                            <i class="bi bi-arrow-left-square-fill"></i> <b>VOLVER</b>
                        </button>
                      </div>';
            }
        ?>
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
        
        <div><!-- Campos ocultos necesarios para el funcionamiento de la aplicacion -->
            <input type="hidden" id="ID" name="ID" value="<?php echo htmlspecialchars($ID, ENT_QUOTES, 'UTF-8'); ?>" readonly>
        </div>

        <div id="EntidadPrincipal" class="row border border-black rounded bg-light p-1 m-1"><!-- Entidad principal del formulario -->
            
            <div class="row mt-3">
                <div class="col">
                    <div class="input-group mb-3">
                        <span class="input-group-text fw-bold">FECHA DEL HECHO:</span>
                        <input type="date" class="form-control" id="Fecha" name="Fecha" value="<?php echo isset($IPData['Fecha']) ? $IPData['Fecha'] : ''; ?>" required>
                    </div>
                </div>
                <div class="col">
                    <div class="input-group mb-3">
                        <label for="Tipo" class="input-group-text fw-bold">TIPO DE FICHA:</label>
                        <select id="Tipo" class="form-select" name="Tipo" required>
                            <option value="Ficha de infractores">Ficha de infractores</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col">
                    <div class="input-group mb-3">
                    <span class="input-group-text fw-bold">JUZGADO INTERVINIENTE:</span>
                        <input type="text" id="Juzgado" class="form-control" name="Juzgado" maxlength="50" value="<?php echo isset($IPData['Juzgado']) ? $IPData['Juzgado'] : ''; ?>">
                    </div>
                </div>
                <div class="col">
                    <div class="input-group mb-3">
                    <span class="input-group-text fw-bold">DEPENDENCIA INTERVINIENTE:</span>
                        <input type="text" id="Dependencia" class="form-control" name="Dependencia" maxlength="50" value="<?php echo isset($IPData['Dependencia']) ? $IPData['Dependencia'] : ''; ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="input-group mb-3">
                    <span class="input-group-text fw-bold">CAUSA:</span>
                        <input type="text" id="Causa" class="form-control" name="Causa" maxlength="50" value="<?php echo isset($IPData['Causa']) ? $IPData['Causa'] : ''; ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="input-group mb-3">
                    <span class="input-group-text fw-bold">RELATO DEL HECHO:</span>
                    <textarea id="Relato" class="form-control" name="Relato" rows="20" required><?php echo isset($IPData['Relato']) ? $IPData['Relato'] : ''; ?></textarea>
                </div>
            </div>
        </div>
    </form>
</div>

</body>
</html>
