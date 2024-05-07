<?php
session_start();

$isLoggedIn = isset($_SESSION['correo']);

if(isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

$nombreUsuario = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : "Usuario";
$foto_perfil = isset($_SESSION['foto_perfil']) ? $_SESSION['foto_perfil'] : "";

include 'php/conexion.php';

$sql = "SELECT * FROM productos";
$resultado = $conexion->query($sql);
$productos = [];
if ($resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $productos[] = $row;
    }
}

if(isset($_POST['add_to_cart'])) {
    $nombre_producto = isset($_POST['nombre']) ? $_POST['nombre'] : null;
    
    if($isLoggedIn) {
        $correo = isset($_SESSION['correo']) ? $_SESSION['correo'] : null;
        if($correo) {
            $sql_usuario = "SELECT id FROM usuarios WHERE correo = ?";
            $stmt = $conexion->prepare($sql_usuario);
            if (!$stmt) {
                die("Error de preparación de la consulta: " . $conexion->error);
            }
            
            $stmt->bind_param("s", $correo);
            if (!$stmt->bind_param("s", $correo)) {
                die("Error al vincular los parámetros: " . $stmt->error);
            }
            
            $stmt->execute();
            $resultado_usuario = $stmt->get_result();
            
            if ($resultado_usuario->num_rows > 0) {
                $fila_usuario = $resultado_usuario->fetch_assoc();
                $id_usuario = $fila_usuario['id'];

                $fecha = date('Y-m-d H:i:s');
                $sql_carrito = "INSERT INTO carrito (nombre_producto, id_usuario, fecha) VALUES (?, ?, ?)";
                $stmt = $conexion->prepare($sql_carrito);
                if (!$stmt) {
                    die("Error de preparación de la consulta: " . $conexion->error);
                }
                
                $stmt->bind_param("sis", $nombre_producto, $id_usuario, $fecha);
                if (!$stmt->bind_param("sis", $nombre_producto, $id_usuario, $fecha)) {
                    die("Error al vincular los parámetros: " . $stmt->error);
                }
                
                if ($stmt->execute()) {
                    // Alerta de éxito al agregar producto al carrito
                    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>";
                    echo "<script>Swal.fire('Producto agregado al carrito', '', 'success');</script>";
                } else {
                    // Alerta de error al agregar producto al carrito
                    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>";
                    echo "<script>Swal.fire('Error al agregar producto al carrito', 'Error: ".$stmt->error."', 'error');</script>";
                }
            } else {
                // Alerta de error: No se pudo obtener el ID de usuario
                echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>";
                echo "<script>Swal.fire('Error', 'No se pudo obtener el ID de usuario.', 'error');</script>";
            }
        } else {
            // Alerta de error: No se encontró el correo de sesión
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>";
            echo "<script>Swal.fire('Error', 'No se encontró el correo de sesión.', 'error');</script>";
        }
    } else {
        // Alerta de error: Debes iniciar sesión para agregar productos al carrito
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>";
        echo "<script>Swal.fire('Error', 'Debes iniciar sesión para agregar productos al carrito.', 'error');</script>";
    }
}

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
    <style>
    .transition-opacity {
        transition: opacity 0.5s;
    }

    .slider-container {
        position: relative;
        width: 50%; 
        margin-right: auto; 
        overflow: hidden;
        margin: 0 auto;
    }

    .slides {
        display: flex;
        transition: transform 0.3s ease;
    }

    .slide {
        flex: 0 0 auto;
        width: 100%;
        display: flex;
        justify-content: center;
    }

    .slide img {
        max-width: 100%; 
        height: auto;
    }

    .slider-button {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        border: none;
        cursor: pointer;
        padding: 10px;
        font-size: 20px;
        z-index: 1;
        border-radius: 10%;
    }

    .slider-button.prev {
        background-color: rgba(0, 0, 255, 0.2);
        border-radius: 10%;
    }

    .slider-button.next {
        background-color: rgba(0, 0, 255, 0.2);
    }

    @media (max-width: 768px) {
        .slider-container {
            width: 90%; 
        }

        .slide img {
            max-width: auto;
            height: auto;
        }
        
        .slides {
            justify-content: flex-start; 
        }

        .slider-button {
            z-index: 2;
        }
    }

    header {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 9999; 
        background-color: white; 
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); 
    }
    </style>
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
<br><br><br>
<section class="py-10 m-15 ">
    <div class="container mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach($productos as $producto): ?>
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <img src="<?php echo $producto['imagen_url']; ?>" alt="<?php echo $producto['nombre']; ?>" class="w-full h-64 object-cover">
            <div class="p-4">
                <h2 class="text-lg font-semibold text-gray-800"><?php echo $producto['nombre']; ?></h2>
                <p class="mt-2 text-gray-600"><?php echo $producto['descripcion']; ?></p>
                <p class="mt-2 text-gray-700 font-bold">$<?php echo $producto['precio']; ?></p>
                <!-- En el formulario, el campo oculto ahora se llama "nombre" -->
                <form method="post">
                    <input type="hidden" name="nombre" value="<?php echo $producto['nombre']; ?>">
                    <button type="submit" name="add_to_cart" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Agregar al carrito</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
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
    
    <div class="container mx-auto  text-center ">
      
        <p class="text-sm ">Desarrollado por </p>
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
<?php


