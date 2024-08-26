function logout() {
    const data = new URLSearchParams();
    data.append('action', 'logout');

    fetch('http://localhost/SIC/Usuarios/PHP/UserEndPoint.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: data
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            window.location.href = 'http://localhost/SIC/Login.php';
        } else {
            console.error('Error:', result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}