// Función para obtener las datalist
let ciudades = [];
let provincias = [];
    document.addEventListener('DOMContentLoaded', function() {
        fetch('../Resources/JSON/CiudadesSantaFe.json')
        .then(response => response.json())
        .then(data => {
            ciudades = data;
            return fetch('../Resources/JSON/ProvinciasArgentinas.json'); // Retorna la siguiente promesa
        })
        .then(response => response.json())
        .then(data => {
            provincias = data;
            inicializarDataLists(); // Llama a la función solo después de haber cargado ambas listas
        })
        .catch(error => console.error('Error al cargar las listas:', error));
    });

// Crear y llenar los datalist globales
function inicializarDataLists() {
    const datalistCiudades = document.querySelector('#globalSugerenciasCiudades');
    const datalistProvincias = document.querySelector('#globalSugerenciasProvincias');
    
    ciudades.forEach(ciudad => {
        const optionCiudades = document.createElement('option');
        optionCiudades.value = ciudad;
        datalistCiudades.appendChild(optionCiudades);
    });

    provincias.forEach(provincia => {
        const optionProvincias = document.createElement('option');
        optionProvincias.value = provincia;
        datalistProvincias.appendChild(optionProvincias);
    });
}