<?php
    // Esta clase es el punto de entrada para todas las solicitudes AJAX a la DB
    require '../../PHP/ServerConnect.php';
    require 'DataFetcher.php';
    require 'DatabaseManager_Insert.php';
    require 'DatabaseManager_Delete.php';
    
    class EndPoint_AJAX { 
        private $conn;
        private $dataFetcher;
        private $dbManagerInsert;
        private $dbManagerDelete;
    
        public function __construct() {
            $this->conn = open_database_connection('sic');
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
                case 'fetchDataNewForm': // Obtener el proximo ID del dispositivo SIACIP
                    $this->fetchDataNewForm();
                    break;
                case 'insertNewForm': // Crear u nuevo formulario
                    $this->insertNewForm();
                    break;
                case 'UPDATE_TipoHecho': // Cambia la tipificación de una instancia del dispositivo SIACIP especifico
                    $this->UPDATE_TipoHecho();
                    break;
                case 'UPDATE_Estado': // Cambio el estado de una instancia del dispositivo
                    $this->UPDATE_Estado();
                    break;
                case 'INSERT_Reporte': // Inserta en la DB la instancia del dispositivo SIACIP
                    $this->INSERT_Reporte();
                    break;
                case 'fetchDataReporte': // Obtiene los datos almacenados de un reporte preliminar especifico
                    $this->fetchDataReporte();
                    break;
                case 'fetchDataIncidenciaPriorizada': // Obtiene los datos almacenados de una incidencia priorizada especifica
                    $this->fetchDataIncidenciaPriorizada();
                    break;
                case 'fetchDataEncabezado': // Obtiene los valores del encabezado de una IP especifica
                    $this->fetchDataEncabezado();
                    break;
                case 'getDomicilios':
                    $this->getDomicilios();
                    break;
                case 'eliminarDomicilio':
                    $this->eliminarDomicilio();
                    break;
                case 'getDatosComplementarios':
                    $this->getDatosComplementarios();
                    break;
                case 'eliminarDatoComplementario':
                    $this->eliminarDatoComplementario();
                    break;
                case 'insertarDatosEncabezado':
                    $this->insertarDatosEncabezado();
                    break;
                case 'insertarDatosPersona':
                    $this->insertarDatosPersona();
                    break;
                case 'eliminarPersona':
                    $this->eliminarPersona();
                    break;
                case 'insertarDatosLugar':
                    $this->insertarDatosLugar();
                    break;
                case 'eliminarLugar':
                    $this->eliminarLugar();
                    break;
                case 'insertarDatosVehiculo':
                    $this->insertarDatosVehiculo();
                    break;
                case 'eliminarVehiculo':
                    $this->eliminarVehiculo();
                    break;
                case 'insertarDatosAF':
                    $this->insertarDatosAF();
                    break;
                case 'eliminarAF':
                    $this->eliminarAF();
                    break;
                case 'insertarDatosSecuestro':
                    $this->insertarDatosSecuestro();
                    break;
                case 'eliminarSecuestro':
                    $this->eliminarSecuestro();
                    break;
                case 'fetchDataQueryBandeja':
                    $this->fetchDataQueryBandeja();
                    break;
                case 'fetchDataQueryIP':
                    $this->fetchDataQueryIP();
                    break;
                case 'fetchDataQueryPersonas':
                    $this->fetchDataQueryPersonas();
                    break;
                case 'fetchDataQueryLugares':
                    $this->fetchDataQueryLugares();
                    break;
                case 'fetchDataQueryVehiculos':
                    $this->fetchDataQueryVehiculos();
                    break;
                case 'fetchDataQueryAF':
                    $this->fetchDataQueryAF();
                    break;
                case 'fetchDataQuerySecuestros':
                    $this->fetchDataQuerySecuestros();
                    break;
                case 'getDataPersona':
                    $this->getDataPersona();
                    break;
                case 'getDataLugar':
                    $this->getDataLugar();
                    break;
                case 'getDataVehiculo':
                    $this->getDataVehiculo();
                    break;
                case 'getDataArma':
                    $this->getDataArma();
                    break;
                case 'getDataSecuestro':
                    $this->getDataSecuestro();
                    break;
                case 'fetchDataQuery911': // Caso para obtener datos de la base de datos externa 911
                    $this->fetchDataQuery911();
                    break;
                case 'fetchDataQueryAUOP': // Caso para obtener datos de la base de datos externa AUOP
                    $this->fetchDataQueryAUOP();
                    break;
                default:
                    echo json_encode(['status' => 'error', 'message' => 'Acción especificada desconocida']);
            }
        }

        private function fetchDataNewForm() {
            $userGroup = $_SESSION['userGroup'] ?? '';
            $response = $this->dataFetcher->fetchDataNewForm($userGroup);
        
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

        private function UPDATE_Estado() {
            $formData = $_POST;
            $ID = isset($formData['ID']) ? $formData['ID'] : null;
            $NuevoEstado = isset($formData['NuevoEstado']) ? $formData['NuevoEstado'] : null;
            
            if ($ID === null || $NuevoEstado === null) {
                echo json_encode(['status' => 'error', 'message' => 'ID o NuevoEstado no especificados']);
                return;
            }
            
            $result = $this->dbManagerInsert->UPDATE_Estado($ID, $NuevoEstado);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Estado del dispositivo modificado con éxito']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al modificar el estado del dispositivo']);
            }
        }
        

        private function fetchDataReporte() {
            $RP_Numero = $_POST['RP_Numero'] ?? '';
            $RPData = $this->dataFetcher->fetchDataReporte($RP_Numero);
            if ($RPData !== null) {
                echo json_encode(['status' => 'success', 'data' => $RPData]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No se encontraron datos para el reporte preliminar con ID ' . $RP_Numero]);
            }
        }

        private function fetchDataAmpliacionesRP() {
            $ID = $_POST['RP_Numero'] ?? '';
            $AmpliacionesData = $this->dataFetcher->fetchDataAmpliacionesRP($ID);
            if ($AmpliacionesData !== null) {
                echo json_encode(['status' => 'success', 'data' => $AmpliacionesData]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No se encontraron datos para el reporte preliminar con ID ' . $ID]);
            }
        }

        private function INSERT_Reporte() {
            $formData = $_POST;
            $result = $this->dbManagerInsert->INSERT_Reporte($formData);
            if (is_string($result)) {
                echo $result;
                return;
            }
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Cambios guardados con éxito']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al guardar los cambios en el reporte preliminar']);
            }
        }

        private function fetchDataIncidenciaPriorizada() {
            // Verificar si el número ID ha sido enviado y no está vacío
            if (isset($_POST['ID']) && !empty($_POST['ID'])) {
                $ID = $_POST['ID'];
                // Intentar obtener los datos de la incidencia priorizada
                try {
                    $Data_IncidenciaPriorizada = $this->dataFetcher->fetchDataIncidenciaPriorizada($ID);
                    // Si se obtuvieron datos, devolver éxito
                    if ($Data_IncidenciaPriorizada !== null) {
                        echo json_encode(['status' => 'success', 'data' => $Data_IncidenciaPriorizada]);
                    } else {
                        // Si no se encontraron datos, devolver error
                        echo json_encode(['status' => 'error', 'message' => 'No se encontraron datos para el formulario solicitado']);
                    }
                } catch (Exception $e) {
                    // Manejar cualquier excepción que pueda ocurrir durante la obtención de los datos
                    echo json_encode(['status' => 'error', 'message' => 'Ocurrió un error al obtener los datos: ' . $e->getMessage()]);
                }
            } else {
                // Devolver error si no se proporcionó el número IP o está vacío
                echo json_encode(['status' => 'error', 'message' => 'El número de ID es requerido.']);
            }
        }

        private function getDataPersona() {
            $ID_Persona = $_POST['ID_Persona'] ?? '';
            $datosPersona = $this->dataFetcher->fetchDataPersona($ID_Persona);
            if ($datosPersona !== null) {
                echo json_encode(['status' => 'success', 'data' => $datosPersona]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No se encontraron datos para la persona con ID ' . $ID_Persona]);
            }
        }

        private function getDataLugar() {
            $ID_Lugar = $_POST['ID_Lugar'] ?? '';
            $datosLugar = $this->dataFetcher->fetchDataLugar($ID_Lugar);
            if ($ID_Lugar !== null) {
                echo json_encode(['status' => 'success', 'data' => $datosLugar]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No se encontraron datos para la persona con ID ' . $ID_Lugar]);
            }
        }

        private function getDataVehiculo() {
            $ID_Vehiculo = $_POST['ID_Vehiculo'] ?? '';
            $datosVehiculo = $this->dataFetcher->fetchDataVehiculo($ID_Vehiculo);
            if ($datosVehiculo !== null) {
                echo json_encode(['status' => 'success', 'data' => $datosVehiculo]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No se encontraron datos para el mensaje con ID ' . $ID_Vehiculo]);
            }
        }

        private function getDataArma() {
            $ID_Arma = $_POST['ID_Arma'] ?? '';
            $datosArma = $this->dataFetcher->fetchDataAF($ID_Arma);
            if ($datosArma !== null) {
                echo json_encode(['status' => 'success', 'data' => $datosArma]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No se encontraron datos para el mensaje con ID ' . $ID_Arma]);
            }
        }

        private function getDataSecuestro() {
            $ID_DatoComplementario = $_POST['ID_DatoComplementario'] ?? '';
            $datosSecuestro = $this->dataFetcher->fetchDataSecuestros($ID_DatoComplementario);
            if ($datosSecuestro !== null) {
                echo json_encode(['status' => 'success', 'data' => $datosSecuestro]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No se encontraron datos para el secuestro ID ' . $ID_DatoComplementario]);
            }
        }

        private function getDomicilios() {
            $ID_Persona = $_GET['ID_Persona'] ?? '';
            $this->dataFetcher->getDomiciliosJSON($ID_Persona);
        }

        private function eliminarDomicilio() {
            $ID_Lugar = $_GET['ID_Lugar'] ?? '';
            $result = $this->dbManagerDelete->eliminarDomicilio($ID_Lugar);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Domicilio eliminado correctamente']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al eliminar domicilio']);
            }
        }

        private function getDatosComplementarios() {
            $ID_Persona = $_GET['ClavePrimaria'] ?? '';
            $this->dataFetcher->getDatosComplementariosJSON($ID_Persona);
        }

        private function eliminarDatoComplementario() {
            $ID_DatoComplementario = $_GET['ID_DatoComplementario'] ?? '';
            $result = $this->dbManagerDelete->eliminarDatoComplementario($ID_DatoComplementario);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Dato complementario eliminado correctamente']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al eliminar dato complementario']);
            }
        }    

        private function insertarDatosEncabezado() {
            $formData = $_POST;
            $result = $this->dbManagerInsert->INSERT_Encabezado($formData);
            if (is_string($result)) {
                echo $result;
                return;
            }
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Cambios guardados con éxito']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al guardar los cambios en el encabezado']);
            }
        }

        private function insertarDatosPersona() {
            $formData = $_POST;
            $result = $this->dbManagerInsert->INSERT_Persona($formData);
            if (is_string($result)) {
                echo $result;
                return;
            }
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Cambios guardados con éxito']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al guardar los cambios en el Mensaje']);
            }
        }

        private function eliminarPersona() {
            $ClavePrimaria = $_POST['ClavePrimaria'];
            $result = $this->dbManagerDelete->DELETE_Persona($ClavePrimaria);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Registro eliminado con éxito']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el registro']);
            }
        }

        private function insertarDatosLugar() {
            $formData = $_POST;
            $result = $this->dbManagerInsert->INSERT_Lugar($formData);
            if (is_string($result)) {
                echo $result;
                return;
            }
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Cambios guardados con éxito']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al guardar los cambios en el Lugar']);
            }
        }

        private function eliminarLugar() {
            $ClavePrimaria = $_POST['ClavePrimaria'];
            $result = $this->dbManagerDelete->DELETE_Lugar($ClavePrimaria);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Registro eliminado con éxito']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el registro']);
            }
        }

        private function insertarDatosVehiculo() {
            $formData = $_POST;
            $result = $this->dbManagerInsert->INSERT_Vehiculo($formData);
            if (is_string($result)) {
                echo $result;
                return;
            }
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Cambios guardados con éxito']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al guardar los cambios en el Vehiculo']);
            }
        }

        private function eliminarVehiculo() {
            $ClavePrimaria = $_POST['ClavePrimaria'];
            $result = $this->dbManagerDelete->DELETE_Vehiculo($ClavePrimaria);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Registro eliminado con éxito']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el registro']);
            }
        }

        private function insertarDatosAF() {
            $formData = $_POST;
            $result = $this->dbManagerInsert->INSERT_Arma($formData);
            if (is_string($result)) {
                echo $result;
                return;
            }
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Cambios guardados con éxito']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al guardar los cambios en el arma de fuego']);
            }
        }

        private function eliminarAF() {
            $ClavePrimaria = $_POST['ClavePrimaria'];
            $result = $this->dbManagerDelete->DELETE_AF($ClavePrimaria);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Registro eliminado con éxito']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el registro']);
            }
        }

        private function insertarDatosSecuestro() {
            $formData = $_POST;
            $result = $this->dbManagerInsert->INSERT_Secuestro($formData);
            if (is_string($result)) {
                echo $result;
                return;
            }
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Cambios guardados con éxito']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al guardar los cambios en el Secuestro']);
            }
        }

        private function eliminarSecuestro() {
            $ClavePrimaria = $_POST['ClavePrimaria'];
            $result = $this->dbManagerDelete->DELETE_Secuestro($ClavePrimaria);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Registro eliminado con éxito']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el registro']);
            }
        }

        private function fetchIncidenciasAbiertas() {
            $incidenciasAbiertas = $this->dataFetcher->fetchIncidenciasAbiertas();
        
            if (!empty($incidenciasAbiertas)) {
                echo json_encode(['status' => 'success', 'data' => $incidenciasAbiertas]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No se encontraron incidencias abiertas']);
            }
        }


// Aquí se agrupan los metodos para obtener los datos de las consultas para mostrar en las tablas del sistema de consultas

        private function fetchDataQueryBandeja() {
            $params = [
                'FechaDesde' => $_REQUEST['FechaDesde'] ?? '',
                'FechaHasta' => $_REQUEST['FechaHasta'] ?? '',
                'Clasificacion' => $_REQUEST['Clasificacion'] ?? '',
                'Causa' => $_REQUEST['Causa'] ?? '',
                'Dependencia' => $_REQUEST['Dependencia'] ?? '',
                'Juzgado' => $_REQUEST['Juzgado'] ?? '',
                'Fiscal' => $_REQUEST['Fiscal'] ?? '',
                'Relato' => $_REQUEST['Relato'] ?? ''
            ];

            $response = $this->dataFetcher->fetchDataQueryBandeja($params);
        
            if ($response !== null) {
                echo json_encode(['status' => 'success', 'data' => $response]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al obtener datos de la bandeja de entrada. Si el error persiste, contacte al administrador']);
            }
        }

        private function fetchDataQueryPersonas() {
            $params = [
                'P_Rol' => $_REQUEST['P_Rol'] ?? '',
                'P_Apellido' => $_REQUEST['P_Apellido'] ?? '',
                'P_Nombre' => $_REQUEST['P_Nombre'] ?? '',
                'P_Alias' => $_REQUEST['P_Alias'] ?? '',
                'P_DNI' => $_REQUEST['P_DNI'] ?? '',
                'P_Genero' => $_REQUEST['P_Genero'] ?? '',
                'P_EstadoCivil' => $_REQUEST['P_EstadoCivil'] ?? ''
            ];
    
            $response = $this->dataFetcher->fetchDataQueryPersonas($params);
        
            if ($response !== null) {
                echo json_encode(['status' => 'success', 'data' => $response]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al obtener datos de las personas. Si el error persiste, contacte al administrador']);
            }
        }

        private function fetchDataQueryLugares() {
            $params = [
                'L_Rol' => $_REQUEST['L_Rol'] ?? '',
                'L_TipoLugar' => $_REQUEST['L_TipoLugar'] ?? '',
                'L_Calle' => $_REQUEST['L_Calle'] ?? '',
                'L_AlturaDesde' => $_REQUEST['L_AlturaDesde'] ?? '',
                'L_AlturaHasta' => $_REQUEST['L_AlturaHasta'] ?? '',
                'L_Barrio' => $_REQUEST['L_Barrio'] ?? '',
                'L_Localidad' => $_REQUEST['L_Localidad'] ?? ''
            ];
    
            $response = $this->dataFetcher->fetchDataQueryLugares($params);
        
            if ($response !== null) {
                echo json_encode(['status' => 'success', 'data' => $response]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al obtener datos de las personas. Si el error persiste, contacte al administrador']);
            }
        }

        private function fetchDataQueryVehiculos() {
            $params = [
                'V_Rol' => $_REQUEST['V_Rol'] ?? '',
                'V_TipoVehiculo' => $_REQUEST['V_TipoVehiculo'] ?? '',
                'V_Color' => $_REQUEST['V_Color'] ?? '',
                'V_Marca' => $_REQUEST['V_Marca'] ?? '',
                'V_Modelo' => $_REQUEST['V_Modelo'] ?? '',
                'V_Año' => $_REQUEST['V_Año'] ?? '',
                'V_Dominio' => $_REQUEST['V_Dominio'] ?? '',
                'V_NumeroChasis' => $_REQUEST['V_NumeroChasis'] ?? '',
                'V_NumeroMotor' => $_REQUEST['V_NumeroMotor'] ?? ''
            ];
    
            $response = $this->dataFetcher->fetchDataQueryVehiculos($params);
        
            if ($response !== null) {
                echo json_encode(['status' => 'success', 'data' => $response]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al obtener datos de las vehículos. Si el error persiste, contacte al administrador']);
            }
        }

        private function fetchDataQueryAF() {
            $params = [
                'AF_TipoAF' => $_REQUEST['AF_TipoAF'] ?? '',
                'AF_EsDeFabricacionCasera' => $_REQUEST['AF_EsDeFabricacionCasera'] ?? '',
                'AF_Marca' => $_REQUEST['AF_Marca'] ?? '',
                'AF_Modelo' => $_REQUEST['AF_Modelo'] ?? '',
                'AF_Calibre' => $_REQUEST['AF_Calibre'] ?? '',
                'AF_NumeroDeSerie' => $_REQUEST['AF_NumeroDeSerie'] ?? ''
            ];
    
            $response = $this->dataFetcher->fetchDataQueryAF($params);
        
            if ($response !== null) {
                echo json_encode(['status' => 'success', 'data' => $response]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al obtener datos de las armas de fuego. Si el error persiste, contacte al administrador']);
            }
        }


        private function fetchDataQuerySecuestros() {
            $params = [
                'DC_Tipo' => $_REQUEST['DC_Tipo'] ?? '',
                'DC_Comentario' => $_REQUEST['DC_Comentario'] ?? ''
            ];
    
            $response = $this->dataFetcher->fetchDataQuerySecuestros($params);
        
            if ($response !== null) {
                echo json_encode(['status' => 'success', 'data' => $response]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al obtener datos de los mensajes extorsivos. Si el error persiste, contacte al administrador']);
            }
        }

        private function fetchDataQuery911() {
            // Crear conexión a la base de datos externa
            $external_conn = open_database_connection($GLOBALS['db_config']['external']);
            $dataFetcher = new DataFetcher($external_conn);
    
            // Obtener los parámetros de la solicitud
            $params = [
                'FechaDesde' => $_POST['FechaDesde'] ?? '',
                'FechaHasta' => $_POST['FechaHasta'] ?? '',
                'Nota' => $_POST['Nota'] ?? '',
                'Region' => $_POST['Region'] ?? '',
                'Localidad' => $_POST['Localidad'] ?? '',
                'Direccion' => $_POST['Direccion'] ?? '',
                'Tipificacion' => $_POST['Tipificacion'] ?? '',
                'Denunciados' => $_POST['Denunciados'] ?? '',
                'Relato' => $_POST['Relato'] ?? ''
            ];
    
            // Llamar al método fetchDataQuery911 en DataFetcher
            $response = $dataFetcher->fetchDataQuery911($params);
    
            // Enviar la respuesta como JSON
            echo json_encode(['status' => 'success', 'data' => $response['pve911']]);
    
            // Cerrar la conexión a la base de datos externa
            $external_conn->close();
        }

        private function fetchDataQueryAUOP() {
            // Crear conexión a la base de datos externa
            $external_conn = open_database_connection($GLOBALS['db_config']['external']);
            $dataFetcher = new DataFetcher($external_conn);

            $params = [
                'FechaDesde' => $_POST['FechaDesde'] ?? '',
                'FechaHasta' => $_POST['FechaHasta'] ?? '',
                'Hora' => $_POST['Hora'] ?? '',
                'OtraDependencia' => $_POST['OtraDependencia'] ?? '',
                'DelitoAUOP' => $_POST['DelitoAUOP'] ?? '',
                'LugardelHecho' => $_POST['LugardelHecho'] ?? '',
                'Barrio' => $_POST['Barrio'] ?? '',
                'Victima' => $_POST['Victima'] ?? '',
                'Imputado' => $_POST['Imputado'] ?? '',
                'RelatoDelHecho' => $_POST['RelatoDelHecho'] ?? ''
            ];

            $response = $dataFetcher->fetchDataQueryAUOP($params);

            if ($response !== null) {
                echo json_encode(['status' => 'success', 'data' => $response]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al obtener los datos de la base de datos.']);
            }

            // Cerrar la conexión a la base de datos externa
            $external_conn->close();
        }

        public function __destruct() {
            $this->conn->close();
        }
    }

    // Configuración de la base de datos al constructor
    $ajaxEndpoint = new EndPoint_AJAX();
    $ajaxEndpoint->handleRequest();
?>
