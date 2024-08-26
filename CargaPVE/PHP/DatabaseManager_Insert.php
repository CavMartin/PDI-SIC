<?php
    // Clase encargada de las operaciones INSERT de la base de datos
    class DataBaseManager_Insert {
        private $conn;
    
        public function __construct($conn) {
            $this->conn = $conn;
        }

        //Metodo para determinar los tipos de datos a insertar
        private function selectDataType($params) {
            $types = '';
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';  // Tipo entero
                } elseif (is_float($param)) {
                    $types .= 'd';  // Tipo doble
                } elseif (is_string($param)) {
                    $types .= 's';  // Tipo string
                } else {
                    $types .= 'b';  // Tipo blob y otros
                }
            }
            return $types;
        }

        // Método para formatear un array como una cadena separada por comas y espacios
        private function formatArrayToString($array) {
            if (is_array($array)) {
                return implode(', ', $array);
            }
            return '';
        }

        // Método para iniciar un nuevo formulario
        public function insertNewForm($Formulario, $Numero, $Año) {
            $this->conn->begin_transaction();
            $stmt = null;
            try {
                $usernameID = $_SESSION['usernameID'];
        
                $stmt = $this->conn->prepare("INSERT INTO entidad_encabezado (Formulario, Numero, Año, UsuarioCreador) VALUES (?, ?, ?, ?)");
        
                $types = $this->selectDataType([$Formulario, $Numero, $Año, $usernameID]);
        
                $stmt->bind_param($types, $Formulario, $Numero, $Año, $usernameID);
        
                if (!$stmt->execute()) {
                    throw new Exception('Error al insertar o actualizar datos: ' . $this->conn->error);
                }
        
                $this->conn->commit();
                return json_encode(['status' => 'success', 'message' => 'Nuevo formulario generado con exito']);
            } catch (Exception $e) {
                $this->conn->rollback();
                return json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
            } finally {
                if ($stmt) {
                    $stmt->close();
                }
            }
        }

        // Método para iniciar un nuevo formulario exclusivo para la URII
        public function insertNewFormURII($Formulario, $Numero, $Año) {
            $this->conn->begin_transaction();
            $stmt = null;
            try {
                $usernameID = $_SESSION['usernameID'];
                $usergroup = $_SESSION['usergroup'];

                $stmt = $this->conn->prepare("INSERT INTO entidad_encabezado (Formulario, Numero, Año, Fuente, UsuarioCreador) VALUES (?, ?, ?, ?, ?)");
        
                $types = $this->selectDataType([$Formulario, $Numero, $Año, $usergroup, $usernameID]);
        
                $stmt->bind_param($types, $Formulario, $Numero, $Año, $usergroup, $usernameID);
        
                if (!$stmt->execute()) {
                    throw new Exception('Error al insertar o actualizar datos: ' . $this->conn->error);
                }
        
                $this->conn->commit();
                return json_encode(['status' => 'success', 'message' => 'Nuevo formulario generado con exito']);
            } catch (Exception $e) {
                $this->conn->rollback();
                return json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
            } finally {
                if ($stmt) {
                    $stmt->close();
                }
            }
        }

        // Método para insertar el formulario
        public function INSERT_Form($form_data) {
            // Iniciar la transacción
            $this->conn->begin_transaction();

            try {
                // Procesar los campos comunes desde $form_data
                $ClavePrimaria = $form_data['formularioPVE'];
                $Fecha = $form_data['Fecha'];
                $Hora = !empty($form_data['Hora']) ? $form_data['Hora'] : null; // null por defecto en Hora, en vez de 00:00
                $Fuente = $form_data['Fuente'];
                $ReporteAsociado = $form_data['ReporteAsociado'];
                $Tipologia = $this->formatArrayToString($form_data['Tipologia']);
                $ModalidadComisiva = $this->formatArrayToString($form_data['ModalidadComisiva']);
                $TipoEstupefaciente = $this->formatArrayToString($form_data['TipoEstupefaciente']);
                $Relevancia = $form_data['Relevancia'];
                $PosiblesUsurpaciones = $form_data['PosiblesUsurpaciones'];
                $ConnivenciaPolicial = $form_data['ConnivenciaPolicial'];
                $UsoAF = $form_data['UsoAF'];
                $ParticipacionDeMenores = $form_data['ParticipacionDeMenores'];
                $ParticipacionOrgCrim = $form_data['ParticipacionOrgCrim'];
                $Relato = $form_data['Relato'];
                $Valoracion = $form_data['Valoracion'];
                $usernameID = $_SESSION['usernameID'];

                // Comprobación para asignar $OrganizacionCriminal
                if (isset($form_data['OrganizacionCriminal']) && $form_data['OrganizacionCriminal'] === "OTRA") {
                    $OrganizacionCriminal = $form_data['OtraOrganizacionCriminal'] ?? '';
                } else {
                    $OrganizacionCriminal = $form_data['OrganizacionCriminal'] ?? '';
                }

                $stmt = $this->conn->prepare("INSERT INTO entidad_encabezado (Formulario, Fecha, Hora, Fuente, ReporteAsociado, Tipologia, ModalidadComisiva, TipoEstupefaciente, Relevancia, PosiblesUsurpaciones, ConnivenciaPolicial, UsoAF, ParticipacionDeMenores, ParticipacionOrgCrim, OrganizacionCriminal, Relato, Valoracion, UsuarioCreador)
                                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                                              ON DUPLICATE KEY UPDATE
                                              Fecha=VALUES(Fecha), Hora=VALUES(Hora), Fuente=VALUES(Fuente), ReporteAsociado=VALUES(ReporteAsociado), Tipologia=VALUES(Tipologia), ModalidadComisiva=VALUES(ModalidadComisiva), TipoEstupefaciente=VALUES(TipoEstupefaciente), Relevancia=VALUES(Relevancia), PosiblesUsurpaciones=VALUES(PosiblesUsurpaciones), ConnivenciaPolicial=VALUES(ConnivenciaPolicial), UsoAF=VALUES(UsoAF), ParticipacionDeMenores=VALUES(ParticipacionDeMenores), ParticipacionOrgCrim=VALUES(ParticipacionOrgCrim), OrganizacionCriminal=VALUES(OrganizacionCriminal), Relato=VALUES(Relato), Valoracion=VALUES(Valoracion)");

                $types = $this->selectDataType([$ClavePrimaria, $Fecha, $Hora, $Fuente, $ReporteAsociado, $Tipologia, $ModalidadComisiva, $TipoEstupefaciente, $Relevancia, $PosiblesUsurpaciones, $ConnivenciaPolicial, $UsoAF, $ParticipacionDeMenores, $ParticipacionOrgCrim, $OrganizacionCriminal, $Relato, $Valoracion, $usernameID]);

                $stmt->bind_param($types, $ClavePrimaria, $Fecha, $Hora, $Fuente, $ReporteAsociado, $Tipologia, $ModalidadComisiva, $TipoEstupefaciente, $Relevancia, $PosiblesUsurpaciones, $ConnivenciaPolicial, $UsoAF, $ParticipacionDeMenores, $ParticipacionOrgCrim, $OrganizacionCriminal, $Relato, $Valoracion, $usernameID);

                if (!$stmt->execute()) {
                    throw new Exception('Error al insertar o actualizar datos: ' . $this->conn->error);
                }

                // Cerrar la sentencia después de su ejecución
                $stmt->close();

                // Recopilar y procesar los datos de los domicilios
                $i = 1; // Iniciar contador de instancias
                while (isset($_POST["ID_Lugar$i"])) {
                    // Recolectar datos del domicilio actual
                    $ID_Lugar = $_POST["ID_Lugar$i"];
                    $L_NumeroDeOrden = $_POST["L_NumeroDeOrden$i"];

                    // Verificar si L_NumeroDeOrden es igual a 0. Para este caso "0" significa sin asignar
                    if ($L_NumeroDeOrden == 0) {
                        $stmt = $this->conn->prepare("SELECT MAX(NumeroDeOrden) AS max_orden FROM entidad_lugares WHERE FK_Encabezado = ?");
                        $stmt->bind_param("s", $ClavePrimaria);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $row = $result->fetch_assoc();

                        // Determinar el nuevo valor de L_NumeroDeOrden
                        $L_NumeroDeOrden = $row['max_orden'] ? $row['max_orden'] + 1 : 1;

                        // Rearmar la ID_Lugar
                        $ID_Lugar = $ClavePrimaria . "-L" . $L_NumeroDeOrden;

                        $stmt->close();
                    }

                    // Comprobación para asignar $L_Rol
                    if ($_POST["L_Rol$i"] === "Otra opción no listada") {
                        $L_Rol = $_POST["L_RolEspecifique$i"];
                    } else {
                        $L_Rol = $_POST["L_Rol$i"];
                    }

                    // Procesar los campos comunes
                    $L_Tipo = $_POST["L_Tipo$i"];
                    $L_SubTipo = $_POST["L_SubTipo$i"];
                    $L_Calle = $_POST["L_Calle$i"];
                    $L_AlturaCatastral = $_POST["L_AlturaCatastral$i"];
                    $L_CalleDetalle = $_POST["L_CalleDetalle$i"];
                    $L_Interseccion1 = $_POST["L_Interseccion1$i"];
                    $L_Interseccion2 = $_POST["L_Interseccion2$i"];
                    $L_Barrio = $_POST["L_Barrio$i"];
                    $L_Localidad = $_POST["L_Localidad$i"];
                    $L_Provincia = $_POST["L_Provincia$i"];
                    $L_Pais = $_POST["L_Pais$i"];
                    $L_Coordenadas = $_POST["L_Coordenadas$i"];

                    // Sentencia SQL preparada "INSERT ON DUPLICATE KEY UPDATE" para la carga de datos de la entidad "Lugares"
                    $stmt = $this->conn->prepare("INSERT INTO entidad_lugares (ID_Lugar, FK_Encabezado, NumeroDeOrden, L_Tipo, L_SubTipo, L_Rol, L_Calle, L_AlturaCatastral, L_CalleDetalle, L_Interseccion1, L_Interseccion2, L_Barrio, L_Localidad, L_Provincia, L_Pais, L_Coordenadas, L_UsuarioCreador) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE 
                    NumeroDeOrden = VALUES(NumeroDeOrden), L_Tipo = VALUES(L_Tipo), L_SubTipo = VALUES(L_SubTipo), L_Rol = VALUES(L_Rol), L_Calle = VALUES(L_Calle), L_AlturaCatastral = VALUES(L_AlturaCatastral), L_CalleDetalle = VALUES(L_CalleDetalle), L_Interseccion1 = VALUES(L_Interseccion1), L_Interseccion2 = VALUES(L_Interseccion2), L_Barrio = VALUES(L_Barrio), L_Localidad = VALUES(L_Localidad), L_Provincia = VALUES(L_Provincia), L_Pais = VALUES(L_Pais), L_Coordenadas = VALUES(L_Coordenadas), L_UsuarioCreador = VALUES(L_UsuarioCreador)");

                    $types = $this->selectDataType([$ID_Lugar, $ClavePrimaria, $L_NumeroDeOrden, $L_Tipo, $L_SubTipo, $L_Rol, $L_Calle, $L_AlturaCatastral, $L_CalleDetalle, $L_Interseccion1, $L_Interseccion2, $L_Barrio, $L_Localidad, $L_Provincia, $L_Pais, $L_Coordenadas, $usernameID]);

                    $stmt->bind_param($types, $ID_Lugar, $ClavePrimaria, $L_NumeroDeOrden, $L_Tipo, $L_SubTipo, $L_Rol, $L_Calle, $L_AlturaCatastral, $L_CalleDetalle, $L_Interseccion1, $L_Interseccion2, $L_Barrio, $L_Localidad, $L_Provincia, $L_Pais, $L_Coordenadas, $usernameID);

                    // Ejecutar la inserción del mensaje
                    if (!$stmt->execute()) {
                        throw new Exception('Error al insertar o actualizar datos: ' . $this->conn->error);
                    }

                    // Cerrar la sentencia después de su ejecución
                    $stmt->close();

                    $i++; // Incrementar el contador para buscar la siguiente instancia
                }

                // Procesar los datos de las personas
                $j = 1; // Iniciar contador de instancias
                while (isset($_POST["ID_Persona$j"])) {
                    // Recolectar datos de la persona actual
                    $ID_Persona = $_POST["ID_Persona$j"];
                    $P_NumeroDeOrden = $_POST["P_NumeroDeOrden$j"];

                    // Verificar si P_NumeroDeOrden es igual a 0. Para este caso "0" significa sin asignar
                    if ($P_NumeroDeOrden == 0) {
                        $stmt = $this->conn->prepare("SELECT MAX(NumeroDeOrden) AS max_orden FROM entidad_personas WHERE FK_Encabezado = ?");
                        $stmt->bind_param("s", $ClavePrimaria);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $row = $result->fetch_assoc();

                        // Determinar el nuevo valor de P_NumeroDeOrden
                        $P_NumeroDeOrden = $row['max_orden'] ? $row['max_orden'] + 1 : 1;

                        // Rearmar la ID_Persona
                        $ID_Persona = $ClavePrimaria . "-P" . $P_NumeroDeOrden;

                        $stmt->close();
                    }

                    // Comprobación para asignar $P_Rol
                    if ($_POST["P_Rol$j"] === "Otra opción no listada") {
                        $P_Rol = $_POST["P_RolEspecifique$j"];
                    } else {
                        $P_Rol = $_POST["P_Rol$j"];
                    }

                    // Procesar los campos comunes
                    $P_Nombre = $_POST["P_Nombre$j"];
                    $P_Apellido = $_POST["P_Apellido$j"];
                    $P_Alias = $_POST["P_Alias$j"];

                    // Sentencia SQL preparada "INSERT ON DUPLICATE KEY UPDATE" para la carga de datos de la entidad "Personas"
                    $stmt = $this->conn->prepare("INSERT INTO entidad_personas (ID_Persona, FK_Encabezado, NumeroDeOrden, P_Rol, P_Nombre, P_Apellido, P_Alias, P_UsuarioCreador) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE 
                    NumeroDeOrden = VALUES(NumeroDeOrden), P_Rol = VALUES(P_Rol), P_Nombre = VALUES(P_Nombre), P_Apellido = VALUES(P_Apellido), P_Alias = VALUES(P_Alias), P_UsuarioCreador = VALUES(P_UsuarioCreador)");

                    $types = $this->selectDataType([$ID_Persona, $ClavePrimaria, $P_NumeroDeOrden, $P_Rol, $P_Nombre, $P_Apellido, $P_Alias, $usernameID]);
                    $stmt->bind_param($types, $ID_Persona, $ClavePrimaria, $P_NumeroDeOrden, $P_Rol, $P_Nombre, $P_Apellido, $P_Alias, $usernameID);

                    // Ejecutar la inserción del mensaje
                    if (!$stmt->execute()) {
                        throw new Exception('Error al insertar o actualizar datos: ' . $this->conn->error);
                    }

                    // Cerrar la sentencia después de su ejecución
                    $stmt->close();

                    $j++; // Incrementar el contador para buscar la siguiente instancia
                }

                // Procesar los datos de los vehículos
                $k = 1; // Iniciar contador de instancias
                while (isset($_POST["ID_Vehiculo$k"])) {
                    // Recolectar datos del vehículo actual
                    $ID_Vehiculo = $_POST["ID_Vehiculo$k"];
                    $V_NumeroDeOrden = $_POST["V_NumeroDeOrden$k"];

                    // Verificar si V_NumeroDeOrden es igual a 0. Para este caso "0" significa sin asignar
                    if ($V_NumeroDeOrden == 0) {
                        $stmt = $this->conn->prepare("SELECT MAX(NumeroDeOrden) AS max_orden FROM entidad_vehiculos WHERE FK_Encabezado = ?");
                        $stmt->bind_param("s", $ClavePrimaria);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $row = $result->fetch_assoc();

                        // Determinar el nuevo valor de V_NumeroDeOrden
                        $V_NumeroDeOrden = $row['max_orden'] ? $row['max_orden'] + 1 : 1;
        
                        // Rearmar la ID_Vehiculo
                        $ID_Vehiculo = $ClavePrimaria . "-V" . $V_NumeroDeOrden;

                        $stmt->close();
                    }

                    // Comprobación para asignar $V_Rol
                    if ($_POST["V_Rol$k"] === "Otra opción no listada") {
                        $V_Rol = $_POST["V_RolEspecifique$k"];
                    } else {
                        $V_Rol = $_POST["V_Rol$k"];
                    }

                    // Procesar los campos comunes
                    $V_Tipo = $_POST["V_Tipo$k"];
                    $V_Color = $_POST["V_Color$k"];
                    $V_Marca = $_POST["V_Marca$k"];
                    $V_Modelo = $_POST["V_Modelo$k"];
                    $V_Dominio = $_POST["V_Dominio$k"];

                    // Sentencia SQL preparada "INSERT ON DUPLICATE KEY UPDATE" para la carga de datos de la entidad "Vehículos"
                    $stmt = $this->conn->prepare("INSERT INTO entidad_vehiculos (ID_Vehiculo, FK_Encabezado, NumeroDeOrden, V_Rol, V_Tipo, V_Color, V_Marca, V_Modelo, V_Dominio, V_UsuarioCreador) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE 
                    NumeroDeOrden = VALUES(NumeroDeOrden), V_Rol = VALUES(V_Rol), V_Tipo = VALUES(V_Tipo), V_Color = VALUES(V_Color), V_Marca = VALUES(V_Marca), V_Modelo = VALUES(V_Modelo), V_Dominio = VALUES(V_Dominio), V_UsuarioCreador = VALUES(V_UsuarioCreador)");

                    $types = $this->selectDataType([$ID_Vehiculo, $ClavePrimaria, $V_NumeroDeOrden, $V_Rol, $V_Tipo, $V_Color, $V_Marca, $V_Modelo, $V_Dominio, $usernameID]);
                    $stmt->bind_param($types, $ID_Vehiculo, $ClavePrimaria, $V_NumeroDeOrden, $V_Rol, $V_Tipo, $V_Color, $V_Marca, $V_Modelo, $V_Dominio, $usernameID);

                    // Ejecutar la inserción del mensaje
                    if (!$stmt->execute()) {
                        throw new Exception('Error al insertar o actualizar datos: ' . $this->conn->error);
                    }

                    // Cerrar la sentencia después de su ejecución
                    $stmt->close();

                    $k++; // Incrementar el contador para buscar la siguiente instancia
                }

                // Si todo fue exitoso, confirmar la transacción y devolver respuesta de éxito
                $this->conn->commit();
                return json_encode(['status' => 'success', 'message' => 'Formulario y entidades guardados con éxito']);
        
            } catch (Exception $e) {
                // En caso de error, revertir la transacción y devolver respuesta de error
                $this->conn->rollback();
                return json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
            }
        }

    }

?>
