<?php
session_start();
// Habilitar la visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Inicializar el carrito si no existe
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Agregar producto al carrito
if (isset($_POST['add_to_cart'])) {
    $id = $_POST['id'] ?? null; // Usar null si no está definido
    $nombre = $_POST['nombre'] ?? null; // Usar null si no está definido
    $precio = $_POST['precio'] ?? null; // Usar null si no está definido

    // Solo agregar si todos los campos están definidos
    if ($id !== null && $nombre !== null && $precio !== null) {
        $_SESSION['cart'][] = ['id' => $id, 'nombre' => $nombre, 'precio' => $precio];
    }
}

// Eliminar un producto del carrito
if (isset($_POST['remove'])) {
    if (isset($_POST['id'])) { // Verifica si el id está definido
        $id = $_POST['id'];
        foreach ($_SESSION['cart'] as $key => $product) {
            if (isset($product['id']) && $product['id'] == $id) { // Verifica si 'id' existe en el producto
                unset($_SESSION['cart'][$key]);
                break; // Detener el bucle después de eliminar el primer producto encontrado
            }
        }
    } else {
        echo "Error: ID no definido"; // Mensaje de error opcional
    }
}

// Inicializar la variable del total
$total = 0;

// Calcular el total si el carrito no está vacío
if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    foreach ($_SESSION['cart'] as $product) {
        if (isset($product['precio'])) { // Verifica que 'precio' existe
            $total += $product['precio'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <link rel="stylesheet" href="styles/estilosCarrito.css">
</head>

<body>

    <div class="sidebar">
    <img class="logo" src="img/Logo_bocarurus.png" alt="Logo">
        <h2>Tu Carrito</h2>
        <ul>
            <li><a href="index.html">Inicio</a></li>
            <li><a href="bocadillos.php">Bocadillos</a></li>
            <li><a href="contacto/contacto.html">Contacto</a></li>
        </ul>
    </div>
    

    <div class="main-content">
        <h1>Carrito de Compras</h1>

        <?php if (count($_SESSION['cart']) > 0): ?>
            <table>
                <tr>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Acción</th>
                </tr>
                <?php foreach ($_SESSION['cart'] as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['nombre'] ?? 'Sin nombre'); ?></td>
                        <?php $precio = floatval($product['precio'])?>
                        <td>€<?php echo number_format( $precio, 2); ?></td>
                        <td>
                            <form method="POST" action="carrito.php">
                                <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                <!-- Usa el ID aquí -->
                                <button type="submit" name="remove">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <h3>Total: €<?php echo number_format($total, 2); ?></h3>

            <h2>Opciones de Pago</h2>
            <!-- Desplegable de métodos de pago -->
            <label for="payment-methods">Selecciona un método de pago:</label>
            <select id="payment-methods">
                <option value="">--Selecciona un método--</option>
                <option value="paypal">Pagar con PayPal</option>
                <option value="card">Pagar con tarjeta</option>
                <option value="cash">Pago en efectivo</option>
            </select>

            <!-- Formulario de pago con tarjeta, inicialmente oculto -->
            <div id="payment-form-card" style="display: none;">
                <h2>Pago con tarjeta de crédito</h2>
                <form method="POST" action="procesar_pago.php">
                    <div>
                        <label for="card-number">Número de tarjeta:</label>
                        <input type="text" id="card-number" name="card_number" required>
                    </div>
                    <div>
                        <label for="card-expiration">Fecha de expiración (MM/AA):</label>
                        <input type="text" id="card-expiration" name="card_expiration" placeholder="MM/AA" required>
                    </div>
                    <div>
                        <label for="card-cvc">CVC:</label>
                        <input type="text" id="card-cvc" name="card_cvc" required>
                    </div>
                    <div>
                        <label for="card-name">Nombre en la tarjeta:</label>
                        <input type="text" id="card-name" name="card_name" required>
                    </div>
                    <button type="submit">Pagar con tarjeta</button>
                </form>
            </div>

            <!-- Formulario de pago en efectivo, inicialmente oculto -->
            <div id="payment-form-cash" style="display: none;">
                <h2>Pago en efectivo</h2>
                <p>Pagará cuando la comida llegue a su casa, intente tener el dinero justo, si no avisar para cambio. Gracias.</p>
            </div>

            <!-- Integración de PayPal (solo si es necesario) -->
            <div id="payment-form-paypal" style="display: none;">
                <h2>Pagar con PayPal</h2>
                <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
                    <input type="hidden" name="cmd" value="_cart">
                    <input type="hidden" name="business" value="tu-correo@dominio.com">
                    <input type="hidden" name="upload" value="1">

                    <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                        <?php foreach ($_SESSION['cart'] as $index => $product): ?>
                            <?php if (isset($product['nombre'], $product['precio'])): ?>
                                <input type="hidden" name="item_name_<?php echo $index + 1; ?>"
                                    value="<?php echo htmlspecialchars($product['nombre']); ?>">
                                <input type="hidden" name="amount_<?php echo $index + 1; ?>"
                                    value="<?php echo number_format($product['precio'], 2); ?>">
                            <?php else: ?>
                                <!-- Mensaje de depuración si falta algún campo -->
                                <?php error_log("Producto en índice $index no tiene 'nombre' o 'precio'."); ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No hay productos en el carrito.</p>

                    <?php endif; ?>

                    <input type="hidden" name="currency_code" value="EUR">
                    <input type="submit" value="Pagar con PayPal">
                </form>
            </div>


        <?php else: ?>
            <p>Tu carrito está vacío.</p>
        <?php endif; ?>

    </div>

    <script>
        document.getElementById('payment-methods').addEventListener('change', function () {
            // Ocultar todos los formularios de pago
            document.getElementById('payment-form-card').style.display = 'none';
            document.getElementById('payment-form-cash').style.display = 'none';
            document.getElementById('payment-form-paypal').style.display = 'none';

            // Mostrar el formulario correspondiente
            switch (this.value) {
                case 'card':
                    document.getElementById('payment-form-card').style.display = 'block';
                    break;
                case 'cash':
                    document.getElementById('payment-form-cash').style.display = 'block';
                    break;
                case 'paypal':
                    document.getElementById('payment-form-paypal').style.display = 'block';
                    break;
            }
        });
        
    </script>
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