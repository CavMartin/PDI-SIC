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
            background: url("CSS/Images/fondo-neon.jpg") no-repeat fixed center;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
            height: 100%;
            width: 100%;
            text-align: center;
        }

        .LoginContainer {
            width: 150px;
            height: 130px;
            border-radius: 0px;
        }
</style>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" href="css/Images/favicon.ico" type="Image/x-icon">
    <link rel="shortcut icon" href="css/Images/favicon.ico" type="Image/x-icon">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="fonts/iconic/css/material-design-iconic-font.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="css/util.css">
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <!--===============================================================================================-->
    <script src="Scripts/Login.js"></script>
    <script src="Scripts/TransformarDatos.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="limiter">
    <div class="portada">
        <div class="container-login100">
            <div class="wrap-login100">
                <form id="loginForm" method="post" onsubmit="return Logear(event)">
                    <span class="login200-form-title p-b-30 p-t-27"> 
                        <h4>SISTEMA DE INFRACTORES LEY 23.737</h4>
                    </span>

                    <div class="wrap-input100">
                        <input class="input100" type="text" id="username" name="username" placeholder="Usuario" onchange="transformarDatosMayusculas('username')" required>
                        <span class="focus-input100" data-placeholder="&#xf207;"></span>
                    </div>
                        
                    <div class="wrap-input100" data-validate="Enter password">
                        <input class="input100" type="password" id="password" name="password" placeholder="Contraseña">
                        <span class="focus-input100" data-placeholder="&#xf191;"></span>
                    </div>

                    <div class="container-login100-form-btn">
                        <button type="submit" class="login100-form-btn">Ingresar al sistema</button>
                    </div>
                    <br>

                    <span class="login200-form-title p-b-25 p-t-27">
                    División Informática y tecnología
                    </span>

                    <span class="login200-form-title p-b-1 p-t-27">
                    Dirección de investigación criminal
                    </span>
                    <span class="login200-form-title p-b-25 p-t-27">
                    sobre el narcotrafico
                    </span>

                    <span class="login200-form-title p-b-25 p-t-27">
                    Policía de investigaciones
                    </span>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>