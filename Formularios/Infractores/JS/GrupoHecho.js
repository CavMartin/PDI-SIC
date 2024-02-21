document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('IP_TipoHecho').addEventListener('change', function() {
        var seleccionado = this.value;
        var grupoHecho = 'Otros'; // Valor por defecto

        switch (seleccionado) {
            case 'Aprehendidos':
            case 'Aprehendidos con arma de fuego':
            case 'Aprehendidos con estupefacientes':
                grupoHecho = 'Aprehendidos';
                break;

            case 'Disparos de arma de fuego al aire':
            case 'Disparos de arma de fuego contra comercios':
            case 'Disparos de arma de fuego contra domicilios':
            case 'Disparos de arma de fuego contra institución pública':
            case 'Disparos de arma de fuego contra personas':
            case 'Disparos de arma de fuego contra vehículos':
                grupoHecho = 'Balaceras';
                break;

            case 'Heridos de arma blanca':
            case 'Heridos de arma de fuego':
            case 'Otro tipo de heridas sin clasificar':
                grupoHecho = 'Heridos';
                break;

            case 'Óbitos':
            case 'Óbitos y heridos':
                grupoHecho = 'Obitos';
                break;
        }

        document.getElementById('IP_GrupoHecho').value = grupoHecho;
    });
});