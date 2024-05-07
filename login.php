<?php
session_start();

include 'php/conexion.php';

$mensaje = "";
$success = false;

if (isset($_SESSION['correo'])) {
    header("location:index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .login_button:hover{
            transition:0.5s;
            background-color:bgb(59, 130, 246, 0.1);
        }
        .login_button:not(:hover){
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
        <form id="loginForm" class="mt-8" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="mb-4 flex items-center">
                <img src="imagenes/login.png" alt="Correo electrónico" class="w-6 h-6 mr-3">
                <input type="text" name="correo" id="correo" placeholder="Ingrese su correo" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500">
            </div>
            <div class="mb-6 flex items-center">
                <img src="imagenes/pass.png" alt="Contraseña" class="w-6 h-6 mr-3">
                <input type="password" name="contrasena" id="contrasena" placeholder="Ingrese su contraseña" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500">
            </div>
            <div class="flex justify-center">
                <button type="submit" class="login_button bg-white text-black px-4 py-2 rounded-m hover:text-blue-700 focus:outline-none focus:bg-blue-600">Iniciar sesión</button>
            </div>
        </form>
        <br>
        <div class="mt-4 text-center">
            <a href="index.php" class="text-blue-500 font-semibold">Inicio</a>
            <span class="mx-2">|</span>
            <a href="registro.php" class="text-blue-500 font-semibold">¿No tienes una cuenta? Registrate</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>

<?php 
include 'php/conexion.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty($_POST['correo']) || empty($_POST['contrasena'])) {
        $mensaje = "Por favor, complete todos los campos.";
    } else {

        $correo = mysqli_real_escape_string($conexion, $_POST['correo']);
        $contrasena = mysqli_real_escape_string($conexion, $_POST['contrasena']);

        // Selecciona el usuario por su correo
        $query = "SELECT * FROM usuarios WHERE correo = '$correo'";
        $result = mysqli_query($conexion, $query);

        if (mysqli_num_rows($result) == 1) {
            $usuario = mysqli_fetch_assoc($result);

            // Compara la contraseña ingresada con la contraseña almacenada en la base de datos
            if (sha1($contrasena) === $usuario['contrasena']) {
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['correo'] = $correo;
                $_SESSION['apellido'] = $usuario['apellido'];
                $_SESSION['numero_documento'] = $usuario['numero_documento'];
                $_SESSION['tipo_documento'] = $usuario['tipo_documento'];
                $_SESSION['foto_perfil'] = $usuario['foto_perfil'];
                $_SESSION['direccion'] = $usuario['direccion']; // Campo de dirección agregado
                $_SESSION['codigo_postal'] = $usuario['codigo_postal']; // Campo de código postal agregado
                $_SESSION['pais'] = $usuario['pais']; // Campo de país agregado

                $mensaje = "¡Inicio de sesión exitoso! Bienvenido, " . $usuario['nombre'];
                $success = true;
            } else {
                $mensaje = "Correo o contraseña incorrectos. Por favor, intente de nuevo.";
            }
        } else {
            $mensaje = "Correo o contraseña incorrectos. Por favor, intente de nuevo.";
        }

        // Mostrar la alerta utilizando SweetAlert2 dentro del mismo PHP
        echo "<script>
                Swal.fire({
                    title: '" . ($success ? '¡Inicio de sesión exitoso!' : 'Error') . "',
                    text: '$mensaje',
                    icon: '" . ($success ? 'success' : 'error') . "',
                    confirmButtonText: 'OK'
                }).then(() => {
                    " . ($success ? "window.location.href = 'index.php';" : "") . "
                });
              </script>";
    }
}
?>
