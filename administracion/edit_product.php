<?php
// Iniciar sesión
session_start();

// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bocarurus";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Asegúrate de que el ID del producto se pasa correctamente
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Obtener los datos del producto para precargar el formulario
$producto = null; // Inicializa la variable

if ($id > 0) {
    $sql = "SELECT * FROM bocadillos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $producto = $result->fetch_assoc(); // Asigna el resultado a la variable
    $stmt->close();
}

// Verifica si el producto fue encontrado
if (!$producto) {
    echo "Producto no encontrado.";
    exit; // Termina el script si no se encuentra el producto
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

    // Mantener la ruta existente por defecto
    $ruta = $producto['imagen_url']; 

    // Procesar la imagen solo si se ha subido una nueva
    if ($_FILES['imagen']['name']) { // Solo procesar si hay una nueva imagen
        // Eliminar la imagen anterior si existe
        $imagenAnterior = "../" . $ruta; // Asumiendo que la ruta está almacenada relativa a la raíz

        // Verifica si el archivo existe antes de intentar eliminarlo
        if (file_exists($imagenAnterior)) {
            unlink($imagenAnterior); // Eliminar archivo anterior
        }
        echo "Ruta anterior: " . $imagenAnterior;
        echo "Ruta nueva: " . $ruta;


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
                $ruta = str_replace("../", "", $target_file); // Actualiza la ruta con la nueva imagen
            } else {
                echo "Error al redimensionar la imagen.";
            }
        }
    }

    // Actualizar el producto en la base de datos
    $stmt = $conn->prepare("UPDATE bocadillos SET nombre = ?, descripcion = ?, precio = ?, imagen_url = ? WHERE id = ?");
    $stmt->bind_param("ssdsi", $nombre, $descripcion, $precio, $ruta, $id); // Asegúrate de incluir el ID
    if ($stmt->execute()) {
        echo "Producto actualizado con éxito.";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Aquí empieza el HTML
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
    <link rel="stylesheet" href="styles/admin.css">
</head>
<body>
    <h1>Editar Producto</h1>

    <form method="POST" action="edit_product.php?id=<?php echo $id; ?>" enctype="multipart/form-data">
        <label for="nombre">Nombre del Producto:</label>
        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>

        <label for="descripcion">Descripción:</label>
        <textarea id="descripcion" name="descripcion" required><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>

        <label for="precio">Precio (€):</label>
        <input type="number" id="precio" name="precio" value="<?php echo htmlspecialchars($producto['precio']); ?>" required min="0" step="0.01">

        <label for="imagen">Imagen del Producto:</label>
        <input type="file" id="imagen" name="imagen">

        <button type="submit" name="submit">Actualizar Producto</button>
        <button type="button" onclick="window.location.href='phpAdmin.php'">Atrás</button>
    </form>

</body>
</html>
<?php
// Cerrar conexión
$conn->close();
?>
