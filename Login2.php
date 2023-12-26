<?php
// Conectar a la base de datos de forma segura
require 'ServerConnect.php';

?>

<!DOCTYPE html>
<html lang="es">
<style>
    /* Remueve las flechas del input Codigo de Usuario
    Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }

        .container-login100 {
            opacity: 0.8;

        }

        .portada {
            background: url("img/fondo-neon.jpg") no-repeat fixed center;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
            height: 100%;
            width: 100%;
            text-align: center;

        }

        .imgRedonda {
            width: 150px;
            height: 130px;
            border-radius: 0px;
        }
</style>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="CSS/styles.css">
    <link rel="icon" href="css/Images/favicon.ico" type="Image/x-icon">
    <link rel="shortcut icon" href="css/Images/favicon.ico" type="Image/x-icon">
    <script src="Scripts/Login.js"></script>
    <script src="Scripts/PasswordVisibility.js"></script>
    <script src="Scripts/TransformarDatos.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="../vendor/bootstrap/css/bootstrap.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css"
        href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css"
        href="fonts/iconic/css/material-design-iconic-font.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="../vendor/animate/animate.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="../vendor/css-hamburgers/hamburgers.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="../vendor/animsition/css/animsition.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="../vendor/select2/select2.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="../vendor/daterangepicker/daterangepicker.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="css/util.css">
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <!--===============================================================================================-->

</head>
<body class="BodyFondo1">

<div class="LoginForm">
    <form id="loginForm" method="post" onsubmit="return Logear(event)">
        <h2>INICIO DE SESIÓN</h2>
        <label for="username" style="text-align: center;">Nombre de usuario:</label>
        <input type="text" id="username" name="username" style="text-align: center;" onchange="transformarDatosMayusculas('username')" required>
        
        <label for="password" style="text-align: center;">Contraseña:</label>
        <div class="password-container">
        <input type="password" id="password" name="password" style="text-align: center;" required>
        <img src="css/Images/Ocultar.png" onclick="togglePasswordVisibility('password', this)" class="toggle-password" alt="Mostrar/Ocultar">
        </div>

        <button type="submit" class="CustomLargeButton" style="text-align: center; margin-top: 1vw;">Iniciar sesión</button>
    </form>
</div>

<div class="limiter">
        <div class="portada">
            <div class="container-login100">
                <div class="wrap-login100">
                                        <form method="POST" action="http://10.1.3.224/drogasaic/public/login">
                        <input type="hidden" name="_token" value="LqPOK7i2iuuHcKOPwNa487WHlE2iyyhHrZ7gQHzf">                        <!-- LOGO 
                        <img src='img/logo_huella.png' class='imgRedonda' /> -->
                        <span class="login200-form-title p-b-34 p-t-27"> 
                            <h4>SISTEMA INFRACTORES LEY 23.737</h4>
                        </span>

                        <div class="wrap-input100">
                            <input class="input100" type="number" id="cod_usr" name="cod_usr" placeholder="Código">
                            <span class="focus-input100" data-placeholder="&#xf207;"></span>
                        </div>

                        <div class="wrap-input100">
                            <input class="input100" type="text" id="name" name="name" placeholder="Nombre" readonly>
                            <span class="focus-input100" data-placeholder="&#xf207;"></span>
                        </div>

                        <div class="wrap-input100" data-validate="Enter password">
                            <input class="input100" type="password" id="password" name="password"
                                placeholder="Contraseña">
                            <span class="focus-input100" data-placeholder="&#xf191;"></span>
                        </div>

                        <div class="container-login100-form-btn">
                            <button type="submit" class="login100-form-btn">
                                Ingresar
                            </button>
                        </div>
                        <br>
                        <span class="login200-form-title p-b-34 p-t-27">
                        División Informática Policial - A.I.C. 
                        </span>
                        <span class="login300-form-title p-b-34 p-t-27">
                             Tel. (0342) 450-5100 Int. 6228/29
                        </span>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="dropDownSelect1"></div>



</body>
</html>