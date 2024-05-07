<?php
session_start();

include 'php/conexion.php';

$mensaje = "";
$success = false;

if (!isset($_SESSION['correo'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $apellido = mysqli_real_escape_string($conexion, $_POST['apellido']);
    $direccion = mysqli_real_escape_string($conexion, $_POST['direccion']);
    $codigo_postal = mysqli_real_escape_string($conexion, $_POST['codigo_postal']);
    $pais = mysqli_real_escape_string($conexion, $_POST['pais']);
    $correo = $_SESSION['correo']; 

    if ($_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
        $foto_perfil_temp = $_FILES['foto_perfil']['tmp_name'];
        $foto_perfil_nombre = $_FILES['foto_perfil']['name'];
        $foto_perfil_destino = "fotosperfiles/" . $foto_perfil_nombre;

        if (move_uploaded_file($foto_perfil_temp, $foto_perfil_destino)) {
            $_SESSION['foto_perfil'] = $foto_perfil_destino;
            $query = "UPDATE usuarios SET foto_perfil = '$foto_perfil_destino' WHERE correo = '$correo'";
            $result = mysqli_query($conexion, $query);
        }
    }

    $query = "UPDATE usuarios SET nombre = '$nombre', apellido = '$apellido', direccion = '$direccion', codigo_postal = '$codigo_postal', pais = '$pais' WHERE correo = '$correo'";
    $result = mysqli_query($conexion, $query);

    if ($result) {
        $_SESSION['nombre'] = $nombre;
        $_SESSION['apellido'] = $apellido;
        $_SESSION['direccion'] = $direccion;
        $_SESSION['codigo_postal'] = $codigo_postal;
        $_SESSION['pais'] = $pais;

        $mensaje = "¡Información actualizada con éxito!";
        $success = true;
    } else {
        $mensaje = "Error al actualizar la información. Por favor, inténtelo de nuevo.";
    }
}
if(empty($foto_perfil)) {
    $foto_perfil = "fotosperfiles/perfil_base.png";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Información</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .boton_guardar {
            border-radius: 2%;
        }

        .boton_guardar:hover {
            transition: 0.5s;
        }

        .boton_guardar:not(:hover) {
            transition: 0.5s;
        }
    </style>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen">
    <div class="max-w-md w-full bg-white p-8 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold text-center mb-4">Actualizar Información</h2>
        <form id="updateForm" class="mt-8" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <div class="mb-4 flex items-center">
                <img src="imagenes/login.png" alt="Correo electrónico" class="w-6 h-6 mr-3">
                <input type="text" name="nombre" id="nombre" value="<?php echo isset($_SESSION['nombre']) ? $_SESSION['nombre'] : ''; ?>" placeholder="Ingrese su nombre" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500">
                <span class="mx-2">|</span>
                <input type="text" name="apellido" id="apellido" value="<?php echo isset($_SESSION['apellido']) ? $_SESSION['apellido'] : ''; ?>" placeholder="Ingrese su apellido" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500">
            </div>
            <div class="mb-4 flex items-center">
                <img src="imagenes/ubicacion.png" alt="Correo electrónico" class="w-6 h-6 mr-3">
                <input type="text" name="direccion" id="direccion" value="<?php echo isset($_SESSION['direccion']) ? $_SESSION['direccion'] : ''; ?>" placeholder="Ingrese su dirección" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500">
            </div>
            <div class="mb-4 flex items-center">
                <img src="imagenes/postal.png" alt="Correo electrónico" class="w-6 h-6 mr-3">
                <input type="text" name="codigo_postal" id="codigo_postal" value="<?php echo isset($_SESSION['codigo_postal']) ? $_SESSION['codigo_postal'] : ''; ?>" placeholder="Ingrese su código postal" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500">
            </div>
            <div class="mb-4 flex items-center">
                <img src="imagenes/pais.png" alt="Correo electrónico" class="w-6 h-6 mr-3">
                <input type="text" name="pais" id="pais" value="<?php echo isset($_SESSION['pais']) ? $_SESSION['pais'] : ''; ?>" placeholder="Ingrese su país" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500">
            </div>
            <div class="mb-4 flex items-center">
                <img src="imagenes/foto.png" alt="Foto de perfil" class="w-6 h-6 mr-3">
                <input type="file" name="foto_perfil" id="foto_perfil" accept="image" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500">
            </div>

            <div class="flex justify-center">
                <button type="submit" class="boton_guardar text-blue-500 px-4 py-2 rounded-m hover:text-blue-800 focus:outline-none">Guardar Cambios</button>
            </div>
            <div class="mt-4 text-center">
                <a href="perfil.php" class="text-blue-500 hover:text-blue-800 ">Volver</a>
            </div>
        </form>

        <?php if ($mensaje !== ''): ?>
        <div class="mt-4 text-center <?php echo $success ? 'text-green-500' : 'text-red-500'; ?>">
            <?php echo $mensaje; ?>
        </div>
        <?php endif; ?>
    </div>

    <?php if ($success): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                icon: 'success',
                title: '¡Información actualizada!',
                text: 'Tu información ha sido actualizada exitosamente.',
                showConfirmButton: true
            }).then(() => {
                window.location.href = 'perfil.php';
            });
        </script>
    <?php endif; ?>
</body>
</html>
