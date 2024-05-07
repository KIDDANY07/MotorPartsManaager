<?php
session_start();

$isLoggedIn = isset($_SESSION['correo']);

if(isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

$nombreUsuario = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : "Usuario";
$identificacionUsuario = isset($_SESSION['identificacion']) ? $_SESSION['identificacion'] : "Identificación";
$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : "";
$apellido = isset($_SESSION['apellido']) ? $_SESSION['apellido'] : "";
$correo = isset($_SESSION['correo']) ? $_SESSION['correo'] : "";
$numero_documento = isset($_SESSION['numero_documento']) ? $_SESSION['numero_documento'] : "";
$tipo_documento = isset($_SESSION['tipo_documento']) ? $_SESSION['tipo_documento'] : "";
$foto_perfil = isset($_SESSION['foto_perfil']) ? $_SESSION['foto_perfil'] : ""; 
$direccion = isset($_SESSION['direccion']) ? $_SESSION['direccion'] : "";
$codigo_postal = isset($_SESSION['codigo_postal']) ? $_SESSION['codigo_postal'] : "";
$pais = isset($_SESSION['pais']) ? $_SESSION['pais'] : "";

$documentos_permitidos = array("1027400008", "1073671038","1000131565");
if (!in_array($numero_documento, $documentos_permitidos)) {
    header("Location: index.php");
    exit();
}

include 'php/conexion.php';

$message = "";

if(isset($_POST['agregar_producto'])) {
    $nombre_producto = $_POST['nombre_producto'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];

    $sql = "INSERT INTO productos (nombre, descripcion, precio) VALUES ('$nombre_producto', '$descripcion', '$precio')";
    if ($conexion->query($sql) === TRUE) {
        $message = "Producto agregado exitosamente.";
    } else {
        $message = "Error al agregar el producto: " . $conexion->error;
    }
}

if(isset($_POST['eliminar_producto'])) {
    $producto_id = $_POST['producto_id'];

    $sql = "DELETE FROM productos WHERE id_producto = '$producto_id'";
    if ($conexion->query($sql) === TRUE) {
        $message = "Producto eliminado exitosamente.";
    } else {
        $message = "Error al eliminar el producto: " . $conexion->error;
    }
}

$sql = "SELECT * FROM productos";
$result = $conexion->query($sql);
if(empty($foto_perfil)) {
    $foto_perfil = "fotosperfiles/perfil_base.png";
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MotorParts Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="bg-gray-100">
<header class="bg-white text-gray-800 py-4 shadow-xl">
    <div class="container mx-auto flex justify-between items-center">
        <div class="my-2">
            <a href="index.php"><img src="imagenes/logo.jpeg" alt="Logo" class="h-14"></a>
        </div>
        <nav class="space-x-4 flex items-center mr-4">
            <a href="index.php" class="hover:text-blue-700 block">
                <i class="fas fa-home"></i>
                <span class="hidden md:inline">Inicio</span>
            </a>
            <?php if(!$isLoggedIn): ?>
                <a href="login.php" class="hover:text-blue-700 block">
                    <i class="fas fa-sign-in-alt"></i>
                    <span class="hidden md:inline">Iniciar sesión</span>
                </a>
            <?php endif; ?>
            <a href="tienda.php" class="hover:text-blue-700 block">
                <i class="fas fa-store"></i>
                <span class="hidden md:inline">Tienda</span>
            </a>
            <a href="contacto.php" class="hover:text-blue-700 block">
                <i class="fas fa-envelope"></i>
                <span class="hidden md:inline">Contacto</span>
            </a>
            <?php if($isLoggedIn): ?>
                <a href="carrito.php" class="hover:text-blue-700 block">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="hidden md:inline">Carrito</span>
                </a>
                <div class="relative inline-block text-left z-50 sm:z-999">
                    <button id="userMenuButton" class="text-gray-800 hover:text-blue-700 focus:outline-none flex items-center">
                        <img src="<?php echo $foto_perfil; ?>" alt="Usuario" class="mr-3 h-8 w-8 rounded-full inline-block">
                        <div class="hidden sm:block ml-1 mr-5"><?php echo $nombreUsuario; ?></div>
                    </button>
                    <div id="userMenu" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden transition-opacity">
                        <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="userMenuButton" tabindex="-1">
                            <a href="perfil.php" class="block px-4 py-2 text-sm text-gray-800 hover:bg-white hover:text-blue-700" role="menuitem" tabindex="-1"><i class="fas fa-user mr-2"></i>Perfil</a>
                            <a href="configuracion.php" class="block px-4 py-2 text-sm text-gray-800 hover:bg-white  hover:text-blue-700" role="menuitem" tabindex="-1"><i class="fas fa-cog mr-2"></i>Configuración</a>
                            <a href="carrito.php" class="block px-4 py-2 text-sm text-gray-800 hover:bg-white  hover:text-blue-700" role="menuitem" tabindex="-1"><i class="fas fa-shopping-cart mr-2"></i>Carrito</a>
                            <a href="index.php?logout=true" class="block px-4 py-2 text-sm text-gray-800 hover:bg-white  hover:text-blue-700 relative" role="menuitem" tabindex="-1">
                                <i class="fas fa-sign-out-alt mr-2"></i>
                                Cerrar sesión
                                <span class="absolute right-0 top-0 -mt-2 bg-blue-500 text-white px-1 rounded-full opacity-0 transition-opacity group-hover:opacity-100">Cerrar sesión</span>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </nav>
    </div>
</header>

<div class="container mx-auto mt-8">
    <a href="agregar_productos.php" class="bg-blue-500 p-6 m-4 hover:bg-blue-700 text-white font-bold py-2 px-3 rounded">Agregar Producto</a>
    <br><br>
    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo $row['id_producto']; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo $row['nombre']; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo $row['descripcion']; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo $row['precio']; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="inline">
                                        <input type="hidden" name="producto_id" value="<?php echo $row['id_producto']; ?>">
                                        <button type="submit" name="eliminar_producto" class="text-red-500 hover:text-red-700">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<br><br>
<footer class="bg-indigo-900 text-white py-8">
    <div class="container text-center mx-auto grid grid-cols-1 md:grid-cols-2 gap-8">
        <div>
            <h3 class="text-lg font-semibold mb-4">Contacto</h3>
            <p class="text-sm">Dirección: Calle Principal, Ciudad, País</p>
            <p class="text-sm">Teléfono: +57 3115824523</p>
            <p class="text-sm">Correo electrónico: info@verafc.com</p>
        </div>
        <div>
            <h3 class="text-lg font-semibold mb-4">Redes sociales</h3>
            <ul class="text-sm">
                <li><a href="#" class="hover:text-blue-300">Facebook</a></li>
                <li><a href="#" class="hover:text-blue-300">Twitter</a></li>
                <li><a href="#" class="hover:text-blue-300">Instagram</a></li>
                <li><a href="#" class="hover:text-blue-300">LinkedIn</a></li>
            </ul>
        </div>
    </div>
    <div class="container mx-auto text-center ">
        <p class="text-sm">Desarrollado por </p>
        <p><a href="#" class=" hover:text-blue-300">Daniel Jose Morales Teatino</a></p>
        <p class="text-sm"><a href="#" class=" hover:text-blue-300">Lukas David Davila Alzate</a></p>
        <p class="text-sm"><a href="#" class="hover:text-blue-300">Edwar Sebastian Ruiz Alvarez</a></p>
    </div>
    <br><br>
    <div class="container mx-auto text-center">
        <p class="text-sm">&copy; 2024 MotorParts. Todos los derechos reservados.</p>
    </div>
</footer>
<script>
    document.getElementById('userMenuButton').addEventListener('click', function () {
        var userMenu = document.getElementById('userMenu');
        userMenu.classList.toggle('hidden');
    });
</script>

</body>
</html>
