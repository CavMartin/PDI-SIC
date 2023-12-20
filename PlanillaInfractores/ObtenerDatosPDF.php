<?php
// Conectar a la base de datos de forma segura
require 'ServerConnect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // El usuario no ha iniciado sesión, redirigirlo a la página de inicio de sesión
    header("Location: Login.php");
    exit();
}

$conn = open_database_connection();
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$DispositivoSIACIP = $_POST['IP_Numero'];

if (!isset($DispositivoSIACIP) || empty($DispositivoSIACIP)) {
    // Manejo de error si IP_Numero no está configurado o es vacío
    http_response_code(400); // Bad Request
    exit("Por favor, proporcione un número de incidencia válido.");
}

// Consulta para obtener el encabezado
$query_encabezado = "SELECT
    IP_Numero,
    IP_Fecha,
    IP_Hora,
    lista_tipo_de_hecho.TipoDeHecho AS IP_TipoHecho,
    IP_OtroTipo,
    IP_Carta911,
    IP_MovilAsignado,
    IP_ZonaPriorizada,
    IP_ZonaPriorizadaEspecifique,
    IP_ResultadoDeLaIncidencia,
    lista_equipo_siacip.Equipo AS IP_EquipoCreador
FROM entidad_incidencia_priorizada
JOIN lista_tipo_de_hecho ON entidad_incidencia_priorizada.IP_TipoHecho = lista_tipo_de_hecho.ID
JOIN lista_equipo_siacip ON entidad_incidencia_priorizada.IP_EquipoCreador = lista_equipo_siacip.ID
WHERE IP_Numero = ?";

$stmt_encabezado = $conn->prepare($query_encabezado);
if (!$stmt_encabezado) {
    die("Error al preparar la consulta encabezado: " . $conn->error);
}
$stmt_encabezado->bind_param("s", $DispositivoSIACIP);
$stmt_encabezado->execute();
if ($stmt_encabezado->error) {
    die("Error en la consulta encabezado: " . $stmt_encabezado->error);
}
$result_encabezado = $stmt_encabezado->get_result();

$data_encabezado = $result_encabezado->fetch_assoc();
$stmt_encabezado->close();

// Consulta para obtener las direcciones de los lugares del hecho para el encabezado
$query_lugaresHechos = "SELECT
    ID_Lugar,
    L_Rol,
    L_Calle,
    L_AlturaCatastral,
    L_CalleDetalle,
    L_Interseccion1,
    L_Interseccion2,
    L_Localidad
    FROM entidad_lugares 
WHERE FK_Encabezado = ? AND L_Rol IN (1, 2)
ORDER BY NumeroDeOrden ASC";

$stmt_lugaresHechos = $conn->prepare($query_lugaresHechos);
if (!$stmt_lugaresHechos) {
    die("Error al preparar la consulta lugares de los Hechos: " . $conn->error);
}
$stmt_lugaresHechos->bind_param("s", $DispositivoSIACIP);
$stmt_lugaresHechos->execute();
if ($stmt_lugaresHechos->error) {
    die("Error en la consulta lugaresHechos: " . $stmt_lugaresHechos->error);
}
$result_lugaresHechos = $stmt_lugaresHechos->get_result();

$data_lugaresHechos = $result_lugaresHechos->fetch_all(MYSQLI_ASSOC);
$stmt_lugaresHechos->close();

