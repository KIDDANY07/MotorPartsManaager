<?php
session_start();

include 'php/conexion.php';

if (!isset($_SESSION['correo'])) {
    header("Location: index.php");
    exit();
}

$target_directory = "fotoproductos/";
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen">
    <div class="max-w-md w-full bg-white p-8 rounded-lg shadow-md">
        <img src="imagenes/logo.jpeg" class="verita w-20 mx-auto"></img>
        <form id="agregarProductoForm" class="mt-8" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <div class="mb-4 flex items-center">
                <input type="text" name="nombre_producto" id="nombre_producto" placeholder="Ingrese el nombre del producto" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500">
            </div>
            <div class="mb-4 flex items-center">
                <textarea name="descripcion" id="descripcion" placeholder="Ingrese la descripción del producto" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500"></textarea>
            </div>
            <div class="mb-4 flex items-center">
                <input type="number" name="precio" id="precio" min="0" step="0.01" placeholder="Ingrese el precio del producto" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500">
            </div>
            <div class="mb-4 flex items-center">
                <input type="file" name="imagen" id="imagen" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="flex justify-center">
                <button type="submit" name="agregar_producto" class="login_button bg-white text-black px-4 py-2 rounded-m hover:text-blue-700 focus:outline-none focus:bg-blue-600">Agregar Producto</button>
            </div>
        </form>
        
        <div class="mt-4 text-center">
            <a href="productos.php" class="text-blue-500 font-semibold">Volver</a>
            
        </div>
    </div>
</body>
</html>


<?php 
if(isset($_POST['agregar_producto'])) {
    $nombre_producto = $_POST['nombre_producto'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    
    // Verificar si los campos están completos
    if(empty($nombre_producto) || empty($descripcion) || empty($precio)) {
        echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Por favor complete todos los campos."
                });
            </script>';
        
    }
    
    $imagen_nombre = $_FILES['imagen']['name'];
    $imagen_temp = $_FILES['imagen']['tmp_name'];

    if(empty($imagen_nombre)) {
        echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Por favor seleccione una imagen."
                });
            </script>';
        
    }

    $imagen_extension = strtolower(pathinfo($imagen_nombre, PATHINFO_EXTENSION));

    $imagen_nombre_final = $target_directory . uniqid() . '_' . $nombre_producto . '.' . $imagen_extension;

    if(move_uploaded_file($imagen_temp, $imagen_nombre_final)) {
        $sql = "INSERT INTO productos (nombre, descripcion, precio, imagen_url) VALUES ('$nombre_producto', '$descripcion', '$precio', '$imagen_nombre_final')";
        if ($conexion->query($sql) === TRUE) {
            echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "Éxito",
                        text: "¡Producto agregado exitosamente!"
                    })
                </script>';
            
        } else {
            echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Error al agregar el producto: ' . $conexion->error . '"
                    })
                </script>';

        }
    } else {
        echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Error al subir la imagen."
                })
            </script>';

    }
}
?>