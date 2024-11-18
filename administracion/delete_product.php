<?php
require_once "../config/config.php";
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Obtener la URL de la imagen antes de eliminar el producto
    $sql = "SELECT imagen_url FROM bocadillos WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $imagePath = "../" . $row['imagen_url']; // Ajusta la ruta según donde estén tus imágenes

        // Eliminar el producto de la base de datos
        $sql = "DELETE FROM bocadillos WHERE id = $id";
        
        if ($conn->query($sql) === TRUE) {
            // Eliminar la imagen del servidor si existe
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
            header("Location: admin.php");
            exit();
        } else {
            echo "Error al eliminar producto: " . $conn->error;
        }
    } else {
        echo "Producto no encontrado.";
    }
}

$conn->close();