// Preparar los datos complementarios para cada lugar del hecho.
foreach ($data_lugaresHechos as $key => $LugarHecho) {
    // Consulta para obtener los datos complementarios para cada lugar del hecho.
    $query_datos_complementarios = "SELECT
        lista_tipo_de_dato_complementario.TipoDeDato AS DC_Tipo,
        DC_ImagenAdjunta,
        DC_Comentario
    FROM entidad_datos_complementarios
    JOIN lista_tipo_de_dato_complementario ON entidad_datos_complementarios.DC_Tipo = lista_tipo_de_dato_complementario.ID
    WHERE FK_Lugar = ?
    ORDER BY NumeroDeOrden ASC";

    $stmt_datos_complementarios = $conn->prepare($query_datos_complementarios);
    $stmt_datos_complementarios->bind_param('s', $LugarHecho['ID_Lugar']);
    $stmt_datos_complementarios->execute();
    $result_datos_complementarios = $stmt_datos_complementarios->get_result();

    $datos_complementarios = [];
    while ($row = $result_datos_complementarios->fetch_assoc()) {
        if (!empty($row['DC_ImagenAdjunta'])) {
            $imgData = str_replace('data:image/jpeg;base64,', '', $row['DC_ImagenAdjunta']);
            $imgData = str_replace(' ', '+', $imgData); // Asegurarse de que los espacios no afectan el decode
            $imgData = base64_decode($imgData);

            $img = imagecreatefromstring($imgData);
            if ($img !== false) {
                $width = imagesx($img);
                $height = imagesy($img);
                $row['anchoImagen'] = $width;
                $row['altoImagen'] = $height;
            } else {
                error_log("Error procesando imagen de datos complementarios: " . substr($imgData, 0, 100));
                $row['anchoImagen'] = null;
                $row['altoImagen'] = null;
            }

            if ($img) {
                imagedestroy($img);
            }
        } else {
            $row['anchoImagen'] = null;
            $row['altoImagen'] = null;
        }

        $datos_complementarios[] = $row;
    }

    $stmt_datos_complementarios->close();

    // Agregar los datos complementarios al lugar del hecho correspondiente.
    $data_lugaresHechos[$key]['datos_complementarios'] = $datos_complementarios;
}

// Consulta para obtener las personas relacionadas
$query_personas = "SELECT
    ID_Persona,
    lista_rol_de_la_persona.Rol AS P_Rol,
    P_FotoPersona,
    P_Apellido,
    P_Nombre,
    P_Alias,
    lista_genero.Genero AS P_Genero,
    P_DNI,
    lista_estado_civil.EstadoCivil AS P_EstadoCivil
FROM entidad_personas 
JOIN lista_rol_de_la_persona ON entidad_personas.P_Rol = lista_rol_de_la_persona.ID
JOIN lista_genero ON entidad_personas.P_Genero = lista_genero.ID
JOIN lista_estado_civil ON entidad_personas.P_EstadoCivil = lista_estado_civil.ID
WHERE FK_Encabezado = ?
ORDER BY NumeroDeOrden ASC";

$stmt_personas = $conn->prepare($query_personas);
if (!$stmt_personas) {
    die("Error al preparar la consulta personas: " . $conn->error);
}
$stmt_personas->bind_param("s", $DispositivoSIACIP);
$stmt_personas->execute();
if ($stmt_personas->error) {
    die("Error en la consulta personas: " . $stmt_personas->error);
}
$result_personas = $stmt_personas->get_result();

$data_personas = $result_personas->fetch_all(MYSQLI_ASSOC);
$stmt_personas->close();

// Preparar los domicilios para cada Persona.
foreach ($data_personas as $key => $Persona) {
    // Consulta para obtener los domicilios para cada Persona.
    $query_domicilios = "SELECT
    ID_Lugar,
    lista_rol_del_lugar.Rol AS L_Rol,
    L_Calle,
    L_AlturaCatastral,
    L_CalleDetalle,
    L_Localidad,
    L_Provincia,
    L_Pais
FROM entidad_lugares
JOIN lista_rol_del_lugar ON entidad_lugares.L_Rol = lista_rol_del_lugar.ID
WHERE FK_Persona = ?
ORDER BY NumeroDeOrden ASC";

    $stmt_domicilios = $conn->prepare($query_domicilios);
    $stmt_domicilios->bind_param('s', $Persona['ID_Persona']);
    $stmt_domicilios->execute();
    $result_domicilios = $stmt_domicilios->get_result();

    $datos_domicilios = $result_domicilios->fetch_all(MYSQLI_ASSOC);    

    $result_domicilios->close();

    // Agregar los domicilios al arma correspondiente.
    $data_personas[$key]['domicilios'] = $datos_domicilios;
}