$isLoggedIn = isset($_SESSION['correo']);

if(isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

$nombreUsuario = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : "Usuario";
$foto_perfil = isset($_SESSION['foto_perfil']) ? $_SESSION['foto_perfil'] : "";

include 'php/conexion.php';

$sql = "SELECT * FROM productos";
$resultado = $conexion->query($sql);
$productos = [];
if ($resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $productos[] = $row;
    }
}

if(isset($_POST['add_to_cart'])) {
    $nombre_producto = isset($_POST['nombre']) ? $_POST['nombre'] : null;
    
    if($isLoggedIn) {
        $correo = isset($_SESSION['correo']) ? $_SESSION['correo'] : null;
        if($correo) {
            $sql_usuario = "SELECT id FROM usuarios WHERE correo = ?";
            $stmt = $conexion->prepare($sql_usuario);
            if (!$stmt) {
                die("Error de preparación de la consulta: " . $conexion->error);
            }
            
            $stmt->bind_param("s", $correo);
            if (!$stmt->bind_param("s", $correo)) {
                die("Error al vincular los parámetros: " . $stmt->error);
            }
            
            $stmt->execute();
            $resultado_usuario = $stmt->get_result();
            
            if ($resultado_usuario->num_rows > 0) {
                $fila_usuario = $resultado_usuario->fetch_assoc();
                $id_usuario = $fila_usuario['id'];

                $fecha = date('Y-m-d H:i:s');
                $sql_carrito = "INSERT INTO carrito (nombre_producto, id_usuario, fecha) VALUES (?, ?, ?)";
                $stmt = $conexion->prepare($sql_carrito);
                if (!$stmt) {
                    die("Error de preparación de la consulta: " . $conexion->error);
                }
                
                $stmt->bind_param("sis", $nombre_producto, $id_usuario, $fecha);
                if (!$stmt->bind_param("sis", $nombre_producto, $id_usuario, $fecha)) {
                    die("Error al vincular los parámetros: " . $stmt->error);
                }
                
                if ($stmt->execute()) {
                    // Alerta de éxito al agregar producto al carrito
                    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>";
                    echo "<script>Swal.fire('Producto agregado al carrito', '', 'success');</script>";
                } else {
                    // Alerta de error al agregar producto al carrito
                    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>";
                    echo "<script>Swal.fire('Error al agregar producto al carrito', 'Error: ".$stmt->error."', 'error');</script>";
                }
            } else {
                // Alerta de error: No se pudo obtener el ID de usuario
                echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>";
                echo "<script>Swal.fire('Error', 'No se pudo obtener el ID de usuario.', 'error');</script>";
            }
        } else {
            // Alerta de error: No se encontró el correo de sesión
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>";
            echo "<script>Swal.fire('Error', 'No se encontró el correo de sesión.', 'error');</script>";
        }
    } else {
        // Alerta de error: Debes iniciar sesión para agregar productos al carrito
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>";
        echo "<script>Swal.fire('Error', 'Debes iniciar sesión para agregar productos al carrito.', 'error');</script>";
    }
}

if(empty($foto_perfil)) {
    $foto_perfil = "fotosperfiles/perfil_base.png";
}
?>

