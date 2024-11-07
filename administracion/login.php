<?php
session_start();

// Simular credenciales de administrador
$admin_user = "admin";
$admin_pass = "admin123"; // Debes usar hashing de contraseñas en un entorno real

// Verificar si se ha enviado el formulario
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Comprobar credenciales
    if ($username === $admin_user && $password === $admin_pass) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin.php");
        exit();
    } else {
        $error_message = "Usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Admin</title>
    <link rel="stylesheet" href="styles/admin.css">
</head>
<body>
    <div class="main-content">
        <h1>Iniciar Sesión - Admin</h1>

        <?php if (isset($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="username">Usuario:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit" name="login">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>