// Preparar los datos complementarios para cada Persona.
foreach ($data_personas as $key => $Persona) {
    // Consulta para obtener los datos complementarios para cada Persona.
    $query_datos_complementarios = "SELECT
        lista_tipo_de_dato_complementario.TipoDeDato AS DC_Tipo,
        DC_ImagenAdjunta,
        DC_Comentario
    FROM entidad_datos_complementarios
    JOIN lista_tipo_de_dato_complementario ON entidad_datos_complementarios.DC_Tipo = lista_tipo_de_dato_complementario.ID
    WHERE FK_Persona = ?
    ORDER BY NumeroDeOrden ASC";

    $stmt_datos_complementarios = $conn->prepare($query_datos_complementarios);
    $stmt_datos_complementarios->bind_param('s', $Persona['ID_Persona']);
    $stmt_datos_complementarios->execute();
    $result_datos_complementarios = $stmt_datos_complementarios->get_result();

    $datos_complementarios = [];
    while ($row = $result_datos_complementarios->fetch_assoc()) {
        if (!empty($row['DC_ImagenAdjunta'])) {
            $imgData = str_replace('data:image/jpeg;base64,', '', $row['DC_ImagenAdjunta']);
            $imgData = str_replace(' ', '+', $imgData); // Asegurarse de que los espacios no afectan el decode
            $imgData = base64_decode($imgData);

            $img = imagecreatefromstring($imgData);
            if ($img !== false) {
                $width = imagesx($img);
                $height = imagesy($img);
                $row['anchoImagen'] = $width;
                $row['altoImagen'] = $height;
            } else {
                error_log("Error procesando imagen de datos complementarios: " . substr($imgData, 0, 100));
                $row['anchoImagen'] = null;
                $row['altoImagen'] = null;
            }

            if ($img) {
                imagedestroy($img);
            }
        } else {
            $row['anchoImagen'] = null;
            $row['altoImagen'] = null;
        }

        $datos_complementarios[] = $row;
    }

    $stmt_datos_complementarios->close();

    // Agregar los datos complementarios al arma correspondiente.
    $data_personas[$key]['datos_complementarios'] = $datos_complementarios;
}

// Consulta para obtener los vehiculos relacionados
$query_vehiculos = "SELECT
    ID_Vehiculo,
    lista_rol_del_vehiculo.Rol AS V_Rol,
    lista_tipo_de_vehiculo.TipoDeVehiculo AS V_TipoVehiculo,
    V_Color,
    V_Marca,
    V_Modelo,
    V_Año,
    V_Dominio,
    V_NumeroChasis,
    V_NumeroMotor
FROM entidad_vehiculos 
JOIN lista_rol_del_vehiculo ON entidad_vehiculos.V_Rol = lista_rol_del_vehiculo.ID
JOIN lista_tipo_de_vehiculo ON entidad_vehiculos.V_TipoVehiculo = lista_tipo_de_vehiculo.ID
WHERE FK_Encabezado = ?
ORDER BY NumeroDeOrden ASC";

$stmt_vehiculos = $conn->prepare($query_vehiculos);
if (!$stmt_vehiculos) {
    die("Error al preparar la consulta vehiculos: " . $conn->error);
}$stmt_vehiculos->bind_param("s", $DispositivoSIACIP);
$stmt_vehiculos->execute();
if ($stmt_vehiculos->error) {
    die("Error en la consulta vehiculos: " . $stmt_vehiculos->error);
}
$result_vehiculos = $stmt_vehiculos->get_result();

$data_vehiculos = $result_vehiculos->fetch_all(MYSQLI_ASSOC);
$stmt_vehiculos->close();

