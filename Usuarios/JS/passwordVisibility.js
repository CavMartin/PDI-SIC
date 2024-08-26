function togglePasswordVisibility(fieldId, imgElement, imgPathPrefix = '') {
    var field = document.getElementById(fieldId);
    var isPasswordVisible = field.type === 'password';
    field.type = isPasswordVisible ? 'text' : 'password';
    imgElement.src = isPasswordVisible 
        ? imgPathPrefix + 'CSS/Images/Mostrar.png' 
        : imgPathPrefix + 'CSS/Images/Ocultar.png';
}
