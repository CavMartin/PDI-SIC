<?php
    require '../PHP/ServerConnect.php'; // Conectar a la base de datos
    require 'PHP/DataFetcher.php'; // Clase para recopilar datos para mostrar en la pagina

    // Sí el usuario no esta logeado, redirigirlo a la página de inicio de sesión
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header("Location: ../Login.php");
        exit();
    }

    // Si el método para acceder a la pagina no es POST, redirige a Main.php
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        header("Location: ../Main.php");
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
    $conn = open_database_connection('sic');
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    // Recuperar valor de $ID
    $ID = $_POST['ID'] ?? '';
    $formularioID = $_POST['formularioID'] ?? '';
    $FK_Encabezado = $_POST['ID'] ?? '';

    // Consultar si ID ya existe en la base de datos
    $stmt = $conn->prepare("SELECT COUNT(*) FROM entidad_encabezado WHERE ID = ?");
    $stmt->bind_param("s", $ID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_array();
    $existeID = $row[0] > 0;
    //Sino existe, redirige automaticamente al formulario de creación del encabezado
    if (!$existeID) {
        echo "<form id='formRedirect' action='Encabezado.php' method='post'>
                <input type='hidden' name='ID' value='" . htmlspecialchars($ID, ENT_QUOTES, 'UTF-8') . "'>
                <input type='hidden' name='FK_Encabezado' value='" . htmlspecialchars($FK_Encabezado, ENT_QUOTES, 'UTF-8') . "'>
                <input type='hidden' name='IP_Existe' value='0'>
              </form>
              <script>
                  document.getElementById('formRedirect').submit();
              </script>";
        exit();
    }

    // Crear una instancia de la clase DataFetcher
    $DataFetcher = new DataFetcher($conn);
        // Llamar al método fetchDataMain de la clase DataFetcher
        $DatosMain = $DataFetcher->fetchDataMain($ID);
            // Acceso a los datos con comprobación
            $Personas = isset($DatosMain['Personas']) ? $DatosMain['Personas'] : [];
            $Lugares = isset($DatosMain['Lugares']) ? $DatosMain['Lugares'] : [];
            $Vehiculos = isset($DatosMain['Vehiculos']) ? $DatosMain['Vehiculos'] : [];
            $Armas = isset($DatosMain['Armas']) ? $DatosMain['Armas'] : [];
            $Secuestros = isset($DatosMain['Secuestros']) ? $DatosMain['Secuestros'] : [];

    // Cierra la conexión a la base de datos
    $conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Main</title>
  <!-- Favicon -->
  <link rel="icon" href="../CSS/Images/favicon.ico" type="Image/x-icon">
  <link rel="shortcut icon" href="../CSS/Images/favicon.ico" type="Image/x-icon">
  <!-- CSS -->
  <link rel="stylesheet" type="text/css" href="CSS/Main.css">
  <!-- JS Main -->
  <script src="JS/Main.js"></script>
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

<body class="bg-secondary" >

<!-- Barra de navegación -->
<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
    <div class="container-fluid d-flex justify-content-between align-items-center">

        <!-- Boton Volver -->
        <div style="position: absolute; left: 0;">
          <button type="button" class="btn btn-warning btn-lg m-3" onclick="window.location.href='index.php'">
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

<input type="hidden" name="ID" id="ID" value="<?php echo htmlspecialchars($ID); ?>">

<div class="MainLeftDiv">
    <!-- Boton de navegación al encabezado -->
    <form method="POST" id="Encabezado" action="Encabezado.php">
        <input type="hidden" name="ID" value="<?php echo htmlspecialchars($ID); ?>">
        <input type="hidden" name="formularioID" value="<?php echo htmlspecialchars($formularioID); ?>">
        <button type="submit" class="CustomButton B_Encabezado">Encabezado</button>
    </form>

    <!-- Boton de navegación a la entidad "Personas" -->
    <button type="button" class="CustomButton B_Personas" onclick="MostrarContenedor('Contenedor_Personas')">Personas</button>

    <!-- Boton de navegación a la entidad "Lugares" -->
    <button type="button" class="CustomButton B_Lugares" onclick="MostrarContenedor('Contenedor_Lugares')">Lugares</button>

    <!-- Boton de navegación a la entidad "Vehículos" -->
    <button type="button" class="CustomButton B_Vehiculos" onclick="MostrarContenedor('Contenedor_Vehiculos')">Vehículos</button>

    <!-- Boton de navegación a la entidad "Armas" -->
    <button type="button" class="CustomButton B_Armas" onclick="MostrarContenedor('Contenedor_Armas')">Armas</button>

    <!-- Boton de navegación a la entidad "Secuestros" -->
    <button type="button" class="CustomButton B_Ampliacion" onclick="MostrarContenedor('Contenedor_Secuestros')">Secuestros</button>
</div>

    <!-- Contenedor inicial -->
    <div class="Contenedor_Entidades" id="Contenedor_Inicial" style="display: block;">
    </div>

    <div class="Contenedor_Entidades" id="Contenedor_Personas" style="display: none;"><!-- Contenedor de Personas ocultos -->
        <div class="Div_AgregarEntidad"><!-- Boton para agregar Personas -->
            <form method="POST" id="Personas" action="Personas.php">
                <input type="hidden" name="ID" value="<?php echo htmlspecialchars($ID); ?>">
                <input type="hidden" name="formularioID" value="<?php echo htmlspecialchars($formularioID); ?>">
                <input type="hidden" name="ClavePrimaria" value="">
                <input type="hidden" name="FK_Encabezado" value="<?php echo htmlspecialchars($FK_Encabezado); ?>">
                <input type="hidden" name="NumeroDeOrden" value="0">
                <input type="hidden" name="Action" value="NuevaEntidad">
                <button type="submit" class="CustomButton BTN_AgregarEntidad Personas">Agregar nueva persona</button>
            </form>
        </div>

        <div class="DivEnumerarEntidades" id="Enumerar_Personas">
            <?php foreach ($Personas as $Persona): ?>
                <form method="POST" id="EditarMensajes<?php echo htmlspecialchars($Persona['NumeroDeOrden']); ?>" action="Personas.php">
                    <input type="hidden" name="ID" value="<?php echo htmlspecialchars($ID); ?>">
                    <input type="hidden" name="formularioID" value="<?php echo htmlspecialchars($formularioID); ?>">
                    <input type="hidden" name="ClavePrimaria" value="<?php echo htmlspecialchars($Persona['ID_Persona']); ?>">
                    <input type="hidden" name="FK_Encabezado" value="<?php echo htmlspecialchars($FK_Encabezado); ?>">
                    <input type="hidden" name="NumeroDeOrden" value="<?php echo htmlspecialchars($Persona['NumeroDeOrden']); ?>">
                    <button type="submit" class="CustomButton CartaEntidad 
                    <?php // Controla la imagen del icono segun el genero
                        if ($Persona['P_Genero'] == 'Varón') {
                            echo 'Hombre';
                        } elseif ($Persona['P_Genero'] == 'Mujer') {
                            echo 'Mujer';
                        } else {
                            echo 'OtroGenero';
                        }
                    ?>">     
                    
                    <?php if (!empty($Persona['P_Apellido'])): ?>
                        Apellido: <?php echo htmlspecialchars($Persona['P_Apellido']); ?><br>
                    <?php else: ?>
                        Apellido: S/D<br>
                    <?php endif; ?>

                    <?php if (!empty($Persona['P_Nombre'])): ?>
                        Nombre: <?php echo htmlspecialchars($Persona['P_Nombre']); ?><br>
                    <?php else: ?>
                        Nombre: S/D<br>
                    <?php endif; ?>

                    <?php if (!empty($Persona['P_DNI'])): ?>
                        DNI: <?php echo htmlspecialchars($Persona['P_DNI']); ?><br>
                    <?php else: ?>
                        DNI: S/D<br>
                    <?php endif; ?>
                </button>
                </form>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="Contenedor_Entidades" id="Contenedor_Lugares" style="display: none;"><!-- Contenedor de Lugares ocultos -->
        <div class="Div_AgregarEntidad"><!-- Boton para agregar Lugares -->
            <form method="POST" id="Lugares" action="Lugares.php">
                <input type="hidden" name="ID" value="<?php echo htmlspecialchars($ID); ?>">
                <input type="hidden" name="formularioID" value="<?php echo htmlspecialchars($formularioID); ?>">
                <input type="hidden" name="ClavePrimaria" value="">
                <input type="hidden" name="FK_Encabezado" value="<?php echo htmlspecialchars($FK_Encabezado); ?>">
                <input type="hidden" name="NumeroDeOrden" value="0">
                <input type="hidden" name="Action" value="NuevaEntidad">
                <button type="submit" class="CustomButton BTN_AgregarEntidad Lugar">Agregar nuevo lugar del hecho</button>
            </form>
        </div>

        <div class="DivEnumerarEntidades" id="Enumerar_Lugares">
            <?php foreach ($Lugares as $Lugar): ?>
                <form method="POST" id="EditarMensajes<?php echo htmlspecialchars($Lugar['NumeroDeOrden']); ?>" action="Lugares.php">
                    <input type="hidden" name="ID" value="<?php echo htmlspecialchars($ID); ?>">
                    <input type="hidden" name="formularioID" value="<?php echo htmlspecialchars($formularioID); ?>">
                    <input type="hidden" name="ClavePrimaria" value="<?php echo htmlspecialchars($Lugar['ID_Lugar']); ?>">
                    <input type="hidden" name="FK_Encabezado" value="<?php echo htmlspecialchars($FK_Encabezado); ?>">
                    <input type="hidden" name="NumeroDeOrden" value="<?php echo htmlspecialchars($Lugar['NumeroDeOrden']); ?>">
                    <button type="submit" class="CustomButton CartaEntidad 
                    <?php 
                        $tipoLugar = $Lugar['L_TipoLugar'];
                            switch ($tipoLugar) {
                                case 'Vía pública':
                                    echo 'ViaPublica';
                                    break;
                                case 'Plaza / Parque':
                                    echo 'Plaza';
                                    break;
                                case 'Ruta / camino':
                                    echo 'Ruta';
                                    break;
                                case 'Cochera / playa de estacionamiento':
                                    echo 'Cochera';
                                    break;
                                case 'Descampado':
                                    echo 'Descampado';
                                    break;
                                case 'Exterior de asociación civil':
                                case 'Interior de asociación civil':
                                    echo 'AsociacionCivil';
                                    break;
                                case 'Exterior de comercio':
                                case 'Interior de comercio':
                                    echo 'Comercio';
                                    break;
                                case 'Exterior de dependencia pública':
                                case 'Interior de dependencia pública':
                                    echo 'DependenciaPublica';
                                    break;
                                case 'Exterior de industria':
                                case 'Interior de industria':
                                    echo 'Industria';
                                    break;
                                case 'Exterior de inmueble':
                                case 'Interior de inmueble':
                                    echo 'Inmueble';
                                    break;
                                case 'Exterior de institución pública':
                                case 'Interior de institución pública':
                                    echo 'Institucion';
                                    break;
                                case 'Exterior de vehículo':
                                case 'Interior de vehículo':
                                    echo 'Automovil';
                                    break;
                                default:
                                    echo 'Lugar';
                                    break;
                            }
                    ?>">
                    
                        <?php if (!empty($Lugar['L_Calle'])): ?>
                            Calle: <?php echo htmlspecialchars($Lugar['L_Calle']); ?><br>
                        <?php endif; ?>

                        <?php if (!empty($Lugar['L_AlturaCatastral'])): ?>
                            Catastral: <?php echo htmlspecialchars($Lugar['L_AlturaCatastral']); ?><br>
                        <?php endif; ?>

                        <?php if (!empty($Lugar['L_Interseccion1'])): ?>
                            Entre calle: <?php echo htmlspecialchars($Lugar['L_Interseccion1']); ?><br>
                        <?php endif; ?>

                        <?php if (!empty($Lugar['L_Interseccion2'])): ?>
                            Y calle: <?php echo htmlspecialchars($Lugar['L_Interseccion2']); ?><br>
                        <?php endif; ?>
                    </button>
                </form>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="Contenedor_Entidades" id="Contenedor_Vehiculos" style="display: none;"><!-- Contenedor de Vehiculos ocultos -->
        <div class="Div_AgregarEntidad"><!-- Boton para agregar Vehiculos -->
            <form method="POST" id="Vehiculos" action="Vehiculos.php">
                <input type="hidden" name="ID" value="<?php echo htmlspecialchars($ID); ?>">
                <input type="hidden" name="formularioID" value="<?php echo htmlspecialchars($formularioID); ?>">
                <input type="hidden" name="ClavePrimaria" value="">
                <input type="hidden" name="FK_Encabezado" value="<?php echo htmlspecialchars($FK_Encabezado); ?>">
                <input type="hidden" name="NumeroDeOrden" value="0">
                <input type="hidden" name="Action" value="NuevaEntidad">
                <button type="submit" class="CustomButton BTN_AgregarEntidad Vehiculo">Agregar nuevo vehículo</button>
            </form>
        </div>

        <div class="DivEnumerarEntidades" id="Enumerar_Vehiculos">
            <?php foreach ($Vehiculos as $Vehiculo): ?>
                <form method="POST" id="EditarMensajes<?php echo htmlspecialchars($Vehiculo['NumeroDeOrden']); ?>" action="Vehiculos.php">
                    <input type="hidden" name="ID" value="<?php echo htmlspecialchars($ID); ?>">
                    <input type="hidden" name="formularioID" value="<?php echo htmlspecialchars($formularioID); ?>">
                    <input type="hidden" name="ClavePrimaria" value="<?php echo htmlspecialchars($Vehiculo['ID_Vehiculo']); ?>">
                    <input type="hidden" name="FK_Encabezado" value="<?php echo htmlspecialchars($FK_Encabezado); ?>">
                    <input type="hidden" name="NumeroDeOrden" value="<?php echo htmlspecialchars($Vehiculo['NumeroDeOrden']); ?>">
                    <button type="submit" class="CustomButton CartaEntidad 
                    <?php 
                        $tipoVehiculo = $Vehiculo['V_TipoVehiculo'];
                            switch ($tipoVehiculo) {
                                case 'Acoplado':
                                    echo 'Acoplado';
                                    break;
                                case 'Automóvil':
                                    echo 'Automovil';
                                    break;
                                case 'Avioneta':
                                    echo 'Avioneta';
                                    break;
                                case 'Bicicleta':
                                case 'Bicicleta eléctrica':
                                    echo 'Bicicleta';
                                    break;
                                case 'Camión':
                                case 'Chasis de camión':
                                    echo 'Camion';
                                    break;
                                case 'Camioneta':
                                    echo 'Camioneta';
                                    break;
                                case 'Ciclomotor':
                                    echo 'Ciclomotor';
                                    break;
                                case 'Cuatriciclo':
                                    echo 'Cuatriciclo';
                                    break;
                                case 'Autobus':
                                    echo 'Autobus';
                                    break;
                                case 'Lancha':
                                case 'Embarcación a motor':
                                    echo 'Lancha';
                                    break;
                                case 'Furgon':
                                    echo 'Furgon';
                                    break;
                                case 'MaquinaAgricola':
                                    echo 'MaquinaAgricola';
                                    break;
                                case 'Maquina de construccion':
                                    echo 'MaquinaConstruccion';
                                    break;
                                case 'Recolector':
                                    echo 'Recolector';
                                    break;
                                case 'Moto vehículo':
                                    echo 'Motovehiculo';
                                    break;
                                case 'Moto vehículo acuático':
                                    echo 'MotovehiculoAcuatico';
                                    break;
                                case 'Tractor':
                                    echo 'Tractor';
                                    break;
                                case 'Triciclo':
                                    echo 'Triciclo';
                                    break;
                                case  'Vehículo oficial':
                                    echo 'VehiculoOficial';
                                    break;
                                case 'Vehículo a tracción animal (Carros)':
                                    echo 'Carro';
                                    break;
                                default:
                                    echo 'Vehiculo';
                                    break;
                            }
                        ?>">

                        <?php if (!empty($Vehiculo['TipoDeVehiculo'])): ?>
                            Tipo: <?php echo htmlspecialchars($Vehiculo['TipoDeVehiculo']); ?><br>
                        <?php endif; ?>

                        <?php if (!empty($Vehiculo['V_Marca'])): ?>
                            Marca: <?php echo htmlspecialchars($Vehiculo['V_Marca']); ?><br>
                        <?php else: ?>
                            Marca: S/D<br>
                        <?php endif; ?>

                        <?php if (!empty($Vehiculo['V_Modelo'])): ?>
                            Modelo: <?php echo htmlspecialchars($Vehiculo['V_Modelo']); ?><br>
                        <?php else: ?>
                            Modelo: S/D<br>
                        <?php endif; ?>

                        <?php if (!empty($Vehiculo['V_Dominio'])): ?>
                            Dominio: <?php echo htmlspecialchars($Vehiculo['V_Dominio']); ?><br>
                        <?php else: ?>
                            Dominio: S/D<br>
                        <?php endif; ?>
                    </button>
                </form>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="Contenedor_Entidades" id="Contenedor_Armas" style="display: none;"><!-- Contenedor de Armas ocultos -->
        <div class="Div_AgregarEntidad"><!-- Boton para agregar Armas -->
            <form method="POST" id="Armas" action="Armas.php">
                <input type="hidden" name="ID" value="<?php echo htmlspecialchars($ID); ?>">
                <input type="hidden" name="formularioID" value="<?php echo htmlspecialchars($formularioID); ?>">
                <input type="hidden" name="FK_Encabezado" value="<?php echo htmlspecialchars($FK_Encabezado); ?>">
                <input type="hidden" name="NumeroDeOrden" value="0">
                <input type="hidden" name="Action" value="NuevaEntidad">
                <button type="submit" class="CustomButton BTN_AgregarEntidad Arma">Agregar nueva arma</button>
            </form>
        </div>

        <div class="DivEnumerarEntidades" id="Enumerar_Armas">
            <?php foreach ($Armas as $Arma): ?>
                <form method="POST" id="EditarArmas<?php echo htmlspecialchars($Arma['NumeroDeOrden']); ?>" action="Armas.php">
                    <input type="hidden" name="ID" value="<?php echo htmlspecialchars($ID); ?>">
                    <input type="hidden" name="formularioID" value="<?php echo htmlspecialchars($formularioID); ?>">
                    <input type="hidden" name="ClavePrimaria" value="<?php echo htmlspecialchars($Arma['ID_Arma']); ?>">
                    <input type="hidden" name="FK_Encabezado" value="<?php echo htmlspecialchars($FK_Encabezado); ?>">
                    <input type="hidden" name="NumeroDeOrden" value="<?php echo htmlspecialchars($Arma['NumeroDeOrden']); ?>">
                    <button type="submit" class="CustomButton CartaEntidad
                        <?php 
                        $tipoAF = $Arma['AF_TipoAF'];
                            switch ($tipoAF) {
                                case 'Carabina':
                                    echo 'Carabina';
                                    break;
                                case 'Escopeta':
                                    echo 'Escopeta';
                                    break;
                                case 'Fusil':
                                    echo 'Fusil';
                                    break;
                                case 'Pistola':
                                    echo 'Pistola';
                                    break;
                                case 'Ametralladora':
                                    echo 'Ametralladora';
                                    break;
                                case 'Pistolon':
                                    echo 'Pistolon';
                                    break;
                                case 'Revolver':
                                    echo 'Revolver';
                                    break;
                                default:
                                    echo 'Pistola';
                                    break;
                            }
                        ?>">

                        <?php if (!empty($Arma['Clasificacion'])): ?>
                            Clasificación: <?php echo htmlspecialchars($Arma['Clasificacion']); ?><br>
                        <?php endif; ?>

                        <?php if (!empty($Arma['AF_Marca'])): ?>
                            Marca: <?php echo htmlspecialchars($Arma['AF_Marca']); ?><br>
                        <?php else: ?>
                            Marca: S/D<br>
                        <?php endif; ?>

                        <?php if (!empty($Arma['AF_Modelo'])): ?>
                            Modelo: <?php echo htmlspecialchars($Arma['AF_Modelo']); ?><br>
                        <?php else: ?>
                            Modelo: S/D<br>
                        <?php endif; ?>

                        <?php if (!empty($Arma['AF_Calibre'])): ?>
                            Calibre: <?php echo htmlspecialchars($Arma['AF_Calibre']); ?><br>
                        <?php else: ?>
                            Calibre: S/D<br>
                        <?php endif; ?>
                    </button>
                </form>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="Contenedor_Entidades" id="Contenedor_Secuestros" style="display: none;"><!-- Contenedor de Secuestros ocultos -->
        <div class="Div_AgregarEntidad">
            <!-- Boton para agregar Secuestros -->
            <form method="POST" id="Secuestros" action="Secuestros.php">
                <input type="hidden" name="ID" value="<?php echo htmlspecialchars($ID); ?>">
                <input type="hidden" name="formularioID" value="<?php echo htmlspecialchars($formularioID); ?>">
                <input type="hidden" name="ClavePrimaria" value="">
                <input type="hidden" name="FK_Encabezado" value="<?php echo htmlspecialchars($FK_Encabezado); ?>">
                <input type="hidden" name="NumeroDeOrden" value="0">
                <input type="hidden" name="Action" value="NuevaEntidad">
                <button type="submit" class="CustomButton BTN_AgregarEntidad Secuestro">Agregar nuevo secuestro</button>
            </form>
        </div>

        <div class="DivEnumerarEntidades" id="Enumerar_Secuestros">
            <?php foreach ($Secuestros as $Secuestro): ?>
                <form method="POST" id="EditarSecuestros<?php echo htmlspecialchars($Secuestro['NumeroDeOrden']); ?>" action="Secuestros.php">
                    <input type="hidden" name="ID" value="<?php echo htmlspecialchars($ID); ?>">
                    <input type="hidden" name="formularioID" value="<?php echo htmlspecialchars($formularioID); ?>">
                    <input type="hidden" name="ClavePrimaria" value="<?php echo htmlspecialchars($Secuestro['ID_DatoComplementario']); ?>">
                    <input type="hidden" name="FK_Encabezado" value="<?php echo htmlspecialchars($FK_Encabezado); ?>">
                    <input type="hidden" name="NumeroDeOrden" value="<?php echo htmlspecialchars($Secuestro['NumeroDeOrden']); ?>">
                    <button type="submit" style="font-size: 1.5vw; text-align: center;" class="CustomButton CartaEntidad Secuestro">Secuestro</button>
                </form>
            <?php endforeach; ?>
        </div>
    </div>

</body>
</html>
