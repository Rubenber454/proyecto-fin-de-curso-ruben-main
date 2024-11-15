<?php
include_once __DIR__ . '/../config/config.php';
session_start();

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Función para redimensionar la imagen sin deformarla
function resizeImage($source, $destination, $width, $height) {
    // Obtener el tamaño original de la imagen
    list($originalWidth, $originalHeight) = getimagesize($source);

    // Obtener el mime-type de la imagen
    $imageType = mime_content_type($source);
    switch ($imageType) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($source);
            break;
        case 'image/png':
            $image = imagecreatefrompng($source);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($source);
            break;
        case 'image/webp':
            $image = imagecreatefromwebp($source);
            break;
        default:
            echo "Error: Formato de imagen no soportado.";
            return false;
    }

    // Calcular la relación de aspecto
    $aspectRatio = $originalWidth / $originalHeight;

    // Calcular el nuevo tamaño manteniendo la proporción
    if ($width / $height > $aspectRatio) {
        // La imagen es más ancha en proporción
        $newWidth = $width;
        $newHeight = $width / $aspectRatio;
    } else {
        // La imagen es más alta en proporción
        $newHeight = $height;
        $newWidth = $height * $aspectRatio;
    }

    $imageResized = imagecreatetruecolor($newWidth, $newHeight);

    // Redimensionar la imagen
    if (!imagecopyresampled($imageResized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight)) {
        echo "Error: No se pudo redimensionar la imagen.";
        imagedestroy($image);
        imagedestroy($imageResized);
        return false;
    }

    // Crear la imagen final de tamaño 300x454 y llenarla de blanco
    $finalImage = imagecreatetruecolor($width, $height);
    $white = imagecolorallocate($finalImage, 255, 255, 255); // Color blanco
    imagefill($finalImage, 0, 0, $white); // Rellenar la imagen con blanco

    // Calcular las posiciones de origen para centrar la imagen
    $srcX = ($newWidth - $width) / 2; // Centrar horizontalmente
    $srcY = ($newHeight - $height) / 2; // Centrar verticalmente
    $srcWidth = $width; // Ancho de la parte a copiar
    $srcHeight = $height; // Alto de la parte a copiar

    // Copiar y recortar la imagen
    imagecopy($finalImage, $imageResized, 0, 0, $srcX, $srcY, $srcWidth, $srcHeight);

    // Guardar la imagen redimensionada en el formato correcto
    switch ($imageType) {
        case 'image/jpeg':
            if (!imagejpeg($finalImage, $destination)) {
                echo "Error: No se pudo guardar la imagen en formato JPG.";
                return false;
            }
            break;
        case 'image/png':
            if (!imagepng($finalImage, $destination)) {
                echo "Error: No se pudo guardar la imagen en formato PNG.";
                return false;
            }
            break;
        case 'image/gif':
            if (!imagegif($finalImage, $destination)) {
                echo "Error: No se pudo guardar la imagen en formato GIF.";
                return false;
            }
            break;
        case 'image/webp':
            if (!imagewebp($finalImage, $destination)) {
                echo "Error: No se pudo guardar la imagen en formato WebP.";
                return false;
            }
            break;
    }

    imagedestroy($imageResized);
    imagedestroy($finalImage);
    imagedestroy($image);

    return true;
}

if (isset($_POST['submit'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];

    // Procesar la imagen
    $imagen = $_FILES['imagen']['name'];
    $target_dir = "../img-bocadillos/"; // Carpeta donde se guardarán las imágenes
    $target_file = $target_dir . basename($imagen);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Verificar si el archivo es una imagen real
    $check = getimagesize($_FILES['imagen']['tmp_name']);
    if ($check === false) {
        echo "El archivo no es una imagen.";
        $uploadOk = 0;
    }

    if (file_exists($target_file)) {
        echo "Lo siento, el archivo ya existe.";
        $uploadOk = 0;
    }

    if ($_FILES['imagen']['size'] > 2000000) {
        echo "Lo siento, el archivo es demasiado grande.";
        $uploadOk = 0;
    }

    if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif", "webp"])) {
        echo "Lo siento, solo se permiten archivos JPG, JPEG, PNG, GIF y WEBP.";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        echo "Lo siento, tu archivo no fue subido.";
    } else {
        $tempFile = $_FILES['imagen']['tmp_name'];
        if (resizeImage($tempFile, $target_file, 300, 454)) { // Cambia el tamaño a 300x454
            $ruta = str_replace("../", "", $target_file);
            $stmt = $conn->prepare("INSERT INTO bocadillos (nombre, descripcion, precio, imagen_url) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssds", $nombre, $descripcion, $precio, $ruta);

            if ($stmt->execute()) {
                echo "Nuevo producto añadido con éxito.";
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error al redimensionar la imagen.";
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto - Admin</title>
    <link rel="stylesheet" href="styles/admin.css">
</head>
<body>
    <h1>Agregar Producto</h1>

    <form method="POST" action="add_product.php" enctype="multipart/form-data">
        <label for="nombre">Nombre del Producto:</label>
        <input type="text" id="nombre" name="nombre" required>

        <label for="descripcion">Descripción:</label>
        <textarea id="descripcion" name="descripcion" required></textarea>

        <label for="precio">Precio (€):</label>
        <input type="number" id="precio" name="precio" required min="0" step="0.01">

        <label for="imagen">Imagen del Producto:</label>
        <input type="file" id="imagen" name="imagen" required>

        <button type="submit" name="submit">Añadir Producto</button>
        <a href="admin.php" class="volver" >Atrás</a>
        
    </form>

</body>
</html>
