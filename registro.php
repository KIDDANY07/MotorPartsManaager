<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

include 'php/conexion.php';

$mensaje = "";
$success = false;

if (isset($_SESSION['correo'])) {
    header("location:index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty($_POST['nombre']) || empty($_POST['apellido']) || empty($_POST['correo']) || empty($_POST['contrasena']) || empty($_POST['repetir_contrasena']) || empty($_POST['numero_documento'])) {
        $mensaje = "Por favor, complete todos los campos.";
    } else {

        $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
        $apellido = mysqli_real_escape_string($conexion, $_POST['apellido']);
        $correo = mysqli_real_escape_string($conexion, $_POST['correo']);
        $contrasena = mysqli_real_escape_string($conexion, $_POST['contrasena']);
        $repetirContrasena = mysqli_real_escape_string($conexion, $_POST['repetir_contrasena']);
        $numeroDocumento = mysqli_real_escape_string($conexion, $_POST['numero_documento']);
        $tipoDocumento = mysqli_real_escape_string($conexion, $_POST['tipo_documento']);
        $foto_perfil = ""; // Inicializa la variable para evitar errores si no se sube ninguna imagen

        // Verificar si el correo ya está registrado
        $query_verificacion_correo = "SELECT * FROM usuarios WHERE correo = '$correo'";
        $result_verificacion_correo = mysqli_query($conexion, $query_verificacion_correo);

        if (mysqli_num_rows($result_verificacion_correo) > 0) {
            $mensaje = "Este correo ya está registrado.";
        } elseif (empty($nombre) || empty($apellido) || empty($correo) || empty($contrasena) || empty($repetirContrasena) || empty($numeroDocumento) || empty($tipoDocumento)) {
            $mensaje = "Por favor, complete todos los campos.";
        } elseif (!ctype_digit($numeroDocumento)) {
            $mensaje = "El número de identificación debe contener solo números.";
        } elseif ($contrasena !== $repetirContrasena) {
            $mensaje = "Las contraseñas no coinciden. Por favor, inténtelo de nuevo.";
        } else {
            // Cambiar la encriptación de la contraseña a SHA1
            $contrasenaHash = sha1($contrasena);
            $query = "INSERT INTO usuarios (nombre, apellido, correo, contrasena, numero_documento, tipo_documento, foto_perfil) 
                      VALUES ('$nombre', '$apellido', '$correo', '$contrasenaHash', '$numeroDocumento', '$tipoDocumento', '$foto_perfil')";
            $result = mysqli_query($conexion, $query);

            if ($result) {
                $mensaje = "¡Registro exitoso!";
                $success = true;
            } else {
                $mensaje = "Error al registrar el usuario. Por favor, inténtelo de nuevo.";
            }
        }
    }
    
    echo json_encode(array("success" => $success, "mensaje" => $mensaje));
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .register_button:hover{
            transition:0.5s;
            background-color:bgb(59, 130, 246, 0.1);
        }
        .register_button:not(:hover){
            transition:0.5s;
        }

        .verita{
            height:90px
        }
    </style>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen">
    <div class="max-w-md w-full bg-white p-8 rounded-lg shadow-md">
        <img src="imagenes/logo.jpeg" class="verita mx-auto"></img>
        <form id="registerForm" class="mt-8" method="POST" action="registro.php" enctype="multipart/form-data">
            <div class="mb-4 flex items-center">
                <img src="imagenes/login.png" alt="Nombre" class="w-6 h-6 mr-3">
                <input type="text" name="nombre" id="nombre" placeholder="Ingrese su nombre" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500">
                <span class="mx-2">|</span>
                <input type="text" name="apellido" id="apellido" placeholder="Ingrese su apellido" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500">
            </div>
            <div class="mb-4 flex items-center">
                <img src="imagenes/correo.png" alt="Correo electrónico" class="w-6 h-6 mr-3">
                <input type="email" name="correo" id="correo" placeholder="Ingrese su correo" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500">
            </div>
            <div class="mb-4 flex items-center">
                <img src="imagenes/pass.png" alt="Contraseña" class="w-6 h-6 mr-3">
                <input type="password" name="contrasena" id="contrasena" placeholder="Ingrese su contraseña" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500">
            </div>
            <div class="mb-4 flex items-center">
                <img src="imagenes/pass.png" alt="Repetir Contraseña" class="w-6 h-6 mr-3">
                <input type="password" name="repetir_contrasena" id="repetir_contrasena" placeholder="Repetir contraseña" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500">
            </div>
            <div class="mb-4 flex items-center">
                <img src="imagenes/dni.png" alt="Tipo de Documento" class="w-6 h-6 mr-3">
                <select name="tipo_documento" id="tipo_documento" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500">
                    <option value="tarjetaidentidad">Tarjeta de identidad</option>
                    <option value="cedula">Cédula</option>
                    <option value="pasaporte">Pasaporte</option>
                </select>
                <span class="mx-2">|</span>
                <input type="text" name="numero_documento" id="numero_documento" placeholder="Ingrese su número de documento" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500">
            </div>            
            <div class="flex justify-center">
                <button type="submit" class="register_button bg-white text-black px-4 py-2 rounded-m hover:text-blue-700 ">Registrar</button>
            </div>
        </form>

        <div class="mt-4 text-center">
            <a href="index.php" class="text-blue-500 font-semibold">Inicio</a>
            <span class="mx-2">|</span>
            <a href="login.php" class="text-blue-500 font-semibold">¿Ya tienes una cuenta? Inicia sesión</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('registerForm').addEventListener('submit', function (event) {
                event.preventDefault();

                var formData = new FormData(this);

                fetch('registro.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: '¡Registro exitoso!',
                            text: data.mensaje,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = 'index.php';
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.mensaje,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        });
    </script>
</body>
</html>
