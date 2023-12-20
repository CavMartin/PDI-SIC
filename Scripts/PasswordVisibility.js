function togglePasswordVisibility(fieldId, imgElement, imgPathPrefix = '') {
    var field = document.getElementById(fieldId);
    var isPasswordVisible = field.type === 'password';
    field.type = isPasswordVisible ? 'text' : 'password';
    imgElement.src = isPasswordVisible 
        ? imgPathPrefix + 'css/Images/Mostrar.png' 
        : imgPathPrefix + 'css/Images/Ocultar.png';
}
