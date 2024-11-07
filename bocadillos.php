<?php
include_once 'config\config.php';
session_start();

// Crear la conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar si la conexión fue exitosa
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Comprobar si se añade algo al carrito

if (isset($_POST['add_to_cart'])) {
    $product = [
        'id' => $_POST['id_bocata'],
        'nombre' => $_POST['nombre'],
        'precio' => $_POST['precio']
    ];
    $_SESSION['cart'][] = $product;
}


// Inicializar la variable del total
$total = 0;

// Calcular el total si el carrito no está vacío
if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    foreach ($_SESSION['cart'] as $product) {
        $total += $product['precio'];
    }
}

// Consulta SQL para obtener los bocadillos
$sql = "SELECT id, nombre, descripcion, precio, imagen_url FROM bocadillos";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bocadillos - Bocaruru</title>
    <link rel="stylesheet" href="styles/estilos.css">
</head>
<body>

    <div class="sidebar">
        <img class="logo" src="img/Logo_bocarurus.png" alt="Logo">
        <h2>Bocaruru</h2>
        <ul>
            <li><a href="index.html">Inicio</a></li>
            <li><a href="bocadillos.php">Bocadillos</a></li>
            <li><a href="contacto\contacto.html">Contacto</a></li>
            <li><a href="carrito.php">Ver Carrito</a></li> <!-- Link al carrito -->
        </ul>
    </div>
    <!-- Carrito flotante -->
    <div class="carrito-flotante">
        <h3>Carrito de Compras</h3>
        <p>Artículos: <?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?></p>
        <p class="total">Total: €<?php echo number_format($total, 2); ?></p>
        <a href="carrito.php" class="ver-carrito">Ver Carrito</a>
    </div>

    <div class="main-content">
        <h1>Nuestra Carta de Bocadillos</h1>
        
        <div class="bocadillos-container">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<div class="bocadillo">';
                    echo '<img src="' . $row["imagen_url"] . '" alt="' . $row["nombre"] . '">';
                    echo '<div class="texto-superpuesto">';
                    echo '<p class="nombre">' . $row["nombre"] . '</p>';
                    echo '<p class="descripcion">' . $row["descripcion"] . '</p>';
                    echo '<p class="precio">€' . number_format($row["precio"], 2) . '</p>';
                    
                    // Formulario oculto para enviar el bocadillo al carrito
                    echo '<form method="POST" action="bocadillos.php">';
                    echo '<input type="hidden" name="id_bocata" value="' . $row["id"] . '">'; 
                    echo '<input type="hidden" name="nombre" value="' . $row["nombre"] . '">';
                    echo '<input type="hidden" name="precio" value="' . $row["precio"] . '">';
                    echo '<button type="submit" name="add_to_cart" class="boton">Añadir al Carrito</button>';
                    echo '</form>';
                    
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "<p>No hay bocadillos disponibles en este momento.</p>";
            }

            $conn->close();
            ?>
        </div>
    </div>

<script src="carrito.js"></script>
 <!-- Botón hamburguesa -->
 <div class="nav-toggle" onclick="toggleMenu()">
        <span></span>
        <span></span>
        <span></span>
    </div>
    
    <script>
        function toggleMenu() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('active');
        }
    </script>

</body>
</html>
