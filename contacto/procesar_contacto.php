<?php

// Establecer encabezado JSON
header('Content-Type: application/json');

// Incluir configuración y PHPMailer
include_once __DIR__ . '/../config/config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Obtener los datos del formulario y asignarlos a variables
$nombre = isset($_POST["nombre"]) ? trim($_POST["nombre"]) : '';
$email = isset($_POST["email"]) ? trim($_POST["email"]) : '';
$mensaje = isset($_POST["mensaje"]) ? trim($_POST["mensaje"]) : '';

// Verificar que los campos no estén vacíos antes de continuar
if (empty($nombre) || empty($email) || empty($mensaje)) {
    echo json_encode(["success" => false, "message" => "Por favor completa todos los campos del formulario."]);
    exit;
}

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Conexión fallida: " . $conn->connect_error]);
    exit;
}

// Insertar los datos en la base de datos
$stmt = $conn->prepare("INSERT INTO contactos (nombre, email, mensaje, fecha) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("sss", $nombre, $email, $mensaje);

if (!$stmt->execute()) {
    echo json_encode(["success" => false, "message" => "Error al guardar los datos: " . $stmt->error]);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

// Definir tu correo como destinatario
$destinatario = 'info@rubenbc.com';  // Reemplaza con tu dirección de correo
$asunto = "Nuevo mensaje de contacto de: $nombre";

// Enviar el correo
$correoEnviado = Correo::enviarCorreo($destinatario, "Administrador", $asunto, "Correo del remitente: $email\n\nMensaje:\n$mensaje");

// Cerrar la conexión
$conn->close();

// Enviar respuesta según el resultado
if ($correoEnviado['success']) {
    echo json_encode(["success" => true, "message" => "Formulario enviado con éxito."]);
} else {
    echo json_encode(["success" => false, "message" => "Error al enviar el formulario."]);
}

class Correo {
    public static function enviarCorreo($forEmail, $forName, $asunto, $body) {
        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor
            $mail->isSMTP();
            $mail->Host       = MAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = MAIL_USER;
            $mail->Password   = MAIL_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Configuración del remitente y destinatario
            $mail->CharSet = 'UTF-8';
            $mail->setFrom(MAIL_USER, '=?UTF-8?B?'.base64_encode('Administración').'?=');
            $mail->addAddress($forEmail, '=?UTF-8?B?'.base64_encode($forName).'?=');

            // Configuración del contenido
            $mail->isHTML(true);
            $mail->Subject = '=?UTF-8?B?'.base64_encode($asunto).'?=';
            $mail->Body    = "<html><head><meta charset='UTF-8'></head><body>".nl2br(htmlspecialchars($body))."</body></html>";
            $mail->AltBody = $body;

            $mail->send();
            return ["success" => true, "message" => "Correo enviado exitosamente."];
        } catch (Exception $e) {
            return ["success" => false, "message" => 'Error al enviar el correo: ' . $mail->ErrorInfo];
        }
    }
}
?>
