<?php
    // Clase encargada de las operaciones INSERT de la base de datos
    class DataBaseManager_Insert {
        private $conn;
    
        public function __construct($conn) {
            $this->conn = $conn;
        }

        //Metodo para determinar los tipos de datos a insertar
        private function determinar_tipo_de_dato($params) {
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

        // Método para realizar un INSERT/UPDATE al encabezado
        public function INSERT_Encabezado($form_data) {
            // Iniciar la transacción
            $this->conn->begin_transaction();
        
            try {

                // Procesar los campos comunes desde $form_data
                $ID = $form_data['ID'];
                $Fecha = $form_data['Fecha'];
                $Tipo = $form_data['Tipo'];
                $Juzgado = $form_data['Juzgado'];
                $Dependencia = $form_data['Dependencia'];
                $Causa = $form_data['Causa'];
                $Relato = $form_data['Relato'];
                $usernameID = $_SESSION['usernameID'];

                $stmt = $this->conn->prepare("INSERT INTO entidad_encabezado (ID, Fecha, Tipo, Juzgado, Dependencia, Causa, Relato, UsuarioCreador)
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                                    ON DUPLICATE KEY UPDATE
                                    Fecha=VALUES(Fecha), Tipo=VALUES(Tipo), Juzgado=VALUES(Juzgado), Dependencia=VALUES(Dependencia), Causa=VALUES(Causa), Relato=VALUES(Relato)");

                $types = $this->determinar_tipo_de_dato([$ID, $Fecha, $Tipo, $Juzgado, $Dependencia, $Causa, $Relato, $usernameID]);

                $stmt->bind_param($types, $ID, $Fecha, $Tipo, $Juzgado, $Dependencia, $Causa, $Relato, $usernameID);

                if (!$stmt->execute()) {
                    throw new Exception('Error al insertar o actualizar datos: ' . $this->conn->error);
                }

                // Cerrar la sentencia después de su ejecución
                $stmt->close();

                // Confirmar la transacción y devolver respuesta de éxito
                $this->conn->commit();
                return json_encode(['status' => 'success', 'message' => 'Cambios guardados con éxito']);

            } catch (Exception $e) {
                // Revertir la transacción y devolver respuesta de error
                $this->conn->rollback();
                return json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
            }
        }

        // Método para realizar la inserción de datos a la entidad "Personas"
        public function INSERT_Persona($form_data) {
            // Iniciar la transacción
            $this->conn->begin_transaction();

            try {
                // Procesar los campos que forman la PK desde $form_data
                $P_ClavePrimaria = $form_data['ClavePrimaria'];
                $FK_Encabezado = $form_data['ID'];
                $P_NumeroDeOrden = $form_data['NumeroDeOrden'];

                // Verificar si P_NumeroDeOrden es igual a 0. Para este caso "0" significa sin asignar
                if ($P_NumeroDeOrden == 0) {
                    $stmt = $this->conn->prepare("SELECT MAX(NumeroDeOrden) AS max_orden FROM entidad_personas WHERE FK_Encabezado = ?");
                    $stmt->bind_param("s", $FK_Encabezado);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $row = $result->fetch_assoc();

                    // Determinar el nuevo valor de P_NumeroDeOrden
                    $P_NumeroDeOrden = $row['max_orden'] ? $row['max_orden'] + 1 : 1;

                    // Rearmar la P_ClavePrimaria
                    $P_ClavePrimaria = $FK_Encabezado . "-P" . $P_NumeroDeOrden;

                    $stmt->close();
                }

                // Comprobación para asignar $P_Rol
                if ($_POST['P_Rol'] === "Otra opción no listada") {
                    $P_Rol = $_POST['P_RolEspecifique'];
                    } else {
                    $P_Rol = $_POST['P_Rol'];
                }

                // Procesar los campos comunes desde $form_data
                $P_FotoPersona = $form_data['DataURL_P_FotoPersona'];
                $P_Apellido = $form_data['P_Apellido'];
                $P_Nombre = $form_data['P_Nombre'];
                $P_Alias = $form_data['P_Alias'];
                $P_DNI = $form_data['P_DNI'];
                $P_Edad = $form_data['P_Edad'];
                $P_Genero = $form_data['P_Genero'];
                $P_EstadoCivil = $form_data['P_EstadoCivil'];
                $P_Pais = $form_data['P_Pais'];
                $usernameID = $_SESSION['usernameID'];

                // Sentencia SQL preparada "INSERT ON DUPLICATE KEY UPDATE" para la carga de datos de la entidad "Personas"
                $stmt = $this->conn->prepare("INSERT INTO entidad_personas (ID_Persona, FK_Encabezado, NumeroDeOrden, P_FotoPersona, P_Rol, P_Apellido, P_Nombre, P_Alias, P_Edad, P_Genero, P_DNI, P_EstadoCivil, P_Pais, P_UsuarioCreador) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                NumeroDeOrden = VALUES(NumeroDeOrden), P_FotoPersona = VALUES(P_FotoPersona), P_Rol = VALUES(P_Rol), P_Apellido = VALUES(P_Apellido), P_Nombre = VALUES(P_Nombre), P_Alias = VALUES(P_Alias), P_Edad = VALUES(P_Edad), P_Genero = VALUES(P_Genero), P_DNI = VALUES(P_DNI), P_EstadoCivil = VALUES(P_EstadoCivil), P_Pais = VALUES(P_Pais), P_UsuarioCreador = VALUES(P_UsuarioCreador)");

                $types = $this->determinar_tipo_de_dato([$P_ClavePrimaria, $FK_Encabezado, $P_NumeroDeOrden, $P_FotoPersona, $P_Rol, $P_Apellido, $P_Nombre, $P_Alias, $P_Edad, $P_Genero, $P_DNI, $P_EstadoCivil, $P_Pais, $usernameID]);

                $stmt->bind_param($types, $P_ClavePrimaria, $FK_Encabezado, $P_NumeroDeOrden, $P_FotoPersona, $P_Rol, $P_Apellido, $P_Nombre, $P_Alias, $P_Edad, $P_Genero, $P_DNI, $P_EstadoCivil, $P_Pais, $usernameID);

                // Ejecutar la inserción de datos de la persona
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

                        // Verificar si P_NumeroDeOrden es igual a 0. Para este caso "0" significa sin asignar
                        if ($L_NumeroDeOrden == 0) {
                            $stmt = $this->conn->prepare("SELECT MAX(NumeroDeOrden) AS max_orden FROM entidad_lugares WHERE FK_Persona = ?");
                            $stmt->bind_param("s", $P_ClavePrimaria);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $row = $result->fetch_assoc();
                        
                            // Determinar el nuevo valor de L_NumeroDeOrden
                            $L_NumeroDeOrden = $row['max_orden'] ? $row['max_orden'] + 1 : 1;
                        
                            // Rearmar la ID_Lugar
                            $ID_Lugar = $P_ClavePrimaria . "-L" . $L_NumeroDeOrden;
                        
                            $stmt->close();
                        }

                        // Comprobación para asignar $L_Rol
                        if ($_POST["L_Rol$i"] === "Otra opción no listada") {
                            $L_Rol = $_POST["L_RolEspecifique$i"];
                        } else {
                            $L_Rol = $_POST["L_Rol$i"];
                        }

                        // Procesar los campos comunes
                        $L_Calle = $_POST["L_Calle$i"];
                        $L_AlturaCatastral = $_POST["L_AlturaCatastral$i"];
                        $L_CalleDetalle = $_POST["L_CalleDetalle$i"];
                        $L_Barrio = $_POST["L_Barrio$i"];
                        $L_Localidad = $_POST["L_Localidad$i"];
                        $L_Provincia = $_POST["L_Provincia$i"];
                        $L_Pais = $_POST["L_Pais$i"];
                        $L_Coordenadas = $_POST["L_Coordenadas$i"];

                        // Sentencia SQL preparada "INSERT ON DUPLICATE KEY UPDATE" para la carga de datos de la entidad "Personas"
                        $stmt = $this->conn->prepare("INSERT INTO entidad_lugares (ID_Lugar, FK_Persona, NumeroDeOrden, L_Rol, L_Calle, L_AlturaCatastral, L_CalleDetalle, L_Barrio, L_Localidad, L_Provincia, L_Pais, L_Coordenadas, L_UsuarioCreador) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE 
                        NumeroDeOrden = VALUES(NumeroDeOrden), L_Rol = VALUES(L_Rol), L_Calle = VALUES(L_Calle), L_AlturaCatastral = VALUES(L_AlturaCatastral), L_CalleDetalle = VALUES(L_CalleDetalle), L_Barrio = VALUES(L_Barrio), L_Localidad = VALUES(L_Localidad), L_Provincia = VALUES(L_Provincia), L_Pais = VALUES(L_Pais), L_Coordenadas = VALUES(L_Coordenadas), L_UsuarioCreador = VALUES(L_UsuarioCreador)");

                        $types = $this->determinar_tipo_de_dato([$ID_Lugar, $P_ClavePrimaria, $L_NumeroDeOrden, $L_Rol, $L_Calle, $L_AlturaCatastral, $L_CalleDetalle, $L_Barrio, $L_Localidad, $L_Provincia, $L_Pais, $L_Coordenadas, $usernameID]);

                        $stmt->bind_param($types, $ID_Lugar, $P_ClavePrimaria, $L_NumeroDeOrden, $L_Rol, $L_Calle, $L_AlturaCatastral, $L_CalleDetalle, $L_Barrio, $L_Localidad, $L_Provincia, $L_Pais, $L_Coordenadas, $usernameID);

                        // Ejecutar la inserción del mensaje
                        if (!$stmt->execute()) {
                            throw new Exception('Error al insertar o actualizar datos: ' . $this->conn->error);
                        }

                        // Cerrar la sentencia después de su ejecución
                        $stmt->close();

                        $i++; // Incrementar el contador para buscar la siguiente instancia
                    }

                    // Recopilar y procesar los datos de los datos compplementarios
                    $DC = 1; // Iniciar contador de instancias de los datos compplementarios
                    while (isset($_POST["ID_DatoComplementario$DC"])) {
                        // Recolectar datos del dato compplementario actual
                        $ID_DatoComplementario = $_POST["ID_DatoComplementario$DC"];
                        $DC_NumeroDeOrden = $_POST["DC_NumeroDeOrden$DC"];

                        // Verificar si P_NumeroDeOrden es igual a 0. Para este caso "0" significa sin asignar
                        if ($DC_NumeroDeOrden == 0) {
                            $stmt = $this->conn->prepare("SELECT MAX(NumeroDeOrden) AS max_orden FROM entidad_datos_complementarios WHERE FK_Persona = ?");
                            $stmt->bind_param("s", $P_ClavePrimaria);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $row = $result->fetch_assoc();

                            // Determinar el nuevo valor de DC_NumeroDeOrden
                            $DC_NumeroDeOrden = $row['max_orden'] ? $row['max_orden'] + 1 : 1;

                            // Rearmar la ID_Lugar
                            $ID_DatoComplementario = $P_ClavePrimaria . "-DC" . $DC_NumeroDeOrden;

                            $stmt->close();
                        }

                        // Comprobación para asignar $DC_Tipo
                        if ($_POST["DC_Tipo$DC"] === "Otra opción no listada") {
                            $DC_Tipo = $_POST["DC_TipoEspecifique$DC"];
                        } else {
                            $DC_Tipo = $_POST["DC_Tipo$DC"];
                        }

                        // Procesar los campos comunes
                        $DC_ImagenAdjunta = $_POST["DataURL_DC_ImagenAdjunta$DC"];
                        $DC_Comentario = $_POST["DC_Comentario$DC"];

                        // Sentencia SQL preparada "INSERT ON DUPLICATE KEY UPDATE" para la carga de datos de la entidad "Personas"
                        $stmt = $this->conn->prepare("INSERT INTO entidad_datos_complementarios (ID_DatoComplementario, FK_Persona, NumeroDeOrden, DC_Tipo, DC_ImagenAdjunta, DC_Comentario, DC_UsuarioCreador) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE 
                        NumeroDeOrden = VALUES(NumeroDeOrden), DC_Tipo = VALUES(DC_Tipo), DC_ImagenAdjunta = VALUES(DC_ImagenAdjunta), DC_Comentario = VALUES(DC_Comentario), DC_UsuarioCreador = VALUES(DC_UsuarioCreador)");

                        $types = $this->determinar_tipo_de_dato([$ID_DatoComplementario, $P_ClavePrimaria, $DC_NumeroDeOrden, $DC_Tipo, $DC_ImagenAdjunta, $DC_Comentario, $usernameID]);

                        $stmt->bind_param($types, $ID_DatoComplementario, $P_ClavePrimaria, $DC_NumeroDeOrden, $DC_Tipo, $DC_ImagenAdjunta, $DC_Comentario, $usernameID);

                        // Ejecutar la inserción del mensaje
                        if (!$stmt->execute()) {
                            throw new Exception('Error al insertar o actualizar datos: ' . $this->conn->error);
                        }

                        // Cerrar la sentencia después de su ejecución
                        $stmt->close();

                        $DC++; // Incrementar el contador para buscar la siguiente instancia
                    }

                    // Si todo fue exitoso, confirmar la transacción y devolver respuesta de éxito
                    $this->conn->commit();
                    return json_encode(['status' => 'success', 'message' => 'Persona y domicilios guardados con éxito']);

                } catch (Exception $e) {
                    // En caso de error, revertir la transacción y devolver respuesta de error
                    $this->conn->rollback();
                    return json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
            }
        }

        // Método para realizar la inserción de datos a la entidad "Lugares"
        public function INSERT_Lugar($form_data) {
            // Iniciar la transacción
            $this->conn->begin_transaction();

            try {
                // Procesar los campos que forman la PK desde $form_data
                $L_ClavePrimaria = $form_data['ClavePrimaria'];
                $FK_Encabezado = $form_data['ID'];
                $L_NumeroDeOrden = $form_data['NumeroDeOrden'];

                // Verificar si L_NumeroDeOrden es igual a 0. Para este caso "0" significa sin asignar
                if ($L_NumeroDeOrden == 0) {
                    $stmt = $this->conn->prepare("SELECT MAX(NumeroDeOrden) AS max_orden FROM entidad_lugares WHERE FK_Encabezado = ?");
                    $stmt->bind_param("s", $FK_Encabezado);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $row = $result->fetch_assoc();

                    // Determinar el nuevo valor de L_NumeroDeOrden
                    $L_NumeroDeOrden = $row['max_orden'] ? $row['max_orden'] + 1 : 1;

                    // Rearmar la L_ClavePrimaria
                    $L_ClavePrimaria = $FK_Encabezado . "-L" . $L_NumeroDeOrden;

                    $stmt->close();
                }

                // Comprobación para asignar $L_Rol
                if ($_POST['L_Rol'] === "Otra opción no listada") {
                    $L_Rol = $_POST['L_RolEspecifique'];
                    } else {
                    $L_Rol = $_POST['L_Rol'];
                }

                // Procesar los campos comunes desde $form_data
                $L_TipoLugar = $form_data['L_TipoLugar'];
                $L_NombreLugarEspecifico = $_POST["L_NombreLugarEspecifico"];
                $L_Calle = $form_data['L_Calle'];
                $L_AlturaCatastral = $form_data['L_AlturaCatastral'];
                $L_CalleDetalle = $form_data['L_CalleDetalle'];
                $L_Interseccion1 = $_POST["L_Interseccion1"];
                $L_Interseccion2 = $_POST["L_Interseccion2"];
                $L_Barrio = $form_data['L_Barrio'];
                $L_Localidad = $form_data['L_Localidad'];
                $L_Provincia = $form_data['L_Provincia'];
                $L_Pais = $form_data['L_Pais'];
                $L_Coordenadas = $form_data['L_Coordenadas'];
                $usernameID = $_SESSION['usernameID'];

                // Sentencia SQL preparada "INSERT ON DUPLICATE KEY UPDATE" para la carga de datos de la entidad "Lugares"
                $stmt = $this->conn->prepare("INSERT INTO entidad_lugares (ID_Lugar, FK_Encabezado, NumeroDeOrden, L_TipoLugar, L_Rol, L_NombreLugarEspecifico, L_Calle, L_AlturaCatastral, L_CalleDetalle, L_Interseccion1, L_Interseccion2, L_Barrio, L_Localidad, L_Provincia, L_Pais, L_Coordenadas, L_UsuarioCreador) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                NumeroDeOrden = VALUES(NumeroDeOrden), L_TipoLugar = VALUES(L_TipoLugar), L_Rol = VALUES(L_Rol), L_NombreLugarEspecifico = VALUES(L_NombreLugarEspecifico), L_Calle = VALUES(L_Calle), L_AlturaCatastral = VALUES(L_AlturaCatastral), L_CalleDetalle = VALUES(L_CalleDetalle), L_Interseccion1 = VALUES(L_Interseccion1), L_Interseccion2 = VALUES(L_Interseccion2), L_Barrio = VALUES(L_Barrio), L_Localidad = VALUES(L_Localidad), L_Provincia = VALUES(L_Provincia), L_Pais = VALUES(L_Pais), L_Coordenadas = VALUES(L_Coordenadas), L_UsuarioCreador = VALUES(L_UsuarioCreador)");

                $types = $this->determinar_tipo_de_dato([$L_ClavePrimaria, $FK_Encabezado, $L_NumeroDeOrden, $L_TipoLugar, $L_Rol, $L_NombreLugarEspecifico, $L_Calle, $L_AlturaCatastral, $L_CalleDetalle, $L_Interseccion1, $L_Interseccion2, $L_Barrio, $L_Localidad, $L_Provincia, $L_Pais, $L_Coordenadas, $usernameID]);

                $stmt->bind_param($types, $L_ClavePrimaria, $FK_Encabezado, $L_NumeroDeOrden, $L_TipoLugar, $L_Rol, $L_NombreLugarEspecifico, $L_Calle, $L_AlturaCatastral, $L_CalleDetalle, $L_Interseccion1, $L_Interseccion2, $L_Barrio, $L_Localidad, $L_Provincia, $L_Pais, $L_Coordenadas, $usernameID);

                // Ejecutar la inserción de datos del lugar
                if (!$stmt->execute()) {
                    throw new Exception('Error al insertar o actualizar datos: ' . $this->conn->error);
                }

                // Cerrar la sentencia después de su ejecución
                $stmt->close();
            
                    // Recopilar y procesar los datos de los datos compplementarios
                    $DC = 1; // Iniciar contador de instancias de los datos compplementarios
                    while (isset($_POST["ID_DatoComplementario$DC"])) {
                        // Recolectar datos del dato compplementario actual
                        $ID_DatoComplementario = $_POST["ID_DatoComplementario$DC"];
                        $DC_NumeroDeOrden = $_POST["DC_NumeroDeOrden$DC"];

                        // Verificar si L_NumeroDeOrden es igual a 0. Para este caso "0" significa sin asignar
                        if ($DC_NumeroDeOrden == 0) {
                            $stmt = $this->conn->prepare("SELECT MAX(NumeroDeOrden) AS max_orden FROM entidad_datos_complementarios WHERE FK_Lugar = ?");
                            $stmt->bind_param("s", $L_ClavePrimaria);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $row = $result->fetch_assoc();

                            // Determinar el nuevo valor de DC_NumeroDeOrden
                            $DC_NumeroDeOrden = $row['max_orden'] ? $row['max_orden'] + 1 : 1;

                            // Rearmar la ID_Lugar
                            $ID_DatoComplementario = $L_ClavePrimaria . "-DC" . $DC_NumeroDeOrden;

                            $stmt->close();
                        }

                        // Comprobación para asignar $DC_Tipo
                        if ($_POST["DC_Tipo$DC"] === "Otra opción no listada") {
                            $DC_Tipo = $_POST["DC_TipoEspecifique$DC"];
                        } else {
                            $DC_Tipo = $_POST["DC_Tipo$DC"];
                        }

                        // Procesar los campos comunes
                        $DC_ImagenAdjunta = $_POST["DataURL_DC_ImagenAdjunta$DC"];
                        $DC_Comentario = $_POST["DC_Comentario$DC"];

                        // Sentencia SQL preparada "INSERT ON DUPLICATE KEY UPDATE" para la carga de datos de la entidad "Personas"
                        $stmt = $this->conn->prepare("INSERT INTO entidad_datos_complementarios (ID_DatoComplementario, FK_Lugar, NumeroDeOrden, DC_Tipo, DC_ImagenAdjunta, DC_Comentario, DC_UsuarioCreador) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE 
                        NumeroDeOrden = VALUES(NumeroDeOrden), DC_Tipo = VALUES(DC_Tipo), DC_ImagenAdjunta = VALUES(DC_ImagenAdjunta), DC_Comentario = VALUES(DC_Comentario), DC_UsuarioCreador = VALUES(DC_UsuarioCreador)");

                        $types = $this->determinar_tipo_de_dato([$ID_DatoComplementario, $L_ClavePrimaria, $DC_NumeroDeOrden, $DC_Tipo, $DC_ImagenAdjunta, $DC_Comentario, $usernameID]);

                        $stmt->bind_param($types, $ID_DatoComplementario, $L_ClavePrimaria, $DC_NumeroDeOrden, $DC_Tipo, $DC_ImagenAdjunta, $DC_Comentario, $usernameID);

                        // Ejecutar la inserción del mensaje
                        if (!$stmt->execute()) {
                            throw new Exception('Error al insertar o actualizar datos: ' . $this->conn->error);
                        }

                        // Cerrar la sentencia después de su ejecución
                        $stmt->close();

                        $DC++; // Incrementar el contador para buscar la siguiente instancia
                    }

                    // Si todo fue exitoso, confirmar la transacción y devolver respuesta de éxito
                    $this->conn->commit();
                    return json_encode(['status' => 'success', 'message' => 'Persona y domicilios guardados con éxito']);

                } catch (Exception $e) {
                    // En caso de error, revertir la transacción y devolver respuesta de error
                    $this->conn->rollback();
                    return json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
            }
        }

        // Método para realizar la inserción de datos a la entidad "Vehiculos"
        public function INSERT_Vehiculo($form_data) {
            // Iniciar la transacción
            $this->conn->begin_transaction();

            try {
                // Procesar los campos que forman la PK desde $form_data
                $V_ClavePrimaria = $form_data['ClavePrimaria'];
                $FK_Encabezado = $form_data['ID'];
                $V_NumeroDeOrden = $form_data['NumeroDeOrden'];

                // Verificar si V_NumeroDeOrden es igual a 0. Para este caso "0" significa sin asignar
                if ($V_NumeroDeOrden == 0) {
                    $stmt = $this->conn->prepare("SELECT MAX(NumeroDeOrden) AS max_orden FROM entidad_vehiculos WHERE FK_Encabezado = ?");
                    $stmt->bind_param("s", $FK_Encabezado);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $row = $result->fetch_assoc();

                    // Determinar el nuevo valor de V_NumeroDeOrden
                    $V_NumeroDeOrden = $row['max_orden'] ? $row['max_orden'] + 1 : 1;

                    // Rearmar la V_ClavePrimaria
                    $V_ClavePrimaria = $FK_Encabezado . "-V" . $V_NumeroDeOrden;

                    $stmt->close();
                }

                // Comprobación para asignar $V_Rol
                if ($_POST['V_Rol'] === "Otra opción no listada") {
                    $V_Rol = $_POST['V_RolEspecifique'];
                    } else {
                    $V_Rol = $_POST['V_Rol'];
                }

                // Procesar los campos comunes desde $form_data
                $V_TipoVehiculo = $form_data['V_TipoVehiculo'];
                $V_Color = $form_data['V_Color'];
                $V_Marca = $form_data['V_Marca'];
                $V_Modelo = $form_data['V_Modelo'];
                $V_Año = $form_data['V_Año'];
                $V_Dominio = $form_data['V_Dominio'];
                $V_NumeroChasis = $form_data['V_NumeroChasis'];
                $V_NumeroMotor = $form_data['V_NumeroMotor'];
                $usernameID = $_SESSION['usernameID'];

                // Sentencia SQL preparada "INSERT ON DUPLICATE KEY UPDATE" para la carga de datos de la entidad "Vehiculos"
                $stmt = $this->conn->prepare("INSERT INTO entidad_vehiculos (ID_Vehiculo, FK_Encabezado, NumeroDeOrden, V_TipoVehiculo, V_Rol, V_Color, V_Marca, V_Modelo, V_Año, V_Dominio, V_NumeroChasis, V_NumeroMotor, V_UsuarioCreador) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                NumeroDeOrden = VALUES(NumeroDeOrden), V_TipoVehiculo = VALUES(V_TipoVehiculo), V_Rol = VALUES(V_Rol), V_Color = VALUES(V_Color), V_Marca = VALUES(V_Marca), V_Modelo = VALUES(V_Modelo), V_Año = VALUES(V_Año), V_Dominio = VALUES(V_Dominio), V_NumeroChasis = VALUES(V_NumeroChasis), V_NumeroMotor = VALUES(V_NumeroMotor), V_UsuarioCreador = VALUES(V_UsuarioCreador)");

                $types = $this->determinar_tipo_de_dato([$V_ClavePrimaria, $FK_Encabezado, $V_NumeroDeOrden, $V_TipoVehiculo, $V_Rol, $V_Color, $V_Marca, $V_Modelo, $V_Año, $V_Dominio, $V_NumeroChasis, $V_NumeroMotor, $usernameID]);

                $stmt->bind_param($types, $V_ClavePrimaria, $FK_Encabezado, $V_NumeroDeOrden, $V_TipoVehiculo, $V_Rol, $V_Color, $V_Marca, $V_Modelo, $V_Año, $V_Dominio, $V_NumeroChasis, $V_NumeroMotor, $usernameID);

                // Ejecutar la inserción de datos del Vehiculo
                if (!$stmt->execute()) {
                    throw new Exception('Error al insertar o actualizar datos: ' . $this->conn->error);
                }

                // Cerrar la sentencia después de su ejecución
                $stmt->close();
            
                    // Recopilar y procesar los datos de los datos compplementarios
                    $DC = 1; // Iniciar contador de instancias de los datos compplementarios
                    while (isset($_POST["ID_DatoComplementario$DC"])) {
                        // Recolectar datos del dato compplementario actual
                        $ID_DatoComplementario = $_POST["ID_DatoComplementario$DC"];
                        $DC_NumeroDeOrden = $_POST["DC_NumeroDeOrden$DC"];

                        // Verificar si V_NumeroDeOrden es igual a 0. Para este caso "0" significa sin asignar
                        if ($DC_NumeroDeOrden == 0) {
                            $stmt = $this->conn->prepare("SELECT MAX(NumeroDeOrden) AS max_orden FROM entidad_datos_complementarios WHERE FK_Vehiculo = ?");
                            $stmt->bind_param("s", $V_ClavePrimaria);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $row = $result->fetch_assoc();

                            // Determinar el nuevo valor de DC_NumeroDeOrden
                            $DC_NumeroDeOrden = $row['max_orden'] ? $row['max_orden'] + 1 : 1;

                            // Rearmar la ID_Lugar
                            $ID_DatoComplementario = $V_ClavePrimaria . "-DC" . $DC_NumeroDeOrden;

                            $stmt->close();
                        }

                        // Comprobación para asignar $DC_Tipo
                        if ($_POST["DC_Tipo$DC"] === "Otra opción no listada") {
                            $DC_Tipo = $_POST["DC_TipoEspecifique$DC"];
                        } else {
                            $DC_Tipo = $_POST["DC_Tipo$DC"];
                        }

                        // Procesar los campos comunes
                        $DC_ImagenAdjunta = $_POST["DataURL_DC_ImagenAdjunta$DC"];
                        $DC_Comentario = $_POST["DC_Comentario$DC"];

                        // Sentencia SQL preparada "INSERT ON DUPLICATE KEY UPDATE" para la carga de datos de la entidad "Personas"
                        $stmt = $this->conn->prepare("INSERT INTO entidad_datos_complementarios (ID_DatoComplementario, FK_Vehiculo, NumeroDeOrden, DC_Tipo, DC_ImagenAdjunta, DC_Comentario, DC_UsuarioCreador) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE 
                        NumeroDeOrden = VALUES(NumeroDeOrden), DC_Tipo = VALUES(DC_Tipo), DC_ImagenAdjunta = VALUES(DC_ImagenAdjunta), DC_Comentario = VALUES(DC_Comentario), DC_UsuarioCreador = VALUES(DC_UsuarioCreador)");

                        $types = $this->determinar_tipo_de_dato([$ID_DatoComplementario, $V_ClavePrimaria, $DC_NumeroDeOrden, $DC_Tipo, $DC_ImagenAdjunta, $DC_Comentario, $usernameID]);

                        $stmt->bind_param($types, $ID_DatoComplementario, $V_ClavePrimaria, $DC_NumeroDeOrden, $DC_Tipo, $DC_ImagenAdjunta, $DC_Comentario, $usernameID);

                        // Ejecutar la inserción del mensaje
                        if (!$stmt->execute()) {
                            throw new Exception('Error al insertar o actualizar datos: ' . $this->conn->error);
                        }

                        // Cerrar la sentencia después de su ejecución
                        $stmt->close();

                        $DC++; // Incrementar el contador para buscar la siguiente instancia
                    }

                    // Si todo fue exitoso, confirmar la transacción y devolver respuesta de éxito
                    $this->conn->commit();
                    return json_encode(['status' => 'success', 'message' => 'Persona y domicilios guardados con éxito']);

                } catch (Exception $e) {
                    // En caso de error, revertir la transacción y devolver respuesta de error
                    $this->conn->rollback();
                    return json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
            }
        }

        // Método para realizar la inserción de datos a la entidad "Armas de fuego"
        public function INSERT_Arma($form_data) {
            // Iniciar la transacción
            $this->conn->begin_transaction();

            try {
                // Procesar los campos que forman la PK desde $form_data
                $AF_ClavePrimaria = $form_data['ClavePrimaria'];
                $FK_Encabezado = $form_data['ID'];
                $AF_NumeroDeOrden = $form_data['NumeroDeOrden'];

                // Verificar si AF_NumeroDeOrden es igual a 0. Para este caso "0" significa sin asignar
                if ($AF_NumeroDeOrden == 0) {
                    $stmt = $this->conn->prepare("SELECT MAX(NumeroDeOrden) AS max_orden FROM entidad_armas WHERE FK_Encabezado = ?");
                    $stmt->bind_param("s", $FK_Encabezado);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $row = $result->fetch_assoc();

                    // Determinar el nuevo valor de AF_NumeroDeOrden
                    $AF_NumeroDeOrden = $row['max_orden'] ? $row['max_orden'] + 1 : 1;

                    // Rearmar la AF_ClavePrimaria
                    $AF_ClavePrimaria = $FK_Encabezado . "-AF" . $AF_NumeroDeOrden;

                    $stmt->close();
                }

                // Procesar los campos comunes desde $form_data
                $AF_EsDeFabricacionCasera = $form_data['AF_EsDeFabricacionCasera'];
                $AF_TipoAF = $form_data['AF_TipoAF'];
                $AF_Marca = $form_data['AF_Marca'];
                $AF_Modelo = $form_data['AF_Modelo'];
                $AF_Calibre = $form_data['AF_Calibre'];
                $AF_PoseeNumeracionVisible = $form_data['AF_PoseeNumeracionVisible'];
                $AF_NumeroDeSerie = $form_data['AF_NumeroDeSerie'];
                $usernameID = $_SESSION['usernameID'];

                // Sentencia SQL preparada "INSERT ON DUPLICATE KEY UPDATE" para la carga de datos de la entidad "Armas"
                $stmt = $this->conn->prepare("INSERT INTO entidad_armas (ID_Arma, FK_Encabezado, NumeroDeOrden, AF_EsDeFabricacionCasera, AF_TipoAF, AF_Marca, AF_Modelo, AF_Calibre, AF_PoseeNumeracionVisible, AF_NumeroDeSerie, AF_UsuarioCreador) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                NumeroDeOrden = VALUES(NumeroDeOrden), AF_EsDeFabricacionCasera = VALUES(AF_EsDeFabricacionCasera), AF_TipoAF = VALUES(AF_TipoAF), AF_Marca = VALUES(AF_Marca), AF_Modelo = VALUES(AF_Modelo), AF_Calibre = VALUES(AF_Calibre), AF_PoseeNumeracionVisible = VALUES(AF_PoseeNumeracionVisible), AF_NumeroDeSerie = VALUES(AF_NumeroDeSerie), AF_UsuarioCreador = VALUES(AF_UsuarioCreador)");

                $types = $this->determinar_tipo_de_dato([$AF_ClavePrimaria, $FK_Encabezado, $AF_NumeroDeOrden, $AF_EsDeFabricacionCasera, $AF_TipoAF, $AF_Marca, $AF_Modelo, $AF_Calibre, $AF_PoseeNumeracionVisible, $AF_NumeroDeSerie, $usernameID]);

                $stmt->bind_param($types, $AF_ClavePrimaria, $FK_Encabezado, $AF_NumeroDeOrden, $AF_EsDeFabricacionCasera, $AF_TipoAF, $AF_Marca, $AF_Modelo, $AF_Calibre, $AF_PoseeNumeracionVisible, $AF_NumeroDeSerie, $usernameID);

                // Ejecutar la inserción de datos del arma
                if (!$stmt->execute()) {
                    throw new Exception('Error al insertar o actualizar datos: ' . $this->conn->error);
                }

                // Cerrar la sentencia después de su ejecución
                $stmt->close();
            
                    // Recopilar y procesar los datos de los datos compplementarios
                    $DC = 1; // Iniciar contador de instancias de los datos compplementarios
                    while (isset($_POST["ID_DatoComplementario$DC"])) {
                        // Recolectar datos del dato compplementario actual
                        $ID_DatoComplementario = $_POST["ID_DatoComplementario$DC"];
                        $DC_NumeroDeOrden = $_POST["DC_NumeroDeOrden$DC"];

                        // Verificar si AF_NumeroDeOrden es igual a 0. Para este caso "0" significa sin asignar
                        if ($DC_NumeroDeOrden == 0) {
                            $stmt = $this->conn->prepare("SELECT MAX(NumeroDeOrden) AS max_orden FROM entidad_datos_complementarios WHERE FK_Arma = ?");
                            $stmt->bind_param("s", $AF_ClavePrimaria);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $row = $result->fetch_assoc();

                            // Determinar el nuevo valor de DC_NumeroDeOrden
                            $DC_NumeroDeOrden = $row['max_orden'] ? $row['max_orden'] + 1 : 1;

                            // Rearmar la ID_Lugar
                            $ID_DatoComplementario = $AF_ClavePrimaria . "-DC" . $DC_NumeroDeOrden;

                            $stmt->close();
                        }

                        // Comprobación para asignar $DC_Tipo
                        if ($_POST["DC_Tipo$DC"] === "Otra opción no listada") {
                            $DC_Tipo = $_POST["DC_TipoEspecifique$DC"];
                        } else {
                            $DC_Tipo = $_POST["DC_Tipo$DC"];
                        }

                        // Procesar los campos comunes
                        $DC_ImagenAdjunta = $_POST["DataURL_DC_ImagenAdjunta$DC"];
                        $DC_Comentario = $_POST["DC_Comentario$DC"];

                        // Sentencia SQL preparada "INSERT ON DUPLICATE KEY UPDATE" para la carga de datos de la entidad "Personas"
                        $stmt = $this->conn->prepare("INSERT INTO entidad_datos_complementarios (ID_DatoComplementario, FK_Arma, NumeroDeOrden, DC_Tipo, DC_ImagenAdjunta, DC_Comentario, DC_UsuarioCreador) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE 
                        NumeroDeOrden = VALUES(NumeroDeOrden), DC_Tipo = VALUES(DC_Tipo), DC_ImagenAdjunta = VALUES(DC_ImagenAdjunta), DC_Comentario = VALUES(DC_Comentario), DC_UsuarioCreador = VALUES(DC_UsuarioCreador)");

                        $types = $this->determinar_tipo_de_dato([$ID_DatoComplementario, $AF_ClavePrimaria, $DC_NumeroDeOrden, $DC_Tipo, $DC_ImagenAdjunta, $DC_Comentario, $usernameID]);

                        $stmt->bind_param($types, $ID_DatoComplementario, $AF_ClavePrimaria, $DC_NumeroDeOrden, $DC_Tipo, $DC_ImagenAdjunta, $DC_Comentario, $usernameID);

                        // Ejecutar la inserción del mensaje
                        if (!$stmt->execute()) {
                            throw new Exception('Error al insertar o actualizar datos: ' . $this->conn->error);
                        }

                        // Cerrar la sentencia después de su ejecución
                        $stmt->close();

                        $DC++; // Incrementar el contador para buscar la siguiente instancia
                    }

                    // Si todo fue exitoso, confirmar la transacción y devolver respuesta de éxito
                    $this->conn->commit();
                    return json_encode(['status' => 'success', 'message' => 'Persona y domicilios guardados con éxito']);

                } catch (Exception $e) {
                    // En caso de error, revertir la transacción y devolver respuesta de error
                    $this->conn->rollback();
                    return json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
            }
        }

}

?>
