<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Control de caché para evitar que la página se almacene
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

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

// Obtener los productos actuales
$sql = "SELECT * FROM bocadillos";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Bocarurus</title>
    <link rel="stylesheet" href="styles/admin.css">
</head>
<body>
    <div class="sidebar">
        <h2>Panel de Administración</h2>
        <ul>
            <li><a href="admin.php" class="active">Inicio</a></li>
            <li><a href="add_product.php">Agregar Producto</a></li>
            <li><a href="logout.php">Cerrar Sesión</a></li> <!-- Cambiado a logout.php -->
        </ul>
        <div class="sidebar-footer">
            <p>Admin Dashboard</p>
        </div>
    </div>

    <div class="main-content">
        <h1>Gestionar Productos</h1>

        <a href="add_product.php" class="add-product-btn">Añadir Nuevo Producto</a>

        <div class="table-container">
            <table border="1">
                <tr>
                    
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Imagen</th>
                    <th>Acciones</th>
                </tr>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        
                        <td><?php echo $row['nombre']; ?></td>
                        <td><?php echo $row['descripcion']; ?></td>
                        <td><?php echo number_format($row['precio'], 2); ?>€</td>
                        <td><img src="../<?php echo htmlspecialchars($row['imagen_url']); ?>" alt="<?php echo htmlspecialchars($row['nombre']); ?>" width="100"></td>
                        <td>
                            <a href="edit_product.php?id=<?php echo $row['id']; ?>">
                                <button class="edit-btn">Editar</button>
                            </a>
                            <a href="delete_product.php?id=<?php echo $row['id']; ?>" onclick="return confirm('¿Estás seguro de eliminar este producto?');">
                                <button class="delete-btn">Eliminar</button>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No hay productos disponibles.</td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>

    <?php $conn->close(); ?>
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

