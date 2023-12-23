<?php
class DatabaseManager {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    //Función para determinar los tipos de datos a 
    private function determineTypes($params) {
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

    private function refValues($arr) {
        $refs = [];
        foreach ($arr as $key => $value) {
            $refs[$key] = &$arr[$key];
        }
        return $refs;
    }

    public function insertOrUpdateFichaInfractor($Ficha_Apellido, $Ficha_Nombre, $Ficha_Alias, $Ficha_TipoDNI, $Ficha_DNI, $Ficha_Prontuario, $Ficha_Genero, $Ficha_FechaNacimiento, $Ficha_LugarNacimiento, $Ficha_EstadoCivil, $Ficha_Provincia, $Ficha_Pais, $Ficha_DomiciliosJSON, $Base64FotoIzquierda, $Base64FotoCentral, $Base64FotoDerecha, $Ficha_FechaHecho, $Ficha_LugarHecho, $Ficha_Causa, $Ficha_Juzgado, $Ficha_Fiscalia, $Ficha_Dependencia, $Ficha_Observaciones, $Ficha_Reseña, $Ficha_DescripcionDelSecuestro, $usernameID, $UserRegion) {
        $sql = "INSERT INTO ficha_de_infractor (Apellido, Nombre, Alias, TipoDocumento, DocumentoNumero, Prontuario, Genero, FechaNacimiento, LugarNacimiento, EstadoCivil, Provincia, Pais, Domicilio, FotoIzquierda, FotoCentral, FotoDerecha, FechaHecho, LugarHecho, Causa, Juzgado, Fiscalia, Dependencia, Observaciones, Reseña, Secuestro, UsuarioCreador, Region)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE Apellido = ?, Nombre = ?, Alias = ?, TipoDocumento = ?, DocumentoNumero = ?, Prontuario = ?, Genero = ?, FechaNacimiento = ?, LugarNacimiento = ?, EstadoCivil = ?, Provincia = ?, Pais = ?, Domicilio = ?, FotoIzquierda = ?, FotoCentral = ?, FotoDerecha = ?, FechaHecho = ?, LugarHecho = ?, Causa = ?, Juzgado = ?, Fiscalia = ?, Dependencia = ?, Observaciones = ?, Reseña = ?, Secuestro = ?, UsuarioCreador = ?, Region = ?";
        $stmtFicha = $this->conn->prepare($sql);
    
        if ($stmtFicha) {
            $params = [$Ficha_Apellido, $Ficha_Nombre, $Ficha_Alias, $Ficha_TipoDNI, $Ficha_DNI, $Ficha_Prontuario, $Ficha_Genero, $Ficha_FechaNacimiento, $Ficha_LugarNacimiento, $Ficha_EstadoCivil, $Ficha_Provincia, $Ficha_Pais, $Ficha_DomiciliosJSON, $Base64FotoIzquierda, $Base64FotoCentral, $Base64FotoDerecha, $Ficha_FechaHecho, $Ficha_LugarHecho, $Ficha_Causa, $Ficha_Juzgado, $Ficha_Fiscalia, $Ficha_Dependencia, $Ficha_Observaciones, $Ficha_Reseña, $Ficha_DescripcionDelSecuestro, $usernameID, $UserRegion];
            // Duplicar los parámetros para la parte de UPDATE
            $params = array_merge($params, $params);
    
            $types = $this->determineTypes($params);
            $bindParams = array_merge([$types], $params);
    
            call_user_func_array([$stmtFicha, 'bind_param'], $this->refValues($bindParams));
    
            $stmtFicha->execute();
            $stmtFicha->close();
        }
    }    

    public function insertPersonasRelacionadas($infractorID, $relacion, $apellido, $nombre, $alias, $tipoDocumento, $documentoNumero, $prontuario, $genero, $domicilio, $informacionDeInteres) {
        $sqlPersona = "INSERT INTO ficha_personas (InfractorID, Relacion, Apellido, Nombre, Alias, TipoDocumento, DocumentoNumero, Prontuario, Genero, Domicilio, InformacionDeInteres)
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtPersona = $this->conn->prepare($sqlPersona);

        if ($stmtPersona) {
            $params = [$infractorID, $relacion, $apellido, $nombre, $alias, $tipoDocumento, $documentoNumero, $prontuario, $genero, $domicilio, $informacionDeInteres];
            $types = $this->determineTypes($params);
            $bindParams = array_merge([$types], $params);

            call_user_func_array([$stmtPersona, 'bind_param'], $this->refValues($bindParams));

            $stmtPersona->execute();
            $stmtPersona->close();
        }
    }

    public function insertImagen($infractorID, $TipoFotografia, $imagen) {
        $sql = "INSERT INTO fotografias (InfractorID, TipoFotografia, Imagen)
                VALUES (?, ?, ?)";
        $stmtImagen = $this->conn->prepare($sql);

        if ($stmtImagen) {
            $params = [$infractorID, $TipoFotografia, $imagen];
            $types = $this->determineTypes($params);
            $bindParams = array_merge([$types], $params);

            call_user_func_array([$stmtImagen, 'bind_param'], $this->refValues($bindParams));

            $stmtImagen->execute();
            $stmtImagen->close();
        }
    }

    public function insertRedesSociales($infractorID, $tipoRedSocial, $redSocialLink) {
        $sqlRedSocial = "INSERT INTO redes_sociales (InfractorID, TipoRedSocial, Link)
                         VALUES (?, ?, ?)";
        $stmtRedSocial = $this->conn->prepare($sqlRedSocial);

        if ($stmtRedSocial) {
            $params = [$infractorID, $tipoRedSocial, $redSocialLink];
            $types = $this->determineTypes($params);
            $bindParams = array_merge([$types], $params);

            call_user_func_array([$stmtRedSocial, 'bind_param'], $this->refValues($bindParams));

            $stmtRedSocial->execute();
            $stmtRedSocial->close();
        }
    }

    // Agregue mas métodos aquí

    public function closeConnection() {
        $this->conn->close();
    }
}
?>
