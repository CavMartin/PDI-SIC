<?php
    // Esta clase es el punto de entrada para todas las solicitudes AJAX a la DB
    require '../../PHP/ServerConnect.php';
    require 'DataFetcher.php';
    require 'DatabaseManager_Insert.php';
    require 'DatabaseManager_Delete.php';
    
    class EndPoint { 
        private $conn;
        private $connSIACIP;
        private $dataFetcher;
        private $dbManagerInsert;
        private $dbManagerDelete;
    
        public function __construct() {
            $this->conn = open_database_connection('carga_pve');
            $this->dataFetcher = new DataFetcher($this->conn);
            $this->dbManagerInsert = new DataBaseManager_Insert($this->conn);
            $this->dbManagerDelete = new DataBaseManager_Delete($this->conn);
        }

        public function handleRequest() {
            header('Content-Type: application/json');
            if (!isset($_REQUEST['action'])) {
                echo json_encode(['status' => 'error', 'message' => 'Sin acción especificada']);
                return;
            }

            $action = $_REQUEST['action'];
        
            switch ($action) {
                case 'fetchDataNewForm': // Obtener el proximo ID del formulario PVE
                    $this->fetchDataNewForm();
                    break;
                case 'insertNewForm': // Crear u nuevo formulario PVE
                    $this->insertNewForm();
                    break;
                case 'fetchDataNewFormURII': // Obtener el proximo ID del formulario PVE_URII
                    $this->fetchDataNewFormURII();
                    break;
                case 'insertNewFormURII': // Crear u nuevo formulario PVE_URII
                    $this->insertNewFormURII();
                    break;
                case 'fetchDataEncabezado': // Obtiene los valores del encabezado de un formulario especifica
                    $this->fetchDataEncabezado();
                    break;
                case 'fetchDataDomicilios': // Obtiene los domicilios relacionados a un formulario especifico
                    $this->fetchDataDomicilios();
                    break;
                case 'fetchDataPersonas': // Obtiene las personas relacionadas a un formulario especifico
                    $this->fetchDataPersonas();
                    break;
                case 'fetchDataVehiculos': // Obtiene los vehículos relacionados a un formulario especifico
                    $this->fetchDataVehiculos();
                    break;
                case 'eliminarPersona': // Elimina una persona especifica de la DB
                    $this->eliminarPersona();
                    break;
                case 'eliminarDomicilio': // Elimina un domicilio especifico de la DB
                    $this->eliminarDomicilio();
                    break;
                case 'eliminarVehiculo': // Elimina un vehículo especifico de la DB
                    $this->eliminarVehiculo();
                    break;
                case 'INSERT_Form': // Guardar cambios en el formulario
                    $this->INSERT_Form();
                    break;
                case 'fetchDataPVE': // Obtener datos del PVE
                    $this->fetchDataPVE();
                    break;
                case 'fetchDataQueryPVE': // Obtener los datos de la consulta cruzada
                    $this->fetchDataQueryPVE();
                    break;
                default:
                    echo json_encode(['status' => 'error', 'message' => 'Acción especificada desconocida']);
            }
        }

        private function fetchDataNewForm() {
            $response = $this->dataFetcher->fetchDataNewForm();
            if ($response !== null) {
                echo json_encode(['status' => 'success', 'data' => $response]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No se pudieron obtener los datos debido a un error inesperado']);
            }
        }

        private function insertNewForm() {
            // Obtener los valores de la solicitud
            $Formulario = $_REQUEST['Formulario'] ?? null;
            $Numero = $_REQUEST['Numero'] ?? null;
            $Año = $_REQUEST['Año'] ?? null;
        
            // Verificar que todos los datos necesarios están presentes
            if ($Formulario === null || $Numero === null || $Año === null) {
                echo json_encode(['status' => 'error', 'message' => 'Datos incompletos para crear el formulario']);
                return;
            }
        
            // Llamar al método del manager con los argumentos
            $result = $this->dbManagerInsert->insertNewForm($Formulario, $Numero, $Año);
            if (is_string($result)) {
                echo $result; // Si es una cadena, probablemente es un mensaje JSON ya formateado
                return;
            }
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Nuevo formulario creado con éxito']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al crear el nuevo formulario']);
            }
        }

        private function fetchDataNewFormURII() {
            $response = $this->dataFetcher->fetchDataNewFormURII();
            if ($response !== null) {
                echo json_encode(['status' => 'success', 'data' => $response]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No se pudieron obtener los datos debido a un error inesperado']);
            }
        }

        private function insertNewFormURII() {
            // Obtener los valores de la solicitud
            $Formulario = $_REQUEST['Formulario'] ?? null;
            $Numero = $_REQUEST['Numero'] ?? null;
            $Año = $_REQUEST['Año'] ?? null;
        
            // Verificar que todos los datos necesarios están presentes
            if ($Formulario === null || $Numero === null || $Año === null) {
                echo json_encode(['status' => 'error', 'message' => 'Datos incompletos para crear el formulario']);
                return;
            }
        
            // Llamar al método del manager con los argumentos
            $result = $this->dbManagerInsert->insertNewFormURII($Formulario, $Numero, $Año);
            if (is_string($result)) {
                echo $result; // Si es una cadena, probablemente es un mensaje JSON ya formateado
                return;
            }
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Nuevo formulario creado con éxito']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al crear el nuevo formulario']);
            }
        }

        private function fetchDataDomicilios() {
            $formularioPVE = $_POST['formularioPVE'] ?? '';
            if ($formularioPVE !== '') {
                $domicilios = $this->dataFetcher->fetchDataDomicilios($formularioPVE);
                echo json_encode(['status' => 'success', 'data' => $domicilios, 'message' => empty($domicilios) ? 'No se encontraron domicilios asociados' : '']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Parámetro formularioPVE es requerido']);
            }
        }
        
        private function fetchDataPersonas() {
            $formularioPVE = $_POST['formularioPVE'] ?? '';
            if ($formularioPVE !== '') {
                $personas = $this->dataFetcher->fetchDataPersonas($formularioPVE);
                echo json_encode(['status' => 'success', 'data' => $personas, 'message' => empty($personas) ? 'No se encontraron personas asociadas' : '']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Parámetro formularioPVE es requerido']);
            }
        }
        
        private function fetchDataVehiculos() {
            $formularioPVE = $_POST['formularioPVE'] ?? '';
            if ($formularioPVE !== '') {
                $vehiculos = $this->dataFetcher->fetchDataVehiculos($formularioPVE);
                echo json_encode(['status' => 'success', 'data' => $vehiculos, 'message' => empty($vehiculos) ? 'No se encontraron vehículos asociados' : '']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Parámetro formularioPVE es requerido']);
            }
        }
        
        private function INSERT_Form() {
            $formData = $_POST;
            $result = $this->dbManagerInsert->INSERT_Form($formData);
            if ($result === true) {
                echo json_encode(['status' => 'success', 'message' => 'Cambios guardados con éxito']);
            } else {
                echo $result; // Esto debería ser un JSON con el mensaje de error
            }
        }
        
        private function eliminarDomicilio() {
            if (!isset($_POST['ClavePrimaria'])) {
                echo json_encode(['status' => 'error', 'message' => 'ClavePrimaria no proporcionada']);
                return;
            }

            $ClavePrimaria = $_POST['ClavePrimaria'];
            $result = $this->dbManagerDelete->DELETE_Lugar($ClavePrimaria);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Registro eliminado con éxito']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el registro']);
            }
        }

        private function eliminarPersona() {
            if (!isset($_POST['ID_Persona'])) {
                echo json_encode(['status' => 'error', 'message' => 'ID_Persona no proporcionado']);
                return;
            }

            $ID_Persona = $_POST['ID_Persona'];
            $result = $this->dbManagerDelete->DELETE_Persona($ID_Persona);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Registro eliminado con éxito']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el registro']);
            }
        }

        private function eliminarVehiculo() {
            if (!isset($_POST['ID_Vehiculo'])) {
                echo json_encode(['status' => 'error', 'message' => 'ID_Vehiculo no proporcionado']);
                return;
            }
        
            $ID_Vehiculo = $_POST['ID_Vehiculo'];
            $result = $this->dbManagerDelete->DELETE_Vehiculo($ID_Vehiculo);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Registro eliminado con éxito']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el registro']);
            }
        }        

        private function fetchDataPVE() {
            // Verificar si el número IP ha sido enviado y no está vacío
            if (isset($_POST['formularioPVE']) && !empty($_POST['formularioPVE'])) {
                $formularioPVE = $_POST['formularioPVE'];
                // Intentar obtener los datos de la incidencia priorizada
                try {
                    $DataPVE = $this->dataFetcher->fetchDataPVE($formularioPVE);
                    // Si se obtuvieron datos, devolver éxito
                    if ($DataPVE !== null) {
                        echo json_encode(['status' => 'success', 'data' => $DataPVE]);
                    } else {
                        // Si no se encontraron datos, devolver error
                        echo json_encode(['status' => 'error', 'message' => 'No se encontraron datos para el PVE con ID ' . $formularioPVE]);
                    }
                } catch (Exception $e) {
                    // Manejar cualquier excepción que pueda ocurrir durante la obtención de los datos
                    echo json_encode(['status' => 'error', 'message' => 'Ocurrió un error al obtener los datos: ' . $e->getMessage()]);
                }
            } else {
                // Devolver error si no se proporcionó el número de ID o está vacío
                echo json_encode(['status' => 'error', 'message' => 'El número de ID es requerido.']);
            }
        }  

        private function fetchDataQueryPVE() {
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
        
            try {
                $response = $this->dataFetcher->fetchDataQueryPVE($params);
                
                if (is_array($response) && count($response) > 0) {
                    // Si hay datos, devolver el éxito con los datos
                    echo json_encode(['status' => 'success', 'data' => $response]);
                } elseif (is_array($response) && count($response) === 0) {
                    // Si no hay datos, devolver éxito pero con mensaje indicando que no hubo resultados
                    echo json_encode(['status' => 'success', 'data' => [], 'message' => 'Su consulta no ha arrojado ningún resultado.']);
                } else {
                    // Manejo de caso inesperado
                    throw new Exception("Ocurrió un error inesperado durante la consulta.");
                }
            } catch (Exception $e) {
                // Enviar mensaje de error genérico y loguear el error detallado para desarrollo
                echo json_encode(['status' => 'error', 'message' => 'Error al obtener los datos. Si el error persiste, contacte al administrador']);
                error_log($e->getMessage()); // Registrar el error para fines de depuración
            }
        }        

        public function __destruct() {
            $this->conn->close();
        }
    }

    // Configuración de la base de datos al constructor
    $ajaxEndpoint = new EndPoint();
    $ajaxEndpoint->handleRequest();
?>