// Preparar los datos complementarios para cada Vehiculo.
foreach ($data_vehiculos as $key => $Vehiculo) {
    // Consulta para obtener los datos complementarios para cada Vehiculo.
    $query_datos_complementarios = "SELECT
        lista_tipo_de_dato_complementario.TipoDeDato AS DC_Tipo,
        DC_ImagenAdjunta,
        DC_Comentario
    FROM entidad_datos_complementarios
    JOIN lista_tipo_de_dato_complementario ON entidad_datos_complementarios.DC_Tipo = lista_tipo_de_dato_complementario.ID
    WHERE FK_Vehiculo = ?
    ORDER BY NumeroDeOrden ASC";

    $stmt_datos_complementarios = $conn->prepare($query_datos_complementarios);
    $stmt_datos_complementarios->bind_param('s', $Vehiculo['ID_Vehiculo']);
    $stmt_datos_complementarios->execute();
    $result_datos_complementarios = $stmt_datos_complementarios->get_result();

    $datos_complementarios = [];
    while ($row = $result_datos_complementarios->fetch_assoc()) {
        if (!empty($row['DC_ImagenAdjunta'])) {
            $imgData = str_replace('data:image/jpeg;base64,', '', $row['DC_ImagenAdjunta']);
            $imgData = str_replace(' ', '+', $imgData); // Asegurarse de que los espacios no afectan el decode
            $imgData = base64_decode($imgData);

            $img = imagecreatefromstring($imgData);
            if ($img !== false) {
                $width = imagesx($img);
                $height = imagesy($img);
                $row['anchoImagen'] = $width;
                $row['altoImagen'] = $height;
            } else {
                error_log("Error procesando imagen de datos complementarios: " . substr($imgData, 0, 100));
                $row['anchoImagen'] = null;
                $row['altoImagen'] = null;
            }

            if ($img) {
                imagedestroy($img);
            }
        } else {
            $row['anchoImagen'] = null;
            $row['altoImagen'] = null;
        }

        $datos_complementarios[] = $row;
    }

    $stmt_datos_complementarios->close();

    // Agregar los datos complementarios al arma correspondiente.
    $data_vehiculos[$key]['datos_complementarios'] = $datos_complementarios;
}

// Consulta para obtener las armas relacionados
$query_armas = "SELECT
    ID_Arma,
    AF_EsDeFabricacionCasera,
    lista_tipo_de_armas.Clasificacion AS AF_TipoAF,
    AF_Marca,
    AF_Modelo,
    AF_Calibre,
    AF_PoseeNumeracionVisible,
    AF_NumeroDeSerie
FROM entidad_armas 
JOIN lista_tipo_de_armas ON entidad_armas.AF_TipoAF = lista_tipo_de_armas.ID
WHERE FK_Encabezado = ?
ORDER BY NumeroDeOrden ASC";

$stmt_armas = $conn->prepare($query_armas);
if (!$stmt_armas) {
    die("Error al preparar la consulta armas: " . $conn->error);
}
$stmt_armas->bind_param("s", $DispositivoSIACIP);
$stmt_armas->execute();
if ($stmt_armas->error) {
    die("Error en la consulta armas: " . $stmt_armas->error);
}
$result_armas = $stmt_armas->get_result();

// Obtener los datos principales de las armas.
$data_armas = $result_armas->fetch_all(MYSQLI_ASSOC);
$stmt_armas->close();  // Cierra el statement después de usarlo.

// Preparar los datos complementarios para cada arma.
foreach ($data_armas as $key => $arma) {
    // Consulta para obtener los datos complementarios para cada arma.
    $query_datos_complementarios = "SELECT
        lista_tipo_de_dato_complementario.TipoDeDato AS DC_Tipo,
        DC_ImagenAdjunta,
        DC_Comentario
    FROM entidad_datos_complementarios
    JOIN lista_tipo_de_dato_complementario ON entidad_datos_complementarios.DC_Tipo = lista_tipo_de_dato_complementario.ID
    WHERE FK_Arma = ?
    ORDER BY NumeroDeOrden ASC";

    $stmt_datos_complementarios = $conn->prepare($query_datos_complementarios);
    $stmt_datos_complementarios->bind_param('s', $arma['ID_Arma']);
    $stmt_datos_complementarios->execute();
    $result_datos_complementarios = $stmt_datos_complementarios->get_result();

    $datos_complementarios = [];
    while ($row = $result_datos_complementarios->fetch_assoc()) {
        if (!empty($row['DC_ImagenAdjunta'])) {
            $imgData = str_replace('data:image/jpeg;base64,', '', $row['DC_ImagenAdjunta']);
            $imgData = str_replace(' ', '+', $imgData); // Asegurarse de que los espacios no afectan el decode
            $imgData = base64_decode($imgData);

            $img = imagecreatefromstring($imgData);
            if ($img !== false) {
                $width = imagesx($img);
                $height = imagesy($img);
                $row['anchoImagen'] = $width;
                $row['altoImagen'] = $height;
            } else {
                error_log("Error procesando imagen de datos complementarios: " . substr($imgData, 0, 100));
                $row['anchoImagen'] = null;
                $row['altoImagen'] = null;
            }

            if ($img) {
                imagedestroy($img);
            }
        } else {
            $row['anchoImagen'] = null;
            $row['altoImagen'] = null;
        }

        $datos_complementarios[] = $row;
    }

    $stmt_datos_complementarios->close();

    // Agregar los datos complementarios al arma correspondiente.
    $data_armas[$key]['datos_complementarios'] = $datos_complementarios;
}

