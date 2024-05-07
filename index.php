<?php
session_start();
include('php/conexion.php'); // Suponiendo que tienes un archivo de conexión llamado conexion.php

$isLoggedIn = isset($_SESSION['correo']);

$usuarios_permitidos = array("1027400008","1073671038","1000131565");

function usuarioPermitido($usuarios_permitidos, $numero_documento) {
    return in_array($numero_documento, $usuarios_permitidos);
}

if(isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

$nombreUsuario = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : "Usuario";
$foto_perfil = "";

$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : "";
$numero_documento = isset($_SESSION['numero_documento']) ? $_SESSION['numero_documento'] : "";

// Recuperar la foto de perfil de la base de datos si el usuario está logueado
if($isLoggedIn && isset($_SESSION['correo'])) {
    $correo = $_SESSION['correo'];
    $query = "SELECT foto_perfil FROM usuarios WHERE correo = '$correo'";
    $result = mysqli_query($conexion, $query);
    if($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $foto_perfil = $row['foto_perfil'];
    }
}

// Si no se encuentra una foto de perfil en la base de datos, usar una imagen predeterminada
if(empty($foto_perfil)) {
    $foto_perfil = "fotosperfiles/perfil_base.png";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MotorParts Manaager</title>
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
            <a href="index.php"><img src="imagenes/logo.png" alt="Logo" class="h-14"></a>
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

<br><br><br><br>
<div id="imagen_motor" class="relative">
    <img src="imagenes/moto.jpg" alt="Descripción de la imagen" class="w-full max-w-8xl m-auto h-auto saturate-300 shadow-lg">
    <div class="absolute inset-0 flex justify-center items-center">
        <div class="text-center">
            <h1 class="text-4xl text-white font-bold drop-shadow-2xl">MotorParts Manaager</h1>
            <p class="text-lg text-white drop-shadow-sm">Impulsando tu pasión, pieza por pieza</p>
        </div>
    </div>
</div>
<div class="bg-gray-100 shadow-sm mt-2">
    <p class="text-gray-700 text-center py-4 text-2xl">En MotorParts Manager, avivamos tu pasión por la carretera.</p>
</div>
<br>
<main class="container mx-auto p-4 grid grid-cols-2 gap-4 relative">
    <div class="slider-container shadow-md ml-1 relative" style="display: flex; width: 100%;">
        <div class="slides">
            <div class="slide">
                <img src="imagenes/slider/slid1.jpg" alt="Imagen 1">
            </div>
            <div class="slide">
                <img src="imagenes/slider/slide5.jpg" alt="Imagen 2">
            </div>
            <div class="slide">
                <img src="imagenes/slider/slide4.jpg" alt="Imagen 3">
            </div>
            <div class="slide">
                <img src="imagenes/slider/slid3.jpg" alt="Imagen 4">
            </div>
            <div class="slide">
                <img src="imagenes/slider/slide2.jpg" alt="Imagen 5">
            </div>
        </div>
        <button class="slider-button prev text-white left-0">&#10094;</button>
        <button class="slider-button next text-white right-0">&#10095;</button>
    </div>
    <div class="bg-white p-2 shadow-md flex justify-center items-center h-full">
        <div class="text-container">
            <p class="text-center sm:text-lg md:text-xl lg:text-2xl xl:text-2xl font-semibold text-gray-800">Tenemos todo lo que tu moto necesita para brillar en la carretera.</p>
        </div>
    </div>

</main>
<footer class="bg-indigo-900 text-white py-8">
    <div class="container text-center mx-auto grid grid-cols-1 md:grid-cols-2 gap-8">
        <div>
            <h3 class="text-lg font-semibold mb-4">Contacto</h3>
            <p class="text-sm">Dirección: Universidad de Cundinamarca</p>
            <p class="text-sm">Teléfono: +57 3196654538</p>
            <p class="text-sm">Correo electrónico: info@MotorPartesManaager.com</p>
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
        <br>
        <?php if($isLoggedIn && usuarioPermitido($usuarios_permitidos, $numero_documento)): ?>
            <div class="text-center">
                <a href="productos.php" class="hover:text-blue-700 ">
                    <i class="fas fa-plus"></i>
                    <span class="hidden md:inline">Agregar producto</span>
                </a>
            </div>
        <?php endif; ?>

    </div>
    <br>
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
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const prevBtn = document.querySelector(".prev");
        const nextBtn = document.querySelector(".next");
        const slides = document.querySelector(".slides");

        let slideIndex = 0;

        function showSlides() {
            slides.style.transform = `translateX(${-slideIndex * 100}%)`;
        }

        function nextSlide() {
            if (slideIndex < slides.children.length - 1) {
                slideIndex++;
            } else {
                slideIndex = 0;
            }
            showSlides();
        }

        function prevSlide() {
            if (slideIndex > 0) {
                slideIndex--;
            } else {
                slideIndex = slides.children.length - 1;
            }
            showSlides();
        }

        nextBtn.addEventListener("click", nextSlide);
        prevBtn.addEventListener("click", prevSlide);

        setInterval(nextSlide, 3000);
    });
</script>
</body>
</html>
