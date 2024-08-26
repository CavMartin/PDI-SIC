<?php
// Función para generar las opciones a partir de los arrays
function generarOpcionesSelect($arrayOpciones, $valorRecuperado) {
    $html = '';
    $html .= '<option value="" disabled';

    if ($valorRecuperado === null) {
        $html .= ' selected';
    }

    $html .= '>Selecciona una opción</option>';

    $opcionEncontrada = false;

    foreach ($arrayOpciones as $opcion) {
        $html .= '<option value="' . htmlspecialchars($opcion) . '" ';
        if ($valorRecuperado === $opcion) {
            $html .= 'selected';
            $opcionEncontrada = true;
        }
        $html .= '>' . htmlspecialchars($opcion) . '</option>';
    }

    $html .= '<option value="Otra opción no listada" ';

    if ($valorRecuperado !== null && !$opcionEncontrada) {
        $html .= 'selected';
    }

    $html .= '>Otra opción no listada</option>';

    return $html;
}

function optionsSearchSelect($arrayOpciones) {
    $html = '';
    $html .= '<option value="" selected';
    $html .= '>Indistinto</option>';

    foreach ($arrayOpciones as $opcion) {
        $html .= '<option value="' . htmlspecialchars($opcion) . '" ';
        $html .= '>' . htmlspecialchars($opcion) . '</option>';
    }

    return $html;
}

function optionsSearchSelect2($arrayOpciones) {
    $html = '';
    $html .= '<option value="Otra opción no listada" selected';
    $html .= '>Otra opción no listada</option>';

    foreach ($arrayOpciones as $opcion) {
        $html .= '<option value="' . htmlspecialchars($opcion) . '" ';
        $html .= '>' . htmlspecialchars($opcion) . '</option>';
    }

    return $html;
}

function optionsSearchSelect3($arrayOpciones) {
    foreach ($arrayOpciones as $opcion) {
        $html .= '<option value="' . htmlspecialchars($opcion) . '" ';
        $html .= '>' . htmlspecialchars($opcion) . '</option>';
    }

    return $html;
}

// Arrays del encabezado
    $Array_Clasificacion = [ // Array del tipo de formulario
        'Ficha de infractores',
        'Parte de allanamientos',
        'Parte de homicidios'
    ];

    $Array_PersonaRol = [ // Array de los roles de las personas
        'INFRACTOR',
        'Padre del infractor',
        'Madre del infractor',
        'Hijo del infractor',
        'Amigo del infractor',
    ];

    $Array_Genero = [ // Array de los generos
        'Varón',
        'Mujer',
        'Desconocido'
    ];

    $Array_EstadoCivil = [ // Array de los estados civiles
        'Desconocido',
        'Casada/o',
        'Concubinato',
        'Conviviente',
        'Divorciada/o',
        'Soltera/o',
        'Unión civil',
        'Viuda/o'
    ];

    $Array_TipoDomicilio = [ // Array de los roles/tipos de los domicilios
        'Domicilio registrado según RENAPER',
        'Domicilio aportado por la persona',
        'Domicilio aportado por terceros',
        'Domicilio registrado en bases de datos policiales',
        'Domicilio registrado según padrón electoral provincial 2017',
        'Domicilio registrado según padrón electoral provincial 2023',
        'Domicilio registrado según fuentes abiertas / periodísticas'
    ];


    $Array_RolLugar = [ // Array de los roles de los lugares del hecho
        'Lugar del hecho',
        'Lugar de finalización del hecho'
    ];

    $Array_TipoLugar = [ // Array de los tipos de lugares del hecho
        'Vía pública',
        'Plaza / Parque',
        'Ruta / camino',
        'Cochera / playa de estacionamiento',
        'Descampado',
        'Exterior de asociación civil',
        'Interior de asociación civil',
        'Exterior de comercio',
        'Interior de comercio',
        'Exterior de dependencia pública',
        'Interior de dependencia pública',
        'Exterior de industria',
        'Interior de industria',
        'Exterior de inmueble',
        'Interior de inmueble',
        'Exterior de institución pública',
        'Interior de institución pública',
        'Exterior de vehículo',
        'Interior de vehículo'
    ];

    $Array_RolVehiculo = [ // Array de los roles de los vehículos
        'Vehículo atacado',
        'Vehículo secuestrado',
        'Vehículo utilizado para trasladar a la persona herida',
        'Vehículo utilizado para cometer el ílicito'
    ];
    
    $Array_TipoVehiculo = [ // Array de los tipos de vehículos
        'Acoplado',
        'Automóvil',
        'Avioneta',
        'Bicicleta',
        'Bicicleta eléctrica',
        'Camión',
        'Camioneta',
        'Chasis de camión',
        'Ciclomotor',
        'Cuatriciclo',
        'Ómnibus / Colectivo / Micro',
        'Embarcación a motor',
        'Furgón de carga',
        'Lancha',
        'Máquina agrícola',
        'Máquina de construcción',
        'Máquina de servicios',
        'Moto vehículo',
        'Moto vehículo acuático',
        'Tractor',
        'Triciclo',
        'Vehículo oficial',
        'Vehículo a tracción animal (Carros)'
    ];

    $Array_TipoSecuestro  = [ // Array de los tipos de secuestros
        'Estupefacientes',
        'Material para la facricación de estupefacientes',
        'Elementos varios',
    ];

?>