// Consulta para obtener los mensajes extorsivos
$query_mensajes = "SELECT
    lista_medio_del_mensaje.MedioDelMensaje AS ME_Medio,
    ME_OtroMedio,
    ME_Contenido,
    ME_Firma,
    ME_InfoContacto,
    ME_Imagen
FROM entidad_mensajes_extorsivos 
JOIN lista_medio_del_mensaje ON entidad_mensajes_extorsivos.ME_Medio = lista_medio_del_mensaje.ID
WHERE FK_Encabezado = ?
ORDER BY NumeroDeOrden ASC";

$stmt_mensajes = $conn->prepare($query_mensajes);
if (!$stmt_mensajes) {
    die("Error al preparar la consulta mensajes: " . $conn->error);
}
$stmt_mensajes->bind_param("s", $DispositivoSIACIP);
$stmt_mensajes->execute();
if ($stmt_mensajes->error) {
    die("Error en la consulta mensajes: " . $stmt_mensajes->error);
}
$result_mensajes = $stmt_mensajes->get_result();

$data_mensajes = [];
while ($row = $result_mensajes->fetch_assoc()) {
    if (!empty($row['ME_Imagen'])) {
        $imgData = str_replace('data:image/jpeg;base64,', '', $row['ME_Imagen']);
        $imgData = base64_decode($imgData);

        $img = imagecreatefromstring($imgData);
        if ($img !== false) {
            $width = imagesx($img);
            $height = imagesy($img);
            $row['anchoImagen'] = $width;
            $row['altoImagen'] = $height;
        } else {
            error_log("Error procesando imagen: " . substr($imgData, 0, 100));
            $row['anchoImagen'] = null;
            $row['altoImagen'] = null;
        }

        if ($img) {
            imagedestroy($img);
        }
    } else {
        $row['anchoImagen'] = null;
        $row['altoImagen'] = null;
    }

    $data_mensajes[] = $row;
}


$stmt_mensajes->close();


// Consulta para obtener las fuentes consultadas
$query_fuentes = "SELECT
    sistema_fuentes_de_datos.FuenteConsultada AS FC_TipoDeFuenteConsultada,
    FC_Resultado,
    FC_FechaActualizacion
FROM entidad_fuentes_consultadas
JOIN sistema_fuentes_de_datos ON entidad_fuentes_consultadas.FC_TipoDeFuenteConsultada = sistema_fuentes_de_datos.ID
WHERE FK_Encabezado  = ?";

$stmt_fuentes = $conn->prepare($query_fuentes);
if (!$stmt_fuentes) {
    die("Error al preparar la consulta fuentes consultadas: " . $conn->error);
}
$stmt_fuentes->bind_param("s", $DispositivoSIACIP);
$stmt_fuentes->execute();
if ($stmt_fuentes->error) {
    die("Error en la consulta fuentes consultadas: " . $stmt_fuentes->error);
}
$result_fuentes = $stmt_fuentes->get_result();

// Obtener los datos principales de las fuentes consultadas
$data_fuentes = $result_fuentes->fetch_all(MYSQLI_ASSOC);
$stmt_fuentes->close();  // Cierra el statement después de usarlo.

// Combina los resultados
$response = [
    'encabezado' => $data_encabezado,
    'lugaresHechos' => $data_lugaresHechos,
    'personas' => $data_personas,
    'vehiculos' => $data_vehiculos,
    'armas' => $data_armas, 
    'mensajes' => $data_mensajes,
    'fuentesConsultadas' => $data_fuentes,
];

// Agregar el encabezado de tipo de contenido para JSON
header('Content-Type: application/json');

// Mostrar los datos antes de la codificación JSON (para depuración)
$json_response = json_encode($response);
if ($json_response === false) {
    die("Error en la codificación JSON: " . json_last_error_msg());
} else {
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
}

$conn->close();

?>
