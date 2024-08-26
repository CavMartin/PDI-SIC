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
    public function fetchDataNewForm() {
        $Año = date("Y"); // Obtener el año actual
        $Numero = 1;  // Inicializar $Numero con un valor por defecto
        $sqlMaxNum = "SELECT MAX(Numero) AS max_num FROM entidad_encabezado WHERE Año = ?";
        $stmtMaxNum = $this->conn->prepare($sqlMaxNum);
        if (!$stmtMaxNum) {
            throw new Exception("Error al preparar la consulta: " . $this->conn->error);
        }
        // Incluir userGroup como segundo parámetro en bind_param
        $stmtMaxNum->bind_param("i", $Año);
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
            'Año' => $Año
        ];
    }
   
    // Método para obtener el año actual y el número máximo de incidencia dentro de dicho año
    public function fetchDataNewFormURII() {
        $Año = date("Y"); // Obtener el año actual
        $Numero = 1;  // Inicializar $Numero con un valor por defecto
        $usergroup = $_SESSION['usergroup'];
        $sqlMaxNum = "SELECT MAX(Numero) AS max_num FROM entidad_encabezado WHERE Año = ? AND Fuente = ?";
        $stmtMaxNum = $this->conn->prepare($sqlMaxNum);
        if (!$stmtMaxNum) {
            throw new Exception("Error al preparar la consulta: " . $this->conn->error);
        }
        // Incluir userGroup como segundo parámetro en bind_param
        $stmtMaxNum->bind_param("is", $Año, $usergroup);
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

        $Formulario = $Numero . "-" . $Año . "_" . $usergroup;

        return [
            'Formulario' => $Formulario,
            'Numero' => $Numero,
            'Año' => $Año,
            'Fuente' => $usergroup
        ];
    }

    public function fetchDataEncabezado($formularioPVE) {
        $encabezadoData = null;
        // Consulta SQL para obtener los datos de una persona específica por su ID
        $stmt = $this->conn->prepare("SELECT * FROM entidad_encabezado WHERE Formulario = ?");
        $stmt->bind_param("s", $formularioPVE);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $encabezadoData = $result->fetch_assoc();
        }

        return $encabezadoData;
    }

    public function fetchDataDomicilios($formularioPVE) {
        $domicilios = []; // Inicializa $domicilios como un arreglo vacío
        $stmt = $this->conn->prepare("SELECT * FROM entidad_lugares WHERE FK_Encabezado = ? ORDER BY NumeroDeOrden ASC"); // Consulta SQL para obtener los domicilios relacionados
        $stmt->bind_param("s", $formularioPVE);
        $stmt->execute();
        $result = $stmt->get_result();
    
        while ($domicilio = $result->fetch_assoc()) {
            $domicilios[] = $domicilio;
        }
    
        $stmt->close();
    
        return $domicilios; // Devuelve el arreglo como datos
    }
    
    public function fetchDataPersonas($formularioPVE) {
        $personas = []; // Inicializa $personas como un arreglo vacío
        $stmt = $this->conn->prepare("SELECT * FROM entidad_personas WHERE FK_Encabezado = ? ORDER BY NumeroDeOrden ASC"); // Consulta SQL para obtener las personas relacionadas
        $stmt->bind_param("s", $formularioPVE);
        $stmt->execute();
        $result = $stmt->get_result();
    
        while ($persona = $result->fetch_assoc()) {
            $personas[] = $persona;
        }
    
        $stmt->close();
    
        return $personas; // Devuelve el arreglo como datos
    }

    public function fetchDataVehiculos($formularioPVE) {
        $vehiculos = []; // Inicializa $vehiculos como un arreglo vacío
        $stmt = $this->conn->prepare("SELECT * FROM entidad_vehiculos WHERE FK_Encabezado = ? ORDER BY NumeroDeOrden ASC"); // Consulta SQL para obtener los vehiculos relacionados
        $stmt->bind_param("s", $formularioPVE);
        $stmt->execute();
        $result = $stmt->get_result();
    
        while ($vehiculo = $result->fetch_assoc()) {
            $vehiculos[] = $vehiculo;
        }
    
        $stmt->close();
    
        return $vehiculos; // Devuelve el arreglo como datos
    }

    public function fetchDataPVE($formularioPVE) {
        $DataPVE = [];

        // Consulta para entidad encabezado
        $Encabezado = $this->fetchData("SELECT * FROM entidad_encabezado WHERE Formulario = ?", $formularioPVE);
        if (count($Encabezado) > 0) {
            $encabezadoDatos = $Encabezado[0];
            
            // Formatear la fecha
            if (!empty($encabezadoDatos['Fecha'])) {
                $date = DateTime::createFromFormat('Y-m-d', $encabezadoDatos['Fecha']);
                if ($date) {
                    $encabezadoDatos['Fecha'] = $date->format('d/m/Y'); // Formato DD/MM/YYYY
                }
            }
            
            $DataPVE['Encabezado'] = $encabezadoDatos;
        } else {
            $DataPVE['Encabezado'] = null;
        }

        // Consulta para entidad "Lugar" y sus entidades complementarias
        $lugares = $this->fetchData("SELECT * FROM entidad_lugares WHERE FK_Encabezado = ? ORDER BY NumeroDeOrden", $formularioPVE);
        $DataPVE['Lugares'] = $lugares;

        // Consulta para entidad "Persona"
        $personas = $this->fetchData("SELECT * FROM entidad_personas WHERE FK_Encabezado = ? ORDER BY NumeroDeOrden", $formularioPVE);
        $DataPVE['Personas'] = $personas;

        // Consulta para entidad "Vehiculo"
        $vehiculos = $this->fetchData("SELECT * FROM entidad_vehiculos WHERE FK_Encabezado = ? ORDER BY NumeroDeOrden", $formularioPVE);
        $DataPVE['Vehiculos'] = $vehiculos;

        // Comprobación simplificada basada solo en la existencia del Encabezado
        if ($DataPVE['Encabezado'] === null) {
            // El Encabezado no existe, por lo tanto, no hay datos relevantes
            return null; // Devolver null o cualquier otra indicación de "sin datos"
        }

        // Si el Encabezado existe, devuelve todos los datos recopilados
        return $DataPVE;

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

    // Consulta para el mapa de PVE público
    public function fetchDataQueryStaticMap() {
        // Definir la consulta SQL para seleccionar datos de entidad_encabezado y entidad_lugares
        $sql = "SELECT 
                    ee.Formulario, 
                    ee.Fecha, 
                    ee.Fuente, 
                    ee.ReporteAsociado, 
                    ee.Tipologia, 
                    ee.ModalidadComisiva, 
                    ee.TipoEstupefaciente, 
                    ee.Relato, 
                    el.L_Calle, 
                    el.L_AlturaCatastral, 
                    el.L_CalleDetalle, 
                    el.L_Interseccion1, 
                    el.L_Interseccion2, 
                    el.L_Localidad,
                    el.L_Coordenadas
                FROM 
                    entidad_encabezado ee
                LEFT JOIN 
                    entidad_lugares el ON ee.Formulario = el.FK_Encabezado
                WHERE 
                    ee.Fecha > '2023-08-01'
                AND 
                    ee.Tipologia != 'CONSUMO'
                ORDER BY 
                    ee.Fecha DESC";
        
        // Ejecutar la consulta y devolver el resultado
        return $this->fetchDataQuery($sql);
    }

    // Consulta cruzada para los PVE
    public function fetchDataQueryPVE() {
        $params = [
            'FechaDesde' => $_REQUEST['FechaDesde'] ?? '',
            'FechaHasta' => $_REQUEST['FechaHasta'] ?? '',
            'Fuente' => $_REQUEST['Fuente'] ?? '',
            'ReporteAsociado' => $_REQUEST['ReporteAsociado'] ?? '',
            'Relevancia' => $_REQUEST['Relevancia'] ?? '',
            'OperadorSQL' => $_REQUEST['OperadorSQL'] ?? 'AND',
            'Tipologia' => $_REQUEST['Tipologia'] ?? [],
            'ModalidadComisiva' => $_REQUEST['ModalidadComisiva'] ?? [],
            'TipoEstupefaciente' => $_REQUEST['TipoEstupefaciente'] ?? [],
            'ConnivenciaPolicial' => $_REQUEST['ConnivenciaPolicial'] ?? '',
            'PosiblesUsurpaciones' => $_REQUEST['PosiblesUsurpaciones'] ?? '',
            'UsoAF' => $_REQUEST['UsoAF'] ?? '',
            'ParticipacionDeMenores' => $_REQUEST['ParticipacionDeMenores'] ?? '',
            'ParticipacionOrgCrim' => $_REQUEST['ParticipacionOrgCrim'] ?? '',
            'OrganizacionCriminal' => $_REQUEST['OrganizacionCriminal'] ?? '',
            'Relato' => $_REQUEST['Relato'] ?? '',
            'Lugares' => json_decode($_REQUEST['joinLugaresParams'] ?? '', true) ?? [],
            'Personas' => json_decode($_REQUEST['joinPersonasParams'] ?? '', true) ?? [],
            'Vehiculos' => json_decode($_REQUEST['joinVehiculosParams'] ?? '', true) ?? []
        ];
    
        $conditions = [];
        $queryParams = [];
    
        // Manejo de fechas
        $fechaDesde = $params['FechaDesde'] ?? '';
        $fechaHasta = $params['FechaHasta'] ?? '';
        if (!empty($fechaDesde) && !empty($fechaHasta)) {
            $conditions[] = "ee.Fecha BETWEEN ? AND ?";
            $queryParams[] = $fechaDesde . " 00:00:00";
            $queryParams[] = $fechaHasta . " 23:59:59";
        } elseif (!empty($fechaDesde)) {
            $conditions[] = "ee.Fecha >= ?";
            $queryParams[] = $fechaDesde . " 00:00:00";
        } elseif (!empty($fechaHasta)) {
            $conditions[] = "ee.Fecha <= ?";
            $queryParams[] = $fechaHasta . " 23:59:59";
        }
    
        // Filtros directos de entidad_encabezado
        foreach (['Fuente', 'ReporteAsociado', 'Relevancia', 'Tipologia', 'ModalidadComisiva', 'TipoEstupefaciente', 'ConnivenciaPolicial', 'PosiblesUsurpaciones', 'UsoAF', 'ParticipacionDeMenores', 'ParticipacionOrgCrim', 'OrganizacionCriminal', 'Relato'] as $field) {
            if (!empty($params[$field])) {
                if (is_array($params[$field])) {
                    $subConditions = [];
                    foreach ($params[$field] as $value) {
                        $subConditions[] = "ee.$field LIKE ?";
                        $queryParams[] = "%" . $value . "%";
                    }
                    $conditions[] = "(" . implode(" {$params['OperadorSQL']} ", $subConditions) . ")";
                } else {
                    $conditions[] = "ee.$field LIKE ?";
                    $queryParams[] = "%" . $params[$field] . "%";
                }
            }
        }
    
        // Filtros de Lugares
        if (!empty($params['Lugares'])) {
            foreach ($params['Lugares'] as $key => $value) {
                if (!empty($value)) {
                    $conditions[] = "el.$key LIKE ?";
                    $queryParams[] = "%" . $value . "%";
                }
            }
        }
    
        // Filtros de Personas
        if (!empty($params['Personas'])) {
            foreach ($params['Personas'] as $key => $value) {
                if (!empty($value)) {
                    $conditions[] = "ep.$key LIKE ?";
                    $queryParams[] = "%" . $value . "%";
                }
            }
        }
    
        // Filtros de Vehículos
        if (!empty($params['Vehiculos'])) {
            foreach ($params['Vehiculos'] as $key => $value) {
                if (!empty($value)) {
                    $conditions[] = "ev.$key LIKE ?";
                    $queryParams[] = "%" . $value . "%";
                }
            }
        }
    
        // Armar la consulta SQL con los JOIN
        $sql = "SELECT ee.*, el.*, ep.*, ev.* 
                FROM entidad_encabezado ee
                LEFT JOIN entidad_lugares el ON ee.Formulario = el.FK_Encabezado
                LEFT JOIN entidad_personas ep ON ee.Formulario = ep.FK_Encabezado
                LEFT JOIN entidad_vehiculos ev ON ee.Formulario = ev.FK_Encabezado";
    
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
    
        $sql .= " ORDER BY ee.Fecha DESC";
    
        // Ejecutar la consulta y devolver el resultado
        return $this->fetchDataQuery($sql, $queryParams);
    }

}

?>
