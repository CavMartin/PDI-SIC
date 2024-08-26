<?php
// Clase encargada de obtener los datos de las entidades a mostrar en la pagina principal
class DataFetcher {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    private function fetchData($query, $param) {
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $this->conn->error);
        }
        $stmt->bind_param("s", $param);
        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $stmt->close();
        return $data;
    }

    // Método para obtener el año actual y el número máximo de incidencia dentro de dicho año
    public function fetchDataNewForm($userGroup) {
        $Año = date("Y"); // Obtener el año actual
        $Numero = 1;  // Inicializar $Numero con un valor por defecto
        $sqlMaxNum = "SELECT MAX(Numero) AS max_num FROM entidad_encabezado WHERE Año = ? AND Division = ?";
        $stmtMaxNum = $this->conn->prepare($sqlMaxNum);
        if (!$stmtMaxNum) {
            throw new Exception("Error al preparar la consulta: " . $this->conn->error);
        }
        // Incluir userGroup como segundo parámetro en bind_param
        $stmtMaxNum->bind_param("is", $Año, $userGroup);
        if (!$stmtMaxNum->execute()) {
            throw new Exception("Error ejecutando la consulta: " . $stmtMaxNum->error);
        }
        $result = $stmtMaxNum->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Comprobar si el resultado es nulo y asignar $Numero
            $Numero = isset($row['max_num']) && $row['max_num'] !== null ? $row['max_num'] + 1 : 1;
        }
        $stmtMaxNum->close();

        $Formulario = $Numero . "-" . $Año;

        return [
            'Formulario' => $Formulario,
            'Numero' => $Numero,
            'Año' => $Año,
            'Grupo' => $userGroup
        ];
    }

    public function fetchDataMain($ID) {
        $DatosMain = [];

        // Consulta para entidad Personas
        $DatosMain['Personas'] = $this->fetchData("SELECT ID_Persona, NumeroDeOrden, P_Apellido, P_Nombre, P_Genero, P_DNI FROM entidad_personas WHERE FK_Encabezado = ? ORDER BY NumeroDeOrden", $ID);

        // Consulta para entidad Lugares
        $DatosMain['Lugares'] = $this->fetchData("SELECT ID_Lugar, NumeroDeOrden, L_TipoLugar, L_Calle, L_AlturaCatastral, L_Interseccion1, L_Interseccion2 FROM entidad_lugares WHERE FK_Encabezado = ? ORDER BY NumeroDeOrden", $ID);

        // Consulta para entidad Arma
        $DatosMain['Armas'] = $this->fetchData("SELECT ID_Arma, NumeroDeOrden, AF_TipoAF, AF_Marca, AF_Modelo, AF_Calibre FROM entidad_armas WHERE FK_Encabezado = ? ORDER BY NumeroDeOrden", $ID);

        // Consulta para entidad Vehiculo
        $DatosMain['Vehiculos'] = $this->fetchData("SELECT ID_Vehiculo, NumeroDeOrden, V_TipoVehiculo, V_Marca, V_Modelo, V_Dominio FROM entidad_vehiculos WHERE FK_Encabezado = ? ORDER BY NumeroDeOrden", $ID);

        // Consulta para entidad Secuestro
        $DatosMain['Secuestros'] = $this->fetchData("SELECT ID_DatoComplementario, NumeroDeOrden, DC_Tipo FROM entidad_datos_complementarios WHERE FK_Encabezado = ? ORDER BY NumeroDeOrden", $ID);

        return $DatosMain;
    }
   
    public function fetchIncidenciasAbiertas() {
        $incidencias = []; // Inicializa el arreglo de incidencias
    
        // Consulta SQL para obtener las incidencias con Estado = 1
        $stmt = $this->conn->prepare("SELECT DispositivoSIACIP FROM sistema_dispositivo_siacip WHERE Estado = 1");
        $stmt->execute();
        $result = $stmt->get_result();
    
        while ($fila = $result->fetch_assoc()) {
            $dispositivoSIACIP = $fila['DispositivoSIACIP'];
            // Para cada DispositivoSIACIP, busca sus incidencias y ampliaciones
            $incidencias[$dispositivoSIACIP] = $this->obtenerDetallesIncidencia($dispositivoSIACIP);
        }
    
        $stmt->close();
        return $incidencias; // Devuelve un arreglo de incidencias con sus detalles
    }
    
    private function obtenerDetallesIncidencia($dispositivoSIACIP) {
        $detallesIncidencia = []; // Inicializa el arreglo de detalles de la incidencia
    
        $likeDispositivoSIACIP = $dispositivoSIACIP . "%"; // Prepara el valor para la cláusula LIKE
        $stmt = $this->conn->prepare("SELECT IP_Numero, IP_FechaDeCreacion FROM entidad_incidencia_priorizada WHERE IP_Numero LIKE ? ORDER BY IP_FechaDeCreacion ASC");
        $stmt->bind_param("s", $likeDispositivoSIACIP);
        $stmt->execute();
        $result = $stmt->get_result();
    
        while ($fila = $result->fetch_assoc()) {
            $detallesIncidencia[] = $fila; // Agrega los detalles de la incidencia al arreglo
        }
    
        $stmt->close();
        return $detallesIncidencia; // Devuelve el arreglo de detalles para la incidencia actual
    }
    
    public function fetchDataEncabezado($ClavePrimaria) {
        $EncabezadoData = null;
        // Consulta SQL para obtener los datos de una persona específica por su ID
        $stmt = $this->conn->prepare("SELECT * FROM entidad_encabezado WHERE ID = ?");
        $stmt->bind_param("s", $ClavePrimaria);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $EncabezadoData = $result->fetch_assoc();
        }

        return $EncabezadoData;
    }

    public function fetchDataPersona($ClavePrimaria) {
        $datosPersona = null;
        // Consulta SQL para obtener los datos de una persona específica por su ID
        $stmt = $this->conn->prepare("SELECT * FROM entidad_personas WHERE ID_Persona = ?");
        $stmt->bind_param("s", $ClavePrimaria);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $datosPersona = $result->fetch_assoc();
        }

        return $datosPersona;
    }

    public function getDomiciliosJSON($ID_Persona) { // Para obtener los domicilios ya cargados por AJAX
        $domicilios = $this->fetchDataDomicilios($ID_Persona);
        header('Content-Type: application/json');
        echo json_encode($domicilios); // Siempre será un arreglo, que puede estar vacío
    }    

    private function fetchDataDomicilios($ID_Persona) {
        $domicilios = []; // Inicializa $domicilios como un arreglo vacío
        // Consulta SQL para obtener los domicilios relacionados a una persona
        $stmt = $this->conn->prepare("SELECT * FROM entidad_lugares WHERE FK_Persona = ? ORDER BY NumeroDeOrden ASC");
        $stmt->bind_param("s", $ID_Persona);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($domicilio = $result->fetch_assoc()) {
            $domicilios[] = $domicilio;
        }
    
        $stmt->close();
        return $domicilios; // Devuelve el arreglo, que estará vacío si no hay domicilios
    }
    
    public function fetchDataLugar($ClavePrimaria) {
        $datosLugar = null;
        // Consulta SQL para obtener los datos de un lugar del hecho específico por su ID
        $stmt = $this->conn->prepare("SELECT * FROM entidad_lugares WHERE ID_Lugar = ?");
        $stmt->bind_param("s", $ClavePrimaria);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $datosLugar = $result->fetch_assoc();
        }

        $stmt->close();
        return $datosLugar;
    }

    public function fetchDataVehiculo($ClavePrimaria) {
        $datosVehiculo = null;
        // Consulta SQL para obtener los datos de un Vehiculo específico por su ID
        $stmt = $this->conn->prepare("SELECT * FROM entidad_vehiculos WHERE ID_Vehiculo = ?");
        $stmt->bind_param("s", $ClavePrimaria);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $datosVehiculo = $result->fetch_assoc();
        }
    
        $stmt->close();
        return $datosVehiculo;
    }

    public function fetchDataAF($ClavePrimaria) {
        $datosAF = null;
        // Consulta SQL para obtener los datos de un arma de fuego específica por su ID
        $stmt = $this->conn->prepare("SELECT * FROM entidad_armas WHERE ID_Arma = ?");
        $stmt->bind_param("s", $ClavePrimaria);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $datosAF = $result->fetch_assoc();
        }
    
        $stmt->close();
        return $datosAF;
    }

    public function fetchDataSecuestros($ClavePrimaria) {
        $SecuestrosData = null;
        // Consulta SQL para obtener los datos de un mensaje extorsivo específico por su ID
        $stmt = $this->conn->prepare("SELECT * FROM entidad_datos_complementarios WHERE ID_DatoComplementario = ?");
        $stmt->bind_param("s", $ClavePrimaria);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $SecuestrosData = $result->fetch_assoc();
        }
    
        $stmt->close();
        return $SecuestrosData;
    }

    public function getDatosComplementariosJSON($ClavePrimaria) { // Para obtener los datos complementarios ya cargados por AJAX
        $datosComplementarios = $this->fetchDataDatosComplementarios($ClavePrimaria);
        header('Content-Type: application/json');
        echo json_encode($datosComplementarios);
    }

    private function fetchDataDatosComplementarios($ClavePrimaria) {
        $stmt = $this->conn->prepare("SELECT * FROM entidad_datos_complementarios WHERE (FK_Persona = ? OR FK_Lugar = ? OR FK_Vehiculo = ? OR FK_Arma = ?) ORDER BY NumeroDeOrden ASC");
        $stmt->bind_param("ssss", $ClavePrimaria, $ClavePrimaria, $ClavePrimaria, $ClavePrimaria);
        $stmt->execute();
        $result = $stmt->get_result();
        $datosComplementarios = [];
    
        if ($result->num_rows > 0) {
            while ($complementario = $result->fetch_assoc()) {
                $datosComplementarios[] = $complementario;
            }
        }
    
        $stmt->close();
        return $datosComplementarios;
    }

    private function construirDirecciones($lugarHecho) {
        $texto = $lugarHecho['L_Calle'];

        // Construcción del texto de la dirección
        if (!empty($lugarHecho['L_AlturaCatastral'])) {
            $texto .= ' N° ' . $lugarHecho['L_AlturaCatastral'];
        }
        if (!empty($lugarHecho['L_CalleDetalle'])) {
            $texto .= ', ' . $lugarHecho['L_CalleDetalle'];
        }
        if (!empty($lugarHecho['L_Interseccion1'])) {
            $texto .= !empty($lugarHecho['L_Interseccion2']) ? ', entre ' . $lugarHecho['L_Interseccion1'] . ' y ' . $lugarHecho['L_Interseccion2'] : ' y ' . $lugarHecho['L_Interseccion1'];
        }
        $texto .= ', ' . $lugarHecho['L_Localidad'];

        return $texto;
    }

    public function fetchDataIncidenciaPriorizada($ID) {
        $Data_IncidenciaPriorizada = [];

        // Consulta para entidad encabezado
        $Encabezado = $this->fetchData("SELECT ID, Formulario, Division, Fecha, Hora, Clasificacion, Dependencia, Juzgado, Fiscal, Causa, Relato FROM entidad_encabezado WHERE ID = ?", $ID);
            if(count($Encabezado) > 0) {
                $encabezadoDatos = $Encabezado[0];
                $Data_IncidenciaPriorizada['Encabezado'] = $encabezadoDatos;
            } else {
                $Data_IncidenciaPriorizada['Encabezado'] = null;
            }

            // Procesamiento de Lugares del Hecho
            $lugaresHechos = $this->fetchData("SELECT ID_Lugar, L_Rol, L_TipoLugar, L_NombreLugarEspecifico, L_Calle, L_AlturaCatastral, L_CalleDetalle, L_Interseccion1, L_Interseccion2, L_Barrio, L_Localidad FROM entidad_lugares WHERE FK_Encabezado = ? ORDER BY NumeroDeOrden ASC", $ID);
            $Data_IncidenciaPriorizada['Lugares'] = [];

            foreach ($lugaresHechos as $lugarHecho) {
                $direccion = $this->construirDirecciones($lugarHecho);
            
                // Consulta para Datos Complementarios del Lugar
                $datosComplementarios = $this->fetchData("SELECT DC_Tipo, DC_ImagenAdjunta, DC_Comentario FROM entidad_datos_complementarios WHERE FK_Lugar = ? ORDER BY NumeroDeOrden ASC", $lugarHecho['ID_Lugar']);
            
                // Agregar los datos complementarios al arreglo de lugar si existen
                if (!empty($direccion)) {
                    $Data_IncidenciaPriorizada['Lugares'][] = [
                        "Rol" => $lugarHecho['L_Rol'],
                        "Tipo" => $lugarHecho['L_TipoLugar'],
                        "Nombre" => $lugarHecho['L_NombreLugarEspecifico'],
                        "Calle" => $lugarHecho['L_Calle'],
                        "AlturaCatastral" => $lugarHecho['L_AlturaCatastral'],
                        "CalleDetalle" => $lugarHecho['L_CalleDetalle'],
                        "Interseccion1" => $lugarHecho['L_Interseccion1'],
                        "Interseccion2" => $lugarHecho['L_Interseccion2'],
                        "Barrio" => $lugarHecho['L_Barrio'],
                        "Localidad" => $lugarHecho['L_Localidad'],
                        "Direccion" => $direccion,
                        "DatosComplementarios" => $datosComplementarios
                    ];
                }
            }

        // Consulta para entidad Personas y sus entidades complementarias
        $personas = $this->fetchData("SELECT ID_Persona, P_FotoPersona, P_Rol, P_Apellido, P_Nombre, P_Alias, P_Genero, P_DNI, P_Edad, P_EstadoCivil, P_Pais FROM entidad_personas WHERE FK_Encabezado = ? ORDER BY NumeroDeOrden", $ID);
            foreach ($personas as $key => $persona) {
                $domicilios = $this->fetchData("SELECT L_Rol, L_Calle, L_AlturaCatastral, L_CalleDetalle, L_Localidad, L_Provincia, L_Pais FROM entidad_lugares WHERE FK_Persona = ? ORDER BY NumeroDeOrden ASC", $persona['ID_Persona']);
                // Procesar cada domicilio a través de construirDirecciones
                $domiciliosProcesados = array_map(function($domicilio) {
                    return [
                        "Rol" => $domicilio['L_Rol'],
                        "Direccion" => $this->construirDirecciones($domicilio)
                    ];
                }, $domicilios);
                $personas[$key]['Domicilios'] = $domiciliosProcesados;

                $datosComplementarios = $this->fetchData("SELECT DC_Tipo, DC_ImagenAdjunta, DC_Comentario FROM entidad_datos_complementarios WHERE FK_Persona = ? ORDER BY NumeroDeOrden ASC", $persona['ID_Persona']);
                $personas[$key]['DatosComplementarios'] = $datosComplementarios;
            }
        $Data_IncidenciaPriorizada['Personas'] = $personas;

        // Consulta para entidad Vehiculo y agregado de datos complementarios
        $vehiculos = $this->fetchData("SELECT ID_Vehiculo, NumeroDeOrden, V_Rol, V_TipoVehiculo, V_Color, V_Marca, V_Modelo, V_Año, V_Dominio, V_NumeroChasis, V_NumeroMotor FROM entidad_vehiculos WHERE FK_Encabezado = ? ORDER BY NumeroDeOrden", $ID);
            foreach ($vehiculos as $key => $vehiculo) {
                $datosComplementariosVehiculo = $this->fetchData("SELECT DC_Tipo, DC_ImagenAdjunta, DC_Comentario FROM entidad_datos_complementarios WHERE FK_Vehiculo = ? ORDER BY NumeroDeOrden ASC", $vehiculo['ID_Vehiculo']);
                $vehiculos[$key]['DatosComplementarios'] = $datosComplementariosVehiculo;
            }
        $Data_IncidenciaPriorizada['Vehiculos'] = $vehiculos;

        // Consulta para entidad Arma y agregado de datos complementarios
        $armas = $this->fetchData("SELECT ID_Arma, NumeroDeOrden, AF_TipoAF, AF_EsDeFabricacionCasera, AF_Marca, AF_Modelo, AF_Calibre, AF_NumeroDeSerie FROM entidad_armas WHERE FK_Encabezado = ? ORDER BY NumeroDeOrden", $ID);
            foreach ($armas as $key => $arma) {
                $datosComplementariosArma = $this->fetchData("SELECT DC_Tipo, DC_ImagenAdjunta, DC_Comentario FROM entidad_datos_complementarios WHERE FK_Arma = ? ORDER BY NumeroDeOrden ASC", $arma['ID_Arma']);
                $armas[$key]['DatosComplementarios'] = $datosComplementariosArma;
            }
        $Data_IncidenciaPriorizada['Armas'] = $armas;

        // Consulta para mensajes extorsivos
        $Data_IncidenciaPriorizada['Secuestros'] = $this->fetchData("SELECT ID_DatoComplementario, NumeroDeOrden, DC_Tipo, DC_ImagenAdjunta, DC_Comentario FROM entidad_datos_complementarios WHERE FK_Encabezado = ? ORDER BY NumeroDeOrden", $ID);

        // Comprobación simplificada basada solo en la existencia del Encabezado
        if ($Data_IncidenciaPriorizada['Encabezado'] === null) {
            // El Encabezado no existe, por lo tanto, no hay datos relevantes
            return null; // Devolver null o cualquier otra indicación de "sin datos"
        }

        // Si el Encabezado existe, devuelve todos los datos recopilados
        return $Data_IncidenciaPriorizada;

    }

    // Método utilizado para enviar los datos a las tablas del sistema de consultas
    private function fetchDataQuery($query, $params = []) {
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $this->conn->error);
        }
    
        // Verifica si hay parámetros para enmascarar
        if (!empty($params)) {
            // Construye una cadena con tipos de datos
            $types = str_repeat("s", count($params));
            $stmt->bind_param($types, ...$params);
        }
    
        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }
    
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $stmt->close();
        return $data;
    }

    public function fetchDataQueryBandeja($params) {
        $DatosMain = [];
    
        $queryParams = [];
        $sql = "SELECT ID, Formulario, Division, Estado, Fecha, Hora, Clasificacion, Dependencia, Juzgado, Fiscal, Causa, Relato FROM entidad_encabezado";
        $conditions = [];
    
        // Asumiendo que 'FechaDesde' y 'FechaHasta' son manejados de forma especial y no incluidos en $params directamente.
        $fechaDesde = $params['FechaDesde'] ?? '';
        $fechaHasta = $params['FechaHasta'] ?? '';
    
        // Remover 'FechaDesde' y 'FechaHasta' de $params para no interferir con el bucle foreach siguiente
        unset($params['FechaDesde'], $params['FechaHasta']);
    
        // Manejar FechaDesde y FechaHasta
        if (!empty($fechaDesde) && !empty($fechaHasta)) {
            if ($fechaDesde == $fechaHasta) {
                // Caso especial: las fechas son iguales, buscar registros para ese día específico
                $conditions[] = "Fecha BETWEEN ? AND ?";
                $queryParams[] = $fechaDesde . " 00:00:00";
                $queryParams[] = $fechaHasta . " 23:59:59";
            } else {
                // Caso 1: Ambas fechas están presentes y son diferentes
                $conditions[] = "Fecha BETWEEN ? AND ?";
                $queryParams[] = $fechaDesde . " 00:00:00";
                $queryParams[] = $fechaHasta . " 23:59:59";
            }
        } elseif (!empty($fechaDesde)) {
            // Caso 2: Solo FechaDesde está presente, aplicar filtro desde la fecha indicada
            $conditions[] = "Fecha >= ?";
            $queryParams[] = $fechaDesde . " 00:00:00";
        } elseif (!empty($fechaHasta)) {
            // Caso 3: Solo FechaHasta está presente, aplicar filtro hasta la fecha indicada
            $conditions[] = "Fecha <= ?";
            $queryParams[] = $fechaHasta . " 23:59:59";
        }
    
        foreach ($params as $key => $value) {
            if (!empty($value)) {
                $conditions[] = "$key LIKE ?";
                $queryParams[] = "%" . $value . "%";
            }
        }
    
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
    
        $sql .= " ORDER BY Fecha DESC";
    
        $DatosMain['ID'] = $this->fetchDataQuery($sql, $queryParams);
    
        return $DatosMain;
    }

    public function fetchDataQueryIP($params) {
        $DatosMain = [];
    
        $queryParams = [];
        $sql = "SELECT IP_Numero, IP_RegionCAE911, IP_Fecha, IP_Hora, IP_TipoHecho, IP_GrupoHecho, IP_ResultadoDeLaIncidencia FROM entidad_incidencia_priorizada";
        $conditions = [];
    
        $fechaDesde = $params['IP_FechaDesde'] ?? '';
        $fechaHasta = $params['IP_FechaHasta'] ?? '';
    
        // Remover 'FechaDesde' y 'FechaHasta' de $params para no interferir con el bucle foreach siguiente
        unset($params['IP_FechaDesde'], $params['IP_FechaHasta']);
    
        // Manejar FechaDesde y FechaHasta
        if (!empty($fechaDesde) && !empty($fechaHasta)) {
            // Caso 1: Ambas fechas están presentes y son diferentes
            $conditions[] = "IP_Fecha BETWEEN ? AND ?";
            $queryParams[] = $fechaDesde;
            $queryParams[] = $fechaHasta;
        } elseif (!empty($fechaDesde)) {
            // Caso 2: Solo FechaDesde está presente, aplicar filtro desde la fecha indicada
            $conditions[] = "IP_Fecha >= ?";
            $queryParams[] = $fechaDesde;
        } elseif (!empty($fechaHasta)) {
            // Caso 3: Solo FechaHasta está presente, aplicar filtro hasta la fecha indicada
            $conditions[] = "IP_Fecha <= ?";
            $queryParams[] = $fechaHasta;
        }
    
        foreach ($params as $key => $value) {
            if (!empty($value)) {
                $conditions[] = "$key LIKE ?";
                $queryParams[] = "%" . $value . "%";
            }
        }
    
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
    
        $sql .= " ORDER BY IP_Fecha DESC";
    
        $DatosMain['IncidenciasPriorizadas'] = $this->fetchDataQuery($sql, $queryParams);
    
        return $DatosMain;
    }

    public function fetchDataQueryPersonas($params) {
        $DatosMain = [];

        $queryParams = [];
        $sql = "SELECT ID_Persona, FK_Encabezado, P_Rol, P_Apellido, P_Nombre, P_Alias, P_Genero, P_DNI, P_EstadoCivil FROM entidad_personas";
        $conditions = [];

        $hasValidParams = false;
        foreach ($params as $key => $value) {
            if (!empty($value)) {
                $conditions[] = "$key LIKE ?";
                $queryParams[] = "%" . $value . "%";
                $hasValidParams = true;
            }
        }

        if ($hasValidParams) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY P_Apellido";

        $DatosMain['Personas'] = $this->fetchDataQuery($sql, $queryParams);

        return $DatosMain;
    }

    public function fetchDataQueryLugares($params) {
        $DatosMain = [];
    
        $queryParams = [];
        $sql = "SELECT ID_Lugar, FK_Encabezado, L_Rol, L_TipoLugar, L_Calle, L_AlturaCatastral, L_CalleDetalle, L_Interseccion1, L_Interseccion2, L_Barrio, L_Localidad, L_Provincia FROM entidad_lugares WHERE FK_Encabezado IS NOT NULL AND FK_Encabezado != ''";
        $conditions = [];
    
        $alturaDesde = $params['L_AlturaDesde'] ?? '';
        $alturaHasta = $params['L_AlturaHasta'] ?? '';
        
        // Validar que los valores sean numéricos o estén vacíos
        $alturaDesdeValido = $alturaDesde === '' || ctype_digit($alturaDesde);
        $alturaHastaValido = $alturaHasta === '' || ctype_digit($alturaHasta);
        
        // Remover 'L_AlturaDesde' y 'L_AlturaHasta' de $params para no interferir con el bucle foreach siguiente
        unset($params['L_AlturaDesde'], $params['L_AlturaHasta']);

        // Proceder solo si al menos uno de los valores es numérico o ambos están vacíos
        if ($alturaDesdeValido && $alturaHastaValido) {
            if (!empty($alturaDesde) && !empty($alturaHasta)) {
                // Caso 1: Ambas alturas están presentes y son diferentes
                $conditions[] = "L_AlturaCatastral BETWEEN ? AND ?";
                $queryParams[] = $alturaDesde;
                $queryParams[] = $alturaHasta;
            } elseif (!empty($alturaDesde)) {
                // Caso 2: Solo alturaDesde está presente
                $conditions[] = "L_AlturaCatastral >= ?";
                $queryParams[] = $alturaDesde;
            } elseif (!empty($alturaHasta)) {
                // Caso 3: Solo alturaHasta está presente
                $conditions[] = "L_AlturaCatastral <= ?";
                $queryParams[] = $alturaHasta;
            }
        }
    
        foreach ($params as $key => $value) {
            if (!empty($value)) {
                $conditions[] = "$key LIKE ?";
                $queryParams[] = "%" . $value . "%";
            }
        }
    
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
    
        $sql .= " ORDER BY L_Calle DESC";
    
        $DatosMain['Lugares'] = $this->fetchDataQuery($sql, $queryParams);
    
        return $DatosMain;
    }

    public function fetchDataQueryVehiculos($params) {
        $DatosMain = [];

        $queryParams = [];
        $sql = "SELECT ID_Vehiculo, FK_Encabezado, V_Rol, V_TipoVehiculo, V_Color, V_Marca, V_Modelo, V_Año, V_Dominio, V_NumeroChasis, V_NumeroMotor FROM entidad_vehiculos";
        $conditions = [];

        $hasValidParams = false;
        foreach ($params as $key => $value) {
            if (!empty($value)) {
                $conditions[] = "$key LIKE ?";
                $queryParams[] = "%" . $value . "%";
                $hasValidParams = true;
            }
        }

        if ($hasValidParams) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY V_TipoVehiculo";

        $DatosMain['Vehiculos'] = $this->fetchDataQuery($sql, $queryParams);

        return $DatosMain;
    }

    public function fetchDataQueryAF($params) {
        $DatosMain = [];

        $queryParams = [];
        $sql = "SELECT ID_Arma, FK_Encabezado, AF_TipoAF, AF_EsDeFabricacionCasera, AF_Marca, AF_Modelo, AF_Calibre, AF_NumeroDeSerie FROM entidad_armas";
        $conditions = [];

        $hasValidParams = false;
        foreach ($params as $key => $value) {
            // Se acepta el valor nulo como no aplicar filtro
            if ($value !== '') {
                if ($key === 'AF_EsDeFabricacionCasera') {
                    if ($value === '1' || $value === '0') {
                        // Comparación directa para valores '1' o '0'
                        $conditions[] = "$key = ?";
                        $queryParams[] = $value;
                    }
                    // No se agrega condición si el valor es nulo (''), para incluir todos los registros
                } else {
                    // Uso de LIKE para otros campos no booleanos
                    $conditions[] = "$key LIKE ?";
                    $queryParams[] = "%" . $value . "%";
                }
                $hasValidParams = true;
            }
        }        

        if ($hasValidParams) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY AF_TipoAF";

        $DatosMain['Armas'] = $this->fetchDataQuery($sql, $queryParams);

        return $DatosMain;
    }

    public function fetchDataQuerySecuestros($params) {
        $DatosMain = [];

        $queryParams = [];
        $sql = "SELECT ID_DatoComplementario, FK_Encabezado, DC_Tipo, DC_Comentario FROM entidad_datos_complementarios";
        $conditions = [];

        $hasValidParams = false;
        foreach ($params as $key => $value) {
            if (!empty($value)) {
                $conditions[] = "$key LIKE ?";
                $queryParams[] = "%" . $value . "%";
                $hasValidParams = true;
            }
        }

        if ($hasValidParams) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY DC_Tipo";

        $DatosMain['Secuestros'] = $this->fetchDataQuery($sql, $queryParams);

        return $DatosMain;
    }

    public function fetchDataQuery911($params) {
        $queryParams = [];
        $sql = "SELECT Nota, Fecha, Region, Localidad, Direccion, `0800/911`, Tipificacion, Denunciados, Relato FROM pve911";
        $conditions = [];
    
        $fechaDesde = $params['FechaDesde'] ?? '';
        $fechaHasta = $params['FechaHasta'] ?? '';
    
        // Remover 'FechaDesde' y 'FechaHasta' de $params para no interferir con el bucle foreach siguiente
        unset($params['FechaDesde'], $params['FechaHasta']);
    
        // Manejar FechaDesde y FechaHasta
        if (!empty($fechaDesde) && !empty($fechaHasta)) {
            $conditions[] = "Fecha BETWEEN ? AND ?";
            $queryParams[] = $fechaDesde;
            $queryParams[] = $fechaHasta;
        } elseif (!empty($fechaDesde)) {
            $conditions[] = "Fecha >= ?";
            $queryParams[] = $fechaDesde;
        } elseif (!empty($fechaHasta)) {
            $conditions[] = "Fecha <= ?";
            $queryParams[] = $fechaHasta;
        }
    
        foreach ($params as $key => $value) {
            if (!empty($value)) {
                $conditions[] = "$key LIKE ?";
                $queryParams[] = "%" . $value . "%";
            }
        }
    
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
    
        $sql .= " ORDER BY Fecha";
    
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            return ['status' => 'error', 'message' => 'Error en la preparación de la consulta.'];
        }
    
        if (!empty($queryParams)) {
            $types = str_repeat('s', count($queryParams));
            $stmt->bind_param($types, ...$queryParams);
        }
    
        $stmt->execute();
        $result = $stmt->get_result();
    
        $DatosMain['pve911'] = $result->fetch_all(MYSQLI_ASSOC);
    
        $stmt->close();
    
        return $DatosMain;
    }   
    
    public function fetchDataQueryAUOP($params) {
        $queryParams = [];
        $sql = "SELECT Fecha, Hora, OtraDependencia, DelitoAUOP, LugardelHecho, Barrio, Victima, Imputado, RelatoDelHecho FROM auop";
        $conditions = [];
    
        $fechaDesde = $params['FechaDesde'] ?? '';
        $fechaHasta = $params['FechaHasta'] ?? '';
    
        // Remover 'FechaDesde' y 'FechaHasta' de $params para no interferir con el bucle foreach siguiente
        unset($params['FechaDesde'], $params['FechaHasta']);
    
        // Manejar FechaDesde y FechaHasta
        if (!empty($fechaDesde) && !empty($fechaHasta)) {
            $conditions[] = "Fecha BETWEEN ? AND ?";
            $queryParams[] = $fechaDesde;
            $queryParams[] = $fechaHasta;
        } elseif (!empty($fechaDesde)) {
            $conditions[] = "Fecha >= ?";
            $queryParams[] = $fechaDesde;
        } elseif (!empty($fechaHasta)) {
            $conditions[] = "Fecha <= ?";
            $queryParams[] = $fechaHasta;
        }
    
        foreach ($params as $key => $value) {
            if (!empty($value)) {
                $conditions[] = "$key LIKE ?";
                $queryParams[] = "%" . $value . "%";
            }
        }
    
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
    
        $sql .= " ORDER BY Fecha";
    
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            return ['status' => 'error', 'message' => 'Error en la preparación de la consulta.'];
        }
    
        if (!empty($queryParams)) {
            $types = str_repeat('s', count($queryParams));
            $stmt->bind_param($types, ...$queryParams);
        }
    
        $stmt->execute();
        $result = $stmt->get_result();
    
        $DatosMain['auop'] = $result->fetch_all(MYSQLI_ASSOC);
    
        $stmt->close();
    
        return $DatosMain;
    }   

}

?>
