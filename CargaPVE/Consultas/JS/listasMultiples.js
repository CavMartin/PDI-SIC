document.addEventListener('DOMContentLoaded', function() {
    // Cargar el archivo JSON
    fetch('../JS/listasMultiples.json')
      .then(response => response.json())
      .then(data => {
        // Llenar los selectores múltiples con los datos del JSON
        populateSelect('Tipologia', data.Tipologia);
        populateSelect('ModalidadComisiva', data.ModalidadComisiva);
        populateSelect('TipoEstupefaciente', data.TipoEstupefaciente);

        // Inicializar Selectize en los campos select después de cargar los datos
        initializeSelectize();
      });

    function populateSelect(selectId, options) {
      const selectElement = document.getElementById(selectId);
      options.forEach(option => {
        const opt = document.createElement('option');
        opt.value = option;
        opt.textContent = option;
        selectElement.appendChild(opt);
      });
    }

    function initializeSelectize() {
      $('#Tipologia').selectize({
        plugins: ['remove_button'],
        delimiter: ',',
        persist: false,
        create: function(input) {
          return { value: input, text: input };
        }
      });

      $('#ModalidadComisiva').selectize({
        plugins: ['remove_button'],
        delimiter: ',',
        persist: false,
        create: function(input) {
          return { value: input, text: input };
        }
      });

      $('#TipoEstupefaciente').selectize({
        plugins: ['remove_button'],
        delimiter: ',',
        persist: false,
        create: function(input) {
          return { value: input, text: input };
        }
      });
    }
});